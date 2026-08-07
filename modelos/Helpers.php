<?php
require_once __DIR__ . '/../configuraciones/ConexionPdo.php';
require_once __DIR__ . '/config/Constants.php';
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
        $stmt = $this->pdo->prepare("
            SELECT moneda
            FROM sucursal
            WHERE idsucursal = :idsucursal
            LIMIT 1
        ");

        $stmt->execute([
            ':idsucursal' => $idsucursal
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['moneda'] ?? 'PEN';
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

        // 1. Verificar si es superusuario
        $stmt = $this->pdo->prepare("
            SELECT superusuario
            FROM usuario
            WHERE idusuario = :idusuario
            LIMIT 1
        ");

        $stmt->execute([
            ':idusuario' => $idusuario
        ]);

        $superusuario = $stmt->fetchColumn();

        if ((int) $superusuario === 1) {
            return true;
        }

        // 2. Verificar permisos por usuario
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM usuario_accion ua
            INNER JOIN accion_permiso ap
                ON ua.idaccion_permiso = ap.idaccion_permiso
            WHERE ua.idusuario = :idusuario
            AND ap.nombre = :nombre_permiso
            LIMIT 1
        ");

        $stmt->execute([
            ':idusuario' => $idusuario,
            ':nombre_permiso' => $nombre_permiso
        ]);

        return (bool) $stmt->fetchColumn();
    }



    public function getUserPermisoModulo(string $modulo, ?string $modulo_parent = null): bool
    {
        $idusuario = $_SESSION['idusuario'] ?? null;

        if ($idusuario === null || empty($modulo)) {
            return false;
        }

        // Verificar si es superusuario
        $stmt = $this->pdo->prepare("
            SELECT superusuario
            FROM usuario
            WHERE idusuario = :idusuario
            LIMIT 1
        ");

        $stmt->execute([
            ':idusuario' => $idusuario
        ]);

        if ($stmt->fetchColumn() == 1) {
            return true;
        }

        if ($modulo_parent === null) {

            $sql = "
                SELECT 1
                FROM usuario_permiso up
                INNER JOIN permiso p
                    ON up.idpermiso = p.idpermiso
                WHERE up.idusuario = :idusuario
                AND p.nombre = :modulo
                LIMIT 1
            ";

        } else {

            $sql = "
                SELECT 1
                FROM usuario_permiso up
                INNER JOIN subpermiso sp
                    ON up.idsubpermiso = sp.idsubpermiso
                WHERE up.idusuario = :idusuario
                AND sp.nombre = :modulo
                LIMIT 1
            ";

        }

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':idusuario' => $idusuario,
            ':modulo' => $modulo
        ]);

        return (bool) $stmt->fetchColumn();
    }


    public function esSuperusuario($idusuario = null): bool
    {
        $idUsuario = $idusuario ?? $_SESSION['idusuario'];

        if ($idUsuario === null) {
            return false;
        }

        $stmt = $this->pdo->prepare("
            SELECT superusuario
            FROM usuario
            WHERE idusuario = :idusuario
            LIMIT 1
        ");

        $stmt->execute([
            ':idusuario' => $idUsuario
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function dataArchivosAdjuntos($idseguimiento)
    {
        $sql = "SELECT * FROM seguimiento_adjuntos WHERE idseguimiento = $idseguimiento";
        $rspta = ejecutarConsulta($sql);
        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = $reg;
        }

        return json_encode($data);
    }



    public function verificarMoraCredito($idsucursal): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                is_mora_credito,
                valor_mora_credito
            FROM sucursal_configuracion
            WHERE idsucursal = :idsucursal
            LIMIT 1
        ");

        $stmt->execute([
            ':idsucursal' => $idsucursal
        ]);

        $config = $stmt->fetch(PDO::FETCH_ASSOC);

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
        $stmt = $this->pdo->prepare("
            SELECT
                is_descuento_anticipado,
                valor_descuento_anticipado,
                dias_anticipacion
            FROM sucursal_configuracion
            WHERE idsucursal = :idsucursal
            LIMIT 1
        ");

        $stmt->execute([
            ':idsucursal' => $idsucursal
        ]);

        $config = $stmt->fetch(PDO::FETCH_ASSOC);

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
        $stmt = $this->pdo->prepare("
            SELECT
                is_refinanciamiento,
                maximo_refinanciamientos
            FROM sucursal_configuracion
            WHERE idsucursal = :idsucursal
            LIMIT 1
        ");

        $stmt->execute([
            ':idsucursal' => $idsucursal
        ]);

        $config = $stmt->fetch(PDO::FETCH_ASSOC);

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



    public function toFloat($valor)
    {
        return is_numeric($valor) ? (float) $valor : 0.0;
    }


    public function verificarAperturaCaja($idcaja): array
    {
        $sql = "
            SELECT ca.*
            FROM caja_apertura ca
            INNER JOIN cajas c ON c.idcaja = ca.idcaja
            WHERE ca.estado = 1
            AND ca.idcaja = :idcaja
            AND ca.fecha_cierre IS NULL
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':idcaja' => $idcaja
        ]);

        $rpta = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'success' => $rpta !== false
        ];
    }

    public function verificarAperturaCajaUsuario(int $idsucursal, int $idusuario): int
    {
        $sql = "
            SELECT ca.idcaja
            FROM caja_apertura ca
            INNER JOIN cajas c ON c.idcaja = ca.idcaja
            WHERE ca.estado = 1
            AND ca.idsucursal = :idsucursal
            AND ca.idusuario = :idusuario
            AND ca.fecha_cierre IS NULL
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'idsucursal' => $idsucursal,
            'idusuario' => $idusuario,
        ]);

        $rpta = $stmt->fetch(PDO::FETCH_ASSOC);

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

        $sql = "
            SELECT COALESCE(MAX(correlativo),0) + 1 AS correlativo
            FROM traslado
            WHERE idorigen = :idsucursal
            AND tipo = :tipo
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'idsucursal' => $idsucursal,
            'tipo' => $tipo
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $correlativo = (int) $row['correlativo'];

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

        return (bool)($sucursal['is_send_sunat'] ?? false);
    }

    public function dataSucursal(int $idsucursal)
    {
        return (new DBQuery($this->pdo))
            ->from('sucursal s')
            ->join('empresas e', 's.idempresa = e.idempresa')
            ->where('idsucursal', '=', $idsucursal)
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

    // public function obtenerComprobanteSucursal(int $idtipo_comprobante, int $idsucursal): array
    // {
    //     $comprobante = (new DBQuery($this->pdo))
    //         ->select("idcomprobante_pago, serie_comprobante, num_comprobante")
    //         ->from("comp_pago")
    //         ->where("idcomprobante_pago", "=", (int) $idtipo_comprobante)
    //         ->where("idempresa", "=", $this->getEmpresa($idsucursal))
    //         ->first() ?? [];

    //     if ($comprobante) {
    //         $numero = (int) $comprobante['num_comprobante'] + 1;
    //         if ($numero > 99999) {
    //             $numero = 1;
    //         }

    //         $comprobante['num_comprobante'] = str_pad($numero, 6, '0', STR_PAD_LEFT);
    //     }

    //     return $comprobante;
    // }

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
}