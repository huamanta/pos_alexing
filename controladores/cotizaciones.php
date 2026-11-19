<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../modelos/Cotizaciones.php';
require_once __DIR__ . '/../modelos/Helpers.php';

$venta = new Cotizacion();

$idcotizacion = isset($_GET["idcotizacion"]) ? limpiarCadena($_GET["idcotizacion"]) : "";
$idsucursal = $_SESSION['idsucursal'] ?? (isset($_POST["idsucursal"]) ? limpiarCadena($_POST["idsucursal"]) : "");
$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
$idpersonal = $_SESSION["idpersonal"];
$tipo_comprobante = isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";
$serie_comprobante = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";
$num_comprobante = isset($_POST["num_comprobante"]) ? limpiarCadena($_POST["num_comprobante"]) : "";
$fecha = isset($_POST["fecha"]) ? limpiarCadena($_POST["fecha"]) : "";
$impuesto = isset($_POST["impuesto"]) ? limpiarCadena($_POST["impuesto"]) : "";
$total_venta = isset($_POST["total_venta"]) ? limpiarCadena($_POST["total_venta"]) : "";

$totalrecibido = isset($_POST["totalrecibido"]) ? limpiarCadena($_POST["totalrecibido"]) : "";

$titulo = isset($_POST["titulo"]) ? limpiarCadena($_POST["titulo"]) : "";
$saludo = isset($_POST["saludo"]) ? limpiarCadena($_POST["saludo"]) : "";
$nota = isset($_POST["nota"]) ? limpiarCadena($_POST["nota"]) : "";

$igv = isset($_POST["igv"]) ? limpiarCadena($_POST["igv"]) : "";

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
$fecha_hora = isset($_POST["fecha_hora"]) ? limpiarCadena($_POST["fecha_hora"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarCadena($_POST["observaciones"]) : "";
$formapago = isset($_POST["formapago"]) ? limpiarCadena($_POST["formapago"]) : "";
$tiempoproduccion = isset($_POST["tiempoproduccion"]) ? limpiarCadena($_POST["tiempoproduccion"]) : "";

$inicial = isset($_POST["inicial"]) ? limpiarCadena($_POST["inicial"]) : "";
$frecuencia = isset($_POST["input_frecuencia"]) ? limpiarCadena($_POST["input_frecuencia"]) : "";
$meses = isset($_POST["numeroMeses"]) ? limpiarCadena($_POST["numeroMeses"]) : "";
$cuotas = isset($_POST["input_cuotas"]) ? limpiarCadena($_POST["input_cuotas"]) : "";
$interes = isset($_POST["inputInteres"]) ? limpiarCadena($_POST["inputInteres"]) : "";

switch ($_GET["op"]) {

	case 'guardaryeditar':

		if (empty($idcotizacion)) {
			$venta->insertar(
				$idsucursal,
				$idcliente,
				$idpersonal,
				$tipo_comprobante,
				$fecha,
				$total_venta,
				$titulo,
				$saludo,
				$nota,
				$igv,
				$formapago,
				$observaciones,
				$tiempoproduccion,
				$_POST["idproducto"],
				$_POST["cantidad"],
				$_POST["precio_venta"],
				$_POST["descuento"],
				$_POST["contenedor"],
				$_POST["cantidad_contenedor"],
				$_POST["idp"],
				$inicial,
				$frecuencia,
				$meses,
				$cuotas,
				$interes,
				$_POST["idserie"]
			);
		} else {
			$venta->editar(
				$idcotizacion,
				$idsucursal,
				$idcliente,
				$idpersonal,
				$tipo_comprobante,
				$fecha,
				$total_venta,
				$titulo,
				$saludo,
				$nota,
				$igv,
				$formapago,
				$observaciones,
				$tiempoproduccion,
				$_POST["idproducto"],
				$_POST["cantidad"],
				$_POST["precio_venta"],
				$_POST["descuento"],
				$_POST["contenedor"],
				$_POST["cantidad_contenedor"],
				$_POST["idp"],
				$inicial,
				$frecuencia,
				$meses,
				$cuotas,
				$interes,
				$_POST["idserie"]
			);
		}

		break;

	case 'guardarCliente':
		if (empty($idpersona)) {
			$rspta = $persona->insertar($tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_hora);
			echo $rspta ? "Cliente registrado" : "Cliente no se pudo registrar";
		}
		break;

	case 'eliminar':
		$venta->eliminar($idcotizacion);
		break;

	case 'mostrar':
		$venta->mostrar($idcotizacion);
		break;

	case 'desistir':
		$venta->desistir($idcotizacion);
		break;

	case 'mostrar_s_ticket':
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();
		$idsucursal = $_SESSION["idsucursal"];
		$idtipo_comprobante = $_GET["idtipo_comprobante"];
		$comprobantes->mostrarSerieTicket($idsucursal, $idtipo_comprobante);
		break;

	case 'listarDetalle':;
		$id = $_GET['id'];
		$venta->listarDetalle($id);
		break;

	case 'listarDetalleCotizacion':
		echo $venta->listarDetalleCotizacion($idcotizacion);
		break;

	case 'listar':

		$fecha_inicio = $_REQUEST["fecha_inicio"];
		$fecha_fin = $_REQUEST["fecha_fin"];
		$idsucursal = $_SESSION['idsucursal'];
		echo $venta->listar($fecha_inicio, $fecha_fin, $idsucursal);
		break;

	case 'selectVendedor':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarv();

		echo '<option value="Todos">Todos</options>';

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idpersonal . '>' . $reg->nombre . ' - ' . $reg->num_documento . '</option>';
		}
		break;

	case 'listarArticulos':
		$idsucursal = $_SESSION["idsucursal"];
		require_once "../modelos/Producto.php";
		$producto = new Producto();
		echo $producto->listarActivosVenta($idsucursal);
		break;

	case 'selectComprobante':
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();
		$idsucursal = $_SESSION["idsucursal"];
		$rspta = $comprobantes->select2($idsucursal);

		while ($reg = $rspta->fetch_object()) {
			echo '<option value="' . $reg->idcomprobante_pago . '" selected>' . $reg->nombre . '</option>';
		}
		break;

	case 'selectCotizaciones':
		$idsucursal = $_SESSION['idsucursal'];
		$is_aprobated = $_POST['is_aprobated'] ?? false; // Valor predeterminado si no se proporciona
		$venta->selectCotizaciones($idsucursal, $is_aprobated);
		break;

	case 'buscarProducto':
		$codigo = $_REQUEST["codigo"];
		$venta->buscarProducto($codigo);
		break;

	case 'cotizacionesCliente':
		$idsucursal = $_SESSION['idsucursal'];
		$idcliente = $_GET["idcliente"];
		$is_aprobated = $_GET["is_aprobated"] ?? false;
		$venta->cotizacionesCliente($idsucursal, $idcliente, $is_aprobated);
		break;
}
