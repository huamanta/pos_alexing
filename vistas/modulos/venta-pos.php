<style type="text/css">
    /* Variables de colores y tipografías */
    :root {
        --primary-color: #2c2fa5;
        /* Color principal para hover y acentos */
        --btn-primary-bg: #008000;
        /* Fondo del botón primario */
        --btn-secondary-bg: red;
        /* Fondo del botón secundario */
        --white: #ffffff;
        --light-font-size: 11.5px;
        --small-font-size: 12px;
    }

    /* Bloque principal del formulario de venta */

    #formularioregistros.pos-form-shell .row.mb-3 {
        margin-bottom: 0 !important;
    }

    #formularioregistros.pos-form-shell .col-lg-6[style*="margin-top: -20px"] {
        margin-top: 0 !important;
    }

    #formularioregistros.pos-form-shell .panel-heading {
        border: none !important;
    }

    #formularioregistros.pos-form-shell .card.card-outline.card-danger {
        margin-top: 0 !important;
        border: none;
        background: transparent;
    }

    #formularioregistros.pos-form-shell .card.shadow.mb-4 {
        margin-bottom: 14px !important;
    }

    #formularioregistros.pos-form-shell .card {
        border-radius: 12px;
        border: 1px solid #e1eaf5;
        box-shadow: 0 6px 18px rgba(23, 42, 73, 0.06);
    }

    #formularioregistros.pos-form-shell .card-header {
        border-bottom: 1px solid #e7edf6;
    }

    #formularioregistros.pos-form-shell .card-header.bg-white.border-bottom-primary {
        background: linear-gradient(90deg, #ffffff 0%, #f5f9ff 100%) !important;
    }

    #formularioregistros.pos-form-shell .card-title {
        letter-spacing: 0.3px;
    }

    #formularioregistros.pos-form-shell #datosgenerales,
    #formularioregistros.pos-form-shell #datosgenerales2 {
        background: #ffffff;
        border-radius: 12px;
    }

    #formularioregistros.pos-form-shell #datosgenerales {
        padding: 14px;
    }

    #formularioregistros.pos-form-shell #datosgenerales2 {
        margin-top: 0 !important;
        padding: 14px !important;
    }

    #formularioregistros.pos-form-shell fieldset {
        border-color: #d9e4f2 !important;
        background: #fafcff;
        border-radius: 10px;
    }

    #formularioregistros.pos-form-shell fieldset legend {
        margin-bottom: 0;
        font-size: 12px;
        color: #2c2fa5;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }

    #formularioregistros.pos-form-shell label {
        font-size: 11.5px;
        margin-bottom: 4px;
        color: #24344f;
    }

    #formularioregistros.pos-form-shell .form-group {
        margin-bottom: 10px;
    }

    #formularioregistros.pos-form-shell .form-control {
        border-color: #d0dceb;
        border-radius: 8px;
        min-height: 34px;
    }

    #formularioregistros.pos-form-shell .form-control:focus {
        border-color: #7aa7de;
        box-shadow: 0 0 0 0.2rem rgba(44, 47, 165, 0.12);
    }

    #formularioregistros.pos-form-shell .input-group-text {
        background: #f3f7fd;
        border-color: #d0dceb;
        color: #49638a;
    }

    #formularioregistros.pos-form-shell .container-fluid[style*="background-color: #28a745"] {
        background: linear-gradient(90deg, #1f9f4e 0%, #28b661 100%) !important;
        border: 1px solid #1b9549;
        border-radius: 12px !important;
    }

    #formularioregistros.pos-form-shell #detalles {
        border: 1px solid #d9e4f2;
        border-radius: 10px;
        background-color: #fff;
        box-shadow: inset 0 0 0 1px rgba(217, 228, 242, 0.3);
    }

    #formularioregistros.pos-form-shell #detalles thead {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    #formularioregistros.pos-form-shell #detalles thead th {
        background: linear-gradient(90deg, #0ea4bd 0%, #0b8fa4 100%) !important;
        color: #fff;
        border: none;
        font-size: 11px;
        letter-spacing: 0.2px;
    }

    #formularioregistros.pos-form-shell #pagosMixtosContainer {
        background: #f7faff;
        border: 1px dashed #cddbf0;
        border-radius: 10px;
        padding: 10px 8px 2px;
    }

    #formularioregistros.pos-form-shell .pagoItem {
        margin: 0 0 8px !important;
        padding: 8px 4px;
        border-radius: 8px;
        background: #ffffff;
        border: 1px solid #e6eef8;
    }

    #formularioregistros.pos-form-shell hr {
        border-top: 1px solid #dce6f4;
        margin: 12px 0;
    }

    #formularioregistros.pos-form-shell .btn-sm {
        border-radius: 7px;
    }

    #formularioregistros.pos-form-shell .btn-outline-info {
        border-color: #18a2b8;
        color: #117a8b;
    }

    #formularioregistros.pos-form-shell .btn-outline-info:hover {
        background: #17a2b8;
        color: #fff;
    }

    @media only screen and (max-width: 991px) {
        #formularioregistros.pos-form-shell {
            padding: 12px 10px 95px;
            border-radius: 12px;
        }

        #formularioregistros.pos-form-shell #datosgenerales,
        #formularioregistros.pos-form-shell #datosgenerales2 {
            padding: 10px !important;
        }
    }

    /* Contenedor de tabla con scroll */
    #detalles-wrapper {
        max-height: 300px;
        overflow-y: auto;
        width: 100%;
    }

    #detalles {
        display: table;
        width: 100% !important;
        min-width: 100% !important;
        font-size: var(--light-font-size);
        table-layout: fixed;
    }

    #detalles thead th:first-child,
    #detalles tbody td:first-child {
        width: 40%;
        max-width: 40%;
    }

    #detalles tbody .fila-vacia-detalles td {
        width: 100% !important;
        min-width: 100% !important;
        white-space: normal !important;
        padding: 14px 10px;
        text-align: center;
    }

    /* Botones flotantes */
    .btn-flotante,
    .btn-flotante2 {
        font-size: 16px;
        text-transform: uppercase;
        font-weight: bold;
        color: var(--white);
        border-radius: 5px;
        letter-spacing: 2px;
        padding: 18px 30px;
        position: fixed;
        bottom: 20px;
        transition: all 300ms ease;
        box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.1);
        z-index: 99;
    }

    .btn-flotante {
        background-color: var(--btn-primary-bg);
        right: 40px;
    }

    .btn-flotante:hover {
        background-color: var(--primary-color);
        box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.3);
        transform: translateY(-7px);
    }

    .btn-flotante2 {
        background-color: var(--btn-secondary-bg);
        right: 310px;
    }

    .btn-flotante2:hover {
        background-color: var(--primary-color);
        box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.3);
        transform: translateY(-7px);
    }

    @media only screen and (max-width: 600px) {
        .btn-flotante {
            font-size: 14px;
            padding: 12px 20px;
            right: 20px;
        }

        .btn-flotante2 {
            font-size: 14px;
            padding: 12px 20px;
            right: 290px;
        }
    }

    /* Ajustes en las tablas */
    #tblarticulos,
    #tblarticulos2 {
        font-size: var(--small-font-size);
    }

    #tblarticulos th,
    #tblarticulos td,
    #tblarticulos2 th,
    #tblarticulos2 td,
    #detalles th,
    #detalles td {
        padding: 4px;
        white-space: nowrap;
        text-align: center;
    }

    /* Estilo para imágenes */
    .img-thumbnail {
        border-radius: 5px;
    }

    .img-producto {
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .img-producto:hover {
        transform: scale(1.05);
    }

    /* Botones compactos */
    .btn-xs {
        padding: 2px 5px;
        font-size: 10px;
    }

    .table-responsive {
        overflow-x: auto;
        max-width: 100%;
    }


    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
    }

    .modal-header {
        background-color: var(--primary-color);
        color: var(--white);
    }

    .modal-title {
        font-weight: bold;
    }

    /* Aseguramos que tu navbar-pos2 esté visible */
    #navbar-pos2 {
        display: flex;
        /* o block, según tu layout */
    }

    /* ============================
   ZOOM GLOBAL FUNCIONAL
   ============================ */
    .scale-global {
        zoom: 0.85;
        /* Cambia el valor a gusto: 0.80 / 0.70 / 0.65 */
        transform-origin: top center;
    }

    /* Para navegadores que no soportan zoom */
    @supports not (zoom:1) {
        .scale-global {
            transform: scale(0.85);
            transform-origin: top center;
        }
    }

    /* --- DISEÑO VENTANA FLOTANTE --- */
    #floating-history {
        display: none;
        position: fixed;
        top: 85px;
        right: 20px;
        width: 600px;
        /* Un poco más ancho para el subtotal */
        background: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        /* Sombra elegante */
        border-radius: 10px;
        border: none;
        z-index: 99999;
        font-family: 'Source Sans Pro', sans-serif;
        overflow: hidden;
        /* Para respetar bordes redondeados */
        /*transition: all 0.3s ease;*/
    }

    /* Cabecera con degradado */
    #floating-header {
        background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%);
        color: #fff;
        padding: 12px 15px;
        cursor: move;
        /* Icono de mano */
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }

    /* Cuerpo con scroll personalizado */
    #floating-body {
        max-height: 450px;
        overflow-y: auto;
        background-color: #fff;
    }

    /* Scrollbar moderno (Chrome/Safari) */
    #floating-body::-webkit-scrollbar {
        width: 6px;
    }

    #floating-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #floating-body::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }

    #floating-body::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }

    /* Estilos de la tabla */
    .table-historial th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 700;
        border-top: none !important;
        border-bottom: 2px solid #dee2e6;
        font-size: 12px;
        text-transform: uppercase;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table-historial td {
        vertical-align: middle !important;
        font-size: 12px;
        padding: 8px 5px !important;
        border-bottom: 1px solid #eee;
    }

    /* Fila resaltada (Producto en carrito) */
    .resaltado-carrito {
        background-color: #fff8e1 !important;
        /* Amarillo muy suave */
        border-left: 4px solid #ffc107;
    }

    .resaltado-carrito td {
        color: #856404;
        font-weight: 600;
    }

    /* Input de búsqueda */
    .search-box-historial {
        padding: 10px;
        background: #f4f6f9;
        border-bottom: 1px solid #e9ecef;
    }

    .search-box-historial input {
        border-radius: 20px;
        border: 1px solid #ced4da;
        padding-left: 15px;
    }

    .search-box-historial input:focus {
        box-shadow: none;
        border-color: #17a2b8;
    }

    #floating-header {
        /* ... tus estilos actuales ... */
        cursor: grab;
        /* Manita abierta */
        user-select: none;
        /* IMPRESCINDIBLE: Evita seleccionar texto al arrastrar */
    }

    #floating-header:active {
        cursor: grabbing;
        /* Manita cerrada al agarrar */
    }

    #floating-history {
        /* ... tus estilos actuales ... */
        /* Aseguramos que el navegador use la GPU para renderizar si es posible */
        will-change: top, left;
    }
</style>
<?php
date_default_timezone_set('America/Lima');
?>


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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header" id="header">
                            <div class="row">
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-primary btn-xs" id="btnNuevo"
                                        onclick="mostrarform(true)"><i class="fa fa-plus"></i>
                                        Nuevo</button>
                                </div>
                                <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Fecha Inicio:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                                            value="<?php echo date("Y-m-01"); ?>">
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
                                            value="<?php echo date("Y-m-d"); ?>">
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

                            </div>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body" id="listadoregistros">
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

                            <br>

                            <div class="row mb-3">

                                <div class="col-lg-6" style="margin-top: -20px;">

                                    <div class="panel-heading" style="border-bottom: 1px dashed hsla(0,0%,80%,.329)">

                                        <div class="card card-outline card-danger" style="margin-top: -20px;">

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
                                                            onclick="toggleCard()"
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
                                                <div class="card-body" id="datosgenerales" hidden>

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
                                                        <legend class="w-auto px-2 small font-weight-bold text-primary">
                                                            Datos principales</legend>
                                                        <div class="row">
                                                            <div class="col-md-4 col-sm-12 mb-2">
                                                                <label for="idsucursal" class="font-weight-bold">
                                                                    <i class="fas fa-map-marked-alt"></i>
                                                                    Almacén
                                                                </label>
                                                                <select id="idsucursal" name="idsucursal"
                                                                    class="form-control"></select>
                                                            </div>
                                                            <div class="col-md-8 col-sm-12 mb-2">
                                                                <label for="idcliente" class="font-weight-bold">
                                                                    <i class="fas fa-users"></i> Cliente
                                                                </label>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between mb-1">
                                                                    <div>
                                                                        <a class="text-info" style="cursor:pointer;"
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
                                                        <legend class="w-auto px-2 small font-weight-bold text-primary">
                                                            Documento</legend>
                                                        <div class="row">
                                                            <div class="col-md-4 col-sm-12 mb-2">
                                                                <label for="tipo_comprobante" class="font-weight-bold">
                                                                    <i class="fas fa-file-alt"></i> Tipo
                                                                    Documento
                                                                </label>
                                                                <select class="form-control" name="tipo_comprobante"
                                                                    id="tipo_comprobante">
                                                                    <?php if (Helpers::getUserPermissionAccion('Crear nota de venta')): ?>
                                                                        <option value="Nota de Venta">Nota de Venta
                                                                        </option>
                                                                    <?php endif; ?>
                                                                    <?php if (Helpers::getUserPermissionAccion('Crear boleta')): ?>
                                                                        <option value="Boleta">Boleta</option>
                                                                    <?php endif; ?>
                                                                    <?php if (Helpers::getUserPermissionAccion('Crear factura')): ?>
                                                                        <option value="Factura">Factura</option>
                                                                    <?php endif; ?>
                                                                </select>
                                                                <small id="validate_categoria"
                                                                    class="text-danger d-none">Debe seleccionar
                                                                    documento</small>
                                                            </div>
                                                            <div class="col-md-4 col-sm-12 mb-2">
                                                                <label for="serie_comprobante" class="font-weight-bold">
                                                                    <i class="fas fa-store-alt"></i> Serie
                                                                </label>
                                                                <input type="text"
                                                                    class="form-control form-control-sm text-center bg-warning"
                                                                    name="serie_comprobante" id="serie_comprobante"
                                                                    maxlength="7" placeholder="Serie" readonly>
                                                            </div>
                                                            <div class="col-md-4 col-sm-12 mb-2">
                                                                <label for="num_comprobante" class="font-weight-bold">
                                                                    <i class="fas fa-file-alt"></i> Nº Orden
                                                                </label>
                                                                <input type="text"
                                                                    class="form-control form-control-sm text-center bg-warning"
                                                                    name="num_comprobante" id="num_comprobante"
                                                                    maxlength="10" placeholder="Número" readonly>
                                                            </div>
                                                        </div>
                                                    </fieldset>

                                                    <!-- Fecha (oculta) -->
                                                    <!-- Fecha -->
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">
                                                            <i class="far fa-calendar-alt"></i> Fecha
                                                        </label>
                                                        <input type="date" class="form-control text-center" name="fecha"
                                                            id="fecha" value="<?php echo date('Y-m-d'); ?>" <?php if ($_SESSION['cargo'] !== 'Administrador'): ?>
                                                                min="<?php echo date('Y-m-d', strtotime('-1 day')); ?>"
                                                                max="<?php echo date('Y-m-d'); ?>" <?php endif; ?> required>
                                                        <?php if ($_SESSION['cargo'] !== 'Administrador'): ?>
                                                            <small class="text-muted">Solo puedes seleccionar hoy o
                                                                un día atrás</small>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Importar Cotizaciones -->
                                                    <div class="form-group mb-3">
                                                        <label for="comprobanteReferencia" class="font-weight-bold">
                                                            <i class="fas fa-money-bill-alt"></i> Importar
                                                            Cotizaciones
                                                        </label>
                                                        <select id="comprobanteReferencia" name="comprobanteReferencia"
                                                            class="form-control select2"
                                                            onchange="mostrarE();"></select>
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
                                            <div class="col-md-12">
                                                <label for="">Detalle de la venta</label>
                                                <input type="hidden" name="idcaja" id="idcaja" style="right: -15px;">
                                                <div id="detalles-wrapper">
                                                    <table id="detalles" class="table table-striped table-hover">
                                                        <thead class="bg-info">
                                                            <th>Producto</th>
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
                                            <div class="card-header" hidden>
                                                <button type="button"
                                                    class="btn btn-block bg-gradient-success btn-sm shadow"
                                                    title="Completa los datos de tu pedido">Opciones de
                                                    Venta</button>
                                            </div>
                                            <div class="card-body p-2" id="datosgenerales2" style="margin-top: -60px;">

                                                <div class="row col-md-12 mt-4">
                                                    <div class="col-md-2 mt-3">
                                                        <label style="font-size: 11px;">¿Crédito?</label>
                                                        <select id="tipopago" name="tipopago" class="form-control"
                                                            data-live-search="true" required>
                                                            <option value="No">No</option>
                                                            <option value="Si">Sí</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3 mt-3">
                                                        <label style="font-size: 11px;">Total Depósito</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="totaldeposito"
                                                                name="totaldeposito" placeholder="Monto recibido"
                                                                value="0" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2 mt-3" hidden>
                                                        <label style="font-size: 11px;">Descuento:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" name="porcentaje" id="porcentaje"
                                                                maxlength="7" placeholder="Descuento"
                                                                onkeyup="calcularPorcentaje();" disabled="disabled">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2 mt-3">
                                                        <label style="font-size: 11px;">Total efectivo</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="totalrecibido"
                                                                name="totalrecibido" placeholder="Monto recibido"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mt-3">
                                                        <label style="font-size: 11px;">Vuelto S/.</label>
                                                        <div class="d-flex">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="vuelto" name="vuelto"
                                                                readonly="">

                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mt-3">
                                                        <label style="font-size: 11px;"></label>
                                                        <div class="d-flex">
                                                            <button type="button" class="btn btn-primary btn-sm ms-4"
                                                                id="addPago">Agregar </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row col-md-12 mt-1">
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
                                                                    <input type="text" class="form-control bancoPago"
                                                                        name="banco_pago[]" placeholder="Banco">
                                                                </div>
                                                                <div class="col-md-3 fechaContainer"
                                                                    style="display:none;">
                                                                    <input type="date"
                                                                        class="form-control fechaDeposito"
                                                                        name="fecha_deposito_pago[]"
                                                                        placeholder="Fecha">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <input type="text" class="form-control nroOperacion"
                                                                        name="nroOperacion_pago[]"
                                                                        placeholder="N° Operación">
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
                                                        <div class="input-group">
                                                            <input style="border-color: #FFC7BB; text-align:center"
                                                                type="text" class="form-control" id="inputInteres"
                                                                name="inputInteres" value="0">
                                                        </div>

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
                                                                    id="modalAcompananteGaranteLabel">Datos de
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
                                                                        <option value="" selected hidden>
                                                                            Seleccionar...</option>
                                                                        <option value="1">Cónyuge</option>
                                                                        <option value="2">Hijo(a)</option>
                                                                        <option value="3">Padre</option>
                                                                        <option value="4">Madre</option>
                                                                        <option value="5">Hermano(a)</option>
                                                                        <option value="6">Amigo(a)</option>
                                                                        <option value="7">Compañero(a) de
                                                                            trabajo</option>
                                                                        <option value="8">Otro</option>
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
                                                <div class="row col-md-12 mt-4" style="display: none;" id="n6">
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
                                                </div>
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
                                <div class="col-lg-6 hidden-md hidden-sm hidden-xs" style="margin-top: -20px;">
                                    <div class="card  card-tabs" style="margin-top: -20px;">
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
                                                            class="form-control" id="search_product">
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
                                                                <th>Nombre</th>
                                                                <th>Categoria</th>
                                                                <th>Código</th>
                                                                <th>Stock</th>
                                                                <th>P Venta</th>
                                                            </thead>
                                                            <tbody id="tbody_articulos">
                                                            </tbody>
                                                            <tfoot>
                                                                <th>Op</th>
                                                                <th>Nombre</th>
                                                                <th>Categoria</th>
                                                                <th>Código</th>
                                                                <th>Stock</th>
                                                                <th>P Venta</th>
                                                            </tfoot>
                                                        </table>
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
                </div>
                <!-- /.col -->
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
                                <option value="Todos">Todos</option>
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


<div class="modal fade" id="getCodeModal22" tabindex="-1" aria-labelledby="getCodeModalLabel" aria-hidden="true">
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
                        <label>Cliente:</label>
                        <h4 id="cliente"></h4>
                    </div>
                    <div class="col-sm-4">
                        <label>Personal:</label>
                        <h4 id="personalm"></h4>
                    </div>
                    <div class="col-sm-4">
                        <label>Fecha:</label>
                        <h4 id="fecha_hora"></h4>
                    </div>
                    <div class="col-sm-4">
                        <label>Tipo comprobante:</label>
                        <h4 id="tipo_comprobantem"></h4>
                    </div>
                    <div class="col-sm-4">
                        <label>Correlativo:</label>
                        <h4 id="correlativo"></h4>
                    </div>
                    <div class="col-sm-4">
                        <label>Forma Pago:</label>
                        <h4 id="formapagom"></h4>
                    </div>
                    <div class="col-sm-4">
                        <label>Observaciones:</label>
                        <h4 id="observaciones"></h4>
                    </div>
                </div>

                <br>
                <div class="form-group col-lg-12 col-md-12 col-xs-12">
                    <table id="detallesm" class="table table-striped table-responsive-lg" width="100%">
                        <tbody></tbody>
                    </table>
                </div>

                <div class="row m-0" hidden>
                    <div class="col-sm-4">
                        <label>Subtotal:</label>
                        <h4 id="subtotalm"></h4>
                    </div>
                    <div class="col-sm-4">
                        <label>IGV:</label>
                        <h4 id="impuestom"></h4>
                    </div>
                    <div class="col-sm-4">
                        <label>Total:</label>
                        <h4 id="totalm"></h4>
                    </div>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" onclick="cancelarform02()" class="btn btn-default"
                    data-bs-dismiss="modal">Cerrar</button>
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
</div>

<script src="vistas/js/venta-pos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fechaInput = document.getElementById('fecha');
        const cargoUsuario = '<?php echo $_SESSION['cargo']; ?>';

        if (cargoUsuario !== 'Administrador') {
            // Función para obtener fecha en Lima, Perú (UTC-5)
            function getFechaLima(diasAtras = 0) {
                const ahora = new Date();

                // Obtener la fecha en Lima usando toLocaleString
                const fechaLima = new Date(ahora.toLocaleString('en-US', {
                    timeZone: 'America/Lima'
                }));

                // Ajustar días
                fechaLima.setDate(fechaLima.getDate() + diasAtras);

                // Formatear a YYYY-MM-DD
                const year = fechaLima.getFullYear();
                const month = String(fechaLima.getMonth() + 1).padStart(2, '0');
                const day = String(fechaLima.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }

            const hoy = getFechaLima(0);    // Hoy en Lima
            const ayer = getFechaLima(-1);  // Ayer en Lima

            console.log('Hoy en Lima:', hoy);
            console.log('Ayer en Lima:', ayer);

            // Configurar límites
            fechaInput.setAttribute('min', ayer);
            fechaInput.setAttribute('max', hoy);

            // Validación adicional al cambiar
            fechaInput.addEventListener('change', function () {
                if (this.value < ayer || this.value > hoy) {
                    Swal.fire({
                        title: 'Fecha no permitida',
                        text: 'Solo puedes seleccionar la fecha de hoy o ayer',
                        icon: 'warning',
                        confirmButtonText: 'Entendido'
                    });
                    this.value = hoy;
                }
            });

            // Establecer valor inicial
            if (!fechaInput.value) {
                fechaInput.value = hoy;
            }
        }
    });
</script>
<style>
    .img-producto {
        cursor: pointer;
    }
</style>