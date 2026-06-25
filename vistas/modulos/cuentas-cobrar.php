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

  .fila-retenida {
    background-color: #ffe5e5 !important;
    /* rojo suave */
    color: #a77170;
  }

  .fila-cuota-vencida {
    background-color: #ffd6d6 !important;
  }

  .fila-cuota-proxima {
    background-color: #fff4cc !important;
  }

  .btn-comment {
    background-color: blue !important;
    color: white !important;
  }

  .btn-amortiar {
    background-color: green !important;
    color: white !important;
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
                  <button class="btn btn-danger" id="btnGenerarReporte" onclick="generarReporte();"><i
                      class="fa fa-file"></i> Reporte Consolidado</button>
                </div>
              </div>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div id="panelSuperiorCxC">
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
                      <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                        value="<?php echo date("Y-m-01"); ?>">
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
                        <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i>
                        <!-- Utilizando la clase fa-lg -->
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
                        <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i>
                        <!-- Utilizando la clase fa-lg -->
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
              </div>

              <div id="vistaListaClientes">
                <table id="tbllistadocuentasxcobrar" class="table table-striped">
                  <thead>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Total creditos</th>
                    <th>Deuda total</th>
                    <th>Total pagado</th>
                    <th>Saldo pendiente</th>
                    <th>Acciones</th>
                  </thead>
                  <tbody>
                  </tbody>
                  <tfoot>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Total creditos</th>
                    <th>Deuda total</th>
                    <th>Total pagado</th>
                    <th>Saldo pendiente</th>
                    <th>Acciones</th>
                  </tfoot>
                </table>
              </div>

              <div id="vistaCreditosCliente" style="display:none;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h5 style="margin:0;">Créditos / Ventas de <span id="detalleClienteTitulo"></span></h5>
                  <button type="button" class="btn btn-secondary btn-sm" onclick="volverListaClientes()">
                    <i class="fas fa-arrow-left"></i> Volver
                  </button>
                </div>

                <table id="tbllistadoCreditosCliente" class="table table-striped table-bordered" width="100%">
                  <thead>
                    <th>Fecha Venta</th>
                    <th>Documento</th>
                    <th>Total Venta</th>
                    <th>Inicial</th>
                    <th>Interes</th>
                    <th>Total Abonado</th>
                    <th>Saldo Pendiente</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </thead>
                  <tbody></tbody>
                  <tfoot>
                    <th>Fecha Venta</th>
                    <th>Documento</th>
                    <th>Total Venta</th>
                    <th>Inicial</th>
                    <th>Interes</th>
                    <th>Total Abonado</th>
                    <th>Saldo Pendiente</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tfoot>
                </table>
              </div>

              <!--table id="tbllistadocuentasxcobrar" class="table table-striped">
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
              </table-->
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

<div class="modal fade" id="modalCuotasCredito">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cuentas por Cobrar del Crédito: <strong id="tituloCreditoCuotas"></strong></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row table-responsive">
          <table id="tbllistadoCuotasCredito" class="table table-striped table-bordered" width="100%">
            <thead>
              <th>Fecha Registro</th>
              <th>Fecha Vencimiento</th>
              <th>Abonado</th>
              <th>Deuda</th>
              <th>Saldo</th>
              <th>Estado</th>
              <th>Acciones</th>
            </thead>
            <tbody></tbody>
            <tfoot>
              <th>Fecha Registro</th>
              <th>Fecha Vencimiento</th>
              <th>Abonado</th>
              <th>Deuda</th>
              <th>Saldo</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalComentario">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Lista de seguimiento de credito</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row table-responsive">
          <table id="tbllistadohistorial" class="table table-striped table-bordered" width="100%">
            <thead>
              <th>#</th>
              <th>Tipo</th>
              <th>Descripcion</th>
              <th>Detalle</th>
              <th>Fecha programada</th>
              <th>Estado</th>
              <th>Prioridad</th>
              <th>Acciones</th>
            </thead>
            <tbody></tbody>
            <tfoot>
              <th>#</th>
              <th>Tipo</th>
              <th>Descripcion</th>
              <th>Detalle</th>
              <th>Fecha programada</th>
              <th>Estado</th>
              <th>Prioridad</th>
              <th>Acciones</th>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalAdjuntos">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Archivos Adjuntos
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div id="contenidoAdjuntos"></div>

            </div>

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
          <style>
            .doc-card {
              border: 1px solid #e9edf3;
              background: #fff;
              border-radius: 12px;
              padding: 14px;
              box-shadow: 0 6px 18px rgba(16, 24, 40, .06);
              max-width: 720px;
            }

            .doc-head {
              display: flex;
              gap: 12px;
              align-items: center;
              padding-bottom: 12px;
              border-bottom: 1px dashed #e9edf3;
            }

            .doc-icon {
              width: 40px;
              height: 40px;
              border-radius: 10px;
              display: grid;
              place-items: center;
              background: rgba(25, 118, 210, .10);
              color: #1976d2;
              font-size: 18px;
            }

            .doc-title strong {
              display: block;
              font-size: 15px;
              color: #0f172a;
            }

            .doc-sub {
              margin-top: 2px;
              font-size: 13px;
              color: #64748b;
            }

            .doc-body {
              padding-top: 12px;
              display: grid;
              gap: 12px;
            }

            .doc-alert {
              background: #f8fafc;
              border: 1px solid #eef2f7;
              border-radius: 12px;
              padding: 12px;
            }

            .doc-alert-row {
              display: flex;
              justify-content: space-between;
              align-items: center;
              gap: 10px;
              padding: 4px 0;
              font-size: 14px;
              color: #334155;
            }

            .money {
              color: #0f172a;
            }

            .doc-totals {
              display: grid;
              grid-template-columns: repeat(2, minmax(0, 1fr));
              gap: 12px;
            }

            .total-box {
              border: 1px solid #eef2f7;
              border-radius: 12px;
              padding: 12px;
              background: #ffffff;
            }

            .total-label {
              font-size: 12px;
              color: #64748b;
              margin-bottom: 6px;
            }

            .total-value {
              font-size: 18px;
              font-weight: 700;
              color: #0f172a;
            }

            /* Responsive */
            @media (max-width: 520px) {
              .doc-totals {
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
                <input class="form-control" type="datetime-local" name="fechaPago" id="fechaPago"
                  value="<?php echo date('Y-m-d H:i:s') ?>">
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
            <strong><i class="fa fa-info"></i> Info!</strong> El monto total del documento electrónico es de <label
              for="abonoTotal2" id="abonoTotal2"></label>, y se han registrado abonos por un total de <label
              for="abonoTotal" id="abonoTotal"></label>.
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
          <input type="hidden" name="idventa_amortizar" id="idventa_amortizar">
          <input type="hidden" id="idcaja" name="idcaja">
          <input type="hidden" name="fecha_inicio_amortizar" id="fecha_inicio_amortizar">
          <input type="hidden" name="fecha_fin_amortizar" id="fecha_fin_amortizar">

          <div class="alert" style="background: #E0F7FA;">
            <strong><i class="fa fa-info"></i> Info!</strong> Amortizacion: tiene un pago pendiente de S/ <label
              for="deudaTotalAmortizar" id="deudaTotalAmortizar"></label>, el cuál se esta realizando una amortizacion;
            A continuación Ingresa el total de dinero abonado y luego haz click en Guardar.
          </div>

          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Condición de Pago::</label>
                <select id="formapagoAmortizar" name="formapagoAmortizar" class="form-control selectpicker"
                  data-live-search="true" required>

                  <option value="Efectivo">En Efectivo</option>

                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Monto a Pagar: <a style="float: right; color: blue"
                    id="btn-seleccionar-cuotas"> Seleccionar cuotas</a></label>
                <input type="text" class="form-control" id="montoPagarAmortizar" name="montoPagarAmortizar" required="">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Monto Adeudado:</label>
                <input class="form-control pull-right" type="text" name="montoAdeudadoAmortizar"
                  id="montoAdeudadoAmortizar" readonly="">
              </div>
            </div>
          </div>
          <style>
            .card-cuotas {
              background: #ffffff;
              border-radius: 16px;
              padding: 25px;
              box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
              margin: auto;
              font-family: Arial, sans-serif;
            }

            .titulo-cuotas {
              margin-bottom: 20px;
              color: #333;
              text-align: center;
            }

            .range-container {
              margin-bottom: 25px;
            }

            #rangeCuotas {
              width: 100%;
              accent-color: #2563eb;
              cursor: pointer;
            }

            .info-cuotas {
              display: flex;
              gap: 15px;
            }

            .box-info {
              flex: 1;
              background: #f5f7fb;
              padding: 15px;
              border-radius: 12px;
              text-align: center;
            }

            .box-info .label {
              display: block;
              font-size: 14px;
              color: #666;
              margin-bottom: 8px;
            }

            .box-info .valor {
              font-size: 24px;
              font-weight: bold;
              color: #222;
            }

            .box-info.total {
              background: #2563eb;
            }

            .box-info.total .label,
            .box-info.total .valor {
              color: white;
            }
          </style>
          <div class="row" id="panel-pagar-cuotas" style="display: none">
            <div class="col-sm-12">
              <div class="card-cuotas">

                <h3 class="titulo-cuotas">
                  Seleccionar cuotas
                </h3>

                <div id="contenedorRange" class="range-container"></div>

                <div class="info-cuotas">

                  <div class="box-info">
                    <span class="label">Cuotas a pagar</span>
                    <span class="valor" id="cantidadSeleccionada">1</span>
                  </div>

                  <div class="box-info total">
                    <span class="label">Total a pagar</span>
                    <span class="valor">S/ <span id="totalPagar">0.00</span></span>
                  </div>

                </div>

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
<div class="modal fade" id="modalRecordatorioResultados" tabindex="-1" role="dialog"
  aria-labelledby="modalRecordatorioResultadosLabel" aria-hidden="true">
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


<!-- MODAL PROGRAMAR VISITA -->
<div class="modal fade" id="modalProgramarVisita" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header bg-warning">
        <h5 class="modal-title">
          <i class="fas fa-calendar-check"></i>
          Programar evento
        </h5>

        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <form id="formProgramarVisita" enctype="multipart/form-data">

        <div class="modal-body">

          <input type="hidden" name="idcpc" id="idcpc_visita">
          <input type="hidden" name="idventa" id="idventa_visita">
          <input type="hidden" name="idcliente" id="idcliente_visita">

          <div class="row">
            <div class="col-md-7">
              <div class="row">

                <!-- RESPONSABLE -->
                <div class="form-group col-md-6">
                  <label>
                    Responsable
                  </label>

                  <select class="form-control" name="idpersonal" id="idpersonal">

                    <option value="">Seleccione</option>

                    <!-- CARGAR USUARIOS -->
                  </select>
                </div>

                <!-- TIPO -->
                <div class="form-group col-md-6">
                  <label>Tipo visita</label>

                  <select class="form-control" name="tipo_visita" id="tipo_visita">

                    <option value="LLAMADA">
                      LLAMADA
                    </option>
                    <option value="VISITA">
                      VISITA
                    </option>
                    <option value="COBRANZA">
                      COBRANZA
                    </option>
                    <option value="WHATSAPP">
                      WHATSAPP
                    </option>
                    <option value="CORREO">
                      CORREO
                    </option>
                    <option value="VERIFICACION">
                      VERIFICACION
                    </option>
                    <option value="SEGUIMIENTO">
                      SEGUIMIENTO
                    </option>

                    <option value="NEGOCIACION">
                      NEGOCIACION
                    </option>
                    <option value="COBRANZA">
                      OTRO
                    </option>

                  </select>
                </div>

                <!-- PRIORIDAD -->
                <div class="form-group col-md-6">
                  <label>Prioridad</label>

                  <select class="form-control" name="prioridad" id="prioridad">

                    <option value="BAJA">Baja</option>
                    <option value="MEDIA">Media</option>
                    <option value="ALTA">Alta</option>
                    <option value="URGENTE">Urgente</option>

                  </select>
                </div>

                <!-- PRIORIDAD -->
                <div class="form-group col-md-6">
                  <label>Estado</label>

                  <select class="form-control" name="estado" id="estado">

                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="REALIZADO">REALIZADO</option>
                    <option value="NO_RESPONDE">NO RESPONDE</option>
                    <option value="REPROGRAMADO">REPROGRAMADO</option>

                  </select>
                </div>


                <!-- FECHA -->
                <div class="form-group col-md-6">
                  <label>
                    Fecha visita <span class="text-danger">*</span>
                  </label>

                  <input type="datetime-local" class="form-control" name="fecha_programada" id="fecha_programada"
                    required>
                </div>


                <!-- FECHA -->
                <div class="form-group col-md-6">
                  <label>
                    Fecha final
                  </label>
                  <input type="datetime-local" class="form-control" name="fecha_final" id="fecha_final">
                </div>

                <!-- DIRECCION -->
                <div class="form-group col-md-12">
                  <label>Dirección</label>

                  <input type="text" class="form-control" name="direccion" id="direccion"
                    placeholder="Ingrese dirección de visita">
                </div>

                <!-- OBSERVACION -->
                <div class="form-group col-md-12">
                  <label>Observación</label>

                  <textarea class="form-control" name="descripcion" id="descripcion" rows="4"
                    placeholder="Detalle de la visita..."></textarea>
                </div>
              </div>
            </div>

            <style>
              .upload-box {
                border: 2px dashed #f0ad4e;
                border-radius: 15px;
                background: #fffaf2;
                cursor: pointer;
                transition: .3s;
              }

              .upload-box:hover {
                background: #fff3df;
                border-color: #ec971f;
              }

              .preview-item {
                border: 1px solid #eee;
                border-radius: 12px;
                padding: 10px;
                background: white;
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 10px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, .05);
              }

              .preview-left {
                display: flex;
                align-items: center;
                gap: 10px;
              }

              .preview-left i {
                font-size: 30px;
              }

              .btn-delete-file {
                border: none;
                background: #dc3545;
                color: white;
                width: 30px;
                height: 30px;
                border-radius: 50%;
              }
            </style>
            <div class="col-md-5">
              <div class="row">
                <!-- ADJUNTOS -->
                <div class="form-group col-md-12">

                  <label>
                    Adjuntar archivos
                  </label>

                  <div class="custom-file-upload">

                    <input type="file" id="adjuntos" class="d-none" multiple accept="
                .pdf,
                .doc,
                .docx,
                .xls,
                .xlsx,
                .jpg,
                .jpeg,
                .png,
                .webp,
                .mp4,
                .mp3
            ">

                    <label for="adjuntos" class="upload-box w-100">

                      <div class="text-center p-4">

                        <i class="fas fa-cloud-upload-alt fa-3x text-warning mb-3"></i>

                        <h5 class="mb-2">
                          Subir documentos
                        </h5>

                        <p class="text-muted mb-2">
                          Agrega múltiples archivos
                        </p>

                        <span class="badge badge-warning p-2">
                          PDF · Word · Excel · Imágenes · Audio · Video
                        </span>

                      </div>

                    </label>

                  </div>

                  <!-- LISTA -->
                  <div id="previewArchivos" class="row mt-3"></div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-secondary" data-dismiss="modal">

            <i class="fas fa-times"></i>
            Cerrar
          </button>

          <button type="submit" class="btn btn-warning">

            <i class="fas fa-save"></i>
            Guardar programación
          </button>

        </div>

      </form>
      <!-- ADJUNTOS -->

    </div>
  </div>
</div>


<div class="modal fade" id="modalCompromisoPago" tabindex="-1" role="dialog" aria-labelledby="modalCompromisoPagoLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h4 class="modal-title">
                    <i class="fas fa-file-signature"></i>
                    Registrar Compromiso de Pago
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="formCompromisoPago">

                <div class="modal-body">

                    <input type="hidden" id="idcpc" name="idcpc">
                    <input type="hidden" id="idventa" name="idventa">
                    <input type="hidden" id="idcliente" name="idcliente">

                    <div class="form-group">
                        <label>
                            Fecha de Compromiso <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            class="form-control"
                            id="fecha_compromiso"
                            name="fecha_compromiso"
                            required>
                    </div>

                    <div class="form-group">
                        <label>
                            Monto Comprometido <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            class="form-control"
                            id="monto"
                            name="monto"
                            placeholder="0.00"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Observación</label>
                        <textarea
                            class="form-control"
                            id="observacion"
                            name="observacion"
                            rows="4"
                            placeholder="Detalle del compromiso realizado con el cliente"></textarea>
                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Compromiso
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script type="text/javascript" src="vistas/js/cuentascobrar.js"></script>
<script type="text/javascript" src="vistas/js/ventasfechacliente2.js"></script>