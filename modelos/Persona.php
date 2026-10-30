<?php
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";
require_once "../configuraciones/ConexionPdo.php";
require_once "../core/FluentQuery.php";
require_once "../core/FluentSave.php";
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";

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
			'editar' => Helpers::verificarPermisoCodigo('proveedor.editar'),
			'historial' => Helpers::verificarPermisoCodigo('proveedor.historial'),
			'eliminar' => Helpers::verificarPermisoCodigo('proveedor.eliminar')
		];
		return Response::json($response);
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
					'num_documento'
				]
			)
			->orderBy('idpersona', 'DESC')
			->paginate(
				$page,
				$limit
			);

		$response['permissions'] = [
			'editar' => Helpers::verificarPermisoCodigo('cliente.editar'),
			'historial' => Helpers::verificarPermisoCodigo('cliente.historial'),
			'puntuacion' => Helpers::verificarPermisoCodigo('cliente.puntuacion'),
			'eliminar' => Helpers::verificarPermisoCodigo('cliente.eliminar')
		];

		return Response::json($response);
	}

	public function listarTransportista()
	{
		$page = (int) ($_GET['page'] ?? 1);
		$limit = (int) ($_GET['limit'] ?? 10);
		$search = trim($_GET['search'] ?? '');

		$response = (new DBQuery($this->pdo))
			->select('*')
			->from('personal')
			->whereIn('cargo', ['Transportista', 'Otro'])
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
			->orderBy('idpersonal', 'DESC')
			->paginate(
				$page,
				$limit
			);

		return Response::json($response);
	}

	public function listarPersonal()
	{
		$page = (int) ($_GET['page'] ?? 1);
		$limit = (int) ($_GET['limit'] ?? 10);
		$search = trim($_GET['search'] ?? '');

		$response = (new DBQuery($this->pdo))
			->select('*')
			->from('personal')
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
			->orderBy('idpersonal', 'DESC')
			->paginate(
				$page,
				$limit
			);

		return Response::json($response);
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
		$seguimiento = $this->obtenerSeguimientosCrediticios($idcliente);

		if (!$credito || (int) $credito['total_creditos'] === 0) {
			return $this->respuestaSinHistorial();
		}

		$score = $this->calcularScoreCredito($credito, $seguimiento);
		$riesgo = $this->clasificarRiesgo($score);
		$alertas = $this->obtenerAlertas($credito, $seguimiento);

		$deudaTotal = (float) $credito['deuda_total'];
		$totalPagado = (float) $credito['total_pagado'];
		$porcentajePagado = $deudaTotal > 0
			? min(100, round(($totalPagado / $deudaTotal) * 100, 2))
			: 0;
		$porcentajePendiente = $deudaTotal > 0
			? min(100, round((((float) $credito['deuda_pendiente'] / $deudaTotal) * 100), 2))
			: 0;

		return [
			'score' => $score,
			'riesgo' => $riesgo['riesgo'],
			'color' => $riesgo['color'],
			'total_creditos' => (int) $credito['total_creditos'],
			'total_cuotas' => (int) $credito['total_cuotas'],
			'cuotas_pagadas' => (int) $credito['cuotas_pagadas'],
			'cuotas_pendientes' => (int) $credito['cuotas_pendientes'],
			'cuotas_vencidas' => (int) $credito['cuotas_vencidas'],
			'cuotas_pendientes_no_vencidas' => (int) $credito['cuotas_pendientes_no_vencidas'],
			'cuotas_pagadas_puntualmente' => (int) $credito['cuotas_pagadas_puntualmente'],
			'cuotas_pagadas_tarde' => (int) $credito['cuotas_pagadas_tarde'],
			'porcentaje_cumplimiento' => (float) $credito['porcentaje_cumplimiento'],
			'dias_atraso_historico' => (int) $credito['dias_atraso_historico'],
			'dias_atraso_actual' => (int) $credito['dias_atraso_actual'],
			'vencimiento_mas_antiguo' => $credito['vencimiento_mas_antiguo'],
			'vencimiento_mas_reciente' => $credito['vencimiento_mas_reciente'],
			'monto_total_creditos' => (float) $credito['monto_total_creditos'],
			'deuda_total' => round($deudaTotal, 2),
			'total_pagado' => round($totalPagado, 2),
			'monto_cuotas_vencidas' => (float) $credito['monto_cuotas_vencidas'],
			'monto_cuotas_pendientes_no_vencidas' => (float) $credito['monto_cuotas_pendientes_no_vencidas'],
			'mora_total' => (float) $credito['mora_total'],
			'mora_actual' => (float) $credito['mora_actual'],
			'porcentaje_pagado' => $porcentajePagado,
			'porcentaje_deuda_pagada' => $porcentajePagado,
			'porcentaje_deuda_pendiente' => $porcentajePendiente,
			'deuda_pendiente' => round((float) $credito['deuda_pendiente'], 2),
			'exposicion_crediticia' => round((float) $credito['deuda_pendiente'] + (float) $credito['mora_actual'], 2),
			'mora_crediticia' => round((float) $credito['mora_actual'], 2),
			'seguimientos_no_responde' => (int) $seguimiento['no_responde'],
			'seguimientos_reprogramados' => (int) $seguimiento['reprogramados'],
			'gestiones_realizadas' => (int) $seguimiento['gestiones'],
			'compromisos_totales' => (int) $seguimiento['compromisos_totales'],
			'compromisos_cumplidos' => (int) $seguimiento['compromisos_cumplidos'],
			'compromisos_incumplidos' => (int) $seguimiento['compromisos_incumplidos'],
			'alertas' => $alertas
		];
	}

	private function obtenerDatosCredito($idcliente)
	{
		return (new DBQuery($this->pdo))
			->query("
				SELECT
					COUNT(DISTINCT v.idventa) AS total_creditos,
					COUNT(DISTINCT c.idcpc) AS total_cuotas,
					SUM(CASE WHEN c.estado_pago = 0
						AND COALESCE(c.deuda, 0) <= 0
						AND COALESCE(c.mora, 0) <= 0
						AND p.effective_payments > 0 THEN 1 ELSE 0 END) AS cuotas_pagadas,
					SUM(CASE WHEN NOT (c.estado_pago = 0
						AND COALESCE(c.deuda, 0) <= 0
						AND COALESCE(c.mora, 0) <= 0
						AND p.effective_payments > 0) THEN 1 ELSE 0 END) AS cuotas_pendientes,
					SUM(CASE WHEN NOT (c.estado_pago = 0
						AND COALESCE(c.deuda, 0) <= 0
						AND COALESCE(c.mora, 0) <= 0
						AND p.effective_payments > 0)
						AND c.fechavencimiento < CURDATE()
						AND GREATEST(COALESCE(c.deuda, GREATEST(c.deudatotal - c.abonototal, 0)), 0) > 0
						THEN 1 ELSE 0 END) AS cuotas_vencidas,
					SUM(CASE WHEN NOT (c.estado_pago = 0
						AND COALESCE(c.deuda, 0) <= 0
						AND COALESCE(c.mora, 0) <= 0
						AND p.effective_payments > 0)
						AND c.fechavencimiento >= CURDATE()
						THEN 1 ELSE 0 END) AS cuotas_pendientes_no_vencidas,
					SUM(CASE WHEN c.estado_pago = 0
						AND COALESCE(c.deuda, 0) <= 0
						AND COALESCE(c.mora, 0) <= 0
						AND p.effective_payments > 0
						AND DATE(p.ultimo_pago) <= c.fechavencimiento THEN 1 ELSE 0 END) AS cuotas_pagadas_puntualmente,
					SUM(CASE WHEN c.estado_pago = 0
						AND COALESCE(c.deuda, 0) <= 0
						AND COALESCE(c.mora, 0) <= 0
						AND p.effective_payments > 0
						AND DATE(p.ultimo_pago) > c.fechavencimiento THEN 1 ELSE 0 END) AS cuotas_pagadas_tarde,
					COALESCE(MAX(CASE WHEN c.estado_pago = 0
						AND p.effective_payments > 0
						AND COALESCE(c.deuda, 0) <= 0
						AND COALESCE(c.mora, 0) <= 0
						AND DATE(p.ultimo_pago) > c.fechavencimiento
						THEN DATEDIFF(DATE(p.ultimo_pago), c.fechavencimiento) ELSE 0 END), 0) AS dias_atraso_historico,
					COALESCE(MAX(CASE WHEN NOT (c.estado_pago = 0
						AND COALESCE(c.deuda, 0) <= 0
						AND COALESCE(c.mora, 0) <= 0
						AND p.effective_payments > 0)
						AND c.fechavencimiento < CURDATE()
						THEN DATEDIFF(CURDATE(), c.fechavencimiento) ELSE 0 END), 0) AS dias_atraso_actual,
					MIN(CASE WHEN NOT (c.estado_pago = 0
						AND COALESCE(c.deuda, 0) <= 0
						AND COALESCE(c.mora, 0) <= 0
						AND p.effective_payments > 0) THEN c.fechavencimiento ELSE NULL END) AS vencimiento_mas_antiguo,
					MAX(CASE WHEN NOT (c.estado_pago = 0
						AND COALESCE(c.deuda, 0) <= 0
						AND COALESCE(c.mora, 0) <= 0
						AND p.effective_payments > 0) THEN c.fechavencimiento ELSE NULL END) AS vencimiento_mas_reciente,
					SUM(c.deudatotal) AS deuda_total,
					SUM(CASE WHEN NOT (c.estado_pago = 0 AND COALESCE(c.deuda,0) <= 0 AND COALESCE(c.mora,0) <= 0)
						THEN GREATEST(COALESCE(c.deuda, GREATEST(c.deudatotal-c.abonototal,0)),0)
						ELSE 0 END) AS deuda_pendiente,
					SUM(c.abonototal) AS total_pagado,
					SUM(CASE WHEN c.fechavencimiento < CURDATE()
						AND NOT (c.estado_pago = 0 AND COALESCE(c.deuda,0) <= 0 AND COALESCE(c.mora,0) <= 0)
						THEN GREATEST(COALESCE(c.deuda, GREATEST(c.deudatotal-c.abonototal,0)),0) ELSE 0 END) AS monto_cuotas_vencidas,
					SUM(CASE WHEN c.fechavencimiento >= CURDATE()
						AND NOT (c.estado_pago = 0 AND COALESCE(c.deuda,0) <= 0 AND COALESCE(c.mora,0) <= 0)
						THEN GREATEST(COALESCE(c.deuda, GREATEST(c.deudatotal-c.abonototal,0)),0) ELSE 0 END) AS monto_cuotas_pendientes_no_vencidas,
					CASE WHEN
						SUM(CASE WHEN c.estado_pago=0 AND COALESCE(c.deuda,0)<=0 AND COALESCE(c.mora,0)<=0 AND p.effective_payments>0 THEN 1 ELSE 0 END)>0
						THEN ROUND(
							SUM(CASE WHEN c.estado_pago=0 AND COALESCE(c.deuda,0)<=0 AND COALESCE(c.mora,0)<=0 AND p.effective_payments>0 AND DATE(p.ultimo_pago)<=c.fechavencimiento THEN 1 ELSE 0 END) * 100 /
							SUM(CASE WHEN c.estado_pago=0 AND COALESCE(c.deuda,0)<=0 AND COALESCE(c.mora,0)<=0 AND p.effective_payments>0 THEN 1 ELSE 0 END),
						2)
						ELSE 100 END AS porcentaje_cumplimiento,
					SUM(COALESCE(c.mora,0) + COALESCE(c.mora_pagada,0)) AS mora_total,
					SUM(COALESCE(c.mora,0)) AS mora_actual,
					COALESCE((SELECT SUM(x.total_venta) FROM venta x
						WHERE x.idcliente=:idcliente AND x.ventacredito='Si'
						AND x.deleted_at IS NULL), 0) AS monto_total_creditos
				FROM venta v
				INNER JOIN cuentas_por_cobrar c ON c.idventa=v.idventa AND c.deleted_at IS NULL
				LEFT JOIN (
					SELECT idcpc, MAX(fechapago) AS ultimo_pago,
						SUM(montopagado+montotarjeta) AS effective_payments
					FROM detalle_cuentas_por_cobrar
					WHERE deleted_at IS NULL
					GROUP BY idcpc
				) p ON p.idcpc=c.idcpc
				WHERE v.idcliente=:idcliente
					AND v.ventacredito='Si'
					AND v.deleted_at IS NULL
					AND c.condicion=1
					AND c.idrefinanciamiento_origen IS NULL
					AND c.estado_pago != 2
					AND (
						c.idrefinanciamiento IS NOT NULL
						AND c.idrefinanciamiento=(
							SELECT MAX(vigente.idrefinanciamiento)
							FROM cuentas_por_cobrar vigente
							WHERE vigente.idventa=c.idventa
								AND vigente.deleted_at IS NULL
								AND vigente.idrefinanciamiento IS NOT NULL
						)
						OR NOT EXISTS (
							SELECT 1
							FROM cuentas_por_cobrar vigente
							WHERE vigente.idventa=c.idventa
								AND vigente.deleted_at IS NULL
								AND vigente.idrefinanciamiento IS NOT NULL
						)
					)
			", ['idcliente' => $idcliente])->first();
	}

	private function obtenerSeguimientosCrediticios($idcliente)
	{
		$row = (new DBQuery($this->pdo))->query("
			SELECT
				COALESCE(SUM(CASE WHEN sc.estado='NO_RESPONDE' THEN 1 ELSE 0 END),0) AS no_responde,
				COALESCE(SUM(CASE WHEN sc.estado='REPROGRAMADO' THEN 1 ELSE 0 END),0) AS reprogramados,
				COUNT(sc.idseguimiento) AS gestiones
			FROM seguimiento_clientes sc
			WHERE sc.idcliente=:idcliente AND sc.deleted_at IS NULL
		", ['idcliente' => $idcliente])->first() ?: [
			'no_responde' => 0,
			'reprogramados' => 0,
			'gestiones' => 0
		];
		$compromisos = (new DBQuery($this->pdo))->query("
			SELECT
				COUNT(cp.idcompromiso_pago) AS compromisos_totales,
				COALESCE(SUM(CASE WHEN cp.fecha_cumplimiento IS NOT NULL
					AND DATE(cp.fecha_cumplimiento)<=cp.fecha_compromiso THEN 1 ELSE 0 END),0) AS compromisos_cumplidos,
				COALESCE(SUM(CASE WHEN cp.fecha_cumplimiento IS NULL
					AND cp.fecha_compromiso<CURDATE() THEN 1 ELSE 0 END),0) AS compromisos_incumplidos
			FROM compromiso_pago cp
			INNER JOIN cuentas_por_cobrar c ON c.idcpc=cp.idcpc AND c.deleted_at IS NULL
			INNER JOIN venta v ON v.idventa=c.idventa AND v.deleted_at IS NULL
			WHERE v.idcliente=:idcliente AND cp.deleted_at IS NULL
		", ['idcliente' => $idcliente])->first() ?: [];
		$row['compromisos_totales'] = (int) ($compromisos['compromisos_totales'] ?? 0);
		$row['compromisos_cumplidos'] = (int) ($compromisos['compromisos_cumplidos'] ?? 0);
		$row['compromisos_incumplidos'] = (int) ($compromisos['compromisos_incumplidos'] ?? 0);
		return $row;
	}

	private function getScoreWeights()
	{
		return [
			'overdue_one' => 12,
			'overdue_many' => 20,
			'overdue_severe' => 30,
			'historical_late' => 12,
			'historical_delay_30' => 6,
			'historical_delay_60' => 12,
			'mora_500' => 6,
			'mora_1000' => 12,
			'no_response' => 3,
			'no_response_recurrence' => 10,
			'reprogramacion' => 2,
			'reprogramacion_recurrence' => 8,
			'compromiso_incumplido' => 8
		];
	}

	private function calcularScoreCredito(array $credito, array $seguimiento)
	{
		$weights = $this->getScoreWeights();
		$score = 100;

		$overdue = (int) $credito['cuotas_vencidas'];
		if ($overdue > 5)
			$score -= $weights['overdue_severe'];
		elseif ($overdue >= 3)
			$score -= $weights['overdue_many'];
		elseif ($overdue >= 1)
			$score -= $weights['overdue_one'];

		$paidLate = (int) $credito['cuotas_pagadas_tarde'];
		$penaltyLate = max($weights['historical_late'] * $this->boundedRatio($paidLate, 3), 0);
		$totalPaid = (int) $credito['cuotas_pagadas'];
		$totalFinished = $totalPaid + $paidLate;
		$punctual = (int) $credito['cuotas_pagadas_puntualmente'];
		$compliance = $totalFinished > 0 ? round(($punctual / $totalFinished) * 100, 2) : 100;
		$penaltyCompliance = $compliance < 60 ? 15 : ($compliance < 80 ? 8 : ($compliance < 95 ? 4 : 0));
		$score -= max($penaltyLate, $penaltyCompliance);

		$days = (int) $credito['dias_atraso_historico'];
		if ($days > 60)
			$score -= $weights['historical_delay_60'];
		elseif ($days > 30)
			$score -= $weights['historical_delay_30'];

		$mora = (float) $credito['mora_actual'];
		if ($mora > 1000)
			$score -= $weights['mora_1000'];
		elseif ($mora > 500)
			$score -= $weights['mora_500'];

		$noResponse = (int) $seguimiento['no_responde'];
		$score -= min(12, $noResponse * $weights['no_response']);
		if ($noResponse >= 3)
			$score -= $weights['no_response_recurrence'];

		$reprogramado = (int) $seguimiento['reprogramados'];
		$score -= min(8, $reprogramado * $weights['reprogramacion']);
		if ($reprogramado >= 3)
			$score -= $weights['reprogramacion_recurrence'];

		$incumplidos = (int) $seguimiento['compromisos_incumplidos'];
		$score -= min(16, $incumplidos * $weights['compromiso_incumplido']);

		return max(0, min(100, $score));
	}

	private function boundedRatio($value, $base)
	{
		return min(1, $value / $base);
	}

	private function clasificarRiesgo($score)
	{
		if ($score >= 80)
			return ['riesgo' => 'BAJO', 'color' => 'success'];
		if ($score >= 60)
			return ['riesgo' => 'MODERADO', 'color' => 'info'];
		if ($score >= 40)
			return ['riesgo' => 'ALTO', 'color' => 'warning'];
		return ['riesgo' => 'CRITICO', 'color' => 'danger'];
	}

	private function obtenerAlertas(array $credito, array $seguimiento)
	{
		$alertas = [];
		if ((int) $credito['cuotas_vencidas'] > 0)
			$alertas[] = 'CUOTAS_VENCIDAS';
		if ((float) $credito['mora_actual'] > 0)
			$alertas[] = 'MORA_ACUMULADA';
		if ((int) $credito['cuotas_pagadas_tarde'] > 0)
			$alertas[] = 'ANTECEDENTES_INCUMPLIMIENTO';
		if ((int) $seguimiento['compromisos_incumplidos'] > 0)
			$alertas[] = 'COMPROMISOS_INCUMPLIDOS';
		return $alertas;
	}

	private function respuestaSinHistorial()
	{
		return [
			'score' => 50,
			'riesgo' => 'ALTO',
			'color' => 'warning',
			'total_creditos' => 0,
			'total_cuotas' => 0,
			'cuotas_pagadas' => 0,
			'cuotas_pendientes' => 0,
			'cuotas_vencidas' => 0,
			'cuotas_pendientes_no_vencidas' => 0,
			'cuotas_pagadas_puntualmente' => 0,
			'cuotas_pagadas_tarde' => 0,
			'porcentaje_cumplimiento' => 0,
			'dias_atraso_historico' => 0,
			'dias_atraso_actual' => 0,
			'vencimiento_mas_antiguo' => null,
			'vencimiento_mas_reciente' => null,
			'monto_total_creditos' => 0,
			'deuda_total' => 0,
			'deuda_pendiente' => 0,
			'total_pagado' => 0,
			'monto_cuotas_vencidas' => 0,
			'monto_cuotas_pendientes_no_vencidas' => 0,
			'mora_total' => 0,
			'mora_actual' => 0,
			'porcentaje_pagado' => 0,
			'porcentaje_deuda_pagada' => 0,
			'porcentaje_deuda_pendiente' => 0,
			'exposicion_crediticia' => 0,
			'mora_crediticia' => 0,
			'seguimientos_no_responde' => 0,
			'seguimientos_reprogramados' => 0,
			'gestiones_realizadas' => 0,
			'compromisos_totales' => 0,
			'compromisos_cumplidos' => 0,
			'compromisos_incumplidos' => 0,
			'alertas' => ['SIN_HISTORIA_CREDITICIA']
		];
	}


}

?>