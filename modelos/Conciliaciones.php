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
            if ($conciliacion['estado'] === 'cerrada') {
                throw new RuntimeException('No se puede modificar una conciliacion cerrada.');
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
            if ($conciliacion['estado'] === 'cerrada') {
                throw new RuntimeException('No se puede modificar una conciliacion cerrada.');
            }

            $fecha = $this->parsearFecha((string) ($data['fecha_operacion'] ?? ''));
            if (!$fecha) {
                throw new InvalidArgumentException('Ingrese una fecha valida.');
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

            $conciliacion = $this->obtenerConciliacionSucursal($idconciliacion);
            if ($conciliacion['estado'] === 'cerrada') {
                throw new RuntimeException('No se puede modificar una conciliacion cerrada.');
            }
            $idusuario = (int) ($_SESSION['idusuario'] ?? 0);
            $movimiento = (new DBQuery($this->pdo))
                ->select(['importe'])
                ->from('conciliacion_movimientos_bancarios mb')
                ->join('conciliacion_importaciones i', 'i.idimportacion = mb.idimportacion')
                ->where('mb.idmovimiento_bancario', '=', $idbancario)
                ->where('i.idconciliacion', '=', $idconciliacion)
                ->first();
            if (!$movimiento) {
                throw new RuntimeException('El movimiento bancario no pertenece a esta conciliacion.');
            }

            $existente = (new DBQuery($this->pdo))
                ->select(['idrelacion'])
                ->from('conciliacion_relaciones')
                ->where('origen_sistema', '=', $origen)
                ->where('idmovimiento_sistema', '=', $idsistema)
                ->first();
            if ($existente) {
                throw new RuntimeException('El movimiento del sistema ya esta conciliado.');
            }

            $bancarioConciliado = (new DBQuery($this->pdo))
                ->select(['idrelacion'])
                ->from('conciliacion_relaciones')
                ->where('idmovimiento_bancario', '=', $idbancario)
                ->first();
            if ($bancarioConciliado) {
                throw new RuntimeException('El movimiento bancario ya esta conciliado.');
            }

            $this->pdo->beginTransaction();

            (new FluentSaver($this->pdo))
                ->table('conciliacion_relaciones')
                ->primaryKey('idrelacion')
                ->timestamps(false)
                ->data([
                    'idmovimiento_bancario' => $idbancario,
                    'origen_sistema' => $origen,
                    'idmovimiento_sistema' => $idsistema,
                    'importe_sistema' => $this->numero((string) ($data['importe_sistema'] ?? $movimiento['importe'])),
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

    public function cerrar(int $idconciliacion, array $data): void
    {
        try {
            if (!Helpers::verificarPermisoCodigo('conciliaciones.cerrar')) {
                throw new RuntimeException('No tiene permiso para cerrar conciliaciones.');
            }

            $conciliacion = $this->obtenerConciliacionSucursal($idconciliacion);
            if ($conciliacion['estado'] === 'cerrada') {
                throw new RuntimeException('La conciliacion ya esta cerrada.');
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

            $idusuario = (int) ($_SESSION['idusuario'] ?? 0);
            $saldoFinal = $this->validarDecimal($data['saldo_final_banco'] ?? 0, 'Saldo final del banco');
            $this->pdo->beginTransaction();

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
            ]);
            $this->pdo->commit();
            Response::success(null, 'Conciliacion cerrada correctamente.');
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
