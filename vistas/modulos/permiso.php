<!-- Contenido -->
<style>
  /* MODAL PERMISOS */
  #modalFormulario .modal-content {
    border-radius: 14px;
  }

  #modalFormulario .modal-header {
    min-height: 75px;
  }

  #modalFormulario .modal-title {
    font-size: 18px;
  }

  #modalFormulario .close {
    font-size: 24px;
    color: #6c757d;
    opacity: .7;
  }

  #modalFormulario .close:hover {
    opacity: 1;
  }

  /* ICONO PRINCIPAL */
  .bg-primary-light {
    background: rgba(13, 110, 253, .10);
  }

  /* INPUTS */

  #modalFormulario .form-control:focus {
    box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .10);
    border-color: #80bdff;
  }

  #modalFormulario .input-group-text {
    border-color: #ced4da;
  }

  /* SUBPERMISOS */
  .subpermiso-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
  }

  .subpermiso-header {
    padding: 14px 16px;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
  }

  .subpermiso-header h6 {
    color: #1f2937;
    font-size: 15px;
  }

  .subpermiso-header small {
    color: #6b7280;
  }

  .subpermiso-icon {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: rgba(13, 110, 253, .10);
    color: #0d6efd;
  }

  /* TABLA */
  #tablaSubpermisos thead th {
    background: #f8fafc;
    color: #495057;
    border-top: 0;
    border-bottom: 1px solid #dee2e6;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .3px;
    padding: 11px 10px;
  }

  #tablaSubpermisos tbody td {
    vertical-align: middle;
    padding: 10px;
    font-size: 13px;
  }

  #tablaSubpermisos tbody tr:last-child td {
    border-bottom: 0;
  }

  /* BOTONES */
  #modalFormulario .btn {
    border-radius: 7px;
    font-weight: 500;
  }

  #btnGuardar {
    box-shadow: 0 3px 8px rgba(13, 110, 253, .20);
  }

  /* FOOTER */
  #modalFormulario .modal-footer {
    border-top: 1px solid #e9ecef !important;
  }
</style>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Permisos</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Permisos</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header with-border">
              <br>
              <h1 class="box-title">
                <button class="btn btn-success" id="btonAgregar">
                  <i class="fa fa-plus-circle"></i> Agregar
                </button>
              </h1>
            </div>

            <div class="card-body table-responsive" id="listadoregistros">
              <table id="tblListado" class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                  <th>Nombre</th>
                  <th class="text-center">Opciones</th>
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



<!-- Modal Formulario -->
<div class="modal fade" id="modalFormulario" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <form id="formulario" method="POST" class="w-100">
      <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">

        <!-- HEADER -->
        <div class="modal-header border-0 px-4 py-3 bg-white">
          <div class="d-flex align-items-center">
            <div
              class="mr-3 d-flex align-items-center justify-content-center bg-primary-light text-primary rounded-circle"
              style="width: 46px; height: 46px;">
              <i class="fas fa-user-shield fa-lg"></i>
            </div>

            <div>
              <h5 class="modal-title font-weight-bold text-dark mb-0" id="modalLabel">
                Registrar Permiso
              </h5>
              <small class="text-muted">
                Configure el permiso y sus subpermisos
              </small>
            </div>
          </div>

          <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body px-4 pt-2 pb-4">

          <input type="hidden" name="idpermiso" id="idpermiso">

          <!-- PERMISO PRINCIPAL -->
          <div class="form-group mb-4">
            <label for="nombre" class="font-weight-bold text-dark">
              Nombre del permiso
              <span class="text-danger">*</span>
            </label>

            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text bg-light border-right-0">
                  <i class="fas fa-tag text-primary"></i>
                </span>
              </div>

              <input type="text" class="form-control border-left-0" name="nombre" id="nombre" maxlength="30"
                placeholder="Ej. Ventas, Compras, Usuarios..." required>
            </div>

            <small class="form-text text-muted">
              Nombre que aparecerá en la administración de permisos.
            </small>
          </div>

          <!-- SUBPERMISOS -->
          <div id="bloqueSubpermisos" style="display: none;">

            <div class="subpermiso-card">

              <!-- CABECERA -->
              <div class="subpermiso-header">
                <div class="d-flex align-items-center">
                  <div class="subpermiso-icon mr-3">
                    <i class="fas fa-layer-group"></i>
                  </div>

                  <div>
                    <h6 class="font-weight-bold mb-0">
                      Subpermisos
                    </h6>
                    <small>
                      Agregue las acciones disponibles para este permiso.
                    </small>
                  </div>
                </div>
              </div>

              <div class="p-3">

                <!-- FORMULARIO SUBPERMISO -->
                <div class="mb-3">
                  <label for="nombre_subpermiso" class="font-weight-bold text-dark">
                    Nuevo subpermiso
                  </label>

                  <div class="row">

                    <div class="col-md-9 mb-2 mb-md-0">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text bg-light border-right-0">
                            <i class="fas fa-list text-primary"></i>
                          </span>
                        </div>

                        <input type="hidden" id="idpermiso_sub" value="">

                        <input type="text" id="nombre_subpermiso" class="form-control border-left-0"
                          placeholder="Ej. Registrar, Editar, Eliminar...">
                      </div>
                    </div>

                    <div class="col-md-3">
                      <button class="btn btn-success btn-block h-100" type="button"
                        onclick="registrarSubpermiso($('#idpermiso_sub').val())">
                        <i class="fas fa-plus mr-1"></i>
                        Agregar
                      </button>
                    </div>

                  </div>
                </div>

                <!-- TABLA -->
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div>
                    <h6 class="font-weight-bold text-dark mb-0">
                      Subpermisos registrados
                    </h6>
                  </div>

                  <span class="badge badge-light border px-2 py-1">
                    <i class="fas fa-list mr-1 text-primary"></i>
                    Lista
                  </span>
                </div>

                <div class="table-responsive border rounded-lg">
                  <table id="tablaSubpermisos" class="table table-hover mb-0">

                    <thead>
                      <tr>
                        <th width="70" class="text-center">ID</th>
                        <th>Módulo</th>
                        <th>Subpermiso</th>
                        <th class="text-center">Opciones</th>
                      </tr>
                    </thead>

                    <tbody></tbody>

                  </table>
                </div>

              </div>
            </div>

          </div>

        </div>

        <!-- FOOTER -->
        <div class="modal-footer bg-light border-0 px-4 py-3">

          <button type="button" class="btn btn-light border px-4" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>
            Cancelar
          </button>

          <button class="btn btn-primary px-4" type="submit" id="btnGuardar">
            <i class="fas fa-save mr-1"></i>
            Guardar permiso
          </button>

        </div>

      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalAcciones" tabindex="-1" role="dialog" aria-labelledby="modalAccionesLabel"
  aria-hidden="true">

  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

    <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">

      <!-- HEADER -->
      <div class="modal-header border-0 px-4 py-3 bg-white">

        <div class="d-flex align-items-center">

          <div
            class="mr-3 d-flex align-items-center justify-content-center bg-primary-light text-primary rounded-circle"
            style="width:46px;height:46px;">
            <i class="fas fa-bolt fa-lg"></i>
          </div>

          <div>
            <h5 class="modal-title font-weight-bold text-dark mb-0" id="modalAccionesLabel">
              Acciones del Subpermiso
            </h5>

            <small class="text-muted">
              Configure las acciones disponibles para este subpermiso
            </small>
          </div>

        </div>

        <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>

      </div>

      <!-- BODY -->
      <div class="modal-body px-4 pt-2 pb-4">

        <!-- FORMULARIO -->
        <div class="accion-card mb-4">

          <div class="accion-card-header">

            <div class="d-flex align-items-center">

              <div class="accion-icon mr-3">
                <i class="fas fa-plus"></i>
              </div>

              <div>
                <h6 class="font-weight-bold mb-0">
                  Nueva acción
                </h6>

                <small>
                  Registre una acción para este subpermiso.
                </small>
              </div>

            </div>

          </div>

          <div class="p-3">

            <form id="formularioAccion" onsubmit="registrarAccion(event)">

              <input type="hidden" id="idsubpermiso_accion" name="idsubpermiso">

              <div class="row">

                <!-- NOMBRE -->
                <div class="col-md-5">

                  <div class="form-group mb-3">

                    <label for="nombre_accion" class="font-weight-bold text-dark">
                      Nombre de la acción
                      <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                      <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-right-0">
                          <i class="fas fa-bolt text-primary"></i>
                        </span>
                      </div>

                      <input type="text" class="form-control border-left-0" id="nombre_accion" name="nombre"
                        maxlength="50" placeholder="Ej. Registrar, Editar..." required>

                    </div>

                  </div>

                </div>

                <!-- DESCRIPCIÓN -->
                <div class="col-md-7">

                  <div class="form-group mb-3">

                    <label for="descripcion_accion" class="font-weight-bold text-dark">
                      Descripción
                    </label>

                    <div class="input-group">

                      <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-right-0">
                          <i class="fas fa-align-left text-primary"></i>
                        </span>
                      </div>

                      <textarea class="form-control border-left-0" id="descripcion_accion" name="descripcion" rows="2"
                        placeholder="Descripción de la acción..."></textarea>

                    </div>

                  </div>

                </div>

              </div>

              <div class="text-right">

                <button type="submit" class="btn btn-primary px-4" id="btnGuardarAccion">

                  <i class="fas fa-save mr-1"></i>
                  Guardar acción

                </button>

              </div>

            </form>

          </div>

        </div>

        <!-- LISTADO -->
        <div>

          <div class="d-flex align-items-center justify-content-between mb-2">

            <div>

              <h6 class="font-weight-bold text-dark mb-0">
                Acciones registradas
              </h6>

              <small class="text-muted">
                Acciones disponibles para este subpermiso.
              </small>

            </div>

            <span class="badge badge-light border px-2 py-1">
              <i class="fas fa-list mr-1 text-primary"></i>
              Lista
            </span>

          </div>

          <div class="table-responsive border rounded-lg">

            <table class="table table-hover mb-0" id="tablaAcciones">

              <thead>

                <tr>

                  <th width="70" class="text-center">
                    ID
                  </th>

                  <th width="25%">
                    Nombre
                  </th>

                  <th>
                    Descripción
                  </th>

                  <th width="120" class="text-center">
                    Opciones
                  </th>

                </tr>

              </thead>

              <tbody></tbody>

            </table>

          </div>

        </div>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer bg-light border-0 px-4 py-3">

        <button type="button" class="btn btn-light border px-4" data-dismiss="modal">

          <i class="fas fa-times mr-1"></i>
          Cerrar

        </button>

      </div>

    </div>

  </div>

</div>


<script src="vistas/js/permiso.js"></script>