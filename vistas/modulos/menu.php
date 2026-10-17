<?php
require_once __DIR__ . '/../../modelos/Helpers.php';
require_once __DIR__ . '/../../core/Rutas.php';

$helpers = new Helpers();
$rutas = new Rutas();

$imagenUsuario = $_SESSION['imagen'] ?? null;
$nombreUsuario = $_SESSION['nombre'] ?? 'Usuario';

$imagenPath = "./files/personal/" . $imagenUsuario;
$imagenDefault = "./files/logos/default.png";
$tieneImagen = !empty($imagenUsuario) && file_exists($imagenPath);

$rutaActual = $_GET['ruta'] ?? 'inicio';

if (is_array($rutaActual)) {
  $rutaActual = $rutaActual['url'] ?? 'inicio';
}

$modulos = $rutas->listaRutas();

?>

<aside class="main-sidebar sidebar-light elevation-0 tailpanel-sidebar">

  <a href="inicio" class="brand-link">
    <div class="brand-image img-circle">
      <i class="fas fa-layer-group" style="font-size: 2rem;"></i>
    </div>
    <div class="brand-text" style="font-weight: 700; font-size: 18px;">
      VENTAS<span style="color: blue;">PRO</span>
    </div>
  </a>

  <div class="sidebar">

    <div class="user-panel mt-3 pb-3 mb-3 d-flex">

      <div class="image">
        <?php if ($tieneImagen): ?>

          <img src="<?php echo htmlspecialchars($imagenPath); ?>" class="brand-image img-circle elevation-2"
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
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <?php foreach ($modulos as $modulo): ?>
  
          <?php
          if($modulo['menu'] != 1){
            continue;
          }
          $nombreModulo = $modulo['nombre'] ?? null;

          // Las rutas internas no se muestran en el menú
          if ($nombreModulo === null) {
            continue;
          }

          $iconoModulo = $modulo['icono'] ?? 'fas fa-folder';
          $children = $modulo['childrens'] ?? [];

          // Módulo independiente
          if (empty($children)) {
            if (empty($modulo['ruta'])) {
              continue;
            }

            $url = $modulo['ruta'];
            $activo = $rutaActual === $url;
            $id = 'nav' . preg_replace('/[^a-zA-Z0-9]/', '', $url);
            ?>

            <li class="nav-item">
              <a href="<?= htmlspecialchars($url) ?>" class="nav-link <?= $activo ? 'active' : '' ?>"
                id="<?= htmlspecialchars($id) ?>">
                <i class="nav-icon <?= htmlspecialchars($iconoModulo) ?>"></i>
                <p><?= htmlspecialchars($nombreModulo) ?></p>
              </a>
            </li>

            <?php continue; ?>
          <?php } ?>

          <?php
          // Filtrar rutas internas y validar permisos de los hijos
          $children = array_filter($children, function ($hijo) use ($helpers, $nombreModulo) {
            if (($hijo['nombre'] ?? null) === null) {
              return false;
            }

            $permiso = $hijo['permiso'] ?? null;

            return empty($permiso);
          });

          // No mostrar módulos sin hijos autorizados
          if (empty($children)) {
            continue;
          }

          $moduloActivo = false;

          foreach ($children as $hijo) {
            if ($rutaActual === ($hijo['ruta'] ?? '')) {
              $moduloActivo = true;
              break;
            }
          }

          $idModulo = 'nav' . preg_replace(
            '/[^a-zA-Z0-9]/',
            '',
            str_replace(' ', '', $nombreModulo)
          );
          ?>

          <li class="nav-item <?= $moduloActivo ? 'menu-open' : '' ?>" id="<?= htmlspecialchars($idModulo) ?>">

            <a href="#" class="nav-link <?= $moduloActivo ? 'active' : '' ?>">
              <i class="nav-icon <?= htmlspecialchars($iconoModulo) ?>"></i>
              <p>
                <?= htmlspecialchars($nombreModulo) ?>
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">
              <?php foreach ($children as $hijo): ?>

                <?php
                $url = $hijo['ruta'];
                $nombre = $hijo['nombre'];
                $icono = $hijo['icono'] ?? 'fas fa-circle';
                $activo = $rutaActual === $url;

                $id = 'nav' . preg_replace(
                  '/[^a-zA-Z0-9]/',
                  '',
                  str_replace(' ', '', $url)
                );
                ?>

                <li class="nav-item">
                  <a href="<?= htmlspecialchars($url) ?>" class="nav-link <?= $activo ? 'active' : '' ?>"
                    id="<?= htmlspecialchars($id) ?>">
                    <i class="nav-icon <?= htmlspecialchars($icono) ?>" style="font-size:14px"></i>
                    <p><?= htmlspecialchars($nombre) ?></p>
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