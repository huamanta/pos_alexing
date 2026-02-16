<!-- Content Wrapper. Contains page content -->
<?php
date_default_timezone_set('America/Lima');
$idcajaSesion = isset($_SESSION['idcaja']) ? $_SESSION['idcaja'] : '';
?>

<style>

#tablaAsistenciaRapida {
    width: 100% !important;
}

/* Tipografía más profesional */
.modal-title {
    letter-spacing: 0.5px;
}

/* Filas más limpias */
.table tbody tr td {
    vertical-align: middle !important;
    font-size: 14px;
}

/* Icono calendario */
.fa-calendar {
    transition: 0.2s;
}
.fa-calendar:hover {
    transform: scale(1.2);
    color: #0d6efd !important;
}

/* Botones más modernos */
.btn {
    border-radius: 6px !important;
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
<div class="scale-global">
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Caja Chica</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Caja Chica</li>
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
                        <div class="card-body">

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

                                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
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

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-store-alt"></i>
                                        </span>
                                        <select id="idsucursal2" name="idsucursal2" class="form-control select2">
                                        </select>

                                    </div>
                                </div>

                                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">

                                    <label>Vendedor:</label>

                                    <div class="input-group-prepend">
                                        <select id="idvendedor" name="idvendedor" class="form-control select2" required>
                                        </select>
                                    </div>

                                </div>

                            </div>

                            <br>

                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Entradas</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="salidas-tab" data-toggle="tab" data-target="#salidas" type="button" role="tab" aria-controls="salidas" aria-selected="false">Salidas</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Ingresos / Egresos</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#Asistenc" type="button" role="tab" aria-controls="Asistenc" aria-selected="false">Asistencia</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#concepts" type="button" role="tab" aria-controls="concepts" aria-selected="false">Conceptos</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                    <section class="content table-responsive">
                                        <table id="tablaCaja" class="table table-striped table-sm table-hover" style="width: 100%;">
                                            <thead style="background: #3C8DBC; color: white;">
                                                <tr>
                                                    <th style="text-align: center; width: 600px;">Comprobantes</th>
                                                    <th style="text-align: center; width: 500px;">Cash/Efectivo</th>
                                                    <th style="text-align: center; width: 500px;">Tarjeta / Transferencia</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tblCompraS">
                                                <tr>
                                                    <td><img src="files/plantilla/facturaa.svg" style="width: 25px; margin-left: 30px;"> Facturas
                                                        <span id="boleta_total_documentos_fac" class="badge badge-success"></span>
                                                    </td>
                                                    <td style="text-align: center;"><label for="facturas" id="facturas">0</label></td>
                                                    <td style="text-align: center;"><label for="facturasT" id="facturasT">0</label></td>
                                                </tr>
                                                <tr>
                                                    <td><img src="files/plantilla/boleta.svg" style="width: 25px; margin-left: 30px;"> Boletas
                                                        <span id="boleta_total_documentos_bol" class="badge badge-success"></span>
                                                    </td>
                                                    <td style="text-align: center;"><label for="boletas" id="boletas">0</label></td>
                                                    <td style="text-align: center;"><label for="boletasT" id="boletasT">0</label></td>
                                                </tr>
                                                <tr>
                                                    <td><img src="files/plantilla/note.svg" style="width: 25px; margin-left: 30px;"> Notas de Venta
                                                        <span id="boleta_total_documentos_not" class="badge badge-success"></span>
                                                    </td>
                                                    <td style="text-align: center;"><label for="notasVenta" id="notasVenta">0</label></td>
                                                    <td style="text-align: center;"><label for="notasVentaT" id="notasVentaT">0</label></td>
                                                </tr>
                                                <tr>
                                                    <td><img src="files/plantilla/download.svg" style="width: 25px; margin-left: 30px;"> Cuentas x Cobrar
                                                        <span id="boleta_total_documentos_cuentas" class="badge badge-success"></span>
                                                    </td>
                                                    <td style="text-align: center;"><label for="cuentasCobrar" id="cuentasCobrar">0</label></td>
                                                    <td style="text-align: center;"><label for="cuentasCobrarT" id="cuentasCobrarT">0</label></td>
                                                    <th style="text-align: center; width: 300px; background: #3C8DBC; color: white;">Total</th>
                                                </tr>
                                                <tr>
                                                    <td><img src="files/plantilla/subtotales.svg" style="width: 25px; margin-left: 30px;"> SubTotales</td>
                                                    <td style="text-align: center;"><label for="totalEfectivo" id="totalEfectivo">0</label></td>
                                                    <td style="text-align: center;"><label for="totalTransferencia" id="totalTransferencia">0</label></td>
                                                    <td style="text-align: center;"><label for="totalT" id="totalT"></label></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </section>
                                </div>

                                <div class="tab-pane fade" id="salidas" role="tabpanel" aria-labelledby="salidas-tab">
                                    <section class="content table-responsive">
                                        <table id="tablaCaja" class="table table-striped table-sm table-hover" style="width: 100%;">
                                            <thead style="background: #3C8DBC; color: white;">
                                                <tr>
                                                    <th style="text-align: center; width: 600px;">Comprobantes</th>
                                                    <th style="text-align: center; width: 500px;">Cash/Efectivo</th>
                                                    <th style="text-align: center; width: 500px;">Tarjeta / Transferencia</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tblCompraS">
                                                <tr>
                                                    <td><img src="files/plantilla/facturac.svg" style="width: 25px; margin-left: 30px;"> Facturas
                                                        <span id="boleta_total_documentos_fac2" class="badge badge-success"></span>
                                                    </td>
                                                    <td style="text-align: center;"><label for="facturassalida" id="facturassalida">0</label></td>
                                                    <td style="text-align: center;"><label for="facturassalidaT" id="facturassalidaT">0</label></td>
                                                </tr>
                                                <tr>
                                                    <td><img src="files/plantilla/boletac.svg" style="width: 25px; margin-left: 30px;"> Boletas
                                                        <span id="boleta_total_documentos_bol2" class="badge badge-success"></span>
                                                    </td>
                                                    <td style="text-align: center;"><label for="boletassalida" id="boletassalida">0</label></td>
                                                    <td style="text-align: center;"><label for="boletassalidaT" id="boletassalidaT">0</label></td>
                                                </tr>
                                                <tr>
                                                    <td><img src="files/plantilla/ticketc.svg" style="width: 25px; margin-left: 30px;"> Ticket
                                                        <span id="boleta_total_documentos_tick" class="badge badge-success"></span>
                                                    </td>
                                                    <td style="text-align: center;"><label for="notasCompra" id="notasCompra">0</label></td>
                                                    <td style="text-align: center;"><label for="notasCompraT" id="notasCompraT">0</label></td>
                                                </tr>
                                                <tr>
                                                    <td><img src="files/plantilla/cuentaspagar.svg" style="width: 25px; margin-left: 30px;"> Cuentas x Pagar
                                                        <span id="boleta_total_documentos_cuentas" class="badge badge-success"></span>
                                                    </td>
                                                    <td style="text-align: center;"><label for="cuentasPagar" id="cuentasPagar">0</label></td>
                                                    <td style="text-align: center;"><label for="cuentasPagarT" id="cuentasPagarT">0</label></td>
                                                    <th style="text-align: center; width: 300px; background: #3C8DBC; color: white;">Total</th>
                                                </tr>
                                                <tr>
                                                    <td><img src="files/plantilla/subtotalcompra.svg" style="width: 25px; margin-left: 30px;"> SubTotales</td>
                                                    <td style="text-align: center;"><label for="totalEfectivoSalida" id="totalEfectivoSalida">0</label></td>
                                                    <td style="text-align: center;"><label for="totalTransferenciaSalida" id="totalTransferenciaSalida">0</label></td>
                                                    <td style="text-align: center;"><label for="totalSalidaT2" id="totalSalidaT2">0</label></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </section>
                                </div>

                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-primary btn-block btn-xs" onclick="crearMovimiento()"><i class="fa fa-plus"></i> Crear Movimiento</button>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-warning btn-block btn-xs" onclick="nuevoAdelanto()">
                                                    <i class="fa fa-money-bill-wave"></i> Registrar adelanto
                                            </button>
                                        </div>
                                        <div class="col-md-2">
                                            <button id="btnReporteAdelantos" class="btn btn-primary btn-block btn-xs">
                                              Reporte Adelantos
                                            </button>
                                        </div>
                                        <div class="col-md-2">
                                            <button id="btnExportarExcel" class="btn btn-success btn-block btn-xs">
                                                <i class="fa fa-file-excel"></i> Exportar reporte
                                            </button>
                                        </div>
                                        <div class="col-md-2" hidden>
                                            <form method="post" action="controladores/exportar_excel.php">
                                                <button type="submit" class="btn btn-outline-success btn-block btn-xs"><i class="fa fa-file-excel-o"></i> Reporte</button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <table id="tbllistado" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Descripción</th>
                                                    <th>Tipo</th>
                                                    <th>Forma pago</th>
                                                    <th>Efectivo</th>
                                                    <th>Otras op</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Descripción</th>
                                                    <th>Tipo</th>
                                                    <th>Forma pago</th>
                                                    <th>Efectivo</th>
                                                    <th>Otras op</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="concepts" role="tabpanel" aria-labelledby="concepts-tab">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-primary btn-block btn-xs" onclick="crearConcepto()"><i class="fa fa-plus"></i> Crear concepto</button>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <table id="tbllistadoconceptos" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Descripcion</th>
                                                    <th>Tipo concepto</th>
                                                    <th>Categoria concepto</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Tipo concepto</th>
                                                    <th>Categoria concepto</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="Asistenc" role="tabpanel" aria-labelledby="concepts-tab">

                                    <div class="card-body">
                                        <table id="tablaAsistenciaRapida" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="seleccionarTodos" />
                                                    </th>
                                                    <th>Empleado</th>
                                                    <th>Asistencia</th>
                                                    <th>Fecha</th>
                                                    <th>Entrada</th>
                                                    <th>Salida</th>
                                                    <th>Monto día</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Se llenará con JS -->
                                            </tbody>
                                        </table>
                                        <br>
                                        <button id="guardarAsistenciaRapida" class="btn btn-primary">Guardar</button>
                                    </div>
                                </div>
                                <table class="table table-striped table-sm table-hover table-lg table-responsive" width="100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 400px;"></th>
                                            <th style="width: 400px;"></th>
                                            <th class="text-right" style="width: 300px; background: #3C8DBC; color: white;">Operaciones</th>
                                            <th style="text-align: center; width: 300px; background: #3C8DBC; color: white;">Totales</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-right" style="color: green;"><strong>Ingresos Caja efectivo:</strong></td>
                                            <td style="text-align: center;"><label for="totalI" id="totalI">0.00</label></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right" style="color: green;"><strong>Ingresos Caja tarjeta:</strong></td>
                                            <td style="text-align: center;"><label for="totalITar" id="totalITar">0.00</label></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right" style="color: red;"><strong>Egresos Caja efectivo:</strong></td>
                                            <td style="text-align: center;"><label for="totalE" id="totalE">0.00</label></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right" style="color: red;"><strong>Egresos Caja tarjeta:</strong></td>
                                            <td style="text-align: center;"><label for="totalETar" id="totalETar">0.00</label></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right" style="color: green;"><strong>Ventas Efectivo:</strong></td>
                                            <td style="text-align: center;"><label for="totalEf" id="totalEf">0.00</label></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right" style="color: green;"><strong>Ventas Tarjeta:</strong></td>
                                            <td style="text-align: center;"><label for="totalTar" id="totalTar">0.00</label></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right" style="color: red;"><strong>Salidas Efectivo Caja:</strong></td>
                                            <td style="text-align: center;"><label for="totalSalEf" id="totalSalEf">0.00</label></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right" style="color: red;"><strong>Salidas Op. Tarjeta:</strong></td>
                                            <td style="text-align: center;"><label for="totalSalTar" id="totalSalTar">0.00</label></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right" style="color: blue;"><strong>Caja General Efectivo:</strong></td>
                                            <td style="text-align: center;"><label for="totalEC" id="totalEC">0.00</label></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-right" style="color: blue;"><strong>Caja General Tarjeta:</strong></td>
                                            <td style="text-align: center;"><label for="totalET" id="totalET">0.00</label></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Movimiento</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="idmovimiento" id="idmovimiento">
                    <input type="hidden" name="idcaja" id="idcaja" value="<?= $idcajaSesion ?>">
                    <div class="row">
                        <div class="form-group col-6">
                            <div class="col-sm-12 text-danger" style="text-align: center;">
                                <input type="radio" id="egresos" name="opcionEI" value="Egresos" checked="" onchange="verificarConceptoMovimiento()">
                                <label for="male">Egresos (-)</label>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <div class="col-sm-12 text-success" style="text-align: center;">
                                <input type="radio" id="ingresos" name="opcionEI" value="Ingresos" onchange="verificarConceptoMovimiento()">
                                <label for="male">Ingresos (+)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="name" class="control-label">Almacen <span class="text-danger">*</span></label>
                            <select id="idsucursal" name="idsucursal" class="form-control select2" data-live-search="true">
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Concepto movimiento <span class="text-danger">*</span></label>
                            <select id="idconcepto_movimiento" name="idconcepto_movimiento" class="form-control" data-live-search="true" required>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="col-form-label">
                                <i class="fas fa-users fs-6"></i>
                                <span class="small">Personal</span>
                            </label>
                            <select id="idpersonal" name="idpersonal" class="form-control select2"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label>Forma de pago <span class="text-danger">*</span></label>
                            <select id="formapago" name="formapago" class="form-control" data-live-search="true" required>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia bancaria</option>
                                <option value="Tarjeta">Tarjeta POS</option>
                                <option value="Deposito">Depósito</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-6">
                            <label>Total Monto tarjeta </label>
                            <div class="input-group">
                                <input style="text-align:center;background-color:#E1FEFF ; border-color: #38F0F9; border-radius:10px;" type="text" class="form-control" id="totaldeposito" name="totaldeposito" placeholder="Monto recibido" value="0" readonly>
                            </div>

                        </div>

                        <div class="form-group col-lg-6">

                            <label># operación</label>
                            <div class="input-group">
                                <input style="text-align:center;background-color:#E1FEFF ; border-color: #38F0F9; border-radius:10px;" type="text" class="form-control" name="noperacion" id="noperacion" maxlength="7" placeholder="Descuento" value="0" readonly>
                            </div>

                        </div>
                        <div class="form-group col-lg-6">
                            <label class="col-form-label">Monto efectivo</label>
                            <input type="number" step="any" class="form-control" id="montoPagar" name="montoPagar">
                        </div>
                        <div class="form-group col-12">
                            <label for="name" class="col-sm-2 control-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="descripcion" id="descripcion"></textarea>
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

<div class="modal fade" id="myModalCocepto">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Conceptos</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" role="form" name="formularioConcepto" id="formularioConcepto" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="idconcepto_movimiento" id="idconcepto_movimiento_form">
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="name" class="control-label">Concepto movimiento <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="descripcion" id="descripcion_concepto"></textarea>
                        </div>
                        <div class="form-group col-lg-12">
                            <label>Tipo concepto<span class="text-danger">*</span></label>
                            <select id="tipo" name="tipo" class="form-control" data-live-search="true" required>
                                <option value="">Seleccione...</option>
                                <option value="ingresos">Ingresos</option>
                                <option value="egresos">Egresos</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-12" id="divCategoriaMov" hidden>
                            <label>Categoria concepto</label>
                            <select id="categoria_concepto" name="categoria_concepto" class="form-control" data-live-search="true">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" type="submit" id="btnGuardarC">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- ========================================================= -->
<!-- MODAL DE RECIBO SEMANAL -->
<!-- ========================================================= -->
<div class="modal fade" id="modalRecibo" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">

    <div class="modal-content shadow-lg border-0" style="border-radius:12px;">
      <div class="modal-body" id="recibo_content" style="
            padding:35px;">
        <!-- Se inserta el reporte aquí -->
      </div>

      <div class="modal-footer" style="background:#f3f4f6; border-bottom-left-radius:12px; border-bottom-right-radius:12px;">
        <button class="btn btn-primary px-4 shadow-sm" onclick="imprimirModalRecibo()">
          <i class="fa fa-print mr-1"></i> Imprimir
        </button>

        <button class="btn btn-danger px-4 shadow-sm" data-dismiss="modal">
          Cerrar
        </button>
      </div>

    </div>

  </div>
</div>

<div class="modal fade" id="modalCalendario" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered modal-lg">

    <div class="modal-content shadow-lg border-0" style="border-radius:12px;">

      <div class="modal-header" style="
            background:#2563eb; 
            color:white; 
            border-top-left-radius:12px; 
            border-top-right-radius:12px;">
        <h5 class="modal-title font-weight-bold">Calendario de Días Trabajados</h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body" style="background:#f3f4f6;">
        <div class="card shadow-sm border-0" style="border-radius:10px;">
          <div class="card-body">
            <div id="calendario_trabajo" style="height:500px;"></div>
          </div>
        </div>
      </div>

      <div class="modal-footer" style="background:#f9fafb; border-bottom-left-radius:12px; border-bottom-right-radius:12px;">
        <button class="btn btn-secondary px-4" data-dismiss="modal">
          Cerrar
        </button>
      </div>

    </div>

  </div>
</div>
</div>

<script src="vistas/js/caja-chica.js"></script>