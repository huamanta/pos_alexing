<?php

session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>SYSPIDER TECHNOLOGY</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="./files/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="./files/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="./files/dist/css/adminlte.min.css">
  <link href="./files/css/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./files/dist/css/neon.css?v=<?= time() ?>">
  <link rel="stylesheet" href="./files/dist/css/tailpanel.css">
  <link rel="stylesheet" href="./files/css/pos.css">
  <!-- jQuery -->
  <script src="./files/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="./files/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="./files/dist/js/adminlte.min.js"></script>

  <!-- jQuery Mapael -->
  <script src="./files/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
  <script src="./files/plugins/raphael/raphael.min.js"></script>
  <script src="./files/plugins/jquery-mapael/jquery.mapael.min.js"></script>
  <script src="./files/plugins/jquery-mapael/maps/usa_states.min.js"></script>
  <!-- ChartJS -->
  <script src="./files/plugins/chart.js/Chart.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <!--<script src="./files/dist/js/demo.js"></script>-->
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="./files/dist/js/pages/dashboard2.js"></script>

  <!-- DataTables -->
  <link rel="stylesheet" href="./files/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="./files/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="./files/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">


  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- Select2 -->
  <link rel="stylesheet" href="./files/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="./files/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="./files/plugins/toastr/toastr.min.css">

  <!-- DataTables  & Plugins -->
  <script src="./files/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="./files/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="./files/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="./files/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="./files/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
  <script src="./files/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
  <script src="./files/plugins/jszip/jszip.min.js"></script>
  <script src="./files/plugins/pdfmake/pdfmake.min.js"></script>
  <script src="./files/plugins/pdfmake/vfs_fonts.js"></script>
  <script src="./files/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
  <script src="./files/plugins/datatables-buttons/js/buttons.print.min.js"></script>
  <script src="./files/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

  <!-- Select2 -->
  <script src="./files/plugins/select2/js/select2.full.min.js"></script>

  <script src="./files/plugins/sweetalert2/sweetalert2.all.js"></script>

  <script src="./files/dist/js/JsBarcode.all.min.js"></script>
  <script src="./files/dist/js/jquery.PrintArea.js"></script>
  <script src="./files/plugins/toastr/toastr.min.js"></script>
  <script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip(); 
    });
  </script>

  <style>
    .select2-container .select2-selection--single {
      height: 38px !important; /* Bootstrap 4 */
      padding: 6px 12px;
      line-height: 1.42857143;
      box-sizing: border-box;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 24px; /* Ajusta según necesidad */
      padding-left: 0px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 36px;
    }

    .loader-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 200px; /* ajusta según tu card */
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e0e0e0;
    border-top: 4px solid #0d6efd; /* color azul bootstrap */
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

.loader-text {
    margin-top: 10px;
    font-size: 14px;
    color: #555;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

  </style>

</head>

<body id="body" class="sidebar-mini layout-fixed text-sm">

  <?php

  if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok" && $_GET["ruta"] != "reset" && $_GET["ruta"] != "recuperar") {


    echo '<div class="wrapper">';

    /*=============================================
    CABEZOTE
    =============================================*/
    if ($_GET["ruta"] != 'pos') {
    include "modulos/cabezote.php";
    }

    /*=============================================
    MENU
    =============================================*/

    include "modulos/menu.php";

    /*=============================================
    CONTENIDO
    =============================================*/

    if (isset($_GET["ruta"])) {

      if (
        $_GET["ruta"] == "inicio" ||
        $_GET["ruta"] == "unidad-medida" ||
        $_GET["ruta"] == "rubro" ||
        $_GET["ruta"] == "recuperar" ||
        $_GET["ruta"] == "reset" ||
        $_GET["ruta"] == "procesar" ||
        $_GET["ruta"] == "reportes-digemid" ||
        $_GET["ruta"] == "reportes-vencimiento" ||
        $_GET["ruta"] == "categoria" ||
        $_GET["ruta"] == "servicio" ||
        $_GET["ruta"] == "producto" ||
        $_GET["ruta"] == "traslado" ||
        $_GET["ruta"] == "restaurant" ||
        $_GET["ruta"] == "nombres-precios" ||
        $_GET["ruta"] == "orden-compra" ||
        $_GET["ruta"] == "compra" ||
        $_GET["ruta"] == "toma-inventario" ||
        $_GET["ruta"] == "ajuste-inventario" ||
        $_GET["ruta"] == "caja-chica" ||
        $_GET["ruta"] == "cuentas-cobrar" ||
        $_GET["ruta"] == "cuentasxpagar" ||
        $_GET["ruta"] == "proveedor" ||
        $_GET["ruta"] == "cotizacion" ||
        $_GET["ruta"] == "venta" ||
        $_GET["ruta"] == "cajas" ||
        $_GET["ruta"] == "pos" ||
        $_GET["ruta"] == "venta-pos" ||
        $_GET["ruta"] == "guia" ||
        $_GET["ruta"] == "service" ||
        $_GET["ruta"] == "nota-credito" ||
        $_GET["ruta"] == "cliente" ||
        $_GET["ruta"] == "asistencia" ||
        $_GET["ruta"] == "personal" ||
        $_GET["ruta"] == "usuario" ||
        $_GET["ruta"] == "permiso" ||
        $_GET["ruta"] == "negocio" ||
        $_GET["ruta"] == "sucursal" ||
        $_GET["ruta"] == "compras-fecha" ||
        $_GET["ruta"] == "compras-proveedor" ||
        $_GET["ruta"] == "ventas-cliente" ||
        $_GET["ruta"] == "ventas-vendedor" ||
        $_GET["ruta"] == "ventas-producto" ||
        $_GET["ruta"] == "ventas-credito" ||
        $_GET["ruta"] == "ventas-servicio" ||
        $_GET["ruta"] == "detalle-venta-comprobante" ||
        $_GET["ruta"] == "kardex" ||
        $_GET["ruta"] == "reporte" ||
        $_GET["ruta"] == "resumen" ||
        $_GET["ruta"] == "salir"
      ) {

        include "modulos/" . $_GET["ruta"] . ".php";
        
      } else {

        include "modulos/404.php";
      }
    } else {

      include "modulos/inicio.php";
      
    }

    /*=============================================
    FOOTER
    =============================================*/

    include "modulos/footer.php";

    echo '</div>';
  } else {

    if (isset($_GET["ruta"]) && $_GET["ruta"] == "recuperar") {
        include "modulos/recuperar.php";

    } elseif (isset($_GET["ruta"]) && $_GET["ruta"] == "reset") {
        include "modulos/reset.php";

    } else {
        include "modulos/login.php";
    }
}


  ?>

</body>

</html>