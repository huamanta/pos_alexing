<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Movimientos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Movimientos</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">

                                <div class="form-group col-lg-2 col-md-3 col-sm-4 col-xs-12">
                                    <label>Fecha Inicio:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                                    </div>
                                </div>

                                <div class="form-group col-lg-2 col-md-3 col-sm-3 col-xs-12">
                                    <label>Fecha Fin:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-primary btn-block btn-xs"
                                        onclick="crearMovimiento()"><i class="fa fa-plus"></i> Crear Movimiento</button>
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
                                <div class="col-md-6 d-flex align-items-center mt-2">
                                    <span class="mr-2">Mostrar</span>
                                    <select id="limit" class="form-control" style="width:100px">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>

                                    <span class="ml-2">Registros</span>

                                </div>
                                <div class="col-md-6 mt-2">
                                    <input type="text" id="search" class="form-control" placeholder="Buscar...">
                                </div>
                                <div class="col-md-12 mt-2">
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
                                        <tbody id="tbodyMovimientos">
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
                    <div class="row">
                        <div class="form-group col-6">
                            <div class="col-sm-12 text-danger" style="text-align: center;">
                                <input type="radio" id="egresos" name="opcionEI" value="Egresos" checked=""
                                    onchange="verificarConceptoMovimiento()">
                                <label for="male">Egresos (-)</label>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <div class="col-sm-12 text-success" style="text-align: center;">
                                <input type="radio" id="ingresos" name="opcionEI" value="Ingresos"
                                    onchange="verificarConceptoMovimiento()">
                                <label for="male">Ingresos (+)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!--div class="form-group col-lg-6">
                            <label for="name" class="control-label">Almacen <span class="text-danger">*</span></label>
                            <select id="idsucursal" name="idsucursal" class="form-control select2"
                                data-live-search="true">
                            </select>
                        </div-->
                        <div class="form-group col-lg-6">
                            <label>Concepto movimiento <span class="text-danger">*</span></label>
                            <select id="idconcepto_movimiento" name="idconcepto_movimiento" class="form-control"
                                data-live-search="true" required>
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
                            <select id="formapago" name="formapago" class="form-control" data-live-search="true"
                                required>
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
                                <input
                                    style="text-align:center;background-color:#E1FEFF ; border-color: #38F0F9; border-radius:10px;"
                                    type="text" class="form-control" id="totaldeposito" name="totaldeposito"
                                    placeholder="Monto recibido" value="0" readonly>
                            </div>

                        </div>

                        <div class="form-group col-lg-6">

                            <label># operación</label>
                            <div class="input-group">
                                <input
                                    style="text-align:center;background-color:#E1FEFF ; border-color: #38F0F9; border-radius:10px;"
                                    type="text" class="form-control" name="noperacion" id="noperacion" maxlength="7"
                                    placeholder="Descuento" value="0" readonly>
                            </div>

                        </div>
                        <div class="form-group col-lg-6">
                            <label class="col-form-label">Monto efectivo</label>
                            <input type="number" step="any" class="form-control" id="montoPagar" name="montoPagar">
                        </div>
                        <div class="form-group col-12">
                            <label for="name" class="col-sm-2 control-label">Descripción <span
                                    class="text-danger">*</span></label>
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

            <div class="modal-footer"
                style="background:#f3f4f6; border-bottom-left-radius:12px; border-bottom-right-radius:12px;">
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
<script src="vistas/js/movimientos.js"></script>