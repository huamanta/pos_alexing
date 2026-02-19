<?php
require_once "../modelos/Traslado.php";
session_start();
$traslado = new Traslado();
$idusuario = $_SESSION['idusuario'] ?? 0;
$idtraslado = isset($_POST["idtraslado"]) ? limpiarCadena($_POST["idtraslado"]) : "";
$idorigen = isset($_POST["idorigen"]) ? limpiarCadena($_POST["idorigen"]) : $_SESSION['idsucursal'];
$iddestino = isset($_POST["iddestino"]) ? limpiarCadena($_POST["iddestino"]) : "";
$fecha = date("Y-m-d H:i:s");
$productos = isset($_POST["productos"]) ? $_POST["productos"] : "";

switch ($_GET["op"]) {
	case 'guardaryeditar':
	    if (empty($idtraslado)) {
	        $rspta = $traslado->insertar($idorigen, $iddestino, $fecha, $productos, $idusuario);
	        echo $rspta;
	    } else {
	        $rspta = $traslado->editar($idtraslado, $idorigen, $iddestino, $fecha, $idusuario);
	        echo $rspta ? "Traslado actualizado correctamente" : "No se pudo actualizar el traslado";
	    }
	break;

	case 'aceptar':
	    $idtraslado = $_POST['idtraslado'];
	    $idusuario = $_SESSION['idusuario'];
	    $rspta = $traslado->aceptarTraslado($idtraslado, $idusuario);
	    echo $rspta;
	break;

	case 'listarnoti':
	    $idsucursal = intval($_GET['idsucursal'] ?? 0);
	    $rspta = $traslado->listarNotificaciones($idsucursal);
	    $data = [];
	    while ($reg = $rspta->fetch_object()) {
	        $data[] = [
	            "idnotificacion" => $reg->idnotificacion,
	            "mensaje" => $reg->mensaje,
	            "leido" => $reg->leido,
	            "fecha" => $reg->fecha,
	            "idtraslado" => $reg->idtraslado,
	            "tipo" => $reg->tipo,
	            "iddestino" => $reg->iddestino ?? null
	        ];
	    }
	    echo json_encode($data);
	break;

	case 'listar':
		$fecha_inicio = $_REQUEST["fecha_inicio"];
		$fecha_fin = $_REQUEST["fecha_fin"];
		$estado = $_REQUEST["estado"];
		$idsucursal = $_SESSION['idsucursal'];

		// ✅ admin si idusuario == 1
		$esAdmin = isset($_SESSION['idusuario']) && intval($_SESSION['idusuario']) === 1;

		$rspta = $traslado->listar($fecha_inicio, $fecha_fin, $estado, $idsucursal);
		$data = array();

		while ($reg = $rspta->fetch_object()) {

			$acciones = '';
			$btnVer = '';
			$btnImprimir = '';

			// Icono y color según tipo
			if ($reg->tipo == 'solicitud') {
				$tipoIcon = '<i class="fa fa-file-text"></i> Solicitud';
				$rowClass = 'table-primary';
			} else {
				$tipoIcon = '<i class="fa fa-exchange-alt"></i> Traslado';
				$rowClass = 'table-success';
			}

			// Botón imprimir según tipo
			if ($reg->tipo == 'solicitud') {
				$btnImprimir = '<button class="btn btn-primary btn-sm" title="Imprimir solicitud" onclick="imprimirSolicitud(' . $reg->idtraslado . ')"><i class="fa fa-print"></i></button> ';
			} else {
				$btnImprimir = '<button class="btn btn-success btn-sm" title="Imprimir traslado" onclick="imprimirTraslado(' . $reg->idtraslado . ')"><i class="fa fa-print"></i></button> ';
			}

			// Acciones según tipo y sucursal
			if ($reg->tipo == 'solicitud') {

				if ($reg->idorigen == $idsucursal) {
					// Sucursal solicitante
					$soloLectura = !$esAdmin ? 'true' : 'false';
					$btnVer = '<button class="btn btn-info btn-sm" title="Ver mi solicitud" onclick="verProductosSolicitud(' . $reg->idtraslado . ', '.$soloLectura.')"><i class="fa fa-eye"></i></button> ';

					$btnImprimir = ($reg->estado == 'Aceptado')
						? '<button class="btn btn-primary btn-sm" title="Imprimir solicitud" onclick="imprimirSolicitud(' . $reg->idtraslado . ')"><i class="fa fa-print"></i></button> '
						: '';

					$acciones = $btnVer . $btnImprimir;

				} else {
					// Sucursal principal (o cualquier otra viendo)
					$soloLectura = ($reg->estado != 'Pendiente') ? 'true' : 'false';
					$btnVer = '<button class="btn btn-info btn-sm" title="Ver solicitud" onclick="verProductosSolicitud(' . $reg->idtraslado . ', ' . $soloLectura . ')"><i class="fa fa-eye"></i></button> ';
					$btnImprimir = '<button class="btn btn-primary btn-sm" title="Imprimir solicitud" onclick="imprimirSolicitud(' . $reg->idtraslado . ')"><i class="fa fa-print"></i></button> ';

					$acciones = $btnVer . $btnImprimir;

					if ($reg->estado != 'Anulado') {
						$acciones .= '<button class="btn btn-danger btn-sm" title="Anular solicitud" onclick="desactivar(' . $reg->idtraslado . ')"><i class="fa fa-times"></i></button> ';
					}
				}
			}
			else {
				// Tipo traslado
				$btnVer = '<button class="btn btn-info btn-sm" title="Ver traslado" onclick="verProductos(' . $reg->idtraslado . ')"><i class="fa fa-eye"></i></button> ';
				$acciones = $btnVer . $btnImprimir;

				if ($reg->idorigen == $idsucursal) {
					// Sucursal origen (envía)
					if ($reg->estado == 'Pendiente') {
						$acciones .= '<button class="btn btn-danger btn-sm" title="Anular traslado" onclick="desactivar(' . $reg->idtraslado . ')"><i class="fa fa-times"></i></button> ';

						// ✅ Admin puede aceptar incluso si está en origen (si quieres que aparezca acá también)
						if ($esAdmin) {
							$acciones .= '<button class="btn btn-success btn-sm" title="Aceptar traslado (ADMIN)" onclick="aceptarTraslado(' . $reg->idtraslado . ')"><i class="fa fa-check"></i></button> ';
						}
					}
				} else {
					// Sucursal destino (recibe) o cualquier otra sucursal mirando
					if ($reg->estado == 'Pendiente') {

						// ✅ Normal: solo destino puede aceptar
						// ✅ Admin: puede aceptar desde cualquier sucursal
						// Requiere que tu query traiga $reg->iddestino
						$esDestino = isset($reg->iddestino) && intval($reg->iddestino) === intval($idsucursal);

						if ($esAdmin || $esDestino) {
							$acciones .= '<button class="btn btn-success btn-sm" title="Aceptar traslado" onclick="aceptarTraslado(' . $reg->idtraslado . ')"><i class="fa fa-check"></i></button> ';
						}
					}
				}
			}

			// Reactivar si está anulado
			if ($reg->estado == 'Anulado') {
				$acciones .= '<button class="btn btn-success btn-sm" title="Reactivar traslado" onclick="activar(' . $reg->idtraslado . ')"><i class="fa fa-check"></i></button>';
			}

			// Badge con tooltip
			$estadoBadge = '<span class="badge bg-' .
				($reg->estado == 'Aceptado' ? 'success' :
				($reg->estado == 'Pendiente' ? 'warning' : 'danger')) .
				'" title="Estado: ' . $reg->estado . '">' . $reg->estado . '</span>';

			$data[] = array(
				"DT_RowClass" => $rowClass,
				"0" => $reg->idtraslado,
				"1" => $reg->origen,
				"2" => $reg->destino,
				"3" => $reg->fecha,
				"4" => $estadoBadge,
				"5" => $tipoIcon,
				"6" => $acciones
			);
		}

		echo json_encode([
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData" => $data
		]);
	break;


	case 'verdetalle':
	    $idtraslado = $_GET['idtraslado'];
	    $rspta = $traslado->listarDetalle($idtraslado);
	    $data = array();
	    while ($reg = $rspta->fetch_object()) {
	        $data[] = array(
	            "producto" => $reg->producto,
	            "cantidad" => $reg->cantidad,
	            "destino" => $reg->destino
	        );
	    }
	    echo json_encode($data);
	break;


	case 'almacenesDestino':
		$idsucursal = $_SESSION['idsucursal'];
		$rspta = $traslado->sucursales($idsucursal);
		while ($reg = $rspta->fetch_object()) {
			if ($reg->idsucursal != $idsucursal)
				echo '<option value="' . $reg->idsucursal . '">' . htmlspecialchars($reg->nombre) . '</option>';
		}
	break;

	case 'listarProductos':
		$idsucursal = $_SESSION['idsucursal'];
		$busqueda = isset($_POST["busqueda"]) ? limpiarCadena($_POST["busqueda"]) : '';
		$pagina = isset($_POST["pagina"]) ? intval($_POST["pagina"]) : 1;
		$limite = isset($_POST["limite"]) ? intval($_POST["limite"]) : 10;
		$iddestino = isset($_POST["iddestino"]) ? intval($_POST["iddestino"]) : null;
		$tipo = isset($_POST["tipo"]) ? limpiarCadena($_POST["tipo"]) : 'traslado';
		$rspta = $traslado->listarProductos($idsucursal, $busqueda, $pagina, $limite, $iddestino,$tipo);
		echo json_encode($rspta);
	break;


	case 'sucursal_actual':
	    require_once "../modelos/Categoria.php";
	    $categoria = new Categoria();
	    $rspta = $categoria->mostrarSuc($_SESSION['idsucursal']);
	    echo json_encode($rspta);
	break;
    // Marcar una notificación como leída
    case 'marcarleida':
        $idnotificacion = intval($_POST['idnotificacion'] ?? 0);
        $rspta = $traslado->marcarLeida($idnotificacion);
        echo $rspta ? json_encode(["status"=>1, "message"=>"Notificación marcada como leída"]) 
                    : json_encode(["status"=>0, "message"=>"Error al marcar notificación"]);
        break;


    case 'guardarSolicitud':
		$idorigen = $_SESSION['idsucursal'];
		$iddestino = $_POST['iddestino_solicitud'];
		$productos = $_POST['productos'];
		$idusuario = $_SESSION['idusuario'];
		$fecha = date("Y-m-d H:i:s");

		// Insertar cabecera de solicitud con estado 0 (pendiente)
		$idtraslado = ejecutarConsulta_retornarID("INSERT INTO traslado (idorigen, iddestino, fecha, estado, idusuario, tipo) 
												VALUES ('$idorigen','$iddestino','$fecha','0','$idusuario', 'solicitud')");

		if(!$idtraslado){
			echo "Error al crear la solicitud";
			exit;
		}

		$productos = json_decode($productos, true);
		foreach($productos as $p){
			$idproducto = intval($p['idproducto']);
			$cantidad = floatval($p['cantidad']);
			ejecutarConsulta("INSERT INTO traslado_detalle (idtraslado, idproducto, cantidad) 
							VALUES ('$idtraslado','$idproducto','$cantidad')");
		}

		// Crear notificación para almacén destino
		$mensaje = "Nueva solicitud pendiente desde el almacén {$_SESSION['idsucursal']} con ID $idtraslado";
		ejecutarConsulta("INSERT INTO notificaciones (idsucursal, idtraslado, mensaje) VALUES ('$iddestino', '$idtraslado', '$mensaje')");

		echo " Solicitud enviada correctamente";
	break;

	case 'aprobarSolicitud':
	    $idtraslado = $_POST["idtraslado"];
	    $productos = json_decode($_POST["productos"], true); // array con productos aceptados/rechazados
	    $idusuario = $_SESSION['idusuario'];
	    
	    $rspta = $traslado->aprobarSolicitud($idtraslado, $productos, $idusuario);
	    echo $rspta;
	break;

	case 'verproductos2':
    $idtraslado = isset($_POST['idtraslado']) ? $_POST['idtraslado'] : 0;

    if ($idtraslado == 0) {
        echo json_encode([]);
        exit;
    }

    // Llamar al modelo para obtener productos
    $rspta = $traslado->verProductosSolicitud($idtraslado);
    $data = [];

    while ($reg = $rspta->fetch_object()) {
        $data[] = [
            'idproducto' => $reg->idproducto,
            'nombre' => $reg->nombre,
            'cantidad' => $reg->cantidad
        ];
    }

    echo json_encode($data);
break;

case 'verProductosSolicitud':
    $idtraslado = isset($_POST["idtraslado"]) ? intval($_POST["idtraslado"]) : 0;
    $soloLectura = isset($_POST["soloLectura"]) ? $_POST["soloLectura"] : false;

    if ($idtraslado <= 0) {
        echo json_encode(["error" => "ID de traslado inválido."]);
        exit;
    }

    $rspta = $traslado->verProductosSolicitud($idtraslado);

    $productos = [];
    while ($reg = $rspta->fetch_object()) {
        $productos[] = [
            "idproducto" => $reg->idproducto,
            "nombre"     => $reg->nombre,
            "cantidad"   => $reg->cantidad,
            "estado_detalle" => $reg->estado_detalle ?? 'pendiente',
            "observacion" => $reg->observacion ?? ''
        ];
    }

    echo json_encode(["productos" => $productos, "soloLectura" => $soloLectura]);
break;

case 'obtenerSucursalOrigen':
    $idtraslado = $_POST['idtraslado'];
    $rspta = $traslado->obtenerSucursalOrigen($idtraslado);
    echo json_encode($rspta);
break;

}
?>
