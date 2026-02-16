<?php 
require_once "../modelos/CondicionVenta.php";

$condicionventa=new CondicionVenta();

$idcondicionventa=isset($_POST["idcondicionventa"])? limpiarCadena($_POST["idcondicionventa"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($idcondicionventa)){
			$rspta=$condicionventa->insertar($nombre);
			echo $rspta ? "Unidad de Medida registrada" : "Unidad de Medida no se pudo registrar";
		}
		else {
			$rspta=$condicionventa->editar($idcondicionventa,$nombre);
			echo $rspta ? "Unidad de Medida actualizada" : "Unidad de Medida no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$condicionventa->desactivar($idcondicionventa);
 		echo $rspta ? "Unidad de Medida Desactivada" : "Unidad de Medida no se puede desactivar";
 		break;
	break;

	case 'activar':
		$rspta=$condicionventa->activar($idcondicionventa);
 		echo $rspta ? "Unidad de Medida activada" : "Unidad de Medida no se puede activar";
 		break;
	break;

	case 'mostrar':
		$rspta=$condicionventa->mostrar($idcondicionventa);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
 		break;
	break;

	case 'listar':
		$rspta=$condicionventa->listar();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
                
 				"0"=>$reg->nombre,
 				"1"=>($reg->condicion)?'<span class="badge bg-green">ACTIVADO</span>':
 				'<span class="badge bg-red">DESACTIVADO</span>',
 				"2"=>($reg->condicion)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idcondicionventa.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idcondicionventa.')"><i class="fas fa-times-circle"></i></button>':
 					'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idcondicionventa.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->idcondicionventa.')"><i class="fa fa-check"></i></button>'
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