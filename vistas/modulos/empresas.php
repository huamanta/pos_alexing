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
                    <div class="row m-0">

                    <input type="hidden" class="form-control" name="idempresa" id="idempresa">
                                    <div class="form-group col-lg-12 col-md-12 col-xs-12 ml-2">
                                        <label for="">Datos Financieros</label>
                                    </div>
                                    

                                    <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Ruc:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="ruc" id="ruc" placeholder="RUC">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Razon Social:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="razon_social" id="razon_social" placeholder="Razon Social">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Nombre Imp:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="nombre_impuesto" id="nombre_impuesto" placeholder="IVA - IGV">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Monto (%):</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="monto_impuesto" id="monto_impuesto">
                                        </div>
                                    </div>

                                </div>

                                <div class="row m-0">

                                    <div class="form-group col-lg-12 col-md-12 col-xs-12 ml-2">
                                        <label for="">Usuario y Password SOL - SUNAT</label>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Usuario Sol:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="usuario_sol" id="usuario_sol" placeholder="Usuario Secundario o Sol">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Contraseña Sol:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="clave_sol" id="clave_sol" placeholder="Contraseña">
                                        </div>
                                    </div>

                                </div>

                                <div class="row m-0">

                                    <div class="form-group col-lg-12 col-md-12 col-xs-12 ml-2">
                                        <label for="">Certificado Electrónico y Password</label>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Certificado Digital:</label>
                                        <div class="col-sm-12">
                                            <input type="file" class="form-control" name="ruta_certificado" id="ruta_certificado">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Contraseña:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="clave_certificado" id="clave_certificado" placeholder="Contraseña">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Estado:</label>
                                        <div class="col-sm-12">
                                            <select class="form-control select-picker" name="estado_certificado" id="estado_certificado" required>
                                                <option value="BETA">BETA</option>
                                                <option value="PRODUCCION">PRODUCCIÓN</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="row m-0">

                                    <div class="form-group col-lg-12 col-md-12 col-xs-12 ml-2">
                                        <label for="">Credenciales OAuth2 SUNAT</label>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Client ID:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="client_id" id="client_id" placeholder="Client ID de SUNAT">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Client Secret:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="client_secret" id="client_secret" placeholder="Client Secret de SUNAT">
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