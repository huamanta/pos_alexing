<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . "/../modelos/OrdenTrabajo.php";
$ordentrabajo = new OrdenTrabajo();
$idsucursal = $_SESSION['idsucursal'];
$idusuario = $_SESSION['idusuario'];
$op = $_GET['op'];

switch ($op) {
    case 'selectPersonal':
        $ordentrabajo->selectPersonal($idsucursal);
        break;

    case 'listar':
        $fecha_inicio = $_GET['fecha_inicio'] ?? '';
        $fecha_fin = $_GET['fecha_fin'] ?? '';
        $ordentrabajo->listarOrdenesTrabajo($idsucursal, $fecha_inicio, $fecha_fin);
        break;
    
    case 'guardarOrdenTrabajo':
        $costosObservaciones = $_POST['costosObservaciones'] ?? '';
        $documentoRelacionado = $_POST['documentoRelacionado'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $fecha = $_POST['fecha'] ?? '';
        $fechaCompromiso = $_POST['fechaCompromiso'] ?? '';
        $lavado = $_POST['lavado'] ?? 0;
        $otrosGastos = $_POST['otrosGastos'] ?? 0;
        $pintura = $_POST['pintura'] ?? 0;
        $prioridad = $_POST['prioridad'] ?? '';
        $referencia = $_POST['referencia'] ?? '';
        $serviciosExternos = $_POST['serviciosExternos'] ?? 0;
        $tipoOrden = $_POST['tipoOrden'] ?? '';
        $transporte = $_POST['transporte'] ?? 0;
        $vehiculoId = $_POST['vehiculoBuscar'] ?? '';
        $mecanicos = $_POST['mechanics'] ?? [];
        $repuestos = $_POST['parts'] ?? [];
        
        $ordentrabajo->guardarOrdenTrabajo($idusuario, $idsucursal, $vehiculoId, $costosObservaciones, $documentoRelacionado, $estado, $fecha, $fechaCompromiso, 
        $lavado, $otrosGastos, $pintura, $prioridad, $referencia, $serviciosExternos, $tipoOrden, $transporte, $mecanicos, $repuestos);
        break;
}