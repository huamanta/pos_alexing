<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h1 class="mb-1"><i class="fas fa-tools text-primary"></i> órdenes de Trabajo</h1>
                    <p class="text-muted mb-0">Gestión integral de reparaciones, ensamblajes, costos y seguimiento de
                        motocicletas.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2"><i class="fas fa-cube me-2"></i> ERP
                        Taller</span>
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Regresar
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <form id="frmOrdenTrabajo" data-endpoint="">
                <input type="hidden" name="payloadJson" id="payloadJson">

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">

                        <div
                            class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
                            <div>
                                <h2 class="h4 mb-1">Nueva Orden de Trabajo</h2>
                                <p class="text-muted mb-0">Flujo guiado para registrar, asignar recursos y controlar
                                    costos del taller.</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success px-3 py-2"><i class="fas fa-clipboard-check me-2"></i>
                                    Estado: Pendiente</span>
                            </div>
                        </div>

                        <style>
                            .wizard-step {
                                border: 1px solid #e5e7eb;
                                background: #fff;
                                transition: all 0.2s ease;
                            }

                            .wizard-step.active {
                                border-color: #2563eb;
                                background: #eff6ff;
                                box-shadow: 0 8px 24px rgba(37, 99, 235, 0.12);
                            }

                            .wizard-step.completed {
                                border-color: #16a34a;
                                background: #f0fdf4;
                            }
                        </style>

                        <div class="row g-2 mb-4">
                            <div class="col-md-3">
                                <div class="wizard-step active rounded-3 p-3" data-step="1">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-bold">1. Información General</span>
                                        <i class="fas fa-file-alt text-primary"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="wizard-step rounded-3 p-3" data-step="2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-bold">2. Mecónicos</span>
                                        <i class="fas fa-users text-info"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="wizard-step rounded-3 p-3" data-step="3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-bold">3. Repuestos</span>
                                        <i class="fas fa-cogs text-warning"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="wizard-step rounded-3 p-3" data-step="4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-bold">4. Costos</span>
                                        <i class="fas fa-chart-pie text-primary"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="wizard-step rounded-3 p-3" data-step="5">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-bold">5. Resumen</span>
                                        <i class="fas fa-clipboard-list text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="step-content" data-step="1">
                            <div class="row g-4">
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header bg-light border-0">
                                            <h3 class="card-title mb-0"><i class="fas fa-file-signature me-2"></i> Datos
                                                generales</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tipo de orden</label>
                                                <select class="form-control" id="tipoOrden" name="tipoOrden">
                                                    <option value="REPARACION">Reparación</option>
                                                    <option value="ENSAMBLAJE">Ensamblaje</option>
                                                    <option value="MANTENIMIENTO">Mantenimiento</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Fecha</label>
                                                <input type="date" class="form-control" name="fecha" id="fecha_registro">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Estado</label>
                                                <select class="form-control" name="estado">
                                                    <option value="PENDIENTE">PENDIENTE</option>
                                                    <option value="EN_PROCESO">EN PROCESO</option>
                                                    <option value="FINALIZADO">FINALIZADO</option>
                                                    <option value="CANCELADO">CANCELADO</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Prioridad</label>
                                                <select class="form-control" name="prioridad">
                                                    <option>Alta</option>
                                                    <option>Media</option>
                                                    <option>Baja</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Observaciones</label>
                                                <textarea class="form-control" rows="4" name="observaciones"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header bg-light border-0">
                                            <h3 class="card-title mb-0"><i class="fas fa-motorcycle me-2"></i> Vehóculo
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="input-group mb-3">
                                                <select type="text" class="form-control form-control-lg select2"
                                                    name="vehiculoBuscar" id="vehiculoBuscar">
                                                </select>
                                            </div>
                                            <div id="panelProductoEmpty"
                                                class="border border-2 rounded-4 text-center py-5"
                                                style="border-style:dashed !important;background:#fafafa;">

                                                <i class="fas fa-motorcycle fa-4x text-primary mb-3"></i>

                                                <h5 class="mb-2">Seleccione una motocicleta</h5>

                                                <p class="text-muted mb-0">
                                                    Utilice el buscador para localizar una motocicleta y crear una nueva
                                                    orden de trabajo.
                                                </p>

                                            </div>
                                            <div class="rounded-4 border p-3 bg-light" id="panelProducto"
                                                style="display: none;">
                                                <div class="text-center mb-3">
                                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/ width='320' height='220' viewBox='0 0 320 220'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop offset='0%25' stop-color='%230d6efd'/%3E%3Cstop offset='100%25' stop-color='%231b4de5'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='320' height='220' rx='24' fill='%23f8fbff'/%3E%3Ccircle cx='100' cy='145' r='48' fill='%23e9f2ff'/%3E%3Ccircle cx='230' cy='145' r='48' fill='%23e9f2ff'/%3E%3Crect x='70' y='95' width='180' height='70' rx='28' fill='url(%23g)'/%3E%3Crect x='110' y='70' width='90' height='40' rx='16' fill='%230d6efd'/%3E%3Crect x='90' y='120' width='145' height='18' rx='9' fill='%23ffffff' opacity='0.8'/%3E%3C/svg%3E"
                                                        class="img-fluid rounded-3" alt="Vehiculo" id="imagenmuestra">
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <h4 class="h5 mb-1" id="productoNombre">@Producto</h4>
                                                        <p class="text-muted mb-0" id="prooductoDescripcion">
                                                            @descripcion</p>
                                                    </div>
                                                    <span class="badge bg-success" id="productoEstado">Recuperado</span>
                                                </div>
                                                <div class="row g-2 small text-muted">
                                                    <div class="col-6"><strong>Numero serie:</strong> <span
                                                            id="productoSerie">@serie</span></div>
                                                    <div class="col-6"><strong>Numero placa:</strong> <span
                                                            id="productoPlaca"></span></div>
                                                    <div class="col-6"><strong>Color:</strong> <span
                                                            id="prodcutoColor"></span></div>
                                                    <div class="col-6"><strong>Año de fabricación:</strong> <span
                                                            id="productoAnio"></span></div>
                                                    <div class="col-6"><strong>Kilometraje:</strong> <span
                                                            id="productoKilometraje"></span></div>
                                                    <div class="col-6"><strong>Precio:</strong> <span
                                                            id="productoPrecio"></span></div>
                                                </div>
                                            </div>

                                            <div class="alert alert-warning mt-3 mb-0" role="alert">
                                                <i class="fas fa-info-circle me-2"></i> Si el tipo de orden es
                                                Ensamblaje, el vehóculo se crearó al finalizar la orden.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header bg-light border-0">
                                            <h3 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>
                                                Información adicional</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Cliente</label>
                                                <select type="text" class="form-control" name="idcliente"
                                                    id="idcliente"></select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Fecha compromiso</label>
                                                <input type="date" class="form-control" name="fechaCompromiso">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Referencia</label>
                                                <input type="text" class="form-control" name="referencia"/>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Documento relacionado</label>
                                                <input type="text" class="form-control" name="documentoRelacionado"/>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Observaciones internas</label>
                                                <textarea class="form-control" rows="4"
                                                    name="observacionesInternas"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-4" role="alert">
                                <i class="fas fa-lightbulb me-2"></i> Complete la información general antes de
                                continuar.
                            </div>
                        </div>

                        <div class="step-content d-none" data-step="2">
                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div
                                            class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                                            <h3 class="card-title mb-0"><i class="fas fa-users me-2"></i> Asignación de
                                                mecónicos</h3>
                                            
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <select class="form-control" style="min-width: 260px;"
                                                    id="mechanicSelect">
                                                    <option value="">Seleccione un mecánico...</option>
                                                </select>
                                            </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Foto</th>
                                                            <th>Nombre</th>
                                                            <th>Rol</th>
                                                            <th>Horas</th>
                                                            <th>Costo/h</th>
                                                            <th>Subtotal</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="mechanicsTableBody">
                                                       
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header bg-light border-0">
                                            <h3 class="card-title mb-0"><i class="fas fa-balance-scale me-2"></i>
                                                Resumen</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-3">
                                                <span>Cantidad de mecónicos</span>
                                                <strong id="mechanicsCount">2</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span>Horas totales</span>
                                                <strong id="hoursTotal">10 h</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span>Costo mano de obra</span>
                                                <strong id="laborCost">S/. 340</strong>
                                            </div>
                                            <div class="border-top pt-3">
                                                <div class="text-center text-muted small">Asignación preparada para el
                                                    taller.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="step-content d-none" data-step="3">
                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div
                                            class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                                            <h3 class="card-title mb-0"><i class="fas fa-boxes me-2"></i> Repuestos y
                                                materiales</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="input-group mb-4">
                                                <Select class="form-control" name="repuestoBuscar" id="repuestoBuscar"></Select>
                                            </div>

                                            <div class="alert alert-primary" role="alert">
                                                <i class="fas fa-cog me-2"></i> Para órdenes de ensamblaje, esta sección
                                                permite incluir todas las piezas necesarias para montar una motocicleta.
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Código</th>
                                                            <th>Producto</th>
                                                            <th>Stock</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio</th>
                                                            <th>Descuento</th>
                                                            <th>Subtotal</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="partsTableBody"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header bg-light border-0">
                                            <h3 class="card-title mb-0"><i class="fas fa-chart-bar me-2"></i> Resumen
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-3">
                                                <span>Cantidad de productos</span>
                                                <strong id="productsCount">2</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span>Costo repuestos</span>
                                                <strong id="repuestosCost">S/. 132</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span>Impuestos</span>
                                                <strong id="taxCost">S/. 19.80</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span>Total parcial</span>
                                                <strong id="partialTotal">S/. 151.80</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="step-content d-none" data-step="4">
                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6 col-xl-3">
                                            <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1">Costo de repuestos</h6>
                                                            <h3 class="mb-0">S/. 132</h3>
                                                        </div>
                                                        <i class="fas fa-boxes fa-2x opacity-75"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <div class="card border-0 shadow-sm h-100 bg-info text-white">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1">Costo mano de obra</h6>
                                                            <h3 class="mb-0">S/. 340</h3>
                                                        </div>
                                                        <i class="fas fa-user-cog fa-2x opacity-75"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <div class="card border-0 shadow-sm h-100 bg-warning text-dark">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1">Otros gastos</h6>
                                                            <h3 class="mb-0">S/. 95</h3>
                                                        </div>
                                                        <i class="fas fa-coins fa-2x opacity-75"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-3">
                                            <div class="card border-0 shadow-sm h-100 bg-success text-white">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1">Costo total</h6>
                                                            <h3 class="mb-0">S/. 567</h3>
                                                        </div>
                                                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light border-0">
                                            <h3 class="card-title mb-0"><i class="fas fa-list-alt me-2"></i> Registrar
                                                costos adicionales</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Otros gastos</label>
                                                    <input type="number" class="form-control" value="95"
                                                        name="otrosGastos">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Servicios externos</label>
                                                    <input type="number" class="form-control" value="0"
                                                        name="serviciosExternos">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Pintura</label>
                                                    <input type="number" class="form-control" value="0" name="pintura">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Transporte</label>
                                                    <input type="number" class="form-control" value="0"
                                                        name="transporte">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Lavado</label>
                                                    <input type="number" class="form-control" value="35" name="lavado">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label fw-bold">Observaciones</label>
                                                    <textarea class="form-control" rows="3"
                                                        name="costosObservaciones">Aplicar pintura de retoque y limpieza final.</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header bg-light border-0">
                                            <h3 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>
                                                Distribución de costos</h3>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center border border-4 border-primary"
                                                style="width:220px;height:220px;background:conic-gradient(#0d6efd 0 45%, #20c997 45% 70%, #ffc107 70% 85%, #6c757d 85% 100%);">
                                                <div class="bg-white rounded-circle d-flex flex-column align-items-center justify-content-center"
                                                    style="width:150px;height:150px;">
                                                    <h4 class="mb-0">S/. 567</h4>
                                                    <small class="text-muted">Total</small>
                                                </div>
                                            </div>
                                            <ul class="list-unstyled text-start mt-4 small">
                                                <li><i class="fas fa-circle text-primary me-2"></i> Repuestos 45%</li>
                                                <li><i class="fas fa-circle text-teal me-2"></i> Mano de obra 35%</li>
                                                <li><i class="fas fa-circle text-warning me-2"></i> Otros gastos 15%
                                                </li>
                                                <li><i class="fas fa-circle text-secondary me-2"></i> Servicios 5%</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="step-content d-none" data-step="5">
                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light border-0">
                                            <h3 class="card-title mb-0"><i class="fas fa-clipboard-list me-2"></i>
                                                Resumen final</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="p-3 rounded-3 bg-light">
                                                        <h6 class="fw-bold text-primary">Información de la orden</h6>
                                                        <p class="mb-1"><strong>Tipo:</strong> Reparación</p>
                                                        <p class="mb-1"><strong>Sucursal:</strong> Principal</p>
                                                        <p class="mb-1"><strong>Estado:</strong> <span
                                                                class="badge bg-warning">Pendiente</span></p>
                                                        <p class="mb-0"><strong>Prioridad:</strong> Alta</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="p-3 rounded-3 bg-light">
                                                        <h6 class="fw-bold text-primary">Vehóculo</h6>
                                                        <p class="mb-1"><strong>Modelo:</strong> Honda XR150</p>
                                                        <p class="mb-1"><strong>Placa:</strong> ABC-123</p>
                                                        <p class="mb-1"><strong>Cliente:</strong> Carlos Mendoza</p>
                                                        <p class="mb-0"><strong>Fecha compromiso:</strong> 06/08/2026
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="p-3 rounded-3 bg-light">
                                                        <h6 class="fw-bold text-primary">Mecónicos asignados</h6>
                                                        <ul class="mb-0 ps-3">
                                                            <li>Juan Pórez ó 6 h</li>
                                                            <li>Marcos Rojas ó 4 h</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="p-3 rounded-3 bg-light">
                                                        <h6 class="fw-bold text-primary">Repuestos utilizados</h6>
                                                        <ul class="mb-0 ps-3">
                                                            <li>Filtro de aceite</li>
                                                            <li>Kit de frenos</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header bg-light border-0">
                                            <h3 class="card-title mb-0"><i class="fas fa-money-bill-wave me-2"></i>
                                                Costos y totales</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-3">
                                                <span>Repuestos</span>
                                                <strong>S/. 132</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span>Mano de obra</span>
                                                <strong>S/. 340</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <span>Otros gastos</span>
                                                <strong>S/. 95</strong>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="fw-bold">Total</span>
                                                <strong class="text-primary fs-5">S/. 567</strong>
                                            </div>
                                            <div class="mt-4">
                                                <span class="badge bg-success">Estado final: Listo para entregar</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4">
                            <button type="button" class="btn btn-outline-secondary btn-lg prev-step" disabled>
                                <i class="fas fa-arrow-left me-2"></i> Anterior
                            </button>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-print me-2"></i> Imprimir
                                </button>
                                <button type="button" class="btn btn-primary btn-lg next-step">
                                    Siguiente <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4 d-none" id="wizardAlert" role="alert"></div>

                    </div>
                </div>

            </form>

        </div>
    </section>

</div>

<script src="vistas/js/orden-trabajo.js"></script>