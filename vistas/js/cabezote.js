let archivosSeleccionadosGeneral = [];

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
            $("#formProgramarVisita")[0].reset();
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
                function (r) {

                    let data = JSON.parse(r);

                    verSeguimiento(data);

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

    $("#ver_cliente").html(data.cliente || '-');
    $("#ver_tipo").html(data.tipo || '-');
    $("#ver_estado").html(data.estado || '-');
    $("#ver_responsable").html(data.personal || '-');
    $("#ver_prioridad").html(data.prioridad || '-');

    $("#ver_cuota").html(
        data.numero_cuota
            ? 'Cuota ' + data.numero_cuota + ' del comp.: ' + data.serie_comprobante + '-' + data.numero_comprobante
            : '-'
    );

    $("#ver_fecha_programada").html(data.fecha_proxima || '-');
    $("#ver_fecha_final").html(data.fecha_final || '-');
    $("#ver_direccion").html(data.direccion || '-');
    $("#ver_descripcion").html(data.descripcion || '-');

    let htmlAdjuntos = '';
    var adjuntos = JSON.parse(data.adjuntos);
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

    $("#ver_adjuntos").html(htmlAdjuntos);

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