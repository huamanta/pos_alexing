<style>
    /* =======================================
   MODAL DE COBROS – ESTILO PROFESIONAL
   ======================================= */

    /* --- Título del modal --- */
    .modal-header-custom {
        background: linear-gradient(45deg, #007bff, #005fcc);
        color: white;
        padding: 14px 22px !important;
        border-bottom: none !important;
    }

    .modal-header-custom .modal-title {
        font-size: 18px;
        font-weight: 600;
    }

    .modal-header-custom .close {
        font-size: 26px;
        opacity: 1;
        color: white;
    }

    /* --- Caja de información --- */
    .success-box-custom {
        background: #d3ffdd;
        border-left: 5px solid #28a745;
        padding: 14px 18px;
        border-radius: 6px;
        box-shadow: 0px 2px 6px rgb(0 0 0 / 5%);
    }

    .info-box-custom {
        background: #f0f8ff;
        border-left: 5px solid #007bff;
        padding: 14px 18px;
        border-radius: 6px;
        box-shadow: 0px 2px 6px rgb(0 0 0 / 5%);
    }

    .warning-box-custom {
        background: #fffef0;
        border-left: 5px solid #ffb700;
        padding: 14px 18px;
        border-radius: 6px;
        box-shadow: 0px 2px 6px rgb(0 0 0 / 5%);
    }

    /* --- Secciones del formulario --- */
    .section-title {
        background: #fafafa;
        padding: 6px 10px;
        font-size: 15px;
        font-weight: bold;
        border-left: 4px solid #007bff;
        margin-top: 18px;
        margin-bottom: 12px;
    }

    /* --- Inputs y selects --- */
    #formulario .form-group label {
        font-weight: 600;
        color: #444;
    }

    #formulario input,
    #formulario select,
    #formulario textarea {
        border-radius: 5px !important;
        border: 1px solid #c9c9c9 !important;
    }

    #formulario input:focus,
    #formulario select:focus,
    #formulario textarea:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 4px #007bff55 !important;
    }

    /* --- Botones del pie del modal --- */
    .modal-footer .btn {
        padding: 10px 22px;
        font-size: 15px;
        border-radius: 6px;
    }

    .btn-primary {
        background-color: #007bff !important;
        border: none !important;
    }

    .btn-primary:hover {
        background-color: #0069d9 !important;
    }

    /* --- Botón cerrar --- */
    .btn-secondary {
        background-color: #6c757d !important;
        border: none !important;
    }

    .btn-secondary:hover {
        background-color: #5a636b !important;
    }

    /* --- Animación suave del modal --- */
    .modal.fade .modal-dialog {
        transition: transform .2s ease-out;
        transform: translateY(-20px);
    }

    .modal.show .modal-dialog {
        transform: translateY(0);
    }

    /* --- Mejora visual en selectpicker --- */
    .bootstrap-select .dropdown-toggle {
        border-radius: 5px !important;
        border: 1px solid #c0c0c0 !important;
    }

    /* --- Colores para montos --- */
    #montoAdeudado {
        font-weight: bold;
        color: #a80000;
    }

    #deutaTotal {
        color: #d10000;
        font-weight: bold;
    }

    /* --- Mejor espaciado entre elementos --- */
    .modal-body .row {
        margin-bottom: 4px;
    }

    /* --- Scroll elegante si el modal crece --- */
    .modal-body {
        max-height: 65vh;
        overflow-y: auto;
        padding-right: 15px;
    }

    #getCodeModal .modal-body {
        max-height: none !important;
        overflow-y: visible !important;
    }

    .fila-retenida {
        background-color: #ffe5e5 !important;
        /* rojo suave */
        color: #a77170;
    }

    .fila-cuota-vencida {
        background-color: #ffd6d6 !important;
    }

    .fila-cuota-proxima {
        background-color: #fff4cc !important;
    }

    .btn-comment {
        background-color: blue !important;
        color: white !important;
    }

    .btn-amortiar {
        background-color: green !important;
        color: white !important;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Contratos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Ventas</a></li>
                        <li class="breadcrumb-item active">Contratos</li>
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
                        <div class="card-header">
                            <div class="row">
                                <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Fecha Inicio:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" />
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
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" />
                                    </div>
                                </div>

                                <!--div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Almacén:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-store-alt"></i>
                                            </span>
                                            <select id="idsucursal" name="idsucursal" class="form-control select2">
                                            </select>
                                        </div>
                                    </div>
                                </div-->

                                <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Estado:</label>
                                    <div class="input-group">
                                        <select id="estado" name="estado" class="form-control select2">
                                            <option value="">Todos</option>
                                            <option value="2">Pagados</option>
                                            <option value="1">Pendientes</option>
                                            <option value="0">Anulados</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Condicion:</label>
                                    <div class="input-group">
                                        <select id="condicion" name="condicion" class="form-control select2">
                                            <option value="">Todos</option>
                                            <option value="1">Normal</option>
                                            <option value="2">Moroso</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Frecuencia:</label>
                                    <select name="input_frecuencia" id="input_frecuencia" class="form-control"
                                        placeholder="Frecuencia">
                                        <option value="" selected hidden>Seleccionar...
                                        </option>
                                        <option value="">Todos</option>
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

                                <div class="col-md-3 d-flex align-items-center">
                                    <span class="mr-2">Mostrar</span>
                                    <select id="limit" class="form-control" style="width:100px">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="ml-2">Registros</span>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-3 flex-wrap align-items-center mt-4">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-success d-inline-block"
                                                style="width: 15px; height: 15px;"></span>
                                            <small>Normal</small>
                                        </div>

                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-warning d-inline-block"
                                                style="width: 15px; height: 15px;"></span>
                                            <small>1 - 30% Letras atrasadas</small>
                                        </div>

                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-orange d-inline-block"
                                                style="width: 15px; height: 15px;"></span>
                                            <small>31 - 60% Letras atrasadas</small>
                                        </div>

                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-danger d-inline-block"
                                                style="width: 15px; height: 15px;"></span>
                                            <small>+60% Letras atrasadas</small>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" id="search" class="form-control" placeholder="Buscar...">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <div class="table-responsive">
                                        <table id="tbllistado" class="table table-striped table-hover">
                                            <thead>
                                                <th>Fecha</th>
                                                <th>Estado pagos</th>
                                                <th>N° Documento</th>
                                                <th>Cliente</th>
                                                <th>Vehiculo </th>
                                                <th>N° Contrato</th>
                                                <th>Venta referencia</th>
                                                <th>Estado contrato</th>
                                                <th>Forma de pago</th>
                                                <th>Frecuencia</th>
                                                <th>Monto</th>
                                                <th width="180px;">Acciones</th>
                                            </thead>
                                            <tbody id="tbody_contratos">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2"></div>
                                <div class="col-md-6 mt-2">
                                    <div id="pagination"></div>
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
    <style>
        .pdf-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px 10px;
            background: #fff;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .pdf-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
        }

        .pdf-icon {
            font-size: 50px;
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .pdf-card p {
            margin: 0;
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }
    </style>
    <!-- modals -->
    <div class="modal fade" id="modal-ver-contrato" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Documentación del Contrato</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="min-height: 400px; overflow-y: auto;">
                    <div id="contratoContent" class="row text-center">

                        <div class="col-md-3 mb-4" id="btnDescargarContrato">
                            <div class="pdf-card">
                                <input type="hidden" id="idventa" />
                                <i class="fa fa-file-pdf pdf-icon"></i>
                                <p>Contrato</p>
                            </div>
                        </div>

                        <div class="col-md-3 mb-4" id="btnDescargarActaEntrega">
                            <div class="pdf-card">
                                <i class="fa fa-file-pdf pdf-icon"></i>
                                <p>Acta de entrega</p>
                            </div>
                        </div>

                        <div class="col-md-3 mb-4" id="btnDescargarOrdenRecojo">
                            <div class="pdf-card">
                                <i class="fa fa-file-pdf pdf-icon"></i>
                                <p>Orden de recojo</p>
                            </div>
                        </div>

                        <div class="col-md-3 mb-4" id="btnDescargarCronogramaPagos">
                            <div class="pdf-card">
                                <i class="fa fa-file-pdf pdf-icon"></i>
                                <p>Cronograma de pagos</p>
                            </div>
                        </div>

                        <div class="col-md-3 mb-4" id="btnDescargarCompraVenta">
                            <div class="pdf-card">
                                <i class="fa fa-file-pdf pdf-icon"></i>
                                <p>Compra venta</p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-retener-contrato" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Retener Contrato</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-retener-contrato" action="">
                    <div class="modal-body">
                        <div class="alert alert-warning" role="alert">
                            <strong>Advertencia:</strong> Retener un contrato significa que no se podrá realizar
                            ninguna acción adicional sobre él, como imprimir o descargar documentos relacionados.
                            Asegúrese de que esta es la acción correcta antes de proceder.
                        </div>
                        <input type="hidden" id="idventa_retenida" name="idventa" />
                        <div class="form-group">
                            <label for="motivoRetencion">Motivo de la retención:</label>
                            <textarea class="form-control" id="motivoRetencion" name="motivo" rows="3"
                                placeholder="Ingrese el motivo por el cual se retiene este contrato..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger" id="confirmarRetencion">Sí, Retener</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-compra-venta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Compra Venta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-compra-venta" action="">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="vendedor">Vendedor:</label>
                                    <select name="idvendedor" id="idvendedor" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="comprador">Comprador:</label>
                                    <div class="d-flex align-items-end">
                                        <input type="hidden" name="idcliente" id="idcliente" />
                                        <input type="text" name="comprador" id="comprador" class="form-control mr-2"
                                            readonly />
                                        <button type="button" class="btn btn-info mr-1" id="btnEditarComprador"
                                            title="Editar cliente"><i class="fas fa-edit"></i></button>
                                        <button type="button" class="btn btn-primary" id="btnNuevoComprador"
                                            title="Nuevo cliente"><i class="fas fa-user-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="idventa_compra_venta" name="idventa" />
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="monto_compra_venta">Monto:</label>
                                    <input type="text" class="form-control" id="monto_compra_venta"
                                        name="monto_compra_venta" placeholder="Ingrese el monto de la compra venta...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger" id="confirmarCompraVenta">Sí, Realizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-cliente-compra-venta" tabindex="-1" role="dialog"
        aria-labelledby="modalClienteCompraVentaLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClienteCompraVentaLabel">Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-cliente-compra-venta" action="">
                    <div class="modal-body">
                        <input type="hidden" name="idpersona" id="cliente_idpersona" value="">
                        <input type="hidden" name="tipo_persona" id="cliente_tipo_persona" value="Cliente">

                        <div class="form-group">
                            <label for="cliente_nombre">Nombre / Razón social:</label>
                            <input type="text" class="form-control" name="nombre" id="cliente_nombre" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-4">
                                <label for="cliente_tipo_documento">Tipo Doc:</label>
                                <select class="form-control" name="tipo_documento" id="cliente_tipo_documento">
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="CE">CE</option>
                                    <option value="OTROS">OTROS</option>
                                </select>
                            </div>
                            <div class="form-group col-8">
                                <label for="cliente_num_documento">N° Documento:</label>
                                <input type="text" class="form-control" name="num_documento" id="cliente_num_documento"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cliente_telefono">Teléfono:</label>
                            <input type="text" class="form-control" name="telefono" id="cliente_telefono">
                        </div>

                        <div class="form-group">
                            <label for="cliente_direccion">Dirección:</label>
                            <input type="text" class="form-control" name="direccion" id="cliente_direccion">
                        </div>

                        <div class="form-group mb-0">
                            <label for="cliente_email">Email:</label>
                            <input type="email" class="form-control" name="email" id="cliente_email">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarClienteCompraVenta">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCuotasCredito">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cuentas por Cobrar del Crédito: <strong id="tituloCreditoCuotas"></strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table id="tbllistadoCuotasCredito" class="table table-striped table-bordered" width="100%">
                        <thead>
                            <th>Fecha Registro</th>
                            <th>Fecha Vencimiento</th>
                            <th>Abonado</th>
                            <th>Deuda</th>
                            <th>Saldo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <th>Fecha Registro</th>
                            <th>Fecha Vencimiento</th>
                            <th>Abonado</th>
                            <th>Deuda</th>
                            <th>Saldo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalComentario">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cuentas por Cobrar del Crédito: <strong id="tituloCreditoCuotas"></strong>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="idventacuentacobrar">
                        <div class="col-md-12 mb-3">
                            <label>Comentario</label>
                            <textarea id="comentarioCredito" class="form-control" rows="3"
                                placeholder="Escribe un comentario..."></textarea>
                        </div>

                        <div class="col-md-12 text-right">
                            <button class="btn btn-primary" onclick="guardarComentarioCredito()">
                                <i class="fas fa-save"></i> Guardar comentario
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAmortizar">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="titulo-formulario-amortizar">Lista de</span> Abonos</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" role="form" name="formulario-amortizar" id="formulario-amortizar"
                    method="POST">
                    <div class="modal-body">

                        <input type="hidden" name="idcliente_amortizar" id="idcliente_amortizar">
                        <input type="hidden" name="idventa_amortizar" id="idventa_amortizar">
                        <input type="hidden" id="idcaja" name="idcaja">
                        <input type="hidden" name="fecha_inicio_amortizar" id="fecha_inicio_amortizar">
                        <input type="hidden" name="fecha_fin_amortizar" id="fecha_fin_amortizar">

                        <div class="alert" style="background: #E0F7FA;">
                            <strong><i class="fa fa-info"></i> Info!</strong> Amortizacion: tiene un pago pendiente
                            de S/ <label for="deudaTotalAmortizar" id="deudaTotalAmortizar"></label>, el cuál se
                            esta realizando una amortizacion;
                            A continuación Ingresa el total de dinero abonado y luego haz click en Guardar.
                        </div>

                        <div class="success-box-custom" id="panelDescuentoAmortizar">
                            <strong>
                                <i class="fas fa-hand-holding-usd"></i>
                                ¡Descuento por pago anticipado!
                            </strong>
                            <br>
                            Has obtenido un descuento de <strong>S/ <span id="montoDescuentoAmortizar"></span></strong>
                            por realizar el pagoantes de la fecha de vencimiento tu crédito.
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="name" class="control-label">Condición de Pago::</label>
                                    <select id="formapagoAmortizar" name="formapagoAmortizar"
                                        class="form-control selectpicker" data-live-search="true" required>

                                        <option value="Efectivo">En Efectivo</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="name" class="control-label">Monto a Pagar: </label>
                                    <input type="text" class="form-control" id="montoPagarAmortizar"
                                        name="montoPagarAmortizar" required="" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="name" class="control-label">Monto Adeudado:</label>
                                    <input class="form-control pull-right" type="text" name="montoAdeudadoAmortizar"
                                        id="montoAdeudadoAmortizar" readonly="">
                                </div>
                            </div>
                        </div>
                        <style>
                            .card-cuotas {
                                background: #ffffff;
                                border-radius: 16px;
                                padding: 25px;
                                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                                margin: auto;
                                font-family: Arial, sans-serif;
                            }

                            .titulo-cuotas {
                                margin-bottom: 20px;
                                color: #333;
                                text-align: center;
                            }

                            .range-container {
                                margin-bottom: 25px;
                            }

                            #rangeCuotas {
                                width: 100%;
                                accent-color: #2563eb;
                                cursor: pointer;
                            }

                            .info-cuotas {
                                display: flex;
                                gap: 15px;
                            }

                            .box-info {
                                flex: 1;
                                background: #f5f7fb;
                                padding: 15px;
                                border-radius: 12px;
                                text-align: center;
                            }

                            .box-info .label {
                                display: block;
                                font-size: 14px;
                                color: #666;
                                margin-bottom: 8px;
                            }

                            .box-info .valor {
                                font-size: 24px;
                                font-weight: bold;
                                color: #222;
                            }

                            .box-info.total {
                                background: #2563eb;
                            }

                            .box-info.total .label,
                            .box-info.total .valor {
                                color: white;
                            }
                        </style>
                        <div class="row" id="panel-pagar-cuotas">
                            <div class="col-sm-12">
                                <div class="card-cuotas">

                                    <h3 class="titulo-cuotas">
                                        Seleccionar cuotas
                                    </h3>

                                    <div id="contenedorRange" class="range-container"></div>

                                    <div class="info-cuotas">

                                        <div class="box-info">
                                            <span class="label">Cuotas a pagar</span>
                                            <input class="valor" type="text" id="cantidadSeleccionada"
                                                style="width: 80px; text-align: center;">
                                        </div>

                                        <div class="box-info total">
                                            <span class="label">Total a pagar</span>
                                            <span class="valor">S/ <span id="totalPagar">0.00</span></span>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button class="btn btn-primary" type="submit">Guardar</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="getCodeModal2">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="titulo-formulario">Lista de</span> Abonos</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
                    <div class="modal-body">

                        <input type="hidden" name="idcpc" id="idcpc">
                        <input type="hidden" id="idcaja" name="idcaja">
                        <input type="hidden" name="idventa" id="idventa">

                        <div class="alert" style="background: #E0F7FA;">
                            <strong><i class="fa fa-info"></i> Info!</strong> El monto total del documento
                            electrónico es de <label for="abonoTotal2" id="abonoTotal2"></label>, y se han
                            registrado abonos por un total de <label for="abonoTotal" id="abonoTotal"></label>.
                        </div>

                        <table id="tbllistadoAbonos"
                            class="table table-striped table-bordered table-condensed table-hover" width="100%">
                            <thead>
                                <th style="width: 100px;">Fecha Registro</th>
                                <th style="width: 25px;">Monto Efectivo</th>
                                <th style="width: 25px;">Monto Tarjeta</th>
                                <th style="width: 150px;">Forma de Pago</th>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Forma de Pago</th>
                            </tfoot>
                        </table>

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


    <div class="modal fade" id="modalEstadoCuenta">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Estado de Cuenta del Cliente</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div id="estadoCuentaContenido"></div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" onclick="imprimirEstadoCuenta()">Imprimir</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="getCodeModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fa fa-money"></i> Registro de Pago / Abono</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>

                <form class="form-horizontal" role="form" id="formulario-pagar" method="POST">

                    <div class="modal-body">

                        <!-- Campos ocultos -->
                        <input type="hidden" name="idcpc" id="idcpc2">
                        <input type="hidden" id="idcaja2" name="idcaja">
                        <input type="hidden" name="idventa" id="idventa2">
                        <style>
                            .doc-card {
                                border: 1px solid #e9edf3;
                                background: #fff;
                                border-radius: 12px;
                                padding: 14px;
                                box-shadow: 0 6px 18px rgba(16, 24, 40, .06);
                                max-width: 720px;
                            }

                            .doc-head {
                                display: flex;
                                gap: 12px;
                                align-items: center;
                                padding-bottom: 12px;
                                border-bottom: 1px dashed #e9edf3;
                            }

                            .doc-icon {
                                width: 40px;
                                height: 40px;
                                border-radius: 10px;
                                display: grid;
                                place-items: center;
                                background: rgba(25, 118, 210, .10);
                                color: #1976d2;
                                font-size: 18px;
                            }

                            .doc-title strong {
                                display: block;
                                font-size: 15px;
                                color: #0f172a;
                            }

                            .doc-sub {
                                margin-top: 2px;
                                font-size: 13px;
                                color: #64748b;
                            }

                            .doc-body {
                                padding-top: 12px;
                                display: grid;
                                gap: 12px;
                            }

                            .doc-alert {
                                background: #f8fafc;
                                border: 1px solid #eef2f7;
                                border-radius: 12px;
                                padding: 12px;
                            }

                            .doc-alert-row {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                gap: 10px;
                                padding: 4px 0;
                                font-size: 14px;
                                color: #334155;
                            }

                            .money {
                                color: #0f172a;
                            }

                            .doc-totals {
                                display: grid;
                                grid-template-columns: repeat(2, minmax(0, 1fr));
                                gap: 12px;
                            }

                            .total-box {
                                border: 1px solid #eef2f7;
                                border-radius: 12px;
                                padding: 12px;
                                background: #ffffff;
                            }

                            .total-label {
                                font-size: 12px;
                                color: #64748b;
                                margin-bottom: 6px;
                            }

                            .total-value {
                                font-size: 18px;
                                font-weight: 700;
                                color: #0f172a;
                            }

                            /* Responsive */
                            @media (max-width: 520px) {
                                .doc-totals {
                                    grid-template-columns: 1fr;
                                }
                            }
                        </style>

                        <div class="mb-2">
                            <div class="doc-totals">
                                <div class="total-box">
                                    <div class="total-label">Total venta</div>
                                    <div class="total-value">S/ <span id="valorVenta"></span></div>
                                </div>

                                <div class="total-box">
                                    <div class="total-label">Total interés</div>
                                    <div class="total-value">S/ <span id="valorInteres"></span></div>
                                </div>
                            </div>
                        </div>
                        <br />
                        <!-- Caja de información -->
                        <div class="info-box-custom">
                            <strong><i class="fa fa-info-circle"></i> Información del Documento</strong><br>
                            El documento <b><span id="documento"></span></b> tiene un pago pendiente de
                            <b>S/ <span id="deutaTotal"></span></b>.
                            Debe pagarse como máximo el día <b><span id="fechavencimiento"></span></b>.
                        </div>
                        <br />

                        <div class="warning-box-custom" id="panelMora">
                            <strong><i class="fa fa-exclamation-triangle"></i> Tiene mora </strong><br>
                            La cuota ha generado <b>S/<span id="montoMora"></span></b> de mora por <b><span
                                    id="diasRetraso"></span></b>
                            dias de retraso en el pago programado de los cuales falta pagar <b>S/<span
                                    id="montoMoraPagar"></span></b>.
                        </div>

                        <div class="success-box-custom" id="panelDescuento">
                            <strong>
                                <i class="fas fa-hand-holding-usd"></i>
                                ¡Descuento por pago anticipado!
                            </strong>
                            <br>

                            Has obtenido un descuento del <strong> <span id="porcentajeDescuento"></span>%</strong>
                            con valor de <strong>S/ <span id="montoDescuento"></span></strong>
                            por realizar el pago
                            <strong><span id="diasAnticipacion"></span> días antes</strong>
                            de la fecha de vencimiento.
                        </div>

                        <div class="section-title"><i class="fa fa-credit-card"></i> Datos del Pago</div>

                        <div class="row">

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Condición de Pago:</label>
                                    <select id="formapago" name="formapago" class="form-control" required>
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia">Transferencia o Tarjeta</option>
                                        <option value="Yape">Yape</option>
                                        <option value="Plin">Plin</option>
                                        <option value="Deposito">Depósito</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Monto Efectivo:</label>
                                    <input type="text" class="form-control" id="montoPagar" name="montoPagar" required>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Monto Tarjeta:</label>
                                    <input type="text" class="form-control" id="montoPagarTarjeta"
                                        name="montoPagarTarjeta" readonly>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Monto Adeudado:</label>
                                    <input class="form-control" type="text" name="montoAdeudado" id="montoAdeudado"
                                        readonly>
                                </div>
                            </div>

                        </div>

                        <div class="section-title"><i class="fa fa-pencil"></i> Observación</div>

                        <div class="row">
                            <div class="col-sm-12">
                                <textarea class="form-control" name="observacion" id="observacion" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="section-title"><i class="fa fa-building"></i> Pago Bancario</div>

                        <div class="row">

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Banco:</label>
                                    <select id="banco" name="banco" class="form-control selectpicker"
                                        data-live-search="true">
                                        <option value="">Seleccione...</option>
                                        <option value="BCP">BCP</option>
                                        <option value="INTERBANK">INTERBANK</option>
                                        <option value="BBVA">BBVA</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Número de Operación (OP):</label>
                                    <input class="form-control" type="text" name="op" id="op">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Fecha de Pago:</label>
                                    <input class="form-control" type="datetime-local" name="fechaPago" id="fechaPago"
                                        value="<?php echo date('Y-m-d H:i:s') ?>">
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" onclick="cancelarform()" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times"></i> Cerrar
                        </button>
                        <button class="btn btn-primary" type="submit" id="btnGuardar">
                            <i class="fa fa-check"></i> Guardar Pago
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<script src="vistas/js/contratos.js"></script>