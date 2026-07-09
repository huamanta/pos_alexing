<?php
require_once __DIR__ . '/../configuraciones/local.php';
class Helpers
{
    private static $conexion = null;

    private static function getConexion()
    {
        if (self::$conexion === null) {
            self::$conexion = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
            if (self::$conexion->connect_error) {
                die("Error de conexión en Helpers: " . self::$conexion->connect_error);
            }
            if (!self::$conexion->set_charset(DB_ENCODE)) {
                die("Error al establecer el charset en Helpers: " . self::$conexion->error);
            }
        }
        return self::$conexion;
    }

    private static function ejecutarConsultaSimpleFila($sql)
    {
        $conexion = self::getConexion();
        $query = $conexion->query($sql);
        if (!$query) {
            throw new Exception($conexion->error);
        }
        $row = $query->fetch_assoc();
        return $row;
    }


    public static function get_currency_symbol($monto, $currency = 'PEN', $locale = "es_PE")
    {
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

    public static function get_symbol($currency = 'PEN', $locale = "es_PE")
    {
        if (!class_exists('NumberFormatter')) {
            return $currency;
        }

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        // Truco: formatear 0 y extraer símbolo
        $formatted = $formatter->formatCurrency(0, $currency);

        // Quitar números y dejar solo símbolo
        return trim(preg_replace('/[0-9\.\,\s]/', '', $formatted));
    }

    public static function get_currency_code($idsucursal)
    {
        $sql = "SELECT moneda FROM sucursal WHERE idsucursal = '$idsucursal'";
        $result = self::ejecutarConsultaSimpleFila($sql);
        return $result['moneda'] ?? 'PEN'; // Devuelve 'PEN' por defecto si no se encuentra la moneda
    }


    public static function getUserPermissionAccion($nombre_permiso)
    {
        $idusuario = $_SESSION['idusuario'] ?? NULL;

        if ($idusuario === NULL) {
            return false;
        }

        if ($nombre_permiso === NULL) {
            return false;
        }

        // 1. Verificar si es superusuario
        $usuario_sql = "SELECT * FROM usuario WHERE idusuario = $idusuario";
        $data_usuario = self::ejecutarConsultaSimpleFila($usuario_sql);

        if ($data_usuario && isset($data_usuario['superusuario']) && $data_usuario['superusuario'] == 1) {
            return true;
        }

        // 2. Verificar permisos por usuario
        $usuario_permiso_sql = "SELECT ap.nombre
            FROM usuario_accion ua
            INNER JOIN accion_permiso ap 
                ON ua.idaccion_permiso = ap.idaccion_permiso 
            WHERE ua.idusuario = $idusuario 
            AND ap.nombre = '$nombre_permiso'";

        $permiso_usuario = self::ejecutarConsultaSimpleFila($usuario_permiso_sql);

        if ($permiso_usuario) {
            return true;
        }

        // 3. (Opcional) Aquí podrías validar roles si usas roles
        // Ejemplo:
        /*
        $rol_sql = "SELECT ap.nombre_permiso 
            FROM usuario_rol ur
            INNER JOIN rol_permiso rp ON ur.idrol = rp.idrol
            INNER JOIN accion_permiso ap ON rp.idaccion_permiso = ap.idaccion_permiso
            WHERE ur.idusuario = $idusuario 
            AND ap.nombre_permiso = '$permiso'";

        $permiso_rol = ejecutarConsultaSimpleFila($rol_sql);

        if($permiso_rol){
            return true;
        }
        */

        // 4. Si no tiene nada
        return false;
    }



    public static function getUserPermisoModulo($modulo, $modulo_parent = null)
    {
        $idusuario = $_SESSION['idusuario'] ?? NULL;

        if ($idusuario === NULL) {
            return false;
        }

        if ($modulo === NULL) {
            return false;
        }

        // 1. Verificar si es superusuario
        $usuario_sql = "SELECT * FROM usuario WHERE idusuario = $idusuario";
        $data_usuario = self::ejecutarConsultaSimpleFila($usuario_sql);

        if ($data_usuario && isset($data_usuario['superusuario']) && $data_usuario['superusuario'] == 1) {
            return true;
        }

        // es mosulo padre o no
        if ($modulo_parent === null) {
            $modulo_sql = "SELECT *
                FROM usuario_permiso up
                INNER JOIN permiso p ON up.idpermiso = p.idpermiso
                WHERE up.idusuario = $idusuario 
                AND p.nombre = '$modulo'";
        } else {
            $modulo_sql = "SELECT *
                FROM usuario_permiso up
                INNER JOIN subpermiso p ON up.idsubpermiso = p.idsubpermiso
                WHERE up.idusuario = $idusuario 
                AND p.nombre = '$modulo'";
        }

        $permiso_usuario = self::ejecutarConsultaSimpleFila($modulo_sql);
        if ($permiso_usuario) {
            return true;
        }

        return false;

    }

    public static function esSuperusuario()
    {
        $idusuario = $_SESSION['idusuario'];

        $usuario = self::ejecutarConsultaSimpleFila("
                        SELECT superusuario
                        FROM usuario
                        WHERE idusuario = '$idusuario'
                    ");

        return $usuario && $usuario['superusuario'] == 1;
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



    public function verificarMoraCredito($idsucursal)
    {
        $sql = "SELECT
                is_mora_credito,
                valor_mora_credito
            FROM sucursal_configuracion
            WHERE idsucursal = '$idsucursal'
            LIMIT 1";

        $config = self::ejecutarConsultaSimpleFila($sql);

        if (!$config) {
            return [
                "activo" => false,
                "valor" => 0
            ];
        }

        return [
            "activo" => (int) $config["is_mora_credito"] === 1,
            "valor" => (float) $config["valor_mora_credito"]
        ];
    }


    public function verificarDecuentoPagoAnticipado($idsucursal)
    {
        $sql = "SELECT
                is_descuento_anticipado,
                valor_descuento_anticipado,
                dias_anticipacion
            FROM sucursal_configuracion
            WHERE idsucursal = '$idsucursal'
            LIMIT 1";

        $config = self::ejecutarConsultaSimpleFila($sql);

        if (!$config) {
            return [
                "activo" => false,
                "valor" => 0,
                "dias_anticipacion" => 0
            ];
        }

        return [
            "activo" => (int) $config["is_descuento_anticipado"] === 1,
            "valor" => (float) $config["valor_descuento_anticipado"],
            "dias_anticipacion" => $config["dias_anticipacion"]
        ];
    }


    public function verificarRefinanciamientos($idsucursal)
    {
        $sql = "SELECT
                is_refinanciamiento,
                maximo_refinanciamientos
            FROM sucursal_configuracion
            WHERE idsucursal = '$idsucursal'
            LIMIT 1";

        $config = self::ejecutarConsultaSimpleFila($sql);

        if (!$config) {
            return [
                "activo" => false,
                "valor" => 0
            ];
        }

        return [
            "activo" => (int) $config["is_refinanciamiento"] === 1,
            "valor" => (float) $config["maximo_refinanciamientos"]
        ];
    }



    public function toFloat($valor)
    {
        return is_numeric($valor) ? (float) $valor : 0.0;
    }


    public function verificarAperturaCaja($idcaja)
    {
        $sql = "SELECT ca.*
                FROM caja_apertura ca
                INNER JOIN cajas c ON c.idcaja = ca.idcaja
                WHERE ca.estado = 1 
                  AND ca.idcaja = '$idcaja'
                  AND ca.fecha_cierre IS NULL
                LIMIT 1";

        $rpta = ejecutarConsultaSimpleFila($sql);
        if (!$rpta) {
            return array('success' => false);
        }

        return array('success' => true);
    }
}