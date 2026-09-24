<?php
require_once __DIR__ . '/../../modelos/Helpers.php';
require_once __DIR__ . '/../../core/Rutas.php';

$helpers = new Helpers();

$imagenUsuario = $_SESSION['imagen'] ?? null;
$nombreUsuario = $_SESSION['nombre'] ?? 'Usuario';

$imagenPath = "./files/personal/" . $imagenUsuario;
$tieneImagen = !empty($imagenUsuario) && file_exists($imagenPath);

$rutaActual = $_GET['ruta'] ?? 'inicio';

if (is_array($rutaActual)) {
    $rutaActual = $rutaActual['url'] ?? 'inicio';
}

$rutasMenu = Rutas::obtenerRutasMenu();

$modulos = [];
$rutasIndependientes = [];

foreach ($rutasMenu as $ruta) {
    if (!empty($ruta['modulo'])) {
        if (!isset($modulos[$ruta['modulo']])) {
            $modulos[$ruta['modulo']] = [];
        }

        $modulos[$ruta['modulo']][] = $ruta;
    } else {
        $rutasIndependientes[] = $ruta;
    }
}
?>

<aside class="main-sidebar sidebar-light elevation-0 tailpanel-sidebar">

  <a href="inicio" class="brand-link">
    <?php if ($tieneImagen): ?>
      <img
        src="<?php echo htmlspecialchars($imagenPath); ?>"
        class="brand-image img-circle elevation-2"
        alt="User Image">
    <?php else: ?>
      <div
        class="brand-image img-circle elevation-2 d-flex align-items-center justify-content-center"
        style="width:35px;height:35px;font-weight:bold;font-size:16px;">
        SP
      </div>
    <?php endif; ?>

    <span
      class="brand-text font-weight-light"
      id="nombreNegocio"
      style="font-weight:bold;font-size:14px;"></span>
  </a>

  <div class="sidebar">

    <div class="user-panel mt-3 pb-3 mb-3 d-flex">

      <div class="image">
        <?php if ($tieneImagen): ?>

          <img
            src="<?php echo htmlspecialchars($imagenPath); ?>"
            class="brand-image img-circle elevation-2"
            alt="User Image">

        <?php else: ?>

          <div
            class="brand-image img-circle elevation-2 d-flex align-items-center justify-content-center bg-primary text-white"
            style="width:35px;height:35px;font-weight:bold;font-size:16px;">
            <?php echo strtoupper(substr($nombreUsuario, 0, 1)); ?>
          </div>

        <?php endif; ?>
      </div>

      <div class="info">
        <a href="#" class="d-block">
          <?php echo htmlspecialchars($nombreUsuario); ?>
        </a>

        <a href="#">
          <i class="fa fa-circle text-success text-xs"></i>
          Online
        </a>
      </div>

    </div>

    <nav class="mt-2">

      <ul
        class="nav nav-pills nav-sidebar flex-column"
        data-widget="treeview"
        role="menu"
        data-accordion="false">

        <?php foreach ($rutasIndependientes as $ruta): ?>

          <?php
          if (empty($ruta['permiso'])) {
              continue;
          }

          if (!$helpers->getUserPermisoModulo($ruta['permiso'])) {
              continue;
          }

          $url = $ruta['url'];
          $nombre = $ruta['submodulo'];
          $icono = $ruta['icono'] ?? 'fas fa-circle';

          $activo = $rutaActual === $url;

          $id = 'nav' . preg_replace(
              '/[^a-zA-Z0-9]/',
              '',
              ucwords(str_replace('-', ' ', $url))
          );
          ?>

          <li class="nav-item">

            <a
              href="<?php echo htmlspecialchars($url); ?>"
              class="nav-link <?php echo $activo ? 'active' : ''; ?>"
              id="<?php echo htmlspecialchars($id); ?>">

              <i class="nav-icon <?php echo htmlspecialchars($icono); ?>"></i>

              <p>
                <?php echo htmlspecialchars($nombre); ?>
              </p>

            </a>

          </li>

        <?php endforeach; ?>


        <?php foreach ($modulos as $nombreModulo => $rutas): ?>

          <?php
          $rutasPermitidas = [];
          $moduloActivo = false;

          foreach ($rutas as $ruta) {

              if (empty($ruta['permiso'])) {
                  continue;
              }

              if (!$helpers->getUserPermisoModulo(
                  $ruta['permiso'],
                  $nombreModulo
              )) {
                  continue;
              }

              $rutasPermitidas[] = $ruta;

              if ($rutaActual === $ruta['url']) {
                  $moduloActivo = true;
              }
          }

          if (empty($rutasPermitidas)) {
              continue;
          }

          $iconoModulo = Rutas::MODULOS[$nombreModulo] ?? 'fas fa-folder';

          $idModulo = 'nav' . preg_replace(
              '/[^a-zA-Z0-9]/',
              '',
              ucwords($nombreModulo)
          );

          $nombreModuloMostrar = ucfirst($nombreModulo);
          ?>

          <li
            class="nav-item <?php echo $moduloActivo ? 'menu-open' : ''; ?>"
            id="<?php echo htmlspecialchars($idModulo); ?>">

            <a
              href="#"
              class="nav-link <?php echo $moduloActivo ? 'active' : ''; ?>"
              id="<?php echo htmlspecialchars($idModulo . 'Active'); ?>">

              <i class="nav-icon <?php echo htmlspecialchars($iconoModulo); ?>"></i>

              <p>
                <?php echo htmlspecialchars($nombreModuloMostrar); ?>

                <i class="fas fa-angle-left right"></i>
              </p>

            </a>

            <ul class="nav nav-treeview">

              <?php foreach ($rutasPermitidas as $ruta): ?>

                <?php
                $url = $ruta['url'];
                $nombre = $ruta['submodulo'];
                $icono = $ruta['icono'] ?? 'fas fa-circle';

                $activo = $rutaActual === $url;

                $id = 'nav' . preg_replace(
                    '/[^a-zA-Z0-9]/',
                    '',
                    ucwords(str_replace('-', ' ', $url))
                );
                ?>

                <li
                  class="nav-item"
                  style="font-size:14px">

                  <a
                    href="<?php echo htmlspecialchars($url); ?>"
                    class="nav-link <?php echo $activo ? 'active' : ''; ?>"
                    id="<?php echo htmlspecialchars($id); ?>">

                    <i
                      class="<?php echo htmlspecialchars($icono); ?> nav-icon"
                      style="font-size:14px"></i>

                    <p>
                      <?php echo htmlspecialchars($nombre); ?>
                    </p>

                  </a>

                </li>

              <?php endforeach; ?>

            </ul>

          </li>

        <?php endforeach; ?>

      </ul>

    </nav>

  </div>

</aside>

<script src="vistas/js/menu.js"></script>