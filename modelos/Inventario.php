<?php
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";

class Inventario extends Helpers
{
	//Implementamos nuestro constructor
	public function __construct()
	{
		parent::__construct();
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$inventarios = (new DBQuery($this->pdo))
			->select('*')
			->from('inventarios')
			->orderBy('id', 'DESC')
			->get();

		$data = array();

		foreach ($inventarios as $reg) {
			$fecha_cierre = $reg['fecha_cierre'] ? date("d-m-Y h:i A", strtotime($reg['fecha_cierre'])) : null;

			// Definir estado
			$estado = $reg['fecha_cierre']
				? '<span class="badge badge-success">Cerrado</span>'
				: '<span class="badge badge-danger">Abierto</span>';

			$data[] = array(
				"0" => date("d-m-Y h:i A", strtotime($reg['fecha_apertura'])),
				"1" => $reg['observacion_apertura'],
				"2" => $reg['fecha_cierre'] ? $reg['fecha_cierre'] : "—",
				"3" => $reg['observacion_cierre'] ? $reg['observacion_cierre'] : "—",
				"4" => $estado, // 👈 Nueva columna de estado
				"5" =>
				'<button class="btn btn-warning btn-xs" onclick="ver(' . $reg['id'] . ')"><i class="fas fa-eye"></i></button>' .
					' <button class="btn btn-secondary btn-xs" onclick="mostrarform(true, ' . $reg['id'] . ', ' . $reg['sucursal_id'] . ', \'' . htmlspecialchars($fecha_cierre ?? '', ENT_QUOTES, 'UTF-8') . '\')"><i class="fa fa-cog"></i></button>' .
					' <button class="btn btn-primary btn-xs" onclick="editar(' . $reg['id'] . ', \'' . htmlspecialchars($reg['observacion_apertura'] ?? '', ENT_QUOTES, 'UTF-8') . '\')"><i class="fa fa-edit"></i></button>'
			);
		}

		$results = array(
			"sEcho" => 1,
			"iTotalRecords" => count($data),
			"iTotalDisplayRecords" => count($data),
			"aaData" => $data
		);

		return Response::json($results);
	}


	public function guardar($observacion_apertura, $sucursal, $usuario)
	{
		$fecha_apertura = date("Y-m-d H:i:s");
		try {
			$save = (new FluentSaver($this->pdo))
				->table('inventarios')
				->timestamps(false)
				->data([
					'fecha_apertura' => $fecha_apertura,
					'observacion_apertura' => $observacion_apertura,
					'usuario_id' => $usuario,
					'sucursal_id' => $sucursal
				])
				->save();
			if (!$save) {
				throw new Exception("Error al guardar el inventario");
			}
			return Response::json([
				'success' => true,
				'message' => 'Inventario guardado correctamente'
			]);
		} catch (\Throwable $th) {
			return Response::error($th->getMessage());
		}
	}

	//Implementamos un método para editar registros
	public function editar($idinventario_edit, $observacion_apertura)
	{
		try {
			$update = (new FluentSaver($this->pdo))
				->table('inventarios')
				->timestamps(false)
				->primaryKey('id')
				->data([
					'id' => $idinventario_edit,
					'observacion_apertura' => $observacion_apertura
				])
				->update();
			if (!$update) {
				throw new Exception("Error al editar el inventario");
			}
			return Response::json([
				'success' => true,
				'message' => 'Inventario editado correctamente'
			]);
		} catch (\Throwable $th) {
			return Response::error($th->getMessage());
		}
	}

	public function buscar_producto($nombre, $codigo, $categoria)
	{
		$query = (new DBQuery($this->pdo))
			->select('p.idproducto, p.nombre as producto, p.codigo, ip.stock, c.nombre as categoria, um.nombre as unidad_medida')
			->from('producto p')
			->join('categoria c', 'c.idcategoria = p.idcategoria')
			->join('unidad_medida um', 'p.idunidad_medida = um.idunidad_medida')
			->join('inventario_producto ip', 'ip.idproducto = p.idproducto')
			->where('p.condicion', '=', 1);

		if (!empty($nombre)) {
			$query->whereLike('p.nombre', $nombre);
		}

		if (!empty($codigo)) {
			$query->whereLike('p.codigo', $codigo);
		}

		$data = $query->get();

		return Response::json($data);
	}



	public function guardarRegistros($idinventario, $idproducto, $cantidad)
	{
		try {
			$this->pdo->beginTransaction();

			if ($idinventario === '') {
				throw new Exception("No se ha seleccionado inventario");
			}

			if (count($idproducto) == 0) {
				throw new Exception("Debe agregar al menos un producto");
			}

			$fecha_registro = date("Y-m-d H:i:s");

			for ($i = 0; $i < count($idproducto); $i++) {
				$id_prod = $idproducto[$i];
				$cant = $cantidad[$i];

				if ($cant == '' || $cant <= 0) continue;

				$inventario_producto = (new DBQuery($this->pdo))
					->select('stock')
					->from('inventario_producto')
					->where('idproducto', '=', $id_prod)
					->first();

				$stock = $inventario_producto['stock'] ?? 0;
				$diferencia = $cant - $stock;

				$inventario_detalle = (new DBQuery($this->pdo))
					->select('*')
					->from('inventario_detalles')
					->where('inventario_id', '=', $idinventario)
					->where('producto_id', '=', $id_prod)
					->first();

				if ($inventario_detalle) {
					// Actualizar detalle existente
					$save = (new FluentSaver($this->pdo))
						->table('inventario_detalles')
						->data([
							'cantidad' => $cant,
							'cantidad_real' => $stock,
							'diferencia' => $diferencia,
							'fecha_registro' => $fecha_registro,
						])
						->where('inventario_id', '=', $idinventario)
						->where('producto_id', '=', $id_prod)
						->update();
				} else {
					// Insertar nuevo detalle
					$save = (new FluentSaver($this->pdo))
						->table('inventario_detalles')
						->timestamps(false)
						->data([
							'inventario_id' => $idinventario,
							'producto_id' => $id_prod,
							'cantidad' => $cant,
							'cantidad_real' => $stock,
							'diferencia' => $diferencia,
							'fecha_registro' => $fecha_registro,
						])
						->save();
				}

				if (!$save) {
					throw new Exception("Error al guardar el detalle del inventario");
				}

				// Inventario temporales: insertar o actualizar
				$save = (new FluentSaver($this->pdo))
					->table('inventario_seleccionados')
					->timestamps(false)
					->data([
						'idinventario' => $idinventario,
						'idproducto' => $id_prod,
						'cantidad' => $cant
					])
					->upsert(['cantidad']);

				if (!$save) {
					throw new Exception("Error al guardar el producto seleccionado");
				}
			}

			$this->pdo->commit();

			return Response::json([
				'success' => true,
				'message' => 'Datos guardados correctamente'
			]);
		} catch (\Throwable $th) {
			if ($this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}

			return Response::error($th->getMessage());
		}
	}


	public function listarInventarios()
	{
		$data = (new DBQuery($this->pdo))
			->select('i.*,
	                (SELECT COUNT(*)
					FROM inventario_detalles ip 
					WHERE ip.inventario_id = i.id AND ip.estado = 0) AS pendientes')
			->from('inventarios i')
			->whereNotNull('i.fecha_cierre')
			->orderBy('i.id', 'DESC')
			->get();

		$options = '<option value="" selected>Seleccionar...</option>';
		foreach ($data as $reg) {
			if ($reg['pendientes'] > 0) {
				$estado = ' (<span style="color:red;">Por ajustar</span>)';
			} else {
				$estado = ' (<span style="color:green;">Ajustado</span>)';
			}
			$options .= '<option value="' . $reg['id'] . '">' . $reg['observacion_apertura'] . $estado . '</option>';
		}

		return $options;
	}


	public function buscarProductosInventario($idsucursal, $idinventario, $idcategoria, $tipo_ajuste)
	{
		$empty = [
			"draw" => (int) ($_GET['draw'] ?? 0),
			"recordsTotal" => 0,
			"recordsFiltered" => 0,
			"data" => [],
		];

		if ($idsucursal === '' || $idinventario === '') {
			return $empty;
		}

		// Total de registros del inventario sin filtros adicionales
		$total = (new DBQuery($this->pdo))
			->select('COUNT(*) AS total')
			->from('inventario_detalles')
			->where('inventario_id', '=', $idinventario)
			->first();

		$total = (int) ($total['total'] ?? 0);

		// Consulta principal
		$query = (new DBQuery($this->pdo))
			->select([
				'ip.producto_id',
				'p.nombre AS producto',
				'um.nombre AS unidad_medida',
				'ip.cantidad',
				'ip.cantidad_real',
				'ip.diferencia',
				'ip.estado'
			])
			->from('inventario_detalles ip')
			->join('producto p', 'p.idproducto = ip.producto_id')
			->join('unidad_medida um', 'um.idunidad_medida = p.idunidad_medida')
			->where('ip.inventario_id', '=', $idinventario);

		// Filtro por categoría
		if (!empty($idcategoria)) {
			$query->where('p.categoria_id', '=', $idcategoria);
		}

		// Filtro por tipo de ajuste
		if ($tipo_ajuste == '1') {
			$query->where('ip.diferencia', '>', 0);
		} elseif ($tipo_ajuste == '2') {
			$query->where('ip.diferencia', '<', 0);
		}

		$registros = $query->get();

		$data = [];
		$count = 1;

		foreach ($registros as $reg) {

			$producto_id = $reg['producto_id'];
			$diferencia = $reg['diferencia'];
			$estado = $reg['estado'];

			$checkbox = $estado == 1
				? '<label class="custom-check disabled">
                    <input type="checkbox" class="checkItem" value="' . $producto_id . '" data-dif="' . $diferencia . '" disabled />
                    <span class="checkmark"></span>
               </label>'
				: '<label class="custom-check">
                    <input type="checkbox" class="checkItem" value="' . $producto_id . '" data-dif="' . $diferencia . '" />
                    <span class="checkmark"></span>
               </label>';

			$icono = $estado
				? '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 50 50" style="fill:#40C057;">
                    <path d="M25 2C12.317 2 2 12.317 2 25s10.317 23 23 23 23-10.317 23-23c0-4.56-1.34-8.81-3.637-12.389l-1.369 1.617C45.895 15.382 47 19.06 47 23c0 11.579-9.421 21-21 21S5 34.579 5 23 14.421 2 26 2c5.443 0 10.394 2.1 14.129 5.51l1.309-1.545C37.353 2.262 32.936 0 27 0zM43.236 7.754 23.914 30.555l-8.133-7.586-1.363 1.463 9.666 9.016L44.763 9.047z"/>
                </svg>'
				: '';

			$data[] = [
				$checkbox,
				$count++,
				htmlspecialchars($reg['producto'] ?? '', ENT_QUOTES, 'UTF-8'),
				htmlspecialchars($reg['unidad_medida'] ?? '', ENT_QUOTES, 'UTF-8'),
				$reg['cantidad'],
				$reg['cantidad_real'],
				$reg['diferencia'],
				$icono
			];
		}

		return Response::json([
			"draw" => (int) ($_GET['draw'] ?? 0),
			"recordsTotal" => $total,
			"recordsFiltered" => count($data),
			"data" => $data
		]);
	}


	public function listarTiposAjuste($tipo = null)
	{
		$query = (new DBQuery($this->pdo))
			->select('*')
			->from('concepto_ajuste');

		if ($tipo) {
			$query->where('tipo', '=', $tipo);
		}
		$data = $query->get();

		$options = '<option value="" selected>Seleccionar...</option>';
		foreach ($data as $reg) {
			$options .= '<option value="' . $reg['id'] . '">' . $reg['nombre'] . '</option>';
		}

		return $options;
	}


	public function cerrarInventario($idinventario_cierre, $observacion_cierre)
	{
		try {
			$this->pdo->beginTransaction();
			$fecha_cierre = date("Y-m-d H:i:s");
			$delete_seleccionados = (new FluentSaver($this->pdo))
				->table('inventario_seleccionados')
				->where('idinventario', '=', $idinventario_cierre)
				->deleteWhere();

			if (!$delete_seleccionados) {
				throw new Exception("Error al eliminar seleccion del inventario");
			}

			$update = (new FluentSaver($this->pdo))
				->table('inventario_detalles')
				->timestamps(false)
				->primaryKey('id')
				->data([
					'id' => $idinventario_cierre,
					'observacion_cierre' => $observacion_cierre,
					'fecha_cierre' => $fecha_cierre
				])
				->update();

			if (!$update) {
				throw new Exception("Error al cerrar el inventario");
			}

			$this->pdo->commit();
			return Response::json([
				'success' => true,
				'message' => 'Datos guardados correctamente'
			]);
		} catch (\Throwable $th) {
			if ($this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}
			return Response::error($th->getMessage());
		}
	}


	public function ajustarInventario($idinventario, $idsucursal, $idtipo_ajuste, $fecha_ajuste, $idconcepto, $observacion_ajuste, $usuario_id, $productos = [])
	{
		try {
			$this->pdo->beginTransaction();

			$fecha_ajuste = !empty($fecha_ajuste) ? $fecha_ajuste : date("Y-m-d H:i:s");

			if (empty($productos)) {
				throw new Exception("Debe seleccionar al menos un producto para ajustar.");
			}

			// Validar tipo de ajuste
			if ($idtipo_ajuste == Constants::INGRESO_KARDEX) {
				$condicion = '>';
				$tipo_movimiento = Constants::MOVIMIENTO_INGRESO;
				$descripcion = 'Ingreso por ajuste de inventario';
			} elseif ($idtipo_ajuste == Constants::EGRESO_KARDEX) {
				$condicion = '<';
				$tipo_movimiento = Constants::MOVIMIENTO_EGRERSO;
				$descripcion = 'Salida por ajuste de inventario';
			} else {
				throw new Exception("Tipo de ajuste no válido.");
			}

			// Productos seleccionados
			$idsSeleccionados = array_fill_keys(
				array_map('strval', array_unique(array_map('intval', $productos))),
				true
			);

			// Verificar cabecera de ajuste existente
			$ajuste = (new DBQuery($this->pdo))
				->select('id')
				->from('ajuste_inventario')
				->where('inventario_id', '=', $idinventario)
				->first();

			$ajuste_existente = $ajuste['id'] ?? null;

			// Obtener productos con diferencias pendientes
			$query = (new DBQuery($this->pdo))
				->select('*')
				->from('inventario_detalles')
				->where('inventario_id', '=', $idinventario)
				->where('diferencia', $condicion, 0)
				->where('estado', '=', 0);

			$detalles = $query->get();

			// Filtrar únicamente los productos seleccionados
			$detalles = array_filter($detalles, function ($producto) use ($idsSeleccionados) {
				return isset($idsSeleccionados[(string) $producto['producto_id']]);
			});

			if (empty($detalles)) {
				throw new Exception("No hay diferencias en los productos seleccionados para ajustar.");
			}

			// Crear cabecera si no existe
			if (!$ajuste_existente) {

				$rowTotal = (new DBQuery($this->pdo))
					->select('COUNT(*) AS total')
					->from('ajuste_inventario')
					->first();

				$total = (int) ($rowTotal['total'] ?? 0) + 1;

				$ajuste_existente = (new FluentSaver($this->pdo))
					->table('ajuste_inventario')
					->timestamps(false)
					->data([
						'fecha_ajuste' => $fecha_ajuste,
						'numero' => $total,
						'serie' => 'A-00001',
						'observacion' => $observacion_ajuste,
						'inventario_id' => $idinventario,
						'sucursal_id' => $idsucursal,
						'usuario_id' => $usuario_id
					])
					->save();

				if (!$ajuste_existente) {
					throw new Exception("Error al crear ajuste de inventario.");
				}
			}

			// Recorrer productos
			foreach ($detalles as $producto) {

				$id_prod = $producto['producto_id'];
				$diferenciaAbs = abs((float) $producto['diferencia']);

				// Consultar stock actual
				$stockResultado = (new DBQuery($this->pdo))
					->select('ip.stock, p.precio, pg.idproducto_configuracion')
					->from('producto p')
					->join('inventario_producto ip', 'ip.idproducto=p.idproducto')
					->leftJoin('producto_configuracion pg', 'pg.idproducto=p.idproducto')
					->where('p.idproducto', '=', $id_prod)
					->where('p.idsucursal', '=', $idsucursal)
					->first();

				if (!$stockResultado) {
					continue;
				}

				$stock_actual = (float) $stockResultado['stock'];

				// Calcular nuevo stock
				if ($idtipo_ajuste == '1') {
					$nuevo_stock = $stock_actual + $diferenciaAbs;
				} else {
					$nuevo_stock = $stock_actual - $diferenciaAbs;
				}

				if ($nuevo_stock < 0) {
					throw new Exception("El producto {$id_prod} no tiene stock suficiente para realizar el ajuste.");
				}

				// Insertar detalle del ajuste
				$saveDetalle = (new FluentSaver($this->pdo))
					->table('detalle_ajuste_inventario')
					->timestamps(false)
					->data([
						'ajuste_inventario_id' => $ajuste_existente,
						'producto_id' => $id_prod,
						'cantidad_ajustada' => $diferenciaAbs,
						'stock_anterior' => $stock_actual,
						'stock_nuevo' => $nuevo_stock,
						'tipo_ajuste' => $idtipo_ajuste,
						'concepto_ajuste_id' => $idconcepto
					])
					->save();

				if (!$saveDetalle) {
					throw new Exception("Error en detalle de ajuste.");
				}

				// Registrar Kardex
				$saveKardex = Helpers::updateKardexSucursal(
					$idsucursal,
					$id_prod,
					$stockResultado['idproducto_configuracion'],
					$diferenciaAbs,
					1,
					$stockResultado['precio'] || 0,
					$nuevo_stock,
					$tipo_movimiento,
					$descripcion,
					'Inventarios'
				);

				if (!$saveKardex) {
					throw new Exception("Error al insertar en Kardex.");
				}

				// Actualizar stock del producto
				$updateStock = (new FluentSaver($this->pdo))
					->table('inventario_producto')
					->data([
						'stock' => $nuevo_stock
					])
					->where('idproducto', '=', $id_prod)
					->where('idsucursal', '=', $idsucursal)
					->update();

				if (!$updateStock) {
					throw new Exception("Error al actualizar stock.");
				}

				// Marcar producto como ajustado
				$updateInventario = (new FluentSaver($this->pdo))
					->table('inventario_detalles')
					->data([
						'estado' => 1
					])
					->where('producto_id', '=', $id_prod)
					->where('inventario_id', '=', $idinventario)
					->update();

				if (!$updateInventario) {
					throw new Exception("Error al actualizar estado del producto.");
				}
			}

			$this->pdo->commit();

			return true;
		} catch (\Throwable $e) {

			if ($this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}

			error_log("AJUSTE INVENTARIO ERROR: " . $e->getMessage());

			throw $e;
		}
	}


	public function resumenInventario($idsucursal, $idinventario)
	{
		// Datos del inventario
		$info = (new DBQuery($this->pdo))
			->select([
				'i.id',
				'i.fecha_apertura',
				'i.fecha_cierre',
				's.nombre AS sucursal',
				"IF(i.fecha_cierre IS NULL, 'Abierto', 'Cerrado') AS estado"
			])
			->from('inventarios i')
			->join('sucursal s', 's.idsucursal = i.sucursal_id')
			->where('i.id', '=', $idinventario)
			->where('s.idsucursal', '=', $idsucursal)
			->first();

		if (!$info) {
			return [
				"status" => false,
				"message" => "Inventario no encontrado"
			];
		}

		// Totales del inventario
		$totales = (new DBQuery($this->pdo))
			->select([
				'COUNT(*) AS total_productos',
				'SUM(CASE WHEN ip.diferencia > 0 THEN 1 ELSE 0 END) AS total_positivos',
				'SUM(CASE WHEN ip.diferencia < 0 THEN 1 ELSE 0 END) AS total_negativos'
			])
			->from('inventario_detalles ip')
			->where('ip.inventario_id', '=', $idinventario)
			->first();

		return Response::json([
			"status" => true,
			"inventario" => $idinventario,
			"sucursal" => $info["sucursal"],
			"estado" => $info["estado"],
			"fecha_apertura" => $info["fecha_apertura"],
			"fecha_cierre" => $info["fecha_cierre"],
			"total_productos" => (int) ($totales["total_productos"] ?? 0),
			"total_positivos" => (int) ($totales["total_positivos"] ?? 0),
			"total_negativos" => (int) ($totales["total_negativos"] ?? 0)
		]);
	}

	public function listarTemporales($idinventario)
	{
		$data = (new DBQuery($this->pdo))
			->select('s.idproducto, p.nombre, p.codigo, um.nombre as unidad, s.cantidad, ip.stock')
			->from('inventario_seleccionados s')
			->join('producto p', 's.idproducto = p.idproducto')
			->join('unidad_medida um', 'p.idunidad_medida = um.idunidad_medida')
			->join('inventario_producto ip', 'ip.idproducto = p.idproducto')
			->where('s.idinventario', '=', $idinventario)
			->get();

		return Response::json($data);
	}

	public function eliminar_temporal($idinventario, $idproducto)
	{
		try {
			$this->pdo->beginTransaction();
			if ($idinventario == '' || $idproducto == '') {
				throw new Exception("Faltan datos para eliminar");
			}

			(new FluentSaver($this->pdo))
				->table('inventario_detalles')
				->where('inventario_id', '=', $idinventario)
				->where('producto_id', '=', $idproducto)
				->deleteWhere();

			(new FluentSaver($this->pdo))
				->table('inventario_seleccionados')
				->where('idinventario', '=', $idinventario)
				->where('idproducto', '=', $idproducto)
				->deleteWhere();
			$this->pdo->commit();
			return Response::json([
				'success' => true,
				'message' => 'Producto eliminado correctamente'
			]);
		} catch (\Throwable $th) {
			if ($this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}

			return Response::error($th->getMessage());
		}
	}

	public function verInventario(int $id)
	{
		$inv = (new DBQuery($this->pdo))
			->select('*')
			->from('inventarios')
			->where('id', '=', $id)
			->first();

		if (!$inv) {
			return null;
		}

		// Traer productos de ese inventario
		$inv["productos"] = (new DBQuery($this->pdo))
			->select('ip.*, p.nombre as producto, um.nombre as unidad_medida')
			->from('inventario_detalles ip')
			->join('producto p', 'p.idproducto = ip.producto_id')
			->join('unidad_medida um', 'um.idunidad_medida = p.idunidad_medida')
			->where('ip.inventario_id', '=', $id)
			->get();

		return Response::json($inv);
	}
}
