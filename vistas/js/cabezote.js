let archivosSeleccionadosGeneral = [];

$(function () {
    const $modal = $("#modalImportarProductos");
    if (!$modal.length) {
        return;
    }

    $modal.on("show.bs.modal", function () {
        const $sucursal = $("#sucursalImportacionJson");
        $sucursal.prop("disabled", true).html('<option value="">Cargando sucursales...</option>');
        $("#btnImportarProductosJson").prop("disabled", true);

        $.ajax({
            url: "controladores/venta.php?op=selectSucursal",
            type: "POST",
            success: function (opciones) {
                if (!opciones.trim()) {
                    $sucursal.html('<option value="">No hay sucursales disponibles</option>');
                    Swal.fire("Error", "No hay sucursales disponibles para importar productos.", "error");
                    return;
                }

                $sucursal.html('<option value="">Seleccione una sucursal</option>' + opciones);
                $sucursal.prop("disabled", false);
                $("#btnImportarProductosJson").prop("disabled", false);
            },
            error: function () {
                $sucursal.html('<option value="">No se pudieron cargar las sucursales</option>');
                Swal.fire("Error", "No se pudieron cargar las sucursales.", "error");
            },
        });
    });

    $("#formImportarProductosJson").on("submit", function (event) {
        event.preventDefault();

        const archivo = $("#archivoProductosJson")[0].files[0];
        const idsucursal = $("#sucursalImportacionJson").val();
        const nombreSucursal = $("#sucursalImportacionJson option:selected").text();

        if (!idsucursal || !archivo) {
            Swal.fire("Datos incompletos", "Seleccione una sucursal y un archivo JSON.", "warning");
            return;
        }

        if (!archivo.name.toLowerCase().endsWith(".json")) {
            Swal.fire("Archivo inválido", "Seleccione un archivo con extensión .json.", "error");
            return;
        }

        Swal.fire({
            title: "¿Importar productos?",
            text: `Se cargarán los productos de "${archivo.name}" en ${nombreSucursal}.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Importar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            const formData = new FormData();
            formData.append("archivo", archivo);
            const $boton = $("#btnImportarProductosJson").prop("disabled", true);

            $.ajax({
                url: `controladores/producto.php?op=cargar_data&idsucursal=${encodeURIComponent(idsucursal)}`,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (data) {
                    Swal.fire({
                        title: data.success ? "Importación completada" : "Error",
                        icon: data.success ? "success" : "error",
                        text: data.success
                            ? `${data.message}. Productos: ${data.productos}; series: ${data.series}.`
                            : data.message,
                    });
                    if (data.success) {
                        $modal.modal("hide");
                        $("#formImportarProductosJson")[0].reset();
                        if (typeof listarProductos !== "undefined" && listarProductos && typeof listarProductos.load === "function") {
                            listarProductos.load();
                        }
                    }
                },
                error: function (xhr) {
                    const mensaje = xhr.responseJSON?.error || xhr.responseJSON?.message || "No se pudo importar el archivo JSON.";
                    Swal.fire("Error al importar", mensaje, "error");
                },
                complete: function () {
                    $boton.prop("disabled", false);
                },
            });
        });
    });
});

function listarEventos(idpersonal) {
    var calendarEl = document.getElementById('calendarGeneral');

    let condicion = '';
    if (idpersonal && idpersonal != undefined) {
        condicion = '&idpersonal=' + idpersonal
    }
    calendar = new FullCalendar.Calendar(calendarEl, {

        locale: 'es',

        initialView: 'dayGridMonth',

        navLinks: true,

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        events: 'controladores/empleado.php?op=eventosCalendario' + condicion,

        // Clic en un día vacío
        dateClick: function (info) {
            $("#modalProgramarVisita").modal("show");
            // $("#formProgramarVisita")[0].reset();
            archivosSeleccionadosGeneral = [];
            $("#fecha_programada").val(info.dateStr + "T08:00");
            listarDatosExtra(null);
        },

        // Clic en un evento
        eventClick: function (info) {

            let e = info.event;

            Swal.fire({
                title: e.title,
                icon: 'info',
                showConfirmButton: true,
                showDenyButton: false,
                confirmButtonText: '<i class="fa fa-eye"></i> Ver',
                confirmButtonColor: '#17a2b8'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.post(
                        "controladores/cuentascobrar.php?op=mostrarSeguimiento",
                        { idseguimiento: e.extendedProps.idseguimiento },
                        function (response) {
                            verSeguimiento(response);
                        }
                    );

                }

            });

        },

        // Clic en el número del día
        navLinkDayClick: function (date) {

            calendar.changeView(
                'timeGridDay',
                date
            );

        }

    });

    setTimeout(function () {

        calendar.render();

    }, 300);
}


function verEventos() {
    $("#myModalEventosGeneral").modal('show');
    listarEventos();
}

function verSeguimiento(data) {
    console.log(data.cliente);
    $("#ver_cliente_general").html(data.cliente || '-');
    $("#ver_tipo_general").html(data.tipo || '-');
    $("#ver_estado_general").html(data.estado || '-');
    $("#ver_responsable_general").html(data.personal || '-');
    $("#ver_prioridad_general").html(data.prioridad || '-');

    $("#ver_cuota_general").html(
        data.numero_cuota
            ? 'Cuota ' + data.numero_cuota + ' del comp.: ' + data.serie_comprobante + '-' + data.numero_comprobante
            : '-'
    );

    $("#ver_fecha_programada_general").html(data.fecha_proxima || '-');
    $("#ver_fecha_final_general").html(data.fecha_final || '-');
    $("#ver_direccion_general").html(data.direccion || '-');
    $("#ver_descripcion_general").html(data.descripcion || '-');

    let htmlAdjuntos = '';
    const adjuntos = data.adjuntos;
    if (adjuntos && adjuntos.length > 0) {

        adjuntos.forEach(function (item) {

            htmlAdjuntos += `
                <a href="files/seguimientos/${item.archivo}"
                   target="_blank"
                   class="btn btn-outline-primary btn-sm m-1">

                    <i class="fa fa-paperclip"></i>
                    ${item.nombre_original}

                </a>
            `;
        });
    } else {
        htmlAdjuntos = `
            <div class="text-muted">
                No existen adjuntos
            </div>
        `;
    }

    $("#ver_adjuntos_general").html(htmlAdjuntos);

    $("#modalVerSeguimientoGeneral").modal("show");
}

function listarDatosExtra(id) {
    $.post("controladores/usuario.php?op=selectEmpleado", function (r) {
        $("#idpersonal_edit").html(r);
        $("#idpersonal_edit").select2();
        if (id) {
            $("#idpersonal_edit")
                .val(id)
                .trigger("change");
        }
    });
}

function editarSeguimiento(data) {
    listarDatosExtra(data.idpersonal);
    $("#id_visita").val(data.idseguimiento);
    $("#idcpc_visita").val(data.idcpc);
    $("#idventa_visita").val(data.idventa);
    $("#idcliente_visita").val(data.idcliente);
    $("#tipo_visita").val(data.tipo);
    $("#prioridad").val(data.prioridad);
    $("#estado").val(data.estado);
    $("#fecha_programada").val(data.fecha_proxima);
    $("#fecha_final").val(data.fecha_final);
    $("#direccion_edit").val(data.direccion);
    $("#descripcion").val(data.descripcion);
    $("#modalProgramarVisita").modal("show");
    if (data.archivos) {
        if (typeof data.archivos === 'string') {
            archivosSeleccionadosGeneral = JSON.parse(data.archivos);
        } else {
            archivosSeleccionadosGeneral = data.archivos;
        }

    }
    setTimeout(function () {
        renderArchivos();
    }, 300);


}

function renderArchivos() {

    $("#previewArchivos").html("");

    archivosSeleccionadosGeneral.forEach((archivo, index) => {

        let nombre = '';
        let size = '';
        let icono = "fa-file";

        // Archivo nuevo (File)
        if (archivo instanceof File) {

            nombre = archivo.name;
            size = (archivo.size / 1024 / 1024).toFixed(2) + ' MB';

        } else {

            nombre = archivo.nombre_original || archivo.archivo;
            size = 'Archivo guardado';

        }

        if (nombre.match(/\.(jpg|jpeg|png|webp)$/i)) {
            icono = "fa-file-image text-primary";
        }
        else if (nombre.match(/\.(pdf)$/i)) {
            icono = "fa-file-pdf text-danger";
        }
        else if (nombre.match(/\.(doc|docx)$/i)) {
            icono = "fa-file-word text-info";
        }
        else if (nombre.match(/\.(xls|xlsx)$/i)) {
            icono = "fa-file-excel text-success";
        }
        else if (nombre.match(/\.(mp4)$/i)) {
            icono = "fa-file-video text-warning";
        }

        $("#previewArchivos").append(`
            <div class="col-md-12 archivo-item-${index}">
                <div class="preview-item">

                    <div class="preview-left">

                        <i class="fas ${icono}"></i>

                        <div>

                            <div style="font-size:13px;font-weight:600;">
                                ${nombre}
                            </div>

                            <small class="text-muted">
                                ${size}
                            </small>

                        </div>

                    </div>

                    <div>

                        ${archivo.archivo ? `
                            <a
                                href="files/seguimientos/${archivo.archivo}"
                                target="_blank"
                                class="btn btn-sm btn-info mr-1">
                                <i class="fa fa-eye"></i>
                            </a>
                        ` : ''}

                        <button
                            type="button"
                            class="btn-delete-file"
                            onclick="eliminarArchivo(${index})">

                            <i class="fas fa-times"></i>

                        </button>

                    </div>

                </div>

            </div>
        `);

    });

}