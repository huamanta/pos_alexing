<?php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/../core/Response.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class Conciliaciones extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listarBancos(): void
    {
        $idsucursal = (int) ($_SESSION['idsucursal'] ?? 0);
        $bancos = (new DBQuery($this->pdo))
            ->select(['idbanco', 'nombre', 'descripcion', 'cuenta', 'cci', 'saldo'])
            ->from('bancos')
            ->softDeletes()
            ->orderBy('nombre', 'ASC')
            ->get();

        Response::json(['success' => true, 'data' => $bancos]);
    }

    public function listar(): void
    {
        $idsucursal = (int) ($_SESSION['idsucursal'] ?? 0);
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = max(1, min(100, (int) ($_GET['limit'] ?? 20)));
        $search = trim((string) ($_GET['search'] ?? ''));

        $query = (new DBQuery($this->pdo))
            ->select([
                'c.*',
                'b.nombre AS banco_nombre',
                'b.cuenta AS banco_cuenta',
                'b.cci AS banco_cci',
            ])
            ->from('conciliaciones_bancarias c')
            ->join('bancos b', 'b.idbanco = c.idbanco')
            ->where('c.idsucursal', '=', $idsucursal)
            ->whereNull('c.deleted_at')
            ->orderBy('c.created_at', 'DESC');

        if ($search !== '') {
            $query->search($search, ['b.nombre', 'b.cuenta', 'c.estado']);
        }

        Response::json($query->paginate($page, $limit));
    }

    public function crear(array $data): void
    {
        try {
            if (!Helpers::verificarPermisoCodigo('conciliaciones.crear')) {
                throw new RuntimeException('No tiene permiso para crear conciliaciones.');
            }

            $idbanco = (int) ($data['idbanco'] ?? 0);
            $inicio = $this->validarFecha($data['periodo_inicio'] ?? '', 'Periodo inicio');
            $fin = $this->validarFecha($data['periodo_fin'] ?? '', 'Periodo fin');
            $saldoInicial = $this->validarDecimal($data['saldo_inicial'] ?? 0, 'Saldo inicial');

            if ($idbanco < 1 || $fin < $inicio) {
                throw new InvalidArgumentException('Seleccione un banco y un periodo valido.');
            }

            $idsucursal = (int) ($_SESSION['idsucursal'] ?? 0);
            $idusuario = (int) ($_SESSION['idusuario'] ?? 0);
            $periodoInicio = $inicio->format('Y-m-d');
            $periodoFin = $fin->format('Y-m-d');

            $conflicto = (new DBQuery($this->pdo))
                ->select(['idconciliacion', 'periodo_inicio', 'periodo_fin'])
                ->from('conciliaciones_bancarias')
                ->where('idbanco', '=', $idbanco)
                ->where('idsucursal', '=', $idsucursal)
                ->where('estado', '=', 'cerrada')
                ->where('periodo_inicio', '<=', $periodoFin)
                ->where('periodo_fin', '>=', $periodoInicio)
                ->first();

            if ($conflicto) {
                throw new RuntimeException('Ya existe una conciliacion cerrada que se superpone con este periodo.');
            }

            $this->pdo->beginTransaction();

            $id = (new FluentSaver($this->pdo))
                ->table('conciliaciones_bancarias')
                ->primaryKey('idconciliacion')
                ->data([
                    'idbanco' => $idbanco,
                    'idsucursal' => $idsucursal,
                    'idusuario_creador' => $idusuario,
                    'periodo_inicio' => $periodoInicio,
                    'periodo_fin' => $periodoFin,
                    'saldo_inicial' => $this->decimalString($saldoInicial),
                    'estado' => 'borrador',
                    'observaciones' => trim((string) ($data['observaciones'] ?? '')),
                ])
                ->save();

            $this->auditar($id, $idusuario, 'crear', [
                'idbanco' => $idbanco,
                'periodo_inicio' => $periodoInicio,
                'periodo_fin' => $periodoFin,
            ]);

            $this->pdo->commit();
            Response::success(['idconciliacion' => $id], 'Conciliacion creada correctamente.');
        } catch (\Throwable $th) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            Response::error($th->getMessage(), 400);
        }
    }

    public function mostrar(int $idconciliacion): void
    {
        $idsucursal = (int) ($_SESSION['idsucursal'] ?? 0);
        $conciliacion = (new DBQuery($this->pdo))
            ->select([
                'c.*',
                'b.nombre AS banco_nombre',
                'b.descripcion AS banco_descripcion',
                'b.cuenta AS banco_cuenta',
                'b.cci AS banco_cci',
            ])
            ->from('conciliaciones_bancarias c')
            ->join('bancos b', 'b.idbanco = c.idbanco')
            ->where('c.idconciliacion', '=', $idconciliacion)
            ->where('c.idsucursal', '=', $idsucursal)
            ->first();

        if (!$conciliacion) {
            Response::error('Conciliacion no encontrada.', 404);
        }

        $movimientos = (new DBQuery($this->pdo))
            ->select('*')
            ->from('conciliacion_movimientos_bancarios mb')
            ->join('conciliacion_importaciones i', 'i.idimportacion = mb.idimportacion')
            ->where('i.idconciliacion', '=', $idconciliacion)
            ->orderBy('mb.fecha_operacion', 'ASC')
            ->get();

        Response::json([
            'success' => true,
            'conciliacion' => $conciliacion,
            'movimientos_bancarios' => $movimientos,
            'movimientos_sistema' => $this->movimientosSistema(
                (int) $conciliacion['idbanco'],
                $conciliacion['periodo_inicio'],
                $conciliacion['periodo_fin']
            ),
        ]);
    }

    public function importar(int $idconciliacion, array $mapping): void
    {
        try {
            if (!Helpers::verificarPermisoCodigo('conciliaciones.importar')) {
                throw new RuntimeException('No tiene permiso para importar estados de cuenta.');
            }

            if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
                throw new InvalidArgumentException('Seleccione un archivo valido.');
            }

            $archivo = $_FILES['archivo'];
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, ['csv', 'xls', 'xlsx'], true)) {
                throw new InvalidArgumentException('Solo se permiten archivos CSV, XLS o XLSX.');
            }
            if ((int) $archivo['size'] > 5 * 1024 * 1024) {
                throw new InvalidArgumentException('El archivo supera el limite de 5 MB.');
            }

            $conciliacion = $this->obtenerConciliacionSucursal($idconciliacion);
            if (!$this->conciliacionEditable($conciliacion)) {
                throw new RuntimeException('No se puede modificar esta conciliacion.');
            }

            $rutaTemporal = $archivo['tmp_name'];
            $hashArchivo = hash_file('sha256', $rutaTemporal);

            $existente = (new DBQuery($this->pdo))
                ->select(['idimportacion'])
                ->from('conciliacion_importaciones')
                ->where('idconciliacion', '=', $idconciliacion)
                ->where('hash_archivo', '=', $hashArchivo)
                ->first();
            if ($existente) {
                throw new RuntimeException('Este archivo ya fue importado en la conciliacion.');
            }

            $spreadsheet = IOFactory::load($rutaTemporal);
            $worksheet = $spreadsheet->getActiveSheet();
            $filas = $worksheet->toArray(null, true, true, false);
            if (count($filas) < 2) {
                throw new InvalidArgumentException('El archivo no contiene movimientos.');
            }

            $headers = array_map('strval', $filas[0]);
            $fechaCol = $this->indiceColumna($mapping['fecha'] ?? null, $headers);
            $descripcionCol = $this->indiceColumna($mapping['descripcion'] ?? null, $headers);
            $numeroCol = $this->indiceColumna($mapping['numero_operacion'] ?? null, $headers);
            $referenciaCol = $this->indiceColumna($mapping['referencia'] ?? null, $headers);
            $cargoCol = $this->indiceColumna($mapping['cargo'] ?? null, $headers);
            $abonoCol = $this->indiceColumna($mapping['abono'] ?? null, $headers);
            $importeCol = $this->indiceColumna($mapping['importe'] ?? null, $headers);
            $tipoCol = $this->indiceColumna($mapping['tipo'] ?? null, $headers);
            $saldoCol = $this->indiceColumna($mapping['saldo'] ?? null, $headers);

            if ($fechaCol === null || ($importeCol === null && ($cargoCol === null || $abonoCol === null))) {
                throw new InvalidArgumentException('Debe mapear fecha, importe o cargo y abono.');
            }

            $idusuario = (int) ($_SESSION['idusuario'] ?? 0);
            $this->pdo->beginTransaction();
            $conciliacion = $this->bloquearConciliacionSucursal($idconciliacion);
            if (!$this->conciliacionEditable($conciliacion)) {
                throw new RuntimeException('No se puede modificar esta conciliacion.');
            }

            $idimportacion = (new FluentSaver($this->pdo))
                ->table('conciliacion_importaciones')
                ->primaryKey('idimportacion')
                ->timestamps(false)
                ->data([
                    'idconciliacion' => $idconciliacion,
                    'nombre_archivo' => basename($archivo['name']),
                    'tipo_archivo' => $extension,
                    'hash_archivo' => $hashArchivo,
                    'tamano_archivo' => (int) $archivo['size'],
                    'total_registros' => 0,
                    'idusuario' => $idusuario,
                ])
                ->save();

            $total = 0;
            for ($i = 1, $count = count($filas); $i < $count; $i++) {
                $fila = $filas[$i];
                $fechaTexto = trim((string) ($fila[$fechaCol] ?? ''));
                if ($fechaTexto === '') {
                    continue;
                }

                $cargo = $cargoCol !== null ? $this->numero($fila[$cargoCol] ?? 0) : 0.0;
                $abono = $abonoCol !== null ? $this->numero($fila[$abonoCol] ?? 0) : 0.0;
                $importe = $importeCol !== null ? $this->numero($fila[$importeCol] ?? 0) : 0.0;
                $tipoTexto = strtolower(trim((string) ($tipoCol !== null ? ($fila[$tipoCol] ?? '') : '')));

                if ($importe === 0.0 && $cargo > 0) {
                    $importe = $cargo;
                    $tipo = 'cargo';
                } elseif ($importe === 0.0 && $abono > 0) {
                    $importe = $abono;
                    $tipo = 'abono';
                } else {
                    $tipo = in_array($tipoTexto, ['cargo', 'egreso', 'retiro', '-1'], true) ? 'cargo' : 'abono';
                }

                if ($importe <= 0) {
                    continue;
                }

                $fecha = $this->parsearFecha($fechaTexto);
                if (!$fecha) {
                    throw new InvalidArgumentException('Fecha invalida en la fila ' . ($i + 1) . '.');
                }
                if (!$this->fechaDentroPeriodo($fecha, $conciliacion)) {
                    throw new InvalidArgumentException('La fecha de la fila ' . ($i + 1) . ' esta fuera del periodo de conciliacion.');
                }

                $descripcion = trim((string) ($descripcionCol !== null ? ($fila[$descripcionCol] ?? '') : ''));
                $numero = $numeroCol !== null ? trim((string) ($fila[$numeroCol] ?? '')) : '';
                $referencia = $referenciaCol !== null ? trim((string) ($fila[$referenciaCol] ?? '')) : '';
                $saldo = $saldoCol !== null ? $this->numero($fila[$saldoCol] ?? 0) : null;

                $hashFila = hash('sha256', implode('|', [
                    $fecha->format('Y-m-d H:i:s'),
                    $tipo,
                    $this->decimalString($importe),
                    $numero,
                    $referencia,
                    $descripcion,
                ]));

                (new FluentSaver($this->pdo))
                    ->table('conciliacion_movimientos_bancarios')
                    ->primaryKey('idmovimiento_bancario')
                    ->data([
                        'idimportacion' => $idimportacion,
                        'fecha_operacion' => $fecha->format('Y-m-d H:i:s'),
                        'descripcion' => $descripcion,
                        'numero_operacion' => $numero !== '' ? $numero : null,
                        'referencia' => $referencia !== '' ? $referencia : null,
                        'tipo' => $tipo,
                        'importe' => $this->decimalString($importe),
                        'saldo' => $saldo !== null ? $this->decimalString($saldo) : null,
                        'estado' => 'pendiente',
                        'hash_fila' => $hashFila,
                        'datos_originales' => json_encode($fila, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ])
                    ->save();
                $total++;
            }

            $stmt = $this->pdo->prepare(
                'UPDATE conciliacion_importaciones SET total_registros = :total WHERE idimportacion = :id'
            );
            $stmt->execute(['total' => $total, 'id' => $idimportacion]);

            $this->auditar($idconciliacion, $idusuario, 'importar', [
                'archivo' => basename($archivo['name']),
                'total_registros' => $total,
            ]);

            $this->pdo->commit();
            Response::success(['total_registros' => $total], 'Estado de cuenta importado correctamente.');
        } catch (\Throwable $th) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            Response::error($th->getMessage(), 400);
        }
    }

    public function crearMovimientoBancario(int $idconciliacion, array $data): void
    {
        try {
            if (!Helpers::verificarPermisoCodigo('conciliaciones.importar')) {
                throw new RuntimeException('No tiene permiso para agregar movimientos bancarios.');
            }

            $conciliacion = $this->obtenerConciliacionSucursal($idconciliacion);
            if (!$this->conciliacionEditable($conciliacion)) {
                throw new RuntimeException('No se puede modificar esta conciliacion.');
            }

            $fecha = $this->parsearFecha((string) ($data['fecha_operacion'] ?? ''));
            if (!$fecha) {
                throw new InvalidArgumentException('Ingrese una fecha valida.');
            }
            if (!$this->fechaDentroPeriodo($fecha, $conciliacion)) {
                throw new InvalidArgumentException('La fecha esta fuera del periodo de conciliacion.');
            }

            $tipo = strtolower(trim((string) ($data['tipo'] ?? '')));
            if (!in_array($tipo, ['cargo', 'abono'], true)) {
                throw new InvalidArgumentException('Seleccione cargo o abono.');
            }

            $importe = $this->validarDecimal($data['importe'] ?? 0, 'Importe');
            if ($importe <= 0) {
                throw new InvalidArgumentException('El importe debe ser mayor a cero.');
            }

            $descripcion = trim((string) ($data['descripcion'] ?? ''));
            $numero = trim((string) ($data['numero_operacion'] ?? ''));
            $referencia = trim((string) ($data['referencia'] ?? ''));
            $idusuario = (int) ($_SESSION['idusuario'] ?? 0);

            $this->pdo->beginTransaction();
            $conciliacion = $this->bloquearConciliacionSucursal($idconciliacion);
            if (!$this->conciliacionEditable($conciliacion)) {
                throw new RuntimeException('No se puede modificar esta conciliacion.');
            }

            $importacion = (new DBQuery($this->pdo))
                ->select(['idimportacion'])
                ->from('conciliacion_importaciones')
                ->where('idconciliacion', '=', $idconciliacion)
                ->where('tipo_archivo', '=', 'manual')
                ->first();

            if (!$importacion) {
                $idimportacion = (new FluentSaver($this->pdo))
                    ->table('conciliacion_importaciones')
                    ->primaryKey('idimportacion')
                    ->timestamps(false)
                    ->data([
                        'idconciliacion' => $idconciliacion,
                        'nombre_archivo' => 'Carga manual',
                        'tipo_archivo' => 'manual',
                        'hash_archivo' => hash('sha256', 'manual|' . $idconciliacion . '|' . microtime(true)),
                        'tamano_archivo' => 0,
                        'total_registros' => 0,
                        'idusuario' => $idusuario,
                    ])
                    ->save();
            } else {
                $idimportacion = (int) $importacion['idimportacion'];
            }

            $hashFila = hash('sha256', implode('|', [
                $fecha->format('Y-m-d H:i:s'),
                $tipo,
                $this->decimalString($importe),
                $numero,
                $referencia,
                $descripcion,
                uniqid('', true),
            ]));

            (new FluentSaver($this->pdo))
                ->table('conciliacion_movimientos_bancarios')
                ->primaryKey('idmovimiento_bancario')
                ->data([
                    'idimportacion' => $idimportacion,
                    'fecha_operacion' => $fecha->format('Y-m-d H:i:s'),
                    'descripcion' => $descripcion !== '' ? $descripcion : 'Movimiento cargado manualmente',
                    'numero_operacion' => $numero !== '' ? $numero : null,
                    'referencia' => $referencia !== '' ? $referencia : null,
                    'tipo' => $tipo,
                    'importe' => $this->decimalString($importe),
                    'estado' => 'pendiente',
                    'hash_fila' => $hashFila,
                    'datos_originales' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ])
                ->save();

            $stmt = $this->pdo->prepare(
                'UPDATE conciliacion_importaciones SET total_registros = (
                    SELECT COUNT(*) FROM conciliacion_movimientos_bancarios
                    WHERE idimportacion = :source_id
                ) WHERE idimportacion = :id'
            );
            $stmt->execute([
                'source_id' => $idimportacion,
                'id' => $idimportacion,
            ]);

            $this->auditar($idconciliacion, $idusuario, 'movimiento_bancario_manual', [
                'fecha_operacion' => $fecha->format('Y-m-d H:i:s'),
                'tipo' => $tipo,
                'importe' => $this->decimalString($importe),
            ]);

            $this->pdo->commit();
            Response::success(null, 'Movimiento bancario agregado correctamente.');
        } catch (\Throwable $th) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            Response::error($th->getMessage(), 400);
        }
    }

    public function conciliarManual(int $idconciliacion, array $data): void
    {
        try {
            if (!Helpers::verificarPermisoCodigo('conciliaciones.conciliar')) {
                throw new RuntimeException('No tiene permiso para conciliar movimientos.');
            }

            $idbancario = (int) ($data['idmovimiento_bancario'] ?? 0);
            $origen = trim((string) ($data['origen_sistema'] ?? ''));
            $idsistema = (int) ($data['idmovimiento_sistema'] ?? 0);

            if ($idbancario < 1 || $idsistema < 1 || !in_array($origen, [
                'movimiento',
                'detalle_cuentas_por_cobrar',
                'detalle_cuentas_por_pagar',
                'venta_pago',
            ], true)) {
                throw new InvalidArgumentException('Datos de conciliacion incompletos.');
            }

            $idusuario = (int) ($_SESSION['idusuario'] ?? 0);
            $this->pdo->beginTransaction();
            $conciliacion = $this->bloquearConciliacionSucursal($idconciliacion);
            if (!$this->conciliacionEditable($conciliacion)) {
                throw new RuntimeException('No se puede modificar esta conciliacion.');
            }
            $movimiento = $this->obtenerMovimientoBancarioConciliable($idconciliacion, $idbancario, true);
            if (!$movimiento || $movimiento['estado'] !== 'pendiente') {
                throw new RuntimeException('El movimiento bancario no esta pendiente en esta conciliacion.');
            }
            $sistema = $this->obtenerMovimientoSistemaConciliable($conciliacion, $origen, $idsistema, true);
            if (!$sistema) {
                throw new RuntimeException('El movimiento del sistema no pertenece a la cuenta y periodo de esta conciliacion.');
            }
            $this->validarCoincidenciaExacta($movimiento, $sistema);
            $this->validarSinRelacionesExistentes($idbancario, $origen, $idsistema);

            (new FluentSaver($this->pdo))
                ->table('conciliacion_relaciones')
                ->primaryKey('idrelacion')
                ->timestamps(false)
                ->data([
                    'idmovimiento_bancario' => $idbancario,
                    'origen_sistema' => $origen,
                    'idmovimiento_sistema' => $idsistema,
                    'importe_sistema' => $this->decimalString($sistema['importe']),
                    'tipo_relacion' => 'manual',
                    'idusuario' => $idusuario,
                ])
                ->save();

            (new FluentSaver($this->pdo))
                ->table('conciliacion_movimientos_bancarios')
                ->primaryKey('idmovimiento_bancario')
                ->data([
                    'idmovimiento_bancario' => $idbancario,
                    'estado' => 'conciliado_manual',
                ])
                ->update();

            $this->auditar($idconciliacion, $idusuario, 'conciliacion_manual', [
                'idmovimiento_bancario' => $idbancario,
                'origen_sistema' => $origen,
                'idmovimiento_sistema' => $idsistema,
            ]);
            $this->pdo->commit();
            Response::success(null, 'Movimiento conciliado manualmente.');
        } catch (\Throwable $th) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            Response::error($th->getMessage(), 400);
        }
    }

    public function conciliarAutomatico(int $idconciliacion): void
    {
        try {
            if (!Helpers::verificarPermisoCodigo('conciliaciones.conciliar')) {
                throw new RuntimeException('No tiene permiso para conciliar movimientos.');
            }

            $this->pdo->beginTransaction();
            $conciliacion = $this->bloquearConciliacionSucursal($idconciliacion);
            if (!$this->conciliacionEditable($conciliacion)) {
                throw new RuntimeException('No se puede modificar esta conciliacion.');
            }

            $bancarios = (new DBQuery($this->pdo))
                ->select([
                    'mb.idmovimiento_bancario',
                    'mb.fecha_operacion',
                    'mb.numero_operacion',
                    'mb.tipo',
                    'mb.importe',
                    'mb.estado',
                ])
                ->from('conciliacion_movimientos_bancarios mb')
                ->join('conciliacion_importaciones i', 'i.idimportacion = mb.idimportacion')
                ->where('i.idconciliacion', '=', $idconciliacion)
                ->where('mb.estado', '=', 'pendiente')
                ->get();
            $totalPendientes = count($bancarios);

            $sistema = $this->movimientosSistema(
                (int) $conciliacion['idbanco'],
                $conciliacion['periodo_inicio'],
                $conciliacion['periodo_fin']
            );
            $sistema = array_values(array_filter($sistema, function ($item) {
                return $this->claveCoincidencia($item) !== null
                    && !$this->tieneRelacionSistema((string) $item['origen'], (int) $item['id']);
            }));
            $bancarios = array_values(array_filter($bancarios, function ($item) {
                return $this->claveCoincidencia($item) !== null;
            }));

            $conteoBancario = $this->contarClavesCoincidencia($bancarios);
            $conteoSistema = $this->contarClavesCoincidencia($sistema);
            $sistemaPorClave = [];
            foreach ($sistema as $candidato) {
                $clave = $this->claveCoincidencia($candidato);
                if ($clave !== null && ($conteoSistema[$clave] ?? 0) === 1) {
                    $sistemaPorClave[$clave] = $candidato;
                }
            }
            $idusuario = (int) ($_SESSION['idusuario'] ?? 0);
            $conciliados = 0;

            foreach ($bancarios as $bancario) {
                $clave = $this->claveCoincidencia($bancario);
                if (($conteoBancario[$clave] ?? 0) !== 1 || ($conteoSistema[$clave] ?? 0) !== 1) {
                    continue;
                }

                $idbancario = (int) $bancario['idmovimiento_bancario'];
                $itemSistema = $sistemaPorClave[$clave] ?? null;
                if (!$itemSistema) {
                    continue;
                }

                $movimiento = $this->obtenerMovimientoBancarioConciliable($idconciliacion, $idbancario, true);
                $registroSistema = $this->obtenerMovimientoSistemaConciliable(
                    $conciliacion,
                    (string) $itemSistema['origen'],
                    (int) $itemSistema['id'],
                    true
                );
                if (!$movimiento || $movimiento['estado'] !== 'pendiente' || !$registroSistema) {
                    continue;
                }
                $this->validarCoincidenciaExacta($movimiento, $registroSistema, true);
                if ($this->tieneRelacionBancaria($idbancario)
                    || $this->tieneRelacionSistema((string) $itemSistema['origen'], (int) $itemSistema['id'])) {
                    continue;
                }

                (new FluentSaver($this->pdo))
                    ->table('conciliacion_relaciones')
                    ->primaryKey('idrelacion')
                    ->timestamps(false)
                    ->data([
                        'idmovimiento_bancario' => $idbancario,
                        'origen_sistema' => $itemSistema['origen'],
                        'idmovimiento_sistema' => (int) $itemSistema['id'],
                        'importe_sistema' => $this->decimalString($registroSistema['importe']),
                        'tipo_relacion' => 'exacta',
                        'idusuario' => $idusuario,
                    ])
                    ->save();

                (new FluentSaver($this->pdo))
                    ->table('conciliacion_movimientos_bancarios')
                    ->primaryKey('idmovimiento_bancario')
                    ->data([
                        'idmovimiento_bancario' => $idbancario,
                        'estado' => 'conciliado_auto',
                    ])
                    ->update();
                $conciliados++;
            }

            $this->auditar($idconciliacion, $idusuario, 'conciliacion_automatica', [
                'conciliados' => $conciliados,
            ]);
            $this->pdo->commit();
            Response::success(
                ['conciliados' => $conciliados, 'pendientes' => $totalPendientes - $conciliados],
                'Proceso de conciliacion automatica completado.'
            );
        } catch (\Throwable $th) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            Response::error($th->getMessage(), 400);
        }
    }

    public function cerrar(int $idconciliacion, array $data): void
    {
        try {
            if (!Helpers::verificarPermisoCodigo('conciliaciones.cerrar')) {
                throw new RuntimeException('No tiene permiso para cerrar conciliaciones.');
            }

            $idusuario = (int) ($_SESSION['idusuario'] ?? 0);
            $saldoFinal = $this->validarDecimal($data['saldo_final_banco'] ?? 0, 'Saldo final del banco');
            $this->pdo->beginTransaction();
            $conciliacion = $this->bloquearConciliacionSucursal($idconciliacion);
            if (!$this->conciliacionEditable($conciliacion)) {
                throw new RuntimeException('No se puede cerrar esta conciliacion.');
            }

            $pendientes = (new DBQuery($this->pdo))
                ->select(['COUNT(*) AS total'])
                ->from('conciliacion_movimientos_bancarios mb')
                ->join('conciliacion_importaciones i', 'i.idimportacion = mb.idimportacion')
                ->where('i.idconciliacion', '=', $idconciliacion)
                ->whereIn('mb.estado', ['pendiente', 'diferencia', 'no_identificado'])
                ->first();

            if ((int) ($pendientes['total'] ?? 0) > 0) {
                throw new RuntimeException('Existen movimientos pendientes o con diferencias sin resolver.');
            }

            $resumen = (new DBQuery($this->pdo))
                ->select([
                    "COALESCE(SUM(CASE WHEN mb.tipo = 'abono' THEN mb.importe ELSE -mb.importe END), 0) AS variacion",
                ])
                ->from('conciliacion_movimientos_bancarios mb')
                ->join('conciliacion_importaciones i', 'i.idimportacion = mb.idimportacion')
                ->where('i.idconciliacion', '=', $idconciliacion)
                ->first();
            $saldoCalculado = round((float) $conciliacion['saldo_inicial'] + (float) ($resumen['variacion'] ?? 0), 2);
            $diferencia = round($saldoFinal - $saldoCalculado, 2);
            if ($diferencia !== 0.0 && empty($data['confirmar_diferencia'])) {
                throw new RuntimeException(
                    'El saldo final difiere del calculado en ' . $this->decimalString($diferencia)
                    . '. Confirme expresamente el cierre con diferencia.'
                );
            }

            (new FluentSaver($this->pdo))
                ->table('conciliaciones_bancarias')
                ->primaryKey('idconciliacion')
                ->data([
                    'idconciliacion' => $idconciliacion,
                    'estado' => 'cerrada',
                    'saldo_final_banco' => $this->decimalString($saldoFinal),
                    'observaciones' => trim((string) ($data['observaciones'] ?? $conciliacion['observaciones'] ?? '')),
                    'fecha_cierre' => date('Y-m-d H:i:s'),
                    'idusuario_cierre' => $idusuario,
                ])
                ->update();

            $this->auditar($idconciliacion, $idusuario, 'cerrar', [
                'saldo_final_banco' => $this->decimalString($saldoFinal),
                'saldo_calculado' => $this->decimalString($saldoCalculado),
                'diferencia' => $this->decimalString($diferencia),
            ]);
            $this->pdo->commit();
            Response::success([
                'saldo_calculado' => $this->decimalString($saldoCalculado),
                'diferencia' => $this->decimalString($diferencia),
            ], 'Conciliacion cerrada correctamente.');
        } catch (\Throwable $th) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            Response::error($th->getMessage(), 400);
        }
    }

    private function obtenerConciliacionSucursal(int $idconciliacion): array
    {
        $idsucursal = (int) ($_SESSION['idsucursal'] ?? 0);
        $conciliacion = (new DBQuery($this->pdo))
            ->select('*')
            ->from('conciliaciones_bancarias')
            ->where('idconciliacion', '=', $idconciliacion)
            ->where('idsucursal', '=', $idsucursal)
            ->first();
        if (!$conciliacion) {
            throw new RuntimeException('Conciliacion no encontrada.');
        }
        return $conciliacion;
    }

    private function bloquearConciliacionSucursal(int $idconciliacion): array
    {
        $idsucursal = (int) ($_SESSION['idsucursal'] ?? 0);
        $stmt = $this->pdo->prepare(
            'SELECT * FROM conciliaciones_bancarias
            WHERE idconciliacion = :idconciliacion AND idsucursal = :idsucursal
            FOR UPDATE'
        );
        $stmt->execute([
            'idconciliacion' => $idconciliacion,
            'idsucursal' => $idsucursal,
        ]);
        $conciliacion = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$conciliacion) {
            throw new RuntimeException('Conciliacion no encontrada.');
        }
        return $conciliacion;
    }

    private function conciliacionEditable(array $conciliacion): bool
    {
        return in_array($conciliacion['estado'] ?? '', ['borrador', 'reabierta'], true);
    }

    private function fechaDentroPeriodo(DateTimeImmutable $fecha, array $conciliacion): bool
    {
        $dia = $fecha->format('Y-m-d');
        return $dia >= $conciliacion['periodo_inicio'] && $dia <= $conciliacion['periodo_fin'];
    }

    private function obtenerMovimientoBancarioConciliable(
        int $idconciliacion,
        int $idmovimiento,
        bool $bloquear = false
    ): ?array {
        $sql = 'SELECT mb.idmovimiento_bancario, mb.fecha_operacion, mb.numero_operacion, mb.tipo, mb.importe, mb.estado
            FROM conciliacion_movimientos_bancarios mb
            INNER JOIN conciliacion_importaciones i ON i.idimportacion = mb.idimportacion
            WHERE mb.idmovimiento_bancario = :idmovimiento
                AND i.idconciliacion = :idconciliacion'
            . ($bloquear ? ' FOR UPDATE' : '');
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'idmovimiento' => $idmovimiento,
            'idconciliacion' => $idconciliacion,
        ]);
        $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);
        return $movimiento ?: null;
    }

    private function obtenerMovimientoSistemaConciliable(
        array $conciliacion,
        string $origen,
        int $idmovimiento,
        bool $bloquear = false
    ): ?array {
        $origenes = [
            'movimiento' => [
                'tabla' => 'movimiento',
                'id' => 'idmovimiento',
                'fecha' => 'fecha',
                'importe' => 'totaldeposito',
                'operacion' => 'noperacion',
                'tipo' => 'tipo',
            ],
            'detalle_cuentas_por_cobrar' => [
                'tabla' => 'detalle_cuentas_por_cobrar',
                'id' => 'iddcpc',
                'fecha' => 'fechapago',
                'importe' => 'montotarjeta',
                'operacion' => 'op',
                'tipo' => "'Ingresos'",
            ],
            'detalle_cuentas_por_pagar' => [
                'tabla' => 'detalle_cuentas_por_pagar',
                'id' => 'iddcpp',
                'fecha' => 'fechapago',
                'importe' => 'montotarjeta',
                'operacion' => 'op',
                'tipo' => "'Egresos'",
            ],
            'venta_pago' => [
                'tabla' => 'venta_pago',
                'id' => 'idventapago',
                'fecha' => 'created_at',
                'importe' => 'monto',
                'operacion' => 'nroOperacion',
                'tipo' => "'Ingresos'",
            ],
        ];
        if (!isset($origenes[$origen])) {
            return null;
        }

        $fuente = $origenes[$origen];
        $sql = 'SELECT ' . $fuente['id'] . ' AS id, ' . $fuente['fecha'] . ' AS fecha, '
            . $fuente['importe'] . ' AS importe, ' . $fuente['operacion'] . ' AS numero_operacion, '
            . $fuente['tipo'] . ' AS tipo FROM ' . $fuente['tabla']
            . ' WHERE ' . $fuente['id'] . ' = :idmovimiento AND idbanco = :idbanco'
            . ' AND ' . $fuente['fecha'] . ' >= :desde AND ' . $fuente['fecha'] . ' <= :hasta'
            . ' AND deleted_at IS NULL AND ' . $fuente['importe'] . ' > 0'
            . ($bloquear ? ' FOR UPDATE' : '');
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'idmovimiento' => $idmovimiento,
            'idbanco' => (int) $conciliacion['idbanco'],
            'desde' => $conciliacion['periodo_inicio'] . ' 00:00:00',
            'hasta' => $conciliacion['periodo_fin'] . ' 23:59:59',
        ]);
        $movimiento = $stmt->fetch(PDO::FETCH_ASSOC);
        return $movimiento ?: null;
    }

    private function validarCoincidenciaExacta(
        array $bancario,
        array $sistema,
        bool $validarOperacion = false
    ): void
    {
        $tipoSistema = $this->tipoConciliacion($sistema['tipo'] ?? '');
        if ($tipoSistema === null || $tipoSistema !== ($bancario['tipo'] ?? null)) {
            throw new InvalidArgumentException('El tipo de movimiento bancario no coincide con el movimiento del sistema.');
        }
        if ((int) round((float) $bancario['importe'] * 100) !== (int) round((float) $sistema['importe'] * 100)) {
            throw new InvalidArgumentException('El importe bancario no coincide con el importe del movimiento del sistema.');
        }
        if ($validarOperacion
            && trim((string) ($bancario['numero_operacion'] ?? '')) !== trim((string) ($sistema['numero_operacion'] ?? ''))) {
            throw new InvalidArgumentException('El numero de operacion ya no coincide con el movimiento del sistema.');
        }
    }

    private function tipoConciliacion($tipo): ?string
    {
        $tipo = strtolower(trim((string) $tipo));
        if (in_array($tipo, ['abono', 'ingreso', 'ingresos', 'deposito'], true)) {
            return 'abono';
        }
        if (in_array($tipo, ['cargo', 'egreso', 'egresos', 'retiro'], true)) {
            return 'cargo';
        }
        return null;
    }

    private function claveCoincidencia(array $movimiento): ?string
    {
        $operacion = trim((string) ($movimiento['numero_operacion'] ?? ''));
        $tipo = $this->tipoConciliacion($movimiento['tipo'] ?? '');
        if ($operacion === '' || $tipo === null || !isset($movimiento['importe'])) {
            return null;
        }
        return json_encode([
            $operacion,
            (int) round((float) $movimiento['importe'] * 100),
            $tipo,
        ]);
    }

    private function contarClavesCoincidencia(array $movimientos): array
    {
        $conteos = [];
        foreach ($movimientos as $movimiento) {
            $clave = $this->claveCoincidencia($movimiento);
            if ($clave !== null) {
                $conteos[$clave] = ($conteos[$clave] ?? 0) + 1;
            }
        }
        return $conteos;
    }

    private function validarSinRelacionesExistentes(int $idbancario, string $origen, int $idsistema): void
    {
        if ($this->tieneRelacionBancaria($idbancario)) {
            throw new RuntimeException('El movimiento bancario ya esta conciliado.');
        }
        if ($this->tieneRelacionSistema($origen, $idsistema)) {
            throw new RuntimeException('El movimiento del sistema ya esta conciliado.');
        }
    }

    private function tieneRelacionBancaria(int $idbancario): bool
    {
        return (bool) (new DBQuery($this->pdo))
            ->select(['idrelacion'])
            ->from('conciliacion_relaciones')
            ->where('idmovimiento_bancario', '=', $idbancario)
            ->first();
    }

    private function tieneRelacionSistema(string $origen, int $idsistema): bool
    {
        return (bool) (new DBQuery($this->pdo))
            ->select(['idrelacion'])
            ->from('conciliacion_relaciones')
            ->where('origen_sistema', '=', $origen)
            ->where('idmovimiento_sistema', '=', $idsistema)
            ->first();
    }

    private function movimientosSistema(int $idbanco, string $inicio, string $fin): array
    {
        $desde = $inicio . ' 00:00:00';
        $hasta = $fin . ' 23:59:59';

        $movimientos = (new DBQuery($this->pdo))
            ->select([
                'idmovimiento AS id',
                'fecha AS fecha',
                'tipo AS tipo',
                'totaldeposito AS importe',
                'noperacion AS numero_operacion',
                'descripcion AS descripcion',
                "'movimiento' AS origen",
            ])
            ->from('movimiento')
            ->where('idbanco', '=', $idbanco)
            ->where('fecha', '>=', $desde)
            ->where('fecha', '<=', $hasta)
            ->where('totaldeposito', '>', 0)
            ->whereNull('deleted_at')
            ->get();

        $cobrar = (new DBQuery($this->pdo))
            ->select([
                'iddcpc AS id',
                'fechapago AS fecha',
                "'Ingresos' AS tipo",
                'montotarjeta AS importe',
                'op AS numero_operacion',
                'observacion AS descripcion',
                "'detalle_cuentas_por_cobrar' AS origen",
            ])
            ->from('detalle_cuentas_por_cobrar')
            ->where('idbanco', '=', $idbanco)
            ->where('fechapago', '>=', $desde)
            ->where('fechapago', '<=', $hasta)
            ->where('montotarjeta', '>', 0)
            ->whereNull('deleted_at')
            ->get();

        $pagar = (new DBQuery($this->pdo))
            ->select([
                'iddcpp AS id',
                'fechapago AS fecha',
                "'Egresos' AS tipo",
                'montotarjeta AS importe',
                'op AS numero_operacion',
                'observacion AS descripcion',
                "'detalle_cuentas_por_pagar' AS origen",
            ])
            ->from('detalle_cuentas_por_pagar')
            ->where('idbanco', '=', $idbanco)
            ->where('fechapago', '>=', $desde)
            ->where('fechapago', '<=', $hasta)
            ->where('montotarjeta', '>', 0)
            ->whereNull('deleted_at')
            ->get();

        $ventas = (new DBQuery($this->pdo))
            ->select([
                'idventapago AS id',
                'created_at AS fecha',
                "'Ingresos' AS tipo",
                'monto AS importe',
                'nroOperacion AS numero_operacion',
                'comprobante AS descripcion',
                "'venta_pago' AS origen",
            ])
            ->from('venta_pago')
            ->where('idbanco', '=', $idbanco)
            ->where('created_at', '>=', $desde)
            ->where('created_at', '<=', $hasta)
            ->where('monto', '>', 0)
            ->whereNull('deleted_at')
            ->get();

        return array_merge($movimientos, $cobrar, $pagar, $ventas);
    }

    private function auditar(int $idconciliacion, int $idusuario, string $accion, array $detalle): void
    {
        (new FluentSaver($this->pdo))
            ->table('conciliacion_auditoria')
            ->primaryKey('idaudit')
            ->timestamps(false)
            ->data([
                'idconciliacion' => $idconciliacion,
                'idusuario' => $idusuario,
                'accion' => $accion,
                'detalle' => json_encode($detalle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ])
            ->save();
    }

    private function indiceColumna($valor, array $headers): ?int
    {
        if ($valor === null || trim((string) $valor) === '') {
            return null;
        }
        if (ctype_digit((string) $valor)) {
            $indice = (int) $valor;
            return isset($headers[$indice]) ? $indice : null;
        }
        foreach ($headers as $indice => $header) {
            if (strcasecmp(trim((string) $header), trim((string) $valor)) === 0) {
                return $indice;
            }
        }
        return null;
    }

    private function validarFecha(string $valor, string $campo): DateTimeImmutable
    {
        $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', trim($valor));
        if (!$fecha || $fecha->format('Y-m-d') !== trim($valor)) {
            throw new InvalidArgumentException($campo . ' es invalido.');
        }
        return $fecha;
    }

    private function parsearFecha(string $valor): ?DateTimeImmutable
    {
        foreach (['Y-m-d H:i:s', 'Y-m-d\TH:i:s', 'Y-m-d\TH:i', 'Y-m-d', 'd/m/Y H:i:s', 'd/m/Y', 'd-m-Y', 'm/d/Y'] as $formato) {
            $fecha = DateTimeImmutable::createFromFormat('!' . $formato, trim($valor));
            if ($fecha !== false) {
                return $fecha;
            }
        }
        return null;
    }

    private function validarDecimal($valor, string $campo): float
    {
        if (!is_numeric(str_replace(',', '.', trim((string) $valor)))) {
            throw new InvalidArgumentException($campo . ' es invalido.');
        }
        return $this->numero($valor);
    }

    private function numero($valor): float
    {
        if ($valor === null) {
            return 0.0;
        }
        return (float) str_replace(',', '.', trim((string) $valor));
    }

    private function decimalString($valor): string
    {
        return number_format((float) $valor, 2, '.', '');
    }
}
