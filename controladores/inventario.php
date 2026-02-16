<?php
session_start();
require_once "../modelos/Inventario.php";

$inventario = new Inventario();

$idinventario_edit = isset($_POST["idinventario_edit"]) ? limpiarCadena($_POST["idinventario_edit"]) : "";
$fecha_apertura = isset($_POST["fecha_apertura"]) ? limpiarCadena($_POST["fecha_apertura"]) : "";
$observacion_apertura = isset($_POST["observacion_apertura"]) ? limpiarCadena($_POST["observacion_apertura"]) : "";
$fecha_cierre = isset($_POST["fecha_cierre"]) ? limpiarCadena($_POST["fecha_cierre"]) : "";
$observacion_cierre = isset($_POST["observacion_cierre"]) ? limpiarCadena($_POST["observacion_cierre"]) : "";

switch ($_GET["op"]) {

	case 'listar':
	    $rspta = $inventario->listar();
	    $data = array();

	    while ($reg = $rspta->fetch_object()) {
	        $fecha_cierre = $reg->fecha_cierre ? date("d-m-Y h:i A", strtotime($reg->fecha_cierre)) : null;

	        // Definir estado
	        $estado = $reg->fecha_cierre
	            ? '<span class="badge badge-success">Cerrado</span>'
	            : '<span class="badge badge-danger">Abierto</span>';

	        $data[] = array(
	            "0" => date("d-m-Y h:i A", strtotime($reg->fecha_apertura)),
	            "1" => $reg->observacion_apertura,
	            "2" => $fecha_cierre ? $fecha_cierre : "—",
	            "3" => $reg->observacion_cierre ? $reg->observacion_cierre : "—",
	            "4" => $estado, // 👈 Nueva columna de estado
	            "5" => 
	                '<button class="btn btn-warning btn-xs" onclick="ver(' . $reg->id . ')"><i class="fas fa-eye"></i></button>' .
	                ' <button class="btn btn-secondary btn-xs" onclick="mostrarform(true, ' . $reg->id . ', ' . $reg->sucursal_id . ', \'' . htmlspecialchars($fecha_cierre, ENT_QUOTES) . '\')"><i class="fa fa-cog"></i></button>' .
	                ' <button class="btn btn-primary btn-xs" onclick="editar(' . $reg->id . ', \'' . htmlspecialchars($reg->observacion_apertura, ENT_QUOTES) . '\')"><i class="fa fa-edit"></i></button>'
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

	case 'guardaryeditar':
		$usuario_id = $_SESSION['idusuario'];
		$sucursal_id = isset($_POST["idsucursal_save"]) ? limpiarCadena($_POST["idsucursal_save"]) : "";
		if ($idinventario_edit) {
			$rspta = $inventario->editar($idinventario_edit, $observacion_apertura, $sucursal_id, $usuario_id);
			echo $rspta ? "Apertura de inventario editado correctamente" : "Apertura de inventario no se pudo editar";
		} else {
			$rspta = $inventario->guardar($observacion_apertura, $sucursal_id, $usuario_id);
			echo $rspta ? "Apertura de inventario guardado correctamente" : "Apertura de inventario no se pudo guardar";
		}
		break;

	case 'buscar_producto':
		$nombre = isset($_GET["nombre"]) ? limpiarCadena($_GET["nombre"]) : "";
		$codigo = isset($_GET["codigo"]) ? limpiarCadena($_GET["codigo"]) : "";
		$categoria = isset($_GET["categoria"]) ? limpiarCadena($_GET["categoria"]) : "";
		$list = $inventario->buscar_producto($nombre, $codigo, $categoria);
		echo json_encode($list);
		break;

	case 'guardar_registros':
		$idinventario = isset($_POST["idinventario"]) ? limpiarCadena($_POST["idinventario"]) : "";
		$idproducto = isset($_POST["idproducto"]) ? array_map('limpiarCadena', $_POST["idproducto"]) : [];
		$cantidad = isset($_POST["cantidad"]) ? array_map('limpiarCadena', $_POST["cantidad"]) : [];
		$rpta = $inventario->guardar_registros($idinventario, $idproducto, $cantidad);
		echo json_encode($rpta);
		break;

	case 'listar_inventarios':
	    $rpta = $inventario->listarInventarios();
	    echo '<option value="" selected>Seleccionar...</option>';
	    while ($reg = $rpta->fetch_object()) {
	        if ($reg->pendientes > 0) {
	            $estado = ' (<span style="color:red;">Por ajustar</span>)';
	        } else {
	            $estado = ' (<span style="color:green;">Ajustado</span>)';
	        }
	        echo '<option value="' . $reg->id . '">' . $reg->observacion_apertura . $estado . '</option>';
	    }
	break;


	case 'buscar_productos_inventario':
		$idsucursal = isset($_GET["idsucursal"]) ? limpiarCadena($_GET["idsucursal"]) : "";
		$idinventario = isset($_GET["idinventario"]) ? limpiarCadena($_GET["idinventario"]) : "";
		$idcategoria = isset($_GET["idcategoria"]) ? limpiarCadena($_GET["idcategoria"]) : "";
		$tipo_ajuste = isset($_GET["tipo_ajuste"]) ? limpiarCadena($_GET["tipo_ajuste"]) : "";
		$list = $inventario->buscarProductosInventario($idsucursal, $idinventario, $idcategoria, $tipo_ajuste);
		echo json_encode($list);
	break;

	case 'listar_tipos_ajuste':
	    $tipo = isset($_GET["tipo"]) ? limpiarCadena($_GET["tipo"]) : "";

	    $rpta = $inventario->listarTiposAjuste($tipo);

	    echo '<option value="" selected>Seleccionar...</option>';
	    while ($reg = $rpta->fetch_object()) {
	        echo '<option value="' . $reg->id . '">' . $reg->nombre . '</option>';
	    }
	break;


	case 'cerrar_inventario':
		$idinventario_cierre = isset($_POST["idinventario_cierre"]) ? limpiarCadena($_POST["idinventario_cierre"]) : "";
		$observacion_cierre = isset($_POST["observacion_cierre"]) ? limpiarCadena($_POST["observacion_cierre"]) : "";
		$rspta = $inventario->cerrarInventario($idinventario_cierre, $observacion_cierre);
		echo $rspta ? "Cierre de inventario realizado correctamente" : "Cierre de inventario no se pudo actualizar";
	break;


	case 'ajustar_inventario':
	    try {
	        $usuario_id = $_SESSION['idusuario'];
	        $idinventario = $_POST["idinventario"];
	        $idsucursal = $_POST["idsucursal"];
	        $idtipo_ajuste = $_POST["idtipo_ajuste"];
	        $fecha_ajuste = $_POST["fecha_ajuste"];
	        $idconcepto = $_POST["idconcepto"];
	        $observacion_ajuste = $_POST["observacion_ajuste"];
	        $productos = isset($_POST["productos"]) ? $_POST["productos"] : []; // ✅ recibe seleccionados

	        $rpta = $inventario->ajustarInventario($idinventario, $idsucursal, $idtipo_ajuste, $fecha_ajuste, $idconcepto, $observacion_ajuste, $usuario_id, $productos);
	        
	        echo $rpta ? "Ajuste agregado correctamente" : "Ajuste no se pudo guardar";
	    } catch (Exception $e) {
	        echo $e->getMessage();
	    }
	break;

	case 'ver':
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

    $sql = "SELECT * FROM inventarios WHERE id = $id";
    $inv = ejecutarConsultaSimpleFila($sql);

    if ($inv) {
        // Traer productos de ese inventario
        $sqlp = "SELECT ip.*, p.nombre as producto, um.nombre as unidad_medida 
                 FROM inventario_productos ip 
                 JOIN producto p ON p.idproducto = ip.producto_id
                 JOIN unidad_medida um ON um.idunidad_medida = p.idunidad_medida
                 WHERE ip.inventario_id = $id";
        $productos = [];
        $rsp = ejecutarConsulta($sqlp);
        while ($reg = $rsp->fetch_object()) {
            $productos[] = $reg;
        }

        $inv["productos"] = $productos;
        echo json_encode($inv);
    } else {
        echo json_encode(null);
    }
    break;

    case 'resumen_inventario':
	    $idsucursal = $_GET["idsucursal"] ?? "";
	    $idinventario = $_GET["idinventario"] ?? "";

	    $rpta = $inventario->resumenInventario($idsucursal, $idinventario);
	    echo json_encode($rpta);
	break;

	case 'agregar_temporal':
	    $idinventario = isset($_POST['idinventario']) ? limpiarCadena($_POST['idinventario']) : '';
	    $idproducto = isset($_POST['idproducto']) ? limpiarCadena($_POST['idproducto']) : '';
	    $cantidad = isset($_POST['cantidad']) ? limpiarCadena($_POST['cantidad']) : 0;
	    
	    $rpta = $inventario->agregarTemporal($idinventario, $idproducto, $cantidad);
	    echo json_encode($rpta);
	break;

	case 'listar_temporales':
	    $idinventario = isset($_GET['idinventario']) ? limpiarCadena($_GET['idinventario']) : '';
	    $list = $inventario->listarTemporales($idinventario);
	    echo json_encode($list);
	break;

	case 'eliminar_temporal':
	    $idinventario = isset($_POST["idinventario"]) ? limpiarCadena($_POST["idinventario"]) : "";
	    $idproducto = isset($_POST["idproducto"]) ? limpiarCadena($_POST["idproducto"]) : "";

	    if($idinventario != "" && $idproducto != "") {
	        $rpta = $inventario->eliminar_temporal($idinventario, $idproducto);
	        echo json_encode($rpta);
	    } else {
	        echo json_encode([
	            'status' => false,
	            'message' => 'Faltan datos necesarios'
	        ]);
	    }
    break;



}
