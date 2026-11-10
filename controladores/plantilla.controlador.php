<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
class ControladorPlantilla{

	static public function plantilla(){
		$rutaActual = $_GET['ruta'] ?? null;

		if ($rutaActual === 'salir') {
			include __DIR__ . '/../vistas/modulos/salir.php';
			exit;
		}

		if ($rutaActual === 'salirsucursal') {
			include __DIR__ . '/../vistas/modulos/salirsucursal.php';
			exit;
		}

		include __DIR__ . '/../vistas/plantilla.php';
	}
}