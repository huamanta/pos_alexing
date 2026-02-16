<?php 
//incluir la conexion de base de datos
require "../configuraciones/Conexion.php";

date_default_timezone_set('America/Lima');

class Cotizacion{


	//implementamos nuestro constructor
public function __construct(){

}

//metodo insertar registro
public function insertar(
    $idsucursal,
    $idcliente,
    $idpersonal,
    $tipo_comprobante,
    $serie_comprobante,
    $num_comprobante,
    $fecha_hora,
    $total_venta,
    $titulo,
    $saludo,
    $nota,
    $igv,
    $formapago,
    $observaciones,
    $tiempoproduccion,
    $idproducto,
    $cantidad,
    $precio_venta,
    $descuento,
    $contenedor,
    $cantidad_contenedor,
    $idp
) {
    // ⚙️ Conexión global
    global $conexion;
    $sw = true;

    try {
        // 🚦 Iniciar transacción (bloquea la tabla para evitar duplicados)
        $conexion->begin_transaction();

        // 🔍 1️⃣ Obtener el último número actual (usando bloqueo)
        $sql_ultimo = "
            SELECT num_comprobante 
            FROM cotizacion 
            WHERE serie_comprobante = '$serie_comprobante' 
            AND tipo_comprobante = '$tipo_comprobante'
            ORDER BY idcotizacion DESC 
            LIMIT 1
            FOR UPDATE
        ";
        $rs = $conexion->query($sql_ultimo);

        if ($rs && $rs->num_rows > 0) {
            $row = $rs->fetch_assoc();
            $num_comprobante = str_pad(((int)$row['num_comprobante'] + 1), 6, "0", STR_PAD_LEFT);
        } else {
            $num_comprobante = "000001";
        }

        // 🧾 2️⃣ Insertar cabecera con el número nuevo
        $sql_cab = "INSERT INTO cotizacion (
                        idsucursal,
                        idcliente,
                        idpersonal,
                        tipo_comprobante,
                        serie_comprobante,
                        num_comprobante,
                        fecha_hora,
                        total_venta,
                        titulo,
                        saludo,
                        nota,
                        igv,
                        formapago,
                        observacion,
                        tiempo_pro,
                        estado
                    ) VALUES (
                        '$idsucursal',
                        '$idcliente',
                        '$idpersonal',
                        '$tipo_comprobante',
                        '$serie_comprobante',
                        '$num_comprobante',
                        '$fecha_hora',
                        '$total_venta',
                        '$titulo',
                        '$saludo',
                        '$nota',
                        '$igv',
                        '$formapago',
                        '$observaciones',
                        '$tiempoproduccion',
                        'EN ESPERA'
                    )";

        $idcotizacion_new = ejecutarConsulta_retornarID($sql_cab);

        // 🧩 3️⃣ Insertar detalles
        $num_elementos = 0;
        while ($num_elementos < count($idp)) {
            $sql_det = "INSERT INTO detalle_cotizacion (
                            idcotizacion,
                            idproducto,
                            cantidad,
                            contenedor,
                            cantidad_contenedor,
                            precio_venta,
                            descuento
                        ) VALUES (
                            '$idcotizacion_new',
                            '{$idp[$num_elementos]}',
                            '{$cantidad[$num_elementos]}',
                            '{$contenedor[$num_elementos]}',
                            '{$cantidad_contenedor[$num_elementos]}',
                            '{$precio_venta[$num_elementos]}',
                            '{$descuento[$num_elementos]}'
                        )";

            if (!ejecutarConsulta($sql_det)) {
                $sw = false;
                break;
            }

            $num_elementos++;
        }

        // 🧹 4️⃣ Limpiar temporales si todo fue bien
        if ($sw) {
            $conexion->query("DELETE FROM cotizacion_tmp WHERE idusuario = '$idpersonal'");
            $conexion->query("DELETE FROM cotizacion_cab_tmp WHERE idusuario = '$idpersonal'");
        }

        // ✅ 5️⃣ Confirmar transacción
        $conexion->commit();

        return $sw ? $num_comprobante : false;
    } catch (Exception $e) {
        // ❌ Si algo falla, revertir
        $conexion->rollback();
        error_log("Error en insertar cotización: " . $e->getMessage());
        return false;
    }
}


public function editar($idcotizacion,$idsucursal,$idcliente,$idpersonal,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$total_venta,$titulo,$saludo,$nota,$igv,$formapago,$observaciones,$tiempoproduccion,$idproducto,$cantidad,$precio_venta,$descuento,$contenedor,$cantidad_contenedor,$idp){

	$sql = "UPDATE cotizacion SET idsucursal='$idsucursal',idcliente='$idcliente', idpersonal='$idpersonal', tipo_comprobante='$tipo_comprobante', serie_comprobante='$serie_comprobante', num_comprobante='$num_comprobante',fecha_hora='$fecha_hora',total_venta='$total_venta',titulo='$titulo',nota='$nota',igv='$igv',formapago='$formapago',observacion='$observaciones',tiempo_pro='$tiempoproduccion' WHERE idcotizacion='$idcotizacion'";

	ejecutarConsulta($sql);

	$sql2 = "DELETE FROM detalle_cotizacion WHERE idcotizacion='$idcotizacion'";

	ejecutarConsulta($sql2);

	$num_elementos=0;
	$sw=true;
	while ($num_elementos < count($idproducto)) {

	$sql_detalle="INSERT INTO detalle_cotizacion (idcotizacion,idproducto,cantidad,contenedor,cantidad_contenedor,precio_venta,descuento) VALUES('$idcotizacion','$idp[$num_elementos]','$cantidad[$num_elementos]','$contenedor[$num_elementos]','$cantidad_contenedor[$num_elementos]','$precio_venta[$num_elementos]','$descuento[$num_elementos]')";

	ejecutarConsulta($sql_detalle) or $sw=false;

	$num_elementos=$num_elementos+1;

	}

	return $sw;
}

//Implementamos un método para desactivar categorías
public function eliminar($idcotizacion)
{
	$sql="UPDATE cotizacion SET condicion='0' WHERE idcotizacion='$idcotizacion'";
	return ejecutarConsulta($sql);
}

//implementar un metodopara mostrar los datos de unregistro a modificar
public function mostrar($idcotizacion){
	$sql="SELECT c.idcotizacion,DATE(c.fecha_hora) as fecha,c.idcliente,p.nombre as cliente,c.titulo,c.nota,c.igv,u.idpersonal,u.nombre as personal,p.telefono,c.tipo_comprobante,c.serie_comprobante,c.num_comprobante,c.total_venta,c.formapago,c.tiempo_pro 
	FROM cotizacion c 
	INNER JOIN persona p ON c.idcliente=p.idpersona 
	INNER JOIN personal u ON c.idPersonal=u.idpersonal 
	WHERE idcotizacion='$idcotizacion'";
	return ejecutarConsultaSimpleFila($sql);
}

public function mostrardetalle($idcotizacion){
	$sql="SELECT dv.idcotizacion,dv.idproducto,a.nombre,dv.cantidad,dv.precio_venta,dv.descuento,(dv.cantidad*dv.precio_venta-dv.descuento) as subtotal, v.total_venta, p.nombre as cliente, v.num_comprobante FROM detalle_cotizacion dv INNER JOIN producto a ON dv.idproducto=a.idproducto INNER JOIN cotizacion v ON v.idcotizacion=dv.idcotizacion INNER JOIN persona p ON v.idcliente=p.idpersona WHERE dv.idcotizacion='$idcotizacion'";
	return ejecutarConsulta($sql);
}

public function listarDetalle($idcotizacion){
	$sql="SELECT dv.idcotizacion,dv.idproducto,a.nombre,dv.cantidad_contenedor,dv.contenedor,dv.cantidad,dv.precio_venta,dv.descuento,(dv.cantidad*dv.precio_venta-dv.descuento) as subtotal, v.total_venta 
		FROM detalle_cotizacion dv
		INNER JOIN producto_configuracion pg ON dv.idproducto=pg.id 
		INNER JOIN producto a ON pg.idproducto=a.idproducto 
		INNER JOIN cotizacion v ON v.idcotizacion=dv.idcotizacion 
		WHERE dv.idcotizacion='$idcotizacion'";
	return ejecutarConsulta($sql);
}

public function desistir($idcotizacion){
	$sql="UPDATE COTIZACION SET estado = 'DESISTIO' where idcotizacion = '$idcotizacion'";
	return ejecutarConsulta($sql);
}

//listar registros
public function listar($fecha_inicio,$fecha_fin,$idsucursal){

	if($idsucursal == "Todos" || $idsucursal == ''){
		
		$sql="SELECT c.idcotizacion,date_format(c.fecha_h,'%d/%m/%y | %H:%i:%s %p') as fecha,c.idcliente,p.nombre as cliente,u.idpersonal,u.nombre 
		as personal, c.tipo_comprobante,c.serie_comprobante,c.num_comprobante,c.total_venta,c.estado FROM cotizacion c 
		INNER JOIN persona p ON c.idcliente=p.idpersona INNER JOIN personal u ON c.idPersonal=u.idpersonal 
		WHERE c.condicion = 1 AND DATE(c.fecha_hora)>='$fecha_inicio' AND DATE(c.fecha_hora)<='$fecha_fin'
		ORDER BY c.idcotizacion DESC";

	}else{

		$sql="SELECT c.idcotizacion,date_format(c.fecha_h,'%d/%m/%y | %H:%i:%s %p') as fecha,c.idcliente,p.nombre as cliente,u.idpersonal,u.nombre 
		as personal, c.tipo_comprobante,c.serie_comprobante,c.num_comprobante,c.total_venta,c.estado FROM cotizacion c 
		INNER JOIN persona p ON c.idcliente=p.idpersona INNER JOIN personal u ON c.idPersonal=u.idpersonal 
		WHERE c.condicion = 1 AND DATE(c.fecha_hora)>='$fecha_inicio' AND DATE(c.fecha_hora)<='$fecha_fin' AND c.idsucursal = '$idsucursal'
		ORDER BY c.idcotizacion DESC";

	}

	
	return ejecutarConsulta($sql);
}

public function listar2(){

	$sql="SELECT c.idcotizacion,DATE(c.fecha_hora) as fecha,c.idcliente,p.nombre as cliente,u.idpersonal,u.nombre 
		as personal, c.tipo_comprobante,c.serie_comprobante,c.num_comprobante,c.total_venta FROM cotizacion c 
		INNER JOIN persona p ON c.idcliente=p.idpersona INNER JOIN personal u ON c.idPersonal=u.idpersonal 
		WHERE c.condicion = 1 and c.estado = 'EN ESPERA'
		ORDER BY c.idcotizacion DESC";

	return ejecutarConsulta($sql);

}


public function ventacabecera($idcotizacion){
	$sql= "SELECT v.idcotizacion, v.idcliente, p.nombre AS cliente, v.titulo, v.nota, v.saludo, v.fecha_h, p.direccion, p.tipo_documento, p.num_documento, p.email, p.telefono, v.idpersonal, u.nombre AS personal, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, DATE(v.fecha_hora) AS fecha, v.total_venta FROM cotizacion v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal WHERE v.idcotizacion='$idcotizacion'";
	return ejecutarConsulta($sql);
}

public function ventadetalle($idcotizacion){
	$sql="SELECT pg.id,a.idproducto,pg.contenedor,pg.cantidad_contenedor, a.nombre AS producto, um.nombre as unidadmedida, a.idunidad_medida, CASE WHEN a.codigo = 'SIN CODIGO' THEN '-' ELSE a.codigo END as codigo, d.cantidad, d.precio_venta, a.preciocigv, d.descuento, (d.cantidad*d.precio_venta-d.descuento) AS subtotal, a.stock, a.precioB, a.precioC, a.precioD, a.imagen, a.proigv 
	FROM detalle_cotizacion d 
	LEFT JOIN producto_configuracion pg 
	ON pg.id=d.idproducto
	INNER JOIN producto a ON pg.idproducto=a.idproducto 
	INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida 
	WHERE d.idcotizacion='$idcotizacion'";
         return ejecutarConsulta($sql);
}

/*public function ventadetalle($idcotizacion)
	{
		// $sql="SELECT a.idproducto,a.nombre AS producto, CASE WHEN a.codigo = 'SIN CODIGO' THEN '-' ELSE a.codigo END as codigo, d.cantidad, d.precio_venta, d.descuento, (d.cantidad*d.precio_venta-d.descuento) AS subtotal, a.stock FROM detalle_venta d INNER JOIN producto a ON d.idproducto=a.idproducto WHERE d.idventa='$idventa'";
		//         return ejecutarConsulta($sql);

		$sql = "SELECT pg.id, a.idproducto, pg.contenedor,pg.cantidad_contenedor, a.nombre AS producto, um.nombre as unidadmedida, a.codigo, d.nombre_producto, d.cantidad, d.precio_venta, a.precioB, a.precioC, a.precioD, a.preciocigv, (d.descuento + v.descuento) AS descuento, (d.cantidad*d.precio_venta-d.descuento) AS subtotal, a.stock, a.proigv
		FROM detalle_venta d 
		LEFT JOIN producto_configuracion pg 
		ON pg.id=d.idproducto
		INNER JOIN producto a 
		ON pg.idproducto=a.idproducto 
		INNER JOIN unidad_medida um 
		ON a.idunidad_medida = um.idunidad_medida
		INNER JOIN venta v
		ON v.idventa = d.idventa
		WHERE d.idventa='$idventa'";
		return ejecutarConsulta($sql);
	}*/


//funcion para selecciolnar el numero de factura
public function numero_venta(){
		 
		    $sql="SELECT num_comprobante FROM venta WHERE tipo_comprobante='Factura' ORDER BY idventa DESC limit 1 ";
 			return ejecutarConsulta($sql);
		  
}

//funcion para seleccionar la serie de la factura
public function numero_serie(){
		 
		    $sql="SELECT serie_comprobante ,num_comprobante FROM venta WHERE tipo_comprobante='Factura' ORDER BY idventa DESC limit 1";

return ejecutarConsulta($sql);
}

//funcion para selecciolnar el numero de boleta
public function numero_venta_boleta(){
		 
		    $sql="SELECT num_comprobante FROM venta WHERE tipo_comprobante='Boleta' ORDER BY idventa DESC limit 1 ";
 			return ejecutarConsulta($sql);
		  
}
//funcion para seleccionar la serie de la boleta
public function numero_serie_boleta(){
		 
		    $sql="SELECT serie_comprobante ,num_comprobante FROM venta WHERE tipo_comprobante='Boleta' ORDER BY idventa DESC limit 1";

return ejecutarConsulta($sql);
}

//funcion para selecciolnar el numero de ticket
public function numero_venta_ticket(){
		 
		    $sql="SELECT num_comprobante FROM venta WHERE tipo_comprobante='Ticket' ORDER BY idventa DESC limit 1 ";
 			return ejecutarConsulta($sql);
		  
}
//funcion para seleccionar la serie de la ticket
public function numero_serie_ticket(){
		 
		    $sql="SELECT serie_comprobante ,num_comprobante FROM venta WHERE tipo_comprobante='Ticket' ORDER BY idventa DESC limit 1";

return ejecutarConsulta($sql);
}

//funcion para selecciolnar el numero de ticket
public function numero_venta_cotizacion($idsucursal){
		 
		    $sql="SELECT num_comprobante FROM cotizacion WHERE tipo_comprobante='Cotización' AND idsucursal = '$idsucursal' ORDER BY idcotizacion DESC limit 1";
 			return ejecutarConsulta($sql);
		  
}
//funcion para seleccionar la serie de la ticket
public function numero_serie_cotizacion($idsucursal){
		 
		    $sql="SELECT serie_comprobante ,num_comprobante FROM cotizacion WHERE tipo_comprobante='Cotización' AND idsucursal = '$idsucursal' ORDER BY idcotizacion DESC limit 1";

return ejecutarConsulta($sql);
}

public function buscarProducto($codigo)
{
	$sql="SELECT * FROM producto WHERE codigo='$codigo'";
	return ejecutarConsultaSimpleFila($sql);
}

// ==================== TABLA TEMPORAL ====================

// Agregar producto temporal
public function agregarTemporal($idusuario, $idproducto, $cantidad, $precio_venta, $descuento, $contenedor, $cantidad_contenedor, $idp){
    $sql = "INSERT INTO cotizacion_tmp (idusuario, idproducto, cantidad, precio_venta, descuento, contenedor, cantidad_contenedor, idp)
            VALUES ('$idusuario', '$idproducto', '$cantidad', '$precio_venta', '$descuento', '$contenedor', '$cantidad_contenedor', '$idp')";
    return ejecutarConsulta_retornarID($sql);
}

// Actualizar un producto temporal
public function actualizarTemporal($idtmp, $cantidad, $precio_venta) {
    $sql = "UPDATE cotizacion_tmp SET cantidad='$cantidad', precio_venta='$precio_venta' WHERE idtmp='$idtmp'";
    return ejecutarConsulta($sql);
}

// Listar productos temporales del usuario
public function listarTmp($idusuario)
{
    $sql = "SELECT 
                ct.idtmp,
                ct.idproducto,
                ct.idp,
                pr.nombre,
                conf.contenedor,
                conf.cantidad_contenedor,
                ct.cantidad,
                ct.precio_venta
            FROM cotizacion_tmp ct
            INNER JOIN producto_configuracion conf ON ct.idproducto = conf.id
            INNER JOIN producto pr ON conf.idproducto = pr.idproducto
            WHERE ct.idusuario = '$idusuario'
            ORDER BY ct.idtmp ASC";
    return ejecutarConsulta($sql);
}



public function eliminarTemporal($idtmp, $idusuario){
    $idtmp = intval($idtmp);
    $idusuario = intval($idusuario);

    // Eliminamos el registro
    $sql = "DELETE FROM cotizacion_tmp WHERE idtmp = '$idtmp' AND idusuario = '$idusuario'";
    $result = ejecutarConsulta($sql);

    // Verificamos si se eliminó realmente
    $verificar = "SELECT COUNT(*) AS total FROM cotizacion_tmp WHERE idtmp = '$idtmp' AND idusuario = '$idusuario'";
    $row = ejecutarConsultaSimpleFila($verificar);

    if (isset($row['total']) && $row['total'] == 0) {
        return true; // eliminado correctamente
    } else {
        return false; // no se eliminó o no existía
    }
}



// Limpiar carrito temporal del usuario (al guardar la cotización)
public function limpiarTemporales($idusuario){
    $sql = "DELETE FROM cotizacion_tmp WHERE idusuario = '$idusuario'";
    return ejecutarConsulta($sql);
}

}
