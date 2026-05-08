<style>
    #navbar-global {
        background-color: #ffffff;
        backdrop-filter: blur(10px);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0.8rem 1rem;
    }

    /* Separador vertical sutil */
    .navbar-divider {
        width: 1px;
        height: 30px;
        background-color: #e2e8f0;
        margin: 0 12px;
        display: none;
        /* Oculto en móvil */
    }

    @media (min-width: 576px) {
        .navbar-divider {
            display: inline-block;
            vertical-align: middle;
        }
    }

    /* Contenedor "Pastilla" del Perfil */
    .user-profile-link {
        display: flex;
        align-items: center;
        padding: 6px 15px !important;
        border-radius: 30px;
        transition: all 0.2s ease-in-out;
        background: #f1f5f9;
        border: 1px solid transparent;
        margin-left: 5px;
    }

    .user-profile-meta {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.15;
        min-height: 35px;
    }

    /* Efecto al pasar el mouse (Hover) */
    .user-profile-link:hover,
    .user-menu.show .user-profile-link {
        background-color: #e2e8f0;
        border-color: #e2e8f0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    /* Círculo para el avatar/icono */
    .user-avatar-circle {
        width: 35px;
        height: 35px;
        background-color: #4f46e5;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 600;
        margin-right: 10px;
    }

    /* Flecha pequeña indicadora */
    .user-chevron {
        font-size: 0.7rem;
        color: #adb5bd;
        margin-left: 8px;
    }

    /* Ajuste de badges de notificación */
    .navbar-badge-custom {
        font-size: 10px;
        font-weight: 600;
        padding: 4px 6px;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        position: absolute;
        right: 0;
        top: 0;
    }

    .navbar-icon-link {
        color: #64748b;
    }

    .navbar-icon-link:hover {
        color: #334155;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div id="toastContainer" style="position: fixed; top: 80px; right: 20px; z-index: 1060;"></div>

<script>
    // Sucursal actual
    const currentSucursal = <?php echo $_SESSION['idsucursal'] ?? 0; ?>;
    if (typeof window.CURRENT_SUCURSAL === "undefined") {
        window.CURRENT_SUCURSAL = currentSucursal;
    }

    // ==================== Sesión ====================
    var status = true;
    var sessionChecker = setInterval(() => {
        if (Boolean(status) === true) {
            loadSesionsApp();
        } else {
            clearInterval(sessionChecker);
        }
    }, 10000);

    function loadSesionsApp() {
        $.ajax({
            url: "controladores/negocio.php?op=sesions",
            type: "GET",
            contentType: false,
            processData: false,
            success: function (datos) {
                var data = JSON.parse(datos);
                if (!data.status) { sessionExpired(); }
            },
            error: function (jqXHR, textStatus, errorThrown) { console.error("Error sesión:", textStatus); }
        });
    }

    function sessionExpired() {
        if (!status) return;
        status = false;
        clearInterval(sessionChecker);
        Swal.fire({
            title: "Sesión expirada",
            text: "Tu sesión ha expirado. Por favor, inicia sesión nuevamente.",
            icon: "warning",
            confirmButtonText: "OK",
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => {
            $.ajax({
                url: "controladores/auth.php",
                type: "POST",
                complete: function () { window.location.href = "ingreso"; }
            });
        });
    }

    // ==================== Comprobantes Pendientes ====================
    function checkComprobantesPendientes() {
        $.ajax({
            url: 'controladores/venta.php?op=comprobantesPendientes',
            type: 'GET',
            dataType: 'json',
            success: function (response) {

                if (response && response.total > 0) {

                    let ultima = localStorage.getItem("notif_comprobantes_time");
                    let ahora = new Date().getTime();

                    // 1 hora = 3600000 ms
                    if (!ultima || (ahora - ultima) > 3600000) {

                        toastr.warning(
                            "Tienes " + response.total + " comprobante(s) sin enviar a SUNAT",
                            "Pendientes de Envío",
                            {
                                positionClass: "toast-top-center"
                            }
                        );

                        localStorage.setItem("notif_comprobantes_time", ahora);
                    }
                }
            }
        });
    }

    // Ejecuta una vez
    checkComprobantesPendientes();



    // ==================== Notificaciones de Traslados ====================
    let notificacionesMostradas = new Set();

    function verificarNuevasNotificaciones() {
        if (currentSucursal <= 0) return;
        $.getJSON("controladores/traslado.php?op=listarnoti&idsucursal=" + currentSucursal, function (data) {
            if (!data || data.length === 0) return;
            data.forEach(n => {
                if (!n.tipo || n.tipo.trim() === "") return;
                if (!notificacionesMostradas.has(n.idnotificacion) && n.leido == 0) {
                    let tipo = (n.tipo && n.tipo.toLowerCase() === "traslado") ? "traslado" : "solicitud";
                    mostrarToast(n.mensaje, n.fecha, n.idnotificacion, n.idtraslado, tipo, n.iddestino);
                    notificacionesMostradas.add(n.idnotificacion);
                }
            });
        });
    }

    function mostrarToast(mensaje, fecha, idnotificacion = null, idtraslado = null, tipo = "solicitud", iddestino = null) {
        const toastId = 'toast_' + Date.now();
        let titulo = "", icono = "", color = "", contenido = "", botonAccion = "";

        switch (tipo) {
            case "traslado":
                titulo = "Nueva notificación de traslado";
                icono = "fa-truck";
                color = "#28a745";
                botonAccion = (idtraslado && iddestino == currentSucursal)
                    ? `<button class="btn btn-success btn-sm mt-2" onclick="aceptarTraslado(${idtraslado}, '${toastId}', ${idnotificacion})"><i class="fa fa-check"></i> Aceptar</button>` : "";
                break;
            case "solicitud":
                titulo = "Nueva solicitud pendiente";
                icono = "fa-bell";
                color = "#007bff";
                botonAccion = `<button class="btn btn-primary btn-sm mt-2" onclick="cerrarSolicitud('${toastId}', ${idnotificacion})"><i class="fa fa-times"></i> Cerrar</button>`;
                break;
            default:
                titulo = "Nueva notificación";
                icono = "fa-info-circle";
                color = "#6c757d";
        }

        contenido = `<div class="toast-body"><small style="color:#777;">${fecha}</small><br>${mensaje}${botonAccion}</div>`;
        const toastHTML = `<div id="${toastId}" class="toast-custom" style="border-left-color:${color}"><div class="toast-header" style="color:${color}"><div><i class="fa ${icono} toast-icon" style="margin-right:8px;"></i> ${titulo}</div><button class="toast-close" title="Cerrar">&times;</button></div>${contenido}</div>`;

        $("#toastContainer").append(toastHTML);
        const $toast = $('#' + toastId);
        $toast.find('.toast-close').on('click', function (e) { e.stopPropagation(); cerrarToast($toast, idnotificacion, tipo); });
    }

    function cerrarToast($toast, idnotificacion, tipo) {
        $toast.css('animation', 'fadeOut 0.4s forwards');
        setTimeout(() => {
            if (tipo === "solicitud" && idnotificacion) { $.post("controladores/traslado.php?op=marcarleida", { idnotificacion: idnotificacion }); }
            $toast.remove();
        }, 400);
    }

    function cerrarSolicitud(toastId, idnotificacion) {
        const $toast = $('#' + toastId);
        $.post("controladores/traslado.php?op=marcarleida", { idnotificacion: idnotificacion }, function () {
            $toast.fadeOut(300, () => $toast.remove());
        });
    }

    function aceptarTraslado(idtraslado, toastId, idnotificacion) {
        Swal.fire({
            title: '¿Deseas aceptar este traslado?',
            text: 'Se ingresará al almacén y se registrará en kardex.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("controladores/traslado.php?op=aceptar", { idtraslado: idtraslado }, function (respuesta) {
                    if (respuesta.includes("correctamente")) {
                        Swal.fire('¡Aceptado!', respuesta, 'success');
                        if (idnotificacion) { $.post("controladores/traslado.php?op=marcarleida", { idnotificacion: idnotificacion }); }
                        $('#' + toastId).fadeOut(3000, () => $('#' + toastId).remove());
                        if (typeof tabla !== 'undefined') tabla.ajax.reload();
                    } else {
                        Swal.fire('Error', respuesta, 'error');
                    }
                });
            }
        });
    }

    setInterval(verificarNuevasNotificaciones, 5000);
    verificarNuevasNotificaciones();

    // Notificaciones Cuentas por Cobrar
    function cargarNotificacionesCXCNavbar() {
        let sucursal = $("#idsucursal2").val();
        if (!sucursal || sucursal === "") return;

        $.getJSON("controladores/cuentascobrar.php?op=obtener_notificaciones&idsucursal=" + sucursal, function (data) {
            let cuotas = data.filter(n => !n.tipo || n.tipo.trim() === "");
            let total = cuotas.length;
            let html = "";
            let ids = [];

            if (total === 0) {
                $(".cxcAlertCount").hide();
                html = `<span class="dropdown-item text-muted">No hay cuentas vencidas</span>`;
            } else {
                $(".cxcAlertCount").text(total).show();
                cuotas.forEach(n => {
                    ids.push(n.idnotificacion);
                    html += `<a href="#" class="dropdown-item"><i class="fas fa-exclamation-triangle text-danger mr-2"></i> ${n.mensaje} <span class="float-right text-muted text-sm">${n.fecha}</span></a><div class="dropdown-divider"></div>`;
                });
            }
            $(".cxcAlertList").html(html);
            $(".cxcAlertLink").data("ids", ids.join(","));
        });
    }

    $(document).on("change", "#idsucursal2", function () { cargarNotificacionesCXCNavbar(); });
    $(document).on("click", ".cxcAlertLink", function () {
        let ids = $(this).data("ids");
        if (!ids) return;
        $.post("controladores/cuentascobrar.php?op=marcar_leida", { ids: ids }, function () { $(".cxcAlertCount").hide(); });
    });

    $(document).ready(function () {
        let esperaSucursal = setInterval(function () {
            let sucursal = $("#idsucursal2").val();
            if (sucursal && sucursal !== "") {
                cargarNotificacionesCXCNavbar();
                clearInterval(esperaSucursal);
            }
        }, 300);
        setInterval(cargarNotificacionesCXCNavbar, 5000);
    });

    function notificacionToast(tipo, mensaje) {

        switch (tipo) {

            case 'success':
                toastr.success(mensaje);
                break;

            case 'error':
                toastr.error(mensaje);
                break;

            case 'warning':
                toastr.warning(mensaje);
                break;

            default:
                toastr.info(mensaje);
        }
    }
</script>

<nav class="main-header navbar navbar-expand navbar-white navbar-light sticky-top" id="navbar-global">

    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link navbar-icon-link" data-widget="pushmenu" role="button"><i
                    class="fas fa-bars fa-lg"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">

        <li class="nav-item">
            <a class="nav-link navbar-icon-link" data-widget="fullscreen" role="button" title="Pantalla Completa">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        <li class="nav-item dropdown mr-3">
            <a class="nav-link cxcAlertLink position-relative navbar-icon-link" data-toggle="dropdown" href="#"
                title="Cuentas por Cobrar">
                <i class="fas fa-file-invoice-dollar fa-lg"></i>
                <span class="badge badge-danger navbar-badge-custom cxcAlertCount" style="display:none;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-lg border-0 rounded-lg mt-2">
                <span class="dropdown-header font-weight-bold bg-light rounded-top py-3">Cuentas por Cobrar</span>
                <div class="dropdown-item p-3">
                    <div class="cxcAlertList" style="max-height:300px; overflow-y:auto;"></div>
                </div>
                <div class="dropdown-divider m-0"></div>
                <a href="cuentas-cobrar" class="dropdown-item dropdown-footer text-primary font-weight-bold py-3">
                    Ver todas las cuentas <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </li>

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
                    <a href="salirsucursal"
                        class="btn btn-outline-success btn-block font-weight-bold border-0 text-left px-3 py-2">
                        <i class="fas fa-box"></i>
                        Cambiar sucursal
                    </a>
                    <a href="salir"
                        class="btn btn-outline-danger btn-block font-weight-bold border-0 text-left px-3 py-2">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</nav>