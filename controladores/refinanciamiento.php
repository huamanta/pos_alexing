<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once "../modelos/Refinanciamiento.php";
session_start();

$refinanciamiento = new Refinanciamiento();

switch ($_GET["op"]) {
    case 'buscarCredito':
        $idsucursal = $_SESSION['idsucursal'];
        $buscar = isset($_GET["buscar"]) ? limpiarCadena($_GET["buscar"]) : "";
        $response = $refinanciamiento->buscarCreditos($idsucursal, $buscar);
        echo json_encode($response);
        break;

    case 'detalleCredito':
        $idventa = isset($_GET["idventa"]) ? limpiarCadena($_GET["idventa"]) : "";
        $response = $refinanciamiento->detalleCredito($idventa);
        echo $response;
        break;

    case 'guardarRefinanciamiento':
        $idventa = isset($_POST["idventa"]) ? limpiarCadena($_POST["idventa"]) : "";
        $interes = isset($_POST["interes"]) ? limpiarCadena($_POST["interes"]) : "";
        $inicial = isset($_POST["inicial"]) ? limpiarCadena($_POST["inicial"]) : "";
        $frecuencia = isset($_POST["frecuencia"]) ? limpiarCadena($_POST["frecuencia"]) : "";
        $cuotas = isset($_POST["cuotas"]) ? limpiarCadena($_POST["cuotas"]) : "";
        $fecha = isset($_POST["fecha"]) ? limpiarCadena($_POST["fecha"]) : "";
        $idusuario = $_SESSION['idusuario'];
        $response = $refinanciamiento->guardarRefinanciamiento($idventa, $interes, $inicial, $frecuencia, $cuotas, $fecha, $idusuario);
        echo $response;
        break;

    case 'historialCreditoRefinanciamiento':
        $idventa = isset($_GET["idventa"]) ? limpiarCadena($_GET["idventa"]) : "";
        $response = $refinanciamiento->historialCreditoRefinanciamiento($idventa);
        echo $response;
        break;
}