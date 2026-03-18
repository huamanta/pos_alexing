<?php 
require_once "local.php";

$conexion=new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);

mysqli_query($conexion, 'SET NAMES "'.DB_ENCODE.'"');

//muestra posible error en la conexion
if (mysqli_connect_errno()) {
	printf("Falló en la conexion con la base de datos: %s\n",mysqli_connect_error());
	exit();
}

//metodo para ejecutar consulta
if (!function_exists('ejecutarConsulta')) 
{

	function ejecutarConsulta($sql) {
		global $conexion;

		$result = $conexion->query($sql);

		if (!$result) {
			throw new Exception($conexion->error); // 👈 ERROR REAL
		}

		return $result;
	}

	function ejecutarConsultaSimpleFila($sql)
	{
		global $conexion;
		$query=$conexion->query($sql);
		$row=$query->fetch_assoc();
		return $row;
	}

	function ejecutarConsulta_retornarID($sql)
	{
		global $conexion;
		$query=$conexion->query($sql);
		return $conexion->insert_id;
	}

	// NUEVAS FUNCIONES PARA SENTENCIAS PREPARADAS
	function ejecutarConsultaPreparada($sql, $types, $params)
	{
		global $conexion;
		$stmt = $conexion->prepare($sql);
		if ($stmt === false) {
			error_log("Error al preparar la consulta: " . $conexion->error);
			return false;
		}
		// Usar call_user_func_array para bind_param
		$a_params = array();
		$a_params[] = &$types;
		for ($i = 0; $i < count($params); $i++) {
			$a_params[] = &$params[$i];
		}
		call_user_func_array(array($stmt, 'bind_param'), $a_params);
		
		if (!$stmt->execute()) {
			error_log("Error al ejecutar la consulta preparada: " . $stmt->error);
			return false;
		}
		return $stmt->get_result(); // Para SELECT
	}

	function ejecutarConsultaSimpleFilaPreparada($sql, $types, $params)
	{
		global $conexion;
		$stmt = $conexion->prepare($sql);
		if ($stmt === false) {
			error_log("Error al preparar la consulta simple fila preparada: " . $conexion->error);
			return false;
		}
		$a_params = array();
		$a_params[] = &$types;
		for ($i = 0; $i < count($params); $i++) {
			$a_params[] = &$params[$i];
		}
		call_user_func_array(array($stmt, 'bind_param'), $a_params);

		if (!$stmt->execute()) {
			error_log("Error al ejecutar la consulta simple fila preparada: " . $stmt->error);
			return false;
		}
		$result = $stmt->get_result();
		$row = $result ? $result->fetch_assoc() : null;
		$stmt->close();
		return $row;
	}

	function ejecutarConsulta_retornarIDPreparada($sql, $types, $params)
	{
		global $conexion;
		$stmt = $conexion->prepare($sql);
		if ($stmt === false) {
			error_log("Error al preparar la consulta para retornar ID: " . $conexion->error);
			return false;
		}
		$a_params = array();
		$a_params[] = &$types;
		for ($i = 0; $i < count($params); $i++) {
			$a_params[] = &$params[$i];
		}
		call_user_func_array(array($stmt, 'bind_param'), $a_params);

		if (!$stmt->execute()) {
			error_log("Error al ejecutar la consulta preparada (retornar ID): " . $stmt->error);
			return false;
		}
		$insert_id = $conexion->insert_id;
		$stmt->close();
		return $insert_id;
	}

	function limpiarCadena($str)
	{
		global $conexion;
		$str=mysqli_real_escape_string($conexion,trim($str));
		return htmlspecialchars($str);
	}
}
?>