<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once "../modelos/Contratos.php";
$contratos = new Contratos();
$op = $_GET['op'] ?? '';
session_start();

switch ($op) {
    case 'listar':
        $fecha_inicio = $_GET['fecha_inicio'] ?? '';
        $fecha_fin = $_GET['fecha_fin'] ?? '';
        $idsucursal = $_GET['idsucursal'] ?? '';
        $estado = $_GET['estado'] ?? '';
        $condicion = $_GET['condicion'] ?? '';
        $frecuencia = $_GET['frecuencia'] ?? '';
        $datos = $contratos->listar($fecha_inicio, $fecha_fin, $idsucursal, $estado, $condicion, $frecuencia);
        echo $datos;
        break;

    case 'retener':
        $idventa = $_POST['idventa'] ?? '';
        $motivo = $_POST['motivo'] ?? '';
        $resultado = $contratos->retenerContrato($idventa, $motivo);
        echo json_encode($resultado);
        break;

    case 'quitar_retencion':
        $idventa = $_POST['idventa'] ?? '';
        $idretencion = $_POST['idretencion'] ?? '';
        $resultado = $contratos->quitarRetencion($idventa, $idretencion);
        echo json_encode($resultado);
        break;

    case 'selectUsuarios':
        $idventa = $_POST['idventa'] ?? '';
        $idsucursal = $_POST['idsucursal'] ?? $_SESSION['idsucursal'];
        $idvendedor = $_SESSION['idusuario'];
        $res = $contratos->selectUsuarios($idventa, $idsucursal);
        $usuarios = $res['data'];
        echo '<option value="">Seleccione tipo de acompañante</option>';
		if ($usuarios) {
			while ($reg = $usuarios->fetch_object()) {
                $selected = ($reg->idusuario == $res['idvendedor']) ? 'selected' : '';
                echo '<option value="' . $reg->idusuario . '" ' . $selected . '>' . $reg->nombre . '</option>';
			}
		} else {
			echo null;
		}
        break;

    case 'anularContrato':
        $idventa = $_POST['idventa'] ?? '';
        $res = $contratos->anularContrato($idventa);
        echo json_encode($res);
        break;
}