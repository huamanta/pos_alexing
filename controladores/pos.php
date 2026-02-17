<?php
ob_start();
session_start();
date_default_timezone_set('America/Lima');
require_once "../modelos/pos.php";
$pos = new Pos();
switch ($_GET['op']) {
    case 'verificarCaja':
        $idusuario = $_SESSION["idusuario"];
        $rpta = $pos->verificarCaja($idusuario);
        echo json_encode($rpta);
        break;

    case 'listarCajas':
        $idsucursal = $_SESSION["idsucursal"];
        $rpta = $pos->listarCajas($idsucursal);
        echo json_encode($rpta);
        break;

    case 'listarVentas':
        $idcaja = isset($_GET["idcaja"]) ? limpiarCadena($_GET["idcaja"]) : "";
        $idsucursal = $_SESSION["idsucursal"];
        $idusuario = $_SESSION["idusuario"];
        $estado = isset($_GET["estado"]) ? limpiarCadena($_GET["estado"]) : "";
        $rpta = $pos->listarVentas($idcaja, $idsucursal, $idusuario, $estado);
        $data = array();

		while ($reg = $rpta->fetch_object()) {
			$url1 = 'reportes/exTicket.php?id=';
			$url2 = 'reportes/factura/generaFactura.php?id=';

			$ruta = 'public/FACT_WebService/Facturacion/files/' . $reg->dov_Nombre . '.xml';

			$rutaCdr = 'public/FACT_WebService/Facturacion/files/R-' . $reg->dov_Nombre . '.zip';

			if ($reg->tipo_comprobante == 'Boleta') {

				$enviarSunat = '<a data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" onclick="EnviarSunat(1,' . $reg->idventa . ',' . $reg->idpersonal . ');"> <button class="btn btn-primary btn-xs"><i class="fa fa-upload"></i></button></a> '
					. '<a href="' . $ruta . '" data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" style="pointer-events: none;"> <button class="btn btn-warning btn-xs"><i class="far fa-file"></i></button></a> ' . '<a href="' . $rutaCdr . '" data-toggle="tooltip" title="" data-original-title="Cdr" style="pointer-events: none;"> <button class="btn btn-danger btn-xs"><i class="fa fa-archive"></i></button></a> ';

				$pdf = '<a target="_blank" data-toggle="tooltip" title="" target="blanck" data-original-title="PDF" onclick="PDF(1,' . $reg->idventa . ',' . $reg->idpersonal . ')"> <button class="btn btn-info btn-xs"><i class="far fa-file"></i></button></a>';

				$ticket = '<a target="_blank" data-toggle="tooltip" title="" target="blanck" data-original-title="Ticket" onclick="Ticket(1,' . $reg->idventa . ',' . $reg->idpersonal . ')"> <button class="btn btn-primary btn-xs"><i class="far fa-file-alt"></i></button></a>';
			} else {
				$enviarSunat = '<a data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" onclick="EnviarSunat(2,' . $reg->idventa . ',' . $reg->idpersonal . ');"> <button class="btn btn-primary btn-xs"><i class="fa fa-upload"></i></button></a> '
					. '<a href="' . $ruta . '" data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" style="pointer-events: none;"> <button class="btn btn-warning btn-xs"><i class="far fa-file"></i></button></a> ' . '<a href="' . $rutaCdr . '" data-toggle="tooltip" title="" data-original-title="Cdr" style="pointer-events: none;"> <button class="btn btn-danger btn-xs"><i class="fa fa-archive"></i></button></a> ';

				$pdf = '<a target="_blank" data-toggle="tooltip" title="" target="blanck" data-original-title="PDF" onclick="PDF(2,' . $reg->idventa . ',' . $reg->idpersonal . ')"> <button class="btn btn-info btn-xs"><i class="far fa-file"></i></button></a>';

				$ticket = '<a target="_blank" data-toggle="tooltip" title="" target="blanck" data-original-title="Ticket" onclick="Ticket(2,' . $reg->idventa . ',' . $reg->idpersonal . ')"> <button class="btn btn-primary btn-xs"><i class="far fa-file-alt"></i></button></a>';
			}

			$urlComprobarEstado = 'public/FACT_WebService/Facturacion/consultacdr.php?idventa=' . $reg->idventa . '&codColab=' . $reg->idpersonal . '';

			if ($reg->estado == 'Aceptado') {
				$estado = '<span class="badge bg-green">ACEPTADO</span>';
				$pdf = '<a target="_blank" href="' . $url2 . $reg->idventa . '" data-toggle="tooltip" title="" target="blanck" data-original-title="PDF"> <button class="btn btn-info btn-xs"><i class="far fa-file"></i></button></a>';
				$ticket = '<a target="_blank" href="' . $url1 . $reg->idventa . '" data-toggle="tooltip" title="" target="blanck" data-original-title="Ticket"> <button class="btn btn-primary btn-xs"><i class="far fa-file"></i></button></a>';
			} else if ($reg->estado == 'Por Enviar') {
				$estado = '<span class="badge bg-yellow">POR ENVIAR</span>';
			} else if ($reg->estado == 'Anulado') {
				$estado = '<span class="badge bg-red">ANULADO</span>';
			} else if ($reg->estado == 'Nota Credito') {
				$estado = '<span class="badge bg-red">NOTA DE CRÉDITO</span>';
				$pdf = '<a target="_blank" href="' . $url2 . $reg->idventa . '" data-toggle="tooltip" title="" target="blanck" data-original-title="PDF"> <button class="btn btn-info btn-xs"><i class="far fa-file"></i></button></a>';
				$ticket = '<a target="_blank" href="' . $url1 . $reg->idventa . '" data-toggle="tooltip" title="" target="blanck" data-original-title="Ticket"> <button class="btn btn-primary btn-xs"><i class="far fa-file-alt"></i></button></a>';
			} else if ($reg->estado == 'Rechazado') {
				$estado = '<span class="badge bg-red">RECHAZADO</span>';
				$pdf = '<a target="_blank" href="' . $url2 . $reg->idventa . '" data-toggle="tooltip" title="" target="blanck" data-original-title="PDF"> <button class="btn btn-info btn-xs"><i class="far fa-file"></i></button></a>';
				$ticket = '<a target="_blank" href="' . $url1 . $reg->idventa . '" data-toggle="tooltip" title="" target="blanck" data-original-title="Ticket"> <button class="btn btn-primary btn-xs"><i class="far fa-file-alt"></i></button></a>';
			} else {
				$estado = '<span class="badge bg-blue">ACTIVADO</span>';
				$pdf = '<a target="_blank" href="' . $url2 . $reg->idventa . '" data-toggle="tooltip" title="" target="blanck" data-original-title="PDF"> <button class="btn btn-info btn-xs"><i class="far fa-file"></i></button></a>';
				$ticket = '<a target="_blank" href="' . $url1 . $reg->idventa . '" data-toggle="tooltip" title="" target="blanck" data-original-title="Ticket"> <button class="btn btn-primary btn-xs"><i class="far fa-file-alt"></i></button></a>';
			}

			if ($reg->estado == 'Por Enviar') {

				$sunat = $enviarSunat;
			} else if ($reg->estado == 'Activado' || $reg->estado == 'Anulado') {

				$sunat = '<a data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" style="pointer-events: none;"> <button class="btn btn-primary btn-xs"><i class="fa fa-upload"></i></button></a> ' . '<a href="' . $ruta . '" data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" style="pointer-events: none;"> <button class="btn btn-warning btn-xs"><i class="far fa-file"></i></button></a> ' . '<a href="' . $rutaCdr . '" data-toggle="tooltip" title="" data-original-title="Cdr" style="pointer-events: none;"> <button class="btn btn-danger btn-xs"><i class="fa fa-archive"></i></button></a> ';
			} else {
				$sunat = '<a data-toggle="tooltip" title="" data-original-title="Enviar a Sunat" style="pointer-events: none;"> <button class="btn btn-primary btn-xs"><i class="fa fa-upload"></i></button></a>' . '<a href="' . $ruta . '" data-toggle="tooltip" title="" data-original-title="XML" target="_blank"> <button class="btn btn-warning btn-xs"><i class="far fa-file"></i></button></a> ' . '<a href="' . $rutaCdr . '" data-toggle="tooltip" title="" data-original-title="Cdr"> <button class="btn btn-danger btn-xs"><i class="fa fa-archive"></i></button></a> ';
			}

			if ($reg->tipo_comprobante == 'Nota') {
				$comprobarEstado = '<center><a href="' . $urlComprobarEstado . '" data-toggle="tooltip" title="" data-original-title="Comprobar Estado" style="pointer-events: none;"> <button class="btn btn-warning btn-xs" onclick="ComprobarEstado(' . $reg->idventa . ')"><i class="fa fa-exclamation"></i></button></a></center>';
			} else {
				$comprobarEstado = '<center><a data-toggle="tooltip" title="" data-original-title="Comprobar Estado" onclick="comprobarEstado(' . $reg->idventa . ',' . $reg->idpersonal . ');"> <button class="btn btn-warning btn-xs"><i class="fa fa-exclamation"></i></button></a></center>';
			}

			if ($reg->estado != 'Anulado') {
				$mostrarResumen = '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fa fa-eye"></i></button>';
				$enviarComprobante = '<a target="_blank" data-toggle="tooltip" title="" target="blanck" data-original-title="Enviar Comprobantes"> <button class="btn btn-success btn-xs" onclick="EnviarComprobante(' . $reg->idventa . ')"><i class="fab fa-whatsapp"></i></button></a>';
				$mostrar = $pdf . $ticket;
				$sunatE = $sunat;
			} else {
				$mostrarResumen = '';
				$enviarComprobante = '';
				$mostrar = "-";
				$sunatE = "-";
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

			$boleta = 'Boleta';
			$factura = 'Factura';

			$data[] = array(
				"0" => $reg->fecha_kardex,
				"1" => $reg->cliente . ' - ' . $reg->num_documento,
				"2" => $reg->sucursal,
				"3" => $reg->tipo_comprobante . ' - ' . $reg->serie_comprobante . ' - ' . $reg->num_comprobante,
				"4" => '<span class="badge bg-purple">'.'S/ '.$reg->total_venta.'</span>',
				"5" => ($reg->ventacredito == 'Si') ? '<center><span class="badge bg-red">Crédito</span></center>' : '<center><span class="badge bg-primary">Contado</span></center>',
				"6" => $estado,
				"7" => $sunatE,
				"8" => $comprobarEstado,
				"9" => (($reg->estado == 'Activado') ?
					'<div class="dropdown">
					<button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> <i class="fa fa-list-ul"></i>
					<span class="caret"></span></button>

					<div class="dropdown-menu">
						<a class="dropdown-item" style="cursor:pointer;" onclick="anularComprobante('.$reg->idventa.')">Eliminar</a>
					</div>

					<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fa fa-eye"></i></button>

					<a target="_blank" data-toggle="tooltip" title="" target="blanck" data-original-title="Enviar Comprobantes"> <button class="btn btn-success btn-xs" onclick="EnviarComprobante(' . $reg->idventa . ')"><i class="fab fa-whatsapp"></i></button></a>
				 ' :

					$mostrarResumen .

					$enviarComprobante .
					'') .


					$mostrar .
					'</div>'
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

    case 'listarVentas2':
        $aperturacajaid = isset($_GET["aperturacajaid"]) ? limpiarCadena($_GET["aperturacajaid"]) : "";
        $rpta = $pos->listarVentas2($aperturacajaid);
        $data = array();

        while ($reg = $rpta->fetch_object()) {
            $data[] = array(
                "0" => $reg->fecha_kardex,
                "1" => $reg->cliente . ' - ' . $reg->num_documento,
                "2" => $reg->sucursal,
                "3" => $reg->tipo_comprobante . ' - ' . $reg->serie_comprobante . ' - ' . $reg->num_comprobante,
                "4" => '<span class="badge bg-purple">'.'S/ '.$reg->pagos.'</span>',
                "5" => ($reg->ventacredito == 'Si') ? '<center><span class="badge bg-red">Crédito</span></center>' : '<center><span class="badge bg-primary">Contado</span></center>',
               	"6" => '<span class="badge bg-red">'.$reg->estado.'</span>',
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


    case 'aperturarCaja':
        $fecha_hora = date("Y-m-d H:i:s");
        $efectivo_apertura = isset($_POST["efectivo_apertura"]) ? limpiarCadena($_POST["efectivo_apertura"]) : "";
        $caja_apertura = isset($_POST["caja_apertura"]) ? limpiarCadena($_POST["caja_apertura"]) : "";
        $idusuario = $_SESSION["idusuario"];
        $rpta = $pos->aperturarCaja($fecha_hora, $efectivo_apertura, $caja_apertura, $idusuario);
        if ($rpta) {
            $info = array('status' => 1);
        } else {
            $info = array('status' => 0);
        }
        echo json_encode($info);
        break;

     case 'showEfectivo':
        $idcaja = isset($_GET["idcaja"]) ? limpiarCadena($_GET["idcaja"]) : "";
        $idsucursal = $_SESSION["idsucursal"];
        $idusuario = $_SESSION["idusuario"];
        $rpta = $pos->showEfectivo($idcaja, $idsucursal, $idusuario);
        echo $rpta;
        # code...
        break;


    case 'cerrarCaja':
        $fecha_hora = date("Y-m-d H:i:s");
        $efectivo_cierre = isset($_GET["efectivo_cierre"]) ? limpiarCadena($_GET["efectivo_cierre"]) : "";;
        $idcaja = isset($_GET["idcaja"]) ? limpiarCadena($_GET["idcaja"]) : "";
        $idsucursal = isset($_GET["idsucursal"]) ? limpiarCadena($_GET["idsucursal"]) : $_SESSION["idsucursal"];
        $idusuario = $_SESSION["idusuario"];
        $rpta = $pos->cerrarCaja($fecha_hora, $efectivo_cierre, $idcaja, $idusuario, $idsucursal);
        if ($rpta) {
            $info = array('status' => 1);
        } else {
            $info = array('status' => 0);
        }
        echo json_encode($info);
        break;

    case 'showResumenCaja':
	    $idcaja = isset($_GET["idcaja"]) ? limpiarCadena($_GET["idcaja"]) : "";
	    $idsucursal = isset($_GET["idsucursal"]) ? limpiarCadena($_GET["idsucursal"]) : $_SESSION["idsucursal"];
	    $idusuario = $_SESSION["idusuario"];
	    $rpta = $pos->showResumenCaja($idcaja, $idsucursal, $idusuario);
	    echo $rpta;
	    break;


    case 'listarProductos':
        $idsucursal = intval($_GET["idsucursal"] ?? $_SESSION["idsucursal"] ?? 0);
        $categoria  = isset($_GET["categoria"]) ? trim($_GET["categoria"]) : null;
        $rpta = $pos->listarProductosActivosFIFO($idsucursal,  $categoria);
        echo json_encode($rpta);
        break;

    case 'listarCategorias':
        $rpta = $pos->listarCategorias();
        echo json_encode($rpta);
        break;

    case 'searchProductos':
        $idsucursal = $_SESSION["idsucursal"];
	    $producto = isset($_GET["producto"]) ? limpiarCadena($_GET["producto"]) : "";
	    $tipo = isset($_GET["type"]) ? intval($_GET["type"]) : 1;
	    $rpta = $pos->searchProductosFIFO($idsucursal, $producto, $tipo);
	    echo json_encode($rpta);
	    break;


    case 'verPreciosItem':
        $idusuario = $_SESSION["idusuario"];
        $idproducto = isset($_GET["idproducto"]) ? limpiarCadena($_GET["idproducto"]) : "";
        $rpta = $pos->verPreciosItem($idproducto, $idusuario);
        echo json_encode($rpta);
        break;

    case 'seleccionarProducto':
    $configuration = isset($_POST["id"]) ? limpiarCadena($_POST["id"]) : "";
    $idproducto = isset($_POST["idproducto"]) ? limpiarCadena($_POST["idproducto"]) : "";
    $producto = isset($_POST["producto"]) ? limpiarCadena($_POST["producto"]) : "";
    $nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
    $precio = isset($_POST["precio"]) ? limpiarCadena($_POST["precio"]) : "";
    $contenedor = isset($_POST["contenedor"]) ? limpiarCadena($_POST["contenedor"]) : "";
    $cantidad_contenedor = isset($_POST["cantidad_contenedor"]) ? limpiarCadena($_POST["cantidad_contenedor"]) : "";
    $cantidad = isset($_POST["cantidad"]) ? limpiarCadena($_POST["cantidad"]) : "1";
    $stock_disponible = isset($_POST["stock_disponible"]) ? limpiarCadena($_POST["stock_disponible"]) : "";
    $id_fifo = isset($_POST["id_fifo"]) ? limpiarCadena($_POST["id_fifo"]) : ""; 
    $token = hash("SHA256", $_SESSION['idusuario']);
    
    $rpta = $pos->seleccionarProducto(
        $idproducto, 
        $producto, 
        $nombre, 
        $token, 
        $precio,
        $contenedor, 
        $cantidad_contenedor, 
        $cantidad, 
        $configuration, 
        $stock_disponible,
        $id_fifo
    );
    
    echo $rpta;
    break;


    case 'listarCarrito':
        $token = hash("SHA256", $_SESSION['idusuario']);
        $rpta = $pos->listarCarrito($token);
        echo json_encode($rpta);
        break;

    case 'eliminarProductoCarrito':
        $idproducto = isset($_GET["idproducto"]) ? limpiarCadena($_GET["idproducto"]) : "";
        $token = hash("SHA256", $_SESSION['idusuario']);
        $rpta = $pos->eliminarProductoCarrito($idproducto, $token);
        if ($rpta) {
            $info = array('status' => 1);
        } else {
            $info = array('status' => 0);
        }
        echo json_encode($info);
        break;

    case 'sumarProductoCarrito':
        $idproducto = isset($_GET["idproducto"]) ? limpiarCadena($_GET["idproducto"]) : "";
        $token = hash("SHA256", $_SESSION['idusuario']);
        $rpta = $pos->sumarProductoCarrito($idproducto, $token);
        if ($rpta) {
            $info = array('status' => 1);
        } else {
            $info = array('status' => 0);
        }
        echo json_encode($info);
        break;

    case 'keyUpProductoCarrito':
        $idproducto = isset($_GET["idproducto"]) ? limpiarCadena($_GET["idproducto"]) : "";
        $cantidad = isset($_GET["cantidad"]) ? limpiarCadena($_GET["cantidad"]) : "";
        $token = hash("SHA256", $_SESSION['idusuario']);
        $rpta = $pos->keyUpProductoCarrito($idproducto, $token, $cantidad);
        if ($rpta) {
            $info = array('status' => 1);
        } else {
            $info = array('status' => 0);
        }
        echo json_encode($info);
        break;


    case 'restarProductoCarrito':
        $idproducto = isset($_GET["idproducto"]) ? limpiarCadena($_GET["idproducto"]) : "";
        $token = hash("SHA256", $_SESSION['idusuario']);
        $rpta = $pos->restarProductoCarrito($idproducto, $token);
        if ($rpta) {
            $info = array('status' => 1);
        } else {
            $info = array('status' => 0);
        }
        echo json_encode($info);
        break;

    case "actualizarPrecioVenta":
	    if (isset($_POST["idproducto"]) && isset($_POST["precio_venta"])) {
	        $idproducto = $_POST["idproducto"];
	        $precio_venta = $_POST["precio_venta"];

	        // Suponiendo que estás manejando el carrito en $_SESSION
	        foreach ($_SESSION["carrito"] as &$item) {
	            if ($item["idproducto"] == $idproducto) {
	                $item["precio_venta"] = $precio_venta;
	                break;
	            }
	        }
	        echo json_encode(["status" => "success", "message" => "Precio actualizado"]);
	    } else {
	        echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
	    }
	    break;


    case 'procesarVenta':
    $fecha_hora = date("Y-m-d H:i:s");
    $tipo_comprobante = isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";
    $serie_comprobante = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";
    $num_comprobante = isset($_POST["num_comprobante"]) ? limpiarCadena($_POST["num_comprobante"]) : "";
    $idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
    $idsucursal = isset($_POST["idsucursal"]) ? limpiarCadena($_POST["idsucursal"]) : "";
    $input_total_venta = isset($_POST["input-total-venta"]) ? floatval($_POST["input-total-venta"]) : 0;
    $observaciones = isset($_POST["observaciones"]) ? limpiarCadena($_POST["observaciones"]) : "";
    $idmotivo = isset($_POST["idmotivo"]) ? limpiarCadena($_POST["idmotivo"]) : "";
    $pagado_total = isset($_POST["pagado_total"]) ? floatval($_POST["pagado_total"]) : 0; // name en el form
    $total_comision = isset($_POST["total_comision"]) ? floatval($_POST["total_comision"]) : 0;
    $vuelto = isset($_POST["vuelto"]) ? floatval($_POST["vuelto"]) : 0;
    $tipopago = isset($_POST["tipopago"]) ? limpiarCadena($_POST["tipopago"]) : "";
    $idpersonal = $_SESSION['idpersonal'];
    $idcaja = isset($_POST["idcaja"]) ? limpiarCadena($_POST["idcaja"]) : "";
    $totalDescuento = isset($_POST["totalDescuento"]) ? floatval($_POST["totalDescuento"]) : 0;
    $token = hash("SHA256", $_SESSION['idusuario']);
    $nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";

    // Arrays de pagos (pueden venir vacíos)
    $pagado = isset($_POST["pagado"]) ? $_POST["pagado"] : array(); // pagado[]
    $metodos = isset($_POST["metodo_pago"]) ? $_POST["metodo_pago"] : array(); // metodo_pago[]
    $nroOperacion = isset($_POST['nroOperacion']) ? $_POST['nroOperacion'] : array();
    $banco = isset($_POST['banco']) ? $_POST['banco'] : array();
    $fechaDeposito = isset($_POST['fechaDeposito']) ? $_POST['fechaDeposito'] : array();

    // Totales enviados desde JS (hidden inputs)
    $totalrecibido = isset($_POST['totalrecibido']) ? floatval($_POST['totalrecibido']) : 0;
    $totaldeposito = isset($_POST['totaldeposito']) ? floatval($_POST['totaldeposito']) : 0;
    $vuelto = isset($_POST['vuelto']) ? floatval($_POST['vuelto']) : 0;

    // Llamada: asegurarnos de pasar argumentos en el mismo orden que la función
    $rpta = $pos->procesarVenta(
        $tipo_comprobante,
        $serie_comprobante,
        $num_comprobante,
        $idcliente,
        $idpersonal,
        $idsucursal,
        $idcaja,
        $input_total_venta,
        $pagado_total,
        $totalrecibido,
        $totaldeposito,
        $tipopago,
        $total_comision,
        $token,
        $pagado,
        $idmotivo,
        $observaciones,
        $fecha_hora,
        $vuelto,
        $totalDescuento,
        $nombre,
        $metodos,
        $nroOperacion,
        $banco,
        $fechaDeposito
    );

    if ($rpta) {
        $info = array('status' => 1, 'idventa' => $rpta);
    } else {
        $info = array('status' => 0, 'idventa' => '');
    }
    echo json_encode($info);
    break;

    case 'actualizarDataItem':
        $idproducto = isset($_POST["idproducto"]) ? limpiarCadena($_POST["idproducto"]) : "";
        $campo = isset($_POST["campo"]) ? limpiarCadena($_POST["campo"]) : "";
        $value = isset($_POST["value"]) ? limpiarCadena($_POST["value"]) : "";
        $token = hash("SHA256", $_SESSION['idusuario']);
        $rpta = $pos->actualizarDataItem($idproducto, $campo, $value, $token);
        echo $rpta? json_encode(array('status' => 1)) : json_encode(array('status' => 0));
        break;

    default:
        # code...
        break;
}
