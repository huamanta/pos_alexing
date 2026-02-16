<?php
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";
class Cajas
{
    public function __construct()
    {
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

    public function listar($cargo, $idsucursal)
{
    $condicion = ($cargo == 'Administrador') ? '' : " AND c.idsucursal = '$idsucursal'";

    $sql = "SELECT 
                c.*, 
                CASE 
                    WHEN c.estado = 2 THEN pe.nombre 
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
            WHERE c.deleted_at IS NULL $condicion";

    return ejecutarConsulta($sql);
}



    //Implementamos un método para desactivar 
     public function desactivar($idcaja)
    {
        $sql = "UPDATE cajas SET estado='0' WHERE idcaja='$idcaja'";
        return ejecutarConsulta($sql);
    }

    //Implementamos un método para activar 
    public function activar($idcaja)
    {
        $sql = "UPDATE cajas SET estado='1' WHERE idcaja='$idcaja'";
        return ejecutarConsulta($sql);
    }

    //Implementar un método para mostrar los datos de un registro a modificar
    public function mostrar($idcaja)
    {
        $sql = "SELECT * FROM cajas WHERE idcaja='$idcaja'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function historialCajas($fecha_inicio, $fecha_fin)
    {
        $sql = "SELECT c.*, ca.*, pe.nombre as personal FROM caja_apertura ca 
            INNER JOIN cajas c ON c.idcaja = ca.idcaja 
            INNER JOIN usuario u ON ca.idusuario= u.idusuario
            INNER JOIN personal pe ON u.idpersonal = pe.idpersonal 
            WHERE DATE(ca.fecha_apertura)>='$fecha_inicio' AND DATE(ca.fecha_cierre)<='$fecha_fin'";
        $rspta = ejecutarConsulta($sql);
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $m = 0;
            if ($reg->fecha_cierre != '') {
                $sql2 = "SELECT * FROM venta WHERE idcaja = '$reg->idcaja' AND fecha_hora>='$reg->fecha_apertura' AND fecha_hora<= '$reg->fecha_cierre'";
            } else {
                $sql2 = "SELECT * FROM venta WHERE idcaja = '$reg->idcaja' AND fecha_hora>='$reg->fecha_apertura' AND fecha_hora<= NOW()";
            }
            $rspta2 = ejecutarConsulta($sql2);
            $ventasdata = array();
            while ($reg2 = $rspta2->fetch_object()) {
                $m++;
                $ventasdata[] = $reg2;
            }
            $data[] = array(
                'aperturacajaid' => $reg->aperturacajaid,
                'numero' => $reg->numero,
                'nombre' => $reg->nombre,
                'personal' => $reg->personal,
                'fecha_apertura' => $reg->fecha_apertura,
                'efectivo_apertura' => '<span class="badge bg-red">'.'S/ '.$reg->efectivo_apertura.'</span>',
                'fecha_cierre' => $reg->fecha_cierre,
                'efectivo_cierre' => '<span class="badge bg-green">'.'S/ '.$reg->efectivo_cierre.'</span>',
                'cantventas' => $m,
                'ventas' => $ventasdata
            );
        }
        return $data;
    }

    public function listarPorApertura($aperturacajaid)
    {
        $sql = "SELECT m.*
                FROM movimiento m
                INNER JOIN cajas c ON m.idcaja = c.idcaja
                INNER JOIN caja_apertura a ON c.idcaja = a.idcaja
                WHERE a.aperturacajaid = '$aperturacajaid'
                  AND DATE(m.fecha) BETWEEN DATE(a.fecha_apertura)
                                       AND IF(a.fecha_cierre IS NULL, CURDATE(), DATE(a.fecha_cierre))
                ORDER BY m.idmovimiento DESC";
        return ejecutarConsulta($sql);
    }

}
