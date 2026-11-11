<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../modelos/Conciliaciones.php';

$conciliaciones = new Conciliaciones();
$op = $_GET['op'] ?? '';
$idconciliacion = isset($_GET['idconciliacion']) ? (int) $_GET['idconciliacion'] : 0;

switch ($op) {
    case 'listar':
        $conciliaciones->listar();
        break;

    case 'listarBancos':
        $conciliaciones->listarBancos();
        break;

    case 'crear':
        $data = json_decode(file_get_contents('php://input'), true);
        $conciliaciones->crear(is_array($data) ? $data : []);
        break;

    case 'mostrar':
        $conciliaciones->mostrar($idconciliacion);
        break;

    case 'importar':
        $mapping = json_decode((string) ($_POST['mapping'] ?? '{}'), true);
        $conciliaciones->importar($idconciliacion, is_array($mapping) ? $mapping : []);
        break;

    case 'conciliarManual':
        $data = json_decode(file_get_contents('php://input'), true);
        $conciliaciones->conciliarManual($idconciliacion, is_array($data) ? $data : []);
        break;

    case 'conciliarAutomatico':
        $conciliaciones->conciliarAutomatico($idconciliacion);
        break;

    case 'crearMovimientoBancario':
        $data = json_decode(file_get_contents('php://input'), true);
        $conciliaciones->crearMovimientoBancario($idconciliacion, is_array($data) ? $data : []);
        break;

    case 'cerrar':
        $data = json_decode(file_get_contents('php://input'), true);
        $conciliaciones->cerrar($idconciliacion, is_array($data) ? $data : []);
        break;

    default:
        Response::error('Operacion no valida.', 400);
}
