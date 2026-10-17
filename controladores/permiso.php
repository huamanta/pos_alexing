<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../modelos/Permiso.php';

$permiso = new Permiso();
$operacion = $_GET['op'] ?? '';

try {
    switch ($operacion) {
        case 'listarNuevos':
            Response::json([
                'data' => $permiso->listarPermisosNuevos(),
            ]);
            break;

        case 'listarModulosNuevos':
            Response::json($permiso->listarModulosNuevos());
            break;

        case 'mostrarNuevo':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                Response::error('ID de permiso inválido.');
            }
            Response::json($permiso->obtenerPermisoNuevo($id));
            break;

        case 'guardarNuevo':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Response::error('Método no permitido.', 405);
            }

            $id = isset($_POST['id']) && $_POST['id'] !== ''
                ? filter_var($_POST['id'], FILTER_VALIDATE_INT)
                : null;
            if (isset($_POST['id']) && $_POST['id'] !== '' && !$id) {
                Response::error('ID de permiso inválido.');
            }

            $moduloId = filter_var($_POST['modulo_id'] ?? null, FILTER_VALIDATE_INT);
            if (!$moduloId) {
                Response::error('Seleccione un módulo válido.');
            }

            Response::json($permiso->guardarPermisoNuevo(
                $_POST['nombre'] ?? '',
                $_POST['codigo'] ?? '',
                $moduloId,
                $id
            ));
            break;

        case 'eliminarNuevo':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Response::error('Método no permitido.', 405);
            }

            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            if (!$id) {
                Response::error('ID de permiso inválido.');
            }

            $permiso->eliminarPermisoNuevo($id);
            Response::json([
                'success' => true,
                'message' => 'Permiso eliminado correctamente.',
            ]);
            break;

        default:
            Response::error('Operación de permisos no válida.', 404);
    }
} catch (\Throwable $th) {
    Response::error(
        $th->getMessage(),
        $th instanceof InvalidArgumentException ? 400 : 500
    );
}
