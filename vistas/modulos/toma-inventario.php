<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Toma de inventario</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Toma de inventario</li>
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

              <div class="row">
                <div class="col-md-1">
                  <button type="button" id="btnnuevo" class="btn btn-outline-primary btn-block btn-xs" onclick="nuevo()"><i class="fa fa-plus"></i> Nuevo</button>
                </div>

              </div>

            </div>
            <!-- /.card-header -->
            <div class="card-body" id="listadoregistros">
              <div class="row">
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
                    <select id="idsucursal2" name="idsucursal2" class="form-control select2"></select>
                  </div>
                </div>
              </div>

              <table id="tbllistado" class="table table-striped">
                <thead>
                  <tr>
                    <th>Fecha apertura</th>
                    <th>Obs. apertura</th>
                    <th>Fecha cierre</th>
                    <th>Obs. cierre</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <tr>
                    <th>Fecha apertura</th>
                    <th>Obs. apertura</th>
                    <th>Fecha cierre</th>
                    <th>Obs. cierre</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </tfoot>
              </table>
            </div>

            <div class="card-body" id="formularioregistros">
              <div class="row">
                <div class="col-md-4">
                  <div class="panel-heading" style="border-bottom: 1px dashed hsla(0,0%,80%,.329)">
                    <div class="card card-outline card-danger" style="margin-top: -20px;">
                      <div class="card shadow mb-4">
                        <!-- Encabezado principal -->
                        <div class="card-header bg-white border-bottom-primary">
                          <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title m-0 font-weight-bold text-primary">Búsqueda de producto</h4>
                          </div>
                        </div>
                        <div class="card-body">
                          <div class="form-group mb-3">
                            <label class="font-weight-bold">
                              <i class="fas fa-house"> Almacen</i>
                            </label>
                            <select id="idsucursal" name="idsucursal" class="form-control select2" required></select>
                          </div>
                          <div class="form-group mb-3">
                            <label class="font-weight-bold">
                              <i class="fas fa-box-open"> Nombre</i>
                            </label>
                            <input id="nombre" name="nombre" class="form-control " required>
                          </div>
                          <div class="form-group mb-3">
                            <label class="font-weight-bold">
                              <i class="fas fa-box-open"> Código</i>
                            </label>
                            <input id="codigo" name="codigo" class="form-control " required>
                          </div>
                          <div class="form-group mb-3">
                            <label class="font-weight-bold">
                              <i class="fas fa-box-open"> Categoria</i>
                            </label>
                            <select id="categoria" name="categoria" class="form-control select2" required></select>
                          </div>
                          <div class="form-group mb-3">
                            <button type="button" id="buscar_producto" class="btn btn-primary btn-block btn-xl"><i class="fa fa-search"></i>Buscar</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="panel-heading" style="border-bottom: 1px dashed hsla(0,0%,80%,.329)">
                    <div class="card card-outline card-danger" style="margin-top: -20px;">
                      <div class="card shadow mb-4">
                        <div class="card-header">
                          <div class="row">
                            <div class="col-md-2">
                              <button type="button" id="btnregresar" onclick="cancelarform()" class="btn btn-danger btn-block btn-xs">
                                <i class="fa fa-chevron-left"></i> Regresar
                              </button>
                            </div>
                            <div class="col-md-3">
                              <button type="button" id="btncerrarinventario" onclick="cerrarInventario()" class="btn btn-primary btn-block btn-xs">
                                Cerrar inventario </button>
                            </div>
                            <div class="col-md-12" id="message_inventario">

                            </div>
                          </div>
                        </div>
                        <div class="card-body">
                          <form action="POST" id="guardar_registros">
                            <input type="hidden" id="idinventario" name="idinventario">
                            <div style="max-height: 250px; overflow-y: auto;">
                              <table id="tbllistado2" class="table table-striped">
                                <thead>
                                  <tr>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>U. medida</th>
                                    <th>Cantidad</th>
                                  </tr>
                                </thead>
                                <tbody id="data_productos">
                                  <tr>
                                    <td colspan="4" style="text-align: center;">Lista de productos vacia</td>
                                  </tr>
                                </tbody>
                                <tbody>
                                  <tr>
                                    <td colspan="3"></td>
                                    <td hidden>
                                      <button class="btn btn-success" type="submit" id="btn_guardar_products">GUARDAR</button>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                            <h5 class="mt-4">Productos a registrar en inventario:</h5>
                            <table class="table table-bordered" id="tabla_seleccionados">
                              <thead>
                                <tr>
                                  <th>Producto</th>
                                  <th>Código</th>
                                  <th>Unidad</th>
                                  <th>Cantidad</th>
                                  <th>Stock</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td colspan="5" style="text-align:center;">No hay productos agregados</td>
                                </tr>
                              </tbody>
                            </table>

                          </form>
                        </div>
                      </div>
                    </div>
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
        <h4 class="modal-title">Crear registro de inventario</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Observación:</label>
            <div class="col-sm-12">
              <input type="hidden" name="idsucursal_save" id="idsucursal_save">
              <input type="hidden" name="idinventario_edit" id="idinventario_edit">
              <textarea class="form-control" name="observacion_apertura" id="observacion_apertura" placeholder="Nombre" required></textarea>
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


<div class="modal fade" id="myModalCierre">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cerrar el inventario</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario_cierre" id="formulario_cierre" method="POST">
        <div class="modal-body">
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Observación:</label>
            <div class="col-sm-12">
              <input type="hidden" name="idinventario_cierre" id="idinventario_cierre">
              <textarea class="form-control" name="observacion_cierre" id="observacion_cierre" placeholder="Nombre" required></textarea>
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

<div class="modal fade" id="modalVerInventario">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title">Detalle de Inventario</h4>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p><strong>Fecha apertura:</strong> <span id="ver_fecha_apertura"></span></p>
        <p><strong>Obs. apertura:</strong> <span id="ver_obs_apertura"></span></p>
        <p><strong>Fecha cierre:</strong> <span id="ver_fecha_cierre"></span></p>
        <p><strong>Obs. cierre:</strong> <span id="ver_obs_cierre"></span></p>

        <hr>
        <h5>Productos</h5>
        <table id="tablaVerProductos" class="table table-striped table-hover w-100">
          <thead class="thead-dark">
            <tr>
              <th>#</th>
              <th>Producto</th>
              <th>Unidad</th>
              <th>Cantidad</th>
              <th>Cantidad Real</th>
              <th>Diferencia</th>
            </tr>
          </thead>
          <tbody id="ver_productos">
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="vistas/js/toma-inventario.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- JS de Toastr y jQuery (requerido por Toastr) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>