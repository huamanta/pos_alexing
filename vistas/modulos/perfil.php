
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Mi perfil</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
            <li class="breadcrumb-item active">Mi perfil</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-8">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Datos personales</h3>
            </div>
            <form id="formPerfil">
              <div class="card-body">
                <div class="form-row">
                  <div class="form-group col-md-8">
                    <label for="perfilNombre">Nombre completo</label>
                    <input type="text" class="form-control" id="perfilNombre" name="nombre" maxlength="250" required>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="perfilCargo">Cargo</label>
                    <input type="text" class="form-control" id="perfilCargo" readonly>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="perfilTipoDocumento">Tipo de documento</label>
                    <select class="form-control" id="perfilTipoDocumento" name="tipo_documento" required>
                      <option value="DNI">DNI</option>
                      <option value="RUC">RUC</option>
                      <option value="CEDULA">Cédula</option>
                    </select>
                  </div>
                  <div class="form-group col-md-8">
                    <label for="perfilNumeroDocumento">Número de documento</label>
                    <input type="text" class="form-control" id="perfilNumeroDocumento" name="num_documento" maxlength="20" required>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="perfilTelefono">Teléfono</label>
                    <input type="text" class="form-control" id="perfilTelefono" name="telefono" maxlength="20">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="perfilEmail">Correo electrónico</label>
                    <input type="email" class="form-control" id="perfilEmail" name="email" maxlength="50">
                  </div>
                  <div class="form-group col-12 mb-0">
                    <label for="perfilDireccion">Dirección</label>
                    <input type="text" class="form-control" id="perfilDireccion" name="direccion" maxlength="255">
                  </div>
                </div>
              </div>
              <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary" id="btnGuardarPerfil">
                  <i class="fas fa-save mr-1"></i>Guardar datos
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card card-secondary card-outline">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-id-card mr-2"></i>Acceso y roles</h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="perfilLogin">Usuario</label>
                <input type="text" class="form-control" id="perfilLogin" readonly>
              </div>
              <div class="form-group mb-0">
                <label>Roles asignados</label>
                <div id="perfilRoles" class="d-flex flex-wrap">
                  <span class="text-muted">Cargando...</span>
                </div>
                <small class="form-text text-muted">Los roles solo pueden ser modificados por un administrador.</small>
              </div>
            </div>
          </div>

          <div class="card card-warning card-outline">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-key mr-2"></i>Cambiar contraseña</h3>
            </div>
            <form id="formClave">
              <div class="card-body">
                <div class="form-group">
                  <label for="claveActual">Contraseña actual</label>
                  <input type="password" class="form-control" id="claveActual" name="clave_actual" autocomplete="current-password" required>
                </div>
                <div class="form-group">
                  <label for="claveNueva">Nueva contraseña</label>
                  <input type="password" class="form-control" id="claveNueva" name="clave_nueva" minlength="8" maxlength="128" autocomplete="new-password" required>
                  <small class="form-text text-muted">Debe tener entre 8 y 128 caracteres.</small>
                </div>
                <div class="form-group mb-0">
                  <label for="confirmarClave">Confirmar nueva contraseña</label>
                  <input type="password" class="form-control" id="confirmarClave" name="confirmar_clave" minlength="8" maxlength="128" autocomplete="new-password" required>
                </div>
              </div>
              <div class="card-footer text-right">
                <button type="submit" class="btn btn-warning" id="btnCambiarClave">
                  <i class="fas fa-lock mr-1"></i>Actualizar contraseña
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script src="vistas/js/perfil.js"></script>
