<?php
date_default_timezone_set('America/Lima');
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";

class Inventario
{
	//Implementamos nuestro constructor
	public function __construct() {}

	//Implementamos un método para insertar registros
	public function insertar($nombre)
	{
		$sql = "INSERT INTO categoria (nombre,condicion)
		VALUES ('$nombre','1')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para insertar registros
	public function insertarSucursal($nombre, $direccion, $telefono, $nombreSucursal, $serie_comprobante, $num_comprobante, $distrito, $provincia, $departamento, $ubigeo)
	{
		$sql = "INSERT INTO sucursal (nombre,direccion,telefono,distrito,provincia,departamento,ubigeo)
		VALUES ('$nombre','$direccion','$telefono','$distrito','$provincia','$departamento','$ubigeo')";

		$idsucursalnew = ejecutarConsulta_retornarID($sql);

		$num_elementos = 0;

		$sw = true;

		while ($num_elementos < count($nombreSucursal)) {

			if ($serie_comprobante[$num_elementos] < 10) {

				$serie_comprobante = $serie_comprobante;
			} else {

				$serie_comprobante = $serie_comprobante;
			}

			$sql = "INSERT INTO comp_pago (nombre,serie_comprobante,num_comprobante,idsucursal,condicion) VALUES
				('$nombreSucursal[$num_elementos]','$serie_comprobante[$num_elementos]','$num_comprobante[$num_elementos]','$idsucursalnew','1')";

			ejecutarConsulta($sql) or $sw = false;

			$num_elementos = $num_elementos + 1;
		}

		return $sw;
	}

	public function insertarComprobantes($nombre, $serie_comprobante, $num_comprobante, $idsucursal) {}

	//Implementamos un método para editar registros
	public function editar($idinventario_edit, $observacion_apertura, $sucursal_id, $usuario_id)
	{
		$sql = "UPDATE inventarios SET observacion_apertura='$observacion_apertura' WHERE id='$idinventario_edit'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editarSucursal($idsucursal, $nombre, $direccion, $telefono, $distrito, $provincia, $departamento, $ubigeo)
	{
		$sql = "UPDATE sucursal SET nombre='$nombre',direccion='$direccion',telefono='$telefono',distrito='$distrito',provincia='$provincia',departamento='$departamento',ubigeo='$ubigeo' WHERE idsucursal='$idsucursal'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para desactivar categorías
	public function desactivar($idcategoria)
	{
		$sql = "UPDATE categoria SET condicion='0' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($idcategoria)
	{
		$sql = "UPDATE categoria SET condicion='1' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idcategoria)
	{
		$sql = "SELECT * FROM categoria WHERE idcategoria='$idcategoria'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrarSucursal($idsucursal)
	{
		$sql = "SELECT * FROM sucursal WHERE idsucursal='$idsucursal'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql = "SELECT * FROM inventarios ORDER BY id DESC";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listarSucursales()
	{
		$sql = "SELECT * FROM sucursal";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros y mostrar en el select
	public function select()
	{
		$sql = "SELECT * FROM categoria where condicion=1";
		return ejecutarConsulta($sql);
	}


	public function guardar($observacion_apertura, $sucursal, $usuario)
	{
		$fecha_apertura = date("Y-m-d H:i:s");
		$sql = "INSERT INTO inventarios (fecha_apertura, observacion_apertura, usuario_id, sucursal_id) VALUES ('$fecha_apertura', '$observacion_apertura', '$usuario', '$sucursal')";
		return ejecutarConsulta($sql);
	}

	public function buscar_producto($nombre, $codigo, $categoria)
	{
		$sql = "SELECT p.idproducto, p.nombre as producto, p.codigo,p.stock, c.nombre as categoria, um.nombre as unidad_medida FROM producto as p, categoria as c, unidad_medida as um WHERE p.idunidad_medida = um.idunidad_medida AND c.idcategoria = p.idcategoria AND p.condicion=1";

		if (!empty($nombre)) {
			$sql .= " AND p.nombre LIKE '%$nombre%'";
		}

		if (!empty($codigo)) {
			$sql .= " AND p.codigo LIKE '%$codigo%'";
		}

		$rspta =  ejecutarConsulta($sql);
		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = $reg;
		}

		return $data;
	}


	public function guardar_registros($idinventario, $idproducto, $cantidad)
{
    if ($idinventario === '') return array('status'=>FALSE,'message'=>'No se ha seleccionado inventario');
    if (count($idproducto) == 0) return array('status'=>FALSE,'message'=>'Debe agregar al menos un producto');

    $fecha_registro = date("Y-m-d H:i:s");

    for($i=0;$i<count($idproducto);$i++){
        $id_prod = $idproducto[$i];
        $cant = $cantidad[$i];
        if($cant == '' || $cant <= 0) continue;

        $rspta = ejecutarConsulta("SELECT stock FROM producto WHERE idproducto=$id_prod")->fetch_object();
        $stock = $rspta->stock;
        $diferencia = $cant - $stock;

        $exists = ejecutarConsulta("SELECT * FROM inventario_productos WHERE inventario_id=$idinventario AND producto_id=$id_prod");
        if($exists->num_rows == 0){
            ejecutarConsulta("INSERT INTO inventario_productos (inventario_id, producto_id, cantidad, cantidad_real, diferencia, fecha_registro)
                              VALUES ('$idinventario','$id_prod','$cant','$stock','$diferencia','$fecha_registro')");
        } else {
            ejecutarConsulta("UPDATE inventario_productos SET cantidad='$cant', cantidad_real='$stock', diferencia='$diferencia', fecha_registro='$fecha_registro'
                              WHERE inventario_id='$idinventario' AND producto_id='$id_prod'");
        }

        // Inventario temporales
        ejecutarConsulta("INSERT INTO inventario_seleccionados (idinventario, idproducto, cantidad)
                          VALUES ('$idinventario','$id_prod','$cant')
                          ON DUPLICATE KEY UPDATE cantidad='$cant'");
    }

    return array('status'=>TRUE,'message'=>'Datos guardados correctamente');
}




	public function listarInventarios()
	{
	    $sql = "SELECT i.*, 
	                   (SELECT COUNT(*) 
	                    FROM inventario_productos ip 
	                    WHERE ip.inventario_id = i.id AND ip.estado = 0) AS pendientes
	            FROM inventarios i 
	            WHERE i.fecha_cierre IS NOT NULL 
	            ORDER BY i.id DESC";
	    return ejecutarConsulta($sql);
	}

	public function buscarProductosInventario($idsucursal, $idinventario, $idcategoria, $tipo_ajuste)
	{
		if ($idsucursal === '') {
			return [
				"draw" => 0,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			];
		}
		if ($idinventario === '') {
			return [
				"draw" => 0,
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			];
		}

		$where = " WHERE ip.inventario_id = $idinventario";

		if (!empty($idcategoria)) {
			$where .= " AND p.categoria_id = $idcategoria";
		}

		if (!empty($tipo_ajuste) && $tipo_ajuste === '1') {
			$where .= " AND ip.diferencia > 0";
		} elseif (!empty($tipo_ajuste) && $tipo_ajuste == '2') {
			$where .= " AND ip.diferencia < 0";
		};

		$sql = "SELECT
			ip.producto_id,
            p.nombre as producto, 
            um.nombre as unidad_medida, 
            ip.cantidad, 
            ip.cantidad_real, 
            ip.diferencia,
			ip.estado
        FROM inventario_productos as ip 
        JOIN producto as p ON p.idproducto = ip.producto_id 
        JOIN unidad_medida as um ON um.idunidad_medida = p.idunidad_medida 
        $where";

		$rspta = ejecutarConsulta($sql);

		// Total de registros (sin filtros adicionales)
		$total = ejecutarConsultaSimpleFila("SELECT COUNT(*) as total FROM inventario_productos WHERE inventario_id = $idinventario");
		$total = $total['total'];

		$data = array();
		$count = 1;
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				($reg->estado == 1
				    ? '<label class="custom-check disabled">
				            <input type="checkbox" class="checkItem" value="'.$reg->producto_id.'" data-dif="'.$reg->diferencia.'" disabled />
				            <span class="checkmark"></span>
				       </label>'
				    : '<label class="custom-check">
				            <input type="checkbox" class="checkItem" value="'.$reg->producto_id.'" data-dif="'.$reg->diferencia.'" />
				            <span class="checkmark"></span>
				       </label>'),
				$count++,
				$reg->producto,
				$reg->unidad_medida,
				$reg->cantidad,
				$reg->cantidad_real,
				$reg->diferencia,
				$reg->estado ? '<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0,0,256,256"
style="fill:#40C057;">
<g fill="#40c057" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(5.12,5.12)"><path d="M25,2c-12.683,0 -23,10.317 -23,23c0,12.683 10.317,23 23,23c12.683,0 23,-10.317 23,-23c0,-4.56 -1.33972,-8.81067 -3.63672,-12.38867l-1.36914,1.61719c1.895,3.154 3.00586,6.83148 3.00586,10.77148c0,11.579 -9.421,21 -21,21c-11.579,0 -21,-9.421 -21,-21c0,-11.579 9.421,-21 21,-21c5.443,0 10.39391,2.09977 14.12891,5.50977l1.30859,-1.54492c-4.085,-3.705 -9.5025,-5.96484 -15.4375,-5.96484zM43.23633,7.75391l-19.32227,22.80078l-8.13281,-7.58594l-1.36328,1.46289l9.66602,9.01563l20.67969,-24.40039z"></path></g></g>
</svg>' : ''
			];
		}

		// Armamos la respuesta que DataTables espera
		return [
			"draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
			"recordsTotal" => $total,
			"recordsFiltered" => count($data),
			"data" => $data
		];
	}

	public function listarTiposAjuste($tipo)
	{
	    if ($tipo == "todos" || $tipo == "") {
	        $sql = "SELECT * FROM concepto_ajuste";
	    } else {
	        $sql = "SELECT * FROM concepto_ajuste WHERE tipo = '$tipo'";
	    }
	    return ejecutarConsulta($sql);
	}


	public function cerrarInventario($idinventario_cierre, $observacion_cierre)
	{
		$fecha_cierre = date("Y-m-d H:i:s");

		$sql_limpiar = "DELETE FROM inventario_seleccionados WHERE idinventario='$idinventario_cierre'";
		ejecutarConsulta($sql_limpiar);

		$sql = "UPDATE inventarios SET observacion_cierre='$observacion_cierre', fecha_cierre='$fecha_cierre' WHERE id='$idinventario_cierre'";
		return ejecutarConsulta($sql);
	}

	public function ajustarInventario($idinventario, $idsucursal, $idtipo_ajuste, $fecha_ajuste, $idconcepto, $observacion_ajuste, $usuario_id, $productos = [])
{
    global $conexion; // conexión mysqli

    try {
        $conexion->begin_transaction(); // Iniciar transacción

        // Usa la fecha enviada o ahora si viene vacía
        $fecha_ajuste = !empty($fecha_ajuste) ? $fecha_ajuste : date("Y-m-d H:i:s");
        $idinventario = intval($idinventario);

        if (empty($productos)) {
            throw new Exception("Debe seleccionar al menos un producto para ajustar.");
        }

        // ¿Ya existe cabecera de ajuste para este inventario?
        $sql_verificar_ajuste = "SELECT id FROM ajuste_inventario WHERE inventario_id = $idinventario";
        $resultado_ajuste = ejecutarConsulta($sql_verificar_ajuste);
        $ajuste_existente = ($resultado_ajuste && $resultado_ajuste->num_rows > 0) ? $resultado_ajuste->fetch_object()->id : null;

        // ✅ Mapeo correcto:
        // ENTRADA (1) => diferencia > 0
        // SALIDA  (2) => diferencia < 0
        if ($idtipo_ajuste == '1') {
            $condicion = "diferencia > 0";
        } elseif ($idtipo_ajuste == '2') {
            $condicion = "diferencia < 0";
        } else {
            throw new Exception("Tipo de ajuste no válido.");
        }

        // Solo productos seleccionados
        $ids = implode(",", array_map('intval', $productos));
        $sql_detalles = "SELECT * 
                         FROM inventario_productos 
                         WHERE inventario_id = $idinventario 
                           AND $condicion 
                           AND estado = 0 
                           AND producto_id IN ($ids)";
        $resultado_detalles = ejecutarConsulta($sql_detalles);

        if (!$resultado_detalles || $resultado_detalles->num_rows === 0) {
            throw new Exception("No hay diferencias en los productos seleccionados para ajustar.");
        }

        // Crear cabecera si no existe
        if (!$ajuste_existente) {
            $rowTotal = ejecutarConsultaSimpleFila("SELECT COUNT(*) AS total FROM ajuste_inventario");
            $total = isset($rowTotal['total']) ? ($rowTotal['total'] + 1) : 1;

            $sql_insert_ajuste = "INSERT INTO ajuste_inventario 
                (fecha_ajuste, numero, serie, observacion, inventario_id, sucursal_id, usuario_id) 
                VALUES ('$fecha_ajuste', '$total', 'A-00001', '$observacion_ajuste', '$idinventario', '$idsucursal', '$usuario_id')";

            $ajuste_existente = ejecutarConsulta_retornarID($sql_insert_ajuste);
            if (!$ajuste_existente) throw new Exception("Error al crear ajuste de inventario.");
        }

        // Recorrer productos
        while ($producto = $resultado_detalles->fetch_object()) {
            $diferenciaAbs = abs((float)$producto->diferencia);

            // Stock actual
            $sql_stock = "SELECT stock FROM producto WHERE idproducto = $producto->producto_id AND idsucursal = $idsucursal";
            $stock_resultado = ejecutarConsulta($sql_stock);
            if (!$stock_resultado || $stock_resultado->num_rows === 0) continue;

            $stock_actual = (float)$stock_resultado->fetch_object()->stock;

            if ($idtipo_ajuste == '1') {
                // ENTRADA → sumamos
                $nuevo_stock = $stock_actual + $diferenciaAbs;
                $tipo_movimiento = '0';
                $descripcion = 'Ingreso por ajuste de inventario';
            } else {
                // SALIDA → restamos
                $nuevo_stock = $stock_actual - $diferenciaAbs;
                $tipo_movimiento = '1';
                $descripcion = 'Salida por ajuste de inventario';
            }

            // Detalle del ajuste
            $sql_detalle = "INSERT INTO detalle_ajuste_inventario 
                (ajuste_inventario_id, producto_id, cantidad_ajustada, stock_anterior, stock_nuevo, tipo_ajuste, concepto_ajuste_id)
                VALUES ('$ajuste_existente', '$producto->producto_id', '$diferenciaAbs', '$stock_actual', '$nuevo_stock', '$idtipo_ajuste', '$idconcepto')";
            if (!ejecutarConsulta($sql_detalle)) throw new Exception("Error en detalle ajuste.");

            // Kardex
            $sql_kardex = "INSERT INTO kardex 
                (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, stock_actual, tipo_movimiento, motivo, descripcion, fecha_kardex) 
                VALUES ('$idsucursal', '$producto->producto_id', '$diferenciaAbs', '1', '', '$nuevo_stock', '$tipo_movimiento', '$descripcion', '', '$fecha_ajuste')";
            if (!ejecutarConsulta($sql_kardex)) throw new Exception("Error al insertar en Kardex.");

            // Actualizar stock
            $sql_update_stock = "UPDATE producto 
                                 SET stock = '$nuevo_stock' 
                                 WHERE idproducto = '$producto->producto_id' AND idsucursal = '$idsucursal'";
            if (!ejecutarConsulta($sql_update_stock)) throw new Exception("Error al actualizar stock.");

            // Marcar línea de inventario como ajustada
            $sql_update_ind = "UPDATE inventario_productos 
                               SET estado = 1
                               WHERE producto_id = '$producto->producto_id' AND inventario_id = '$idinventario'";
            if (!ejecutarConsulta($sql_update_ind)) throw new Exception("Error al actualizar estado del producto.");
        }

        $conexion->commit();
        return true;
    } catch (Exception $e) {
        $conexion->rollback();
        error_log("AJUSTE INVENTARIO ERROR: " . $e->getMessage());
        // Propaga el detalle al controlador si quieres verlo en el alert:
        throw $e;
        // o si prefieres conservar tu retorno booleano:
        // return false;
    }
}


	public function resumenInventario($idsucursal, $idinventario)
	{
	    // Datos del inventario
	    $sql = "SELECT i.id, i.fecha_apertura, i.fecha_cierre, 
	                   s.nombre AS sucursal,
	                   IF(i.fecha_cierre IS NULL,'Abierto','Cerrado') AS estado
	            FROM inventarios i
	            JOIN sucursal s ON s.idsucursal = i.sucursal_id
	            WHERE i.id = $idinventario AND s.idsucursal = $idsucursal";
	    $info = ejecutarConsultaSimpleFila($sql);
	    if (!$info) {
	        return ["status" => false, "message" => "Inventario no encontrado"];
	    }

	    // Totales
	    $sql_totales = "SELECT 
	                        COUNT(*) AS total_productos,
	                        SUM(CASE WHEN ip.diferencia > 0 THEN 1 ELSE 0 END) AS total_positivos,
	                        SUM(CASE WHEN ip.diferencia < 0 THEN 1 ELSE 0 END) AS total_negativos
	                    FROM inventario_productos ip
	                    WHERE ip.inventario_id = $idinventario";
	    $totales = ejecutarConsultaSimpleFila($sql_totales);

	    return [
	        "status" => true,
	        "inventario" => $idinventario,
	        "sucursal" => $info["sucursal"],
	        "estado" => $info["estado"],
	        "fecha_apertura" => $info["fecha_apertura"],
	        "fecha_cierre" => $info["fecha_cierre"],
	        "total_productos" => $totales["total_productos"],
	        "total_positivos" => $totales["total_positivos"],
	        "total_negativos" => $totales["total_negativos"]
	    ];
	}

	public function agregarTemporal($idinventario, $idproducto, $cantidad)
	{
	    if ($idinventario == '' || $idproducto == '' || $cantidad <= 0) {
	        return array('status' => FALSE, 'message' => 'Datos inválidos');
	    }

	    // Verificar si ya existe
	    $sql = "SELECT * FROM inventario_seleccionados WHERE idinventario='$idinventario' AND idproducto='$idproducto'";
	    $exists = ejecutarConsulta($sql);

	    if ($exists->num_rows > 0) {
		    // Actualizar cantidad reemplazando la anterior, no sumando
		    $sql = "UPDATE inventario_seleccionados 
		            SET cantidad = '$cantidad' 
		            WHERE idinventario='$idinventario' AND idproducto='$idproducto'";
		} else {
		    // Insertar nuevo registro
		    $sql = "INSERT INTO inventario_seleccionados (idinventario, idproducto, cantidad) 
		            VALUES ('$idinventario', '$idproducto', '$cantidad')";
		}


	    ejecutarConsulta($sql);
	    return array('status' => TRUE, 'message' => 'Producto agregado temporalmente');
	}

	public function listarTemporales($idinventario)
	{
	    $sql = "SELECT s.idproducto, p.nombre, p.codigo, um.nombre as unidad, s.cantidad, p.stock 
	            FROM inventario_seleccionados s
	            INNER JOIN producto p ON s.idproducto = p.idproducto
	            INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
	            WHERE s.idinventario = '$idinventario'";
	    $rspta = ejecutarConsulta($sql);
	    $data = array();
	    while ($reg = $rspta->fetch_object()) {
	        $data[] = $reg;
	    }
	    return $data;
	}

	public function eliminar_temporal($idinventario, $idproducto)
{
    if($idinventario==''||$idproducto=='') return array('status'=>FALSE,'message'=>'Faltan datos para eliminar');

    ejecutarConsulta("DELETE FROM inventario_productos WHERE inventario_id='$idinventario' AND producto_id='$idproducto'");
    ejecutarConsulta("DELETE FROM inventario_seleccionados WHERE idinventario='$idinventario' AND idproducto='$idproducto'");

    return array('status'=>TRUE,'message'=>'Producto eliminado correctamente');
}




}
