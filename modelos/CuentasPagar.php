<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";

Class CuentasPagar
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	public function insertar($idcpc,$montopagado,$observacion,$banco,$op,$fechaPago,$formapago, $montoPagarTarjeta){

		$sql="INSERT INTO detalle_cuentas_por_pagar (idcpp,montopagado,banco,op,observacion,formapago, fechapago, montotarjeta)
		VALUES ('$idcpc','$montopagado','$banco','$op','$observacion','$formapago', '$fechaPago', '$montoPagarTarjeta')";
		ejecutarConsulta($sql);

		//$sql1="UPDATE cuentas_por_pagar SET fechavencimiento='$fechaPago' WHERE idcpp='$idcpc'";
		//ejecutarConsulta($sql1);
		$montopagado = floatval($montopagado); // Asegura que sea numérico
		$montoPagarTarjeta = floatval($montoPagarTarjeta); // Asegura que sea numérico
		$abono = $montopagado+$montoPagarTarjeta;
		$sql2="UPDATE cuentas_por_pagar SET deudatotal = deudatotal - '$abono' WHERE idcpp='$idcpc'";
		return ejecutarConsulta($sql2);
		
	}

	public function deudacliente($idventa){

		$sql="SELECT v.idcompra,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,cc.idcpp,date_format(cc.fecharegistro,'%d/%m/%y') as fecharegistro, v.tipo_comprobante, c.nombre,TRUNCATE(cc.deudatotal + cc.abonototal,2) as deudatotal, cc.deudatotal as deuda, cc.abonototal,date_format(cc.fechavencimiento,'%d/%m/%y') as fechavencimiento 
				FROM compra v 
				INNER JOIN cuentas_por_pagar cc
		        ON v.idcompra = cc.idcompra
		        INNER JOIN persona c
		        ON c.idpersona = v.idcliente
		        WHERE cc.idcompra = '$idventa'";
		return ejecutarConsulta($sql);
		
	}


	public function listarSaldos($fecha_inicio,$fecha_fin,$idcliente,$idsucursal){
		if($idcliente == 'Todos' || $idcliente == null){
			$sql = "SELECT SUM(cpp.abonototal) AS abonototal, SUM(cpp.deudatotal) AS deudatotal, c.idproveedor 
			FROM cuentas_por_pagar cpp, compra c WHERE cpp.idcompra = c.idcompra
			AND (cpp.fecharegistro)>='$fecha_inicio' 
			AND DATE(cpp.fecharegistro)<='$fecha_fin' AND cpp.condicion='1'";
		}else{
			$sql = "SELECT SUM(cpp.abonototal) AS abonototal, SUM(cpp.deudatotal) AS deudatotal, c.idproveedor 
			FROM cuentas_por_pagar cpp, compra c 
			WHERE cpp.idcompra = c.idcompra
			AND (cpp.fecharegistro)>='$fecha_inicio' 
			AND DATE(cpp.fecharegistro)<='$fecha_fin'
			AND c.idproveedor = '$idcliente' AND cpp.condicion='1'";
		}

		$data = ejecutarConsulta($sql)->fetch_object();
		return $data;
	}

	//Implementar un método para listar los registros
	public function listar($fecha_inicio,$fecha_fin,$idcliente,$idsucursal)
	{


		if($idcliente == "Todos" || $idcliente == null){

			$sql="SELECT cc.idcpp,date_format(cc.fecha_hora,'%d/%m/%y | %H:%i:%s %p') as fecharegistro, v.tipo_comprobante, c.nombre, c.num_documento, v.serie_comprobante, v.num_comprobante, cc.deudatotal, cc.abonototal, date_format(cc.fechavencimiento,'%d/%m/%y') as fechavencimiento, cc.idcompra 
				FROM compra v 
				INNER JOIN cuentas_por_pagar cc
		        ON v.idcompra = cc.idcompra
		        INNER JOIN persona c
		        ON c.idpersona = v.idproveedor
		        WHERE DATE(cc.fecharegistro)>='$fecha_inicio' AND DATE(cc.fecharegistro)<='$fecha_fin' AND cc.condicion='1'
		        ORDER BY cc.idcpp desc";

		}else{

			$sql="SELECT cc.idcpp,date_format(cc.fecha_hora,'%d/%m/%y | %H:%i:%s %p') as fecharegistro, v.tipo_comprobante, c.nombre, c.num_documento, v.serie_comprobante, v.num_comprobante, cc.deudatotal, cc.abonototal, date_format(cc.fechavencimiento,'%d/%m/%y') as fechavencimiento, cc.idcompra 
				FROM compra v 
				INNER JOIN cuentas_por_pagar cc
		        ON v.idcompra = cc.idcompra
		        INNER JOIN persona c
		        ON c.idpersona = v.idproveedor
		        WHERE DATE(cc.fecharegistro)>='$fecha_inicio' AND DATE(cc.fecharegistro)<='$fecha_fin' AND v.idproveedor = '$idcliente' AND cc.condicion='1'
		        ORDER BY cc.idcpp desc";

		}
		
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros
	public function listarDetalle($idcpc)
	{
		$sql="SELECT cc.iddcpp,cc.iddcpp, cc.montopagado, cc.montotarjeta, date_format(cc.fechapago,'%d/%m/%y | %H:%i:%s %p') as fechapago,cc.formapago,cc.banco,cc.op FROM detalle_cuentas_por_pagar cc
				WHERE idcpp = '$idcpc'
		        ORDER BY iddcpp asc";
		return ejecutarConsulta($sql);		
	}

	public function mostrar($idcpc)
	{

		$sql="SELECT v.idcompra,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,cc.idcpp,date_format(cc.fecharegistro,'%d/%m/%y') as fecharegistro, v.tipo_comprobante, c.nombre,TRUNCATE(cc.deudatotal,2) as deudatotal, cc.deudatotal as deuda, cc.abonototal,date_format(cc.fechavencimiento,'%d/%m/%y') as fechavencimiento 
				FROM compra v 
				INNER JOIN cuentas_por_pagar cc
		        ON v.idcompra = cc.idcompra
		        INNER JOIN persona c
		        ON c.idpersona = v.idproveedor
		        WHERE cc.idcpp = '$idcpc'";
		return ejecutarConsultaSimpleFila($sql);

	}

	public function mostrarTicket($idventa)
	{

		$sql="SELECT v.idcompra,v.tipo_comprobante,v.serie_comprobante,v.num_comprobante,cc.idcpp,DATE(cc.fecharegistro) as fecharegistro, v.tipo_comprobante, c.nombre,TRUNCATE(cc.deudatotal,2) as deudatotal, cc.deudatotal as deuda, cc.abonototal,cc.fechavencimiento 
				FROM compra v 
				INNER JOIN cuentas_por_pagar cc
		        ON v.idcompra = cc.idcompra
		        INNER JOIN persona c
		        ON c.idpersona = v.idproveedor
		        WHERE cc.idcompra = '$idventa'";
		return ejecutarConsulta($sql);

	}

	public function mostrarDeuda($idVenta){
		$sql="SELECT * FROM cuentas_por_pagar WHERE idcompra='".$idVenta."'";
		return ejecutarConsulta($sql);
	}

	public function amortizarDeuda($deuda, $idcliente, $fecha_inicio, $fecha_fin, $formapago, $montopago){
		$sql3="SELECT cc.idcpp,date_format(cc.fecharegistro,'%d/%m/%y | %H:%i:%s %p') as fecharegistro, v.tipo_comprobante, c.nombre, c.num_documento, v.serie_comprobante, v.num_comprobante, cc.deudatotal, cc.abonototal, date_format(cc.fechavencimiento,'%d/%m/%y') as fechavencimiento, cc.idcompra 
		FROM compra v 
		INNER JOIN cuentas_por_pagar cc
		ON v.idcompra = cc.idcompra
		INNER JOIN persona c
		ON c.idpersona = v.idproveedor
		WHERE DATE(cc.fecharegistro)>='$fecha_inicio' 
		AND DATE(cc.fecharegistro)<='$fecha_fin' 
		AND v.idproveedor = '$idcliente'
		AND cc.condicion = 1
		ORDER BY cc.idcpp desc";

		$lista = ejecutarConsulta($sql3);
		$data = false;
		$pago = $montopago;
		while ($reg = $lista->fetch_object()) {
			if($reg->deudatotal < $pago){
				$pago = $pago - $reg->deudatotal;
				$sql="INSERT INTO detalle_cuentas_por_pagar (idcpp,montopagado,banco,op,observacion,formapago)
				VALUES ('$reg->idcpp','$reg->deudatotal','','','','$formapago')";
				ejecutarConsulta($sql);
				$sql2="UPDATE cuentas_por_pagar SET deudatotal = deudatotal - '$reg->deudatotal', condicion=0 WHERE idcpp='$reg->idcpp'";
				ejecutarConsulta($sql2);
				$data = true;
			}else{
				$amortizar = $reg->deudatotal-$pago;
				if($pago > 0){
					$sql="INSERT INTO detalle_cuentas_por_pagar (idcpp,montopagado,banco,op,observacion,formapago)
					VALUES ('$reg->idcpp','$pago','','','','$formapago')";
					ejecutarConsulta($sql);
					$sql2="UPDATE cuentas_por_pagar SET deudatotal = deudatotal - '$pago' WHERE idcpp='$reg->idcpp'";
					ejecutarConsulta($sql2);
					$data = true;
				}
				$pago = 0;
			}
        }

		if ($data) {
			return array('success'=> true);
		}else{
            return array('success'=> false);
        }
	}

}

?>