
<?php
require_once __DIR__ . '/../../modelos/Helpers.php';
$helpers = new Helpers();
?>

<div id="toastContainer" style="position: fixed; top: 80px; right: 20px; z-index: 1060;"></div>

<nav class="main-header navbar navbar-expand navbar-white navbar-light sticky-top" id="navbar-global">

    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link navbar-icon-link" data-widget="pushmenu" role="button"><i
                    class="fas fa-bars fa-lg"></i></a>
        </li>
        <li class="nav-item d-flex align-items-center">
            <span class="badge user-profile-link px-3 py-2">
                <i class="fas fa-store mr-1"></i>
                <span id="nombreSucursal"></span>
            </span>
        </li>
        <li class="nav-item d-flex align-items-center">
            <span class="badge user-profile-link px-3 py-2">
                <i class="fas fa-money-bill-wave mr-1"></i>
                <span id="monedaSucursal"></span>
            </span>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto align-items-center">

        <?php if (!empty($_SESSION['idsucursal'])): ?>
            <li class="nav-item">
                <a class="nav-link navbar-icon-link" data-widget="fullscreen" role="button" title="Pantalla Completa">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <?php if ($helpers->verificarPermisoCodigo('configuraciongeneral.vercalendario')): ?>
                <li class="nav-item">
                    <a class="nav-link navbar-icon-link" role="button" title="Ver calendario" onclick="verEventos()">
                        <i class="fas fa-calendar"></i>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item dropdown">
                <a class="nav-link cxcAlertLink position-relative navbar-icon-link" data-toggle="dropdown" href="#"
                    title="Cuentas por Cobrar">
                    <i class="fas fa-file-invoice-dollar fa-lg"></i>
                    <span class="badge badge-danger navbar-badge-custom cxcAlertCount" style="display:none;">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow border-0 rounded-lg mt-2 p-0">
                    <div class="px-3 py-3 border-bottom bg-light rounded-top">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="font-weight-bold text-dark">
                                    <i class="fas fa-file-invoice-dollar text-primary mr-2"></i>
                                    Cuentas por Cobrar
                                </div>
                                <small class="text-muted">Pagos pendientes de atención</small>
                            </div>
                            <span class="badge badge-danger cxcAlertCount rounded-pill"></span>
                        </div>
                    </div>

                    <div class="cxcAlertList p-2" style="max-height: 320px; overflow-y: auto;"></div>

                    <div class="border-top">
                        <a href="cuentas-cobrar" class="dropdown-item text-center text-primary font-weight-bold py-3">
                            Ver todas las cuentas
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </li>

            <li class="nav-item dropdown mr-2" id="stockbajito">
                <a class="nav-link position-relative text-secondary" data-toggle="dropdown" href="#" id="stockAlertLink">
                    <i class="fas fa-bell fa-lg"></i>
                    <span class="badge badge-warning badge-notify navbar-badge-custom" id="stockAlertCount"
                        style="display:none;">0</span>
                </a>
                <div
                    class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-lg border-0 rounded-lg mt-2 p-0 stock-alert-dropdown">
                    <div class="px-3 py-3 border-bottom bg-light rounded-top">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="font-weight-bold text-dark">
                                    <i class="fas fa-box-open text-warning mr-2"></i>
                                    Stock bajo
                                </div>
                                <small class="text-muted">Productos que requieren reposición</small>
                            </div>
                            <span class="badge badge-warning badge-pill" id="stockAlertCount2">0</span>
                        </div>
                    </div>

                    <div id="stockAlertTable" class="stock-alert-list">
                        <div id="stockAlertTableBody"></div>
                    </div>

                    <div class="px-3 py-2 border-top bg-light rounded-bottom text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Revisa el inventario para realizar la reposición
                        </small>
                    </div>
                </div>
            </li>
        <?php endif; ?>

        <div class="navbar-divider"></div>

        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link user-profile-link" data-toggle="dropdown">
                <div class="user-avatar-circle">
                    <?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?>
                </div>
                <div class="d-none d-md-flex user-profile-meta ml-2 text-left">
                    <span class="d-block font-weight-bold text-dark" style="font-size: 0.85rem;">
                        <?php echo explode(" ", $_SESSION['nombre'])[0]; ?>
                    </span>
                    <span class="d-block text-muted" style="font-size: 0.7rem;">
                        <?php echo isset($_SESSION['nombre_negocio']) ? $_SESSION['nombre_negocio'] : 'Admin'; ?>
                    </span>
                </div>
                <i class="fas fa-chevron-down user-chevron"></i>
            </a>

            <ul
                class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-0 shadow-xl mt-3 rounded-lg overflow-hidden">
                <li class="user-header bg-primary text-white p-4 text-center">
                    <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-2 shadow-sm"
                        style="width: 60px; height: 60px; font-size:1.5rem; font-weight:bold;">
                        <?php echo strtoupper(substr($_SESSION['nombre'], 0, 1)); ?>
                    </div>
                    <p class="mb-0 font-weight-bold">
                        <?php echo $_SESSION['nombre']; ?>
                    </p>
                    <small class="text-white-50"><?php echo $_SESSION['cargo']; ?></small>
                </li>

                <li class="user-footer p-2 bg-white">
                    <?php if (!empty($_SESSION['idsucursal'])): ?>
                        <a href="salirsucursal"
                            class="btn btn-outline-success btn-block font-weight-bold border-0 text-left px-3 py-2">
                            <i class="fas fa-box"></i>
                            Cambiar sucursal
                        </a>
                    <?php endif; ?>
                    <?php if ($helpers->verificarPermisoCodigo('configuraciongeneral.ver')): ?>
                        <a href="configuraciongeneral"
                            class="btn btn-outline-success btn-block font-weight-bold border-0 text-left px-3 py-2">
                            <i class="fas fa-cog"></i>
                            Configuracion
                        </a>
                    <?php endif; ?>
                    <a href="salir"
                        class="btn btn-outline-danger btn-block font-weight-bold border-0 text-left px-3 py-2">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</nav>
<div class="modal fade" id="myModalEventosGeneral">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Agenda de Seguimiento
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <style>
                #calendar {
                    min-height: 300px;
                }

                .fc {
                    font-size: 14px;
                }

                .fc-toolbar-title {
                    font-size: 1.3rem !important;
                    font-weight: 600;
                }

                .fc-daygrid-event {
                    border-radius: 8px;
                    padding: 2px 6px;
                }

                .fc-theme-standard td,
                .fc-theme-standard th {
                    border-color: #e9ecef;
                }
            </style>

            <div class="modal-body p-2 bg-light">

                <div id="calendarGeneral"></div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">

                    <i class="fas fa-times"></i>
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalVerSeguimientoGeneral" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">
                    <i class="fas fa-calendar-check"></i>
                    Detalle del Seguimiento
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label><strong>Cliente</strong></label>
                        <div id="ver_cliente_general" class="form-control bg-light"></div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label><strong>Tipo</strong></label>
                        <div id="ver_tipo_general" class="form-control bg-light"></div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label><strong>Estado</strong></label>
                        <div id="ver_estado_general" class="form-control bg-light"></div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label><strong>Responsable</strong></label>
                        <div id="ver_responsable_general" class="form-control bg-light"></div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label><strong>Prioridad</strong></label>
                        <div id="ver_prioridad_general" class="form-control bg-light"></div>
                    </div>

                    <div class="col-md-5 mb-3">
                        <label><strong>Cuota</strong></label>
                        <div id="ver_cuota_general" class="form-control bg-light"></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><strong>Fecha Programada</strong></label>
                        <div id="ver_fecha_programada_general" class="form-control bg-light"></div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><strong>Fecha Final</strong></label>
                        <div id="ver_fecha_final_general" class="form-control bg-light"></div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label><strong>Dirección</strong></label>
                        <div id="ver_direccion_general" class="form-control bg-light"></div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label><strong>Descripción</strong></label>
                        <div id="ver_descripcion_general" class="form-control bg-light" style="min-height:120px;"></div>
                    </div>

                    <div class="col-md-12">
                        <label><strong>Archivos Adjuntos</strong></label>

                        <div id="ver_adjuntos_general" class="border rounded p-2" style="min-height:80px;">
                        </div>
                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-dismiss="modal">

                    <i class="fas fa-times"></i>
                    Cerrar

                </button>

            </div>

        </div>
    </div>
</div>
<script src="vistas/js/cabezote.js"></script>