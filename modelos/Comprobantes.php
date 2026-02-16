<?php
//incluir la conexion de base de datos
require "../configuraciones/Conexion.php";
class Comprobantes
{

	//implementamos nuestro constructor
	public function __construct()
	{
	}

	public function editar($id_comp_pago, $nombre, $serie_comprobante, $num_comprobante)
	{
		$sql = "UPDATE comp_pago SET nombre='$nombre',serie_comprobante='$serie_comprobante',num_comprobante='$num_comprobante' 
	WHERE id_comp_pago='$id_comp_pago'";
		return ejecutarConsulta($sql);
	}
	public function desactivar($id_comp_pago)
	{
		$sql = "UPDATE comp_pago SET condicion='0' WHERE id_comp_pago='$id_comp_pago'";
		return ejecutarConsulta($sql);
	}
	public function activar($id_comp_pago)
	{
		$sql = "UPDATE comp_pago SET condicion='1' WHERE id_comp_pago='$id_comp_pago'";
		return ejecutarConsulta($sql);
	}

	//metodo para mostrar registros
	public function mostrar($id_comp_pago)
	{
		$sql = "SELECT * FROM comp_pago WHERE id_comp_pago='$id_comp_pago'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//listar registros
	public function listar()
	{
		$sql = "SELECT * FROM comp_pago WHERE nombre != 'Cotización'";
		return ejecutarConsulta($sql);
	}
	//listar y mostrar en selct
	public function select()
	{
		$sql = "SELECT * FROM comp_pago WHERE condicion=1 AND nombre != 'Cotización' AND nombre != 'NC' AND nombre != 'NCB'
	ORDER BY id_comp_pago ASC LIMIT 3";
		return ejecutarConsulta($sql);
	}
	//listar y mostrar en selct
	public function selectNC()
	{
		$sql = "SELECT * FROM comp_pago WHERE condicion=1 AND nombre IN ('NC', 'NCB') LIMIT 2";
		return ejecutarConsulta($sql);
	}
	//listar y mostrar en selct
	public function selectDocumentos($idsucursal)
	{
	    $sql = "SELECT idventa, serie_comprobante, num_comprobante 
	            FROM venta 
	            WHERE dov_Estado='ACEPTADO' 
	            AND estado NOT IN ('Nota Credito', 'Nota Credito Parcial' ,'Anulado', 'Activado') 
	            AND tipo_comprobante IN ('Boleta', 'Factura', 'Nota de Venta')
	            AND idsucursal='$idsucursal'";
	    return ejecutarConsulta($sql);
	}

	public function selectMotivos()
	{
		$sql = "SELECT * FROM motivos_nota WHERE condicion = '1'";
		return ejecutarConsulta($sql);
	}

	//listar y mostrar en selct
	public function select2()
	{
		$sql = "SELECT * FROM comp_pago WHERE condicion=1 AND nombre = 'Cotización' LIMIT 1";
		return ejecutarConsulta($sql);
	}
	public function mostrar_serie_ticket($idsucursal)
	{
		$sql = "SELECT serie_comprobante, num_comprobante FROM comp_pago WHERE nombre='Nota de Venta' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}
	public function mostrar_numero_ticket($idsucursal)
	{
		$sql = "SELECT num_comprobante FROM comp_pago WHERE nombre='Nota de Venta' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}

	public function mostrar_serie_ticket2($idsucursal)
	{
		$sql = "SELECT serie_comprobante, num_comprobante FROM comp_pago WHERE nombre='Ticket' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}
	public function mostrar_numero_ticket2($idsucursal)
	{
		$sql = "SELECT num_comprobante FROM comp_pago WHERE nombre='Ticket' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}

	public function mostrar_serie_boleta($idsucursal)
	{
		$sql = "SELECT serie_comprobante, num_comprobante FROM comp_pago WHERE nombre='Boleta' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}
	public function mostrar_numero_boleta($idsucursal)
	{
		$sql = "SELECT num_comprobante FROM comp_pago WHERE nombre='Boleta' AND idsucursal='$idsucursal'";
		return ejecutarConsulta($sql);
	}
	public function mostrar_numero_nc($idsucursal)
	{
		$sql = "SELECT num_comprobante FROM comp_pago WHERE nombre='NC' AND idsucursal='$idsucursal'";
		return ejecutarConsulta($sql);
	}

	public function mostrar_numero_ncb($idsucursal)
	{
		$sql = "SELECT num_comprobante FROM comp_pago WHERE nombre='NCB' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}

	public function mostrar_serie_nc($idsucursal)
	{
		$sql = "SELECT serie_comprobante, num_comprobante FROM comp_pago WHERE nombre='NC' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}

	public function mostrar_serie_ncb($idsucursal)
	{
		$sql = "SELECT serie_comprobante, num_comprobante FROM comp_pago WHERE nombre='NCB' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}

	public function mostrar_serie_factura($idsucursal)
	{
		$sql = "SELECT serie_comprobante, num_comprobante FROM comp_pago WHERE nombre='Factura' AND idsucursal='$idsucursal'";
		return ejecutarConsulta($sql);
	}
	public function mostrar_numero_factura($idsucursal)
	{
		$sql = "SELECT num_comprobante FROM comp_pago WHERE nombre='Factura' AND idsucursal='$idsucursal'";
		return ejecutarConsulta($sql);
	}
	public function mostrar_serie_cotizacion($idsucursal)
	{
		$sql = "SELECT serie_comprobante, num_comprobante FROM comp_pago WHERE nombre='Cotización' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}
	public function mostrar_numero_cotizacion($idsucursal)
	{
		$sql = "SELECT num_comprobante FROM comp_pago WHERE nombre='Cotización' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}
	public function mostrar_numero_ordencompra($idsucursal)
	{
		$sql = "SELECT num_comprobante FROM comp_pago WHERE nombre='Orden Compra' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}
	public function numero_venta_ordencompra($idsucursal)
	{

		$sql = "SELECT num_comprobante FROM compra WHERE tipo_c='Orden Compra' AND idsucursal = '$idsucursal' ORDER BY idcompra DESC limit 1";
		return ejecutarConsulta($sql);
	}
	public function mostrar_serie_ordencompra($idsucursal)
	{
		$sql = "SELECT serie_comprobante, num_comprobante FROM comp_pago WHERE nombre='Orden Compra' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}
	public function numero_serie_ordencompra($idsucursal)
	{

		$sql = "SELECT serie_comprobante ,num_comprobante FROM compra WHERE tipo_c='Orden Compra' AND idsucursal = '$idsucursal' ORDER BY idcompra DESC limit 1";

		return ejecutarConsulta($sql);
	}

	public function siguiente_numero_real($nombre_comprobante, $idsucursal) {
	    // Obtener la serie actual del comprobante
	    $sql_serie = "SELECT serie_comprobante FROM comp_pago 
	                  WHERE nombre='$nombre_comprobante' AND idsucursal='$idsucursal'";
	    $rspta_serie = ejecutarConsulta($sql_serie);
	    $serie = '';
	    if ($reg = $rspta_serie->fetch_object()) {
	        $serie = $reg->serie_comprobante;
	    }

	    // Consultar el último número emitido de esta serie
	    $sql = "SELECT MAX(num_comprobante) as ultimo FROM venta 
	            WHERE tipo_comprobante='$nombre_comprobante' AND serie_comprobante='$serie' 
	            AND idsucursal='$idsucursal'";
	    $rspta = ejecutarConsulta($sql);
	    $num = 0;
	    if ($reg = $rspta->fetch_object()) {
	        $num = (int)$reg->ultimo;
	    }

	    // Siguiente número
	    $num++;
	    if ($num > 9999999) $num = 1; // reinicia si pasa el límite

	    return $num;
	}

	public function select_comprobantes_guia()
	{
		$sql = "SELECT v.idventa, v.serie_comprobante, v.num_comprobante, p.nombre AS cliente
						FROM venta v
						INNER JOIN persona p ON v.idcliente = p.idpersona
						WHERE v.tipo_comprobante IN ('Boleta','Factura') AND v.estado = 'Aceptado'";
		return ejecutarConsulta($sql);
	}

	public function get_numeracion_guia($idsucursal, $serie)
	{
		$sql = "SELECT num_comprobante FROM comp_pago WHERE nombre = 'Guia' AND idsucursal = '$idsucursal' AND serie_comprobante = '$serie'";
		$rspta = ejecutarConsultaSimpleFila($sql);

		$sql_ultima_guia = "SELECT num_comprobante FROM guia_remision WHERE serie_comprobante = '$serie' AND idsucursal = '$idsucursal' ORDER BY idguia DESC LIMIT 1";
		$ultima_guia = ejecutarConsultaSimpleFila($sql_ultima_guia);

		if ($ultima_guia) {
			$nuevo_numero = $ultima_guia['num_comprobante'] + 1;
		} else {
			$nuevo_numero = $rspta ? $rspta['num_comprobante'] : 1;
		}

		return array("serie" => $serie, "numero" => str_pad($nuevo_numero, 7, "0", STR_PAD_LEFT));
	}

	public function getSeries($idsucursal)
	{
		$sql = "SELECT serie_comprobante FROM comp_pago WHERE nombre = 'Guia' AND idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}
}
