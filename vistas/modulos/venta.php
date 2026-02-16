<style type="text/css">
    .total-compra2 {
        font-size: 1rem;
        font-weight: ;
        /* Otros estilos según sea necesario */
    }

    .valor-rojo {
        color: red;
        font-weight: ;

        /* Otros estilos según sea necesario */
    }

  /* ============================
   ZOOM GLOBAL FUNCIONAL
   ============================ */
.scale-global {
    zoom: 0.85; /* Cambia el valor a gusto: 0.80 / 0.70 / 0.65 */
    transform-origin: top center;
}

/* Para navegadores que no soportan zoom */
@supports not (zoom:1) {
    .scale-global {
        transform: scale(0.85);
        transform-origin: top center;
    }
}
</style>
<!-- Content Wrapper. Contains page content -->
<?php
date_default_timezone_set('America/Lima');
?>
<div class="scale-global">
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid" style="margin-top: -10px;">
                <div class="row ">
                    <div class="col-sm-6">
                        <h1 style="font-size: 15px;">Lista de Comprobantes</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Comprobantes</li>
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

                                <div class="row" hidden>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-primary btn-block btn-xs" id="btnNuevo" onclick="mostrarform(true)"><i class="fa fa-plus"></i> Nuevo</button>
                                    </div>
                                </div>

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" id="listadoregistros">

                                <div class="row">

                                        <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
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

                                        <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
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

                                        <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                            <label>Almacén:</label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-store-alt"></i>
                                                    </span>
                                                </div>
                                                <select id="idsucursal2" name="idsucursal2" class="form-control select2">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                            <label>Producto:</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-box"></i>
                                                    </span>
                                                </div>
                                                <select id="idproducto" name="idproducto" class="form-control select2">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                            <label>Estado:</label>

                                            <div class="input-group">
                                                <select id="estado" name="estado" class="form-control select2">
                                                    <option value="Todos">Todos</option>
                                                    <option value="Aceptado">Aceptado</option>
                                                    <option value="Por Enviar">Por Enviar</option>
                                                    <option value="Nota Credito">Nota de Crédito</option>
                                                    <option value="Rechazado">Rechazado</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                            <label>Reporte:</label>
                                            <div class="input-group">
                                                <button id="btnExportarExcel" class="btn btn-success btn-sm" title="Exportar a Excel">
                                                    <i class="fa fa-file-excel-o"></i> Exportar Excel
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                <table id="tbllistado" class="table table-tailpanel dt-responsive">
                                    <thead>
                                        <th>Fecha</th>
                                        <th>Cliente / N° Documento</th>
                                        <th>Sucursal</th>
                                        <th>Número</th>
                                        <th>Total Venta</th>
                                        <th>Forma de pago</th>
                                        <th>Tipo Pago</th>
                                        <th>Estado</th>
                                        <th width="70px;">Sunat</th>
                                        <th style="text-align: center;"><i class="fa fa-shield" aria-hidden="true" title="Comprobar estado"></i></th>
                                        <th width="180px;">Acciones</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Sucursal</th>
                                        <th>Número</th>
                                        <th>Total Venta</th>
                                        <th>Forma de pago</th>
                                        <th>Tipo Pago</th>
                                        <th>Estado</th>
                                        <th>Sunat</th>
                                        <th></th>
                                        <th>Acciones</th>
                                    </tfoot>
                                </table>

                            </div>
                            <!-- /.card-body -->

                            <div class="card-body" id="formularioregistros">

                                <form name="formulario" id="formulario" method="POST">

                                    <input type="hidden" name="tipo" id="tipo" value="venta">

                                    <div class="row">

                                        <div class="col-md-12" id="btnAgregarArt">




                                            <div class="row">
                                                <button class="btn btn-primary btn-sm" id="btnGuardar">
                                                    <i class="fas fa-shopping-cart"></i> Realizar Venta
                                                </button>


                                                <div class="col-md-2">
                                                    <button id="btnCancelar" class="btn btn-danger btn-sm" onclick="cancelarform()" type="button">
                                                        <i class="fas fa-window-close"></i> Cancelar
                                                    </button>
                                                </div>

                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-outline-info btn-block btn-xs" data-toggle="modal" data-target="#myModal" onclick="listarArticulos();"><i class="fa fa-plus"></i> Productos</button>
                                                </div>

                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-outline-info btn-block btn-xs" data-toggle="modal" data-target="#myModal2" onclick="listarArticulos2();"><i class="fa fa-plus"></i> Servicios</button>
                                                </div>
                                            </div>



                                        </div>


                                    </div>

                                    <br>

                                    <div class="row mb-3 mt-4">

                                        <div class="col-md-9">

                                            <div class="row" style="margin-top: -50px;">

                                                <input type="hidden" name="idventa" id="idventa">

                                                <!-- BOTONES PARA VACIAR LISTADO Y COMPLETAR LA VENTA -->


                                                <div class="row col-md-4">

                                                    <div class="form-group col-md-12 m-0">

                                                        <label class="col-form-label">
                                                            <span class="small">Importar Cotizaciones</span>
                                                        </label>

                                                        <select id="comprobanteReferencia" name="comprobanteReferencia" class="form-control select2"></select>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-8">

                                                    <div class="form-group mb-6" id="btnAgregarArt2">

                                                        <label class="col-form-label" for="iptCodigoVenta">
                                                            <i class="fas fa-barcode fs-6"></i>
                                                            <span class="small">Productos</span>
                                                        </label>

                                                        <input type="text" class="form-control form-control-sm ui-autocomplete-input" id="idCodigoBarra" placeholder="Ingrese el código de barras o el nombre del producto" onkeypress="buscarProductoCod(event, this.value)">
                                                    </div>

                                                </div>



                                                <!-- LISTADO QUE CONTIENE LOS PRODUCTOS QUE SE VAN AGREGANDO PARA LA COMPRA -->
                                                <div class="col-md-12">

                                                    <table id="detalles" class="table table-striped table-responsive-sm">
                                                        <thead class="bg-info text-left fs-6">
                                                            <th style="width: 350px;">Producto</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio</th>
                                                            <th>Descuento</th>
                                                            <th>Stock</th>
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
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="col-md-3 mt-2">
                                                            <label>¿Venta al crédito?</label>
                                                            <select id="tipopago" name="tipopago" class="form-control" data-live-search="true" required>
                                                                <option value="No">No</option>
                                                                <option value="Si">Sí</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 mt-2">
                                                            <label>Forma de pago:</label>
                                                            <select id="formapago" name="formapago" class="form-control" data-live-search="true" required>
                                                                <option value="Efectivo">Efectivo</option>
                                                                <option value="Transferencia">Transferencia bancaria</option>
                                                                <option value="Tarjeta">Tarjeta POS</option>
                                                                <option value="Deposito">Depósito</option>
                                                                <option value="Yape">Yape</option>
                                                                <option value="Plin">Plin</option>
                                                                <option value="Reposicion">Reposición</option>
                                                                <option value="Costo0">Costo 0</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2 mt-2" hidden>
                                                            <label>Descuento:</label>
                                                            <div class="input-group">
                                                                <input style="text-align:center" type="text" class="form-control" name="porcentaje" id="porcentaje" maxlength="7" placeholder="Descuento" onkeyup="calcularPorcentaje();" disabled="disabled">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 mt-2">
                                                            <label>Total Recibido S/.</label>
                                                            <div class="input-group">
                                                                <input style="text-align:center" type="text" class="form-control" id="totalrecibido" name="totalrecibido" placeholder="Monto recibido" onkeyup="calcularVuelto();">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 mt-">

                                                            <label>Vuelto S/.</label>
                                                            <div class="input-group">
                                                                <input style="text-align:center" type="text" class="form-control" id="vuelto" name="vuelto" readonly="">
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3 ml-auto text-right" hidden>
                                                            <h3 class="total-compra2">Total Venta: <span class="valor-rojo" id="totalVenta">0.00</span></h3>
                                                        </div>

                                                    </div>
                                                    <div class="row">
                                                        <div class="row col-md-12 mb-3 pull-right">

                                                            <div class="col-md-12 ml-auto text-right">
                                                                <h3 class="total-compra2">Sub Total: <span class="valor-rojo" id="most_total">0.00</span></h3>
                                                                <!--<h3>Sub Total: S./ <span id="Subtotal">0.00</span></h3>

                                                        <h8 class="form-control input-lg" id="most_total" readonly></h8>-->

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="row col-md-12 mb-3 pull-right">
                                                            <div class="col-md-12 ml-auto text-right">
                                                                <h3 class="total-compra2">IGV: <span class="valor-rojo" id="most_imp">0.00</span></h3>
                                                                <!--<div class="input-group-addon">S/ IGV 18.00%:</div>

                                                        <h8 class="form-control input-lg" id="most_imp" placeholder="Impuesto" readonly></h8>-->

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="row col-md-12 ">
                                                            <div class="col-md-12 ml-auto text-right">
                                                                <h3 class="total-compra2">Total: <span class="valor-rojo" id="total">0.00</span></h3>

                                                                <!--<div id="Total" class="input-group-addon">Total:</div>
                                                        <h8 class="form-control input-lg" id="total" readonly></h8>-->
                                                                <input type="hidden" name="total_venta" id="total_venta">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ////ETIQUETA QUE MUESTRA LA SUMA TOTAL DE LOS PRODUCTOS AGREGADOS AL LISTADO -->
                                                <div class="row col-md-12 mt-4">









                                                </div>


                                                <!--calcular totales -->




                                                <!--/calcular totales -->



                                                <div class="row col-md-12 mt-3">

                                                    <div class="form-group col-lg-2" style="display: none;" id="n1">

                                                        <label>Fecha de Pago:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="date" class="form-control" id="fechaOperacion" name="fechaOperacion" value="<?php echo date("Y-m-d"); ?>">
                                                        </div>

                                                    </div>

                                                    <div class="form-group col-lg-2" style="display: none;" id="n2">

                                                        <label>Monto Pagado:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text" class="form-control" id="montoPagado" name="montoPagado" value="0" onkeyup="calcularDeuda();">
                                                        </div>

                                                    </div>

                                                    <div class="form-group col-lg-2" style="display: none;" id="n3">

                                                        <label>Monto Deuda:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text" class="form-control" id="montoDeuda" name="montoDeuda" readonly="">
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="row col-md-12 mt-3" style="display: none;" id="n6">

                                                    <div class="col-lg-2">

                                                        <label># de Operación:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text" class="form-control" id="nroOperacion" name="nroOperacion">
                                                        </div>

                                                    </div>

                                                    <div class="col-lg-2" style="display: none;" id="fechadeposito">

                                                        <label>Fecha Depósito:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="date" class="form-control" id="fechaDepostivo" name="fechaDepostivo">
                                                        </div>

                                                    </div>

                                                    <div class="col-lg-2" style="display: none;" id="banco">

                                                        <label>Banco:</label>
                                                        <select id="banco" name="banco" class="form-control" data-live-search="true" title="Seleccione Banco">
                                                            <option value="BCP">BCP</option>
                                                            <option value="BBVA">BBVA</option>
                                                            <option value="INTERBANK">INTERBANK</option>
                                                            <option value="OTRO">OTRO</option>
                                                        </select>
                                                    </div>

                                                </div>



                                            </div>

                                        </div>

                                        <div class="col-lg-3">

                                            <div class="card shadow" style="margin-top: -90px;">

                                                <h5 class="card-header py-1 bg-primary text-white text-center">
                                                    Datos Generales
                                                </h5>

                                                <div class="card-body p-2" style="">

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
                                                            <span class="small mr-2">Cliente</span><a class="input-group-addon" style="cursor: pointer;" data-toggle="modal" data-target="#ModalClientes"><i class="fa fa-plus fa-xs"></i> Nuevo Cliente</a>
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

                                                        <select name="tipo_comprobante" id="tipo_comprobante" class="form-control select2" required>
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

                                                        <input style="text-align:center" type="text" class="form-control form-control-sm" name="impuesto" id="impuesto" readonly>

                                                        <span id="validate_categoria" class="text-danger small fst-italic" style="display:none">
                                                            Debe Ingresar tipo de pago
                                                        </span>

                                                    </div>

                                                    <!-- SERIE Y NRO DE BOLETA -->
                                                    <div class="form-group mt-4">

                                                        <div class="row">

                                                            <div class="col-md-4">

                                                                <label for="iptNroSerie">Serie</label>

                                                                <input style="text-align:center" type="text" class="form-control form-control-sm" name="serie_comprobante" id="serie_comprobante" maxlength="7" placeholder="Serie" readonly>
                                                            </div>

                                                            <div class="col-md-8">

                                                                <label for="iptNroVenta">N° Orden</label>

                                                                <input style="text-align:center" type="text" class="form-control form-control-sm" name="num_comprobante" id="num_comprobante" maxlength="10" placeholder="Número" readonly>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div class="form-group">

                                                        <span class="small">Fecha</span>

                                                        <input style="text-align:center" class="form-control pull-right" type="date" name="fecha" id="fecha" required>

                                                    </div>

                                                </div><!-- ./ CARD BODY -->

                                            </div><!-- ./ CARD -->
                                        </div>

                                    </div>

                                </form>

                            </div>

                            <div class="card-body row" id="aperturcaja">
                                <div class="col-sm-4" style="margin: 0 auto;">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="card shadow" style="margin-top: -10px;">
                                                <div class="card-body">
                                                    <h1 class="text-center">APERTURAR CAJA</h1>
                                                    <div class="col-md-12" style="margin-bottom: 10px;">
                                                        <div class="scrollmenu" id="cardCategorias" style="background-color: transparent;">
                                                        </div>
                                                    </div>
                                                    <form action="" id="form-apertura-caja">
                                                        <div class="col-md-12 md-1">
                                                            <div class="form-group">
                                                                <label for="">Caja</label>
                                                                <select class="form-control" name="caja_apertura" id="input-caja" required>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 md-1">
                                                            <div class="form-group">
                                                                <label for="">Efectivo</label>
                                                                <input type="number" class="form-control" name="efectivo_apertura" id="efectivo_apertura" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 md-1 text-center">
                                                            <button type="submit" class="btn btn-success">Aperturar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

    <!-- Modal para registrar número de celular -->
    <div class="modal fade" id="modalCelular" tabindex="-1" role="dialog" aria-labelledby="modalCelularLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCelularLabel">Registrar Número de Celular</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <label for="numeroCelular">Número de Celular:</label>
            <input type="text" name="numeroCelular" id="numeroCelular" class="form-control" placeholder="Ingrese número de celular">
            <!-- Campos ocultos para tipo de comprobante, serie y número -->
                    <input type="hidden" id="idventa">
                    <input type="hidden" id="tipoComprobante">
                    <input type="hidden" id="numComprobante">
                    <input type="hidden" id="serieComprobante">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="cancelarmodalCelular()">Cerrar</button>
            <button type="button" class="btn btn-primary" onclick="abrirWhatsApp()">Abrir WhatsApp</button>
          </div>
        </div>
      </div>
    </div>


    <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Seleccione un Producto</h4>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
                <div class="modal-body" class="panel-body">
                    <table id="tblarticulos" class="table table-striped table-responsive-lg" width="100%">
                        <thead>
                            <th style="width: 5px;">Opciones</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoria</th>
                            <th>UM</th>
                            <th>Stock</th>
                            <th>Precio</th>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <th style="width: 5px;">Opciones</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoria</th>
                            <th>UM</th>
                            <th>Stock</th>
                            <th>Precio</th>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="myModal2">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Seleccione un Servicio</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" class="panel-body">
                    <table id="tblarticulos2" class="table table-striped table-responsive-lg" width="100%">
                        <thead>
                            <th>Opciones</th>
                            <th>Nombre</th>
                            <th>Categoria</th>
                            <th>UM</th>
                            <th>Fecha Vencimiento</th>
                            <th>Stock</th>
                            <th>Precio</th>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <th>Opciones</th>
                            <th>Nombre</th>
                            <th>Categoria</th>
                            <th>UM</th>
                            <th>Fecha Vencimiento</th>
                            <th>Stock</th>
                            <th>Precio</th>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="getCodeModal" tabindex="-1" aria-labelledby="getCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Datos de venta</h3>
                    </div>
                </div>
                <div class="modal-body panel-body">

                    <div class="row m-0">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name" class="control-label">Cliente:</label>
                                <h4 id="cliente"></h4>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <label class="control-label">Personal:</label>
                            <h4 id="personalm"></h4>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name" class="control-label">Fecha:</label>
                                <h4 id="fecha_hora"></h4>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name" class="control-label">Tipo comprobante:</label>
                                <h4 id="tipo_comprobantem"></h4>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name" class="control-label">Correlativo:</label>
                                <h4 id="correlativo"></h4>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name" class="control-label">Forma Pago:</label>
                                <h4 id="formapagom"></h4>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name" class="control-label">Numero de Operacion:</label>
                                <h4 id=""></h4>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name" class="control-label">Banco:</label>
                                <h4 id=""></h4>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name" class="control-label">Observaciones:</label>
                                <h4 id="observaciones"></h4>
                            </div>
                        </div>
                    </div>

                    <div class="row m-0">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name" class="control-label">Crédito:</label>
                                <h4 id="ventacreditom"></h4>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name" class="control-label">Abonos:</label>
                                <h4 id="abonos"></h4>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="name" class="control-label">Deuda:</label>
                                <h4 id="deuda"></h4>
                            </div>
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
                    <button type="button" onclick="cancelarform()" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    <div class="modal fade" id="ModalClientes">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cliente</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="limpiarCliente()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" role="form" name="formularioClientes" id="formularioClientes" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name" class="control-label">Nombre:</label>
                                    <input type="hidden" name="idpersona" id="idpersona">
                                    <input type="hidden" name="tipo_persona" id="tipo_persona" value="Cliente">
                                    <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100" placeholder="Nombre del proveedor" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name" class="control-label">Tipo Documento:</label>
                                    <select class="form-control select-picker" name="tipo_documento" id="tipo_documento" required>
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                        <option value="CEDULA">CEDULA</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="name" class="control-label">Número Documento:</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="num_documento" id="num_documento" maxlength="20" placeholder="Documento">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="cursor: pointer;" id="Buscar_Cliente" onclick="BuscarCliente()" title="Buscar Cliente" type="button"><i class="fa fa-search"></i></span>
                                        <span class="input-group-text" id="cargando" title="Cargando" type="button" style="display: none;"><i><img src="files/plantilla/cargando.gif" width="15px"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name" class="control-label">Dirección:</label>
                                    <input type="text" class="form-control" name="direccion" id="direccion" maxlength="70" placeholder="Dirección">
                                    Estado:<label for="" id="estado2">-</label>
                                    Condición:<label for="" id="condicion">-</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name" class="control-label">Teléfono:</label>
                                    <input type="text" class="form-control" name="telefono" id="telefono" maxlength="20" placeholder="Teléfono">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name" class="control-label">Email:</label>
                                    <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="Email">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" onclick="limpiarCliente()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="ModalTipocomprobante">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">IMPRIMIR COMPROBANTE</h4>
                    <button type="button" class="close" aria-label="Close" onclick="limpiarCliente()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row" id="pant-imprimir">

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary pull-right" type="button" onclick="sinComprobante()">SIN COMPROBANTE</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
<script src="vistas/js/venta.js"></script>