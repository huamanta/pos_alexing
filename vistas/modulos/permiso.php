<!-- Contenido -->
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <br>
                        <h1 class="box-title">
                            <button class="btn btn-success" data-toggle="modal" data-target="#modalFormulario">
                                <i class="fa fa-plus-circle"></i> Agregar
                            </button>
                        </h1>
                    </div>

                    <div class="panel-body table-responsive" id="listadoregistros">
                        <table id="tblListado" class="table table-striped table-bordered table-condensed table-hover">
                            <thead>
                                <th>Opciones</th>
                                <th>Nombre</th>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <th>Opciones</th>
                                <th>Nombre</th>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>



<!-- Modal Formulario -->
<div class="modal fade" id="modalFormulario" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formulario" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Registrar Permiso</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="idpermiso" id="idpermiso">
          <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" name="nombre" id="nombre" maxlength="30" required>
          </div>

          <!-- BLOQUE DE SUBPERMISOS: oculto por defecto -->
          <div id="bloqueSubpermisos" style="display: none;">
            <h4>Subpermisos</h4>
            <div class="form-group row">
              <div class="col-md-5">
                <input type="hidden" id="idpermiso_sub" value="">
                <input type="text" id="nombre_subpermiso" class="form-control" placeholder="Nombre del subpermiso">
              </div>
              <div class="col-md-3">
                <button class="btn btn-success" type="button" onclick="registrarSubpermiso($('#idpermiso_sub').val())">Registrar</button>
              </div>
            </div>

            <div class="table-responsive">
              <table id="tablaSubpermisos" class="table table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Módulo</th>
                    <th>Subpermiso</th>
                    <th>Opciones</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
          <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-times-circle"></i> Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalAcciones" tabindex="-1" role="dialog" aria-labelledby="modalAccionesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Acciones del Subpermiso</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Formulario para registrar acción -->
        <form id="formularioAccion" onsubmit="registrarAccion(event)">
          <input type="hidden" id="idsubpermiso_accion" name="idsubpermiso">
          <div class="form-group">
            <label for="nombre_accion">Nombre de la Acción</label>
            <input type="text" class="form-control" id="nombre_accion" name="nombre" required>
          </div>
          <div class="form-group">
            <label for="descripcion_accion">Descripción</label>
            <textarea class="form-control" id="descripcion_accion" name="descripcion"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Guardar Acción</button>
        </form>

        <hr>

        <!-- Tabla de acciones -->
        <table class="table table-bordered" id="tablaAcciones">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>


<script src="vistas/js/permiso.js"></script>
