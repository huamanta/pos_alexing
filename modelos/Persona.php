<?php
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";
require_once "../configuraciones/ConexionPdo.php";
require_once "../core/FluentQuery.php";
require_once "../core/FluentSave.php";
require_once __DIR__ . "/Helpers.php";

class Persona extends Helpers
{

	public function __construct()
	{
		parent::__construct();
	}

	//Implementamos un método para insertar registros
	public function insertar(
		$tipo_persona,
		$nombre,
		$tipo_documento,
		$num_documento,
		$direccion,
		$telefono,
		$email,
		$fecha_hora,
		$latitude = null,
		$longitude = null
	) {

		try {

			$this->pdo->beginTransaction();

			$id = (new FluentSaver($this->pdo))
				->table('persona')
				->nullable([
					'direccion',
					'telefono',
					'email',
					'latitude',
					'longitude',
					'fecha'
				])
				->data([

					'tipo_persona' => $tipo_persona,
					'nombre' => $nombre,
					'tipo_documento' => $tipo_documento,
					'num_documento' => $num_documento,
					'direccion' => $direccion,
					'telefono' => $telefono,
					'email' => $email,
					'fecha' => $fecha_hora,
					'latitude' => $latitude ?? '',
					'longitude' => $longitude ?? ''
				])
				->save();

			$this->pdo->commit();

			return json_encode(array("success" => true, "message" => "Datos registrados correctamente", "id" => $id));

		} catch (Throwable $e) {

			if (isset($this->pdo) && $this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}
			return json_encode(array("success" => false, "message" => "Error al guardar los datos: " . $e->getMessage()));
		}
	}

	//Implementamos un método para editar registros
	public function editar($idpersona, $tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $fecha_hora, $latitude, $longitude)
	{
		try {

			$this->pdo->beginTransaction();

			$update = (new FluentSaver($this->pdo))
				->table('persona')
				->primaryKey('idpersona')
				->nullable([
					'direccion',
					'telefono',
					'email',
					'latitude',
					'longitude',
					'fecha'
				])
				->data([
					'idpersona' => $idpersona,
					'tipo_persona' => $tipo_persona,
					'nombre' => $nombre,
					'tipo_documento' => $tipo_documento,
					'num_documento' => $num_documento,
					'direccion' => $direccion,
					'telefono' => $telefono,
					'email' => $email,
					'fecha' => $fecha_hora,
					'latitude' => $latitude,
					'longitude' => $longitude
				])
				->update();

			if (!$update) {
				throw new Exception("No se pudo actualizar el registro");
			}

			$this->pdo->commit();

			return json_encode(array("success" => true, "message" => "Datos actualizados correctamente", "id" => $update));

		} catch (Throwable $e) {

			if (isset($this->pdo) && $this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}
			return json_encode(array("success" => false, "message" => "Error al guardar los datos: " . $e->getMessage()));
		}
	}


	//Implementamos un método para eliminar categorías
	public function eliminar($idpersona)
	{
		try {

			$this->pdo->beginTransaction();

			$deleted = (new FluentSaver($this->pdo))
				->table('persona')
				->primaryKey('idpersona')
				->softDelete($idpersona);

			if (!$deleted) {
				throw new Exception("No se pudo eliminar el registro");
			}

			$this->pdo->commit();

			return json_encode([
				"success" => true,
				"message" => "Registro eliminado correctamente"
			]);

		} catch (Throwable $e) {

			if ($this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}

			return json_encode([
				"success" => false,
				"message" => "Error al eliminar los datos: " . $e->getMessage()
			]);
		}
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
		return (new DBQuery($this->pdo))
			->select('*')
			->from('persona')
			->softDeletes()
			->where('idpersona', '=', $idpersona)
			->first();
	}

	//Implementar un método para listar los registros
	public function listarp()
	{
		$page = (int) ($_GET['page'] ?? 1);
		$limit = (int) ($_GET['limit'] ?? 10);
		$search = trim($_GET['search'] ?? '');
		$response = (new DBQuery($this->pdo))
			->select('*')
			->from('persona')
			->softDeletes()
			->whereRaw(
				"(tipo_persona = :tipo OR isproveedor = :proveedor)",
				[
					'tipo' => 'Proveedor',
					'proveedor' => 1
				]
			)
			->search(
				$search,
				[
					'nombre',
					'num_documento',
					'telefono',
					'email'
				]
			)
			->orderBy('idpersona', 'DESC')
			->paginate(
				$page,
				$limit
			);
		$response['permissions'] = [
			'editar' => Helpers::getUserPermissionAccion('Editar proveedor'),
			'historial' => Helpers::getUserPermissionAccion('Historial proveedor'),
			'eliminar' => Helpers::getUserPermissionAccion('Eliminar proveedor')
		];
		return json_encode($response);
	}


	//Implementar un método para listar los registros 
	public function listarc($tipo_documento = "", $excluirId = false)
	{
		$page = (int) ($_GET['page'] ?? 1);
		$limit = (int) ($_GET['limit'] ?? 10);
		$search = trim($_GET['search'] ?? '');

		$paginator = (new DBQuery($this->pdo))
			->select('*')
			->from('persona')
			->where('tipo_persona', '=', 'Cliente');

		if ($tipo_documento !== '') {
			$paginator->where(
				"tipo_documento",
				"=",
				$tipo_documento
			);
		}

		if ($excluirId !== false) {
			$paginator->where(
				"idpersona",
				"<>",
				$excluirId
			);
		}

		$response = $paginator
			->softDeletes()
			->search(
				$search,
				[
					'nombre',
					'num_documento',
					'telefono',
					'email'
				]
			)
			->orderBy('idpersona', 'DESC')
			->paginate(
				$page,
				$limit
			);

		$response['permissions'] = [
			'editar' => Helpers::getUserPermissionAccion('Editar cliente'),
			'historial' => Helpers::getUserPermissionAccion('Historial cliente'),
			'puntuacion' => Helpers::getUserPermissionAccion('Puntuacion cliente'),
			'eliminar' => Helpers::getUserPermissionAccion('Eliminar cliente')
		];

		return json_encode($response);
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

	public function scoreCrediticioCliente($idcliente)
	{
		$credito = $this->obtenerDatosCredito($idcliente);

		if (!$credito || $credito['total_creditos'] == 0) {
			return json_encode(
				$this->respuestaSinHistorial()
			);
		}


		$seguimientos = $this->obtenerSeguimientos($idcliente);


		$porcentaje_pagado = $this->calcularPorcentajePago(
			$credito['deuda_total'],
			$credito['total_pagado']
		);

		$creditos = $this->obtenerCuotasCreditoCliente($idcliente);

		$moraCliente = $this->calcularMoraCliente($creditos);

		$score = $this->calcularScoreCredito(
			$credito,
			$seguimientos,
			$porcentaje_pagado,
			$moraCliente
		);


		$riesgo = $this->clasificarRiesgo($score);

		return json_encode([
			"score" => $score,
			"riesgo" => $riesgo['riesgo'],
			"color" => $riesgo['color'],

			...$credito,

			"mora_total" => round($moraCliente['mora_total'], 2),
			"porcentaje_pagado" => round($porcentaje_pagado, 2)
		]);
	}

	private function obtenerDatosCredito($idcliente)
	{
		return (new DBQuery($this->pdo))
			->query("
            SELECT
                COUNT(DISTINCT v.idventa) AS total_creditos,

                COALESCE(SUM(c.deudatotal),0) AS deuda_total,

                COALESCE(SUM(c.abonototal),0) AS total_pagado,


                -- cuotas pendientes vencidas actualmente
                COALESCE(
                    SUM(
                        CASE
                            WHEN c.estado_pago = 1
                            AND c.fechavencimiento < CURDATE()
                            THEN 1
                            ELSE 0
                        END
                    ),0
                ) AS cuotas_vencidas,


                -- cuotas pagadas después de vencer
                COALESCE(
                    SUM(
                        CASE
                            WHEN c.estado_pago = 0
                            AND p.ultimo_pago > c.fechavencimiento
                            THEN 1
                            ELSE 0
                        END
                    ),0
                ) AS cuotas_pagadas_tarde,


                -- días de atraso actual
                COALESCE(
                    MAX(
                        CASE
                            WHEN c.estado_pago = 1
                            AND c.fechavencimiento < CURDATE()
                            THEN DATEDIFF(
                                CURDATE(),
                                c.fechavencimiento
                            )
                            ELSE 0
                        END
                    ),0
                ) AS dias_atraso_actual,


                -- máximo atraso histórico
                COALESCE(
                    MAX(
                        CASE
                            WHEN c.estado_pago = 0
                            AND p.ultimo_pago > c.fechavencimiento
                            THEN DATEDIFF(
                                p.ultimo_pago,
                                c.fechavencimiento
                            )
                            ELSE 0
                        END
                    ),0
                ) AS dias_atraso_historico


            FROM venta v

            INNER JOIN cuentas_por_cobrar c
                ON c.idventa = v.idventa


            LEFT JOIN (
                SELECT
                    idcpc,
                    MAX(fechapago) AS ultimo_pago
                FROM detalle_cuentas_por_cobrar
                GROUP BY idcpc
            ) p
                ON p.idcpc = c.idcpc


            WHERE v.idcliente = :idcliente
            AND v.ventacredito = 'Si'
			AND c.idrefinanciamiento IS NULL
        ",
				[
					'idcliente' => $idcliente
				]
			)
			->first();
	}

	private function obtenerSeguimientos($idcliente)
	{
		return (new DBQuery($this->pdo))
			->select([
				'estado',
				'COUNT(*) AS cantidad'
			])
			->from('seguimiento_clientes')
			->where(
				'idcliente',
				'=',
				$idcliente
			)
			->whereNull('deleted_at')
			->groupBy('estado')
			->get();
	}

	private function calcularPorcentajePago(
		float $deuda,
		float $pagado
	) {
		if ($deuda <= 0) {
			return 0;
		}

		return ($pagado / $deuda) * 100;
	}

	private function calcularScoreCredito(
		array $credito,
		array $seguimientos,
		float $porcentaje_pagado,
		array $moraCliente
	) {

		$score = 0;


		// cuotas vencidas
		$cuotas = $credito['cuotas_vencidas'];

		if ($cuotas > 5) {
			$score += 30;
		} elseif ($cuotas > 2) {
			$score += 20;
		} elseif ($cuotas > 0) {
			$score += 10;
		}



		// atraso
		$dias = $credito['dias_atraso_historico'];

		if ($dias > 60) {
			$score += 40;
		} elseif ($dias > 30) {
			$score += 20;
		} elseif ($dias > 15) {
			$score += 10;
		}



		// mora
		if ($moraCliente['mora_total'] > 1000) {
			$score += 20;
		} elseif ($moraCliente['mora_total'] > 500) {
			$score += 10;
		}



		// pagos

		if ($porcentaje_pagado >= 90) {

			$score -= 20;

		} elseif ($porcentaje_pagado >= 70) {

			$score -= 10;

		} elseif ($porcentaje_pagado <= 30) {

			$score += 15;
		}



		// cantidad créditos

		$creditos = $credito['total_creditos'];

		if ($creditos >= 10) {

			$score -= 15;

		} elseif ($creditos >= 5) {

			$score -= 10;

		} elseif ($creditos >= 3) {

			$score -= 5;
		}



		// seguimientos

		foreach ($seguimientos as $seg) {

			switch ($seg['estado']) {

				case 'NO_RESPONDE':
					$score += $seg['cantidad'] * 5;
					break;


				case 'REPROGRAMADO':
					$score += $seg['cantidad'] * 2;
					break;


				case 'REALIZADO':
					$score -= $seg['cantidad'];
					break;
			}
		}


		return max(0, min(100, $score));
	}

	private function clasificarRiesgo($score)
	{

		if ($score <= 20) {

			return [
				"riesgo" => "BAJO",
				"color" => "success"
			];

		} elseif ($score <= 50) {

			return [
				"riesgo" => "MEDIO",
				"color" => "warning"
			];

		} elseif ($score <= 80) {

			return [
				"riesgo" => "ALTO",
				"color" => "danger"
			];

		}


		return [
			"riesgo" => "CRITICO",
			"color" => "dark"
		];
	}

	private function respuestaSinHistorial()
	{
		return [
			"score" => 0,
			"riesgo" => "SIN HISTORIAL",
			"color" => "secondary",
			"total_creditos" => 0,
			"cuotas_vencidas" => 0,
			"porcentaje_pagado" => 0,
			"deuda_total" => 0,
			"total_pagado" => 0,
			"cuotas_pagadas_tarde" => 0,
			"dias_atraso_actual" => 0,
			"dias_atraso_historico" => 0,
			"mora_total" => 0,
		];
	}

	private function obtenerCuotasCreditoCliente($idcliente)
	{
		return (new DBQuery($this->pdo))
			->query("
            SELECT
                c.idcpc,
                c.estado_pago,
                c.mora_pagada,
                c.fecha_update_mora,
                c.fechavencimiento,
                v.idsucursal

            FROM cuentas_por_cobrar c

            INNER JOIN venta v
                ON v.idventa = c.idventa

            WHERE v.idcliente = :idcliente
            AND c.idrefinanciamiento IS NULL
        ",
				[
					'idcliente' => $idcliente
				]
			)
			->get();
	}


	private function calcularMoraCliente(array $cuotas): array
	{
		$moraPagada = 0;
		$moraPendiente = 0;

		foreach ($cuotas as $cuota) {

			// Mora que ya fue pagada siempre queda como historial
			$moraPagada += (float) $cuota['mora_pagada'];


			// Verificar si la sucursal cobra mora
			$config = $this->verificarMoraCredito(
				$cuota['idsucursal']
			);


			if (!$config['activo']) {
				continue;
			}


			// Solo cuotas abiertas generan mora nueva
			if ((int) $cuota['estado_pago'] === 1) {


				$fechaInicio = !empty($cuota['fecha_update_mora'])
					? $cuota['fecha_update_mora']
					: $cuota['fechavencimiento'];


				$fechaInicio = new DateTime($fechaInicio);
				$hoy = new DateTime();


				if ($fechaInicio < $hoy) {

					$dias = $fechaInicio->diff($hoy)->days;


					$moraPendiente +=
						$dias * (float) $config['valor'];
				}
			}
		}


		return [
			'mora_pagada' => $moraPagada,
			'mora_pendiente' => $moraPendiente,
			'mora_total' => $moraPagada + $moraPendiente
		];
	}


}

?>