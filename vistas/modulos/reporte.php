<?php
date_default_timezone_set('America/Lima');
?>
<style type="text/css">
/* Estilo para los precios */
.precio-venta {
    color: #28a745;
    font-weight: bold;
}

.precio-compra {
    color: #dc3545;
    font-weight: bold;
}


strong {
    font-size: 16px;
    color: #000;
}

.barra-utilidad + span {
    margin-left: 5px;
    font-weight: bold;
}
/* TABLA MÁS PROFESIONAL */
#tbllistado {
    border-radius: 10px !important;
    overflow: hidden;
    font-family: "Roboto", sans-serif;
    font-size: 14px;
}

#tbllistado thead {
    background: #003d66;
    color: #fff;
    font-weight: bold;
}

#tbllistado tbody tr:hover {
    background: #eaf4ff !important;
}

/* FOOTER ESTÉTICO */
#tbllistado tfoot {
    background: #f5f5f5;
    font-weight: bold;
}

/* INPUT BUSCAR */
.dataTables_filter input {
    border-radius: 50px !important;
    padding: 6px 15px;
}

/* SELECT LÍMITES */
.dataTables_length select {
    border-radius: 5px;
}

/* BOTONES MEJORADOS */
.dt-buttons .btn {
    margin: 3px;
    font-weight: 600;
}

</style>
<script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<div class="content-wrapper">

    <!-- TITULO -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Reporte Consolidado</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Reporte Consolidado</li>
                    </ol>
                </div>

            </div>
        </div>
    </section>

    <!-- CONTENIDO PRINCIPAL -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filtros</h3>
                </div>

                <div class="card-body">

                    <!-- FILTROS -->
                    <div class="row">

                      <div class="form-group col-lg-2 col-md-3 col-sm-6">
                          <label>Fecha Inicio:</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text">
                                      <i class="far fa-calendar-alt"></i>
                                  </span>
                              </div>
                              <input type="date" class="form-control" id="fecha_inicio" value="<?php echo date('Y-m-d'); ?>">
                          </div>
                      </div>

                      <div class="form-group col-lg-2 col-md-3 col-sm-6">
                          <label>Fecha Fin:</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text">
                                      <i class="far fa-calendar-alt"></i>
                                  </span>
                              </div>
                              <input type="date" class="form-control" id="fecha_fin" value="<?php echo date('Y-m-d'); ?>">
                          </div>
                      </div>

                      <div class="form-group col-lg-3 col-md-3 col-sm-6">
                          <label>Almacén:</label>
                          <div class="input-group">
                              <div class="input-group-prepend">
                                  <span class="input-group-text">
                                      <i class="fas fa-store-alt"></i>
                                  </span>
                              </div>
                              <select id="idsucursal2" class="form-control"></select>
                          </div>
                      </div>

                      <!-- Botón para exportar a Excel -->
                      <div class="form-group col-lg-2 col-md-3 col-sm-6 align-self-end">
                          <button id="btnExportExcel" class="btn btn-success btn-block">
                              <i class="fas fa-file-excel"></i> Exportar Excel
                          </button>
                      </div>

                  </div>

                    <!-- TARJETAS DE RESUMEN -->
                    <div class="row mt-3">

                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="small-box" style="color: blue; font-weight: 900;">
                                <div class="inner">
                                    <p>Total Ingresos:</p>
                                    <h4 id="lblIngresos">0.00</h4>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill" style="font-size:20px !important"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="small-box" style="color: blue; font-weight: 900;">
                                <div class="inner">
                                    <p>Total Egresos:</p>
                                    <h4 id="lblEgresos">0.00</h4>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill" style="font-size:20px !important"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="small-box" style="color: blue; font-weight: 900;">
                                <div class="inner">
                                    <p>Total Compras:</p>
                                    <h4 id="lblCompraPV">0.00</h4>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill" style="font-size:20px !important"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="small-box" style="color: purple; font-weight: 900;">
                                <div class="inner">
                                    <p>Total Venta:</p>
                                    <h4 id="lblVentaPV">0.00</h4>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill" style="font-size:20px !important"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="small-box" style="color: red; font-weight: 900;">
                                <div class="inner">
                                    <p>Utilidad Neta:</p>
                                    <h4 id="lblUtilidadNetaPV">0.00</h4>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line" style="font-size:20px !important"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <hr>

                    <!-- DETALLE DE VENTAS -->
                    <h4 class="mt-4"><b>Detalle Consolidado</b></h4>
                    <table id="tblDetalleConsolidado" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Comprobante / Proveedor / Descripción</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>


                    <hr>

                    <!-- TABLA PRINCIPAL RESUMEN -->
                    <h4 class="mt-4"><b>Resumen por Mes</b></h4>

                    <table id="tbllistado" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Compras</th>
                                <th>Ventas</th>
                                <th>Ingresos</th>
                                <th>Egresos</th>
                                <th>Amortizaciones</th>
                                <th>Utilidad</th>                            
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th>Mes</th>
                                <th>Compras</th>
                                <th>Ventas</th>
                                <th>Ingresos</th>
                                <th>Egresos</th>
                                <th>Amortizaciones</th>
                                <th>Utilidad</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>

        </div>
    </section>
</div>

<!-- SCRIPT -->
<script src="vistas/js/reporte.js"></script>
