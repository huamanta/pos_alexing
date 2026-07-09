<!-- Content Wrapper. Contains page content -->
<?php
date_default_timezone_set('America/Lima');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cajas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Cajas</li>
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
                        <div class="card-body" id="panelCajas">
                            <div class="card-header">
                                <h3 class="card-title"> </h3>

                                <div class="row">
                                    <div class="col-md-11">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-primary btn-block btn-xs"
                                            data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>
                                            Nuevo</button>
                                    </div>
                                </div>

                            </div><br>

                            <table id="tbllistado" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Numero</th>
                                        <th>Caja</th>
                                        <th>Personal</th>
                                        <th>Almacen</th>
                                        <th>Estdo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Numero</th>
                                        <th>Caja</th>
                                        <th>Personal</th>
                                        <th>Almacen</th>
                                        <th>Estdo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-body" id="panelHistorial">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    Mostrar
                                    <select id="limitHistorial" class="form-control form-control-sm d-inline-block"
                                        style="width:80px;">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    registros
                                </div>
                                <div class="col-md-6 d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-primary btn-xs"
                                        onclick="regrearLista()">
                                        <i class="fa fa-arrow-left"></i>
                                        Regresar
                                    </button>
                                </div>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <th>Caja</th>
                                    <th>Personal</th>
                                    <th>Fecha apertura</th>
                                    <th>Efectivo apertura</th>
                                    <th>Fecha cierre</th>
                                    <th>Efectivo cierre</th>
                                    <th>Ventas</th>
                                    <th>Opciones</th>
                                </thead>
                                <tbody id="tblhistorial">

                                </tbody>
                            </table>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                </div>

                                <div class="col-md-6 text-right">
                                    <ul class="pagination pagination-sm justify-content-end mb-0"
                                        id="paginadorHistorial"></ul>
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

<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cajas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <select id="idsucursal" name="idsucursal" class="form-control">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Nombre:</label>
                        <div class="col-sm-12">
                            <input type="hidden" name="idcaja" id="idcaja">
                            <input type="text" class="form-control" name="nombre" id="nombre" maxlength="50"
                                placeholder="Nombre" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Numero:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="numero" id="numero" maxlength="50"
                                placeholder="Numero" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" onclick="cancelarform()" class="btn btn-default"
                        data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="myModal2">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Lista de movimientos de apertura de caja</h4>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- Tabs -->
                <ul class="nav nav-tabs" id="custom-tabs" role="tablist">

                    <li class="nav-item">
                        <a class="nav-link active" id="tab-ventas" data-toggle="pill" href="#contenidoVentas"
                            role="tab">
                            Ventas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="tab-movimientos" data-toggle="pill" href="#contenidoMovimientos"
                            role="tab">
                            Movimientos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="tab-pagos" data-toggle="pill" href="#contenidoCobros" role="tab">
                            Cobros
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="tab-pagos" data-toggle="pill" href="#contenidoPagos" role="tab">
                            Pagos
                        </a>
                    </li>

                </ul>

                <!-- Contenido tabs -->
                <div class="tab-content pt-3">

                    <!-- TAB VENTAS -->
                    <div class="tab-pane fade show active" id="contenidoVentas" role="tabpanel">

                        <table id="tbllistadoVentas" class="table table-striped table-bordered">

                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Almacen</th>
                                    <th>Comprobante</th>
                                    <th>Total</th>
                                    <th>Pago</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>

                    <!-- TAB MOVIMIENTOS -->
                    <div class="tab-pane fade" id="contenidoMovimientos" role="tabpanel">

                        <table id="tbllistadoMovimientos" class="table table-striped table-bordered">

                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                    <th>Tipo</th>
                                    <th>Forma Pago</th>
                                    <th>Efectivo</th>
                                    <th>Transferencia</th>
                                    <th>Total</th>
                                </tr>
                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>

                    <!-- TAB COBROS -->
                    <div class="tab-pane fade" id="contenidoCobros" role="tabpanel">

                        <table id="tbllistadoCobros" class="table table-striped table-bordered">

                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Forma Pago</th>
                                    <th>Descripcion</th>
                                    <th>Efectivo</th>
                                    <th>Credito</th>
                                </tr>
                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>

                    <!-- TAB PAGOS -->
                    <div class="tab-pane fade" id="contenidoPagos" role="tabpanel">

                        <table id="tbllistadoPagos" class="table table-striped table-bordered">

                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Forma Pago</th>
                                    <th>Descripcion</th>
                                    <th>Efectivo</th>
                                    <th>Credito</th>
                                </tr>
                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>
                </div>

            </div>

            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Close
                </button>

                <button type="button" class="btn btn-primary">
                    Save changes
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalCerrarCaja" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h4 class="modal-title">
                    <i class="fa fa-cash-register"></i>
                    Cierre de Caja
                </h4>

                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
            </div>
            <form id="formCerrarCaja">
                <div class="modal-body">
                    <input id="aperturacajaid" name="aperturacajaid" hidden readonly/>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <th>Total ventas</th>
                                <td class="text-right" id="total_ventas"></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered table-sm">

                        <tbody>
                            <tr>
                                <th>Efectivo Apertura</th>
                                <td class="text-right" id="efectivo_apertura"></td>
                            </tr>
                            <tr class="table-success">
                                <th>Efectivo Esperado</th>
                                <td class="text-right font-weight-bold" id="efectivo_esperado"></td>
                            </tr>
                        </tbody>

                    </table>
                    <label>Ingresos</label>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td>
                                    <label>Efectivo</label>
                                    <table class="table table-bordered table-sm">
                                        <tbody>
                                            <tr>
                                                <th>Ventas Efectivo</th>
                                                <td class="text-right" id="ventas_efectivo"></td>
                                            </tr>
                                            <tr>
                                                <th>Abonos Efectivo</th>
                                                <td class="text-right" id="abonos_efectivo"></td>
                                            </tr>
                                            <tr>
                                                <th>Otros Ingresos Efectivo</th>
                                                <td class="text-right" id="movimientos_efectivo"></td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </td>
                                <td>
                                    <label>Transferencia</label>
                                    <table class="table table-bordered table-sm">

                                        <tbody>
                                            <tr>
                                                <th>Ventas Depósito</th>
                                                <td class="text-right" id="ventas_deposito"></td>
                                            </tr>
                                            <tr>
                                                <th>Abonos Depósito</th>
                                                <td class="text-right" id="abonos_deposito"></td>
                                            </tr>
                                            <tr>
                                                <th>Otros Ingresos Depósito</th>
                                                <td class="text-right" id="movimientos_deposito"></td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <label>Egresos</label>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td>
                                    <label>Efectivo</label>
                                    <table class="table table-bordered table-sm">
                                        <tbody>
                                            <tr>
                                                <th>Otros egrsos Efectivo</th>
                                                <td class="text-right" id="gastos_efectivo"></td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </td>
                                <td>
                                    <label>Transferencia</label>
                                    <table class="table table-bordered table-sm">

                                        <tbody>
                                            <tr>
                                                <th>Otros egrsos Depósito</th>
                                                <td class="text-right" id="gastos_deposito"></td>
                                            </tr>
                                        </tbody>

                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group">
                        <label>Efectivo contado</label>
                        <input type="number" class="form-control form-control-lg" id="efectivo_contado" step="0.01"
                            value="0" name="efectivo_contado">
                    </div>
                    <div class="form-group">
                        <label>Diferencia</label>
                        <input type="text" class="form-control form-control-lg font-weight-bold" id="diferencia"
                            readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button class="btn btn-danger" id="btnCerrarCaja">
                        <i class="fa fa-lock"></i>
                        Cerrar Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="vistas/js/cajas.js"></script>