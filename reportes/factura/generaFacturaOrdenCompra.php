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


		$query = mysqli_query($conexion,"SELECT i.idcompra,i.idproveedor,p.nombre as proveedor,p.direccion,i.tipo_c,p.tipo_documento,p.num_documento,p.email,p.telefono,i.idpersonal,u.nombre as personal,i.tipo_comprobante,i.serie_comprobante,i.num_comprobante,date_format(i.fecha_hora,'%d/%m/%y') as fecha,i.impuesto,i.total_compra,i.formapago,i.lugar_entrega,i.lugar_entrega,i.documento,i.nota,i.motivo_compra FROM compra i INNER JOIN persona p ON i.idproveedor=p.idpersona INNER JOIN personal u ON i.idpersonal=u.idpersonal WHERE i.idcompra='$idventa'");

		$result = mysqli_num_rows($query);
		if($result > 0){

			$factura = mysqli_fetch_assoc($query);

			$query_productos = mysqli_query($conexion,"SELECT a.nombre as producto,um.nombre as unidadmedida, d.nombre_producto,CASE WHEN a.codigo = 'SIN CODIGO' THEN '-' ELSE a.codigo END as codigo,d.cantidad,d.precio_compra,d.precio_venta,(d.cantidad*d.precio_compra) as subtotal FROM detalle_compra d INNER JOIN producto a ON d.idproducto=a.idproducto INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida WHERE d.idcompra='$idventa'");
			$result_detalle = mysqli_num_rows($query_productos);

			ob_start();
		    include(dirname('__FILE__').'/facturaOrdenCompra.php');
		    $html = ob_get_clean();

			// instantiate and use the dompdf class

			$dompdf = new Dompdf(array('enable_remote' => true));

			$dompdf->loadHtml($html);
			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('letter', 'portrait');
			// Render the HTML as PDF
			$dompdf->render();
			// Output the generated PDF to Browser
			$dompdf->stream('Orden de Compra_N°_'.$factura['serie_comprobante'].'-'.$factura['num_comprobante'].'.pdf',array('Attachment'=>0));
			exit;
		}
	}

?>