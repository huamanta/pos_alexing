<!-- Content Wrapper. Contains page content -->
<style>
  #myModal .modal-content {
    border-radius: 12px;
    overflow: hidden;
  }

  #myModal .modal-header {
    padding: 18px 22px;
  }

  #myModal .modal-body {
    max-height: 75vh;
    overflow-y: auto;
  }

  #myModal .card {
    border-radius: 10px;
  }

  #myModal .card-header {
    padding: 15px 18px;
  }


  #myModal .permiso-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    width: 100%;
    height: 360px;
    overflow-y: auto;
    padding: 5px;
  }

  #myModal .permiso-card {
    width: auto;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-left: 4px solid #007bff;
    border-radius: 8px;
    padding: 12px 14px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
    transition: all .2s ease;
  }

  #myModal .permiso-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, .10);
    transform: translateY(-1px);
  }

  #myModal .permiso-title {
    font-weight: 600;
    color: #343a40;
    margin-bottom: 8px;
  }

  #myModal .subpermiso {
    margin-left: 10px;
    margin-top: 8px;
  }

  #myModal .accion {
    margin-left: 22px;
    font-size: 13px;
    color: #6c757d;
    margin-top: 4px;
  }

  #myModal input[type="checkbox"] {
    margin-right: 6px;
    cursor: pointer;
  }

  #myModal .modal-footer {
    padding: 14px 20px;
  }

  @media (max-width: 1200px) {
    #myModal .permiso-container {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 768px) {
    #myModal .permiso-container {
      grid-template-columns: repeat(1, 1fr);
      height: 300px;
    }
  }
</style>
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
                  <button type="button" class="btn btn-outline-primary btn-block btn-xs" onclick="nuevoUsuario()"><i
                      class="fa fa-plus"></i> Nuevo</button>
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
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">

      <!-- HEADER -->
      <div class="modal-header bg-primary text-white">
        <div>
          <h4 class="modal-title mb-1">
            <i class="fas fa-user-cog mr-2"></i>
            Gestión de usuario
          </h4>
          <small class="opacity-75">
            Configura los datos, sucursales y permisos de acceso
          </small>
        </div>

        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">

        <div class="modal-body bg-light">

          <input type="hidden" name="idusuario" id="idusuario">

          <!-- DATOS DEL USUARIO -->
          <div class="card border-0 shadow-sm mb-3">

            <div class="card-header bg-white border-bottom">
              <h5 class="mb-0 text-primary">
                <i class="fas fa-user mr-2"></i>
                Datos del usuario
              </h5>
            </div>

            <div class="card-body">

              <div class="row">

                <!-- PERSONAL -->
                <div class="col-md-3 mb-3">

                  <label for="idpersonal" class="font-weight-bold">
                    Personal
                  </label>

                  <div class="input-group">

                    <select id="idpersonal" name="idpersonal" class="form-control select2" style="width: 100%;"
                      title="Seleccione Trabajador" required>
                    </select>
                  </div>

                </div>

                <!-- LOGIN -->
                <div class="col-md-3 mb-3">

                  <label for="login" class="font-weight-bold">
                    Usuario / Login
                  </label>

                  <div class="input-group">

                    <input type="text" class="form-control" name="login" id="login" maxlength="20"
                      placeholder="Ingrese el usuario" required onchange="verificarUsuario(this.value);">

                  </div>

                  <div class="alert alert-danger py-2 px-3 mt-2 mb-0" id="n1" style="display:none;">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    El login ya está en uso
                  </div>

                </div>

                <!-- CLAVE -->
                <div class="col-md-3 mb-3">

                  <label for="clave" class="font-weight-bold">
                    Contraseña
                  </label>

                  <div class="input-group">

                    <input type="password" class="form-control" name="clave" id="clave" maxlength="64"
                      placeholder="Ingrese la contraseña">

                  </div>

                  <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Dejar vacío para conservar la contraseña actual.
                  </small>

                </div>

                <!-- SUCURSALES -->
                <div class="col-md-3 mb-3">

                  <label for="idsucursal" class="font-weight-bold">
                    Sucursales
                  </label>

                  <div class="input-group">
                    
                    <select id="idsucursal" name="idsucursal[]" class="form-control select2" multiple="multiple"
                      style="width: 100%;" required>
                    </select>

                  </div>

                  <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Seleccione una o más sucursales.
                  </small>

                </div>

              </div>

            </div>

          </div>


          <!-- PERMISOS -->
          <div class="card border-0 shadow-sm">

            <div class="card-header bg-white border-bottom">

              <div class="row align-items-center">

                <div class="col-md-5">

                  <h5 class="mb-0 text-primary">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Permisos de acceso
                  </h5>

                  <small class="text-muted">
                    Seleccione las funciones que podrá utilizar el usuario.
                  </small>

                </div>

                <div class="col-md-7 mt-2 mt-md-0">

                  <div class="input-group">

                    <div class="input-group-prepend">
                      <span class="input-group-text bg-white">
                        <i class="fas fa-search text-muted"></i>
                      </span>
                    </div>

                    <input type="text" id="buscarPermisos" class="form-control" placeholder="Buscar permisos...">

                  </div>

                </div>

              </div>

            </div>

            <div class="card-body p-3">

              <div class="d-flex justify-content-between align-items-center mb-3">

                <div class="text-muted small">
                  <i class="fas fa-key mr-1"></i>
                  Permisos disponibles
                </div>

                <div>
                  <button type="button" class="btn btn-outline-primary btn-sm" id="seleccionarTodosPermisos">
                    <i class="fas fa-check-double mr-1"></i>
                    Seleccionar todos
                  </button>

                  <button type="button" class="btn btn-outline-secondary btn-sm" id="deseleccionarTodosPermisos">
                    <i class="fas fa-times mr-1"></i>
                    Limpiar
                  </button>
                </div>

              </div>

              <div class="border rounded bg-light p-3">

                <ul style="list-style:none;" id="permisos" class="permiso-container mb-0">
                </ul>

              </div>

              <div id="noPermisosFound" class="alert alert-warning mt-3 mb-0" style="display:none;">

                <i class="fas fa-search mr-2"></i>
                No se encontraron permisos.

              </div>

            </div>

          </div>

        </div>


        <!-- FOOTER -->
        <div class="modal-footer bg-white">

          <button type="button" onclick="cancelarform()" class="btn btn-light border" data-dismiss="modal">

            <i class="fas fa-times mr-1"></i>
            Cancelar

          </button>

          <button class="btn btn-primary px-4" type="submit" id="btnGuardar">

            <i class="fas fa-save mr-1"></i>
            Guardar usuario

          </button>

        </div>

      </form>

    </div>
  </div>
</div>

<script src="vistas/js/usuario.js"></script>