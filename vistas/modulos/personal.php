<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Personal</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Personal</li>
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
            <div class="card-header">
              <h3 class="card-title"> </h3>

              <div class="row">
                <div class="col-md-1">
                  <button type="button" class="btn btn-outline-primary btn-block btn-xs" data-toggle="modal"
                    data-target="#myModal"><i class="fa fa-plus"></i> Nuevo</button>
                </div>
                <?php if ($helpers->getUserPermissionAccion('Puede ver calendario')): ?>
                  <button type="button" class="btn btn-outline-info btn-xs" onclick="verEventos('', 0)">
                    <i class="fa fa-calendar"></i> Calendario
                  </button>
                <?php endif; ?>
              </div>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="tbllistado" class="table table-striped">
                <thead>
                  <tr>
                    <th style="width: 400px;">Nombre</th>
                    <th>Documento</th>
                    <th>Número</th>
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
                    <th>Número</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Foto</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </tfoot>
              </table>
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

<div class="modal fade" id="myModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Personal </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
        <div class="modal-body">

          <div class="form-group row">
            <label for="name" class="col-sm-2 control-label">Nombre:</label>
            <div class="col-sm-12">
              <input type="hidden" name="idpersonal" id="idpersonal">
              <input type="text" class="form-control" name="nombre" id="nombre" maxlength="250" placeholder="Nombres"
                required>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Documento:</label>
                <select id="tipo_documento" name="tipo_documento" class="form-control" required>
                  <option value="DNI">DNI</option>
                  <option value="RUC">RUC</option>
                  <option value="CEDULA">CEDULA</option>
                </select>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Número:</label>
                <input type="text" class="form-control" name="num_documento" id="num_documento" maxlength="20"
                  placeholder="Documento" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Dirección:</label>
                <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Dirección">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Teléfono:</label>
                <input type="text" class="form-control" name="telefono" id="telefono" maxlength="20"
                  placeholder="Teléfono">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Salario:</label>
                <input type="number" class="form-control" name="salario" id="salario" maxlength="50"
                  placeholder="salario">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Email:</label>
                <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="Email">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Cargo:</label>
                <select id="cargo" name="cargo" class="form-control" required>
                  <option value="Administrador">Administrador</option>
                  <option value="Vendedor">Vendedor</option>
                  <option value="Tecnico">Técnico</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Imagen:</label>
                <input type="file" class="form-control" name="imagen" id="imagen">
                <input type="hidden" name="imagenactual" id="imagenactual">
                <img src="" class="img-thumbnail" id="imagenmuestra" width="150px">
              </div>
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
  <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="myModalEventos">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="fas fa-calendar-alt mr-2"></i>
          Agenda de Seguimiento
        </h5>

        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <style>
        #calendar {
          min-height: 300px;
        }

        .fc {
          font-size: 14px;
        }

        .fc-toolbar-title {
          font-size: 1.3rem !important;
          font-weight: 600;
        }

        .fc-daygrid-event {
          border-radius: 8px;
          padding: 2px 6px;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
          border-color: #e9ecef;
        }
      </style>

      <div class="modal-body p-2 bg-light">

        <div id="calendar"></div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">

          <i class="fas fa-times"></i>
          Cerrar
        </button>
      </div>

    </div>
  </div>
</div>

<!-- MODAL PROGRAMAR VISITA -->
<div class="modal fade" id="modalProgramarVisita" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header bg-warning">
        <h5 class="modal-title">
          <i class="fas fa-calendar-check"></i>
          Programar evento
        </h5>

        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <form id="formProgramarVisita" enctype="multipart/form-data">

        <div class="modal-body">

          <input type="hidden" name="id" id="id_visita">
          <input type="hidden" name="idcpc" id="idcpc_visita">
          <input type="hidden" name="idventa" id="idventa_visita">
          <input type="hidden" name="idcliente" id="idcliente_visita">

          <div class="row">
            <div class="col-md-7">
              <div class="row">

                <!-- RESPONSABLE -->
                <div class="form-group col-md-6">
                  <label>
                    Responsable
                  </label>

                  <select class="form-control" name="idpersonal" id="idpersonal_edit">

                    <option value="">Seleccione</option>

                    <!-- CARGAR USUARIOS -->
                  </select>
                </div>

                <!-- TIPO -->
                <div class="form-group col-md-6">
                  <label>Tipo visita</label>

                  <select class="form-control" name="tipo_visita" id="tipo_visita">

                    <option value="LLAMADA">
                      LLAMADA
                    </option>
                    <option value="VISITA">
                      VISITA
                    </option>
                    <option value="COBRANZA">
                      COBRANZA
                    </option>
                    <option value="WHATSAPP">
                      WHATSAPP
                    </option>
                    <option value="CORREO">
                      CORREO
                    </option>
                    <option value="VERIFICACION">
                      VERIFICACION
                    </option>
                    <option value="SEGUIMIENTO">
                      SEGUIMIENTO
                    </option>

                    <option value="NEGOCIACION">
                      NEGOCIACION
                    </option>
                    <option value="COBRANZA">
                      OTRO
                    </option>

                  </select>
                </div>

                <!-- PRIORIDAD -->
                <div class="form-group col-md-6">
                  <label>Prioridad</label>

                  <select class="form-control" name="prioridad" id="prioridad">

                    <option value="BAJA">Baja</option>
                    <option value="MEDIA">Media</option>
                    <option value="ALTA">Alta</option>
                    <option value="URGENTE">Urgente</option>

                  </select>
                </div>

                <!-- ESTADO -->
                <div class="form-group col-md-6">
                  <label>Estado</label>

                  <select class="form-control" name="estado" id="estado">

                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="REALIZADO">REALIZADO</option>
                    <option value="NO_RESPONDE">NO RESPONDE</option>
                    <option value="REPROGRAMADO">REPROGRAMADO</option>

                  </select>
                </div>


                <!-- FECHA -->
                <div class="form-group col-md-6">
                  <label>
                    Fecha visita <span class="text-danger">*</span>
                  </label>

                  <input type="datetime-local" class="form-control" name="fecha_programada" id="fecha_programada"
                    required>
                </div>


                <!-- FECHA -->
                <div class="form-group col-md-6">
                  <label>
                    Fecha final
                  </label>
                  <input type="datetime-local" class="form-control" name="fecha_final" id="fecha_final">
                </div>

                <!-- DIRECCION -->
                <div class="form-group col-md-12">
                  <label>Dirección</label>

                  <input type="text" class="form-control" name="direccion" id="direccion_edit"
                    placeholder="Ingrese dirección de visita">
                </div>

                <!-- OBSERVACION -->
                <div class="form-group col-md-12">
                  <label>Observación</label>

                  <textarea class="form-control" name="descripcion" id="descripcion" rows="4"
                    placeholder="Detalle de la visita..."></textarea>
                </div>
              </div>
            </div>

            <style>
              .upload-box {
                border: 2px dashed #f0ad4e;
                border-radius: 15px;
                background: #fffaf2;
                cursor: pointer;
                transition: .3s;
              }

              .upload-box:hover {
                background: #fff3df;
                border-color: #ec971f;
              }

              .preview-item {
                border: 1px solid #eee;
                border-radius: 12px;
                padding: 10px;
                background: white;
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 10px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, .05);
              }

              .preview-left {
                display: flex;
                align-items: center;
                gap: 10px;
              }

              .preview-left i {
                font-size: 30px;
              }

              .btn-delete-file {
                border: none;
                background: #dc3545;
                color: white;
                width: 30px;
                height: 30px;
                border-radius: 50%;
              }
            </style>
            <div class="col-md-5">
              <div class="row">
                <!-- ADJUNTOS -->
                <div class="form-group col-md-12">

                  <label>
                    Adjuntar archivos
                  </label>

                  <div class="custom-file-upload">

                    <input type="file" id="adjuntos" class="d-none" multiple accept="
                .pdf,
                .doc,
                .docx,
                .xls,
                .xlsx,
                .jpg,
                .jpeg,
                .png,
                .webp,
                .mp4,
                .mp3
            ">

                    <label for="adjuntos" class="upload-box w-100">

                      <div class="text-center p-4">

                        <i class="fas fa-cloud-upload-alt fa-3x text-warning mb-3"></i>

                        <h5 class="mb-2">
                          Subir documentos
                        </h5>

                        <p class="text-muted mb-2">
                          Agrega múltiples archivos
                        </p>

                        <span class="badge badge-warning p-2">
                          PDF · Word · Excel · Imágenes · Audio · Video
                        </span>

                      </div>

                    </label>

                  </div>

                  <!-- LISTA -->
                  <div id="previewArchivos" class="row mt-3"></div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-secondary" data-dismiss="modal">

            <i class="fas fa-times"></i>
            Cerrar
          </button>

          <button type="submit" class="btn btn-warning">

            <i class="fas fa-save"></i>
            Guardar programación
          </button>

        </div>

      </form>
      <!-- ADJUNTOS -->

    </div>
  </div>
</div>

<div class="modal fade" id="modalVerSeguimiento" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-info">
        <h5 class="modal-title text-white">
          <i class="fas fa-calendar-check"></i>
          Detalle del Seguimiento
        </h5>

        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <div class="row">

          <div class="col-md-6 mb-3">
            <label><strong>Cliente</strong></label>
            <div id="ver_cliente" class="form-control bg-light"></div>
          </div>

          <div class="col-md-3 mb-3">
            <label><strong>Tipo</strong></label>
            <div id="ver_tipo" class="form-control bg-light"></div>
          </div>

          <div class="col-md-3 mb-3">
            <label><strong>Estado</strong></label>
            <div id="ver_estado" class="form-control bg-light"></div>
          </div>

          <div class="col-md-4 mb-3">
            <label><strong>Responsable</strong></label>
            <div id="ver_responsable" class="form-control bg-light"></div>
          </div>

          <div class="col-md-3 mb-3">
            <label><strong>Prioridad</strong></label>
            <div id="ver_prioridad" class="form-control bg-light"></div>
          </div>

          <div class="col-md-5 mb-3">
            <label><strong>Cuota</strong></label>
            <div id="ver_cuota" class="form-control bg-light"></div>
          </div>

          <div class="col-md-6 mb-3">
            <label><strong>Fecha Programada</strong></label>
            <div id="ver_fecha_programada" class="form-control bg-light"></div>
          </div>

          <div class="col-md-6 mb-3">
            <label><strong>Fecha Final</strong></label>
            <div id="ver_fecha_final" class="form-control bg-light"></div>
          </div>

          <div class="col-md-12 mb-3">
            <label><strong>Dirección</strong></label>
            <div id="ver_direccion" class="form-control bg-light"></div>
          </div>

          <div class="col-md-12 mb-3">
            <label><strong>Descripción</strong></label>
            <div id="ver_descripcion" class="form-control bg-light" style="min-height:120px;"></div>
          </div>

          <div class="col-md-12">
            <label><strong>Archivos Adjuntos</strong></label>

            <div id="ver_adjuntos" class="border rounded p-2" style="min-height:80px;">
            </div>
          </div>

        </div>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-secondary" data-dismiss="modal">

          <i class="fas fa-times"></i>
          Cerrar

        </button>

      </div>

    </div>
  </div>
</div>

<script src="vistas/js/personal.js"></script>