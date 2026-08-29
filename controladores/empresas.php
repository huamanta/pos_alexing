<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once "../modelos/Empresas.php";
$empresa = new Empresa();
$op = isset($_GET["op"]) ? $_GET["op"] : '';

//Obtenemos las variables del formulario
$ruc = isset($_POST["ruc"]) ? limpiarCadena($_POST["ruc"]) : "";
$razon_social = isset($_POST["razon_social"]) ? limpiarCadena($_POST["razon_social"]) : "";
$usuario_sol = isset($_POST["usuario_sol"]) ? limpiarCadena($_POST["usuario_sol"]) : "";
$clave_sol = isset($_POST["clave_sol"]) ? limpiarCadena($_POST["clave_sol"]) : "";
$ruta_certificado = isset($_POST["ruta_certificado"]) ? limpiarCadena($_POST["ruta_certificado"]) : "";
$clave_certificado = isset($_POST["clave_certificado"]) ? limpiarCadena($_POST["clave_certificado"]) : "";
$client_id = isset($_POST["client_id"]) ? limpiarCadena($_POST["client_id"]) : "";
$client_secret = isset($_POST["client_secret"]) ? limpiarCadena($_POST["client_secret"]) : "";
$estado_certificado = isset($_POST["estado_certificado"]) ? limpiarCadena($_POST["estado_certificado"]) : "";
$nombre_impuesto = isset($_POST["nombre_impuesto"]) ? limpiarCadena($_POST["nombre_impuesto"]) : "";
$monto_impuesto = isset($_POST["monto_impuesto"]) ? limpiarCadena($_POST["monto_impuesto"]) : "";
$idempresa = isset($_POST["idempresa"]) ? limpiarCadena($_POST["idempresa"]) : "";
$estado = isset($_POST["estado"]) ? limpiarCadena($_POST["estado"]) : "";
$nombreSucursal = isset($_POST["nombreSucursal"]) ? $_POST["nombreSucursal"] : [];
$serie = isset($_POST["serie"]) ? $_POST["serie"] : [];
$numero = isset($_POST["numero"]) ? $_POST["numero"] : [];

switch ($op) {
    case 'listarEmpresas':
        $response = $empresa->listarEmpresas();
        //Vamos a declarar un array
        $data = [];
        foreach ($response as $reg) {
            $data[] = [
                "0"=>$reg['ruc'],
                "1"=>$reg['razon_social'],
                "2"=>$reg['usuario_sol'],
                "3"=>$reg['estado_certificado'],
                "4"=>$reg['nombre_impuesto'],
                "5"=>$reg['monto_impuesto'],
                "6"=>($reg['estado'])?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg['idempresa'].')"><i class="fas fa-edit"></i></button>'.
                    ' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg['idempresa'].')"><i class="fas fa-times-circle"></i></button>':
                    '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg['idempresa'].')"><i class="fas fa-edit"></i></button>'.
                    ' <button class="btn btn-primary btn-xs" onclick="activar('.$reg['idempresa'].')"><i class="fa fa-check"></i></button>'
            ];
        }
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);
        break;

    case 'guardaryeditar':
        $idsucursal = $_SESSION['idsucursal'];
        $empresa->guardaryeditar($idsucursal, $idempresa, $ruc, $razon_social, $usuario_sol, $clave_sol, $_FILES["ruta_certificado"], $clave_certificado, $client_id, $client_secret, $estado_certificado, $nombre_impuesto, $monto_impuesto, $nombreSucursal, $serie, $numero);
        break;

    case 'mostrarEmpresa':
        $empresa->mostrarEmpresa($idempresa);
        break;

    case 'activar_descativar':
        $empresa->activarDesactivar($idempresa, $estado);
        break;

    case 'mostrarComprobantes':
        $empresa->mostrarComprobantesEmpresa($idempresa);
        break;

    // case 'guardarComprobantes':
    //     require_once "../modelos/Categoria.php";
    //     $categoria = new Categoria();
    //     $nombreSucursal = isset($_POST["nombreSucursal"]) ? $_POST["nombreSucursal"] : [];
    //     $serie = isset($_POST["serie"]) ? $_POST["serie"] : [];
    //     $numero = isset($_POST["numero"]) ? $_POST["numero"] : [];
        
    //     $rspta = $categoria->actualizarComprobantesEmpresa($idempresa, $nombreSucursal, $serie, $numero);
    //     echo $rspta ? "Comprobantes actualizados" : "Error al actualizar comprobantes";
    //     break;
}
