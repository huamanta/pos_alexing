<?php
require "../configuraciones/Conexion.php";

class Servicio
{
    public function __construct() {}

    public function insertar($idsucursal, $tipo_comprobante, $serie_comprobante, $num_comprobante, $idcliente, $equipo, $idtecnico, $fecha_ingreso, $descripcion_problema, $descripcion_solucion, $productos, $total)
	{
		if (empty($num_comprobante)) {
	        $numc = "SELECT serie_comprobante, num_comprobante FROM servicio WHERE tipo_comprobante = '$tipo_comprobante' AND idsucursal = '$idsucursal' ORDER BY idservicio DESC LIMIT 1";
	        $existeNum = ejecutarConsulta($numc);
	        $v = 0;
	        while ($regn = $existeNum->fetch_object()) {
	            $c = $regn->serie_comprobante;
	            $v = $regn->num_comprobante;
	        }
	        if (!empty($v)) {
	            $serie_comprobante = $c;
	            $num = $v + 1;
	            $num_comprobante = str_pad($num, 7, "0", STR_PAD_LEFT);
	        } else {
	            $num_comprobante = '0000001';
	        }
	    }

	    $existeComprobante = "SELECT * FROM servicio WHERE serie_comprobante = '$serie_comprobante' AND num_comprobante = '$num_comprobante' AND idsucursal = '$idsucursal'";
	    $existeCompro = ejecutarConsulta($existeComprobante);
	    if ($existeCompro->num_rows > 0) {
	        $sqlUltimoC = "SELECT idservicio, num_comprobante FROM servicio WHERE tipo_comprobante = '$tipo_comprobante' AND idsucursal = '$idsucursal' ORDER BY idservicio DESC LIMIT 1";
	        $ultimoComprobante = ejecutarConsulta($sqlUltimoC);
	        $var2 = 0;
	        while ($reg = $ultimoComprobante->fetch_object()) {
	            $var2 = $reg->num_comprobante;
	        }
	        if ($var2 > 0) {
	            $num_comprobante = str_pad($var2 + 1, 7, "0", STR_PAD_LEFT);
	        }
	    }
	    $fecha_ingreso = date("Y-m-d H:i:s", strtotime($fecha_ingreso));
	    $sql = "INSERT INTO servicio (idsucursal, tipo_comprobante, serie_comprobante, num_comprobante, idcliente, equipo, idtecnico, fecha_ingreso, descripcion_problema, descripcion_solucion, estado, total)
	            VALUES ('$idsucursal', '$tipo_comprobante', '$serie_comprobante', '$num_comprobante', '$idcliente', '$equipo', '$idtecnico', '$fecha_ingreso', '$descripcion_problema', '$descripcion_solucion', 'Recibido', '$total')";
	    
	    $idservicio = ejecutarConsulta_retornarID($sql);

	    if ($idservicio) {
	        foreach ($productos as $producto) {
	            $idproducto = $producto["idproducto"];
	            $nombre = limpiarCadena($producto["nombre"]);
	            $cantidad = $producto["cantidad"];
	            $precio = $producto["precio"];

	            $sql_detalle = "INSERT INTO detalle_servicio (idservicio, idproducto, nombre, cantidad, precio)
	                            VALUES ('$idservicio', '$idproducto', '$nombre', '$cantidad', '$precio')";
	            ejecutarConsulta($sql_detalle);
	        }
	        return true;
	    } else {
	        return false;
	    }
	}
	
    public function editar($idservicio, $idsucursal, $tipo_comprobante, $serie_comprobante, $num_comprobante, $idcliente, $equipo,$idtecnico, $estado, $fecha_reparacion, $fecha_entrega,$descripcion_problema, $descripcion_solucion, $productos, $total)
	{
	    $sql = "UPDATE servicio 
	            SET idsucursal='$idsucursal', idcliente='$idcliente', equipo='$equipo', idtecnico='$idtecnico', estado='$estado', fecha_reparacion='$fecha_reparacion', fecha_entrega='$fecha_entrega', descripcion_problema='$descripcion_problema', descripcion_solucion='$descripcion_solucion', total='$total' 
	            WHERE idservicio='$idservicio'";
	    $ok = ejecutarConsulta($sql);

	    // Eliminar servicios anteriores
	    $sqldel = "DELETE FROM detalle_servicio WHERE idservicio='$idservicio'";
	    ejecutarConsulta($sqldel);

	    // Insertar nuevos servicios
	    foreach ($productos as $producto) {
	        $idproducto = $producto["idproducto"];
	        $nombre = limpiarCadena($producto["nombre"]);
	        $cantidad = $producto["cantidad"];
	        $precio = $producto["precio"];

	        $sql_detalle = "INSERT INTO detalle_servicio (idservicio, idproducto, nombre, cantidad, precio)
	                        VALUES ('$idservicio', '$idproducto', '$nombre','$cantidad', '$precio')";
	        ejecutarConsulta($sql_detalle);
	    }

	    return $ok;
	}

    public function mostrar($idservicio)
    {
        $sql = "SELECT s.idservicio,s.idsucursal,s.idcliente,s.idtecnico,s.tipo_comprobante,s.serie_comprobante,s.num_comprobante,p.nombre as cliente,su.nombre as sucursal,s.equipo,pe.nombre as tecnico,s.descripcion_problema,s.descripcion_solucion,s.fecha_ingreso,s.fecha_reparacion,s.fecha_entrega,s.total,s.estado FROM servicio s
        INNER JOIN sucursal su ON s.idsucursal = su.idsucursal
        INNER JOIN persona p ON s.idcliente=p.idpersona
		INNER JOIN personal pe ON s.idtecnico = pe.idpersonal
        WHERE s.idservicio='$idservicio'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar($fecha_inicio, $fecha_fin, $estado, $idsucursal)
    {
        $sql = "SELECT s.idservicio,s.tipo_comprobante,s.serie_comprobante,s.num_comprobante,p.nombre as cliente,su.nombre as sucursal,s.equipo,pe.nombre as tecnico,s.fecha_ingreso,s.fecha_entrega,s.total,s.estado
		FROM servicio s
		INNER JOIN sucursal su ON s.idsucursal = su.idsucursal
		INNER JOIN persona p ON s.idcliente=p.idpersona
		INNER JOIN personal pe ON s.idtecnico = pe.idpersonal
		WHERE s.estado IN ('Recibido','En proceso','Terminado','Entregado')
		AND DATE(s.fecha_ingreso) >= '$fecha_inicio' 
		AND DATE(s.fecha_ingreso) <= '$fecha_fin'";
		// Filtrado por sucursal (si aplica)
	    if ($idsucursal != "Todos") {
	        $sql .= " AND s.idsucursal = '$idsucursal'";
	    }

	    // Filtrado por estado (si aplica)
	    if ($estado != "Todos") {
	        $sql .= " AND s.estado = '$estado'";
	    }

	    $sql .= " ORDER BY s.idservicio DESC";
        return ejecutarConsulta($sql);
    }

    public function listarServicios() {
	  $sql = "SELECT idproducto, nombre, precio FROM producto WHERE condicion = 1";
	  $query = $this->conexion->prepare($sql);
	  $query->execute();
	  return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listarDetalle($idservicio) {
	    $sql = "SELECT ds.idproducto, ds.nombre, ds.cantidad, ds.precio
	            FROM detalle_servicio ds
	            WHERE ds.idservicio = '$idservicio'";
	    return ejecutarConsulta($sql);
	}

	public function numero_venta_ticket2($idsucursal)
	{

		$sql = "SELECT num_comprobante FROM servicio WHERE tipo_comprobante='Ticket' 
			AND idsucursal = '$idsucursal'
			ORDER BY idservicio DESC limit 1";
		return ejecutarConsulta($sql);
	}
	//funcion para seleccionar la serie de la ticket
	public function numero_serie_ticket2($idsucursal)
	{

		$sql = "SELECT REPLACE(serie_comprobante,'T','') AS serie_comprobante, num_comprobante 
		FROM servicio WHERE tipo_comprobante='Ticket' AND idsucursal = '$idsucursal' 
		ORDER BY idservicio DESC limit 1";

		return ejecutarConsulta($sql);
	}

	public function eliminar($idservicio) {
	    $sql1 = "DELETE FROM detalle_servicio WHERE idservicio = $idservicio";
	    $sql2 = "DELETE FROM servicio WHERE idservicio = $idservicio";

	    ejecutarConsulta($sql1); // Llama como función global, igual que en el resto del modelo
	    return ejecutarConsulta($sql2);
	}

	public function serviciocabecera($idservicio)
	{
		$sql = "SELECT s.idservicio,s.idsucursal,p.idpersona,s.tipo_comprobante,s.serie_comprobante,s.num_comprobante,p.nombre as cliente,p.direccion,p.num_documento,p.tipo_documento,su.nombre as sucursal,s.equipo,pe.nombre as tecnico,s.descripcion_problema,s.descripcion_solucion,date_format(s.fecha_ingreso,'%d/%m/%y | %H:%i:%s %p') as fecha_ingreso,date_format(s.fecha_entrega,'%d/%m/%y | %H:%i:%s %p') as fecha_entrega, s.fecha_reparacion,s.total,s.estado FROM servicio s
        INNER JOIN sucursal su ON s.idsucursal = su.idsucursal
				INNER JOIN personal pe ON s.idtecnico = pe.idpersonal
				INNER JOIN persona p ON s.idcliente = p.idpersona
        WHERE s.idservicio='$idservicio'";
		return ejecutarConsulta($sql);
	}

	public function serviciodetalle($idservicio)
	{

		$sql = "SELECT a.idproducto,a.codigo, d.nombre, d.cantidad, d.precio, (d.cantidad*d.precio) AS subtotal, a.stock, a.proigv,s.total 
		FROM detalle_servicio d 
		INNER JOIN producto a 
		ON d.idproducto=a.idproducto 
		INNER JOIN servicio s
		ON s.idservicio = d.idservicio
		WHERE d.idservicio='$idservicio'";
		return ejecutarConsulta($sql);
	}

}
?>
