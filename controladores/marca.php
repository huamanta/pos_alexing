<?php 
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once "../modelos/Marca.php";

$marca=new Marca();

$idmarca=isset($_POST["idmarca"])? limpiarCadena($_POST["idmarca"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$descripcion=isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($idmarca)){
			$rspta=$marca->insertar($nombre, $descripcion);
			echo $rspta ? "Marca registrada" : "Marca no se pudo registrar";
		}
		else {
			$rspta=$marca->editar($idmarca,$nombre, $descripcion);
			echo $rspta ? "Marca actualizada" : "Marca no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$marca->desactivar($idmarca);
 		echo $rspta ? "Marca Desactivada" : "Marca no se puede desactivar";
	break;

	case 'activar':
		$rspta=$marca->activar($idmarca);
 		echo $rspta ? "Marca activada" : "Marca no se puede activar";
	break;

	case 'mostrar':
		$rspta=$marca->mostrar($idmarca);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
	break;

	case 'listar':
		$rspta=$marca->listar();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>$reg->nombre,
 				"1"=>$reg->descripcion,
 				"2"=>($reg->estado)?'<span class="badge bg-green">ACTIVADO</span>':
 				'<span class="badge bg-red">DESACTIVADO</span>',
 				"3"=>($reg->estado)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idmarca.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idmarca.')"><i class="fas fa-times-circle"></i></button>':
 					'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idmarca.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->idmarca.')"><i class="fa fa-check"></i></button>'
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