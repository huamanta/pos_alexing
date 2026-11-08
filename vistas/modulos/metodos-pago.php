<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Métodos de pago</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="inicio">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="#">Facturación y cajas</a></li>
                        <li class="breadcrumb-item active">Métodos de pago</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" id="panelMetodosPago">
                        <div class="card-header">
                            <h3 class="card-title">Métodos disponibles</h3>
                        </div>
                        <div class="card-body">
                            <div class="row" id="listaMetodosPago">
                                <div class="col-12 text-center text-muted py-4">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Cargando métodos de pago...
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" id="panelDetalleMetodoPago" style="display:none;">
                        <div class="card-header d-flex align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i id="detalleMetodoIcono" class="fas fa-wallet mr-1"></i>
                                    <span id="detalleMetodoNombre">Detalle del método</span>
                                </h5>
                                <small class="text-muted">Movimientos registrados para este método de pago</small>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm ml-auto" onclick="regresarMetodosPago()">
                                <i class="fa fa-arrow-left"></i> Regresar
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-list-alt fa-lg text-primary mr-3"></i>
                                            <div>
                                                <small class="text-muted d-block">Movimientos</small>
                                                <h4 class="mb-0 font-weight-bold" id="resumenTotalMovimientos">0</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-arrow-down fa-lg text-success mr-3"></i>
                                            <div>
                                                <small class="text-muted d-block">Ingresos</small>
                                                <h4 class="mb-0 font-weight-bold text-success" id="resumenIngresos">S/ 0.00</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-arrow-up fa-lg text-danger mr-3"></i>
                                            <div>
                                                <small class="text-muted d-block">Egresos</small>
                                                <h4 class="mb-0 font-weight-bold text-danger" id="resumenEgresos">S/ 0.00</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <div class="border rounded p-3 h-100">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-wallet fa-lg text-info mr-3"></i>
                                            <div>
                                                <small class="text-muted d-block">Saldo neto</small>
                                                <h4 class="mb-0 font-weight-bold text-info" id="resumenNeto">S/ 0.00</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="mb-2">Lista de movimientos</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Responsable</th>
                                            <th>Origen</th>
                                            <th>Cliente</th>
                                            <th>Descripción</th>
                                            <th>Tipo</th>
                                            <th class="text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDetalleMetodosPago"></tbody>
                                </table>
                            </div>
                            <div id="paginationDetalleMetodosPago"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="vistas/js/metodos-pago.js"></script>
