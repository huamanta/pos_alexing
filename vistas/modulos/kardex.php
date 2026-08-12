<?php date_default_timezone_set('America/Lima'); ?>
<div class="content-wrapper">
  <!-- Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2 align-items-center">
        <div class="col-sm-6">
          <h1 class="mb-0"><i class="fas fa-boxes"></i> Kardex</h1>
          <small class="text-muted">Movimientos y stock valorizado</small>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item active">Kardex</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main -->
  <section class="content">
    <div class="container-fluid">

      <!-- Tabla -->
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="row">
            <div class="form-group col-lg-4 col-md-6 col-12">
              <label class="mb-1"><i class="fas fa-box"></i> Producto</label>
              <select id="idproducto" name="idproducto" class="form-control select2" required></select>
            </div>

            <div class="form-group col-lg-2 col-md-6 col-12">
              <label class="mb-1"><i class="far fa-calendar-alt"></i> Fecha Inicio</label>
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i
                      class="far fa-calendar-alt"></i></span></div>
                <input type="date" class="form-control" id="fecha_inicio">
              </div>
            </div>

            <div class="form-group col-lg-2 col-md-6 col-12">
              <label class="mb-1"><i class="far fa-calendar-alt"></i> Fecha Fin</label>
              <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i
                      class="far fa-calendar-alt"></i></span></div>
                <input type="date" class="form-control" id="fecha_fin">
              </div>
            </div>
            <div class="col-md-6 d-flex align-items-center">
              <span class="mr-2">Mostrar</span>
              <select id="limit" class="form-control" style="width:100px" onchange="cambiarLimit()">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>

              <span class="ml-2">Registros</span>

            </div>
            <div class="col-md-6">
              <input type="text" id="search" class="form-control" placeholder="Buscar...">
            </div>
            <div class="col-md-12 mt-2">
              <div class="table-responsive">
                <table id="tbllistado" class="table table-striped table-hover table-bordered mb-0 w-100">
                  <thead class="bg-light sticky-head">
                    <tr>
                      <th>Fecha</th>
                      <th>Producto</th>
                      <th>Movimiento</th>
                      <th>Tipo</th>
                      <th class="text-right">Cantidad</th>
                      <th class="text-right">Cant. Contenedor</th>
                      <th class="text-right">Precio</th>
                      <th class="text-right">Valorizado</th>
                      <th class="text-right">Stock Actual</th>
                      <th class="text-right">Stock Valorizado</th>
                    </tr>
                  </thead>
                  <tbody id="tbodyData"></tbody>
                </table>
              </div>
            </div>
            <div class="col-md-6 mt-2"></div>
            <div class="col-md-6 mt-2">
              <div id="pagination"></div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<!-- Tu JS de la vista -->
<script src="vistas/js/kardex.js"></script>

<style>
  /* ====== Mejora visual sin romper nada ====== */
  .content-header h1 {
    font-weight: 700;
  }

  .card {
    border-radius: .6rem;
  }

  .table thead.sticky-head th {
    position: sticky;
    top: 0;
    z-index: 1;
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .05);
  }

  .table td,
  .table th {
    vertical-align: middle;
  }

  .dataTables_wrapper .dt-buttons .btn {
    margin-right: .35rem;
    border-radius: .5rem;
  }

  .btn-excel {
    background-color: #28a745 !important;
    color: #fff !important;
    border: none;
  }

  .btn-pdf {
    background-color: #dc3545 !important;
    color: #fff !important;
    border: none;
  }

  .btn-colvis {
    background-color: #007bff !important;
    color: #fff !important;
    border: none;
  }

  .select2-container .select2-selection--single {
    height: 38px;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px;
  }

  /* Pequeño gap para los botones de acción */
  .gap-8>*+* {
    margin-left: .5rem;
  }
</style>