let estadoConexion = navigator.onLine;

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

function notificarConexion(conectado) {
    if (conectado) {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Conexión a Internet restablecida",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    } else {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "warning",
            title: "Sin conexión a Internet",
            text: "Verifica tu conexión.",
            showConfirmButton: false,
            allowOutsideClick: false
        });
    }
}

window.addEventListener("offline", function () {
    estadoConexion = false;
    notificarConexion(false);
});

window.addEventListener("online", function () {
    estadoConexion = true;
    notificarConexion(true);
});



// ==================== Comprobantes Pendientes ====================
function checkComprobantesPendientes() {

    let ultima = localStorage.getItem("notif_comprobantes_time");
    let ahora = Date.now();

    // Si aún no pasa 1 hora, no consultar
    if (ultima && (ahora - Number(ultima)) < 3600000) {
        return;
    }

    $.ajax({
        url: 'controladores/venta.php?op=comprobantesPendientes',
        type: 'GET',
        dataType: 'json',
        success: function (response) {

            if (response && response.total > 0) {

                toastr.warning(
                    "Tienes " + response.total + " comprobante(s) sin enviar a SUNAT",
                    "Pendientes de Envío", {
                    positionClass: "toast-top-center"
                }
                );
            }

            // Guardar la hora de la última consulta,
            // independientemente de si hubo pendientes o no.
            localStorage.setItem("notif_comprobantes_time", ahora);
        }
    });
}

// Ejecuta una vez
setInterval(checkComprobantesPendientes, 5000);



// ==================== Notificaciones de Traslados ====================
let notificacionesMostradas = new Set();

function verificarNuevasNotificaciones() {

    const ahora = Date.now();
    const ultima = localStorage.getItem("notif_traslados_time");

    // 1 hora = 3600000 ms
    if (ultima && (ahora - Number(ultima)) < 3600000) {
        return;
    }

    $.getJSON(
        "controladores/traslado.php?op=listarnoti",
        function (data) {

            // Guarda la hora de la consulta
            localStorage.setItem("notif_traslados_time", ahora);

            if (!data || data.length === 0) return;

            data.forEach(n => {

                if (!n.tipo || n.tipo.trim() === "") return;

                if (!notificacionesMostradas.has(n.idnotificacion) && n.leido == 0) {

                    let tipo = n.tipo.toLowerCase() === "traslado" ?
                        "traslado" :
                        "solicitud";

                    mostrarToast(
                        n.mensaje,
                        n.fecha,
                        n.idnotificacion,
                        n.idtraslado,
                        tipo,
                        n.iddestino,
                        n.currentSucursal
                    );

                    notificacionesMostradas.add(n.idnotificacion);
                }
            });
        }
    );
}

function mostrarToast(mensaje, fecha, idnotificacion = null, idtraslado = null, tipo = "solicitud", iddestino = null, currentSucursal = null) {
    const toastId = 'toast_' + Date.now();
    let titulo = "",
        icono = "",
        color = "",
        contenido = "",
        botonAccion = "";

    switch (tipo) {
        case "traslado":
            titulo = "Nueva notificación de traslado";
            icono = "fa-truck";
            color = "#28a745";
            botonAccion = (idtraslado && iddestino == currentSucursal) ?
                `<button class="btn btn-success btn-sm mt-2" onclick="aceptarTraslado(${idtraslado}, '${toastId}', ${idnotificacion})"><i class="fa fa-check"></i> Aceptar</button>` :
                "";
            break;
        case "solicitud":
            titulo = "Nueva solicitud pendiente";
            icono = "fa-bell";
            color = "#007bff";
            botonAccion =
                `<button class="btn btn-primary btn-sm mt-2" onclick="cerrarSolicitud('${toastId}', ${idnotificacion})"><i class="fa fa-times"></i> Cerrar</button>`;
            break;
        default:
            titulo = "Nueva notificación";
            icono = "fa-info-circle";
            color = "#6c757d";
    }

    contenido = `<div class="toast-body"><small style="color:#777;">${fecha}</small><br>${mensaje}${botonAccion}</div>`;
    const toastHTML =
        `<div id="${toastId}" class="toast-custom" style="border-left-color:${color}"><div class="toast-header" style="color:${color}"><div><i class="fa ${icono} toast-icon" style="margin-right:8px;"></i> ${titulo}</div><button class="toast-close" title="Cerrar">&times;</button></div>${contenido}</div>`;

    $("#toastContainer").append(toastHTML);
    const $toast = $('#' + toastId);
    $toast.find('.toast-close').on('click', function (e) {
        e.stopPropagation();
        cerrarToast($toast, idnotificacion, tipo);
    });
}

function cerrarToast($toast, idnotificacion, tipo) {
    $toast.css('animation', 'fadeOut 0.4s forwards');
    setTimeout(() => {
        if (tipo === "solicitud" && idnotificacion) {
            $.post("controladores/traslado.php?op=marcarleida", {
                idnotificacion: idnotificacion
            });
        }
        $toast.remove();
    }, 400);
}

function cerrarSolicitud(toastId, idnotificacion) {
    const $toast = $('#' + toastId);
    $.post("controladores/traslado.php?op=marcarleida", {
        idnotificacion: idnotificacion
    }, function () {
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
            $.post("controladores/traslado.php?op=aceptar", {
                idtraslado: idtraslado
            }, function (respuesta) {
                if (respuesta.includes("correctamente")) {
                    Swal.fire('¡Aceptado!', respuesta, 'success');
                    if (idnotificacion) {
                        $.post("controladores/traslado.php?op=marcarleida", {
                            idnotificacion: idnotificacion
                        });
                    }
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

// Notificaciones Cuentas por Cobrar
function cargarNotificacionesCXCNavbar() {
    const $lista = $(".cxcAlertList");
    const $contador = $(".cxcAlertCount");

    $lista.html(`
            <div class="cxc-loading">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="text-muted small mt-2">
                    Cargando cuentas...
                </div>
            </div>
        `);

    $.getJSON(
        "controladores/cuentascobrar.php?op=obtener_notificaciones",
        function (data) {
            const total = data.length;
            let html = "";
            let ids = [];

            if (total === 0) {
                $contador.hide();

                html = `
                    <div class="text-center py-4">
                        <div class="mb-2">
                            <i class="fas fa-check-circle text-success" style="font-size: 32px;"></i>
                        </div>
                        <div class="font-weight-bold text-dark">
                            Todo está al día
                        </div>
                        <small class="text-muted">
                            No hay cuentas vencidas
                        </small>
                    </div>
                `;
            } else {
                $contador.text(total).show();

                data.forEach(n => {
                    ids.push(n.idnotificacion);

                    const estado = n.dias_vencido > 0
                        ? `Vencida hace ${n.dias_vencido} día(s)`
                        : "Vence hoy";

                    const mensaje = n.dias_vencido > 0
                        ? `💸 ${n.cliente} tiene una cuota vencida hace ${n.dias_vencido} día(s) por S/ ${n.monto}`
                        : `⏰ ${n.cliente} tiene una cuota que vence HOY por S/ ${n.monto}`;

                    html += `
                        <a href="cuentas-cobrar"
                           class="d-flex align-items-start p-3 rounded-lg text-decoration-none cxc-alert-item">

                            <div class="mr-3">
                                <div class="d-flex align-items-center justify-content-center bg-danger-light text-danger rounded-circle"
                                     style="width: 38px; height: 38px;">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                            </div>

                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="font-weight-bold text-dark mb-1">
                                    ${mensaje}
                                </div>

                                <div class="d-flex align-items-center">
                                    <small class="text-danger mr-2">
                                        <i class="far fa-clock mr-1"></i>
                                        ${estado}
                                    </small>

                                    <small class="text-muted">
                                        ${n.fecha}
                                    </small>
                                </div>
                            </div>

                            <div class="ml-2 text-muted">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    `;
                });
            }

            $lista.html(html);
            $(".cxcAlertLink").data("ids", ids.join(","));
        }
    ).fail(function () {
        $contador.hide();

        $lista.html(`
            <div class="text-center py-4">
                <div class="mb-2">
                    <i class="fas fa-exclamation-circle text-danger" style="font-size: 30px;"></i>
                </div>
                <div class="font-weight-bold text-dark">
                    No se pudieron cargar las cuentas
                </div>
                <small class="text-muted">
                    Intenta nuevamente
                </small>
            </div>
        `);
    });
}

function obtenerProductosStockBajo() {

    $.get("controladores/producto.php?op=listarStockBajoAlert", function (response) {
        let productos = response;
        let cantidadBaja = productos.length; // La cantidad de productos con stock bajo (0, 1, 2 o 3)

        // Actualizar el contador de productos en el navbar
        $('#stockAlertCount').text(cantidadBaja); // Actualiza el contador
        $('#stockAlertCount2').text(cantidadBaja); // Actualiza el contador

        const stockAlertTableBody = document.getElementById('stockAlertTableBody');

        stockAlertTableBody.innerHTML = productos.map(producto => `
            <div class="stock-alert-item px-3 py-2">
                <div class="d-flex align-items-center">
                    <div class="stock-product-icon mr-3">
                        <i class="fas fa-box"></i>
                    </div>

                    <div class="flex-grow-1 min-width-0">
                        <div class="stock-product-name text-truncate">
                            ${producto.nombre}
                        </div>
                        <small class="text-muted">
                            ${producto.categoria || 'Sin categoría'}
                        </small>
                    </div>

                    <div class="text-right ml-3">
                        <div class="stock-value text-warning font-weight-bold">
                            ${producto.stock}
                        </div>
                        <small class="text-muted">stock</small>
                    </div>
                </div>
            </div>
        `).join('');
        $("#stockAlertCount").show();
    });
}

$(document).on("click", ".cxcAlertLink", function () {
    let ids = $(this).data("ids");
    if (!ids) return;
    $.post("controladores/cuentascobrar.php?op=marcar_leida", {
        ids: ids
    }, function () {
        //$(".cxcAlertCount").hide();
    });
});

$(document).ready(function () {
    cargarNotificacionesCXCNavbar();
    obtenerProductosStockBajo();
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

function initDataSucursal() {
    $.get('controladores/configuracion.php', { 'op': 'dataSucursal' }, function (response) {
        $("#nombreSucursal").text(response.nombre);
        $("#monedaSucursal").text(response.moneda);
    })
}

initDataSucursal();