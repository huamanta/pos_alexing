<?php
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";
require_once 'Helpers.php';
class Cajas extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    //Implementamos un mÃ©todo para insertar registros
    public function insertar($nombre, $numero, $idsucursal)
    {
        $sql = "INSERT INTO cajas (nombre,numero,idsucursal)
        VALUES ('$nombre','$numero','$idsucursal')";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para editar registros
    public function editar($idcaja, $nombre, $numero)
    {
        $sql = "UPDATE cajas SET nombre='$nombre',numero='$numero' WHERE idcaja='$idcaja'";
        return ejecutarConsulta($sql);
    }

    public function listar($idsucursal)
    {

        $abierto = 2;

        $sql = "SELECT 
                c.*, 
                CASE 
                    WHEN c.estado = $abierto THEN pe.nombre 
                    ELSE '' 
                END AS personal,
                s.nombre AS almacen, 
                s.idsucursal
            FROM cajas c
            LEFT JOIN (
                SELECT ca1.*
                FROM caja_apertura ca1
                INNER JOIN (
                    SELECT idcaja, MAX(aperturacajaid) AS ultima_apertura
                    FROM caja_apertura
                    GROUP BY idcaja
                ) ca2 ON ca1.aperturacajaid = ca2.ultima_apertura
            ) ca ON c.idcaja = ca.idcaja
            LEFT JOIN usuario u ON ca.idusuario = u.idusuario
            LEFT JOIN personal pe ON u.idpersonal = pe.idpersonal
            LEFT JOIN sucursal s ON c.idsucursal = s.idsucursal
            WHERE c.idsucursal = '$idsucursal' AND c.deleted_at IS NULL";

        return ejecutarConsulta($sql);
    }



    //Implementamos un método para desactivar 
    public function desactivar($idcaja)
    {
        try {

            $verificarAperturaCaja = Helpers::verificarAperturaCaja($idcaja);

            if ($verificarAperturaCaja) {
                throw new Exception("La caja esta abierta no se puede descativar.");
            }

            $sql = "UPDATE cajas
                SET estado = '0'
                WHERE idcaja = '$idcaja'";

            if (!ejecutarConsulta($sql)) {
                throw new Exception("No se pudo desactivar la caja.");
            }

            return json_encode(['success' => true, 'message' => 'La caja se desactivo con exito']);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    //Implementamos un método para activar 
    public function activar($idcaja)
    {
        try {
            $sql = "UPDATE cajas SET estado='1' WHERE idcaja='$idcaja'";
            if (!ejecutarConsulta($sql)) {
                throw new Exception("No se pudo desactivar la caja.");
            }
            return json_encode(['success' => true, 'message' => 'La caja se activo con exito']);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    //Implementar un método para mostrar los datos de un registro a modificar
    public function mostrar($idcaja)
    {
        $sql = "SELECT * FROM cajas WHERE idcaja='$idcaja'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function historialCajas($idcaja, $limit = 10, $offset = 0)
    {
        $sqlTotal = "SELECT COUNT(*)
                 FROM caja_apertura
                 WHERE idcaja = '$idcaja'";

        $total = ejecutarConsultaSimpleFila($sqlTotal);

        $sql = "SELECT 
                ca.aperturacajaid,
                ca.idcaja,
                ca.idusuario,
                ca.fecha_apertura,
                ca.fecha_cierre,
                ca.efectivo_apertura,
                ca.efectivo_cierre,
                c.numero,
                c.nombre,
                pe.nombre AS personal,

                (
                    SELECT COUNT(*)
                    FROM venta v
                    WHERE v.idcaja = ca.idcaja
                    AND v.fecha_hora BETWEEN ca.fecha_apertura
                        AND IFNULL(ca.fecha_cierre, NOW())
                ) AS cantventas,

                (
                    SELECT IFNULL(SUM(v.total_venta), 0)
                    FROM venta v
                    WHERE v.idcaja = ca.idcaja
                    AND v.fecha_hora BETWEEN ca.fecha_apertura
                        AND IFNULL(ca.fecha_cierre, NOW())
                ) AS totalventas

            FROM caja_apertura ca
            INNER JOIN cajas c ON c.idcaja = ca.idcaja
            INNER JOIN usuario u ON u.idusuario = ca.idusuario
            INNER JOIN personal pe ON pe.idpersonal = u.idpersonal

            WHERE ca.idcaja = '$idcaja'

            ORDER BY ca.aperturacajaid DESC
            LIMIT $offset, $limit";

        $rspta = ejecutarConsulta($sql);

        $data = [];

        while ($reg = $rspta->fetch_object()) {

            $supeUsuario = Helpers::esSuperusuario();
            $pemisoUsuario = Helpers::getUserPermissionAccion('Cerrar caja');
            $puedeCerrarCaja = $supeUsuario || ($pemisoUsuario && $reg->idusuario == $_SESSION['idusuario']);

            $data[] = [
                'idcaja' => $reg->idcaja,
                'aperturacajaid' => $reg->aperturacajaid,
                'numero' => $reg->numero,
                'nombre' => $reg->nombre,
                'personal' => $reg->personal,
                'fecha_apertura' => $reg->fecha_apertura,
                'efectivo_apertura' => '<span class="badge bg-danger">S/ ' . number_format($reg->efectivo_apertura, 2) . '</span>',
                'fecha_cierre' => $reg->fecha_cierre,
                'efectivo_cierre' => '<span class="badge bg-success">S/ ' . number_format($reg->efectivo_cierre, 2) . '</span>',
                'cantventas' => $reg->cantventas,
                'totalventas' => $reg->totalventas,
                'puede_cerrar_caja' => $puedeCerrarCaja,
            ];
        }

        return [
            'total' => (int) $total,
            'rows' => $data
        ];
    }

    public function listarPorApertura($aperturacajaid)
    {
        $sqlap = "SELECT fecha_apertura, fecha_cierre, idcaja 
              FROM caja_apertura 
              WHERE aperturacajaid = '$aperturacajaid'
              LIMIT 1";

        $ap = ejecutarConsulta($sqlap)->fetch_object();

        if (!$ap)
            return [];

        $inicio = $ap->fecha_apertura;
        $fin = !empty($ap->fecha_cierre) ? $ap->fecha_cierre : date('Y-m-d H:i:s');
        $idcaja = $ap->idcaja;

        $sql = "SELECT m.*
            FROM movimiento m
            WHERE m.idcaja = '$idcaja'
              AND m.fecha BETWEEN '$inicio' AND '$fin'
            ORDER BY m.idmovimiento DESC";

        $rspta = ejecutarConsulta($sql);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->fecha,
                "1" => $reg->descripcion,
                "2" => ($reg->tipo == 'Egresos')
                    ? '<span class="badge bg-red">EGRESO</span>'
                    : '<span class="badge bg-green">INGRESO</span>',
                "3" => $reg->formapago,
                "4" => Helpers::get_currency_symbol($reg->totalefectivo),
                "5" => Helpers::get_currency_symbol($reg->totaldeposito),
                "6" => Helpers::get_currency_symbol($reg->totalefectivo + $reg->totaldeposito)
            );
        }

        return json_encode([
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ]);
    }

    public function listarCobrrosPorApertura($aperturacajaid)
    {
        $sqlap = "SELECT fecha_apertura, fecha_cierre, idcaja 
              FROM caja_apertura 
              WHERE aperturacajaid = '$aperturacajaid'
              LIMIT 1";

        $ap = ejecutarConsulta($sqlap)->fetch_object();

        if (!$ap)
            return [];

        $inicio = $ap->fecha_apertura;
        $fin = !empty($ap->fecha_cierre) ? $ap->fecha_cierre : date('Y-m-d H:i:s');
        $idcaja = $ap->idcaja;

        $sql = "SELECT dcc.*
            FROM detalle_cuentas_por_cobrar dcc
            WHERE dcc.idcaja = '$idcaja'
              AND dcc.fechapago BETWEEN '$inicio' AND '$fin'
            ORDER BY dcc.iddcpc DESC";

        $rspta = ejecutarConsulta($sql);

        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->fechapago,
                "1" => $this->getClienteVenta($reg->idcpc),
                "2" => $reg->formapago,
                "3" => $reg->observacion,
                "4" => $reg->montopagado,
                "5" => $reg->montotarjeta,
            );
        }

        return array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
    }


    public function getClienteVenta($idcpc)
    {
        $sql = "SELECT * FROM cuentas_por_cobrar cc
        INNER JOIN venta v ON v.idventa = cc.idventa
        INNER JOIN persona p ON v.idcliente = p.idpersona
        WHERE cc.idcpc = $idcpc";

        $data = ejecutarConsultaSimpleFila($sql);

        return $data['nombre'];
    }

    public function listarPagosPorApertura($aperturacajaid)
    {
        $sqlap = "SELECT fecha_apertura, fecha_cierre, idcaja 
              FROM caja_apertura 
              WHERE aperturacajaid = '$aperturacajaid'
              LIMIT 1";

        $ap = ejecutarConsulta($sqlap)->fetch_object();

        if (!$ap)
            return [];

        $inicio = $ap->fecha_apertura;
        $fin = !empty($ap->fecha_cierre) ? $ap->fecha_cierre : date('Y-m-d H:i:s');
        $idcaja = $ap->idcaja;

        $sql = "SELECT dcp.*
            FROM detalle_cuentas_por_pagar dcp
            WHERE dcp.idcaja = '$idcaja'
              AND dcp.fechapago BETWEEN '$inicio' AND '$fin'
            ORDER BY dcp.iddcpp DESC";

        $rspta = ejecutarConsulta($sql);

        $data = array();
        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => $reg->fechapago,
                "1" => $this->getProveedorCompra($reg->idcpp),
                "2" => $reg->formapago,
                "3" => $reg->observacion,
                "4" => $reg->montopagado,
                "5" => $reg->montotarjeta,
            );
        }

        return array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
    }


    public function getProveedorCompra($idcpp)
    {
        $sql = "SELECT * FROM cuentas_por_paga cp
        INNER JOIN compra c ON c.idventa = cp.icompra
        INNER JOIN persona p ON c.idcliente = p.idpersona
        WHERE cp.idcpp = $idcpp";

        $data = ejecutarConsultaSimpleFila($sql);

        return $data['nombre'];
    }

}
