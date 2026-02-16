<?php 
require_once "../modelos/CuentasPagar.php";

$cuentaspagar=new CuentasPagar();

$idcpc=isset($_POST["idcpc"])? limpiarCadena($_POST["idcpc"]):"";
$idventa=isset($_POST["idventa"])? limpiarCadena($_POST["idventa"]):"";
$montopagado=isset($_POST["montoPagar"])? limpiarCadena($_POST["montoPagar"]):"";
$montoPagarTarjeta=isset($_POST["montoPagarTarjeta"])? limpiarCadena($_POST["montoPagarTarjeta"]):"";
$observacion=isset($_POST["observacion"])? limpiarCadena($_POST["observacion"]):"";

$banco=isset($_POST["banco"])? limpiarCadena($_POST["banco"]):"";
$op=isset($_POST["op"])? limpiarCadena($_POST["op"]):"";

$fechaPago=isset($_POST["fechaPago"])? limpiarCadena($_POST["fechaPago"]):"";
$formapago=isset($_POST["formapago"])? limpiarCadena($_POST["formapago"]):"";

	switch ($_GET["op"]){

		case 'guardaryeditar':
		
			$rspta=$cuentaspagar->insertar($idcpc,$montopagado,$observacion,$banco,$op,$fechaPago,$formapago, $montoPagarTarjeta);
			echo $rspta ? "Abono registrado" : "Abono no se pudo registrar";
			
		break;

		case 'listar_saldos':
			$fecha_inicio=$_REQUEST["fecha_inicio"];
			$fecha_fin=$_REQUEST["fecha_fin"];
			$idcliente=$_REQUEST["idcliente"];
			$idsucursal=$_REQUEST["idsucursal"];
			$rspta = $cuentaspagar->listarSaldos($fecha_inicio, $fecha_fin, $idcliente, $idsucursal);
			echo json_encode($rspta);
			break;

		case 'listar':

			$fecha_inicio=$_REQUEST["fecha_inicio"];
			$fecha_fin=$_REQUEST["fecha_fin"];
			$idcliente=$_REQUEST["idcliente"];
			$idsucursal=$_REQUEST["idsucursal"];

			$rspta=$cuentaspagar->listar($fecha_inicio,$fecha_fin,$idcliente,$idsucursal);
	 		//Vamos a declarar un array
	 		$data= Array();

	 		while ($reg=$rspta->fetch_object()){

				$deuda = $reg->abonototal + $reg->deudatotal;

	 			$url1='reportes/factura/generaFacturaCompra.php?id=';
	 			$data[]=array(
	 				"0"=>$reg->fecharegistro,
	 				"1"=>$reg->tipo_comprobante . '-' . $reg->serie_comprobante . '-' . $reg->num_comprobante,
	 				"2"=>$reg->nombre,
	 				"3"=>$reg->num_documento,
	 				"4"=>number_format($reg->deudatotal, 2, ".", ","),
	 				"5"=>$reg->abonototal,
	 				"6"=>number_format($deuda, 2, ".", ","),
	 				"7"=>$reg->fechavencimiento,
	 				"8"=>(($reg->abonototal + $reg->deudatotal) == $reg->abonototal)?'<center><span class="badge bg-green">Cancelado</span></center>':'<center><span class="badge bg-red">Por Cancelar</span></center>',
	 				"9"=>'<center><a target="_blank" href="'.$url1.$reg->idcompra.'" data-toggle="tooltip" title="" target="blanck" data-original-title="Ticket"> <button class="btn btn-primary btn-xs"><i class="far fa-file-alt"></i></button></a></center>',	
	 				"10"=>(($reg->abonototal + $reg->deudatotal) == $reg->abonototal)?
					
						'<div class="dropdown">
						  <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> <i class="fa fa-list-ul"></i>
						  <span class="caret"></span></button>
						  <div class="dropdown-menu">
							<a class="dropdown-item" style="cursor:pointer;" onclick="mostrarAbonos('.$reg->idcpp.')">Ver abonos</a>
						  </div>
						</div>':'<div class="dropdown">
						  <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> <i class="fa fa-list-ul"></i>
						  <span class="caret"></span></button>
						  <div class="dropdown-menu">
							<a class="dropdown-item" style="cursor:pointer;" onclick="mostrar('.$reg->idcpp.')">Crear abonos</a>
							<a class="dropdown-item" style="cursor:pointer;" onclick="mostrarAbonos('.$reg->idcpp.')">Ver abonos</a>
						  </div>
						</div>',
	 				);
	 		}
	 		$results = array(
	 			"sEcho"=>1, //Informaci��n para el datatables
	 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
	 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
	 			"aaData"=>$data);
	 		echo json_encode($results);

		break;

		case 'listarDetalle':

			$idcpc=$_REQUEST["idcpc"];

			$rspta=$cuentaspagar->listarDetalle($idcpc);
	 		//Vamos a declarar un array
	 		$data= Array();

	 		while ($reg=$rspta->fetch_object()){

				if($reg->formapago == 'Efectivo'){
					$formapago = '-';
				}else{

					if($reg->formapago != '' || $reg->formapago != null){

						$fp = "$reg->formapago - ";
						
					}else{
						$fp = '';
					}
					
					if($reg->banco != '' || $reg->banco != null){
						$bn= "$reg->banco - OP: $reg->op";
					}else{
						$bn='-';
					}

					$formapago = $fp . $bn;
				}

	 			$data[]=array(
	 				"0"=>$reg->fechapago,
	 				"1"=>$reg->montopagado+$reg->montotarjeta,
	 				"2"=>$formapago
	 				);
	 		}
	 		$results = array(
	 			"sEcho"=>1, //Informaci��n para el datatables
	 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
	 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
	 			"aaData"=>$data);
	 		echo json_encode($results);

		break;

		case 'mostrar':
		$rspta=$cuentaspagar->mostrar($idcpc);
		echo json_encode($rspta);
		break;

		case 'amortizar_deuda':
			$deuda = $_POST['montoPagarAmortizar'];
			$idcliente = $_POST['idcliente_amortizar'];
			$fecha_inicio = $_POST['fecha_inicio_amortizar'];
			$fecha_fin = $_POST['fecha_fin_amortizar'];
			$formapago= $_POST['formapagoAmortizar'];
			$montopago= $_POST['montoPagarAmortizar'];
            $rspta=$cuentaspagar->amortizarDeuda($deuda, $idcliente, $fecha_inicio, $fecha_fin, $formapago, $montopago);
        	echo json_encode($rspta);
        	break;

	}

?>