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
          <h1>Cliente</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Cliente</li>
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
                  <button type="button" class="btn btn-outline-primary btn-block btn-xs" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Nuevo</button>
                </div>
              </div>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="tbllistado" class="table table-striped">
                <thead>
                  <th>Nombre</th>
                  <th>Documento</th>
                  <th>Número</th>
                  <th>Teléfono</th>
                  <th>Email</th>
                  <th>Acciones</th>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <th>Nombre</th>
                  <th>Documento</th>
                  <th>Número</th>
                  <th>Teléfono</th>
                  <th>Email</th>
                  <th>Acciones</th>
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

<div class="modal fade" id="myModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cliente</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cancelarform()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Nombre:</label>
                <input type="hidden" name="idpersona" id="idpersona">
                <input type="hidden" name="tipo_persona" id="tipo_persona" value="Cliente">
                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100" placeholder="Nombre del proveedor" required>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Tipo Documento:</label>
                <select class="form-control select-picker" name="tipo_documento" id="tipo_documento" required>
                  <option value="DNI">DNI</option>
                  <option value="RUC">RUC</option>
                  <option value="CEDULA">CEDULA</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <label for="name" class="control-label">Número Documento:</label>
              <div class="input-group mb-3">
                  <input type="text" class="form-control" name="num_documento" id="num_documento" maxlength="20" placeholder="Documento">
                  <div class="input-group-append">
                    <span class="input-group-text" style="cursor: pointer;" id="Buscar_Cliente" onclick="BuscarCliente()" title="Buscar Cliente" type="button"><i class="fa fa-search"></i></span>
                    <span class="input-group-text" id="cargando" title="Cargando" type="button" style="display: none;"><i><img src="files/plantilla/cargando.gif" width="15px"></i></span>
                  </div>
                </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Dirección:</label>
                <input type="text" class="form-control" name="direccion" id="direccion" maxlength="70" placeholder="Dirección">
                Estado:<label for="" id="estado2">-</label>
                Condición:<label for="" id="condicion">-</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Teléfono:</label>
                <input type="text" class="form-control" name="telefono" id="telefono" maxlength="20" placeholder="Teléfono">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Email:</label>
                <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="Email">
              </div>
            </div>
          </div>
          <div class="row">
            <label for="name" class="col-sm-4 control-label">Activar como proveedor:</label>
                <div class="col-sm-1">
                  <input style="border-color: #99C0E7; text-align:center" class="checkbox pull-right" type="checkbox" name="proveedor" id="proveedor" value="1">
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

<!-- Modal -->
    <div class="modal fade" id="listarReporteCliente" tabindex="-1" role="dialog">

      <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header" >
              <h4 class="modal-title">Clientes</h4>
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="row">
              <input type="hidden" id="clientesreporte">
              <div class="col-md-4">
                <label>Fecha Inicio</label>
                <div class="input-group date">
                  <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo date("Y-m-d"); ?>">
                </div>
              </div>
              <div class="col-md-4">
                <label>Fecha Fin</label>
                <div class="input-group date">
                  <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?php echo date("Y-m-d"); ?>">
                </div>
              </div>
            </div>
              
              

            <div class="modal-body">
              <div class="row">
              <div class="col-md-12">
                <label for="" style="color: green;">Historial de compras y cuentas por cobrar como cliente</label>

                <table class="table" id="data_compras">

                </table>

                <table class="table" id="data_cuentas_pagar">
                </table>
                <label for="" style="color: red;">Historial de compras y cuentas por pagar como proveedor</label>
                <table class="table" id="data_proveedor">
                </table>

                <table class="table" id="data_proveedor_pagar">
                </table>
              </div>
            </div>
              

            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-danger pull-left" onclick="cerrarModal()" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</button>
              <button class="btn btn-primary" onclick="imprimir()"><i class="fa fa-save"></i> Imprimir</button>
            </div>
        </div>
      </div>
    </div>
    <!-- Fin modal -->

    <div class="card" id="card-plantilla">

    </div>

<script src="vistas/js/cliente.js"></script>