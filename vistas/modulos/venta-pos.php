<link rel="stylesheet" href="<?= $rutas->asset('vistas/css/venta-pos.css') ?>">
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Punto de venta</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Ventas</a></li>
                        <li class="breadcrumb-item active">Punto de venta</li>
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
                    <div class="card" id="header">
                        <div class="card-header">
                            <div class="row">
                                <?php if ($helpers->verificarPermisoCodigo('puntoventa.crear')): ?>
                                    <div class="col-md-1 mt-4">
                                        <button type="button" class="btn btn-outline-primary btn-block" id="btnNuevo"
                                            onclick="mostrarform(true)">
                                            <i class="fa fa-plus"></i>
                                            Nuevo
                                        </button>
                                    </div>
                                <?php endif; ?>
                                <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Fecha Inicio:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                                            value="">
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
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin"
                                            value="">
                                    </div>
                                </div>

                                <!--div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
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
                                </div-->
                                <div class="form-group col-lg-4 col-md-2 col-sm-4 col-xs-12">
                                    <label>Producto:</label>
                                    <select id="idproducto" name="idproducto" class="form-control select2">
                                    </select>
                                </div>
                                <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Estado:</label>

                                    <div class="input-group">
                                        <select id="estado" name="estado" class="form-control select2">
                                            <option value="">Todos</option>
                                            <option value="Aceptado">Aceptado</option>
                                            <option value="Por Enviar">Por Enviar</option>
                                            <option value="Nota Credito">Nota de Crédito</option>
                                            <option value="Rechazado">Rechazado</option>
                                        </select>
                                    </div>
                                </div>
                                <?php if ($helpers->verificarPermisoCodigo('puntoventa.exportar')): ?>
                                    <div class="form-group col-lg-1 col-md-1 col-sm-4 col-xs-12">
                                        <label>Reporte:</label>
                                        <div class="input-group">
                                            <button id="btnExportarExcel" class="btn btn-success btn-md"
                                                title="Exportar a Excel">
                                                <i class="fas fa-file-excel"></i> Exportar
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="col-md-6 d-flex align-items-center mt-3">
                                    <span class="mr-2">Mostrar</span>
                                    <select id="limit" class="form-control" style="width:100px"
                                        onchange="cambiarLimit()">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>

                                    <span class="ml-2">Registros</span>

                                </div>
                                <div class="col-md-6 mt-3">
                                    <input type="text" id="search" class="form-control" placeholder="Buscar...">
                                </div>

                            </div>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body" id="listadoregistros">
                            <table id="tbllistado" class="table table-tailpanel dt-responsive">
                                <thead>
                                    <th>Fecha</th>
                                    <th>Cliente / N° Documento</th>
                                    <th>Número</th>
                                    <th>Total Venta</th>
                                    <th>Forma de pago</th>
                                    <th>Tipo Pago</th>
                                    <th>Estado</th>
                                    <th>Sunat</th>
                                    <th style="text-align: center;"><i class="fa fa-shield" aria-hidden="true"
                                            title="Comprobar estado"></i></th>
                                    <th>Acciones</th>
                                </thead>
                                <tbody id="tbody_ventas">
                                </tbody>
                            </table>
                            <div class="row mt-1">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div id="pagination"></div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <div class="pos-form-shell" id="formularioregistros">

                        <form name="formulario" id="formulario" method="POST">
                            <input type="hidden" name="idventa" id="idventa">

                            <input type="hidden" name="tipo" id="tipo" value="venta">

                            <div class="row">

                                <div class="col-md-6" id="btnAgregarArt">

                                    <!-- <button id="btnCancelar" class="btn btn-danger btn-sm" onclick="cancelarform()" type="button">
                                                <i class="fas fa-window-close"></i> Cancelar
                                            </button> -->

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-lg-6">

                                    <div class="panel-heading">

                                        <div class="card card-outline card-danger">

                                            <div class="card shadow mb-4">
                                                <!-- Encabezado principal -->
                                                <div class="card-header bg-white border-bottom-primary">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="card-title m-0 font-weight-bold text-primary">
                                                            Nueva Venta</h4>
                                                        <small id="fechaActual" class="text-muted"
                                                            style="font-size:11.5px;"></small>
                                                    </div>
                                                </div>

                                                <!-- Botón para desplegar datos -->
                                                <div class="card-header bg-light py-2 d-flex justify-content-end gap-2">
                                                    <div class="ms-auto d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-primary shadow-sm"
                                                            onclick="toggleCollapse(event,this)" data-target="detalle1"
                                                            title="Completa los datos de tu pedido">
                                                            <i class="fas fa-info-circle"></i> Datos cliente
                                                            <i class="fas fa-chevron-down" id="chevron-down"></i>
                                                            <i class="fas fa-chevron-up" id="chevron-up"
                                                                style="display:none;"></i>
                                                        </button>

                                                        <button type="button" class="btn btn-outline-info btn-sm"
                                                            data-toggle="modal" data-target="#modalAcompananteGarante">
                                                            <i class="fas fa-user-friends"></i> Datos
                                                            adicionales
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Cuerpo del formulario (oculto inicialmente) -->
                                                <div class="position-relative">
                                                    <div class="p-2 collapse collapse-section" id="detalle1">
                                                        <div class="card-body">

                                                            <!-- Sección de Personal (oculta) -->
                                                            <div class="form-group mb-3" hidden>
                                                                <label for="idpersonal" class="font-weight-bold">
                                                                    <i class="fas fa-users"></i> Personal
                                                                </label>
                                                                <select id="idpersonal" name="idpersonal"
                                                                    class="form-control select2" required></select>
                                                            </div>

                                                            <!-- Almacén y Cliente (estructura mejorada) -->
                                                            <fieldset class="border p-2 rounded mb-3">
                                                                <legend
                                                                    class="w-auto px-2 small font-weight-bold text-primary">
                                                                    Datos principales</legend>
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-12 mb-2">
                                                                        <label for="idsucursal"
                                                                            class="font-weight-bold">
                                                                            <i class="fas fa-map-marked-alt"></i>
                                                                            Almacén
                                                                        </label>
                                                                        <select id="idsucursal" name="idsucursal"
                                                                            class="form-control"></select>
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-12 mb-2">
                                                                        <div
                                                                            class="d-flex align-items-center justify-content-between">
                                                                            <label for="idcliente"
                                                                                class="font-weight-bold">
                                                                                <i class="fas fa-users"></i> Cliente
                                                                            </label>
                                                                            <div>
                                                                                <a class="text-info"
                                                                                    style="cursor:pointer;"
                                                                                    data-toggle="modal"
                                                                                    data-target="#ModalClientes">
                                                                                    <i class="fa fa-plus"></i> Nuevo
                                                                                </a>
                                                                                <a class="ml-3 text-success"
                                                                                    style="cursor:pointer;"
                                                                                    onclick="verHistorialCliente()">
                                                                                    <i class="fas fa-history"></i>
                                                                                    Historial
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <select id="idcliente" name="idcliente"
                                                                            class="form-control" required></select>
                                                                    </div>
                                                                </div>
                                                            </fieldset>

                                                            <fieldset class="border p-2 rounded mb-3">
                                                                <legend
                                                                    class="w-auto px-2 small font-weight-bold text-primary">
                                                                    Documento</legend>
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-12 mb-2">
                                                                        <label for="tipo_comprobante"
                                                                            class="font-weight-bold">
                                                                            <i class="fas fa-file-alt"></i> Tipo
                                                                            Documento
                                                                        </label>
                                                                        <select class="form-control"
                                                                            name="tipo_comprobante"
                                                                            id="tipo_comprobante">

                                                                        </select>
                                                                        <small id="validate_categoria"
                                                                            class="text-danger d-none">Debe seleccionar
                                                                            documento</small>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-12 mb-2">
                                                                        <label for="serie_comprobante"
                                                                            class="font-weight-bold">
                                                                            <i class="fas fa-store-alt"></i> Serie
                                                                        </label>
                                                                        <input type="text"
                                                                            class="form-control form-control text-center bg-warning"
                                                                            name="serie_comprobante"
                                                                            id="serie_comprobante" maxlength="7"
                                                                            placeholder="Serie" readonly>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-12 mb-2">
                                                                        <label for="num_comprobante"
                                                                            class="font-weight-bold">
                                                                            <i class="fas fa-file-alt"></i> Nº Orden
                                                                        </label>
                                                                        <input type="text"
                                                                            class="form-control form-control text-center bg-warning"
                                                                            name="num_comprobante" id="num_comprobante"
                                                                            maxlength="10" placeholder="Número"
                                                                            readonly>
                                                                    </div>
                                                                </div>
                                                            </fieldset>

                                                            <!-- Fecha (oculta) -->
                                                            <!-- Fecha -->
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">
                                                                    <i class="far fa-calendar-alt"></i> Fecha
                                                                </label>
                                                                <input type="date" class="form-control text-center"
                                                                    name="fecha" id="fecha"
                                                                    value="<?php echo date('Y-m-d'); ?>" <?php if ($_SESSION['cargo'] !== 'Administrador'): ?>
                                                                    min="<?php echo date('Y-m-d', strtotime('-1 day')); ?>"
                                                                    max="<?php echo date('Y-m-d'); ?>" <?php endif; ?>
                                                                    required>
                                                                <?php if ($_SESSION['cargo'] !== 'Administrador'): ?>
                                                                    <small class="text-muted">Solo puedes seleccionar hoy o
                                                                        un día atrás</small>
                                                                <?php endif; ?>
                                                            </div>

                                                            <!-- Importar Cotizaciones -->
                                                            <div class="form-group mb-3">
                                                                <label for="comprobanteReferencia"
                                                                    class="font-weight-bold">
                                                                    <i class="fas fa-money-bill-alt"></i> Importar
                                                                    Cotizaciones
                                                                </label>
                                                                <select id="comprobanteReferencia"
                                                                    name="comprobanteReferencia"
                                                                    class="form-control select2"></select>
                                                            </div>

                                                            <!-- Observaciones -->
                                                            <div class="form-group">
                                                                <label for="observaciones" class="font-weight-bold">
                                                                    <i class="fas fa-file-alt"></i> Observaciones
                                                                </label>
                                                                <textarea class="form-control" name="observaciones"
                                                                    id="observaciones" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="">Detalle de la venta</label>
                                                <input type="hidden" name="idcaja" id="idcaja" style="right: -15px;">
                                                <div id="detalles-wrapper">
                                                    <table id="detalles" class="table table-striped table-hover">
                                                        <thead class="bg-info">
                                                            <th style="width: 250px;">Producto</th>
                                                            <th>UM</th>
                                                            <th>Precio</th>
                                                            <th>Blz</th>
                                                            <th>Cantidad</th>
                                                            <th>Descuento</th>
                                                            <th>Subtotal</th>
                                                        </thead>
                                                        <tfoot>
                                                        </tfoot>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- IMPUESTOS Y TOTAL -->
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="border rounded shadow-sm overflow-hidden">

                                                    <!-- Fila Impuesto -->
                                                    <div class="d-flex">
                                                        <div class="col-9 py-2 fw-bold text-right">
                                                            Impuesto
                                                        </div>
                                                        <div class="col-3 text-right py-2">
                                                            <span class="fw-bold fs-4">S/. </span>
                                                            <span id="sp-impuesto" class="fw-bold fs-4">0.00</span>
                                                            <input type="hidden" name="impuesto" id="impuesto">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex">
                                                        <div class="col-9 py-2 fw-bold text-right">
                                                            Subtotal
                                                        </div>
                                                        <div class="col-3 text-right py-2">
                                                            <span class="fw-bold fs-4">S/. </span>
                                                            <span id="sp-subtotal" class="fw-bold fs-4">0.00</span>
                                                            <input type="hidden" name="subtotal" id="subtotal">
                                                        </div>
                                                    </div>

                                                    <!-- Fila Total -->
                                                    <div class="d-flex">
                                                        <div class="col-9 py-2 fw-bold text-right">
                                                            Total
                                                        </div>
                                                        <div class="col-3 text-right py-2">
                                                            <span class="fw-bold fs-4">S/. </span>
                                                            <span id="total" class="fw-bold fs-4">0.00</span>
                                                            <input type="hidden" name="total_venta" id="total_venta">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <!-- MÉTODO DE PAGO -->
                                            <!--//***************************************************************************//-->

                                            <div class="card-body p-2" id="datosgenerales2">
                                                <label for="">Opciones de pago</label>
                                                <div class="row col-md-12">
                                                    <div class="col-md-2">
                                                        <label style=" font-size: 11px;">¿Crédito?</label>
                                                        <select id="tipopago" name="tipopago" class="form-control"
                                                            data-live-search="true" required>
                                                            <option value="No">No</option>
                                                            <option value="Si">Sí</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label style="font-size: 11px;">Total Depósito</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="totaldeposito"
                                                                name="totaldeposito" placeholder="Monto recibido"
                                                                value="0" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2" hidden>
                                                        <label style="font-size: 11px;">Descuento:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" name="porcentaje" id="porcentaje"
                                                                maxlength="7" placeholder="Descuento"
                                                                onkeyup="calcularPorcentaje();" disabled="disabled">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label style="font-size: 11px;">Total efectivo</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="totalrecibido"
                                                                name="totalrecibido" placeholder="Monto recibido"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label style="font-size: 11px;">Vuelto S/.</label>
                                                        <div class="d-flex">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="vuelto" name="vuelto"
                                                                readonly="">

                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label style="font-size: 11px;"></label>
                                                        <div class="d-flex">
                                                            <button type="button" class="btn btn-primary btn-sm ms-4"
                                                                id="addPago">Agregar </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row col-md-12">
                                                    <div class="col-md-12">
                                                        <label style="font-size: 11px;">Pagos Mixtos:</label>
                                                        <div id="pagosMixtosContainer">
                                                            <div class="row mb-2 pagoItem">
                                                                <div class="col-md-3">
                                                                    <select class="form-control metodoPago"
                                                                        name="metodo_pago[]">
                                                                        <option value="Efectivo">Efectivo
                                                                        </option>
                                                                        <option value="Transferencia">
                                                                            Transferencia bancaria</option>
                                                                        <option value="Tarjeta">Tarjeta POS
                                                                        </option>
                                                                        <option value="Deposito">Depósito
                                                                        </option>
                                                                        <option value="Yape">Yape</option>
                                                                        <option value="Plin">Plin</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <input type="text" class="form-control montoPago"
                                                                        name="monto_pago[]" placeholder="Monto">
                                                                    <input type="hidden" class="montoRealPago"
                                                                        name="monto_real_pago[]" value="0">
                                                                </div>
                                                                <div class="col-md-2 bancoContainer"
                                                                    style="display:none;">
                                                                    <select class="form-control bancoPago"
                                                                        name="banco_pago[]">
                                                                        <option value="">Seleccione banco</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <input type="text" class="form-control nroOperacion"
                                                                        name="nroOperacion_pago[]"
                                                                        placeholder="N° Operación">
                                                                </div>
                                                                <div class="col-md-3 fechaContainer"
                                                                    style="display:none;">
                                                                    <input type="date"
                                                                        class="form-control fechaDeposito"
                                                                        name="fecha_deposito_pago[]"
                                                                        placeholder="Fecha">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm removePago"><i
                                                                            class="fa fa-trash"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr style="border: 1 px solid #007bff;" />
                                                <div class="row col-md-12 mt-4">
                                                    <div class="form-group col-lg-3" id="n0">
                                                        <label>Frecuencia:</label>
                                                        <select name="input_frecuencia" id="input_frecuencia"
                                                            class="form-control" placeholder="Frecuencia">
                                                            <option value="" selected hidden>Seleccionar...
                                                            </option>
                                                            <option value="1">Diario</option>
                                                            <option value="2">Semanal</option>
                                                            <option value="3">Quincenal</option>
                                                            <option value="4">Mensual</option>
                                                            <option value="5">Bimestral</option>
                                                            <option value="6">Trimestral</option>
                                                            <option value="7">Semestral</option>
                                                            <option value="8">Anual</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-lg-3" id="n1">
                                                        <label>N° de cuotas:</label>
                                                        <select name="input_cuotas" id="input_cuotas"
                                                            class="form-control" placeholder="Cuotas">
                                                            <option value="" selected hidden>Seleccionar...
                                                            </option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                            <option value="8">8</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-lg-3" id="n2">
                                                        <label style="font-size: 11px;">N° meses:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="numeroMeses"
                                                                name="numeroMeses">
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-lg-3" id="n3">
                                                        <label style="font-size: 11px;">Fecha Inicio:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="date"
                                                                class="form-control" id="fechaOperacion"
                                                                name="fechaOperacion"
                                                                value="<?php echo date("Y-m-d"); ?>">
                                                        </div>
                                                    </div>

                                                    <!--<div class="form-group col-lg-2" style="display: none;" id="n1">

                                                                    <label style="font-size: 11px;">Fecha de Pago:</label>
                                                                    <div class="input-group">
                                                                        <input style="text-align:center" type="date" class="form-control" id="fechaOperacion" name="fechaOperacion" value="<?php echo date("Y-m-d"); ?>">
                                                                    </div>

                                                                </div>-->

                                                    <div class="form-group col-lg-2" style="display: none;" id="n2">

                                                        <label style="font-size: 11px;">Monto Pagado:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="montoPagado" name="montoPagado"
                                                                value="0" onkeyup="calcularDeuda();">
                                                        </div>

                                                    </div>

                                                    <div class="form-group col-lg-3" id="panelPorcentajeInicial"
                                                        style="display:none;">
                                                        <label style="font-size: 11px;">Porcentaje de inicial:</label>
                                                        <div class="alert alert-info py-2 mb-0"
                                                            id="porcentajeInicial">0.00%</div>
                                                    </div>

                                                    <div class="form-group col-lg-2" style="display: none;" id="n4">

                                                        <label style="font-size: 11px;">Monto Deuda:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="montoDeuda" name="montoDeuda"
                                                                readonly="">
                                                        </div>

                                                    </div>

                                                    <div class="form-group col-lg-2" style="display: none;" id="n5">

                                                        <label style="font-size: 11px;">Interes %:</label>
                                                        <?php if ($helpers->verificarPermisoCodigo('puntoventa.editarinteres')): ?>
                                                            <input style="border-color: #FFC7BB; text-align:center"
                                                                type="text" class="form-control" id="inputInteres"
                                                                name="inputInteres" value="0">
                                                        <?php endif; ?>
                                                        <?php if (!$helpers->verificarPermisoCodigo('puntoventa.editarinteres')): ?>
                                                            <input style="border-color: #FFC7BB; text-align:center"
                                                                type="text" class="form-control" id="inputInteres"
                                                                name="inputInteres" value="0" readonly>
                                                        <?php endif; ?>

                                                    </div>

                                                    <div class="form-group col-lg-1" style="display: none;" id="b1">
                                                        <br>
                                                        <button type="button" class="btn btn-success"
                                                            id="calcular_cuotas">Calcular</button>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="modalAcompananteGarante" tabindex="-1"
                                                    role="dialog" aria-labelledby="modalAcompananteGaranteLabel"
                                                    aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="modalAcompananteGaranteLabel">
                                                                    Datos de
                                                                    acompañante y garante</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="text-muted mb-3">Si el cliente tiene
                                                                    un acompañante o garante, por favor ingresa
                                                                    sus datos aquí.</p>
                                                                <div class="form-group">
                                                                    <label for="idtipoacompanante">Tipo de
                                                                        acompañante</label>
                                                                    <select class="form-control" id="idtipoacompanante"
                                                                        name="idtipoacompanante">
                                                                    </select>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="idacompanante">Acompañante</label>
                                                                    <select class="form-control select2"
                                                                        id="idacompanante"
                                                                        name="idacompanante"></select>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="idgarante">Garante</label>
                                                                    <select class="form-control select2" id="idgarante"
                                                                        name="idgarante"></select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cerrar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--div class="row col-md-12 mt-4" style="display: none;" id="n6">
                                                    <div class="col-lg-2">
                                                        <label style="font-size: 11px;"># de Operación:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="nroOperacion"
                                                                name="nroOperacion">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2" style="display: none;" id="fechadeposito">
                                                        <label style="font-size: 11px;">Fecha Depósito:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="date"
                                                                class="form-control" id="fechaDepostivo"
                                                                name="fechaDepostivo">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2" style="display: none;" id="banco">
                                                        <label style="font-size: 11px;">Banco:</label>
                                                        <select id="banco" name="banco" class="form-control"
                                                            data-live-search="true" title="Seleccione Banco">
                                                            <option value="BCP">BCP</option>
                                                            <option value="BBVA">BBVA</option>
                                                            <option value="INTERBANK">INTERBANK</option>
                                                            <option value="OTRO">OTRO</option>
                                                        </select>
                                                    </div>
                                                </div-->
                                                <div class="row col-md-12 mt-4" id="panel1" style="display: none;">
                                                    <table class="table" style="width:100%;">
                                                        <thead
                                                            style="display: table; width: 100%; table-layout: fixed;">
                                                            <tr>
                                                                <th>Fecha de pagos</th>
                                                                <th>Monto</th>
                                                                <th>Interés</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>

                                                        <style>
                                                            #datafechas {
                                                                display: block;
                                                                max-height: 300px;
                                                                overflow-y: auto;
                                                                width: 100%;
                                                            }

                                                            #datafechas tr {
                                                                display: table;
                                                                width: 100%;
                                                                table-layout: fixed;
                                                            }

                                                            #datafechas td {
                                                                width: 25%;
                                                            }
                                                        </style>

                                                        <tbody id="datafechas">
                                                            <tr>
                                                                <td colspan="4" class="text-center">
                                                                    No se han calculado las fechas de pago
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <table class="table" style="width:100%;">
                                                        <tbody id="resumenCuotas"></tbody>
                                                    </table>
                                                </div>
                                                <div id="fechasHiddenContainer" style="display:none;"></div>
                                            </div>
                                            <!--//***************************************************************************//-->
                                        </div>

                                        <div class="card-footer">

                                            <div class="col-md-6">
                                                <button type="button" class="btn-flotante" id="btnGuardar">
                                                    <i class="fas fa-shopping-cart"></i> Realizar Venta
                                                </button>
                                                <button id="btnCancelar" class="btn-flotante2" onclick="cancelarform()"
                                                    type="button">
                                                    <i class="fas fa-window-close"></i> Cancelar
                                                </button>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <!-- INICIO DE TABLE PRODUCTO Y SERVICIOS-->
                                <div class="col-lg-6 hidden-md hidden-sm hidden-xs">
                                    <div class="card  card-tabs">
                                        <div class="card-header p-0 pt-1">
                                            <div class="card-header p-0 pt-1">
                                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                                    <li class="nav-item" onclick="selectTab(1)">
                                                        <a class="nav-link active" id="custom-tabs-two-home-tab"
                                                            data-toggle="pill" href="#custom-tabs-two-home" role="tab"
                                                            aria-controls="custom-tabs-two-home"
                                                            aria-selected="true">Producto</a>
                                                    </li>
                                                    <li class="nav-item" onclick="selectTab(2)">
                                                        <a class="nav-link" id="custom-tabs-two-profile-tab"
                                                            data-toggle="pill" href="#custom-tabs-two-profile"
                                                            role="tab" aria-controls="custom-tabs-two-profile"
                                                            aria-selected="false">Servicio</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="card-body" style="margin-top: -35px; overflow-x: auto;">
                                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                                    <br>
                                                    <style>
                                                        .active-search {
                                                            background: #007bff;
                                                            color: white;

                                                        }

                                                        .active-search:hover {
                                                            background: #007bff;
                                                            color: white;
                                                        }
                                                    </style>
                                                    <div style="display: flex; align-items: center; gap: 8px;"
                                                        id="div_search">
                                                        <button type="button" class="btn btn-default"
                                                            id="btn_text_search" onclick="activeSearch(1)"><span
                                                                class="fas fa-keyboard"></span></button>
                                                        <button type="button" class="btn btn-default"
                                                            id="btn_barcode_search" onclick="activeSearch(2)"><span
                                                                class="fas fa-barcode"></span></button>
                                                        <input type="search" placeholder="Buscar producto"
                                                            class="form-control" id="searchProductos">
                                                        <button type="button"
                                                            class="btn btn-default mb-3 d-xl-none d-lg-none btnAgregarProducto">
                                                            Agregar producto
                                                        </button>
                                                    </div>
                                                    <div class="tab-pane fade show active" id="custom-tabs-two-home"
                                                        role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                                                        <table id="tblarticulos"
                                                            class="table table-striped table-responsive-lg"
                                                            width="100%">
                                                            <thead class="bg-info">
                                                                <th>Op</th>
                                                                <th>Codigo</th>
                                                                <th>Nombre</th>
                                                                <th>Stock</th>
                                                                <th>P Venta</th>
                                                                <th>Color</th>
                                                            </thead>
                                                            <tbody id="tbody_articulos">
                                                            </tbody>
                                                        </table>
                                                        <div class="col-md-6"></div>
                                                        <div class="col-md-6">
                                                            <div id="paginationProductos"></div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="custom-tabs-two-profile"
                                                        role="tabpanel" aria-labelledby="custom-tabs-two-profile-tab">
                                                        <table id="tblarticulos2"
                                                            class="table table-striped table-responsive-lg"
                                                            width="100%">
                                                            <thead class="bg-info">
                                                                <th>Op</th>
                                                                <th width="200px">Nombre</th>
                                                                <th style="text-align: center;">Stock</th>
                                                                <th>P Venta</th>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                            <tfoot>
                                                                <th>Op</th>
                                                                <th>Nombre</th>
                                                                <th>Stock</th>
                                                                <th>P Venta</th>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- FIN DE TABLE PRODUCTO Y SERVICIOS-->
                            </div>
                        </form>
                    </div>

                    <div id="floating-history">
                        <div id="floating-header">
                            <span><i class="fas fa-shopping-bag mr-2"></i> Historial de Cliente</span>
                            <button type="button" class="btn btn-sm text-white"
                                onclick="$('#floating-history').fadeOut()" title="Cerrar">
                                <i class="fas fa-times fa-lg"></i>
                            </button>
                        </div>

                        <div class="search-box-historial">
                            <div class="input-group input-group-sm">
                                <input type="text" id="inputBusquedaHistorial" class="form-control"
                                    placeholder="Escribe para buscar producto...">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-white border-left-0"><i
                                            class="fas fa-search text-muted"></i></span>
                                </div>
                            </div>
                        </div>

                        <div id="floating-body">
                            <table class="table table-hover table-historial mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center" width="50">Cant.</th>
                                        <th class="text-right" width="70">Precio</th>
                                        <th class="text-right" width="60">Desc.</th>
                                        <th class="text-right" width="70">Subtotal</th>
                                        <th class="text-center" width="80">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody id="body_historial_flotante">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-body row" id="aperturarcaja">
                        <div class="col-sm-4" style="margin: 0 auto;">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card shadow" style="margin-top: -10px;">
                                        <div class="card-body">
                                            <h1 class="text-center">APERTURAR CAJA</h1>
                                            <div class="col-md-12" style="margin-bottom: 10px;">
                                                <div class="scrollmenu" for="selCategoriaReg"
                                                    style="background-color: transparent;">
                                                </div>
                                            </div>
                                            <form action="" id="formularioappcaja">
                                                <div class="col-md-12 md-1">
                                                    <div class="form-group">
                                                        <label for="">Caja</label>
                                                        <select class="form-control" name="cajas" id="cajas" required>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 md-1">
                                                    <div class="form-group">
                                                        <label for="">Efectivo</label>
                                                        <input step="0.001" type="number" class="form-control"
                                                            name="monto_apertura" id="monto_apertura" required>
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
                    <!-- /.card -->
                    <!-- /.col -->
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<div class="modal fade" id="myModal2">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Lista de ventas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">

                    </div>

                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">

                    </div>

                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <label>Almacén:</label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-store-alt"></i>
                                </span>
                            </div>
                            <select id="idsucursalVentas" name="idsucursalVentas" class="form-control" readonly>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <label>Estado:</label>

                        <div class="input-group">
                            <select id="estadoVentas" name="estadoVentas" class="form-control select2" required>
                                <option value="">Todos</option>
                                <option value="Aceptado">Aceptado</option>
                                <option value="Por Enviar">Por Enviar</option>
                                <option value="Nota Credito">Nota de Crédito</option>
                                <option value="Rechazado">Rechazado</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="tale-resposive">
                    <table id="tbllistadoVentas" class="table table-striped">
                        <thead>
                            <th>ID</th>
                            <th>Cliente / N° Documento</th>
                            <th>Sucursal</th>
                            <th>Número</th>
                            <th>Total Venta</th>
                            <th>Tipo Pago</th>
                            <th>Estado</th>
                            <th width="70px;">Sunat</th>
                            <th style="text-align: center;"><i class="fa fa-shield" aria-hidden="true"
                                    title="Comprobar estado"></i></th>
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
                            <th>Tipo Pago</th>
                            <th>Estado</th>
                            <th>Sunat</th>
                            <th></th>
                            <th>Acciones</th>
                        </tfoot>
                    </table>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <div></div>
                <button type="button" class="btn btn-primary" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h4 class="modal-title"><i class="fas fa-cash-register"></i> Caja Chica - Movimiento</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form class="form-horizontal" role="form" name="formularioMovimiento" id="formularioMovimiento"
                method="POST">
                <input type="hidden" name="idmovimiento" id="idmovimiento">

                <div class="modal-body" style="background-color: #f8f9fa; border-radius: 10px;">
                    <!-- Fila de selección de tipo de movimiento (Ingresos/Egresos) -->
                    <div class="row text-center">
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

                    <!-- Selección de almacén y personal -->
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="name" class="control-label">Almacen <span class="text-danger">*</span></label>
                            <select id="idsucursal02" name="idsucursal02" class="form-control select2"
                                data-live-search="true">
                            </select>
                        </div>
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
                            <select id="idpersonal02" name="idpersonal02" class="form-control select2"></select>
                        </div>
                    </div>

                    <!-- Detalles de pago y monto -->
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="formapago" class="col-form-label">Forma de pago:</label>
                            <select id="formapago" name="formapago" class="form-control" required>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia bancaria</option>
                                <option value="Tarjeta">Tarjeta POS</option>
                                <option value="Deposito">Depósito</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="totaldeposito" class="col-form-label">Total Monto tarjeta S/.</label>
                            <input
                                style="text-align:center; background-color:#E1FEFF; border-color: #38F0F9; border-radius:10px;"
                                type="text" class="form-control" id="totaldeposito" name="totaldeposito" value="0"
                                readonly>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="noperacion" class="col-form-label"># Operación:</label>
                            <input
                                style="text-align:center; background-color:#E1FEFF; border-color: #38F0F9; border-radius:10px;"
                                type="text" class="form-control" name="noperacion" id="noperacion" maxlength="7"
                                value="0" readonly>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="montoPagar" class="col-form-label">Monto:</label>
                            <input type="number" step="any" class="form-control" id="montoPagar" name="montoPagar"
                                required>
                        </div>
                    </div>

                    <!-- Descripción del movimiento -->
                    <div class="form-group">
                        <label for="descripcion" class="col-form-label">Descripción:</label>
                        <input type="text" class="form-control" name="descripcion" id="descripcion"
                            placeholder="Descripción del movimiento (opcional)">
                    </div>
                </div>

                <!-- Footer con botones -->
                <div class="modal-footer" style="background-color: #f1f1f1;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i>
                        Cerrar</button>
                    <button class="btn btn-success" type="submit" id="btnGuardar"><i class="fas fa-save"></i>
                        Guardar Movimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="getCodeModal22" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-receipt mr-2"></i>Detalle de Venta
                </h5>
                <button type="button" class="close text-white" onclick="cancelarmodalDetalle()">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- Información General -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <strong><i class="fas fa-info-circle"></i> Información General</strong>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Cliente</small>
                                <h5 id="cliente" class="font-weight-bold mb-0"></h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Vendedor</small>
                                <h5 id="personalm" class="mb-0"></h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Fecha</small>
                                <h5 id="fecha_hora" class="mb-0"></h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Comprobante</small>
                                <h5 id="tipo_comprobantem" class="mb-0"></h5>
                            </div>

                            <div class="col-md-8 mb-3">
                                <small class="text-muted">Correlativo</small>
                                <h5 id="correlativo" class="mb-0"></h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Venta crédito</small> <br>
                                <span id="formapagom" class="badge badge-info p-2"></span>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Inicial</small><br>
                                <span id="pagoinicial"></span>

                                <button id="imprimirComp" type="button" class="btn btn-dark btn-sm"
                                    title="Imprimir comprobante de pago">
                                    <i class="fa fa-print"></i>
                                </button>

                                <button id="ajuntarComp" type="button" class="btn btn-success btn-sm"
                                    title="Adjuntar comprobante de pago">
                                    <i class="fa fa-paperclip"></i>
                                </button>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Saldo</small> <br>
                                <span id="pagocredito"></span>
                            </div>


                            <div class="col-12">
                                <small class="text-muted">Observaciones</small>
                                <div id="observaciones" class="border rounded p-3 bg-light text-secondary">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Detalle -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <strong><i class="fas fa-shopping-cart"></i> Productos</strong>
                    </div>

                    <div class="card-body p-0">
                        <table id="detallesm" class="table table-hover table-striped mb-0" style="width: 100%;">

                        </table>
                    </div>
                </div>

                <!-- Totales -->
                <div class="card mt-3 shadow-sm">
                    <div class="card-body">

                        <div class="row text-center">

                            <div class="col-md-4">
                                <small class="text-muted d-block">Subtotal</small>
                                <h4 id="subtotalm" class="mb-0 text-primary"></h4>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">IGV</small>
                                <h4 id="impuestom" class="mb-0 text-warning"></h4>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">Total</small>
                                <h3 id="totalm" class="mb-0 text-success font-weight-bold"></h3>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="cancelarmodalDetalle()">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>

        </div>
    </div>
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
                                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100"
                                    placeholder="Nombre del proveedor" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Tipo Documento:</label>
                                <select class="form-control select-picker" name="tipo_documento" id="tipo_documento"
                                    required>
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
                                <input type="text" class="form-control" name="num_documento" id="num_documento"
                                    maxlength="20" placeholder="Documento">
                                <div class="input-group-append">
                                    <span class="input-group-text" style="cursor: pointer;" id="Buscar_Cliente"
                                        onclick="BuscarCliente()" title="Buscar Cliente" type="button"><i
                                            class="fa fa-search"></i></span>
                                    <span class="input-group-text" id="cargando" title="Cargando" type="button"
                                        style="display: none;"><i><img src="files/plantilla/cargando.gif"
                                                width="15px"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Dirección:</label>
                                <input type="text" class="form-control" name="direccion" id="direccion" maxlength="70"
                                    placeholder="Dirección">
                                Estado:<label for="" id="estado2">-</label>
                                Condición:<label for="" id="condicion">-</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Teléfono:</label>
                                <input type="text" class="form-control" name="telefono" id="telefono" maxlength="20"
                                    placeholder="Teléfono">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Email:</label>
                                <input type="email" class="form-control" name="email" id="email" maxlength="50"
                                    placeholder="Email">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" onclick="limpiarCliente()" class="btn btn-default"
                        data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" type="submit" id="">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- Modal para registrar número de celular -->
<div class="modal fade" id="modalCelular" tabindex="-1" role="dialog" aria-labelledby="modalCelularLabel"
    aria-hidden="true">
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
                <input type="text" name="numeroCelular" id="numeroCelular" class="form-control"
                    placeholder="Ingrese número de celular">
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


<!-- Modal Detalle Producto -->
<div class="modal fade" id="modalDetalleProducto" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel">Detalle del Producto</h5>
                <button type="button" class="btn btn-sm btn-danger ml-2" id="btnCerrarModalProducto">
                    Cerrar
                </button>
            </div>

            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" id="detalleProductoTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-imagen-tab" data-toggle="tab" href="#tab-imagen" role="tab"
                            aria-controls="tab-imagen" aria-selected="true">
                            Imagen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-detalles-tab" data-toggle="tab" href="#tab-detalles" role="tab"
                            aria-controls="tab-detalles" aria-selected="false">
                            Detalles del producto
                        </a>
                    </li>
                </ul>

                <!-- Contenido de las tabs -->
                <div class="tab-content" id="detalleProductoTabsContent">
                    <!-- TAB 1: Imagen -->
                    <div class="tab-pane fade show active text-center" id="tab-imagen" role="tabpanel"
                        aria-labelledby="tab-imagen-tab">
                        <div class="d-flex justify-content-center align-items-center border rounded shadow mb-3"
                            style="height: 600px; background-color: #f8f9fa;">
                            <img id="detalleImagenProducto" src="" alt="Producto"
                                style="max-height: 100%; max-width: 100%; object-fit: contain;">
                        </div>
                    </div>

                    <!-- TAB 2: Detalles -->
                    <div class="tab-pane fade" id="tab-detalles" role="tabpanel" aria-labelledby="tab-detalles-tab">
                        <div class="row" id="detalleProductoContenido">
                            <!-- Contenido dinámico generado por JS -->
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5 class="text-primary">Configuraciones del producto</h5>
                                <div class="accordion" id="acordeonConfiguraciones">
                                    <!-- Aquí irá el contenido generado por AJAX -->
                                    <div id="detallePreciosAdicionales">
                                        <i>Cargando...</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /.modal-body -->
        </div>
    </div>
</div>

<div class="modal fade" id="ModalPrecios">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Lista de precios</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="limpiarCliente()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive" id="tabla-precios">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
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
                <button class="btn btn-primary pull-right" type="button" onclick="sinComprobante()">SIN
                    COMPROBANTE</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<!-- Modal -->
<div class="modal fade" id="modalListarPagos">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAjuntarCompLabel">Adjuntar comprobante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Metodo</th>
                                <th>Documento</th>
                                <th>Monto</th>
                                <th>N° operación</th>
                                <th>Banco</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="listaComprobantes">
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No hay comprobantes adjuntos
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" onclick="cerarrAjuntarComp()">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAjuntarComp">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAjuntarCompLabel">Adjuntar comprobante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formAdjuntarComp" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="idventapago">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="comprobanteAdjunto">Documento</label>
                                <input type="file" class="form-control" id="comprobanteAdjunto" name="comprobante"
                                    accept="image/*,.pdf">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Adjuntar
                    </button>

                    <button type="button" class="btn btn-secondary" onclick="cerarrAjuntarComp()">
                        Cerrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("click", cerrarCollapse);

    function toggleCollapse(e, btn) {
        e.stopPropagation();

        const panel = document.getElementById(btn.dataset.target);
        panel.classList.toggle("show");

        const icon = btn.querySelector("i");
        icon.classList.toggle("fa-chevron-down");
        icon.classList.toggle("fa-chevron-up");
    }

    function cerrarCollapse(e) {

        // Si el click fue dentro de Select2 no cerrar
        if (
            e.target.closest(".select2-container") ||
            e.target.closest(".select2-dropdown")
        ) {
            return;
        }


        document.querySelectorAll(".collapse.show").forEach(panel => {

            const btn = document.querySelector(`[data-target="${panel.id}"]`);

            if (panel.contains(e.target) || (btn && btn.contains(e.target))) {
                return;
            }

            panel.classList.remove("show");

            if (btn) {
                const icon = btn.querySelector("i");
                icon.classList.remove("fa-chevron-up");
                icon.classList.add("fa-chevron-down");
            }

        });

    }
</script>
<script type="text/javascript" src="<?= $rutas->asset('vistas/js/venta-pos.js') ?>"></script>