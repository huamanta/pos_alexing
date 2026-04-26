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
                  <button type="button" class="btn btn-outline-primary btn-block btn-xs" data-toggle="modal"
                    data-target="#myModal" onclick="initMap()"><i class="fa fa-plus"></i> Nuevo</button>
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
                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100"
                  placeholder="Nombre del proveedor" required>
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
                <input type="text" class="form-control" name="num_documento" id="num_documento" maxlength="20"
                  placeholder="Documento">
                <div class="input-group-append">
                  <span class="input-group-text" style="cursor: pointer;" id="Buscar_Cliente" onclick="BuscarCliente()"
                    title="Buscar Cliente" type="button"><i class="fa fa-search"></i></span>
                  <span class="input-group-text" id="cargando" title="Cargando" type="button"
                    style="display: none;"><i><img src="files/plantilla/cargando.gif" width="15px"></i></span>
                </div>
              </div>

              Estado:<label for="" id="estado2">-</label>
              Condición:<label for="" id="condicion">-</label>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Dirección:</label>
                <div class="input-group mb-3">
                  <input type="text" class="form-control" name="direccion" id="direccion" maxlength="70"
                    placeholder="Dirección">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" onclick="buscarDireccion()">Buscar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Teléfono:</label>
                <input type="text" class="form-control" name="telefono" id="telefono" maxlength="20"
                  placeholder="Teléfono">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Email:</label>
                <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="Email">
              </div>
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <label for="name" class="col-sm-4 control-label">Activar como proveedor:</label>
              <input style="border-color: #99C0E7; text-align:center" class="checkbox pull-right" type="checkbox"
                name="proveedor" id="proveedor" value="1">
            </div>
          </div>
          <div class="col-sm-12">
            <div class="form-group">
              <label for="name" class="col-sm-4 control-label">Mapa:</label>
              <input type="hidden" id="latitude" name="latitude" value="-6.487595468705555">
              <input type="hidden" id="longitude" name="longitude" value="-76.3601303100586">
              <div id="map" style="height:400px;"></div>
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

<style>
  .reporte-cliente-modal .modal-header {
    background: #f4f6f9;
    border-bottom: 1px solid #dee2e6;
  }

  .reporte-cliente-modal .modal-title {
    font-weight: 600;
  }

  .reporte-cliente-modal .filtros-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fafbfc;
    padding: 12px;
    margin-bottom: 14px;
  }

  .reporte-cliente-modal .section-box {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    padding: 10px;
    margin-bottom: 12px;
  }

  .reporte-cliente-modal .section-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 13px;
  }

  .reporte-cliente-modal .section-title .badge {
    font-size: 11px;
  }

  .reporte-cliente-modal .report-slot table {
    margin-bottom: 8px;
  }

  .reporte-cliente-modal .modal-body {
    max-height: 72vh;
    overflow-y: auto;
  }
</style>

<!-- Modal -->
<div class="modal fade reporte-cliente-modal" id="listarReporteCliente" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title mb-0">Resumen de Cliente / Proveedor</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="clientesreporte">

        <div class="filtros-card">
          <div class="row align-items-end">
            <div class="col-md-4">
              <label class="mb-1">Fecha Inicio</label>
              <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                value="<?php echo date("Y-01-01"); ?>">
            </div>
            <div class="col-md-4">
              <label class="mb-1">Fecha Fin</label>
              <input type="date" class="form-control" name="fecha_fin" id="fecha_fin"
                value="<?php echo date("Y-m-d"); ?>">
            </div>
            <div class="col-md-4 text-md-right mt-2 mt-md-0">
              <button type="button" class="btn btn-outline-primary"
                onclick="ListarReportesClientes($('#clientesreporte').val())">
                <i class="fa fa-sync"></i> Actualizar resumen
              </button>
            </div>
          </div>
        </div>

        <div class="section-box">
          <div class="section-title text-success">
            <span>Historial como Cliente</span>
            <span class="badge badge-success">Compras y CxC</span>
          </div>
          <div class="report-slot" id="data_compras"></div>
          <div class="report-slot" id="data_cuentas_pagar"></div>
        </div>

        <div class="section-box">
          <div class="section-title text-danger">
            <span>Historial como Proveedor</span>
            <span class="badge badge-danger">Compras y CxP</span>
          </div>
          <div class="report-slot" id="data_proveedor"></div>
          <div class="report-slot" id="data_proveedor_pagar"></div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" onclick="cerrarModal()" data-dismiss="modal">
          <i class="fa fa-times"></i> Cerrar
        </button>
        <button class="btn btn-primary" onclick="imprimir()">
          <i class="fa fa-save"></i> Imprimir
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Fin modal -->

<div class="card" id="card-plantilla">

</div>


<script async
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAEfzrVHyxezdBMPmKlF8Hs-of68DzrRFY&callback=initMap">
  </script>

<script src="vistas/js/cliente.js"></script>