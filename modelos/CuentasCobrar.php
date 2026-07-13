<?php
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";
require "Contratos.php";
require_once "Helpers.php";
require_once "../configuraciones/ConexionPdo.php";
require_once "../core/FluentSave.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class CuentasCobrar extends Helpers
{

    private PDO $pdo;

    //Implementamos nuestro constructor
    public function __construct()
    {
        $this->pdo = Conexion::conectar();
    }

    public function insertar(
        $idcpc,
        $montopagado,
        $observacion,
        $banco,
        $op,
        $fechaPago,
        $formapago,
        $montoPagarTarjeta,
        $idcaja,
        $idpersonal,
        $idsucursal,
        $idusuario
    ) {

        ejecutarConsulta("START TRANSACTION");

        try {

            $fechaPago = $this->obtenerFechaPago($fechaPago);

            $this->validarCaja($idsucursal, $idusuario);

            $cuenta = $this->obtenerCuentaPorCobrar($idcpc);

            $cuenta = $this->actualizarMora($cuenta, $idcpc);

            $this->registrarDetallePago(
                $idcpc,
                $idcaja,
                $idpersonal,
                $montopagado,
                $montoPagarTarjeta,
                $banco,
                $op,
                $fechaPago,
                $formapago,
                $observacion
            );

            $resultado = $this->procesarPago(
                $cuenta,
                $montopagado,
                $montoPagarTarjeta,
                $fechaPago
            );

            $this->guardarCuenta($idcpc, $resultado);

            $this->actualizarEstadoVenta(
                $cuenta["idventa"],
                $idcpc,
                $resultado["deuda"]
            );

            ejecutarConsulta("COMMIT");

            return [
                "success" => true,
                "message" => "Pago registrado correctamente."
            ];

        } catch (Exception $e) {

            ejecutarConsulta("ROLLBACK");

            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    private function obtenerFechaPago($fechaPago)
    {
        return empty($fechaPago)
            ? date('Y-m-d H:i:s')
            : $fechaPago;
    }

    private function validarCaja($idsucursal, $idusuario)
    {

        $sql = "SELECT * FROM caja_apertura WHERE idusuario = '$idusuario' AND idsucursal = '$idsucursal' AND estado = 1 AND fecha_cierre IS NULL";
        $apertura = ejecutarConsulta($sql)->fetch_object();
        if (!$apertura) {
            throw new Exception("La caja está cerrada.");
        }
    }

    private function obtenerCuentaPorCobrar($idcpc)
    {
        $sql = "
        SELECT
            cc.*,
            v.idsucursal
        FROM cuentas_por_cobrar cc
        INNER JOIN venta v
            ON v.idventa=cc.idventa
        WHERE cc.idcpc='$idcpc'
        FOR UPDATE
    ";

        $fila = ejecutarConsultaSimpleFila($sql);

        if (!$fila) {
            throw new Exception("No se encontró la cuota.");
        }

        return $fila;
    }

    private function registrarDetallePago(
        $idcpc,
        $idcaja,
        $idpersonal,
        $montopagado,
        $montoTarjeta,
        $banco,
        $op,
        $fechaPago,
        $formapago,
        $observacion
    ) {

        $sql = "
    INSERT INTO detalle_cuentas_por_cobrar(
        idcpc,
        idcaja,
        idpersonal,
        montopagado,
        montotarjeta,
        banco,
        op,
        fechapago,
        formapago,
        observacion
    ) VALUES(
        '$idcpc',
        '$idcaja',
        '$idpersonal',
        '$montopagado',
        '$montoTarjeta',
        '$banco',
        '$op',
        '$fechaPago',
        '$formapago',
        '$observacion'
    )";

        if (!ejecutarConsulta($sql)) {
            throw new Exception("No se pudo registrar el pago.");
        }
    }

    private function actualizarMora($fila, $idcpc)
    {
        $config = Helpers::verificarMoraCredito($fila["idsucursal"]);

        if (
            !$config["activo"] ||
            floatval($fila["deuda"]) <= 0
        ) {
            return $fila;
        }

        $hoy = new DateTime();

        $ultimaFecha = !empty($fila["fecha_update_mora"])
            ? $fila["fecha_update_mora"]
            : $fila["fechavencimiento"];

        $fechaInicio = new DateTime($ultimaFecha);

        if ($hoy <= $fechaInicio) {
            return $fila;
        }

        $dias = $fechaInicio->diff($hoy)->days;

        if ($dias <= 0) {
            return $fila;
        }

        $moraNueva = round(
            floatval($fila["deuda"]) *
            ($config["valor"] / 100) *
            $dias,
            2
        );

        $fila["mora"] += $moraNueva;

        ejecutarConsulta("
        UPDATE cuentas_por_cobrar
        SET
            mora='{$fila["mora"]}',
            deudatotal='{$fila["deudatotal"]}',
            fecha_update_mora=NOW()
        WHERE idcpc='$idcpc'
    ");

        return $fila;
    }

    private function procesarPago($fila, $montoEfectivo, $montoTarjeta, $fechaPago)
    {
        $resultado = [

            "deuda" => round(floatval($fila["deuda"]), 2),

            "mora" => round(floatval($fila["mora"]), 2),

            "interes" => round(floatval($fila["interes"]), 2),

            "deudatotal" => round(floatval($fila["deudatotal"]), 2),

            "abonototal" => round(floatval($fila["abonototal"]), 2),

            "mora_pagada" => round(floatval($fila["mora_pagada"]), 2),

            "descuento" => 0
        ];

        $monto = round(
            Helpers::toFloat($montoEfectivo) +
            Helpers::toFloat($montoTarjeta),
            2
        );

        $monto = $this->pagarMora($resultado, $monto);

        $monto = $this->aplicarDescuento(
            $fila,
            $resultado,
            $monto,
            $fechaPago
        );

        $this->pagarCapital($resultado, $monto);


        return $resultado;
    }

    private function pagarMora(&$r, $monto)
    {

        if ($monto <= 0)
            return 0;

        if ($r["mora"] <= 0)
            return $monto;

        $pagado = min($monto, $r["mora"]);

        $r["mora"] -= $pagado;

        $r["mora_pagada"] += $pagado;

        return $monto - $pagado;
    }


    private function aplicarDescuento($fila, &$r, $montoPago, $fechaPago)
    {
        $config = Helpers::verificarDecuentoPagoAnticipado(
            $fila["idsucursal"]
        );

        if (
            !$config["activo"] ||
            $r["mora"] > 0 ||
            $r["deuda"] <= 0
        ) {
            return $montoPago;
        }

        $fechaPagoObj = new DateTime($fechaPago);
        $fechaVencimiento = new DateTime($fila["fechavencimiento"]);

        if ($fechaPagoObj > $fechaVencimiento) {
            return $montoPago;
        }

        $dias = $fechaPagoObj->diff($fechaVencimiento)->days;

        if ($dias < $config["dias_anticipacion"]) {
            return $montoPago;
        }

        $descuento = round(
            $r["deuda"] * ($config["valor"] / 100),
            2
        );

        // Sólo aplica si con el pago + descuento cancela el capital.
        if (($montoPago + $descuento) < $r["deuda"]) {
            return $montoPago;
        }

        $r["descuento"] = $descuento;
        $r["deuda"] -= $descuento;

        return $montoPago;
    }


    private function pagarCapital(&$r, $montoPago)
    {
        if ($montoPago <= 0 || $r["deuda"] <= 0) {
            return;
        }

        $pagado = min($montoPago, $r["deuda"]);

        $r["deuda"] -= $pagado;

        $r["abonototal"] += $pagado;
    }

    private function guardarCuenta($idcpc, $r)
    {

        ejecutarConsulta("
        UPDATE cuentas_por_cobrar
        SET
            deuda='{$r["deuda"]}',
            mora='{$r["mora"]}',
            mora_pagada='{$r["mora_pagada"]}',
            abonototal='{$r["abonototal"]}',
            descuento='{$r["descuento"]}',
            deudatotal='{$r["deudatotal"]}'
        WHERE idcpc='$idcpc'
    ");
    }

    private function actualizarEstadoVenta(
        $idventa,
        $idcpc,
        $deudaTotal
    ) {
        if ($deudaTotal <= 0) {

            ejecutarConsulta("
            UPDATE cuentas_por_cobrar
            SET estado_pago=0
            WHERE idcpc='$idcpc'
        ");

            $pendientes = ejecutarConsultaSimpleFila("
            SELECT COUNT(*) total
            FROM cuentas_por_cobrar
            WHERE idventa='$idventa'
            AND estado_pago=1
        ");

            if (intval($pendientes["total"]) == 0) {

                ejecutarConsulta("
                UPDATE documentacion
                SET estado=2
                WHERE idventa='$idventa'
                AND tipo='1'
            ");
            }

        } else {

            ejecutarConsulta("
            UPDATE cuentas_por_cobrar
            SET estado_pago=1
            WHERE idcpc='$idcpc'
        ");
        }
    }

    public function deudacliente($idventa)
    {

        $sql = "SELECT v.idventa,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,cc.idcpc,date_format(cc.fecharegistro,'%d/%m/%y') as fecharegistro, v.tipo_comprobante, c.nombre,TRUNCATE(cc.deudatotal + cc.abonototal,2) as deudatotal, cc.deudatotal as deuda, cc.abonototal,date_format(cc.fechavencimiento,'%d/%m/%y') as fechavencimiento 
				FROM venta v 
				INNER JOIN cuentas_por_cobrar cc
		        ON v.idventa = cc.idventa
		        INNER JOIN persona c
		        ON c.idpersona = v.idcliente
		        WHERE cc.idventa = '$idventa'";
        return ejecutarConsulta($sql);

    }

    public function listarSaldos($fecha_inicio, $fecha_fin, $idcliente, $idsucursal)
    {
        $filtroCliente = ($idcliente != "Todos" && $idcliente != null)
            ? "AND v.idcliente = '$idcliente'" : "";

        $filtroSucursal = ($idsucursal != "Todos" && $idsucursal != null && $idsucursal != "")
            ? "AND v.idsucursal = '$idsucursal'" : "";

        $sql = "SELECT 
                SUM(cpc.abonototal) AS abonototal,
                SUM(cpc.deudatotal) AS deudatotal,
                SUM(v.total_venta) AS totalventa,
                v.interes AS interes
            FROM cuentas_por_cobrar cpc
            INNER JOIN venta v ON cpc.idventa = v.idventa
            WHERE DATE(cpc.fecharegistro) >= '$fecha_inicio'
              AND DATE(cpc.fecharegistro) <= '$fecha_fin'
              AND cpc.condicion = '1'
              $filtroCliente
              $filtroSucursal";

        return ejecutarConsulta($sql)->fetch_object();
    }


    public function verSucursal($idsucursal)
    {
        $sql = "SELECT * FROM sucursal WHERE idsucursal = '$idsucursal'";
        $sucursal = ejecutarConsulta($sql)->fetch_object();
        if ($sucursal) {
            return $sucursal->nombre;
        } else {
            return "--";
        }
    }

    public function listar($fecha_inicio, $fecha_fin, $idcliente, $idsucursal)
    {
        $filtroCliente = ($idcliente != "Todos" && $idcliente != null)
            ? "AND v.idcliente = '$idcliente'"
            : "";
        $filtroSucursal = "";
        if (!empty($idsucursal) && $idsucursal != "Todos") {
            // Si viene como CSV (de select múltiple)
            if (strpos($idsucursal, ',') !== false) {
                $filtroSucursal = "AND v.idsucursal IN ($idsucursal)";
            } else {
                // Si es un solo valor
                $filtroSucursal = "AND v.idsucursal = '$idsucursal'";
            }
        }

        $sql = "SELECT 
                    cc.idcpc,
                    v.idsucursal,
                    v.idcliente,
                    DATE_FORMAT(cc.fecharegistro, '%d/%m/%y | %H:%i:%s %p') AS fecharegistro,
                    v.tipo_comprobante,
                    c.nombre,
                    cc.mora,
                    cc.deuda,
                    c.num_documento,
                    v.serie_comprobante,
                    v.num_comprobante,
                    cc.deudatotal,
                    cc.abonototal,
                    DATE_FORMAT(cc.fechavencimiento, '%d/%m/%y') AS fechavencimiento,
                    cc.idventa,
                    IFNULL((
                        SELECT 1
                        FROM recordatorio_envios r 
                        WHERE r.idcpc = cc.idcpc 
                          AND DATE(r.fecha_envio) = CURDATE()
                        LIMIT 1
                    ), 0) AS yaEnviadoHoy
                FROM venta v
                INNER JOIN cuentas_por_cobrar cc ON v.idventa = cc.idventa
                INNER JOIN persona c ON c.idpersona = v.idcliente
                WHERE DATE(cc.fecharegistro) >= '$fecha_inicio'
                  AND DATE(cc.fecharegistro) <= '$fecha_fin'
                  AND cc.condicion = '1'
                  $filtroCliente
                  $filtroSucursal
                ORDER BY cc.idcpc DESC";

        return ejecutarConsulta($sql);
    }

    //Implementar un método para listar los registros
    public function listarDetalle($idcpc)
    {
        $sql = "SELECT cc.iddcpc,cc.iddcpc,cc.montopagado,cc.montotarjeta,date_format(cc.fechapago,'%d/%m/%y | %H:%i:%s %p') as fechapago,cc.formapago,cc.banco,cc.op FROM detalle_cuentas_por_cobrar cc
				WHERE cc.idcpc = '$idcpc'
		        ORDER BY cc.iddcpc asc";
        return ejecutarConsulta($sql);
    }

    public function mostrar($idcpc)
    {
        $sql = "SELECT
            v.idventa,
            v.idsucursal,
            v.total_venta,
            v.interes,
            v.tipo_comprobante,
            v.serie_comprobante,
            v.num_comprobante,
            cc.idcpc,
            cc.deuda,
            cc.deudatotal,
            cc.abonototal,
            cc.mora,
            cc.mora_pagada,
            cc.interes AS interes_cuota,
            cc.fechavencimiento AS fecha_vencimiento_bd,
            DATE_FORMAT(cc.fecharegistro,'%d/%m/%y') AS fecharegistro,
            DATE_FORMAT(cc.fechavencimiento,'%d/%m/%y') AS fechavencimiento,
            c.nombre
        FROM venta v
        INNER JOIN cuentas_por_cobrar cc
            ON v.idventa = cc.idventa
        INNER JOIN persona c
            ON c.idpersona = v.idcliente
        WHERE cc.idcpc = '$idcpc'";

        $credito = ejecutarConsultaSimpleFila($sql);

        $moraCredito = Helpers::verificarMoraCredito($credito['idsucursal']);

        $diasMora = 0;
        $moraTotal = 0;
        $moraPendiente = 0;
        $hoy = new DateTime();
        $vence = new DateTime($credito["fecha_vencimiento_bd"]);
        if ($moraCredito["activo"] && $vence < $hoy) {
            $diasMora = $vence->diff($hoy)->days;

            // mora total generada
            $moraTotal = ($credito["deuda"] * ($moraCredito["valor"] / 100)) * $diasMora;

            // mora pendiente real (lo que falta pagar)
            $moraPendiente = max(0, $moraTotal - floatval($credito["mora_pagada"]));
        }

        $descuentoPago = Helpers::verificarDecuentoPagoAnticipado($credito['idsucursal']);
        $porcentajeDescuento = 0;
        $diasDescuento = 0;
        $descuentoTotal = 0;
        if ($descuentoPago["activo"] && $vence > $hoy) {
            $diasDescuento = $hoy->diff($vence)->days;
            if ($diasDescuento >= $descuentoPago["dias_anticipacion"]) {
                $porcentajeDescuento = $descuentoPago["valor"];
                $descuentoTotal = $credito["deuda"] * ($porcentajeDescuento / 100);
            }
        }

        // =========================
        // RESPUESTA CORRECTA
        // =========================
        $credito["dias_mora"] = $diasMora;
        $credito["mora"] = round($moraPendiente, 2);
        $credito["mora_total"] = round($moraTotal, 2);
        $credito["porcentaje_descuento"] = $porcentajeDescuento;
        $credito["dias_descuento"] = $diasDescuento;
        $credito["descuento_total"] = round($descuentoTotal, 2);

        // total correcto a pagar
        $credito["total_pagar"] = round(
            floatval($credito["deuda"]) + $moraPendiente - $descuentoTotal,
            2
        );

        return json_encode($credito);
    }

    public function calcularMora($idcpc)
    {
        $sql = "SELECT 
                    cc.deudatotal,
                    cc.deuda_base,
                    cc.fechavencimiento,
                    v.interes
                FROM cuentas_por_cobrar cc
                INNER JOIN venta v ON cc.idventa = v.idventa
                WHERE cc.idcpc = '$idcpc'";

        $fila = ejecutarConsultaSimpleFila($sql);

        if (!$fila) {
            return [
                'cuota_sin_mora' => 0.00,
                'dias_retraso' => 0,
                'mora' => 0.00,
                'total_con_mora' => 0.00
            ];
        }

        // Tomar deuda_base si existe, caso contrario usar deudatotal
        $base = isset($fila['deuda_base']) ? floatval($fila['deuda_base']) : 0;
        if ($base <= 0) {
            $base = floatval($fila['deudatotal']);
        }

        $interes = floatval($fila['interes']);
        $fechaVenc = $fila['fechavencimiento'];

        // Calcular la cuota con interés (base + interés si la venta tenía)
        $cuotaConInteres = $base;

        // Si no hay interés, no calcular mora
        if ($interes <= 0) {
            return [
                'cuota_sin_mora' => round($cuotaConInteres, 2),
                'dias_retraso' => 0,
                'mora' => 0.00,
                'total_con_mora' => round($cuotaConInteres, 2)
            ];
        }

        // Fechas sin horas
        $fechaV = new DateTime(date('Y-m-d', strtotime($fechaVenc)));
        $hoy = new DateTime(date('Y-m-d'));

        $diasRetraso = 0;
        if ($hoy > $fechaV) {
            $diasRetraso = (int) $hoy->diff($fechaV)->days;
        }

        // Si no hay días de retraso → no hay mora
        if ($diasRetraso <= 0) {
            return [
                'cuota_sin_mora' => round($cuotaConInteres, 2),
                'dias_retraso' => 0,
                'mora' => 0.00,
                'total_con_mora' => round($cuotaConInteres, 2)
            ];
        }

        // Calcular mora (10% mensual sobre cuotaConInteres)
        $porcMoraMes = 10.0;
        $moraDiaria = ($cuotaConInteres * ($porcMoraMes / 100)) / 30;
        $moraTotal = round($moraDiaria * $diasRetraso, 2);

        return [
            'cuota_sin_mora' => round($cuotaConInteres, 2),
            'dias_retraso' => $diasRetraso,
            'mora' => $moraTotal,
            'total_con_mora' => round($cuotaConInteres + $moraTotal, 2)
        ];
    }


    public function mostrarTicket($idventa)
    {

        $sql = "SELECT v.idventa,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,cc.idcpc,DATE(cc.fecharegistro) as fecharegistro, v.tipo_comprobante, c.nombre,TRUNCATE(cc.deudatotal,2) as deudatotal, cc.deudatotal as deuda, cc.abonototal,cc.fechavencimiento 
				FROM venta v 
				INNER JOIN cuentas_por_cobrar cc
		        ON v.idventa = cc.idventa
		        INNER JOIN persona c
		        ON c.idpersona = v.idcliente
		        WHERE cc.idventa = '$idventa'";
        return ejecutarConsulta($sql);

    }

    public function mostrarDeuda($idVenta)
    {
        $sql = "SELECT 
	                cc.idcpc,
	                cc.deudatotal,
	                cc.abonototal,
	                cc.fechavencimiento,
	                GREATEST((cc.deudatotal - cc.abonototal),0) AS saldo_pendiente,
	                COUNT(dcp.iddcpc) AS cuotas_pagadas
	            FROM cuentas_por_cobrar cc
	            LEFT JOIN detalle_cuentas_por_cobrar dcp 
	                ON cc.idcpc = dcp.idcpc
	            WHERE cc.idventa = '$idVenta'
	            GROUP BY cc.idcpc, cc.deudatotal, cc.abonototal
	            ORDER BY cc.idcpc ASC";
        return ejecutarConsulta($sql);
    }

    public function listarRecordatorioSemana()
    {
        $sql = "SELECT 
	                cc.idcpc, 
	                v.idventa, 
	                v.idcliente,
	                c.nombre, 
	                c.telefono, 
	                cc.deudatotal, 
	                cc.fechavencimiento
	            FROM cuentas_por_cobrar cc
	            INNER JOIN venta v ON v.idventa = cc.idventa
	            INNER JOIN persona c ON c.idpersona = v.idcliente
	            WHERE cc.condicion = '1'
	              AND DATE(cc.fechavencimiento) = DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
        return ejecutarConsulta($sql);
    }

    public function amortizarDeuda($deuda, $idcliente, $fecha_inicio, $fecha_fin, $formapago, $montopago, $idcaja, $idpersonal)
    {
        // Obtener cuotas del cliente ordenadas por fecha de vencimiento más cercana
        $sql3 = "SELECT cc.idcpc,
                        cc.deudatotal,
                        cc.deuda_base,
                        cc.mora,
                        cc.mora_pagada,
                        cc.fechavencimiento,
                        cc.fecharegistro,
                        v.tipo_comprobante,
                        c.nombre,
                        c.num_documento,
                        v.serie_comprobante,
                        v.num_comprobante,
                        cc.idventa
                 FROM venta v
                 INNER JOIN cuentas_por_cobrar cc ON v.idventa = cc.idventa
                 INNER JOIN persona c ON c.idpersona = v.idcliente
                 WHERE DATE(cc.fecharegistro) >= '$fecha_inicio'
                   AND DATE(cc.fecharegistro) <= '$fecha_fin'
                   AND v.idcliente = '$idcliente'
                   AND cc.condicion = 1
                 ORDER BY cc.fechavencimiento ASC, cc.fecharegistro ASC";

        $lista = ejecutarConsulta($sql3);

        $data = false;
        $pago = $montopago;
        $totalAmortizado = 0;

        while ($reg = $lista->fetch_object()) {

            if ($pago <= 0)
                break;
            $this->actualizarMoraDiaria($reg->idcpc);

            // Obtener deuda actualizada
            $filaAct = ejecutarConsultaSimpleFila("
                SELECT deuda_base, mora, deudatotal, mora_pagada, abonototal
                FROM cuentas_por_cobrar
                WHERE idcpc = '$reg->idcpc'
            ");

            $deuda_base = floatval($filaAct['deuda_base']);
            $mora = floatval($filaAct['mora']);
            $mora_pagada_total = floatval($filaAct['mora_pagada']);
            $abonototal_actual = floatval($filaAct['abonototal']);
            $deudaPendiente = floatval($filaAct['deudatotal']);

            if ($deudaPendiente <= 0)
                continue;
            $mora_pagada = min($pago, $mora);
            $mora -= $mora_pagada;
            $pago -= $mora_pagada;
            $capital_pagado = min($pago, $deuda_base);
            $deuda_base -= $capital_pagado;
            $pago -= $capital_pagado;
            $montoPagadoTotal = $capital_pagado + $mora_pagada;

            $sqlDetalle = "INSERT INTO detalle_cuentas_por_cobrar 
                            (idcpc, idcaja, idpersonal, montopagado, montotarjeta, banco, op, fechapago, formapago, observacion)
                           VALUES
                            ('$reg->idcpc', '$idcaja', '$idpersonal', '$montoPagadoTotal', 0, '', '', CURDATE(), '$formapago', 'AMORTIZACIÓN')";
            ejecutarConsulta($sqlDetalle);
            $nuevoTotal = round($deuda_base + $mora, 2);
            $abonototal_nuevo = $abonototal_actual + $montoPagadoTotal;

            $sqlUpdate = "UPDATE cuentas_por_cobrar
                          SET deuda_base       = '$deuda_base',
                              deudatotal       = '$nuevoTotal',
                              mora_pagada      = mora_pagada + '$mora_pagada',
                              abonototal       = '$abonototal_nuevo',
                              fecha_update_mora = CURDATE()
                          WHERE idcpc = '$reg->idcpc'";
            ejecutarConsulta($sqlUpdate);
            if ($nuevoTotal <= 0) {
                ejecutarConsulta("
                    UPDATE cuentas_por_cobrar
                    SET estado_pago = 0
                    WHERE idcpc = '$reg->idcpc'
                ");

                // ACTUALIZAR ESTADO DEL CONTRATO A FINALIZADO
                ejecutarConsulta("
                    UPDATE documentacion
                    SET estado = 'finalizado'
                    WHERE idventa = '{$reg->idventa}' AND tipo = '1'
                ");
            }

            $totalAmortizado += $montoPagadoTotal;
            $data = true;
        }

        if ($data) {
            return [
                'success' => true,
                'message' => "Se amortizó correctamente S/ " . number_format($totalAmortizado, 2)
            ];
        } else {
            return [
                'success' => false,
                'message' => "No se realizó ninguna amortización"
            ];
        }
    }


    public function verificarYActualizarEstadoContrato($idventa)
    {
        // Verificar si todas las cuotas de la venta están pagadas
        $sql = "SELECT COUNT(*) as total_cuotas,
                       SUM(CASE WHEN estado_pago = 0 THEN 1 ELSE 0 END) as cuotas_pagadas
                FROM cuentas_por_cobrar
                WHERE idventa = '$idventa' AND condicion = 1";

        $resultado = ejecutarConsultaSimpleFila($sql);

        if ($resultado && $resultado['total_cuotas'] > 0) {
            // Si todas las cuotas están pagadas
            if ($resultado['total_cuotas'] == $resultado['cuotas_pagadas']) {
                ejecutarConsulta("UPDATE documentacion
                                SET estado = 'finalizado'
                                WHERE idventa = '$idventa' AND tipo = '1'");
                return true;
            }
        }
        return false;
    }

    public function actualizarMoraDiaria($idcpc)
    {
        $sqlInteres = "
            SELECT v.interes
            FROM cuentas_por_cobrar cc
            INNER JOIN venta v ON cc.idventa = v.idventa
            WHERE cc.idcpc = '$idcpc'
        ";
        $venta = ejecutarConsultaSimpleFila($sqlInteres);

        // SIN INTERÉS → NO APLICA MORA
        if (!$venta || floatval($venta['interes']) <= 0) {
            ejecutarConsulta("
                UPDATE cuentas_por_cobrar
                SET mora = 0,
                    deudatotal = deuda_base
                WHERE idcpc = '$idcpc'
            ");
            return true;
        }
        // Obtener datos actuales
        $sql = "SELECT deuda_base, deudatotal, fechavencimiento
                FROM cuentas_por_cobrar
                WHERE idcpc = '$idcpc'";

        $fila = ejecutarConsultaSimpleFila($sql);
        if (!$fila)
            return false;

        $estado = ejecutarConsultaSimpleFila("SELECT estado_pago FROM cuentas_por_cobrar WHERE idcpc='$idcpc'");

        if ($estado && $estado['estado_pago'] == 0) {
            // Ya pagado → aseguramos que NO genere mora nunca más
            ejecutarConsulta("UPDATE cuentas_por_cobrar
                              SET 
                                  deudatotal = 0
                              WHERE idcpc = '$idcpc'");
            return true;
        }

        $deuda_base = floatval($fila['deuda_base']);
        $deuda_total_actual = floatval($fila['deudatotal']);
        $fechaVenc = $fila['fechavencimiento'];

        // Si deuda_base está vacía → asignarla
        if ($deuda_base <= 0) {
            $deuda_base = $deuda_total_actual;
            ejecutarConsulta("UPDATE cuentas_por_cobrar SET deuda_base = '$deuda_base' WHERE idcpc = '$idcpc'");
        }

        // Si aún así queda en cero → no hacer nada
        if ($deuda_base <= 0)
            return true;

        // Calcular días vencidos
        $fechaV = new DateTime(date('Y-m-d', strtotime($fechaVenc)));
        $hoy = new DateTime(date('Y-m-d'));

        if ($hoy <= $fechaV) {
            // No vencido, no hay mora
            ejecutarConsulta("UPDATE cuentas_por_cobrar 
                              SET deudatotal = '$deuda_base' 
                              WHERE idcpc = '$idcpc'");
            return true;
        }

        // Días de retraso
        $diasRetraso = (int) $hoy->diff($fechaV)->days;

        // Mora diaria = 10% mensual / 30
        $moraDiaria = ($deuda_base * 0.10) / 30;
        $moraTotal = round($moraDiaria * $diasRetraso, 2);

        // Nuevo total
        $nuevoTotal = round($deuda_base + $moraTotal, 2);

        ejecutarConsulta("UPDATE cuentas_por_cobrar
                          SET mora = '$moraTotal',
                              deudatotal = '$nuevoTotal'
                          WHERE idcpc = '$idcpc'");

        return true;
    }

    public function enviarRecordatorioWhatsApp($idcpc = null)
    {
        // Traer cuotas vencidas
        $sql = "SELECT cc.idcpc, v.idcliente, c.nombre, c.telefono, cc.fechavencimiento
                FROM cuentas_por_cobrar cc
                INNER JOIN venta v ON v.idventa = cc.idventa
                INNER JOIN persona c ON c.idpersona = v.idcliente
                WHERE cc.condicion = 1";

        if ($idcpc !== null) {
            $sql .= " AND cc.idcpc = '$idcpc'";
        } else {
            $sql .= " AND DATE(cc.fechavencimiento) < CURDATE()"; // solo vencidas
        }

        $rspta = ejecutarConsulta($sql);

        $clientes = [];

        // Agrupar cuotas por cliente
        while ($reg = $rspta->fetch_object()) {

            $clienteId = $reg->idcliente;

            // Calcular mora actualizada de esta cuota
            $moraData = $this->calcularMora($reg->idcpc);

            $cuotaBase = round($moraData['cuota_sin_mora'], 2);
            $mora = round($moraData['mora'], 2);
            $totalConMora = round($moraData['total_con_mora'], 2);
            $diasRetraso = $moraData['dias_retraso'];

            if ($totalConMora <= 0)
                continue; // No enviar si ya está pagado

            // Verificar si ya se envió hoy
            $yaEnviado = ejecutarConsultaSimpleFila(
                "SELECT id FROM recordatorio_envios 
                 WHERE idcpc = '{$reg->idcpc}' 
                 LIMIT 1"
            );
            if ($yaEnviado)
                continue;

            // Agrupar por cliente
            $clientes[$clienteId]['nombre'] = $reg->nombre;
            $clientes[$clienteId]['telefono'] = $reg->telefono;
            $clientes[$clienteId]['cuotas'][] = [
                'idcpc' => $reg->idcpc,
                'fechavenc' => $reg->fechavencimiento,
                'cuotaBase' => $cuotaBase,
                'mora' => $mora,
                'totalConMora' => $totalConMora,
                'diasRetraso' => $diasRetraso
            ];
        }

        $customers = [];
        $idsEnviados = [];

        // Generar mensaje por cliente
        foreach ($clientes as $idcliente => $info) {

            $mensaje = "Hola {$info['nombre']}, le recordamos que tiene las siguientes cuotas vencidas:\n\n";

            $totalCliente = 0;

            foreach ($info['cuotas'] as $cuota) {
                $mensaje .= "- Cuota vencida el {$cuota['fechavenc']}: S/ {$cuota['cuotaBase']}, Mora: S/ {$cuota['mora']} ({$cuota['diasRetraso']} días), Total: S/ {$cuota['totalConMora']}\n";
                $totalCliente += $cuota['totalConMora'];

                // Registrar envío por cada cuota
                ejecutarConsulta("INSERT INTO recordatorio_envios (idcpc, idcliente) VALUES ('{$cuota['idcpc']}', '$idcliente')");
                $idsEnviados[] = $cuota['idcpc'];
            }

            $mensaje .= "\nTotal a pagar: S/ " . number_format($totalCliente, 2) . ". Por favor realizar el pago.";

            // Preparar teléfono
            $telefono = preg_replace('/[^0-9]/', '', $info['telefono']);
            if (substr($telefono, 0, 2) != '51') {
                $telefono = '51' . ltrim($telefono, '0');
            }

            $customers[] = [
                "phone" => $telefono,
                "message" => $mensaje
            ];
        }

        // Enviar al API si hay clientes
        if (!empty($customers)) {
            $data = ["lsCustomers" => $customers];
            $ch = curl_init("http://161.132.41.205:3001/lead");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                return ['success' => false, 'message' => "Error cURL: $err"];
            }

            return ['success' => true, 'message' => count($idsEnviados) . " recordatorios enviados.", 'response' => json_decode($response, true)];
        }

        return ['success' => true, 'message' => "No hay cuotas pendientes para enviar recordatorio.", 'response' => []];
    }

    public function generarNotificaciones($idsucursal)
    {
        $hoy = date('Y-m-d');

        $sql = "
        SELECT 
            cc.idcpc,
            c.nombre AS cliente,
            cc.deudatotal,
            cc.fechavencimiento,
            DATEDIFF('$hoy', cc.fechavencimiento) AS dias_vencido
        FROM cuentas_por_cobrar cc
        INNER JOIN venta v ON v.idventa = cc.idventa
        INNER JOIN persona c ON c.idpersona = v.idcliente
        WHERE cc.estado_pago = 1
          AND cc.condicion = 1
          AND v.idsucursal = '$idsucursal'
          AND cc.fechavencimiento <= '$hoy'
        ORDER BY cc.fechavencimiento ASC
    ";

        $rspta = ejecutarConsulta($sql);
        $notificaciones = [];

        while ($r = $rspta->fetch_object()) {

            $monto = number_format($r->deudatotal, 2);
            $fecha = date("d/m/Y", strtotime($r->fechavencimiento));

            if ($r->dias_vencido > 0) {
                $mensaje = "💸 <b>{$r->cliente}</b> tiene una cuota vencida hace 
                        <b>{$r->dias_vencido} día(s)</b> por 
                        <b>S/ {$monto}</b> (venció el {$fecha})";
            } else {
                $mensaje = "⏰ <b>{$r->cliente}</b> tiene una cuota que 
                        <b>vence HOY</b> por <b>S/ {$monto}</b>";
            }

            $notificaciones[] = [
                'idcpc' => $r->idcpc,
                'mensaje' => $mensaje,
                'fecha' => $fecha,
                'tipo' => '' // cuota
            ];
        }

        return $notificaciones;
    }


    public function estadoCuentaDocumento($idcpc)
    {
        $sql = "
        SELECT
            cc.fecharegistro AS fecha,
            CONCAT(v.tipo_comprobante,'-',v.serie_comprobante,'-',v.num_comprobante) AS documento,

            cc.deuda_base,
            IFNULL(cc.interes,0) AS interes,
            0 AS mora,
            IFNULL(cc.descuento,0) AS descuento,
            0 AS pago,

            'VENTA' AS tipo,
            1 AS orden

        FROM cuentas_por_cobrar cc
        INNER JOIN venta v
            ON v.idventa = cc.idventa
        WHERE cc.idcpc='$idcpc'

        UNION ALL

        SELECT
            cc.fecha_update_mora AS fecha,
            'MORA' AS documento,

            0 AS deuda_base,
            0 AS interes,
            IFNULL(cc.mora_pagada,0) AS mora,
            0 AS descuento,
            0 AS pago,

            'MORA' AS tipo,
            2 AS orden

        FROM cuentas_por_cobrar cc
        WHERE cc.idcpc='$idcpc'
        AND cc.mora_pagada>0

        UNION ALL

        SELECT
            dcc.fechapago AS fecha,
            'ABONO' AS documento,

            0 AS deuda_base,
            0 AS interes,
            0 AS mora,
            0 AS descuento,
            (dcc.montopagado+dcc.montotarjeta) AS pago,

            'PAGO' AS tipo,
            3 AS orden

        FROM detalle_cuentas_por_cobrar dcc
        WHERE dcc.idcpc='$idcpc'

        ORDER BY fecha, orden
    ";

        $rspta = ejecutarConsulta($sql);

        $saldo = 0;

        $html = '
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th>Fecha</th>
                    <th>Documento</th>
                    <th>Deuda</th>
                    <th>Interés</th>
                    <th>Mora</th>
                    <th>Descuento</th>
                    <th>Pago</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
    ';

        while ($r = $rspta->fetch_object()) {

            $movimiento =
                $r->deuda_base +
                $r->interes +
                $r->mora -
                $r->descuento -
                $r->pago;

            $saldo += $movimiento;

            $html .= '
            <tr>
                <td>' . $r->fecha . '</td>
                <td>' . $r->documento . '</td>

                <td class="text-right">' . ($r->deuda_base > 0 ? 'S/ ' . number_format($r->deuda_base, 2) : '-') . '</td>

                <td class="text-right">' . ($r->interes > 0 ? 'S/ ' . number_format($r->interes, 2) : '-') . '</td>

                <td class="text-right">' . ($r->mora > 0 ? 'S/ ' . number_format($r->mora, 2) : '-') . '</td>

                <td class="text-right">' . ($r->descuento > 0 ? 'S/ ' . number_format($r->descuento, 2) : '-') . '</td>

                <td class="text-right">' . ($r->pago > 0 ? 'S/ ' . number_format($r->pago, 2) : '-') . '</td>

                <td class="text-right">
                    <strong>S/ ' . number_format($saldo, 2) . '</strong>
                </td>
            </tr>
        ';
        }

        $html .= '
            </tbody>
        </table>
    </div>';

        return $html;
    }

    public function estadoCuentaCliente($idcliente, $fecha_inicio, $fecha_fin)
    {
        /* ========= CLIENTE ========= */
        $cliente = ejecutarConsultaSimpleFila("
        SELECT nombre, num_documento
        FROM persona
        WHERE idpersona = '$idcliente'
    ");

        /* ========= VENTAS ========= */
        $ventas = ejecutarConsulta("
        SELECT *
        FROM venta
        WHERE idcliente = '$idcliente'
        AND DATE(fecha_hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'
        AND estado IN ('Activado','Aceptado','Por Enviar')
        AND tipo_comprobante IN ('Factura','Boleta','Nota de Venta')
        ORDER BY fecha_hora ASC
    ");

        $totalDebe = 0;
        $totalHaber = 0;
        $saldoGeneral = 0;

        $html = "
        <div class='card mb-3 shadow-sm'>
            <div class='card-body'>
                <div class='row align-items-center'>
                    <div class='col-md-7'>
                        <h5 class='mb-1 text-primary'>
                            <i class='fas fa-user'></i> {$cliente['nombre']}
                        </h5>
                        <small class='text-muted'>
                            DNI / RUC: {$cliente['num_documento']}
                        </small>
                    </div>
                    <div class='col-md-5 text-right'>
                        <div class='text-muted'>Periodo del Estado de Cuenta</div>
                        <span class='badge badge-secondary'>$fecha_inicio</span>
                        <span class='mx-1'>—</span>
                        <span class='badge badge-secondary'>$fecha_fin</span>
                    </div>
                </div>
            </div>
        </div>

        <table class='table table-bordered table-sm'>
            <thead class='bg-primary text-white'>
                <tr>
                    <th>Fecha</th>
                    <th>Documento</th>
                    <th class='text-right'>Debe</th>
                    <th class='text-right'>Haber</th>
                    <th class='text-right'>Saldo</th>
                </tr>
            </thead>
            <tbody>
    ";

        while ($v = $ventas->fetch_object()) {

            /* ====== DATOS VENTA ====== */
            $docVenta = "{$v->tipo_comprobante}-{$v->serie_comprobante}-{$v->num_comprobante}";
            $saldoVenta = $v->total_venta;

            /* ====== VENTA (DEBE) ====== */
            $totalDebe += $v->total_venta;
            $saldoGeneral += $v->total_venta;

            $html .= "
            <tr style='background:#eef'>
                <td>{$v->fecha_hora}</td>
                <td><b>VENTA $docVenta</b></td>
                <td class='text-right'>S/ " . number_format($v->total_venta, 2) . "</td>
                <td class='text-right'>S/ 0.00</td>
                <td class='text-right'><b>S/ " . number_format($saldoVenta, 2) . "</b></td>
            </tr>
        ";

            /* ====== ANTICIPO (montoPagado) ====== */
            if ($v->montoPagado > 0) {

                $anticipo = $v->montoPagado;

                $saldoVenta -= $anticipo;
                $saldoGeneral -= $anticipo;

                if ($saldoVenta < 0)
                    $saldoVenta = 0;
                if ($saldoGeneral < 0)
                    $saldoGeneral = 0;

                $totalHaber += $anticipo;

                $html .= "
                <tr>
                    <td>{$v->fecha_hora}</td>
                    <td style='padding-left:30px;color:#0d6efd'>
                        ↳ ANTICIPO $docVenta
                    </td>
                    <td class='text-right'>S/ 0.00</td>
                    <td class='text-right'>S/ " . number_format($anticipo, 2) . "</td>
                    <td class='text-right'><b>S/ " . number_format($saldoVenta, 2) . "</b></td>
                </tr>
            ";
            }

            /* ====== CUOTAS / ABONOS ====== */
            $cpcs = ejecutarConsulta("
            SELECT idcpc
            FROM cuentas_por_cobrar
            WHERE idventa = '$v->idventa'
            AND condicion = 1
        ");

            while ($cc = $cpcs->fetch_object()) {

                $abonos = ejecutarConsulta("
                SELECT fechapago, montopagado, montotarjeta
                FROM detalle_cuentas_por_cobrar
                WHERE idcpc = '$cc->idcpc'
                ORDER BY fechapago ASC
            ");

                while ($ab = $abonos->fetch_object()) {

                    $montoAbono = $ab->montopagado + $ab->montotarjeta;

                    $saldoVenta -= $montoAbono;
                    $saldoGeneral -= $montoAbono;

                    if ($saldoVenta < 0)
                        $saldoVenta = 0;
                    if ($saldoGeneral < 0)
                        $saldoGeneral = 0;

                    $totalHaber += $montoAbono;

                    $html .= "
                    <tr>
                        <td>{$ab->fechapago}</td>
                        <td style='padding-left:30px;color:green'>
                            ↳ ABONO $docVenta
                        </td>
                        <td class='text-right'>S/ 0.00</td>
                        <td class='text-right'>S/ " . number_format($montoAbono, 2) . "</td>
                        <td class='text-right'><b>S/ " . number_format($saldoVenta, 2) . "</b></td>
                    </tr>
                ";
                }
            }

            /* ====== SALDO FINAL DE LA VENTA ====== */
            $html .= "
            <tr style='background:#f9f9f9'>
                <td colspan='4' class='text-right'><b>Saldo Venta</b></td>
                <td class='text-right'><b>S/ " . number_format($saldoVenta, 2) . "</b></td>
            </tr>
        ";
        }

        /* ====== TOTALES ====== */
        $html .= "
            </tbody>
            <tfoot class='bg-light'>
                <tr>
                    <th colspan='2' class='text-right'>TOTALES</th>
                    <th class='text-right'>S/ " . number_format($totalDebe, 2) . "</th>
                    <th class='text-right'>S/ " . number_format($totalHaber, 2) . "</th>
                    <th class='text-right'><b>S/ " . number_format($saldoGeneral, 2) . "</b></th>
                </tr>
            </tfoot>
        </table>
    ";

        return $html;
    }


    public function listaVentasPorCliente($idcliente, $idsucursal, $fecha_inicio, $fecha_fin)
    {
        $filtroSucursal = "";
        if (!empty($idsucursal) && $idsucursal != "Todos" && $idsucursal != "null") {
            $filtroSucursal = "AND v.idsucursal = '$idsucursal'";
        }

        $sql = "SELECT
                v.idventa,
                DATE_FORMAT(v.fecha_hora, '%d/%m/%y | %H:%i:%s %p') AS fecha_venta,
                v.tipo_comprobante,
                v.serie_comprobante,
                v.num_comprobante,
                v.total_venta,
                v.nota,
                v.estado_venta,
                v.interes,
                v.totalrecibido,
                v.totaldeposito
            FROM venta v
            WHERE v.idcliente='$idcliente'
              AND DATE(v.fecha_hora) BETWEEN '$fecha_inicio' AND '$fecha_fin'
              $filtroSucursal
            ORDER BY v.idventa DESC";

        $result = ejecutarConsulta($sql);

        $data = array();

        while ($row = $result->fetch_object()) {

            // ¿Tiene refinanciamiento?
            $sqlRef = "SELECT MAX(idrefinanciamiento) idref
                   FROM cuentas_por_cobrar
                   WHERE idventa='$row->idventa'
                     AND idrefinanciamiento IS NOT NULL";

            $ref = ejecutarConsultaSimpleFila($sqlRef);

            if (!empty($ref["idref"])) {

                $refinanciado = true;

                $sqlSaldo = "SELECT
                            SUM(abonototal) abonado,
                            SUM(deuda) deuda,
                            MAX(condicion) condicion
                         FROM cuentas_por_cobrar
                         WHERE idrefinanciamiento='{$ref["idref"]}'";

            } else {

                $refinanciado = false;

                $sqlSaldo = "SELECT
                            SUM(abonototal) abonado,
                            SUM(deuda) deuda,
                            MAX(condicion) condicion
                         FROM cuentas_por_cobrar
                         WHERE idventa='$row->idventa'
                           AND idrefinanciamiento IS NULL
                           AND idrefinanciamiento_origen IS NULL";
            }

            $credito = ejecutarConsultaSimpleFila($sqlSaldo);

            $totalAbonado = floatval($credito["abonado"]);
            $saldoPendiente = floatval($credito["deuda"]);

            $contratos = new Contratos();
            $retension = $contratos->buscarRetencion($row->idventa);
            $estadoRetension = $retension['estado'];

            $estado = ($saldoPendiente <= 0)
                ? '<center><span class="badge bg-green">Cancelado</span></center>'
                : '<center><span class="badge bg-red">Por Cancelar</span></center>';

            if ($estadoRetension === true) {
                $estado = '<center><span class="badge bg-orange">Retenido</span></center>';
            }

            if (!$row->estado_venta) {
                $estado = '<center><span class="badge bg-danger">Anulado</span></center>';
            }

            $doc = $row->tipo_comprobante . '-' . $row->serie_comprobante . '-' . $row->num_comprobante;

            $buttons = "";

            $buttons .= "<button class='btn btn-sm btn-success'
                        title='Ver cuotas'
                        onclick='verCuotasCredito({$row->idventa}, {$saldoPendiente}, \"{$doc}\", \"{$row->nota}\")'>
                        <i class='fas fa-list'></i>
                    </button> ";

            if ($saldoPendiente > 0 && Helpers::getUserPermissionAccion('Amortizar deuda')) {

                $buttons .= "<button class='btn btn-sm btn-primary'
                            title='Amortizar deuda'
                            onclick='amortizarCuotasCredito({$row->idventa}, {$saldoPendiente}, \"{$doc}\", \"{$row->nota}\")'>
                            <i class='fas fa-hand-holding-usd'></i>
                        </button> ";
            }

            $buttons .= "<button class='btn btn-sm btn-warning'
                        title='Ver calendario'
                        onclick='calendarioCuotasCredito({$row->idventa}, {$saldoPendiente}, \"{$doc}\", \"{$row->nota}\")'>
                        <i class='fas fa-calendar'></i>
                    </button> ";

            if ($refinanciado) {
                $buttons .= "<button class='btn btn-sm btn-info'
                        title='Ver historial refinaciamientos'
                        onclick='historialCreditoRefinanciamiento({$row->idventa}, {$saldoPendiente}, \"{$doc}\", \"{$row->nota}\")'>
                        <i class='fas fa-history'></i>
                    </button>";
            }

            $recibido = floatval($row->totalrecibido) + floatval($row->totaldeposito);

            $interes = ($row->total_venta - $recibido) * (floatval($row->interes) / 100);

            $badgeRef = $refinanciado
                ? "<span class='badge bg-blue'>Sí</span>"
                : "<span class='badge bg-default'>No</span>";

            $data[] = array(
                "0" => $row->fecha_venta,
                "1" => $doc,
                "2" => number_format($row->total_venta, 2),
                "3" => number_format($recibido, 2),
                "4" => ($row->interes)
                    ? number_format($interes, 2) . " <span class='badge badge-info'>{$row->interes}%</span>"
                    : "0.00",
                "5" => number_format($totalAbonado, 2),
                "6" => number_format($saldoPendiente, 2),
                "7" => $badgeRef,
                "8" => $estado,
                "9" => $buttons
            );
        }

        return json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
    }

    public function listaCuotasPorCredito($idventa)
    {
        // Buscar si el crédito tiene refinanciamiento
        $sql = "SELECT MAX(idrefinanciamiento) AS idref
            FROM cuentas_por_cobrar
            WHERE idventa = '$idventa'
              AND idrefinanciamiento IS NOT NULL";

        $ref = ejecutarConsultaSimpleFila($sql);

        if (!empty($ref["idref"])) {

            // Mostrar únicamente las cuotas del último refinanciamiento
            $sql = "SELECT
                    cc.idcpc,
                    DATE_FORMAT(cc.fecharegistro, '%d/%m/%y | %H:%i:%s %p') AS fecha_registro,
                    DATE_FORMAT(cc.fechavencimiento, '%d/%m/%y') AS fecha_vencimiento,
                    DATE(cc.fechavencimiento) AS fecha_venc_raw,
                    cc.abonototal,
                    cc.deudatotal,
                    cc.deuda,
                    cc.estado_pago,
                    cc.mora,
                    cc.idventa,
                    v.idcliente
                FROM cuentas_por_cobrar cc
                INNER JOIN venta v ON v.idventa = cc.idventa
                WHERE cc.idventa = '$idventa'
                  AND cc.idrefinanciamiento = '{$ref["idref"]}'
                  AND cc.condicion = '1'
                ORDER BY cc.fechavencimiento";

        } else {

            // Mostrar cuotas originales
            $sql = "SELECT
                    cc.idcpc,
                    DATE_FORMAT(cc.fecharegistro, '%d/%m/%y | %H:%i:%s %p') AS fecha_registro,
                    DATE_FORMAT(cc.fechavencimiento, '%d/%m/%y') AS fecha_vencimiento,
                    DATE(cc.fechavencimiento) AS fecha_venc_raw,
                    cc.abonototal,
                    cc.deudatotal,
                    cc.deuda,
                    cc.estado_pago,
                    cc.mora,
                    cc.mora_pagada,
                    cc.descuento,
                    cc.idventa,
                    v.idcliente
                FROM cuentas_por_cobrar cc
                INNER JOIN venta v ON v.idventa = cc.idventa
                WHERE cc.idventa = '$idventa'
                  AND cc.idrefinanciamiento IS NULL
                  AND cc.idrefinanciamiento_origen IS NULL
                  AND cc.condicion = '1'
                ORDER BY cc.fechavencimiento";

        }

        $result = ejecutarConsulta($sql);

        $data = array();
        $rows = array();
        $hoy = new DateTime(date('Y-m-d'));
        $proximaIdcpc = null;
        $minDias = PHP_INT_MAX;
        $acciones = '';

        while ($row = $result->fetch_object()) {

            $saldo = floatval($row->deuda);
            $row->saldo_calculado = $saldo;
            $rows[] = $row;

            if ($saldo > 0 && !empty($row->fecha_venc_raw)) {

                $fechaVenc = new DateTime($row->fecha_venc_raw);

                if ($fechaVenc >= $hoy) {

                    $dias = (int) $hoy->diff($fechaVenc)->days;

                    if ($dias < $minDias) {
                        $minDias = $dias;
                        $proximaIdcpc = $row->idcpc;
                    }
                }
            }
        }

        foreach ($rows as $row) {

            $saldo = floatval($row->saldo_calculado);

            $contratos = new Contratos();
            $retension = $contratos->buscarRetencion($row->idventa);
            $estadoRetension = $retension['estado'];

            $estado = ($saldo <= 0)
                ? '<center><span class="badge bg-green">Cancelado</span></center>'
                : '<center><span class="badge bg-red">Por Cancelar</span></center>';

            if ($estadoRetension === true) {
                $estado = '<center><span class="badge bg-orange">Retenido</span></center>';
            }

            if ($saldo > 0 && $estadoRetension !== true) {

                if (Helpers::getUserPermissionAccion('Crear abono')) {

                    $acciones .= "
                    <button
                        type='button'
                        class='btn btn-sm btn-success'
                        onclick='mostrar({$row->idcpc})'
                        title='Crear abono'>
                        <i class='fas fa-plus-circle'></i>
                    </button>";
                }

                if (Helpers::getUserPermissionAccion('Programar visita')) {

                    $acciones .= "
                    <button
                        type='button'
                        class='btn btn-sm btn-warning'
                        onclick='programarVisita({$row->idcpc}, {$row->idventa}, {$row->idcliente})'
                        title='Programar visita'>
                        <i class='fas fa-calendar-check'></i>
                    </button>";
                }

                if (
                    Helpers::getUserPermissionAccion('Programar compromiso de pago') &&
                    $row->fecha_venc_raw < date('Y-m-d')
                ) {

                    $acciones .= "
                <button
                    type='button'
                    class='btn btn-sm btn-danger'
                    onclick='programarCompromiso({$row->idcpc}, {$row->idventa}, {$row->idcliente})'
                    title='Programar compromiso de pago'>
                    <i class='fas fa-file-signature'></i>
                </button>";
                }
            }

            if (Helpers::getUserPermissionAccion('Ver abonos')) {

                $acciones .= "
                <button
                    type='button'
                    class='btn btn-sm btn-info'
                    onclick='mostrarAbonos({$row->idcpc})'
                    title='Ver abonos'>
                    <i class='fas fa-money-check-alt'></i>
                </button>";
            }

            if (Helpers::getUserPermissionAccion('Ver estado de cuenta')) {

                $acciones .= "
                <button
                    type='button'
                    class='btn btn-sm btn-secondary'
                    onclick='verEstadoCuenta({$row->idcpc})'
                    title='Ver estado de cuenta'>
                    <i class='fas fa-file-invoice-dollar'></i>
                </button>";
            }

            $rowClass = '';

            if ($saldo > 0 && !empty($row->fecha_venc_raw)) {

                $fechaVenc = new DateTime($row->fecha_venc_raw);

                if ($fechaVenc < $hoy) {
                    $rowClass = 'fila-cuota-vencida';
                } elseif ($proximaIdcpc == $row->idcpc) {
                    $rowClass = 'fila-cuota-proxima';
                }
            }

            if ($estadoRetension === true) {
                $rowClass = 'fila-retenida';
            }


            $data[] = array(
                "DT_RowClass" => $rowClass,
                "0" => $row->fecha_registro,
                "1" => $row->fecha_vencimiento,
                "2" => number_format($row->abonototal + $row->mora_pagada + $row->descuento, 2),
                "3" => number_format($row->deuda, 2),
                "4" => number_format($saldo, 2),
                "5" => $estado,
                "6" => $acciones
            );

            $acciones = '';
        }

        return json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
    }

    public function listaCreditos($idsucursal, $fecha_inicio, $fecha_fin, $idcliente = null)
    {
        $filtroSucursal = "";
        if (!empty($idsucursal) && $idsucursal != "Todos" && $idsucursal != "null") {
            $filtroSucursal = "AND v.idsucursal = '$idsucursal'";
        }

        $filtroCliente = "";
        if (!empty($idcliente) && $idcliente != "Todos" && $idcliente != "null") {
            $filtroCliente = "AND cl.idpersona = '$idcliente'";
        }

        $sql = "SELECT 
                    cl.idpersona,
                    cl.nombre AS cliente,
                    COUNT(DISTINCT v.idventa) AS total_creditos,
                    SUM(c.deudatotal) AS deuda_total,
                    SUM(c.abonototal) AS total_pagado,
                    SUM(c.deudatotal - c.abonototal) AS saldo_pendiente,
                    cl.latitude,
                    cl.longitude,
                    cl.direccion
                FROM persona cl
                INNER JOIN venta v ON v.idcliente = cl.idpersona
                INNER JOIN cuentas_por_cobrar c ON c.idventa = v.idventa
                WHERE DATE(c.fecharegistro) BETWEEN '$fecha_inicio' AND '$fecha_fin'
                  AND c.condicion = '1'
                  $filtroSucursal
                  $filtroCliente
                GROUP BY cl.idpersona, cl.nombre
                ORDER BY cl.nombre ASC";
        $result = ejecutarConsulta($sql);

        $data = array();
        $count = 1;
        while ($row = $result->fetch_object()) {
            $nombreCliente = addslashes($row->cliente);
            $data[] = array(
                "0" => $count++,
                "1" => $row->cliente,
                "2" => $row->total_creditos,
                "3" => number_format($row->deuda_total, 2),
                "4" => number_format($row->total_pagado, 2),
                "5" => number_format($row->saldo_pendiente, 2),
                "6" => "
                        <button class='btn btn-sm btn-success'
                            onclick='verDetalleCliente({$row->idpersona}, " . json_encode($nombreCliente) . ")'>
                            <i class='fas fa-eye'></i> Ver Detalle
                        </button>

                        <button class='btn btn-info btn-sm'
                            onclick='verUbicacionCliente(
                                " . json_encode($row->latitude) . ",
                                " . json_encode($row->longitude) . ",
                                " . json_encode($row->direccion) . "
                            )'
                            title='Ver ubicación del cliente'>
                            <i class='fas fa-search-location'></i> Ubicación
                        </button>
                        "
            );
        }

        return json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
    }

    public function amortizarDeudaVenta($idsucursal, $idventa, $formapago, $montopago, $idcaja, $idpersonal, $idusuario)
    {
        ejecutarConsulta("START TRANSACTION");

        try {

            if (empty($idsucursal)) {
                throw new Exception('La sucursal no se ha encontrado');
            }

            $this->validarCaja($$idsucursal, $idusuario);

            $configDescuento = Helpers::verificarDecuentoPagoAnticipado($idsucursal);

            $validacionMora = ejecutarConsultaSimpleFila("
                    SELECT COUNT(*) AS total
                    FROM cuentas_por_cobrar
                    WHERE idventa = '$idventa'
                    AND mora > 0
                    AND estado_pago = 1
                ");

            if ($validacionMora['total'] > 0) {
                throw new Exception('No se puede amortizar: la venta tiene mora pendiente');
            }

            $sql = "SELECT
                    cc.idcpc,
                    cc.fechavencimiento,
                    cc.fecharegistro
                FROM cuentas_por_cobrar cc
                WHERE cc.idventa = '$idventa'
                AND cc.condicion = 1
                AND cc.deuda > 0
                ORDER BY cc.fechavencimiento ASC, cc.fecharegistro ASC";

            $lista = ejecutarConsulta($sql);

            $pago = round((float) $montopago, 2);
            $totalAmortizado = 0;
            $data = false;

            while ($reg = $lista->fetch_object()) {

                if ($pago <= 0) {
                    break;
                }

                $filaAct = ejecutarConsultaSimpleFila("
                        SELECT
                            deuda,
                            interes,
                            mora,
                            abonototal,
                            descuento
                        FROM cuentas_por_cobrar
                        WHERE idcpc = '$reg->idcpc'
                    ");

                if (!$filaAct) {
                    continue;
                }

                $deuda = round((float) $filaAct['deuda'], 2);
                $abonototal = round((float) $filaAct['abonototal'], 2);
                $descuentoActual = round((float) $filaAct['descuento'], 2);

                if ($deuda <= 0) {
                    continue;
                }

                // ============================
                // DESCUENTO POR PAGO ANTICIPADO
                // ============================
                $descuento = 0;

                if ($configDescuento['activo']) {

                    $hoy = new DateTime();
                    $vencimiento = new DateTime($reg->fechavencimiento);

                    $dias = $hoy->diff($vencimiento)->days;

                    if (
                        $vencimiento > $hoy &&
                        $dias >= $configDescuento['dias_anticipacion']
                    ) {
                        $descuento = round(
                            $deuda * ($configDescuento['valor'] / 100),
                            2
                        );
                    }
                }

                // deuda que realmente debe pagar el cliente
                $deudaConDescuento = max(0, $deuda - $descuento);

                // pago aplicado
                $capital_pagado = min($pago, $deudaConDescuento);

                // nueva deuda
                $nuevaDeuda = round(
                    $deuda - $descuento - $capital_pagado,
                    2
                );

                if ($nuevaDeuda < 0) {
                    $nuevaDeuda = 0;
                }

                $pago -= $capital_pagado;

                // lo que realmente ingresó a caja
                $montoPagadoTotal = $capital_pagado;

                if (
                    $montoPagadoTotal <= 0 &&
                    $descuento <= 0
                ) {
                    continue;
                }

                // Registrar movimiento
                ejecutarConsulta("
                        INSERT INTO detalle_cuentas_por_cobrar
                        (
                            idcpc,
                            idcaja,
                            idpersonal,
                            montopagado,
                            montotarjeta,
                            banco,
                            op,
                            fechapago,
                            formapago,
                            observacion
                        )
                        VALUES
                        (
                            '$reg->idcpc',
                            '$idcaja',
                            '$idpersonal',
                            '$montoPagadoTotal',
                            0,
                            '',
                            '',
                            NOW(),
                            '$formapago',
                            'AMORTIZACION CREDITO'
                        )
                    ");

                $nuevoAbonoTotal = round(
                    $abonototal + $montoPagadoTotal,
                    2
                );

                $nuevoDescuento = round(
                    $descuentoActual + $descuento,
                    2
                );

                ejecutarConsulta("
                        UPDATE cuentas_por_cobrar
                        SET
                            deuda = '$nuevaDeuda',
                            abonototal = '$nuevoAbonoTotal',
                            descuento = '$nuevoDescuento'
                        WHERE idcpc = '$reg->idcpc'
                    ");

                if ($nuevaDeuda <= 0) {

                    ejecutarConsulta("
                            UPDATE cuentas_por_cobrar
                            SET estado_pago = 0
                            WHERE idcpc = '$reg->idcpc'
                        ");
                }

                $totalAmortizado += ($montoPagadoTotal + $descuento);
                $data = true;
            }

            $pendientes = ejecutarConsultaSimpleFila("
                    SELECT COUNT(*) AS total
                    FROM cuentas_por_cobrar
                    WHERE idventa = '$idventa'
                    AND estado_pago = 1
                    AND deuda > 0
                ");

            if ($pendientes['total'] == 0) {

                ejecutarConsulta("
                        UPDATE documentacion
                        SET estado = 2
                        WHERE idventa = '$idventa'
                        AND tipo = '1'
                    ");
            }

            ejecutarConsulta("COMMIT");

            return [
                'success' => $data,
                'message' => $data
                    ? 'Se amortizó correctamente S/ ' . number_format($totalAmortizado, 2)
                    : 'No se realizó ninguna amortización',
                'saldo_restante' => round($pago, 2)
            ];

        } catch (Exception $e) {

            ejecutarConsulta("ROLLBACK");

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'saldo_restante' => $montopago
            ];
        }
    }


    public function guardarComentario($idventa, $comentario)
    {

        if (empty($idventa) || empty($comentario)) {
            return array(
                'status' => false,
                'mensaje' => 'Datos incompletos'
            );
        }

        $sql = "UPDATE venta 
            SET nota = '$comentario'
            WHERE idventa = '$idventa'";

        $rspta = ejecutarConsulta($sql);

        if ($rspta) {
            $sql = "SELECT
                    v.idventa,
                    DATE_FORMAT(v.fecha_hora, '%d/%m/%y | %H:%i:%s %p') AS fecha_venta,
                    v.tipo_comprobante,
                    v.serie_comprobante,
                    v.num_comprobante,
                    v.total_venta,
                    v.nota,
                    SUM(cc.abonototal) AS total_abonado,
                    SUM(cc.deuda) AS saldo_pendiente
                FROM venta v
                INNER JOIN cuentas_por_cobrar cc ON cc.idventa = v.idventa
                WHERE v.idventa = '$idventa'
                GROUP BY v.idventa, v.fecha_hora, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, v.total_venta
                ORDER BY v.idventa DESC";

            $result = ejecutarConsultaSimpleFila($sql);
            $doc = $result['tipo_comprobante'] . '-' . $result['serie_comprobante'] . '-' . $result['num_comprobante'];
            return array(
                'status' => true,
                'idventa' => $result['idventa'],
                'saldoPendiente' => $result['saldo_pendiente'],
                'documento' => $doc,
                'nota' => $result['nota'],
                'mensaje' => 'Comentario guardado correctamente'
            );
        } else {
            return array(
                'status' => false,
                'mensaje' => 'Error al guardar comentario'
            );
        }
    }

    public function cuotasPorPagar($idventa)
    {
        $sql = "
        SELECT
            cc.*,
            v.idsucursal
        FROM cuentas_por_cobrar cc
        INNER JOIN venta v
            ON v.idventa = cc.idventa
        WHERE cc.idventa = '$idventa'
        AND cc.estado_pago = 1
        AND cc.condicion = 1
        ORDER BY cc.idcpc ASC
    ";

        $query = ejecutarConsulta($sql);

        $data = [];

        while ($row = $query->fetch_assoc()) {

            $mora = floatval($row["mora"]);
            $descuento = 0;

            // ======================
// CALCULAR MORA
// ======================

            $configMora = Helpers::verificarMoraCredito($row["idsucursal"]);

            if (
                $configMora["activo"] &&
                floatval($row["deuda"]) > 0
            ) {

                $fechaInicio = !empty($row["fecha_update_mora"])
                    ? $row["fecha_update_mora"]
                    : $row["fechavencimiento"];

                $hoy = new DateTime();
                $inicio = new DateTime($fechaInicio);

                if ($hoy > $inicio) {

                    $dias = $inicio->diff($hoy)->days;

                    if ($dias > 0) {

                        $mora += round(
                            floatval($row["deuda"]) *
                            ($configMora["valor"] / 100) *
                            $dias,
                            2
                        );
                    }
                }
            }

            // ======================
// CALCULAR DESCUENTO
// ======================

            $configDescuento = Helpers::verificarDecuentoPagoAnticipado(
                $row["idsucursal"]
            );

            if (
                $configDescuento["activo"] &&
                $mora <= 0 &&
                floatval($row["deuda"]) > 0
            ) {

                $hoy = new DateTime();
                $vencimiento = new DateTime($row["fechavencimiento"]);

                if ($hoy <= $vencimiento) {

                    $dias = $hoy->diff($vencimiento)->days;

                    if ($dias >= $configDescuento["dias_anticipacion"]) {

                        $descuento = round(
                            floatval($row["deuda"]) *
                            ($configDescuento["valor"] / 100),
                            2
                        );
                    }
                }
            }

            $row["mora_calculada"] = round($mora, 2);
            $row["descuento_calculado"] = round($descuento, 2);


            $data[] = $row;
        }

        return json_encode($data);
    }

    public function guardarVisita(
        $idcpc,
        $idventa,
        $idcliente,
        $fecha_programada,
        $idpersonal,
        $tipo_visita,
        $prioridad,
        $estado,
        $direccion,
        $descripcion,
        $idusuario,
        $fecha_final
    ) {
        try {
            if (empty($fecha_programada)) {
                throw new Exception("Debe ingresar fecha programada");
            }

            $this->pdo->beginTransaction();

            $iddocumento = null;

            if (!empty($idventa)) {

                $documentacion = $this->obtenerDocumento($idventa);

                if (!empty($documentacion)) {
                    $iddocumento = $documentacion['iddocumento'];
                }
            }

            $idseguimiento = (new FluentSaver($this->pdo))
                ->table('seguimiento_clientes')
                ->nullable([
                    'idventa',
                    'iddocumento',
                    'idcpc',
                    'idcliente',
                    'direccion',
                    'fecha_final'
                ])
                ->cast([
                    'idventa' => 'int',
                    'iddocumento' => 'int',
                    'idcpc' => 'int',
                    'idcliente' => 'int',
                    'idpersonal' => 'int',
                    'idusuario' => 'int'
                ])
                ->data([

                    'idventa' => $idventa,
                    'iddocumento' => $iddocumento,
                    'idcpc' => $idcpc,
                    'idcliente' => $idcliente,
                    'idpersonal' => $idpersonal,
                    'tipo' => $tipo_visita,
                    'descripcion' => $descripcion,
                    'fecha_proxima' => $fecha_programada,
                    'idusuario' => $idusuario,
                    'estado' => $estado,
                    'prioridad' => $prioridad,
                    'direccion' => $direccion,
                    'fecha_final' => $fecha_final

                ])
                ->save();

            if (!$idseguimiento) {
                throw new Exception("No se pudo guardar el seguimiento");
            }

            // Adjuntos
            if (
                isset($_FILES['adjuntos']) &&
                !empty($_FILES['adjuntos']['name'][0])
            ) {

                $ruta = "../files/seguimientos/";

                if (!is_dir($ruta)) {
                    mkdir($ruta, 0777, true);
                }

                foreach ($_FILES['adjuntos']['tmp_name'] as $key => $tmp) {

                    if ($_FILES['adjuntos']['error'][$key] != UPLOAD_ERR_OK) {
                        continue;
                    }

                    $nombreOriginal = $_FILES['adjuntos']['name'][$key];

                    $extension = strtolower(
                        pathinfo($nombreOriginal, PATHINFO_EXTENSION)
                    );

                    $nombreArchivo =
                        date('YmdHis') .
                        "_" .
                        uniqid() .
                        "." .
                        $extension;

                    if (move_uploaded_file($tmp, $ruta . $nombreArchivo)) {

                        (new FluentSaver($this->pdo))

                            ->table('seguimiento_adjuntos')

                            ->data([

                                'idseguimiento' => $idseguimiento,
                                'archivo' => $nombreArchivo,
                                'nombre_original' => $nombreOriginal

                            ])

                            ->save();
                    }
                }
            }

            $this->pdo->commit();

            return json_encode([
                "success" => true,
                "message" => "Seguimiento registrado correctamente"
            ]);

        } catch (Throwable $e) {

            if (isset($this->pdo) && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return json_encode([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }


    public function editarVisita(
        $id,
        $idcpc,
        $idventa,
        $idcliente,
        $fecha_programada,
        $idpersonal,
        $tipo_visita,
        $prioridad,
        $estado,
        $direccion,
        $descripcion,
        $idusuario,
        $fecha_final
    ) {

        try {

            if (empty($id)) {
                throw new Exception("ID de seguimiento inválido");
            }

            if (empty($fecha_programada)) {
                throw new Exception("Debe ingresar fecha programada");
            }

            $this->pdo->beginTransaction();

            $iddocumento = null;

            if (!empty($idventa)) {

                $documentacion = $this->obtenerDocumento($idventa);

                if (!empty($documentacion)) {
                    $iddocumento = $documentacion['iddocumento'];
                }
            }

            $update = (new FluentSaver($this->pdo))
                ->table('seguimiento_clientes')
                ->primaryKey('idseguimiento')
                ->timestamps(false)
                ->nullable([
                    'idventa',
                    'iddocumento',
                    'idcpc',
                    'idcliente',
                    'direccion',
                    'fecha_final'
                ])
                ->cast([
                    'idseguimiento' => 'int',
                    'idventa' => 'int',
                    'iddocumento' => 'int',
                    'idcpc' => 'int',
                    'idcliente' => 'int',
                    'idpersonal' => 'int',
                    'idusuario' => 'int'
                ])
                ->data([
                    'idseguimiento' => $id,
                    'idventa' => $idventa,
                    'iddocumento' => $iddocumento,
                    'idcpc' => $idcpc,
                    'idcliente' => $idcliente,
                    'idpersonal' => $idpersonal,
                    'tipo' => $tipo_visita,
                    'descripcion' => $descripcion,
                    'fecha_proxima' => $fecha_programada,
                    'estado' => $estado,
                    'prioridad' => $prioridad,
                    'direccion' => $direccion,
                    'fecha_final' => $fecha_final,
                    'idusuario' => $idusuario
                ])
                ->save();

            if (!$update) {
                throw new Exception("No se pudo actualizar el seguimiento");
            }

            /*
            |--------------------------------------------------------------------------
            | Eliminar adjuntos
            |--------------------------------------------------------------------------
            */

            if (!empty($_POST['archivos_eliminados'])) {

                $archivosEliminar = json_decode(
                    $_POST['archivos_eliminados'],
                    true
                );

                if (is_array($archivosEliminar)) {

                    foreach ($archivosEliminar as $idadjunto) {

                        $stmt = $this->pdo->prepare("
                            SELECT *
                            FROM seguimiento_adjuntos
                            WHERE idadjunto = :idadjunto
                        ");

                        $stmt->execute([
                            'idadjunto' => $idadjunto
                        ]);

                        $adjunto = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($adjunto) {

                            $rutaArchivo =
                                "../files/seguimientos/" .
                                $adjunto['archivo'];

                            if (file_exists($rutaArchivo)) {
                                unlink($rutaArchivo);
                            }

                            $stmt = $this->pdo->prepare("
                                                        DELETE
                                                        FROM seguimiento_adjuntos
                                                        WHERE idadjunto = :idadjunto
                                                    ");

                            $stmt->execute([
                                'idadjunto' => $idadjunto
                            ]);
                        }
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Nuevos adjuntos
            |--------------------------------------------------------------------------
            */

            if (
                isset($_FILES['adjuntos']) &&
                !empty($_FILES['adjuntos']['name'][0])
            ) {

                $ruta = "../files/seguimientos/";

                if (!file_exists($ruta)) {
                    mkdir($ruta, 0777, true);
                }

                foreach ($_FILES['adjuntos']['tmp_name'] as $key => $tmp) {

                    if ($_FILES['adjuntos']['error'][$key] == 0) {

                        $nombreOriginal =
                            $_FILES['adjuntos']['name'][$key];

                        $extension = strtolower(
                            pathinfo(
                                $nombreOriginal,
                                PATHINFO_EXTENSION
                            )
                        );

                        $nombreArchivo =
                            date('YmdHis') .
                            "_" .
                            uniqid() .
                            "." .
                            $extension;

                        if (move_uploaded_file($tmp, $ruta . $nombreArchivo)) {

                            try {
                                $adjunto = (new FluentSaver($this->pdo))
                                    ->table('seguimiento_adjuntos')
                                    ->timestamps(false)
                                    ->data([
                                        'idseguimiento' => $id,
                                        'archivo' => $nombreArchivo,
                                        'nombre_original' => $nombreOriginal
                                    ])
                                    ->save();

                                if (!$adjunto) {
                                    throw new Exception("Error al guardar el adjunto.");
                                }
                            } catch (Exception $e) {

                                if (file_exists($ruta . $nombreArchivo)) {
                                    unlink($ruta . $nombreArchivo);
                                }

                                throw $e;
                            }
                        }
                    }
                }
            }

            $this->pdo->commit();

            return json_encode([
                "status" => true,
                "msg" => "Seguimiento actualizado correctamente"
            ]);

        } catch (Exception $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return json_encode([
                "status" => false,
                "msg" => $e->getMessage()
            ]);

        }
    }


    public function obtenerDocumento($idventa)
    {
        $sql = "SELECT * FROM documentacion WHERE idventa = '$idventa'";
        return ejecutarConsultaSimpleFila($sql);
    }


    public function obtenerCredito($idventa, $idcpc)
    {
        $sql = "SELECT 
                c.*,

                (
                    SELECT COUNT(*)
                    FROM cuentas_por_cobrar x
                    WHERE x.idventa = c.idventa
                    AND x.fechavencimiento <= c.fechavencimiento
                ) AS numero_cuota,

                (
                    SELECT COUNT(*)
                    FROM cuentas_por_cobrar y
                    WHERE y.idventa = c.idventa
                ) AS total_cuotas,
                v.*

            FROM cuentas_por_cobrar c
            INNER JOIN venta v ON v.idventa = c.idventa
            WHERE c.idcpc = '$idcpc'";

        return ejecutarConsultaSimpleFila($sql);
    }

    public function listarHistorialSeguimiento($idventa)
    {
        $sql = "SELECT * FROM seguimiento_clientes WHERE idventa = $idventa AND deleted_at IS NULL";
        $rspta = ejecutarConsulta($sql);

        $data = array();

        $count = 1;
        while ($reg = $rspta->fetch_object()) {
            $credito = $this->obtenerCredito($reg->idventa, $reg->idcpc);
            $archivos_adjuntos = $this->dataArchivosAdjuntos($reg->idseguimiento);
            $data[] = array(
                "0" => $count++,
                "1" => $reg->tipo,
                "2" => "Corresponde a cuota " . $credito['numero_cuota'] . " de " . $credito['total_cuotas'],
                "3" => $reg->descripcion,
                "4" => $reg->fecha_proxima,
                "5" => $reg->estado,
                "6" => $reg->prioridad,
                "7" => '<button class="btn btn-primary" onclick=\'verArchivosAdjuntos(' . $archivos_adjuntos . ')\'>
                        <i class="fa fa-eye"></i> Adjuntos
                        </button>'
            );
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        return json_encode($results);
    }

    public function mostrarSeguimiento($idseguimiento)
    {
        $sql = "SELECT
                s.*,
                p.nombre as personal,
                c.nombre as cliente
            FROM seguimiento_clientes s
            INNER JOIN personal p ON p.idpersonal = s.idpersonal
            LEFT JOIN persona c ON c.idpersona = s.idcliente
            WHERE s.idseguimiento = $idseguimiento";
        $rspta = ejecutarConsultaSimpleFila($sql);
        $credito = '';
        $total_cuotas = '';
        $numero_comprobante = '';
        $serie_comprobante = '';
        if ($rspta['idventa'] && $rspta['idcpc']) {
            $data_credito = $this->obtenerCredito($rspta['idventa'], $rspta['idcpc']);
            $credito = $data_credito['numero_cuota'];
            $total_cuotas = $data_credito['total_cuotas'];
            $numero_comprobante = $data_credito['num_comprobante'];
            $serie_comprobante = $data_credito['serie_comprobante'];
        }
        $rspta['adjuntos'] = $this->dataArchivosAdjuntos($idseguimiento);
        $rspta['numero_cuota'] = $credito;
        $rspta['total_cuotas'] = $total_cuotas;
        $rspta['numero_comprobante'] = $numero_comprobante;
        $rspta['serie_comprobante'] = $serie_comprobante;
        return json_encode($rspta);
    }


    public function eliminarSeguimiento($idseguimiento)
    {
        if (empty($idseguimiento)) {
            return json_encode([
                "status" => false,
                "msg" => "Debe enviar el segimiento a eliminar"
            ]);
        }
        $date = date('Y-m-d H:i:s');

        $sql = "UPDATE seguimiento_clientes
            SET deleted_at = '$date'
            WHERE idseguimiento = '$idseguimiento'";

        $rspta = ejecutarConsulta($sql);

        if ($rspta) {

            return json_encode([
                "status" => true,
                "msg" => "Seguimiento eliminado correctamente"
            ]);

        } else {

            return json_encode([
                "status" => false,
                "msg" => "No se pudo eliminar el seguimiento"
            ]);
        }
    }


    public function guardarCompromisoPago(
        $idcpc,
        $idventa,
        $idcliente,
        $fecha_compromiso,
        $monto,
        $observacion,
        $idusuario
    ) {

        $sql = "INSERT INTO compromiso_pago(
                idcpc,
                fecha_compromiso,
                monto,
                observacion,
                idusuario
            )
            VALUES(
                '$idcpc',
                '$fecha_compromiso',
                '$monto',
                '$observacion',
                '$idusuario'
            )";

        $rspta = ejecutarConsulta($sql);

        if (!$rspta) {
            return json_encode([
                "status" => false,
                "msg" => "No se guardo compromiso de pago"
            ]);
        }

        return json_encode([
            "status" => true,
            "msg" => "El compromiso de pago se ha guardado correctamente"
        ]);
    }


    public function calendarioCuotasCredito($idventa)
    {
        $sql = "SELECT
                idcpc,
                fechavencimiento,
                deudatotal,
                estado_pago
            FROM cuentas_por_cobrar
            WHERE idventa = '$idventa'
            ORDER BY fechavencimiento";

        $rspta = ejecutarConsulta($sql);

        $eventos = [];
        $hoy = new DateTime();
        $cuota = 1;

        while ($row = $rspta->fetch_object()) {

            $fechaVencimiento = new DateTime($row->fechavencimiento);

            if ($row->estado_pago == 1) {
                $color = '#28a745';
            } elseif ($fechaVencimiento < $hoy) {
                $color = '#dc3545';
            } else {
                $color = '#ffc107';
            }
            $deudatotal = Helpers::get_currency_symbol($row->deudatotal);
            $eventos[] = [
                "id" => $row->idcpc,
                "title" => "Cuota {$cuota} - {$deudatotal}",
                "start" => $row->fechavencimiento,
                "allDay" => true,
                "backgroundColor" => $color,
                "borderColor" => $color,
                "textColor" => "#fff",
                "extendedProps" => [
                    "cuota" => $cuota,
                    "estado" => $row->estado_pago,
                    "monto" => $deudatotal
                ]
            ];

            $cuota++;
        }

        return json_encode($eventos);
    }

}

?>