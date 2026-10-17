<div class="content-wrapper">
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
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary btn-block btn-xs"
                                id="btnNuevoPermiso">
                                <i class="fa fa-plus"></i> Nuevo permiso
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblListado" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Módulo</th>
                                    <th>Código</th>
                                    <th>Permiso</th>
                                    <th style="width: 120px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalPermiso" tabindex="-1" role="dialog" aria-labelledby="modalPermisoTitulo"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title" id="modalPermisoTitulo">Permiso</h4>
                    <small class="text-muted">Asocia el permiso a uno de los módulos del sistema.</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formularioPermiso" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="idPermiso">
                    <div class="form-group">
                        <label for="moduloPermiso">Módulo</label>
                        <select class="form-control" name="modulo_id" id="moduloPermiso" required>
                            <option value="">Seleccione un módulo...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="codigoPermiso">Código</label>
                        <input type="text" class="form-control" name="codigo" id="codigoPermiso"
                            maxlength="100" placeholder="Ej. clientes.crear" required>
                        <small class="form-text text-muted">
                            Código único utilizado por la validación de permisos.
                        </small>
                    </div>
                    <div class="form-group mb-0">
                        <label for="nombrePermiso">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombrePermiso"
                            maxlength="100" placeholder="Ej. Crear cliente" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary" type="submit" id="btnGuardarPermiso">Guardar permiso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="vistas/js/permiso.js?v=2"></script>
