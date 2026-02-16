<style>
    /* Separador vertical sutil */
    .navbar-divider {
        width: 1px;
        height: 24px;
        background-color: #dee2e6;
        margin: 0 12px;
        display: none; /* Oculto en móvil */
    }
    @media (min-width: 576px) {
        .navbar-divider { display: inline-block; vertical-align: middle; }
    }

    /* Contenedor "Pastilla" del Perfil */
    .user-profile-link {
        display: flex;
        align-items: center;
        padding: 4px 12px !important;
        border-radius: 50px; /* Bordes redondeados */
        transition: all 0.2s ease-in-out;
        border: 1px solid transparent;
        margin-left: 5px;
    }

    /* Efecto al pasar el mouse (Hover) */
    .user-profile-link:hover, .user-menu.show .user-profile-link {
        background-color: #f8f9fa;
        border-color: #e9ecef;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Círculo para el avatar/icono */
    .user-avatar-circle {
        width: 35px;
        height: 35px;
        background-color: #e2e6ea;
        color: #6c757d;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
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
        font-size: .6rem;
        font-weight: 300;
        padding: 2px 4px;
        position: absolute;
        right: 5px;
        top: 7px;
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
    }, 3000);

    function loadSesionsApp() {
        $.ajax({
            url: "controladores/negocio.php?op=sesions",
            type: "GET",
            contentType: false,
            processData: false,
            success: function(datos) {
                var data = JSON.parse(datos);
                if (!data.status) { sessionExpired(); }
            },
            error: function(jqXHR, textStatus, errorThrown) { console.error("Error sesión:", textStatus); }
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
            success: function(response){
               if (response && response.total > 0) {
                  toastr.warning("Tienes " + response.total + " comprobante(s) sin enviar a SUNAT", "Pendientes de Envío");
               }
            },
            error: function(err) { console.error("Error comprobantes:", err); }
        });
    }
    setInterval(checkComprobantesPendientes, 600000);
    checkComprobantesPendientes();

    // ==================== Notificaciones de Traslados ====================
    let notificacionesMostradas = new Set();

    function verificarNuevasNotificaciones() {
        if (currentSucursal <= 0) return;
        $.getJSON("controladores/traslado.php?op=listarnoti&idsucursal=" + currentSucursal, function(data) {
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
        $toast.find('.toast-close').on('click', function(e) { e.stopPropagation(); cerrarToast($toast, idnotificacion, tipo); });
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
        $.post("controladores/traslado.php?op=marcarleida", { idnotificacion: idnotificacion }, function() {
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
            if(result.isConfirmed){
                $.post("controladores/traslado.php?op=aceptar", { idtraslado: idtraslado }, function(respuesta){
                    if(respuesta.includes("correctamente")){
                        Swal.fire('¡Aceptado!', respuesta, 'success');
                        if (idnotificacion) { $.post("controladores/traslado.php?op=marcarleida", { idnotificacion: idnotificacion }); }
                        $('#' + toastId).fadeOut(300, () => $('#' + toastId).remove());
                        if(typeof tabla !== 'undefined') tabla.ajax.reload();
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
</script>

<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0 shadow-sm" id="navbar-global">
    
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">

        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" role="button" title="Pantalla Completa">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link cxcAlertLink" data-toggle="dropdown" href="#" title="Cuentas por Cobrar">
                <i class="fas fa-file-invoice-dollar"></i>
                <span class="badge badge-danger navbar-badge-custom cxcAlertCount" style="display:none;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0 border-0 shadow-lg">
                <span class="dropdown-header font-weight-bold text-secondary">Cuentas por Cobrar</span>
                <div class="dropdown-divider"></div>
                <div class="dropdown-item p-0">
                    <div class="cxcAlertList" style="max-height:300px; overflow-y:auto;"></div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="cuentas-cobrar" class="dropdown-item dropdown-footer text-primary font-weight-bold">Ver todas</a>
            </div>
        </li>

        <div class="navbar-divider"></div>

        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link user-profile-link" data-toggle="dropdown">
                <div class="user-avatar-circle">
                    <i class="fas fa-user"></i>
                </div>
                <div class="d-none d-md-block" style="line-height: 1.1; text-align: left;">
                    <span class="d-block font-weight-bold text-dark" style="font-size: 0.9rem;">
                        <?php echo $_SESSION['nombre']; ?>
                    </span>
                    <span class="d-block text-muted" style="font-size: 0.75rem;">
                        <?php echo isset($_SESSION['nombre_negocio']) ? $_SESSION['nombre_negocio'] : 'Sistema'; ?>
                    </span>
                </div>
                <i class="fas fa-chevron-down user-chevron"></i>
            </a>

            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-0 shadow-lg mt-2">
                <li class="user-header bg-primary text-white">
                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                         <i class="fas fa-user-tie fa-2x text-primary"></i>
                    </div>
                    <p class="mb-0 font-weight-bold">
                        <?php echo $_SESSION['nombre']; ?>
                    </p>
                    <small style="opacity: 0.8;"><?php echo $_SESSION['cargo']; ?></small>
                </li>
                
                <li class="user-footer bg-light">
                    <a href="salir" class="btn btn-default btn-flat float-right btn-block text-danger font-weight-bold">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</nav>