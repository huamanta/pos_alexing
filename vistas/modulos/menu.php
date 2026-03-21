<aside class="main-sidebar sidebar-light elevation-0 tailpanel-sidebar">
  <!-- Brand Logo -->
  <a href="inicio" class="brand-link">
    <center>
      <span class="brand-text" id="nombreNegocio" style="font-size: 20px;color: black !important;"></span>
    </center>
  </a>

<?php
function tieneSubpermiso($idpermiso, $nombreSubpermiso) {
  return isset($_SESSION['subpermisos'][$idpermiso]) && 
         in_array($nombreSubpermiso, $_SESSION['subpermisos'][$idpermiso]);
}
?>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="./files/personal/<?php echo $_SESSION['imagen']; ?>" class="brand-image img-circle elevation-3" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo $_SESSION['nombre']; ?></a>
        <a href="#"><i class="fa fa-circle text-success text-xs"></i> Online</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


        

        <?php
        if ($_SESSION['inicio'] == 1) {
        ?>
          <li class="nav-item">
            <a href="inicio" class="nav-link" id="navInicio">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                inicio
              </p>
            </a>
          </li>
        <?php
        }
        ?>

        <?php
        if ($_SESSION['procesar'] == 1) {
        ?>
          <li class="nav-item">
            <a href="procesar" class="nav-link" id="navProcesar">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Procesar comprobante
              </p>
            </a>
          </li>
        <?php
        }
        ?>
        
        <?php
        if ($_SESSION['pos'] == 1) {
        ?>
          <li class="nav-item" id="navPos">
            <a href="#" class="nav-link" id="navPosActive">
              <i class="nav-icon fas fas fa-shopping-bag"></i>
              <p>
                Ventas
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            <?php if (tieneSubpermiso(12, 'Punto de Venta')): ?>
            <li class="nav-item" style="font-size: 14px">
                <a href="pos" class="nav-link" id="navPos1">
                  <i class="fas fa-credit-card nav-icon text-primary" style="font-size: 14px"></i>
                  <p>Punto de Venta</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(12, 'Venta Pos')): ?>
              <li class="nav-item" style="font-size: 14px">
              <a href="venta-pos" class="nav-link" id="navCrearVenta">
                <i class="fas fa-shopping-cart nav-icon text-primary" style="font-size: 14px"></i>
                <p>Venta Pos</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(12, 'Guia de Remision')): ?>
              <li class="nav-item" style="font-size: 14px">
              <a href="guia" class="nav-link" id="navGuia">
                <i class="fas fa-truck nav-icon text-primary" style="font-size: 14px"></i>
                <p>Guia de Remision</p>
              </a>
            </li>
            <?php endif; ?>

          <?php if (tieneSubpermiso(12, 'Cotizaciones')): ?>
          <li class="nav-item" style="font-size: 14px">
                <a href="cotizacion" class="nav-link" id="navCotizaciones">
                  <i class="fas fa-file-invoice-dollar nav-icon text-primary" style="font-size: 14px"></i>
                  <p>Cotizaciones</p>
                </a>
              </li>
          <?php endif; ?>
            
            <?php if (tieneSubpermiso(12, 'NotasCredito')): ?>  
              <li class="nav-item" style="font-size: 14px">
                <a href="nota-credito" class="nav-link" id="navNotasCredito">
                  <i class="fas fa-receipt nav-icon text-primary" style="font-size: 14px"></i>
                  <p>Notas de Crédito</p>
                </a>
              </li>
            <?php endif; ?>

            </ul>
          </li>
        <?php
        }
        ?>

        <?php
        if ($_SESSION['crearservicio'] == 1) {
        ?>
          <li class="nav-item" >
            <a href="service" class="nav-link" id="navCrearVenta">
              <i class="nav-icon fa fa-wrench"></i>
              <p>
                Aperturar Servicio
              </p>
            </a>
          </li>
        <?php
        }
        ?>

        <?php
        if ($_SESSION['ventas'] == 1) {
        ?>
          <li class="nav-item" id="navVentas">
            <a href="#" class="nav-link" id="navVentasActive">
              <i class="nav-icon fas fa-store-alt"></i>
              <p>
                Facturación y Cajas
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            <?php if (tieneSubpermiso(4, 'Comprobantes')): ?>
            <li class="nav-item" style="font-size: 14px">
                <a href="venta" class="nav-link" id="navVenta">
                  <i class="fas fa-file-invoice nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Lista de Comprobantes</p>
                </a>
              </li>
            <?php endif; ?>

            <li class="nav-item" style="font-size: 14px">
                <a href="resumen" class="nav-link" id="navResumen">
                  <i class="fas fa-file-alt nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Resumen Diario</p>
                </a>
              </li>

            <?php if (tieneSubpermiso(4, 'Cajas')): ?>
            <li class="nav-item" style="font-size: 14px">
            <a href="cajas" class="nav-link" id="navCajas">
              <i class="fas fa-cash-register nav-icon text-orange" style="font-size: 14px"></i>
              <p>Cajas</p>
            </a>
          </li>
          <?php endif; ?>

          <?php if (tieneSubpermiso(4, 'Clientes')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="cliente" class="nav-link" id="navCliente">
                  <i class="fas fa-users nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Clientes</p>
                </a>
              </li>
            <?php endif; ?>
            </ul>
          </li>
        <?php
        }
        ?>



        <?php
          if ($_SESSION['almacen'] == 1) {
          ?>
            <li class="nav-item" id="navAlmacen">
              <a href="#" class="nav-link" id="navAlmacenActive">
                <i class="nav-icon fas fa-home"></i>
                <p>
                  Ingreso a almacen
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php if (tieneSubpermiso(2, 'Productos')): ?>
                  <li class="nav-item" style="font-size: 14px">
                    <a href="producto" class="nav-link" id="navProducto">
                      <i class="fas fa-boxes nav-icon text-purple" style="font-size: 14px"></i>
                      <p>Productos</p>
                    </a>
                  </li>
                <?php endif; ?>

                <?php if (tieneSubpermiso(2, 'Servicios')): ?>
                  <li class="nav-item" style="font-size: 14px">
                    <a href="servicio" class="nav-link" id="navServicio">
                      <i class="fas fa-concierge-bell nav-icon text-purple" style="font-size: 14px"></i>
                      <p>Servicios</p>
                    </a>
                  </li>
                <?php endif; ?>

                <?php if (tieneSubpermiso(2, 'Nombres Precios')): ?>
                  <li class="nav-item" style="font-size: 14px">
                    <a href="nombres-precios" class="nav-link" id="navNombrep">
                      <i class="fas fa-tags nav-icon text-purple" style="font-size: 14px"></i>
                      <p>Nombres precios</p>
                    </a>
                  </li>
                <?php endif; ?>

                <?php if (tieneSubpermiso(2, 'Categorias')): ?>
                  <li class="nav-item" style="font-size: 14px">
                    <a href="categoria" class="nav-link" id="navCategoria">
                      <i class="fas fa-layer-group nav-icon text-purple" style="font-size: 14px"></i>
                      <p>Categorías</p>
                    </a>
                  </li>
                <?php endif; ?>

                <?php if (tieneSubpermiso(2, 'Unidad de medida')): ?>
                  <li class="nav-item" style="font-size: 14px">
                    <a href="unidad-medida" class="nav-link" id="navUnidadMedida">
                      <i class="fas fa-ruler-combined nav-icon text-purple" style="font-size: 14px"></i>
                      <p>Unidad de Medida</p>
                    </a>
                  </li>
                <?php endif; ?>

                <?php if (tieneSubpermiso(2, 'Traslados')): ?>
                  <li class="nav-item" style="font-size: 14px">
                    <a href="traslado" class="nav-link" id="navTraslado">
                      <i class="fas fa-concierge-bell nav-icon text-purple" style="font-size: 14px"></i>
                      <p>Traslados</p>
                    </a>
                  </li>
                <?php endif; ?>

                <?php if (tieneSubpermiso(2, 'Rubro')): ?>
                  <li class="nav-item" style="font-size: 14px">
                    <a href="rubro" class="nav-link" id="navRubro">
                      <i class="fas fa-industry nav-icon text-purple" style="font-size: 14px"></i>
                      <p>Rubro</p>
                    </a>
                  </li>
                <?php endif; ?>

                <?php if (tieneSubpermiso(2, 'Reportes')): ?>
                  <li class="nav-item" style="font-size: 14px">
                    <a href="reportes-digemid" class="nav-link" id="navReportes">
                      <i class="fas fa-chart-bar nav-icon text-purple" style="font-size: 14px"></i>
                      <p>Reportes</p>
                    </a>              
                  </li>
                <?php endif; ?>

                <?php if (tieneSubpermiso(2, 'Vencimiento')): ?>
                  <li class="nav-item" style="font-size: 14px">
                    <a href="reportes-vencimiento" class="nav-link" id="navVencimiento">
                      <i class="fas fa-hourglass-end nav-icon text-purple" style="font-size: 14px"></i>
                      <p>Vencimiento</p>
                    </a>              
                  </li>
                <?php endif; ?>
              </ul>
            </li>
          <?php
          }
          ?>

        <?php
        if ($_SESSION['inventario'] == 1) {
        ?>
          <li class="nav-item" id="navInventario">
            <a href="#" class="nav-link" id="navInventarioActive">
              <i class="nav-icon fas fa-box"></i>
              <p>
                Inventario
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

            <?php if (tieneSubpermiso(15, 'Ajuste Inventario')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="toma-inventario" class="nav-link" id="navtoma-inventario">
                  <i class="fas fa-clipboard-list nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Toma de inventario</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(15, 'Toma Inventario')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="ajuste-inventario" class="nav-link" id="navajuste-inventario">
                  <i class="fas fa-sliders-h nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Ajuste de inventario</p>
                </a>
              </li>
            <?php endif; ?>

            </ul>
          </li>
        <?php
        }
        ?>

        <?php
        if ($_SESSION['compras'] == 1) {
        ?>
          <li class="nav-item" id="navCompras">
            <a href="#" class="nav-link" id="navComprasActive">
              <i class="nav-icon fas fas fa-dolly"></i>
              <p>
                Compras
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <?php if (tieneSubpermiso(3, 'CrearCompras')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="compra" class="nav-link" id="navCompra">
                    <i class="fas fa-cart-arrow-down nav-icon text-orange" style="font-size: 14px"></i>
                    <p>Crear Compras</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (tieneSubpermiso(3, 'Proveedores')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="proveedor" class="nav-link" id="navProveedor">
                    <i class="fas fa-truck nav-icon text-orange" style="font-size: 14px"></i>
                    <p>Proveedores</p>
                  </a>
                </li>
              <?php endif; ?>
              
            </ul>
          </li>
        <?php
        }
        ?>

        <?php
        if ($_SESSION['cajachica'] == 1) {
        ?>
          <li class="nav-item">
            <a href="caja-chica" class="nav-link" id="navCajaChica">
              <i class="nav-icon fas fa-hand-holding-usd"></i>
              <p>
                Caja chica
              </p>
            </a>
          </li>
        <?php
        }
        ?>

        <?php
        if ($_SESSION['cuentascobrar'] == 1) {
        ?>
          <li class="nav-item">
            <a href="cuentas-cobrar" class="nav-link" id="navCuentasPorCobrar">
              <i class="nav-icon fa fa-list-ul"></i>
              <p>
              Cuentas por Cobrar
              </p>
            </a>
          </li>
        <?php
        }
        ?>

        <?php
          if ($_SESSION['cuentasxpagar'] == 1) {
        ?>
          <li class="nav-item">
            <a href="cuentasxpagar" class="nav-link" id="navCuentasPorCobrar">
              <i class="nav-icon fa fa-list-ul"></i>
              <p>
              Cuentas por Pagar
              </p>
            </a>
          </li>
        <?php
          }
        ?>
        
        <?php
        if ($_SESSION['kardex'] == 1) {
        ?>
          <li class="nav-item">
            <a href="kardex" class="nav-link" id="navKardex">
              <i class="nav-icon fa fa-list-ul"></i>
              <p>
                Kardex
              </p>
            </a>
          </li>
        <?php
        }
        ?>



        

        <?php
        if ($_SESSION['personal'] == 1) {
        ?>
          <li class="nav-item" id="navPersonal">
            <a href="#" class="nav-link" id="navPersonalActive">
              <i class="nav-icon fas fa-users-cog"></i>
              <p>
                Personal
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

            <?php if (tieneSubpermiso(5, 'Asistencia')): ?>  
              <li class="nav-item" style="font-size: 14px">
                <a href="asistencia" class="nav-link" id="navAsistencia">
                  <i class="fas fa-calendar-check nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Asistencia</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(5, 'Personal')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="personal" class="nav-link" id="navPersonalI">
                  <i class="fas fa-user-tie nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Personal</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(5, 'Usuarios')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="usuario" class="nav-link" id="navUsuario">
                  <i class="fas fa-users-cog nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Usuarios</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(5, 'Permisos')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="permiso" class="nav-link">
                  <i class="fas fa-key nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Permisos</p>
                </a>
              </li>
            <?php endif; ?>

          </ul>

          </li>
        <?php
        }
        ?>

        <?php
        if ($_SESSION['configuracion'] == 1) {
        ?>
          <li class="nav-item" id="navConfiguracion">
            <a href="#" class="nav-link" id="navConfiguracionActive">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                Configuración
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            <?php if (tieneSubpermiso(8, 'Datos Generales')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="negocio" class="nav-link" id="navDatosGeneralesI">
                  <i class="fas fa-building nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Datos Generales</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(8, 'Facturadores')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="empresas" class="nav-link" id="navFacturadores">
                  <i class="fas fa-file-alt nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Facturadores</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(8, 'Sucursales')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="sucursal" class="nav-link" id="navSucursal">
                  <i class="fas fa-map-marker-alt nav-icon text-orange" style="font-size: 14px"></i>
                  <p>Sucursales</p>
                </a>
              </li>
            <?php endif; ?>

            </ul>
          </li>
        <?php
        }
        ?>

        <?php
        if ($_SESSION['consultac'] == 1) {
        ?>
          <li class="nav-item" id="navConsultaCompras">
            <a href="#" class="nav-link" id="navConsultaComprasActive">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>
                Consulta Compras
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <?php if (tieneSubpermiso(6, 'Consulta Compras')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="compras-fecha" class="nav-link" id="navConsultaComprasI">
                    <i class="fas fa-calendar-alt nav-icon text-info" style="font-size: 14px"></i>
                    <p>Consulta Compras</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (tieneSubpermiso(6, 'Consulta Compras XP')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="compras-proveedor" class="nav-link" id="navConsultaComprasII">
                    <i class="fas fa-truck-loading nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Compras por proveedor</p>
                  </a>
                </li>
              <?php endif; ?>
              
            </ul>
          </li>
        <?php
        }
        ?>

        <?php
        if ($_SESSION['consultav'] == 1) {
        ?>
          <li class="nav-item" id="navConsultaVentas">
            <a href="#" class="nav-link" id="navConsultaVentasActive">
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
                Consulta Ventas
                <i class="fas fa-angle-left right" style="font-size: 14px"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

            <?php if (tieneSubpermiso(7, 'Ventas Cliente')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="ventas-cliente" class="nav-link" id="navVentasCliente">
                  <i class="fas fa-user-tag nav-icon text-primary" style="font-size: 14px"></i>
                  <p>Ventas x Cliente</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(7, 'Ventas Vendedor')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="ventas-vendedor" class="nav-link" id="navVentasVendedor">
                  <i class="fas fa-user-tie nav-icon text-primary" style="font-size: 14px"></i>
                  <p>Ventas x Vendedor</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(7, 'Ventas Utilidades')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="ventas-producto" class="nav-link" id="navVentasProducto">
                  <i class="fas fa-chart-line nav-icon text-primary" style="font-size: 14px"></i>
                  <p>Ventas - Utilidades</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(7, 'Creditos Utilidades')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="ventas-credito" class="nav-link" id="navVentasCredito">
                  <i class="fas fa-credit-card nav-icon text-primary" style="font-size: 14px"></i>
                  <p>Créditos - Utilidades</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(7, 'Consolidado')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="reporte" class="nav-link" id="navVentasCredito">
                  <i class="fas fa-credit-card nav-icon text-primary" style="font-size: 14px"></i>
                  <p>Reporte Consolidado</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(7, 'Ventas Servicios')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="ventas-servicio" class="nav-link" id="navVentasServicio">
                  <i class="fas fa-concierge-bell nav-icon text-primary" style="font-size: 14px"></i>
                  <p>Ventas x Servicio</p>
                </a>
              </li>
            <?php endif; ?>

            <?php if (tieneSubpermiso(7, 'Ventas Detalle')): ?>
              <li class="nav-item" style="font-size: 14px">
                <a href="detalle-venta-comprobante" class="nav-link" id="navVentasDetalle">
                  <i class="fas fa-receipt nav-icon text-primary" style="font-size: 14px"></i>
                  <p>Ventas Detalle</p>
                </a>
              </li>
            <?php endif; ?>

            </ul>
          </li>
        <?php
        }
        ?>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>

<script src="vistas/js/menu.js"></script>