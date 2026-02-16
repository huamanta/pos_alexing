<!-- Content Wrapper. Contains page content -->
<?php
date_default_timezone_set('America/Lima');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cajas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Cajas</li>
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
                        <!-- /.card-header -->
                        <div class="card-body">

                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">lista de cajas</button>
                                </li>
                                <li class="nav-item" role="presentation" onclick="historial()">
                                    <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Historial</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">


                                    <div class="card-header">
                                        <h3 class="card-title"> </h3>

                                        <div class="row">
                                            <div class="col-md-11">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-outline-primary btn-block btn-xs" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Nuevo</button>
                                            </div>
                                        </div>

                                    </div><br>
                                    <table id="tbllistado" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Numero</th>
                                                <th>Caja</th>
                                                <th>Personal</th>
                                                <th>Almacen</th>
                                                <th>Estdo</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Numero</th>
                                                <th>Caja</th>
                                                <th>Personal</th>
                                                <th>Almacen</th>
                                                <th>Estdo</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label class="card-title">Lista de aperutas y cierres de todas las cajas </label>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                <h3>Fecha Inicio:</h3>

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
                                                <h3>Fecha Fin:</h3>

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
                                                <h3>Almacén:</h3>

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

                                    </div><br>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <th>Caja</th>
                                                <th>Personal</th>
                                                <th>Fecha apertura</th>
                                                <th>Efectivo apertura</th>
                                                <th>Fecha cierre</th>
                                                <th>Efectivo cierre</th>
                                                <th>Ventas</th>
                                                <th>Opciones</th>
                                            </thead>
                                            <tbody id="tblhistorial">

                                            </tbody>
                                            <tfoot>
                                            <th>Caja</th>
                                            <th>Personal</th>
                                            <th>Fecha apertura</th>
                                            <th>Efectivo apertura</th>
                                            <th>Fecha cierre</th>
                                            <th>Efectivo cierre</th>
                                            <th>Ventas</th>
                                            <th>Opciones</th>   
                                            </tfoot>
                                        </table>
                                    </div>

                                </div>
                            </div>
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

<div class="modal fade" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cajas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
                <div class="modal-body">
                      <div class="form-group">
                        <select id="idsucursal" name="idsucursal" class="form-control">
                        </select>
                      </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Nombre:</label>
                        <div class="col-sm-12">
                            <input type="hidden" name="idcaja" id="idcaja">
                            <input type="text" class="form-control" name="nombre" id="nombre" maxlength="50" placeholder="Nombre" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Numero:</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="numero" id="numero" maxlength="50" placeholder="Numero" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
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
        <!-- Tabla de VENTAS -->
    <table id="tbllistadoVentas" class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Cliente</th>
          <th>Almacen</th>
          <th>Comprobante</th>
          <th>Total</th>
          <th>Pago</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>


<hr>

<!-- Tabla de MOVIMIENTOS -->
<table id="tbllistadoMovimientos" class="table table-striped table-bordered">
  <thead>
    <tr>
      <th>Fecha</th><th>Descripción</th><th>Tipo</th><th>Forma Pago</th><th>Monto</th><th>Acciones</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>


<script src="vistas/js/cajas.js"></script>