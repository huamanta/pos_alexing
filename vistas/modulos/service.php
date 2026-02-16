<?php
date_default_timezone_set('America/Lima');
?>
<style type="text/css">
  <style>
.ticket {
    background: #fff;
    font-family: 'Courier New', Courier, monospace;
    padding: 20px;
    border-radius: 5px;
}

.ticket-header, .ticket-footer {
    text-align: center;
    margin-bottom: 10px;
}

.ticket-footer {
    font-size: 13px;
    color: gray;
}

@media print {
  body * {
    visibility: hidden;
  }
  .no-print {
    display: none !important;
  }
  #ticketContenido, #ticketContenido * {
    visibility: visible;
  }
  #ticketContenido {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
}
#modalServicio .modal-body {
  max-height: 70vh;
  overflow-y: auto;
}
/* Scroll modal ajustado */
#modalServicio .modal-body {
  max-height: 75vh;
  overflow-y: auto;
  padding: 20px;
  background-color: #f8f9fa;
}

/* Inputs y selects con estilo moderno */
#modalServicio .form-control {
  border-radius: 0.35rem;
  box-shadow: none;
  transition: border-color 0.3s, box-shadow 0.3s;
}
#modalServicio .form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

/* Secciones con fondo claro y sombra */
#modalServicio .form-group {
  background-color: #fff;
  padding: 15px;
  border-radius: 0.5rem;
  margin-bottom: 15px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Encabezado con degradado */
#modalServicio .modal-header {
  background: linear-gradient(90deg, #007bff, #00c6ff);
  color: #fff;
}

/* Botones */
#modalServicio .btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}
#modalServicio .btn-primary:hover {
  background-color: #0056b3;
}
#modalServicio .btn-success {
  background-color: #28a745;
  border-color: #28a745;
}
#modalServicio .btn-success:hover {
  background-color: #218838;
}

/* Tabla de servicios */
#tablaServicios {
  background-color: #fff;
  border-radius: 5px;
  overflow: hidden;
}
#tablaServicios th {
  background-color: #e9ecef;
  color: #495057;
}
#tablaServicios td, #tablaServicios th {
  vertical-align: middle;
}

/* Separadores por título */
#modalServicio label {
  font-weight: 600;
  color: #333;
}

/* Total con estilo moderno */
#total {
  font-weight: bold;
  text-align: right;
  background-color: #f1f3f5 !important;
  color: #000;
}

</style>


</style>
<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row align-items-center mb-3">
        <div class="col-md-6">
          <h1 class="mb-0">Servicio Técnico</h1>
        </div>
        <div class="col-md-6 text-md-right">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
            <li class="breadcrumb-item active">Servicio Técnico</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Content -->
  <section class="content">
    <div class="container-fluid">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title mb-0">Listado de Servicios</h3>
          <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" onclick="mostrarmodal()">
            <i class="fa fa-plus"></i> Nuevo Servicio
          </button>
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
                                        <select id="estadofiltro" name="estadofiltro" class="form-control select2">
                                            <option value="Todos">Todos</option>
                                            <option value="Recibido">Recibido</option>
                                            <option value="En proceso">En proceso</option>
                                            <option value="Terminado">Terminado</option>
                                            <option value="Entregado">Entregado</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                                
          <div class="table-responsive">
            <table id="tbllistado" class="table table-hover table-bordered">
              <thead class="thead-light">
                <tr>
                  <th>Comprobante</th>
                  <th>Cliente</th>
                  <th>Equipo</th>
                  <th>Técnico</th>
                  <th>Fecha Ingreso</th>
                  <th>Fecha Salida</th>
                  <th>Estado</th>
                  <th>Total</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody></tbody>
              <tfoot>
                <tr>
                  <th>Comprobante</th>
                  <th>Cliente</th>
                  <th>Equipo</th>
                  <th>Técnico</th>
                  <th>Fecha Ingreso</th>
                  <th>Fecha Salida</th>
                  <th>Estado</th>
                  <th>Total</th>
                  <th>Acciones</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal Servicio Técnico -->
<div class="modal fade" id="modalServicio">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <form id="formularioServicio" method="POST">
        
        <!-- Encabezado del Modal -->
        <div class="modal-header bg-gradient-primary">
          <h5 class="modal-title"><i class="fas fa-tools mr-2"></i>Agregar Servicio</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <!-- Cuerpo del Modal -->
        <div class="modal-body">
          <input type="hidden" name="idservicio" id="idservicio">

          <div class="row">
            <!-- Información de comprobante -->
            <div class="col-md-2 form-group">
              <label><i class="fas fa-map-marked-alt mr-1"></i>Almacén</label>
              <select id="idsucursal" name="idsucursal" class="form-control select2bs4" style="width: 100%;"></select>
            </div>

            <div class="col-md-2 form-group">
              <label><i class="fas fa-file-alt mr-1"></i>Comprobante</label>
              <select id="tipo_comprobante" name="tipo_comprobante" class="form-control select2bs4" required>
                <option value="Ticket">Ticket</option>
              </select>
            </div>

            <div class="col-md-2 form-group">
              <label><i class="fas fa-store-alt mr-1"></i>Serie</label>
              <input type="text" id="serie_comprobante" name="serie_comprobante" class="form-control bg-warning text-center" maxlength="7" readonly>
            </div>

            <div class="col-md-2 form-group">
              <label><i class="fas fa-file-alt mr-1"></i>N°</label>
              <input type="text" id="num_comprobante" name="num_comprobante" class="form-control bg-warning text-center" maxlength="10" readonly>
            </div>

            <div class="col-md-4 form-group">
              <label><i class="fas fa-users mr-1"></i>Cliente 
                <a class="ml-2 text-info" style="cursor:pointer;" data-toggle="modal" data-target="#ModalClientes">
                  <i class="fa fa-plus-circle"></i> Nuevo
                </a>
              </label>
              <select id="idcliente" name="idcliente" class="form-control select2bs4" required></select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 form-group">
              <label><i class="fas fa-laptop mr-1"></i>Equipo</label>
              <input type="text" class="form-control" name="equipo" id="equipo" required>
            </div>

            <div class="col-md-4 form-group">
              <label><i class="fas fa-user-cog mr-1"></i>Técnico</label>
              <select id="idtecnico" name="idtecnico" class="form-control select2bs4"></select>
            </div>

            <div class="col-md-4 form-group">
              <label><i class="far fa-calendar-check mr-1"></i>Fecha Ingreso</label>
              <input type="datetime-local" class="form-control" name="fecha_ingreso" id="fecha_ingreso" required>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 form-group">
              <label><i class="fas fa-exclamation-circle mr-1"></i>Descripción del Problema</label>
              <textarea class="form-control" name="descripcion_problema" id="descripcion_problema" rows="2"></textarea>
            </div>

            <div class="col-md-6 form-group">
              <label><i class="fas fa-check-circle mr-1"></i>Descripción de la Solución</label>
              <textarea class="form-control" name="descripcion_solucion" id="descripcion_solucion" rows="2"></textarea>
            </div>
          </div>

          <!-- Servicios -->
          <div class="form-group">
            <label><i class="fas fa-concierge-bell mr-1"></i>Servicios Realizados</label>
            <div class="table-responsive">
              <table class="table table-bordered table-sm text-center" id="tablaServicios">
                <thead class="thead-light">
                  <tr>
                    <th>Servicio</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Acción</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <button type="button" class="btn btn-success btn-sm" onclick="abrirModalProductos()">
                <i class="fa fa-plus-circle"></i> Agregar Servicio
              </button>
              <div class="input-group" style="width: 200px;">
                <div class="input-group-prepend">
                  <span class="input-group-text font-weight-bold">Total</span>
                </div>
                <input type="text" class="form-control bg-light" id="total" name="total" readonly>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 form-group">
              <label><i class="fas fa-info-circle mr-1"></i>Estado</label>
              <select class="form-control select2bs4" name="estado" id="estado">
                <option value="Recibido">Recibido</option>
                <option value="En proceso">En proceso</option>
                <option value="Terminado">Terminado</option>
                <option value="Entregado">Entregado</option>
              </select>
            </div>

            <div class="col-md-4 form-group">
              <label><i class="far fa-calendar-alt mr-1"></i>Fecha Reparación</label>
              <input type="datetime-local" class="form-control" name="fecha_reparacion" id="fecha_reparacion">
            </div>

            <div class="col-md-4 form-group">
              <label><i class="far fa-calendar-minus mr-1"></i>Fecha Salida</label>
              <input type="datetime-local" class="form-control" name="fecha_entrega" id="fecha_entrega">
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="cancelarform()">
            <i class="fas fa-times"></i> Cerrar
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal Producto -->
<div class="modal fade" id="modalProducto" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Seleccionar Servicios</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-hover" id="tablaProductos">
            <thead class="thead-light">
              <tr>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Ticket -->
<div id="modalTicket" class="modal fade" tabindex="-1" aria-labelledby="modalTicketLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="ticketContenido"> <!-- ID necesario para imprimir -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalTicketLabel">Ticket de Servicio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body ticket">
        <div class="ticket-header text-center">
          <h3 class="ticket-title">🧾 Servicio Técnico</h3>
          <span style="font-weight:bold" id="ticket-num-comprobante"></span>
        </div>

        <div class="ticket-body">
          <div class="row mb-2">
            <div class="col-md-7">
              <p><strong>Cliente:</strong> <span id="ticket-cliente"></span></p>
            </div>
            <div class="col-md-5">
              <p><strong>Fecha Ingreso:</strong> <span id="ticket-fecha-ingreso"></span></p>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-5">
              <p><strong>Equipo:</strong> <span id="ticket-equipo"></span></p>
            </div>
            <div class="col-md-4">
              <p><strong>Técnico:</strong> <span id="ticket-tecnico"></span></p>
            </div>
            <div class="col-md-3">
              <p><strong>Fecha Entrega:</strong> <span id="ticket-fecha-entrega"></span></p>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-4">
              <p><strong>Estado:</strong> <span id="ticket-estado"></span></p>
            </div>
            <div class="col-md-4">
              <p><strong>Problema:</strong> <span id="ticket-descripcion-problema"></span></p>
            </div>
            <div class="col-md-4">
              <p><strong>Solución:</strong> <span id="ticket-descripcion-solucion"></span></p>
            </div>
          </div>
          <hr>
          <h5 class="text-center">🛠 Servicios Realizados</h5>
          <table class="table table-bordered table-sm" style="font-size:14px;">
            <thead>
              <tr>
                <th>Servicio</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody id="ticket-detalle-servicios"></tbody>
          </table>

          <h5 class="text-end">Total: S/ <span id="ticket-total"></span></h5>
        </div>

        <div class="ticket-footer text-center">
          <p>Gracias por confiar en nosotros</p>
        </div>
      </div>

      <div class="modal-footer no-print">
        <button class="btn btn-success" onclick="imprimirTicket()">🖨 Imprimir</button>
        <button class="btn btn-secondary" onclick="cerrarmodalticket()">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ModalClientes">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="limpiarCliente()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" role="form" name="formularioClientes" id="formularioClientes" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Nombre:</label>
                                <input type="hidden" name="idpersona" id="idpersona">
                                <input type="hidden" name="tipo_persona" id="tipo_persona" value="Cliente">
                                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100" placeholder="Nombre del proveedor" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Tipo Documento:</label>
                                <select class="form-control select-picker" name="tipo_documento" id="tipo_documento" required>
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="CEDULA">CEDULA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="name" class="control-label">Número Documento:</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="num_documento" id="num_documento" maxlength="20" placeholder="Documento">
                                <div class="input-group-append">
                                    <span class="input-group-text" style="cursor: pointer;" id="Buscar_Cliente" onclick="BuscarCliente()" title="Buscar Cliente" type="button"><i class="fa fa-search"></i></span>
                                    <span class="input-group-text" id="cargando" title="Cargando" type="button" style="display: none;"><i><img src="files/plantilla/cargando.gif" width="15px"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Dirección:</label>
                                <input type="text" class="form-control" name="direccion" id="direccion" maxlength="70" placeholder="Dirección">
                                Estado:<label for="" id="estado2">-</label>
                                Condición:<label for="" id="condicion">-</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Teléfono:</label>
                                <input type="text" class="form-control" name="telefono" id="telefono" maxlength="20" placeholder="Teléfono">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Email:</label>
                                <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="Email">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" onclick="limpiarCliente()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" type="submit" id="">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>



<!-- JS -->
<script src="vistas/js/service.js"></script>
