<?php
require_once __DIR__ . '/../../modelos/Helpers.php';
?>
<aside class="main-sidebar sidebar-light elevation-0 tailpanel-sidebar">
  <!-- Brand Logo -->
  <a href="inicio" class="brand-link">
    <?php if (!empty($_SESSION['imagen']) && file_exists("./files/personal/" . $_SESSION['imagen'])) { ?>
      <img src="./files/personal/<?php echo $_SESSION['imagen']; ?>" class="brand-image img-circle elevation-2"
        alt="User Image">
    <?php } else { ?>
      <div class="brand-image img-circle elevation-2 d-flex align-items-center justify-content-center"
        style="width: 35px; height: 35px; font-weight: bold; font-size: 16px;">
        SP
      </div>
    <?php } ?>
    <span class="brand-text font-weight-light" id="nombreNegocio" style="font-weight: bold; font-size: 14px;"> </span>
  </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <?php if (!empty($_SESSION['imagen']) && file_exists("./files/personal/" . $_SESSION['imagen'])) { ?>

          <img src="./files/personal/<?php echo $_SESSION['imagen']; ?>" class="brand-image img-circle elevation-2"
            alt="User Image">

        <?php } else { ?>

          <div
            class="brand-image img-circle elevation-2 d-flex align-items-center justify-content-center bg-primary text-white"
            style="width: 35px; height: 35px; font-weight: bold; font-size: 16px;">

            <?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?>

          </div>

        <?php } ?>
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo $_SESSION['nombre']; ?></a>
        <a href="#"><i class="fa fa-circle text-success text-xs"></i> Online</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <?php
        if (Helpers::getUserPermisoModulo('inicio')) {
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
        if (Helpers::getUserPermisoModulo('procesar')) {
          ?>
          <!--li class="nav-item">
            <a href="procesar" class="nav-link" id="navProcesar">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Procesar comprobante
              </p>
            </a>
          </li-->
          <?php
        }
        ?>

        <?php
        if (Helpers::getUserPermisoModulo('Ventas')) {
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
              <?php if (Helpers::getUserPermisoModulo('Contratos', 'Ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="contrato" class="nav-link" id="navContratos">
                    <i class="fas fa-file-contract nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Contratos</p>
                  </a>
                </li>
              <?php endif; ?>
              <?php if (Helpers::getUserPermisoModulo('Solicitudes', 'Ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="solicitudes" class="nav-link" id="navSolicitudes">
                    <i class="fas fa-file-contract nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Solicitudes</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Venta Pos', 'Ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="venta-pos" class="nav-link" id="navCrearVenta">
                    <i class="fas fa-shopping-cart nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Punto de venta</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Guia de Remision', 'Ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="guia" class="nav-link" id="navGuia">
                    <i class="fas fa-truck nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Guia de Remision</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Cotizaciones', 'Ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="cotizacion" class="nav-link" id="navCotizaciones">
                    <i class="fas fa-file-invoice-dollar nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Cotizaciones</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('NotasCredito', 'Ventas')): ?>
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
        if (Helpers::getUserPermisoModulo('crearservicio')) {
          ?>
          <!--li class="nav-item">
            <a href="service" class="nav-link" id="navCrearVenta">
              <i class="nav-icon fa fa-wrench"></i>
              <p>
                Aperturar Servicio
              </p>
            </a>
          </li-->
          <?php
        }
        ?>

        <?php if (Helpers::getUserPermisoModulo('Clientes')): ?>
          <li class="nav-item" style="font-size: 14px">
            <a href="cliente" class="nav-link" id="navClienteActive">
              <i class="fas fa-users nav-icon" style="font-size: 14px"></i>
              <p>Clientes</p>
            </a>
          </li>
        <?php endif; ?>

        <?php
        if (Helpers::getUserPermisoModulo('Facturacion y cajas')) {
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
              <?php if (Helpers::getUserPermisoModulo('Comprobantes', 'Facturacion y cajas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="venta" class="nav-link" id="navVenta">
                    <i class="fas fa-file-invoice nav-icon te xt-orange" style="font-size: 14px"></i>
                    <p>Lista de Comprobantes</p>
                  </a>
                </li>
              <?php endif; ?>
              <?php if (Helpers::getUserPermisoModulo('Resumen diario', 'Facturacion y cajas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="resumen" class="nav-link" id="navResumen">
                    <i class="fas fa-file-alt nav-icon text-orange" style="font-size: 14px"></i>
                    <p>Resumen Diario</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Cajas', 'Facturacion y cajas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="cajas" class="nav-link" id="navCajas">
                    <i class="fas fa-cash-register nav-icon text-orange" style="font-size: 14px"></i>
                    <p>Cajas</p>
                  </a>
                </li>
              <?php endif; ?>
            </ul>
          </li>
          <?php
        }
        ?>



        <?php
        if (Helpers::getUserPermisoModulo('Almacen')) {
          ?>
          <li class="nav-item" id="navAlmacen">
            <a href="#" class="nav-link" id="navAlmacenActive">
              <i class="nav-icon fas fa-home"></i>
              <p>
                Almacén
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php if (Helpers::getUserPermisoModulo('Productos', 'Almacen')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="producto" class="nav-link" id="navProducto">
                    <i class="fas fa-boxes nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Productos</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Servicios', 'Almacen')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="servicio" class="nav-link" id="navServicio">
                    <i class="fas fa-concierge-bell nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Servicios</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Nombres Precios', 'Almacen')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="nombres-precios" class="nav-link" id="navNombrep">
                    <i class="fas fa-tags nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Nombres precios</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Categorias', 'Almacen')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="categoria" class="nav-link" id="navCategoria">
                    <i class="fas fa-layer-group nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Categorías</p>
                  </a>
                </li>
              <?php endif; ?>
              <?php if (Helpers::getUserPermisoModulo('Marcas', 'Almacen')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="marca" class="nav-link" id="navMarca">
                    <i class="fas fa-newspaper nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Marcas</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Modelos', 'Almacen')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="modelo" class="nav-link" id="navModelo">
                    <i class="fas fa-tags nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Modelos</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Lineas', 'Almacen')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="rubro" class="nav-link" id="navLinea">
                    <i class="fas fa-server nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Lineas</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Condicion de venta', 'Almacen')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="condicionventa" class="nav-link" id="navCondicionVenta">
                    <i class="fas fa-server nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Condición de Venta</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Unidad de medida', 'Almacen')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="unidad-medida" class="nav-link" id="navUnidadMedida">
                    <i class="fas fa-ruler-combined nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Unidad de Medida</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Traslados', 'Almacen')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="traslado" class="nav-link" id="navTraslado">
                    <i class="fas fa-concierge-bell nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Traslados</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Reportes', 'Almacen')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="reportes-digemid" class="nav-link" id="navReportes">
                    <i class="fas fa-chart-bar nav-icon text-purple" style="font-size: 14px"></i>
                    <p>Reportes</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Vencimientos', 'Almacen')): ?>
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
        if (Helpers::getUserPermisoModulo('Inventario')) {
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

              <?php if (Helpers::getUserPermisoModulo('Toma de nventario', 'Inventario')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="toma-inventario" class="nav-link" id="navtoma-inventario">
                    <i class="fas fa-clipboard-list nav-icon text-orange" style="font-size: 14px"></i>
                    <p>Toma de inventario</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Ajuste de inventario', 'Inventario')): ?>
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
        if (Helpers::getUserPermisoModulo('Compras')) {
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

              <?php if (Helpers::getUserPermisoModulo('Crear compras', 'Compras')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="compra" class="nav-link" id="navCompra">
                    <i class="fas fa-cart-arrow-down nav-icon text-orange" style="font-size: 14px"></i>
                    <p>Crear Compras</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Proveedores', 'Compras')): ?>
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
        if (Helpers::getUserPermisoModulo('Caja chica')) {
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
        if (Helpers::getUserPermisoModulo('Cobros')) {
          ?>
          <li class="nav-item" id="navCobros">
            <a href="#" class="nav-link" id="navCobrosActive">
              <i class="nav-icon fas fa-box"></i>
              <p>
                Cobros
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">

              <?php if (Helpers::getUserPermisoModulo('Cuentas por cobrar', 'Cobros')): ?>

                <li class="nav-item">
                  <a href="cuentas-cobrar" class="nav-link" id="navCuentasPorCobrar">
                    <i class="nav-icon fa fa-list-ul  text-primary"></i>
                    <p>
                      Cuentas por Cobrar
                    </p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Refinanciar creditos', 'Cobros')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="refinanciamientos" class="nav-link" id="navRefinanciarDeuda">
                    <i class="fas fa-sliders-h nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Refinanciar créditos</p>
                  </a>
                </li>
              <?php endif; ?>

            </ul>
          </li>
          <?php
        }
        ?>

        <?php
        if (Helpers::getUserPermisoModulo('Cuentas por pagar')) {
          ?>
          <li class="nav-item">
            <a href="cuentasxpagar" class="nav-link" id="navCuentasPorPagar">
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
        if (Helpers::getUserPermisoModulo('Kardex')) {
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
        if (Helpers::getUserPermisoModulo('Personal')) {
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

              <?php if (Helpers::getUserPermisoModulo('Asistencia', 'Personal')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="asistencia" class="nav-link" id="navAsistencia">
                    <i class="fas fa-calendar-check nav-icon text-orange" style="font-size: 14px"></i>
                    <p>Asistencia</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Personal', 'Personal')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="personal" class="nav-link" id="navPersonalI">
                    <i class="fas fa-user-tie nav-icon text-orange" style="font-size: 14px"></i>
                    <p>Personal</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Usuarios', 'Personal')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="usuario" class="nav-link" id="navUsuario">
                    <i class="fas fa-users-cog nav-icon text-orange" style="font-size: 14px"></i>
                    <p>Usuarios</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Permisos', 'Personal')): ?>
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
        if (Helpers::getUserPermisoModulo('Configuracion')) {
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
              <?php if (Helpers::getUserPermisoModulo('Datos generales', 'Configuracion')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="negocio" class="nav-link" id="navDatosGeneralesI">
                    <i class="fas fa-building nav-icon text-orange" style="font-size: 14px"></i>
                    <p>Datos Generales</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Facturadores', 'Configuracion')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="empresas" class="nav-link" id="navFacturadores">
                    <i class="fas fa-file-alt nav-icon text-orange" style="font-size: 14px"></i>
                    <p>Facturadores</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Sucursales', 'Configuracion')): ?>
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
        if (Helpers::getUserPermisoModulo('Consultar compras')) {
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

              <?php if (Helpers::getUserPermisoModulo('Compras', 'Consulta compras')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="compras-fecha" class="nav-link" id="navConsultaComprasI">
                    <i class="fas fa-calendar-alt nav-icon text-info" style="font-size: 14px"></i>
                    <p>Compras</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Compras por proveedor', 'Consulta compras')): ?>
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
        if (Helpers::getUserPermisoModulo('Consultar ventas')) {
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

              <?php if (Helpers::getUserPermisoModulo('Ventas por cliente', 'Consultar ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="ventas-cliente" class="nav-link" id="navVentasCliente">
                    <i class="fas fa-user-tag nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Ventas por Cliente</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Ventas por vendedor', 'Consultar ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="ventas-vendedor" class="nav-link" id="navVentasVendedor">
                    <i class="fas fa-user-tie nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Ventas x Vendedor</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Ventas - utilidades', 'Consultar ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="ventas-producto" class="nav-link" id="navVentasProducto">
                    <i class="fas fa-chart-line nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Ventas - utilidades</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Creditos - utilidades', 'Consultar ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="ventas-credito" class="nav-link" id="navVentasCredito">
                    <i class="fas fa-credit-card nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Créditos - utilidades</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Reporte consolidado', 'Consultar ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="reporte" class="nav-link" id="navVentasCredito">
                    <i class="fas fa-credit-card nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Reporte consolidado</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Ventas por servicio', 'Consultar ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="ventas-servicio" class="nav-link" id="navVentasServicio">
                    <i class="fas fa-concierge-bell nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Ventas por servicio</p>
                  </a>
                </li>
              <?php endif; ?>

              <?php if (Helpers::getUserPermisoModulo('Ventas detalle', 'Consultar ventas')): ?>
                <li class="nav-item" style="font-size: 14px">
                  <a href="detalle-venta-comprobante" class="nav-link" id="navVentasDetalle">
                    <i class="fas fa-receipt nav-icon text-primary" style="font-size: 14px"></i>
                    <p>Ventas detalle</p>
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