<div class="content-wrapper">

    <!-- HEADER -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Refinanciar deuda</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Cuentas por Cobrar</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php
            $refinanciamiento = Helpers::verificarRefinanciamientos($_SESSION['idsucursal']);
            if (!$refinanciamiento['activo']) {
                ?>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

                <style>
                    .ref-wrapper {
                        min-height: 75vh;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        padding: 30px;
                        background: #fff;
                    }

                    .ref-card {
                        width: 100%;
                        max-width: 900px;
                        text-align: center;
                    }

                    .ref-icon {
                        position: relative;
                        width: 150px;
                        height: 150px;
                        margin: 0 auto 25px;
                        border-radius: 50%;
                        background: linear-gradient(180deg, #fff9e8, #fff4d2);
                        border: 5px solid #ffd56b;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }

                    .ref-icon i.wallet {
                        font-size: 80px;
                        color: #f7a81b;
                    }

                    .ref-icon .close {
                        position: absolute;
                        right: 15px;
                        bottom: 15px;
                        width: 58px;
                        height: 58px;
                        border-radius: 50%;
                        background: #ef5350;
                        color: #fff;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        font-size: 28px;
                        border: 4px solid #fff;
                        box-shadow: 0 6px 18px rgba(0, 0, 0, .15);
                    }

                    .ref-title {
                        font-size: 28px;
                        font-weight: 800;
                        color: #16243d;
                        margin-bottom: 20px;
                    }

                    .ref-divider {
                        width: 180px;
                        margin: 25px auto;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .ref-divider::before,
                    .ref-divider::after {
                        content: "";
                        flex: 1;
                        height: 4px;
                        background: #f5b63f;
                        border-radius: 5px;
                    }

                    .ref-divider span {
                        width: 12px;
                        height: 12px;
                        background: #f5b63f;
                        border-radius: 50%;
                        margin: 0 12px;
                    }

                    .ref-text {
                        font-size: 18px;
                        color: #48566d;
                        line-height: 1.7;
                        max-width: 900px;
                        margin: auto;
                    }

                    .ref-alert {
                        margin: 25px auto 0;
                        max-width: 520px;
                        display: flex;
                        align-items: center;
                        gap: 25px;
                        background: #eef5ff;
                        border: 2px solid #d7e6ff;
                        border-radius: 18px;
                        padding: 8px 5px;
                        text-align: left;
                    }

                    .ref-alert .info {
                        width: 62px;
                        height: 62px;
                        border-radius: 50%;
                        background: #2563eb;
                        color: #fff;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        font-size: 20px;
                        flex-shrink: 0;
                    }

                    .ref-alert p {
                        margin: 0;
                        color: #0f4fd8;
                        font-size: 17px;
                        font-weight: 500;
                    }

                    @media(max-width:768px) {

                        .ref-title {
                            font-size: 20px;
                        }

                        .ref-text {
                            font-size: 20px;
                        }

                        .ref-alert p {
                            font-size: 10px;
                        }

                        .ref-icon {
                            width: 140px;
                            height: 140px;
                        }

                        .ref-icon i.wallet {
                            font-size: 60px;
                        }
                    }
                </style>

                <div class="ref-wrapper">

                    <div class="ref-card">

                        <div class="ref-icon">

                            <i class="fa-solid fa-wallet wallet"></i>

                            <div class="close">
                                <i class="fa-solid fa-xmark"></i>
                            </div>

                        </div>

                        <h1 class="ref-title">
                            Refinanciamientos no activos
                        </h1>

                        <div class="ref-divider">
                            <span></span>
                        </div>

                        <div class="ref-text">
                            Los refinanciamientos no están activos para esta sucursal.<br>
                            Si considera que se trata de un error, comuníquese con el administrador del sistema.
                        </div>

                        <div class="ref-alert">

                            <div class="info">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>

                            <p>
                                Esta funcionalidad no se encuentra habilitada en este momento.
                            </p>

                        </div>

                    </div>

                </div>
                <?php
            } else {
                ?>

                <div id="panelBusqueda">
                    <!-- CARD: BUSCAR -->
                    <div class="card card-primary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">🔎 Buscar Crédito</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-4">
                                    <label>Documento / Cliente</label>
                                    <input type="text" id="buscar" class="form-control" placeholder="DNI, RUC o Cliente">
                                </div>

                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button class="btn btn-primary btn-block" id="btnBuscar">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- CARD: LISTA CREDITOS -->
                    <div class="card card-info shadow-sm" id="listaCreditos" style="display:none;">
                        <div class="card-header">
                            <h3 class="card-title">📋 Créditos encontrados</h3>
                        </div>

                        <div class="card-body table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Venta</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Pagado</th>
                                        <th>Saldo</th>
                                        <th>Refinanciado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tblCreditos"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="preload-carga"></div>
                    <div id="emptyCreditos">

                        <div class="text-center p-5">

                            <i class="fa fa-folder-open text-muted" style="font-size: 60px;"></i>

                            <h4 class="mt-3 text-muted">No se encontraron créditos</h4>

                            <p class="text-muted">
                                La lista de creditos esta vacia, realize una busqueda para mostrar resultados
                            </p>

                        </div>

                    </div>
                </div>
                <!-- CARD: DETALLE -->
                <div id="panelDetalle" style="display:none;">
                    <!-- BOTÓN REGRESAR -->
                    <div class="mb-2">
                        <button class="btn btn-secondary" id="btnVolver">
                            🔙 Volver
                        </button>
                    </div>
                    <!-- RESUMEN -->
                    <div class="card card-outline card-secondary shadow-sm">
                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-3">
                                    <label>Cliente</label>
                                    <input class="form-control" id="cliente" readonly>
                                </div>

                                <div class="col-md-2">
                                    <label>Documento</label>
                                    <input class="form-control" id="documento" readonly>
                                </div>

                                <div class="col-md-2">
                                    <label>Total</label>
                                    <input class="form-control" id="credito" readonly>
                                </div>

                                <div class="col-md-2">
                                    <label>Pagado</label>
                                    <input class="form-control" id="pagado" readonly>
                                </div>

                                <div class="col-md-2">
                                    <label>Saldo</label>
                                    <input class="form-control" id="saldo" readonly>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- CUOTAS + REFINANCIAMIENTO -->
                    <div class="row">

                        <!-- CUOTAS -->
                        <div class="col-md-7">

                            <div class="card card-primary shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title">📅 Cuotas actuales</h3>
                                </div>

                                <div class="card-body table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Vence</th>
                                                <th>Monto</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tblCuotas"></tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <!-- REFINANCIAMIENTO -->
                        <div class="col-md-5">

                            <div class="card card-success shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title">💰 Nuevo Plan</h3>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label>Saldo</label>
                                            <input id="montoDeuda" name="montoDeuda" class="form-control" readonly>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Cuota Inicial</label>
                                            <input id="inicial" name="inicial" class="form-control" value="0">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Frecuencia</label>
                                            <select name="input_frecuencia" id="input_frecuencia" class="form-control"
                                                placeholder="Frecuencia">
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
                                        <div class="form-group col-md-6">
                                            <label>N° Cuotas</label>
                                            <select name="input_cuotas" id="input_cuotas" class="form-control"
                                                placeholder="Cuotas">
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

                                        <div class="form-group col-md-6">
                                            <label>N° meses</label>
                                            <input id="numeroMeses" name="numeroMeses" class="form-control" value="0">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Interés</label>
                                            <input id="inputInteres" name="inputInteres" class="form-control" value="0">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Fecha Inicio</label>
                                            <input type="date" id="fechaOperacion" name="fechaOperacion"
                                                class="form-control">
                                        </div>

                                        <button class="btn btn-success btn-block" id="btnGenerar">
                                            Simular
                                        </button>

                                        <hr>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Monto</th>
                                                        <th>Interes</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="cronograma"></tbody>
                                            </table>
                                        </div>

                                        <button class="btn btn-primary btn-block mt-2" id="btnGuardar">
                                            Guardar Refinanciamiento
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
                <?php
            }
            ?>
        </div>
    </section>

</div>

<script src="vistas/js/refinanciamientos.js"></script>