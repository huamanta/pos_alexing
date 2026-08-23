<?php
require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . "/../modelos/Usuario.php";
require_once __DIR__ . "/email.php";

$usuario = new Usuario();

$idusuario = isset($_POST["idusuario"]) ? limpiarCadena($_POST["idusuario"]) : "";
$idpersonal = isset($_POST["idpersonal"]) ? limpiarCadena($_POST["idpersonal"]) : "";
$login = isset($_POST["login"]) ? limpiarCadena($_POST["login"]) : "";
$clave = isset($_POST["clave"]) ? limpiarCadena($_POST["clave"]) : "";
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

switch ($_GET["op"]) {

	case 'guardaryeditar':

		//Hash SHA256 en la contraseña
		if (!empty($clave)) {
			$clavehash = hash("SHA256", $clave);
		} else {
			$clavehash = null; // No se actualiza la contraseña
		}

		if (empty($idusuario)) {
			$usuario->insertar($idpersonal, $login, $clavehash, $idsucursal, $permisos, $subpermisos, $acciones);
		} else {
			$usuario->editar($idusuario, $idpersonal, $login, $clavehash, $idsucursal, $permisos, $subpermisos, $acciones);
		}
		break;

	case 'desactivar':
		$usuario->desactivar($idusuario);
		break;

	case 'activar':
		$usuario->activar($idusuario);
		break;

	case 'verificarLogin':

		$nombre = $_GET['nombre'];

		$rspta = $usuario->verificarUsuario($nombre);
		echo json_encode($rspta);

		break;

	case 'mostrar':
		$usuario->mostrar($idusuario);
		break;

	case 'listar':

		$rspta = $usuario->listar();
		//Vamos a declarar un array
		$data = array();

		while ($reg = $rspta->fetch_object()) {

			$nombre = 'Acceso a Todas las Sucursales';
			$btnEditar = '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idusuario . ')"><i class="fas fa-edit"></i></button>';
			$btnAnular = '';
			if (!$reg->superusuario) {
				$nombre = $reg->nombre;
				$btnAnular = '<button class="btn btn-danger btn-xs" onclick="desactivar(' . $reg->idusuario . ')"><i class="fas fa-times-circle"></i></button>';
				if (!$reg->condicion) {
					$btnAnular = '<button class="btn btn-primary btn-xs" onclick="activar(' . $reg->idusuario . ')"><i class="fa fa-check"></i></button>';
				}
			}

			$data[] = array(
				"0" => $reg->trabajador,
				"1" => $reg->login,
				"2" => $nombre,
				"3" => ($reg->condicion) ? '<span class="badge bg-green">ACTIVADO</span>' :
					'<span class="badge bg-red">DESACTIVADO</span>',
				"4" => $btnEditar . $btnAnular,
			);
		}
		$results = array(
			"sEcho" => 1, //Información para el datatables
			"iTotalRecords" => count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);

		break;

	case "selectEmpleado":
		require_once "../modelos/Empleado.php";
		$empleado = new Empleado();
		$idusuario = $_SESSION['idusuario'];
		$only_personal = isset($_POST["only_personal"]) ? limpiarCadena($_POST["only_personal"]) : "";
		$idpersonal = isset($_POST["idpersonal"]) ? limpiarCadena($_POST["idpersonal"]) : "";
		$rspta = $empleado->select($idusuario, $idpersonal, $only_personal);
		$empleados = [];
		while ($reg = $rspta->fetch_object()) {
			$empleados[] = $reg;
		}
		echo '<option value="">Seleccione...</option>';
		$autoSelect = count($empleados) === 1;
		foreach ($empleados as $reg) {
			$selected = $autoSelect ? ' selected' : '';
			echo '<option value="' . $reg->idpersonal . '"' . $selected . '>' . $reg->nombre . '</option>';
		}

		break;

	case "selectEmpleadoServicio":
		require_once "../modelos/Empleado.php";
		$empleado = new Empleado();
		$rspta = $empleado->SelectEmpleadoServicio();
		echo '<option value="">-- Seleccionar un técnico--</option>';
		while ($reg = $rspta->fetch_object()) {
			if ($_SESSION['idpersonal'] == $reg->idpersonal) {
				echo '<option value=' . $reg->idpersonal . ' selected>' . $reg->nombre . '</option>';
			} else {
				echo '<option value=' . $reg->idpersonal . '>' . $reg->nombre . '</option>';
			}
		}
		break;

	case 'permisos':
		require_once "../modelos/Permiso.php";

		$permiso = new Permiso();
		$rspta = $permiso->listarPermisos();

		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

		$marcados = $usuario->listarmarcados($id);

		$permisosMarcados = [];
		$subpermisosMarcados = [];

		foreach ($marcados as $per) {
			if (!empty($per['idsubpermiso'])) {
				$subpermisosMarcados[] = intval($per['idsubpermiso']);
			} else {
				$permisosMarcados[] = intval($per['idpermiso']);
			}
		}

		$accionesMarcadas = [];

		if ($id > 0) {
			$rspta_acciones = $usuario->listaraccionesmarcadas($id);

			while ($act = $rspta_acciones->fetch_assoc()) {
				$accionesMarcadas[] = intval($act['idaccion_permiso']);
			}
		}

		$subpermisos = [];
		$rs_sub = $permiso->listarSubPermisos();
		foreach ($rs_sub as $row) {
			$subpermisos[$row['idpermiso']][] = $row;
		}

		$acciones = [];

		$rs_acc = $permiso->listarAccionesPermiso();
		foreach ($rs_acc as $row) {
			$acciones[$row['idsubpermiso']][] = $row;
		}

		foreach ($rspta as $perm) {

			$idPermiso = intval($perm['idpermiso']);
			$nombrePermiso = htmlspecialchars($perm['nombre'], ENT_QUOTES, 'UTF-8');

			$checked = in_array(
				$idPermiso,
				$permisosMarcados
			) ? 'checked' : '';

			echo "
			<div class='permiso-card'>

				<label class='permiso-title'>
					<input
						type='checkbox'
						name='permiso[]'
						value='{$idPermiso}'
						{$checked}
					>
					{$nombrePermiso}
				</label>
			";

			if (isset($subpermisos[$idPermiso])) {

				foreach ($subpermisos[$idPermiso] as $sub) {

					$idSubPermiso = intval($sub['idsubpermiso']);
					$nombreSubPermiso = htmlspecialchars(
						$sub['nombre'],
						ENT_QUOTES,
						'UTF-8'
					);

					$sub_checked = in_array(
						$idSubPermiso,
						$subpermisosMarcados
					) ? 'checked' : '';

					echo "
					<div class='subpermiso'>

						<label>
							<input
								type='checkbox'
								name='subpermisos[]'
								value='{$idSubPermiso}'
								{$sub_checked}
							>
							{$nombreSubPermiso}
						</label>
					";

					if (isset($acciones[$idSubPermiso])) {

						foreach ($acciones[$idSubPermiso] as $accion) {

							$idAccion = intval($accion['idaccion_permiso']);
							$nombreAccion = htmlspecialchars(
								$accion['nombre'],
								ENT_QUOTES,
								'UTF-8'
							);

							$accion_checked = in_array(
								$idAccion,
								$accionesMarcadas
							) ? 'checked' : '';

							echo "
							<div class='accion'>

								<label>
									<input
										type='checkbox'
										name='acciones[]'
										value='{$idAccion}'
										{$accion_checked}
									>
									{$nombreAccion}
								</label>

							</div>
							";
						}
					}

					echo "</div>";
				}
			}

			echo "</div>";
		}

		break;


	case 'verificar':
		$logina = $_POST['logina'];
		$clavea = $_POST['clavea'];
		// Hash SHA256 en la contraseña
		$clavehash = hash("SHA256", $clavea);
		echo $usuario->verificar($logina, $clavehash);
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
		echo json_encode($usuario->listarSucursalesUsuario($idusuario));
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
		$token = bin2hex(random_bytes(32));
		$expira = date("Y-m-d H:i:s", strtotime("+15 minutes"));

		ejecutarConsulta("
	        UPDATE usuario 
	        SET reset_token = '$token',
	            reset_expira = '$expira'
	        WHERE idusuario = '{$user['idusuario']}'
	    ");

		// link real
		$link = env('APP_URL') . "/index.php?ruta=reset&token=" . $token;


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
		if (
			enviarCorreo(
				$user['email'],
				$user['nombre'],
				'Recuperar contraseña',
				$html
			)
		) {
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
		$res = $usuario->seleccionarSucursal($idsucursal);
		if ($res) {
			$_SESSION['idsucursal'] = $res['idsucursal'];
			$_SESSION['nombre_sucursal'] = $res['nombre'];
			$_SESSION['nombre_impuesto'] = $res['nombre_impuesto'];
			$_SESSION['monto_impuesto'] = $res['monto_impuesto'];
			echo 'ok';
		} else {
			echo 'error';
		}
		break;

	case 'crearSucursal':
		require_once "../modelos/Empresas.php";
		$empresa = new Empresa();
		$ruc = isset($_POST["ruc"]) ? limpiarCadena($_POST["ruc"]) : "";
		$razon_social = isset($_POST["razon_social"]) ? limpiarCadena($_POST["razon_social"]) : "";
		$nombre_impuesto = isset($_POST["nombre_impuesto"]) ? limpiarCadena($_POST["nombre_impuesto"]) : "";
		$monto_impuesto = isset($_POST["monto_impuesto"]) ? limpiarCadena($_POST["monto_impuesto"]) : "";
		$estado = 1; // Activo por defecto
		// Insertar empresa
		$res_empresa = $empresa->guardaryeditar(
			"",
			$ruc,
			$razon_social,
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			$nombre_impuesto,
			$monto_impuesto,
			$estado,
			['Nota de Venta', 'Factura', 'Boleta', 'Nota de Crédito', 'Nota de Débito', 'Cotización', 'Orden de Compra', 'Guia de Remisión'],
			['NV001', 'F001', 'B001', 'NC01', 'ND01', 'COT01', 'OC01', 'T001'],
			[0, 0, 0, 0, 0, 0, 0]
		);
		if (intval($res_empresa['code']) === 200) {
			// Obtener idempresa
			$sql_emp = "SELECT idempresa FROM empresas ORDER BY idempresa DESC LIMIT 1";
			$emp = ejecutarConsultaSimpleFila($sql_emp);
			$idempresa = $emp['idempresa'];

			require_once "../modelos/Categoria.php";
			$categoria = new Categoria();
			$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
			$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
			$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
			$rspta = $categoria->insertarSucursal(
				$nombre,
				$direccion,
				$telefono,
				'',
				'',
				'',
				'',
				$idempresa,
				'PEN',
				'S/'
			);

			if ($rspta) {
				$sql_new = "SELECT idsucursal FROM sucursal ORDER BY idsucursal DESC LIMIT 1";
				$new_suc = ejecutarConsultaSimpleFila($sql_new);
				$idsucursal_new = $new_suc['idsucursal'];
				$sql_asignar = "INSERT INTO usuario_sucursal (idusuario, idsucursal) VALUES ('{$_SESSION['idusuario']}', '$idsucursal_new')";
				ejecutarConsulta($sql_asignar);
				$res = $usuario->seleccionarSucursal($idsucursal_new);
				if ($res) {
					$_SESSION['idsucursal'] = $res['idsucursal'];
					$_SESSION['nombre_impuesto'] = $res['nombre_impuesto'];
					$_SESSION['monto_impuesto'] = $res['monto_impuesto'];
					echo 'ok';
				} else {
					echo 'error al seleccionar';
				}
			} else {
				echo 'error al crear sucursal';
			}
		} else {
			echo 'error al crear empresa';
		}
		break;


}
?>