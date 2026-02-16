<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Orden de Compra</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Orden de Compra</li>
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
                        <div class="card-header" id="header">
                            <h3 class="card-title"> </h3>

                            <div class="row">
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-outline-primary btn-block btn-xs" id="btnNuevo" onclick="mostrarform(true)"><i class="fa fa-plus"></i> Nuevo</button>
                                </div>
                            </div>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body" id="listadoregistros">

                            <div class="row">

                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label>Fecha Inicio:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo date("Y-m-d"); ?>">
                                    </div>
                                </div>

                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label>Fecha Fin:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?php echo date("Y-m-d"); ?>">
                                    </div>
                                </div>

                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label>Almacén:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <select id="idsucursal2" name="idsucursal2" class="form-control">
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <table id="tbllistado" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Proveedor</th>
                                        <th>Personal</th>
                                        <th>Tipo Documento</th>
                                        <th>Número</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th style="width: 120px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Proveedor</th>
                                        <th>Personal</th>
                                        <th>Tipo Documento</th>
                                        <th>Número</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th style="width: 120px;">Acciones</th>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-body" id="formularioregistros">

                            <form name="formulario" id="formulario" method="POST">

                                <div class="row mb-3">

                                    <div class="col-md-9">

                                        <div class="row">

                                            <input type="hidden" name="idcompra" id="idcompra">

                                            <!-- INPUT PARA INGRESO DEL CODIGO DE BARRAS O DESCRIPCION DEL PRODUCTO -->
                                            <div class="col-md-12 mb-3">

                                                <div class="form-group mb-2">

                                                    <a data-toggle="modal" href="#myModal">
                                                        <button id="btnAgregarArt" type="button" class="btn btn-default btn-sm" onclick="listarArticulos();"><span class="fa fa-plus"></span> Agregar Productos</button>
                                                    </a>
                                                </div>

                                            </div>

                                            <!-- ETIQUETA QUE MUESTRA LA SUMA TOTAL DE LOS PRODUCTOS AGREGADOS AL LISTADO -->
                                            <div class="col-md-6 mb-3">

                                                <div class="form-group">
                                                    <label for="name" class="control-label"><i class="fas fa-map-marker-alt"></i> Lugar de Entrega</label>
                                                    <input style="border-color: #FFC7BB;" type="text" class="form-control" name="lugar_entrega" id="lugar_entrega" placeholder="Lugar de entrega">
                                                </div>

                                                <input style="border-color: #FFC7BB; text-align:center" type="text" class="form-control" name="impuesto" id="impuesto" hidden>

                                            </div>

                                            <div class="col-md-6 mb-3">

                                                <div class="form-group">
                                                    <label for="name" class="control-label">Motivo de la Compra</label>
                                                    <input style="border-color: #FFC7BB;" type="text" class="form-control" name="motivo_compra" id="motivo_compra" placeholder="Motivo de la Compra">
                                                </div>

                                            </div>

                                            <!-- BOTONES PARA VACIAR LISTADO Y COMPLETAR LA VENTA -->
                                            <div class="col-md-6">
                                                <button class="btn btn-primary btn-sm" id="btnGuardar">
                                                    <i class="fas fa-shopping-cart"></i> Realizar Orden
                                                </button>
                                                <button id="btnCancelar" class="btn btn-danger btn-sm" onclick="cancelarform()" type="button">
                                                    <i class="fas fa-window-close"></i> Cancelar
                                                </button>
                                            </div>

                                            <br><br>

                                            <!-- LISTADO QUE CONTIENE LOS PRODUCTOS QUE SE VAN AGREGANDO PARA LA COMPRA -->
                                            <div class="col-md-12">

                                                <table id="detalles" class="table table-striped table-responsive-sm">
                                                    <thead>
                                                        <th>Producto</th>
                                                        <th>UM</th>
                                                        <th>Cantidad</th>
                                                        <th>P. Compra</th>
                                                        <th>P. Venta</th>
                                                        <th>Subtotal</th>
                                                        <th>Opciones</th>
                                                    </thead>
                                                    <tfoot>

                                                    </tfoot>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                                <!-- / table -->
                                            </div>
                                            <!-- /.col -->

                                            <!-- ETIQUETA QUE MUESTRA LA SUMA TOTAL DE LOS PRODUCTOS AGREGADOS AL LISTADO -->
                                            <div class="col-md-6 mb-3">
                                                <h3>Total Orden: S./ <span id="totalVenta">0.00</span></h3>
                                            </div>

                                            <div class="row col-md-12" hidden>

                                                <div class="col-md-4">

                                                    <div id="Subtotal" class="input-group-addon">S/ Sub Total:</div>

                                                    <h8 class="form-control input-lg" id="most_total" readonly></h8>

                                                </div>

                                                <div class="col-md-4">

                                                    <div id="IGV" class="input-group-addon">S/ IGV 18.00%:</div>

                                                    <h8 class="form-control input-lg" id="most_imp" placeholder="Impuesto" readonly></h8>

                                                </div>

                                                <div class="col-md-4">

                                                    <div id="Total" class="input-group-addon">Total:</div>
                                                    <h8 class="form-control input-lg" id="total" readonly></h8>
                                                    <input type="hidden" name="total_compra" id="total_compra">

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-3">

                                        <div class="card shadow">

                                            <h5 class="card-header py-1 bg-primary text-white text-center">
                                                Datos Generales
                                            </h5>

                                            <div class="card-body p-2">

                                                <div class="form-group mb-2">

                                                    <label class="col-form-label" for="selCategoriaReg">
                                                        <i class="fas fa-map-marked-alt"></i>
                                                        <span class="small">Almacén</span>
                                                    </label>

                                                    <select id="idsucursal" name="idsucursal" class="form-control" data-live-search="true">
                                                    </select>

                                                </div>

                                                <div class="form-group mb-2">

                                                    <label class="col-form-label" for="selCategoriaReg">
                                                        <i class="fas fa-users fs-6"></i>
                                                        <span class="small">Proveedor</span>
                                                    </label>

                                                    <select id="idproveedor" name="idproveedor" class="form-control select2" required>

                                                    </select>

                                                </div>

                                                <!-- SELECCIONAR TIPO DE DOCUMENTO -->
                                                <div class="form-group mb-2">

                                                    <label class="col-form-label" for="selCategoriaReg">
                                                        <i class="fas fa-file-alt fs-6"></i>
                                                        <span class="small">Documento</span>
                                                    </label>

                                                    <select name="tipo_c" id="tipo_c" class="form-control">
                                                        <option value="Orden Compra" selected="true">Orden de compra</option>
                                                    </select>

                                                    <span id="validate_categoria" class="text-danger small fst-italic" style="display:none">
                                                        Debe Seleccione documento
                                                    </span>

                                                </div>

                                                <!-- SELECCIONAR TIPO DE PAGO -->
                                                <div class="form-group mb-2">

                                                    <label class="col-form-label" for="selCategoriaReg">
                                                        <i class="fas fa-money-bill-alt fs-6"></i>
                                                        <span class="small">Forma de Pago</span>
                                                    </label>

                                                    <select name="formapago" id="formapago" class="form-control select2" data-live-search="true" title="Seleccione Forma de Pago">
                                                        <option value="Contado">Contado</option>
                                                        <option value="Crédito a 7 días">Crédito a 7 días</option>
                                                        <option value="Crédito a 15 días">Crédito a 15 días</option>
                                                        <option value="Crédito a 30 días">Crédito a 30 días</option>
                                                        <option value="Crédito a 45 días">Crédito a 45 días</option>
                                                        <option value="Crédito a 60 días">Crédito a 60 días</option>
                                                        <option value="Crédito a 90 días">Crédito a 90 días</option>
                                                        <option value="Crédito a 120 días">Crédito a 120 días</option>
                                                        <option value="Contraentrega">Contraentrega</option>
                                                        <option value="Transferencia">Transferencia</option>
                                                        <option value="Yape">Yape</option>
                                                        <option value="Plin">Plin</option>
                                                        <option value="Reposición">Reposición</option>
                                                        <option value="Control de Calidad">Control de Calidad</option>
                                                    </select>

                                                    <span id="validate_categoria" class="text-danger small fst-italic" style="display:none">
                                                        Debe Ingresar tipo de pago
                                                    </span>

                                                </div>

                                                <!-- SERIE Y NRO DE BOLETA -->
                                                <div class="form-group mt-4">

                                                    <div class="row">

                                                        <div class="col-md-4">

                                                            <label for="iptNroSerie">Serie</label>

                                                            <input style="border-color: #FFC7BB; text-align:center" type="text" class="form-control form-control-sm" name="serie_comprobante" id="serie_comprobante" maxlength="7" placeholder="Serie" readonly>
                                                        </div>

                                                        <div class="col-md-8">

                                                            <label for="iptNroVenta">N° Orden</label>

                                                            <input style="border-color: #99C0E7; text-align:center" type="text" class="form-control form-control-sm" name="num_comprobante" id="num_comprobante" maxlength="10" placeholder="Número" readonly>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="form-group">

                                                    <span class="small">Fecha</span>

                                                    <input style="border-color: #99C0E7; text-align:center" class="form-control pull-right" type="date" name="fecha" id="fecha" required>

                                                </div>

                                            </div><!-- ./ CARD BODY -->

                                        </div><!-- ./ CARD -->
                                    </div>

                                </div>

                            </form>

                        </div>

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
                <h4 class="modal-title">Seleccione un Producto</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" class="panel-body">
                <table id="tblarticulos" class="table table-striped table-responsive-lg" width="100%">
                    <thead>
                        <th>Nombre</th>
                        <th>UM</th>
                        <th>Categoría</th>
                        <th>Código</th>
                        <th>Stock</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <th>Nombre</th>
                        <th>UM</th>
                        <th>Categoría</th>
                        <th>Código</th>
                        <th>Stock</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="getCodeModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Vista de Compra</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" class="panel-body">

                <div class="row m-0">

                    <div class="col-md-4">

                        <div id="Subtotal" class="input-group-addon">Proveedor:</div>

                        <input class="form-control" type="text" name="idproveedorm" id="idproveedorm" readonly>

                    </div>

                    <div class="col-md-4">

                        <div id="IGV" class="input-group-addon">Personal:</div>

                        <input type="text" class="form-control" id="nuevoVendedor" value="<?php echo $_SESSION["nombre"]; ?>" readonly>

                    </div>

                    <div class="col-md-4">

                        <div id="Total" class="input-group-addon">Fecha:</div>
                        <input class="form-control pull-right" type="text" name="fecha_horam" id="fecha_horam" readonly>

                    </div>

                </div>

                <br>

                <div class="row m-0">

                    <div class="col-md-4">

                        <div id="Subtotal" class="input-group-addon">Comprobante:</div>

                        <input class="form-control" type="text" name="serie_comprobantem" id="serie_comprobantem" maxlength="7" readonly>

                    </div>

                    <div class="col-md-4">

                        <div id="IGV" class="input-group-addon">Número:</div>

                        <input class="form-control" type="text" name="num_comprobantem" id="num_comprobantem" maxlength="10" readonly>

                    </div>

                    <div class="col-md-4">

                        <div id="Total" class="input-group-addon">Impuesto:</div>
                        <input class="form-control" type="text" name="impuestom" id="impuestom" readonly>

                    </div>

                </div>

                <br>

                <div class="form-group col-lg-12 col-md-12 col-xs-12">
                    <table id="detallesm" class="table table-striped table-responsive-lg" width="100%">
                        <tbody>

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script src="vistas/js/ordencompra.js"></script>