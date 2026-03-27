<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Administrar sucursales</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Administrar sucursales</li>
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
                                    <button id="btnNuevoSucursal"
                                            type="button"
                                            class="btn btn-outline-primary btn-block btn-xs">
                                        <i class="fa fa-plus"></i> Nuevo
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- /.card-header -->
                        <div class="card-body" id="listadoregistros">
                            <table id="tbllistado" class="table table-striped">
                                <thead>
                                    <th>Almacén</th>
                                    <th>Acciones</th>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
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
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Sucursales</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
                <div class="modal-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-general" data-toggle="tab" href="#general-content" role="tab">Información General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-comprobantes" data-toggle="tab" href="#comprobantes-content" role="tab">Comprobantes y Series</a>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content mt-3">
                        <!-- General Information Tab -->
                        <div class="tab-pane fade show active" id="general-content" role="tabpanel" aria-labelledby="tab-general">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                    <label for="name" class="col-sm-2 control-label">Nombre:</label>
                                        <div class="col-sm-12">
                                            <input type="hidden" name="idsucursal" id="idsucursal">
                                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name" class="col-sm-2 control-label">Telefono:</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" name="telefono" id="telefono" maxlength="50" placeholder="Telefono" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="name" class="col-sm-2 control-label">Dirección:</label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Direccion" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ubigeo" class="col-sm-2 control-label">Ubigeo:</label>
                                        <div class="col-sm-12">
                                            <input type="hidden" class="form-control" name="ubigeo" id="ubigeo" maxlength="50" required>
                                            <span id="ubigeo_display" class="form-control" readonly></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="departamento_select" class="col-sm-12 control-label">Departamento:</label>
                                        <div class="col-sm-12">
                                            <select class="form-control" name="departamento_select" id="departamento_select" required>
                                                <option value="">Seleccione Departamento</option>
                                            </select>
                                            <input type="hidden" name="departamento" id="departamento">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="provincia_select" class="col-sm-12 control-label">Provincia:</label>
                                        <div class="col-sm-12">
                                            <select class="form-control" name="provincia_select" id="provincia_select" required disabled>
                                                <option value="">Seleccione Provincia</option>
                                            </select>
                                            <input type="hidden" name="provincia" id="provincia">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="distrito_select" class="col-sm-12 control-label">Distrito:</label>
                                        <div class="col-sm-12">
                                            <select class="form-control" name="distrito_select" id="distrito_select" required disabled>
                                                <option value="">Seleccione Distrito</option>
                                            </select>
                                            <input type="hidden" name="distrito" id="distrito">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <hr style="width: 100%; border-top: 1px solid #ced4da; margin-left: 10px; margin-right: 50px;">
                                    <h5 for="name" class="col-sm-6 control-label" style="font-weight: bold;">Facturacòn y moneda</h5>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                    <label for="name" class="col-sm-6 control-label">Empresa facturacion:</label>
                                    <div class="col-sm-12">
                                        <select name="idempresa" id="idempresa" class="select2" style="width: 100%;">
                                            <option value="">Seleccione Empresa</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-lg-3 col-md-6 col-xs-12">
                                    <label for="name" class="col-sm-6 control-label">Moneda:</label>
                                    <div class="col-sm-12">
                                        <input class="form-control" type="text" name="moneda" id="moneda" placeholder="SOLES - Dolares">
                                    </div>
                                </div>
                                <div class="form-group col-lg-3 col-md-6 col-xs-12">
                                    <label for="name" class="col-sm-6 control-label">Simbolo:</label>
                                    <div class="col-sm-12">
                                        <input class="form-control" type="text" name="simbolo" id="simbolo" placeholder="S/ - $">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comprobantes y Series Tab -->
                        <div class="tab-pane fade" id="comprobantes-content" role="tabpanel" aria-labelledby="tab-comprobantes">
                            <div class="table-responsive">
                                <table id="detalles" class="table table-striped table-bordered table-condensed table-hover" width="100%">
                                    <thead>
                                        <th>Comprobante</th>
                                        <th>Serie</th>
                                        <th>Número</th>
                                    </thead>
                                    <tfoot>
                                    </tfoot>
                                    <tbody>

                                    </tbody>
                                </table>
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

<script src="vistas/js/sucursal.js"></script>