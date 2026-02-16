<?php 
require_once "../modelos/NombresPrecios.php";

$nombresprecios=new NombresPrecios();

$idnombre_p=isset($_POST["idnombre_p"])? limpiarCadena($_POST["idnombre_p"]):"";
$descripcion=isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($idnombre_p)){
			$rspta=$nombresprecios->insertar($descripcion);
			echo $rspta ? "Precio registrado" : "El precio no se pudo registrar";
		}
		else {
			$rspta=$nombresprecios->editar($idnombre_p,$descripcion);
			echo $rspta ? "Precio actualizado" : "El precio no se pudo actualizar";
		}
	break;

	case 'desactivar':
		$rspta=$nombresprecios->desactivar($idnombre_p);
 		echo $rspta ? "Precio Desactivado" : "El precio no se puede desactivar";
	break;

	case 'activar':
		$rspta=$nombresprecios->activar($idnombre_p);
 		echo $rspta ? "Precio activado" : "El precio no se puede activar";
	break;

	case 'mostrar':
		$rspta=$nombresprecios->mostrar($idnombre_p);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
	break;

	case 'listar':
    $rspta = $nombresprecios->listar();
    $data = array();
    while ($reg = $rspta->fetch_object()) {
        $data[] = array(
            "0" => $reg->descripcion,
            "1" => ($reg->estado) ? '<span class="badge bg-green">ACTIVADO</span>' :
                                     '<span class="badge bg-red">DESACTIVADO</span>',
            "2" => ($reg->estado) ?
                '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idnombre_p.')"><i class="fas fa-edit"></i></button>' .
                ' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idnombre_p.')"><i class="fas fa-times-circle"></i></button>' :
                '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idnombre_p.')"><i class="fas fa-edit"></i></button>' .
                ' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->idnombre_p.')"><i class="fa fa-check"></i></button>'
        );
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    );

    echo json_encode($results);
break;

}
