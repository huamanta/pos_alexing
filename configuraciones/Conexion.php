<?php
$conexion = null;
$host = env('DB_HOST');
$db = env('DB_DATABASE');
$user = env('DB_USERNAME');
$pass = env('DB_PASSWORD');
$db_encode = env('DB_ENCODE');

$conexion = new mysqli($host, $user, $pass, $db);

if (!$conexion) {
	die("Failed to connect to database");
}

mysqli_query($conexion, 'SET NAMES "' . $db_encode . '"');

//muestra posible error en la conexion
if (mysqli_connect_errno()) {
	printf("Falló en la conexion con la base de datos: %s\n", mysqli_connect_error());
	exit();
}

//metodo para ejecutar consulta
if (!function_exists('ejecutarConsulta')) {

	function ejecutarConsulta($sql)
	{
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
		$query = $conexion->query($sql);
		if (!$query) {
			error_log("Error en consulta: " . $conexion->error . " | SQL: " . $sql);
			return null;
		}
		$row = $query->fetch_assoc();
		return $row;
	}

	function ejecutarConsulta_retornarID($sql)
	{
		global $conexion;

		$query = $conexion->query($sql);

		if (!$query) {
			throw new Exception(
				"Error SQL: " . $conexion->error . "<br>Consulta: " . $sql
			);
		}

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
		$str = mysqli_real_escape_string($conexion, trim($str));
		return htmlspecialchars($str);
	}
}
?>