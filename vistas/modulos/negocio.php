<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Datos de la Empresa</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Datos de la Empresa</li>
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
                        <div class="card-body" id="listadoregistros">
                            <table id="tbllistado" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>logo</th>
                                        <th>Nombre</th>
                                        <th>Documento</th>
                                        <th>Direccion</th>
                                        <th>Telefono</th>
                                        <th>E-mail</th>
                                        <th>Pais/Ciudad</th>
                                        <th>Impuesto</th>
                                        <th>Moneda</th>
                                        <th>Opcion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>logo</th>
                                        <th>Nombre</th>
                                        <th>Documento</th>
                                        <th>Direccion</th>
                                        <th>Telefono</th>
                                        <th>E-mail</th>
                                        <th>Pais/Ciudad</th>
                                        <th>Impuesto</th>
                                        <th>Moneda</th>
                                        <th>Opcion</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="panel-body" id="formularioregistros">

                            <form action="" name="formulario" id="formulario" method="POST">

                                <br>

                                <div class="row m-0">

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-2 control-label">Imagen:</label>
                                        <div class="col-sm-12">
                                            <input type="file" class="form-control" name="imagen" id="imagen">
                                            <input type="hidden" name="imagenactual" id="imagenactual">
                                            <img src="" class="img-thumbnail" id="imagenmuestra" width="100px">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-2 control-label">Nombre:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="hidden" name="id_negocio" id="id_negocio">
                                            <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Nombre" required>
                                        </div>
                                    </div>

                                </div>

                                <div class="row m-0">

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Tipo Documento:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="ndocumento" placeholder="RUC" id="ndocumento" required>
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Documento:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="documento" id="documento" required>
                                        </div>
                                    </div>

                                </div>

                                <div class="row m-0">

                                    <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Dirección:</label>
                                        <div class="col-sm-12">
                                        <input class="form-control" type="text" name="direccion" id="direccion" maxlength="256" placeholder="Dirección" required>
                                        </div>
                                    </div>

                                </div>

                                <div class="row m-0">

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Pais:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="pais" id="pais">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Telefono:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="telefono" id="telefono" required>
                                        </div>
                                    </div>

                                </div>

                                <div class="row m-0">

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">E-mail:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="email" name="email" id="email">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Ciudad:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="ciudad" id="ciudad">
                                        </div>
                                    </div>

                                </div>

                                <div class="row m-0">

                                    <div class="form-group col-lg-12 col-md-12 col-xs-12 ml-2">
                                        <label for="">Datos Financieros</label>
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

                                    <div class="form-group col-lg-3 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Moneda:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="moneda" id="moneda" placeholder="SOLES - Dolares">
                                        </div>
                                    </div>

                                    <div class="form-group col-lg-3 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Simbolo:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="simbolo" id="simbolo" placeholder="SOLES - Dolares">
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

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Contraseña de tu Certificado:</label>
                                        <div class="col-sm-12">
                                            <input class="form-control" type="text" name="clave_certificado" id="clave_certificado" placeholder="Contraseña">
                                        </div>
                                    </div>

                                </div>

                                <div class="row m-0">

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <label for="name" class="col-sm-6 control-label">Estado Certificado:</label>
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

                                <div class="modal-footer justify-content-between">
                                    <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
                                </div>

                            </form>
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

<script src="vistas/js/negocio.js"></script>