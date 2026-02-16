<!-- Content Wrapper. Contains page content -->
<style>
  #tbllistado {
    width: 100%;
    font-size: 12px;
  }

  #myModal {
    width: 100%;
    font-size: 12px;
  }

  /* Estilo base para las imágenes */
  .img-container {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #ccc;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    transition: transform 0.3s ease;
    /* Transición para el movimiento */
  }

  /* Efecto cuando el mouse pasa sobre la imagen */
  .img-container:hover {
    transform: scale(1.1) translateY(-5px);
    /* Agranda y mueve la imagen ligeramente hacia arriba */
  }

  /* Asegura que la imagen se ajuste correctamente dentro del contenedor */
  .img-container img {
    max-width: 100%;
    max-height: 100%;
    transition: transform 0.3s ease;
  }

  /* Estilo inicial para la imagen */
  .imagen-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  /* Estilo aplicado cuando el cursor pasa sobre la imagen */
  .imagen-hover:hover {
    transform: scale(1.05);
    /* Aumenta ligeramente el tamaño de la imagen */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    /* Agrega una sombra sutil */
  }

  .custom-button {
    background-color: blue;
    /* Fondo azul */
    color: white;
    /* Texto blanco */
    transition: background-color 0.3s, color 0.3s;
    /* Transición de 0.3 segundos para suavizar el cambio de colores */
  }

  .custom-button:hover {
    background-color: lightblue;
    /* Nuevo fondo azul al pasar el mouse */
    color: #fff;
    /* Nuevo color de texto blanco al pasar el mouse */
  }

  #print {
    display: none;
    /* Inicialmente, el div está oculto */
    /* Otros estilos que desees aplicar al div */
  }

  .icon-label {
    margin-right: 8px;
    /* Ajusta el valor según lo necesites */
  }
  #contenedor-imagenes img {
  width: 100%;
  max-width: 150px;
}
/* Filas más delgadas y compactas */
  #tbllistado th,
  #tbllistado td {
    padding: 0.45rem 0.5rem; /* Reduce altura de fila */
    vertical-align: middle;
    font-size: 0.875rem; /* Tamaño de letra más pequeño */
  }

  /* Encabezado más destacado */
  #tbllistado thead {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
  }

  /* Hover más elegante */
  #tbllistado tbody tr:hover {
    background-color: #e9ecef;
    transition: 0.2s;
  }

  /* Botones dentro de la tabla (acciones) */
  #tbllistado .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
  }

  /* Scroll horizontal suave */
  table.dataTable.nowrap {
    white-space: nowrap;
  }

  /* Mejor alineación de números */
  #tbllistado td:nth-child(5), /* Stock */
  #tbllistado td:nth-child(6), /* P. Venta */
  #tbllistado td:nth-child(7)  /* P. Compra */ {
    text-align: right;
  }
  /* ====== Mejora visual sin romper nada ====== */
  .content-header h1 { font-weight: 700; }
  .card { border-radius: .6rem; }
  .table thead.sticky-head th {
    position: sticky; top: 0; z-index: 1;
    box-shadow: inset 0 -1px 0 rgba(0,0,0,.05);
  }
  .table td, .table th { vertical-align: middle; }
  .dataTables_wrapper .dt-buttons .btn { margin-right: .35rem; border-radius: .5rem; }
  .btn-excel { background-color: #28a745 !important; color: #fff !important; border: none; }
  .btn-pdf   { background-color: #dc3545 !important; color: #fff !important; border: none; }
  .btn-colvis{ background-color: #007bff !important; color: #fff !important; border: none; }
  .select2-container .select2-selection--single { height: 38px; }
  .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; }
  .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px; }
  /* Pequeño gap para los botones de acción */
  .gap-8 > * + * { margin-left: .5rem; }
  
  .precio-chip {
  display: inline-block;
  background-color: #f1f1f1;
  border-radius: 25px;
  padding: 6px 14px;
  margin: 4px;
  cursor: pointer;
  transition: all 0.2s ease;
  user-select: none;
}

.precio-chip input[type="checkbox"] {
  display: none;
}

.precio-chip.active {
  background-color: #007bff;
  color: #fff;
  font-weight: 600;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}
/* Agregar al archivo CSS */
.lote-activo {
    background-color: #d1ecf1 !important;
    border-left: 4px solid #0c5460 !important;
    font-weight: 500;
}

.badge-lote-activo {
    background-color: #007bff;
    color: white;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 11px;
    margin-left: 5px;
}
</style>
<script>
  document.getElementById("barcode").addEventListener("click", function() {
    var printDiv = document.getElementById("print");

    // Verifica si el div está actualmente visible
    if (window.getComputedStyle(printDiv).display === "none") {
      // Si está oculto, lo muestra
      printDiv.style.display = "block";
    } else {
      // Si está visible, lo oculta
      printDiv.style.display = "none";
    }
  });
</script>
<?php
function tienePermiso($modulo, $submodulo, $accion) {
    return isset($_SESSION['acciones'][$modulo][$submodulo][$accion]) && $_SESSION['acciones'][$modulo][$submodulo][$accion] === true;
}
?>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid" style="margin-top: -10px;">
      <div class="row ">
        <div class="col-sm-6">
          <h1 style="font-size: 15px;">Producto</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Producto</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content" style="margin-top: -15px;">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"> </h3>

              <div class="row">
                <?php if (tienePermiso('Almacen', 'Productos', 'Agregar Productos')) { ?>
                  <div class="col-md-1">
                    <button type="button" class="btn btn-primary btn-block float-right btn-xs" onclick="nuevo()"><i class="fa fa-plus"></i> Nuevo</button>
                  </div>
                <?php } ?>

                <?php if (tienePermiso('Almacen', 'Productos', 'Catalago')) { ?>
                  <div class="col-md-1">
                    <button type="button" class="btn btn-success btn-block float-right btn-xs" data-toggle="modal" data-target="#modalCatalogoConfig">
                      <i class="fas fa-file"></i> Catálogo
                    </button>
                  </div>
                <?php } ?>


                <?php if (tienePermiso('Almacen', 'Productos', 'Traslados')) { ?>
                <div class="col-md-1">
                  <button type="button" class="btn btn-success btn-block btn-xs" data-toggle="modal" data-target="#myModalTraslados"><i class="fas fa-file"></i> Traslados</button>
                </div>
                <?php } ?>

                <?php if (tienePermiso('Almacen', 'Productos', 'Empaque')) { ?>
                <div class="col-md-1">
                  <button type="button" class="btn btn-warning btn-block btn-xs" data-toggle="modal" data-target="#myModalDesempaquetar" onclick="llenarProductos()"><i class="fas fa-file"></i> Empaque</button>
                </div>
                <?php } ?>

                <?php if (tienePermiso('Almacen', 'Productos', 'InversionPP')) { ?>
                <div class="col-md-1">
                  <a href="reportes/rptproductoscompra.php" target="_blank"><button type="button" class="btn btn-info btn-block btn-xs"><i class="fas fa-file"></i> Inversión</button></a>
                </div>
                <?php } ?>

                <div class="col-md-3">
                  <!--<label>Almacén:</label>-->
                  <select id="idsucursal2" name="idsucursal2" class="form-control select2" style="width: 100%; height: 100%;">
                  </select>
                </div>
                <?php if (tienePermiso('Almacen', 'Productos', 'Filtrar Stock')) { ?>
                <div class="form-group">
                  <input type="number" class="form-control" id="stock_filtro" placeholder="Filtrar stock..." min="0" style="width: 200px;" />
                </div>
                <?php } ?>

                <?php if (tienePermiso('Almacen', 'Productos', 'Consultar Producto Sucursal')) { ?>
                  <div class="col-md-2">
                    <button class="btn btn-info btn-block btn-xs" onclick="abrirModalStockSucursales()">
                      <i class="fas fa-search-location"></i> Ver en otras sucursales
                    </button>
                  </div>
                <?php } ?>

              </div>

            </div>
            <!-- /.card-header -->
            <div class="card-body" style="margin-top: -15px;">



              <!-- Tabla HTML -->
              <table id="tbllistado" class="table table-tailpanel dt-responsive" style="width:100%">
                <thead class="thead-light">
                  <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                    <th>Código</th>
                    <th>Stock</th>
                    <th>P. Venta</th>
                    <th>P. Compra</th>
                    <!-- <th>Imagen</th> -->
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                    <th>Código</th>
                    <th>Stock</th>
                    <th>P. Venta</th>
                    <th>P. Compra</th>
                    <!-- <th>Imagen</th> -->
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


<!-- Modal para configurar imágenes del catálogo -->
<div class="modal fade" id="modalCatalogoConfig" tabindex="-1" role="dialog" aria-labelledby="modalCatalogoLabel">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formCatalogoConfig" method="POST" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Subir Imágenes para su presentación Catálogo</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="categoriaCatalogo">Categoría:</label>
                <select id="categoriaCatalogo" class="form-control">
                  <option value="0">Todas las categorías</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="preciosCatalogo">Seleccionar precios a mostrar:</label>
                <div id="contenedor-precios" class="border rounded p-2" style="max-height: 180px; overflow-y: auto;">
                  <!-- Checkboxes cargados dinámicamente -->
                </div>
              </div>
            </div>

          </div>
          
          <div class="mb-3">
            <button type="button" class="btn btn-primary" id="btnAgregarImagen">
              <i class="fas fa-plus"></i> Agregar otra imagen
            </button>
          </div>
          <div id="contenedor-imagenes" class="row"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="btnGenerarCatalogo">
            <i class="fas fa-check-circle"></i> Generar Catálogo
          </button>
        </div>

      </div>
    </form>
  </div>
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
                    <th style="width: 10%;">Mas precios</th>
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

<div class="modal fade" id="ModalPreciosProducto">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Configuración de precios</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" name="savePrecios" id="savePrecios">
        <div class="modal-body">
          <input type="hidden" id="idproductoPrecio" name="idproductoPrecio">
          <div class="col-md-12 d-flex justify-content-end mb-3">
            <button class="btn btn-primary" type="button" id="nuevo_precio">NUEVO</button>
          </div>
          <div id="precios" class="row" style="max-height: 400px; overflow-y: auto">
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">CERRAR</button>
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

<div class="modal fade modal-tailpanel" id="myModal">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content modal-content-tailpanel">
      <div class="modal-header modal-header-tailpanel">
        <h4 class="modal-title">
          <i class="fas fa-box-open text-primary"></i> Producto
        </h4>
        <button type="button" class="close close-tailpanel" data-dismiss="modal">
          ×
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
        <div class="modal-body">
          <div class="row" style="margin-top:-15px">
            <div class="col-sm-8" style="margin-top:-15px">
              <div class="row">
                <div class="col-sm-10">
                  <div class="form-group">
                    <label for="name" class="control-label">Nombre:</label>
                    <input type="hidden" name="idproducto" id="idproducto">
                    <input type="text" class="form-control" name="nombre" id="nombre" maxlength="250" placeholder="Nombre" required>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="name" class="control-label">Stock:</label>

                    <input type="number" class="form-control" step="any" name="stock" id="stock" value="0" readonly>

                  </div>
                </div>
              </div>

              <div class="row" style="margin-top:-15px">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="name" class="control-label">Descripción:</label>
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

                <div class="col-sm-5">
                  <div class="form-group">
                    <label for="name" class="control-label"><i class="fas fa-users fs-6"></i>
                      <span class="control-label">Unidad de Medidad: </span><a class="input-group-addon" style="cursor: pointer;color: blue;" data-toggle="modal" data-target="#ModalUM"> <i class="fa fa-plus fa-xs"></i> Nuevo</a></label>
                    <select id="idunidad_medida" name="idunidad_medida" class="form-control select2" style="width: 100%; height: 100%;" required></select>
                  </div>
                </div>

                <div class="col-sm-5">
                  <div class="form-group">
                    <label for="name" class="control-label"><i class="fas fa-users fs-6"></i>
                      <span class="control-label">Categoria: </span><a class="input-group-addon" style="cursor: pointer; color: blue;" data-toggle="modal" data-target="#myModalCategoria"><i class="fa fa-plus fa-xs"></i> Nueva Categoría</a></label>
                    <select id="idcategoria" name="idcategoria" class="form-control select2" style="width: 100%; height: 100%;" required>
                      <option value="" selected></option>
                    </select>
                  </div>
                </div>

                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="name" class="control-label">Stock Mínimo:</label>

                    <input type="number" class="form-control" name="stockMinimo" id="stockMinimo" value="5" required>

                  </div>
                </div>
              </div>





              <div class="row" style="margin-top:-15px">


                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="name" class="control-label">Precio de Compra:</label>
                    <input type="number" step="any" class="form-control" name="precioCompra" id="precioCompra" min="0" placeholder="S/ 0.00">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="name" class="control-label">Proveedor: </label>
                    <input type="text" class="form-control" name="fabricante" id="fabricante" maxlength="256" placeholder="Fabricante">
                  </div>
                </div>

                <div class="col-sm-3" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">Rubro:</label>
                    <select id="idrubro" name="idrubro" class="form-control select2" style="width: 100%; height: 100%;"></select>
                  </div>
                </div>

                <div class="col-sm-4">
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
                    <label for="name" class="control-label">R Sanitario: </label>
                    <input type="text" class="form-control" name="registrosan" id="registrosan" maxlength="256" placeholder="N° de R Sanitario">
                  </div>
                </div>
              </div>

              <div class="row" style="margin-top:-15px">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="name" class="control-label">Precio de Venta:</label>
                    <input type="number" step="any" class="form-control" name="precio" id="precio" min="0" placeholder="S/ 0.00">
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="name" class="control-label">Utilidad PVP:</label>
                    <input style="border-color: red; " type="number" step="any" class="form-control" name="utilprecio" id="utilprecio" readonly>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label for="name" class="control-label">Marg.P.PUBLICO %:</label>
                    <input style="border-color: green; " type="number" step="any" class="form-control" name="margenpubl" id="margenpubl" readonly>
                  </div>
                </div>
                <div class="col-sm-2" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">P.DESCUENTO:</label>
                    <input type="number" step="any" class="form-control" name="precioB" id="precioB" placeholder="S/ 0.00">
                  </div>
                </div>
                <div class="col-sm-2" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">PMAY 1:</label>
                    <input type="number" step="any" class="form-control" name="precioC" id="precioC" placeholder="S/ 0.00">
                  </div>
                </div>
                <div class="col-sm-2" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">PMAY 2:</label>
                    <input type="number" step="any" class="form-control" name="precioD" id="precioD" placeholder="S/ 0.00">
                  </div>
                </div>
                <div class="col-sm-3" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">P.DISTRIBUIDOR:</label>
                    <input type="number" step="any" class="form-control" name="precioE" id="precioE" placeholder="S/ 0.00">
                  </div>
                </div>
              </div>

              <div class="row" style="margin-top:-15px" hidden>

                <div class="col-sm-2" >
                  <div class="form-group">
                    <label for="name" class="control-label">Marg.Desc %:</label>
                    <input style="border-color: green; " type="number" step="any" class="form-control" name="margendes" id="margendes" readonly>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="name" class="control-label">Marg.P1 %:</label>
                    <input style="border-color: green; " type="number" step="any" class="form-control" name="margenp1" id="margenp1" readonly>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="name" class="control-label">Marg.P2 %:</label>
                    <input style="border-color: green; " type="number" step="any" class="form-control" name="margenp2" id="margenp2" readonly>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="name" class="control-label">Marg.Dist %:</label>
                    <input style="border-color: green; " type="number" step="any" class="form-control" name="margendist" id="margendist" readonly>
                  </div>
                </div>
              </div>
              <div class="row" style="margin-top:-15px" hidden>
                
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="name" class="control-label">Utilidad Desc:</label>
                    <input style="border-color: red; " type="number" step="any" class="form-control" name="utilprecioB" id="utilprecioB" readonly>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="name" class="control-label">Utilidad P1:</label>
                    <input style="border-color: red; " type="number" step="any" class="form-control" name="utilprecioC" id="utilprecioC" readonly>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                    <label for="name" class="control-label">Utilidad P2:</label>
                    <input style="border-color: red;" type="number" step="any" class="form-control" name="utilprecioD" id="utilprecioD" readonly>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-group">
                    <label for="name" class="control-label">Utilidad Dist:</label>
                    <input style="border-color: red;" type="number" step="any" class="form-control" name="utilprecioE" id="utilprecioE" readonly>
                  </div>
                </div>
              </div>



              <div class="row" style="margin-top:-15px">
                <div class="col-sm-3" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">Comisión Vendedor:</label>
                    <input type="number" step="any" class="form-control" name="comisionV" id="comisionV">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-4" hidden>
                  <div class="form-group">
                    <label for="name" class="control-label">Condicion de Venta:</label>
                    <select id="idcondicionventa" name="idcondicionventa" class="form-control select2" style="width: 100%; height: 100%;"></select>
                  </div>
                </div>


                <div class="col-sm-6">
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
                    <input type="file" class="form-control d-none" name="imagen" id="imagen">
                    <input type="hidden" name="imagenactual" id="imagenactual">
                    <img src="" class="img-thumbnail imagen-hover" id="imagenmuestra" width="400px" style="cursor: pointer;" onclick="document.getElementById('imagen').click();">
                    <button type="button" class="btn btn-danger mt-2" id="restaurarImagen">Eliminar Imagen</button>
                  </div>
                </div>
              </div>


              <div class="row" style="margin-top:-15px">
                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="codigo" class="control-label">Código de barras - unidad:</label>
                    <div class="input-group">
                      <input type="text" class="form-control" name="codigo" id="codigo" placeholder="Código Barras" oninput="generarbarcode()">
                      <div class="input-group-append">
                        <button class="btn btn-info" type="button" onclick="imprimir()" title="Imprimir código de barras">
                          <i class="fa fa-print"></i>
                        </button>
                      </div>
                    </div>
                    <div class="form-check form-switch mt-2">
                      <input class="form-check-input" type="checkbox" id="modoCodigo">
                      <label class="form-check-label" for="modoCodigo">
                        <i class="fa fa-edit text-success"></i> Manual
                      </label>
                    </div>

                    <div id="print" hidden>
                      <svg id="barcode" width="100" height="50"></svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="modal-footer modal-footer-tailpanel">
          <button type="button" onclick="cancelarform()" class="btn btn-outline-danger" data-dismiss="modal">
            Cerrar
          </button>
          <button class="btn btn-primary btn-save-tailpanel" type="submit" id="btnGuardar">
            <i class="fas fa-save"></i> Guardar
          </button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<!-- Modal -->
<div class="modal fade" id="myModalCategoria" tabindex="-1" role="dialog">

  <div class="modal-dialog" style="width: 480px">

    <div class="modal-content">
      <!-- form -->
      <form class="form-horizontal" role="form" name="formularioCategoria" id="formularioCategoria" method="POST">

        <div class="modal-header">
          <h4 class="modal-title">Categoría</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Nombre:</label>
            <div class="col-sm-12">
              <input type="hidden" name="idcategoria" id="idcategoria">
              <input type="text" class="form-control" name="nombre" id="nombre" maxlength="50" placeholder="Nombre" required>
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarformcat()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Fin modal -->>

<!-- Modal unidad de medida -->
<div class="modal fade" id="ModalUM" tabindex="-1" role="dialog">

  <div class="modal-dialog" style="width: 480px">

    <div class="modal-content">
      <!-- form -->
      <form class="form-horizontal" role="form" name="formularioUM" id="formularioUM" method="POST">

        <div class="modal-header">
          <h4 class="modal-title">Unidad de Medida</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Nombre:</label>
            <div class="col-sm-12">
              <input type="hidden" name="idunidad_medida" id="idunidad_medida">
              <input type="text" class="form-control" name="nombre" id="nombre" maxlength="50" placeholder="Nombre" required>
            </div>
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

<div class="modal fade" id="myModalTraslados">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">TRASLADAR PRODUCTOS</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formularioTraslados" id="formularioTraslados" method="POST">
        <div class="modal-body">
          <div class="alert" style="background: #E0F7FA;">
            <strong><i class="fa fa-info"></i> Info!</strong> TRASLADAR: <label for="documento" id="documento"></label> Para hacer uso de este módulo, debe tener en claro el producto a TRASLADAR a un almacén específico.</i></a>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Almacén Origen:</label>
                <select id="idsucursal3" name="idsucursal3" class="form-control select2" data-live-search="true" onchange="cargarComboProductos();">
                </select>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Almacén Destino:</label>
                <select id="idsucursal4" name="idsucursal4" class="form-control select2" data-live-search="true" onchange="cargarComboProductos2();">
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Producto:</label>
                <select id="idproducto2" name="idproducto2" class="form-control select2" data-live-search="true">
                </select>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Producto:</label>
                <select id="idproducto3" name="idproducto3" class="form-control select2" data-live-search="true">
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">¿Cantidad de Productos a Trasladar?</label>
                <input type="text" class="form-control" name="cantidadT" id="cantidadT" placeholder="Cantidad" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" onclick="limpiarTraslado()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div id="fechavencimiento-modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-xl modal-dialog-scrollable"> <!-- scroll vertical si hay muchas filas -->
    <div class="modal-content panel panel-primary">

      <div class="modal-header panel-heading">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <div class="modal-title text-center w-100"></div>
      </div>

      <div class="modal-body panel-body">
        <div class="form-group col-lg-12 col-md-12 col-xs-12">
          <table id="tbllistadoKardex" class="table table-tailpanel dt-responsive" 
                 cellpadding="0" cellspacing="0" aria-describedby="tblIngresos_info" 
                 width="100%" role="grid" style="width: 100%;">
            <thead>
              <tr>
                <th>#</th>
                <th>Fecha Ingreso</th>
                <th>Fecha Vencimiento</th>
                <th>Días Restantes</th>
                <th>Cant. Comprada</th>
                <th>Stock Lote</th>
                <th>N° Lote</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
              </tr>
            </thead>
            <tbody id="dataVencimiento"></tbody>
            <tfoot>
              <tr>
                <th>#</th>
                <th>Fecha Ingreso</th>
                <th>Fecha Vencimiento</th>
                <th>Días Restantes</th>
                <th>Cant. Comprada</th>
                <th>Stock Lote</th>
                <th>N° Lote</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
              </tr>
            </tfoot>
          </table>
          <br>
          <table class="table table-striped table-bordered table-condensed table-hover dataTable">
            <tbody>
              <tr>
                <td colspan="2" style="float: right;">
                  <h4>Total de productos:</h4>
                </td>
                <td>
                  <h4 id="totareal"></h4>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer panel-footer">
        <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">
          <i class="fa fa-times"></i> Cancelar
        </button>
      </div>

    </div>
  </div>
</div>



<!-- Modal Detalle Producto -->
<div class="modal fade" id="modalDetalleProducto" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalLabel">Detalle del Producto</h5>
        <button type="button" class="btn btn-sm btn-danger ml-2" id="btnCerrarModalProducto">
          Cerrar
        </button>
      </div>

      <div class="modal-body">
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="detalleProductoTabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="tab-imagen-tab" data-toggle="tab" href="#tab-imagen" role="tab" aria-controls="tab-imagen" aria-selected="true">
              Imagen
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="tab-detalles-tab" data-toggle="tab" href="#tab-detalles" role="tab" aria-controls="tab-detalles" aria-selected="false">
              Detalles del producto
            </a>
          </li>
        </ul>

        <!-- Contenido de las tabs -->
        <div class="tab-content" id="detalleProductoTabsContent">
          <!-- TAB 1: Imagen -->
          <div class="tab-pane fade show active text-center" id="tab-imagen" role="tabpanel" aria-labelledby="tab-imagen-tab">
            <div class="d-flex justify-content-center align-items-center border rounded shadow mb-3" style="height: 600px; background-color: #f8f9fa;">
              <img id="detalleImagenProducto" src="" alt="Producto" style="max-height: 100%; max-width: 100%; object-fit: contain;">
            </div>
          </div>

          <!-- TAB 2: Detalles -->
          <div class="tab-pane fade" id="tab-detalles" role="tabpanel" aria-labelledby="tab-detalles-tab">
            <div class="row" id="detalleProductoContenido">
              <!-- Contenido dinámico generado por JS -->
            </div>
            <div class="row mt-3">
              <div class="col-md-12">
                <h5 class="text-primary">Configuraciones del producto</h5>
                <div class="accordion" id="acordeonConfiguraciones">
                  <!-- Aquí irá el contenido generado por AJAX -->
                  <div id="detallePreciosAdicionales">
                    <i>Cargando...</i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- /.modal-body -->
    </div>
  </div>
</div>

<div class="modal fade" id="myModalDesempaquetar">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">DESEMPAQUETAR PRODUCTOS</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formularioDesempaquetar" id="formularioDesempaquetar" method="POST">
        <div class="modal-body">
          <div class="alert" style="background: #E0F7FA;">
            <strong><i class="fa fa-info"></i> Info!</strong> DESEMPAQUETAR: <label for="documento" id="documento"></label> Para hacer uso de este módulo <label for="deudaTotal" id="deutaTotal"></label>, debe tener en claro el producto empaquetado y el producto al cual se le va a asignar lo desempaquetado.</i></a>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Producto a Desempaquetar:</label>
                <select id="idproductoE" name="idproductoE" class="form-control select2" data-live-search="true" title="Seleccione Producto" onchange="stockProductoE()" required></select>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Producto Asignado:</label>
                <select id="idproductoD" name="idproductoD" class="form-control select2" data-live-search="true" title="Seleccione Producto" onchange="stockProductoD()" required></select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Cantidad a Desempaquetar:</label>
                <input type="hidden" name="productoE" id="productoE">
                El Producto tiene <label id="productoDesempaquetar" name="productoDesempaquetar">0</label>
                <input type="text" class="form-control" name="cantidadE" id="cantidadE" placeholder="Cantidad" required>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">¿Cuántos Productos Contiene?</label>
                <input type="hidden" name="productoD" id="productoD">
                <input type="text" class="form-control" name="cantidadD" id="cantidadD" placeholder="Cantidad" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" onclick="limpiarDesempaquetado()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="myModalEntradas">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">INGRESAR PRODUCTOS</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formularioIngreso" id="formularioIngreso" method="POST">
        <div class="modal-body">
          <div class="row">
            <input type="hidden" class="form-control" name="idproducto" id="input-idproducto" placeholder="Cantidad" required>

            <input type="hidden" class="form-control" name="idsucursal" id="input-idsucursal" placeholder="Cantidad" required>
            <div class="col-sm-12">
              <div class="form-group">
                <label>Lote:</label>
                <select class="form-control" name="idfifo" id="idfifo" required></select>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Tipo de movimiento:</label>
                <select class="form-control" name="tipo_movimiento" id="tipo_movimiento" required>
                  <option value="" hidden selected>Seleccionar...</option>
                  <option value="0">Entrada</option>
                  <option value="1">Salida</option>
                </select>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label for="name" class="control-label">Cantidad:</label>
                <input type="number" step="0.001" class="form-control" name="cantidad" id="cantidad" placeholder="Cantidad" required>
              </div>
            </div>
            <div class="col-sm-6 precio-box" style="display: none;">
              <div class="form-group">
                <label>Precio de Venta:</label>
                <input type="number" step="0.01" class="form-control" name="precio_venta" id="precio_venta">
              </div>
            </div>
            <div class="col-sm-6 precio-box" style="display: none;">
              <div class="form-group">
                <label>Precio de Compra:</label>
                <input type="number" step="0.01" class="form-control" name="precio_compra" id="precio_compra">
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group">
                <label for="name" class="control-label">Motivo:</label>
                <textarea class="form-control" id="" name="motivo" cols="30" rows="3">

                </textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" onclick="limpiarIngreso()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<!-- Modal para consultar stock en otras sucursales -->
<div class="modal fade" id="modalStockSucursales" tabindex="-1" role="dialog" aria-labelledby="tituloStockSucursales" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="tituloStockSucursales">
          <i class="fas fa-store-alt"></i> Consultar stock por producto y sucursal
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="form-group">
          <label for="sucursalFiltro"><i class="fas fa-store"></i> Filtrar por sucursal:</label>
          <select id="sucursalFiltro" class="form-control">
            <option value="">-- Seleccione sucursal --</option>
          </select>
        </div>

        <div class="form-group">
          <label for="buscarProducto"><i class="fas fa-search"></i> Buscar producto:</label>
          <input type="text" class="form-control" id="buscarProducto" placeholder="Ingrese nombre o código del producto">
        </div>

        <hr>
        <div class="table-responsive">
          <table id="tablaStockSucursales" class="table table-bordered table-striped">
            <thead class="bg-light">
              <tr>
                <th></th> <!-- checkbox -->
                <th>Producto</th>
                <th>Código</th>
                <th>Sucursal</th>
                <th>Stock disponible</th>
                <th>Cantidad a solicitar</th> 
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="5" class="text-center">Ingrese un producto para ver el stock.</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btnGenerarSolicitudDesdeStock">
          <i class="fa fa-paper-plane"></i> Generar Solicitud
        </button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times"></i> Cerrar
        </button>
      </div>

    </div>
  </div>
</div>



<script src="vistas/js/producto.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- JS de Toastr y jQuery (requerido por Toastr) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>