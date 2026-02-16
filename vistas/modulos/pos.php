
<?php
function tienePermiso($modulo, $accion, $submodulo = null) {
    if ($submodulo === null) {
        return isset($_SESSION['acciones'][$modulo][$accion]) && $_SESSION['acciones'][$modulo][$accion] === true;
    } else {
        return isset($_SESSION['acciones'][$modulo][$submodulo][$accion]) && $_SESSION['acciones'][$modulo][$submodulo][$accion] === true;
    }
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link active" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto" id="navbar-pos">

  </ul>

</nav>

<form action="" id="procesar-venta">
  <div class="content-wrapper" id="pos-venta" hidden>
    <section class="content-header">
      <div class="container-fluid">


        <div class="row">
          <div class="col-sm-6">
            <div class="row">
              <div class="col-sm-12">
                <div class="card shadow" style="margin-top: -10px;">
                  <div class="col-md-12" style="margin-bottom: 10px;">
                    <div class="scrollmenu" id="cardCategorias" style="background-color: transparent;">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="container-fluid">
                  <div class="card">
                    <div class="card-body">
                      <div id="cardContainer" class="row">
                        <!-- Aquí se agregarán las cards dinámicamente con JavaScript -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="card shadow" id="card2" style="margin-top: -10px;"> <input type="hidden" id="idcaja" name="idcaja">
              <div class="card-header">
                <span style="font-weight: bold;">Nuevo pedido</span>
                <span id="fechaActual" style="font-size: 10.5px; text-align: right; margin-left: 10px;"></span>
              </div>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text active-search" id="btn_text_search" style="cursor:pointer;">
                    <i class="fas fa-keyboard"></i>
                  </span>
                  <span class="input-group-text" id="btn_barcode_search" style="cursor:pointer;">
                    <i class="fas fa-barcode"></i>
                  </span>
                  <!-- Botón cámara -->
                  <span class="input-group-text" id="btn_camera_search" style="cursor:pointer;" title="Escanear con cámara">
                    <i class="fas fa-camera"></i>
                  </span>
                </div>
                <input autocomplete="off" type="search" class="form-control" id="search-producto" placeholder="Buscar producto por nombre">
              </div>
              <div class="card-header">
                <button type="button" class="btn btn-block bg-gradient-primary btn-sm shadow" onclick="toggleCard()" title="Completa los datos de tu pedido">Datos</button>
              </div>
              <br>

              <div class="card">
                <div class="card-body" id="datosgenerales" style="margin-top: -35px;background-color: #cce5ff;" hidden>
                  <div class="row" style="margin-top: -15px;">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <select id="idsucursal" name="idsucursal" class="form-control">
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <select class="form-control" name="tipo_comprobante" id="tipo_comprobante">
                          <?php if (tienePermiso('Pos', 'Crear Nota de Venta', 'Punto de Venta',)): ?>
                            <option>Nota de Venta</option>
                          <?php endif; ?>
                          <?php if (tienePermiso('Pos', 'Crear Boleta', 'Punto de Venta')): ?>
                            <option>Boleta</option>
                          <?php endif; ?>
                          <?php if (tienePermiso('Pos', 'Crear Factura', 'Punto de Venta')): ?>
                            <option>Factura</option>
                          <?php endif; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row" style="margin-top: -20px;">
                    <div class="col-md-6">
                      <div id="" class="input-group-addon">Serie:</div>
                      <input class="form-control" type="text" name="serie_comprobante" id="serie_comprobante" maxlength="7" readonly>
                    </div>

                    <div class="col-md-6">
                      <div class="input-group-addon">Número:</div>

                      <input class="form-control" type="text" name="num_comprobante" id="num_comprobante" maxlength="10" readonly>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <label class="col-form-label" for="selCategoriaReg">
                        <i class="fas fa-users fs-6"></i>
                        <span class="small mr-2">Cliente</span><a class="input-group-addon" style="cursor: pointer;" data-toggle="modal" data-target="#ModalClientes"><i class="fa fa-plus fa-xs"></i> Nuevo Cliente</a>
                      </label>
                      <select id="idcliente" name="idcliente" class="form-control" required>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div id="" class="input-group-addon">Observaciones:</div>
                      <textarea class="form-control" name="observaciones" id="observaciones" rows="3" placeholder="Enter ..."></textarea>
                    </div>
                  </div>
                </div>
                <div class="card-body" style="margin-top: -45px;">
                  <div class="table-responsive table-scroll-limit">
                    <table id="agregarcarrito" class="table table-striped table-responsive-sm">
                      <thead style="width: 100%;">
                        <th width="300px">Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                      </thead>
                      <tbody>

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="card-footer">
                <div class="col-sm-12">
                  <div class="row">
                    <div class="col-sm-8">
                      <label>Sub Total:</label>
                    </div>
                    <div class="col-sm-4">
                      <span style=" text-align: right; display: block;" id="subtotal-venta">0.00</span>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="row">
                    <div class="col-sm-8">
                      <label>Comisión Vendedor:</label>
                    </div>
                    <div class="col-sm-4">
                      <span style=" text-align: right; display: block;" id="subtotal-ventaC">0.00</span>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12" hidden>
                  <div class="row">
                    <div class="col-sm-8">
                      <label style="color:blue">IGV 18%:</label>
                    </div>
                    <div class="col-sm-4">
                      <span style="color: blue; text-align: right; display: block;" id="igv-venta">0.00</span>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12" hidden>
                  <div class="row">
                    <div class="col-sm-8">
                      <label style="color:green;">Total:</label>
                    </div>
                    <div class="col-sm-4">
                      <input type="hidden" name="input-total-venta" id="input-total-venta" value="0">
                      <span style="color: green; text-align: right; display: block;" id="total-venta">0.00</span>
                    </div>
                  </div>
                </div>
                <button type="button" class="btn btn-block bg-gradient-primary btn-lg" id="pasar-caja">Pasar a caja</button>
              </div>
            </div>
          </div>
        </div>


      </div><!-- /.container-fluid -->
    </section>

    <!-- Modal Scanner -->
<div id="cameraScannerModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.75); justify-content:center; align-items:center; z-index:9999;">
    <div id="interactive-scanner" style="position:relative; width:100%; max-width:400px; aspect-ratio:16/9; border-radius:1rem; overflow:hidden;">
        <video autoplay playsinline style="width:100%; height:100%; object-fit:cover;"></video>
        <canvas id="scannerOverlay" style="position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;"></canvas>
    </div>
    <button id="btn_stop_scanner" style="position:absolute; top:10px; right:10px; z-index:10;">Cerrar</button>
</div>

<!-- Modal Caja de Cobro -->
<div class="modal fade" id="modal-default" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title font-weight-bold">Caja de Cobro</h4>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>

      <!-- Body -->
      <div class="modal-body">

        <!-- Totales generales -->
        <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded shadow-sm">
          <div>
            <div>Total Venta:</div>
            <div class="h5 font-weight-bold" id="total-pedido">S/. 0.00</div>
            <input type="hidden" id="input-total-venta" value="0">
          </div>
          <div>
            <div>Total Pagado:</div>
            <div class="h5 font-weight-bold text-success" id="total-pagado">S/. 0.00</div>
            <input type="hidden" id="pagado-total">
          </div>
          <div>
            <div>Vuelto:</div>
            <div class="h5 font-weight-bold text-primary" id="vuelto">0.00</div>
            <input type="hidden" id="input-vuelto">
          </div>
        </div>

        <!-- Métodos de pago -->
        <div class="payment-methods">

          <!-- Efectivo por defecto -->
          <div class="d-flex align-items-center mb-2 p-2 border rounded bg-white shadow-sm">
            <img id="icono-principal" src="files/icons/efectivo.ico" alt="Efectivo" style="height: 28px; margin-right:10px;">
            <label id="label-principal" class="flex-grow-1 mb-0 font-weight-bold">Efectivo</label>
            <select id="tipo-principal" class="form-control form-control-sm mr-2" style="max-width:140px;">
              <option value="Efectivo" selected>Efectivo</option>
              <option value="yape">Yape</option>
              <option value="plin">Plin</option>
              <option value="visa">Visa</option>
              <option value="mastercard">MasterCard</option>
              <option value="deposito">Depósito</option>
            </select>
            <!-- input VISIBLE: NO debe tener name="pagado[]" (evitamos que se envíe directamente) -->
            <input type="text" id="input-efectivo" class="form-control form-control-sm text-right" placeholder="Monto">

            <!-- hidden para enviar al backend el total recibido (lo que entregó el cliente) -->
            <input type="hidden" id="totalrecibido" name="totalrecibido" value="0.00">
          </div>
          <!-- inputs adicionales (ocultos por defecto) -->
          <div id="extras-principal" class="d-none mt-2">
            <div class="d-flex gap-1">
              <input type="text" class="form-control form-control-sm" name="nroOperacion[]" placeholder="Nro Operación">
              <input type="text" class="form-control form-control-sm" name="banco[]" placeholder="Banco">
              <input type="date" class="form-control form-control-sm" name="fechaDeposito[]">
            </div>
          </div>


        </div>

        <!-- Botón agregar pago -->
        <div class="text-center mb-3">
          <button type="button" class="btn btn-outline-primary btn-sm" id="agregar-pago-btn">
            <i class="fas fa-plus"></i> Agregar pago
          </button>
        </div>

        <!-- Totales resumidos -->
        <div class="mt-2 p-2 bg-light rounded shadow-sm">
          <div class="d-flex justify-content-between mb-1">
            <label class="mb-0 font-weight-bold">Total recibido efectivo:</label>
            <input type="text" id="total-efectivo" class="form-control text-right form-control-sm" readonly value="0.00" style="max-width:120px;">
          </div>
          <div class="d-flex justify-content-between">
            <label class="mb-0 font-weight-bold">Total recibido otros pagos:</label>
            <input type="text" id="total-otros" class="form-control text-right form-control-sm" readonly value="0.00" style="max-width:120px;">
          </div>
        </div>

      </div>
      <!-- Hidden para enviar al backend -->
        <!-- hidden que envía JS al backend -->
        <input type="hidden" id="pagado-total" name="pagado_total" value="0.00">   <!-- asegúrate que tenga name -->
        <input type="hidden" id="hidden-totalrecibido" name="totalrecibido" value="0.00">
        <input type="hidden" id="hidden-totaldeposito" name="totaldeposito" value="0.00">
        <input type="hidden" id="hidden-vuelto" name="vuelto" value="0.00">
      <!-- Footer -->
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-danger btn-sm" id="cancelar-btn"  data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success btn-sm" id="guardar-sin-imprimir" data-dismiss="modal">Guardar <i class="fas fa-save"></i></button>
        <button hidden type="submit" class="btn btn-primary btn-sm">Imprimir <i class="fas fa-print"></i></button>
      </div>

    </div>
  </div>
</div>


    <!-- /.modal -->
  </div>
</form>

<div class="content-wrapper" id="pos-caja" hidden>
  <section class="content-header">
    <div class="container-fluid">


      <div class="row">
        <div class="col-sm-4" style="margin: 0 auto;">
          <div class="row">
            <div class="col-sm-12">
              <div class="card shadow" style="margin-top: -10px;">
                <div class="card-body">
                  <h1 class="text-center">APERTURAR CAJA</h1>
                  <div class="col-md-12" style="margin-bottom: 10px;">
                    <div class="scrollmenu" id="cardCategorias" style="background-color: transparent;">
                    </div>
                  </div>
                  <form action="" id="form-apertura-caja">
                    <div class="col-md-12 md-1">
                      <div class="form-group">
                        <label for="">Caja</label>
                        <select class="form-control" name="caja_apertura" id="input-caja" required>

                        </select>
                      </div>
                    </div>
                    <div class="col-md-12 md-1">
                      <div class="form-group">
                        <label for="">Efectivo</label>
                        <input step="0.001" type="number" class="form-control" name="efectivo_apertura" id="efectivo_apertura" required>
                      </div>
                    </div>
                    <div class="col-md-12 md-1 text-center">
                      <button type="submit" class="btn btn-success">Aperturar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


    </div><!-- /.container-fluid -->
  </section>


  <!-- /.modal -->
</div>



<div class="modal fade" id="myModal2">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Lista de ventas</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">

          <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">

          </div>

          <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">

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
                <option value="Nota Credito">Nota de Crédito</option>
                <option value="Rechazado">Rechazado</option>
              </select>
            </div>
          </div>

        </div>
        <div class="tale-resposive">
          <table id="tbllistado" class="table table-striped">
            <thead>
              <th>ID</th>
              <th>Cliente / N° Documento</th>
              <th>Sucursal</th>
              <th>Número</th>
              <th>Total Venta</th>
              <th>Tipo Pago</th>
              <th>Estado</th>
              <th width="70px;">Sunat</th>
              <th style="text-align: center;"><i class="fa fa-shield" aria-hidden="true" title="Comprobar estado"></i></th>
              <th width="180px;">Acciones</th>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>Sucursal</th>
              <th>Número</th>
              <th>Total Venta</th>
              <th>Tipo Pago</th>
              <th>Estado</th>
              <th>Sunat</th>
              <th></th>
              <th>Acciones</th>
            </tfoot>
          </table>
        </div>

      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h4 class="modal-title"><i class="fas fa-cash-register"></i> Caja Chica - Movimiento</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form class="form-horizontal" role="form" name="formularioMovimiento2" id="formularioMovimiento2" method="POST">
                <input type="hidden" name="idmovimiento" id="idmovimiento">

                <div class="modal-body" style="background-color: #f8f9fa; border-radius: 10px;">
                    <!-- Fila de selección de tipo de movimiento (Ingresos/Egresos) -->
                    <div class="row text-center">
                        <div class="form-group col-6">
                            <div class="col-sm-12 text-danger" style="text-align: center;">
                                <input type="radio" id="egresos" name="opcionEI" value="Egresos" checked="" onchange="verificarConceptoMovimiento()">
                                <label for="male">Egresos (-)</label>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <div class="col-sm-12 text-success" style="text-align: center;">
                                <input type="radio" id="ingresos" name="opcionEI" value="Ingresos" onchange="verificarConceptoMovimiento()">
                                <label for="male">Ingresos (+)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Selección de almacén y personal -->
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="name" class="control-label">Almacen <span class="text-danger">*</span></label>
                            <select id="idsucursal02" name="idsucursal02" class="form-control select2" data-live-search="true">
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Concepto movimiento <span class="text-danger">*</span></label>
                            <select id="idconcepto_movimiento" name="idconcepto_movimiento" class="form-control" data-live-search="true" required>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="col-form-label">
                                <i class="fas fa-users fs-6"></i>
                                <span class="small">Personal</span>
                            </label>
                            <select id="idpersonal02" name="idpersonal02" class="form-control select2"></select>
                        </div>
                    </div>

                    <!-- Detalles de pago y monto -->
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="formapago" class="col-form-label">Forma de pago:</label>
                            <select id="formapago" name="formapago" class="form-control" required>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia bancaria</option>
                                <option value="Tarjeta">Tarjeta POS</option>
                                <option value="Deposito">Depósito</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="totaldeposito" class="col-form-label">Total Monto tarjeta S/.</label>
                            <input style="text-align:center; background-color:#E1FEFF; border-color: #38F0F9; border-radius:10px;" 
                                   type="text" class="form-control" id="totaldeposito" name="totaldeposito" value="0" readonly>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="noperacion" class="col-form-label"># Operación:</label>
                            <input style="text-align:center; background-color:#E1FEFF; border-color: #38F0F9; border-radius:10px;" 
                                   type="text" class="form-control" name="noperacion" id="noperacion" maxlength="7" value="0" readonly>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="montoPagar" class="col-form-label">Monto:</label>
                            <input type="number" step="any" class="form-control" id="montoPagar" name="montoPagar" required>
                        </div>
                    </div>

                    <!-- Descripción del movimiento -->
                    <div class="form-group">
                        <label for="descripcion" class="col-form-label">Descripción:</label>
                        <input type="text" class="form-control" name="descripcion" id="descripcion" placeholder="Descripción del movimiento (opcional)">
                    </div>
                </div>

                <!-- Footer con botones -->
                <div class="modal-footer" style="background-color: #f1f1f1;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
                    <button class="btn btn-success" type="submit" id="btnGuardar"><i class="fas fa-save"></i> Guardar Movimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalPrecios">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Lista de precios</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="limpiarCliente()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <div class="table-responsive" id="tabla-precios">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
  </div>
</div>


<div class="modal fade" id="ModalClientes">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cliente</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="limpiarCliente()">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formularioClientes" id="formularioClientes" method="POST">
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Nombre:</label>
                <input type="hidden" name="idpersona" id="idpersona">
                <input type="hidden" name="tipo_persona" id="tipo_persona" value="Cliente">
                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100" placeholder="Nombre del proveedor" required>
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
                <input type="text" class="form-control" name="num_documento" id="num_documento" maxlength="20" placeholder="Documento">
                <div class="input-group-append">
                  <span class="input-group-text" style="cursor: pointer;" id="Buscar_Cliente" onclick="BuscarCliente()" title="Buscar Cliente" type="button"><i class="fa fa-search"></i></span>
                  <span class="input-group-text" id="cargando" title="Cargando" type="button" style="display: none;"><i><img src="files/plantilla/cargando.gif" width="15px"></i></span>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Dirección:</label>
                <input type="text" class="form-control" name="direccion" id="direccion" maxlength="70" placeholder="Dirección">
                Estado:<label for="" id="estado2">-</label>
                Condición:<label for="" id="condicion">-</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Teléfono:</label>
                <input type="text" class="form-control" name="telefono" id="telefono" maxlength="20" placeholder="Teléfono">
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Email:</label>
                <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="Email">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" onclick="limpiarCliente()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>




<div class="modal fade" id="ModalTipocomprobante">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">IMPRIMIR COMPROBANTE</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row" id="pant-imprimir">

        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary pull-right" type="button" data-dismiss="modal">SIN COMPROBANTE</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>


<script src="vistas/js/pos.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- JS de Toastr y jQuery (requerido por Toastr) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://unpkg.com/quagga/dist/quagga.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4.1.3/dist/tesseract.min.js"></script>
