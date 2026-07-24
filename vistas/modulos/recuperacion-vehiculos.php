<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Recuperacion de vehiculos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Creditos</li>
                        <li class="breadcrumb-item active">Recuperacion</li>
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
                    <div class="card card-outline">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="recuperacion-tab" role="tablist">

                                <li class="nav-item" onclick="initListarCandidatos()">
                                    <a class="nav-link active" id="tab-candidatos" data-toggle="pill" href="#candidatos"
                                        role="tab">
                                        <i class="fas fa-user-clock"></i>
                                        Candidatos
                                    </a>
                                </li>

                                <li class="nav-item" onclick="initListarPendientes()">
                                    <a class="nav-link" id="tab-gestion" data-toggle="pill" href="#gestion" role="tab">
                                        <i class="fas fa-phone"></i>
                                        En Gestión
                                    </a>
                                </li>

                                <li class="nav-item" onclick="initListarCompromisos()">
                                    <a class="nav-link" id="tab-compromisos" data-toggle="pill" href="#compromisos"
                                        role="tab">
                                        <i class="fas fa-calendar-check"></i>
                                        Compromisos
                                    </a>
                                </li>

                                <li class="nav-item" onclick="initListarRecuperados()">
                                    <a class="nav-link" id="tab-recuperados" data-toggle="pill" href="#recuperados"
                                        role="tab">
                                        <i class="fas fa-car"></i>
                                        Recuperados
                                    </a>
                                </li>

                                <li class="nav-item" onclick="initListarHistorial()">
                                    <a class="nav-link" id="tab-historial" data-toggle="pill" href="#historial"
                                        role="tab">
                                        <i class="fas fa-history"></i>
                                        Historial
                                    </a>
                                </li>

                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="candidatos">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <h3 class="card-title">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Candidatos a recuperación
                                            </h3>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-center">
                                            <span class="mr-2">Mostrar</span>
                                            <select id="limitCandidatos" class="form-control" style="width:100px">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>

                                            <span class="ml-2">Registros</span>

                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" id="searchCandidatos" class="form-control"
                                                placeholder="Buscar...">
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <table id="tbllistado_candidatos" class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Cliente</th>
                                                        <th>Documento</th>
                                                        <th>Vehículo</th>
                                                        <th>Placa</th>
                                                        <th>Cuotas vencidas</th>
                                                        <th>Días mora</th>
                                                        <th>Deuda</th>
                                                        <th>Riesgo</th>
                                                        <th>Acción</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="tbodyCandidatos"></tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <div id="paginationCandidatos"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="gestion">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <h3 class="card-title">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Lista de vehiculos en recuperación
                                            </h3>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-center">
                                            <span class="mr-2">Mostrar</span>
                                            <select id="limitPendientes" class="form-control" style="width:100px">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>

                                            <span class="ml-2">Registros</span>

                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" id="searchPendientes" class="form-control"
                                                placeholder="Buscar...">
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <table id="tbllistado_pendientes" class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Cliente</th>
                                                        <th>Documento</th>
                                                        <th>Vehículo</th>
                                                        <th>Placa</th>
                                                        <th>Cuotas vencidas</th>
                                                        <th>Días mora</th>
                                                        <th>Deuda</th>
                                                        <th>Riesgo</th>
                                                        <th>Acción</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="tbodyPendientes"></tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <div id="paginationPendientes"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="compromisos">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <h3 class="card-title">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Lista de registro de incidencias
                                            </h3>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-center">
                                            <span class="mr-2">Mostrar</span>
                                            <select id="limitCompromisos" class="form-control" style="width:100px">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>

                                            <span class="ml-2">Registros</span>

                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" id="searchCompromisos" class="form-control"
                                                placeholder="Buscar...">
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <table id="tbllistado_compromisos" class="table table-hover table-striped">
                                                <thead class="thead-dark">

                                                    <tr>

                                                        <th>#</th>

                                                        <th>Cliente</th>

                                                        <th>Vehículo</th>
                                                        <th>Detalle</th>

                                                        <th>Fecha compromiso</th>

                                                        <th>Monto</th>

                                                        <th>Deuda</th>

                                                        <th>Estado</th>

                                                        <th>Registrado por</th>

                                                        <th width="90">Acciones</th>

                                                    </tr>

                                                </thead>

                                                <tbody id="tbodyCompromisos"></tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <div id="paginationCompromisos"></div>
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane fade" id="recuperados">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <h3 class="card-title">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Lista de vehiculos recuperados
                                            </h3>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-center">
                                            <span class="mr-2">Mostrar</span>
                                            <select id="limitRecuperados" class="form-control" style="width:100px">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>

                                            <span class="ml-2">Registros</span>

                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" id="searchRecuperados" class="form-control"
                                                placeholder="Buscar...">
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <table id="tbllistado_recuperados" class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Cliente</th>
                                                        <th>Documento</th>
                                                        <th>Vehículo</th>
                                                        <th>Placa</th>
                                                        <th>Cuotas vencidas</th>
                                                        <th>Días mora</th>
                                                        <th>Deuda</th>
                                                        <th>Riesgo</th>
                                                        <th>Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyRecuperados"></tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <div id="paginationRecuperados"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="historial">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <h3 class="card-title">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Lista de historial de los vehiculos
                                            </h3>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-center">
                                            <span class="mr-2">Mostrar</span>
                                            <select id="limitHistorial" class="form-control" style="width:100px">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>

                                            <span class="ml-2">Registros</span>

                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" id="searchHistorial" class="form-control"
                                                placeholder="Buscar...">
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <table id="tbllistado_historial" class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Cliente</th>
                                                        <th>Documento</th>
                                                        <th>Vehículo</th>
                                                        <th>Placa</th>
                                                        <th>Cuotas vencidas</th>
                                                        <th>Días mora</th>
                                                        <th>Deuda</th>
                                                        <th>Riesgo</th>
                                                        <th>Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyHistorial"></tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <div id="paginationHistorial"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalCandidato">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">


            <!-- HEADER -->
            <div class="modal-header bg-primary text-white">

                <div>
                    <h5 class="modal-title mb-0">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Detalle del Crédito
                    </h5>

                    <small>
                        Información del cliente y cuentas por cobrar
                    </small>
                </div>


                <button type="button" class="close text-white" data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>



            <div class="modal-body bg-light">



                <!-- CLIENTE -->
                <div class="card shadow-sm mb-3 border-0">

                    <div class="card-body">

                        <div class="row">


                            <div class="col-md-4">

                                <div class="d-flex align-items-center">

                                    <div class="mr-3">

                                        <i class="fas fa-user-circle fa-3x text-primary"></i>

                                    </div>

                                    <div>

                                        <small class="text-muted">
                                            Cliente
                                        </small>

                                        <h5 class="mb-0" id="d_cliente">

                                        </h5>

                                        <span id="d_documento"></span>

                                    </div>

                                </div>

                            </div>



                            <div class="col-md-4">

                                <small class="text-muted">
                                    Teléfono
                                </small>

                                <h6>
                                    <i class="fas fa-phone text-success"></i>
                                    <span id="d_telefono"></span>
                                </h6>

                            </div>



                            <div class="col-md-4">

                                <small class="text-muted">
                                    Vehículo
                                </small>

                                <h6>
                                    <i class="fas fa-car text-primary"></i>
                                    <span id="d_vehiculo"></span>
                                </h6>

                                <span class="badge badge-dark">
                                    Placa:
                                    <span id="d_placa"></span>
                                </span>

                            </div>


                        </div>


                    </div>

                </div>





                <!-- DATOS VEHICULO -->
                <div class="card shadow-sm mb-3 border-0">

                    <div class="card-header bg-white">

                        <strong>
                            <i class="fas fa-cogs"></i>
                            Datos del vehículo
                        </strong>

                    </div>


                    <div class="card-body">


                        <div class="row text-center">


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Serie
                                </small>

                                <h6 id="d_serie"></h6>

                            </div>



                            <div class="col-md-4">

                                <small class="text-muted">
                                    Motor
                                </small>

                                <h6 id="d_motor"></h6>

                            </div>



                            <div class="col-md-4">

                                <small class="text-muted">
                                    Total crédito
                                </small>

                                <h4 class="text-primary">

                                    S/ <span id="d_total"></span>

                                </h4>

                            </div>


                        </div>


                    </div>

                </div>





                <!-- RESUMEN -->
                <div class="row mb-3">


                    <div class="col-md-3">

                        <div class="card shadow-sm border-left-primary">

                            <div class="card-body">

                                <small class="text-muted">
                                    Cuotas
                                </small>

                                <h3 id="d_cuotas">

                                </h3>

                            </div>

                        </div>

                    </div>




                    <div class="col-md-3">

                        <div class="card shadow-sm border-left-danger">

                            <div class="card-body">

                                <small class="text-muted">
                                    Saldo pendiente
                                </small>

                                <h4 class="text-danger">

                                    S/ <span id="d_saldo"></span>

                                </h4>

                            </div>

                        </div>

                    </div>




                    <div class="col-md-3">

                        <div class="card shadow-sm border-left-warning">

                            <div class="card-body">

                                <small class="text-muted">
                                    Mora
                                </small>

                                <h4 class="text-warning">

                                    S/ <span id="d_mora"></span>

                                </h4>

                            </div>

                        </div>

                    </div>




                    <div class="col-md-3">

                        <div class="card shadow-sm border-left-success">

                            <div class="card-body">

                                <small class="text-muted">
                                    Abonado
                                </small>

                                <h4 class="text-success">

                                    S/ <span id="d_abonado"></span>

                                </h4>

                            </div>

                        </div>

                    </div>


                </div>





                <!-- CUOTAS -->
                <div class="card shadow-sm border-0">


                    <div class="card-header bg-white">

                        <strong>
                            <i class="fas fa-calendar-alt"></i>
                            Cuentas por cobrar
                        </strong>

                    </div>



                    <div class="card-body p-0">


                        <div class="table-responsive">


                            <table class="table table-hover mb-0">


                                <thead class="thead-dark">

                                    <tr>

                                        <th>#</th>
                                        <th>Vencimiento</th>
                                        <th>Días atraso</th>
                                        <th>Total letra</th>
                                        <th>Deuda</th>
                                        <th>Abonado</th>
                                        <th>Estado</th>

                                    </tr>


                                </thead>



                                <tbody id="tablaCuotas">


                                </tbody>


                            </table>


                        </div>


                    </div>


                </div>



            </div>




            <div class="modal-footer bg-white">


                <button class="btn btn-secondary" data-dismiss="modal">

                    <i class="fas fa-times"></i>
                    Cerrar

                </button>


                <button class="btn btn-success">

                    <i class="fab fa-whatsapp"></i>
                    Contactar cliente

                </button>


            </div>


        </div>
    </div>
</div>

<div class="modal fade" id="modalVerCompromiso">

    <div class="modal-dialog modal-xl">

        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-primary text-white">

                <div>

                    <h4 class="mb-0">
                        <i class="fas fa-handshake mr-2"></i>
                        Detalle del Compromiso
                    </h4>

                    <small class="text-white-50">
                        Información completa del compromiso de pago
                    </small>

                </div>

                <button class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <!-- Cliente -->

                <div class="card shadow-sm mb-3">

                    <div class="card-header bg-light">

                        <strong>
                            <i class="fas fa-user mr-2 text-primary"></i>
                            Cliente
                        </strong>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <small class="text-muted">
                                    Cliente
                                </small>

                                <h5 id="v_cliente"></h5>

                            </div>

                            <div class="col-md-3">

                                <small class="text-muted">
                                    Documento
                                </small>

                                <h6 id="v_documento"></h6>

                            </div>

                            <div class="col-md-3">

                                <small class="text-muted">
                                    Registrado por
                                </small>

                                <h6 id="v_usuario"></h6>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Vehículo -->

                <div class="card shadow-sm mb-3">

                    <div class="card-header bg-light">

                        <strong>

                            <i class="fas fa-motorcycle mr-2 text-success"></i>

                            Vehículo

                        </strong>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-8">

                                <small class="text-muted">

                                    Vehículo

                                </small>

                                <h5 id="v_vehiculo"></h5>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted">

                                    Placa

                                </small>

                                <h5 id="v_placa"></h5>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Resumen -->

                <div class="card shadow-sm mb-3">

                    <div class="card-header bg-light">

                        <strong>

                            <i class="fas fa-file-invoice-dollar mr-2 text-warning"></i>

                            Resumen del Compromiso

                        </strong>

                    </div>

                    <div class="card-body">

                        <div class="row text-center">

                            <div class="col-md-3">

                                <small class="text-muted">

                                    Fecha

                                </small>

                                <h5 id="v_fecha"></h5>

                            </div>

                            <div class="col-md-3">

                                <small class="text-muted">

                                    Monto

                                </small>

                                <h4 class="text-success">

                                    <span id="v_monto"></span>

                                </h4>

                            </div>

                            <div class="col-md-3">

                                <small class="text-muted">

                                    Deuda

                                </small>

                                <h4 class="text-danger">

                                    <span id="v_deuda"></span>

                                </h4>

                            </div>

                            <div class="col-md-3">

                                <small class="text-muted">

                                    Estado

                                </small>

                                <div id="v_estado"></div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- Detalle -->

                <div class="card shadow-sm">

                    <div class="card-header bg-light">

                        <strong>

                            <i class="fas fa-align-left mr-2 text-info"></i>

                            Información adicional

                        </strong>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-12 mb-3">

                                <small class="text-muted">

                                    Detalle

                                </small>

                                <div class="border rounded p-3 bg-light" id="v_detalle">

                                </div>

                            </div>

                            <div class="col-md-12 mb-3">

                                <small class="text-muted">

                                    Observación

                                </small>

                                <div class="border rounded p-3 bg-light" id="v_observacion">

                                </div>

                            </div>

                            <div class="col-md-4">

                                <small class="text-muted">

                                    Fecha de cumplimiento

                                </small>

                                <h5 id="v_cumplimiento"></h5>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-dismiss="modal">

                    <i class="fas fa-times mr-1"></i>

                    Cerrar

                </button>

            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="modalRecuperacion">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h4 class="modal-title">

                    <i class="fas fa-folder-open mr-2"></i>

                    Expediente de Recuperación

                </h4>

                <button class="close text-white" data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <div class="row mb-3">

                    <div class="col-md-3">

                        <small class="text-muted">Cliente</small>

                        <h5 id="r_cliente"></h5>

                    </div>

                    <div class="col-md-2">

                        <small class="text-muted">Documento</small>

                        <h5 id="r_documento"></h5>

                    </div>

                    <div class="col-md-2">

                        <small class="text-muted">Teléfono</small>

                        <h5 id="r_telefono"></h5>

                    </div>

                    <div class="col-md-3">

                        <small class="text-muted">Vehículo</small>

                        <h5 id="r_vehiculo"></h5>

                    </div>

                    <div class="col-md-2">

                        <small class="text-muted">Placa</small>

                        <h5 id="r_placa"></h5>

                    </div>

                </div>

                <div class="row text-center mb-4">

                    <div class="col">

                        <small>Días Mora</small>

                        <h4 id="r_dias"></h4>

                    </div>

                    <div class="col">

                        <small>Deuda</small>

                        <h4 class="text-danger" id="r_deuda"></h4>

                    </div>

                    <div class="col">

                        <small>Mora</small>

                        <h4 class="text-warning" id="r_mora"></h4>

                    </div>

                    <div class="col">

                        <small>Estado</small>

                        <div id="r_estado"></div>

                    </div>

                    <div class="col">

                        <small>Riesgo</small>

                        <div id="r_riesgo"></div>

                    </div>

                </div>

                <ul class="nav nav-tabs">

                    <li class="nav-item">

                        <a class="nav-link active" data-toggle="tab" href="#tabSeguimientos">

                            Seguimientos

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link" data-toggle="tab" href="#tabCompromisos">

                            Compromisos

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link" data-toggle="tab" href="#tabAdjuntos">

                            Adjuntos

                        </a>

                    </li>
                    <li class="nav-item">

                        <a class="nav-link" data-toggle="tab" href="#tabDocumentosLegales">
                            Documentos legales
                        </a>

                    </li>

                </ul>

                <div class="tab-content mt-3">

                    <div class="tab-pane fade show active" id="tabSeguimientos">

                        <table class="table table-sm table-bordered" id="tblSeguimientos">

                            <thead>

                                <tr>

                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Expediente</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>

                                </tr>

                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>

                    <div class="tab-pane fade" id="tabCompromisos">

                        <table class="table table-sm table-bordered" id="tblCompromisos">

                            <thead>

                                <tr>

                                    <th>#</th>
                                    <th>Fecha</th>
                                    <th>Detalle</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Usuario</th>

                                </tr>

                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>

                    <div class="tab-pane fade" id="tabAdjuntos">

                        <table class="table table-sm table-bordered" id="tblAdjuntos">

                            <thead>

                                <tr>

                                    <th>#</th>
                                    <th>Archivo</th>
                                    <th>Fecha</th>
                                    <th></th>

                                </tr>

                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>
                    <div class="tab-pane fade" id="tabDocumentosLegales">

                        <table class="table table-sm table-bordered" id="tblDocumentosLegales">

                            <thead>

                                <tr>

                                    <th>#</th>
                                    <th>Tipo</th>
                                    <th>Descripcion</th>
                                    <th>Fecha</th>
                                    <th></th>

                                </tr>

                            </thead>

                            <tbody></tbody>

                        </table>

                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="modalProgramarVisita" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check"></i>
                    Programar evento
                </h5>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="formProgramarVisita" enctype="multipart/form-data">

                <div class="modal-body">
                    <input type="hidden" name="idventa" id="idventa_visita">
                    <input type="hidden" name="idcliente" id="idcliente_visita">
                    <input type="hidden" name="idrecuperacion" id="idrecuperacion_visita">

                    <div class="row">
                        <div class="col-md-7">
                            <div class="row">
                                <!-- RESPONSABLE -->
                                <div class="form-group col-md-6">
                                    <label> Responsable</label>
                                    <select class="form-control" name="idpersonal" id="idpersonal">
                                        <option value="">Seleccione</option>
                                        <!-- CARGAR USUARIOS -->
                                    </select>
                                </div>

                                <!-- TIPO -->
                                <div class="form-group col-md-6">
                                    <label>Tipo visita</label>
                                    <select class="form-control" name="tipo_visita" id="tipo_visita">
                                        <option value="VISITA">
                                            VISITA
                                        </option>
                                        <option value="COBRANZA">
                                            COBRANZA
                                        </option>
                                        <option value="REUNION">
                                            REUNION
                                        </option>
                                        <option value="LLAMADA">
                                            LLAMADA
                                        </option>
                                        <option value="WHATSAPP">
                                            WHATSAPP
                                        </option>
                                        <option value="CORREO">
                                            CORREO
                                        </option>
                                        <option value="VERIFICACION">
                                            VERIFICACION
                                        </option>
                                        <option value="SEGUIMIENTO">
                                            SEGUIMIENTO
                                        </option>

                                        <option value="NEGOCIACION">
                                            NEGOCIACION
                                        </option>
                                        <option value="COBRANZA">
                                            OTRO
                                        </option>

                                    </select>
                                </div>

                                <!-- PRIORIDAD -->
                                <div class="form-group col-md-6">
                                    <label>Prioridad</label>
                                    <select class="form-control" name="prioridad" id="prioridad">
                                        <option value="BAJA">Baja</option>
                                        <option value="MEDIA">Media</option>
                                        <option value="ALTA">Alta</option>
                                        <option value="URGENTE">Urgente</option>
                                    </select>
                                </div>

                                <!-- PRIORIDAD -->
                                <div class="form-group col-md-6">
                                    <label>Estado</label>

                                    <select class="form-control" name="estado" id="estado">

                                        <option value="PENDIENTE">PENDIENTE</option>
                                        <option value="REALIZADO">REALIZADO</option>
                                        <option value="NO_RESPONDE">NO RESPONDE</option>
                                        <option value="REPROGRAMADO">REPROGRAMADO</option>

                                    </select>
                                </div>


                                <!-- FECHA -->
                                <div class="form-group col-md-6">
                                    <label>
                                        Fecha visita <span class="text-danger">*</span>
                                    </label>

                                    <input type="datetime-local" class="form-control" name="fecha_programada"
                                        id="fecha_programada" required>
                                </div>


                                <!-- FECHA -->
                                <div class="form-group col-md-6">
                                    <label>
                                        Fecha final
                                    </label>
                                    <input type="datetime-local" class="form-control" name="fecha_final"
                                        id="fecha_final">
                                </div>

                                <!-- DIRECCION -->
                                <div class="form-group col-md-12">
                                    <label>Dirección</label>

                                    <input type="text" class="form-control" name="direccion" id="direccion"
                                        placeholder="Ingrese dirección de visita">
                                </div>

                                <!-- OBSERVACION -->
                                <div class="form-group col-md-12">
                                    <label>Observación</label>

                                    <textarea class="form-control" name="descripcion" id="descripcion" rows="4"
                                        placeholder="Detalle de la visita..."></textarea>
                                </div>
                            </div>
                        </div>

                        <style>
                            .upload-box {
                                border: 2px dashed #f0ad4e;
                                border-radius: 15px;
                                background: #fffaf2;
                                cursor: pointer;
                                transition: .3s;
                            }

                            .upload-box:hover {
                                background: #fff3df;
                                border-color: #ec971f;
                            }

                            .preview-item {
                                border: 1px solid #eee;
                                border-radius: 12px;
                                padding: 10px;
                                background: white;
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                margin-bottom: 10px;
                                box-shadow: 0 2px 5px rgba(0, 0, 0, .05);
                            }

                            .preview-left {
                                display: flex;
                                align-items: center;
                                gap: 10px;
                            }

                            .preview-left i {
                                font-size: 30px;
                            }

                            .btn-delete-file {
                                border: none;
                                background: #dc3545;
                                color: white;
                                width: 30px;
                                height: 30px;
                                border-radius: 50%;
                            }
                        </style>
                        <div class="col-md-5">
                            <div class="row">
                                <!-- ADJUNTOS -->
                                <div class="form-group col-md-12">

                                    <label>
                                        Adjuntar archivos
                                    </label>

                                    <div class="custom-file-upload">

                                        <input type="file" id="adjuntos" class="d-none" multiple accept="
                .pdf,
                .doc,
                .docx,
                .xls,
                .xlsx,
                .jpg,
                .jpeg,
                .png,
                .webp,
                .mp4,
                .mp3
            ">

                                        <label for="adjuntos" class="upload-box w-100">

                                            <div class="text-center p-4">

                                                <i class="fas fa-cloud-upload-alt fa-3x text-warning mb-3"></i>

                                                <h5 class="mb-2">
                                                    Subir documentos
                                                </h5>

                                                <p class="text-muted mb-2">
                                                    Agrega múltiples archivos
                                                </p>

                                                <span class="badge badge-warning p-2">
                                                    PDF · Word · Excel · Imágenes · Audio · Video
                                                </span>

                                            </div>

                                        </label>

                                    </div>

                                    <!-- LISTA -->
                                    <div id="previewArchivos" class="row mt-3"></div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">

                        <i class="fas fa-times"></i>
                        Cerrar
                    </button>

                    <button type="submit" class="btn btn-warning">

                        <i class="fas fa-save"></i>
                        Guardar programación
                    </button>

                </div>

            </form>
            <!-- ADJUNTOS -->

        </div>
    </div>
</div>

<div class="modal fade" id="modalEstadoRecuperacion">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h5 class="modal-title">
                    Actualizar Estado
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <input type="hidden" id="idrecuperacion_estado">

                <div class="form-group">

                    <label>Estado</label>

                    <select id="estadoRecuperacion" class="form-control">

                        <option value="PENDIENTE">
                            Pendiente
                        </option>

                        <option value="CONTACTADO">
                            Contactado
                        </option>

                        <option value="NEGOCIACION">
                            Negociación
                        </option>

                        <option value="VISITA_PROGRAMADA">
                            Visita Programada
                        </option>

                        <option value="RECUPERADO">
                            Recuperado
                        </option>

                        <option value="CERRADO">
                            Cerrado
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Observación</label>

                    <textarea id="observacionRecuperacion" class="form-control" rows="4"></textarea>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-dismiss="modal">

                    Cancelar

                </button>

                <button class="btn btn-success" onclick="guardarEstadoRecuperacion()">

                    <i class="fas fa-save"></i>
                    Guardar

                </button>

            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="modalDocumentoRecuperacion">

    <div class="modal-dialog">

        <div class="modal-content">

            <form id="formDocumentoRecuperacion" enctype="multipart/form-data">

                <div class="modal-header bg-warning">

                    <h5 class="modal-title">
                        <i class="fas fa-paperclip"></i>
                        Adjuntar Documento
                    </h5>

                    <button type="button" class="close" data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <input type="hidden" name="idrecuperacion" id="idrecuperacion_documento">

                    <div class="form-group">

                        <label>Tipo de documento</label>

                        <select class="form-control" name="tipo" required>

                            <option value="">Seleccione...</option>

                            <option value="NOTIFICACION">
                                Notificación
                            </option>

                            <option value="CARTA_NOTARIAL">
                                Carta Notarial
                            </option>

                            <option value="ACTA_VISITA">
                                Acta de Visita
                            </option>

                            <option value="ACTA_ENTREGA">
                                Acta de Entrega
                            </option>

                            <option value="DENUNCIA">
                                Denuncia
                            </option>

                            <option value="PODER">
                                Poder
                            </option>

                            <option value="CONTRATO">
                                Contrato
                            </option>

                            <option value="FOTO">
                                Fotografía
                            </option>

                            <option value="OTRO">
                                Otro
                            </option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label>Descripción</label>

                        <textarea class="form-control" name="descripcion" rows="3"></textarea>

                    </div>

                    <div class="form-group">

                        <label>Archivo</label>

                        <input type="file" class="form-control" name="archivo" required>

                        <small class="text-muted">
                            PDF, Word, Excel o imágenes.
                        </small>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="submit" class="btn btn-success">

                        <i class="fas fa-save"></i>

                        Guardar

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script src="vistas/js/recuperacion.js"></script>