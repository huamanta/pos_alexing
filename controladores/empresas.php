<?php
require_once "../modelos/Empresas.php";
$empresa = new Empresa();
$op = isset($_GET["op"]) ? $_GET["op"] : '';

//Obtenemos las variables del formulario
$ruc = isset($_POST["ruc"]) ? limpiarCadena($_POST["ruc"]) : "";
$razon_social = isset($_POST["razon_social"]) ? limpiarCadena($_POST["razon_social"]) : "";
$usuario_sol = isset($_POST["usuario_sol"]) ? limpiarCadena($_POST["usuario_sol"]) : "";
$clave_sol = isset($_POST["clave_sol"]) ? limpiarCadena($_POST["clave_sol"]) : "";
$ruta_certificado = isset($_POST["ruta_certificado"]) ? limpiarCadena($_POST["ruta_certificado"]) : "";
$clave_certificado = isset($_POST["clave_certificado"]) ? limpiarCadena($_POST["clave_certificado"]) : "";
$client_id = isset($_POST["client_id"]) ? limpiarCadena($_POST["client_id"]) : "";
$client_secret = isset($_POST["client_secret"]) ? limpiarCadena($_POST["client_secret"]) : "";
$estado_certificado = isset($_POST["estado_certificado"]) ? limpiarCadena($_POST["estado_certificado"]) : "";
$nombre_impuesto = isset($_POST["nombre_impuesto"]) ? limpiarCadena($_POST["nombre_impuesto"]) : "";
$monto_impuesto = isset($_POST["monto_impuesto"]) ? limpiarCadena($_POST["monto_impuesto"]) : "";
$idempresa = isset($_POST["idempresa"]) ? limpiarCadena($_POST["idempresa"]) : "";
$estado = isset($_POST["estado"]) ? limpiarCadena($_POST["estado"]) : "";

switch ($op) {
    case 'listarEmpresas':
        $rspta = $empresa->listarEmpresas();
         		//Vamos a declarar un array
        $data= Array();

 		while ($reg=$rspta->fetch_object()){
		 			$data[]=array(
		 				"0"=>$reg->ruc,
		 				"1"=>$reg->razon_social,
		 				"2"=>$reg->usuario_sol,
		 				"3"=>$reg->estado_certificado,
		 				"4"=>$reg->nombre_impuesto,
		 				"5"=>$reg->monto_impuesto,
		 				"6"=>($reg->estado)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idempresa.')"><i class="fas fa-edit"></i></button>'.
		 					' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idempresa.')"><i class="fas fa-times-circle"></i></button>':
		 					'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idempresa.')"><i class="fas fa-edit"></i></button>'.
		 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->idempresa.')"><i class="fa fa-check"></i></button>'
		 				);
		 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);
        break;

    case 'guardaryeditar':
        $res = $empresa->guardaryeditar($idempresa, $ruc, $razon_social, $usuario_sol, $clave_sol, $ruta_certificado, $clave_certificado, $client_id, $client_secret, $estado_certificado, $nombre_impuesto, $monto_impuesto, $estado);
        echo json_encode($res);
        break;

    case 'mostrarEmpresa':
        $res = $empresa->mostrarEmpresa($idempresa);
        echo json_encode($res);
        break;

    case 'activar_descativar':
        $res = $empresa->activarDesactivar($idempresa, $estado);
        echo json_encode($res);
}