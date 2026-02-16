<!-- Content Wrapper. Contains page content -->
<style>
  /* Estilo base del contenedor */
.custom-check {
  display: inline-block;
  position: relative;
  padding-left: 28px;
  cursor: pointer;
  font-size: 14px;
  user-select: none;
}

/* Ocultamos el checkbox nativo */
.custom-check input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Caja visual */
.custom-check .checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 18px;
  width: 18px;
  background-color: #eee;
  border-radius: 4px;
  transition: all 0.2s ease;
  border: 1px solid #ccc;
}

/* Hover */
.custom-check:hover input ~ .checkmark {
  background-color: #ddd;
}

/* Activo (cuando está checked) */
.custom-check input:checked ~ .checkmark {
  background-color: #40c057;
  border-color: #40c057;
}

/* Check (✓) */
.custom-check .checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.custom-check input:checked ~ .checkmark:after {
  display: block;
}

.custom-check .checkmark:after {
  left: 6px;
  top: 2px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

/* Deshabilitado */
.custom-check.disabled .checkmark {
  background-color: #f5f5f5;
  border-color: #ddd;
  cursor: not-allowed;
}
.custom-check.disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

</style>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Ajuste de inventario</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Ajuste</li>
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
              <div class="row" justify-content-end>
                <div class="col-md-2">
                  <button type="button" class="btn btn-primary btn-block btn-xl" id="btn-ajustar-inventario">
                    <i class="fa fa-plus"></i> Ajustar
                  </button>
                </div>
                <div class="col-md-3">
                  <button type="button" class="btn btn-success btn-block btn-xl" id="btn-consultar-inventarios"><i class="fa fa-search"></i> Consultar inventarios</button>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Almacén:</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fas fa-store-alt"></i>
                    </div>
                    <select id="idsucursal2" name="idsucursal2" class="form-control select2">
                    </select>
                  </div>
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Seleccionar inventario:</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fas fa-store-alt"></i>
                    </div>
                    <select id="idinventario" name="idinventario" class="form-control select2">
                    </select>
                  </div>
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Categoría:</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fas fa-store-alt"></i>
                      </span>
                    </div>
                    <select id="idcategoria" name="idcategoria" class="form-control select2" style="height: 30px !important;">
                    </select>
                  </div>
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Tipo de ajuste:</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fas fa-store-alt"></i>
                    </div>
                    <select id="tipo_ajuste" name="tipo_ajuste" class="form-control">
                      <option value="0" selected>Todos</option>
                      <option value="1">Entrada</option>
                      <option value="2">Salida</option>
                    </select>
                  </div>
                </div>
                <div class="form-group col-lg-10 col-md-9 col-sm-8 col-xs-12">
                </div>
                <div class="form-group col-lg-2 col-md-3 col-sm-4 col-xs-12">
                  <button type="button" class="btn btn-success  btn-block" id="btn_buscar_products">BUSCAR</button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="tbllistado" class="table table-striped">
                  <thead>
                    <tr>
                      <th><input type="checkbox" id="checkAll"/></th>
                      <th>#</th>
                      <th>Producto</th>
                      <th>U. medida</th>
                      <th>Cantidad</th>
                      <th>Cantidad real</th>
                      <th>Diferencia</th>
                      <th>Estado</th>
                    </tr>
                  </thead>
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

<div class="modal fade" id="modal-ajustar-inventario">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Ajustar Inventario</h4>
        <button type="button" class="close" data-dismiss="modal">
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
        <div class="modal-body">

          <div class="form-group col-sm-12">
            <label for="idtipoajuste" class="control-label">Tipo de ajuste:</label>
            <select class="form-control" name="idtipoajuste" id="idtipoajuste">
              <option value="0">Todos</option>
              <option value="1">Entrada</option>
              <option value="2">Salida</option>
            </select>
          </div>

          <div class="form-group col-sm-12">
            <label for="fecha_ajuste" class="control-label">Fecha de ajuste:</label>
            <input type="datetime-local" class="form-control" name="fecha_ajuste" id="fecha_ajuste">
          </div>

          <div class="form-group col-sm-12">
            <label for="idconcepto" class="control-label">Concepto de ajuste:</label>
            <select id="idconcepto" name="idconcepto" class="form-control"></select>
          </div>

          <div class="form-group col-sm-12">
            <label for="observacion_ajuste" class="control-label">Observación:</label>
            <textarea class="form-control" name="observacion_ajuste" id="observacion_ajuste"></textarea>
          </div>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnguardar_ajuste">GUARDAR</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal consultar inventarios -->
<div class="modal fade" id="modal-consultar-inventarios" tabindex="-1" role="dialog" aria-labelledby="modalConsultarInventariosLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalConsultarInventariosLabel">Inventarios disponibles</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tblInventarios" class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Fecha apertura</th>
              <th>Observación</th>
              <th>Sucursal</th>
              <th>Usuario</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="vistas/js/ajuste-inventario.js"></script><!-- CSS de Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- JS de Toastr y jQuery (requerido por Toastr) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>