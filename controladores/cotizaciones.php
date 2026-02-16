<?php
require_once "../modelos/Cotizaciones.php";
if (strlen(session_id()) < 1)
	session_start();

if (!isset($_SESSION['cotizacion_token'])) {
    // Generar token único para esta cotización temporal
    $_SESSION['cotizacion_token'] = bin2hex(random_bytes(8)); // 16 caracteres
}
$token = $_SESSION['cotizacion_token'];


$venta = new Cotizacion();

$idcotizacion = isset($_POST["idcotizacion"]) ? limpiarCadena($_POST["idcotizacion"]) : "";
$idsucursal = isset($_POST["idsucursal"]) ? limpiarCadena($_POST["idsucursal"]) : "";
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

switch ($_GET["op"]) {

	case 'guardaryeditar':

		if (empty($idcotizacion)) {
			$rspta = $venta->insertar($idsucursal, $idcliente, $idpersonal, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha, $total_venta, $titulo, $saludo, $nota, $igv, $formapago,$observaciones, $tiempoproduccion, $_POST["idproducto"], $_POST["cantidad"], $_POST["precio_venta"], $_POST["descuento"], $_POST["contenedor"], 
			$_POST["cantidad_contenedor"], $_POST["idp"]);
			echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
		} else {
			$rspta = $venta->editar($idcotizacion, $idsucursal, $idcliente,  $idpersonal, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha, $total_venta, $titulo, $saludo, $nota, $igv, $formapago,$observaciones, $tiempoproduccion, $_POST["idproducto"], $_POST["cantidad"], $_POST["precio_venta"], $_POST["descuento"], $_POST["contenedor"], 
			$_POST["cantidad_contenedor"], $_POST["idp"]);
			echo $rspta ? "Datos editados correctamente" : "No se pudo editar la Cotización";
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
	    $rspta = $venta->mostrar($idcotizacion);
	    if ($rspta) {
	        echo json_encode($rspta);
	    } else {
	        echo json_encode(["error" => "No se encontró la cotización"]);
	    }
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

		//_______________________________________________________________________________________________________

		//opcion para mostrar la numeracion y la serie_comprobante de la ticket
	case 'mostrar_num_ticket':
		$idsucursal = $_REQUEST["idsucursal"];
		//mostrando el numero de boleta de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_numero_cotizacion($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$num_comp_tic = $reg->num_comprobante
			);
		}
		$numero_tic_comp = (int)$num_comp_tic;
		//fin de mostrar numero de boleta de la tabla comprobantes
		$rspta = $venta->numero_venta_cotizacion($idsucursal);
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
		//mostrando el numero de factura de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_serie_cotizacion($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$serie_comp_tic = $reg->serie_comprobante,
				$num_comp_tic = $reg->num_comprobante
			);
		}
		$serie_tic_comp = (int)$serie_comp_tic;
		$num_tic_comp = (int)$num_comp_tic;
		//fin de mostrar numero de factura de la tabla comprobantes
		$rspta = $venta->numero_serie_cotizacion($idsucursal);
		$data = array();
		$numero_s_tic = $serie_tic_comp;
		$numero_bolet = $num_tic_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numero_s_tic = $reg->serie_comprobante,
				$numero_bolet = $reg->num_comprobante
			);
		}
		$num_s_ticket = (int)$numero_s_tic;
		$nuew_serie_ticket = 0;
		$numbo = (int)$numero_bolet;
		if ($numbo == 9999999 or empty($numero_s_tic)) {
			$nuew_serie_ticket = $num_s_ticket + 1;
			echo json_encode($nuew_serie_ticket);
		} else {
			echo json_encode($num_s_ticket);
		}
		break; //fin de opcion de mostrar num_comprobante y serie_comprobante del ticket

		//______________________________________________________________________________________________


	case 'listarDetalle':

		require_once "../modelos/Negocio.php";
		$cnegocio = new Negocio();
		$rsptan = $cnegocio->listar();
		$regn = $rsptan->fetch_object();
		if (empty($regn)) {
			$smoneda = 'Simbolo de moneda';
		} else {
			$smoneda = $regn->simbolo;
			$nom_imp = $regn->nombre_impuesto;
		};

		//recibimos el idcotizacion
		$id = $_GET['id'];

		$rspta = $venta->listarDetalle($id);
		$total = 0;
		echo ' <thead style="background-color:#A9D0F5">
        <th style="text-align:center; width: 200px;">Producto</th>
        <th style="text-align:center; width: 40px;">Cantidad</th>
        <th style="text-align:center;">Precio Venta</th>
        <th style="text-align:center;">Descuento</th>
        <th style="text-align:center;">Subtotal</th>
       </thead>';
		while ($reg = $rspta->fetch_object()) {
			echo '<tr class="filas">
			<td style="text-align:center; width: 200px;">' . $reg->nombre . '</td>
			<td style="text-align:center; width: 40px;">' . $reg->cantidad . ' - ' . $reg->contenedor .  '</td>
			<td style="text-align:center;">' . $reg->precio_venta . '</td>
			<td style="text-align:center;">' . $reg->descuento . '</td>
			<td style="text-align:center;">' . $reg->subtotal . '</td></tr>';
			$total = $reg->total_venta;
		}

		echo '<tfoot>
         <th></th>
         <th></th>
         <th></th>
         <th>TOTAL</th>
         <th><h4 id="total">' . $smoneda . ' ' . $total . '</h4><input type="hidden" name="total_venta" id="total_venta"></th>
       </tfoot>';

		break;

	case 'listarDetalleCotizacion':

		$rspta = $venta->ventadetalle($idcotizacion);

		$data = array();

		while ($reg = $rspta->fetch_object()) {

			$data[] = array(
				"0" => $reg->id,
				"1" => $reg->idproducto,
				"2" => $reg->producto.' ('.$reg->contenedor.')',
				"3" => $reg->cantidad,
				"4" => $reg->descuento,
				"5" => $reg->precio_venta,
				"6" => $reg->precioB,
				"7" => $reg->precioC,
				"8" => $reg->precioD,
				"9" => $reg->preciocigv,
				"10" => $reg->stock,
				"11" => $reg->proigv,
				"12" => $reg->unidadmedida,
				"13" => $reg->cantidad_contenedor,
				"14" => $reg->contenedor
			);
		}

		echo json_encode($data);


		break;

	case 'listar':

		$fecha_inicio = $_REQUEST["fecha_inicio"];
		$fecha_fin = $_REQUEST["fecha_fin"];
		$idsucursal2 = $_REQUEST["idsucursal2"];

		$rspta = $venta->listar($fecha_inicio, $fecha_fin, $idsucursal2);
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$url1 = 'reportes/exTicketCoti.php?id=';
			$url2 = 'reportes/factura/generaFacturaCoti.php?id=';

			if($reg->estado == 'EN ESPERA'){

				$estado = '<span class="badge bg-yellow">EN ESPERA</span>';
				$editar = '<button class="btn btn-success btn-xs" onclick="mostrarEditar(' . $reg->idcotizacion . ')" data-toggle="tooltip" title="" target="blanck" data-original-title="EDITAR COTIZACIÓN"><i class="fas fa-edit"></i></button> ';
				$desistir = '<button class="btn btn-danger btn-xs" onclick="desistir(' . $reg->idcotizacion . ')" data-toggle="tooltip" title="" target="blanck" data-original-title="DESISTIR"><i class="fa fa-times"></i></button>';

			}else if($reg->estado == 'VENDIDO'){

				$estado = '<span class="badge bg-green">VENDIDO</span>';
				$editar = '';
				$desistir = '';

			}else{

				$estado = '<span class="badge bg-red">DESISTIÓ</span>';
				$editar = '';
				$desistir = '';

			}

			$data[] = array(
				"0" => $reg->fecha,
				"1" => $reg->cliente,
				"2" => $reg->personal,
				"3" => $reg->tipo_comprobante,
				"4" => $reg->serie_comprobante . '-' . $reg->num_comprobante,
				"5" => $reg->total_venta,
				"6" => $estado,
				"7" => '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idcotizacion . ')" data-toggle="tooltip" title="" target="blanck" data-original-title="VER"><i class="fa fa-eye"></i></button>' . ' ' .
					$editar .
					'<a target="_blank" href="' . $url2 . $reg->idcotizacion . '" data-toggle="tooltip" title="" target="blanck" data-original-title="PDF"> <button class="btn btn-info btn-xs"><i class="fa fa-file"></i></button></a>' .
					'<a target="_blank" data-toggle="tooltip" title="" target="blanck" data-original-title="ENVIAR COMPROBANTE"> <button class="btn btn-success btn-xs" onclick="EnviarComprobante(' . $reg->idcotizacion . ')"><i class="fab fa-whatsapp"></i></button></a> ' .
					$desistir
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

	case 'selectCliente':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarc();

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . ' - ' . $reg->num_documento . '</option>';
		}
		break;

	case 'selectProducto':
		require_once "../modelos/Producto.php";
		$persona = new Producto();

		$rspta = $persona->listar();

		echo '<option value="Todos">Todos</options>';

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idproducto . '>' . $reg->nombre . '</option>';
		}
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
				"1"=>"<img onclick='verimagen(" . $reg->idproducto . ", \"" . $reg->imagen . "\", \"" . $reg->nombre . "\",\"" . $reg->stock . "\",\"" . $reg->precio_venta . "\",\"" . $reg->precioB . "\",\"" . $reg->precioC . "\",\"" . $reg->precioD . "\" )' src='files/productos/".$reg->imagen."' height='35px' width='35px' >". '<span style="font-weight: bold;">'.$reg->nombre.'</span>'.' - '.'<span class="badge bg-green">'.$reg->cantidad_contenedor.' Und.</span>'.' - '.'<span style="font-size:10px">'.$reg->contenedor.'</span>',
				//"2" => $reg->categoria,
				"2" => $reg->codigo,
				"3" => floor($reg->stock / $reg->cantidad_contenedor),
				"4" => '<span class="badge bg-info">'.'S/ ' . $reg->precio_venta . '</span>',
				//"4" => '<span class="badge bg-orange">'.'S/ '.$reg->precioB.'</span>',
				//"5" => '<span class="badge bg-purple">'.' S/ '.$reg->precioC.'</span>',
				//"6" => '<span class="badge bg-primary">'.'S/ '.$reg->precioD.'</span>',
				//"4"=>'<span class="badge bg-orange">'.'PrecioI '.'S/ '.$reg->precioB.'</span>'.
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

	/*case 'listarArticulos':

		$idsucursal = $_REQUEST["idsucursal"];

		require_once "../modelos/Producto.php";
		$producto = new Producto();

		$rspta = $producto->listarActivosVenta($idsucursal);
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => '<button class="btn btn-success" onclick="agregarDetalle(' . $reg->id . ',' . $reg->idproducto . ',\'' . $reg->nombre . '\',1,0,\'' . $reg->precio_venta . '\',\'' . $reg->preciocigv . '\',\'' . $reg->precioB . '\',\'' . $reg->precioC . '\',\'' . $reg->precioD . '\',\'' . $reg->stock . '\',\'' . $reg->proigv . '\',\'' . $reg->contenedor . '\',\'' . $reg->cantidad_contenedor . '\',' . $reg->idcategoria . ')"><span class="fa fa-shopping-cart"></span></button>',
				"1"=>"<img onclick='verimagen(" . $reg->idproducto . ", \"" . $reg->imagen . "\", \"" . $reg->nombre . "\",\"" . $reg->stock . "\",\"" . $reg->precio_venta . "\",\"" . $reg->precioB . "\",\"" . $reg->precioC . "\",\"" . $reg->precioD . "\" )' src='files/productos/".$reg->imagen."' height='35px' width='35px' >". '<span style="font-weight: bold;">'.$reg->nombre.'</span>'.' - '.'<span class="badge bg-green">'.$reg->cantidad_contenedor.' Und.</span>'.' - '.'<span style="font-size:10px">'.$reg->contenedor.'</span>',
				//"2" => $reg->categoria,
				//"2" => $reg->codigo,
				"2" => $reg->stock,
				"3" => $reg->precio_venta,
				//"4" => "<img src='files/productos/" . $reg->imagen . "' height='50px' width='50px'>"

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

	case 'selectComprobante':
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->select2();

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->nombre . '>' . $reg->nombre . '</option>';
		}
		break;

	case 'selectCotizaciones':
		require_once "../modelos/Cotizaciones.php";
		$venta = new Cotizacion();

		$rspta = $venta->listar2();

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idcotizacion . '>' . $reg->serie_comprobante . '-' . $reg->num_comprobante . '</option>';
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
            "idtmp"               => $reg->idtmp,
            "idproducto"          => $reg->idproducto,
            "idp"                 => $reg->idp,
            "nombre"              => $reg->nombre,
            "contenedor"          => $reg->contenedor,
            "cantidad_contenedor" => $reg->cantidad_contenedor,
            "cantidad"            => $reg->cantidad,
            "precio_venta"        => $reg->precio_venta,
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

case 'guardarDatosTmp':
    $idusuario = $_SESSION['idusuario'];
    $token     = $_SESSION['cotizacion_token'] ?? '';

    if (!$token) {
        echo json_encode(["status"=>"error","msg"=>"No hay token de cotización"]);
        exit;
    }

    // Recibir valores de forma segura
    $idsucursal        = $_POST['idsucursal'] ?? '';
    $idcliente         = $_POST['idcliente'] ?? '';
    $tipo_comprobante  = $_POST['tipo_comprobante'] ?? '';
    $serie_comprobante = $_POST['serie_comprobante'] ?? '';
    $num_comprobante   = $_POST['num_comprobante'] ?? '';
    $titulo            = $_POST['titulo'] ?? '';
    $saludo            = $_POST['saludo'] ?? '';
    $nota              = $_POST['nota'] ?? '';
    $igv               = $_POST['igv'] ?? '0.00';
    $formapago         = $_POST['formapago'] ?? '';
    $observacion       = $_POST['observacion'] ?? '';
    $tiempo_pro        = $_POST['tiempoproduccion'] ?? '';
    $total_venta       = $_POST['total_venta'] ?? '0.00';

    if (empty($idsucursal) || empty($idcliente) || empty($tipo_comprobante)) {
        echo json_encode(["status"=>"incompleto","msg"=>"Faltan campos clave"]);
        exit;
    }

    $sql_check = "SELECT idtmp FROM cotizacion_cab_tmp WHERE token='$token'";
    $rspta = ejecutarConsultaSimpleFila($sql_check);

    if ($rspta) {
        // Actualizar temporal existente
        $sql_update = "UPDATE cotizacion_cab_tmp SET
            idsucursal='$idsucursal',
            idcliente='$idcliente',
            tipo_comprobante='$tipo_comprobante',
            serie_comprobante='$serie_comprobante',
            num_comprobante='$num_comprobante',
            titulo='$titulo',
            saludo='$saludo',
            nota='$nota',
            igv='$igv',
            formapago='$formapago',
            observacion='$observacion',
            tiempoproduccion='$tiempo_pro',
            total_venta='$total_venta'
        WHERE token='$token'";
        ejecutarConsulta($sql_update);
    } else {
        // Insertar nuevo temporal
        $sql_insert = "INSERT INTO cotizacion_cab_tmp 
            (idusuario, token, idsucursal, idcliente, tipo_comprobante, serie_comprobante, num_comprobante, titulo, saludo, nota, igv, formapago, observacion, tiempoproduccion, total_venta)
        VALUES
            ('$idusuario','$token','$idsucursal','$idcliente','$tipo_comprobante','$serie_comprobante','$num_comprobante','$titulo','$saludo','$nota','$igv','$formapago','$observacion','$tiempo_pro','$total_venta')";
        ejecutarConsulta($sql_insert);
    }

    echo json_encode(["status"=>"ok"]);
break;


case 'obtenerDatosTmp':
    $token = $_SESSION['cotizacion_token'] ?? '';
    if (!$token) {
        echo json_encode([]);
        exit;
    }

    $sql = "SELECT * FROM cotizacion_cab_tmp WHERE token='$token' LIMIT 1";
    $rspta = ejecutarConsultaSimpleFila($sql);
    echo json_encode($rspta);
break;



}
