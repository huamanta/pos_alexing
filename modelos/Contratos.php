<?php
require "../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');

class Contratos {

    public function __construct() {
    }

    public function listar($fecha_inicio, $fecha_fin) {
        $query = "SELECT 
c.idpersona,
c.nombre,
c.num_documento,
c.direccion,
v.formapago,
v.num_comprobante,
v.serie_comprobante,
v.tipo_comprobante,
v.total_venta,
d.iddocumento,
v.idventa,
d.fecha_contrato,
d.tipo,
d.correlativo,
d.estado
FROM documentacion d 
INNER JOIN venta v ON d.idventa = v.idventa 
LEFT JOIN persona c ON v.idcliente = c.idpersona
WHERE d.tipo = 'C'";
        $stmt = ejecutarConsulta($query);
        return $stmt;
    }
}
