<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}
require_once __DIR__ . '/../configuraciones/bootstrap.php';

require_once "../modelos/Compra.php";
require_once "../modelos/compras/Compra.php";

$sisCompra = new SisCompra();
$compra = new Compra();

$idcompra = isset($_POST["idcompra"]) ? limpiarCadena($_POST["idcompra"]) : "";
$idsucursal = isset($_POST["idsucursal"]) ? limpiarCadena($_POST["idsucursal"]) : "";
$idproveedor = isset($_POST["idproveedor"]) ? limpiarCadena($_POST["idproveedor"]) : "";
//Almacenar lo que tenemos en la variable sesion
$idpersonal = $_SESSION["idpersonal"];
$tipo_comprobante = isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";
$tipo_c = isset($_POST["tipo_c"]) ? limpiarCadena($_POST["tipo_c"]) : "";
$serie_comprobante = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";
$num_comprobante = isset($_POST["num_comprobante"]) ? limpiarCadena($_POST["num_comprobante"]) : "";
$fecha = isset($_POST["fecha"]) ? limpiarCadena($_POST["fecha"]) : "";
$impuesto = isset($_POST["impuesto"]) ? limpiarCadena($_POST["impuesto"]) : "";
$tipo_igv = isset($_POST["tipo_igv"]) ? limpiarCadena($_POST["tipo_igv"]) : "EXONERADA";
$monto_gravado = isset($_POST["monto_gravado"]) ? limpiarCadena($_POST["monto_gravado"]) : "0";
$monto_exonerado = isset($_POST["monto_exonerado"]) ? limpiarCadena($_POST["monto_exonerado"]) : "0";
$monto_igv = isset($_POST["monto_igv"]) ? limpiarCadena($_POST["monto_igv"]) : "0";
// Actualizar impuesto = 0 si es exonerada
if ($tipo_igv == 'EXONERADA') {
	$impuesto = 0;
} else {
	$impuesto = $monto_igv;
}
$total_compra = isset($_POST["total_compra"]) ? limpiarCadena($_POST["total_compra"]) : "";
$formapago = isset($_POST["formapago"]) ? limpiarCadena($_POST["formapago"]) : "";
$totaldeposito = isset($_POST["totaldeposito"]) ? limpiarCadena($_POST["totaldeposito"]) : "";
$noperacion = isset($_POST["noperacion"]) ? limpiarCadena($_POST["noperacion"]) : "";
$totalrecibido = isset($_POST["totalrecibido"]) ? limpiarCadena($_POST["totalrecibido"]) : "";
$fecha_deposito = isset($_POST["fecha_deposito"]) ? limpiarCadena($_POST["fecha_deposito"]) : "";
$lugar_entrega = isset($_POST["lugar_entrega"]) ? limpiarCadena($_POST["lugar_entrega"]) : "";
$motivo_compra = isset($_POST["motivo_compra"]) ? limpiarCadena($_POST["motivo_compra"]) : "";
$documento = isset($_POST["documento"]) ? limpiarCadena($_POST["documento"]) : "";
$nota = isset($_POST["nota"]) ? limpiarCadena($_POST["nota"]) : "";
$comprobanteReferencia = isset($_POST["comprobanteReferencia"]) ? limpiarCadena($_POST["comprobanteReferencia"]) : "";

$tipopago = isset($_POST["tipopago"]) ? limpiarCadena($_POST["tipopago"]) : "";
$fechaOperacion = isset($_POST["fechaOperacion"]) ? limpiarCadena($_POST["fechaOperacion"]) : "";
$montoPagado = isset($_POST["montoPagado"]) ? limpiarCadena($_POST["montoPagado"]) : "";
$montoDeuda = isset($_POST["montoDeuda"]) ? limpiarCadena($_POST["montoDeuda"]) : "";
$input_cuotas = isset($_POST["input_cuotas"]) ? limpiarCadena($_POST["input_cuotas"]) : "";


$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";
$idcompraI = isset($_POST["idcompraI"]) ? limpiarCadena($_POST["idcompraI"]) : "";
$tipo_pago = isset($_POST['tipo_pago']) ? $_POST['tipo_pago'] : [];
$monto_pago = isset($_POST['monto_pago']) ? $_POST['monto_pago'] : [];
$operacion_pago = isset($_POST['operacion_pago']) ? $_POST['operacion_pago'] : [];
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

switch ($_GET["op"]) {
	case 'guardaryeditar':
		if (empty($idcompra)) {
			if (!empty($_POST["fecha_pago"])) {
				$fecha_pago = $_POST["fecha_pago"];
			} else {
				$fecha_pago = '';
			}
			$rspta = $sisCompra->insertar($idsucursal, $idproveedor, $idpersonal, $tipo_c, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha, $impuesto, $tipo_igv, $monto_gravado, $monto_exonerado, $monto_igv, $total_compra, $formapago, $lugar_entrega, $motivo_compra, $documento, $nota, $comprobanteReferencia, $_POST["idproducto"], $_POST["nombreProducto"], $_POST["cantidad"], $_POST["precio_compra"], $_POST["precio_venta"], $_POST["nlote"], $_POST["fvencimiento"], $tipopago, $fechaOperacion, $input_cuotas, $montoPagado, $montoDeuda, $fecha_pago, $totaldeposito, $noperacion, $totalrecibido, $fecha_deposito, $tipo_pago, $monto_pago, $operacion_pago);
			echo $rspta;
		} else {

			$rspta = $compra->editar($idcompra, $idsucursal, $idproveedor, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha, $impuesto, $tipo_igv, $monto_gravado, $monto_exonerado, $monto_igv, $total_compra, $formapago, $tipopago, $fecha_deposito, $lugar_entrega, $motivo_compra, $documento, $nota, $comprobanteReferencia, $_POST["idproducto"], $_POST["nombreProducto"], $_POST["cantidad"], $_POST["precio_compra"], $_POST["precio_venta"], $_POST["nlote"], $_POST["fvencimiento"], $fechaOperacion, $input_cuotas, $montoPagado, $montoDeuda, $tipo_c, $totaldeposito, $noperacion, $totalrecibido, $fecha_deposito, $tipo_pago, $monto_pago, $operacion_pago);
			echo $rspta ? "Datos actualizados correctamente" : "No se pudieron actualizar todos los datos";
		}
		break;

	case 'guardarProveedor':
		if (empty($idpersona)) {
			$rspta = $persona->insertar('Proveedor', $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_hora);
			echo $rspta;
		}
		break;

	case 'guardarImagen':

		if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
			$imagen = $_POST["imagenactual"];
		} else {
			$ext = explode(".", $_FILES["imagen"]["name"]);
			if ($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png") {
				$imagen = round(microtime(true)) . '.' . end($ext);
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/compras/" . $imagen);
			}
		}

		if (empty($idcompra)) {
			$rspta = $compra->subirImagen($idcompraI, $imagen);
			echo $rspta ? "Datos registrados correctamente" : "No se pudieron registrar todos los datos";
		} else {


		}


		break;

	case 'aprobar':
		$rspta = $compra->aprobar($idcompra);
		echo $rspta ? "Orden de Compra Aprobada" : "Orden de Compra no se puede aprobar";
		break;

	case 'anular':
		$rspta = $compra->anular($idcompra, $comprobanteReferencia);
		echo $rspta ? "Compra Anulada" : "Compra no se puede anular porque parte del stock ya fue vendido";
		break;


	case 'mostrar':
		$rspta = $compra->mostrar($idcompra);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);
		break;

	case 'mostrar2':
		$rspta = $compra->mostrar2($idcompra);
		echo json_encode($rspta);
		break;

	case 'listarDetalleCompra':

		$rspta = $compra->compradetalle($idcompra);

		$data = array();

		while ($reg = $rspta->fetch_object()) {

			$data[] = array(
				"0" => $reg->idproducto,
				"1" => $reg->nombre,
				"2" => $reg->cantidad,
				"3" => $reg->precio_compra,
				"4" => $reg->precio_venta,
				"5" => $reg->unidadmedida
			);
		}

		echo json_encode($data);


		break;

	//_______________________________________________________________________________________________________

	//opcion para mostrar la numeracion y la serie_comprobante de la ticket
	case 'mostrar_num_ticket':
		$idsucursal = $_REQUEST["idsucursal"];
		//mostrando el numero de boleta de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_numero_ordencompra($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$num_comp_tic = $reg->num_comprobante
			);
		}
		$numero_tic_comp = (int) $num_comp_tic;
		//fin de mostrar numero de boleta de la tabla comprobantes
		$rspta = $comprobantes->numero_venta_ordencompra($idsucursal);
		$data = array();
		$numerot = $numero_tic_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numerot = $reg->num_comprobante
			);
		}
		$numero_ticket = (int) $numerot;
		$new_ticket = '';

		if ($numero_ticket == 9999999 or empty($numerot)) {
			$new_ticket = '0000001';
			echo json_encode($new_ticket);
		} elseif ($numerot == 9999999) {
			$new_ticket = '0000001';
			echo json_encode($new_ticket);
		} else {
			$sumatic = $numero_ticket + 1;
			echo json_encode($sumatic);
		}
		//$num = (int)$numerof; 
		//echo json_encode($numerof);
		break;
	case 'mostrar_s_ticket':
		$idsucursal = $_REQUEST["idsucursal"];
		//mostrando el numero de factura de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_serie_ordencompra($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$serie_comp_tic = $reg->serie_comprobante,
				$num_comp_tic = $reg->num_comprobante
			);
		}
		$serie_tic_comp = (int) $serie_comp_tic;
		$num_tic_comp = (int) $num_comp_tic;
		//fin de mostrar numero de factura de la tabla comprobantes
		$rspta = $comprobantes->numero_serie_ordencompra($idsucursal);
		$data = array();
		$numero_s_tic = $serie_tic_comp;
		$numero_bolet = $num_tic_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numero_s_tic = $reg->serie_comprobante,
				$numero_bolet = $reg->num_comprobante
			);
		}
		$num_s_ticket = (int) $numero_s_tic;
		$nuew_serie_ticket = 0;
		$numbo = (int) $numero_bolet;
		if ($numbo == 9999999 or empty($numero_s_tic)) {
			$nuew_serie_ticket = $num_s_ticket + 1;
			echo json_encode($nuew_serie_ticket);
		} else {
			echo json_encode($num_s_ticket);
		}
		break; //fin de opcion de mostrar num_comprobante y serie_comprobante del ticket

	//______________________________________________________________________________________________

	case 'listarDetalle':
		//Recibimos el idingreso
		$id = $_GET['id'];

		$rspta = $compra->listarDetalle($id);
		$total = 0;
		echo '<thead style="background-color:#A9D0F5">
									<th>Opciones</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>P. Compra</th>
                                    <th>P. Venta</th>
                                    <th>Subtotal</th>
                                </thead>';

		while ($reg = $rspta->fetch_object()) {
			echo '<tr class="filas">
					<td></td>
					<td>' . $reg->nombre . '</td>
					<td>' . $reg->cantidad . '</td>
					<td>' . $reg->precio_compra . '</td>
					<td>' . $reg->precio_venta . '</td>
					<td>' . $reg->precio_compra * $reg->cantidad . '</td>
					</tr>';
			$total = $total + ($reg->precio_compra * $reg->cantidad);
		}
		echo '<tfoot>
                                    <th>TOTAL</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><h4 id="total">S/.' . $total . '</h4><input type="hidden" name="total_compra" id="total_compra"></th> 
                                </tfoot>';
		break;

	case 'listar':
		$fecha_inicio = $_REQUEST["fecha_inicio"];
		$fecha_fin = $_REQUEST["fecha_fin"];
		$idsucursal = $_SESSION["idsucursal"];
		$compra->listar($fecha_inicio, $fecha_fin, $idsucursal);
		break;

	case 'listar2':

		$fecha_inicio = $_REQUEST["fecha_inicio"];
		$fecha_fin = $_REQUEST["fecha_fin"];
		$idsucursal = $_REQUEST["idsucursal2"];

		if ($idsucursal == "" || $idsucursal == NULL) {

			if ($_SESSION['idsucursal'] == 0) {

				$idsucursal = 'Todos';
			} else {

				$idsucursal = $_SESSION['idsucursal'];
			}
		}

		$rspta = $compra->listar2($fecha_inicio, $fecha_fin, $idsucursal);
		//Vamos a declarar un array
		$data = array();

		$editar = "";
		$aprobar = "";

		while ($reg = $rspta->fetch_object()) {


			if ($reg->estadoC == 'POR APROBACIÓN') {

				$estadoC = '<span class="badge bg-yellow">POR APROBACIÓN</span>';
				$editar = '<button class="btn btn-danger btn-xs" onclick="mostrarE(' . $reg->idcompra . ')" data-toggle="tooltip" title="" target="blanck"><i class="fas fa-edit"></i></button>';
				$aprobar = '<button class="btn btn-dark btn-xs" onclick="aprobar(' . $reg->idcompra . ')" data-toggle="tooltip" title="APROBAR" target="blanck"><i class="fa fa-check"></i></button> ';
				$eliminar = '<button class="btn btn-danger btn-xs" onclick="anular(' . $reg->idcompra . ')"><i class="fas fa-times-circle"></i></button> ';

			} else if ($reg->estadoC == 'APROBADO') {

				$estadoC = '<span class="badge bg-green">APROBADO</span>';
				if ($_SESSION["cargo"] == 'Administrador' || $_SESSION["cargo"] == 'administrador' || $_SESSION["cargo"] == 'admin' || $_SESSION["cargo"] == 'Admin' || $_SESSION["cargo"] == 'ADMINISTRADOR' || $_SESSION["cargo"] == 'ADMIN') {
					$editar = '<button class="btn btn-danger btn-xs" onclick="mostrarE(' . $reg->idcompra . ')" data-toggle="tooltip" title="" target="blanck"><i class="fas fa-edit"></i></button>';
				}
				$eliminar = '<button class="btn btn-danger btn-xs" onclick="anular(' . $reg->idcompra . ')"><i class="fas fa-times-circle"></i></button> ';
			} else if ($reg->estadoC == 'VENDIDO') {

				$estadoC = '<span class="badge bg-blue">VENDIDO</span>';
				$editar = '';
				$eliminar = '';
				$aprobar = '';
			} else {

				$estadoC = '<span class="badge bg-red">ANULADO</span>';
			}

			$data[] = array(
				"0" => $reg->fecha_kardex,
				"1" => $reg->proveedor,
				"2" => $reg->personal,
				"3" => $reg->tipo_c,
				"4" => $reg->serie_comprobante . '-' . $reg->num_comprobante,
				"5" => $reg->total_compra,
				"6" => $estadoC,
				"7" => (($reg->estado == 'REGISTRADO') ?
					$eliminar .
					$aprobar .
					'<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idcompra . ')"><i class="fa fa-eye"></i></button> ' .
					$editar :
					'<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idcompra . ')"><i class="fa fa-eye"></i></button>') .
					'<a target="_blank" href="reportes/factura/generaFacturaOrdenCompra.php?id=' . $reg->idcompra . '"> <button class="btn btn-info btn-xs"><i class="fa fa-file"></i></button></a> '
			);
		}
		$results = array(
			"sEcho" => 1, //Información para el datatables
			"iTotalRecords" => count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);

		break;

	case 'selectOrdenCompra':
		$idsucursal = $_REQUEST["idsucursal"];

		if ($idsucursal == "" || $idsucursal == NULL) {

			$idsucursal = 21;
		}

		require_once "../modelos/Compra.php";
		$compra = new Compra();

		$rspta = $compra->selectCompras($idsucursal);

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idcompra . '>' . $reg->serie_comprobante . '-' . $reg->num_comprobante . '</option>';
		}
		break;
		
	case 'selectProveedor':
		require_once "../modelos/Persona.php";
		$persona = new Persona();
		$persona->listarp();
		break;

	case 'listarArticulos':
		$idsucursal = $_SESSION["idsucursal"];
		require_once "../modelos/Producto.php";
		$producto = new Producto();
		// Obtener registros paginados
		$producto->listarActivos($idsucursal);
		
		break;

	case 'exportar_excel':
		$compra = new Compra();

		$fecha_inicio = $_GET["fecha_inicio"] ?? '';
		$fecha_fin = $_GET["fecha_fin"] ?? '';
		$idsucursal = $_SESSION["idsucursal"];

		$compra->exportarExcel($fecha_inicio, $fecha_fin, $idsucursal);
		break;

	case 'mostrarEditar':
		$compra->mostrarEditar($_POST["idcompra"]);
		break;

	case 'listarDetalleEdicion':
		$compra->listarDetalleEdicion($_GET["idcompra"]);
		break;

	case 'limpiar_tmp':
		$idpersonal = $_SESSION['idpersonal'];
		$idsucursal = $_POST['idsucursal'];
		$rspta = $compra->limpiarTemporal($idpersonal, $idsucursal);
		echo $rspta ? "OK" : "Error";
		break;

}
//Fin de las validaciones de acceso


ob_end_flush();
