<?php
// Zona horaria
date_default_timezone_set('America/Lima');
?>
<!-- Content Wrapper. Contains page content -->
<style type="text/css">
    /* ================== OPTIMIZACIONES Y ESTÉTICA ================== */

    /* Encabezados fijos en tablas con scroll interno */
    #tbllistado thead th,
    #tblarticulos thead th,
    #tblarticulos2 thead th,
    #detalles thead th,
    #detallesm thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #007bff;
        color: #fff;
        text-align: center;
    }


    /* Hover en filas */
    #tbllistado tbody tr:hover,
    #tblarticulos tbody tr:hover,
    #tblarticulos2 tbody tr:hover,
    #detalles tbody tr:hover,
    #detallesm tbody tr:hover {
        background-color: #f0f8ff;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    /* Feedback visual de validación */
    input:valid,
    select:valid,
    textarea:valid {
        border: 1px solid #28a745 !important;
    }

    input:invalid,
    select:invalid,
    textarea:invalid {
        border: 1px solid #dc3545 !important;
    }

    /* Botones flotantes con animación pulsante */
    .btn-flotante,
    .btn-flotante2 {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1)
        }

        50% {
            transform: scale(1.05)
        }

        100% {
            transform: scale(1)
        }
    }

    .collapse-section {
        position: absolute;
        top: 90%;
        left: 0;
        width: 100%;
        z-index: 1050;
        border: rgba(0, 0, 0, .15) solid 1px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .15);

        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);

        transition: all .25s ease;
    }

    .collapse-section.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .content-wrapper {
            padding: 10px;
        }

        .btn-flotante,
        .btn-flotante2 {
            font-size: 12px;
            padding: 10px 15px;
        }

        table th,
        table td {
            font-size: 11px;
        }
    }

    /* ================== TUS ESTILOS (ajustados) ================== */
    /* Establecer una altura máxima para la tabla */
    #detalles {
        max-height: 300px;
        overflow-y: auto;
        display: block;
        width: 100%;
    }

    .btn-flotante {
        font-size: 16px;
        text-transform: uppercase;
        font-weight: bold;
        color: #ffffff;
        border-radius: 5px;
        letter-spacing: 2px;
        background-color: #008000;
        padding: 18px 30px;
        position: fixed;
        bottom: 20px;
        right: 20px;
        transition: all 300ms ease 0ms;
        box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
        z-index: 99;
    }

    .btn-flotante:hover {
        background-color: #2c2fa5;
        box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.3);
        transform: translateY(-7px);
    }

    @media only screen and (max-width: 600px) {
        .btn-flotante {
            font-size: 14px;
            padding: 12px 20px;
            bottom: 20px;
            right: 20px;
        }
    }

    .btn-flotante2 {
        font-size: 16px;
        text-transform: uppercase;
        font-weight: bold;
        color: #ffffff;
        border-radius: 5px;
        letter-spacing: 2px;
        background-color: red;
        padding: 18px 30px;
        position: fixed;
        bottom: 20px;
        right: 325px;
        transition: all 300ms ease 0ms;
        box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
        z-index: 99;
    }

    .btn-flotante2:hover {
        background-color: #2c2fa5;
        box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.3);
        transform: translateY(-7px);
    }

    @media only screen and (max-width: 600px) {
        .btn-flotante2 {
            font-size: 14px;
            padding: 12px 20px;
            bottom: 20px;
            right: 290px;
        }
    }

    .orange-cart {
        color: orange;
    }

    #datosgenerales2 {
        display: none;
        transition: display 0.3s ease;
    }

    /* Reducir tamaño de texto en tabla */
    #tblarticulos,
    #tblarticulos2 {
        font-size: 12px;
    }

    #tblarticulos th,
    #tblarticulos td,
    #tblarticulos2 th,
    #tblarticulos2 td {
        padding: 4px;
        white-space: nowrap;
        text-align: center;
    }

    #detalles th,
    #detalles td {
        padding: 4px;
        white-space: nowrap;
        text-align: center;
    }

    .img-thumbnail {
        border-radius: 5px;
    }

    .btn-xs {
        padding: 2px 5px;
        font-size: 10px;
    }

    .table-responsive {
        overflow-x: auto;
        max-width: 100%;
    }

    /* Caja total destacada */
    .total-box {
        padding: 10px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .total-box span {
        font-size: 28px;
        font-weight: bold;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cotizaciones</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Cuentas por Cobrar</li>
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
                            <h3 class="card-title">Gestión de Cotizaciones</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-primary btn-xs" id="btnNuevo"
                                        onclick="mostrarform(true)"><i class="fa fa-plus"></i> Nuevo</button>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->

                        <!-- ============== LISTADO ============== -->
                        <div class="card-body" id="listadoregistros">
                            <div class="row">
                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label>Fecha Inicio:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i
                                                    class="far fa-calendar-alt"></i></span></div>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                                            value="<?php echo date('Y-m-01'); ?>">
                                    </div>
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label>Fecha Fin:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i
                                                    class="far fa-calendar-alt"></i></span></div>
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin"
                                            value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <label>Almacén:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text"><i
                                                    class="fas fa-store-alt"></i></span></div>
                                        <select id="idsucursal2" name="idsucursal2" class="form-control"></select>
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
                                <tbody></tbody>
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
                        <!-- /.card-body listado -->
                    </div>
                    <!-- /.card -->
                    <!-- ============== FORMULARIO ============== -->
                    <div id="formularioregistros">
                        <form name="formulario" id="formulario" method="POST">
                            <br>
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <div class="panel-heading" style="border-bottom: 1px dashed hsla(0,0%,80%,.329)">
                                        <div class="card card-outline card-danger">
                                            <div class="card-header d-flex justify-content-between align-items-center">

                                                <div>
                                                    <h5 class="mb-0">
                                                        <i class="fas fa-file-invoice"></i>
                                                        Nueva Cotización
                                                    </h5>

                                                    <small>
                                                        Complete la información del cliente y los productos
                                                    </small>
                                                </div>

                                                <div class="text-end">
                                                    <span id="fechaActual"></span>
                                                </div>

                                            </div>
                                            <br>
                                            <div class="row align-items-center mb-3">

                                                <div class="col-md-4">
                                                    <button type="button"
                                                        class="btn btn-primary btn-lg w-100 shadow-sm d-flex justify-content-between align-items-center"
                                                        onclick="toggleCollapse('datosgenerales', this)">

                                                        <span>
                                                            <i class="fas fa-user-edit mr-2"></i>
                                                            Datos Generales
                                                        </span>

                                                        <i class="fas fa-chevron-down" id="iconDatos"></i>
                                                    </button>
                                                </div>

                                                <div class="col-md-8">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-body py-2">

                                                            <div
                                                                class="d-flex justify-content-between align-items-center">

                                                                <div>
                                                                    <small class="text-muted d-block">
                                                                        TOTAL DE LA COTIZACIÓN
                                                                    </small>

                                                                    <h2 class="text-success font-weight-bold mb-0">
                                                                        S/. <span id="total">0.00</span>
                                                                    </h2>

                                                                    <input type="hidden" name="total_venta"
                                                                        id="total_venta">

                                                                </div>

                                                                <div class="text-success">
                                                                    <i class="fas fa-coins fa-3x"></i>
                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <!-- Sección colapsable con animación (ANTES estaba hidden) -->
                                            <div class="position-relative">
                                                <div class="p-2 collapse-section" id="datosgenerales">

                                                    <div class="form-group mb-2" hidden>
                                                        <label class="col-form-label" for="selCategoriaReg">
                                                            <i class="fas fa-users fs-6"></i>
                                                            <span class="small">Personal</span>
                                                        </label>
                                                        <select id="idpersonal" name="idpersonal"
                                                            class="form-control select2" required></select>
                                                    </div>

                                                    <div class="form-group mb-2">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label class="col-form-label" for="selCategoriaReg">
                                                                    <i class="fas fa-map-marked-alt"></i>
                                                                    <span class="small">Almacén</span>
                                                                </label>
                                                                <select id="idsucursal" name="idsucursal"
                                                                    class="form-control"></select>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <label class="col-form-label" for="selCategoriaReg">
                                                                    <i class="fas fa-users fs-6"></i>
                                                                    <span class="small mr-2">Cliente</span><a
                                                                        class="input-group-addon"
                                                                        style="cursor: pointer;" data-toggle="modal"
                                                                        data-target="#ModalClientes"><i
                                                                            class="fa fa-plus fa-xs"></i> Nuevo
                                                                        Cliente</a>
                                                                </label>
                                                                <select id="idcliente" name="idcliente"
                                                                    class="form-control" required></select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Forma de pago y validez -->
                                                    <div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-2">
                                                                    <label class="col-form-label" for="selCategoriaReg">
                                                                        <i class="fas fa-money-bill-alt fs-6"></i>
                                                                        <span class="small">¿Cotizacion a
                                                                            credito?</span>
                                                                    </label>
                                                                    <select name="formapago" id="formapago"
                                                                        class="form-control" data-live-search="true"
                                                                        title="Seleccione Forma de Pago" required>
                                                                        <option value="No">No</option>
                                                                        <option value="Si">Sí</option>
                                                                    </select>
                                                                    <span id="validate_categoria"
                                                                        class="text-danger small fst-italic"
                                                                        style="display:none">Debe Ingresar tipo de
                                                                        pago</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group mb-2">
                                                                    <label class="col-form-label">
                                                                        <i class="fas fa-money-bill-alt fs-6"></i>
                                                                        <span class="small">Validez de la
                                                                            Cotización</span>
                                                                    </label>
                                                                    <select name="nota" id="nota" class="form-control"
                                                                        data-live-search="true"
                                                                        title="Seleccione Tiempo de Producción"
                                                                        required>
                                                                        <option value="3 Días calendario">3 Días
                                                                            calendario</option>
                                                                        <option value="7 Días calendario">7 Días
                                                                            calendario</option>
                                                                        <option value="15 Días calendario">15 Días
                                                                            calendario</option>
                                                                        <option value="30 Días calendario">30 Días
                                                                            calendario</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mt-2">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group mb-2">
                                                                    <label class="col-form-label" for="selCategoriaReg">
                                                                        <i class="fas fa-file-alt fs-6"></i>
                                                                        <span class="small">Tipo Documento </span>
                                                                    </label>
                                                                    <select name="tipo_comprobante"
                                                                        id="tipo_comprobante" class="form-control"
                                                                        required>
                                                                        <option value="Boleta">Boleta</option>
                                                                        <option value="Factura">Factura</option>
                                                                        <option value="Ticket">Ticket</option>
                                                                    </select>
                                                                    <select name="tipo_c" id="tipo_c"
                                                                        class="form-control" hidden>
                                                                        <option value="Compra">Compra</option>
                                                                    </select>
                                                                    <span id="validate_categoria"
                                                                        class="text-danger small fst-italic"
                                                                        style="display:none">Debe Seleccione
                                                                        documento</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="iptNroSerie">Serie</label>
                                                                <input style="border-color: #FFC7BB; text-align:center"
                                                                    type="text" class="form-control form-control-sm"
                                                                    name="serie_comprobante" id="serie_comprobante"
                                                                    maxlength="7" placeholder="Serie" readonly>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label for="iptNroVenta">N° Orden</label>
                                                                <input style="border-color: #99C0E7; text-align:center"
                                                                    type="text" class="form-control form-control-sm"
                                                                    name="num_comprobante" id="num_comprobante"
                                                                    maxlength="10" placeholder="Número" readonly>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-2">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <label class="col-form-label" for="selCategoriaReg">
                                                                    <i class="fas fa-file-alt fs-6"></i>
                                                                    <span class="small">Observaciones </span>
                                                                </label>
                                                                <textarea class="form-control" name="observaciones"
                                                                    id="observaciones"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <span class="small">Fecha</span>
                                                        <input style="text-align:center" class="form-control pull-right"
                                                            type="date" name="fecha" id="fecha" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12" style="margin-top: -17px;">
                                                <input type="hidden" name="idcotizacion" id="idcotizacion">
                                                <table id="detalles" class="table table-striped table-responsive-sm">
                                                    <thead class="bg-info">
                                                        <tr>
                                                            <th style="width: 500px;">Producto</th>
                                                            <th>UM</th>
                                                            <th>Precio</th>
                                                            <th>Cantidad</th>
                                                            <th style="width: 30px;">Subtotal</th>
                                                            <th style="width: 50px;">Eliminar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="detalles_empty">
                                                        <tr>
                                                            <td colspan="6">

                                                                <div class="text-center bg-light">

                                                                    <i
                                                                        class="fas fa-box-open fa-4x text-primary mb-3 mt-2"></i>

                                                                    <h5 class="font-weight-bold">
                                                                        No hay productos agregados
                                                                    </h5>

                                                                    <p class="text-muted mb-0">
                                                                        Agrega productos o servicios para generar la
                                                                        cotización.
                                                                    </p>

                                                                </div>

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tbody>
                                                        <!-- Filas dinámicas -->
                                                    </tbody>
                                                </table>
                                            </div>



                                            <hr>

                                            <div class="card-body p-2" hidden id="datosCredito"
                                                style="border: 1px dashed hsla(0,0%,80%,.329); border-top: none;">
                                                <!-- Sección colapsable con animación (ANTES estaba hidden) -->
                                                <hr style="border: 1 px solid #007bff;" />
                                                <div class="row col-md-12 mt-4">
                                                    <div class="form-group col-lg-3">
                                                        <label for="">Pago inicial:</label>
                                                        <input type="text" class="form-control" name="inicial"
                                                            id="inicial">
                                                    </div>
                                                    <div class="form-group col-lg-3" id="n1">
                                                        <label style="font-size: 11px;">Monto Deuda:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="montoDeuda" name="montoDeuda"
                                                                readonly="">
                                                        </div>

                                                    </div>
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

                                                    <div class="form-group col-lg-3" id="n5">
                                                        <label style="font-size: 11px;">Interes %:</label>
                                                        <div class="input-group">
                                                            <?php if (Helpers::getUserPermissionAccion('Editar interes credito')): ?>
                                                                <input style="border-color: #FFC7BB; text-align:center"
                                                                    type="text" class="form-control" id="inputInteres"
                                                                    name="inputInteres" value="0">
                                                            <?php endif; ?>
                                                            <?php if (!Helpers::getUserPermissionAccion('Editar interes credito')): ?>
                                                                <input style="border-color: #FFC7BB; text-align:center"
                                                                    type="text" class="form-control" id="inputInteres"
                                                                    name="inputInteres" value="0" readonly>
                                                            <?php endif; ?>
                                                        </div>

                                                    </div>

                                                    <div class="form-group col-lg-3" id="b1">
                                                        <br>
                                                        <button type="button" class="btn btn-success"
                                                            id="calcular_cuotas">Calcular</button>
                                                    </div>
                                                </div>
                                                <div class="row col-md-12 mt-4" id="panel1">
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
                                                </div>
                                                <div id="fechasHiddenContainer" style="display:none;"></div>
                                            </div>
                                            <!--//***************************************************************************//-->
                                        </div>

                                        <div class="col-md-6">
                                            <button class="btn-flotante" id="btnGuardar" onclick="guardaryeditar()">
                                                <i class="fas fa-shopping-cart"></i> Realizar Cotizacion
                                            </button>
                                            <button id="btnCancelar" class="btn-flotante2" onclick="cancelarform()"
                                                type="button">
                                                <i class="fas fa-window-close"></i> Cancelar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- INICIO DE TABLE PRODUCTO Y SERVICIOS-->
                                <div class="col-lg-6 hidden-md hidden-sm hidden-xs">
                                    <div class="card  card-tabs">
                                        <div class="card-header p-0 pt-1">
                                            <div class="card-header p-0 pt-1">
                                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="custom-tabs-two-home-tab"
                                                            data-toggle="pill" href="#custom-tabs-two-home" role="tab"
                                                            aria-controls="custom-tabs-two-home"
                                                            aria-selected="true">Producto</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="custom-tabs-two-profile-tab"
                                                            data-toggle="pill" href="#custom-tabs-two-profile"
                                                            role="tab" aria-controls="custom-tabs-two-profile"
                                                            aria-selected="false">Servicio</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="card-body" style="margin-top: -35px; overflow-x: auto;">
                                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                                    <div class="tab-pane fade show active" id="custom-tabs-two-home"
                                                        role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                                                        <table id="tblarticulos"
                                                            class="table table-striped table-responsive-lg"
                                                            width="100%">
                                                            <thead class="bg-info">
                                                                <tr>
                                                                    <th>Op</th>
                                                                    <th>Nombre</th>
                                                                    <th>Código</th>
                                                                    <th>Stock</th>
                                                                    <th>P Venta</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th>Op</th>
                                                                    <th>Nombre</th>
                                                                    <th>Código</th>
                                                                    <th>Stock</th>
                                                                    <th>P Venta</th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                    <div class="tab-pane fade" id="custom-tabs-two-profile"
                                                        role="tabpanel" aria-labelledby="custom-tabs-two-profile-tab">
                                                        <table id="tblarticulos2"
                                                            class="table table-striped table-responsive-lg"
                                                            width="100%">
                                                            <thead class="bg-info">
                                                                <tr>
                                                                    <th>Op</th>
                                                                    <th width="200px">Nombre</th>
                                                                    <th style="text-align: center;">Stock</th>
                                                                    <th>P Venta</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th>Op</th>
                                                                    <th>Nombre</th>
                                                                    <th>Stock</th>
                                                                    <th>P Venta</th>
                                                                </tr>
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
                    <!-- /.card-body formulario -->


                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<!-- ================== MODAL VISTA COTIZACIÓN ================== -->
<div class="modal fade" id="getCodeModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Vista de Cotización</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <style>
                .modal-body {
                    background: #f2f3f3;
                }

                .card-section {
                    background: #fff;
                    border-radius: 14px;
                    padding: 18px;
                    margin-bottom: 18px;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
                    border: 1px solid #e9ecef;
                }

                .section-title {
                    font-size: 15px;
                    font-weight: 700;
                    color: #343a40;
                    margin-bottom: 15px;
                    border-left: 4px solid #007bff;
                    padding-left: 10px;
                }

                .custom-label {
                    font-size: 12px;
                    font-weight: 600;
                    color: #6c757d;
                    margin-bottom: 5px;
                    display: block;
                }

                .form-control {
                    border-radius: 10px;
                    border: 1px solid #dcdfe3;
                    height: 42px;
                    background: #fdfdfd;
                    font-size: 14px;
                }

                .form-control:focus {
                    border-color: #007bff;
                    box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, .15);
                }

                .table-modern {
                    border-radius: 12px;
                    overflow: hidden;
                    background: #fff;
                }

                .table-modern thead {
                    background: linear-gradient(90deg, #007bff, #0056b3);
                    color: white;
                }

                .table-modern th {
                    font-size: 13px;
                    font-weight: 600;
                    border: none;
                    padding: 14px;
                }

                .table-modern td {
                    vertical-align: middle;
                    padding: 12px;
                    border-color: #f1f1f1;
                    font-size: 13px;
                }

                .table-modern tbody tr:hover {
                    background: #f8fbff;
                    transition: .2s;
                }

                .info-box-credit {
                    background: linear-gradient(135deg, #ffffff, #f8f9fa);
                    border-radius: 12px;
                    padding: 15px;
                    text-align: center;
                    border: 1px solid #e9ecef;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
                }

                .info-box-credit h6 {
                    font-size: 12px;
                    color: #6c757d;
                    margin-bottom: 6px;
                    font-weight: 600;
                    text-transform: uppercase;
                }

                .info-box-credit p {
                    margin: 0;
                    font-size: 18px;
                    font-weight: bold;
                    color: #212529;
                }

                #dataCuotasCredito {
                    display: block;
                    max-height: 280px;
                    overflow-y: auto;
                    width: 100%;
                }

                #dataCuotasCredito tr {
                    display: table;
                    width: 100%;
                    table-layout: fixed;
                }

                #dataCuotasCredito td {
                    width: 25%;
                }

                .table-cuotas thead {
                    background: #343a40;
                    color: white;
                }

                .table-cuotas th,
                .table-cuotas td {
                    padding: 12px;
                    font-size: 13px;
                }
            </style>

            <div class="modal-body">

                <!-- DATOS GENERALES -->
                <div class="card-section">
                    <div class="section-title">
                        Información General
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="custom-label">Cliente</label>
                            <input class="form-control" type="text" name="cliente" id="cliente" readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="custom-label">Personal</label>
                            <input type="text" class="form-control" id="nuevoVendedor"
                                value="<?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : ''; ?>" readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="custom-label">Fecha</label>
                            <input class="form-control" type="text" name="fecha_horam" id="fecha_horam" readonly>
                        </div>
                    </div>
                </div>

                <!-- COMPROBANTE -->
                <div class="card-section">
                    <div class="section-title">
                        Información del Comprobante
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="custom-label">Comprobante</label>
                            <input class="form-control" type="text" name="tipo_comprobantem" id="tipo_comprobantem"
                                readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="custom-label">Serie</label>
                            <input class="form-control" type="text" name="serie_comprobantem" id="serie_comprobantem"
                                readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="custom-label">Número</label>
                            <input class="form-control" type="text" name="num_comprobantem" id="num_comprobantem"
                                readonly>
                        </div>
                    </div>
                </div>

                <!-- DETALLE -->
                <div class="card-section">
                    <div class="section-title">
                        Detalle de Productos
                    </div>

                    <div class="table-responsive">
                        <table id="detallesm" class="table table-modern table-hover" width="100%">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>UM</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Llenado dinámico -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- CRÉDITO -->
                <div class="card-section" id="panelCredito" hidden>
                    <div class="section-title">
                        Información del Crédito
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="info-box-credit">
                                <h6>Crédito</h6>
                                <p id="textCredito">S/ 0.00</p>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="info-box-credit">
                                <h6>Inicial</h6>
                                <p id="textInicial">S/ 0.00</p>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="info-box-credit">
                                <h6>Interés</h6>
                                <p id="textInteres">0%</p>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="info-box-credit">
                                <h6>Cuotas</h6>
                                <p id="textCuotas">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CRONOGRAMA -->
                <div class="card-section" id="panel2" hidden>
                    <div class="section-title">
                        Cronograma de Pagos
                    </div>

                    <div class="table-responsive">
                        <table class="table table-modern table-cuotas" style="width:100%;">
                            <thead style="display: table; width: 100%; table-layout: fixed;">
                                <tr>
                                    <th>Fecha de Pago</th>
                                    <th>Monto</th>
                                    <th>Interés</th>
                                    <th>Total</th>
                                </tr>
                            </thead>

                            <tbody id="dataCuotasCredito">
                                <tr>
                                    <td colspan="4" class="text-center">
                                        No se han calculado las fechas de pago
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <p></p>
                <button type="button" onclick="cancelarform()" class="btn btn-default"
                    data-dismiss="modal">Cerrar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- ================== MODAL CLIENTES ================== -->
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
                    <button class="btn btn-primary" type="submit" id="btnGuardarCliente">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<style>
    .img-producto {
        cursor: pointer;
    }
</style>

<!-- ================== SCRIPTS EXTRA ================== -->
<script>
    // 🔎 Búsqueda rápida en tablas (por texto completo de cada fila)
    function filtrarTabla(inputId, tablaId) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var filtro = input.value.toLowerCase();
        var filas = document.querySelectorAll('#' + tablaId + ' tbody tr');
        filas.forEach(function (fila) {
            var visible = fila.textContent.toLowerCase().includes(filtro);
            fila.style.display = visible ? '' : 'none';
        });
    }

    // 📌 Colapsar sección con animación
    function toggleCollapse(id, btn) {
        var panel = document.getElementById(id);
        if (!panel) return;
        panel.classList.toggle('show');
        const icon = btn.querySelector(".fa-chevron-down, .fa-chevron-up");

        if (!panel.classList.contains("show")) {
            icon.classList.remove("fa-chevron-up");
            icon.classList.add("fa-chevron-down");
        } else {
            icon.classList.remove("fa-chevron-down");
            icon.classList.add("fa-chevron-up");
        }
    }

    // 🗓️ Fecha/hora visible en cabecera de formulario
    (function pintarFechaActual() {
        var el = document.getElementById('fechaActual');
        if (!el) return;
        try {
            var fecha = new Date();
            // Mostrar en formato local "dd/mm/yyyy hh:mm"
            var dd = ('0' + fecha.getDate()).slice(-2);
            var mm = ('0' + (fecha.getMonth() + 1)).slice(-2);
            var yyyy = fecha.getFullYear();
            var hh = ('0' + fecha.getHours()).slice(-2);
            var min = ('0' + fecha.getMinutes()).slice(-2);
            el.textContent = dd + '/' + mm + '/' + yyyy + ' ' + hh + ':' + min;
        } catch (e) { }
    })();

    // 💰 Helper para recalcular total si tu JS no lo hace ya
    function recalcularTotalDesdeDetalles() {
        var total = 0;
        document.querySelectorAll('#detalles tbody tr').forEach(function (tr) {
            var celdaSubtotal = tr.querySelector('[data-subtotal]');
            if (celdaSubtotal) {
                var val = parseFloat(celdaSubtotal.textContent.replace(/,/g, '') || '0');
                if (!isNaN(val)) total += val;
            }
        });
        var totalEl = document.getElementById('total');
        var totalInput = document.getElementById('total_venta');
        if (totalEl) totalEl.textContent = total.toFixed(2);
        if (totalInput) totalInput.value = total.toFixed(2);
    }

    // Observador opcional para actualizar total cuando cambien cantidades/precios (si marcas celdas con data-atributos)
    var detallesTabla = document.getElementById('detalles');
    if (detallesTabla && 'MutationObserver' in window) {
        var obs = new MutationObserver(function () { recalcularTotalDesdeDetalles(); });
        obs.observe(detallesTabla.tBodies[0], { childList: true, subtree: true, characterData: true });
    }
</script>

<!-- Tu JS principal -->
<script src="vistas/js/cotizacion.js"></script>