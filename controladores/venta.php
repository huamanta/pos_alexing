<?php
require_once "../modelos/Venta.php";
require_once "../modelos/Producto.php";
if (strlen(session_id()) < 1)
	session_start();

$venta = new Venta();

$idventa = isset($_POST["idventa"]) ? limpiarCadena($_POST["idventa"]) : "";
$idsucursal = isset($_POST["idsucursal"]) ? limpiarCadena($_POST["idsucursal"]) : "";
$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
$idpersonal = isset($_POST["idpersonal"]) ? limpiarCadena($_POST["idpersonal"]) : "";
$tipo_comprobante = isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";
$serie_comprobante = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";
$num_comprobante = isset($_POST["num_comprobante"]) ? limpiarCadena($_POST["num_comprobante"]) : "";
if (!empty($_POST["fecha"])) {
    // Le llega solo YYYY-MM-DD desde el input
    $fecha = limpiarCadena($_POST["fecha"]);
    // Concatenamos la hora actual
    $fecha .= " " . date("H:i:s");
} else {
    // Si no hay fecha en el POST, usamos fecha y hora actual
    $fecha = date("Y-m-d H:i:s");
}
$impuesto = isset($_POST["impuesto"]) ? limpiarCadena($_POST["impuesto"]) : "";
$total_venta = isset($_POST["total_venta"]) ? limpiarCadena($_POST["total_venta"]) : "";

$tipopago = isset($_POST["tipopago"]) ? limpiarCadena($_POST["tipopago"]) : "";
$formapago = isset($_POST["formapago"]) ? limpiarCadena($_POST["formapago"]) : "";
$nroOperacion = isset($_POST["nroOperacion"]) ? limpiarCadena($_POST["nroOperacion"]) : "";
$fechaDepostivo = isset($_POST["fechaDepostivo"]) ? limpiarCadena($_POST["fechaDepostivo"]) : "";
$porcentaje = isset($_POST["porcentaje"]) ? limpiarCadena($_POST["porcentaje"]) : "";
$totalrecibido = isset($_POST["totalrecibido"]) ? limpiarCadena($_POST["totalrecibido"]) : "";
$totaldeposito = isset($_POST["totaldeposito"]) ? limpiarCadena($_POST["totaldeposito"]) : "";
$vuelto = isset($_POST["vuelto"]) ? limpiarCadena($_POST["vuelto"]) : "";

$fechaOperacion = isset($_POST["fechaOperacion"]) ? limpiarCadena($_POST["fechaOperacion"]) : "";
$montoDeuda = isset($_POST["montoDeuda"]) ? limpiarCadena($_POST["montoDeuda"]) : "";
$montoPagado = isset($_POST["montoPagado"]) ? limpiarCadena($_POST["montoPagado"]) : "";

$idmotivo = isset($_POST["idmotivo"]) ? limpiarCadena($_POST["idmotivo"]) : "";

$comprobanteReferencia = isset($_POST["comprobanteReferencia"]) ? limpiarCadena($_POST["comprobanteReferencia"]) : "";

$observaciones = isset($_POST["observaciones"]) ? limpiarCadena($_POST["observaciones"]) : "";

$estadoS = isset($_POST["estadoS"]) ? limpiarCadena($_POST["estadoS"]) : "";

$tipo = isset($_POST["tipo"]) ? limpiarCadena($_POST["tipo"]) : "";

$banco = isset($_POST["banco"]) ? limpiarCadena($_POST["banco"]) : "";

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
$fecha_hora = date("Y-m-d H:i:s");
$input_cuotas = isset($_POST["input_cuotas"]) ? limpiarCadena($_POST["input_cuotas"]) : "";
$inputInteres = isset($_POST["inputInteres"]) ? limpiarCadena($_POST["inputInteres"]) : "";
function tienePermiso($modulo, $submodulo, $accion) {
    return isset($_SESSION['acciones'][$modulo][$submodulo][$accion]) && $_SESSION['acciones'][$modulo][$submodulo][$accion] === true;
}

switch ($_GET["op"]) {

	case 'verificar_caja':
	    $idusuario = $_SESSION["idusuario"];
	    $idsucursal = $_SESSION["idsucursal"];
	    $rspta = $venta->verificarCaja($idusuario, $idsucursal);
	    echo json_encode($rspta);
	break;


	case 'listar_cajas':
	    $idsucursal = isset($_GET["idsucursal"]) ? $_GET["idsucursal"] : $_SESSION["idsucursal"];
	    $rspta = $venta->listarCajas($idsucursal);
	    echo json_encode($rspta);
	break;


	case 'aperturar_caja':
		$idcaja = $_POST['cajas'];
		$idusuario = $_SESSION["idusuario"];
		$idsucursal = $_SESSION["idsucursal"];
		$monto_apertura = $_POST['monto_apertura'];
		$rspta = $venta->aperturarCaja($idcaja, $monto_apertura, $idusuario, $idsucursal);
		echo json_encode($rspta);
		break;

	case 'verificar_caja_por_sucursal':
	    $idusuario = $_SESSION["idusuario"];
	    $idsucursal = $_GET["idsucursal"];

	    $rspta = $venta->verificarCaja($idusuario, $idsucursal);
	    echo json_encode($rspta);
	break;

	case 'comprobantesPendientes':
        $venta = new Venta();
        $result = $venta->comprobantesPendientes();
        echo json_encode($result);
        break;


	case 'guardaryeditar':
		 if (!empty($_POST["fecha"])) {
	        $fechaInput = limpiarCadena($_POST["fecha"]);
	        
	        // Validar restricción de fecha según cargo
	        if ($_SESSION['cargo'] !== 'Administrador') {
	            $fechaSeleccionada = strtotime($fechaInput);
	            $hoy = strtotime(date('Y-m-d'));
	            $ayer = strtotime(date('Y-m-d', strtotime('-1 day')));
	            
	            // Verificar que la fecha esté dentro del rango permitido
	            if ($fechaSeleccionada < $ayer || $fechaSeleccionada > $hoy) {
	                echo json_encode([
	                    'status' => 'error',
	                    'mensaje' => 'No tienes permisos para usar esta fecha. Solo puedes registrar ventas de hoy o ayer.'
	                ]);
	                exit;
	            }
	        }
	        
	        // Si pasa la validación, concatenamos la hora actual
	        $fecha = $fechaInput . " " . date("H:i:s");
	    } else {
	        // Si no hay fecha en el POST, usamos fecha y hora actual
	        $fecha = date("Y-m-d H:i:s");
	    }
		if (empty($idventa)) {
			$idcaja = $_POST['idcaja'];
			$fecha_pago = isset($_POST["fecha_pago"]) && is_array($_POST["fecha_pago"]) ? $_POST["fecha_pago"] : [];
			$rspta = $venta->insertar($idsucursal, $idcliente, $idpersonal, $idcaja, $tipo_comprobante, 
			$serie_comprobante, $num_comprobante, $fecha, $impuesto, $total_venta, $tipopago, $formapago, 
			$nroOperacion, $fechaDepostivo, $porcentaje, $totalrecibido,$totaldeposito, $vuelto, $tipo, $banco,
			$_POST["idproducto"], $_POST["nombreProducto"], $_POST["cantidad"], $_POST["precio_venta"], 
			$_POST["descuento"], $fechaOperacion, $montoDeuda, $montoPagado, $comprobanteReferencia, 
			$idmotivo, $observaciones, $fecha_pago, $inputInteres, $input_cuotas, $_POST["cantidad_contenedor"], 
			$_POST["contenedor"], $_POST["idp"],$_POST["check_precio"], $_POST["id_detalle_compra_lote"], $_POST["idcategoria"]);
			echo $rspta;
		} else {
			$idcaja = $_POST['idcaja'];
			$fecha_pago = isset($_POST["fecha_pago"]) && is_array($_POST["fecha_pago"]) ? $_POST["fecha_pago"] : [];
			$rspta = $venta->editar($idventa, $idsucursal, $idcliente, $idpersonal, $idcaja, $tipo_comprobante, 
			$serie_comprobante, $num_comprobante, $fecha, $impuesto, $total_venta, $tipopago, $formapago, 
			$nroOperacion, $fechaDepostivo, $porcentaje, $totalrecibido, $totaldeposito,$vuelto, $tipo, $banco,
			$_POST["idproducto"], $_POST["nombreProducto"], $_POST["cantidad"], $_POST["precio_venta"], 
			$_POST["descuento"], $fechaOperacion, $montoDeuda, $montoPagado, $comprobanteReferencia, 
			$idmotivo, $observaciones, $fecha_pago, $inputInteres, $input_cuotas, $_POST["cantidad_contenedor"], 
			$_POST["contenedor"], $_POST["idp"],  $_POST["check_precio"], $_POST["id_detalle_compra_lote"], $_POST["idcategoria"]);
			echo $rspta;
		}

		break;

	case 'agregar_carrito':
	  $sql = "INSERT INTO carrito_venta_tmp (
	            idpersonal, idsucursal, idcaja, idproducto, idcategoria,
	            nombre_producto, cantidad, cantidad_contenedor, contenedor,
	            precio_venta, descuento, check_precio, id_fifo
	          ) VALUES (
	            '{$_SESSION['idpersonal']}',
	            '{$_POST['idsucursal']}',
	            '{$_POST['idcaja']}',
	            '{$_POST['idproducto']}',
	            '{$_POST['idcategoria']}',
	            '{$_POST['nombre_producto']}',
	            '{$_POST['cantidad']}',
	            '{$_POST['cantidad_contenedor']}',
	            '{$_POST['contenedor']}',
	            '{$_POST['precio_venta']}',
	            '{$_POST['descuento']}',
	            '{$_POST['check_precio']}',
	            '{$_POST['id_fifo']}'
	          )
	          ON DUPLICATE KEY UPDATE
	            cantidad = cantidad + VALUES(cantidad),
	            descuento = VALUES(descuento),
	            precio_venta = VALUES(precio_venta)";

	  ejecutarConsulta($sql);
	  echo "ok";
	break;

	case 'verPreciosItem':
        $idusuario = $_SESSION["idusuario"];
        $idproducto = isset($_GET["idproducto"]) ? limpiarCadena($_GET["idproducto"]) : "";
        $rpta = $pos->verPreciosItem($idproducto, $idusuario);
        echo json_encode($rpta);
        break;

	case 'actualizarDataItem':
        $idproducto = isset($_POST["idproducto"]) ? limpiarCadena($_POST["idproducto"]) : "";
        $campo = isset($_POST["campo"]) ? limpiarCadena($_POST["campo"]) : "";
        $value = isset($_POST["value"]) ? limpiarCadena($_POST["value"]) : "";
        $rpta = $pos->actualizarDataItem($idproducto, $campo, $value);
        echo $rpta? json_encode(array('status' => 1)) : json_encode(array('status' => 0));
        break;

	case 'verificarProductos':
	    // Verificar si se reciben los parámetros necesarios
	    if (isset($_POST['idsucursal']) && isset($_POST['productos'])) {
	        $idsucursal = $_POST['idsucursal'];
	        $productos = $_POST['productos'];
	        $productosList = implode(',', array_map('intval', $productos));
	        $sql = "SELECT idproducto, nombre FROM producto WHERE idsucursal = '$idsucursal' AND idproducto IN ($productosList)";
	        $result = ejecutarConsulta($sql);
	        $productosDisponibles = [];
	        
	        while ($row = $result->fetch_assoc()) {
	            $productosDisponibles[] = $row['idproducto'];
	        }
	        $noDisponibles = array_diff($productos, $productosDisponibles);
	        echo json_encode(['no_disponibles' => array_values($noDisponibles)]);
	    } else {
	        echo json_encode(['no_disponibles' => []]);
	    }
	    break;


	case 'guardarCliente':
		if (empty($idpersona)) {
			$rspta = $persona->insertar($tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_hora);
			echo $rspta ? "Cliente registrado" : "Cliente no se pudo registrar";
		}
		break;


	case 'anular':
		$rspta = $venta->anular($idventa, $idsucursal);
		echo $rspta ? "Venta anulado correctamente" : "No se pudo anular el Venta";
		break;

	case 'notacredito':
		$rspta = $venta->notaCredito($comprobanteReferencia, $idsucursal, $idmotivo);
		echo $rspta ? "Venta anulado correctamente" : "No se pudo anular el Venta";
		break;

	case 'mostrar':
		$rspta = $venta->mostrar($idventa);
		echo json_encode($rspta);
		break;

	case 'mostraredit':
		$rspta = $venta->mostrarEdit($idventa);
		echo json_encode($rspta);
		break;
	case 'listarCuotas':
	    $rspta = $venta->listarCuotas($_POST["idventa"]);
	    // Retornar un arreglo con todas las cuotas encontradas
	    $cuotas = array();
	    while($reg = $rspta->fetch_object()){
	        $cuotas[] = $reg;
	    }
	    echo json_encode($cuotas);
	    break;


	/*case 'mostrar':
		$rspta = $venta->mostrarPOS($idventa);
		echo json_encode($rspta);
		break;*/

	case 'mostrardetalle':

		//recibimos el idventa
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

	case 'mostrarf':

		$idsucursal = $_REQUEST["idsucursal"];

		//mostrando el numero de factura de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_numero_factura($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$num_comp = $reg->num_comprobante
			);
		}
		$numero_fac_comp = (int)$num_comp;

		$rspta = $venta->numero_venta($idsucursal);
		$data = array();
		$numerof = $numero_fac_comp;
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numerof = $reg->num_comprobante
			);
		}
		$numero_factura = (int)$numerof;
		$new_factura = '';

		if ($numero_factura == 9999999 or empty($numerof)) {
			$new_factura = '0000001';
			$numero_nuevo = (int)$new_factura;
			echo json_encode($numero_nuevo);
		} elseif ($numerof == 9999999) {
			$new_factura = '0000001';
			$numero_nuevo = (int)$new_factura;
			echo json_encode($numero_nuevo);
		} else {
			$sumafact = $numero_factura + 1;
			echo json_encode($sumafact);
		}
		//$num = (int)$numerof; 
		//echo json_encode($numerof);
		break;

	case 'mostrars':

		$idsucursal = $_REQUEST["idsucursal"];

		//mostrando el numero de factura de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_serie_factura($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$serie_comp = $reg->serie_comprobante,
				$num_comp = $reg->num_comprobante
			);
		}
		$serie_fac_comp = (int)$serie_comp;
		$num_fac_comp = (int)$num_comp;
		//fin de mostrar numero de factura de la tabla comprobantes
		$rspta = $venta->numero_serie($idsucursal);
		$data = array();
		$numeros = $serie_fac_comp;
		$numerofa = $num_fac_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numeros = $reg->serie_comprobante,
				$numerofa = $reg->num_comprobante
			);
		}
		$nums = (int)$numeros;
		$nuew_serie = 0;
		$numf = (int)$numerofa;
		if ($numf == 9999999 or empty($numerofa)) {
			$nuew_serie = $nums + 1;
			echo json_encode($nuew_serie);
		} else {
			echo json_encode($nums);
		}
		break; //opcion para mostrar la numeracion y la serie_comprobante de la factura

		//opcion para mostrar la numeracion y la serie_comprobante de la boleta
	case 'mostrar_num_boleta':

		$idsucursal = $_REQUEST["idsucursal"];

		//mostrando el numero de boleta de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_numero_boleta($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$num_comp = $reg->num_comprobante
			);
		}
		$numero_bol_comp = (int)$num_comp;
		//fin de mostrar numero de boleta de la tabla comprobantes

		$rspta = $venta->numero_venta_boleta($idsucursal);
		$data = array();
		$numerob = $numero_bol_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numerob = $reg->num_comprobante
			);
		}
		$numero_boleta = (int)$numerob;
		$new_boleta = '';

		if ($numero_boleta == 9999999 or empty($numerob)) {
			$new_boleta = '0000001';
			echo json_encode($new_boleta);
		} elseif ($numerob == 9999999) {
			$new_boleta = '0000001';
			echo json_encode($new_boleta);
		} else {
			$sumabol = $numero_boleta + 1;
			echo json_encode($sumabol);
		}
		//$num = (int)$numerof; 
		//echo json_encode($numerof);
		break;
	case 'mostrar_serie_boleta':
		//mostrando el numero de factura de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";

		$idsucursal = $_REQUEST["idsucursal"];

		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_serie_boleta($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$serie_comp_bol = $reg->serie_comprobante,
				$num_comp_bol = $reg->num_comprobante
			);
		}
		$serie_bol_comp = (int)$serie_comp_bol;
		$num_bol_comp = (int)$num_comp_bol;
		//fin de mostrar numero de factura de la tabla comprobantes
		$rspta = $venta->numero_serie_boleta($idsucursal);
		$data = array();
		$numero_s_bol = $serie_bol_comp;
		$numero_bol = $num_bol_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numero_s_bol = $reg->serie_comprobante,
				$numero_bol = $reg->num_comprobante
			);
		}
		$nums_bol = (int)$numero_s_bol;
		$nuew_serie_bol = 0;
		$numb = (int)$numero_bol;
		if ($numb == 9999999 or empty($numero_s_bol)) {
			$nuew_serie_bol = $nums_bol + 1;
			echo json_encode($nuew_serie_bol);
		} else {
			echo json_encode($nums_bol);
		}
		break; //fin de opcion de mostrar num_comprobante y serie_comprobante de boleta

		//opcion para mostrar la numeracion y la serie_comprobante de la ticket
	case 'mostrar_num_ticket':

		$idsucursal = $_REQUEST["idsucursal"];

		//mostrando el numero de boleta de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_numero_ticket($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$num_comp_tic = $reg->num_comprobante
			);
		}
		$numero_tic_comp = (int)$num_comp_tic;
		//fin de mostrar numero de boleta de la tabla comprobantes
		$rspta = $venta->numero_venta_ticket($idsucursal);
		$data = array();
		$numerot = $numero_tic_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numerot = $reg->num_comprobante
			);
		}
		$numero_ticket = (int)$numerot;
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

		// Defaults para evitar Undefined variable
		$serie_comp_tic = 0;
		$num_comp_tic   = 0;

		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_serie_ticket($idsucursal);

		while ($reg = $rspta->fetch_object()) {
			$serie_comp_tic = (int)$reg->serie_comprobante;
			$num_comp_tic   = (int)$reg->num_comprobante;
		}

		$serie_tic_comp = (int)$serie_comp_tic;
		$num_tic_comp   = (int)$num_comp_tic;

		// fin de mostrar numero de factura de la tabla comprobantes
		$rspta = $venta->numero_serie_ticket($idsucursal);

		$numero_s_tic   = $serie_tic_comp;
		$numero_bolet   = $num_tic_comp;

		while ($reg = $rspta->fetch_object()) {
			$numero_s_tic = (int)$reg->serie_comprobante;
			$numero_bolet = (int)$reg->num_comprobante;
		}

		$num_s_ticket = (int)$numero_s_tic;
		$numbo        = (int)$numero_bolet;

		if ($numbo == 9999999 || empty($numero_s_tic)) {
			$nuew_serie_ticket = $num_s_ticket + 1;
			echo json_encode($nuew_serie_ticket);
		} else {
			echo json_encode($num_s_ticket);
		}
		break;
		
	case 'mostrar_num_nc':

		$idsucursal = $_REQUEST["idsucursal"];

		//mostrando el numero de boleta de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_numero_nc($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$num_comp = $reg->num_comprobante
			);
		}
		$numero_bol_comp = (int)$num_comp;
		//fin de mostrar numero de boleta de la tabla comprobantes

		$rspta = $venta->numero_venta_nc($idsucursal);
		$data = array();
		$numerob = $numero_bol_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numerob = $reg->num_comprobante
			);
		}
		$numero_boleta = (int)$numerob;
		$new_boleta = '';

		if ($numero_boleta == 9999999 or empty($numerob)) {
			$new_boleta = '0000001';
			echo json_encode($new_boleta);
		} elseif ($numerob == 9999999) {
			$new_boleta = '0000001';
			echo json_encode($new_boleta);
		} else {
			$sumabol = $numero_boleta + 1;
			echo json_encode($sumabol);
		}
		//$num = (int)$numerof; 
		//echo json_encode($numerof);
		break;

	case 'mostrar_serie_nc':

		$idsucursal = $_REQUEST["idsucursal"];

		//mostrando el numero de factura de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_serie_nc($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$serie_comp_bol = $reg->serie_comprobante,
				$num_comp_bol = $reg->num_comprobante
			);
		}
		$serie_bol_comp = (int)$serie_comp_bol;
		$num_bol_comp = (int)$num_comp_bol;
		//fin de mostrar numero de factura de la tabla comprobantes
		$rspta = $venta->numero_serie_nc($idsucursal);
		$data = array();
		$numero_s_bol = $serie_bol_comp;
		$numero_bol = $num_bol_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numero_s_bol = $reg->serie_comprobante,
				$numero_bol = $reg->num_comprobante
			);
		}
		$nums_bol = (int)$numero_s_bol;
		$nuew_serie_bol = 0;
		$numb = (int)$numero_bol;
		if ($numb == 9999999 or empty($numero_s_bol)) {
			$nuew_serie_bol = $nums_bol + 1;
			echo json_encode($nuew_serie_bol);
		} else {
			echo json_encode($nums_bol);
		}
		break; //fin de opcion de mostrar num_comprobante y serie_comprobante de boleta

		//opcion para mostrar la numeracion y la serie_comprobante de la boleta
	case 'mostrar_num_ncb':

		$idsucursal = $_REQUEST["idsucursal"];

		//mostrando el numero de boleta de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_numero_ncb($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$num_comp = $reg->num_comprobante
			);
		}
		$numero_bol_comp = (int)$num_comp;
		//fin de mostrar numero de boleta de la tabla comprobantes

		$rspta = $venta->numero_venta_ncb($idsucursal);
		$data = array();
		$numerob = $numero_bol_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numerob = $reg->num_comprobante
			);
		}
		$numero_boleta = (int)$numerob;
		$new_boleta = '';

		if ($numero_boleta == 9999999 or empty($numerob)) {
			$new_boleta = '0000001';
			echo json_encode($new_boleta);
		} elseif ($numerob == 9999999) {
			$new_boleta = '0000001';
			echo json_encode($new_boleta);
		} else {
			$sumabol = $numero_boleta + 1;
			echo json_encode($sumabol);
		}
		//$num = (int)$numerof; 
		//echo json_encode($numerof);
		break;

	case 'mostrar_serie_ncb':

		$idsucursal = $_REQUEST["idsucursal"];

		//mostrando el numero de factura de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_serie_ncb($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$serie_comp_bol = $reg->serie_comprobante,
				$num_comp_bol = $reg->num_comprobante
			);
		}
		$serie_bol_comp = (int)$serie_comp_bol;
		$num_bol_comp = (int)$num_comp_bol;
		//fin de mostrar numero de factura de la tabla comprobantes
		$rspta = $venta->numero_serie_ncb($idsucursal);
		$data = array();
		$numero_s_bol = $serie_bol_comp;
		$numero_bol = $num_bol_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numero_s_bol = $reg->serie_comprobante,
				$numero_bol = $reg->num_comprobante
			);
		}
		$nums_bol = (int)$numero_s_bol;
		$nuew_serie_bol = 0;
		$numb = (int)$numero_bol;
		if ($numb == 9999999 or empty($numero_s_bol)) {
			$nuew_serie_bol = $nums_bol + 1;
			echo json_encode($nuew_serie_bol);
		} else {
			echo json_encode($nums_bol);
		}
		break; //fin de opcion de mostrar num_comprobante y serie_comprobante de boleta

		//________________________________

	case 'cambiarEstado':
		$venta = new Venta();
		$rspta = $venta->cambiarEstado($idventa, $estadoS);
		echo $rspta ? "Producto Actualizado" : "Producto no se puede actualizar";
		break;

	case 'listarDetalle':
    require_once "../modelos/Negocio.php";
    $cnegocio = new Negocio();
    $rsptan = $cnegocio->listar();
    $regn = $rsptan->fetch_object();
    $smoneda = empty($regn) ? 'Simbolo de moneda' : $regn->simbolo;
    $nom_imp = empty($regn) ? '' : $regn->nombre_impuesto;

    $id = $_GET['id'];

    $rspta = $venta->listarDetalle($id);

    $total = 0;
    $totaldescuento = 0;
    $total_efectivo = 0;
    $total_otro_pago = 0;

    echo '<thead style="background-color:#A9D0F5; font-weight: bold;">
            <tr>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Cantidad</th>
                <th>P. Venta</th>
                <th>Subtotal</th>
            </tr>
          </thead>';

    while ($reg = $rspta->fetch_object()) {

        // 🔹 Calcular subtotal de forma robusta
        $subtotalCalc = 0;
        if ($reg->cantidad > 0) {
            $precioUnitarioEstimado = $reg->precio_venta / $reg->cantidad;

            // Si el precio_unitario_estimado es menor a la mitad del precio
            // o la cantidad tiene decimales → asumimos que el precio fue directo (check activado)
            if ($precioUnitarioEstimado < ($reg->precio_venta * 0.5) || fmod($reg->cantidad, 1) !== 0.0) {
                $subtotalCalc = $reg->precio_venta; // precio directo (no multiplicar)
            } else {
                $subtotalCalc = $reg->cantidad * $reg->precio_venta;
            }
        } else {
            $subtotalCalc = $reg->precio_venta;
        }

        echo '<tr class="filas" style="border-bottom: 1px solid #ccc;">
                <td><span class="badge bg-success">' . $reg->cantidad . '</span> ' . htmlspecialchars($reg->nombre_producto) . ' <span class="badge bg-danger">' . htmlspecialchars($reg->contenedor) . '</span></td>
                <td>' . htmlspecialchars($reg->categoria) . '</td>
                <td>' . $reg->cantidad . '</td>
                <td>' . number_format($reg->precio_venta, 2) . '</td>
                <td>' . number_format($subtotalCalc, 2) . '</td>
            </tr>';

        // Totales
        $total = $reg->total_venta;
        $totaldescuento = $reg->descuento;
        $total_efectivo = $reg->total_efectivo;
        $total_otro_pago = $reg->total_otro_pago;

        $numoperacion = $reg->numoperacion;
        $banco = $reg->banco;
        $fechadeposito = $reg->fechadeposito;
    }

    // TOTALES ORDENADOS
    echo '<tfoot style="background-color:#E3F2FD;">
            <tr>
                <th colspan="3"></th>
                <th>TOTAL VENTA</th>
                <th>' . $smoneda . ' ' . number_format($total, 2) . '</th>
            </tr>
          </tfoot>';

    echo '<tfoot style="background-color:#E3F2FD;">
            <tr>
                <th colspan="3"></th>
                <th>PAGO EN PLATAFORMAS DIGITALES</th>
                <th>' . $smoneda . ' ' . number_format($total_otro_pago, 2) . '</th>
            </tr>
          </tfoot>';

    echo '<tfoot style="background-color:#E3F2FD;">
            <tr>
                <th colspan="3"></th>
                <th>PAGO CON EFECTIVO</th>
                <th>' . $smoneda . ' ' . number_format($total_efectivo, 2) . '</th>
            </tr>
          </tfoot>';

    echo "<script>
            $('#nrooperacionm').text('{$numoperacion}');
            $('#banco').text('{$banco}');
            $('#fechadeposito').text('{$fechadeposito}');
          </script>";

    break;



	case 'listar':

	$fecha_inicio = $_REQUEST["fecha_inicio"];
	$fecha_fin = $_REQUEST["fecha_fin"];
	$estado = $_REQUEST["estado"];
	$idsucursal = $_REQUEST["idsucursal2"];
	$idproducto = isset($_REQUEST["idproducto"]) ? $_REQUEST["idproducto"] : "";

	if ($idsucursal == "" || $idsucursal == NULL) {
		if ($_SESSION['idsucursal'] == 0) {
			$idsucursal = 'Todos';
		} else {
			$idsucursal = $_SESSION['idsucursal'];
		}
	}

	$rspta = $venta->listar($fecha_inicio, $fecha_fin, $estado, $idsucursal, $idproducto);

	$data = array();

	while ($reg = $rspta->fetch_object()) {
		$url1 = 'reportes/exTicket.php?id=';
		$url2 = 'reportes/factura/generaFactura.php?id=';

		$ruta = 'public/FACT_WebService/Facturacion/files/' . $reg->dov_Nombre . '.xml';
		$rutaCdr = 'public/FACT_WebService/Facturacion/files/R-' . $reg->dov_Nombre . '.zip';

		if ($reg->tipo_comprobante == 'Boleta') {

			$enviarSunat = '<a data-toggle="tooltip" title="Enviar a Sunat" onclick="EnviarSunat(1,' . $reg->idventa . ',' . $reg->idpersonal . ');"> 
				<button class="btn btn-primary btn-xs"><i class="fas fa-paper-plane"></i></button></a> 
				<a href="' . $ruta . '" style="pointer-events: none;"> 
				<button class="btn btn-warning btn-xs"><i class="fas fa-file-code"></i></button></a> 
				<a href="' . $rutaCdr . '" style="pointer-events: none;"> 
				<button class="btn btn-danger btn-xs"><i class="fas fa-file-archive"></i></button></a>';

			$pdf = '<a target="_blank" title="PDF" onclick="PDF(1,' . $reg->idventa . ',' . $reg->idpersonal . ')"> 
				<button class="btn btn-info btn-xs"><i class="fas fa-file-pdf"></i></button></a>';

			$ticket = '<a target="_blank" title="Ticket" onclick="Ticket(1,' . $reg->idventa . ',' . $reg->idpersonal . ')"> 
				<button class="btn btn-primary btn-xs"><i class="fas fa-receipt"></i></button></a>';

		} else {
			$enviarSunat = '<a data-toggle="tooltip" title="Enviar a Sunat" onclick="EnviarSunat(2,' . $reg->idventa . ',' . $reg->idpersonal . ');"> 
				<button class="btn btn-primary btn-xs"><i class="fas fa-paper-plane"></i></button></a> 
				<a href="' . $ruta . '" style="pointer-events: none;"> 
				<button class="btn btn-warning btn-xs"><i class="fas fa-file-code"></i></button></a> 
				<a href="' . $rutaCdr . '" style="pointer-events: none;"> 
				<button class="btn btn-danger btn-xs"><i class="fas fa-file-archive"></i></button></a>';

			$pdf = '<a target="_blank" title="PDF" onclick="PDF(2,' . $reg->idventa . ',' . $reg->idpersonal . ')"> 
				<button class="btn btn-info btn-xs"><i class="fas fa-file-pdf"></i></button></a>';

			$ticket = '<a target="_blank" title="Ticket" onclick="Ticket(2,' . $reg->idventa . ',' . $reg->idpersonal . ')"> 
				<button class="btn btn-primary btn-xs"><i class="fas fa-receipt"></i></button></a>';
		}

		$urlComprobarEstado = 'public/FACT_WebService/Facturacion/consultacdr.php?idventa=' . $reg->idventa . '&codColab=' . $reg->idpersonal;

		if ($reg->estado == 'Aceptado') {
			$estado = '<span class="badge badge-neon neon-green">ACEPTADO</span>';
			$pdf = '<a target="_blank" href="' . $url2 . $reg->idventa . '"> 
				<button class="btn btn-info btn-xs"><i class="fas fa-file-pdf"></i></button></a>';
			$ticket = '<a target="_blank" href="' . $url1 . $reg->idventa . '"> 
				<button class="btn btn-primary btn-xs"><i class="fas fa-receipt"></i></button></a>';

		} else if ($reg->estado == 'Por Enviar') {
			$estado = '<span class="badge badge-neon neon-yellow">POR ENVIAR</span>';

		} else if ($reg->estado == 'En Resumen') {
			$estado = '<span class="badge badge-neon neon-blue">EN RESUMEN</span>';

		} else if ($reg->estado == 'Anulado') {
			$estado = '<span class="badge badge-neon neon-red">ANULADO</span>';

		} else if ($reg->estado == 'Nota Credito') {
			$estado = '<span class="badge badge-neon neon-red">NOTA DE CRÉDITO</span>';
			$pdf = '<a target="_blank" href="' . $url2 . $reg->idventa . '"> 
				<button class="btn btn-info btn-xs"><i class="fas fa-file-pdf"></i></button></a>';
			$ticket = '<a target="_blank" href="' . $url1 . $reg->idventa . '"> 
				<button class="btn btn-primary btn-xs"><i class="fas fa-receipt"></i></button></a>';

		} else if ($reg->estado == 'Rechazado') {
			$estado = '<span class="badge badge-neon neon-red">RECHAZADO</span>';
			$pdf = '<a target="_blank" href="' . $url2 . $reg->idventa . '"> 
				<button class="btn btn-info btn-xs"><i class="fas fa-file-pdf"></i></button></a>';
			$ticket = '<a target="_blank" href="' . $url1 . $reg->idventa . '"> 
				<button class="btn btn-primary btn-xs"><i class="fas fa-receipt"></i></button></a>';

		} else if ($reg->estado == 'Aceptado por resumen') {
			$estado = '<span class="badge badge-neon neon-green">ACEPTADO POR RESUMEN</span>';
			$pdf = '<a target="_blank" href="' . $url2 . $reg->idventa . '"> 
				<button class="btn btn-info btn-xs"><i class="fas fa-file-pdf"></i></button></a>';
			$ticket = '<a target="_blank" href="' . $url1 . $reg->idventa . '"> 
				<button class="btn btn-primary btn-xs"><i class="fas fa-receipt"></i></button></a>';

		} else {
			$estado = '<span class="badge badge-neon neon-blue">ACTIVADO</span>';
			$pdf = '<a target="_blank" href="' . $url2 . $reg->idventa . '"> 
				<button class="btn btn-info btn-xs"><i class="fas fa-file-pdf"></i></button></a>';
			$ticket = '<a target="_blank" href="' . $url1 . $reg->idventa . '"> 
				<button class="btn btn-primary btn-xs"><i class="fas fa-receipt"></i></button></a>';
		}

		if ($reg->estado == 'Por Enviar') {
			$sunat = $enviarSunat;
		} else if ($reg->estado == 'Activado' || $reg->estado == 'Anulado') {
			$sunat = '<a style="pointer-events: none;"> 
				<button class="btn btn-primary btn-xs"><i class="fas fa-paper-plane"></i></button></a> 
				<a href="' . $ruta . '" style="pointer-events: none;"> 
				<button class="btn btn-warning btn-xs"><i class="fas fa-file-code"></i></button></a> 
				<a href="' . $rutaCdr . '" style="pointer-events: none;"> 
				<button class="btn btn-danger btn-xs"><i class="fas fa-file-archive"></i></button></a>';
		} else if ($reg->estado == 'Aceptado' || $reg->estado == 'Aceptado por resumen') {
			// Solo cuando está ACEPTADO se pueden descargar los archivos
			$sunat = '<a style="pointer-events: none;"> 
				<button class="btn btn-primary btn-xs" title="No Disponible"><i class="fas fa-paper-plane"></i></button></a> 
				<a href="' . $ruta . '" download="' . $reg->dov_Nombre . '.xml" class="btn btn-warning btn-xs ml-1" title="Descargar XML"> 
				<i class="fas fa-file-code"></i></a> 
				<a href="' . $rutaCdr . '" target="_blank" class="btn btn-danger btn-xs ml-1" title="Descargar CDR ZIP"> 
				<i class="fas fa-file-archive"></i></a>';
		} else {
			// Para 'En Resumen', 'Nota Credito', 'Rechazado' - botones deshabilitados
			$sunat = '<a style="pointer-events: none;"> 
				<button class="btn btn-primary btn-xs" title="No Disponible"><i class="fas fa-paper-plane"></i></button></a> 
				<a style="pointer-events: none;"> 
				<button class="btn btn-warning btn-xs ml-1" title="No Disponible"><i class="fas fa-file-code"></i></button></a> 
				<a style="pointer-events: none;"> 
				<button class="btn btn-danger btn-xs ml-1" title="No Disponible"><i class="fas fa-file-archive"></i></button></a>';
		}

		if ($reg->tipo_comprobante == 'Nota de Venta') {
			$comprobarEstado = '<center><a style="pointer-events: none;"> 
				<button class="btn btn-warning btn-xs" onclick="ComprobarEstado(' . $reg->idventa . ')"><i class="fas fa-exclamation-circle"></i></button></a></center>';
		} else {
			$comprobarEstado = '<center><a onclick="comprobarEstado(' . $reg->idventa . ',' . $reg->idpersonal . ');"> 
				<button class="btn btn-warning btn-xs"><i class="fas fa-exclamation-circle"></i></button></a></center>';
		}

		if ($reg->estado == 'Anulado') {
		    // Mostrar solo el botón de ojo para ver los productos de la nota anulada
		    $mostrarResumen = '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fas fa-eye"></i></button>';
		    $enviarComprobante = '';
		    $mostrar = " ";
		    $sunatE = "-";
		} else {
		    // Casos normales
		    $mostrarResumen = '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fas fa-eye"></i></button>';
		    $enviarComprobante = '<a target="_blank" title="Enviar Comprobantes"> 
		        <button class="btn btn-success btn-xs" onclick="EnviarComprobante(' . $reg->idventa . ')"><i class="fab fa-whatsapp"></i></button></a>';
		    $mostrar = $pdf . $ticket;
		    $sunatE = $sunat;
		}

		// --- NUEVO BOTÓN DE NOTA DE CRÉDITO ---
		if ($reg->estado == 'Aceptado') {
		    $notaCreditoBtn = '<a title="Pasar a Nota de Crédito" onclick="notaCredito(' . $reg->idventa . ','.$reg->idsucursal.')"> 
		        <button class="btn btn-danger btn-xs"><i class="fas fa-ban"></i></button></a>';
		} else {
		    $notaCreditoBtn = '';
		}


		if ($reg->estadoS == '') {
			$estadoS = '-';
		} else if ($reg->estadoS == 'PENDIENTE') {
			$estadoS = '<span class="badge bg-red">PENDIENTE</span>';
		} else if ($reg->estadoS == 'TERMINADO') {
			$estadoS = '<span class="badge bg-green">TERMINADO</span>';
		} else {
			$estadoS = '<span class="badge bg-yellow">ENTREGADO</span>';
		}

		$data[] = array(
			"0" => $reg->fecha,
			"1" => $reg->cliente . ' - ' . $reg->num_documento,
			"2" => $reg->sucursal,
			"3" => $reg->tipo_comprobante . ' - ' . $reg->serie_comprobante . ' - ' . $reg->num_comprobante,
			"4" => '<span class="badge badge-neon neon-purple sm">S/ ' . number_format($reg->total_venta, 2) . '</span>',
			"5" => $reg->formapago,
			"6" => ($reg->ventacredito == 'Si') ? '<center><span class="badge badge-neon neon-red">Crédito</span></center>' : '<center><span class="badge badge-neon neon-blue">Contado</span></center>',
			"7" => $estado,
			"8" => $sunatE,
			"9" => $comprobarEstado,
			"10" => (($reg->estado == 'Activado') ?
				    '<div class="dropdown">
				        <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> 
				            <i class="fas fa-list-ul"></i>
				        <span class="caret"></span></button>

				        <div class="dropdown-menu">' 
				        . (tienePermiso('Pos','Venta Pos','Editar') ? 
				            '<a class="dropdown-item" style="cursor:pointer;" onclick="generarComprobante('.$reg->idventa.')">Editar</a>' 
				        : '') 
				        . (tienePermiso('Pos','Venta Pos','Eliminar') ? 
				            '<a class="dropdown-item" style="cursor:pointer;" onclick="anularComprobante('.$reg->idventa.')">Eliminar</a>' 
				        : '') 
				        . (($reg->tipo_comprobante == 'Nota de Venta') ?
				            '<a class="dropdown-item" style="cursor:pointer;" onclick="cambiarComprobante('.$reg->idventa.','.$reg->idsucursal.')">
				                Cambiar a Boleta/Factura
				            </a>'
				        : '') 
				        . '</div>

				        <button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fas fa-eye"></i></button>
				        <a target="_blank" title="Enviar Comprobantes"> 
				            <button class="btn btn-success btn-xs" onclick="EnviarComprobante(' . $reg->idventa . ')"><i class="fab fa-whatsapp"></i></button></a>'
				    :
				    $mostrarResumen . $enviarComprobante . ''
				) 
				. $mostrar 
				. $notaCreditoBtn 
				. '</div>'
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


	case 'selectSucursal3':
	    require_once "../modelos/Venta.php";
	    $venta = new Venta();

	    $rspta = $venta->listarSucursal2($_SESSION['idpersonal'], $_SESSION['idsucursal']);

	    // Opción "Todas" SOLO si tiene acceso total
	    if ((int)$_SESSION['idsucursal'] === 0) {
	        echo '<option value="0">Todas las Sucursales</option>';
	    }
		$selectedSucursal = isset($_SESSION['idsucursal']) ? (int)$_SESSION['idsucursal'] : 0;
	    while ($reg = $rspta->fetch_object()) {
			if ((int)$reg->idsucursal === $selectedSucursal) {
	            echo '<option value="' . (int)$reg->idsucursal . '" selected>' . $reg->nombre . '</option>';
	        } else {
	       		echo '<option value="' . (int)$reg->idsucursal . '">' . $reg->nombre . '</option>';
			}
	    }
	break;

	case 'selectSucursal':
		require_once "../modelos/Venta.php";
		$venta = new Venta();

		$rspta = $venta->listarSucursal2($_SESSION['idpersonal'], $_SESSION['idsucursal']);
		$sucursal = isset($_SESSION['idsucursal']) ? $_SESSION['idsucursal'] : 0;
		while ($reg = $rspta->fetch_object()) {
			if ($reg->idsucursal == $sucursal) {
				echo '<option value="' . $reg->idsucursal . '" selected>' . $reg->nombre . '</option>';
			} else {
				echo '<option value="' . $reg->idsucursal . '">' . $reg->nombre . '</option>';
			}
		}
		break;
	

	case 'selectSucursal2':
		require_once "../modelos/Venta.php";
		$venta = new Venta();

		$rspta = $venta->listarSucursal();

		echo '<option value="0">Acceso a todas las Sucursales</option>';

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idsucursal . '>' . $reg->nombre . '</option>';
		}
		break;

	case 'selectCliente':
	    require_once "../modelos/Persona.php";
	    $persona = new Persona();
	    $tipo_documento = isset($_POST["tipo_documento"]) ? $_POST["tipo_documento"] : "";
	    $es_factura = isset($_POST["es_factura"]) ? $_POST["es_factura"] : "0";
	    $rspta = $persona->listarc($tipo_documento);
	    
	    
	    while ($reg = $rspta->fetch_object()) {
	        echo '<option value="' . $reg->idpersona . '">' . $reg->nombre . ' - ' . $reg->num_documento . '</option>';
	    }
	    break;



	case 'obtenerCliente':
	    require_once "../modelos/Persona.php";
	    $persona = new Persona();
	    
	    // Obtener el idcliente de la solicitud
	    $idcliente = $_POST['idcliente'];

	    // Realizamos la consulta para obtener los datos del cliente
	    $rspta = $persona->obtenerPorId($idcliente);

	    // Verificar si el cliente existe y devolver el nombre
	    if ($reg = $rspta->fetch_object()) {
	        echo json_encode(array('nombre_cliente' => $reg->nombre));
	    } else {
	        echo json_encode(array('error' => 'Cliente no encontrado'));
	    }
	    break;


	case 'selectCliente2':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarc();

		echo '<option value="Todos">Todos</options>';

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . ' - ' . $reg->num_documento . '</option>';
		}
		break;

	case 'selectCliente3':

		$numero = $_GET['numero'];

		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarc2($numero);

		echo json_encode($rspta);

		break;

	case 'selectCliente4':

		$numero = $_GET['numero'];

		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarc3($numero);

		echo json_encode($rspta);

		break;

	case 'selectCliente5':

		$numero = $_GET['numero'];

		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarc3($numero);

		echo json_encode($rspta);

		break;

	case 'listarNC':

		$fecha_inicio = $_REQUEST["fecha_inicio"];
		$fecha_fin = $_REQUEST["fecha_fin"];
		$estado = $_REQUEST["estado"];
		$idsucursal = $_REQUEST["idsucursal2"];
		$rspta = $venta->listarNC($fecha_inicio, $fecha_fin, $estado,$idsucursal);
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$url1 = 'reportes/exTicket.php?id=';
			$url2 = 'reportes/factura/generaNotaCredito.php?id=';

			if ($reg->tipo_comprobante == 'NC' || $reg->tipo_comprobante == 'NCB') {
				$urlFac = 'public/FACT_WebService/Facturacion/NotaCredito.php?idnc=' . $reg->idventa . '&codColab=' . $reg->idpersonal . '';
			}

			$ruta = 'public/FACT_WebService/Facturacion/files/' . $reg->dov_Nombre . '.xml';

			$rutaCdr = 'public/FACT_WebService/Facturacion/files/R-' . $reg->dov_Nombre . '.zip';

			$pdf = '<a target="_blank" data-toggle="tooltip" title="" target="blanck" data-original-title="PDF" onclick="PDFNC(' . $reg->idventa . ',' . $reg->idpersonal . ')"> <button class="btn btn-info btn-xs"><i class="far fa-file"></i></button></a>';

			$ticket = '<a target="_blank" data-toggle="tooltip" title="" target="blanck" data-original-title="Ticket" onclick="TicketNC(' . $reg->idventa . ',' . $reg->idpersonal . ')"> <button class="btn btn-primary btn-xs"><i class="far fa-file-alt"></i></button></a>';

			if ($reg->estado == 'Aceptado') {
				$estado = '<span class="badge bg-green">ACEPTADO</span>';
				$pdf = '<a target="_blank" href="' . $url2 . $reg->idventa . '" data-toggle="tooltip" title="" target="blanck" data-original-title="PDF"> <button class="btn btn-info btn-xs"><i class="far fa-file"></i></button></a>';
				$ticket = '<a target="_blank" href="' . $url1 . $reg->idventa . '" data-toggle="tooltip" title="" target="blanck" data-original-title="Ticket"> <button class="btn btn-primary btn-xs"><i class="far fa-file-alt"></i></button></a>';
			} else if ($reg->estado == 'Por Enviar') {
				$estado = '<span class="badge bg-yellow">POR ENVIAR</span>';
			} else if ($reg->estado == 'Anulado') {
				$estado = '<span class="badge bg-red">ANULADO</span>';
			} else if ($reg->estado == 'Rechazado') {
				$estado = '<span class="badge bg-red">RECHAZADO</span>';
			} else {
				$estado = '<span class="badge bg-blue">ACTIVADO</span>';
			}

			if ($reg->estado == 'Por Enviar') {
				$sunat = '<a data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" onclick="EnviarSunat(3,' . $reg->idventa . ',' . $reg->idpersonal . ');"> <button class="btn btn-primary btn-xs"><i class="fa fa-upload"></i></button></a> ' . '<a href="' . $ruta . '" data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" style="pointer-events: none;"> <button class="btn btn-warning btn-xs"><i class="far fa-file"></i></button></a> ' . '<a href="' . $rutaCdr . '" data-toggle="tooltip" title="" data-original-title="Cdr" style="pointer-events: none;"> <button class="btn btn-danger btn-xs"><i class="fa fa-archive"></i></button></a> ';
			} else if ($reg->estado == 'Activado' || $reg->estado == 'Anulado') {

				$sunat = '<a href="' . $urlFac . '" data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" style="pointer-events: none;"> <button class="btn btn-primary btn-xs"><i class="fa fa-upload"></i></button></a> ' . '<a href="' . $ruta . '" data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" style="pointer-events: none;"> <button class="btn btn-warning btn-xs"><i class="far fa-file"></i></button></a> ' . '<a href="' . $rutaCdr . '" data-toggle="tooltip" title="" data-original-title="Cdr" style="pointer-events: none;"> <button class="btn btn-danger btn-xs"><i class="fa fa-archive"></i></button></a> ';
			} else {
				$sunat = '<a href="' . $urlFac . '" data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" style="pointer-events: none;"> <button class="btn btn-primary btn-xs"><i class="fa fa-upload"></i></button></a>' . '<a href="' . $ruta . '" data-toggle="tooltip" title="" data-original-title="XML" target="_blank"> <button class="btn btn-warning btn-xs"><i class="far fa-file"></i></button></a> ' . '<a href="' . $rutaCdr . '" data-toggle="tooltip" title="" data-original-title="Cdr"> <button class="btn btn-danger btn-xs"><i class="fa fa-archive"></i></button></a> ';
			}

			$data[] = array(
				"0" => $reg->fecha,
				"1" => $reg->cliente,
				"2" => $reg->sucursal,
				"3" => $reg->tipo_comprobante,
				"4" => $reg->serie_comprobante . '-' . $reg->num_comprobante,
				"5" => number_format($reg->total_anulado, 2, ".", ""),
				"6" => ($reg->ventacredito == 'Si') ? '<center><span class="badge bg-red">Crédito</span></center>' : '<center><span class="badge bg-primary">Contado</span></center>',
				"7" => $estado,
				"8" => $sunat,
				"9" => (($reg->estado == 'Activado') ?
					'<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fa fa-eye"></i></button>' . ' ' . '<button class="btn btn-danger btn-xs" onclick="anular(' . $reg->idventa . ')"><i class="fa fa-close"></i></button>' :
					'<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fa fa-eye"></i></button>') .
					$pdf .
					$ticket .
					'<a target="_blank" data-toggle="tooltip" title="" target="blanck" data-original-title="Enviar Comprobantes"> <button class="btn btn-success btn-xs" onclick="EnviarComprobante(' . $reg->idventa . ')"><i class="fab fa-whatsapp"></i></button></a>'
			);
		}
		$results = array(
			"sEcho" => 1, //info para datatables
			"iTotalRecords" => count($data), //enviamos el total de registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);
		break;

	case 'selectProducto2':
		$persona = new Producto();

		$rspta = $persona->listar4();

		echo '<option value="Todos">Todos</options>';

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idproducto . '>' . $reg->nombre . '</option>';
		}
		break;

	case 'selectProducto':
	    $idsucursal = $_REQUEST["idsucursal2"]; // Obtiene el ID de la sucursal seleccionada

	    $producto = new Producto();

	    if ($idsucursal === 'all') {
	        $rspta = $producto->listarTodos(); // Llama a la función que devuelve todos los productos
	    } else {
	        $rspta = $producto->listar($idsucursal); // Llama a la función que filtra por sucursal
	    }

	    echo '<option value="Todos">Todos</option>'; // Opción por defecto

	    while ($reg = $rspta->fetch_object()) {
	        echo '<option value="' . $reg->idproducto . '">' . $reg->nombre . '</option>';
	    }
	    break;

	case 'selectProductoV':
	    $idsucursal = $_REQUEST["idsucursal2"]; // Obtiene el ID de la sucursal seleccionada

	    $producto = new Producto();

	    // Si seleccionas 'all', listar todos los productos
	    if ($idsucursal === 'all' || $idsucursal === null) {
	        $rspta = $producto->listarTodosV(); // Llama a la función que devuelve todos los productos
	    } else {
	        $rspta = $producto->listarV($idsucursal); // Llama a la función que filtra por sucursal
	    }

	    echo '<option value="Todos">Todos</option>'; // Opción por defecto

	    while ($reg = $rspta->fetch_object()) {
	        echo '<option value="' . $reg->idproducto . '">' . $reg->nombre . '</option>';
	    }
	    break;



	case 'selectProductoS':
		$persona = new Producto();

		$idp = $_GET['idp'];

		$rspta = $persona->listarS($idp);

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idproducto . '>' . $reg->nombre . '</option>';
		}
		break;

	case 'selectProductoDesempaquetar':
		$persona = new Producto();

		$rspta = $persona->listar();

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idproducto . '>' . $reg->nombre . ' - ' . $reg->unidad . '</option>';
		}
		break;

	case 'selectVendedor':
	    require_once "../modelos/Persona.php";
	    $persona = new Persona();

	    $cargo = $persona->obtenerCargo($_SESSION['idusuario']);
	    $idsucursal = isset($_POST['idsucursal']) ? (int)$_POST['idsucursal'] : 0;

	    if ($cargo === 'Administrador') {

	        // Todas las sucursales
	        if ($idsucursal === 0) {
	            $rspta = $persona->listarv();
	        } else {
	            $rspta = $persona->listarvPorSucursal($idsucursal);
	        }

	        echo '<option value="0">Todos</option>';
	        while ($reg = $rspta->fetch_object()) {
	            echo '<option value="' . (int)$reg->idpersonal . '">' . $reg->nombre . '</option>';
	        }

	    } else {

	        // Vendedor normal: solo él mismo
	        $sql = "SELECT p.idpersonal, p.nombre
	                FROM personal p
	                INNER JOIN usuario u ON u.idpersonal = p.idpersonal
	                WHERE u.idusuario = '{$_SESSION['idusuario']}'";

	        $rspta = ejecutarConsultaSimpleFila($sql);

	        if ($rspta) {
	            echo '<option value="' . (int)$rspta['idpersonal'] . '" selected>' . $rspta['nombre'] . '</option>';
	        } else {
	            echo '<option value="">No asignado</option>';
	        }
	    }
	break;

	case 'selectProveedor':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarProveedor();

		echo '<option value="Todos">Todos</options>';

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
		}
		break;

	case 'listarArticulos3':

		$fechaActual = date('Y-m-d');

		$idsucursal = $_REQUEST["idsucursal"];

		require_once "../modelos/Producto.php";
		$producto = new Producto();

		$rspta = $producto->listarActivosVenta($idsucursal);

		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => (($reg->stock == 0) ? 
				    '<a class="btn btn-danger btn-sm" onclick="nostock()"> <span class="fa fa-shopping-cart"></span></a>' : 
				    '<a class="btn btn-success btn-sm" onclick="agregarDetalle(' . $reg->idproducto . ',\'' . $reg->nombre . '\',1,0,\'' . $reg->precio_venta . '\',\'' . $reg->preciocigv . '\',\'' . $reg->precioB . '\',\'' . $reg->precioC . '\',\'' . $reg->precioD . '\',\'' . $reg->stock . '\',\'' . $reg->proigv . '\',\'' . $reg->unidadmedida . '\'); mostrarAlerta(\'Se agrego correctamente al carrito\');"><span class="fa fa-shopping-cart"></span></a>'),

				"1"=>"<img src='files/productos/".$reg->imagen."' height='50px' width='50px'>",
				"2" => '<span style="font-weight: bold;">'.$reg->nombre.'</span>'.' - '.'<span style="font-size:10px">'.$reg->descripcion.'</span>',
				"3" => $reg->categoria,
				"4" => $reg->unidadmedida,
				"5" => $reg->stock,
				"6" => '<span class="badge bg-info">' . $reg->precio_venta . '</span>',
				"7" => $reg->descripcion

			);
		}
		$results = array(
			"sEcho" => 1, //info para datatables
			"iTotalRecords" => count($data), //enviamos el total de registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);

		break;

	/*case 'listarArticulos2':

		$fechaActual = date('Y-m-d');

		$idsucursal = $_REQUEST["idsucursal"];

		require_once "../modelos/Producto.php";
		$producto = new Producto();

		$rspta = $producto->listarActivosVenta2($idsucursal);

		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => '<button class="btn btn-success btn-sm" onclick="agregarDetalle(' . $reg->idproducto . ',\'' . $reg->nombre . '\',1,0,\'' . $reg->precio_venta . '\',\'' . $reg->preciocigv . '\',\'' . $reg->precioB . '\',\'' . $reg->precioC . '\',\'' . $reg->precioD . '\',\'1\',\'' . $reg->proigv . '\',\'' . $reg->contendor . '\')"><span class="fa fa-shopping-cart"></span></button>',
				"1" => $reg->nombre,
				//"2" => ($reg->fecha != $fechaActual) ? $reg->fecha : '<span class="badge bg-red">' . $reg->fecha . '</span>',
				"2" => $reg->contendor,
				"3" => $reg->categoria,
				"4" => 1,
				"5" => '<span class="badge bg-info">' . $reg->precio_venta . '</span>',

			);
		}
		$results = array(
			"sEcho" => 1, //info para datatables
			"iTotalRecords" => count($data), //enviamos el total de registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);

		break;*/

	case 'listarArticulos2':

		$fechaActual = date('Y-m-d');

		$idsucursal = $_REQUEST["idsucursal"];

		require_once "../modelos/Producto.php";
		$producto = new Producto();

		$rspta = $producto->listarActivosVenta2($idsucursal);

		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => ('<a class="btn btn-success btn-sm" onclick="agregarDetalle(' . $reg->id . ',' . $reg->idproducto . ',\'' . $reg->nombre . '\',1,0,\'' . $reg->precio_venta . '\',\'' . $reg->preciocigv . '\',\'' . $reg->precioB . '\',\'' . $reg->precioC . '\',\'' . $reg->precioD . '\',\'' . $reg->stock . '\',\'' . $reg->proigv . '\',\'' . $reg->cantidad_contenedor . '\',\'' . $reg->contenedor . '\',' . $reg->idcategoria . ')"><span class="fa fa-shopping-cart"></span></a>'),
				"1"=>"<img onclick='verimagen(" . $reg->idproducto . ", \"" . $reg->imagen . "\", \"" . $reg->nombre . "\",\"" . $reg->stock . "\",\"" . $reg->precio . "\")' src='files/productos/".$reg->imagen."' height='35px' width='35px' >". '<span style="font-weight: bold;">'.$reg->nombre.'</span>'.' - '.'<span class="badge bg-green">'.$reg->cantidad_contenedor.' Und.</span>'.' - '.'<span style="font-size:10px">'.$reg->contenedor.'</span>',
				//"2" => $reg->categoria,
				//"2" => $reg->codigo,
				"2" => floor($reg->stock / $reg->cantidad_contenedor),
				"3" => '<span class="badge bg-info">'.'S/ ' . $reg->precio . '</span>',
			);
		}
		$results = array(
			"sEcho" => 1, //info para datatables
			"iTotalRecords" => count($data), //enviamos el total de registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);

		break;

	case 'listarArticulosSearch':
	$fechaActual = date('Y-m-d');
	$idsucursal = isset($_REQUEST["idsucursal"]) ? $_REQUEST["idsucursal"] : "";
	$search = isset($_REQUEST["search"]) ? $_REQUEST["search"] : "";
	$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "";
	require_once "../modelos/Producto.php";
	$producto = new Producto();
	$rspta = $producto->listarArticulosSearch($idsucursal, $search, $type);
	$data = array();

	while ($reg = $rspta->fetch_object()) {
		$data[] = array(
			'id' => $reg->id,
			'idproducto' => $reg->idproducto,
			'nombre' => $reg->nombre,
			'precio_venta' => $reg->precio_venta,
			'preciocigv' => $reg->preciocigv,
			'precioB' => $reg->precioB,
			'precioC' => $reg->precioC,
			'precioD' => $reg->precioD ,
			'stock1' => $reg->stock,
			'stock_num' => $reg->stock,
			'proigv' => $reg->proigv,
			'cantidad_contenedor' => $reg->cantidad_contenedor,
			'contenedor' => $reg->contenedor,
			'idcategoria' => $reg->idcategoria,
			'unidadmedida' =>$reg->unidadmedida,
			"stock" => (($reg->stock == 0) 
				? '<a class="btn btn-danger btn-sm" onclick="nostock()"> <span class="fa fa-shopping-cart"></span></a>' 
				: '<a class="btn btn-success btn-sm" onclick="agregarDetalle(' . $reg->id . ',' . $reg->idproducto . ',\'' . $reg->nombre . '\',1,0,\'' . $reg->precio_venta . '\',\'' . $reg->preciocigv . '\',\'' . $reg->precioB . '\',\'' . $reg->precioC . '\',\'' . $reg->precioD . '\',\'' . $reg->stock . '\',\'' . $reg->proigv . '\',\'' . $reg->cantidad_contenedor . '\',\'' . $reg->contenedor . '\',' . $reg->idcategoria . ',\''.$reg->unidadmedida.'\')"><span class="fa fa-shopping-cart"></span></a>'),
			
			"product"=>"
				<div style='display: flex; align-items: center; gap: 6px;'>
				    <!-- Imagen -->
				    <img onclick='verimagen(" . $reg->idproducto . ", \"" . $reg->imagen . "\", \"" . $reg->nombre . "\",\"" . $reg->stock . "\",\"" . $reg->precio_venta . "\",\"" . $reg->fabricante . "\",\"" . $reg->descripcion . "\")' 
				         src='files/productos/".$reg->imagen."' 
				         height='35px' width='35px' 
				         style='border-radius: 6px; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.2);'>

				    <!-- Texto -->
				    <div style='min-width: 250px; text-align: left; word-wrap: break-word; overflow-wrap: break-word; line-height:1.3;'>
				        <!-- Nombre -->
				        <span style='font-weight:bold; font-size:12px; display:block; color:#222;'>
				            " . wordwrap($reg->nombre, 30, "<br>", true) . "
				        </span>
				        
				        <!-- Cantidad contenedor -->
				        <span style='font-size:10px; background:#28a745; color:#fff; padding:2px 6px; border-radius:6px; display:inline-block; margin:2px 0;'>
				            " . $reg->cantidad_contenedor . " Und.
				        </span>
				        
				        <!-- Unidad x Contenedor -->
				        <span style='font-size:11px; display:block; color:#0056b3; font-weight:bold; margin-top:1px;'>
				            " . $reg->unidadmedida . "
				            <span style='color:#d9534f; font-weight:bold; padding:0 3px;'>x</span>
				            " . $reg->contenedor . "
				        </span>
				    </div>
			    </div>",

			"code" => $reg->codigo_extra,
			"cat" => $reg->categoria,
			"quantity" => number_format(($reg->stock / $reg->cantidad_contenedor), 2, '.', ''),
			"price" => '<span class="badge bg-info">'.'S/ ' . $reg->precio_venta . '</span>',
		);
	}

	echo json_encode($data);
	break;

	case 'listarArticulosSearchFIFO':
	    require_once "../modelos/Producto.php";
	    $producto = new Producto();
	    $idsucursal = $_GET['idsucursal'] ?? '';
	    $search     = $_GET['search'] ?? '';
	    $type       = $_GET['type'] ?? 1;
	    
	    $rspta = $producto->listarActivosVentaFIFO($idsucursal, $search, $type);
	    $data = [];
	    
	    while ($reg = $rspta->fetch_object()) {
	        // ← Validar que haya stock
	        $stockLoteFifo = floatval($reg->stock_lote_fifo);
	        
	        // ← Si no hay lote FIFO activo, saltar este contenedor
	        if ($stockLoteFifo <= 0) {
	            $btnStock = '<a class="btn btn-danger btn-sm" onclick="nostock()">
	                            <span class="fa fa-shopping-cart"></span>
	                         </a>';
	        } else {
	            $btnStock = '<a class="btn btn-success btn-sm"
	                onclick="agregarDetalle(
	                    '.$reg->id_producto_config.',
	                    '.$reg->id_producto_real.',
	                    \''.addslashes($reg->nombre).'\',
	                    1,
	                    0,
	                    '.$reg->precio_venta_fifo.',
	                    0, 0, 0, 0,
	                    '.$stockLoteFifo.',
	                    \''.$reg->proigv.'\',
	                    '.$reg->cantidad_contenedor.',
	                    \''.addslashes($reg->contenedor).'\',
	                    '.$reg->idcategoria.',
	                    \''.addslashes($reg->unidadmedida).'\',
	                    '.$reg->id_fifo.'
	                )">
	                <span class="fa fa-shopping-cart"></span>
	            </a>';
	        }
	        
	        // ← Calcular cantidad disponible en contenedores
	        $cantidadContenedor = max(1, floatval($reg->cantidad_contenedor));
	        $cantidadDisponible = floor($stockLoteFifo / $cantidadContenedor);
	        
	        $data[] = [
	            "stock" => $btnStock,
	            "product" => "
	            <div style='display:flex; align-items:center; gap:6px;'>
	                <img src='files/productos/".$reg->imagen."'
	                     height='35' width='35'
	                     style='border-radius: 6px; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.2);'
	                     onclick='verimagen(
	                        ".$reg->id_producto_real.",
	                        \"".$reg->imagen."\",
	                        \"".addslashes($reg->nombre)."\",
	                        \"".$stockLoteFifo."\",
	                        \"".$reg->precio_venta_fifo."\"
	                     )'>
	                <div style='min-width:250px; line-height:1.3;'>
	                    <span style='font-weight:bold; font-size:12px; display:block;'>
	                        ".wordwrap($reg->nombre, 30, "<br>", true)."
	                    </span>
	                    <span style='font-size:10px; background:#28a745; color:#fff;
	                          padding:2px 6px; border-radius:6px; display:inline-block; margin:2px 0;'>
	                        ".$reg->cantidad_contenedor." Und.
	                    </span>
	                    <span style='font-size:11px; color:#0056b3; font-weight:bold;'>
	                        ".$reg->unidadmedida."
	                        <span style='color:#d9534f;'> x </span>
	                        ".addslashes($reg->contenedor)."
	                    </span>
	                </div>
	            </div>",
	            "cat"      => $reg->categoria,
	            "code"     => $reg->codigo,
	            "quantity" => number_format($cantidadDisponible, 0),
	            "price"    => '<span class="badge bg-info">S/ '
	                            .number_format($reg->precio_venta_fifo, 2).
	                          '</span>'
	        ];
	    }
	    
	    echo json_encode($data);
	break;

	case 'listarArticulos':

		$fechaActual = date('Y-m-d');

		$idsucursal = $_REQUEST["idsucursal"];

		require_once "../modelos/Producto.php";
		$producto = new Producto();

		$rspta = $producto->listarActivosVenta($idsucursal);

		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => (($reg->stock == 0) ? '<a class="btn btn-danger btn-sm" onclick="nostock()"> <span class="fa fa-shopping-cart"></span></a>' 
				: '<a class="btn btn-success btn-sm" onclick="agregarDetalle(' . $reg->id . ',' . $reg->idproducto . ',\'' . $reg->nombre . '\',1,0,\'' . $reg->precio_venta . '\',\'' . $reg->preciocigv . '\',\'' . $reg->precioB . '\',\'' . $reg->precioC . '\',\'' . $reg->precioD . '\',\'' . $reg->stock . '\',\'' . $reg->proigv . '\',\'' . $reg->cantidad_contenedor . '\',\'' . $reg->contenedor . '\',' . $reg->idcategoria . ')"><span class="fa fa-shopping-cart"></span></a>'),
				"1" => "<div style='display: flex; align-items: center; gap: 1px;'>
				            <img onclick='verimagen(" . $reg->idproducto . ", \"" . $reg->imagen . "\", \"" . $reg->nombre . "\",\"" . $reg->stock . "\",\"" . $reg->precio_venta . "\",\"" . $reg->precioB . "\",\"" . $reg->precioC . "\",\"" . $reg->precioD . "\",\"" . $reg->precioE . "\" ,\"" . $reg->margenpubl . "\",\"" . $reg->margendes . "\",\"" . $reg->margenp1 . "\",\"" . $reg->margenp2 . "\",\"" . $reg->margendist . "\",\"" . $reg->utilprecio . "\",\"" . $reg->utilprecioB . "\",\"" . $reg->utilprecioC . "\",\"" . $reg->utilprecioD . "\",\"" . $reg->utilprecioE . "\")' 
				                 src='files/productos/".$reg->imagen."' 
				                 height='35px' width='35px' 
				                 style='border-radius: 5px; cursor: pointer;'>

				            <div style='min-width: 250px; text-align: left; word-wrap: break-word; overflow-wrap: break-word;'>
				                <span style='font-weight: bold; font-size:12px; display: block;'>" . wordwrap($reg->nombre, 30, "<br>", true) . "</span>
				                <span class='badge bg-green' style='font-size:10px;'>" . $reg->cantidad_contenedor . " Und.</span>
				                <span style='font-size:10px; display: block;'>" . $reg->contenedor . "</span>
				            </div>
       				 	</div>",
				//"2" => $reg->categoria,
				"2" => "<div style='min-width: 120px; text-align: left;'>" . $reg->codigo . "</div>",
				"3" => floor($reg->stock / $reg->cantidad_contenedor),
				"4" => '<span class="badge bg-info">'.'S/ ' . $reg->precio_venta . '</span>',
				"5" => '<span class="badge bg-orange text-white">'.'S/ '.$reg->precioB.'</span>',
				"6" => '<span class="badge bg-purple">'.' S/ '.$reg->precioC.'</span>',
				"7" => '<span class="badge bg-primary">'.'S/ '.$reg->precioD.'</span>',
				"8"=>'<span class="badge bg-orange">'.'S/ '.$reg->precioE.'</span>',
 					//' '.'<span class="badge bg-purple">'.'PrecioII '.' S/ '.$reg->precioC.'</span>'.
 					//' '.'<span class="badge bg-primary">'.'PrecioIII '.'S/ '.$reg->precioD.'</span>',
			);
		}
		$results = array(
			"sEcho" => 1, //info para datatables
			"iTotalRecords" => count($data), //enviamos el total de registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);

		break;

	case 'updateFactura':
		$rspta = $venta->updateBoleta($idventa);
		echo json_encode($rspta);
		break;

	case 'updateBoleta':
		$rspta = $venta->updateFactura($idventa);
		echo json_encode($rspta);
		break;

	case 'selectComprobante':
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->select();

		while ($reg = $rspta->fetch_object()) {
			echo '<option value="' . $reg->nombre . '">' . $reg->nombre . '</option>';
		}
		break;

	case 'selectComprobante2':
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->selectNC();

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->nombre . '>' . $reg->nombre . '</option>';
		}
		break;

	case 'selectDocumentos':
	    require_once "../modelos/Comprobantes.php";
	    $comprobantes = new Comprobantes();

	    $idsucursal = isset($_POST["idsucursal2"]) ? limpiarCadena($_POST["idsucursal2"]) : "";

	    $rspta = $comprobantes->selectDocumentos($idsucursal);

	    while ($reg = $rspta->fetch_object()) {
	        echo '<option value="' . $reg->idventa . '">' . $reg->serie_comprobante . '-' . $reg->num_comprobante . '</option>';
	    }
	break;

	case 'selectMotivos':
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->selectMotivos();

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->id . '>' . $reg->descripcion . '</option>';
		}
		break;

	case 'buscarProducto':

		$codigo = $_REQUEST["codigo"];

		$rspta = $venta->buscarProducto($codigo);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);

		break;

	case 'mostrarUltimoCliente':

		$rspta = $venta->mostrarUltimoCliente();
		echo json_encode($rspta);

		break;

	case 'listarDetalleVenta':

		$rspta = $venta->ventadetalle($idventa);

		$data = array();

		while ($reg = $rspta->fetch_object()) {

			$data[] = array(
				0 => $reg->id,
                1 => $reg->idproducto,
                2 => $reg->producto,
                3 => $reg->cantidad,
                4 => $reg->descuento,
                5 => $reg->precio_venta,
                6 => $reg->precioB,
                7 => $reg->precioC,
                8 => $reg->precioD,
                9 => $reg->preciocigv,
                10 => $reg->stock,
                11 => $reg->proigv,
                12 => $reg->unidadmedida,
                13 => $reg->cantidad_contenedor,
                14 => $reg->contenedor,
                15 => $reg->subtotal,
                16 => $reg->idcategoria
			);
		}

		echo json_encode($data);


		break;

	case 'listarhistorialcliente':
	    $idcliente = $_GET['idcliente'];
	    $fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
	    $fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;
	    $ventas = $venta->listarHistorialCliente($idcliente, $fecha_inicio, $fecha_fin);

	   // print_r($ventas);
	    //exit();

	    // Verificar el resultado de la función
	    if ($ventas === false) {
	        echo "Error en la consulta.";
	    } else {
	        echo  json_encode($ventas);
	    }
	    break;

	// Listar pisos disponibles


// ===================== LISTAR PRODUCTOS =========================



case 'selectProductoFiltro':
    require_once "../modelos/Producto.php";
    $producto = new Producto();
    $rspta = $producto->selectProductosVenta();

    echo '<option value="Todos">Todos</option>';  // ← ESTA ES LA LÍNEA NECESARIA

    while ($reg = $rspta->fetch_object()) {
        echo '<option value="' . $reg->idproducto . '">' . $reg->nombre . '</option>';
    }
break;

case 'exportar_excel':
    $venta = new Venta();

    $fecha_inicio = $_GET["fecha_inicio"];
    $fecha_fin = $_GET["fecha_fin"];
    $estado = $_GET["estado"];
    $idsucursal = $_GET["idsucursal"];
    $idproducto = $_GET["idproducto"];

    $venta->exportarExcel($fecha_inicio, $fecha_fin, $estado, $idsucursal, $idproducto);
break;

case 'cambiar_comprobante':
    $idventa = $_POST['idventa'];
    $idsucursal = $_POST['idsucursal'];
    $tipo = $_POST['tipo']; // Boleta o Factura
    $rspta = $venta->cambiarComprobante($idventa, $tipo, $idsucursal);
    echo $rspta;
break;

case 'selectClienteRUC':

    $sql = "SELECT idpersona, nombre, num_documento 
            FROM persona 
            WHERE tipo_documento='RUC'";

    $rspta = ejecutarConsulta($sql);

    while ($reg = $rspta->fetch_object()) {
        echo '<option value="'.$reg->idpersona.'">'.$reg->nombre.' - '.$reg->num_documento.'</option>';
    }
break;

case 'actualizarClienteVentaFactura':

    $idventa = $_POST["idventa"];
    $idcliente = $_POST["idcliente"];

    $sql = "UPDATE venta SET idcliente='$idcliente' WHERE idventa='$idventa'";
    echo ejecutarConsulta($sql);
break;

case 'selectProductoFiltro':
    require_once "../modelos/Producto.php";
    $producto = new Producto();
    $rspta = $producto->selectProductosVenta();

    echo '<option value="Todos">Todos</option>';  // ← ESTA ES LA LÍNEA NECESARIA

    while ($reg = $rspta->fetch_object()) {
        echo '<option value="' . $reg->idproducto . '">' . $reg->nombre . '</option>';
    }
break;

case 'listarProductosCliente':
    $idcliente = isset($_POST["idcliente"]) ? $_POST["idcliente"] : "";
    // 1. RECIBIMOS LA SUCURSAL
    $idsucursal = isset($_POST["idsucursal"]) ? $_POST["idsucursal"] : ""; 
    
    $ids_carrito = isset($_POST["ids_carrito"]) ? $_POST["ids_carrito"] : [];
    $ids_carrito = array_map('intval', $ids_carrito);

    if($idcliente == "") {
        echo json_encode([]);
        break;
    }

    // 2. PASAMOS LA SUCURSAL COMO SEGUNDO PARÁMETRO
    $rspta = $venta->listarUltimosProductosCliente($idcliente, $idsucursal, $ids_carrito);
    
    $data = array();

    while ($reg = $rspta->fetch_object()) {
        // ... (Tu código del while sigue igual) ...
        $id_db = intval($reg->id_real);
        $coincide = in_array($id_db, $ids_carrito);
        $fecha_visual = str_replace(' ', '<br><small class="text-muted">', $reg->fecha) . ' HORAS</small>';

        $data[] = array(
            "fecha" => $fecha_visual,
            "producto" => $reg->nombre_producto,
            "cantidad" => number_format($reg->cantidad, 0) . ' <small class="text-muted">' . $reg->contenedor . '</small>',
            "precio" => 'S/ ' . number_format($reg->precio_venta, 2),
            "descuento" => $reg->descuento > 0 ? number_format($reg->descuento, 2) : '-',
            "subtotal" => 'S/ ' . number_format($reg->subtotal, 2),
            "comprobante" => $reg->tipo_comprobante . ' ' . $reg->serie_comprobante . '-' . $reg->num_comprobante,
            "coincide" => $coincide
        );
    }
    echo json_encode($data);
break;

}