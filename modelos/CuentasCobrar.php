<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
Class CuentasCobrar
{
	//Implementamos nuestro constructor
	public function __construct()
	{

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
    $idpersonal
) {
    global $conexion;

    // Normalizar fecha
    $fechaPago = trim((string)$fechaPago);
    if ($fechaPago === "") $fechaPago = date("Y-m-d H:i:s");

    // formapago (NOT NULL)
    $formapago = trim((string)$formapago);
    if ($formapago === "") $formapago = "Efectivo";

    // 1) validar caja abierta
    $sqlCaja = "SELECT estado FROM cajas WHERE idcaja='$idcaja'";
    $caja = ejecutarConsultaSimpleFila($sqlCaja);
    if (!$caja || intval($caja['estado']) != 2) {
        return ['success'=>false, 'message'=>'La caja está cerrada, no se puede registrar el pago.'];
    }

    // 2) obtener idventa de la cuota seleccionada
    $rowVenta = ejecutarConsultaSimpleFila("SELECT idventa FROM cuentas_por_cobrar WHERE idcpc='$idcpc'");
    if (!$rowVenta) {
        return ['success'=>false, 'message'=>'Cuenta no encontrada'];
    }
    $idventa = intval($rowVenta['idventa']);

    // 3) pago total y proporciones para registrar detalle
    $pagoEfe = round(floatval($montopagado), 2);
    $pagoTar = round(floatval($montoPagarTarjeta), 2);
    $pagoTotal = round($pagoEfe + $pagoTar, 2);

    if ($pagoTotal <= 0) {
        return ['success'=>false, 'message'=>'El monto de pago debe ser mayor a 0.'];
    }

    $ratioEfe = ($pagoTotal > 0) ? ($pagoEfe / $pagoTotal) : 1;
    $ratioTar = ($pagoTotal > 0) ? ($pagoTar / $pagoTotal) : 0;

    // 4) cuotas pendientes de esa venta
    $rsCuotas = ejecutarConsulta("
        SELECT idcpc, fechavencimiento
        FROM cuentas_por_cobrar
        WHERE idventa='$idventa' AND estado_pago=1
        ORDER BY fechavencimiento ASC, idcpc ASC
    ");
    if (!$rsCuotas) {
        return ['success'=>false, 'message'=>'No se pudo obtener las cuotas de la venta.'];
    }

    $cuotas = [];
    while ($r = $rsCuotas->fetch_assoc()) $cuotas[] = $r;
    if (empty($cuotas)) {
        return ['success'=>false, 'message'=>'No hay cuotas pendientes para esta venta.'];
    }

    // 5) priorizar la cuota enviada al inicio
    usort($cuotas, function ($a, $b) use ($idcpc) {
        if (intval($a['idcpc']) === intval($idcpc)) return -1;
        if (intval($b['idcpc']) === intval($idcpc)) return 1;

        $fa = strtotime($a['fechavencimiento']);
        $fb = strtotime($b['fechavencimiento']);
        if ($fa === $fb) return intval($a['idcpc']) <=> intval($b['idcpc']);
        return $fa <=> $fb;
    });

    // 6) transacción
    ejecutarConsulta("START TRANSACTION");

    try {
        $restante = $pagoTotal;
        $saldoFavor = 0.00;

        foreach ($cuotas as $c) {
            if ($restante <= 0) break;

            $idcpc_i = intval($c['idcpc']);

            // lock cuota
            $fila = ejecutarConsultaSimpleFila("
                SELECT idcpc, deudatotal, deuda_base, deuda, mora, mora_pagada, abonototal, fechavencimiento, estado_pago
                FROM cuentas_por_cobrar
                WHERE idcpc='$idcpc_i'
                FOR UPDATE
            ");
            if (!$fila) continue;
            if (intval($fila['estado_pago']) != 1) continue;

            $fechaVenc = $fila['fechavencimiento'];

            // ===== Normalizar modelo =====
            // deuda_base = monto original cuota (27.50)
            $deuda_base = round(floatval($fila['deuda_base']), 2);
            $mora       = round(floatval($fila['mora']), 2);
            $mora_pagada_actual = round(floatval($fila['mora_pagada']), 2);

            // Si deuda_base viene en 0, reconstruir desde deudatotal - mora
            if ($deuda_base <= 0) {
                $deuda_base = round(floatval($fila['deudatotal']) - $mora, 2);
                if ($deuda_base < 0) $deuda_base = 0;
                $fix = ejecutarConsulta("UPDATE cuentas_por_cobrar SET deuda_base='$deuda_base' WHERE idcpc='$idcpc_i'");
                if (!$fix) throw new Exception("No se pudo normalizar deuda_base (idcpc=$idcpc_i)");
            }

            // deuda = saldo principal (si es NULL o 0 pero estado pendiente, lo asumimos como deuda_base)
            $deuda = $fila['deuda'];
            $deuda = ($deuda === null || $deuda === '') ? $deuda_base : round(floatval($deuda), 2);
            if ($deuda < 0) $deuda = 0;

            // ===== Faltante real =====
            $faltanteMora = max(0, $mora);
            $faltanteBase = max(0, $deuda);
            $faltanteTotal = round($faltanteMora + $faltanteBase, 2);

            if ($faltanteTotal <= 0) {
                // cerrar por seguridad
                $close = ejecutarConsulta("
                    UPDATE cuentas_por_cobrar
                    SET estado_pago=0, deudatotal=0, deuda=0, mora=0, abonototal='$deuda_base', fecha_update_mora=CURDATE()
                    WHERE idcpc='$idcpc_i'
                ");
                if (!$close) throw new Exception("No se pudo cerrar cuota (idcpc=$idcpc_i)");
                continue;
            }

            // Aplicar solo lo que corresponde a esta cuota
            $aplicar = min($restante, $faltanteTotal);
            if ($aplicar <= 0) continue;

            // 1) pagar mora
            $pagoMora = min($aplicar, $faltanteMora);
            $aplicar -= $pagoMora;

            // 2) pagar principal
            $pagoBase = min($aplicar, $faltanteBase);
            $aplicar -= $pagoBase;

            $pagadoEnEstaCuota = round($pagoMora + $pagoBase, 2);
            $restante = round($restante - $pagadoEnEstaCuota, 2);

            // Nuevos saldos
            $moraNueva  = round(max(0, $mora  - $pagoMora), 2);
            $deudaNueva = round(max(0, $deuda - $pagoBase), 2);

            // Si quieres recalcular mora por atraso, hazlo pero SOBRE el saldo que queda:
            $fechaV = new DateTime(date('Y-m-d', strtotime($fechaVenc)));
            $hoy    = new DateTime(date('Y-m-d'));

            if ($deudaNueva > 0 && $hoy > $fechaV) {
                $diasRetraso = (int)$hoy->diff($fechaV)->days;
                $porcMoraMes = 10.0;
                $moraDiaria  = ($deudaNueva * ($porcMoraMes / 100)) / 30;
                $moraCalc    = round($moraDiaria * $diasRetraso, 2);

                // toma el mayor entre lo que ya quedó de mora y lo calculado
                if ($moraCalc > $moraNueva) $moraNueva = $moraCalc;
            }

            $deudatotalNuevo = round($deudaNueva + $moraNueva, 2);

            // abonototal (pagado de principal) = cuota - saldo principal
            $abonototalNuevo = round(max(0, $deuda_base - $deudaNueva), 2);
            if ($abonototalNuevo > $deuda_base) $abonototalNuevo = $deuda_base;

            $estadoNuevo = ($deudatotalNuevo <= 0) ? 0 : 1;

            // Update cuota
            $sqlUpdate = "
                UPDATE cuentas_por_cobrar
                SET deuda       = '$deudaNueva',
                    mora        = '$moraNueva',
                    deudatotal   = '$deudatotalNuevo',
                    abonototal   = '$abonototalNuevo',
                    mora_pagada  = COALESCE(mora_pagada,0) + '".round($pagoMora,2)."',
                    fecha_update_mora = CURDATE(),
                    estado_pago  = '$estadoNuevo'
                WHERE idcpc = '$idcpc_i'
            ";
            $save = ejecutarConsulta($sqlUpdate);
            if (!$save) throw new Exception("No se pudo actualizar la cuenta (idcpc=$idcpc_i)");

            // Insertar detalle SOLO por lo aplicado a ESTA cuota
            if ($pagadoEnEstaCuota > 0) {
                $detEfe = round($pagadoEnEstaCuota * $ratioEfe, 2);
                $detTar = round($pagadoEnEstaCuota * $ratioTar, 2);
                $diff   = round($pagadoEnEstaCuota - ($detEfe + $detTar), 2);
                if ($diff != 0) $detEfe = round($detEfe + $diff, 2);

                $obs = (string)$observacion;
                if ($idcpc_i != intval($idcpc)) {
                    $obs = trim(($obs ? $obs . " | " : "") . "Amortización automática a otra cuota (misma venta)");
                }

                $obsEsc   = mysqli_real_escape_string($conexion, $obs);
                $formaEsc = mysqli_real_escape_string($conexion, $formapago);

                $bancoSQL = (isset($banco) && trim((string)$banco) !== '')
                    ? "'" . mysqli_real_escape_string($conexion, (string)$banco) . "'"
                    : "NULL";
                $opSQL = (isset($op) && trim((string)$op) !== '')
                    ? "'" . mysqli_real_escape_string($conexion, (string)$op) . "'"
                    : "NULL";

                $sqlDetalle = "
                    INSERT INTO detalle_cuentas_por_cobrar
                    (idcpc, idcaja, idpersonal, montopagado, montotarjeta, banco, op, fechapago, formapago, observacion)
                    VALUES
                    ('$idcpc_i', '$idcaja', '$idpersonal', '$detEfe', '$detTar', $bancoSQL, $opSQL, '$fechaPago', '$formaEsc', '$obsEsc')
                ";
                $det = ejecutarConsulta($sqlDetalle);
                if (!$det) {
                    throw new Exception("No se pudo registrar el detalle del pago (idcpc=$idcpc_i).");
                }
            }
        }

        if ($restante > 0) $saldoFavor = round($restante, 2);

        ejecutarConsulta("COMMIT");

        $msg = "Pago registrado correctamente";
        if ($saldoFavor > 0) $msg .= ". Excedente / saldo a favor: S/ " . number_format($saldoFavor, 2);

        return ['success'=>true, 'message'=>$msg, 'saldo_favor'=>$saldoFavor];

    } catch (Exception $e) {
        ejecutarConsulta("ROLLBACK");
        return ['success'=>false, 'message'=>$e->getMessage()];
    }
}


	public function deudacliente($idventa){

		$sql="SELECT v.idventa,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,cc.idcpc,date_format(cc.fecharegistro,'%d/%m/%y') as fecharegistro, v.tipo_comprobante, c.nombre,TRUNCATE(cc.deudatotal + cc.abonototal,2) as deudatotal, cc.deudatotal as deuda, cc.abonototal,date_format(cc.fechavencimiento,'%d/%m/%y') as fechavencimiento 
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


	public function verSucursal($idsucursal){
		$sql = "SELECT * FROM sucursal WHERE idsucursal = '$idsucursal'";
		$sucursal = ejecutarConsulta($sql)->fetch_object();
		if($sucursal){
			return $sucursal->nombre;
		}else{
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
		$sql="SELECT cc.iddcpc,cc.iddcpc,cc.montopagado,cc.montotarjeta,date_format(cc.fechapago,'%d/%m/%y | %H:%i:%s %p') as fechapago,cc.formapago,cc.banco,cc.op FROM detalle_cuentas_por_cobrar cc
				WHERE cc.idcpc = '$idcpc'
		        ORDER BY cc.iddcpc asc";
		return ejecutarConsulta($sql);		
	}

	public function mostrar($idcpc)
	{

		$sql="SELECT v.idventa, v.total_venta, v.interes, v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,cc.idcpc,date_format(cc.fecharegistro,'%d/%m/%y') as fecharegistro, v.tipo_comprobante, c.nombre,TRUNCATE(cc.deudatotal,2) as deudatotal, cc.deudatotal as deuda, cc.abonototal,date_format(cc.fechavencimiento,'%d/%m/%y') as fechavencimiento 
				FROM venta v 
				INNER JOIN cuentas_por_cobrar cc
		        ON v.idventa = cc.idventa
		        INNER JOIN persona c
		        ON c.idpersona = v.idcliente
		        WHERE cc.idcpc = '$idcpc'";
		return ejecutarConsultaSimpleFila($sql);

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
                'dias_retraso'   => 0,
                'mora'           => 0.00,
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
                'dias_retraso'   => 0,
                'mora'           => 0.00,
                'total_con_mora' => round($cuotaConInteres, 2)
            ];
        }

        // Fechas sin horas
        $fechaV = new DateTime(date('Y-m-d', strtotime($fechaVenc)));
        $hoy = new DateTime(date('Y-m-d'));

        $diasRetraso = 0;
        if ($hoy > $fechaV) {
            $diasRetraso = (int)$hoy->diff($fechaV)->days;
        }

        // Si no hay días de retraso → no hay mora
        if ($diasRetraso <= 0) {
            return [
                'cuota_sin_mora' => round($cuotaConInteres, 2),
                'dias_retraso'   => 0,
                'mora'           => 0.00,
                'total_con_mora' => round($cuotaConInteres, 2)
            ];
        }

        // Calcular mora (10% mensual sobre cuotaConInteres)
        $porcMoraMes = 10.0;
        $moraDiaria = ($cuotaConInteres * ($porcMoraMes / 100)) / 30;
        $moraTotal = round($moraDiaria * $diasRetraso, 2);

        return [
            'cuota_sin_mora' => round($cuotaConInteres, 2),
            'dias_retraso'   => $diasRetraso,
            'mora'           => $moraTotal,
            'total_con_mora' => round($cuotaConInteres + $moraTotal, 2)
        ];
    }


	public function mostrarTicket($idventa)
	{

		$sql="SELECT v.idventa,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,cc.idcpc,DATE(cc.fecharegistro) as fecharegistro, v.tipo_comprobante, c.nombre,TRUNCATE(cc.deudatotal,2) as deudatotal, cc.deudatotal as deuda, cc.abonototal,cc.fechavencimiento 
				FROM venta v 
				INNER JOIN cuentas_por_cobrar cc
		        ON v.idventa = cc.idventa
		        INNER JOIN persona c
		        ON c.idpersona = v.idcliente
		        WHERE cc.idventa = '$idventa'";
		return ejecutarConsulta($sql);

	}

	public function mostrarDeuda($idVenta){
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

            if ($pago <= 0) break;
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

            if ($deudaPendiente <= 0) continue;
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
        if (!$fila) return false;

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
        if ($deuda_base <= 0) return true;

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
        $diasRetraso = (int)$hoy->diff($fechaV)->days;

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

            if ($totalConMora <= 0) continue; // No enviar si ya está pagado

            // Verificar si ya se envió hoy
            $yaEnviado = ejecutarConsultaSimpleFila(
                "SELECT id FROM recordatorio_envios 
                 WHERE idcpc = '{$reg->idcpc}' 
                 LIMIT 1"
            );
            if ($yaEnviado) continue;

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
            'idcpc'      => $r->idcpc,
            'mensaje'    => $mensaje,
            'fecha'      => $fecha,
            'tipo'       => '' // cuota
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
                cc.deudatotal AS debe,
                0 AS haber,
                'VENTA' AS tipo
            FROM cuentas_por_cobrar cc
            INNER JOIN venta v ON cc.idventa = v.idventa
            WHERE cc.idcpc = '$idcpc'

            UNION ALL

            SELECT
                dcc.fechapago AS fecha,
                'ABONO' AS documento,
                0 AS debe,
                (dcc.montopagado + dcc.montotarjeta) AS haber,
                'PAGO' AS tipo
            FROM detalle_cuentas_por_cobrar dcc
            WHERE dcc.idcpc = '$idcpc'

            ORDER BY fecha ASC
        ";

        $rspta = ejecutarConsulta($sql);

        $saldo = 0;
        $html = '';

        $html .= '
        <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th>Fecha</th>
                    <th>Documento</th>
                    <th>Debe</th>
                    <th>Haber</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
        ';

        while ($r = $rspta->fetch_object()) {
            $saldo += ($r->debe - $r->haber);

            $html .= '
                <tr>
                    <td>'.$r->fecha.'</td>
                    <td>'.$r->documento.'</td>
                    <td class="text-right">S/ '.number_format($r->debe, 2).'</td>
                    <td class="text-right">S/ '.number_format($r->haber, 2).'</td>
                    <td class="text-right"><strong>S/ '.number_format($saldo, 2).'</strong></td>
                </tr>
            ';
        }

        $html .= '
            </tbody>
        </table>
        </div>
        ';

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

    $totalDebe     = 0;
    $totalHaber    = 0;
    $saldoGeneral  = 0;

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
        $docVenta   = "{$v->tipo_comprobante}-{$v->serie_comprobante}-{$v->num_comprobante}";
        $saldoVenta = $v->total_venta;

        /* ====== VENTA (DEBE) ====== */
        $totalDebe    += $v->total_venta;
        $saldoGeneral += $v->total_venta;

        $html .= "
            <tr style='background:#eef'>
                <td>{$v->fecha_hora}</td>
                <td><b>VENTA $docVenta</b></td>
                <td class='text-right'>S/ ".number_format($v->total_venta,2)."</td>
                <td class='text-right'>S/ 0.00</td>
                <td class='text-right'><b>S/ ".number_format($saldoVenta,2)."</b></td>
            </tr>
        ";

        /* ====== ANTICIPO (montoPagado) ====== */
        if ($v->montoPagado > 0) {

            $anticipo = $v->montoPagado;

            $saldoVenta   -= $anticipo;
            $saldoGeneral -= $anticipo;

            if ($saldoVenta < 0) $saldoVenta = 0;
            if ($saldoGeneral < 0) $saldoGeneral = 0;

            $totalHaber += $anticipo;

            $html .= "
                <tr>
                    <td>{$v->fecha_hora}</td>
                    <td style='padding-left:30px;color:#0d6efd'>
                        ↳ ANTICIPO $docVenta
                    </td>
                    <td class='text-right'>S/ 0.00</td>
                    <td class='text-right'>S/ ".number_format($anticipo,2)."</td>
                    <td class='text-right'><b>S/ ".number_format($saldoVenta,2)."</b></td>
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

                $saldoVenta   -= $montoAbono;
                $saldoGeneral -= $montoAbono;

                if ($saldoVenta < 0) $saldoVenta = 0;
                if ($saldoGeneral < 0) $saldoGeneral = 0;

                $totalHaber += $montoAbono;

                $html .= "
                    <tr>
                        <td>{$ab->fechapago}</td>
                        <td style='padding-left:30px;color:green'>
                            ↳ ABONO $docVenta
                        </td>
                        <td class='text-right'>S/ 0.00</td>
                        <td class='text-right'>S/ ".number_format($montoAbono,2)."</td>
                        <td class='text-right'><b>S/ ".number_format($saldoVenta,2)."</b></td>
                    </tr>
                ";
            }
        }

        /* ====== SALDO FINAL DE LA VENTA ====== */
        $html .= "
            <tr style='background:#f9f9f9'>
                <td colspan='4' class='text-right'><b>Saldo Venta</b></td>
                <td class='text-right'><b>S/ ".number_format($saldoVenta,2)."</b></td>
            </tr>
        ";
    }

    /* ====== TOTALES ====== */
    $html .= "
            </tbody>
            <tfoot class='bg-light'>
                <tr>
                    <th colspan='2' class='text-right'>TOTALES</th>
                    <th class='text-right'>S/ ".number_format($totalDebe,2)."</th>
                    <th class='text-right'>S/ ".number_format($totalHaber,2)."</th>
                    <th class='text-right'><b>S/ ".number_format($saldoGeneral,2)."</b></th>
                </tr>
            </tfoot>
        </table>
    ";

    return $html;
}



}

?>