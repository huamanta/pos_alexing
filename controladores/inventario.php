<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once "../modelos/Inventario.php";

$inventario = new Inventario();

$idinventario_edit = isset($_POST["idinventario_edit"]) ? limpiarCadena($_POST["idinventario_edit"]) : "";
$fecha_apertura = isset($_POST["fecha_apertura"]) ? limpiarCadena($_POST["fecha_apertura"]) : "";
$observacion_apertura = isset($_POST["observacion_apertura"]) ? limpiarCadena($_POST["observacion_apertura"]) : "";
$fecha_cierre = isset($_POST["fecha_cierre"]) ? limpiarCadena($_POST["fecha_cierre"]) : "";
$observacion_cierre = isset($_POST["observacion_cierre"]) ? limpiarCadena($_POST["observacion_cierre"]) : "";

switch ($_GET["op"]) {

	case 'listar':
		$inventario->listar();
		break;

	case 'guardaryeditar':
		$idusuario = $_SESSION['idusuario'];
		$idsucursal = $_SESSION['idsucursal'];
		if ($idinventario_edit) {
			$inventario->editar($idinventario_edit, $observacion_apertura);
		} else {
			$inventario->guardar($observacion_apertura, $idsucursal, $idusuario);
		}
		break;

	case 'buscar_producto':
		$nombre = isset($_GET["nombre"]) ? limpiarCadena($_GET["nombre"]) : "";
		$codigo = isset($_GET["codigo"]) ? limpiarCadena($_GET["codigo"]) : "";
		$categoria = isset($_GET["categoria"]) ? limpiarCadena($_GET["categoria"]) : "";
		$inventario->buscar_producto($nombre, $codigo, $categoria);
		break;

	case 'guardar_registros':
		$idinventario = isset($_POST["idinventario"]) ? limpiarCadena($_POST["idinventario"]) : "";
		$idproducto = isset($_POST["idproducto"]) ? array_map('limpiarCadena', $_POST["idproducto"]) : [];
		$cantidad = isset($_POST["cantidad"]) ? array_map('limpiarCadena', $_POST["cantidad"]) : [];
		$inventario->guardarRegistros($idinventario, $idproducto, $cantidad);
		break;

	case 'listar_inventarios':
		echo $inventario->listarInventarios();
		break;


	case 'buscar_productos_inventario':
		$idsucursal = isset($_GET["idsucursal"]) ? limpiarCadena($_GET["idsucursal"]) : "";
		$idinventario = isset($_GET["idinventario"]) ? limpiarCadena($_GET["idinventario"]) : "";
		$idcategoria = isset($_GET["idcategoria"]) ? limpiarCadena($_GET["idcategoria"]) : "";
		$tipo_ajuste = isset($_GET["tipo_ajuste"]) ? limpiarCadena($_GET["tipo_ajuste"]) : "";
		$inventario->buscarProductosInventario($idsucursal, $idinventario, $idcategoria, $tipo_ajuste);
		break;

	case 'listar_tipos_ajuste':
		$tipo = isset($_GET["tipo"]) ? limpiarCadena($_GET["tipo"]) : "";
		echo $inventario->listarTiposAjuste($tipo);
		break;


	case 'cerrar_inventario':
		$idinventario_cierre = isset($_POST["idinventario_cierre"]) ? limpiarCadena($_POST["idinventario_cierre"]) : "";
		$observacion_cierre = isset($_POST["observacion_cierre"]) ? limpiarCadena($_POST["observacion_cierre"]) : "";
		$inventario->cerrarInventario($idinventario_cierre, $observacion_cierre);
		break;


	case 'ajustar_inventario':
		$usuario_id = $_SESSION['idusuario'];
		$idinventario = $_POST["idinventario"];
		$idsucursal = $_POST["idsucursal"];
		$idtipo_ajuste = $_POST["idtipo_ajuste"];
		$fecha_ajuste = $_POST["fecha_ajuste"];
		$idconcepto = $_POST["idconcepto"];
		$observacion_ajuste = $_POST["observacion_ajuste"];
		$productos = isset($_POST["productos"]) ? $_POST["productos"] : []; // ✅ recibe seleccionados

		$inventario->ajustarInventario($idinventario, $idsucursal, $idtipo_ajuste, $fecha_ajuste, $idconcepto, $observacion_ajuste, $usuario_id, $productos);
		break;

	case 'ver':
		$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
		$inventario->verInventario($id);
		break;

	case 'resumen_inventario':
		$idsucursal = $_GET["idsucursal"] ?? "";
		$idinventario = $_GET["idinventario"] ?? "";
		$inventario->resumenInventario($idsucursal, $idinventario);
		break;

	case 'listar_temporales':
		$idinventario = isset($_GET['idinventario']) ? limpiarCadena($_GET['idinventario']) : '';
		$inventario->listarTemporales($idinventario);
		break;

	case 'eliminar_temporal':
		$idinventario = isset($_POST["idinventario"]) ? limpiarCadena($_POST["idinventario"]) : "";
		$idproducto = isset($_POST["idproducto"]) ? limpiarCadena($_POST["idproducto"]) : "";
		$inventario->eliminar_temporal($idinventario, $idproducto);
		break;
}
