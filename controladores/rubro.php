<?php 
require_once "../modelos/Rubro.php";

$rubro=new Rubro();

$idrubro=isset($_POST["idrubro"])? limpiarCadena($_POST["idrubro"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($idrubro)){
			$rspta=$rubro->insertar($nombre);
			echo $rspta ? "Rubro registrado" : "Rubro no se pudo registrar";
		}
		else {
			$rspta=$rubro->editar($idrubro,$nombre);
			echo $rspta ? "Rubro actualizado" : "Rubro no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$rubro->desactivar($idrubro);
 		echo $rspta ? "Rubro Desactivado" : "Rubro no se puede desactivar";
 		break;
	break;

	case 'activar':
		$rspta=$rubro->activar($idrubro);
 		echo $rspta ? "Rubro activado" : "Rubro no se puede activar";
 		break;
	break;

	case 'mostrar':
		$rspta=$rubro->mostrar($idrubro);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
 		break;
	break;

	case 'listar':
		$rspta=$rubro->listar();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
                
 				"0"=>$reg->nombre,
 				"1"=>($reg->condicion)?'<span class="badge bg-green">ACTIVADO</span>':
 				'<span class="badge bg-red">DESACTIVADO</span>',
 				"2"=>($reg->condicion)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idrubro.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idrubro.')"><i class="fas fa-times-circle"></i></button>':
 					'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idrubro.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->idrubro.')"><i class="fa fa-check"></i></button>'
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