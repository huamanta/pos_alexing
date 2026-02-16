<?php 
require_once "../modelos/Servicio.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$servicio = new Servicio();

$idservicio = isset($_POST["idservicio"])? limpiarCadena($_POST["idservicio"]):"";
$idsucursal = isset($_POST["idsucursal"])? limpiarCadena($_POST["idsucursal"]):"";
$tipo_comprobante = isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";
$serie_comprobante = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";
$num_comprobante = isset($_POST["num_comprobante"]) ? limpiarCadena($_POST["num_comprobante"]) : "";
$idcliente = isset($_POST["idcliente"])? limpiarCadena($_POST["idcliente"]):"";
$equipo = isset($_POST["equipo"])? limpiarCadena($_POST["equipo"]):"";
$idtecnico = isset($_POST["idtecnico"])? limpiarCadena($_POST["idtecnico"]):"";
$fecha_ingreso = isset($_POST["fecha_ingreso"]) ? limpiarCadena($_POST["fecha_ingreso"]) : "";
$fecha_reparacion = isset($_POST["fecha_reparacion"]) ? limpiarCadena($_POST["fecha_reparacion"]) : "";
$fecha_entrega = isset($_POST["fecha_entrega"]) ? limpiarCadena($_POST["fecha_entrega"]) : "";
$total = isset($_POST["total"]) ? limpiarCadena($_POST["total"]) : "";
$descripcion_problema = isset($_POST["descripcion_problema"])? limpiarCadena($_POST["descripcion_problema"]):"";
$descripcion_solucion = isset($_POST["descripcion_solucion"])? limpiarCadena($_POST["descripcion_solucion"]):"";
$estado = isset($_POST["estado"])? limpiarCadena($_POST["estado"]):"";
$fecha_ingreso = date("Y-m-d H:i:s", strtotime($fecha_ingreso));
$fecha_reparacion = !empty($fecha_reparacion) ? date("Y-m-d H:i:s", strtotime($fecha_reparacion)) : null;
$fecha_entrega = !empty($fecha_entrega) ? date("Y-m-d H:i:s", strtotime($fecha_entrega)) : null;
require_once "../modelos/Persona.php";

$persona = new Persona();

$idpersona = isset($_POST["idpersona"]) ? limpiarCadena($_POST["idpersona"]) : "";
$tipo_persona = isset($_POST["tipo_persona"]) ? limpiarCadena($_POST["tipo_persona"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
$num_documento = isset($_POST["num_documento"]) ? limpiarCadena($_POST["num_documento"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
$fecha_hora = isset($_POST["fecha_hora"]) ? limpiarCadena($_POST["fecha_hora"]) : "";
$input_cuotas = isset($_POST["input_cuotas"]) ? limpiarCadena($_POST["input_cuotas"]) : "";
$inputInteres = isset($_POST["inputInteres"]) ? limpiarCadena($_POST["inputInteres"]) : "";

switch ($_GET["op"]){
	case 'guardaryeditar':
		$productos = isset($_POST["productos"]) ? json_decode($_POST["productos"], true) : [];

		if (empty($idservicio)){
		    $rspta = $servicio->insertar($idsucursal, $tipo_comprobante, 
			$serie_comprobante, $num_comprobante, $idcliente, $equipo, $idtecnico, $fecha_ingreso, $descripcion_problema, $descripcion_solucion, $productos,$total);
		    echo $rspta ? "Servicio registrado" : "No se pudo registrar";
		} else {
		    $rspta = $servicio->editar($idservicio, $idsucursal, $tipo_comprobante, 
			$serie_comprobante, $num_comprobante, $idcliente, $equipo, $idtecnico, $estado, $fecha_reparacion, $fecha_entrega, $descripcion_problema, $descripcion_solucion, $productos,$total);
		    echo $rspta ? "Servicio actualizado" : "No se pudo actualizar";
		}

	break;

	case 'mostrar':
		$rspta = $servicio->mostrar($idservicio);
 		echo json_encode($rspta);
	break;

	case 'listar':
		$fecha_inicio = $_REQUEST["fecha_inicio"];
		$fecha_fin = $_REQUEST["fecha_fin"];
		$estado = $_REQUEST["estado"];
		$idsucursal = $_REQUEST["idsucursal2"];

		if ($idsucursal == "" || $idsucursal == NULL) {

			if ($_SESSION['idsucursal'] == 0) {

				$idsucursal = 'Todos';
			} else {

				$idsucursal = $_SESSION['idsucursal'];
			}
		}
	    $rspta = $servicio->listar($fecha_inicio, $fecha_fin, $estado, $idsucursal);
	    $data = array();

	    while ($reg = $rspta->fetch_object()) {
	    	$url1 = 'reportes/exTicketServicio.php?id=';
	    	$ticket = '';
		    $mostrar = ''; 

		    if ($reg->tipo_comprobante == 'Ticket') {
		        $ticket = '<a target="_blank" <a target="_blank" href="' . $url1 . $reg->idservicio . '"> <button class="btn btn-primary btn-xs"><i class="fa fa-print"></i></button></a>';
		        $mostrar = $ticket;
		    } else {
		        $mostrar = '';
		    }
	        // Verifica si la fecha_entrega es null y asigna un valor vacío si lo es
	        $fecha_entrega = ($reg->fecha_entrega === null || $reg->fecha_entrega === '0000-00-00 00:00:00') 
		    ? '' 
		    : date("d/m/Y H:i", strtotime($reg->fecha_entrega));

	        // Asignar el color basado en el estado
	        switch ($reg->estado) {
	            case 'Recibido':
	                $estado_color = '<span class="badge badge-primary">Recibido</span>';  // Color gris para "Recibido"
	                break;
	            case 'En proceso':
	                $estado_color = '<span class="badge badge-warning">En proceso</span>';  // Color amarillo para "En proceso"
	                break;
	            case 'Terminado':
	                $estado_color = '<span class="badge badge-danger">Terminado</span>';  // Color verde para "Terminado"
	                break;
	            case 'Entregado':
	                $estado_color = '<span class="badge badge-success">Entregado</span>';  // Color azul para "Entregado"
	                break;
	            default:
	                $estado_color = '<span class="badge badge-light">Desconocido</span>';  // Color por defecto si el estado no es válido
	                break;
	        }

	        $data[] = array(
	            "0" => $reg->tipo_comprobante . ' ' . $reg->serie_comprobante . '-' . $reg->num_comprobante,
	            "1" => $reg->cliente,
	            "2" => $reg->equipo,
	            "3" => $reg->tecnico,
	            "4" => date("d/m/Y H:i", strtotime($reg->fecha_ingreso)),
	            "5" => $fecha_entrega,  // Usa la fecha de entrega si existe, si no, muestra vacío
	            "6" => $estado_color,  // Aquí estamos usando el estado con color
	            "7" => $reg->total,
	            "8" => '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idservicio . ')"><i class="fas fa-edit"></i></button>' .  
       				 ' <button class="btn btn-success btn-xs" onclick="ver(' . $reg->idservicio . ')"><i class="fas fa-eye"></i></button>' . ' <button class="btn btn-danger btn-xs" onclick="eliminarservicio(' . $reg->idservicio . ')"><i class="fas fa-trash"></i></button>'.
					$mostrar 
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


	case 'listarDetalle':
	    $rspta = $servicio->listarDetalle($_POST['idservicio']);
	    $data = array();
	    while ($row = $rspta->fetch_object()) {
	        $data[] = $row;
	    }
	    echo json_encode($data);
	break;

	case 'mostrar_num_ticket2':

		$idsucursal = $_REQUEST["idsucursal"];
		//mostrando el numero de boleta de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_numero_ticket2($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$num_comp_tic = $reg->num_comprobante
			);
		}
		$numero_tic_comp = (int)$num_comp_tic;
		//fin de mostrar numero de boleta de la tabla comprobantes
		$rspta = $servicio->numero_venta_ticket2($idsucursal);
		$data = array();
		$numerot = $numero_tic_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numerot = $reg->num_comprobante
			);
		}
		$numero_ticket = (int)$numerot;
		$new_ticket = '';

		if ($numero_ticket == 9999999 or empty($numerot)) {
			$new_ticket = '0000001';
			echo json_encode($new_ticket);
		} elseif ($numerot == 9999999) {
			$new_ticket = '0000001';
			echo json_encode($new_ticket);
		} else {
			$sumatic = $numero_ticket + 1;
			echo json_encode($sumatic);
		}
		//$num = (int)$numerof; 
		//echo json_encode($numerof);
		break;
	case 'mostrar_s_ticket2':

		$idsucursal = $_REQUEST["idsucursal"];

		//mostrando el numero de factura de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->mostrar_serie_ticket2($idsucursal);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$serie_comp_tic = $reg->serie_comprobante,
				$num_comp_tic = $reg->num_comprobante
			);
		}
		$serie_tic_comp = (int)$serie_comp_tic;
		$num_tic_comp = (int)$num_comp_tic;
		//fin de mostrar numero de factura de la tabla comprobantes
		$rspta = $servicio->numero_serie_ticket2($idsucursal);
		$data = array();
		$numero_s_tic = $serie_tic_comp;
		$numero_bolet = $num_tic_comp;

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				$numero_s_tic = $reg->serie_comprobante,
				$numero_bolet = $reg->num_comprobante
			);
		}
		$num_s_ticket = (int)$numero_s_tic;
		$nuew_serie_ticket = 0;
		$numbo = (int)$numero_bolet;
		if ($numbo == 9999999 or empty($numero_s_tic)) {
			$nuew_serie_ticket = $num_s_ticket + 1;
			echo json_encode($nuew_serie_ticket);
		} else {
			echo json_encode($num_s_ticket);
		}
		break;

	case 'eliminar':
	    $idservicio = isset($_POST["idservicio"]) ? intval($_POST["idservicio"]) : 0;

	    if ($idservicio > 0) {
	        // Asegúrate de que este método elimine tanto el servicio como sus detalles
	        $respuesta = $servicio->eliminar($idservicio);
	        echo $respuesta ? "Servicio eliminado correctamente." : "No se pudo eliminar el servicio.";
	    } else {
	        echo "ID de servicio no válido.";
	    }
	    break;

	case 'guardarCliente':
		if (empty($idpersona)) {
			$rspta = $persona->insertar($tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_hora);
			echo $rspta ? "Cliente registrado" : "Cliente no se pudo registrar";
		}
		break;

	case 'selectCliente':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarc();

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . ' - ' . $reg->num_documento . '</option>';
		}
		break;

	case 'mostrarUltimoCliente':

		$rspta = $servicio->mostrarUltimoCliente();
		echo json_encode($rspta);

		break;

	case 'selectCliente3':

		$numero = $_GET['numero'];

		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarc2($numero);

		echo json_encode($rspta);

		break;

}
?>
