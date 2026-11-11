<style>
    .conciliation-card { border-radius: 12px; border: 1px solid #e9edf3; }
    .conciliation-card .card-body { padding: 18px; }
    .conciliation-state { font-weight: 700; }
    .bank-movement-row.manual { background: rgba(23, 162, 184, .08); }
    .bank-movement-row.pending { background: rgba(255, 193, 7, .08); }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Conciliaciones bancarias</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <?php if ($helpers->verificarPermisoCodigo('conciliaciones.crear')) { ?>
                        <button type="button" class="btn btn-primary" id="btnNuevaConciliacion">
                            <i class="fas fa-plus mr-1"></i>Nueva conciliacion
                        </button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card" id="panelListadoConciliaciones">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-5">
                            <input type="search" id="searchConciliaciones" class="form-control"
                                placeholder="Buscar banco, cuenta o estado">
                        </div>
                        <div class="col-md-3">
                            <select id="limitConciliaciones" class="form-control">
                                <option value="20">20 registros</option>
                                <option value="50">50 registros</option>
                                <option value="100">100 registros</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Banco</th>
                                    <th>Periodo</th>
                                    <th>Saldo inicial</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyConciliaciones"></tbody>
                        </table>
                    </div>
                    <div id="paginationConciliaciones"></div>
                </div>
            </div>

            <div class="card" id="panelDetalleConciliacion" style="display:none;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0" id="detalleBancoConciliacion">Conciliacion bancaria</h5>
                            <small class="text-muted" id="detallePeriodoConciliacion"></small>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm ml-auto"
                            id="btnRegresarConciliaciones">
                            <i class="fas fa-arrow-left mr-1"></i>Regresar
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-3 col-md-6 mb-2">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">Saldo inicial</small>
                                <h4 class="mb-0" id="detalleSaldoInicial">S/ 0.00</h4>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-2">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">Saldo bancario final</small>
                                <h4 class="mb-0" id="detalleSaldoFinal">S/ 0.00</h4>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-2">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">Movimientos bancarios</small>
                                <h4 class="mb-0" id="detalleTotalBancario">0</h4>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-2">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted d-block">Pendientes</small>
                                <h4 class="mb-0 text-warning" id="detalleTotalPendientes">0</h4>
                            </div>
                        </div>
                    </div>

                    <?php if ($helpers->verificarPermisoCodigo('conciliaciones.importar')) { ?>
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary" id="btnImportarEstado">
                                <i class="fas fa-file-import mr-1"></i>Importar estado de cuenta
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnAgregarMovimientoBancario">
                                <i class="fas fa-plus mr-1"></i>Agregar movimiento
                            </button>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-lg-6">
                            <h5>Movimientos bancarios</h5>
                            <div id="movimientoBancarioSeleccionado" class="alert alert-light border py-2" style="display:none;">
                                <i class="fas fa-link text-primary mr-1"></i>
                                <span>Seleccione un movimiento bancario</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Descripcion</th>
                                            <th>Tipo</th>
                                            <th class="text-right">Importe</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyMovimientosBancarios"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h5>Movimientos del sistema</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Origen</th>
                                            <th>Tipo</th>
                                            <th class="text-right">Importe</th>
                                            <th>Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyMovimientosSistema"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php if ($helpers->verificarPermisoCodigo('conciliaciones.cerrar')) { ?>
                        <div class="text-right mt-3">
                            <button type="button" class="btn btn-success" id="btnCerrarConciliacion">
                                <i class="fas fa-lock mr-1"></i>Cerrar conciliacion
                            </button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalConciliacion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formConciliacion">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva conciliacion</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Cuenta bancaria</label>
                        <select id="conciliacionBanco" class="form-control select2" required></select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Periodo desde</label>
                            <input type="date" id="periodoInicio" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Periodo hasta</label>
                            <input type="date" id="periodoFin" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Saldo inicial segun estado de cuenta</label>
                        <input type="number" step="0.01" id="saldoInicial" class="form-control" value="0.00" required>
                    </div>
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea id="observacionesConciliacion" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear conciliacion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImportarConciliacion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formImportarConciliacion">
                <div class="modal-header">
                    <h5 class="modal-title">Importar estado de cuenta</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Archivo CSV, XLS o XLSX</label>
                        <input type="file" id="archivoConciliacion" class="form-control-file"
                            accept=".csv,.xls,.xlsx" required>
                    </div>
                    <div class="alert alert-info mb-2">
                        Indique los nombres o numeros de columna que corresponden al archivo.
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Fecha</label>
                            <input type="text" name="fecha" class="form-control" value="Fecha" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Descripcion</label>
                            <input type="text" name="descripcion" class="form-control" value="Descripcion">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Numero de operacion</label>
                            <input type="text" name="numero_operacion" class="form-control" value="Operacion">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Referencia</label>
                            <input type="text" name="referencia" class="form-control" value="Referencia">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Importe</label>
                            <input type="text" name="importe" class="form-control" value="Importe">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Tipo</label>
                            <input type="text" name="tipo" class="form-control" value="Tipo">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Cargo</label>
                            <input type="text" name="cargo" class="form-control" value="Cargo">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Abono</label>
                            <input type="text" name="abono" class="form-control" value="Abono">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Saldo</label>
                            <input type="text" name="saldo" class="form-control" value="Saldo">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMovimientoBancario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formMovimientoBancario">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar movimiento bancario</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Fecha y hora</label>
                            <input type="datetime-local" id="movimientoBancarioFecha" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tipo</label>
                            <select id="movimientoBancarioTipo" class="form-control" required>
                                <option value="abono">Abono / ingreso</option>
                                <option value="cargo">Cargo / egreso</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Importe</label>
                        <input type="number" step="0.01" min="0.01" id="movimientoBancarioImporte" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Descripcion</label>
                        <input type="text" id="movimientoBancarioDescripcion" class="form-control">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Numero de operacion</label>
                            <input type="text" id="movimientoBancarioOperacion" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Referencia</label>
                            <input type="text" id="movimientoBancarioReferencia" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="vistas/js/conciliaciones-bancarias.js?v=202611081412"></script>
