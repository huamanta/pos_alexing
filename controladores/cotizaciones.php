<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../modelos/Cotizaciones.php';
require_once __DIR__ . '/../modelos/Helpers.php';

$helpers = new Helpers();

// if (!isset($_SESSION['cotizacion_token'])) {
// 	// Generar token único para esta cotización temporal
// 	$_SESSION['cotizacion_token'] = bin2hex(random_bytes(8)); // 16 caracteres
// }
// $token = $_SESSION['cotizacion_token'];


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
		$rspta = $venta->eliminar($idcotizacion);
		echo $rspta ? "Cotización Eliminada" : "Cotización No Se Puedo Eliminar";
		break;

	case 'mostrar':
		echo $venta->mostrar($idcotizacion);
		break;

	case 'desistir':
		$rspta = $venta->desistir($idcotizacion);
		echo $rspta ? "Operación Exitosa" : "Operación no se pudo realizar";
		break;

	case 'mostrardetalle':

		//recibimos el idcotizacion
		$id = $_GET['id'];

		$rspta = $venta->mostrardetalle($id);
		$total = 0;
		$c = 1;
		while ($reg = $rspta->fetch_object()) {

			if ($c == 1) {

				echo 'Pedido N° ';

				echo $reg->num_comprobante;

				echo ', CLIENTE: ';

				echo $reg->cliente;

				echo ',  LISTA DE PEDIDO: ';
			}

			echo '(' . $c . ')';
			echo '. ' . $reg->nombre . ',  CANTIDAD:  ' . $reg->cantidad . '     ';
			$c = $c + 1;
		}

		break;

	case 'mostrar_s_ticket':
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();
		$idsucursal = $_SESSION["idsucursal"];
		$idtipo_comprobante = $_GET["idtipo_comprobante"];
		$comprobantes->mostrarSerieTicket($idsucursal, $idtipo_comprobante);
		break;


	case 'listarDetalle':
		$idsucursal = $_SESSION["idsucursal"];
		$smoneda = $helpers->get_symbol();
		// Recibir ID
		$id = $_GET['id'];

		$rspta = $venta->listarDetalle($id);

		$total = 0;

		echo '
			<style>
				.table-detalle {
					width: 100%;
					border-collapse: collapse;
					background: #fff;
					border-radius: 12px;
					overflow: hidden;
					box-shadow: 0 2px 10px rgba(0,0,0,.05);
				}

				.table-detalle thead {
					background: linear-gradient(90deg, #007bff, #0056b3);
					color: #fff;
				}

				.table-detalle thead th {
					padding: 14px;
					font-size: 13px;
					font-weight: 600;
					text-align: center;
					border: none;
				}

				.table-detalle tbody td {
					padding: 12px;
					font-size: 13px;
					border-bottom: 1px solid #f1f1f1;
					text-align: center;
					vertical-align: middle;
				}

				.table-detalle tbody tr:hover {
					background: #f8fbff;
					transition: .2s;
				}

				.producto-name {
					font-weight: 600;
					color: #343a40;
				}

				.badge-cantidad {
					background: #eef4ff;
					color: #0056b3;
					padding: 6px 10px;
					border-radius: 20px;
					font-size: 12px;
					font-weight: 600;
					display: inline-block;
				}

				.precio {
					color: #28a745;
					font-weight: 600;
				}

				.descuento {
					color: #dc3545;
					font-weight: 600;
				}

				.subtotal {
					font-weight: bold;
					color: #212529;
				}

				.tfoot-total {
					background: #f8f9fa;
				}

				.tfoot-total th {
					padding: 16px;
					font-size: 14px;
					border-top: 2px solid #dee2e6;
				}

				.total-box {
					background: linear-gradient(135deg, #28a745, #1e7e34);
					color: #fff;
					padding: 10px 15px;
					border-radius: 10px;
					display: inline-block;
					font-size: 18px;
					font-weight: bold;
					min-width: 140px;
					text-align: center;
					box-shadow: 0 3px 8px rgba(40,167,69,.25);
				}
			</style>

			<table class="table-detalle">

				<thead>
					<tr>
						<th style="width: 35%;">Producto</th>
						<th style="width: 15%;">Cantidad</th>
						<th style="width: 15%;">Precio Venta</th>
						<th style="width: 15%;">Descuento</th>
						<th style="width: 20%;">Subtotal</th>
					</tr>
				</thead>

				<tbody>
			';

		while ($reg = $rspta->fetch_object()) {

			echo '
				<tr class="filas">

					<td>
						<div class="producto-name">
							' . $reg->nombre . '
						</div>
					</td>

					<td>
						<span class="badge-cantidad">
							' . $reg->cantidad . ' - ' . $reg->contenedor . '
						</span>
					</td>

					<td>
						<span class="precio">
							' . $smoneda . ' ' . number_format($reg->precio_venta, 2) . '
						</span>
					</td>

					<td>
						<span class="descuento">
							' . $smoneda . ' ' . number_format($reg->descuento, 2) . '
						</span>
					</td>

					<td>
						<span class="subtotal">
							' . $smoneda . ' ' . number_format($reg->subtotal, 2) . '
						</span>
					</td>

				</tr>
				';

			$total += $reg->total_venta;
		}

		echo '
				</tbody>

				<tfoot class="tfoot-total">
					<tr>
						<th colspan="4" style="text-align:right;">
							TOTAL GENERAL
						</th>

						<th style="text-align:center;">
							<div class="total-box">
								' . $smoneda . ' ' . number_format($total, 2) . '
							</div>

							<input type="hidden" 
								name="total_venta" 
								id="total_venta" 
								value="' . $total . '">
						</th>
					</tr>
				</tfoot>

			</table>
			';

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

	// case 'selectCliente':
	// 	require_once "../modelos/Persona.php";
	// 	$persona = new Persona();

	// 	$rspta = $persona->listarc();

	// 	while ($reg = $rspta->fetch_object()) {
	// 		echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . ' - ' . $reg->num_documento . '</option>';
	// 	}
	// 	break;

	// case 'selectProducto':
	// 	require_once "../modelos/Producto.php";
	// 	$persona = new Producto();

	// 	$rspta = $persona->listar();

	// 	echo '<option value="Todos">Todos</options>';

	// 	while ($reg = $rspta->fetch_object()) {
	// 		echo '<option value=' . $reg->idproducto . '>' . $reg->nombre . '</option>';
	// 	}
	// 	break;

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
		$rspta = $venta->listar2($idsucursal, $is_aprobated);

		while ($reg = $rspta->fetch_object()) {
			echo '<option value="' . $reg->idcotizacion . '">' . $reg->serie_comprobante . '-' . $reg->num_comprobante . ': ' . $reg->cliente . '</option>';
		}
		break;

	case 'buscarProducto':

		$codigo = $_REQUEST["codigo"];

		$rspta = $venta->buscarProducto($codigo);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);

		break;

	// ======== CARRITO TEMPORAL ========

	case 'agregarTmp':
		$rspta = $venta->agregarTemporal(
			$idpersonal,
			$_POST['idproducto'],
			$_POST['cantidad'],
			$_POST['precio_venta'],
			$_POST['descuento'],
			$_POST['contenedor'],
			$_POST['cantidad_contenedor'],
			$_POST['idp']
		);
		echo $rspta ? $rspta : "Error al agregar producto temporal";
		break;

	case 'actualizarTmp':
		$idtmp = isset($_POST['idtmp']) ? limpiarCadena($_POST['idtmp']) : '';
		$cantidad = isset($_POST['cantidad']) ? limpiarCadena($_POST['cantidad']) : '';
		$precio_venta = isset($_POST['precio_venta']) ? limpiarCadena($_POST['precio_venta']) : '';

		if (!empty($idtmp) && !empty($cantidad) && !empty($precio_venta)) {
			$rspta = $venta->actualizarTemporal($idtmp, $cantidad, $precio_venta);
			echo $rspta ? "Temporal actualizado" : "No se pudo actualizar";
		} else {
			echo "Datos incompletos";
		}
		break;

	case 'listarTmp':
		$rspta = $venta->listarTmp($idpersonal);
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"idtmp" => $reg->idtmp,
				"idproducto" => $reg->idproducto,
				"idp" => $reg->idp,
				"nombre" => $reg->nombre,
				"contenedor" => $reg->contenedor,
				"cantidad_contenedor" => $reg->cantidad_contenedor,
				"cantidad" => $reg->cantidad,
				"precio_venta" => $reg->precio_venta,
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

	case 'eliminarTmp':

		$idtmp = isset($_POST['idtmp']) ? intval($_POST['idtmp']) : 0;
		$sessionUser = $_SESSION['idusuario'] ?? null;
		$sessionPersonal = $_SESSION['idpersonal'] ?? null;

		if (!$idtmp) {
			echo "ID temporal inválido.";
			break;
		}

		if (!$sessionUser && !$sessionPersonal) {
			echo "Usuario no autenticado.";
			break;
		}

		// Buscar el propietario del registro
		$sql_owner = "SELECT idusuario FROM cotizacion_tmp WHERE idtmp = '$idtmp' LIMIT 1";
		$row = ejecutarConsultaSimpleFila($sql_owner);

		if (!$row) {
			echo "Registro temporal no encontrado.";
			break;
		}

		$owner = $row['idusuario'];

		// Verificar que el usuario sea el dueño del registro
		if ($owner != $sessionUser && $owner != $sessionPersonal) {
			echo "No puedes eliminar este ítem (pertenece a otro usuario).";
			break;
		}

		// Ejecutar eliminación
		$rspta = $venta->eliminarTemporal($idtmp, $owner);

		echo $rspta ? "Eliminado del carrito temporal" : "No se pudo eliminar o no existe.";
		break;

	case 'cotizacionesCliente':
		$idsucursal = $_SESSION['idsucursal'];
		$idcliente = $_GET["idcliente"];
		$is_aprobated = $_GET["is_aprobated"] ?? false;
		$rspta = $venta->cotizacionesCliente($idsucursal, $idcliente, $is_aprobated);
		echo '<option value="">Seleccione una cotización</option>';
		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idcotizacion . '>' . $reg->serie_comprobante . '-' . $reg->num_comprobante . '</option>';
		}
		break;



}
