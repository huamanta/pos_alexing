<style type="text/css">
    
      .orange-cart {
        color: orange;
      }
      
       /* Establecer una altura máxima para la tabla */
    #detalles {
        max-height: 400px; /* Puedes ajustar esta altura según tus necesidades */
        overflow-y: auto; /* Agregar desplazamiento vertical */
        display: block; /* Para que el overflow funcione correctamente */
    }

    /* Establecer una anchura de la tabla del 100% */
    #detalles {
        width: 100%;
    }

    input::placeholder {
    transition: all 0.3s ease;
    color: #ccc; /* Color del placeholder */
}
/* Zoom para alejar todo el módulo de compras */
.zoom-modulo {
    zoom: 0.85;   /* Reduce a 85% del tamaño original */
}

/* En pantallas pequeñas, aleja un poco más */
@media (max-width: 768px) {
    .zoom-modulo {
        zoom: 0.75;
    }
}
/* Mejoras visuales para el módulo de compras */
.card {
    border-radius: 8px;
    border: none;
}

.card-header {
    border-radius: 8px 8px 0 0 !important;
}

.table thead th {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody td {
    vertical-align: middle;
    font-size: 13px;
}

.table-sm td, .table-sm th {
    padding: 0.5rem;
}

/* Inputs más pequeños y limpios */
.form-control-sm {
    font-size: 13px;
    border-radius: 4px;
}

/* Botones mejorados */
.btn {
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Scrollbar personalizado */
.table-responsive::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Animación para inputs modificados */
input:focus, select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Badge mejorado para subtotales */

/* Truncar texto largo con puntos suspensivos */
.text-truncate-custom {
    max-width: 250px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
    vertical-align: middle;
    cursor: help;
}

/* Tooltip personalizado */
.tooltip-custom {
    position: relative;
    display: inline-block;
}

.tooltip-custom:hover::after {
    content: attr(data-fulltext);
    position: absolute;
    left: 0;
    top: 100%;
    z-index: 1000;
    background: #333;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 12px;
    white-space: normal;
    max-width: 300px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    margin-top: 5px;
    word-wrap: break-word;
}

.tooltip-custom:hover::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 100%;
    border: 6px solid transparent;
    border-bottom-color: #333;
    margin-top: -6px;
}

/* Para el input del carrito - expandible */
.producto-nombre-input {
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.producto-nombre-input:hover {
    background-color: #f8f9fa !important;
    border-color: #667eea !important;
}

/* Expandir input al hacer foco */
.producto-nombre-input:focus {
    max-width: 100%;
    width: auto;
    min-width: 350px;
    position: relative;
    z-index: 10;
    background-color: white !important;
}

/* Para nombres muy largos en el carrito */
.nombre-producto-wrapper {
    position: relative;
}

.nombre-producto-short {
    display: block;
}

.nombre-producto-full {
    display: none;
    position: absolute;
    background: white;
    border: 2px solid #667eea;
    padding: 8px;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 100;
    min-width: 300px;
    max-width: 500px;
    word-wrap: break-word;
}

.nombre-producto-wrapper:hover .nombre-producto-full {
    display: block;
}
</style>

<?php
date_default_timezone_set('America/Lima');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper zoom-modulo">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid" style="margin-top: -10px;">
            <div class="row ">
                <div class="col-sm-6">
                    <h1 style="font-size: 15px;">Compra</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Compra</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content" style="margin-top: -15px;">
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

                                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
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

                                <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Reporte:</label>
                                    <div class="input-group">
                                        <button id="btnExportarExcel" class="btn btn-success btn-sm" title="Exportar a Excel">
                                            <i class="fa fa-file-excel-o"></i> Exportar Excel
                                        </button>
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
                                        <th>Gravadas</th>
                                        <th>Exoneradas</th>
                                        <th>Igv</th>
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
                                        <th>Gravadas</th>
                                        <th>Exoneradas</th>
                                        <th>Igv</th>
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
                                <div class="row">
                                    <!-- PANEL IZQUIERDO - CARRITO DE COMPRA -->
                                    <div class="col-lg-8" style="margin-top: -15px;">
                                        
                                        <!-- HEADER CON FECHA -->
                                        <div class="card shadow-sm mb-3">
                                            <div class="card-body p-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h4 class="mb-0 text-white"><i class="fas fa-shopping-cart"></i> Nueva Compra</h4>
                                                    </div>
                                                    <div class="text-white text-right">
                                                        <small id="fechaActual" style="font-size: 11px;"></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- DATOS DE LA COMPRA (COLAPSABLE) -->
                                        <div class="card shadow-sm mb-3">
                                            <div class="card-header bg-light p-2">
                                                <button type="button" class="btn btn-sm btn-block btn-primary" onclick="toggleCard()">
                                                    <i class="fas fa-file-invoice"></i> Datos de la Compra
                                                </button>
                                            </div>
                                            <div class="card-body p-3" id="datosgenerales" hidden>
                                                <div class="row">
                                                    <!-- Almacén -->
                                                    <div class="col-md-3 mb-2">
                                                        <label class="small font-weight-bold">
                                                            <i class="fas fa-warehouse text-primary"></i> Almacén
                                                        </label>
                                                        <select id="idsucursal" name="idsucursal" class="form-control form-control-sm">
                                                        </select>
                                                    </div>
                                                    <!-- Después del campo "Tipo Doc." y antes de "Fecha" -->
                                                    <div class="col-md-3 mb-2">
                                                        <label class="small font-weight-bold">
                                                            <i class="fas fa-percent text-danger"></i> Tipo IGV
                                                        </label>
                                                        <select name="tipo_igv" id="tipo_igv" class="form-control form-control-sm select2" required>
                                                            <option value="EXONERADA">Exonerada (0%)</option>
                                                            <option value="GRAVADA">Gravada (18%)</option>                                                            
                                                        </select>
                                                    </div>
                                                    <!-- Proveedor -->
                                                    <div class="col-md-6 mb-2">
                                                        <label class="small font-weight-bold">
                                                            <i class="fas fa-truck text-success"></i> Proveedor
                                                            <a class="ml-2" style="cursor: pointer; font-size: 11px;" data-toggle="modal" data-target="#myModalProveedor">
                                                                <i class="fa fa-plus-circle text-success"></i> Nuevo
                                                            </a>
                                                        </label>
                                                        <select id="idproveedor" name="idproveedor" class="form-control form-control-sm select2" required>
                                                        </select>
                                                    </div>

                                                    <!-- Tipo Documento -->
                                                    <div class="col-md-3 mb-2">
                                                        <label class="small font-weight-bold">
                                                            <i class="fas fa-file-alt text-info"></i> Tipo Doc.
                                                        </label>
                                                        <select name="tipo_comprobante" id="tipo_comprobante" class="form-control form-control-sm" required>
                                                            <option value="Boleta">Boleta</option>
                                                            <option value="Factura">Factura</option>
                                                            <option value="Ticket">Ticket</option>
                                                        </select>
                                                        <select name="tipo_c" id="tipo_c" class="form-control" hidden>
                                                            <option value="Compra">Compra</option>
                                                        </select>
                                                    </div>

                                                    <!-- Fecha -->
                                                    <div class="col-md-3 mb-2">
                                                        <label class="small font-weight-bold">
                                                            <i class="far fa-calendar text-warning"></i> Fecha
                                                        </label>
                                                        <input class="form-control form-control-sm" type="date" name="fecha" id="fecha" required>
                                                    </div>

                                                    <!-- Serie -->
                                                    <div class="col-md-3 mb-2">
                                                        <label class="small font-weight-bold">Serie</label>
                                                        <input type="text" class="form-control form-control-sm" name="serie_comprobante" id="serie_comprobante" maxlength="7" placeholder="F001" required>
                                                    </div>

                                                    <!-- Número -->
                                                    <div class="col-md-3 mb-2">
                                                        <label class="small font-weight-bold">Número</label>
                                                        <input type="text" class="form-control form-control-sm" name="num_comprobante" id="num_comprobante" maxlength="10" placeholder="00000001" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TABLA DE PRODUCTOS EN EL CARRITO -->
                                        <div class="card shadow-sm mb-3">
                                            <div class="card-header bg-light p-2">
                                                <h6 class="mb-0"><i class="fas fa-list"></i> Productos en la Compra</h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                                    <table id="detalles" class="table table-sm table-hover mb-0">
                                                        <thead class="thead-light" style="position: sticky; top: 0; z-index: 10;">
                                                            <tr>
                                                                <th style="width: 35%;">Producto</th>
                                                                <th style="width: 10%;" class="text-center">Cant.</th>
                                                                <th style="width: 12%;" class="text-center">P. Compra</th>
                                                                <th style="width: 12%;" class="text-center">P. Venta</th>
                                                                <th style="width: 10%;" class="text-center">Subtotal</th>
                                                                <th style="width: 8%;" class="text-center">Lote</th>
                                                                <th style="width: 10%;" class="text-center">F. Venc.</th>
                                                                <th style="width: 3%;" class="text-center"><i class="fas fa-cog"></i></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <!-- Los productos se agregan aquí dinámicamente -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TOTALES -->
                                        <div class="card shadow-sm" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                                            <div class="card-body p-3">
                                                <div class="row">
                                                    <!-- Monto Exonerado -->
                                                    <div class="col-md-3 text-right">
                                                        <p class="mb-1"><strong>Exonerado:</strong></p>
                                                        <h5 class="text-info mb-0">S/ <span id="most_exonerado">0.00</span></h5>
                                                        <input type="hidden" name="monto_exonerado" id="monto_exonerado" value="0">
                                                    </div>
                                                    
                                                    <!-- Monto Gravado -->
                                                    <div class="col-md-3 text-right">
                                                        <p class="mb-1"><strong>Gravado:</strong></p>
                                                        <h5 class="text-primary mb-0">S/ <span id="most_gravado">0.00</span></h5>
                                                        <input type="hidden" name="monto_gravado" id="monto_gravado" value="0">
                                                    </div>
                                                    
                                                    <!-- IGV -->
                                                    <div class="col-md-3 text-right">
                                                        <p class="mb-1"><strong>IGV (18%):</strong></p>
                                                        <h5 class="text-warning mb-0">S/ <span id="most_imp">0.00</span></h5>
                                                        <input type="hidden" name="monto_igv" id="monto_igv" value="0">
                                                    </div>
                                                    
                                                    <!-- TOTAL -->
                                                    <div class="col-md-3 text-right">
                                                        <p class="mb-1"><strong>TOTAL:</strong></p>
                                                        <h3 class="text-success mb-0" style="font-size: 2rem;">S/ <span id="total">0.00</span></h3>
                                                        <input type="hidden" name="total_compra" id="total_compra">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- OPCIONES DE PAGO -->
                                        <div class="card shadow-sm mt-3">
                                            <div class="card-header bg-light p-2">
                                                <h6 class="mb-0"><i class="fas fa-money-bill-wave"></i> Métodos de Pago</h6>
                                            </div>
                                            <div class="card-body p-3">
                                                <!-- Compra al Crédito -->
                                                <div class="row mb-2">
                                                    <div class="col-md-3">
                                                        <label class="small font-weight-bold">¿Compra al crédito?</label>
                                                        <select id="tipopago" name="tipopago" class="form-control form-control-sm" required>
                                                            <option value="No">No</option>
                                                            <option value="Si">Sí</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="small font-weight-bold">Total Depósito</label>
                                                        <input type="text" class="form-control form-control-sm" id="totaldeposito" name="totaldeposito" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="small font-weight-bold">Total Efectivo</label>
                                                        <input type="text" class="form-control form-control-sm" id="totalrecibido" name="totalrecibido" readonly>
                                                    </div>
                                                </div>

                                                <!-- Contenedor Dinámico de Pagos -->
                                                <div id="pagos_wrapper"></div>

                                                <!-- Campos de Crédito (Ocultos por defecto) -->
                                                <div class="row mt-3">
                                                    <div class="col-md-2" id="n0" style="display: none;">
                                                        <label class="small font-weight-bold">N° Cuotas</label>
                                                        <select name="input_cuotas" id="input_cuotas" class="form-control form-control-sm">
                                                            <option value="">Seleccionar...</option>
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
                                                    <div class="col-md-2" id="n1" style="display: none;">
                                                        <label class="small font-weight-bold">Fecha Pago</label>
                                                        <input type="date" class="form-control form-control-sm" id="fechaOperacion" name="fechaOperacion" value="<?php echo date("Y-m-d"); ?>">
                                                    </div>
                                                    <div class="col-md-2" id="n2" style="display: none;">
                                                        <label class="small font-weight-bold">Monto Pagado</label>
                                                        <input type="text" class="form-control form-control-sm" id="montoPagado" name="montoPagado" value="0">
                                                    </div>
                                                    <div class="col-md-2" id="n3" style="display: none;">
                                                        <label class="small font-weight-bold">Monto Deuda</label>
                                                        <input type="text" class="form-control form-control-sm" id="montoDeuda" name="montoDeuda" readonly>
                                                    </div>
                                                    <div class="col-md-2" id="n4" style="display: none;">
                                                        <label class="small">&nbsp;</label>
                                                        <button type="button" class="btn btn-success btn-sm btn-block" id="calcular_cuotas">
                                                            <i class="fas fa-calculator"></i> Calcular
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Tabla de Cuotas -->
                                                <div class="row mt-3" id="panel1" style="display: none;">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th>Fecha de Pago</th>
                                                                        <th>Monto a Pagar</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="datafechas"></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- BOTONES DE ACCIÓN -->
                                        <div class="row mt-3 mb-4">
                                            <div class="col-md-6">
                                                <button type="submit" class="btn btn-success btn-lg btn-block shadow" id="btnGuardar">
                                                    <i class="fas fa-check-circle"></i> Realizar Compra
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-secondary btn-lg btn-block shadow" id="btnCancelar" onclick="cancelarform()">
                                                    <i class="fas fa-times-circle"></i> Cancelar
                                                </button>
                                            </div>
                                        </div>

                                        <input type="hidden" name="idcompra" id="idcompra">
                                        <input type="hidden" name="impuesto" id="impuesto">

                                    </div>

                                    <!-- PANEL DERECHO - BÚSQUEDA DE PRODUCTOS -->
                                    <div class="col-lg-4" style="margin-top: -15px;">
                                        <div class="card shadow-sm" style="position: sticky; top: 20px;">
                                            <div class="card-header p-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                <h6 class="mb-0 text-white"><i class="fas fa-search"></i> Buscar Productos</h6>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="table-responsive" style="max-height: calc(100vh - 150px); overflow-y: auto;">
                                                    <table id="tblarticulos" class="table table-sm table-hover" width="100%">
                                                        <thead class="thead-light" style="position: sticky; top: 0; z-index: 10;">
                                                            <tr>
                                                                <th>Nombre</th>
                                                                <th>Código</th>
                                                                <th class="text-center">Stock</th>
                                                                <th class="text-center">Cant.</th>
                                                                <th class="text-center">Acción</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>Nombre</th>
                                                                <th>Código</th>
                                                                <th class="text-center">Stock</th>
                                                                <th class="text-center">Cant.</th>
                                                                <th class="text-center">Acción</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <!-- hasta que llega formulario registros-->
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
    <div class="modal-dialog modal-xl">
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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

                        <div class="input-group-addon">Personal:</div>

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

                        <div class="input-group-addon">Número:</div>

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

<div class="modal fade" id="myModalP">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form class="form-horizontal" role="form" name="formularioGuardarImagen" id="formularioGuardarImagen" method="POST">
                <div class="modal-header">
                    <h4 class="modal-title">Documento Compra</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="limpiarImagen()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" class="panel-body">

                    <div class="row col-md-12">

                        <label for="name" class="control-label">Imagen:</label>

                        <div class="col-lg-12">
                            <input type="file" class="form-control" name="imagen" id="imagen">
                            <input type="hidden" name="imagenactual" id="imagenactual">
                            <input type="hidden" name="idcompraI" id="idcompraI">
                            <img src="" class="img-thumbnail" id="imagenmuestra" width="650px">

                        </div>

                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" onclick="limpiarImagen()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="myModalProveedor">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Proveedores</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="limpiarProveedor()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formularioProveedores" id="formularioProveedores" method="POST">
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Nombre:</label>
                <input type="hidden" name="idpersona" id="idpersona">
                <input type="hidden" name="tipo_persona" id="tipo_persona" value="Proveedor">
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
          <button type="button" onclick="limpiarProveedor()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<script src="vistas/js/compra.js"></script>