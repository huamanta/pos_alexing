<?php
date_default_timezone_set('America/Lima');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Cuentas por Pagar</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Cuentas por Pagar</li>
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

              <div class="row" hidden>
                <div class="col-md-2">
                  <button class="btn btn-danger" id="btnGenerarReporte" onclick="generarReporte();"><i class="fa fa-file"></i> Reporte Consolidado</button>
                </div>
              </div>

            </div>
            <!-- /.card-header -->
            <div class="card-body">

              <div class="row">

                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
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

                <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
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
                  <label>Proveedor:</label>

                  <div class="input-group mb-3">
                    <select id="idcliente" name="idcliente" class="form-control select2" required>
                    </select>
                  </div>

                </div>

              </div>
              <!-- row Tarjetas Informativas -->
              <div class="row">
                <div class="col-lg-4" style="color: blue; font-weight: 900; font-size: 25px">
                  <!-- small box -->
                  <div class="small-box ">
                    <div class="inner">
                      <h4 id=""></h4>
                      <p>Total: <span id="saldos"></span></p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i> <!-- Utilizando la clase fa-lg -->
                    </div>
                  </div>
                </div>

                <!-- TARJETA TOTAL COMPRAS -->
                <div class="col-lg-4" style="color: green; font-weight: 900; font-size: 25px">
                  <!-- small box -->
                  <div class="small-box ">
                    <div class="inner">
                      <h4 id=""></h4>
                      <p>Abono: <span id="abonos"></span></p>
                    </div>
                    <div class="icon">
                      <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i> <!-- Utilizando la clase fa-lg -->
                    </div>
                  </div>
                </div>

                <!-- TARJETA TOTAL VENTAS -->
                <div class="col-lg-4">
                  <!-- small box -->
                  <div class="small-box ">
                    <div class="inner" style="color: red; font-weight: 900; font-size: 25px">
                      <h4 id=""></h4>
                      <p>Deuda: <span id="deudas"></span></p>
                    </div>
                    <div class="icon" id="panel_amortizar">

                    </div>
                  </div>
                </div>
              </div>


              <!-- ./row Tarjetas Informativas -->

              <table id="tbllistadocuentasxcobrar" class="table table-striped">
                <thead>
                  <th>Fecha Registro</th>
                  <th>Documento</th>
                  <th>Cliente</th>
                  <th>Dni / Ruc</th>
                  <th>Saldo</th>
                  <th>Abonos Total</th>
                  <th>Total Compra</th>
                  <th>Fecha Vencimiento</th>
                  <th>Estado</th>
                  <th>Detalle</th>
                  <th>Acciones</th>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <th>Fecha Registro</th>
                  <th>Documento</th>
                  <th>Cliente</th>
                  <th>Dni / Ruc</th>
                  <th>Saldo</th>
                  <th>Abonos Total</th>
                  <th>Total Compra</th>
                  <th>Fecha Vencimiento</th>
                  <th>Estado</th>
                  <th>Detalle</th>
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

<!-- Modal -->
<div id="getCodeModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><span id="titulo-formulario">Registrar</span> Abono</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form role="form" name="formulario" id="formulario" method="POST">

        <div class="modal-body">

          <input type="hidden" name="idcpc" id="idcpc">

          <input type="hidden" name="idventa" id="idventa">

          <div class="alert" style="background: #E0F7FA;">
            <strong><i class="fa fa-info"></i> Info!</strong> El Documento: <label for="documento" id="documento"></label> tiene un pago pendiente de S/ <label for="deudaTotal" id="deutaTotal"></label>, el cuál se debe realizar como máximo el día: <label for="fechavencimiento" id="fechavencimiento"></label>. A continuación Ingresa el total de dinero abonado y luego haz click en Guardar.
          </div>
          <div class="row">
            <div class="form-group col-lg-3">
              <label for="name" class="control-label">Condición de Pago: </label>
              <select id="formapago" name="formapago" class="form-control selectpicker" data-live-search="true" required>

                <option value="Efectivo">En Efectivo</option>
                <option value="Transferencia">Transferencia o Tarjeta</option>
                <option value="Yape">Yape</option>
                <option value="Plin">Plin</option>
                <option value="Deposito">Deposito</option>

              </select>
            </div>
            <div class="form-group col-lg-3">
              <label class="col-form-label">Monto Adeudado:</label>
              <input class="form-control pull-right" type="text" name="montoAdeudado" id="montoAdeudado" readonly="">
            </div>

            <div class="form-group col-lg-3">
              <label class="col-form-label">Monto a deposito:</label>
              <input type="text" class="form-control" id="montoPagarTarjeta" name="montoPagarTarjeta" readonly>
            </div>

            <div class="form-group col-lg-3">
            <label class="col-form-label">Monto a efectivo:</label>
            <input type="text" class="form-control" id="montoPagar" name="montoPagar" required="">
          </div>
        </div>

          <div class="row">
            <div class="form-group col-lg-4">
              <label class="col-form-label">Banco:</label>
              <select id="banco" name="banco" class="form-control selectpicker" data-live-search="true" title="Seleccionar Banco" readonly="">

                <option value="BCP">BCP</option>
                <option value="INTERBANK">INTERBANK</option>
                <option value="BBVA">BBVA</option>

              </select>
            </div>
            <div class="form-group col-lg-4">
              <label class="col-form-label">OP:</label>
              <input class="form-control pull-right" type="text" name="op" id="op" readonly>
            </div>
            <div class="form-group col-lg-4">
              <label class="col-form-label">Fecha de Pago:</label>
              <input class="form-control pull-right" type="date" name="fechaPago" id="fechaPago" value="<?php echo date("Y-m-d"); ?>">
            </div>
          </div>

          <div class="form-group col-lg-12">
            <label class="col-form-label">Observación:</label>
            <textarea class="form-control" name="observacion" id="observacion"></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Fin modal -->

<!-- Modal -->
<div id="getCodeModal2" class="modal fade" role="dialog">

  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content panel panel-primary">

      <form role="form" name="formulario" id="formulario" method="POST">

        <div class="modal-body">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title"><span id="titulo-formulario">Lista de</span> Abonos</h4>

        </div>

        <div class="modal-body panel-body">

          <div class="alert" style="background: #E0F7FA;">
            <strong><i class="fa fa-info"></i> Info!</strong> El monto total del documento electrónico es de <label for="abonoTotal2" id="abonoTotal2"></label>, y se han registrado abonos por un total de <label for="abonoTotal" id="abonoTotal"></label>.
          </div>

          <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover" width="100%">
            <thead>
              <th style="width: 100px;">Fecha Registro</th>
              <th style="width: 25px;">Monto</th>
              <th style="width: 150px;">Forma de Pago</th>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <th>Nombre</th>
              <th>Estado</th>
              <th>Forma de Pago</th>
            </tfoot>
          </table>


        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarform()" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
        </div>

      </form>

    </div>

  </div>

</div>

<div class="modal fade" id="modalAmortizar">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><span id="titulo-formulario-amortizar">Lista de</span> Abonos</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario-amortizar" id="formulario-amortizar" method="POST">
        <div class="modal-body">

          <input type="hidden" name="idcliente_amortizar" id="idcliente_amortizar">

          <input type="hidden" name="fecha_inicio_amortizar" id="fecha_inicio_amortizar">
          <input type="hidden" name="fecha_fin_amortizar" id="fecha_fin_amortizar">

          <div class="alert" style="background: #E0F7FA;">
            <strong><i class="fa fa-info"></i> Info!</strong> Amortizacion: tiene un pago pendiente de S/ <label for="deudaTotalAmortizar" id="deudaTotalAmortizar"></label>, el cuál se esta realizando una amortizacion; A continuación Ingresa el total de dinero abonado y luego haz click en Guardar.
          </div>

          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Condición de Pago::</label>
                <select id="formapagoAmortizar" name="formapagoAmortizar" class="form-control selectpicker" data-live-search="true" required>

                  <option value="Efectivo">En Efectivo</option>

                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Monto a Pagar:</label>
                <input type="text" class="form-control" id="montoPagarAmortizar" name="montoPagarAmortizar" required="">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Monto Adeudado:</label>
                <input class="form-control pull-right" type="text" name="montoAdeudadoAmortizar" id="montoAdeudadoAmortizar" readonly="">
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<!-- Fin modal -->

<script type="text/javascript" src="vistas/js/cuentaspagar.js"></script>
<script type="text/javascript" src="vistas/js/ventasfechacliente2.js"></script>