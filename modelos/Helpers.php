<?php
require_once __DIR__ . '/../configuraciones/ConexionPdo.php';
require_once __DIR__ . '/../core/Constants.php';
class Helpers
{
    public PDO $pdo;
    //Implementamos nuestro constructor
    public function __construct()
    {
        $this->pdo = Conexion::conectar();
    }

    public static function clienteDefault($idcliente): int
    {
        return !empty($idcliente)
            ? (int) $idcliente
            : Constants::CLIENTE_DEFAULT;
    }

    public function get_currency_code($idsucursal)
    {
        $data = (new DBQuery($this->pdo))
            ->select('moneda')
            ->from('sucursal')
            ->where('idsucursal', '=', $idsucursal)
            ->first();

        return $data['moneda'] ?? 'PEN';
    }


    public function get_currency_symbol($monto, $currency = null, $locale = "es_PE")
    {
        if (!$currency) {
            $sucursal = $_SESSION['idsucursal'];
            $currency = self::get_currency_code($sucursal);
        }
        // Validar monto
        if (!is_numeric($monto)) {
            $monto = 0;
        }

        // Crear formateador
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        // Formatear moneda correctamente (usa código ISO: PEN, USD, EUR, etc.)
        $resultado = $formatter->formatCurrency($monto, $currency);

        return $resultado;
    }


    public function get_impuesto_empresa($idsucursal)
    {
        $data = (new DBQuery($this->pdo))
            ->select('e.nombre_impuesto, e.monto_impuesto')
            ->from('empresas e')
            ->join('sucursal s', "s.idempresa = e.idempresa")
            ->where('s.idsucursal', '=', $idsucursal)
            ->first();
        return [
            'impuesto' => $data['nombre_impuesto'],
            'valor' => $data['monto_impuesto']
        ];
    }


    public function get_symbol($currency = null, $locale = "es_PE")
    {
        if (!$currency) {
            $sucursal = $_SESSION['idsucursal'];
            $currency = self::get_currency_code($sucursal);
        }

        if (!class_exists('NumberFormatter')) {
            return $currency;
        }

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        // Truco: formatear 0 y extraer símbolo
        $formatted = $formatter->formatCurrency(0, $currency);

        // Quitar números y dejar solo símbolo
        return trim(preg_replace('/[0-9\.\,\s]/', '', $formatted));
    }

    public function getUserPermissionAccion(string $nombre_permiso): bool
    {
        $idusuario = $_SESSION['idusuario'] ?? null;

        if ($idusuario === null || empty($nombre_permiso)) {
            return false;
        }

        $esSuperusuario = self::esSuperusuario($idusuario);

        if ($esSuperusuario) {
            return true;
        }

        // 2. Verificar permisos por usuario
        return (new DBQuery($this->pdo))
            ->select('ua.*')
            ->from('usuario_accion ua')
            ->join('accion_permiso ap', 'ua.idaccion_permiso = ap.idaccion_permiso')
            ->softDeletes('ua.deleted_at')
            ->where('ua.idusuario', '=', $idusuario)
            ->where('ap.nombre', '=', $nombre_permiso)
            ->exists();
    }



    public function getUserPermisoModulo(string $modulo, ?string $modulo_parent = null): bool
    {
        $idusuario = $_SESSION['idusuario'] ?? null;

        if ($idusuario === null || empty($modulo)) {
            return false;
        }

        $esSuperusuario = self::esSuperusuario($idusuario);

        if ($esSuperusuario) {
            return true;
        }

        if ($modulo_parent === null) {
            return (new DBQuery($this->pdo))
                ->select('up.*')
                ->from('usuario_permiso up')
                ->join('permiso p', 'up.idpermiso = p.idpermiso')
                ->softDeletes('up.deleted_at')
                ->where('up.idusuario', '=', $idusuario)
                ->where('p.nombre', '=', $modulo)
                ->exists();
        }

        return (new DBQuery($this->pdo))
            ->select('up.*')
            ->from('usuario_permiso up')
            ->join('subpermiso sp', 'up.idsubpermiso = sp.idsubpermiso')
            ->softDeletes('up.deleted_at')
            ->where('up.idusuario', '=', $idusuario)
            ->where('sp.nombre', '=', $modulo)
            ->exists();
    }


    public function esSuperusuario($idusuario = null): bool
    {
        $idUsuario = $idusuario ?? $_SESSION['idusuario'];

        if ($idUsuario === null) {
            return false;
        }

        $user = (new DBQuery($this->pdo))
            ->select('superusuario')
            ->from('usuario')
            ->where('idusuario', '=', $idusuario)
            ->first();

        if (!$user) {
            return false;
        }

        if ((int) $user['superusuario'] != 1) {
            return false;
        }

        return true;
    }

    public function dataArchivosAdjuntos($idseguimiento)
    {
        return (new DBQuery($this->pdo))
            ->select('*')
            ->from('seguimiento_adjuntos')
            ->where('idseguimiento', '=', $idseguimiento)
            ->get();
    }



    public function verificarMoraCredito($idsucursal): array
    {
        $config = (new DBQuery($this->pdo))
            ->select('is_mora_credito, valor_mora_credito')
            ->from('sucursal_configuracion')
            ->where('idsucursal', '=', $idsucursal)
            ->first();

        if (!$config) {
            return [
                'activo' => false,
                'valor' => 0
            ];
        }

        return [
            'activo' => (int) $config['is_mora_credito'] === 1,
            'valor' => (float) $config['valor_mora_credito']
        ];
    }


    public function verificarDecuentoPagoAnticipado($idsucursal): array
    {
        $config = (new DBQuery($this->pdo))
            ->select('is_descuento_anticipado, valor_descuento_anticipado, dias_anticipacion')
            ->from('sucursal_configuracion')
            ->where('idsucursal', '=', $idsucursal)
            ->first();

        if (!$config) {
            return [
                'activo' => false,
                'valor' => 0,
                'dias_anticipacion' => 0
            ];
        }

        return [
            'activo' => (int) $config['is_descuento_anticipado'] === 1,
            'valor' => (float) $config['valor_descuento_anticipado'],
            'dias_anticipacion' => (int) $config['dias_anticipacion']
        ];
    }


    public function verificarRefinanciamientos($idsucursal): array
    {
        $config = (new DBQuery($this->pdo))
            ->select('is_refinanciamiento,  maximo_refinanciamientos')
            ->from('sucursal_configuracion')
            ->where('idsucursal', '=', $idsucursal)
            ->first();

        if (!$config) {
            return [
                'activo' => false,
                'valor' => 0
            ];
        }

        return [
            'activo' => (int) $config['is_refinanciamiento'] === 1,
            'valor' => (float) $config['maximo_refinanciamientos']
        ];
    }


    public function verificarMes30Dias($idsucursal): bool
    {
        $config = (new DBQuery($this->pdo))
            ->select('is_calculo_mes')
            ->from('sucursal_configuracion')
            ->where('idsucursal', '=', $idsucursal)
            ->first();

        if (!$config) {
            return false;
        }

        return (bool) $config['is_calculo_mes'];
    }


    public function toFloat($valor)
    {
        return is_numeric($valor) ? (float) $valor : 0.0;
    }


    public function verificarAperturaCaja($idcaja): array
    {
        return (new DBQuery($this->pdo))
            ->select('ca.*')
            ->from('caja_apertura ca')
            ->join('cajas c', 'c.idcaja = ca.idcaja')
            ->where('ca.estado', '=', 1)
            ->where('ca.idcaja', '=', $idcaja)
            ->whereNull('ca.fecha_cierre')
            ->first();
    }

    public function verificarAperturaCajaUsuario(int $idsucursal, int $idusuario): int
    {
        $rpta = (new DBQuery($this->pdo))
            ->select('ca.idcaja')
            ->from('caja_apertura ca')
            ->join('cajas c', 'c.idcaja = ca.idcaja')
            ->where('ca.estado', '=', 1)
            ->where('ca.idsucursal', '=', $idsucursal)
            ->where('ca.idusuario', '=', $idusuario)
            ->whereNull('ca.fecha_cierre')
            ->first();

        return $rpta ? (int) $rpta['idcaja'] : 0;
    }

    public function updateKardexSucursal(
        $idsucursal,
        $idproducto,
        $idproducto_configuracion,
        $cantidad,
        $cantidad_contenedor,
        $precio,
        $nuevo_stock,
        $tipo_movimiento,
        $descripcion,
        $motivo,
    ) {
        $fecha_kardex = date('Y-m-d H:i:s');
        $kardex = (new FluentSaver($this->pdo))
            ->table('kardex')
            ->data([
                'idsucursal' => $idsucursal,
                'idproducto' => $idproducto,
                'idproducto_configuracion' => $idproducto_configuracion,
                'cantidad' => $cantidad,
                'cantidad_contenedor' => $cantidad_contenedor,
                'precio_unitario' => $precio,
                'stock_actual' => $nuevo_stock,
                'tipo_movimiento' => $tipo_movimiento,
                'motivo' => $descripcion,
                'descripcion' => $motivo,
                'fecha_kardex' => $fecha_kardex
            ])
            ->save();
        if (!$kardex) {
            throw new Exception("No se pudo registrar el movimiento en kardex.");
        }

        return true;
    }

    public function correlativoTraslado($idsucursal, $tipo)
    {
        $prefijo = strtoupper($tipo) === 'TRASLADO' ? 'TR' : 'SL';

        $data = (new DBQuery($this->pdo))
            ->select('COALESCE(MAX(correlativo),0) + 1 AS correlativo')
            ->from('traslado')
            ->where('idorigen', '=', $idsucursal)
            ->where('tipo', '=', $tipo)
            ->first();

        $correlativo = (int) $data['correlativo'];

        return sprintf('%s-%07d', $prefijo, $correlativo);
    }


    public static function calcularIgv(float $monto, float $porcentajeIgv = 18): float
    {
        if ($monto <= 0 || $porcentajeIgv <= 0) {
            return 0.00;
        }

        return round($monto * ($porcentajeIgv / (100 + $porcentajeIgv)), 2);
    }

    public static function calcularBaseImponible(float $monto, float $porcentajeIgv = 18): float
    {
        if ($monto <= 0 || $porcentajeIgv <= 0) {
            return round($monto, 2);
        }

        return round($monto / (1 + ($porcentajeIgv / 100)), 2);
    }

    public static function calcularOperacionGravada(float $monto, float $porcentajeIgv = 18): float
    {
        return self::calcularBaseImponible($monto, $porcentajeIgv);
    }

    public function sucursalConfiguracion(int $idsucursal)
    {
        return (new DBQuery($this->pdo))
            ->from('sucursal_configuracion')
            ->where('idsucursal', '=', $idsucursal)
            ->first();
    }


    public function verificarEnvioSunat(int $idsucursal): bool
    {
        $sucursal = $this->sucursalConfiguracion($idsucursal);

        return (bool) ($sucursal['is_send_sunat'] ?? false);
    }

    public function dataSucursal(int $idsucursal)
    {
        return (new DBQuery($this->pdo))
            ->from('sucursal s')
            ->join('empresas e', 's.idempresa = e.idempresa')
            ->where('idsucursal', '=', $idsucursal)
            ->first();
    }

    public function datosGerencia(int $idsucursal)
    {
        return (new DBQuery($this->pdo))
            ->from('comite_credito cc')
            ->join('personal p', 'p.idpersonal = cc.idpersonal')
            ->where('cc.cargo', '=', Constants::GERENTE)
            ->where('cc.idsucursal', '=', $idsucursal)
            ->first();
    }

    public function datosDocumentacion(int $idventa, int $tipo = 1)
    {
        return (new DBQuery($this->pdo))
            ->from('documentacion')
            ->where('idventa', '=', $idventa)
            ->where('tipo', '=', $tipo)
            ->first();
    }

    public function dataInicioFinPagos(int $idventa)
    {
        return (new DBQuery($this->pdo))
            ->select(
                '(SELECT deudatotal
                FROM cuentas_por_cobrar
                WHERE idventa = ' . (int) $idventa . '
                ORDER BY fechavencimiento ASC
                LIMIT 1
            ) AS deuda,
            MIN(fechavencimiento) AS fecha_inicio_cuota,
            MAX(fechavencimiento) AS fecha_fin_cuota'
            )
            ->from('cuentas_por_cobrar')
            ->where('idventa', '=', $idventa)
            ->first();
    }

    public function getEmpresa($idsucursal): int
    {
        $empresa = (new DBQuery($this->pdo))
            ->select("idempresa")
            ->from("sucursal")
            ->where("idsucursal", "=", $idsucursal)
            ->first();

        return (int) ($empresa['idempresa'] ?? 0);
    }


    public function actualizarCorrelativo(int $idtipo_comprobante, int $idsucursal): array
    {
        $idempresa = $this->getEmpresa($idsucursal);

        $comprobante = (new DBQuery($this->pdo))
            ->select("idcomprobante_pago, serie_comprobante, num_comprobante")
            ->from("comp_pago")
            ->where("idcomprobante_pago", "=", $idtipo_comprobante)
            ->where("idempresa", "=", $idempresa)
            ->softDeletes()
            ->orderBy("idcomprobante_pago", 'DESC')
            ->forUpdate()
            ->first();

        if (!$comprobante) {
            throw new Exception("No se encontró la configuración del comprobante.");
        }

        $numero = (int) $comprobante['num_comprobante'] + 1;

        if ($numero > 999999) {
            throw new Exception(
                "La serie {$comprobante['serie_comprobante']} llegó a su límite. Cree una nueva serie antes de continuar."
            );
        }

        $update = (new FluentSaver($this->pdo))
            ->table('comp_pago')
            ->primaryKey('idcomprobante_pago')
            ->data([
                'idcomprobante_pago' => $idtipo_comprobante,
                'num_comprobante' => $numero,
            ])
            ->update();

        if (!$update) {
            throw new Exception(
                "Ocurrio un error al actualizar la serie del comprobante."
            );
        }

        $comprobante['num_comprobante'] = str_pad($numero, 6, '0', STR_PAD_LEFT);

        return $comprobante;
    }

    public function obtenerComprobanteSucursal(int $idtipo_comprobante, int $idsucursal): array
    {
        $idempresa = $this->getEmpresa($idsucursal);

        $comprobante = (new DBQuery($this->pdo))
            ->select("idcomprobante_pago, serie_comprobante, num_comprobante")
            ->from("comp_pago")
            ->where("idcomprobante_pago", "=", $idtipo_comprobante)
            ->where("idempresa", "=", $idempresa)
            ->softDeletes()
            ->orderBy("idcomprobante_pago", 'DESC')
            ->first();

        if (!$comprobante) {
            throw new Exception("No se encontró la configuración del comprobante.");
        }

        $numero = (int) $comprobante['num_comprobante'] + 1;

        $comprobante['num_comprobante'] = str_pad($numero, 6, '0', STR_PAD_LEFT);

        return $comprobante;
    }

    public function incrementarBanco(int $idbanco, float $monto): bool
    {
        if ($monto <= 0) {
            throw new InvalidArgumentException('El monto debe ser mayor a cero.');
        }

        $banco = (new DBQuery($this->pdo))
            ->select('idbanco')
            ->from('bancos')
            ->where('idbanco', '=', $idbanco)
            ->first();

        if (!$banco) {
            throw new RuntimeException('No se encontró el banco.');
        }

        $sumarBanco = (new FluentSaver($this->pdo))
            ->table('bancos')
            ->primaryKey('idbanco')
            ->data(['idbanco' => $idbanco])
            ->increment('saldo', $monto);

        return (bool) $sumarBanco;
    }

    public function restarBanco(int $idbanco, float $monto): bool
    {
        if ($monto <= 0) {
            throw new InvalidArgumentException('El monto debe ser mayor a cero.');
        }

        $banco = (new DBQuery($this->pdo))
            ->select('saldo')
            ->from('bancos')
            ->where('idbanco', '=', $idbanco)
            ->first();

        if (!$banco) {
            throw new RuntimeException('No se encontró el banco.');
        }

        if ((float) $banco['saldo'] < $monto) {
            throw new RuntimeException(
                'Fondos insuficientes. Saldo disponible:' .
                self::get_currency_symbol((float) $banco['saldo'])
            );
        }

        $restarBanco = (new FluentSaver($this->pdo))
            ->table('bancos')
            ->primaryKey('idbanco')
            ->data(['idbanco' => $idbanco])
            ->decrement('saldo', $monto);

        return (bool) $restarBanco;
    }

    public function cajaAperturada(
        int $idsucursal,
        int $idusuario
    ): ?array {
        return (new DBQuery($this->pdo))
            ->select('*')
            ->from('caja_apertura ca')
            ->join(
                'cajas c',
                'c.idcaja = ca.idcaja'
            )
            ->where('ca.estado', '=', 1)
            ->where('ca.idsucursal', '=', $idsucursal)
            ->where('ca.idusuario', '=', $idusuario)
            ->whereNull('ca.fecha_cierre')
            ->limit(1)
            ->first();
    }

    public function incrementarCajaApertura(int $aperturacajaid, float $monto): bool
    {
        if ($monto <= 0) {
            throw new InvalidArgumentException('El monto debe ser mayor a cero.');
        }

        $caja = (new DBQuery($this->pdo))
            ->select('aperturacajaid')
            ->from('caja_apertura')
            ->where('aperturacajaid', '=', $aperturacajaid)
            ->where('estado', '=', 1)
            ->first();

        if (!$caja) {
            throw new RuntimeException('No se encontró la apertura de caja.');
        }

        $sumarCaja = (new FluentSaver($this->pdo))
            ->table('caja_apertura')
            ->primaryKey('aperturacajaid')
            ->data(['aperturacajaid' => $aperturacajaid])
            ->increment('efectivo_cierre', $monto);

        return (bool) $sumarCaja;
    }

    public function restarCajaApertura(int $aperturacajaid, float $monto): bool
    {
        if ($monto <= 0) {
            throw new InvalidArgumentException('El monto debe ser mayor a cero.');
        }

        $caja = (new DBQuery($this->pdo))
            ->select('efectivo_cierre')
            ->from('caja_apertura')
            ->where('aperturacajaid', '=', $aperturacajaid)
            ->first();

        if (!$caja) {
            throw new RuntimeException('No se encontró la apertura de caja.');
        }

        if ((float) $caja['efectivo_cierre'] < $monto) {
            throw new RuntimeException(
                'Fondos insuficientes. Saldo disponible:' .
                self::get_currency_symbol((float) $caja['efectivo_cierre'])
            );
        }

        $restarCaja = (new FluentSaver($this->pdo))
            ->table('caja_apertura')
            ->primaryKey('aperturacajaid')
            ->data(['aperturacajaid' => $aperturacajaid])
            ->decrement('efectivo_cierre', $monto);

        if (!$restarCaja) {
            throw new RuntimeException('No se pudo actualizar el saldo de caja.');
        }

        return true;
    }

    public function verificarVentaLotes($idsucursal): array
    {
        $config = (new DBQuery($this->pdo))
            ->select('is_venta_lotes')
            ->from('sucursal_configuracion')
            ->where('idsucursal', '=', $idsucursal)
            ->first();

        if (!$config) {
            return [
                'activo' => false
            ];
        }

        return [
            'activo' => (int) $config['is_venta_lotes'] === 1
        ];
    }
}
