<?php
date_default_timezone_set('America/Lima');
?>
<style>
        /* Estilo para el nombre del producto */
        .producto-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            text-transform: capitalize;
            padding: 5px 0;
            display: block;
        }

        /* Estilo para el proveedor */
        .proveedor-name {
            font-size: 14px;
            color: #666;
            padding: 5px 0;
        }

        /* Estilo para la fecha del Kardex */
        .fecha-kardex {
            font-size: 12px;
            color: #999;
            text-align: center;
            display: block;
        }

        /* Estilo para el comprobante */
        .comprobante-info {
            font-size: 14px;
            color: #333;
            font-style: italic;
        }

        /* Estilo para la cantidad y unidad */
        .cantidad {
            font-size: 14px;
            color: #444;
            display: flex;
            align-items: center;
        }

        .cantidad-num {
            font-weight: bold;
            color: #2C3E50;
        }

        .unidad {
            font-size: 12px;
            color: #7F8C8D;
            margin-left: 5px;
        }

        /* Estilo para el precio */
        .precio {
            font-size: 16px;
            font-weight: bold;
            color: #27AE60;  /* Color verde */
            padding: 5px 0;
        }

        .precio b {
            font-size: 18px;
        }

        /* Agrega un borde sutil y un fondo en las filas */
        .table tbody tr {
            border-bottom: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .table td {
            padding: 10px;
            text-align: center;
        }
    </style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Reporte de Compras por proveedor</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Reporte de Compras por proveedor</li>
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

                <div class="form-group col-lg-3 col-md-3 col-sm- col-xs-12">
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

                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
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

                <div class="form-group col-lg-3 col-md-3 col-sm-3 col-xs-12">
                  <label>Proveedor:</label>
                  <div class="input-group mb-3">
                    <select id="idproveedor" name="idproveedor" class="form-control select2" required>
                      
                    </select>
                  </div>
                </div>
              </div>

              <!-- row Tarjetas Informativas -->
              <div class="row">
                <div class="col-lg-4" style="color: blue; font-weight: 900; font-size: 25px">
                  <!-- small box -->
                  <div class="small-box ">
                    <div class="inner">
                      <h4 id=""></h4>
                      <p><span id=""></span></p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i> <!-- Utilizando la clase fa-lg -->
                    </div>
                  </div>
                </div>

                <!-- TARJETA TOTAL COMPRAS -->
                <div class="col-lg-4" style="color: green; font-weight: 900; font-size: 25px">
                  <!-- small box -->
                  <div class="small-box ">
                    <div class="inner">
                      <h4 id=""></h4>
                      <p>Total productos: <span id="lblComprasCantidad"></span></p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i> <!-- Utilizando la clase fa-lg -->
                    </div>
                  </div>
                </div>

                <!-- TARJETA TOTAL VENTAS -->
                <div class="col-lg-4">
                  <!-- small box -->
                  <div class="small-box ">
                    <div class="inner" style="color: red; font-weight: 900; font-size: 25px">
                      <h4 id=""></h4>
                      <p>Total Compra: <span id="lblComprasProveedor"></span></p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i> <!-- Utilizando la clase fa-lg -->
                    </div>
                  </div>
                </div>
              </div>


              <!-- ./row Tarjetas Informativas -->

              <table id="tbllistado" class="table table-striped">
                <thead>
                  <th>Fecha</th>
                  <th>Comprobante</th>
                  <th>Proveedor</th>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Total Compra</th>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <th>Fecha</th>
                  <th>Comprobante</th>
                  <th>Proveedor</th>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>Total Compra</th>
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

<script src="vistas/js/compras-proveedor.js"></script>