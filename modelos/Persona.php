<?php
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";
require_once "../configuraciones/ConexionPdo.php";
require_once "../core/Paginanation.php";

class Persona
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_hora, $latitude, $longitude)
	{
		$sql = "INSERT INTO persona (tipo_persona,nombre,tipo_documento,num_documento,direccion,telefono,email,fecha, latitude, longitude)
		VALUES ('$tipo_persona','$nombre','$tipo_documento','$num_documento','$direccion','$telefono','$email','$fecha_hora', '$latitude', '$longitude')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idpersona, $tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_hora, $latitude, $longitude)
	{
		$sql = "UPDATE persona SET tipo_persona='$tipo_persona',nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email', fecha='$fecha_hora', latitude='$latitude', longitude='$longitude' WHERE idpersona='$idpersona'";
		return ejecutarConsulta($sql);
	}


	//Implementamos un método para eliminar categorías
	public function eliminar($idpersona)
	{

		$clienteExiste = "SELECT * FROM venta v WHERE v.idcliente = '$idpersona'";

		$existeCliente = ejecutarConsulta($clienteExiste);

		$var = 0;

		while ($reg = $existeCliente->fetch_object()) {

			$var = $reg->idventa;
		}

		if ($var > 0) {

			$sql = 2;

		} else {

			$sql = "DELETE FROM persona WHERE idpersona='$idpersona'";
			ejecutarConsulta($sql);
			$sql = 1;

		}

		return $sql;
	}

	//Implementamos un método para eliminar categorías
	public function eliminar2($idpersona)
	{

		$clienteExiste = "SELECT * FROM compra c WHERE c.idproveedor = '$idpersona'";

		$existeCliente = ejecutarConsulta($clienteExiste);

		$var = 0;

		while ($reg = $existeCliente->fetch_object()) {

			$var = $reg->idcompra;
		}

		if ($var > 0) {

			$sql = 2;

		} else {

			$sql = "DELETE FROM persona WHERE idpersona='$idpersona'";
			ejecutarConsulta($sql);
			$sql = 1;

		}

		return $sql;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idpersona)
	{
		$sql = "SELECT * FROM persona WHERE idpersona='$idpersona'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listarp()
	{
		$sql = "SELECT * FROM persona WHERE tipo_persona='Proveedor' OR isproveedor = 1";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros 
	public function listarc($tipo_documento = "", $excluirId = false)
	{
		$pdo = Conexion::conectar();

		$page = $_GET['page'] ?? 1;
		$limit = $_GET['limit'] ?? 10;
		$search = $_GET['search'] ?? '';


		$paginator = (new FluentPaginator($pdo))
			->query("
				SELECT *
				FROM persona
				WHERE tipo_persona = 'Cliente'
			");


		if (!empty($tipo_documento)) {

			$paginator->where("tipo_documento", "=", $tipo_documento);
		}


		if ($excluirId !== false) {
			$paginator->where("idpersona", "=>", $excluirId);
		}


		$response = $paginator
			->withSoftDeletes()
			->search(
				$search,
				[
					'nombre',
					'num_documento',
					'telefono',
					'email'
				]
			)
			->paginate(
				(int) $page,
				(int) $limit
			);


		echo json_encode($response);
	}


	public function obtenerPorId($idcliente)
	{
		$sql = "SELECT nombre FROM persona WHERE idpersona = '$idcliente' AND tipo_persona = 'Cliente'";
		return ejecutarConsulta($sql);
	}

	public function listarc2($numero)
	{
		$sql = "SELECT * FROM persona WHERE num_documento='$numero' AND tipo_persona = 'Cliente'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarc3($numero)
	{
		$sql = "SELECT * FROM persona WHERE num_documento='$numero' AND tipo_persona = 'Proveedor'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros 
	public function listarv()
	{
		$sql = "SELECT DISTINCT p.idpersonal, p.nombre
	            FROM personal p
	            INNER JOIN usuario u ON u.idpersonal = p.idpersonal
	            INNER JOIN usuario_sucursal us ON us.idusuario = u.idusuario
	            WHERE p.condicion = 1
	              AND u.condicion = 1";
		return ejecutarConsulta($sql);
	}


	// Método para obtener el cargo del personal
	public function obtenerCargo($idusuario)
	{
		$sql = "SELECT p.cargo 
            FROM personal p
            INNER JOIN usuario u ON u.idpersonal = p.idpersonal
            WHERE u.idusuario = '$idusuario'";
		$query = ejecutarConsulta($sql);

		if ($query && $reg = $query->fetch_object()) {
			return $reg->cargo;
		} else {
			return 'jj';
		}
	}

	public function listarvPorSucursal($idsucursal)
	{
		$sql = "SELECT DISTINCT p.idpersonal, p.nombre
	            FROM personal p
	            INNER JOIN usuario u ON u.idpersonal = p.idpersonal
	            INNER JOIN usuario_sucursal us ON us.idusuario = u.idusuario
	            WHERE us.idsucursal = '$idsucursal'
	              AND p.condicion = 1
	              AND u.condicion = 1";

		return ejecutarConsulta($sql);
	}


	public function listarProveedor()
	{
		$sql = "SELECT * FROM persona WHERE tipo_persona = 'proveedor'";
		return ejecutarConsulta($sql);
	}

	public function scorecrediticiocliente($idcliente)
	{
		$sql = "SELECT
                COUNT(DISTINCT v.idventa) AS total_creditos,

                SUM(c.deudatotal) AS deuda_total,

                SUM(c.abonototal) AS total_pagado,

                SUM(c.mora) AS mora_total,

                SUM(
                    CASE
                        WHEN c.estado_pago <> 1
                        AND c.fechavencimiento < CURDATE()
                        THEN 1
                        ELSE 0
                    END
                ) AS cuotas_vencidas,

                MAX(
                    CASE
                        WHEN c.estado_pago <> 1
                        THEN DATEDIFF(CURDATE(), c.fechavencimiento)
                        ELSE 0
                    END
                ) AS dias_atraso

            FROM venta v

            INNER JOIN cuentas_por_cobrar c
                ON c.idventa = v.idventa

            WHERE v.idcliente = '$idcliente'
            AND v.ventacredito = 'Si'";

		$data = ejecutarConsultaSimpleFila($sql);

		if (!$data) {

			return json_encode([
				"score" => 0,
				"riesgo" => "SIN HISTORIAL"
			]);
		}

		$score = 0;

		$total_creditos = (int) $data['total_creditos'];
		$deuda_total = (float) $data['deuda_total'];
		$total_pagado = (float) $data['total_pagado'];
		$mora_total = (float) $data['mora_total'];
		$cuotas_vencidas = (int) $data['cuotas_vencidas'];
		$dias_atraso = (int) $data['dias_atraso'];


		if ($total_creditos === 0) {

			return json_encode([
				"score" => 0,
				"riesgo" => "SIN HISTORIAL",
				"color" => "secondary",
				"total_creditos" => 0,
				"cuotas_vencidas" => 0,
				"dias_atraso" => 0,
				"mora_total" => 0,
				"porcentaje_pagado" => 0
			]);
		}

		/*
		|--------------------------------------------------------------------------
		| Cuotas vencidas (0 - 30)
		|--------------------------------------------------------------------------
		*/

		if ($cuotas_vencidas <= 0) {
			$score += 0;
		} elseif ($cuotas_vencidas <= 2) {
			$score += 10;
		} elseif ($cuotas_vencidas <= 5) {
			$score += 20;
		} else {
			$score += 30;
		}

		/*
		|--------------------------------------------------------------------------
		| Días de atraso (0 - 40)
		|--------------------------------------------------------------------------
		*/

		if ($dias_atraso <= 0) {
			$score += 0;
		} elseif ($dias_atraso <= 15) {
			$score += 10;
		} elseif ($dias_atraso <= 30) {
			$score += 20;
		} elseif ($dias_atraso <= 60) {
			$score += 30;
		} else {
			$score += 40;
		}

		/*
		|--------------------------------------------------------------------------
		| Mora acumulada (0 - 20)
		|--------------------------------------------------------------------------
		*/

		if ($mora_total > 1000) {
			$score += 20;
		} elseif ($mora_total > 500) {
			$score += 10;
		}

		/*
		|--------------------------------------------------------------------------
		| Porcentaje pagado (-20 a +15)
		|--------------------------------------------------------------------------
		*/

		$porcentaje_pagado = 0;

		if ($deuda_total > 0) {

			$porcentaje_pagado =
				($total_pagado / $deuda_total) * 100;
		}

		if ($porcentaje_pagado >= 90) {

			$score -= 20;

		} elseif ($porcentaje_pagado >= 70) {

			$score -= 10;

		} elseif ($porcentaje_pagado <= 30) {

			$score += 15;
		}

		/*
		|--------------------------------------------------------------------------
		| Historial de créditos (-15)
		|--------------------------------------------------------------------------
		*/

		if ($total_creditos >= 10) {

			$score -= 15;

		} elseif ($total_creditos >= 5) {

			$score -= 10;

		} elseif ($total_creditos >= 3) {

			$score -= 5;
		}

		/*
		|--------------------------------------------------------------------------
		| Seguimientos de cobranza
		|--------------------------------------------------------------------------
		*/

		$sqlSeguimiento = "SELECT
                            estado,
                            COUNT(*) cantidad
                        FROM seguimiento_clientes
                        WHERE idcliente = '$idcliente'
                        AND deleted_at IS NULL
                        GROUP BY estado";

		$seguimientos = ejecutarConsulta($sqlSeguimiento);

		while ($seg = $seguimientos->fetch_object()) {

			switch ($seg->estado) {

				case 'NO_RESPONDE':
					$score += ($seg->cantidad * 5);
					break;

				case 'REPROGRAMADO':
					$score += ($seg->cantidad * 2);
					break;

				case 'REALIZADO':
					$score -= ($seg->cantidad * 1);
					break;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Limitar score
		|--------------------------------------------------------------------------
		*/

		if ($score < 0) {
			$score = 0;
		}

		if ($score > 100) {
			$score = 100;
		}

		/*
		|--------------------------------------------------------------------------
		| Clasificación
		|--------------------------------------------------------------------------
		*/

		if ($score <= 20) {

			$riesgo = "BAJO";
			$color = "success";

		} elseif ($score <= 50) {

			$riesgo = "MEDIO";
			$color = "warning";

		} elseif ($score <= 80) {

			$riesgo = "ALTO";
			$color = "danger";

		} else {

			$riesgo = "CRITICO";
			$color = "dark";
		}

		return json_encode([
			"score" => $score,
			"riesgo" => $riesgo,
			"color" => $color,
			"total_creditos" => $total_creditos,
			"cuotas_vencidas" => $cuotas_vencidas,
			"dias_atraso" => $dias_atraso,
			"mora_total" => round($mora_total, 2),
			"porcentaje_pagado" => round($porcentaje_pagado, 2)
		]);
	}

}

?>