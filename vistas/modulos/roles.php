<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Roles</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">Personal</li>
                        <li class="breadcrumb-item active">Roles</li>
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
                                <?php if ($helpers->verificarPermisoCodigo('roles.crear')) { ?>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-primary btn-block btn-xs"
                                            data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>
                                            Nuevo rol
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 d-flex align-items-center">
                                    <span class="mr-2">Mostrar</span>
                                    <select id="limit" class="form-control" style="width:100px">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>

                                    <span class="ml-2">Registros</span>

                                </div>
                                <div class="col-md-6">
                                    <input type="text" id="search" class="form-control" placeholder="Buscar...">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <table id="tbllistado" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th style="width: 120px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyRoles">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div id="pagination"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="myModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rol</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="hidden" name="idrol" id="idrol">
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="50"
                                    placeholder="Nombre del rol" required>
                            </div>
                        </div>

                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <input type="text" class="form-control" name="descripcion" id="descripcion"
                                    maxlength="100" placeholder="Descripción del rol" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                <label class="mb-0 font-weight-bold">Permisos del rol</label>
                                <div class="mt-2 mt-md-0">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        id="btnSeleccionarTodos">
                                        Seleccionar todos
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        id="btnDeseleccionarTodos">
                                        Deseleccionar todos
                                    </button>
                                </div>
                            </div>

                            <div id="listaModulos">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Cargando módulos y permisos...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script src="vistas/js/roles.js"></script>