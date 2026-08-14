<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once "../modelos/Cajachica.php";

date_default_timezone_set('America/Lima');
// Iniciar la sesión solo si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$cajachica = new Cajachica();

$idmovimiento = isset($_POST["idmovimiento"]) ? limpiarCadena($_POST["idmovimiento"]) : "";

$opcionEI = isset($_POST["opcionEI"]) ? limpiarCadena($_POST["opcionEI"]) : "";
$idsucursal = isset($_POST["idsucursal"]) ? limpiarCadena($_POST["idsucursal"]) : "";
$idpersonal = isset($_POST["idpersonal"]) ? limpiarCadena($_POST["idpersonal"]) : "";
$idpersonal2 = isset($_POST["idpersonal2"]) ? limpiarCadena($_POST["idpersonal2"]) : "";
$montoPagar = isset($_POST["montoPagar"]) ? limpiarCadena($_POST["montoPagar"]) : "";
$formapago = isset($_POST["formapago"]) ? limpiarCadena($_POST["formapago"]) : "";
$totaldeposito = isset($_POST["totaldeposito"]) ? limpiarCadena($_POST["totaldeposito"]) : "";
$noperacion = isset($_POST["noperacion"]) ? limpiarCadena($_POST["noperacion"]) : "";
$banco = isset($_POST["banco"]) ? limpiarCadena($_POST["banco"]) : "";
$fechaDeposito = isset($_POST["fechaDeposito"]) ? limpiarCadena($_POST["fechaDeposito"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
$idconcepto_movimiento = isset($_POST["idconcepto_movimiento"]) ? limpiarCadena($_POST["idconcepto_movimiento"]) : "";
$idasistencia = isset($_POST["idasistenciaEI"]) ? limpiarCadena($_POST["idasistenciaEI"]) : "";
$idsucursal2 = isset($_POST["idsucursal2"]) ? limpiarCadena($_POST["idsucursal2"]) : "";

switch ($_GET["op"]) {

	case "resumenBancos":
		$idsucursal = $_SESSION['idsucursal'];
		$idusuario = $_SESSION['idusuario'];
		$cajachica->resumenBancos($idsucursal, $idusuario);
		break;


	case "resumenComprobantes":
		$idsucursal = $_SESSION['idsucursal'];
		$idusuario = $_SESSION['idusuario'];
		$cajachica->resumenComprobantes($idsucursal, $idusuario);
		break;

	case 'guardaryeditar':
		$idusuario = $_SESSION['idusuario'];
		$idsucursal = $_SESSION['idsucursal'];
		if (empty($idmovimiento)) {
			$cajachica->insertar($opcionEI, $idsucursal, $idpersonal, $montoPagar, $descripcion, $formapago, $totaldeposito, $noperacion, $idconcepto_movimiento, $idusuario, $banco, $fechaDeposito);
		} else {
			$cajachica->editar($idmovimiento, $opcionEI, $idcaja, $idsucursal, $idpersonal, $montoPagar, $descripcion, $formapago, $totaldeposito, $noperacion, $idconcepto_movimiento, $idusuario,);
		}

		break;

	case 'mostrar':
		$rspta = $cajachica->mostrar($idmovimiento);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);
		break;
		break;

	case 'eliminar':
		$cajachica->eliminar($idmovimiento);
		break;

	case 'listar':
		$fecha_inicio = $_GET["fecha_inicio"] ?? '';
		$fecha_fin = $_GET["fecha_fin"] ?? '';
		$idsucursal = $_SESSION["idsucursal"];
		$cajachica->listar($fecha_inicio, $fecha_fin, $idsucursal);
		break;

	case 'coceptoMovimiento':
		$tipo = isset($_GET['tipo']) ? limpiarCadena($_GET['tipo']) : '';
		$rspta = $cajachica->coceptoMovimiento($tipo);

		echo '<option value="" selected>Seleccione...</option>';

		foreach ($rspta as $reg) {
			echo '<option value="'.$reg['idconcepto_movimiento'].'">'.$reg['descripcion'].'</option>';
		}
		break;
	case 'guardaryeditarConcepto':
		$idconcepto_movimiento = isset($_POST["idconcepto_movimiento"]) ? limpiarCadena($_POST["idconcepto_movimiento"]) : "";
		$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
		$tipo = isset($_POST["tipo"]) ? limpiarCadena($_POST["tipo"]) : "";
		$categoria_concepto = isset($_POST["categoria_concepto"]) ? limpiarCadena($_POST["categoria_concepto"]) : "";
		if (empty($idconcepto_movimiento)) {
			$cajachica->insertarConcepto($descripcion, $tipo, $categoria_concepto);
		} else {
			$cajachica->editarConcepto($idconcepto_movimiento, $descripcion, $tipo, $categoria_concepto);
		}
		break;

	case 'listarConceptos':
		$cajachica->listarConceptos();
		break;

	case 'guardarPagoDiario':
		if (!$idcaja) {
			echo json_encode(array(
				"tipo" => "error",
				"mensaje" => "No se ha abierto ninguna caja."
			));
			return;
		}

		$rspta = $cajachica->guardarPagoDiario($opcionEI, $idcaja, $idsucursal2, $idpersonal2, $montoPagar, $descripcion, $formapago, $totaldeposito, $noperacion, $idconcepto_movimiento, $idasistencia);

		if ($rspta) {
			echo json_encode(array(
				"tipo" => "success",
				"mensaje" => "Pago diario registrado correctamente."
			));
		} else {
			echo json_encode(array(
				"tipo" => "error",
				"mensaje" => "No se pudo registrar el pago diario."
			));
		}
		break;

		case 'listarAdelantos':
		    $idpersonal = $_GET['idpersonal'];
		    $desde = $_GET['desde'];
		    $hasta = $_GET['hasta'];

		    $rspta = $cajachica->listarAdelantos($idpersonal, $desde, $hasta);

		    $total = 0;
		    $data = [];

		    while ($reg = $rspta->fetch_object()) {
		        $data[] = $reg;
		        $total += $reg->monto;
		    }

		    echo json_encode([
		        "total"   => $total,
		        "detalle" => $data
		    ]);
		break;

		case 'getIdConceptoAdelanto':
		    $id = $cajachica->obtenerIdConceptoAdelanto();
		    echo json_encode($id);
		break;

		case 'listarIngresosSemana':
		    $idpersonal = $_GET["idpersonal"];
		    $desde = $_GET["desde"];
		    $hasta = $_GET["hasta"];

		    $rspta = $cajachica->listarIngresosSemana($idpersonal, $desde, $hasta);

		    $total = 0;
		    $detalle = [];

		    while ($reg = $rspta->fetch_object()) {
		        $detalle[] = $reg;
		        $total += floatval($reg->monto);
		    }

		    echo json_encode([
		        "total" => $total,
		        "detalle" => $detalle
		    ]);
		break;

		case 'getMovimiento':
    
		    $idmovimiento = $_GET["idmovimiento"];

		    $sql = "SELECT m.*, p.nombre AS trabajador
		            FROM movimiento m
		            LEFT JOIN personal p ON p.idpersonal = m.idpersonal
		            WHERE m.idmovimiento = '$idmovimiento'";

		    $rspta = ejecutarConsultaSimpleFila($sql);

		    echo json_encode($rspta);

		break;

		case 'reporteAdelantos':
		    $desde = $_GET['fecha_inicio'] ?? '';
		    $hasta = $_GET['fecha_fin'] ?? '';
		    $cajachica->reporteAdelantos($desde, $hasta);
		    // $detalle = [];
		    // $total = 0;

		    // while ($reg = $rspta->fetch_object()) {
		    //     $detalle[] = [
		    //         'fecha' => $reg->fecha,
		    //         'trabajador' => $reg->trabajador,
		    //         'descripcion' => $reg->descripcion,
		    //         'monto' => floatval($reg->monto)
		    //     ];
		    //     $total += floatval($reg->monto);
		    // }


		    // /* ============================
		    //    2. LISTAR DÍAS TRABAJADOS
		    // ============================= */
		    // $rsptaDias = $cajachica->listarDiasTrabajadosPorFechas($desde, $hasta);

		    // $dias_tmp = [];

		    // while ($reg = $rsptaDias->fetch_object()) {

			//     $trabajador = (string)$reg->trabajador;
			//     $fecha = $reg->fecha;
			//     $monto_dia  = floatval($reg->monto_dia);

			//     if (!isset($dias_tmp[$trabajador])) {
			//         $dias_tmp[$trabajador] = [
			//             'trabajador' => $trabajador,
			//             'dias' => 0,
			//             'monto_dia' => $monto_dia,
			//             'total_pago' => 0,
			//             'fechas' => []
			//         ];
			//     }

			//     // Contar días trabajados
			//     $dias_tmp[$trabajador]['dias'] += 1;

			//     // Sumar total pagado
			//     $dias_tmp[$trabajador]['total_pago'] += $monto_dia;

			//     // Mantener el monto por día real (NO promedio)
			//     $dias_tmp[$trabajador]['monto_dia'] = $monto_dia;

			//     // Guardar fechas trabajadas
			//     $dias_tmp[$trabajador]['fechas'][] = [
			// 	    "fecha" => $reg->fecha,
			// 	    "monto" => $monto_dia
			// 	];
			// }

		    // // Convertir a array final
		    // $dias = array_values($dias_tmp);

		    // echo json_encode([
		    //     "detalle" => $detalle,
		    //     "total" => $total,
		    //     "dias" => $dias
		    // ]);

		break;


}
