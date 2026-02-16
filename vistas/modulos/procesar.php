<?php
function tienePermiso($modulo, $submodulo, $accion) {
    return isset($_SESSION['acciones'][$modulo][$submodulo][$accion]) 
        && $_SESSION['acciones'][$modulo][$submodulo][$accion] === true;
}
?>
<style>
  .btn {
    transition: all 0.3s ease;
}

/* Botón principal: hover */
.btn-primary:hover {
    background-color: #0056b3; /* un azul más intenso */
    box-shadow: 0 4px 12px rgba(0,0,0,0.15); /* sombra ligera al pasar el mouse */
    transform: translateY(-2px);
}

/* Botón secundario: hover */
.btn-outline-danger:hover {
    background-color: #dc3545; /* rojo fuerte */
    color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

/* Sombra ligera por defecto */
.shadow-sm {
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.custom-file-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
    height: 50px;
    background: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
}

/* Input file real */
.custom-file-wrapper input[type="file"] {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

/* Botón dentro del input */
.custom-file-button {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 20px;
    height: 100%;
    background: #0d6efd;
    color: #fff;
    font-weight: 500;
    border-right: 1px solid #ced4da;
    transition: background 0.3s ease;
}

.custom-file-button i {
    margin-right: 6px;
}

/* Hover botón */
.custom-file-button:hover {
    background: #0b5ed7;
}

/* Label del archivo */
.custom-file-label {
    flex: 1;
    padding: 0 12px;
    font-size: 0.95rem;
    color: #495057;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Hover contenedor */
.custom-file-wrapper:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-1px);
    transition: all 0.3s ease;
}
</style>
<div class="content-wrapper">

  <!-- Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Convertidor XML a Ticket</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">XML → Ticket</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Content -->
  <section class="content">
    <div class="container-fluid">

      <div class="row">

        <!-- SUBIR XML -->
        <div class="col-md-4">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Cargar XML SUNAT</h3>
            </div>

            <form id="formXml" enctype="multipart/form-data">
              <div class="card-body">
                  <div class="mb-3">
                      <label for="xml" class="form-label fw-semibold">Seleccionar Archivo XML</label>

                      <div class="custom-file-wrapper">
                          <input type="file" name="xml" id="xml" class="custom-file-input" accept=".xml" required>
                          <span class="custom-file-button"><i class="fa fa-file-import"></i> Elegir Archivo</span>
                          <span class="custom-file-label" id="xmlLabel">Ningún archivo seleccionado</span>
                      </div>
                  </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Formato de salida</label>

                <div class="d-flex gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="formato" id="formato_html" value="html" checked>
                    <label class="form-check-label" for="formato_html">
                      Ticket (Vista previa)
                    </label>
                  </div>

                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="formato" id="formato_pdf" value="pdf">
                    <label class="form-check-label" for="formato_pdf">
                      Ticket PDF
                    </label>
                  </div>
                </div>
              </div>

              <div class="card-footer d-flex justify-content-end gap-2 align-items-center">
                  <!-- Botón principal: Procesar -->
                  <button type="submit" class="btn btn-primary btn-sm shadow-sm" id="btnProcesar">
                      <i class="fa fa-sync me-1"></i> Procesar XML
                  </button>
                  <!-- Botón secundario: Limpiar -->
                  <button type="button" class="btn btn-outline-danger btn-sm shadow-sm" id="btnLimpiar">
                      <i class="fa fa-times me-1"></i> Limpiar
                  </button>
              </div>
            </form>
          </div>
        </div>

        <!-- PREVIEW TICKET -->
        <div class="col-md-8">
          <div class="card card-outline card-success">
            <div class="card-header">
              <h3 class="card-title">Vista previa del Ticket</h3>

              <div class="card-tools">
                <button class="btn btn-primary btn-sm shadow-sm" onclick="imprimirTicket()">
                  <i class="fa fa-print"></i> Imprimir
                </button>
              </div>
            </div>

            <div class="card-body">
              <div id="previewTicket" class="ticket-preview text-monospace">
                <center class="text-muted">
                  <p>Sube un archivo XML para visualizar el ticket</p>
                </center>
              </div>
            </div>

          </div>
        </div>

      </div>

    </div>
  </section>
</div>

<!-- ESTILOS TICKET -->
<style>
.ticket-preview {
    width: 80mm;
    margin: auto;
    font-size: 12px;
    font-family: monospace;
    background: #fff;
    padding: 10px;
    border: 1px dashed #ccc;
}
</style>

<!-- JS -->
<script src="vistas/js/procesar.js"></script>
