<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once "../modelos/Persona.php";
$persona = new Persona();

$idpersona = isset($_POST["idpersona"]) ? limpiarCadena($_POST["idpersona"]) : "";
$tipo_persona = isset($_POST["tipo_persona"]) ? limpiarCadena($_POST["tipo_persona"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
$num_documento = isset($_POST["num_documento"]) ? limpiarCadena($_POST["num_documento"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
$latitude = isset($_POST["latitude"]) ? limpiarCadena($_POST["latitude"]) : "";
$longitude = isset($_POST["longitude"]) ? limpiarCadena($_POST["longitude"]) : "";
$fecha_hora = isset($_POST["fecha_hora"]) ? limpiarCadena($_POST["fecha_hora"]) : "";

switch ($_GET["op"]) {
	case 'guardaryeditar':
		if (empty($idpersona)) {
			$rspta = $persona->insertar($tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_hora, $latitude, $longitude);
			echo $rspta;
		} else {
			$rspta = $persona->editar($idpersona, $tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_hora, $latitude, $longitude);
			echo $rspta;
		}
		break;
	case 'eliminar':
		$rspta = $persona->eliminar($idpersona);
		echo $rspta;
		break;

	case 'eliminar2':
		$rspta = $persona->eliminar2($idpersona);
		if ($rspta == 2) {
			$res = 2;
		} else if ($rspta == 1) {
			$res = 1;
		} else {
			$res = 3;
		}
		echo $res;
		break;

	case 'mostrar':
		$rspta = $persona->mostrar($idpersona);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);
		break;

	case 'listarp':
		$rspta = $persona->listarp();
		echo $rspta;
		break;

	case 'listarc':
		$rspta = $persona->listarc($tipo_documento = "", $excluirId = true);
		echo $rspta;
		break;


	case 'scorecrediticiocliente':
		$idcliente = $_GET["idcliente"];
		$rspta = $persona->scoreCrediticioCliente($idcliente);
		echo $rspta;
		break;

}
?>