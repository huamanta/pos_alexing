<?php
if (isset($_SESSION['idpersonal'])) {
?>
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Resumen Diario de Boletas</h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Filtros y Boletas para Resumen</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-3">
            <label>Fecha Inicio:</label>
            <input type="date" class="form-control" name="fecha_inicio_busqueda" id="fecha_inicio_busqueda" value="<?php echo date("Y-m-d"); ?>">
          </div>

          <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-3">
            <label>Fecha Fin:</label>
            <input type="date" class="form-control" name="fecha_fin_busqueda" id="fecha_fin_busqueda" value="<?php echo date("Y-m-d"); ?>">
          </div>

          <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-3">
            <label>Sucursal:</label>
            <select id="idsucursal" name="idsucursal" class="form-control select2" style="width:100%;">
            </select>
          </div>

          <div class="form-group col-lg-3 col-md-3 col-sm-12 col-xs-12 d-flex align-items-end gap-2 mb-3">
            <button class="btn btn-success" onclick="generarResumen()">Generar Resumen</button>
          </div>
        </div>
        <hr>

        <table id="tblboletas" class="table table-bordered table-striped table-hover dt-responsive" style="width:100%">
          <thead>
            <tr>
              <th><input type="checkbox" id="select-all"></th>
              <th>Comprobante</th>
              <th>Cliente</th>
              <th>Total</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Resúmenes Generados</h3>
        </div>
        <div class="card-body">
            <table id="tblresumenes" class="table table-bordered table-striped table-hover dt-responsive" style="width:100%">
                <thead>
                    <tr>
                        <th>Fecha de Generación</th>
                        <th>Ticket</th>
                        <th>Correlativo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
  </section>
</div>

<script>
  var idpersonal_session = <?php echo $_SESSION['idpersonal']; ?>;
</script>
<script src="vistas/js/resumen.js"></script>

<?php
} else {
  require 'noacceso.php';
}
?>