<?php 
require_once "../modelos/Categoria.php";

$categoria=new Categoria();

$idsucursal=isset($_POST["idsucursal"])? limpiarCadena($_POST["idsucursal"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$direccion=isset($_POST["direccion"])? limpiarCadena($_POST["direccion"]):"";
$telefono=isset($_POST["telefono"])? limpiarCadena($_POST["telefono"]):"";
$distrito=isset($_POST["distrito"])? limpiarCadena($_POST["distrito"]):"";
$provincia=isset($_POST["provincia"])? limpiarCadena($_POST["provincia"]):"";
$departamento=isset($_POST["departamento"])? limpiarCadena($_POST["departamento"]):"";
$ubigeo=isset($_POST["ubigeo"])? limpiarCadena($_POST["ubigeo"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($idsucursal)){
			$rspta=$categoria->insertarSucursal($nombre,$direccion,$telefono,$_POST["nombreSucursal"],$_POST["serie"],$_POST["numero"],$distrito,$provincia,$departamento,$ubigeo);
			echo $rspta ? "Sucursal registrada" : "Sucursal no se pudo registrar";
		}
		else {
			$rspta = $categoria->editarSucursal($idsucursal,$nombre,$direccion,$telefono,$distrito,$provincia,$departamento,$ubigeo);
		    $categoria->actualizarComprobantes($idsucursal,$_POST["nombreSucursal"],$_POST["serie"],$_POST["numero"]);
		    echo $rspta ? "Sucursal actualizada" : "Sucursal no se pudo actualizar";
				}
	break;

	case 'desactivar':
		$rspta=$categoria->desactivar($idsucursal);
 		echo $rspta ? "Categoría Desactivada" : "Categoría no se puede desactivar";
 		break;
	break;

	case 'activar':
		$rspta=$categoria->activar($idsucursal);
 		echo $rspta ? "Categoría activada" : "Categoría no se puede activar";
 		break;
	break;

	case 'mostrarSucursal':
	    $rspta = $categoria->mostrarSucursal($idsucursal);
	    $data = array();

	    while ($reg = $rspta->fetch_object()) {
	        $data[] = array(
	            "idsucursal" => $reg->idsucursal,
	            "nombre" => $reg->nombre,
	            "direccion" => $reg->direccion,
	            "telefono" => $reg->telefono,
	            "distrito" => $reg->distrito,
	            "provincia" => $reg->provincia,
	            "departamento" => $reg->departamento,
	            "ubigeo" => $reg->ubigeo,
	            "comprobantes" => array(
	                "id_comp_pago" => $reg->id_comp_pago,
	                "nombre" => $reg->comp_nombre,
	                "serie" => $reg->serie_comprobante,
	                "numero" => $reg->num_comprobante
	            )
	        );
	    }

	    echo json_encode($data);
	break;


	case 'listarSucursales':
		$rspta=$categoria->listarSucursales();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>$reg->nombre,
 				"1"=>'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idsucursal.')"><i class="fas fa-edit"></i></button>'.
 				     ' <button class="btn btn-danger btn-xs" onclick="eliminar('.$reg->idsucursal.')"><i class="fa fa-trash"></i></button>'
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;

	case 'listar':
		$rspta=$categoria->listar();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>$reg->nombre,
 				"1"=>($reg->condicion)?'<span class="badge bg-green">ACTIVADO</span>':
 				'<span class="badge bg-red">DESACTIVADO</span>',
 				"2"=>($reg->condicion)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idcategoria.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idcategoria.')"><i class="fa fa-close"></i></button>':
 					'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idcategoria.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->idcategoria.')"><i class="fa fa-check"></i></button>'
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;

	case 'eliminar':
		$rspta = $categoria->eliminarSucursal($idsucursal);
		echo $rspta ? "Sucursal eliminada" : "Sucursal no se pudo eliminar";
		break;

	case 'obtenerUltimaSerie':
	    $rspta = $categoria->obtenerUltimaSerie();
	    echo json_encode($rspta);
	break;

	case 'listarDepartamentos':
		$rspta=$categoria->listarDepartamentos();
 		$data= Array();
 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"id"=>$reg->id,
 				"name"=>$reg->name
 				);
 		}
 		echo json_encode($data);
 	break;

	case 'listarProvinciasPorDepartamento':
		$id_department = isset($_POST["id_department"])? limpiarCadena($_POST["id_department"]):"";
		$rspta=$categoria->listarProvinciasPorDepartamento($id_department);
 		$data= Array();
 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"id"=>$reg->id,
 				"name"=>$reg->name
 				);
 		}
 		echo json_encode($data);
 	break;

	case 'listarDistritosPorProvincia':
		$id_province = isset($_POST["id_province"])? limpiarCadena($_POST["id_province"]):"";
		$rspta=$categoria->listarDistritosPorProvincia($id_province);
 		$data= Array();
 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"id"=>$reg->id,
 				"name"=>$reg->name
 				);
 		}
 		echo json_encode($data);
 	break;

}
?>