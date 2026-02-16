<?php

	//print_r($_REQUEST);
	//exit;
	//echo base64_encode('2');
	//exit;
	session_start();
	if(empty($_SESSION['nombre']))
	{
		echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
	}

	include "../../configuraciones/Conexion.php";
	require_once 'pdf/vendor/autoload.php';
	use Dompdf\Dompdf;

	if(empty($_GET["id"]))
	{
		echo "No es posible generar la factura.";
	}else{
		$idventa = $_GET["id"];
		$anulada = '';

		$query_config   = mysqli_query($conexion,"SELECT * FROM datos_negocio");
		$result_config  = mysqli_num_rows($query_config);
		if($result_config > 0){
			$configuracion = mysqli_fetch_assoc($query_config);
		}


		$query = mysqli_query($conexion,"SELECT v.idcotizacion, v.idcliente, s.nombre as almacen, p.nombre AS cliente, v.titulo, v.nota, v.igv, v.saludo, date_format(v.fecha_h,'%d/%m/%y') as fecha_h, p.direccion, p.tipo_documento, p.num_documento, p.email, p.telefono, v.idpersonal, u.nombre AS personal, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, date_format(v.fecha_h, '%d/%m/%Y') as fecha,
			date_format(v.fecha_h, '%r') as hora, v.total_venta, v.nota, v.formapago,v.observacion, v.tiempo_pro FROM cotizacion v INNER JOIN persona p ON v.idcliente=p.idpersona INNER JOIN personal u ON v.idpersonal=u.idpersonal INNER JOIN sucursal s ON v.idsucursal = s.idsucursal WHERE v.idcotizacion='$idventa'");

		$result = mysqli_num_rows($query);
		if($result > 0){

			$factura = mysqli_fetch_assoc($query);

			$query_productos = mysqli_query($conexion,"SELECT a.idproducto, a.nombre AS producto, pg.contenedor as unidadmedida, a.idunidad_medida, 
				CASE WHEN a.codigo = 'SIN CODIGO' THEN '-' ELSE a.codigo END as codigo, d.cantidad_contenedor,d.cantidad, d.precio_venta, d.descuento, (d.cantidad*d.precio_venta-d.descuento) AS subtotal, a.stock, a.imagen, a.proigv 
				FROM detalle_cotizacion d 
				INNER JOIN producto_configuracion pg ON d.idproducto=pg.id 
				INNER JOIN producto a ON pg.idproducto=a.idproducto 
				INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida 
				WHERE d.idcotizacion='$idventa'");
			$result_detalle = mysqli_num_rows($query_productos);

			ob_start();
		    include(dirname('__FILE__').'/facturaCoti.php');
		    $html = ob_get_clean();

			// instantiate and use the dompdf class

			$dompdf = new Dompdf(array('enable_remote' => true));

			$dompdf->loadHtml($html);
			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('letter', 'portrait');
			// Render the HTML as PDF
			$dompdf->render();
			// Output the generated PDF to Browser
			$dompdf->stream('Cotización_N°_'.$factura['serie_comprobante'].'-'.$factura['num_comprobante'].'.pdf',array('Attachment'=>0));
			exit;
		}
	}

?>