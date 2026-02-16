<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Servicio</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Servicio</li>
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
                  <button type="button" class="btn btn-outline-primary btn-block btn-xs" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Nuevo</button>
                </div>
                <div class="col-md-1">
                  <a href="reportes/rptservicios.php" target="_blank"><button type="button" class="btn btn-outline-danger btn-block btn-xs" data-toggle="modal" data-target="#myModal"><i class="fas fa-file"></i> Reporte</button></a>
                </div>
                 <div class="col-md-3">
                  <!--<label>Almacén:</label>-->
                  <select id="idsucursal2" name="idsucursal2" class="form-control select2" style="width: 100%; height: 100%;">
                  </select>
                </div>
              </div>
              
            </div>
            <!-- /.card-header -->
            <div class="card-body">

              <table id="tbllistado" class="table table-striped">
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Cantidad</th>
                    <th>P. Venta</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                  <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Cantidad</th>
                    <th>P. Venta</th>
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

<div class="modal fade" id="ModalConfigProducto">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cofiguración de producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" name="saveCofigurtion" id="saveCofigurtion">
        <div class="modal-body">
          <div class="row">
            <input type="hidden" id="idproductoconfig" name="idproductoconfig">
            <div class="col-md-2"><strong>
                <i class="fa fa-product"></i> PRODUCTO:</strong></div>
            <div class="col-md-5">
              <P id="p-producto"></P>
            </div>
            <div class="col-md-2"><strong>
                <i class="fa fa-money"></i> Precio unitario:</strong></div>
            <div class="col-md-3">
              <P id="p-unitario"></P>
            </div>
            <div class="col-md-12 mb-1">
            </div>
            <div class="col-md-8">
              <label>Detalles de cofigurción</label>
            </div>
            <div class="col-md-4" style="text-align: right;"><button type="button" class="btn btn-success" onclick="agregarCofiguracion()">NUEVO</button>
              <button type="button" class="btn btn-info" onclick="imprimirCodigosBarras()">IMPRIMIR COD. BARRAS</button>
            </div>
            <div class="col-md-12 mb-1">
            </div>
            <div class="col-md-12 table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th style="width: 30%;">Codigo de barras</th>
                    <th style="width: 30%;">Cotenedor</th>
                    <th style="width: 10%;">Unidades</th>
                    <th style="width: 10%;">Precio</th>
                    <th style="width: 10%;">Pre. Promo</th>
                    <th style="width: 10%;">Acciones</th>
                  </tr>
                </thead>
                <tbody id="detalle">
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-success" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="ModalCodigosProducto">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Codigos de barra</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" name="saveCofigurtion" id="saveCofigurtion">
        <div class="modal-body">
          <div id="codigos" class=" col-md-12 row"></div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="button" id="btnGuardar" onclick="imprSelec('codigos')">IMPRIMIR</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="myModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Nuevo Servicio</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
        <div class="modal-body">
          <div class="row" style="margin-top:-15px">
            <div class="col-sm-8" style="margin-top:-15px">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="name" class="control-label">Nombre:</label>
                    <input type="hidden" name="idproducto" id="idproducto">
                    <input type="text" class="form-control" name="nombre" id="nombre" maxlength="250" placeholder="Nombre" required>
                  </div>
                </div>
                <div class="col-sm-2" >
                  <div class="form-group">
                    <label for="name" class="control-label">Stock:</label>
                    
                      <input type="number" class="form-control" step="any" name="stock" id="stock" value="0" >
                    
                    </div>
                  </div>
              </div>

              <div class="row" style="margin-top:-15px" hidden>
                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="name" class="control-label">Descripción (Principio Activo):</label>
                    <input type="text" class="form-control" name="descripcion" id="descripcion" maxlength="50" placeholder="Descripción">
                  </div>
                </div>
                <!-- <div class="col-sm-3"hidden >
                   <div class="form-group">
                    <label for="name" class="control-label">Lote: </label>
                    <input type="text" class="form-control" name="modelo" id="modelo" maxlength="256" placeholder="N° de Lote">
                  </div>
                </div>-->
              </div>

              <div class="row" style="margin-top:-15px">
                <div class="col-sm-6" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">Almacén:</label>
                    <select id="idsucursal" name="idsucursal" class="form-control select2" style="width: 100%; height: 100%;">
                    </select>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="name" class="control-label"><i class="fas fa-users fs-6"></i>
                      <span class="control-label">Unidad de Medidad: </span></label>
                    <select id="idunidad_medida" name="idunidad_medida" class="form-control select2" style="width: 100%; height: 100%;" required></select>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="name" class="control-label"><i class="fas fa-users fs-6"></i>
                      <span class="control-label">Categoria: </span></label>
                    <select id="idcategoria" name="idcategoria" class="form-control select2" style="width: 100%; height: 100%;" required><option value="" selected></option></select>
                  </div>
                </div>
                



                <div class="col-sm-2" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">Stock Mínimo:</label>

                    <input type="number" class="form-control" name="stockMinimo" id="stockMinimo" value="5" required>

                  </div>
                </div>
              </div>





              <div class="row" style="margin-top:-15px">
                <div class="col-sm-12">
                  <div class="form-group">
                      <label for="name" class="control-label">Precio de Venta:</label>
                      <input type="number" step="any" class="form-control" name="precio" id="precio" placeholder="S/ 0.00" >
                  </div>
              </div>

              <div class="col-sm-4" hidden>
                  <div class="form-group">
                      <label for="name" class="control-label">Precio de Compra:</label>
                      <input type="number" step="any" class="form-control" name="precioCompra" id="precioCompra" placeholder="S/ 0.00">
                  </div>
              </div>


                <!-- <div class="col-sm-4" >
                   <div class="form-group">
                    <label for="name" class="control-label">Condicion de Venta:</label>
                    <select id="cv" name="cv" class="form-control select2" style="width: 100%; height: 100%;" required>
                      <option>Todos</option>
                      <option>Con Receta</option>
                      <option>Con Receta médica retenida</option>
                      <option>Sin receta</option>
                    </select>
                  </div>
                 </div>-->
                <div class="col-sm-3" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">Fabricante: </label>
                    <input type="text" class="form-control" name="fabricante" id="fabricante" maxlength="256" placeholder="Fabricante">
                  </div>
                </div>

                <div class="col-sm-3" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">Rubro:</label>
                    <select id="idrubro" name="idrubro" class="form-control select2" style="width: 100%; height: 100%;" ></select>
                  </div>
                </div>

                <div class="col-sm-3" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">Tipo Igv:</label>
                    <select id="tipoigv" name="tipoigv" class="form-control" style="width: 100%; height: 100%;" required>
                      <option value="No Gravada">No Gravada</option>
                      <option value="Gravada">Gravada</option>
                    </select>
                  </div>
                </div>

                <div class="col-sm-3" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">Comisión Vendedor:</label>
                    <input type="number" step="any" class="form-control" name="comisionV" id="comisionV">
                  </div>
                </div>


                <!--<div class="col-sm-6" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">Precio de Venta (Con IGV):</label>
                    <input type="number" step="any" class="form-control" name="preciocigv" id="preciocigv" required>
                  </div>
                </div>-->
              </div>
              <div class="row" hidden>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="name" class="control-label">Precio B:</label>
                    <input type="number" step="any" class="form-control" name="precioB" id="precioB">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="name" class="control-label">Precio C:</label>
                    <input type="number" step="any" class="form-control" name="precioC" id="precioC">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="name" class="control-label">Precio D:</label>
                    <input type="number" step="any" class="form-control" name="precioD" id="precioD">
                  </div>
                </div>
              </div>
              <div class="row">

              </div>


              <div class="row">
                <!-- <div class="col-sm-3">
                  <div class="form-group" >
                    <label for="name" class="control-label">Fecha de Vencimiento:</label>
                    <input style="border-color: #99C0E7; text-align:center" class="form-control" type="date" name="fecha_hora" id="fecha_hora">
                  </div>
                </div>-->
 
                <div class="col-sm-4" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">Condicion de Venta:</label>
                    <select id="idcondicionventa" name="idcondicionventa" class="form-control select2" style="width: 100%; height: 100%;"></select>
                  </div>
                </div>

                
                <div class="col-sm-6" >
                  <div class="form-group">
                    <label for="name" class="control-label">Sucursales:</label>
                    <div class="col-sm-12">
                      <ul style="list-style: none;" id="sucursales">

                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-4" style="margin-top:-15px">
              <div class="row">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="name" class="control-label">Imagen:</label>
                    <input type="file" class="form-control" name="imagen" id="imagen">
                    <input type="hidden" name="imagenactual" id="imagenactual">
                    <img src="" class="img-thumbnail" id="imagenmuestra" width="400px">
                  </div>
                </div>

              </div>
              <div class="row" style="margin-top:-15px" hidden>
                <div class="col-sm-12">
                  <div class="form-group">
                        <label for="name" class="control-label">Código de barras - unidad:</label>
                        <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código Barras" oninput="generarbarcode()">
                        <button class="btn btn-info" type="button" onclick="imprimir()"><i class="fa fa-print"></i></button>
                        <div id="print" hidden>
                            <svg id="barcode" width="100" height="50">
                                <!-- Contenido del gráfico de barras -->
                            </svg>
                        </div>
                    </div>

                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarform()" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<script src="vistas/js/servicio.js"></script>