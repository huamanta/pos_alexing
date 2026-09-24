<?php
//redirigir directamente
$rutaActual = $_GET['ruta'] ?? null;

if (
  empty($rutaActual) &&
  isset($_SESSION["iniciarSesion"]) &&
  $_SESSION["iniciarSesion"] == "ok"
) {
  header("Location: " . $_SERVER['PHP_SELF'] . "?ruta=inicio");
  exit();
}
if (isset($rutaActual)) {

  if ($rutaActual == "salir") {
    include "modulos/salir.php";
    exit;
  }

  if ($rutaActual == "salirsucursal") {
    include "modulos/salirsucursal.php";
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>SYSPIDER TECHNOLOGY</title>
  <link rel="icon" type="image/x-icon" href="files/favicon.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="./files/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="./files/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="./files/dist/css/adminlte.min.css">
  <link href="./files/css/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./files/dist/css/neon.css">
  <link rel="stylesheet" href="./files/dist/css/tailpanel.css">
  <link rel="stylesheet" href="./files/css/pos.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@7.0.2/skeleton.min.css">
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


  <!-- Select2 -->
  <link rel="stylesheet" href="./files/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="./files/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="./files/plugins/toastr/toastr.min.css">
  <link rel="stylesheet" href="./files/plugins/fullcalendar/main.min.css">

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

  <script src="./files/plugins/fullcalendar/main.min.js"></script>
  <script src="./vistas/js/pagination.js"></script>
  <script>
    $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>

  <style>
    .select2-container .select2-selection--single {
      height: 38px !important;
      /* Bootstrap 4 */
      padding: 6px 12px;
      line-height: 1.42857143;
      box-sizing: border-box;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 24px;
      /* Ajusta según necesidad */
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
      height: 200px;
      /* ajusta según tu card */
    }

    .spinner {
      width: 40px;
      height: 40px;
      border: 4px solid #e0e0e0;
      border-top: 4px solid #0d6efd;
      /* color azul bootstrap */
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    .loader-text {
      margin-top: 10px;
      font-size: 14px;
      color: #555;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }
  </style>

</head>

<body id="body" class="sidebar-mini layout-fixed text-sm">
  <?php
  $rutaUrl = $_GET['ruta'] ?? 'inicio';
  if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok" && $rutaUrl != "reset" && $rutaUrl != "recuperar") {

    echo '<div class="wrapper">     
                <input type="hidden" value="' . ($_SESSION['monto_impuesto'] ?? '') . '" id="valorImpuestoGlobal"/>';
    /*=============================================
    CABEZOTE
    =============================================*/
    if ($rutaUrl != 'pos') {
      include "modulos/cabezote.php";
    }

    /*=============================================
    MENU
    =============================================*/
    include "modulos/menu.php";

    /*=============================================
    CONTENIDO
    =============================================*/

    if (isset($_SESSION['idsucursal']) && !empty($_SESSION['idsucursal'])) {

      require_once __DIR__ . "/../core/Rutas.php";
      require_once __DIR__ . "/../modelos/Helpers.php";

      $helpers = new Helpers();

      $rutaConfig = Rutas::obtener((string) $rutaUrl);

      if ($rutaConfig === null) {
        include __DIR__ . "/modulos/errores/404.php";
        exit;
      }

      if ($rutaConfig['submodulo']) {
        $permiso = $helpers->getUserPermisoModulo(
          $rutaConfig['permiso'],
          $rutaConfig['modulo']
        );
      } else {
        $permiso = $helpers->getUserPermisoModulo(
          $rutaConfig['permiso']
        );
      }

      if ($rutaConfig['permiso'] !== null && !$permiso) {
        include __DIR__ . "/modulos/errores/403.php";
        exit;
      }

      include __DIR__ . "/modulos/" . $rutaConfig['url'] . ".php";
    } else {

      include __DIR__ . "/modulos/elegir-sucursal.php";
    }

    /*=============================================
    FOOTER
    =============================================*/

    include "modulos/footer.php";

    echo '</div>';
  } else {

    if (isset($rutaUrl) && $rutaUrl == "recuperar") {
      include "modulos/recuperar.php";
    } elseif (isset($rutaUrl) && $rutaUrl == "reset") {
      include "modulos/reset.php";
    } else {
      include "modulos/login.php";
    }

  ?>
</body>

</html>
<?php
  }
?>