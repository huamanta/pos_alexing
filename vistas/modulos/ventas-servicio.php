<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reporte de Ventas por fecha, servicio y vendedor</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Reporte de Ventas por fecha, servicio y vendedor</li>
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
                            <h3 class="card-title"> </h3>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">

                            <div class="row">

                                <div class="form-group col-lg-6 col-md-6 col-sm-4 col-xs-12">
                                    <label>Servicios:</label>

                                    <div class="input-group mb-3">
                                        <select id="idproducto" name="idproducto" class="form-control select2" required>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-lg-6 col-md-6 col-sm-4 col-xs-12">

                                    <label>Vendedor:</label>

                                    <div class="input-group mb-3">
                                        <select id="idvendedor" name="idvendedor" class="form-control select2" required>
                                        </select>
                                        <span class="input-group-append">
                                            <button type="button" class="btn btn-info btn-flat btn-sm" onclick="listar()"><i class="fas fa-search"></i> Mostrar</button>
                                        </span>
                                    </div>

                                </div>

                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
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

                                <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
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
                                                <i class="fas fa-store-alt"></i>
                                            </span>
                                        </div>
                                        <select id="idsucursal2" name="idsucursal2" class="form-control">
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <br>

                            <table id="tbllistado" class="table table-striped">
                                <thead>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Total Venta</th>
                                    <th>Total Compra</th>
                                    <th>Utilidad</th>
                                    <th>Vendedor</th>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <th colspan="2">SUMA</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>
                                    <th>Total Compra</th>
                                    <th>Utilidad</th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.card-body -->
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

<script src="vistas/js/ventas-servicio.js"></script>