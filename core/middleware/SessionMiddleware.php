<?php
require_once __DIR__ . "/../Constants.php";

class SessionMiddleware
{
    public static function verificarSesion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (PHP_SAPI === 'cli' || self::esRutaPublica()) {
            return;
        }


        $sesionValida =
            isset(
                $_SESSION['idusuario'],
                $_SESSION['idpersonal'],
                $_SESSION['login'],
                $_SESSION['iniciarSesion']
            ) &&
            $_SESSION['idusuario'] !== '' &&
            $_SESSION['idpersonal'] !== '' &&
            $_SESSION['login'] !== '' &&
            $_SESSION['iniciarSesion'] !== '';

        if (!$sesionValida) {
            session_unset();
            session_destroy();

            $esAjax = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest'
                || strcasecmp((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json') === 0
                || stripos((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json') !== false;

            if ($esAjax) {
                require_once __DIR__ . '/../Response.php';
                Response::json([
                    'status' => 0,
                    'message' => 'Sesión expirada. Inicie sesión nuevamente para continuar.'
                ], 401);
            }

            header('Location: login');
            exit;
        }
    }

    private static function esRutaPublica(): bool
    {
        $rutasPublicas = ['login', 'recuperar', 'reset'];
        $ruta = (string) ($_GET['ruta'] ?? '');

        if (in_array($ruta, $rutasPublicas, true)) {
            return true;
        }

        $controlador = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
        $operacion = $_GET['op'] ?? '';
        
        $publicos = Constants::PUBLICOS;

        if (!array_key_exists($controlador, $publicos)) {
            return false;
        }

        $publico = $publicos[$controlador];

        return is_array($publico)
            ? in_array($operacion, $publico, true)
            : true;
    }
}
