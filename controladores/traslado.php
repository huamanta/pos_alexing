<?php
require_once "../modelos/Traslado.php";
session_start();
$traslado = new Traslado();
$idusuario = $_SESSION['idusuario'] ?? 0;
$idtraslado = isset($_POST["idtraslado"]) ? limpiarCadena($_POST["idtraslado"]) : "";
$idorigen = isset($_POST["idorigen"]) ? limpiarCadena($_POST["idorigen"]) : $_SESSION['idsucursal'];
$iddestino = isset($_POST["iddestino"]) ? limpiarCadena($_POST["iddestino"]) : "";
$fecha = date("Y-m-d H:i:s");
$productos = isset($_POST["productos"]) ? $_POST["productos"] : "";

switch ($_GET["op"]) {
	case 'guardaryeditar':
		if (empty($idtraslado)) {
			$rspta = $traslado->insertar($idorigen, $iddestino, $fecha, $productos, $idusuario);
			echo $rspta;
		} else {
			$rspta = $traslado->editar($idtraslado, $idorigen, $iddestino, $fecha, $idusuario);
			echo $rspta ? "Traslado actualizado correctamente" : "No se pudo actualizar el traslado";
		}
		break;

	case 'aceptar':
		$idtraslado = $_POST['idtraslado'];
		$idusuario = $_SESSION['idusuario'];
		$rspta = $traslado->aceptarTraslado($idtraslado, $idusuario);
		echo $rspta;
		break;

	case 'listarnoti':
		$idsucursal = intval($_GET['idsucursal'] ?? 0);
		$rspta = $traslado->listarNotificaciones($idsucursal);
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				"idnotificacion" => $reg->idnotificacion,
				"mensaje" => $reg->mensaje,
				"leido" => $reg->leido,
				"fecha" => $reg->fecha,
				"idtraslado" => $reg->idtraslado,
				"tipo" => $reg->tipo,
				"iddestino" => $reg->iddestino ?? null
			];
		}
		echo json_encode($data);
		break;

	case 'listar':
		$fecha_inicio = $_REQUEST["fecha_inicio"];
		$fecha_fin = $_REQUEST["fecha_fin"];
		$estado = $_REQUEST["estado"];
		$tipo = $_REQUEST["tipo"];
		$idsucursal = $_SESSION['idsucursal'];
		$origen = !empty($_REQUEST['origen']) ? true : false;

		$rspta = $traslado->listar($fecha_inicio, $fecha_fin, $estado, $idsucursal, $tipo, $origen);
		echo $rspta;
		break;


	case 'verdetalle':
		$idtraslado = $_GET['idtraslado'];
		$rspta = $traslado->listarDetalle($idtraslado);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"producto" => $reg->producto,
				"cantidad" => $reg->cantidad,
				"destino" => $reg->destino
			);
		}
		echo json_encode($data);
		break;

	case 'listarSucursales':
		$rspta = $traslado->listarSucursales();
		echo $rspta;
		break;


	case 'almacenesDestino':
		$idsucursal = $_SESSION['idsucursal'];
		$rspta = $traslado->sucursales($idsucursal);
		while ($reg = $rspta->fetch_object()) {
			if ($reg->idsucursal != $idsucursal)
				echo '<option value="' . $reg->idsucursal . '">' . htmlspecialchars($reg->nombre) . '</option>';
		}
		break;

	// case 'listarProductos':
	// 	$idsucursal = $_SESSION['idsucursal'];
	// 	$busqueda = isset($_POST["busqueda"]) ? limpiarCadena($_POST["busqueda"]) : '';
	// 	$pagina = isset($_POST["pagina"]) ? intval($_POST["pagina"]) : 1;
	// 	$limite = isset($_POST["limite"]) ? intval($_POST["limite"]) : 10;
	// 	$iddestino = isset($_POST["iddestino"]) ? intval($_POST["iddestino"]) : null;
	// 	$tipo = isset($_POST["tipo"]) ? limpiarCadena($_POST["tipo"]) : 'traslado';
	// 	$rspta = $traslado->listarProductos($idsucursal, $busqueda, $pagina, $limite, $iddestino, $tipo);
	// 	echo json_encode($rspta);
	// 	break;


	case 'sucursal_actual':
		require_once "../modelos/Categoria.php";
		$categoria = new Categoria();
		$rspta = $categoria->mostrarSuc($_SESSION['idsucursal']);
		echo json_encode($rspta);
		break;

	// Marcar una notificación como leída
	case 'marcarleida':
		$idnotificacion = intval($_POST['idnotificacion'] ?? 0);
		$rspta = $traslado->marcarLeida($idnotificacion);
		echo $rspta ? json_encode(["status" => 1, "message" => "Notificación marcada como leída"])
			: json_encode(["status" => 0, "message" => "Error al marcar notificación"]);
		break;


	case 'guardarSolicitud':
		$idorigen = $_SESSION['idsucursal'];
		$iddestino = $_POST['iddestino_solicitud'];
		$productos = $_POST['productos'];
		$idusuario = $_SESSION['idusuario'];
		$fecha = date("Y-m-d H:i:s");
		// Insertar cabecera de solicitud con estado 0 (pendiente)
		$idtraslado = ejecutarConsulta_retornarID("INSERT INTO traslado (idorigen, iddestino, fecha, estado, idusuario, tipo) 
												VALUES ('$idorigen','$iddestino','$fecha','0','$idusuario', 'solicitud')");

		if (!$idtraslado) {
			echo "Error al crear la solicitud";
			exit;
		}

		$productos = json_decode($productos, true);
		foreach ($productos as $p) {
			$idproducto = intval($p['idproducto']);
			$idserie = intval($p['idserie']);
			$cantidad = floatval($p['cantidad']);
			ejecutarConsulta("INSERT INTO traslado_detalle (idtraslado, idproducto, idserie, cantidad) 
							VALUES ('$idtraslado','$idproducto', '$idserie', '$cantidad')");
		}

		// Crear notificación para almacén destino
		$mensaje = "Nueva solicitud pendiente desde el almacén {$_SESSION['idsucursal']} con ID $idtraslado";
		ejecutarConsulta("INSERT INTO notificaciones (idsucursal, idtraslado, mensaje) VALUES ('$iddestino', '$idtraslado', '$mensaje')");

		echo " Solicitud enviada correctamente";
		break;

	case 'aprobarSolicitud':
		$idtraslado = $_POST["idtraslado"];
		$productos = json_decode($_POST["productos"], true); // array con productos aceptados/rechazados
		$idusuario = $_SESSION['idusuario'];

		$rspta = $traslado->aprobarSolicitud($idtraslado, $productos, $idusuario);
		echo $rspta;
		break;

	case 'verproductos2':
		$idtraslado = isset($_POST['idtraslado']) ? $_POST['idtraslado'] : 0;

		if ($idtraslado == 0) {
			echo json_encode([]);
			exit;
		}

		// Llamar al modelo para obtener productos
		$rspta = $traslado->verProductosSolicitud($idtraslado);
		$data = [];

		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				'idproducto' => $reg->idproducto,
				'nombre' => $reg->nombre,
				'cantidad' => $reg->cantidad
			];
		}

		echo json_encode($data);
		break;

	case 'verProductosSolicitud':
		$idtraslado = isset($_POST["idtraslado"]) ? intval($_POST["idtraslado"]) : 0;
		$soloLectura = isset($_POST["soloLectura"]) ? $_POST["soloLectura"] : false;

		if ($idtraslado <= 0) {
			echo json_encode(["error" => "ID de traslado inválido."]);
			exit;
		}

		$rspta = $traslado->verProductosSolicitud($idtraslado);

		$productos = [];
		while ($reg = $rspta->fetch_object()) {
			$productos[] = [
				"idproducto" => $reg->idproducto,
				"nombre" => $reg->nombre,
				"cantidad" => $reg->cantidad,
				"estado_detalle" => $reg->estado_detalle ?? 'pendiente',
				"observacion" => $reg->observacion ?? ''
			];
		}

		echo json_encode(["productos" => $productos, "soloLectura" => $soloLectura]);
		break;

	case 'obtenerSucursalOrigen':
		$idtraslado = $_POST['idtraslado'];
		$rspta = $traslado->obtenerSucursalOrigen($idtraslado);
		echo json_encode($rspta);
		break;

}
?>