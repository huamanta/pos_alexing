<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once "../modelos/Notificaciones.php";

$notificaciones = new Notificaciones();
$notificaciones->ejecutar();