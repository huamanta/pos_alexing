<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION["nombre"])) {
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
  if ($_SESSION['almacen'] == 1) {

    //Inlcuímos a la clase PDF_MC_Table
    require('PDF_MC_Table.php');

    $fecha_inicio = $_GET['fechai'];
    $fecha_fin = $_GET['fechaf'];
    $idcliente = $_GET['idcliente'];
    $idsucursal = $_GET['idsucursal'];

    //Instanciamos la clase para generar el documento pdf
    $pdf = new PDF_MC_Table();

    //Agregamos la primera página al documento pdf
    $pdf->AddPage();

    //Seteamos el inicio del margen superior en 25 pixeles 
    $y_axis_initial = 25;

    //Seteamos el tipo de letra y creamos el título de la página. No es un encabezado no se repetirá
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(40, 6, '', 0, 0, 'C');
    $pdf->Cell(100, 6, 'REPORTE POR COBRAR CONSOLIDADO', 1, 0, 'C');
    $pdf->Ln(16);

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 2, 'FECHA INICIO    :     ' . date("d/m/Y", strtotime($fecha_inicio)));

    $pdf->Ln(6);

    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 2, 'FECHA FIN         :     ' . date("d/m/Y", strtotime($fecha_fin)));

    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 2, 'DETALLE DE DEUDA  :     ');

    $pdf->Ln(6);

    //Comenzamos a crear las filas de los registros según la consulta mysql
    require_once "../modelos/Consultas.php";
    $consulta = new Consultas();

    $rspta = $consulta->ventasfechacliente($fecha_inicio, $fecha_fin, $idcliente, $idsucursal);

    require_once "../modelos/CuentasCobrar.php";
    $CC = new CuentasCobrar();

    //Table with rows and columns
    $pdf->SetWidths(array(58, 67, 25, 35));

    //Obtenemos los datos de la cabecera de la venta actual
    require_once "../modelos/Venta.php";
    $venta = new Venta();

    $estadocuenta = 0;

    while ($reg = $rspta->fetch_object()) {

      $contador = 0;

      //Creamos las celdas para los títulos de cada columna y le asignamos un fondo gris y el tipo de letra
      $pdf->SetFillColor(200, 200, 200);
      $pdf->SetFont('Arial', 'B', 10);
      $pdf->Cell(58, 6, 'Cliente', 1, 0, 'C', 1);
      $pdf->Cell(67, 6, utf8_decode('Comprobante'), 1, 0, 'C', 1);
      $pdf->Cell(25, 6, 'Total Venta', 1, 0, 'C', 1);
      $pdf->Cell(35, 6, utf8_decode('TIPO VENTA'), 1, 0, 'C', 1);

      $pdf->Ln(6);

      $nombre = $reg->cliente;
      $codigo = $reg->tipo_comprobante . ' - ' . $reg->serie_comprobante . ' - ' . $reg->num_comprobante;
      $stock = $reg->total_venta;
      if ($reg->ventacredito == 'Si') {
        $ventacredito = 'CRÉDITO';
      } else {
        $ventacredito = 'CONTADO';
      }
      $descripcion = $ventacredito;

      $pdf->SetFont('Arial', '', 10);
      $pdf->Row(array(utf8_decode($nombre), $codigo, $stock, utf8_decode($descripcion)));

      $rsptad = $venta->ventadetalle($reg->idventa);


      $rsptacc = $CC->deudacliente($reg->idventa);
      //Recorremos todos los valores obtenidos
      $regv = $rsptacc->fetch_object();

      $pdf->Ln(4);

      $pdf->SetFontSize(10);
      $pdf->SetFillColor(37, 198, 229);
      // $pdf->SetTextColor(40, 40, 40);
      // $pdf->SetDrawColor(88, 88, 88);
      $pdf->SetTextColor(255);
      $pdf->SetFont('Arial', 'B');
      $pdf->Cell(20, 7, 'CODIGO', 1, 0, 'C', 1);
      $pdf->Cell(105, 7, 'PRODUCTO ', 1, 0, 'C', 1);
      $pdf->Cell(12, 7, 'CANT', 1, 0, 'C', 1);
      $pdf->Cell(13, 7, 'P.U', 1, 0, 'C', 1);
      $pdf->Cell(15, 7, 'DESC', 1, 0, 'C', 1);
      $pdf->Cell(20, 7, 'TOTAL', 1, 0, 'C', 1);
      $pdf->SetLineWidth(0.02);

      $pdf->SetFont('Arial', '');
      // $pdf->SetFillColor(255, 255, 255);
      // $pdf->SetTextColor(40, 40, 40);
      // $pdf->SetDrawColor(88, 88, 88);
      $pdf->Ln();

      if($regv->deudatotal > 0){
        $deudatotal = $regv->deudatotal - $regv->abonototal;
      }else{
        $deudatotal = 0;
      }

      while ($regd = $rsptad->fetch_object()) {

        $contador += 1;

        if($regd->codigo == 'SIN CODIGO'){

          $codigo = '-';

        }else{

          $codigo = $regd->codigo;

        }

        $pdf->SetFillColor(250, 250, 250);
        $pdf->SetTextColor(0);

        $pdf->Cell(20, 7, "$codigo", 1, 0, 'C', 1);
        $pdf->Cell(105, 7, utf8_decode("$regd->producto"), 1, 0, 'L', 1);
        $pdf->Cell(12, 7, "$regd->cantidad", 1, 0, 'C', 1);
        $pdf->Cell(13, 7, "$regd->precio_venta", 1, 0, 'C', 1);
        $pdf->Cell(15, 7, "$regd->descuento", 1, 0, 'C', 1);
        $pdf->Cell(20, 7, "$regd->subtotal", 1, 0, 'C', 1);
        $pdf->Ln();
        $pdf->Cell(185, 7, "DEUDA PENDIENTE    :   $deudatotal", 1, 0, 'C', 1);
      }

      $pdf->Ln(4);

      

      $pdf->Cell(0, 2, );

      $pdf->Ln(4);

      $pdf->Ln(8);

      $estadocuenta += $deudatotal;

    }

    $pdf->SetFillColor(200, 200, 200);

    $pdf->SetFont('Arial', 'B', 10);

    $pdf->Cell(185, 7, "DEUDA TOTAL    :   $estadocuenta", 0, 0, 'C', 1);

    //Mostramos el documento pdf
    $pdf->Output();

?>
<?php
  } else {
    echo 'No tiene permiso para visualizar el reporte';
  }
}
ob_end_flush();
?>