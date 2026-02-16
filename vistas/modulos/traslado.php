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
  opacity: 1 !important; /* evita el gris tenue por defecto */
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
            <div class="card-header">
              <div class="row">
                <div class="col-md-2">
                  <button type="button" class="btn btn-outline-primary btn-block btn-xs" data-toggle="modal" data-target="#modalTraslado">
                    <i class="fa fa-plus"></i> Nuevo Traslado
                  </button>
                </div>
                <div>
                  <button class="btn btn-outline-warning btn-block btn-xs" data-toggle="modal" data-target="#modalSolicitud">
                    <i class="fa fa-plus"></i> Nueva Solicitud
                  </button>
                </div>
              </div>
            </div>

            <div class="card-body">
              <div class="row">

                                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <label>Fecha Inicio:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo date("Y-m-d"); ?>">
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
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?php echo date("Y-m-d"); ?>">
                                    </div>
                                </div>

                                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <label>Almacén:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-store-alt"></i>
                                            </span>
                                        </div>
                                        <select id="idsucursal2" name="idsucursal2" class="form-control select2">
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <label>Estado:</label>

                                    <div class="input-group">
                                        <select id="estado" name="estado" class="form-control select2">
                                            <option value="Todos">Todos</option>
                                            <option value="0">Pendiente</option>
                                            <option value="1">Aceptado</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
              <table id="tbllistado" class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Solicitud</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
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
<div class="modal fade" id="modalAprobarSolicitud" tabindex="-1" aria-labelledby="tituloSolicitudLabel" aria-hidden="true">
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

        <div class="mb-3">
          <label class="form-label fw-bold">Sucursal Solicitante:</label>
          <input type="text" id="sucursal_origen_solicitud" class="form-control" readonly>
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle" id="tablaProductosSolicitudTable">
            <thead class="table-primary">
              <tr>
                <th style="width:35%">Producto</th>
                <th style="width:15%">Cantidad</th>
                <th style="width:25%">Estado</th>
                <th style="width:25%">Observación</th>
              </tr>
            </thead>
            <tbody id="tablaProductosSolicitud"></tbody>
          </table>
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
              <input type="text" class="form-control" id="nombre_sucursal_origen" value="nombre_sucursal_origen" readonly>
            </div>

            <div class="col-md-6">
              <label>Almacén Destino</label>
              <select id="iddestino_solicitud" name="iddestino_solicitud" class="form-control" required></select>
            </div>
          </div>

          <hr>

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
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal" onclick="cancelarformS()" >Cancelar</button>
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
        <button type="submit" form="formTraslado" class="btn btn-success btn-sm" onclick="cancelarformT()">Guardar</button>
        <button type="button" class="btn btn-danger btn-sm"  data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal productos -->
<!-- Modal productos -->
<div class="modal fade" id="modalProductos" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5>Seleccionar Productos</h5>
      </div>
      <div class="modal-body">

        <div class="input-group mb-2">
          <input type="text" id="buscarProducto" class="form-control form-control-sm" placeholder="Buscar producto por nombre o código...">
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


<script src="vistas/js/traslado.js"></script>
