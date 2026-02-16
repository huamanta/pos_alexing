<?php
ob_start();
if (strlen(session_id()) < 1)
  session_start();

require_once "../modelos/Guia.php";
require_once "../modelos/Persona.php";
require_once "../modelos/Negocio.php";
require_once "../modelos/Comprobantes.php";

$guia = new Guia();
$persona = new Persona();
$negocio = new Negocio();
$comprobante = new Comprobantes();

$idguia = isset($_POST["idguia"]) ? limpiarCadena($_POST["idguia"]) : "";
$idsucursal = isset($_POST["idsucursal"]) ? limpiarCadena($_POST["idsucursal"]) : "";
$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
$idpersonal = isset($_SESSION["idpersonal"]) ? limpiarCadena($_SESSION["idpersonal"]) : "";
$serie = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";
$numero = isset($_POST["num_comprobante"]) ? limpiarCadena($_POST["num_comprobante"]) : "";
$fecha_emision = isset($_POST["fecha_emision"]) ? limpiarCadena($_POST["fecha_emision"]) : "";
$fecha_traslado = isset($_POST["fecha_traslado"]) ? limpiarCadena($_POST["fecha_traslado"]) : "";
$idcomprobante = isset($_POST["idcomprobante"]) ? limpiarCadena($_POST["idcomprobante"]) : "";
$factura_ref = isset($_POST["factura_ref"]) ? limpiarCadena($_POST["factura_ref"]) : "";
$fecha_factura_ref = isset($_POST["fecha_factura_ref"]) ? limpiarCadena($_POST["fecha_factura_ref"]) : "";
$tipo_transporte = isset($_POST["tipo_transporte"]) ? limpiarCadena($_POST["tipo_transporte"]) : "";
$idtransportista = isset($_POST["idtransportista"]) ? limpiarCadena($_POST["idtransportista"]) : "";
$peso = isset($_POST["peso"]) ? limpiarCadena($_POST["peso"]) : "";
$punto_partida = isset($_POST["punto_partida"]) ? limpiarCadena($_POST["punto_partida"]) : "";
$ubigeo_partida = isset($_POST["ubigeo_partida"]) ? limpiarCadena($_POST["ubigeo_partida"]) : "";
$punto_llegada = isset($_POST["punto_llegada"]) ? limpiarCadena($_POST["punto_llegada"]) : "";
$ubigeo_llegada = isset($_POST["ubigeo_llegada"]) ? limpiarCadena($_POST["ubigeo_llegada"]) : "";
$atencion = isset($_POST["atencion"]) ? limpiarCadena($_POST["atencion"]) : "";
$referencia = isset($_POST["referencia"]) ? limpiarCadena($_POST["referencia"]) : "";
$idtrabajador = isset($_POST["idtrabajador"]) ? limpiarCadena($_POST["idtrabajador"]) : "";
$idmotivo = isset($_POST["idmotivo"]) ? limpiarCadena($_POST["idmotivo"]) : "";
$ord_compra = isset($_POST["ord_compra"]) ? limpiarCadena($_POST["ord_compra"]) : "";
$ord_pedido = isset($_POST["ord_pedido"]) ? limpiarCadena($_POST["ord_pedido"]) : "";
$observacion = isset($_POST["observacion"]) ? limpiarCadena($_POST["observacion"]) : "";

switch ($_GET["op"]) {
  case 'guardaryeditar':
    if (empty($idguia)) {
      $rspta = $guia->insertar(
        $idsucursal,
        $idcliente,
        $idpersonal,
        $serie,
        $numero,
        $fecha_emision,
        $fecha_traslado,
        $factura_ref,
        $fecha_factura_ref,
        $tipo_transporte,
        $idtransportista,
        $peso,
        $punto_partida,
        $ubigeo_partida,
        $punto_llegada,
        $ubigeo_llegada,
        $atencion,
        $referencia,
        $idtrabajador,
        $idmotivo,
        $ord_compra,
        $ord_pedido,
        $observacion,
        $_POST["idproducto"],
        $_POST["codigo"],
        $_POST["nombre_producto"],
        $_POST["cantidad"],
        $_POST["unidad"],
        $_POST["peso_det"],
        $_POST["bultos"],
        $_POST["lotes"]
      );
      echo $rspta ? "Guía registrada" : "No se pudieron registrar todos los datos de la Guía";
    } else {
      $rspta = $guia->editar(
        $idguia,
        $idsucursal,
        $idcliente,
        $idpersonal,
        $serie,
        $numero,
        $fecha_emision,
        $fecha_traslado,
        $factura_ref,
        $fecha_factura_ref,
        $tipo_transporte,
        $idtransportista,
        $peso,
        $punto_partida,
        $ubigeo_partida,
        $punto_llegada,
        $ubigeo_llegada,
        $atencion,
        $referencia,
        $idtrabajador,
        $idmotivo,
        $ord_compra,
        $ord_pedido,
        $observacion,
        $_POST["idproducto"],
        $_POST["codigo"],
        $_POST["nombre_producto"],
        $_POST["cantidad"],
        $_POST["unidad"],
        $_POST["peso_det"],
        $_POST["bultos"],
        $_POST["lotes"]
      );
      echo $rspta ? "Guía actualizada" : "No se pudieron actualizar todos los datos de la Guía";
    }
    break;

  case 'mostrar':
    $rspta = $guia->mostrar($idguia);
    echo json_encode($rspta);
    break;

  case 'anular':
    $rspta = $guia->anular($idguia);
    echo $rspta ? "Guía anulada" : "Guía no se puede anular";
    break;

  case 'baja_sunat':
    $idguia = $_POST['idguia'];
    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/test/public/FACT_WebService/Facturacion/baja_guia.php';
    $postData = ['idguia' => $idguia, 'motivo' => 'Error en los datos'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    echo $response;
    break;

  case 'send_sunat':
    $idguia = $_POST['idguia'];
    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/test/public/FACT_WebService/Facturacion/guia.php?idguia=' . $idguia;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    echo $response;
    break;

  case 'listar':

    $idsucursal = $_GET["idsucursal2"];
    $fecha_inicio = $_GET["fecha_inicio"];
    $fecha_fin = $_GET["fecha_fin"];
    $estado = $_GET["estado"];

    $rspta = $guia->listar($idsucursal, $fecha_inicio, $fecha_fin, $estado);
    $data = array();

    while ($reg = $rspta->fetch_object()) {
      $url = '../reportes/exGuia.php?id=';
      $data[] = array(
        "0" => $reg->idguia,
        "1" => $reg->serie . '-' . $reg->numero,
        "2" => $reg->fecha_emision,
        "3" => $reg->cliente,
        "4" => ($reg->estado == 'Por Enviar') ? '<span class="label bg-yellow">Por Enviar</span>' : (($reg->estado == 'Aceptado') ? '<span class="label bg-green">Aceptado</span>' : (($reg->estado == 'Anulado') ? '<span class="label bg-red">Anulado</span>' : '<span class="label bg-blue">Nota de Crédito</span>')),
        "5" => ($reg->estado == 'Anulado') ? '<button class="btn btn-danger btn-xs" disabled><i class="fa fa-ban"></i></button>' : '<button class="btn btn-danger btn-xs" onclick="anular(' . $reg->idguia . ')"><i class="fa fa-ban"></i></button>' .
          ' <a target="_blank" href="' . $url . $reg->idguia . '"> <button class="btn btn-info btn-xs"><i class="fa fa-file-text"></i></button></a> ' .
          '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idguia . ')"><i class="fa fa-edit"></i></button>',
        "6" => ($reg->estado_sunat == '1') ? '<button class="btn btn-success btn-xs" onclick="baja_sunat(' . $reg->idguia . ')">Dar de baja</button>' : (($reg->estado_sunat == '3' || $reg->estado == 'Anulado') ? '' : '<button class="btn btn-warning btn-xs" onclick="send_sunat(' . $reg->idguia . ')">Enviar a Sunat</button>'),
        "7" => ($reg->estado_sunat == '1') ? '<a target="_blank" href="../public/FACT_WebService/files/produccion/' . $reg->ruc . '/GRE/' . $reg->serie . '-' . $reg->numero . '.xml"> <i class="fa fa-download"></i></a>' : '',
        "8" => $reg->resumen_sunat,
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

  case 'listarDetalles':
    $rspta = $guia->listarDetalles($_POST["idguia"]);
    $data = [];
    while ($reg = $rspta->fetch_object()) {
        $data[] = $reg;
    }
    echo json_encode($data);
    break;

  case 'selectCliente':
    $rspta = $persona->listarC();
    echo '<option value="">Seleccione</option>';
    while ($reg = $rspta->fetch_object()) {
      echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
    }
    break;

  case 'selectTransportista':
    $rspta = $persona->listarTransportista();
    echo '<option value="">Seleccione</option>';
    while ($reg = $rspta->fetch_object()) {
      echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
    }
    break;

  case 'selectPersonal':
    $rspta = $persona->listarPersonal();
    echo '<option value="">Seleccione</option>';
    while ($reg = $rspta->fetch_object()) {
      echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
    }
    break;

  case 'selectMotivo':
    require_once "../modelos/Consultas.php";
    $consulta = new Consultas();
    $rspta = $consulta->listarMotivosTraslado();
    echo '<option value="">Seleccione</option>';
    while ($reg = $rspta->fetch_object()) {
      echo '<option value=' . $reg->idmotivo . '>' . $reg->descripcion . '</option>';
    }
    break;

  case 'selectComprobante':
    $rspta = $comprobante->select_comprobantes_guia();
    echo '<option value="">Seleccione</option>';
    while ($reg = $rspta->fetch_object()) {
      echo '<option value=' . $reg->idcomprobante . '>' . $reg->nombre . '</option>';
    }
    break;

  case 'get_numeracion':
    $idsucursal = $_POST["idsucursal"];
    $serie = $_POST["serie"];
    $rspta = $comprobante->get_numeracion_guia($idsucursal, $serie);
    echo json_encode($rspta);
    break;

  case "listarArticulos":
    require_once "../modelos/Producto.php";
    $producto = new Producto();
    $rspta = $producto->listarActivosVenta($idsucursal);
    $data = array();

    while ($reg = $rspta->fetch_object()) {
      $data[] = array(
        "0" => $reg->codigo,
        "1" => $reg->nombre,
        "2" => $reg->stock,
        "3" => $reg->unidad,
        "4" => '<button class="btn btn-success btn-xs" onclick="agregarDetalle(' . $reg->idproducto . ',\'' . $reg->codigo . '\',\'' . $reg->nombre . '\',\'' . $reg->unidad . '\')"><span class="fa fa-plus"></span></button>'
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

  case 'getComprobante':
    require_once "../modelos/Venta.php";
    $venta = new Venta();
    $idventa = $_POST["idventa"];
    $rspta = $venta->getVentaData($idventa);
    $detalles = $venta->getVentaDetalles($idventa);
    $data = array(
      'cabecera' => $rspta,
      'detalles' => $detalles->fetch_all(MYSQLI_ASSOC)
    );
    echo json_encode($data);
    break;

  case 'getSeries':
    $idsucursal = $_POST["idsucursal"];
    $rspta = $comprobante->getSeries($idsucursal);
    while ($reg = $rspta->fetch_object()) {
      echo '<option value=' . $reg->serie_comprobante . '>' . $reg->serie_comprobante . '</option>';
    }
    break;

  case 'getSeries':
    $idsucursal = $_POST["idsucursal"];
    $rspta = $comprobante->getSeries($idsucursal);
    while ($reg = $rspta->fetch_object()) {
      echo '<option value=' . $reg->serie_comprobante . '>' . $reg->serie_comprobante . '</option>';
    }
    break;

  case 'selectDepartamento':
    $rspta = $guia->getDepartamentos();
    echo '<option value="">Seleccione</option>';
    while ($reg = $rspta->fetch_object()) {
      echo '<option value=' . $reg->id . '>' . $reg->name . '</option>';
    }
    break;

  case 'selectProvincia':
    $iddepartamento = $_POST['iddepartamento'];
    $rspta = $guia->getProvincias($iddepartamento);
    echo '<option value="">Seleccione</option>';
    while ($reg = $rspta->fetch_object()) {
      echo '<option value=' . $reg->id . '>' . $reg->name . '</option>';
    }
    break;

  case 'selectDistrito':
    $idprovincia = $_POST['idprovincia'];
    $rspta = $guia->getDistritos($idprovincia);
    echo '<option value="">Seleccione</option>';
    while ($reg = $rspta->fetch_object()) {
      echo '<option value=' . $reg->id . '>' . $reg->name . '</option>';
    }
    break;
}

ob_end_flush();
?>

