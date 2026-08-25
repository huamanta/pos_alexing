<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bancos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Facturacion y cajas</a></li>
                        <li class="breadcrumb-item active">Bancos</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card" id="panelBancos">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 d-flex align-items-center">
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
                                    <input type="text" id="search" class="form-control" placeholder="Buscar...">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <table id="tbllistado" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th>N° cuenta</th>
                                                <th>CCI</th>
                                                <th>Saldo</th>
                                                <th style="width: 120px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyBancos">
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
                    <div class="card" id="panelMovimientoBancos">
                        <div class="card-header d-flex align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i class="fa fa-university"></i>
                                    Detalle del banco
                                </h5>
                                <small class="text-muted">
                                    Información de la cuenta y movimientos registrados
                                </small>
                            </div>

                            <button
                                type="button"
                                class="btn btn-secondary btn-sm ml-auto"
                                onclick="regresarBancos()"
                            >
                                <i class="fa fa-arrow-left"></i>
                                Regresar
                            </button>
                        </div>

                        <div class="card-body">

                            <!-- Detalle del banco -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Banco</label>
                                        <div id="detalleBancoNombre">
                                            -
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <div id="detalleBancoDescripcion">
                                            -
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>N° Cuenta</label>
                                        <div id="detalleBancoCuenta">
                                            -
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>CCI</label>
                                        <div id="detalleBancoCci">
                                            -
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3"
                                            style="
                                                width: 42px;
                                                height: 42px;
                                                border-radius: 8px;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                background: #e8f5e9;
                                            ">
                                            <i class="fa fa-file-invoice-dollar fa-lg text-success"></i>
                                        </div>

                                        <div>
                                            <strong class="text-muted d-block">
                                                Saldo actual
                                            </strong>

                                            <h4
                                                class="mb-0 font-weight-bold text-success"
                                                id="detalleBancoSaldo"
                                            >
                                                S/ 0.00
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br>

                            <!-- Movimientos -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">
                                    <label>Lista de movimientos</label>
                                </h5>
                            </div>

                            <div class="table-responsive">
                                <table
                                    id="tbllistadoMovimientos"
                                    class="table table-striped table-bordered"
                                >
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Responsable</th>
                                            <th>Ingreso</th>
                                            <th>Salida</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbodyBancoMovimientos">
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="vistas/js/bancos.js"></script>