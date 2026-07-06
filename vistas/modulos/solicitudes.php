<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Solicitudes de Crédito</h1>
                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="#">Ventas</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Solicitudes
                        </li>
                    </ol>

                </div>

            </div>

        </div>
    </section>

    <section class="content">

        <div class="container-fluid">

            <!-- KPIs -->

            <div class="row">

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-info">

                        <div class="inner">

                            <h3 id="kpiTotalSolicitudes">0</h3>

                            <p>
                                Solicitudes
                            </p>

                        </div>

                        <div class="icon">
                            <i class="fa fa-file-alt"></i>
                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-success">

                        <div class="inner">

                            <h3 id="kpiAprobados">0</h3>

                            <p>
                                Aprobados
                            </p>

                        </div>

                        <div class="icon">
                            <i class="fa fa-check"></i>
                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-warning">

                        <div class="inner">

                            <h3 id="kpiObservados">0</h3>

                            <p>
                                Observados
                            </p>

                        </div>

                        <div class="icon">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>

                    </div>

                </div>

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-danger">

                        <div class="inner">

                            <h3 id="kpiRechazados">0</h3>

                            <p>
                                Rechazados
                            </p>

                        </div>

                        <div class="icon">
                            <i class="fa fa-times"></i>
                        </div>

                    </div>

                </div>

            </div>

            <!-- TABLA -->

            <div class="card">

                <div class="card-header">

                    <div class="row">

                        <div class="col-md-2">
                            <?php if (Helpers::getUserPermissionAccion('Crear solicitud')): ?>
                                <button type="button" class="btn btn-primary btn-block" onclick="nuevaSolicitud()">

                                    <i class="fa fa-plus"></i>
                                    Nuevo

                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-2">

                            <select class="form-control" id="filtroEstado">

                                <option value="">
                                    Todos los estados
                                </option>

                                <option value="BORRADOR">
                                    Borrador
                                </option>

                                <option value="EN_PROCESO">
                                    En proceso
                                </option>

                                <option value="OBSERVADO">
                                    Observado
                                </option>

                                <option value="PENDIENTE_DOCUMENTOS">
                                    Pendiente documentos
                                </option>

                                <option value="APROBADO">
                                    Aprobado
                                </option>

                                <option value="RECHAZADO">
                                    Rechazado
                                </option>

                            </select>

                        </div>

                        <div class="col-md-2">

                            <select class="form-control" id="filtroRiesgo">

                                <option value="">
                                    Todos los riesgos
                                </option>

                                <option value="BAJO">
                                    Bajo
                                </option>

                                <option value="MEDIO">
                                    Medio
                                </option>

                                <option value="ALTO">
                                    Alto
                                </option>

                                <option value="CRITICO">
                                    Crítico
                                </option>

                            </select>

                        </div>

                        <div class="col-md-3">

                            <select class="form-control" id="filtroPaso">

                                <option value="">
                                    Todas las etapas
                                </option>

                                <option value="1">
                                    Evaluación Inicial
                                </option>

                                <option value="2">
                                    Validación Documentaria
                                </option>

                                <option value="3">
                                    Verificación Domiciliaria
                                </option>

                                <option value="4">
                                    Comité de Crédito
                                </option>

                                <option value="5">
                                    Aprobación Final
                                </option>

                            </select>

                        </div>

                        <div class="col-md-3">

                            <input type="text" class="form-control" id="filtroTexto"
                                placeholder="Buscar cliente o código">

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <table id="tblSolicitudes" class="table table-bordered table-striped">

                        <thead>

                            <tr>

                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Score</th>
                                <th>Riesgo</th>
                                <th>Paso Actual</th>
                                <th>Días Etapa</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th width="120">Acciones</th>

                            </tr>

                        </thead>

                        <tbody></tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>

</div>

<!-- MODAL NUEVA SOLICITUD -->

<div class="modal fade" id="modalSolicitud">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header bg-primary">

                <h4 class="modal-title">
                    Solicitud de Crédito
                </h4>

                <button type="button" class="close" data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form id="formSolicitud">

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6">
                            <label>Cliente</label>
                            <select class="form-control select2" id="idcliente" name="idcliente"
                                onchange="seleccionarCliente(this.value)">
                            </select>

                        </div>

                        <div class="col-md-6">

                            <label>Cotizacion</label>

                            <select class="form-control select2" id="idcotizacion" name="idcotizacion">
                                <option value="">Seleccione una cotización</option>
                            </select>

                        </div>

                        <div class="col-md-3">

                            <label>Ingreso Mensual</label>

                            <input type="number" class="form-control" name="ingreso_mensual">

                        </div>

                        <div class="col-md-3">

                            <label>Inicial Disponible</label>

                            <input type="number" class="form-control" name="inicial">

                        </div>

                    </div>

                    <div class="row mt-3">

                        <div class="col-md-12">

                            <label>Observación</label>

                            <textarea class="form-control" rows="4" name="observacion"></textarea>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">

                        Cerrar

                    </button>

                    <button type="submit" class="btn btn-primary">

                        Guardar

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- MODAL FLUJO -->

<div class="modal fade" id="modalWorkflow">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header bg-info">

                <h4 class="modal-title">
                    Flujo de Aprobación
                </h4>

                <button type="button" class="close" data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <div id="timelineWorkflow"></div>

            </div>

        </div>

    </div>

</div>

<!-- MODAL DOCUMENTOS -->

<div class="modal fade" id="modalArchivos">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header bg-success">

                <h4 class="modal-title">
                    Documentos
                </h4>

                <button type="button" class="close" data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <div id="listaArchivos"></div>

            </div>

        </div>

    </div>

</div>

<!-- MODAL DETALLE -->

<div class="modal fade" id="modalDetalleSolicitud">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header bg-dark">

                <h4 class="modal-title">

                    Detalle de Solicitud

                </h4>

                <button type="button" class="close" data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>
            <div class="modal-body">
                <div id="detalleSolicitud"></div>

            </div>

        </div>

    </div>

</div>

<style>
    .solicitud-progressbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        margin-bottom: 1rem;
    }

    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        min-width: 0;
        text-align: center;
        transition: transform .2s ease;
    }

    .step-item.step-clickable {
        cursor: pointer;
    }

    .step-item.step-clickable .step-marker {
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .step-item.step-clickable:hover .step-marker {
        box-shadow: 0 0 0 8px rgba(40, 167, 69, .18);
        transform: scale(1.05);
    }

    .step-item.step-clickable:hover .step-label {
        color: #155724;
    }

    .step-marker {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #fff;
        background: #d1d1d1;
        position: relative;
        z-index: 2;
    }

    .step-marker.completed {
        background: #28a745;
    }

    .step-marker.current {
        background: #ffffff;
        color: #28a745;
        border: 3px solid #28a745;
    }

    .step-connector {
        flex: 1;
        height: 4px;
        background: #d1d1d1;
        border-radius: 2px;
        margin: 0 6px;
    }

    .step-connector.completed {
        background: #28a745;
    }

    .step-label {
        margin-top: .5rem;
        font-size: .82rem;
        color: #6c757d;
        line-height: 1.2;
    }

    .step-label.completed {
        color: #212529;
        font-weight: 600;
    }

    .step-label.current {
        color: #155724;
        font-weight: 700;
    }

    .step-item.selected .step-marker {
        box-shadow: 0 0 0 8px rgba(0, 123, 255, .18);
        transform: scale(1.05);
    }

    .step-item.selected .step-label {
        color: #004085;
        font-weight: 700;
    }
</style>

<script src="vistas/js/solicitudes.js"></script>