<?php
date_default_timezone_set('America/Lima');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Nota de Crédito</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Notas de Crédito</li>
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

                                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
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

                                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
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

                                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <label>Almacén:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-store-alt"></i>
                                            </span>
                                        </div>
                                        <select id="idsucursal2" name="idsucursal2" class="form-control">
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                    <label>Estado:</label>

                                    <div class="input-group">
                                        <select id="estado" name="estado" class="form-control select2" required>
                                            <option value="Todos">Todos</option>
                                            <option value="Aceptado">Aceptado</option>
                                            <option value="Por Enviar">Por Enviar</option>
                                            <option value="Nota Credito">Nota de Crédito</option>
                                            <option value="Rechazado">Rechazado</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <table id="tbllistado" class="table table-striped">
                                <thead>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Almacen</th>
                                    <th>Documento</th>
                                    <th>Número</th>
                                    <th>Total Venta</th>
                                    <th>Tipo Pago</th>
                                    <th>Estado</th>
                                    <th width="70px;">Sunat</th>
                                    <th width="120px;">Acciones</th>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Almacen</th>
                                    <th>Documento</th>
                                    <th>Número</th>
                                    <th>Total Venta</th>
                                    <th>Tipo Pago</th>
                                    <th>Estado</th>
                                    <th>Sunat</th>
                                    <th>Acciones</th>
                                </tfoot>
                            </table>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-body" id="formularioregistros">

                            <form name="formulario" id="formulario" method="POST">

                                <input type="hidden" name="tipo" id="tipo" value="notac">

                                <div class="row">

                                    <div class="col-md-6" id="btnAgregarArt">

                                        <button class="btn btn-primary btn-sm" id="btnGuardar">
                                            <i class="fas fa-shopping-cart"></i> Realizar Nota de Crédito
                                        </button>
                                        <button id="btnCancelar" class="btn btn-danger btn-sm" onclick="cancelarform()" type="button">
                                            <i class="fas fa-window-close"></i> Cancelar
                                        </button>

                                    </div>

                                </div>

                                <br>

                                <div class="row mb-3 mt-4">

                                    <div class="col-md-9">

                                        <div class="row">

                                            <input type="hidden" name="idventa" id="idventa">

                                            <div class="row col-md-12" style="margin-top:-40px">

                                                <div class="form-group col-md-4 m-0">

                                                    <label class="col-form-label">
                                                        <span class="small">Seleccionar Comprobante</span>
                                                    </label>

                                                    <select id="comprobanteReferencia" name="comprobanteReferencia" class="form-control select2" onchange="mostrarE();"></select>
                                                </div>

                                                <div class="form-group col-md-4 m-0">

                                                    <label class="col-form-label">
                                                        <span class="small">Motivo</span>
                                                    </label>

                                                    <select id="idmotivo" name="idmotivo" class="form-control select2" onchange="mostrarE();"></select>
                                                </div>

                                            </div>

                                            <!-- LISTADO QUE CONTIENE LOS PRODUCTOS QUE SE VAN AGREGANDO PARA LA COMPRA -->
                                            <div class="col-md-12 mt-2">

                                                <table id="detalles" class="table table-striped table-responsive-sm">
                                                    <thead>
                                                        <th style="width: 550px;">Producto</th>
                                                        <th>Cantidad</th>
                                                        <th>Subtotal</th>
                                                        <th style="width: 50px;">Opciones</th>
                                                    </thead>
                                                    <tfoot>
                                                    </tfoot>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                                </table>
                                                <!-- / table -->
                                            </div>
                                            <!-- /.col -->

                                            <!-- ETIQUETA QUE MUESTRA LA SUMA TOTAL DE LOS PRODUCTOS AGREGADOS AL LISTADO -->
                                            <div class="col-md-6 mb-3" hidden>
                                                <h3>Total: S./ <span id="totalVenta">0.00</span></h3>
                                            </div>

                                            <div class="row col-md-12">

                                                <div class="col-md-4">

                                                    <div id="Subtotal" class="input-group-addon">Sub Total:</div>

                                                    <h8 class="form-control input-lg" id="most_total" readonly></h8>

                                                </div>

                                                <div class="col-md-4">

                                                    <div class="input-group-addon">IGV 18.00%:</div>

                                                    <h8 class="form-control input-lg" id="most_imp" placeholder="Impuesto" readonly></h8>

                                                </div>

                                                <div class="col-md-4">

                                                    <div id="Total" class="input-group-addon">Total:</div>
                                                    <h8 class="form-control input-lg" id="total" readonly></h8>
                                                    <input type="hidden" name="total_venta" id="total_venta">

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-3">

                                        <div class="card shadow" style="margin-top:-90px">

                                            <h5 class="card-header py-1 bg-primary text-white text-center">
                                                Datos Generales
                                            </h5>

                                            <div class="card-body p-2">

                                                <div class="form-group mb-2">

                                                    <label class="col-form-label" for="selCategoriaReg">
                                                        <i class="fas fa-map-marked-alt"></i>
                                                        <span class="small">Almacén</span>
                                                    </label>

                                                    <select id="idsucursal" name="idsucursal" class="form-control">
                                                    </select>

                                                </div>

                                                <div class="form-group mb-2">

                                                    <label class="col-form-label" for="selCategoriaReg">
                                                        <i class="fas fa-users fs-6"></i>
                                                        <span class="small">Personal</span>
                                                    </label>

                                                    <select id="idpersonal" name="idpersonal" class="form-control select2" required></select>

                                                </div>

                                                <div class="form-group mb-2">

                                                    <label class="col-form-label" for="selCategoriaReg">
                                                        <i class="fas fa-users fs-6"></i>
                                                        <span class="small">Cliente</span>
                                                    </label>

                                                    <select id="idcliente" name="idcliente" class="form-control" required>
                                                    </select>

                                                </div>

                                                <!-- SELECCIONAR TIPO DE DOCUMENTO -->
                                                <div class="form-group mb-2">

                                                    <label class="col-form-label" for="selCategoriaReg">
                                                        <i class="fas fa-file-alt fs-6"></i>
                                                        <span class="small">Tipo Documento </span>
                                                    </label>

                                                    <select name="tipo_comprobante" id="tipo_comprobante" class="form-control select2" onchange="limpiarDetalle();" required>
                                                    </select>

                                                    <span id="validate_categoria" class="text-danger small fst-italic" style="display:none">
                                                        Debe Seleccione documento
                                                    </span>

                                                </div>

                                                <!-- SELECCIONAR TIPO DE PAGO -->
                                                <div class="form-group mb-2">

                                                    <label class="col-form-label" for="selCategoriaReg">
                                                        <i class="fas fa-money-bill-alt fs-6"></i>
                                                        <span class="small">Impuesto</span>
                                                    </label>

                                                    <input style="border-color: #FFC7BB; text-align:center" type="text" class="form-control form-control-sm" name="impuesto" id="impuesto" readonly>

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

<script src="vistas/js/nota-credito.js"></script>