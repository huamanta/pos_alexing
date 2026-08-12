<?php
//Incluímos inicialmente la conexión a la base de datos
require_once __DIR__ . "/../configuraciones/Conexion.php";
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/FluentQuery.php";
require_once __DIR__ . '/../core/Response.php';

class Cajachica extends Helpers
{
	//Implementamos nuestro constructor
	public function __construct()
	{
		parent::__construct();
	}

	public function resumenBancos($idsucursal)
	{
		$cuentas = $this->resumenBancosCuentasCobrar($idsucursal);
		$ventas = $this->resumenBancosVentas($idsucursal);
		return Response::json([
			'resumen' => $this->calcularResumen($cuentas, $ventas),
			'cuentasxcobrar' => $cuentas,
			'ventas' => $ventas,

			'totales' => [
				'cuentasxcobrar' => array_sum(array_column($cuentas, 'total')),
				'ventas' => array_sum(array_column($ventas, 'total'))
			]
		]);
	}

	private function calcularResumen(array $cuentas, array $ventas): array
	{
		$datos = [];

		foreach (array_merge($cuentas, $ventas) as $item) {

			$forma = strtoupper(trim($item['forma_pago']));
			$forma = str_replace('Ó', 'O', $forma);

			if (!isset($datos[$forma])) {
				$datos[$forma] = [
					'cantidad' => 0,
					'total' => 0
				];
			}

			$datos[$forma]['cantidad'] += (int) $item['cantidad'];
			$datos[$forma]['total'] += (float) $item['total'];
		}

		$efectivo = 0;
		$transferencias = 0;
		$depositos = 0;
		$tarjetas = 0;

		foreach ($datos as $forma => $valor) {

			switch ($forma) {

				case 'EFECTIVO':
					$efectivo += $valor['total'];
					break;

				case 'TRANSFERENCIA':
					$tarjetas += $valor['total'];
					break;

				case 'YAPE':
					$transferencias += $valor['total'];
					break;

				case 'PLIN':
					$transferencias += $valor['total'];
					break;

				case 'DEPOSITO':
					$depositos += $valor['total'];
					break;

				case 'TARJETA':
					$tarjetas += $valor['total'];
					break;

				case 'VISA':
					$tarjetas += $valor['total'];
					break;

				case 'MASTERCARD':
					$tarjetas += $valor['total'];
					break;
			}
		}
		$operaciones = array_sum(array_column($datos, 'cantidad'));
		$total = array_sum(array_column($datos, 'total'));

		$promedio = $operaciones > 0
			? round($total / $operaciones, 2)
			: 0;

		return [
			'total' => round(array_sum(array_column($datos, 'total')), 2),
			'efectivo' => round($efectivo, 2),
			'transferencias' => round($transferencias, 2),
			'depositos' => round($depositos, 2),
			'tarjetas' => round($tarjetas, 2),
			'operaciones' => $operaciones,
			'promedio' => $promedio,
			'promedio_str' => Helpers::get_currency_symbol($promedio),
			// Formateado para mostrar directamente
			'total_str' => Helpers::get_currency_symbol(array_sum(array_column($datos, 'total'))),
			'efectivo_str' => Helpers::get_currency_symbol($efectivo),
			'transferencias_str' => Helpers::get_currency_symbol($transferencias),
			'depositos_str' => Helpers::get_currency_symbol($depositos),
			'tarjetas_str' => Helpers::get_currency_symbol($tarjetas),
		];
	}

	public function resumenBancosCuentasCobrar($idsucursal)
	{
		$movimientos = (new DBQuery($this->pdo))
			->select("
            dcpc.formapago,
            b.nombre AS banco,
            dcpc.montopagado,
            dcpc.montotarjeta
        ")
			->from("detalle_cuentas_por_cobrar dcpc")
			->join("cajas c", "dcpc.idcaja = c.idcaja")
			->leftJoin('bancos b', "b.idbanco = dcpc.idbanco")
			->where("c.idsucursal", "=", $idsucursal)
			->whereNull("dcpc.deleted_at")
			->get();

		$resumen = [];

		foreach ($movimientos as $item) {

			// EFECTIVO
			if ((float) $item['montopagado'] > 0) {

				if (!isset($resumen['EFECTIVO'])) {
					$resumen['EFECTIVO'] = [
						'forma_pago' => 'EFECTIVO',
						'banco' => '',
						'cantidad' => 0,
						'total' => 0
					];
				}

				$resumen['EFECTIVO']['cantidad']++;
				$resumen['EFECTIVO']['total'] += (float) $item['montopagado'];
			}

			// Otros medios de pago
			if ((float) $item['montotarjeta'] > 0) {

				$formaPago = strtoupper(trim($item['formapago']));
				$banco = strtoupper(trim($item['banco'] ?? ''));

				// Solo Transferencia y Depósito se agrupan por banco
				if ($banco !== '') {
					$key = $formaPago . '_' . $banco;
					$nombreBanco = $banco;
				} else {
					$key = $formaPago;
					$nombreBanco = '';
				}

				if (!isset($resumen[$key])) {
					$resumen[$key] = [
						'forma_pago' => $formaPago,
						'banco' => $nombreBanco,
						'cantidad' => 0,
						'total' => 0
					];
				}

				$resumen[$key]['cantidad']++;
				$resumen[$key]['total'] += (float) $item['montotarjeta'];
			}
		}

		foreach ($resumen as &$item) {
			$item['total'] = round($item['total'], 2);
			$item['total_str'] = Helpers::get_currency_symbol($item['total']);
		}

		unset($item);

		return array_values($resumen);
	}

	public function resumenBancosVentas($idsucursal)
	{
		$ventas = (new DBQuery($this->pdo))
			->select("
            	vp.metodo_pago AS formapago,
				b.nombre AS banco,
				vp.monto
			")
			->from("venta v")
			->leftJoin("venta_pago vp", "vp.idventa = v.idventa")
			->leftJoin("bancos b", "b.idbanco = vp.idbanco")
			->where("v.idsucursal", "=", $idsucursal)
			->where("v.estado", "<>", "Anulado")
			->whereNull("v.deleted_at")
			->get();

		$resumen = [];

		foreach ($ventas as $item) {
			// Otros medios de pago
			if ((float) $item['monto'] > 0) {

				$formaPago = strtoupper(trim($item['formapago'] ?? ''));
				$banco = strtoupper(trim($item['banco'] ?? ''));

				// Solo Transferencia y Depósito se separan por banco
				if ($banco !== '') {
					$key = $formaPago . '_' . $banco;
					$nombreBanco = $banco;
				} else {
					$key = $formaPago;
					$nombreBanco = '';
				}

				if (!isset($resumen[$key])) {
					$resumen[$key] = [
						'forma_pago' => $formaPago,
						'banco' => $nombreBanco,
						'cantidad' => 0,
						'total' => 0
					];
				}

				$resumen[$key]['cantidad']++;
				$resumen[$key]['total'] += (float) $item['monto'];
			}
		}

		foreach ($resumen as &$item) {
			$item['total'] = round($item['total'], 2);
			$item['total_str'] = Helpers::get_currency_symbol($item['total']);
		}

		unset($item);

		return array_values($resumen);
	}

	public function resumenComprobantes($idsucursal)
	{
		$comprobantes = (new DBQuery($this->pdo))
			->select("
            cp.nombre AS tipo_comprobante,
            v.ventacredito,
            COUNT(*) AS cantidad,
            SUM(v.total_venta) AS total
        ")
			->from("venta v")
			->join("comp_pago cp", "cp.idcomprobante_pago = v.idcomprobante_pago")
			->where("v.idsucursal", "=", $idsucursal)
			->where("v.estado", "<>", "Anulado")
			->whereNull("v.deleted_at")
			->groupBy("cp.nombre, v.ventacredito")
			->orderBy("cp.nombre", "ASC")
			->orderBy("v.ventacredito", "ASC")
			->get();

		$resumen = [
			'contado' => [
				'cantidad' => 0,
				'total' => 0
			],
			'credito' => [
				'cantidad' => 0,
				'total' => 0
			],
			'comprobantes' => [
				'cantidad' => 0,
				'total' => 0
			]
		];

		foreach ($comprobantes as &$item) {

			$item['cantidad'] = (int) $item['cantidad'];
			$item['total'] = round($item['total'], 2);
			$item['total_str'] = Helpers::get_currency_symbol($item['total']);

			$resumen['comprobantes']['cantidad'] += $item['cantidad'];
			$resumen['comprobantes']['total'] += $item['total'];

			if (strtoupper(trim($item['ventacredito'])) === 'SI') {

				$resumen['credito']['cantidad'] += $item['cantidad'];
				$resumen['credito']['total'] += $item['total'];

			} else {

				$resumen['contado']['cantidad'] += $item['cantidad'];
				$resumen['contado']['total'] += $item['total'];
			}
		}

		unset($item);

		$resumen['contado']['total'] = round($resumen['contado']['total'], 2);
		$resumen['credito']['total'] = round($resumen['credito']['total'], 2);
		$resumen['comprobantes']['total'] = round($resumen['comprobantes']['total'], 2);

		$resumen['contado']['total_str'] = Helpers::get_currency_symbol($resumen['contado']['total']);
		$resumen['credito']['total_str'] = Helpers::get_currency_symbol($resumen['credito']['total']);
		$resumen['comprobantes']['total_str'] = Helpers::get_currency_symbol($resumen['comprobantes']['total']);

		return Response::json([
			'resumen' => $resumen,
			'comprobantes' => array_values($comprobantes)
		]);
	}

	//Implementamos un método para insertar registros
	public function insertar($tipo, $idsucursal, $idpersonal, $montoEfectivo, $descripcion, $formapago, $montoDeposito, $noperacion, $idconcepto_movimiento, $idusuario, $banco, $fechaDeposito)
	{
		try {
			$caja = Helpers::cajaAperturada($idsucursal, $idusuario);
			$save = (new FluentSaver($this->pdo))
				->table('movimiento')
				->nullable([
					'idbanco',
					'idpersonal',
					'noperacion',
					'totaldeposito',
					'fecha',
				])
				->data([
					'tipo' => $tipo,
					'idcaja' => $caja['idcaja'],
					'idsucursal' => $idsucursal,
					'idpersonal' => $idpersonal ?? null,
					'idusuario' => $idusuario,
					'totalefectivo' => $montoEfectivo,
					'descripcion' => $descripcion,
					'formapago' => $formapago,
					'idbanco' => $banco ?? null,
					'totaldeposito' => $montoDeposito ?? null,
					'noperacion' => $noperacion ?? null,
					'idconcepto_movimiento' => $idconcepto_movimiento,
					'fecha' => $fechaDeposito ?? null,
				])
				->save();

			if (!$save) {
				throw new Exception("Movimiento no se pudo guardar");
			}

			// sumar monto de tajeta si viene tarjeta 
			if ($montoDeposito > 0 && !empty($banco)) {
				if ($tipo == 'Egresos') {
					$sumarBanco = Helpers::restarBanco($banco, $montoDeposito);
				} else {
					$sumarBanco = Helpers::incrementarBanco($banco, $montoDeposito);
				}
				if (!$sumarBanco) {
					throw new Exception("Error al ingrmentar/restar saldo banco");
				}
			}

			if ($montoEfectivo > 0) {
				$caja = Helpers::cajaAperturada($idsucursal, $idusuario);

				if (!$caja) {
					throw new Exception("No existe una caja abierta para el usuario.");
				}
				if ($tipo == 'Egresos') {
					$sumarCaja = Helpers::restarCajaApertura($caja['aperturacajaid'], $montoEfectivo);
				} else {

					$sumarCaja = Helpers::incrementarCajaApertura($caja['aperturacajaid'], $montoEfectivo);
				}
				if (!$sumarCaja) {
					throw new Exception("Error al incrementar/restar el efectivo de la caja.");
				}
			}

			return Response::json(['success' => true, 'message' => 'Movimiento registrado correctamente']);
		} catch (Exception $e) {
			return Response::error($e->getMessage());
		}
	}

	public function editar($idmovimiento, $tipo, $idcaja, $idsucursal, $idpersonal, $monto, $descripcion, $formapago, $totaldeposito, $noperacion, $idconcepto_movimiento, $idusuario)
	{
		try {
			$update = (new FluentSaver($this->pdo))
				->table('movimiento')
				->primaryKey('idmovimiento')
				->data([
					'idmovimiento' => $idmovimiento,
					'tipo' => $tipo,
					'idcaja' => $idcaja,
					'idsucursal' => $idsucursal,
					'idpersonal' => $idpersonal,
					'totalefectivo' => $monto,
					'descripcion' => $descripcion,
					'formapago' => $formapago,
					'totaldeposito' => $totaldeposito,
					'noperacion' => $noperacion,
					'idconcepto_movimiento' => $idconcepto_movimiento,
				])
				->update();

			if (!$update) {
				throw new Exception("Movimiento no se pudo actualizar");
			}

			return Response::json(['success' => true, 'message' => 'Movimiento actualizado']);

		} catch (Exception $e) {
			return Response::error($e->getMessage());
		}
	}


	public function listar($fecha_inicio, $fecha_fin, $idsucursal)
	{
		$page = $_GET['page'] ?? 1;
		$limit = $_GET['limit'] ?? 20;
		$search = trim($_GET['search'] ?? '');

		$query = (new DBQuery($this->pdo))
			->select("m.*,cm.descripcion as concepto_descripcion, cm.tipo as concepto_tipo, cm.categoria_concepto")
			->from("movimiento m")
			->join("concepto_movimiento cm", "m.idconcepto_movimiento = cm.idconcepto_movimiento")
			->softDeletes('m.deleted_at')
			->where("m.idsucursal", "=", $idsucursal)
			->orderBy("m.idmovimiento", "DESC");

		if ($fecha_inicio && $fecha_fin) {
			$query->whereBetween("DATE(m.fecha)", $fecha_inicio, $fecha_fin);
		}

		if ($search !== '') {
			$query->search($search, [
				'm.descripcion',
				'm.formapago',
				'm.tipo',
			]);
		}

		$response = $query->paginate($page, $limit);

		return Response::json($response);
	}


	public function listaar($fecha_inicio, $fecha_fin, $idsucursal)
	{
		$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
		$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
		$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
		$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

		$condicion = "WHERE DATE(fecha) >= '$fecha_inicio' AND DATE(fecha) <= '$fecha_fin'";

		if ($idsucursal !== "Todos") {
			$condicion .= " AND idsucursal = '$idsucursal'";
		}

		if (!empty($search)) {
			$condicion .= " AND (
            descripcion LIKE '%$search%' OR
            formapago LIKE '%$search%' OR
            tipo LIKE '%$search%' OR
            totalefectivo LIKE '%$search%' OR
            totaldeposito LIKE '%$search%'
        )";
		}

		// Total de registros filtrados
		$sql_total = "SELECT COUNT(*) as total FROM movimiento $condicion";
		$rspta_total = ejecutarConsultaSimpleFila($sql_total);
		$total = $rspta_total["total"];

		// Consulta con paginación
		$sql = "SELECT m.*,cm.* FROM movimiento m
						INNER JOIN concepto_movimiento cm ON m.idconcepto_movimiento = cm.idconcepto_movimiento 
						$condicion ORDER BY m.idmovimiento DESC LIMIT $start, $length";
		$rspta = ejecutarConsulta($sql);

		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => $reg->fecha,
				"1" => $reg->descripcion,
				"2" => (strtolower($reg->tipo) == 'egresos') ?
					'<span class="badge bg-danger">EGRESO</span>' :
					'<span class="badge bg-success">INGRESO</span>',
				"3" => $reg->formapago,
				"4" => $reg->totalefectivo,
				"5" => $reg->totaldeposito,
				"6" => '<div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
            <i class="fa fa-list-ul"></i> <span class="caret"></span>
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" style="cursor:pointer;" onclick="mostrar(' . $reg->idmovimiento . ')">Editar</a>
            <a class="dropdown-item" style="cursor:pointer;" onclick="eliminar(' . $reg->idmovimiento . ')">Eliminar</a>

            <div class="dropdown-divider"></div>

            <a class="dropdown-item text-primary" style="cursor:pointer;" 
               onclick="abrirRecibo(' . $reg->idmovimiento . ')">
               <i class="fa fa-print"></i> Ver Recibo
            </a>
          </div>
        </div>',
			);
		}

		return array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $data
		);
	}


	public function eliminar($idmovimiento)
	{
		try {
			$deleted = (new FluentSaver($this->pdo))
				->table('movimiento')
				->primaryKey('idmovimiento')
				->softDelete($idmovimiento);

			if (!$deleted) {
				throw new Exception("No se pudo eliminar el registro");
			}


			return Response::json([
				"success" => true,
				"message" => "Registro eliminado correctamente"
			]);

		} catch (Throwable $e) {
			return Response::error($e->getMessage());
		}
	}

	public function mostrar($idmovimiento)
	{
		$sql = "SELECT * FROM movimiento WHERE idmovimiento='$idmovimiento'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function coceptoMovimiento($tipo)
	{
		$sql = "SELECT * FROM concepto_movimiento WHERE tipo='$tipo' AND estado='1'";
		return ejecutarConsulta($sql);
	}


	public function listarConceptos()
	{
		$page = $_GET['page'] ?? 1;
		$limit = $_GET['limit'] ?? 20;
		$search = trim($_GET['search'] ?? '');

		$query = (new DBQuery($this->pdo))
			->select("*")
			->from("concepto_movimiento")
			->orderBy("idconcepto_movimiento", "DESC");

		if ($search !== '') {
			$query->search($search, [
				'descripcion',
				'tipo',
				'categoria_concepto'
			]);
		}

		$response = $query->paginate($page, $limit);

		return Response::json($response);
	}

	public function listarConceptos2()
	{
		$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
		$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
		$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
		$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

		$condicion = "";

		if (!empty($search)) {
			$search = str_replace("'", "", $search); // seguridad básica
			$condicion .= "(
        descripcion LIKE '%$search%' OR
        tipo LIKE '%$search%' OR
        categoria_concepto LIKE '%$search%'
    )";
		}

		// Agregar WHERE si hay condición
		$condicion_sql = !empty($condicion) ? "WHERE $condicion" : "";

		// Total filtrado
		$sql_total = "SELECT COUNT(*) as total FROM concepto_movimiento $condicion_sql";
		$rspta_total = ejecutarConsultaSimpleFila($sql_total);
		$total = $rspta_total["total"] ?? 0;

		// Consulta principal con paginación
		$sql = "SELECT * FROM concepto_movimiento $condicion_sql 
        ORDER BY idconcepto_movimiento DESC 
        LIMIT $start, $length";

		$rspta = ejecutarConsulta($sql);
		$rspta = ejecutarConsulta($sql);

		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => $reg->descripcion,
				"1" => ($reg->tipo == 'egresos') ? '<span class="badge bg-danger">EGRESO</span>' :
					'<span class="badge bg-success">INGRESO</span>',
				"2" => $reg->categoria_concepto,
				"3" => '<div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-list-ul"></i> <span class="caret"></span>
                      </button>
                      <div class="dropdown-menu">
            <a class="dropdown-item" style="cursor:pointer;" onclick=\'mostrarConcepto(' . json_encode($reg) . ')\'>Editar</a>
            <a class="dropdown-item" style="cursor:pointer;" onclick="eliminarConcepto(' . $reg->idconcepto_movimiento . ')">Eliminar</a>
          </div>
                    </div>',
			);
		}

		return array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $data
		);
	}

	public function insertarConcepto($descripcion, $tipo, $categoria_concepto)
	{
		try {
			$save = (new FluentSaver($this->pdo))
				->table('concepto_movimiento')
				->data([
					'descripcion' => $descripcion,
					'tipo' => $tipo,
					'categoria_concepto' => $categoria_concepto,
					'estado' => 1
				])
				->save();

			if (!$save) {
				throw new Exception("Concepto movimiento no se pudo guardar");
			}

			return Response::json(['success' => true, 'message' => 'Concepto movimiento registrada']);
		} catch (Exception $e) {
			return Response::error($e->getMessage());
		}
	}

	public function editarConcepto($idconcepto_movimiento, $descripcion, $tipo, $categoria_concepto)
	{
		try {
			$update = (new FluentSaver($this->pdo))
				->table('concepto_movimiento')
				->primaryKey('idconcepto_movimiento')
				->data([
					'idconcepto_movimiento' => $idconcepto_movimiento,
					'descripcion' => $descripcion,
					'tipo' => $tipo,
					'categoria_concepto' => $categoria_concepto
				])
				->update();

			if (!$update) {
				throw new Exception("Concepto movimiento no se pudo actualizar");
			}

			return Response::json(['success' => true, 'message' => 'Concepto movimiento actualizado']);

		} catch (Exception $e) {
			return Response::error($e->getMessage());
		}
	}

	function guardarPagoDiario($tipo, $idcaja, $idsucursal, $idpersonal, $monto, $descripcion, $formapago, $totaldeposito, $noperacion, $idconcepto_movimiento, $idasistencia)
	{
		if (!$idcaja) {
			return false; // Tipo inválido
		}
		$idpersonal_sql = ($idpersonal === '' || $idpersonal === null) ? "NULL" : "'$idpersonal'";
		$sql = "INSERT INTO movimiento (tipo,idcaja,idsucursal,idpersonal,totalefectivo,descripcion, formapago, totaldeposito, noperacion, idconcepto_movimiento)
		VALUES ('$tipo','$idcaja','$idsucursal',$idpersonal_sql,'$monto','$descripcion', '$formapago', '$totaldeposito', '$noperacion', '$idconcepto_movimiento')";
		ejecutarConsulta($sql);

		$sql_asistencia = "UPDATE asistencias SET estado_pago='1', totalefectivo='$monto' WHERE idasistencia='$idasistencia'";
		return ejecutarConsulta($sql_asistencia);
	}

	public function obtenerIdConceptoAdelanto()
	{
		$sql = "SELECT idconcepto_movimiento 
	            FROM concepto_movimiento
	            WHERE descripcion LIKE '%adelanto%'
	            LIMIT 1";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarAdelantos($idpersonal, $desde, $hasta)
	{
		// obtener id dinámico
		$id = $this->obtenerIdConceptoAdelanto();
		$id_adelanto = $id['idconcepto_movimiento'];

		$sql = "SELECT fecha, descripcion, totalefectivo, totaldeposito 
            FROM movimiento
            WHERE idpersonal='$idpersonal'
            AND idconcepto_movimiento = '$id_adelanto'
            AND DATE(fecha) BETWEEN '$desde' AND '$hasta'
            ORDER BY fecha ASC";

		return ejecutarConsulta($sql);
	}

	public function listarIngresosSemana($idpersonal, $desde, $hasta)
	{

		$sql = "SELECT fecha, descripcion, totalefectivo, totaldeposito
            FROM movimiento
            WHERE idpersonal = '$idpersonal'
            AND tipo = 'Ingresos'
            AND DATE(fecha) BETWEEN '$desde' AND '$hasta'
            ORDER BY fecha ASC";

		return ejecutarConsulta($sql);
	}

	public function reporteAdelantos($desde, $hasta)
	{
		$detalle = $this->listarAdelantosPorFechas($desde, $hasta);
		$diasTrabajados = $this->listarDiasTrabajadosPorFechas($desde, $hasta);

		$total = 0;
		foreach ($detalle as &$item) {
			$item['monto'] = round($item['monto'], 2);
			$item['monto_str'] = Helpers::get_currency_symbol($item['monto']);
			$total += $item['monto'];
		}
		unset($item);

		$dias = [];

		foreach ($diasTrabajados as $reg) {

			$trabajador = $reg['trabajador'];

			if (!isset($dias[$trabajador])) {
				$dias[$trabajador] = [
					'trabajador' => $trabajador,
					'dias' => 0,
					'monto_dia' => $reg['monto_dia'],
					'total_pago' => 0,
					'fechas' => []
				];
			}

			$dias[$trabajador]['dias']++;
			$dias[$trabajador]['total_pago'] += $reg['monto_dia'];
			$dias[$trabajador]['monto_dia'] = $reg['monto_dia'];

			$dias[$trabajador]['fechas'][] = [
				'fecha' => $reg['fecha'],
				'monto' => $reg['monto_dia']
			];
		}

		foreach ($dias as &$item) {
			$item['monto_dia'] = round($item['monto_dia'], 2);
			$item['monto_dia_str'] = Helpers::get_currency_symbol($item['monto_dia']);

			$item['total_pago'] = round($item['total_pago'], 2);
			$item['total_pago_str'] = Helpers::get_currency_symbol($item['total_pago']);
		}
		unset($item);

		return Response::json([
			'detalle' => $detalle,
			'total' => round($total, 2),
			'total_str' => Helpers::get_currency_symbol($total),
			'dias' => array_values($dias)
		]);
	}

	public function listarAdelantosPorFechas($desde, $hasta)
	{
		$id = $this->obtenerIdConceptoAdelanto();
		$id_adelanto = $id['idconcepto_movimiento'];

		$query = (new DBQuery($this->pdo))
			->select("
            DATE_FORMAT(m.created_at, '%d/%m/%Y %h:%i %p') AS fecha,
            m.descripcion,
            (m.totalefectivo + m.totaldeposito) AS monto,
            p.nombre AS trabajador
        ")
			->from("movimiento m")
			->leftJoin("personal p", "p.idpersonal = m.idpersonal")
			->where("m.idconcepto_movimiento", "=", $id_adelanto);

		if (!empty($desde) && !empty($hasta)) {
			$query->whereBetween("DATE(m.created_at)", $desde, $hasta);
		}

		return $query
			->orderBy("m.created_at", "ASC")
			->get();
	}

	public function listarDiasTrabajadosPorFechas($desde, $hasta)
	{
		$query = (new DBQuery($this->pdo))
			->select("
            p.nombre AS trabajador,
            DATE(a.fecha) AS fecha,
            SUM(a.monto) AS monto_dia
        ")
			->from("asistencias a")
			->leftJoin("personal p", "p.idpersonal = a.idpersonal")
			->where("a.estado", "=", "asistio");

		if (!empty($desde) && !empty($hasta)) {
			$query->whereBetween("DATE(a.fecha)", $desde, $hasta);
		}

		return $query
			->groupBy("p.idpersonal")
			->groupBy("DATE(a.fecha)")
			->orderBy("p.nombre", "ASC")
			->orderBy("fecha", "ASC")
			->get();
	}

}
