<?php
require_once __DIR__ . '/configuraciones/bootstrap.php';

require_once "controladores/plantilla.controlador.php";

$plantilla = new ControladorPlantilla();
$plantilla->plantilla();