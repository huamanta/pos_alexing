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
    --primary: #4f46e5;
    /* Indigo moderno */
    --secondary: #64748b;
    /* Slate grey */
    --success: #10b981;
    /* Emerald */
    --warning: #f59e0b;
    /* Amber */
    --danger: #ef4444;
    /* Red */
    --info: #06b6d4;
    /* Cyan */
    --bg-body: #f1f5f9;
    /* Fondo gris muy suave */
    --card-radius: 16px;
    /* Bordes redondeados modernos */
    --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    --card-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  }

  body,
  .content-wrapper {
    font-family: 'Poppins', sans-serif !important;
    background-color: var(--bg-body) !important;
    color: #334155;
  }

  /* Navbar Premium */
  #navbar-inicio {
    background-color: #ffffff;
    backdrop-filter: blur(10px);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 0.8rem 1rem;
  }

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
    color: #64748b;
    /* Texto secundario */
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
  }

  .tp-card-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1e293b;
    /* Texto oscuro */
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

  .tp-card:hover .tp-icon {
    transform: rotate(10deg) scale(1.1);
  }

  /* Colores de Iconos */
  .tp-indigo {
    background: rgba(79, 70, 229, 0.1);
    color: var(--primary);
  }

  .tp-red {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
  }

  .tp-green {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
  }

  .tp-yellow {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
  }

  .tp-gray {
    background: rgba(100, 116, 139, 0.1);
    color: var(--secondary);
  }

  .tp-card-footer {
    margin-top: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--primary);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
  }

  .tp-card-footer:hover {
    text-decoration: underline;
  }

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

  .card-title {
    font-weight: 600;
    font-size: 1.1rem;
    color: #334155;
  }

  .form-control,
  .input-group-text {
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

  .input-group-text {
    border-right: none;
    background: #fff;
    color: #94a3b8;
  }

  .input-group .form-control {
    border-left: none;
  }

  /* =========================================
           GRÁFICOS Y TARJETAS LIMPIAS
           ========================================= */
  .card-primary,
  .card-danger {
    background: #ffffff;
    border-radius: var(--card-radius);
    box-shadow: var(--card-shadow);
    border: none;
  }

  /* Eliminamos los fondos de colores sólidos antiguos */
  .card-primary:not(.card-outline)>.card-header,
  .card-danger:not(.card-outline)>.card-header {
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
    display: flex;
    align-items: center;
  }

  .user-profile-link:hover {
    background: #e2e8f0;
  }

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
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    top: 0px;
    right: 0px;
  }
</style>

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
      <?php
      $puedeVerPaneles = $helpers->getUserPermissionAccion('Ver paneles');
      $puedeVerGraficos = $helpers->getUserPermissionAccion('Ver graficos');
      ?>

      <?php if (!$puedeVerPaneles && !$puedeVerGraficos) { ?>
        <div class="row justify-content-center">
          <div class="col-xl-9 col-lg-10">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
              <div class="card-body p-0">
                <div class="row no-gutters">
                  <div class="col-md-4 d-flex align-items-center justify-content-center" style=" background: linear-gradient(145deg, #4f46e5, #6366f1); min-height: 300px; ">
                    <div class="text-center text-white px-4">
                      <div class="mb-4" style=" width: 82px; height: 82px; margin: 0 auto; border-radius: 22px; background: rgba(255,255,255,.14); display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0,0,0,.12); "> <i class="fas fa-layer-group" style="font-size: 2rem;"></i> </div>
                      <h4 class="font-weight-bold mb-2"> Panel principal </h4>
                      <p class="mb-0" style=" color: rgba(255,255,255,.78); font-size: .9rem; line-height: 1.6; "> Gestiona las opciones disponibles desde el menú de navegación. </p>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="p-4 p-lg-5">
                      <div class="d-flex align-items-center mb-4">
                        <div style=" width: 46px; height: 46px; border-radius: 12px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; margin-right: 14px; "> <i class="fas fa-hand-sparkles"></i> </div>
                        <div>
                          <div class="text-muted" style="font-size: .8rem;"> ACCESO AL SISTEMA </div>
                          <h3 class="font-weight-bold text-dark mb-0"> ¡Bienvenido! </h3>
                        </div>
                      </div>
                      <p class="text-dark mb-2" style=" font-size: 1rem; line-height: 1.6; "> Has iniciado sesión correctamente. </p>
                      <p class="text-muted mb-4" style=" font-size: .9rem; line-height: 1.6; "> Desde el menú lateral podrás acceder a los módulos y funcionalidades habilitados para tu usuario. </p>
                      <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                          <div style=" width: 34px; height: 34px; border-radius: 9px; background: #f8fafc; color: #64748b; display: flex; align-items: center; justify-content: center; margin-right: 12px; "> <i class="fas fa-bars"></i> </div>
                          <div>
                            <div class="font-weight-bold text-dark" style="font-size: .88rem;"> Menú de navegación </div>
                            <div class="text-muted" style="font-size: .78rem;"> Consulta las opciones disponibles para tu perfil. </div>
                          </div>
                        </div>
                        <div class="d-flex align-items-center">
                          <div style=" width: 34px; height: 34px; border-radius: 9px; background: #f8fafc; color: #64748b; display: flex; align-items: center; justify-content: center; margin-right: 12px; "> <i class="fas fa-shield-alt"></i> </div>
                          <div>
                            <div class="font-weight-bold text-dark" style="font-size: .88rem;"> Acceso controlado </div>
                            <div class="text-muted" style="font-size: .78rem;"> Las opciones visibles dependen de los permisos asignados. </div>
                          </div>
                        </div>
                      </div>
                      <div class="pt-3" style=" border-top: 1px solid #edf0f5; "> <span class="text-muted" style="font-size: .75rem;"> <i class="fas fa-info-circle mr-1"></i> Selecciona una opción del menú para comenzar. </span> </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      <?php } else { ?>
        <?php if ($puedeVerPaneles || $puedeVerGraficos) { ?>
          <div class="row">
            <div class="col-lg-12">
              <div class="card card-default">
                <div class="card-header d-flex align-items-center">
                  <h3 class="card-title"><i class="fas fa-filter mr-2 text-primary"></i> Filtros de Resumen</h3>
                  <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="form-group col-lg-3 col-md-3 col-sm-6">
                      <label class="small text-uppercase font-weight-bold text-muted">Fecha Inicio</label>
                      <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                        value="<?php echo date("Y-m-01"); ?>">
                    </div>

                    <div class="form-group col-lg-3 col-md-3 col-sm-6">
                      <label class="small text-uppercase font-weight-bold text-muted">Fecha Fin</label>
                      <input type="date" class="form-control" name="fecha_fin" id="fecha_fin"
                        value="<?php echo date("Y-m-d"); ?>">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
        <?php if ($puedeVerPaneles) { ?>
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
              <div class="card border-0 shadow-lg text-white"
                style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); border-radius: 16px;">
                <div
                  class="card-body p-4 d-flex align-items-center justify-content-between position-relative overflow-hidden">
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
              <div class="card border-0 shadow-lg text-white"
                style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); border-radius: 16px;">
                <div
                  class="card-body p-4 d-flex align-items-center justify-content-between position-relative overflow-hidden">
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
        <?php } ?>

        <?php if ($puedeVerGraficos) { ?>
          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="card card-primary h-100">
                <div class="card-header d-flex justify-content-between align-items-center pt-3 pb-2">
                  <h3 class="card-title font-weight-bold"><i class="fas fa-chart-line mr-2 text-primary"></i> Utilidades (12
                    Meses)</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
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
                  <h3 class="card-title font-weight-bold"><i class="fas fa-exchange-alt mr-2 text-success"></i> Flujo
                    Efectivo</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart">
                    <canvas id="graficoIngresosEgresos"
                      style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-4">
              <div class="card card-primary h-100">
                <div class="card-header d-flex justify-content-between align-items-center pt-3 pb-2">
                  <h3 class="card-title font-weight-bold"><i class="fas fa-chart-area mr-2 text-info"></i> Ventas vs Compras
                  </h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
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
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                        class="fas fa-minus"></i></button>
                  </div>
                </div>
                <div class="card-body">
                  <div id="productosmasvendido2" style="min-height: 250px; height: 250px; max-width: 100%;"></div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      <?php } ?>
    </div>
  </section>
</div>

<script src="vistas/js/inicio.js"></script>
<script src="./files/plugins/apexcharts/apexcharts.min.js"></script>
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', async () => {
    try {
      const [ventasResponse, comprasResponse] = await Promise.all([
        fetch("controladores/consultas.php?op=totalVentas"),
        fetch("controladores/consultas.php?op=totalCompras")
      ]);

      const ventasData = await ventasResponse.json();
      const comprasData = await comprasResponse.json();

      const periodos = [
        ...new Set([
          ...ventasData.map(item => item.fecha),
          ...comprasData.map(item => item.fecha)
        ])
      ];

      const ventas = Object.fromEntries(
        ventasData.map(item => [item.fecha, item])
      );

      const compras = Object.fromEntries(
        comprasData.map(item => [item.fecha, item])
      );

      const montosVentas = periodos.map(fecha =>
        parseFloat(ventas[fecha]?.total || 0)
      );

      const montosCompras = periodos.map(fecha =>
        parseFloat(compras[fecha]?.total || 0)
      );

      crearGrafico(
        periodos,
        montosVentas,
        montosCompras,
        ventas,
        compras
      );
    } catch (error) {
      console.error("Error al cargar los datos:", error);
    }
  });

  function crearGrafico(
    periodos,
    montosVentas,
    montosCompras,
    ventas,
    compras
  ) {
    const options = {
      chart: {
        type: 'bar',
        height: 350,
        toolbar: {
          show: false
        },
        fontFamily: 'Poppins, sans-serif'
      },

      series: [{
          name: 'Ventas',
          data: montosVentas
        },
        {
          name: 'Compras',
          data: montosCompras
        }
      ],

      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '55%',
          borderRadius: 4
        }
      },

      dataLabels: {
        enabled: false
      },

      xaxis: {
        categories: periodos,
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        }
      },

      yaxis: {
        labels: {
          formatter: value => value
        }
      },

      tooltip: {
        custom: function({
          seriesIndex,
          dataPointIndex
        }) {
          const fecha = periodos[dataPointIndex];

          const item = seriesIndex === 0 ?
            ventas[fecha] :
            compras[fecha];

          return `
                    <div class="px-3 py-2">
                        <strong>${fecha}</strong>
                        <div class="mt-1">
                            ${item?.total_str || 'S/ 0.00'}
                        </div>
                    </div>
                `;
        }
      },

      colors: ['#4f46e5', '#f59e0b'],

      legend: {
        position: 'top',
        horizontalAlign: 'left'
      },

      grid: {
        borderColor: '#f1f5f9'
      }
    };

    new ApexCharts(
      document.querySelector("#areaChart"),
      options
    ).render();
  }


  $("#cxcAlertLink").on("click", function() {

    let ids = $(this).data("ids");
    if (!ids) return;

    $.post(
      "controladores/cuentascobrar.php?op=marcar_leida", {
        ids: ids
      },
      function() {
        $("#cxcAlertCount").hide();
      }
    );
  });
</script>