<?php
require "../configuraciones/Conexion.php";
class Refinanciamiento
{

    public function buscarCreditos($idsucursal, $buscar)
    {
        $sql = "SELECT
                p.idpersona,
                p.nombre,
                p.num_documento
            FROM persona p
            WHERE p.num_documento LIKE '%$buscar%'
               OR p.nombre LIKE '%$buscar%'";

        $clientes = ejecutarConsulta($sql);

        $creditos = [];

        while ($cliente = $clientes->fetch_assoc()) {

            $sqlVentas = "SELECT
                        v.idventa,
                        v.fecha_hora,
                        v.total_venta,
                        v.serie_comprobante,
                        v.num_comprobante
                    FROM venta v
                    WHERE v.idsucursal = '$idsucursal'
                      AND v.idcliente = '{$cliente["idpersona"]}'
                    ORDER BY v.fecha_hora ASC";

            $ventas = ejecutarConsulta($sqlVentas);

            while ($venta = $ventas->fetch_assoc()) {

                // 🔥 Obtener último refinanciamiento (si existe)
                $sqlRef = "SELECT MAX(idrefinanciamiento) AS idref
                       FROM cuentas_por_cobrar
                       WHERE idventa = '{$venta["idventa"]}'
                         AND idrefinanciamiento IS NOT NULL";

                $ref = ejecutarConsultaSimpleFila($sqlRef);

                $idref = $ref["idref"];

                // 🔥 Cuotas según estado
                if (!empty($idref)) {

                    $sqlCuotas = "SELECT estado_pago, deudatotal
                              FROM cuentas_por_cobrar
                              WHERE idrefinanciamiento = '$idref'";

                } else {

                    $sqlCuotas = "SELECT estado_pago, deudatotal
                              FROM cuentas_por_cobrar
                              WHERE idventa = '{$venta["idventa"]}'
                                AND idrefinanciamiento IS NULL
                                AND idrefinanciamiento_origen IS NULL";
                }

                $cuotas = ejecutarConsulta($sqlCuotas);

                $pagado = 0;
                $saldo = 0;

                while ($c = $cuotas->fetch_assoc()) {

                    if ($c["estado_pago"] == 0) {
                        $pagado += $c["deudatotal"];
                    } else {
                        $saldo += $c["deudatotal"];
                    }
                }

                // 🔥 Solo créditos con saldo
                if ($saldo > 0) {

                    $creditos[] = [
                        "idventa" => $venta["idventa"],
                        "cliente" => $cliente["nombre"],
                        "documento_cliente" => $cliente["num_documento"],
                        "documento_venta" => $venta["serie_comprobante"] . '-' . $venta["num_comprobante"],
                        "fecha" => $venta["fecha_hora"],
                        "total" => $venta["total_venta"],
                        "pagado" => $pagado,
                        "saldo" => $saldo,
                        "refinanciado" => ($idref ? true : false),
                        "idrefinanciamiento" => $idref
                    ];
                }
            }
        }

        if (empty($creditos)) {
            return json_encode([
                "estado" => false,
                "mensaje" => "No se encontraron créditos pendientes para refinanciar."
            ]);
        }

        return json_encode([
            "estado" => true,
            "creditos" => $creditos
        ]);
    }


    public function detalleCredito($idventa)
    {
        $sql = "SELECT
                v.idventa,
                p.nombre,
                p.num_documento,
                v.total_venta
            FROM venta v
            INNER JOIN persona p ON p.idpersona = v.idcliente
            WHERE v.idventa = '$idventa'
            LIMIT 1";

        $venta = ejecutarConsultaSimpleFila($sql);

        if (!$venta) {
            return json_encode([
                "estado" => false,
                "mensaje" => "No se encontró el crédito."
            ]);
        }

        // Buscar último refinanciamiento
        $sqlRef = "SELECT MAX(idrefinanciamiento) AS idref
               FROM cuentas_por_cobrar
               WHERE idventa = '$idventa'
                 AND idrefinanciamiento IS NOT NULL";

        $ref = ejecutarConsultaSimpleFila($sqlRef);

        if (!empty($ref["idref"])) {

            // CUOTAS REFINANCIADAS (ÚLTIMO PLAN)
            $sql = "SELECT *
                FROM cuentas_por_cobrar
                WHERE idrefinanciamiento = '{$ref["idref"]}'
                ORDER BY fechavencimiento";

        } else {

            // CUOTAS ORIGINALES
            $sql = "SELECT *
                FROM cuentas_por_cobrar
                WHERE idventa = '$idventa'
                  AND idrefinanciamiento IS NULL
                  AND idrefinanciamiento_origen IS NULL
                ORDER BY fechavencimiento";
        }

        $rspta = ejecutarConsulta($sql);

        $cuotas = [];
        $pagado = 0;
        $saldo = 0;

        while ($row = $rspta->fetch_assoc()) {

            $cuotas[] = $row;

            // corregido: 0 = pagado, 1 = pendiente
            if ($row["estado_pago"] == 0) {
                $pagado += $row["deudatotal"];
            } else {
                $saldo += $row["deudatotal"];
            }
        }

        return json_encode([
            "estado" => true,
            "refinanciado" => !empty($ref["idref"]),
            "venta" => [
                "idventa" => $venta["idventa"],
                "cliente" => $venta["nombre"],
                "documento" => $venta["num_documento"],
                "total" => round($venta["total_venta"], 2),
                "pagado" => round($pagado, 2),
                "saldo" => round($saldo, 2)
            ],
            "cuotas" => $cuotas
        ]);
    }


    public function guardarRefinanciamiento($idventa, $interes, $inicial, $frecuencia, $cuotas, $fecha, $idusuario)
    {
        ejecutarConsulta("START TRANSACTION");

        try {

            // Obtener saldo pendiente
            $sql = "SELECT SUM(deudatotal) AS saldo
                FROM cuentas_por_cobrar
                WHERE idventa='$idventa'
                AND estado_pago=1";

            $saldo = ejecutarConsultaSimpleFila($sql)["saldo"];

            if ($saldo <= 0) {
                throw new Exception("No existe saldo pendiente.");
            }

            // Total a refinanciar
            $montoRef = $saldo - $inicial;
            $total_interes = $montoRef * ($interes / 100);

            if ($montoRef <= 0) {
                throw new Exception("El monto refinanciado es inválido.");
            }

            // Crear cabecera del refinanciamiento
            $sql = "INSERT INTO refinanciamientos(
                    idventa,
                    saldo_original,
                    interes,
                    inicial,
                    total_refinanciado,
                    cuotas,
                    frecuencia,
                    fecha_inicio,
                    fecha_registro,
                    idusuario
                )
                VALUES(
                    '$idventa',
                    '$saldo',
                    '$interes',
                    '$inicial',
                    '$montoRef',
                    '$cuotas',
                    '$frecuencia',
                    '$fecha',
                    NOW(),
                    '$idusuario'
                )";

            $id_refinanciamiento = ejecutarConsulta_retornarID($sql);

            if (!$id_refinanciamiento) {
                throw new Exception("No se pudo registrar el refinanciamiento.");
            }

            // Marcar cuotas pendientes como refinanciadas
            $sql = "UPDATE cuentas_por_cobrar
                SET
                    estado_pago = 2,
                    idrefinanciamiento_origen = '$id_refinanciamiento'
                WHERE idventa='$idventa'
                AND estado_pago=0";

            ejecutarConsulta($sql);

            // Calcular monto por cuota
            $interes_cuota_base = round($total_interes / $cuotas, 2);
            $deuda_base = round($montoRef / $cuotas, 2);
            $monto_cuota = round((($montoRef * ($interes / 100)) + $montoRef) / $cuotas, 2);
            $fechaVence = new DateTime($fecha);
            $fechaRegistro = (new DateTime())->format('Y-m-d H:i:s');
            for ($i = 1; $i <= $cuotas; $i++) {

                $sql = "INSERT INTO cuentas_por_cobrar(
                    idventa,
                    idrefinanciamiento,
                    fecharegistro,
                    deudatotal,
                    deuda_base,
                    fechavencimiento,
                    abonototal,
                    deuda,
                    interes
                )
                VALUES(
                    '$idventa',
                    '$id_refinanciamiento',
                    '$fechaRegistro',
                    '$monto_cuota',
                    '$deuda_base',
                    '" . $fechaVence->format('Y-m-d') . "',
                    0,
                    '$monto_cuota',
                    '$interes_cuota_base'
                )";

                ejecutarConsulta($sql);

                $intervalos = [
                    1 => '+1 day',
                    2 => '+1 week',
                    3 => '+15 days',
                    4 => '+1 month',
                    5 => '+2 months',
                    6 => '+3 months',
                    7 => '+6 months',
                    8 => '+1 year'
                ];

                $fechaVence->modify($intervalos[$frecuencia] ?? '+1 month');
            }

            ejecutarConsulta("COMMIT");

            return json_encode([
                "estado" => true,
                "mensaje" => "Refinanciamiento registrado correctamente."
            ]);

        } catch (Exception $e) {

            ejecutarConsulta("ROLLBACK");

            return json_encode([
                "estado" => false,
                "mensaje" => $e->getMessage()
            ]);
        }
    }

    public function historialCreditoRefinanciamiento($idventa)
    {
        // Cabecera del crédito
        $sql = "SELECT
                v.idventa,
                CONCAT(v.tipo_comprobante,'-',v.serie_comprobante,'-',v.num_comprobante) documento,
                v.fecha_hora,
                v.total_venta,
                p.nombre as cliente,
                p.num_documento
            FROM venta v
            INNER JOIN persona p ON p.idpersona=v.idcliente
            WHERE v.idventa='$idventa'";

        $venta = ejecutarConsultaSimpleFila($sql);

        if (!$venta) {
            return json_encode([
                "estado" => false,
                "mensaje" => "No existe el crédito."
            ]);
        }

        $historial = [];

        /*=====================================================
        =            CRÉDITO ORIGINAL
        =====================================================*/

        $sql = "SELECT
                idcpc,
                deudatotal,
                deuda,
                abonototal,
                estado_pago,
                fecharegistro,
                fechavencimiento,
                idrefinanciamiento_origen
            FROM cuentas_por_cobrar
            WHERE idventa='$idventa'
            AND idrefinanciamiento IS NULL
            ORDER BY fechavencimiento";

        $rspta = ejecutarConsulta($sql);

        $cuotas = [];

        while ($row = $rspta->fetch_assoc()) {

            $row["estado"] = $row["idrefinanciamiento_origen"]
                ? "REFINANCIADA"
                : ($row["estado_pago"] == 0 ? "PAGADA" : "PENDIENTE");

            $cuotas[] = $row;
        }

        $historial[] = [
            "tipo" => "ORIGINAL",
            "titulo" => "Crédito Original",
            "cuotas" => $cuotas
        ];

        /*=====================================================
        =            REFINANCIAMIENTOS
        =====================================================*/

        $sql = "SELECT *
            FROM refinanciamientos
            WHERE idventa='$idventa'
            ORDER BY idrefinanciamiento ASC";

        $refinanciamientos = ejecutarConsulta($sql);

        while ($ref = $refinanciamientos->fetch_assoc()) {

            $sql = "SELECT
                    idcpc,
                    deudatotal,
                    deuda,
                    abonototal,
                    estado_pago,
                    fecharegistro,
                    fechavencimiento
                FROM cuentas_por_cobrar
                WHERE idrefinanciamiento='{$ref["idrefinanciamiento"]}'
                ORDER BY fechavencimiento";

            $detalle = ejecutarConsulta($sql);

            $cuotas = [];

            while ($c = $detalle->fetch_assoc()) {

                $c["estado"] = ($c["estado_pago"] == 0)
                    ? "PAGADA"
                    : "PENDIENTE";

                $cuotas[] = $c;
            }

            $historial[] = [
                "tipo" => "REFINANCIAMIENTO",
                "idrefinanciamiento" => $ref["idrefinanciamiento"],
                "fecha" => $ref["fecha_registro"],
                "saldo_original" => $ref["saldo_original"],
                "interes" => $ref["interes"],
                "inicial" => $ref["inicial"],
                "total_refinanciado" => $ref["total_refinanciado"],
                "cuotas" => $cuotas
            ];
        }

        return json_encode([
            "estado" => true,
            "venta" => $venta,
            "historial" => $historial
        ]);
    }
}