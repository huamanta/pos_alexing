<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/FluentQuery.php';
require_once __DIR__ . '/../core/FluentSave.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

require_once __DIR__ . '/env.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function limpiarCadena($str)
{
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| Configuración de errores
|--------------------------------------------------------------------------
*/
error_reporting(E_ALL);

$debug = ($_ENV['APP_DEBUG'] ?? 'true') === 'true';

ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');

ini_set('log_errors', '1');

$logDir = dirname(__DIR__) . '/logs';

if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

$logFile = $logDir . '/app.log';

if (!file_exists($logFile)) {
    touch($logFile);
}

ini_set('error_log', $logFile);