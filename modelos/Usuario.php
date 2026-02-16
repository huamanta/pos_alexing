<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');
Class Usuario
{

//Implementamos nuestro constructor
	public function __construct()
	{
	}

	//Implementamos un método para insertar registros
	public function insertar($idpersonal, $login, $clave, $idsucursal, $permisos, $subpermisos, $acciones)
{
    ejecutarConsulta("START TRANSACTION");
    $sw = true;

    $sql = "INSERT INTO usuario (idpersonal, login, clave, condicion) 
            VALUES ('$idpersonal','$login','$clave','1')";
    $idusuarionew = ejecutarConsulta_retornarID($sql);

    if (!$idusuarionew) { ejecutarConsulta("ROLLBACK"); return false; }

    // Insertar sucursales
    if (!empty($idsucursal)) {
        foreach ($idsucursal as $suc) {
            $sql_suc = "INSERT INTO usuario_sucursal(idusuario, idsucursal) VALUES('$idusuarionew', '$suc')";
            if (!ejecutarConsulta($sql_suc)) { $sw = false; break; }
        }
    }

    // Insertar permisos
    foreach ($permisos as $permiso) {
        $sql_detalle = "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES('$idusuarionew', '$permiso')";
        if (!ejecutarConsulta($sql_detalle)) { $sw = false; break; }
    }

    // Insertar subpermisos
    foreach ($subpermisos as $subpermiso) {
        $sql_permiso = "SELECT idpermiso FROM subpermiso WHERE idsubpermiso='$subpermiso' LIMIT 1";
        $permiso_result = ejecutarConsultaSimpleFila($sql_permiso);
        if ($permiso_result) {
            $idpermiso = $permiso_result['idpermiso'];
            $sql_detalle = "INSERT INTO usuario_permiso(idusuario, idpermiso, idsubpermiso)
                            VALUES('$idusuarionew', '$idpermiso', '$subpermiso')";
            if (!ejecutarConsulta($sql_detalle)) { $sw = false; break; }
        }
    }

    // Insertar acciones
    foreach ($acciones as $accion) {
        $sql_accion = "INSERT INTO usuario_accion(idusuario, idaccion_permiso) VALUES('$idusuarionew', '$accion')";
        if (!ejecutarConsulta($sql_accion)) { $sw = false; break; }
    }

    if ($sw) {
        ejecutarConsulta("COMMIT");
        return true;
    } else {
        ejecutarConsulta("ROLLBACK");
        return false;
    }
}


public function editar($idusuario, $idpersonal, $login, $clave, $idsucursal, $permisos, $subpermisos, $acciones)
{
    ejecutarConsulta("START TRANSACTION");
    $sw = true;

    // 1. Actualizar datos básicos del usuario (sin idsucursal)
    $sql = "UPDATE usuario 
            SET idpersonal='$idpersonal', login='$login', clave='$clave' 
            WHERE idusuario='$idusuario'";
    if (!ejecutarConsulta($sql)) {
        ejecutarConsulta("ROLLBACK");
        return false;
    }

    // 2. Limpiar sucursales anteriores
    if (!ejecutarConsulta("DELETE FROM usuario_sucursal WHERE idusuario='$idusuario'")) {
        ejecutarConsulta("ROLLBACK");
        return false;
    }

    // 3. Insertar las nuevas sucursales
    if (!empty($idsucursal)) {
        foreach ($idsucursal as $suc) {
            $sql_suc = "INSERT INTO usuario_sucursal(idusuario, idsucursal) VALUES('$idusuario', '$suc')";
            if (!ejecutarConsulta($sql_suc)) {
                ejecutarConsulta("ROLLBACK");
                return false;
            }
        }
    }

    // 4. Limpiar permisos y acciones previas
    if (!ejecutarConsulta("DELETE FROM usuario_permiso WHERE idusuario='$idusuario'")) {
        ejecutarConsulta("ROLLBACK");
        return false;
    }
    if (!ejecutarConsulta("DELETE FROM usuario_accion WHERE idusuario='$idusuario'")) {
        ejecutarConsulta("ROLLBACK");
        return false;
    }

    // 5. Insertar permisos principales
    if (!empty($permisos)) {
        foreach ($permisos as $permiso) {
            $sql_detalle = "INSERT INTO usuario_permiso (idusuario, idpermiso) VALUES('$idusuario', '$permiso')";
            if (!ejecutarConsulta($sql_detalle)) {
                $sw = false;
                break;
            }
        }
    }

    // 6. Insertar subpermisos
    if ($sw && !empty($subpermisos)) {
        foreach ($subpermisos as $subpermiso) {
            $sql_permiso = "SELECT idpermiso FROM subpermiso WHERE idsubpermiso = '$subpermiso' LIMIT 1";
            $permiso_result = ejecutarConsultaSimpleFila($sql_permiso);

            if ($permiso_result && isset($permiso_result['idpermiso'])) {
                $idpermiso = $permiso_result['idpermiso'];
                $sql_insert = "INSERT INTO usuario_permiso (idusuario, idpermiso, idsubpermiso)
                               VALUES('$idusuario', '$idpermiso', '$subpermiso')";
                if (!ejecutarConsulta($sql_insert)) {
                    $sw = false;
                    break;
                }
            }
        }
    }

    // 7. Insertar acciones
    if ($sw && !empty($acciones)) {
        foreach ($acciones as $accion) {
            $sql_accion = "INSERT INTO usuario_accion (idusuario, idaccion_permiso)
                           VALUES('$idusuario', '$accion')";
            if (!ejecutarConsulta($sql_accion)) {
                $sw = false;
                break;
            }
        }
    }

    // 8. Confirmar transacción
    if ($sw) {
        ejecutarConsulta("COMMIT");
        return true;
    } else {
        ejecutarConsulta("ROLLBACK");
        return false;
    }
}

public function listarSucursalesUsuario($idusuario)
{
    $sql = "SELECT idsucursal FROM usuario_sucursal WHERE idusuario='$idusuario'";
    return ejecutarConsulta($sql);
}

	//Implementamos un método para desactivar categorías
	public function desactivar($idusuario)
	{
		$sql="UPDATE usuario SET condicion='0' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($idusuario)
	{
		$sql="UPDATE usuario SET condicion='1' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	public function verificarUsuario($nombre){
		$sql="SELECT * FROM usuario WHERE login = '$nombre'";
		return ejecutarConsultaSimpleFila($sql);
	}
	
	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idusuario)
	{
		$sql="SELECT * FROM usuario WHERE idusuario='$idusuario'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
{
    $sql="SELECT a.idusuario,a.idpersonal,c.nombre as trabajador,a.login,a.condicion,
                 GROUP_CONCAT(s.nombre SEPARATOR ', ') as nombre
          FROM usuario a 
          INNER JOIN personal c ON a.idpersonal=c.idpersonal 
          LEFT JOIN usuario_sucursal us ON a.idusuario=us.idusuario
          LEFT JOIN sucursal s ON us.idsucursal=s.idsucursal
          GROUP BY a.idusuario";
    return ejecutarConsulta($sql);		
}


	//Implementar un método para listar los permisos marcados
	public function listarmarcados($idusuario)
	{
	    $sql = "SELECT idpermiso, idsubpermiso FROM usuario_permiso WHERE idusuario='$idusuario'";
	    return ejecutarConsulta($sql);
	}


	//Función para verificar el acceso al sistema
	public function verificar($login,$clave)
    {
    	$sql="SELECT a.idusuario,a.idpersonal,c.imagen,c.nombre as nombre,c.cargo,a.login,a.idsucursal FROM usuario a INNER JOIN personal c ON a.idpersonal=c.idpersonal WHERE a.login='$login' AND a.clave='$clave' AND a.condicion='1'"; 
    	return ejecutarConsulta($sql);
    }

    public function listarsubpermisos($idusuario)
	{
	    $sql = "SELECT sp.idpermiso, sp.nombre 
	            FROM usuario_permiso up
	            INNER JOIN subpermiso sp ON up.idsubpermiso = sp.idsubpermiso
	            WHERE up.idusuario = '$idusuario'";
	    return ejecutarConsulta($sql);
	}

	public function listaraccionesmarcadas($idusuario)
	{
	    $sql = "SELECT idaccion_permiso FROM usuario_accion WHERE idusuario = '$idusuario'";
	    return ejecutarConsulta($sql);
	}
	public function listaracciones($idusuario)
	{
	    $sql = "SELECT 
	                p.nombre AS modulo,
	                sp.nombre AS submodulo,
	                ap.nombre AS accion
	            FROM usuario_accion ua
	            INNER JOIN accion_permiso ap ON ua.idaccion_permiso = ap.idaccion_permiso
	            INNER JOIN subpermiso sp ON ap.idsubpermiso = sp.idsubpermiso
	            INNER JOIN permiso p ON sp.idpermiso = p.idpermiso
	            WHERE ua.idusuario = '$idusuario'";
	    return ejecutarConsulta($sql);
	}


    public function registrarHistorial($idusuario, $ip, $user_agent, $exito)
{
    $sql = "
        INSERT INTO login_historial (idusuario, ip, user_agent, exito, fecha)
        VALUES (?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 5 HOUR))
    ";

    $stmt = $GLOBALS['conexion']->prepare($sql);
    $stmt->bind_param("isss", $idusuario, $ip, $user_agent, $exito);
    return $stmt->execute();
}



    public function cerrarSesionesPrevias($idusuario)
{
    $sql = "UPDATE login_historial
            SET logout = NOW()
            WHERE idusuario = '$idusuario'
              AND exito = 1
              AND logout IS NULL";
    return ejecutarConsulta($sql);
}




}

?>