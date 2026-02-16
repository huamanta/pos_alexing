<?php 
date_default_timezone_set('America/Lima');
// Incluimos inicialmente el modelo de Asistencia
require_once "../modelos/Asistencia.php";
// Iniciar la sesión solo si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Instanciamos el modelo
$asistencia = new Asistencia();

// Obtenemos los datos del formulario
$idasistencia = isset($_POST["idasistencia"]) ? limpiarCadena($_POST["idasistencia"]) : "";
$idpersonal = isset($_POST["idpersonal"]) ? limpiarCadena($_POST["idpersonal"]) : "";
$fecha = isset($_POST["fecha"]) ? limpiarCadena($_POST["fecha"]) : "";
$hora_entrada = isset($_POST["hora_entrada"]) ? limpiarCadena($_POST["hora_entrada"]) : "";
$hora_salida = isset($_POST["hora_salida"]) ? limpiarCadena($_POST["hora_salida"]) : "";
$estado = isset($_POST["estado"]) ? limpiarCadena($_POST["estado"]) : "asistió";
$hora_tardanza = isset($_POST["hora_tardanza"]) ? limpiarCadena($_POST["hora_tardanza"]) : null;
$permiso = isset($_POST["permiso"]) ? limpiarCadena($_POST["permiso"]) : 'no';
$vacaciones = isset($_POST["vacaciones"]) ? limpiarCadena($_POST["vacaciones"]) : 'no';

require_once "../modelos/Empleado.php";
$empleado = new Empleado();

$idpersonal=isset($_POST["idpersonal"])? limpiarCadena($_POST["idpersonal"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$tipo_documento=isset($_POST["tipo_documento"])? limpiarCadena($_POST["tipo_documento"]):"";
$num_documento=isset($_POST["num_documento"])? limpiarCadena($_POST["num_documento"]):"";
$direccion=isset($_POST["direccion"])? limpiarCadena($_POST["direccion"]):"";
$telefono=isset($_POST["telefono"])? limpiarCadena($_POST["telefono"]):"";
$email=isset($_POST["email"])? limpiarCadena($_POST["email"]):"";
$cargo=isset($_POST["cargo"])? limpiarCadena($_POST["cargo"]):"";
$imagen=isset($_POST["imagen"])? limpiarCadena($_POST["imagen"]):"";
$porcentaje=isset($_POST["porcentaje"])? limpiarCadena($_POST["porcentaje"]):"";
$monto = isset($_POST["monto"]) ? limpiarCadena($_POST["monto"]) : 0;

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if ($permiso == "sí") {
            $estado = "falto";  // Cambiar el estado a "falto" si el permiso es "Sí"
        }
        // Verificar si ya existe una asistencia para este personal en esta fecha
        if (!empty($idasistencia)) {
            $rspta = $asistencia->editar($idasistencia, $idpersonal, $fecha, $hora_entrada, $hora_salida, $estado, $hora_tardanza, $permiso, $vacaciones,$monto);
            echo $rspta ? "Asistencia actualizada correctamente." : "No se pudo actualizar la asistencia.";
        } else {
            $rspta = $asistencia->insertar($idpersonal, $fecha, $hora_entrada, $hora_salida, $estado, $hora_tardanza, $permiso, $vacaciones,$monto);
            echo $rspta ? "Asistencia registrada correctamente." : "No se pudo registrar la asistencia.";
        }
    break;

    case 'verificarAsistencia':
        $idpersonal = isset($_POST["idpersonal"]) ? limpiarCadena($_POST["idpersonal"]) : "";
        $fecha = isset($_POST["fecha"]) ? limpiarCadena($_POST["fecha"]) : "";

        // Verificar si ya existe una asistencia para este personal en la fecha
        $asistenciaExistente = $asistencia->existeAsistencia($idpersonal, $fecha);
        if ($asistenciaExistente) {
            echo "existe"; // Indicar que ya existe una asistencia
        } else {
            echo "no_existe"; // No existe asistencia, se puede registrar
        }
    break;

    case 'obtenerAsistencia':
        $idpersonal = isset($_POST["idpersonal"]) ? limpiarCadena($_POST["idpersonal"]) : "";
        $fecha = isset($_POST["fecha"]) ? limpiarCadena($_POST["fecha"]) : "";

        // Obtener los datos de la asistencia
        $asistencia = $asistencia->obtenerAsistencia($idpersonal, $fecha);
        echo json_encode($asistencia);
        break;

    case 'obtener_por_idasistencia':
        $idasistencia = isset($_POST["idasistencia"]) ? limpiarCadena($_POST["idasistencia"]) : "";
        $asistencia_data = $asistencia->obtenerAsistenciaPorId($idasistencia);
        echo json_encode($asistencia_data);
        break;

    case 'eliminar':
        $idasistencia = isset($_POST["idasistencia"]) ? limpiarCadena($_POST["idasistencia"]) : "";
        $rspta = $asistencia->eliminar($idasistencia);
        echo $rspta ? "Asistencia eliminada correctamente." : "No se pudo eliminar la asistencia.";
        break;

    case 'eliminar_multiple':
        $idasistencias_str = isset($_POST["idasistencias"]) ? limpiarCadena($_POST["idasistencias"]) : "";
        $idasistencias_array = explode(',', $idasistencias_str);
        $rspta = $asistencia->eliminarMultiple($idasistencias_array);
        echo $rspta ? "Asistencias eliminadas correctamente." : "No se pudieron eliminar las asistencias.";
        break;
        
    case 'listarpersonal':
          // Asegúrate de que la zona horaria esté configurada correctamente
        $hoy = date('Y-m-d');
        $rspta=$asistencia->listarpersonal();
        //Vamos a declarar un array
        $data= Array();

        while ($reg = $rspta->fetch_object()) {
            $idPer = intval( $reg->idpersonal );
            $idAsi = intval( $reg->idasistencia );

            // Si está activo
            if ($reg->condicion) {
                error_log("idpersonal: " . $reg->idpersonal . ", idasistencia: " . $reg->idasistencia);
                if ($idAsi > 0) {
                    $botones = '<button class="btn btn-warning btn-xs" onclick="cargarAsistenciaParaEditar('. $idPer .', \'' . $hoy . '\')">
                                    <i class="fas fa-edit"></i>
                                </button> ';
                } else {
                    // No tiene registro → mostrar Registrar
                    $botones = '<button class="btn btn-primary btn-xs" onclick="registrarasis('. $idPer .')">
                                    <i class="fas fa-user-check"></i>
                                </button> ';
                }

                // Opcional: siempre permitir desactivar al usuario
                $botones .= '<button class="btn btn-danger btn-xs" onclick="desactivar('. $idPer .')">
                                 <i class="fas fa-times-circle"></i>
                             </button>';
            } else {
                // Usuario inactivo, otros botones…
                $botones = '<button class="btn btn-success btn-xs" onclick="activar('. $idPer .')">
                                <i class="fa fa-check"></i>
                            </button>';
            }

            $data[] = [
                "0" => $reg->nombre,
                "1" => $reg->tipo_documento . ': ' . $reg->num_documento,
                "2" => $reg->telefono,
                "3" => $reg->email,
                "4" => "<img src='files/personal/{$reg->imagen}' height='60' width='60'>",
                "5" => $reg->condicion
                           ? '<span class="badge bg-green">ACTIVADO</span>'
                           : '<span class="badge bg-red">DESACTIVADO</span>',
                "6" => $botones,
                "7" => $reg->idpersonal,
                "8" => $reg->idasistencia
            ];
        }

        $results = array(
            "sEcho"=>1, //Información para el datatables
            "iTotalRecords"=>count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
            "aaData"=>$data);
        echo json_encode($results);

    break;

    case 'listarehistorial':
    $fecha_inicio = $_REQUEST["fecha_inicio"];
    $fecha_fin = $_REQUEST["fecha_fin"];
    $rspta = $asistencia->listarHistorialAsistencias($fecha_inicio, $fecha_fin);

    $data = Array();

    while ($reg = $rspta->fetch_object()) {
        $costo_total = $reg->horas_trabajadas * $reg->costo_por_hora;
        if (empty($reg->hora_salida)) {
            $reg->horas_trabajadas = 0;
        }

        // Estado formateado con color
        $estado_html = '';
        if ($reg->estado == "asistio") {
            $estado_html = "<span class='badge bg-success'>Asistió</span>";
        } elseif ($reg->estado == "falto") {
            $estado_html = "<span class='badge bg-danger'>Faltó</span>";
        } else {
            $estado_html = "<span class='badge bg-secondary'>$reg->estado</span>";
        }

        // Retraso visual
        if ($reg->vacaciones === "si") {
            $retraso = "<span class='badge bg-success'><i class='fa fa-sun'></i> De vacaciones</span>";
        } elseif ($reg->permiso === "si") {
            $retraso = "<span class='badge bg-info'><i class='fa fa-check-circle'></i> De permiso</span>";
        } else {
            $retraso = $reg->minutos_retraso > 0 
                ? "<span class='badge bg-danger'><i class='fa fa-clock'></i> Retraso: " . $reg->retraso_formateado . "</span>" 
                : "<span class='badge bg-warning'><i class='fa fa-smile'></i> Sin retrasos</span>";
        }

                    $data[] = [
                        "0" => '<input type="checkbox" class="asistencia-checkbox" name="idasistencia_check[]" value="' . $reg->idasistencia . '">', // Checkbox
                        "1" => $reg->nombre,
                        "2" => $reg->fecha,
                        "3" => $reg->hora_entrada,
                        "4" => $reg->hora_salida,
                        "5" => $reg->horas_trabajadas,
                        "6" => $reg->tardanza,
                        "7" => $estado_html, // Estado con color
                        "8" => $reg->permiso,
                        "9" => $reg->vacaciones,
                        "10" => "S/ " . number_format($costo_total, 2),
                        "11" => $retraso,
                        "12" => '
                            <button class="btn btn-warning btn-xs" onclick="mostrarAsistencia(' . $reg->idasistencia . ')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-xs" onclick="eliminarAsistencia(' . $reg->idasistencia . ')">
                                <i class="fas fa-trash"></i>
                            </button>'
                    ];    }

    echo json_encode([
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    ]);
break;


case 'resumen_por_personal':
  $fecha_inicio = $_GET["fecha_inicio"];
  $fecha_fin = $_GET["fecha_fin"];

  $rspta = $asistencia->resumenPorPersonal($fecha_inicio, $fecha_fin);
  $data = [];

  while ($reg = $rspta->fetch_object()) {
    $data[] = [
      $reg->nombre,
      $reg->num_documento,
      $reg->total_asistencias,
      $reg->total_horas,
      $reg->tardanzas,
      $reg->permisos,
      $reg->vacaciones,
      $reg->dias_asistidos,
      $reg->horas_segundos
    ];
  }

  echo json_encode([
    "sEcho" => 1,
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data,
  ]);
  break;

case 'guardarmonto':
    $rspta = $asistencia->guardarMontoDia($_POST['idpersonal'], $_POST['fecha'], $_POST['monto']);
    echo $rspta ? "ok" : "error";
break;

case 'registrar_pago_dia':

    $idasistencia = $_POST["idasistencia"];
    $idpersonal = $_POST["idpersonal"];
    $fecha = $_POST["fecha"];
    $monto_pago = $_POST["monto_pago"];
    $observacion = $_POST["observacion"];

    $rspta = $asistencia->registrarPagoDia($idasistencia, $idpersonal, $fecha, $monto_pago, $observacion);

    echo $rspta ? "Pago registrado correctamente." : "No se pudo registrar el pago.";
break;

case 'eliminarasis':
    $idasistencia = $_POST["idasistencia"];
    $rspta = $asistencia->eliminarAsis($idasistencia);
    echo $rspta ? "ok" : "error";
break;

}
?>
