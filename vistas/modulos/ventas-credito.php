
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

<style>
  /* Contenedor de los small boxes */
  .small-box {
    border-radius: 8px;
    padding: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
  }
  .small-box .inner {
    text-align: center;
  }
  .small-box .inner h4 {
    font-size: 28px;
    margin: 0 0 10px 0;
  }
  .small-box .inner p {
    font-size: 18px;
    margin: 0;
  }
  .small-box .icon {
    position: absolute;
    top: 10px;
    right: 10px;
    opacity: 0.8;
    font-size: 40px;
  }
  /* Colores de fondo */
  .bg-blue {
    background: linear-gradient(45deg, #007bff, #0056b3);
  }
  .bg-green {
    background: linear-gradient(45deg, #28a745, #1e7e34);
  }
  .bg-red {
    background: linear-gradient(45deg, #dc3545, #bd2130);
  }
  /* Agrega un hover sutil */
  .small-box:hover {
    transform: translateY(-3px);
    transition: transform 0.2s;
  }
  /* Contenedor general (Card) */
  .card-custom {
    background: linear-gradient(135deg, #f7f9fc, #e4ebf5);
    border-radius: 12px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    padding: 20px;
    margin-bottom: 30px;
  }
  .card-custom h3 {
    margin-bottom: 20px;
    color: #333;
    font-weight: 700;
  }
  /* Estilos para cada form-group */
  .form-group {
    margin-bottom: 15px;
  }
  .form-group label {
    font-size: 14px;
    font-weight: 600;
    color: #555;
    margin-bottom: 5px;
    display: block;
  }
  .input-group {
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border-radius: 4px;
    overflow: hidden;
    background: #fff;
  }
  .input-group .form-control {
    border: none;
    padding: 10px 12px;
    font-size: 14px;
    border-radius: 0;
  }
  .input-group .form-control:focus {
    box-shadow: none;
  }
  .input-group-prepend .input-group-text {
    background: #007bff;
    color: #fff;
    border: none;
    font-size: 16px;
    padding: 10px 12px;
  }
  /* Personaliza el Select2 */
  .select2-container--default .select2-selection--single {
    height: 42px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 4px 12px;
    background: #fff;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px;
    color: #495057;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    right: 8px;
    color: #495057;
  }
  /* Botón (ejemplo de estilo) */
  .btn-custom {
    background: #007bff;
    color: #fff;
    border-radius: 4px;
    padding: 8px 16px;
    font-weight: 600;
    border: none;
    transition: background 0.3s;
  }
  .btn-custom:hover {
    background: #0056b3;
  }
</style>


</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Reporte de Ventas por producto al Crédito</h1>
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

                                    <label>Cliente:</label>

                                    <div class="input-group mb-3">
                                        <select id="idvendedor" name="idvendedor" class="form-control select2" required>
                                        </select>
                                        <span class="input-group-append" hidden>
                                            <button type="button" class="btn btn-info btn-flat btn-sm"><i class="fas fa-search"></i> Mostrar</button>
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
          <!-- Total Productos -->
          <div class="col-lg-3 col-md-6">
            <div class="small-box bg-blue">
              <div class="inner">
                <h4 id="lblCantidadPV2">0</h4>
                <p>Total Productos</p>
              </div>
              <div class="icon">
                <i class="fas fa-boxes"></i>
              </div>
            </div>
          </div>
          
          <!-- Total Compra -->
          <div class="col-lg-3 col-md-6">
            <div class="small-box bg-blue">
              <div class="inner">
                <h4 id="lblCompraPV2">0.00</h4>
                <p>Total Compra</p>
              </div>
              <div class="icon">
                <i class="fas fa-shopping-cart"></i>
              </div>
            </div>
          </div>
          
          <!-- Total Venta -->
          <div class="col-lg-3 col-md-6">
            <div class="small-box bg-green">
              <div class="inner">
                <h4 id="lblVentaPV2">0.00</h4>
                <p>Total Venta</p>
              </div>
              <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
              </div>
            </div>
          </div>
          
          <!-- Utilidad -->
          <div class="col-lg-3 col-md-6">
            <div class="small-box bg-red">
              <div class="inner">
                <h4 id="lblUtilidadPV2">0.00</h4>
                <p>Utilidad</p>
              </div>
              <div class="icon">
                <i class="fas fa-chart-line"></i>
              </div>
            </div>
          </div>
        </div>

              <!-- ./row Tarjetas Informativas -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card shadow">
                                    <table id="tbllistado2" class="table table-striped">
                                        <thead>
                                            <th>Comprobante</th>
                                            <th>Precio Venta</th>
                                            <th>Precio Compra</th>
                                            <th>Abonos</th>
                                            <th>Utilidad</th>
                                            <th>Estado</th>
                                        </thead>
                                        <tfoot>
                                            <th>Comprobante</th>
                                            <th>Precio Venta</th>
                                            <th>Precio Compra</th>
                                            <th>Abonos</th>
                                            <th>Utilidad</th>
                                            <th>Estado</th>
                                        </tfoot>
                                </table>
                                </div>      
                            </div>
                           <div class="col-sm-6">
                            <div class="card shadow">
                                <table id="tbllistado" class="table table-striped">
                                        <thead>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Total Venta</th>
                                            <th>Total Compra</th>
                                            <th>Utilidad </th>
                                        </thead>
                                        <tfoot>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Total Venta</th>
                                            <th>Total Compra</th>
                                            <th>Utilidad </th>
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

<script src="vistas/js/ventas-credito.js"></script>