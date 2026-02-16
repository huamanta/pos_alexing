<style type="text/css">
  #formularioregistros {
    display: none;
  }
</style>

<!-- Content Wrapper. Contains page content -->
<?php
date_default_timezone_set('America/Lima');
?>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h1 style="font-size: 22px;">
            Guías de Remisión
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="inicio">Home</a></li>
            <li class="breadcrumb-item active">Comprobantes</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <button type="button" class="btn btn-primary btn-sm shadow-sm" id="btnNuevo" onclick="mostrarform(true)">
                <i class="fas fa-plus"></i> Nueva Guía
              </button>
            </div>
            <!-- /.card-header -->
            <div class="card-body" id="listadoregistros">
              <div class="row">
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Fecha Inicio:</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                      value="<?php echo date("Y-m-d"); ?>">
                  </div>
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Fecha Fin:</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                      </span>
                    </div>
                    <input type="date" class="form-control" name="fecha_fin" id="fecha_fin"
                      value="<?php echo date("Y-m-d"); ?>">
                  </div>
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
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
                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                  <label>Estado:</label>
                  <div class="input-group">
                    <select id="estado" name="estado" class="form-control select2" required>
                      <option value="Todos">Todos</option>
                      <option value="Aceptado">Aceptado</option>
                      <option value="Por Enviar">Por Enviar</option>
                      <option value="Anulado">Anulado</option>
                      <option value="Rechazado">Rechazado</option>
                    </select>
                  </div>
                </div>
              </div>
              <table id="tbllistado" class="table table-striped">
                <thead>
                  <th>ID</th>
                  <th>Documento</th>
                  <th>Fecha</th>
                  <th>Cliente</th>
                  <th>Estado</th>
                  <th>Opciones</th>
                  <th width="70px;">Sunat</th>
                  <th style="text-align: center;"><i class="fa fa-download" aria-hidden="true"
                      title="Descargar XML"></i></th>
                  <th width="180px;">RESPUESTA SUNAT</th>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->

            <div class="card-body" id="formularioregistros">
              <form name="formulario" id="formulario" method="POST">
                <input type="hidden" name="idguia" id="idguia">
                <input type="hidden" name="idsucursal" id="idsucursal" value="<?php echo $_SESSION["idsucursal"]; ?>">

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="datos-generales-tab" data-toggle="tab" href="#datos-generales"
                      role="tab">Datos Generales</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="detalles-tab" data-toggle="tab" href="#tabdetalles" role="tab">Detalles</a>
                  </li>
                </ul>

                <div class="tab-content mt-3">
                  <div class="tab-pane fade show active" id="datos-generales" role="tabpanel">
                    <div class="row">
                      <div class="col-md-3">
                        <label>N° Serie:</label>
                        <select name="serie_comprobante" id="serie_comprobante" class="form-control select2"></select>
                      </div>
                      <div class="col-md-3">
                        <label>N° Número:</label>
                        <input type="text" class="form-control" name="num_comprobante" id="num_comprobante" readonly>
                      </div>
                      <div class="col-md-3">
                        <label>Fecha Emisión:</label>
                        <input type="date" class="form-control" name="fecha_emision" id="fecha_emision" required>
                      </div>
                      <div class="col-md-3">
                        <label>Cliente:</label>
                        <select name="idcliente" id="idcliente" class="form-control select2" required></select>
                      </div>
                      <div class="col-md-2">
                        <label>Fecha Traslado:</label>
                        <input type="date" class="form-control" name="fecha_traslado" id="fecha_traslado" required>
                      </div>
                      <div class="col-md-3">
                        <label>Importar desde:</label>
                        <select id="idcomprobante" name="idcomprobante" class="form-control select2"></select>
                      </div>
                      <div class="col-md-2 mt-2">
                        <label>Factura Ref:</label>
                        <input type="text" class="form-control" name="factura_ref" id="factura_ref" readonly>
                      </div>
                      <div class="col-md-2 mt-2">
                        <label>Fecha Factura Ref:</label>
                        <input type="date" class="form-control" name="fecha_factura_ref" id="fecha_factura_ref">
                      </div>
                      <div class="col-md-2 mt-2">
                        <label>Tipo de Transporte:</label>
                        <select name="tipo_transporte" id="tipo_transporte" class="form-control select2">
                          <option value="0">Público</option>
                          <option value="1">Privado</option>
                        </select>
                      </div>
                      <div class="col-md-2 mt-2">
                        <label>Transportista:</label>
                        <select name="idtransportista" id="idtransportista" class="form-control select2"></select>
                      </div>
                      <div class="col-md-2 mt-2">
                        <label>Peso:</label>
                        <input type="text" class="form-control" name="peso" id="peso">
                      </div>
                      <div class="col-md-2 mt-2">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-outline-info btn-sm w-100" onclick="abrirModalProductos()">
                          <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="tab-pane fade" id="tabdetalles" role="tabpanel">
                    <div class="row">
                      <div class="col-md-2">
                        <label>Atencion:</label>
                        <input type="text" class="form-control" name="atencion" id="atencion">
                      </div>
                      <div class="col-md-2">
                        <label>Referencia:</label>
                        <input type="text" class="form-control" name="referencia" id="referencia">
                      </div>
                      <div class="col-md-2">
                        <label>Trabajadores:</label>
                        <select name="idtrabajador" id="idtrabajador" class="form-control select2"></select>
                      </div>
                      <div class="col-md-2">
                        <label>Motivos:</label>
                        <select name="idmotivo" id="idmotivo" class="form-control select2"></select>
                      </div>
                      <div class="col-md-2">
                        <label>Orden de Compra:</label>
                        <input type="text" class="form-control" name="ord_compra" id="ord_compra">
                      </div>
                      <div class="col-md-2">
                        <label>Orden de Pedido:</label>
                        <input type="text" class="form-control" name="ord_pedido" id="ord_pedido">
                      </div>
                      <div class="col-md-12">
                        <label>Punto de Partida:</label>
                        <input type="text" class="form-control" name="punto_partida" id="punto_partida">
                      </div>
                      <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                          <label>Departamento (Partida):</label>
                          <select id="departamento_partida" name="departamento_partida" class="form-control select2" required></select>
                      </div>
                      <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                          <label>Provincia (Partida):</label>
                          <select id="provincia_partida" name="provincia_partida" class="form-control select2" required></select>
                      </div>
                      <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                          <label>Distrito (Partida):</label>
                          <select id="distrito_partida" name="distrito_partida" class="form-control select2" required></select>
                      </div>
                      <input type="hidden" class="form-control" name="ubigeo_partida" id="ubigeo_partida">
                      <div class="col-md-12">
                        <label>Punto de Llegada:</label>
                        <input type="text" class="form-control" name="punto_llegada" id="punto_llegada">
                      </div>
                      <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                          <label>Departamento (Llegada):</label>
                          <select id="departamento_llegada" name="departamento_llegada" class="form-control select2" required></select>
                      </div>
                      <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                          <label>Provincia (Llegada):</label>
                          <select id="provincia_llegada" name="provincia_llegada" class="form-control select2" required></select>
                      </div>
                      <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12">
                          <label>Distrito (Llegada):</label>
                          <select id="distrito_llegada" name="distrito_llegada" class="form-control select2" required></select>
                      </div>
                      <input type="hidden" class="form-control" name="ubigeo_llegada" id="ubigeo_llegada">
                      <div class="col-md-12">
                        <label>Observación:</label>
                        <textarea class="form-control" name="observacion" id="observacion" rows="3"></textarea>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-12 mt-2 table-responsive">
                  <table id="tabla_detalles" class="table table-striped">
                    <thead>
                      <th>Item</th>
                      <th>Código</th>
                      <th>Artículo</th>
                      <th>Cantidad</th>
                      <th>Unidad</th>
                      <th>Peso</th>
                      <th>Bultos</th>
                      <th>Lotes</th>
                      <th>Quitar</th>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>

                <div class="col-12 text-center mt-3">
                  <button class="btn btn-success btn-lg" type="submit" id="btnGuardar">
                    <i class="fas fa-save"></i> Guardar
                  </button>

                  <button class="btn btn-danger btn-lg" onclick="cancelarform()" type="button">
                    <i class="fas fa-times"></i> Cancelar
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal Seleccionar Producto -->
<div class="modal fade" id="modalProductos" tabindex="-1" role="dialog" aria-labelledby="modalProductosLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalProductosLabel">Seleccionar Producto</h5>
        <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="tabla_productos_modal" class="table table-striped">
          <thead>
            <tr>
              <th>Código</th>
              <th>Producto</th>
              <th>Stock</th>
              <th>Unidad</th>
              <th>Agregar</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="vistas/js/guia.js"></script>