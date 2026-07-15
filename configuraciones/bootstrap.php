<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

require_once __DIR__ . '/env.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}