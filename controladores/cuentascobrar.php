<?php 
require_once "../modelos/CuentasCobrar.php";
require_once "../modelos/Negocio.php";

$cuentascobrar=new CuentasCobrar();
$negocio = new Negocio();

// Obtener nombre del negocio
$infoNegocio   = $negocio->mostrarNombreNegocio();
$nombreNegocio = $infoNegocio ? $infoNegocio['nombre'] : 'Su negocio';

$idcpc=isset($_POST["idcpc"])? limpiarCadena($_POST["idcpc"]):"";
$idventa=isset($_POST["idventa"])? limpiarCadena($_POST["idventa"]):"";
$montopagado=isset($_POST["montoPagar"])? limpiarCadena($_POST["montoPagar"]):"";
$montoPagarTarjeta=isset($_POST["montoPagarTarjeta"])? limpiarCadena($_POST["montoPagarTarjeta"]):"";
$observacion=isset($_POST["observacion"])? limpiarCadena($_POST["observacion"]):"";

$banco=isset($_POST["banco"])? limpiarCadena($_POST["banco"]):"";
$op=isset($_POST["op"])? limpiarCadena($_POST["op"]):"";

$fechaPago=isset($_POST["fechaPago"])? limpiarCadena($_POST["fechaPago"]):"";
$formapago=isset($_POST["formapago"])? limpiarCadena($_POST["formapago"]):"";

$op = $_GET["op"] ?? '';

	switch ($_GET["op"]){

		case 'guardaryeditar':

		    $idcaja = isset($_POST['idcaja']) ? limpiarCadena($_POST['idcaja']) : 0;
		    $idpersonal = $_SESSION["idpersonal"];
		    // Validar que se pase un idcaja
		    if($idcaja == 0){
		        echo json_encode(['success'=>false,'message'=>'Debe seleccionar una caja abierta']);
		        exit;
		    }

		    // Registrar abono
		    $rspta=$cuentascobrar->insertar($idcpc,$montopagado,$observacion,$banco,$op,$fechaPago,$formapago,$montoPagarTarjeta,$idcaja, $idpersonal);

		    echo json_encode($rspta);

		break;



		case 'listar_saldos':
			$fecha_inicio=$_REQUEST["fecha_inicio"];
			$fecha_fin=$_REQUEST["fecha_fin"];
			$idcliente=$_REQUEST["idcliente"];
			$idsucursal=$_REQUEST["idsucursal"];
			if (empty($idsucursal) || $idsucursal == "null" || $idsucursal == "Todos") {
		        if (isset($_SESSION['idsucursal']) && $_SESSION['idsucursal'] != 0) {
		            // Si el usuario tiene una sucursal específica asignada
		            $idsucursal = $_SESSION['idsucursal'];
		        } else {
		            // Si el usuario es administrador y puede ver todas
		            $idsucursal = "Todos";
		        }
		    }
			$rspta = $cuentascobrar->listarSaldos($fecha_inicio, $fecha_fin, $idcliente, $idsucursal);
			echo json_encode($rspta);
		break;

		case 'listar':
		    $fecha_inicio = $_REQUEST["fecha_inicio"];
		    $fecha_fin    = $_REQUEST["fecha_fin"];
		    $idcliente    = $_REQUEST["idcliente"];
		    $idsucursal   = $_REQUEST["idsucursal"] ?? null;

		    if (empty($idsucursal) || $idsucursal == "null" || $idsucursal == "Todos") {
		        if (isset($_SESSION['idsucursal']) && $_SESSION['idsucursal'] != 0) {
		            $idsucursal = $_SESSION['idsucursal'];
		        } else {
		            $idsucursal = "Todos";
		        }
		    }

		    $rspta = $cuentascobrar->listar($fecha_inicio, $fecha_fin, $idcliente, $idsucursal);
		    $data = array();

		    while ($reg = $rspta->fetch_object()) {

		        // Obtener mora calculada por el modelo (ya hace validaciones: interés>0, dias enteros, redondeo)
		        $moraData = $cuentascobrar->calcularMora($reg->idcpc);
				if ($moraData['dias_retraso'] > 0 && $moraData['mora'] > 0) {
				    $cuentascobrar->actualizarMoraDiaria($reg->idcpc);
				}

		        // cuota_sin_mora: cuota con interés (según cálculo del modelo)
		        $cuotaConInteres = floatval($moraData['cuota_sin_mora']);
		        $moraTotal       = floatval($moraData['mora']);

		        // Deuda total = cuota con interés + mora (redondeado)
		        $deudaTotalConMora = round($cuotaConInteres + $moraTotal, 2);

		        // Saldo real = deudaTotalConMora - abonos
		        $saldo = round($reg->deuda + floatval($reg->mora), 2);

		        $url1 = 'reportes/exTicketCC.php?id=';

		        $btnRecordatorio = '<button class="btn btn-info btn-sm" onclick="enviarRecordatorioManual(' . $reg->idcpc . ')">
                        <i class="fas fa-paper-plane"></i>
                    </button>';

		        $data[] = array(
		            "0"  => $reg->fecharegistro,
		            "1"  => $reg->tipo_comprobante . '-' . $reg->serie_comprobante . '-' . $reg->num_comprobante,
		            "2"  => $reg->nombre,
		            "3"  => $reg->num_documento,
		            "4"  => number_format($saldo, 2, ".", ","),              // saldo restante
		            "5"  => number_format($reg->abonototal, 2, ".", ","),   // abonos
		            "6"  => number_format($deudaTotalConMora, 2, ".", ","), // deuda + mora
		            "7"  => $reg->fechavencimiento,
		            "8"  => ($reg->deudatotal == 0)
		                        ? '<center><span class="badge bg-green">Cancelado</span></center>'
		                        : '<center><span class="badge bg-red">Por Cancelar</span></center>',
		            "9"  => '<center><a target="_blank" href="' . $url1 . $reg->idventa . '" data-toggle="tooltip" title="Ticket"> <button class="btn btn-primary btn-xs"><i class="far fa-file-alt"></i></button></a></center>',
		            "10" => ($saldo <= 0)
				    ? '<div class="dropdown">
				            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> 
				                <i class="fa fa-list-ul"></i><span class="caret"></span>
				            </button>
				            <div class="dropdown-menu">
				                <a class="dropdown-item" style="cursor:pointer;"
				                   onclick="verEstadoCuenta('.$reg->idcpc.')">
				                   Estado de cuenta
				                </a>
				                <a class="dropdown-item" style="cursor:pointer;"
				                   onclick="mostrarAbonos('.$reg->idcpc.')">
				                   Ver abonos
				                </a>
				            </div>
				        </div>'
				    : '<div class="dropdown">
				            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> 
				                <i class="fa fa-list-ul"></i><span class="caret"></span>
				            </button>
				            <div class="dropdown-menu">
				                <a class="dropdown-item" style="cursor:pointer;"
				                   onclick="mostrar('.$reg->idcpc.')">
				                   Crear abonos
				                </a>
				                <a class="dropdown-item" style="cursor:pointer;"
				                   onclick="mostrarAbonos('.$reg->idcpc.')">
				                   Ver abonos
				                </a>
				                <a class="dropdown-item" style="cursor:pointer;"
				                   onclick="verEstadoCuenta('.$reg->idcpc.')">
				                   Estado de cuenta
				                </a>
				            </div>
				        </div>',
		            "11" => $btnRecordatorio
		        );
		    }

		    $results = array(
		        "sEcho" => 1,
		        "iTotalRecords" => count($data),
		        "iTotalDisplayRecords" => count($data),
		        "aaData" => $data
		    );
		    echo json_encode($results);
		break;

		case 'calcular_mora':
		    $idcpc = $_POST['idcpc'];
		    $rspta = $cuentascobrar->calcularMora($idcpc);
		    echo json_encode($rspta);
		break;

		case 'listarDetalle':
			$idcpc=$_REQUEST["idcpc"];
			$rspta=$cuentascobrar->listarDetalle($idcpc);
	 		//Vamos a declarar un array
	 		$data= Array();
	 		while ($reg=$rspta->fetch_object()){

				if($reg->formapago == 'Efectivo'){
					$formapago = '-';
				}else{

					if($reg->formapago != '' || $reg->formapago != null){

						$fp = "$reg->formapago - ";
						
					}else{
						$fp = '';
					}
					
					if($reg->banco != '' || $reg->banco != null){
						$bn= "$reg->banco - OP: $reg->op";
					}else{
						$bn='-';
					}

					$formapago = $fp . $bn;
				}

	 			$data[]=array(
	 				"0"=>$reg->fechapago,
	 				"1"=>$reg->montopagado,
	 				"2"=>$reg->montotarjeta,
	 				"3"=>$formapago
	 				);
	 		}
	 		$results = array(
	 			"sEcho"=>1, //Información para el datatables
	 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
	 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
	 			"aaData"=>$data);
	 		echo json_encode($results);

		break;

		case 'mostrar':
			$rspta=$cuentascobrar->mostrar($idcpc);
			echo json_encode($rspta);
		break;

		case 'amortizar_deuda':
		    $deuda = $_POST['montoPagarAmortizar'];
		    $idcliente = $_POST['idcliente_amortizar'];
		    $fecha_inicio = $_POST['fecha_inicio_amortizar'];
		    $fecha_fin = $_POST['fecha_fin_amortizar'];
		    $formapago = $_POST['formapagoAmortizar'];
		    $montopago = $_POST['montoPagarAmortizar'];
		    $idcaja = $_POST['idcaja']; // Caja abierta actual
		    $idpersonal = $_SESSION['idusuario'];
		    $rspta = $cuentascobrar->amortizarDeuda(
		        $deuda, 
		        $idcliente, 
		        $fecha_inicio, 
		        $fecha_fin, 
		        $formapago, 
		        $montopago, 
		        $idcaja,
        		$idpersonal
		    );
		    echo json_encode($rspta);
		break;

		case 'recordatorio_semana':
		    $rspta = $cuentascobrar->listarRecordatorioSemana();
		    $data = array();

		    while ($reg = $rspta->fetch_object()) {
		        $data[] = $reg;
		    }

		    echo json_encode($data);
	    break;

	    case 'actualizar_mora_diaria':
		    $idcpc = $_POST['idcpc'];
		    $rspta = $cuentascobrar->actualizarMoraDiaria($idcpc);
		    echo json_encode(['success' => $rspta]);
		break;

		case 'enviar_recordatorio':
		    $idcpc = isset($_POST['idcpc']) ? limpiarCadena($_POST['idcpc']) : null;
		    $rspta = $cuentascobrar->enviarRecordatorioWhatsApp($idcpc);
		    echo json_encode($rspta);
		break;

		case 'obtener_notificaciones':
		    $idsucursal = isset($_GET["idsucursal"]) ? intval($_GET["idsucursal"]) : 0;
		    $notificaciones = $cuentascobrar->generarNotificaciones($idsucursal);
		    echo json_encode($notificaciones);
		break;

		case 'marcar_leida':
		    $ids = $_POST['ids']; // Recibiremos varios IDs en una cadena tipo: 12,15,33
		    ejecutarConsulta("UPDATE notificaciones SET leido = 1 WHERE idnotificacion IN ($ids)");
		    echo json_encode(['success' => true]);
		break;

		case 'estado_cuenta':
		    $idcpc = $_GET['idcpc'];
		    echo $cuentascobrar->estadoCuentaDocumento($idcpc);
		break;

		case 'estado_cuenta_cliente':
			echo $cuentascobrar->estadoCuentaCliente(
				$_GET['idcliente'],
				$_GET['fecha_inicio'],
				$_GET['fecha_fin']
			);
		break;

	}

?>