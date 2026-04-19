<?php 
require_once "../modelos/Modelo.php";

$modelo=new Modelo();

$idmodelo=isset($_POST["idmodelo"])? limpiarCadena($_POST["idmodelo"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$descripcion=isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($idmodelo)){
			$rspta=$modelo->insertar($nombre, $descripcion);
			echo $rspta ? "Modelo registrado" : "Modelo no se pudo registrar";
		}
		else {
			$rspta=$modelo->editar($idmodelo,$nombre, $descripcion);
			echo $rspta ? "Modelo actualizado" : "Modelo no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$modelo->desactivar($idmodelo);
 		echo $rspta ? "Modelo Desactivado" : "Modelo no se puede desactivar";
	break;

	case 'activar':
		$rspta=$modelo->activar($idmodelo);
 		echo $rspta ? "Modelo activado" : "Modelo no se puede activar";
	break;

	case 'mostrar':
		$rspta=$modelo->mostrar($idmodelo);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
	break;

	case 'listar':
		$rspta=$modelo->listar();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>$reg->nombre,
 				"1"=>$reg->descripcion,
 				"2"=>($reg->estado)?'<span class="badge bg-green">ACTIVADO</span>':
 				'<span class="badge bg-red">DESACTIVADO</span>',
 				"3"=>($reg->estado)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idmodelo.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idmodelo.')"><i class="fas fa-times-circle"></i></button>':
 					'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idmodelo.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->idmodelo.')"><i class="fa fa-check"></i></button>'
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;
}
?>