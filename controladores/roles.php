<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . "/../modelos/Roles.php";
$roles = new Roles();
$op = $_GET['op'] ?? null;
$idrol = isset($_POST["idrol"]) ? limpiarCadena($_POST["idrol"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";

switch ($op) {
    case 'listar':
        $roles->listar();
        break;

    case 'listarModulos':
        $roles->listarModulos();
        break;

    case 'mostrar':
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $roles->obtener($id);
        break;

    case 'guardar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            throw new RuntimeException('Método no permitido.');
        }

        $id = isset($_POST['idrol']) && $_POST['idrol'] !== ''
            ? filter_var($_POST['idrol'], FILTER_VALIDATE_INT)
            : null;

        if (isset($_POST['idrol']) && $_POST['idrol'] !== '' && !$id) {
            throw new InvalidArgumentException('ID de rol inválido.');
        }

        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $permisos = $_POST['permisos'] ?? [];

        $roles->guardar($nombre, $descripcion, $permisos, $id);

        break;

    case 'eliminar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            throw new RuntimeException('Método no permitido.');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        $roles->eliminar($id);

        break;


    default:
        echo "No existe la opcion";
        break;
}