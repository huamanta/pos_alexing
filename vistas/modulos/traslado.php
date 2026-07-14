<?php
date_default_timezone_set('America/Lima');
?>
<style>
  /* Estilo visual para inputs desactivados */
  .readonly-input {
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
    cursor: not-allowed !important;
    color: #495057 !important;
    opacity: 1 !important;
    /* evita el gris tenue por defecto */
  }

  /* Modal de lectura con sutil diferencia */
  #modalAprobarSolicitud.readonly-mode .modal-content {
    background-color: #fdfefe;
    border: 2px solid #17a2b8;
  }

  #modalAprobarSolicitud.readonly-mode .modal-header {
    background-color: #17a2b8 !important;
    color: white !important;
  }

  #modalAprobarSolicitud.readonly-mode .modal-footer {
    display: none !important;
  }

  .bg-success.text-white {
    background-color: #28a745 !important;
    color: #fff !important;
  }

  .badge.bg-warning {
    background-color: #ffc107 !important;
    font-size: 0.7em;
  }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Traslados entre Almacenes</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Traslados</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <ul class="nav nav-tabs" id="tabsTraslados" role="tablist" style="margin-bottom: 10px;">
                    <li class="nav-item">
                      <a class="nav-link active" id="tab-basico-link" data-toggle="tab" href="#tab-solicitudes"
                        role="tab" aria-controls="tab-solicitudes" aria-selected="true">Solicitudes</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="tab-basico-link" data-toggle="tab" href="#tab-mis-solicitudes" role="tab"
                        aria-controls="tab-mis-solicitudes" aria-selected="true">Mis solicitudes</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="tab-stock-link" data-toggle="tab" href="#tab-traslados" role="tab"
                        aria-controls="tab-traslados" aria-selected="false">Traslados</a>
                    </li>
                  </ul>
                </div>
                <div class="tab-content p-2" id="tabsTrasladosContent">
                  <div class="tab-pane fade show active" id="tab-solicitudes" role="tabpanel"
                    aria-labelledby="tab-basico-link">
                    <div class="row">

                      <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 d-flex align-items-center">
                        <span class="mr-2">Mostrar</span>
                        <select id="limitSolicitudes" class="form-control" style="width:100px">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select>
                        <span class="ml-2">Registros</span>
                      </div>
                      <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <label>Sucursal origen:</label>
                        <div class="input-group">
                          <select id="origenSolicitudes" name="origenSolicitudes" class="form-control select2">
                            <option value="Todos">Todos</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <label>Estado:</label>
                        <div class="input-group">
                          <select id="estadoSolicitudes" name="estadoSolicitudes" class="form-control select2">
                            <option value="Todos">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="aceptado">Aceptado</option>
                            <option value="cancelado">Cancelado</option>
                            <option value="rechazado">Rechazado</option>
                            <option value="en_transito">En transito</option>
                            <option value="recibido">Recibido</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <input type="text" id="searchSolicitudes" class="form-control mt-7" placeholder="Buscar...">
                      </div>
                      <div class="col-md-12">
                        <div class="responsive">
                          <table id="tbllistado" class="table table-striped table-hover">
                            <thead>
                              <tr>
                                <th>Correlativo</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Solicitante</th>
                                <th>Fecha Solicitud</th>
                                <th>Fecha Aceptación</th>
                                <th>Usuario Acepta</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                              </tr>
                            </thead>
                            <tbody id="tbody_solicitudes"></tbody>
                          </table>
                        </div>
                      </div>
                      <div class="col-md-6"></div>
                      <div class="col-md-6">
                        <div id="paginationSolicitudes"></div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane fade show" id="tab-mis-solicitudes" role="tabpanel"
                    aria-labelledby="tab-basico-link">
                    <div class="row">
                      <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <br />
                        <button class="btn btn-outline-warning " data-toggle="modal" data-target="#modalSolicitud">
                          <i class="fa fa-plus"></i> Nueva Solicitud
                        </button>
                      </div>
                      <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <label>Fecha Inicio:</label>

                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="far fa-calendar-alt"></i>
                            </span>
                          </div>
                          <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                            value="<?php echo date("Y-m-d"); ?>">
                        </div>
                      </div>

                      <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <label>Fecha Fin:</label>

                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="far fa-calendar-alt"></i>
                            </span>
                          </div>
                          <input type="date" class="form-control" name="fecha_fin" id="fecha_fin"
                            value="<?php echo date("Y-m-d"); ?>">
                        </div>
                      </div>

                      <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <label>Estado:</label>

                        <div class="input-group">
                          <select id="estadoMisSolicitudes" name="estado" class="form-control select2">
                            <option value="Todos">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="aceptado">Aceptado</option>
                            <option value="cancelado">Cancelado</option>
                            <option value="rechazado">Rechazado</option>
                            <option value="en_transito">En transito</option>
                            <option value="recibido">Recibido</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6 d-flex align-items-center">
                        <span class="mr-2">Mostrar</span>
                        <select id="limitMisSolicitudes" class="form-control" style="width:100px">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select>

                        <span class="ml-2">Registros</span>

                      </div>
                      <div class="col-md-6">
                        <input type="text" id="searchMisSolicitudes" class="form-control" placeholder="Buscar...">
                      </div>
                      <div class="col-md-12 mt-3">
                        <table id="tbllistado" class="table table-striped">
                          <thead>
                            <tr>
                              <th>Correlativo</th>
                              <th>Origen</th>
                              <th>Destino</th>
                              <th>Solicitante</th>
                              <th>Fecha Solicitud</th>
                              <th>Fecha Aceptación</th>
                              <th>Usuario Acepta</th>
                              <th>Estado</th>
                              <th>Acciones</th>
                            </tr>
                          </thead>
                          <tbody id="tbody_mis_solicitudes"></tbody>
                        </table>
                      </div>
                      <div class="col-md-6"></div>
                      <div class="col-md-6">
                        <div id="paginationMisSolicitudes"></div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane fade show" id="tab-traslados" role="tabpanel" aria-labelledby="tab-basico-link">
                    <div class="row">
                      <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <button type="button" class="btn btn-outline-primary" data-toggle="modal"
                          data-target="#modalTraslado">
                          <i class="fa fa-plus"></i> Nuevo Traslado
                        </button>
                      </div>
                      <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12"></div>
                      <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <label>Estado:</label>

                        <div class="input-group">
                          <select id="estadoTraslados" name="estado" class="form-control select2">
                            <option value="Todos">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="aceptado">Aceptado</option>
                            <option value="cancelado">Cancelado</option>
                            <option value="rechazado">Rechazado</option>
                            <option value="en_transito">En transito</option>
                            <option value="recibido">Recibido</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6 d-flex align-items-center">
                        <span class="mr-2">Mostrar</span>
                        <select id="limitTraslados" class="form-control" style="width:100px">
                          <option value="10">10</option>
                          <option value="25">25</option>
                          <option value="50">50</option>
                          <option value="100">100</option>
                        </select>
                        <span class="ml-2">Registros</span>

                      </div>
                      <div class="col-md-6">
                        <input type="text" id="searchTraslados" class="form-control" placeholder="Buscar...">
                      </div>
                      <div class="col-md-12 mt-3">
                        <div class="responsive">
                          <table id="tbllistado" class="table table-striped">
                            <thead>
                              <tr>
                                <th>Correlativo</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Solicitante</th>
                                <th>Fecha Solicitud</th>
                                <th>Fecha Aceptación</th>
                                <th>Usuario Acepta</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                              </tr>
                            </thead>
                            <tbody id="tbody_traslados"></tbody>
                          </table>
                        </div>
                      </div>
                      <div class="col-md-6"></div>
                      <div class="col-md-6">
                        <div id="paginationTraslados"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </section>
</div>
<!-- =======================
 MODAL: APROBAR SOLICITUD
======================= -->
<div class="modal fade" id="modalAprobarSolicitud" tabindex="-1" aria-labelledby="tituloSolicitudLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-3">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="tituloSolicitudLabel">
          <i class="fa fa-check-circle"></i> Aprobar Solicitud de Traslado
        </h5>
        <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="idtraslado_solicitud">
        <input type="hidden" name="tipoTraslado" id="tipoTraslado">
        <div class="row">
          <div class="col-md-6">
            <label class="form-label fw-bold" id="labelSucursalOrigen">Sucursal origen:</label>
            <input type="text" id="sucursal_origen_solicitud" class="form-control" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold" id="labelSucursalDestino">Sucursal Destino:</label>
            <input type="text" id="sucursal_destino_solicitud" class="form-control" readonly>
          </div>
          <div class="col-md-12 mt-3">
            <div class="table-responsive">
              <table class="table table-sm table-striped align-middle" id="tablaProductosSolicitudTable">
                <thead class="table-primary">
                  <tr>
                    <th style="width:35%">Producto</th>
                    <th style="width:15%">Cantidad enviada</th>
                    <th style="width:15%">Cantidad recibida</th>
                    <th style="width:15%">Estado</th>
                    <th style="width:25%">Observación</th>
                  </tr>
                </thead>
                <tbody id="tablaProductosSolicitud"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Cerrar
        </button>
        <button type="button" class="btn btn-success" onclick="aprobarSolicitud()">
          <i class="fa fa-check"></i> Confirmar Aprobación
        </button>
      </div>

    </div>
  </div>
</div>

<!-- Modal Crear Solicitud -->
<div class="modal fade" id="modalSolicitud" tabindex="-1" aria-labelledby="modalSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalSolicitudLabel">Nueva Solicitud de Productos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="formSolicitud">
          <input type="hidden" id="idsolicitud" name="idsolicitud">
          <div class="row mb-2">
            <div class="col-md-6">
              <label>Almacén Origen</label>
              <input type="text" class="form-control" id="nombre_sucursal_origen" value="nombre_sucursal_origen"
                readonly>
            </div>

            <div class="col-md-6">
              <label>Almacén Destino</label>
              <select id="iddestino_solicitud" name="iddestino_solicitud" class="form-control" required></select>
            </div>
          </div>

          <button type="button" class="btn btn-info btn-sm" id="btnAgregarProductosSolicitud">
            <i class="fa fa-search"></i> Seleccionar Productos
          </button>

          <table class="table table-bordered table-sm mt-2" id="tablaDetalleSolicitud">
            <thead class="bg-light">
              <tr>
                <th>Producto</th>
                <th width="100px">Cantidad</th>
                <th>Quitar</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </form>
      </div>
      <div class="modal-footer">
        <button type="submit" form="formSolicitud" class="btn btn-success btn-sm">Enviar Solicitud</button>
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"
          onclick="cancelarformS()">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detalle de Productos -->
<div class="modal fade" id="modalDetalleProductos" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5>Productos Trasladados</h5>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-sm" id="tablaDetalleProductos">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Almacén Destino</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Traslado -->
<div class="modal fade" id="modalTraslado" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title">Nuevo Traslado</h4>
      </div>
      <div class="modal-body">
        <form id="formTraslado" method="POST">
          <input type="hidden" name="idtraslado" id="idtraslado">
          <input type="hidden" name="idorigen" id="idorigen" value="<?php echo $_SESSION['idsucursal']; ?>">

          <div class="row mb-2">
            <div class="col-md-6">
              <label>Almacén Origen</label>
              <input type="hidden" id="idorigen" name="idorigen" value="<?php echo $_SESSION['idsucursal']; ?>">
              <input type="text" id="nombre_origen" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label>Almacén Destino</label>
              <select id="iddestino" name="iddestino" class="form-control" required></select>
            </div>

          </div>

          <hr>

          <button type="button" class="btn btn-info btn-sm" id="btnAgregarProductos">
            <i class="fa fa-search"></i> Seleccionar Productos
          </button>

          <table class="table table-bordered table-sm mt-2" id="tablaDetalle">
            <thead class="bg-light">
              <tr>
                <th>Producto</th>
                <th width="100px">Cantidad</th>
                <th>Quitar</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </form>
      </div>

      <div class="modal-footer">
        <button type="submit" form="formTraslado" class="btn btn-success btn-sm"
          onclick="cancelarformT()">Guardar</button>
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal productos -->
<div class="modal fade" id="modalProductos" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5>Seleccionar Productos</h5>
      </div>
      <div class="modal-body">
        <input type="hidden" id="tipoModal">
        <div class="input-group mb-2">
          <input type="text" id="buscarProducto" class="form-control form-control-sm"
            placeholder="Buscar producto por nombre o código...">
          <div class="input-group-append">
            <button class="btn btn-primary btn-sm" id="btnBuscarProducto"><i class="fa fa-search"></i></button>
          </div>
        </div>

        <table class="table table-bordered table-sm" id="tablaProductos">
          <thead>
            <tr>
              <th></th>
              <th>Código</th>
              <th>Nombre</th>
              <th>Stock</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

        <div id="paginacionProductos" class="mt-2 text-center"></div>

      </div>
      <div class="modal-footer">
        <button id="btnAgregarSeleccionados" class="btn btn-primary btn-sm">Agregar</button>
        <button class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="vistas/js/pagination.js"></script>
<script src="vistas/js/traslado.js"></script>