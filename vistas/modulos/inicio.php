<?php
date_default_timezone_set('America/Lima');
// Asegúrate de iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
    
    
    <style type="text/css">
        /* =========================================
           VARIABLES Y CONFIGURACIÓN GLOBAL
           ========================================= */
        :root {
            --primary: #4f46e5;      /* Indigo moderno */
            --secondary: #64748b;    /* Slate grey */
            --success: #10b981;      /* Emerald */
            --warning: #f59e0b;      /* Amber */
            --danger: #ef4444;       /* Red */
            --info: #06b6d4;         /* Cyan */
            --bg-body: #f1f5f9;      /* Fondo gris muy suave */
            --card-radius: 16px;     /* Bordes redondeados modernos */
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --card-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body, .content-wrapper {
            font-family: 'Poppins', sans-serif !important;
            background-color: var(--bg-body) !important;
            color: #334155;
        }

        /* Navbar Premium */
        #navbar-inicio {
            background-color: #ffffff;
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 0.8rem 1rem;
        }

        #navbar-global { display: none !important; }

        /* =========================================
           TARJETAS KPI (tp-card) REDISEÑADAS
           ========================================= */
        .tp-card {
            background: #ffffff;
            border-radius: var(--card-radius);
            padding: 24px;
            position: relative;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: 1px solid #f1f5f9;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .tp-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover);
        }

        .tp-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .tp-card-title {
            font-size: 0.85rem;
            font-weight: 500;
            color: #64748b; /* Texto secundario */
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .tp-card-value {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1e293b; /* Texto oscuro */
            line-height: 1.2;
        }

        /* Iconos con fondo suave (Bubble effect) */
        .tp-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .tp-card:hover .tp-icon { transform: rotate(10deg) scale(1.1); }

        /* Colores de Iconos */
        .tp-indigo { background: rgba(79, 70, 229, 0.1); color: var(--primary); }
        .tp-red    { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .tp-green  { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .tp-yellow { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .tp-gray   { background: rgba(100, 116, 139, 0.1); color: var(--secondary); }

        .tp-card-footer {
            margin-top: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--primary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
        }
        .tp-card-footer:hover { text-decoration: underline; }

        /* =========================================
           FILTROS Y INPUTS MODERNOS
           ========================================= */
        .card-default {
            background: #ffffff;
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            border: none;
            margin-bottom: 25px;
        }

        .card-default .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 20px 25px;
        }

        .card-title { font-weight: 600; font-size: 1.1rem; color: #334155; }

        .form-control, .input-group-text {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            font-size: 0.9rem;
            background-color: #f8fafc;
        }
        
        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .input-group-text { border-right: none; background: #fff; color: #94a3b8; }
        .input-group .form-control { border-left: none; }

        /* =========================================
           GRÁFICOS Y TARJETAS LIMPIAS
           ========================================= */
        .card-primary, .card-danger {
            background: #ffffff;
            border-radius: var(--card-radius);
            box-shadow: var(--card-shadow);
            border: none;
        }
        
        /* Eliminamos los fondos de colores sólidos antiguos */
        .card-primary:not(.card-outline) > .card-header,
        .card-danger:not(.card-outline) > .card-header {
            background-color: transparent;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        /* =========================================
           TARJETAS DE GRADIENTE (Categorias/Productos)
           ========================================= */
        .bg-gradient-to-r {
            border-radius: var(--card-radius);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        /* Ajustes Navbar Perfil */
        .user-profile-link {
            padding: 6px 15px !important;
            border-radius: 30px;
            background: #f1f5f9;
            transition: all 0.2s;
            display: flex; align-items: center;
        }
        .user-profile-link:hover { background: #e2e8f0; }
        
        .user-avatar-circle {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }
        
        /* Badges */
        .badge-notify {
            padding: 4px 6px;
            border-radius: 50%;
            font-size: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            top: 0px; right: 0px;
        }

    </style>

<nav class="main-header navbar navbar-expand navbar-white navbar-light sticky-top" id="navbar-inicio">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link text-secondary" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars fa-lg"></i></a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto align-items-center">
    
    <li class="nav-item">
      <a class="nav-link text-secondary" data-widget="fullscreen" href="#" role="button" title="Pantalla Completa">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>

    <li class="nav-item dropdown mr-2" id="stockbajito">
      <a class="nav-link position-relative text-secondary" data-toggle="dropdown" href="#" id="stockAlertLink">
          <i class="fas fa-bell fa-lg"></i>
          <span class="badge badge-danger badge-notify" id="stockAlertCount">0</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-lg border-0 rounded-lg mt-2">
        <div class="dropdown-header font-weight-bold text-danger bg-light rounded-top py-3">
            <i class="fas fa-exclamation-triangle mr-2"></i> Stock Bajo
        </div>
        <div id="stockAlertTable" class="dropdown-item p-0">
          <table class="table table-sm table-hover mb-0">
            <thead class="bg-white text-muted">
              <tr><th class="pl-3 border-0">Producto</th><th class="text-center border-0">Stock</th></tr>
            </thead>
            <tbody id="stockAlertTableBody"></tbody>
          </table>
        </div>
      </div>
    </li>

    <li class="nav-item dropdown mr-3">
        <a class="nav-link position-relative text-secondary" data-toggle="dropdown" href="#" id="cxcAlertLink">
            <i class="fas fa-file-invoice-dollar fa-lg"></i>
            <span class="badge badge-warning text-white badge-notify" id="cxcAlertCount" style="display:none;">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-lg border-0 rounded-lg mt-2">
            <span class="dropdown-header font-weight-bold bg-light rounded-top py-3">Cuentas por Cobrar</span>
            <div class="dropdown-item p-3">
                <div id="cxcAlertList" style="max-height:300px; overflow-y:auto;"></div>
            </div>
            <div class="dropdown-divider m-0"></div>
            <a href="cuentas-cobrar" class="dropdown-item dropdown-footer text-primary font-weight-bold py-3">
                Ver todas las cuentas <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </li>

    <div class="navbar-divider d-none d-sm-block" style="border-right: 1px solid #e2e8f0; height: 30px; margin: 0 10px;"></div>

    <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link user-profile-link" data-toggle="dropdown">
            <div class="user-avatar-circle shadow-sm">
                 <?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?>
            </div>
            <div class="d-none d-md-block ml-2 text-left" style="line-height: 1.2;">
                <span class="d-block font-weight-bold text-dark" style="font-size: 0.85rem;">
                    <?php echo explode(" ", $_SESSION['nombre'])[0]; ?>
                </span>
                <span class="d-block text-muted" style="font-size: 0.7rem;">
                    <?php echo isset($_SESSION['nombre_negocio']) ? $_SESSION['nombre_negocio'] : 'Admin'; ?>
                </span>
            </div>
            <i class="fas fa-chevron-down text-muted ml-2" style="font-size: 0.7rem;"></i>
        </a>

        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-0 shadow-xl mt-3 rounded-lg overflow-hidden">
            <div class="bg-primary p-4 text-center text-white">
                <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 60px; height: 60px; font-size:1.5rem; font-weight:bold;">
                     <?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?>
                </div>
                <h6 class="mb-0 font-weight-bold"><?php echo $_SESSION['nombre']; ?></h6>
                <small class="text-white-50"><?php echo $_SESSION['cargo']; ?></small>
            </div>
            <div class="p-2 bg-white">
                <a href="salir" class="btn btn-outline-danger btn-block font-weight-bold border-0 text-left px-3 py-2">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </li>
  </ul>
</nav>

<div class="content-wrapper">
  
  <div class="content-header pb-1">
    <div class="container-fluid">
      <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
          <h1 class="m-0 font-weight-bold text-dark" style="letter-spacing: -0.5px;">Dashboard</h1>
          <p class="text-muted m-0 small">Vista general de tu negocio</p>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
            <li class="breadcrumb-item"><a href="#" class="text-primary font-weight-bold">Inicio</a></li>
            <li class="breadcrumb-item active text-muted">Panel</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-lg-12">
          <div class="card card-default">
            <div class="card-header d-flex align-items-center">
              <h3 class="card-title"><i class="fas fa-filter mr-2 text-primary"></i> Filtros de Resumen</h3>
              <div class="card-tools ml-auto">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="form-group col-lg-3 col-md-3 col-sm-6">
                  <label class="small text-uppercase font-weight-bold text-muted">Fecha Inicio</label>
                  <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo date("Y-m-d"); ?>">
                </div>

                <div class="form-group col-lg-3 col-md-3 col-sm-6">
                  <label class="small text-uppercase font-weight-bold text-muted">Fecha Fin</label>
                  <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?php echo date("Y-m-d"); ?>">
                </div>

                <div class="form-group col-lg-3 col-md-3 col-sm-6">
                  <label class="small text-uppercase font-weight-bold text-muted">Almacén</label>
                  <select id="idsucursal2" name="idsucursal2" class="form-control"></select>
                </div>
                
                <div class="form-group col-lg-3 col-md-3 col-sm-6">
                  <label class="small text-uppercase font-weight-bold text-muted">Vendedor</label>
                  <div class="input-group">
                    <select id="idcliente" name="idcliente" class="form-control select2" required></select>
                    <div class="input-group-append ml-2">
                      <button type="button" class="btn btn-primary shadow-sm px-3" style="border-radius:10px" onclick="mostrarInicio()">
                          <i class="fas fa-search"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="tp-card">
            <div class="tp-card-header">
                <div>
                  <div class="tp-card-title">Compras Hoy</div>
                  <div class="tp-card-value" id="lblComprasHoy">S/ 0.00</div>
                </div>
                <div class="tp-icon tp-indigo"><i class="fas fa-shopping-bag"></i></div>
            </div>
            <div class="tp-card-footer">Ver detalle <i class="fas fa-chevron-right ml-1 small"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
          <div class="tp-card">
            <div class="tp-card-header">
                <div>
                  <div class="tp-card-title text-danger">Por Pagar</div>
                  <div class="tp-card-value text-danger" id="lblCuentasPagar">S/ 0.00</div>
                </div>
                <div class="tp-icon tp-red"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
            <div class="tp-card-footer text-danger">Gestionar <i class="fas fa-chevron-right ml-1 small"></i></div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
          <div class="tp-card">
            <div class="tp-card-header">
                <div>
                  <div class="tp-card-title text-success">Ventas Contado</div>
                  <div class="tp-card-value text-success" id="lblVentasHoy">S/ 0.00</div>
                </div>
                <div class="tp-icon tp-green"><i class="fas fa-cash-register"></i></div>
            </div>
            <div class="tp-card-footer text-success">Ver arqueo <i class="fas fa-chevron-right ml-1 small"></i></div>
          </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="tp-card">
            <div class="tp-card-header">
                <div>
                  <div class="tp-card-title text-primary">Ventas Crédito</div>
                  <div class="tp-card-value text-primary" id="lblTotalVentasC">S/ 0.00</div>
                </div>
                <div class="tp-icon tp-indigo"><i class="fas fa-credit-card"></i></div>
            </div>
            <div class="tp-card-footer">Ver reporte <i class="fas fa-chevron-right ml-1 small"></i></div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="tp-card border-left-warning" style="border-left: 4px solid #f59e0b;">
            <div class="tp-card-header">
                <div>
                  <div class="tp-card-title">Por Cobrar</div>
                  <div class="tp-card-value" id="lblCuentasCobrar">S/ 0.00</div>
                </div>
                <div class="tp-icon tp-yellow"><i class="fas fa-hand-holding-usd"></i></div>
            </div>
            <div class="tp-card-footer text-warning">Cobrar ahora <i class="fas fa-arrow-right ml-1"></i></div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
          <div class="tp-card">
            <div class="tp-card-header">
                <div>
                  <div class="tp-card-title">Empleados</div>
                  <div class="tp-card-value" id="lblEmpleados">0</div>
                </div>
                <div class="tp-icon tp-gray"><i class="fas fa-user-friends"></i></div>
            </div>
            <div class="tp-card-footer text-secondary">Gestionar <i class="fas fa-arrow-right ml-1"></i></div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
          <div class="tp-card">
            <div class="tp-card-header">
                <div>
                  <div class="tp-card-title">Proveedores</div>
                  <div class="tp-card-value" id="lblProveedores">0</div>
                </div>
                <div class="tp-icon tp-indigo"><i class="fas fa-truck"></i></div>
            </div>
            <div class="tp-card-footer">Ver lista <i class="fas fa-arrow-right ml-1"></i></div>
          </div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
          <div class="card border-0 shadow-lg text-white" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); border-radius: 16px;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between position-relative overflow-hidden">
                <div style="z-index: 2;">
                    <h6 class="text-white-50 font-weight-bold text-uppercase mb-1">Total Categorías</h6>
                    <h2 class="font-weight-bold m-0" id="lblCategorias">0</h2>
                </div>
                <div style="font-size: 4rem; opacity: 0.2; position: absolute; right: 20px; top: 10px;">
                    <i class="far fa-clipboard"></i>
                </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card border-0 shadow-lg text-white" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); border-radius: 16px;">
            <div class="card-body p-4 d-flex align-items-center justify-content-between position-relative overflow-hidden">
                <div style="z-index: 2;">
                    <h6 class="text-white-50 font-weight-bold text-uppercase mb-1">Total Productos</h6>
                    <h2 class="font-weight-bold m-0" id="lblProductos">0</h2>
                </div>
                <div style="font-size: 4rem; opacity: 0.2; position: absolute; right: 20px; top: 10px;">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card card-primary h-100">
              <div class="card-header d-flex justify-content-between align-items-center pt-3 pb-2">
                <h3 class="card-title font-weight-bold"><i class="fas fa-chart-line mr-2 text-primary"></i> Utilidades (12 Meses)</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="uti12m" style="min-height: 250px; height: 250px; max-height: 300px; width: 100%;"></canvas>
                </div>
              </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card card-primary h-100">
              <div class="card-header d-flex justify-content-between align-items-center pt-3 pb-2">
                <h3 class="card-title font-weight-bold"><i class="fas fa-exchange-alt mr-2 text-success"></i> Flujo Efectivo</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="graficoIngresosEgresos" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
            </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card card-primary h-100">
              <div class="card-header d-flex justify-content-between align-items-center pt-3 pb-2">
                <h3 class="card-title font-weight-bold"><i class="fas fa-chart-area mr-2 text-info"></i> Ventas vs Compras</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <div id="areaChart" style="min-height: 250px; height: 250px; max-width: 100%;"></div>
                </div>
              </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card card-danger h-100">
                <div class="card-header d-flex justify-content-between align-items-center pt-3 pb-2">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-crown mr-2 text-warning"></i> Top Productos</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="productosmasvendido2" style="min-height: 250px; height: 250px; max-width: 100%;"></div>
                </div>
            </div>
        </div>
      </div>
      
    </div>
  </section>
</div>

<script src="vistas/js/inicio.js"></script>
<script src="./files/plugins/apexcharts/apexcharts.min.js"></script>
<script type="text/javascript">

document.addEventListener('DOMContentLoaded', async () => {
    try {
        // Obtener ventas
        const ventasResponse = await fetch("controladores/consultas.php?op=totalVentas");
        const ventasData = await ventasResponse.json();
        const periodos = ventasData.map(item => item[0]);
        const montosVentas = ventasData.map(item => parseFloat(item[1] || 0));

        // Obtener compras
        const comprasResponse = await fetch("controladores/consultas.php?op=totalCompras");
        const comprasData = await comprasResponse.json();
        const montosCompras = comprasData.map(item => parseFloat(item[1] || 0));

        crearGrafico(periodos, montosVentas, montosCompras);
    } catch (error) {
        console.error("Error al cargar los datos:", error);
    }

    function crearGrafico(periodos, montosVentas, montosCompras) {
        const options = {
            chart: {
                type: 'bar',
                height: 350, // Ajuste altura
                toolbar: { show: false }, // Ocultar toolbar para diseño limpio
                fontFamily: 'Poppins, sans-serif' // Fuente consistente
            },
            series: [
                {
                    name: 'Ventas en S/',
                    data: montosVentas
                },
                {
                    name: 'Compras en S/',
                    data: montosCompras
                }
            ],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    borderRadius: 4, // Bordes redondeados en barras
                }
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: { 
                categories: periodos,
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { title: { text: '' } }, // Limpieza eje Y
            fill: { opacity: 1 },
            tooltip: {
                y: {
                    formatter: val => `S/ ${val.toLocaleString()}`
                },
                theme: 'light' // Tooltip claro
            },
            colors: ['#4f46e5', '#f59e0b'], // Colores Premium (Indigo y Amber)
            legend: { position: 'top', horizontalAlign: 'left' },
            grid: { borderColor: '#f1f5f9' } // Grid suave
        };

        const chart = new ApexCharts(document.querySelector("#areaChart"), options);
        chart.render();
    }
});

const CURRENT_SUCURSAL = <?= $_SESSION['idsucursal'] ?? 0 ?>;
function getSucursalNavbar() {
    let id = $("#idsucursal2").val();
    return id && id !== "" ? id : null;
}

function cargarNotificacionesCXCNavbar() {

    let currentSucursal = getSucursalNavbar();
    if (!currentSucursal) return; 

    $.getJSON(
        "controladores/cuentascobrar.php?op=obtener_notificaciones&idsucursal=" + currentSucursal,
        function (data) {

            let cuotas = data.filter(n => !n.tipo || n.tipo.trim() === "");

            let total = cuotas.length;
            let html = "";
            let ids = [];

            if (total === 0) {
                $("#cxcAlertCount").hide();
                html = `
                    <span class="dropdown-item text-muted text-center py-3">
                        <i class="far fa-check-circle mb-1 d-block text-success fa-2x"></i>
                        No hay cuentas vencidas
                    </span>`;
            } else {
                $("#cxcAlertCount").text(total).show();

                cuotas.forEach(n => {
                    ids.push(n.idnotificacion);

                    html += `
                        <a href="#" class="dropdown-item px-3 py-2">
                            <div class="media">
                                <div class="mr-3">
                                    <span class="btn btn-sm btn-light text-danger rounded-circle"><i class="fas fa-exclamation"></i></span>
                                </div>
                                <div class="media-body">
                                    <p class="mb-0 text-sm font-weight-bold text-dark">${n.mensaje}</p>
                                    <p class="text-sm text-muted mb-0"><i class="far fa-clock mr-1"></i> ${n.fecha}</p>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>`;
                });
            }

            $("#cxcAlertList").html(html);
            $("#cxcAlertLink").data("ids", ids.join(","));
        }
    );
}

$(document).on("change", "#idsucursal2", function () {
    cargarNotificacionesCXCNavbar(); 
});

$("#cxcAlertLink").on("click", function () {

    let ids = $(this).data("ids");
    if (!ids) return;

    $.post(
        "controladores/cuentascobrar.php?op=marcar_leida",
        { ids: ids },
        function () {
            $("#cxcAlertCount").hide();
        }
    );
});

$(document).ready(function () {

    // Esperar a que idsucursal2 tenga valor
    let esperaSucursal = setInterval(function () {
        if (getSucursalNavbar()) {
            cargarNotificacionesCXCNavbar();
            clearInterval(esperaSucursal);
        }
    }, 300);

    // Refresco normal cada 5 segundos
    setInterval(cargarNotificacionesCXCNavbar, 5000);
});

</script>