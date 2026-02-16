<?php
date_default_timezone_set('America/Lima');
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Asistencias</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Control de asistencias</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <!-- /.card-header -->
            <div class="card-body">
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Registrar Asistencia</button>
                </li>
                <li class="nav-item" role="presentation" onclick="historial()">
                  <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Historial de Asistencias</button>
                </li>
              </ul>
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                  <table id="tbllistado" class="table table-striped">
                    <thead>
                      <tr>
                        <th style="width: 400px;">Nombre</th>
                        <th>Documento</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Foto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th style="width: 400px;">Nombre</th>
                        <th>Documento</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Foto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                  <div class="card-header">
                    <div class="row align-items-end">
                      <!-- Fecha Inicio -->
                      <div class="form-group col-md-4">
                        <label for="fecha_inicio" class="font-weight-bold">
                          <i class="far fa-calendar-alt mr-1"></i> Fecha Inicio
                        </label>
                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo date('Y-m-d'); ?>">
                      </div>

                      <!-- Fecha Fin -->
                      <div class="form-group col-md-4">
                        <label for="fecha_fin" class="font-weight-bold">
                          <i class="far fa-calendar-alt mr-1"></i> Fecha Fin
                        </label>
                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?php echo date('Y-m-d'); ?>">
                      </div>

                      <!-- Botón Resumen -->
                      <div class="form-group col-md-4 text-md-left text-center">
                        <button class="btn btn-info btn-block" onclick="mostrarResumenAsistencia()">
                          <i class="fas fa-chart-pie mr-1"></i> Ver Resumen
                        </button>
                      </div>
                    </div>
                  
                  <!-- Botón para eliminar seleccionados -->
                  <div class="mb-3">
                    <button class="btn btn-danger" id="btnEliminarSeleccionados" style="display: none;">
                      <i class="fas fa-trash-alt mr-1"></i> Eliminar Seleccionados
                    </button>
                  </div>

                  <table id="tbllistado2" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllAsistencias"></th>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th>Hora Entrada</th>
                            <th>Hora Salida</th>
                            <th>Horas Trabajadas</th>
                            <th>Tardanza</th>
                            <th>Estado</th> <!-- Esta es la columna con colores -->
                            <th>Permiso</th>
                            <th>Vacaciones</th>
                            <th>Costo</th>
                            <th>Retraso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se llenarán las filas con los datos -->
                    </tbody>
                </table>
                </div>
              
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->

        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>

<div class="modal fade" id="modalResumenAsistencia" tabindex="-1" role="dialog" aria-labelledby="tituloResumenAsistencia" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content shadow-lg border-0">

      <!-- Encabezado -->
      <div class="modal-header bg-gradient-info text-white py-3">
        <h5 class="modal-title" id="tituloResumenAsistencia">
          <i class="fas fa-user-check mr-2"></i>Resumen de Asistencias por Personal
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Cuerpo -->
      <div class="modal-body bg-light">
        <div class="table-responsive">
          <table id="tablaResumenAsistencia" class="table table-hover table-bordered table-sm bg-white">
            <thead class="thead-dark text-center">
              <tr>
                <th>Personal</th>
                <th>DNI</th>
                <th>Días Asistidos</th>
                <th>Horas Totales</th>
                <th>Tardanzas</th>
                <th>Permisos</th>
                <th>Vacaciones</th>
                <th>Calendario</th>
              </tr>
            </thead>
            <tbody class="text-center align-middle">
              <!-- Aquí se llena dinámicamente -->
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="modalCalendarioAsistencia" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="tituloCalendario"></h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="contenedorCalendario">
        <!-- Calendario generado dinámicamente -->
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="asistenciaModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Asistencia</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cancelarform()">
        </button>
      </div>
      <form class="form-horizontal" name="formulario" id="formulario" method="POST">
        <div class="modal-body">
          <div class="row">
            <input  name="idasistencia" id="idasistencia" hidden>
            <input type="text" name="idpersonal" id="idpersonal" hidden>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="fecha" class="control-label">Fecha:</label>
                <input type="date" class="form-control" name="fecha" id="fecha" required>
              </div>
            </div>
            <div class="col-sm-6">
              <label for="estado" class="control-label">Estado:</label>
              <select class="form-control" name="estado" id="estado">
                <option value="asistio">Asistió</option>
                <option value="falto">Faltó</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <label for="hora_entrada" class="control-label">Hora Entrada:</label>
              <input type="time" class="form-control" name="hora_entrada" id="hora_entrada" required>
            </div>
            <div class="col-sm-6">
              <label for="hora_salida" class="control-label">Hora Salida:</label>
              <input type="time" class="form-control" name="hora_salida" id="hora_salida" >
            </div>
          </div>
          <div class="row">
              <div class="col-sm-6">
                  <label for="hora_tardanza" class="control-label">Hora Tardanza:</label>
                  <input type="time" class="form-control" name="hora_tardanza" id="hora_tardanza">
              </div>
              <div class="col-sm-6">
                  <label for="permiso" class="control-label">Permiso:</label>
                  <select class="form-control" name="permiso" id="permiso">
                      <option value="no">No</option>
                      <option value="si">Sí</option>
                  </select>
              </div>
          </div>

          <div class="row">
              <div class="col-sm-6">
                  <label for="vacaciones" class="control-label">Vacaciones:</label>
                  <select class="form-control" name="vacaciones" id="vacaciones">
                      <option value="no">No</option>
                      <option value="si">Sí</option>
                  </select>
              </div>
              <div class="col-sm-6">
                <label for="monto" class="control-label">Monto:</label>
                <input type="number" step="0.01" class="form-control" name="monto" id="monto" value="0">
              </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="pagarAsistenciaModal">
  <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Movimiento</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" role="form" name="formularioPago" id="formularioPago" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="idasistenciaEI" id="idasistenciaEI">
                    <input type="hidden" name="opcionEI" id="opcionEI" value="Egresos">
                    <input type="hidden" name="idcaja" id="idcaja">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="name" class="control-label">Almacén <span class="text-danger">*</span></label>
                            <select id="idsucursal2" name="idsucursal2" class="form-control select2" data-live-search="true">
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Concepto movimiento <span class="text-danger">*</span></label>
                            <select id="idconcepto_movimiento" name="idconcepto_movimiento" class="form-control" data-live-search="true" required>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="col-form-label">
                                <i class="fas fa-users fs-6"></i>
                                <span class="small">Personal</span>
                            </label>
                            <select id="idpersonal2" name="idpersonal2" class="form-control select2"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label>Forma de pago <span class="text-danger">*</span></label>
                            <select id="formapago" name="formapago" class="form-control" data-live-search="true" required>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia bancaria</option>
                                <option value="Tarjeta">Tarjeta POS</option>
                                <option value="Deposito">Depósito</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-6">
                            <label>Total Monto tarjeta </label>
                            <div class="input-group">
                                <input style="text-align:center;background-color:#E1FEFF ; border-color: #38F0F9; border-radius:10px;" type="text" class="form-control" id="totaldeposito" name="totaldeposito" placeholder="Monto recibido" value="0" readonly>
                            </div>

                        </div>

                        <div class="form-group col-lg-6">

                            <label># operación</label>
                            <div class="input-group">
                                <input style="text-align:center;background-color:#E1FEFF ; border-color: #38F0F9; border-radius:10px;" type="text" class="form-control" name="noperacion" id="noperacion" maxlength="7" placeholder="Descuento" value="0" readonly>
                            </div>

                        </div>
                        <div class="form-group col-lg-6">
                            <label class="col-form-label">Monto efectivo</label>
                            <input type="number" step="any" class="form-control" id="montoPagar" name="montoPagar">
                        </div>
                        <div class="form-group col-12">
                            <label for="name" class="col-sm-2 control-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="descripcion" id="descripcion"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
</div>
<script src="vistas/js/asistencia.js"></script>