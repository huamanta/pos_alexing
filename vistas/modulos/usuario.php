<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Usuario</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Usuario</li>
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
                  <button type="button" class="btn btn-outline-primary btn-block btn-xs" onclick="nuevoUsuario()"><i class="fa fa-plus"></i> Nuevo</button>
                </div>
              </div>
              
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="tbllistado" class="table table-striped">
                <thead>
                  <tr>
                    <th style="width: 400px;">Nombre</th>
                    <th>Login</th>
                    <th>Sucursal</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <tr>
                  <th style="width: 400px;">Nombre</th>
                    <th>Login</th>
                    <th>Sucursal</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
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
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Usuarios </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
        <div class="modal-body">
          <div class="row">
              
              <div class="col-md-3">
                <label for="name">Personal:</label>
                <input type="hidden" name="idusuario" id="idusuario">
                <select id="idpersonal" name="idpersonal" class="form-control select2" style="width: 100%; height: 100%;" title="Seleccione Trabajador" required></select>
              </div>
              
              <div class="col-md-3">
                <label for="name">Login:</label>
                <input type="text" class="form-control" name="login" id="login" maxlength="20" placeholder="Login" required onchange="verificarUsuario(this.value);">
                <div class="alert alert-danger" id="n1" style="display: none;">Login ya está en uso</div>
              </div>
              
              <div class="col-sm-3">
                <label for="name">Clave:</label>
                <input type="password" class="form-control" name="clave" id="clave" maxlength="64" placeholder="Clave">
              </div>
              
              <div class="col-sm-3">
                <label for="name">Sucursales:</label>
                <select id="idsucursal" name="idsucursal[]" class="form-control select2" multiple="multiple" 
                        style="width: 100%; height: 100%;" required>
                </select>
              </div>

            </div>
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Permisos:</label>
            <div class="col-sm-12">
              <ul style="list-style: none;" id="permisos">
              </ul>
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

<script src="vistas/js/usuario.js"></script>