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



		$query = mysqli_query($conexion,"SELECT v.idventa, s.nombre as almacen, v.idcliente, p.nombre AS cliente, p.direccion, p.tipo_documento, p.num_documento, p.email, p.telefono, v.idpersonal, u.nombre AS personal, v.montoPagado, v.formaPago, date_format(v.fechadeposito, '%d/%m/%y') as fechadeposito, v.banco, v.numoperacion, v.tipo_comprobante, v.serie_comprobante, v.num_comprobante, date_format(v.fecha_hora, '%d/%m/%Y') as fecha,
			date_format(v.fecha_hora, '%r') as hora,date_format(v.fecha_kardex,'%d/%m/%y | %H:%i:%s %p') as fecha_kardex, v.impuesto, v.total_venta, v.ventacredito, v.estado,v.observacion,v.interes FROM venta v 
			INNER JOIN persona p ON v.idcliente=p.idpersona 
			INNER JOIN personal u ON v.idpersonal=u.idpersonal
			INNER JOIN sucursal s ON v.idsucursal = s.idsucursal
			WHERE v.idventa='$idventa'");

		$query2 = mysqli_query($conexion, "SELECT idventa, fecharegistro, sum(deudatotal) AS totalDeuda, date_format(fechavencimiento,'%d/%m/%y') as fechavencimiento, abonototal FROM cuentas_por_cobrar WHERE idventa='".$idventa."'");

		$result = mysqli_num_rows($query);
		if($result > 0){

			$factura = mysqli_fetch_assoc($query);

			$cuentasc = mysqli_fetch_assoc($query2);
			// Obtener todos los pagos de la venta
			$pagos = [];
			$query_pagos = mysqli_query($conexion, "SELECT metodo_pago, monto, nroOperacion, fechaDeposito, banco 
			                                        FROM venta_pago 
			                                        WHERE idventa = '$idventa'");
			while($row_pago = mysqli_fetch_assoc($query_pagos)){
			    $pagos[] = $row_pago;
			}

			if($factura['estado'] == 'Nota Credito'){
				$anulada = '<img class="anulada" src="img/anulado.png" alt="Anulada">';
			}

			$query_productos = mysqli_query($conexion, "
			    SELECT 
			        a.idproducto, 
			        pg.contenedor, 
			        a.nombre AS producto, 
			        d.nombre_producto AS dproducto, 
			        um.nombre AS unidadmedida, 
			        a.fabricante, 
			        CASE WHEN pg.codigo_extra = 'SIN CODIGO' THEN '-' ELSE pg.codigo_extra END AS codigo, 
			        d.cantidad, 
			        d.precio_venta,
			        d.descuento as descuentodv,
			        a.precioB, a.precioC, a.precioD, a.preciocigv,
			        CASE 
			            WHEN d.check_precio = 1 THEN d.precio_venta 
			            ELSE (d.cantidad * d.precio_venta - d.descuento)
			        END AS subtotal,
			        a.stock, 
			        a.proigv,
			        d.check_precio
			    FROM detalle_venta d 
			    LEFT JOIN producto_configuracion pg ON pg.id = d.idproducto
			    INNER JOIN producto a ON pg.idproducto = a.idproducto 
			    INNER JOIN unidad_medida um ON a.idunidad_medida = um.idunidad_medida
			    INNER JOIN venta v ON v.idventa = d.idventa
			    WHERE d.idventa = '$idventa'
			");
			$result_detalle = mysqli_num_rows($query_productos);

			ob_start();
		    include(dirname('__FILE__').'/notacredito.php');
		    $html = ob_get_clean();

			// instantiate and use the dompdf class
			// $dompdf = new Dompdf();

			$dompdf = new Dompdf(array('enable_remote' => true));

			$dompdf->loadHtml($html);
			// (Optional) Setup the paper size and orientation
			$dompdf->setPaper('A4', 'portrait');
			// Render the HTML as PDF
			$dompdf->render();
			// Output the generated PDF to Browser
			if($factura['tipo_comprobante'] == 'NCB' || $factura['tipo_comprobante'] == 'NCF'){
				$tipodoc = 'Nota de Crédito';
			}else{
				$tipodoc = $factura['tipo_comprobante'];
			}
			$dompdf->stream($tipodoc.'_N°_'.$factura['serie_comprobante'].'-'.$factura['num_comprobante'].'.pdf',array('Attachment'=>0));
			exit;
		}
	}

