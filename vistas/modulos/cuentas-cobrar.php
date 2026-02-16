<!-- Content Wrapper. Contains page content -->
<?php
date_default_timezone_set('America/Lima');
?>

<style>
    .modal-header-custom {
        background: #007bff;
        color: white;
        padding: 12px 20px;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
    }
    .info-box-custom {
        background: #eaf7ff;
        border-left: 5px solid #007bff;
        padding: 12px 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        font-size: 14px;
    }
    .section-title {
        font-size: 15px;
        font-weight: bold;
        margin-bottom: 8px;
        margin-top: 10px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 4px;
        color: #444;
    }

    /* =======================================
   MODAL DE COBROS – ESTILO PROFESIONAL
   ======================================= */

/* --- Título del modal --- */
.modal-header-custom {
    background: linear-gradient(45deg, #007bff, #005fcc);
    color: white;
    padding: 14px 22px !important;
    border-bottom: none !important;
}
.modal-header-custom .modal-title {
    font-size: 18px;
    font-weight: 600;
}
.modal-header-custom .close {
    font-size: 26px;
    opacity: 1;
    color: white;
}

/* --- Caja de información --- */
.info-box-custom {
    background: #f0f8ff;
    border-left: 5px solid #007bff;
    padding: 14px 18px;
    border-radius: 6px;
    box-shadow: 0px 2px 6px rgb(0 0 0 / 5%);
}

/* --- Secciones del formulario --- */
.section-title {
    background: #fafafa;
    padding: 6px 10px;
    font-size: 15px;
    font-weight: bold;
    border-left: 4px solid #007bff;
    margin-top: 18px;
    margin-bottom: 12px;
}

/* --- Inputs y selects --- */
#formulario .form-group label {
    font-weight: 600;
    color: #444;
}
#formulario input,
#formulario select,
#formulario textarea {
    border-radius: 5px !important;
    border: 1px solid #c9c9c9 !important;
}
#formulario input:focus,
#formulario select:focus,
#formulario textarea:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 4px #007bff55 !important;
}

/* --- Botones del pie del modal --- */
.modal-footer .btn {
    padding: 10px 22px;
    font-size: 15px;
    border-radius: 6px;
}
.btn-primary {
    background-color: #007bff !important;
    border: none !important;
}
.btn-primary:hover {
    background-color: #0069d9 !important;
}

/* --- Botón cerrar --- */
.btn-secondary {
    background-color: #6c757d !important;
    border: none !important;
}
.btn-secondary:hover {
    background-color: #5a636b !important;
}

/* --- Animación suave del modal --- */
.modal.fade .modal-dialog {
    transition: transform .2s ease-out;
    transform: translateY(-20px);
}
.modal.show .modal-dialog {
    transform: translateY(0);
}

/* --- Mejora visual en selectpicker --- */
.bootstrap-select .dropdown-toggle {
    border-radius: 5px !important;
    border: 1px solid #c0c0c0 !important;
}

/* --- Colores para montos --- */
#montoAdeudado {
    font-weight: bold;
    color: #a80000;
}
#deutaTotal {
    color: #d10000;
    font-weight: bold;
}

/* --- Mejor espaciado entre elementos --- */
.modal-body .row {
    margin-bottom: 4px;
}

/* --- Scroll elegante si el modal crece --- */
.modal-body {
    max-height: 65vh;
    overflow-y: auto;
    padding-right: 15px;
}
#getCodeModal .modal-body {
    max-height: none !important;
    overflow-y: visible !important;
}

</style>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Cuentas por Cobrar</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Cuentas por Cobrar</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content" style="margin-top: -20px;">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">

            <div class="card-header" hidden>
              <h3 class="card-title"> </h3>

              <div class="row">
                <div class="col-md-2" hidden>
                  <button class="btn btn-danger" id="btnGenerarReporte" onclick="generarReporte();"><i class="fa fa-file"></i> Reporte Consolidado</button>
                </div>
              </div>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="col-md-3">
                  <button class="btn btn-warning" id="btnEnviarRecordatorioSemana">
                      <i class="fas fa-paper-plane"></i> Enviar recordatorios vencidos
                  </button>
              </div>
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
                  <label>Cliente:</label>

                  <div class="input-group mb-3">
                    <select id="idcliente" name="idcliente" class="form-control select2" required>
                    </select>
                  </div>
                  <div id="btnEstadoCuenta" style="display:none;">
                      <button type="button" class="btn btn-info btn-sm" id="btnEstadoCuentaAccion">
                          Estado de Cuenta
                      </button>
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
                  <th>Total Venta</th>
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
                  <th>Total Venta</th>
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

<div class="modal fade" id="modalEstadoCuenta">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Estado de Cuenta</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="estadoCuentaContenido"></div>
    </div>
  </div>
</div>

<div class="modal fade" id="getCodeModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header modal-header-custom">
        <h5 class="modal-title"><i class="fa fa-money"></i> Registro de Pago / Abono</h5>
        <button type="button" class="close text-white" data-dismiss="modal">×</button>
      </div>

      <form class="form-horizontal" role="form" id="formulario" method="POST">

        <div class="modal-body">

          <!-- Campos ocultos -->
          <input type="hidden" name="idcpc" id="idcpc">
          <input type="hidden" id="idcaja" name="idcaja">
          <input type="hidden" name="idventa" id="idventa">
          <style>.doc-card{
            border: 1px solid #e9edf3;
            background: #fff;
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 6px 18px rgba(16,24,40,.06);
            max-width: 720px;
          }

          .doc-head{
            display: flex;
            gap: 12px;
            align-items: center;
            padding-bottom: 12px;
            border-bottom: 1px dashed #e9edf3;
          }

          .doc-icon{
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            background: rgba(25,118,210,.10);
            color: #1976d2;
            font-size: 18px;
          }

          .doc-title strong{
            display: block;
            font-size: 15px;
            color: #0f172a;
          }

          .doc-sub{
            margin-top: 2px;
            font-size: 13px;
            color: #64748b;
          }

          .doc-body{
            padding-top: 12px;
            display: grid;
            gap: 12px;
          }

          .doc-alert{
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 12px;
          }

          .doc-alert-row{
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 4px 0;
            font-size: 14px;
            color: #334155;
          }

          .money{
            color: #0f172a;
          }

          .doc-totals{
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
          }

          .total-box{
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 12px;
            background: #ffffff;
          }

          .total-label{
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
          }

          .total-value{
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
          }

          /* Responsive */
          @media (max-width: 520px){
            .doc-totals{
              grid-template-columns: 1fr;
            }
          }
      </style>

      <div class="doc-body mb-2">
        <div class="doc-totals">
          <div class="total-box">
            <div class="total-label">Total venta</div>
            <div class="total-value">S/ <span id="valorVenta"></span></div>
          </div>

          <div class="total-box">
            <div class="total-label">Total interés</div>
            <div class="total-value">S/ <span id="valorInteres"></span></div>
          </div>
        </div>
      </div>


          <!-- Caja de información -->
          <div class="info-box-custom">
            <strong><i class="fa fa-info-circle"></i> Información del Documento</strong><br>
            El documento <b><span id="documento"></span></b> tiene un pago pendiente de  
            <b>S/ <span id="deutaTotal"></span></b>.  
            Debe pagarse como máximo el día <b><span id="fechavencimiento"></span></b>.
          </div>

          <div class="section-title"><i class="fa fa-credit-card"></i> Datos del Pago</div>

          <div class="row">

            <div class="col-sm-3">
              <div class="form-group">
                <label>Condición de Pago:</label>
                <select id="formapago" name="formapago" class="form-control" required>
                  <option value="Efectivo">Efectivo</option>
                  <option value="Transferencia">Transferencia o Tarjeta</option>
                  <option value="Yape">Yape</option>
                  <option value="Plin">Plin</option>
                  <option value="Deposito">Depósito</option>
                </select>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label>Monto Efectivo:</label>
                <input type="text" class="form-control" id="montoPagar" name="montoPagar" required>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label>Monto Tarjeta:</label>
                <input type="text" class="form-control" id="montoPagarTarjeta" name="montoPagarTarjeta" readonly>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label>Monto Adeudado:</label>
                <input class="form-control" type="text" name="montoAdeudado" id="montoAdeudado" readonly>
              </div>
            </div>

          </div>

          <div class="section-title"><i class="fa fa-pencil"></i> Observación</div>

          <div class="row">
            <div class="col-sm-12">
              <textarea class="form-control" name="observacion" id="observacion" rows="2"></textarea>
            </div>
          </div>

          <div class="section-title"><i class="fa fa-building"></i> Pago Bancario</div>

          <div class="row">

            <div class="col-sm-4">
              <div class="form-group">
                <label>Banco:</label>
                <select id="banco" name="banco" class="form-control selectpicker" data-live-search="true">
                  <option value="BCP">BCP</option>
                  <option value="INTERBANK">INTERBANK</option>
                  <option value="BBVA">BBVA</option>
                </select>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label>Número de Operación (OP):</label>
                <input class="form-control" type="text" name="op" id="op">
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label>Fecha de Pago:</label>
                <input class="form-control" type="date" name="fechaPago" id="fechaPago">
              </div>
            </div>

          </div>

        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarform()" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cerrar
          </button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">
            <i class="fa fa-check"></i> Guardar Pago
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<div class="modal fade" id="getCodeModal2">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><span id="titulo-formulario">Lista de</span> Abonos</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
        <div class="modal-body">

          <input type="hidden" name="idcpc" id="idcpc">
          <input type="hidden" id="idcaja" name="idcaja">
          <input type="hidden" name="idventa" id="idventa">

          <div class="alert" style="background: #E0F7FA;">
            <strong><i class="fa fa-info"></i> Info!</strong> El monto total del documento electrónico es de <label for="abonoTotal2" id="abonoTotal2"></label>, y se han registrado abonos por un total de <label for="abonoTotal" id="abonoTotal"></label>.
          </div>

          <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover" width="100%">
            <thead>
              <th style="width: 100px;">Fecha Registro</th>
              <th style="width: 25px;">Monto Efectivo</th>
              <th style="width: 25px;">Monto Tarjeta</th>
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
          <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
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
          <input type="hidden"  id="idcaja" name="idcaja">
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
<!-- Modal para mostrar resultados -->
<div class="modal fade" id="modalRecordatorioResultados" tabindex="-1" role="dialog" aria-labelledby="modalRecordatorioResultadosLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Resultados de envío de recordatorios</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="recordatorioResultadosContenido">
        <!-- Aquí se mostrará la tabla con resultados -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEstadoCuenta">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Estado de Cuenta del Cliente</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <div id="estadoCuentaContenido"></div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button class="btn btn-primary" onclick="imprimirEstadoCuenta()">Imprimir</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="vistas/js/cuentascobrar.js"></script>
<script type="text/javascript" src="vistas/js/ventasfechacliente2.js"></script>