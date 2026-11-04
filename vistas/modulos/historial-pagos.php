<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Historial de Pagos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item">Creditos</li>
                        <li class="breadcrumb-item active">Historial de Pagos</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
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

                                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
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
                                <div class="col-md-6">
                                </div>
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
                                    <div class="table-responsive">
                                        <table id="tblhistorialpagos" class="table table-striped">
                                            <thead>
                                                <th>Fecha de registro</th>
                                                <th>Cliente</th>
                                                <th>Fecha de pago</th>
                                                <th>Descripción</th>
                                                <th>Metodo</th>
                                                <th>Efectivo</th>
                                                <th>Transferencia</th>
                                                <th>Total</th>
                                                <th>Operacion</th>
                                                <th>Banco</th>
                                                <th>Comprobante</th>
                                            </thead>
                                            <tbody id="tbodyHistorialPagos">
                                                <!-- Contenido generado dinámicamente -->
                                            </tbody>
                                        </table>
                                    </div>
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
<script type="text/javascript" src="<?= $rutas->asset('vistas/js/historial-pagos.js') ?>"></script>