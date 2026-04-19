<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Administrar empresas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Configuración</a></li>
                        <li class="breadcrumb-item active">Administrar empresas</li>
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
                                    <th>Ruc</th>
                                    <th>Razon social</th>
                                    <th>Usuario sol</th>
                                    <th>Certificado digital</th>
                                    <th>Impuesto</th>
                                    <th>Monto impuesto</th>
                                    <th>Acciones</th>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <th>Ruc</th>
                                    <th>Razon social</th>
                                    <th>Usuario sol</th>
                                    <th>Certificado digital</th>
                                    <th>Impuesto</th>
                                    <th>Monto impuesto</th>
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
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-general" data-toggle="tab" href="#general-content" role="tab">Información General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-comprobantes" data-toggle="tab" href="#comprobantes-content" role="tab">Comprobantes y Series</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="general-content" role="tabpanel" aria-labelledby="tab-general">
                            <input type="hidden" class="form-control" name="idempresa" id="idempresa">
                            
                            <div class="row m-0 mt-3">
                                <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                    <label><strong>Datos Financieros</strong></label>
                                </div>

                                <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                    <label for="ruc" class="control-label">RUC:</label>
                                    <input class="form-control" type="text" name="ruc" id="ruc" placeholder="RUC">
                                </div>

                                <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                    <label for="razon_social" class="control-label">Razón Social:</label>
                                    <input class="form-control" type="text" name="razon_social" id="razon_social" placeholder="Razon Social">
                                </div>

                                <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                    <label for="nombre_impuesto" class="control-label">Nombre Impuesto:</label>
                                    <input class="form-control" type="text" name="nombre_impuesto" id="nombre_impuesto" placeholder="IVA - IGV">
                                </div>

                                <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                    <label for="monto_impuesto" class="control-label">Monto (%):</label>
                                    <input class="form-control" type="text" name="monto_impuesto" id="monto_impuesto" placeholder="0.00">
                                </div>
                            </div>

                            <div class="row m-0 mt-3">
                                <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                    <label><strong>Usuario y Contraseña SOL - SUNAT</strong></label>
                                </div>

                                <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                    <label for="usuario_sol" class="control-label">Usuario Sol:</label>
                                    <input class="form-control" type="text" name="usuario_sol" id="usuario_sol" placeholder="Usuario Secundario o Sol">
                                </div>

                                <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                    <label for="clave_sol" class="control-label">Contraseña Sol:</label>
                                    <input class="form-control" type="text" name="clave_sol" id="clave_sol" placeholder="Contraseña">
                                </div>
                            </div>

                            <div class="row m-0 mt-3">
                                <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                    <label><strong>Certificado Electrónico y Contraseña</strong></label>
                                </div>

                                <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                    <label for="ruta_certificado" class="control-label">Certificado Digital:</label>
                                    <input type="file" class="form-control" name="ruta_certificado" id="ruta_certificado">
                                </div>

                                <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                    <label for="clave_certificado" class="control-label">Contraseña:</label>
                                    <input class="form-control" type="text" name="clave_certificado" id="clave_certificado" placeholder="Contraseña">
                                </div>

                                <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                    <label for="estado_certificado" class="control-label">Estado:</label>
                                    <select class="form-control" name="estado_certificado" id="estado_certificado">
                                        <option value="BETA">BETA</option>
                                        <option value="PRODUCCION">PRODUCCIÓN</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row m-0 mt-3">
                                <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                    <label><strong>Credenciales OAuth2 SUNAT</strong></label>
                                </div>

                                <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                    <label for="client_id" class="control-label">Client ID:</label>
                                    <input class="form-control" type="text" name="client_id" id="client_id" placeholder="Client ID de SUNAT">
                                </div>

                                <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                    <label for="client_secret" class="control-label">Client Secret:</label>
                                    <input class="form-control" type="text" name="client_secret" id="client_secret" placeholder="Client Secret de SUNAT">
                                </div>
                            </div>
                        </div>

                        <!-- Comprobantes y Series Tab -->
                        <div class="tab-pane fade" id="comprobantes-content" role="tabpanel" aria-labelledby="tab-comprobantes">
                            <div class="table-responsive mt-3">
                                <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                                    <thead>
                                        <tr>
                                            <th>Comprobante</th>
                                            <th>Serie</th>
                                            <th>Número</th>
                                        </tr>
                                    </thead>
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

<script src="vistas/js/empresas.js"></script>