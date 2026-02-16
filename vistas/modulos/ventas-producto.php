
<?php
date_default_timezone_set('America/Lima');
?>
<style type="text/css">
    /* Estilo para los precios */
.precio-venta {
    color: #28a745; /* Verde */
    font-weight: bold;
}

.precio-compra {
    color: #dc3545; /* Rojo */
    font-weight: bold;
}

/* Barra de Utilidad */
.barra-utilidad {
    height: 10px;
    background-color: #007bff; /* Azul */
    border-radius: 5px;
    margin-bottom: 5px;
}

/* Estilo para resaltar el nombre del producto */
strong {
    font-size: 16px;
    color: #000;
}

/* Estilo adicional para el texto de la utilidad */
.barra-utilidad + span {
    margin-left: 5px;
    font-weight: bold;
}

</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reporte de Ventas por producto al contado</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Reporte de Ventas por fecha, producto y vendedor</li>
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

                                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                    <label>Productos:</label>

                                    <div class="input-group mb-3">
                                        <select id="idproducto" name="idproducto" class="form-control select2" required>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">

                                    <label>Vendedor:</label>

                                    <div class="input-group mb-3">
                                        <select id="idvendedor" name="idvendedor" class="form-control select2" required>
                                        </select>
                                        <span class="input-group-append" hidden>
                                            <button type="button" class="btn btn-info btn-flat btn-sm" onclick="listar()"><i class="fas fa-search"></i> Mostrar</button>
                                        </span>
                                    </div>

                                </div>

                                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
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

                                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
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

                                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
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
                            <!-- row Tarjetas Informativas -->
              <div class="row">
                <div class="col-lg-3" style="color: blue; font-weight: 900; font-size: 25px">
                  <!-- small box -->
                  <div class="small-box ">
                    <div class="inner">
                      <h4 id=""></h4>
                      <p>Total productos: <span id="lblCantidadPV"></span></p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i> <!-- Utilizando la clase fa-lg -->
                    </div>
                  </div>
                </div>

                <div class="col-lg-3" style="color: blue; font-weight: 900; font-size: 25px">
                  <!-- small box -->
                  <div class="small-box ">
                    <div class="inner">
                      <h4 id=""></h4>
                      <p>Total Compra: <span id="lblCompraPV"></span></p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i> <!-- Utilizando la clase fa-lg -->
                    </div>
                  </div>
                </div>

                <!-- TARJETA TOTAL COMPRAS -->
                <div class="col-lg-2" style="color: purple; font-weight: 900; font-size: 25px">
                  <!-- small box -->
                  <div class="small-box ">
                    <div class="inner">
                      <h4 id=""></h4>
                      <p>Total Venta: <span id="lblVentaPV"></span></p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i> <!-- Utilizando la clase fa-lg -->
                    </div>
                  </div>
                </div>

                <!-- TARJETA TOTAL VENTAS -->
                <div class="col-lg-2" >
                  <!-- small box -->
                  <div class="small-box ">
                    <div class="inner" style="color: green; font-weight: 900; font-size: 25px">
                      <h4 id=""></h4>
                      <p>Utilidad: <span id="lblUtilidadPV"></span></p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i> <!-- Utilizando la clase fa-lg -->
                    </div>
                  </div>
                </div>
                <div class="col-lg-2" style="color: red; font-weight: 900; font-size: 25px">
                  <div class="small-box">
                    <div class="inner">
                      <h4 id=""></h4>
                      <p>Utilidad Neta: <span id="lblUtilidadNetaPV"></span></p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-chart-line fa-lg" style="font-size:20px !important"></i>
                    </div>
                  </div>
                </div>
              </div>
              <!-- ./row Tarjetas Informativas -->

                            <table id="tbllistado" class="table table-striped">
                                <thead>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Total Venta</th>
                                    <th>Total Compra</th>
                                    <th>Utilidad </th>
                                    <th>%</th>
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
                                    <th>%</th>
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

<script src="vistas/js/ventas-producto.js"></script>