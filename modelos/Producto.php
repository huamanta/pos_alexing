
	<?php
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";
date_default_timezone_set('America/Lima');
class Producto
{
	//Implementamos nuestro constructor
	public function __construct() {}

	//Implementamos un método para insertar registros
	public function insertar($idsucursal, $idcategoria, $idunidad_medida, $idrubro, $idcondicionventa, $registrosan, $fabricante, $codigo, $nombre, $stock, $stockMinimo, $precio, $preciocigv, $precioB, $precioC, $precioD, $precioE, $margenpubl, $margendes, $margenp1, $margenp2, $margendist, $utilprecio, $utilprecioB, $utilprecioC, $utilprecioD, $utilprecioE, $precioCompra, $fecha, $descripcion, $imagen, $modelo, $nserie, $tipoigv, $comisionV, $sucursales)
	{
		if ($codigo == "") {
			$codigo = "SIN CODIGO";
		}
		$sucursales = array_unique($_POST['sucursales']);
		$num_elementos = 0;
		$sw = true;

		while ($num_elementos < count($sucursales)) {

			$sql = "INSERT INTO producto (idsucursal,idcategoria,idunidad_medida,idrubro,idcondicionventa,registrosan,fabricante,codigo,nombre,stock,stock_minimo,precio,preciocigv,precioB,precioC,precioD,precioE,margenpubl,margendes,margenp1,margenp2,margendist,utilprecio,utilprecioB,utilprecioC,utilprecioD,utilprecioE,precio_compra,fecha,descripcion,imagen,modelo,numserie,proigv,comisionV,condicion)
			VALUES ('$sucursales[$num_elementos]','$idcategoria','$idunidad_medida','$idrubro','$idcondicionventa','$registrosan','$fabricante','$codigo','$nombre','$stock','$stockMinimo','$precio','$preciocigv','$precioB','$precioC','$precioD','$precioE','$margenpubl','$margendes','$margenp1','$margenp2','$margendist','$utilprecio','$utilprecioB','$utilprecioC','$utilprecioD','$utilprecioE','$precioCompra','$fecha','$descripcion','$imagen','$modelo','$nserie','$tipoigv','$comisionV','1')";
			$idproducto = ejecutarConsulta_retornarID($sql);
			$idproducto or $sw = false;
			$sql1 = "INSERT INTO producto_configuracion (codigo_extra, contenedor, cantidad_contenedor, precio_venta, precio_promocion, idproducto) 
			VALUES ('$codigo', 'UNIDAD', '1', '$precio', '$precioB', '$idproducto')";
			ejecutarConsulta($sql1);
			$num_elementos = $num_elementos + 1;
		}

		return $sw;
	}

	//Implementamos un método para editar registros
	public function editar($idproducto, $idsucursal, $idcategoria, $idunidad_medida, $idrubro, $idcondicionventa, $registrosan, $fabricante, $codigo, $nombre, $stock, $stockMinimo, $precio, $preciocigv, $precioB, $precioC, $precioD, $precioE, $margenpubl, $margendes, $margenp1, $margenp2, $margendist, $utilprecio, $utilprecioB, $utilprecioC, $utilprecioD, $utilprecioE, $precioCompra, $fecha, $descripcion, $imagen, $modelo, $nserie, $tipoigv, $comisionV)
	{
		$sql = "UPDATE producto SET idsucursal='$idsucursal',idcategoria='$idcategoria',idunidad_medida='$idunidad_medida',idrubro='$idrubro',idcondicionventa='$idcondicionventa',registrosan='$registrosan',fabricante='$fabricante',codigo='$codigo',nombre='$nombre',stock='$stock',stock_minimo='$stockMinimo',precio='$precio',preciocigv='$preciocigv',comisionV='$comisionV',precioB='$precioB',precioC='$precioC',precioD='$precioD',precioE='$precioE',margenpubl='$margenpubl',margendes='$margendes',margenp1='$margenp1',margenp2='$margenp2',margendist='$margendist',utilprecio='$utilprecio',utilprecioB='$utilprecioB',utilprecioC='$utilprecioC',utilprecioD='$utilprecioD',utilprecioE='$utilprecioE',precio_compra='$precioCompra',fecha='$fecha',descripcion='$descripcion', modelo='$modelo', numserie='$nserie',proigv='$tipoigv',imagen='$imagen' WHERE idproducto='$idproducto'";
		ejecutarConsulta($sql);

		$editar = "UPDATE producto_configuracion SET precio_venta = '$precio', codigo_extra='$codigo' WHERE idproducto = '$idproducto' AND cantidad_contenedor = 1";
		return ejecutarConsulta($editar);
	}

	public function mostrarStockProductoE($idproductoE)
	{

		$sql = "SELECT a.stock, um.nombre as unidadmedida FROM producto a INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida WHERE idproducto = '$idproductoE'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarStockProductoD($idproductoD)
	{

		$sql = "SELECT a.stock, um.nombre as unidadmedida FROM producto a INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida WHERE idproducto = '$idproductoD'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function desempaquetar($idproductoE, $idproductoD, $cantidadE, $cantidadD, $productoEmpaquetado, $productoDesempaquetar)
	{

		$cantidadEmpaquetado = $productoEmpaquetado - $cantidadE;

		$cantidadTotalDesempacar = ($cantidadE * $cantidadD) + $productoDesempaquetar;

		$actualizarStockEmpaquetado = "UPDATE producto SET stock = '$cantidadEmpaquetado' where idproducto = '$idproductoE'";
		ejecutarConsulta($actualizarStockEmpaquetado);

		$actualizarStockDesempaquetar = "UPDATE producto SET stock = '$cantidadTotalDesempacar' where idproducto = '$idproductoD'";

		return ejecutarConsulta($actualizarStockDesempaquetar);
	}

	public function trasladar($almacenOrigen, $almacenDestino, $productoTrasladado, $productoTrasladar, $cantidadTrasladar)
	{

		$trasladarM = "UPDATE producto SET stock = stock - '$cantidadTrasladar' where idproducto = '$productoTrasladado' AND  idsucursal = '$almacenOrigen'";
		ejecutarConsulta($trasladarM);

		$trasladar = "UPDATE producto SET stock = stock + '$cantidadTrasladar' where idproducto = '$productoTrasladar' AND  idsucursal = '$almacenDestino'";
		return ejecutarConsulta($trasladar);
	}


	//Implementamos un método para desactivar registros
	public function desactivar($idproducto)
	{
		$sql = "UPDATE producto SET condicion='0' WHERE idproducto='$idproducto'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar registros
	public function activar($idproducto)
	{
		$sql = "UPDATE producto SET condicion='1' WHERE idproducto='$idproducto'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idproducto)
	{
	    $sql = "SELECT p.*,
	                   COALESCE((
	                       SELECT sf.precio_venta
	                       FROM stock_fifo sf
	                       WHERE sf.idproducto = p.idproducto
	                         AND sf.cantidad_restante > 0
	                         AND sf.estado = 1
	                       ORDER BY sf.fecha_ingreso ASC
	                       LIMIT 1
	                   ), p.precio) AS precio,
	                   COALESCE((
	                       SELECT sf.precio_compra
	                       FROM stock_fifo sf
	                       WHERE sf.idproducto = p.idproducto
	                         AND sf.cantidad_restante > 0
	                         AND sf.estado = 1
	                       ORDER BY sf.fecha_ingreso ASC
	                       LIMIT 1
	                   ), p.precio_compra) AS precio_compra
	            FROM producto p
	            WHERE p.idproducto = '$idproducto'";

	    return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function porcentaje($idcategoria)
	{
		$sql = "SELECT * FROM categoria WHERE idcategoria='$idcategoria'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrarProducto($idproducto)
	{
		$sql = "SELECT * from producto p WHERE p.idproducto = '$idproducto'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function listarsucursales($idusuario)
	{
	    $sql = "SELECT s.idsucursal, s.nombre 
	            FROM sucursal s
	            INNER JOIN usuario_sucursal us ON us.idsucursal = s.idsucursal
	            WHERE us.idusuario = '$idusuario'";
	    return ejecutarConsulta($sql);
	}


	/*public function listarKardex($idproducto)
	{
		$sql = "select date_format(c.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha, CONCAT('Compra Nacional') as motivo,concat_ws('-', c.serie_comprobante, c.num_comprobante) as comprobante, dt.cantidad as cantidad, CONCAT('0') as salida, dt.precio_compra as precio,
		dt.precio_compra * dt.cantidad as valor, CONCAT('0') as stock, CONCAT('0') as valorexis
		from compra c
		INNER JOIN detalle_compra dt
		ON c.idcompra = dt.idcompra
		where dt.idproducto = '$idproducto' AND c.tipo_c = 'Compra'
		
		UNION ALL
		
		select date_format(c.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha, CONCAT('Venta Nacional') as motivo,concat_ws('-', c.serie_comprobante, c.num_comprobante) as comprobante, CONCAT('0') as cantidad,  dt.cantidad as salida, p.precio_compra as precio,
		p.precio_compra * dt.cantidad as valor, CONCAT('0') as stock, CONCAT('0') as valorexis
		from venta c
		INNER JOIN detalle_venta dt
		ON c.idventa = dt.idventa
		INNER JOIN producto p
		ON dt.idproducto = p.idproducto
		where dt.idproducto = '$idproducto'
		
		ORDER BY fecha asc";
		return ejecutarConsulta($sql);
	}*/

	public function listarstock22($idsucursal, $ids)
	{
		$sql = "SELECT a.idproducto, a.nombre, a.stock, a.imagen, c.nombre as categoria
	            FROM producto a
	            INNER JOIN categoria c ON a.idcategoria = c.idcategoria
	            WHERE c.nombre != 'SERVICIO' AND a.stock <= 3";

		// Filtrar por sucursal si es necesario
		if ($ids != '0' || $idsucursal != '' && $idsucursal != 'Todos') {
			$sql .= " AND a.idsucursal = '$idsucursal'";
		}

		// Ordenar y limitar los resultados a los 5 más recientes
		$sql .= " ORDER BY a.fechac DESC LIMIT 5";

		return ejecutarConsulta($sql);
	}



	public function listarstock33($idsucursal, $ids)
	{
		$sql = "SELECT a.idproducto, a.nombre, a.stock, a.imagen, c.nombre as categoria
	            FROM producto a
	            INNER JOIN categoria c ON a.idcategoria = c.idcategoria
	            WHERE a.idsucursal = '$ids' AND c.nombre != 'SERVICIO' AND a.stock <= 3
	            ORDER BY a.fechac DESC LIMIT 5";

		return ejecutarConsulta($sql);
	}


	public function listar4()
	{
		$sql = "SELECT a.idproducto,a.idcategoria,a.idunidad_medida,um.nombre as unidad,date_format(a.fecha,'%d/%m/%y') as fecha,c.nombre as categoria,a.codigo,a.nombre,a.stock, a.stock_minimo, a.numserie,a.descripcion,a.imagen,a.condicion 
		FROM producto a 
		INNER JOIN categoria c ON a.idcategoria=c.idcategoria 
		INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida
		INNER JOIN rubro r ON a.idrubro = r.idrubro
		INNER JOIN condicionventa cv ON a.idcondicionventa = cv.idcondicionventa 
		WHERE c.nombre = 'SERVICIO'
		ORDER BY a.idproducto DESC";
		return ejecutarConsulta($sql);
	}

	public function listar($idsucursal)
	{
		$sql = "SELECT a.idproducto,a.idcategoria,a.idunidad_medida,um.nombre as unidad,date_format(a.fecha,'%d/%m/%y') as fecha,c.nombre as categoria,r.nombre as rubro,a.registrosan,a.fabricante,a.codigo,a.nombre,a.stock, a.stock_minimo,a.precioB,a.precioC,a.precioD, a.numserie,a.descripcion,a.imagen,a.condicion 
		FROM producto a 
		INNER JOIN categoria c ON a.idcategoria=c.idcategoria 
		INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida 
		INNER JOIN rubro r ON a.idrubro = r.idrubro
		INNER JOIN condicionventa cv ON a.idcondicionventa = cv.idcondicionventa 
		WHERE c.nombre != 'SERVICIO' AND a.idsucursal='$idsucursal'
		ORDER BY a.idproducto DESC";
		return ejecutarConsulta($sql);
	}

	public function listarcatalogo($idsucursal, $idcategoria = 0)
	{
	    $filtro_categoria = $idcategoria > 0 ? " AND a.idcategoria = '$idcategoria'" : "";

	    $sql = "
	        SELECT 
	            a.idproducto,
	            um.nombre AS unidad,
	            pg.id AS idconfig,
	            pg.contenedor,
	            pg.precio_venta,
	            a.nombre,
	            a.codigo,
	            a.stock,
	            a.imagen,
	            a.fabricante,
	            a.modelo,
	            a.registrosan,
	            a.precio AS precioventa,
	            c.nombre AS categoria,
	            COALESCE(
	                GROUP_CONCAT(
	                    CONCAT(np.idnombre_p, ':', np.descripcion, ':', pgp.precio)
	                    ORDER BY np.idnombre_p ASC
	                    SEPARATOR '|'
	                ), ''
	            ) AS precios_adicionales
	        FROM producto_configuracion pg
	        INNER JOIN producto a ON a.idproducto = pg.idproducto
	        LEFT JOIN producto_configuracion_precios pgp ON pg.id = pgp.producto_configuracion_id
	        LEFT JOIN nombre_precios np ON pgp.idnombre_p = np.idnombre_p
	        INNER JOIN categoria c ON a.idcategoria = c.idcategoria
	        INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida
	        WHERE c.nombre != 'SERVICIO'
	          AND a.idsucursal = '$idsucursal'
	          $filtro_categoria
	        GROUP BY a.idproducto, pg.id, pg.contenedor, pg.precio_venta
	        ORDER BY a.nombre ASC
	    ";

	    $result = ejecutarConsulta($sql);

	    // Forzar UTF-8 a todos los campos de texto
	    $utf8Result = [];
	    while ($row = $result->fetch_assoc()) {
	        foreach ($row as $key => $value) {
	            if (is_string($value)) {
	                $row[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8'); 
	            }
	        }
	        $utf8Result[] = (object)$row; // devolver como objeto para compatibilidad con fetch_object
	    }

	    return $utf8Result;
	}


	public function guardarImagenCatalogo($idsucursal, $nombre_imagen, $orden) {
	  $sql = "INSERT INTO catalogo_imagen (idsucursal, nombre_imagen, orden)
	          VALUES ('$idsucursal', '$nombre_imagen', '$orden')";
	  return ejecutarConsulta($sql);
	}

	public function eliminarImagenCatalogo($nombre_imagen)
	{
	  $sql = "DELETE FROM catalogo_imagen WHERE nombre_imagen = '$nombre_imagen'";
	  return ejecutarConsulta($sql);
	}
	public function obtenerImagenesCatalogo($idsucursal) {
	  return ejecutarConsulta("SELECT * FROM catalogo_imagen WHERE idsucursal = '$idsucursal' ORDER BY orden ASC");
	}

	public function selectcateg()
	{
	  $sql = "SELECT * FROM categoria WHERE nombre != 'SERVICIO'";
	  return ejecutarConsulta($sql);
	}

	public function obtenerPrecios()
	{
	  $sql = "SELECT idnombre_p, descripcion FROM nombre_precios";
	  return ejecutarConsulta($sql);
	}

	// Implementar un método para listar todos los productos
	public function listarTodos()
	{
		$sql = "SELECT a.idproducto, a.nombre 
	            FROM producto a 
	            WHERE a.idcategoria IS NOT NULL"; // Modifica según sea necesario para tu lógica
		return ejecutarConsulta($sql);
	}

	public function listarV($idsucursal)
	{
		$sql = "SELECT a.idproducto,a.idcategoria,a.idunidad_medida,um.nombre as unidad,date_format(a.fecha,'%d/%m/%y') as fecha,c.nombre as categoria,r.nombre as rubro,a.registrosan,a.fabricante,a.codigo,a.nombre,a.stock, a.stock_minimo,a.precioB,a.precioC,a.precioD, a.numserie,a.descripcion,a.imagen,a.condicion 
		FROM producto a 
		INNER JOIN categoria c ON a.idcategoria=c.idcategoria 
		INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida 
		INNER JOIN rubro r ON a.idrubro = r.idrubro
		INNER JOIN condicionventa cv ON a.idcondicionventa = cv.idcondicionventa 
		WHERE c.nombre != 'SERVICIO' AND a.idsucursal='$idsucursal'
		ORDER BY a.idcategoria DESC";
		return ejecutarConsulta($sql);
	}

	// Implementar un método para listar todos los productos
	public function listarTodosV()
	{
		$sql = "SELECT a.idproducto, a.nombre 
	            FROM producto a 
	            WHERE a.idcategoria IS NOT NULL"; // Modifica según sea necesario para tu lógica
		return ejecutarConsulta($sql);
	}


	public function listarServicio()
	{
		$sql = "SELECT a.idproducto,a.idcategoria,a.idunidad_medida,um.nombre as unidad,date_format(a.fecha,'%d/%m/%y') as fecha,c.nombre as categoria,a.codigo,a.nombre,a.stock, a.stock_minimo, a.numserie,a.descripcion,a.imagen,a.condicion 
		FROM producto a 
		INNER JOIN categoria c ON a.idcategoria=c.idcategoria 
		INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida
		INNER JOIN rubro r ON a.idrubro = r.idrubro
			INNER JOIN condicionventa cv ON a.idcondicionventa = cv.idcondicionventa 
		WHERE c.nombre = 'SERVICIO'
		ORDER BY a.idproducto DESC";
		return ejecutarConsulta($sql);
	}

	public function listarS($idsucursal)
	{
		$sql = "SELECT a.idproducto,a.idcategoria,a.idunidad_medida,um.nombre as unidad,date_format(a.fecha,'%d/%m/%y') as fecha,c.nombre as categoria,a.codigo,a.nombre,a.stock, a.stock_minimo, a.numserie,a.descripcion,a.imagen,a.condicion 
		FROM producto a 
		INNER JOIN categoria c ON a.idcategoria=c.idcategoria 
		INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida
		INNER JOIN rubro r ON a.idrubro = r.idrubro
			INNER JOIN condicionventa cv ON a.idcondicionventa = cv.idcondicionventa 
		WHERE a.idsucursal = '$idsucursal'
		ORDER BY a.idproducto DESC";
		return ejecutarConsulta($sql);
	}

	public function listarS2($idsucursal, $ids)
	{

		if ($ids == '0' and $idsucursal == '' || $idsucursal == 'Todos') {

			$sql = "SELECT a.idproducto, a.idsucursal, a.idcategoria,s.nombre as almacen,a.idunidad_medida,date_format(a.fechac,'%d/%m/%y | %H:%i:%s %p') as fechac, a.precio, a.precio_compra, um.nombre as unidad,date_format(a.fecha,'%d/%m/%y') as fecha,c.nombre as categoria,r.nombre as rubro,cv.nombre as condicionventa,a.registrosan,a.fabricante,a.codigo,a.nombre,a.stock, a.stock_minimo, a.numserie,a.descripcion,a.imagen,a.condicion,DATEDIFF(a.fecha, now()) AS dias_transcurridos1 
			FROM producto a 
			INNER JOIN categoria c 
			ON a.idcategoria=c.idcategoria 
			INNER JOIN unidad_medida um 
			ON a.idunidad_medida = um.idunidad_medida
			INNER JOIN rubro r
			ON a.idrubro = r.idrubro
			INNER JOIN condicionventa cv 
			ON a.idcondicionventa = cv.idcondicionventa 
			INNER JOIN sucursal s
			ON s.idsucursal = a.idsucursal
			WHERE c.nombre = 'SERVICIO'
					ORDER BY a.idproducto DESC";
		} else {

			$sql = "SELECT a.idproducto, a.idsucursal, a.idcategoria,s.nombre as almacen,a.idunidad_medida, date_format(a.fechac,'%d/%m/%y | %H:%i:%s %p') as fechac, a.precio, a.precio_compra, um.nombre as unidad, date_format(a.fecha,'%d/%m/%y') as fecha,c.nombre as categoria,r.nombre as rubro,cv.nombre as condicionventa,a.registrosan,a.fabricante,a.codigo,a.nombre,a.stock, a.stock_minimo, a.numserie,a.descripcion,a.imagen,a.condicion,DATEDIFF(a.fecha, now()) AS dias_transcurridos1 
			FROM producto a 
			INNER JOIN categoria c 
			ON a.idcategoria=c.idcategoria 
			INNER JOIN unidad_medida um 
			ON a.idunidad_medida = um.idunidad_medida
			INNER JOIN rubro r
			ON a.idrubro = r.idrubro
			INNER JOIN condicionventa cv 
			ON a.idcondicionventa = cv.idcondicionventa 
			INNER JOIN sucursal s
			ON s.idsucursal = a.idsucursal
			WHERE a.idsucursal = '$idsucursal'
			AND c.nombre = 'SERVICIO'
					ORDER BY a.idproducto DESC";
		}


		return ejecutarConsulta($sql);
	}

	public function listarS3($idsucursal, $ids)
	{

		$sql = "SELECT a.idproducto , a.idsucursal, a.idcategoria,s.nombre as almacen,a.idunidad_medida,  date_format(a.fechac,'%d/%m/%y | %H:%i:%s %p') as fechac, a.precio, a.precio_compra, um.nombre as unidad, date_format(a.fechac,'%d/%m/%y') as fecha,c.nombre as categoria,r.nombre as rubro,cv.nombre as condicionventa,a.registrosan,a.fabricante,a.codigo,a.nombre,a.stock, a.stock_minimo, a.numserie,a.descripcion,a.imagen,a.condicion,DATEDIFF(a.fecha, now()) AS dias_transcurridos1 
		FROM producto a 
		INNER JOIN categoria c 
		ON a.idcategoria=c.idcategoria
		INNER JOIN unidad_medida um 
		ON a.idunidad_medida = um.idunidad_medida
		INNER JOIN rubro r
		ON a.idrubro = r.idrubro
		INNER JOIN condicionventa cv 
		ON a.idcondicionventa = cv.idcondicionventa 
		INNER JOIN sucursal s
		ON s.idsucursal = a.idsucursal
		WHERE a.idsucursal = '$ids' and c.nombre = 'SERVICIO'
		ORDER BY a.idproducto DESC";

		return ejecutarConsulta($sql);
	}



	/*
	public function listar2($idsucursal, $ids)
	{

		if ($ids == '0' and $idsucursal == '' || $idsucursal == 'Todos') {

			$sql = "SELECT a.idproducto, a.idsucursal, a.idcategoria,s.nombre as almacen,a.idunidad_medida,date_format(a.fechac,'%d/%m/%y | %H:%i:%s %p') as fechac, a.precio, a.precio_compra, um.nombre as unidad,date_format(a.fecha,'%d/%m/%y') as fecha,c.nombre as categoria,r.nombre as rubro,cv.nombre as condicionventa,a.registrosan,a.fabricante,a.codigo,a.nombre,a.stock, a.stock_minimo, a.numserie,a.descripcion,a.imagen,a.condicion,DATEDIFF(a.fecha, now()) AS dias_transcurridos1 
			FROM producto a 
			INNER JOIN categoria c 
			ON a.idcategoria=c.idcategoria 
			INNER JOIN unidad_medida um 
			ON a.idunidad_medida = um.idunidad_medida
			INNER JOIN rubro r
			ON a.idrubro = r.idrubro
			INNER JOIN condicionventa cv 
			ON a.idcondicionventa = cv.idcondicionventa 
			INNER JOIN sucursal s
			ON s.idsucursal = a.idsucursal
			WHERE c.nombre != 'SERVICIO'
					ORDER BY a.fechac DESC";
		} else {

			$sql = "SELECT a.idproducto, a.idsucursal, a.idcategoria,s.nombre as almacen,a.idunidad_medida, date_format(a.fechac,'%d/%m/%y | %H:%i:%s %p') as fechac, a.precio, a.precio_compra, um.nombre as unidad, date_format(a.fecha,'%d/%m/%y') as fecha,c.nombre as categoria,r.nombre as rubro,cv.nombre as condicionventa,a.registrosan,a.fabricante,a.codigo,a.nombre,a.stock, a.stock_minimo, a.numserie,a.descripcion,a.imagen,a.condicion,DATEDIFF(a.fecha, now()) AS dias_transcurridos1 
			FROM producto a 
			INNER JOIN categoria c 
			ON a.idcategoria=c.idcategoria 
			INNER JOIN unidad_medida um 
			ON a.idunidad_medida = um.idunidad_medida
			INNER JOIN rubro r
			ON a.idrubro = r.idrubro
			INNER JOIN condicionventa cv 
			ON a.idcondicionventa = cv.idcondicionventa 
			INNER JOIN sucursal s
			ON s.idsucursal = a.idsucursal
			WHERE a.idsucursal = '$idsucursal'
			AND c.nombre != 'SERVICIO'
					ORDER BY a.fechac DESC";
		}


		return ejecutarConsulta($sql);
	}
*/

	public function listarPaginado($idsucursal_filtro, $idsucursal_sesion, $stock_filtro, $start, $length, $search, $es_admin)
{
    // 1. Determinar Sucursal
    $sucursal_final = (!empty($idsucursal_filtro) && $idsucursal_filtro != 'Todos' && $idsucursal_filtro != '0') 
                      ? $idsucursal_filtro 
                      : $idsucursal_sesion;

    // 2. Construir Buscador Seguro (Evita errores de sintaxis)
    $searching = "";
    if (!empty($search)) {
        // Limpiamos caracteres raros
        $search_clean = preg_replace('/[^a-zA-Z0-9\s\-\.]/', '', trim($search)); 
        $palabras = explode(" ", $search_clean);
        $cond_search = array();
        foreach ($palabras as $p) {
            if(!empty($p)) {
                $cond_search[] = "(a.nombre LIKE '%$p%' OR a.codigo LIKE '%$p%' OR c.nombre LIKE '%$p%' OR a.fabricante LIKE '%$p%')";
            }
        }
        if(!empty($cond_search)){
            $searching = " AND " . implode(" AND ", $cond_search);
        }
    }

    // 3. Filtro de Stock
    $filtro_stock = ($stock_filtro > 0) ? " AND a.stock <= $stock_filtro " : "";

    // 4. SQL OPTIMIZADO Y COMPATIBLE
    // El cambio clave está en el LEFT JOIN stock_fifo
    $sql = "SELECT a.idproducto, a.idsucursal, a.idcategoria, s.nombre as almacen, a.idunidad_medida, 
            DATE_FORMAT(a.fechac,'%d/%m/%y | %H:%i:%s %p') as fechac,
            um.nombre as unidad, DATE_FORMAT(a.fecha,'%d/%m/%y') as fecha, c.nombre as categoria, 
            r.nombre as rubro, cv.nombre as condicionventa, a.registrosan, a.fabricante, a.codigo, 
            a.nombre, a.stock, a.stock_minimo, a.numserie, a.descripcion, a.imagen, a.condicion,
            a.precioB, a.precioC, a.precioD, a.precioE,
            
            -- LÓGICA DE PRECIOS --
            -- Si f.precio_venta existe (del FIFO), úsalo. Si no, usa a.precio --
            COALESCE(f.precio_venta, a.precio) as precio,
            COALESCE(f.precio_compra, a.precio_compra) as precio_compra
            
            FROM producto a 
            INNER JOIN categoria c ON a.idcategoria = c.idcategoria 
            INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida 
            INNER JOIN rubro r ON a.idrubro = r.idrubro 
            INNER JOIN condicionventa cv ON a.idcondicionventa = cv.idcondicionventa 
            INNER JOIN sucursal s ON s.idsucursal = a.idsucursal 
            INNER JOIN usuario_sucursal us ON us.idsucursal = a.idsucursal
            INNER JOIN usuario u ON u.idusuario = us.idusuario
            
            -- JOIN FIFO CORREGIDO (Estándar SQL) --
            -- Busca el registro FIFO más antiguo activo para este producto --
            LEFT JOIN stock_fifo f ON f.idfifo = (
                SELECT MIN(sf.idfifo)
                FROM stock_fifo sf
                WHERE sf.idproducto = a.idproducto
                AND sf.estado = 1
                AND sf.cantidad_restante > 0
            )

            WHERE c.nombre != 'SERVICIO' 
            AND u.idpersonal = '" . $_SESSION['idpersonal'] . "'
            AND a.idsucursal = '$sucursal_final'
            $filtro_stock
            $searching
            
            ORDER BY a.fechac DESC
            LIMIT $start, $length";
    
    // Ejecutamos la consulta
    $result = ejecutarConsulta($sql);
    
    // Si falla la consulta (devuelve false), devolvemos un objeto vacío o manejamos el error
    // para evitar el 'Fatal error'
    if (!$result) {
        // Opción: Loguear error o retornar null. 
        // Si tienes acceso al objeto conexión ($conexion), podrías hacer: echo $conexion->error;
        return false; 
    }

    return $result;
}

public function contarTotalPaginado($idsucursal_filtro, $idsucursal_sesion, $stock_filtro, $search)
{
    // 1. Determinar la Sucursal Final
    $sucursal_final = (!empty($idsucursal_filtro) && $idsucursal_filtro != 'Todos' && $idsucursal_filtro != '0') 
                      ? $idsucursal_filtro 
                      : $idsucursal_sesion;

    // 2. Construcción del Buscador (Optimizado y Seguro)
    $searching = "";
    if (!empty($search)) {
        // Limpiamos caracteres peligrosos para evitar errores SQL
        $search_clean = preg_replace('/[^a-zA-Z0-9\s\-\.\ñ\Ñ]/', '', trim($search)); 
        $palabras = explode(" ", $search_clean);
        
        $condiciones_busqueda = array();
        foreach ($palabras as $p) {
            if (!empty($p)) {
                // Buscamos en Nombre, Código, Categoría y Fabricante
                $condiciones_busqueda[] = "(a.nombre LIKE '%$p%' OR a.codigo LIKE '%$p%' OR c.nombre LIKE '%$p%' OR a.fabricante LIKE '%$p%')";
            }
        }
        
        // Unimos todas las palabras con AND
        if (count($condiciones_busqueda) > 0) {
            $searching = " AND " . implode(" AND ", $condiciones_busqueda);
        }
    }

    // 3. Filtro de Stock
    $filtro_stock = ($stock_filtro > 0) ? " AND a.stock <= $stock_filtro " : "";

    // 4. Consulta SQL Optimizada
    // Nota: Se eliminó 'INNER JOIN sucursal s' porque no se usa para filtrar por nombre de sucursal en el conteo,
    // y el ID ya lo tenemos en 'a.idsucursal'. Esto acelera la respuesta.
    $sql = "SELECT COUNT(DISTINCT a.idproducto) as total
            FROM producto a 
            INNER JOIN categoria c ON a.idcategoria = c.idcategoria 
            INNER JOIN usuario_sucursal us ON us.idsucursal = a.idsucursal
            INNER JOIN usuario u ON u.idusuario = us.idusuario
            WHERE c.nombre != 'SERVICIO' 
            AND u.idpersonal = '" . $_SESSION['idpersonal'] . "'
            AND a.idsucursal = '$sucursal_final'
            $filtro_stock
            $searching";
    
    $result = ejecutarConsultaSimpleFila($sql);
    return $result['total'];
}

	public function eliminar($idproducto) {
    // Iniciar transacción
    ejecutarConsulta("START TRANSACTION");

    try {
        // 1️⃣ Obtener IDs de configuraciones del producto
        $sqlConf = "SELECT id FROM producto_configuracion WHERE idproducto = $idproducto";
        $resConf = ejecutarConsulta($sqlConf);

        $idsConf = [];
        while($row = $resConf->fetch_object()){
            $idsConf[] = $row->id;
        }

        // 2️⃣ Borrar precios de configuraciones
        if(count($idsConf) > 0){
            $idsStr = implode(',', $idsConf);
            $sqlPrecios = "DELETE FROM producto_configuracion_precios WHERE producto_configuracion_id IN ($idsStr)";
            ejecutarConsulta($sqlPrecios);
        }

        // 3️⃣ Borrar configuraciones
        $sqlConfDel = "DELETE FROM producto_configuracion WHERE idproducto = $idproducto";
        ejecutarConsulta($sqlConfDel);

        // 4️⃣ Borrar producto
        $sqlProducto = "DELETE FROM producto WHERE idproducto = $idproducto";
        ejecutarConsulta($sqlProducto);

        // ✅ No tocamos detalle_venta para conservar historial
        ejecutarConsulta("COMMIT");
        return true;

    } catch (Exception $e) {
        ejecutarConsulta("ROLLBACK");
        return false;
    }
}



	//Implementar un método para listar los registros
	public function listarProductosCompra()
	{
		$sql = "SELECT a.idproducto,a.idcategoria,a.idunidad_medida,a.fecha,c.nombre as categoria,um.nombre as unidadmedida,a.codigo,a.nombre,a.stock, a.numserie,a.descripcion,a.precio_compra,a.condicion 
		FROM producto a 
		INNER JOIN categoria c ON a.idcategoria=c.idcategoria 
		INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida
		where a.condicion = 1 AND a.stock > 0 
		ORDER BY a.idproducto DESC";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros activos
	public function listarActivos($idsucursal, $buscar = "", $inicio = 0, $limite = 5)
{
    // Sanitizar idsucursal
    $idsucursal = intval($idsucursal);
    $inicio = intval($inicio);
    $limite = intval($limite);
    
    // Preparar búsqueda
    $condicionBusqueda = "";
    if (!empty(trim($buscar))) {
        // Escapar caracteres especiales
        $buscar = addslashes(trim($buscar));
        $condicionBusqueda = " AND (
            a.nombre LIKE '%$buscar%' 
            OR a.codigo LIKE '%$buscar%' 
            OR c.nombre LIKE '%$buscar%'
            OR a.fabricante LIKE '%$buscar%'
        )";
    }
    
    $sql = "SELECT 
            a.idproducto, 
            a.codigo, 
            a.nombre, 
            a.stock, 
            a.precio, 
            a.precio_compra,
            um.nombre as unidadmedida,
            c.nombre as categoria
        FROM producto a 
        INNER JOIN categoria c ON a.idcategoria = c.idcategoria 
        INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida 
        INNER JOIN rubro r ON a.idrubro = r.idrubro
        WHERE a.condicion = '1' 
            AND a.idcategoria != '1' 
            AND a.idsucursal = $idsucursal
            $condicionBusqueda
        ORDER BY a.nombre ASC
        LIMIT $inicio, $limite";
    
    return ejecutarConsulta($sql);
}

public function contarActivos($idsucursal, $buscar = "")
{
    $idsucursal = intval($idsucursal);
    
    $condicionBusqueda = "";
    if (!empty(trim($buscar))) {
        $buscar = addslashes(trim($buscar));
        $condicionBusqueda = " AND (
            a.nombre LIKE '%$buscar%' 
            OR a.codigo LIKE '%$buscar%'
        )";
    }
    
    // Consulta simplificada - solo cuenta, no hace JOIN innecesarios
    $sql = "SELECT COUNT(*) as total
        FROM producto a 
        INNER JOIN categoria c ON a.idcategoria = c.idcategoria 
        WHERE a.condicion = '1' 
            AND c.nombre != 'SERVICIO' 
            AND a.idsucursal = $idsucursal
            $condicionBusqueda";
    
    $result = ejecutarConsulta($sql);
    $row = $result->fetch_object();
    return $row ? $row->total : 0;
}

	public function listarArticulosSearch($idsucursal, $search, $type)
{
    // Escapar búsqueda para prevenir SQL injection
    $search_escaped = mysqli_real_escape_string($GLOBALS['conexion'] ?? null, trim($search));
    $searching = "";
    
    if ($search_escaped) {
        if ($type == 2) {
            // Búsqueda por código
            $searching = "AND REPLACE(pg.codigo_extra, ' ', '') 
                          LIKE CONCAT('%', REPLACE('$search_escaped', ' ', ''), '%') COLLATE utf8mb4_general_ci";
        } elseif ($type == 1) {
            // Búsqueda por nombre con palabras múltiples
            $palabras = explode(" ", $search_escaped);
            $condiciones = [];
            
            foreach ($palabras as $palabra) {
                $palabra = trim($palabra);
                if (strlen($palabra) > 0) {
                    // Normalización para ignorar acentos y ñ
                    $condiciones[] = "REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                LOWER(p.nombre),
                                                'á', 'a'),
                                            'é', 'e'),
                                        'í', 'i'),
                                    'ó', 'o'),
                                'ú', 'u'),
                            'ñ', 'n'),
                        'ü', 'u') LIKE CONCAT('%', REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(
                                            REPLACE(
                                                REPLACE(
                                                    LOWER('$palabra'),
                                                    'á', 'a'),
                                                'é', 'e'),
                                            'í', 'i'),
                                        'ó', 'o'),
                                    'ú', 'u'),
                                'ñ', 'n'),
                            'ü', 'u'), '%')";
                }
            }
            
            if ($condiciones) {
                $searching = "AND (" . implode(" AND ", $condiciones) . ")";
            }
        }
    }
    
    $sql = "SELECT p.*, pg.*, um.idunidad_medida, um.nombre AS unidadmedida, c.nombre AS categoria
            FROM producto p 
            INNER JOIN categoria c ON p.idcategoria = c.idcategoria
            INNER JOIN producto_configuracion pg ON p.idproducto = pg.idproducto 
            INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
            WHERE p.condicion = 1 
            AND p.idsucursal = '$idsucursal' 
            $searching
            AND c.nombre != 'SERVICIO' 
            ORDER BY p.nombre ASC
            LIMIT 20";
            
    return ejecutarConsulta($sql);
}




	/*public function listarArticulosSearch($idsucursal, $search, $type) {
		$searching = "";
		if ($search) {
			if ($type == 2) {
				$searching = "AND pg.codigo_extra LIKE '%$search%'";
			}
			if ($type == 1) {
				$searching = "AND p.nombre LIKE '%$search%'";
			}
		}

		$sql = "SELECT p.*, pg.* 
				FROM producto p 
				INNER JOIN categoria c ON p.idcategoria = c.idcategoria
				INNER JOIN producto_configuracion pg ON p.idproducto = pg.idproducto 
				WHERE p.condicion = 1 
				AND p.idsucursal = '$idsucursal' 
				$searching
				AND c.nombre != 'SERVICIO' LIMIT 20";
		return ejecutarConsulta($sql);
	}*/


	//Implementar un método para listar los registros activos, su último precio y el stock (vamos a unir con el último registro de la tabla detalle_ingreso)
	public function listarActivosVenta($idsucursal)
	{
		// $sql="SELECT a.idproducto,a.idcategoria,c.nombre as categoria,a.codigo, a.nombre,a.stock,(SELECT precio FROM detalle_compra WHERE idproducto=a.idproducto ORDER BY iddetalle_compra DESC LIMIT 0,1) AS precio,a.descripcion,a.imagen,a.condicion FROM producto a INNER JOIN Categoria c ON a.idcategoria=c.idcategoria WHERE a.condicion='1'";
		$sql = "SELECT p.* , pg.*, um.nombre as unidad  FROM producto p 
		INNER JOIN categoria c ON p.idcategoria = c.idcategoria
		LEFT JOIN producto_configuracion pg ON p.idproducto = pg.idproducto 
        INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
		WHERE p.condicion=1 AND p.idsucursal = '$idsucursal' AND c.nombre != 'SERVICIO' GROUP BY p.idproducto";
		return ejecutarConsulta($sql);
	}

	public function listarActivosVenta2($idsucursal)
	{
		// $sql="SELECT a.idproducto,a.idcategoria,c.nombre as categoria,a.codigo, a.nombre,a.stock,(SELECT precio FROM detalle_compra WHERE idproducto=a.idproducto ORDER BY iddetalle_compra DESC LIMIT 0,1) AS precio,a.descripcion,a.imagen,a.condicion FROM producto a INNER JOIN Categoria c ON a.idcategoria=c.idcategoria WHERE a.condicion='1'";
		$sql = "SELECT p.* , pg.* FROM producto p 
		INNER JOIN producto_configuracion pg ON p.idproducto = pg.idproducto 
		INNER JOIN categoria c ON p.idcategoria = c.idcategoria
		WHERE p.condicion=1 AND p.idsucursal = '$idsucursal' AND c.nombre = 'SERVICIO'";
		return ejecutarConsulta($sql);
	}

	public function listarXvencer()
	{
		$sql = "SELECT a.idproducto,a.idcategoria,a.idunidad_medida,um.nombre as unidad,a.fecha,c.nombre 
		as categoria,a.codigo,a.nombre,a.stock, a.stock_minimo, a.numserie,a.descripcion,
		a.imagen,a.condicion, DATEDIFF(a.fecha, now()) AS dias_transcurridos1
		FROM producto a
		INNER JOIN categoria c ON a.idcategoria=c.idcategoria
		INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida
		WHERE DATEDIFF(a.fecha, now()) <= 150
		ORDER BY a.idproducto DESC";

		return ejecutarConsulta($sql);
	}

	public function insertarcat($nombre)
	{
		$sql = "INSERT INTO categoria (nombre,condicion)
		VALUES ('$nombre','1')";
		return ejecutarConsulta($sql);
	}

	public function mostrarUltimaCategoria()
	{

		$sql = "SELECT * FROM categoria order by idcategoria desc limit 1";
		return ejecutarConsultaSimpleFila($sql);
	}


	public function calcularDiasVencimiento1($dias_transcurridos1)
	{
		// code...
		$data = 'Sin rango';
		$dias_vencidos = $dias_transcurridos1 * -1;
		if ($dias_transcurridos1 == 0) {
			$data = '<span class="badge bg-red">S/V</span>';
		} else if ($dias_transcurridos1 == 1) {
			$data = '<span class="badge bg-red">Vence mañana</span>';
		} else if ($dias_transcurridos1 > 1 && $dias_transcurridos1 <= 365) {
			$data = '<span class="badge bg-red">Vence en ' . $dias_transcurridos1 . ' dias</span>';
		} else if ($dias_transcurridos1 > 365 && $dias_transcurridos1 <= 730) {
			$data = '<span class="badge bg-orange">Vence en un año</span>';
		} else if ($dias_transcurridos1 > 730 && $dias_transcurridos1 <= 1095) {
			$data = '<span class="badge bg-green">Vence en dos años</span>';
		} else if ($dias_transcurridos1 > 1095 && $dias_transcurridos1 <= 1461) {
			$data = '<span style="color: green">Vence en tres años</span>';
		} else if ($dias_transcurridos1 == -1) {
			$data = '<span class="badge bg-red">Venció Ayer</span>';
		} else if ($dias_transcurridos1 < -1 && $dias_transcurridos1 >= -365) {
			$data = '<span class="badge bg-red">Venció hace ' . $dias_vencidos . ' dias</span>';
		} else if ($dias_transcurridos1 < -365 && $dias_transcurridos1 >= -720) {
			$data = '<span class="badge bg-red">Venció hace un año</span>';
		} else if ($dias_transcurridos1 < -720 && $dias_transcurridos1 >= -1085) {
			$data = '<span class="badge bg-red">Venció hace dos años</span>';
		}
		return $data;
	}

	public function calcularDiasVencimiento($dias_transcurridos)
	{
		$data = 'Sin rango';
		$dias_restantes = $dias_transcurridos;
		if ($dias_transcurridos == 0) {
			$data = '<span class="badge bg-red">S/V</span>';
		} else if ($dias_transcurridos == 1) {
			$data = '<span class="badge bg-red">Vence mañana</span>';
		} else if ($dias_transcurridos > 1 && $dias_transcurridos <= 365) {
			$data = '<span class="badge bg-red">Vence en ' . $dias_transcurridos . ' días</span>';
		} else if ($dias_transcurridos > 365 && $dias_transcurridos <= 730) {
			$data = '<span class="badge bg-orange">Vence en un año</span>';
		} else if ($dias_transcurridos > 730 && $dias_transcurridos <= 1095) {
			$data = '<span class="badge bg-green">Vence en dos años</span>';
		} else if ($dias_transcurridos > 1095 && $dias_transcurridos <= 1461) {
			$data = '<span style="color: green">Vence en tres años</span>';
		} else if ($dias_transcurridos == -1) {
			$data = '<span class="badge bg-red">Venció Ayer</span>';
		} else if ($dias_transcurridos < -1 && $dias_transcurridos >= -365) {
			$data = '<span class="badge bg-red">Venció hace ' . $dias_transcurridos . ' días</span>';
		} else if ($dias_transcurridos < -365 && $dias_transcurridos >= -730) {
			$data = '<span class="badge bg-red">Venció hace un año</span>';
		} else if ($dias_transcurridos < -730 && $dias_transcurridos >= -1095) {
			$data = '<span class="badge bg-red">Venció hace dos años</span>';
		} else if ($dias_transcurridos < -1095 && $dias_transcurridos >= -43800) {
			$anios = floor($dias_restantes / 365);
			$meses = floor(($dias_restantes % 365) / 30);
			$semanas = floor(($dias_restantes % 30) / 7);
			$dias = $dias_restantes % 7;
			$horas = floor($dias * 24);
			$minutos = floor(($dias * 24 - $horas) * 60);
			$data = '<span class="badge bg-red">Vence en ' . $anios . ' años, ' . $meses . ' meses, ' . $semanas . ' semanas, ' . $dias . ' días, ' . $horas . ' horas, ' . $minutos . ' minutos</span>';
		}
		return $data;
	}

	public function saveCofigurationFromJson($jsonData, $idproducto)
{
    global $conexion;

    $configuraciones = json_decode($jsonData, true);
    if (!$configuraciones) {
        return ['status' => false, 'msg' => 'JSON inválido'];
    }

    $conexion->begin_transaction();

    try {
        // Obtener precio base del lote FIFO activo
        $sqlFifo = "SELECT precio_venta, precio_compra, idfifo
                    FROM stock_fifo 
                    WHERE idproducto = '$idproducto' 
                      AND cantidad_restante > 0 
                      AND estado = 1
                    ORDER BY fecha_ingreso ASC 
                    LIMIT 1";
        $fifo = ejecutarConsultaSimpleFila($sqlFifo);
        $precioBaseUnitario = $fifo ? floatval($fifo['precio_compra']) : 0;
        $idLoteActual = $fifo ? intval($fifo['idfifo']) : 0;

        // Paso 0: Marcar todos como eliminados (soft delete)
        $softDelete = "UPDATE producto_configuracion SET deleted_at = NOW() WHERE idproducto = $idproducto";
        ejecutarConsulta($softDelete);

        // Paso 1: Recorremos configuraciones
        foreach ($configuraciones as $config) {
            $id = isset($config['id']) ? intval($config['id']) : 0;
            $codigo_extra = isset($config['codigo_extra']) ? limpiarCadena($config['codigo_extra']) : '';
            $contenedor = isset($config['contenedor']) ? limpiarCadena($config['contenedor']) : '';
            $cantidad_contenedor = isset($config['cantidad_contenedor']) ? floatval($config['cantidad_contenedor']) : 1;
            
            // ← BLINDAJE: Validar cantidad
            if ($cantidad_contenedor <= 0) {
                $cantidad_contenedor = 1;
            }
            
            // ============================================================
            // NUEVA LÓGICA: Solo UNIDAD se actualiza automáticamente
            // ============================================================
            $esUnidad = ($cantidad_contenedor == 1 || strtoupper(trim($contenedor)) === 'UNIDAD');
            $precio_venta = 0;
            
            if ($esUnidad) {
                // UNIDAD siempre usa precio automático del lote FIFO
                $precio_venta = $precioBaseUnitario;
            } else {
                // Otros contenedores mantienen precio manual
                if (isset($config['precio_venta_manual']) && floatval($config['precio_venta_manual']) > 0) {
                    $precio_venta = floatval($config['precio_venta_manual']);
                } else if (isset($config['precio_venta']) && floatval($config['precio_venta']) > 0) {
                    $precio_venta = floatval($config['precio_venta']);
                } else {
                    // Si no hay precio manual, calcular automático la primera vez
                    $precio_venta = round($precioBaseUnitario * $cantidad_contenedor, 2);
                }
            }
            
            // ← BLINDAJE: Asegurar precio mínimo
            if ($precio_venta <= 0 && $precioBaseUnitario > 0) {
                $precio_venta = round($precioBaseUnitario * $cantidad_contenedor, 2);
            }
            
            if ($id === 0) {
                // INSERT
                $sql = "INSERT INTO producto_configuracion 
                    (codigo_extra, contenedor, cantidad_contenedor, precio_venta, idfifo_origen, idproducto, deleted_at) 
                    VALUES ('$codigo_extra', '$contenedor', '$cantidad_contenedor', '$precio_venta', '$idLoteActual', '$idproducto', NULL)";
                $producto_configuracion_id = ejecutarConsulta_retornarID($sql);
            } else {
                // UPDATE
                $sql = "UPDATE producto_configuracion SET 
                    codigo_extra = '$codigo_extra', 
                    contenedor = '$contenedor', 
                    cantidad_contenedor = $cantidad_contenedor, 
                    precio_venta = '$precio_venta',
                    idfifo_origen = '$idLoteActual',
                    idproducto = '$idproducto', 
                    deleted_at = NULL 
                WHERE id = $id";
                ejecutarConsulta($sql);
                $producto_configuracion_id = $id;
            }

            // Actualizar producto base solo si es UNIDAD
            if ($esUnidad) {
                $updateProducto = "UPDATE producto 
                                   SET codigo = '$codigo_extra' 
                                   WHERE idproducto = $idproducto";
                ejecutarConsulta($updateProducto);
            }

            // Manejo de precios adicionales
            if (isset($config['precios']) && is_array($config['precios'])) {
                $marcarInactivos = "UPDATE producto_configuracion_precios 
                        SET estado = 0 
                        WHERE producto_configuracion_id = $producto_configuracion_id";
                ejecutarConsulta($marcarInactivos);

                foreach ($config['precios'] as $precio) {
                    $idnombre_p = limpiarCadena($precio['idnombre_p']);
                    $precio_valor = floatval($precio['precio']);

                    $checkSql = "SELECT id FROM producto_configuracion_precios 
                     WHERE producto_configuracion_id = $producto_configuracion_id 
                     AND idnombre_p = '$idnombre_p' LIMIT 1";
                    $existe = ejecutarConsultaSimpleFila($checkSql);

                    $margen = isset($precio['margen_utilidad']) ? floatval($precio['margen_utilidad']) : 0;

                    if ($existe && isset($existe['id'])) {
                        $updatePrecio = "UPDATE producto_configuracion_precios 
                            SET precio = '$precio_valor', 
                                margen_utilidad = '$margen', 
                                estado = 1 
                            WHERE id = {$existe['id']}";
                        ejecutarConsulta($updatePrecio);
                    } else {
                        $insertPrecio = "INSERT INTO producto_configuracion_precios 
                            (producto_configuracion_id, idnombre_p, precio, margen_utilidad, estado) 
                            VALUES ($producto_configuracion_id, '$idnombre_p', '$precio_valor', '$margen', 1)";
                        ejecutarConsulta($insertPrecio);
                    }
                }
            }
        }

        $conexion->commit();
        return ['status' => true, 'msg' => 'Configuraciones guardadas correctamente'];
    } catch (Exception $e) {
        $conexion->rollback();
        return ['status' => false, 'msg' => 'Error al guardar configuraciones: ' . $e->getMessage()];
    }
}

	public function listCofiguration($idproducto)
	{
	    // 1. Obtenemos los datos del lote FIFO activo (Costo y Venta)
	    $sqlFifo = "SELECT precio_venta, precio_compra, idfifo
	                FROM stock_fifo 
	                WHERE idproducto = '$idproducto' 
	                  AND cantidad_restante > 0 
	                  AND estado = 1
	                ORDER BY fecha_ingreso ASC 
	                LIMIT 1";
	    $fifo = ejecutarConsultaSimpleFila($sqlFifo);

	    // Definimos las bases unitarias
	    $costoCompraUnitario = $fifo ? floatval($fifo['precio_compra']) : 0;
	    $precioVentaUnitario = $fifo ? floatval($fifo['precio_venta']) : 0;
	    $idLoteActual = $fifo ? intval($fifo['idfifo']) : 0;

	    // 2. Obtenemos las configuraciones del producto
	    $sql = "SELECT pc.* FROM producto_configuracion pc
	            WHERE pc.idproducto = '$idproducto' 
	              AND pc.deleted_at IS NULL
	            ORDER BY pc.cantidad_contenedor ASC";

	    $list = ejecutarConsulta($sql);
	    $data = array();

	    while ($reg = $list->fetch_object()) {
	        $cantidadContenedor = floatval($reg->cantidad_contenedor);
	        if ($cantidadContenedor <= 0) { $cantidadContenedor = 1; }
	        
	        // Pasamos ambos valores al frontend para que el JS sepa distinguir
	        $reg->costo_compra_unitario = $costoCompraUnitario; 
	        $reg->precio_venta_unitario = $precioVentaUnitario;
	        // Para la tabla del modal, el "precio_base" sigue siendo el de venta
	        $reg->precio_base_unitario = $precioVentaUnitario;
	        
	        $idLoteGuardado = isset($reg->idfifo_origen) ? intval($reg->idfifo_origen) : 0;
	        $precioGuardado = floatval($reg->precio_venta);
	        $precioAutomatico = round($precioVentaUnitario * $cantidadContenedor, 2);
	        
	        $esUnidad = ($cantidadContenedor == 1 || strtoupper(trim($reg->contenedor)) === 'UNIDAD');
	        
	        if ($esUnidad) {
	            if ($idLoteGuardado != $idLoteActual && $idLoteActual > 0) {
	                $reg->precio_venta = $precioVentaUnitario;
	                $reg->cambio_lote = true;
	                ejecutarConsulta("UPDATE producto_configuracion SET precio_venta = '$precioVentaUnitario', idfifo_origen = '$idLoteActual' WHERE id = {$reg->id}");
	            } else {
	                $reg->precio_venta = $precioVentaUnitario;
	            }
	        } else {
	            $reg->precio_venta = $precioGuardado > 0 ? $precioGuardado : $precioAutomatico;
	            if ($precioGuardado > 0) { $reg->precio_venta_manual = $precioGuardado; }
	            if ($idLoteGuardado != $idLoteActual && $idLoteActual > 0) {
	                ejecutarConsulta("UPDATE producto_configuracion SET idfifo_origen = '$idLoteActual' WHERE id = {$reg->id}");
	            }
	        }

	        // 3. Obtenemos los precios adicionales de esta configuración
	        $idconfig = $reg->id;
	        $sqlPrecios = "SELECT id, idnombre_p, precio, margen_utilidad
	                       FROM producto_configuracion_precios
	                       WHERE producto_configuracion_id = '$idconfig' AND estado = 1";
	        $listPrecios = ejecutarConsulta($sqlPrecios);
	        $precios = array();
	        while ($precioReg = $listPrecios->fetch_object()) {
	            $precios[] = $precioReg;
	        }
	        $reg->precios = $precios;
	        $data[] = $reg;
	    }
	    return $data;
	}

	public function eliminarCofiguration($idconfig)
	{
		$sql = "DELETE FROM producto_configuracion WHERE id = '$idconfig'";
		return ejecutarConsulta($sql);
	}

	public function mostrarFechaVencProducto($idproducto)
	{
	    $sql = "SELECT
	                sf.idfifo,
	                sf.fecha_ingreso,
	                dc.fvencimiento,
	                DATEDIFF(dc.fvencimiento, NOW()) AS dias_transcurridos,
	                sf.cantidad_ingreso AS cantidad,
	                sf.cantidad_restante AS stock_lote,
	                dc.nlote,
	                sf.precio_compra,
	                sf.precio_venta,
	                p.nombre AS nombre_producto
	            FROM stock_fifo sf
	            INNER JOIN producto p 
	                ON p.idproducto = sf.idproducto
	            LEFT JOIN detalle_compra dc 
	                ON dc.iddetalle_compra = sf.referencia_id
	               AND sf.origen = 'COMPRA'
	            WHERE sf.idproducto = '$idproducto'
	              AND sf.estado = 1
	            ORDER BY sf.fecha_ingreso ASC";

	    return ejecutarConsulta($sql);
	}


	public function movimientoEntradaSalida(
	    $idproducto,
	    $idsucursal,
	    $tipo_movimiento,   // 0 = Entrada, 1 = Salida
	    $cantidad,
	    $motivo,            // Descripción o detalle del movimiento
	    $cantidad_contenedor = 1,
	    $precio_compra = null,
    	$precio_venta = null,
    	$idfifo = 0
	) {
	    date_default_timezone_set('America/Lima'); // Zona horaria de Lima

	    // Normalizar entradas
	    $idproducto = intval($idproducto);
	    $idsucursal = intval($idsucursal);
	    $cantidad = floatval(str_replace(',', '.', $cantidad));
	    $idfifo = intval($idfifo);  // ← Mover esta línea aquí arriba
	    $cantidad_contenedor = floatval(str_replace(',', '.', $cantidad_contenedor));
	    if ($cantidad_contenedor <= 0) $cantidad_contenedor = 1;
	    // Calcular total en unidades
	    $total_unidades = round($cantidad * $cantidad_contenedor, 2);
	    if ($total_unidades <= 0) {
	        return ['status' => 0, 'message' => 'La cantidad total no puede ser cero o negativa'];
	    }

	    $intentos = 0;
	    $max_intentos = 3;

	    while ($intentos < $max_intentos) {
	        try {
	            $intentos++;
	            ejecutarConsulta("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");
	            ejecutarConsulta("BEGIN");

	            //  Leer stock actual y precio bloqueando la fila
	            $sql = "SELECT stock, precio 
	                    FROM producto 
	                    WHERE idproducto = '$idproducto' 
	                      AND idsucursal = '$idsucursal' 
	                    FOR UPDATE";
	            $res = ejecutarConsulta($sql);
	            if (!$res) throw new Exception("Error SQL al consultar producto");

	            $producto = $res->fetch_object();
	            if (!$producto) throw new Exception("Producto no encontrado (id:$idproducto)");

	            $stock_actual = floatval($producto->stock);
	            $precio_venta  = ($precio_venta !== null) ? floatval($precio_venta) : floatval($producto->precio);
				$precio_compra = ($precio_compra !== null) ? floatval($precio_compra) : $precio_venta;
				// Validar precios SOLO si es ENTRADA y NUEVO LOTE
				if ($tipo_movimiento == 0 && $idfifo == 0) {
				    if ($precio_venta <= 0 || $precio_compra <= 0) {
				        throw new Exception("Debe ingresar precio de compra y venta para crear un nuevo lote");
				    }
				}
	            $fecha_kardex = date('Y-m-d H:i:s');

	            if ($tipo_movimiento == 1) {

				    $sqlCheckFifo = "
				        SELECT SUM(cantidad_restante) AS total_fifo
				        FROM stock_fifo
				        WHERE idproducto = '$idproducto'
				          AND idsucursal = '$idsucursal'
				          AND cantidad_restante > 0
				          AND estado = 1
				        FOR UPDATE
				    ";

				    $rsFifo = ejecutarConsulta($sqlCheckFifo);
				    if (!$rsFifo) {
				        throw new Exception("Error al validar stock FIFO");
				    }

				    $fifo = $rsFifo->fetch_object();
				    $total_fifo = floatval($fifo->total_fifo ?? 0);

				    if ($total_fifo < $total_unidades) {
				        throw new Exception(
				            "Stock FIFO insuficiente (FIFO: $total_fifo, salida: $total_unidades)"
				        );
				    }
				}
	            //  Calcular nuevo stock
	            if ($tipo_movimiento == 0) {
	                //  Entrada
	                $nuevo_stock = round($stock_actual + $total_unidades, 2);
	                $type = "Entrada de almacén";
	            } else {
	                //  Salida
	                if ($stock_actual < $total_unidades) {
	                    ejecutarConsulta("ROLLBACK");
	                    return [
	                        'status' => 0,
	                        'message' => "Stock insuficiente (actual: $stock_actual, salida: $total_unidades)"
	                    ];
	                }
	                $nuevo_stock = round($stock_actual - $total_unidades, 2);
	                $type = "Salida de almacén";
	            }
	            if ($tipo_movimiento == 1) {

				    $cantidad_salida = $total_unidades;

				    $sqlFifoLotes = "
				        SELECT idfifo, cantidad_restante, precio_venta, precio_compra
				        FROM stock_fifo
				        WHERE idproducto = '$idproducto'
				          AND idsucursal = '$idsucursal'
				          AND cantidad_restante > 0
				          AND estado = 1
				        ORDER BY fecha_ingreso ASC
				        FOR UPDATE
				    ";

				    $rsLotes = ejecutarConsulta($sqlFifoLotes);

				    while ($cantidad_salida > 0 && $lote = $rsLotes->fetch_object()) {

				        $descontar = min($cantidad_salida, $lote->cantidad_restante);

				        ejecutarConsulta("
				            UPDATE stock_fifo 
				            SET cantidad_restante = cantidad_restante - $descontar
				            WHERE idfifo = {$lote->idfifo}
				        ");

				        $cantidad_salida -= $descontar;
				    }
				    ejecutarConsulta("
					    UPDATE stock_fifo 
					    SET estado = 0 
					    WHERE cantidad_restante <= 0
					      AND idproducto = '$idproducto'
					      AND idsucursal = '$idsucursal'
					");
				}
	            // Actualizar stock
	            $sql_update = "UPDATE producto 
	                           SET stock = '$nuevo_stock' 
	                           WHERE idproducto = '$idproducto' 
	                             AND idsucursal = '$idsucursal'";
	            $okStock = ejecutarConsulta($sql_update);
	            if (!$okStock) throw new Exception("Error al actualizar stock del producto");

				// -------- ENTRADA --------
				// -------- ENTRADA --------
				if ($tipo_movimiento == 0) {

				    if ($idfifo > 0) {

				        // ➜ ENTRAR A LOTE EXISTENTE (mantiene precios del lote)
				        
				        // Primero obtener los precios del lote existente
				        $sqlGetPrecio = "
				            SELECT precio_venta, precio_compra
				            FROM stock_fifo
				            WHERE idfifo = '$idfifo'
				              AND idproducto = '$idproducto'
				              AND idsucursal = '$idsucursal'
				            FOR UPDATE
				        ";
				        
				        $resLote = ejecutarConsulta($sqlGetPrecio);
				        if (!$resLote) {
				            throw new Exception('Error al consultar lote existente');
				        }
				        
				        $loteData = $resLote->fetch_object();
				        if (!$loteData) {
				            throw new Exception('Lote no encontrado');
				        }
				        
				        // Usar los precios del lote existente para el kardex
				        $precio_venta = floatval($loteData->precio_venta);
				        $precio_compra = floatval($loteData->precio_compra);
				        
				        // Ahora actualizar el lote
				        $sqlLote = "
				            UPDATE stock_fifo 
				            SET 
				                cantidad_ingreso = cantidad_ingreso + $total_unidades,
				                cantidad_restante = cantidad_restante + $total_unidades
				            WHERE idfifo = '$idfifo'
				              AND idproducto = '$idproducto'
				              AND idsucursal = '$idsucursal'
				        ";
				        
				        if (!ejecutarConsulta($sqlLote)) {
				            throw new Exception('Error al actualizar lote existente');
				        }

				    } else {

				        // ➜ CREAR NUEVO LOTE (solo si no eligió lote)
				        $sqlFifo = "INSERT INTO stock_fifo (
				            idsucursal, idproducto, origen, referencia_id,
				            cantidad_ingreso, cantidad_restante, precio_venta, precio_compra, fecha_ingreso
				        ) VALUES (
				            '$idsucursal','$idproducto','ALMACEN',NULL,
				            '$total_unidades','$total_unidades',
				            '$precio_venta','$precio_compra','$fecha_kardex'
				        )";

				        if (!ejecutarConsulta($sqlFifo)) {
				            throw new Exception('Error al crear lote FIFO');
				        }
				    }
				}
	            //Registrar movimiento en kardex
	            $sql_kardex = "INSERT INTO kardex 
	                (idsucursal, idproducto, cantidad, cantidad_contenedor, precio_unitario, 
	                 stock_actual, tipo_movimiento, motivo, descripcion, fecha_kardex)
	                VALUES (
	                    '$idsucursal',
	                    '$idproducto',
	                    '$total_unidades',
	                    '$cantidad_contenedor',
	                    '$precio_compra',
	                    '$nuevo_stock',
	                    '$tipo_movimiento',
	                    '$type',
	                    '$motivo',
	                    '$fecha_kardex'
	                )";
	            $okKardex = ejecutarConsulta($sql_kardex);
	            if (!$okKardex) throw new Exception("Error al registrar movimiento en kardex");
	            $sqlCheck = "SELECT SUM(cantidad_restante) AS s 
				             FROM stock_fifo 
				             WHERE idproducto='$idproducto' AND idsucursal='$idsucursal' AND estado=1";
				$r = ejecutarConsulta($sqlCheck)->fetch_object();

				if (round($r->s,2) != round($nuevo_stock,2)) {
				    throw new Exception("Descuadre FIFO vs stock global");
				}
	            //  Confirmar transacción
	            ejecutarConsulta("COMMIT");

	            return [
	                'status' => 1,
	                'message' => "Movimiento registrado correctamente ($type)",
	                'stock_anterior' => $stock_actual,
	                'stock_nuevo' => $nuevo_stock,
	                'fecha' => $fecha_kardex
	            ];

	        } catch (Exception $e) {
	            //  Deshacer si algo falla
	            ejecutarConsulta("ROLLBACK");

	            // Si el error es de bloqueo o concurrencia, reintenta
	            if (stripos($e->getMessage(), 'deadlock') !== false ||
	                stripos($e->getMessage(), 'lock wait timeout') !== false) {
	                if ($intentos < $max_intentos) {
	                    usleep(200000); // Esperar 0.2s antes de reintentar
	                    continue;
	                }
	            }

	            return ['status' => 0, 'message' => 'Error: ' . $e->getMessage()];
	        }
	    }

	    // Si falla después de varios intentos
	    return [
	        'status' => 0,
	        'message' => 'No se pudo completar el movimiento tras varios intentos por concurrencia.'
	    ];
	}

	public function listarService()
	{
		$sql = "SELECT p.idproducto, p.nombre, p.precio 
        FROM producto p
        INNER JOIN categoria c ON p.idcategoria = c.idcategoria 
        WHERE p.condicion = 1 AND c.nombre = 'SERVICIO'";
		return ejecutarConsulta($sql);
	}


	function selectNombrePrecios()
	{
		$sql = "SELECT  * FROM nombre_precios  WHERE estado = 1";
		return ejecutarConsulta($sql);
	}

	public function consultarStockOtrasSucursales($idproducto, $idsucursalActual)
{
    // Primero obtenemos el código del producto base
    $codigo = ejecutarConsultaSimpleFila("SELECT codigo FROM producto WHERE idproducto = '$idproducto'")['codigo'];

    $sql = "
        SELECT 
            s.nombre AS sucursal,
            IFNULL(p.stock, 0) AS stock
        FROM producto p
        INNER JOIN sucursal s ON p.idsucursal = s.idsucursal
        WHERE p.codigo = '$codigo'
          AND p.idsucursal != '$idsucursalActual'
          AND p.stock > 0
        ORDER BY s.nombre ASC
    ";
    return ejecutarConsulta($sql);
}

public function buscarStockPorSucursales($termino, $idsucursalActual, $idsucursalFiltro = '')
{
    $termino = "%$termino%";
    $condSucursal = $idsucursalFiltro ? "AND s.idsucursal = '$idsucursalFiltro'" : "AND s.idsucursal != '$idsucursalActual'";
    $sql = "
        SELECT 
            p.idproducto,
            p.nombre,
            p.codigo,
            s.idsucursal,
            s.nombre AS sucursal,
            p.stock
        FROM producto p
        INNER JOIN sucursal s ON p.idsucursal = s.idsucursal
        WHERE (p.nombre LIKE '$termino' OR p.codigo LIKE '$termino')
          $condSucursal
          AND p.stock > 0
        ORDER BY p.nombre ASC, s.nombre ASC
        LIMIT 100
    ";
    return ejecutarConsulta($sql);
}

public function generarCodigo()
{
    $anio = date('Y');

    // Buscar el último código AUTOMÁTICO del año actual (mayor numéricamente)
    $sql = "
        SELECT MAX(CAST(SUBSTRING(codigo, 5, 3) AS UNSIGNED)) AS ultimo
        FROM producto
        WHERE codigo REGEXP '^[0-9]{4}[0-9]{3}$'
        AND codigo LIKE '$anio%'
    ";
    $rspta = ejecutarConsultaSimpleFila($sql);

    if (isset($rspta['ultimo']) && $rspta['ultimo'] !== null) {
        $nuevoNumero = intval($rspta['ultimo']) + 1;
    } else {
        $nuevoNumero = 1;
    }

    // Formatear el número a 3 dígitos (ej: 001, 002, ...)
    $codigo = $anio . str_pad($nuevoNumero, 3, '0', STR_PAD_LEFT);

    return $codigo;
}

public function selectProductosVenta()
{
    $sql = "SELECT idproducto, nombre FROM producto WHERE condicion='1'";
    return ejecutarConsulta($sql);
}

public function listarActivosVentaFIFO($idsucursal, $search = null, $type = null)
{
    $searching = "";
    if ($search) {
        if ($type == 2) {
            $searching = "AND (p.codigo LIKE '$search%' OR pg.codigo_extra LIKE '$search%')";
        } else {
            $palabras = explode(" ", trim($search));
            $condiciones = [];
            foreach ($palabras as $palabra) {
                if (!empty($palabra)) {
                    $condiciones[] = "p.nombre LIKE '%$palabra%'";
                }
            }
            if (!empty($condiciones)) {
                $searching = "AND (" . implode(" AND ", $condiciones) . ")";
            }
        }
    }

    $sql = "SELECT
                p.idproducto AS id_producto_real,
                p.nombre,
                p.imagen,
                p.codigo,
                p.proigv,
                c.idcategoria,
                c.nombre AS categoria,
                um.nombre AS unidadmedida,
                pg.id AS id_producto_config,
                pg.cantidad_contenedor,
                pg.contenedor,
                f.idfifo AS id_fifo,
                f.precio_venta AS precio_base_fifo,
                f.cantidad_restante AS stock_lote_fifo,
                CASE 
                    WHEN UPPER(TRIM(pg.contenedor)) = 'UNIDAD' THEN COALESCE(f.precio_venta, 0)
                    ELSE COALESCE(NULLIF(pg.precio_venta, 0), 0)
                END AS precio_venta_fifo
            FROM producto p
            INNER JOIN producto_configuracion pg ON p.idproducto = pg.idproducto AND pg.deleted_at IS NULL
            INNER JOIN categoria c ON p.idcategoria = c.idcategoria
            INNER JOIN unidad_medida um ON p.idunidad_medida = um.idunidad_medida
            LEFT JOIN (
                SELECT sf.idproducto, sf.idfifo, sf.precio_venta, sf.cantidad_restante, sf.fecha_ingreso
                FROM stock_fifo sf
                INNER JOIN (
                    SELECT x.idproducto, MIN(x.idfifo) AS min_idfifo
                    FROM stock_fifo x
                    INNER JOIN (
                        SELECT idproducto, MIN(fecha_ingreso) AS min_fecha
                        FROM stock_fifo
                        WHERE idsucursal = '$idsucursal'
                          AND cantidad_restante > 0
                          AND estado = 1
                        GROUP BY idproducto
                    ) m
                      ON m.idproducto = x.idproducto
                     AND m.min_fecha  = x.fecha_ingreso
                    WHERE x.idsucursal = '$idsucursal'
                      AND x.cantidad_restante > 0
                      AND x.estado = 1
                    GROUP BY x.idproducto
                ) pick
                  ON pick.idproducto = sf.idproducto
                 AND pick.min_idfifo = sf.idfifo
            ) f ON p.idproducto = f.idproducto
            WHERE p.condicion = 1
              AND p.idsucursal = '$idsucursal'
              AND c.nombre != 'SERVICIO'
              $searching
            ORDER BY p.nombre ASC
            LIMIT 20";

    return ejecutarConsulta($sql);
}

}

