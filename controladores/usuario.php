<?php
session_start();
require_once "../modelos/Usuario.php";
require_once __DIR__ . "/email.php";
require_once "../configuraciones/app.php";

$usuario=new Usuario();
function getClientIP() {
    // Primero intentamos obtener la IP local o la del proxy
    $ip = '';

	$host = $_SERVER['HTTP_HOST'] ?? '';
	$isProduction = !($host === 'localhost' || strpos($host, '127.0.0.1') !== false);

    $headers = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            foreach ($ips as $i) {
                $i = trim($i);
                if (filter_var($i, FILTER_VALIDATE_IP)) {
                    $ip = $i;
                    break 2; // salimos de ambos foreach
                }
            }
        }
    }

	// Solo en producción intentamos resolver una IP pública externa
	if ($isProduction && ($ip === '127.0.0.1' || $ip === '::1' || $ip === '')) {
        try {
            $ip = file_get_contents('https://api.ipify.org');
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $ip = '0.0.0.0';
            }
        } catch (Exception $e) {
            $ip = '0.0.0.0';
        }
	}

	if (!$isProduction && ($ip === '' || $ip === '::1')) {
		$ip = '127.0.0.1';
    }

    return $ip;
}


$idusuario=isset($_POST["idusuario"])? limpiarCadena($_POST["idusuario"]):"";
$idpersonal=isset($_POST["idpersonal"])? limpiarCadena($_POST["idpersonal"]):"";
$login=isset($_POST["login"])? limpiarCadena($_POST["login"]):"";
$clave=isset($_POST["clave"])? limpiarCadena($_POST["clave"]):"";
if (isset($_POST["idsucursal"])) {
    if (is_array($_POST["idsucursal"])) {
            $idsucursal = $_POST["idsucursal"]; // viene como array desde el select multiple
    } else {
            $idsucursal = [$_POST["idsucursal"]]; // si es una sola, la convertimos en array
        }
    } else {
        $idsucursal = [];
    }
$permisos = isset($_POST['permiso']) ? $_POST['permiso'] : [];
$subpermisos = isset($_POST['subpermisos']) ? $_POST['subpermisos'] : [];
$acciones = isset($_POST['acciones']) ? $_POST['acciones'] : [];

switch ($_GET["op"]){
	
	case 'guardaryeditar':

		//Hash SHA256 en la contraseña
		if (!empty($clave)) {
		    $clavehash = hash("SHA256", $clave);
		} else {
		    $clavehash = null; // No se actualiza la contraseña
		}
		
		if (empty($idusuario)){
			$rspta=$usuario->insertar($idpersonal,$login,$clavehash,$idsucursal,$permisos, $subpermisos, $acciones);
			echo $rspta ? "Usuario registrado" : "No se pudieron registrar todos los datos del usuario";
		}
		else {
			$rspta=$usuario->editar($idusuario,$idpersonal,$login,$clavehash,$idsucursal,$permisos, $subpermisos, $acciones);
			echo $rspta ? "Usuario actualizado" : "No se pudieron actualizar todos los datos del usuario";
		}
	break;

	case 'desactivar':
		$rspta=$usuario->desactivar($idusuario);
 		echo $rspta ? "Usuario Desactivado" : "Usuario no se puede desactivar";
	break;

	case 'activar':
		$rspta=$usuario->activar($idusuario);
 				echo $rspta ? "Usuario activado" : "Usuario no se puede activar";
	break;

	case 'verificarLogin':

		$nombre=$_GET['nombre'];

		$rspta=$usuario->verificarUsuario($nombre);
		echo json_encode($rspta);

	break;

	case 'mostrar':
		$rspta=$usuario->mostrar($idusuario);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
	break;

	case 'listar':

		$rspta=$usuario->listar();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){

					if($reg->nombre == NULL){

						$nombre='Acceso a Todas las Sucursales'; 

					}else{

						$nombre = $reg->nombre;

					}

		 			$data[]=array(
		 				"0"=>$reg->trabajador,
		 				"1"=>$reg->login,
						"2"=>$nombre,
		 				"3"=>($reg->condicion)?'<span class="badge bg-green">ACTIVADO</span>':
		 				'<span class="badge bg-red">DESACTIVADO</span>',
		 				"4"=>($reg->condicion)?'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idusuario.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idusuario.')"><i class="fas fa-times-circle"></i></button>':
 					'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idusuario.')"><i class="fas fa-edit"></i></button>'.
 					' <button class="btn btn-primary btn-xs" onclick="activar('.$reg->idusuario.')"><i class="fa fa-check"></i></button>',
		 				);
		 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;

	case "selectEmpleado":
		require_once "../modelos/Empleado.php";
		$empleado = new Empleado();
		$rspta = $empleado->select();
		while ($reg = $rspta->fetch_object())
				{

					if($_SESSION['idpersonal'] == $reg->idpersonal){

						echo '<option value=' . $reg->idpersonal . ' selected>' . $reg->nombre . '</option>';

					}else{

						echo '<option value=' . $reg->idpersonal . '>' . $reg->nombre . '</option>';
						
					}

				}
	break;

	case "selectEmpleadoServicio":
		require_once "../modelos/Empleado.php";
		$empleado = new Empleado();
		$rspta = $empleado->SelectEmpleadoServicio();
		echo '<option value="">-- Seleccionar un técnico--</option>';
		while ($reg = $rspta->fetch_object())
		{
			if($_SESSION['idpersonal'] == $reg->idpersonal){
				echo '<option value=' . $reg->idpersonal . ' selected>' . $reg->nombre . '</option>';
			}else{
				echo '<option value=' . $reg->idpersonal . '>' . $reg->nombre . '</option>';
			}
		}
	break;

	case 'permisos':
	    require_once "../modelos/Permiso.php";
	    $permiso = new Permiso();
	    $rspta = $permiso->listar();

	    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

	    // Permisos y subpermisos marcados del usuario
	    $marcados = ($id > 0) ? $usuario->listarmarcados($id) : false;
	    $permisosMarcados = [];
	    $subpermisosMarcados = [];

	    if ($marcados) {
	        while ($per = $marcados->fetch_object()) {
	            if (!is_null($per->idsubpermiso) && $per->idsubpermiso != '') {
	                $subpermisosMarcados[] = intval($per->idsubpermiso);
	            } else {
	                $permisosMarcados[] = intval($per->idpermiso);
	            }
	        }
	    }

	    // Acciones marcadas
	    $accionesMarcadas = [];
	    if ($id > 0) {
	        $rspta_acciones = $usuario->listaraccionesmarcadas($id);
	        while ($act = $rspta_acciones->fetch_object()) {
	            $accionesMarcadas[] = intval($act->idaccion_permiso);
	        }
	    }

	    // Cargar subpermisos y acciones de una sola vez
	    $subpermisos = [];
	    $sql_sub = "SELECT * FROM subpermiso ORDER BY idpermiso, idsubpermiso";
	    $rs_sub = ejecutarConsulta($sql_sub);
	    while ($row = $rs_sub->fetch_object()) {
	        $subpermisos[$row->idpermiso][] = $row;
	    }

	    $acciones = [];
	    $sql_acc = "SELECT * FROM accion_permiso ORDER BY idsubpermiso, idaccion_permiso";
	    $rs_acc = ejecutarConsulta($sql_acc);
	    while ($row = $rs_acc->fetch_object()) {
	        $acciones[$row->idsubpermiso][] = $row;
	    }

	    echo "<div style='display: flex; flex-wrap: wrap;'>";
	    while ($perm = $rspta->fetch_object()) {
	        $checked = in_array($perm->idpermiso, $permisosMarcados) ? 'checked' : '';

	        echo "
	        <div style='width: 25%; padding: 5px 10px; box-sizing: border-box;'>
	            <label style='font-weight:bold;'>
	                <input type='checkbox' name='permiso[]' value='{$perm->idpermiso}' $checked> {$perm->nombre}
	            </label>";

	        if (isset($subpermisos[$perm->idpermiso])) {
	            foreach ($subpermisos[$perm->idpermiso] as $sub) {
	                $sub_checked = in_array($sub->idsubpermiso, $subpermisosMarcados) ? 'checked' : '';

	                echo "
	                <div style='margin-left: 20px;'>
	                    <label>
	                        <input type='checkbox' name='subpermisos[]' value='{$sub->idsubpermiso}' $sub_checked> {$sub->nombre}
	                    </label>";

	                if (isset($acciones[$sub->idsubpermiso])) {
	                    foreach ($acciones[$sub->idsubpermiso] as $accion) {
	                        $accion_checked = in_array($accion->idaccion_permiso, $accionesMarcadas) ? 'checked' : '';
	                        echo "
	                        <div style='margin-left: 20px;'>
	                            <label>
	                                <input type='checkbox' name='acciones[]' value='{$accion->idaccion_permiso}' $accion_checked> {$accion->nombre}
	                            </label>
	                        </div>";
	                    }
	                }

	                echo "</div>";
	            }
	        }

	        echo "</div>";
	    }
	    echo "</div>";
	break;

	
	case 'verificar':
		$logina = $_POST['logina'];
		$clavea = $_POST['clavea'];

		// Hash SHA256 en la contraseña
		$clavehash = hash("SHA256", $clavea);

		$rspta = $usuario->verificar($logina, $clavehash);
		$fetch = $rspta->fetch_object();

		// Datos de IP y user agent
		$ip = getClientIP();

		$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

		if (isset($fetch)) {
			// Login exitoso
			$_SESSION['idusuario'] = $fetch->idusuario;
			$_SESSION['idpersonal'] = $fetch->idpersonal;
			$_SESSION['imagen'] = $fetch->imagen;
			$_SESSION['nombre'] = $fetch->nombre;
			$_SESSION['login'] = $fetch->login;
			$_SESSION['cargo'] = $fetch->cargo;
			$_SESSION["iniciarSesion"] = "ok";

			require_once "../modelos/Negocio.php"; 
				$negocioModel = new Negocio();
				$datosNegocio = $negocioModel->mostrarNombreNegocio();
				
				// Verificamos si trajo datos y si existe la columna 'nombre'
				// Nota: ejecutarConsultaSimpleFila suele devolver un Array asociativo
				if ($datosNegocio && !empty($datosNegocio['nombre'])) {
					$_SESSION['nombre_negocio'] = $datosNegocio['nombre'];
				} else {
					// Fallback por si la tabla datos_negocio está vacía
					$_SESSION['nombre_negocio'] = 'Mi Empresa'; 
				}
			// Registrar historial de login exitoso
			$usuario->registrarHistorial($fetch->idusuario, $ip, $user_agent, 1);

			// ======================================
			// Obtener sucursales asignadas
			// ======================================
			$sql_suc = "SELECT idsucursal FROM usuario_sucursal WHERE idusuario='{$fetch->idusuario}'";
			$rs_suc = ejecutarConsulta($sql_suc);
			$_SESSION['sucursales'] = array();
			while ($row = $rs_suc->fetch_object()) {
				$_SESSION['sucursales'][] = $row->idsucursal;
			}
			//$_SESSION['idsucursal'] = $_SESSION['sucursales'][0] ?? null;

			// Obtener permisos, subpermisos y acciones
			$marcados = $usuario->listarmarcados($fetch->idusuario);
			$valores = array();
			while ($per = $marcados->fetch_object()) {
				array_push($valores, $per->idpermiso);
			}
			// Accesos del usuario
			in_array(1, $valores) ? $_SESSION['inicio'] = 1 : $_SESSION['inicio'] = 0;
			in_array(2, $valores) ? $_SESSION['almacen'] = 1 : $_SESSION['almacen'] = 0;
			in_array(3, $valores) ? $_SESSION['compras'] = 1 : $_SESSION['compras'] = 0;
			in_array(4, $valores) ? $_SESSION['ventas'] = 1 : $_SESSION['ventas'] = 0;
			in_array(5, $valores) ? $_SESSION['personal'] = 1 : $_SESSION['personal'] = 0;
			in_array(6, $valores) ? $_SESSION['consultac'] = 1 : $_SESSION['consultac'] = 0;
			in_array(7, $valores) ? $_SESSION['consultav'] = 1 : $_SESSION['consultav'] = 0;
			in_array(8, $valores) ? $_SESSION['configuracion'] = 1 : $_SESSION['configuracion'] = 0;
			in_array(9, $valores) ? $_SESSION['cajachica'] = 1 : $_SESSION['cajachica'] = 0;
			in_array(10, $valores) ? $_SESSION['cuentascobrar'] = 1 : $_SESSION['cuentascobrar'] = 0;
			in_array(11, $valores) ? $_SESSION['kardex'] = 1 : $_SESSION['kardex'] = 0;
			in_array(12, $valores) ? $_SESSION['pos'] = 1 : $_SESSION['pos'] = 0;
			in_array(13, $valores) ? $_SESSION['cuentasxpagar'] = 1 : $_SESSION['cuentasxpagar'] = 0;
			in_array(14, $valores) ? $_SESSION['crearventa'] = 1 : $_SESSION['crearventa'] = 0;
			in_array(15, $valores) ? $_SESSION['inventario'] = 1 : $_SESSION['inventario'] = 0;
			in_array(16, $valores) ? $_SESSION['crearservicio'] = 1 : $_SESSION['crearservicio'] = 0;
			in_array(17, $valores) ? $_SESSION['procesar'] = 1 : $_SESSION['procesar'] = 0;

			// Subpermisos
			$subpermisos = $usuario->listarsubpermisos($fetch->idusuario);
			$_SESSION['subpermisos'] = array();
			while ($sub = $subpermisos->fetch_object()) {
				$_SESSION['subpermisos'][$sub->idpermiso][] = $sub->nombre;
			}

			// Acciones
			$acciones = $usuario->listaracciones($fetch->idusuario);
			$_SESSION['acciones'] = array();
			while ($act = $acciones->fetch_object()) {
				$_SESSION['acciones'][$act->modulo][$act->submodulo][$act->accion] = true;
			}

		} else {
			// Login fallido
			$usuario->registrarHistorial(0, $ip, $user_agent, 0);
		}

		echo json_encode($fetch);
	break;



	case 'salir':
	    if (isset($_SESSION['idusuario'])) {
	        $idusuario = $_SESSION['idusuario'];
	        // Marcar sesión como cerrada y exito=0
	        $sql = "UPDATE login_historial
	                SET logout = NOW(), exito = 0
	                WHERE idusuario = '$idusuario'
	                  AND exito = 1
	                  AND logout IS NULL
	                ORDER BY fecha DESC
	                LIMIT 1";
	        ejecutarConsulta($sql);
	    }

	    // Limpiamos las variables de sesión   
	    session_unset();
	    session_destroy();
	    header("Location: ../index.php");
	break;

	case 'listarSucursalesUsuario':
	    $idusuario = $_GET['idusuario'];
	    $rspta = $usuario->listarSucursalesUsuario($idusuario);
	    $data = array();
	    while ($reg = $rspta->fetch_object()) {
	        $data[] = $reg->idsucursal;
	    }
	    echo json_encode($data);
	break;

	case 'recuperar':

	    $email = limpiarCadena($_POST['email']);

	    $user = ejecutarConsultaSimpleFila("
	        SELECT u.idusuario, u.login, p.nombre, p.email
	        FROM usuario u
	        INNER JOIN personal p ON u.idpersonal = p.idpersonal
	        WHERE p.email = '$email'
	          AND u.condicion = '1'
	        LIMIT 1
	    ");

	    if (!$user) {
	        echo "<div class='alert alert-danger'>
	                Correo no registrado
	              </div>";
	        exit;
	    }

	    // token seguro
	    $token  = bin2hex(random_bytes(32));
	    $expira = date("Y-m-d H:i:s", strtotime("+15 minutes"));

	    ejecutarConsulta("
	        UPDATE usuario 
	        SET reset_token = '$token',
	            reset_expira = '$expira'
	        WHERE idusuario = '{$user['idusuario']}'
	    ");

	    // link real
	    $link = APP_URL . "/index.php?ruta=reset&token=" . $token;


	    // HTML del correo
	    $html = "
	        <h3>Recuperación de contraseña</h3>
	        <p>Hola <b>{$user['nombre']}</b>,</p>
	        <p>Haz clic en el siguiente botón para restablecer tu contraseña:</p>
	        <p>
	            <a href='$link'
	               style='background:#0d6efd;color:#fff;padding:10px 15px;
	                      text-decoration:none;border-radius:5px;'>
	               Restablecer contraseña
	            </a>
	        </p>
	        <p>Este enlace expira en 15 minutos.</p>
	    ";

	    // ENVÍO POR GMAIL
	    if (enviarCorreo(
	        $user['email'],
	        $user['nombre'],
	        'Recuperar contraseña',
	        $html
	    )) {
	        echo "<div class='alert alert-success'>
	                Hemos enviado un enlace a tu correo
	              </div>";
	    } else {
	        echo "<div class='alert alert-danger'>
	                Error al enviar correo
	              </div>";
	    }

	break;


	case 'reset':

    require_once "../configuraciones/Conexion.php";

    $token = $_POST['token'];
    $clave = hash("SHA256", $_POST['clave']);

    $user = ejecutarConsultaSimpleFila("
        SELECT idusuario
        FROM usuario
        WHERE reset_token='$token'
        AND reset_expira > NOW()
    ");

    if (!$user) {
        echo "<div class='alert alert-danger'>
                Token inválido o expirado
              </div>";
        exit;
    }

    ejecutarConsulta("
        UPDATE usuario
        SET clave='$clave',
            reset_token=NULL,
            reset_expira=NULL
        WHERE idusuario='{$user['idusuario']}'
    ");

    echo "<div class='alert alert-success'>
            Contraseña actualizada correctamente.<br>
            <a href='login'>Iniciar sesión</a>
          </div>";
break;

case 'validar_token':

    $token = $_GET['token'] ?? '';

    if (!$token) {
        echo json_encode(['status' => false]);
        exit;
    }

    $user = ejecutarConsultaSimpleFila("
        SELECT idusuario
        FROM usuario
        WHERE reset_token = '$token'
        AND reset_expira > NOW()
    ");

    if ($user) {
        echo json_encode(['status' => true]);
    } else {
        echo json_encode(['status' => false]);
    }

break;

case 'seleccionarSucursal':
    $idsucursal = $_POST['idsucursal'];
    if (in_array($idsucursal, $_SESSION['sucursales'])) {
        $_SESSION['idsucursal'] = $idsucursal;
        echo 'ok';
    } else {
        echo 'error';
    }
break;


}
?>