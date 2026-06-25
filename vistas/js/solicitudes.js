$('#navPosActive').addClass("treeview active");
$('#navPos').addClass("treeview menu-open");
$('#navSolicitudes').addClass("active");

var tablaSolicitudes;
var pasoSeleccionado = null;
var pasoActualSolicitud = null;
var solicitudActual = null;
let archivos = [];

function init() {

    listarSolicitudes();

    $("#filtroEstado, #filtroRiesgo, #filtroPaso").change(function () {
        tablaSolicitudes.ajax.reload();
    });

    $("#filtroTexto").keyup(function () {
        tablaSolicitudes.ajax.reload();
    });

    $("#formSolicitud").submit(function (e) {
        guardarSolicitud(e);
    });

    $.post("controladores/venta.php?op=selectCliente", { only_client: true }, function (r) {
        $("#idcliente").html(r);
        $('#idcliente').select2('');
    });

}

function listarSolicitudes() {

    tablaSolicitudes = $("#tblSolicitudes").DataTable({

        processing: true,
        serverSide: true,

        responsive: true,
        autoWidth: false,

        ajax: {
            url: "controladores/solicitudes.php?op=listarSolicitudes",
            type: "GET",

            data: function (d) {

                d.estado = $("#filtroEstado").val();
                d.riesgo = $("#filtroRiesgo").val();
                d.paso = $("#filtroPaso").val();
                d.texto = $("#filtroTexto").val();

            },

            dataType: "json",

            error: function (e) {

                console.log(e.responseText);

            }
        },

        destroy: true,

        pageLength: 10,

        order: [[7, "desc"]],

        language: {

            processing:
                "<img src='files/plantilla/loading-page.gif' width='60'>"

        }

    });

}

function nuevaSolicitud() {

    $("#formSolicitud")[0].reset();

    $("#modalSolicitud").modal("show");

}

function guardarSolicitud(e) {

    e.preventDefault();

    var formData = new FormData(
        $("#formSolicitud")[0]
    );

    $.ajax({

        url: "controladores/solicitudes.php?op=guardar",

        type: "POST",

        data: formData,

        contentType: false,

        processData: false,

        success: function (r) {

            let data = JSON.parse(r);

            if (!data.status) {

                Swal.fire(
                    "Error",
                    data.msg,
                    "error"
                );

                return;
            }

            Swal.fire(
                "Correcto",
                data.msg,
                "success"
            );

            $("#modalSolicitud").modal("hide");

            tablaSolicitudes.ajax.reload();

            cargarKPIs();

        }

    });

}

function verSolicitud(idsolicitud) {

    $.getJSON(
        "controladores/solicitudes.php?op=mostrarSolicitud&idsolicitud=" + idsolicitud,
        function (data) {

            let currentStep = parseInt(data.paso_actual) || 1;
            let actionButtons = '';

            actionButtons += `
                <button class="btn btn-info btn-sm mr-2"
                    onclick="verWorkflow(${data.idsolicitud})">
                    Ver historial
                </button>`;

            if (data.estado === 'OBSERVADO') {
                actionButtons += `
                    <button class="btn btn-secondary btn-sm mr-2"
                        onclick="verArchivos(${data.idsolicitud})">
                        Ver documentos
                    </button>`;

                if (currentStep !== 2) {
                    actionButtons += `
                        <button class="btn btn-primary btn-sm mr-2"
                            onclick="avanzarPasoSolicitud(${data.idsolicitud}, 2)">
                            Regresar a documentación
                        </button>`;
                }
            }

            if (data.estado !== 'APROBADO' && data.estado !== 'RECHAZADO') {
                if (currentStep === 1) {
                    actionButtons += `
                        <button class="btn btn-primary btn-sm mr-2"
                            onclick="cargarDocumentacion(${data.idsolicitud})">
                            Pasar a documentación
                        </button>`;
                    actionButtons += `
                        <button class="btn btn-warning btn-sm"
                            onclick="observarSolicitud(${data.idsolicitud})">
                            Observar
                        </button>`;
                } else if (currentStep === 2) {
                    actionButtons += `
                        <button class="btn btn-success btn-sm mr-2"
                            onclick="aprobarDocumentacion(${data.idsolicitud})">
                            Aprobar documentación
                        </button>`;
                    actionButtons += `
                        <button class="btn btn-secondary btn-sm mr-2"
                            onclick="verArchivos(${data.idsolicitud})">
                            Ver documentos
                        </button>`;
                    actionButtons += `
                        <button class="btn btn-warning btn-sm"
                            onclick="observarSolicitud(${data.idsolicitud})">
                            Observar
                        </button>`;
                } else if (currentStep === 3) {
                    actionButtons += `
                        <button class="btn btn-primary btn-sm mr-2"
                            onclick="avanzarPasoSolicitud(${data.idsolicitud}, 4)">
                            Enviar a Comité
                        </button>`;
                    actionButtons += `
                        <button class="btn btn-warning btn-sm"
                            onclick="observarSolicitud(${data.idsolicitud})">
                            Observar
                        </button>`;
                } else if (currentStep === 4) {
                    actionButtons += `
                        <button class="btn btn-success btn-sm mr-2"
                            onclick="aprobarSolicitud(${data.idsolicitud})">
                            Aprobar final
                        </button>`;
                    actionButtons += `
                        <button class="btn btn-warning btn-sm"
                            onclick="observarSolicitud(${data.idsolicitud})">
                            Observar
                        </button>`;
                }
            }

            const steps = [
                { id: 1, label: 'Evaluación Inicial' },
                { id: 2, label: 'Validación Documentaria' },
                { id: 3, label: 'Verificación Domiciliaria' },
                { id: 4, label: 'Comité de Crédito' },
                { id: 5, label: 'Aprobación Final' }
            ];

            const selectedStep = currentStep;

            const backHint = currentStep > 1
                ? '<div class="text-muted small mb-2">Haga clic en un paso anterior para ver esa etapa. Esto no modificará el estado actual.</div>'
                : '';

            let stepHtml = `<div>${backHint}<div class="solicitud-progressbar mb-4">`;
            steps.forEach(function (step, index) {
                const isCompleted = step.id < currentStep;
                const isCurrent = step.id === currentStep;
                const isSelected = step.id === selectedStep;
                const isClickable = isCompleted;

                let itemClass = isCurrent ? 'step-item current' : isCompleted ? 'step-item completed' : 'step-item pending';
                if (isSelected && !isCurrent) {
                    itemClass += ' selected';
                }
                const markerClass = isCurrent ? 'step-marker current' : isCompleted ? 'step-marker completed' : 'step-marker pending';
                const labelClass = isCurrent ? 'step-label current' : isCompleted ? 'step-label completed' : 'step-label pending';
                const connectorClass = isCompleted ? 'step-connector completed' : 'step-connector pending';
                const actionAttr = isClickable ? ` onclick="verPaso(${data.idsolicitud}, ${step.id})" title="Ver ${step.label}" role="button" tabindex="0"` : '';
                const dataAttr = ` data-paso="${step.id}"`;

                stepHtml += `
                    <div class="${itemClass}${isClickable ? ' step-clickable' : ''}"${dataAttr}${actionAttr}>
                        <div class="${markerClass}">
                            ${isCompleted ? '<i class="fa fa-check"></i>' : step.id}
                        </div>
                        <div class="${labelClass}">${step.label}</div>
                    </div>`;

                if (index < steps.length - 1) {
                    stepHtml += `
                        <div class="${connectorClass}"></div>`;
                }
            });
            stepHtml += '</div></div>';

            $("#detalleSolicitud").html(`
                <div class="">
                    <div class="">
                        ${stepHtml}
                        <div id="stepViewer"></div>
                    </div>
                </div>
            `);

            solicitudActual = data;
            pasoActualSolicitud = currentStep;
            pasoSeleccionado = currentStep;
            mostrarPanelPasoSeleccionado(data.idsolicitud, currentStep);
            $("#modalDetalleSolicitud")
                .modal("show");

        }
    ).fail(function () {
        Swal.fire("Error", "No se pudo cargar la solicitud.", "error");
    });

}

function mostrarPanelPasoSeleccionado(idsolicitud, stepId) {
    pasoSeleccionado = stepId;
    $("#detalleSolicitud .solicitud-progressbar .step-item").removeClass("selected");
    $("#detalleSolicitud .solicitud-progressbar .step-item[data-paso='" + stepId + "']").addClass("selected");

    const steps = [
        { id: 1, label: 'Evaluación Inicial', description: 'Revisión inicial de score y datos básicos del cliente.' },
        { id: 2, label: 'Validación Documentaria', description: 'Comprobación y recepción de la documentación requerida.' },
        { id: 3, label: 'Verificación Domiciliaria', description: 'Comprobación de la dirección y la situación del cliente.' },
        { id: 4, label: 'Comité de Crédito', description: 'Evaluación de la operación por parte del comité interno.' },
        { id: 5, label: 'Aprobación Final', description: 'Decisión final y cierre del proceso de crédito.' }
    ];

    const step = steps.find(function (item) {
        return item.id === stepId;
    });

    if (!step) {
        return;
    }

    const currentStep = pasoActualSolicitud || stepId;
    const isCurrent = stepId === currentStep;
    let updateVerificacion = false;

    let note = '';
    // if (!isCurrent) {
    //     note = '<div class="alert alert-info p-2">Estás navegando una etapa anterior sin cambiar el estado actual de la solicitud.</div>';
    // }

    let stepDetailHtml = '';
    switch (stepId) {
        case 1:
            stepDetailHtml = `
                <form id="formPaso1_${idsolicitud}">
                    <div class="form-group">
                        <label>Cliente</label>
                        <input type="text" class="form-control" value="${solicitudActual ? solicitudActual.cliente : ''}" readonly>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Score</label>
                            <input type="text" class="form-control" value="${solicitudActual ? solicitudActual.score : ''}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Riesgo</label>
                            <input type="text" class="form-control" value="${solicitudActual ? solicitudActual.riesgo : ''}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Paso actual</label>
                            <input type="text" class="form-control" value="${solicitudActual ? solicitudActual.paso_actual_nombre || solicitudActual.paso_actual : ''}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Observaciones de evaluación</label>
                        <textarea class="form-control" id="observacion_evaluacion" rows="3" ${!isCurrent ? 'disabled' : ''}>${solicitudActual ? solicitudActual.observacion : ''}</textarea>
                    </div>
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
                        ${isCurrent ? '<button type="button" class="btn btn-primary btn-sm" onclick="cargarDocumentacion(' + idsolicitud + ')">Enviar a documentación</button>' : ''}
                    </div>
                </form>
            `;
            break;
        case 2:
            stepDetailHtml = `
                <form id="formPaso2_${idsolicitud}">
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea class="form-control" rows="3" readonly>Validación documentaria: carga y revisión de los archivos obligatorios.</textarea>
                    </div>
                    ${isCurrent ? `
                        <div class="form-group">
                            <label>Elegir documento</label>
                            <input type="file" class="form-control" id="archivoSolicitud_${idsolicitud}_step2">
                        </div>
                        <div class="form-group">
                            <label>Descripción del documento</label>
                            <textarea class="form-control" id="descripcionDocumento_${idsolicitud}" rows="2" placeholder="Ej: Cédula de identidad, recibo de servicios, etc."></textarea>
                        </div>
                        <button type="button" class="btn btn-info btn-sm mb-3" onclick="subirDocumentoPaso2(${idsolicitud})">Subir documento</button>
                        <button type="button" class="btn btn-success btn-sm mb-3" onclick="aprobarDocumentacion(${idsolicitud})">Aprobar documentación</button>
                    ` : ''}
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h6 class="mb-0">Documentos del paso</h6>
                        </div>
                        <div class="card-body" id="stepViewerDocs">
                            <div class="text-muted">Cargando documentos...</div>
                        </div>
                    </div>
                </form>
            `;
            setTimeout(function () {
                cargarDocumentos(idsolicitud);
            }, 100);
            break;
        case 3:
            const resultado = solicitudActual ? solicitudActual.resultado_verificacion || '' : '';
            let is_conform = false;
            if (["NO_CONFORME", "NO_UBICADO", "PENDIENTE"].includes(resultado)) {
                is_conform = true;
            }
            stepDetailHtml = `
                <form id="formPaso3_${idsolicitud}">
                    ${is_conform? '<button type="button" class="btn btn-info float-right" onclick="actualizarPaso3(' + idsolicitud + ')">Actualizar</button>':''}
                    <div class="form-group">
                        <label>Dirección registrada</label>
                        <input type="text" id="direccion_registrada_${idsolicitud}" class="form-control" ${!isCurrent || is_conform ? 'disabled' : ''} value="${solicitudActual ? solicitudActual.direccion || '' : ''}">
                    </div>
                    <div class="form-group">
                        <label>Resultado de verificación domiciliaria</label>     
                        <select id="resultadoVerificacion_${idsolicitud}"
                                class="form-control"
                                ${!isCurrent || is_conform ? 'disabled' : ''}>
                            <option value="">Seleccione...</option>

                            <option value="CONFORME"
                                ${resultado === 'CONFORME' ? 'selected' : ''}>
                                Conforme
                            </option>

                            <option value="NO_CONFORME"
                                ${resultado === 'NO_CONFORME' ? 'selected' : ''}>
                                No conforme
                            </option>

                            <option value="NO_UBICADO"
                                ${resultado === 'NO_UBICADO' ? 'selected' : ''}>
                                No ubicado
                            </option>

                            <option value="PENDIENTE"
                                ${resultado === 'PENDIENTE' ? 'selected' : ''}>
                                Pendiente
                            </option>

                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comentarios</label>
                        <textarea id="comentariosVerificacion_${idsolicitud}" class="form-control" rows="3" ${!isCurrent || is_conform ? 'disabled' : ''}>${solicitudActual ? solicitudActual.comentarios || '' : ''}</textarea>
                    </div>
                    ${(isCurrent && !is_conform) ? '<button type="button" class="btn btn-primary btn-sm float-right" onclick="registrarVerificacionDomiciliaria(' + idsolicitud + ')">Registrar verificación</button>' : ''}
                    ${is_conform ? '<button type="button" class="btn btn-primary btn-sm float-right" disabled id="btn_actualizar_verificacion_'+idsolicitud+'"  onclick="registrarVerificacionDomiciliaria(' + idsolicitud + ')">Actualizar verificación</button>' : ''}
                </form>
            `;
            break;
        case 4:
            stepDetailHtml = `
                <form id="formPaso4_${idsolicitud}">
                    <div class="form-group">
                        <label>Notas del comité</label>
                        <textarea id="notasComite_${idsolicitud}" class="form-control" rows="3" ${!isCurrent ? 'disabled' : ''}>${solicitudActual ? solicitudActual.notas_comite || '' : ''}</textarea>
                    </div>
                    <div class="text-right mt-3">
                        ${isCurrent ? '<button type="button" class="btn btn-default btn-sm float-right" onclick="actualizrSolicitudEstado(' + idsolicitud + ')">Observar</button>' : ''}
                        ${isCurrent ? '<button type="button" class="btn btn-success btn-sm float-right" onclick="enviarComiteAprobacion(' + idsolicitud + ')">Enviar a aprobación final</button>' : ''}
                    </div>
                </form>
            `;
            break;
        case 5:
            stepDetailHtml = `
                <form id="formPaso5_${idsolicitud}">
                    <div class="form-group">
                        <label>Estado final</label>
                        <input type="text" class="form-control" value="${solicitudActual ? solicitudActual.estado : ''}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Observación final</label>
                        <textarea class="form-control" rows="3" readonly>${solicitudActual ? solicitudActual.observacion || '' : ''}</textarea>
                    </div>
                </form>
            `;
            break;
        default:
            stepDetailHtml = '<p>Detalle de paso no disponible.</p>';
    }


    const currentStepLabel = steps.find(function (item) {
        return item.id === currentStep;
    })?.label || 'Paso actual';

    const panelHtml = `
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Vista del paso seleccionado: ${step.label}</h5>
            </div>
            <div class="card-body">
                <p>${step.description}</p>
                ${stepDetailHtml}
                ${note}
               ${!isCurrent ? `
                <div class="alert alert-info mt-3">
                    El estado real de la solicitud sigue en <strong>${currentStepLabel}</strong>
                    <button
                        class="btn btn-sm btn-danger float-right"
                        onclick="mostrarPanelPasoSeleccionado(${idsolicitud}, ${currentStep})">
                        Volver a la etapa actual
                    </button>
                </div>
                ` : ''}
            </div>
        </div>`;

    $("#stepViewer").html(panelHtml);
    actualizarVistaDocumentosPaso(idsolicitud, stepId);
}

function actualizarVistaDocumentosPaso(idsolicitud, stepId) {
    if (stepId !== 2) {
        return;
    }

    const html = $("#documentosSolicitud_" + idsolicitud).html();
    $("#stepViewerDocs").html(html ? html : '<div class="text-muted">No hay documentos cargados.</div>');
}

function subirDocumentoPaso2(idsolicitud) {
    let input = document.getElementById('archivoSolicitud_' + idsolicitud + '_step2');
    let descripcion = document.getElementById('descripcionDocumento_' + idsolicitud).value.trim();

    if (!input || input.files.length === 0) {
        Swal.fire("Atención", "Seleccione un archivo para subir.", "warning");
        return;
    }

    let archivo = input.files[0];
    if (archivo.size > 30 * 1024 * 1024) {

        Swal.fire(
            "Archivo muy grande",
            "El tamaño máximo permitido es 30 MB.",
            "warning"
        );

        return;
    }

    let formData = new FormData();
    formData.append('archivo', archivo);
    formData.append('idsolicitud', idsolicitud);
    formData.append('tipo_documento', 'Documento de crédito');
    formData.append('descripcion', descripcion);

    $.ajax({
        url: "controladores/solicitudes.php?op=subirDocumento",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (r) {
            let data = JSON.parse(r);

            if (!data.status) {
                Swal.fire(
                    "Error",
                    data.msg,
                    "error"
                );
                return;
            }

            Swal.fire(
                "Correcto",
                data.msg,
                "success"
            );

            verSolicitud(idsolicitud);
        }
    });
}

function registrarVerificacionDomiciliaria(idsolicitud) {

    let resultado = $("#resultadoVerificacion_" + idsolicitud).val();
    let comentarios = $("#comentariosVerificacion_" + idsolicitud).val().trim();
    let direccion_registrada = $("#direccion_registrada_" + idsolicitud).val().trim();

    if (resultado == "") {

        Swal.fire(
            "Atención",
            "Seleccione el resultado de la verificación domiciliaria.",
            "warning"
        );

        return;
    }

    $.post(

        "controladores/solicitudes.php?op=verificacionDomiciliaria",

        {
            idsolicitud: idsolicitud,
            resultado: resultado,
            comentarios: comentarios,
            direccion_registrada: direccion_registrada
        },

        function (r) {

            let data = JSON.parse(r);

            if (!data.status) {

                Swal.fire(
                    "Error",
                    data.msg,
                    "error"
                );

                return;
            }

            Swal.fire(
                "Correcto",
                data.msg,
                "success"
            );

            $("#modalDetalleSolicitud").modal("hide");

            tablaSolicitudes.ajax.reload();

            cargarKPIs();

        }

    );

}

function enviarComiteAprobacion(idsolicitud) {
    const notas = document.getElementById('notasComite_' + idsolicitud).value.trim();
    const observacion = 'Enviado a aprobación final';

    $.post(
        "controladores/solicitudes.php?op=aprobarSolicitud",
        {
            idsolicitud: idsolicitud,
            observacion: observacion,
            notas_comite: notas

        },
        function (r) {
            let data = JSON.parse(r);

            if (!data.status) {
                Swal.fire(
                    "Error",
                    data.msg,
                    "error"
                );
                return;
            }

            Swal.fire(
                "Correcto",
                data.msg,
                "success"
            );

            verSolicitud(idsolicitud);
        }
    );
}

function verPaso(idsolicitud, idpaso) {
    mostrarPanelPasoSeleccionado(idsolicitud, idpaso);
}

function cargarDocumentos(idsolicitud) {

    $.post(
        "controladores/solicitudes.php?op=archivos",
        { idsolicitud: idsolicitud },
        function (r) {

            try {
                archivos = JSON.parse(r);
            } catch (e) {
                archivos = [];
            }

            let html = '';

            if (!archivos || archivos.length === 0) {
                html = '<div class="text-muted">No hay documentos cargados.</div>';
            } else {

                archivos.forEach(function (item) {

                    html += `
                        <div class="card mb-2">
                            <div class="card-body">
                                <a href="files/solicitudes/${item.archivo}" target="_blank">
                                    <i class="fa fa-file"></i>
                                    ${item.nombre_original}
                                </a>
                            </div>
                        </div>
                    `;
                });

            }

            $("#stepViewerDocs").html(html);

        }
    );
}

function actualizarPaso3(idsolicitud) {
    $("#direccion_registrada_" + idsolicitud).prop("disabled", false);
    $("#resultadoVerificacion_" + idsolicitud).prop("disabled", false);
    $("#comentariosVerificacion_" + idsolicitud).prop("disabled", false);
    $("#btn_actualizar_verificacion_" + idsolicitud).prop("disabled", false);
}

function cargarDocumentacion(idsolicitud) {

    $.post(
        "controladores/solicitudes.php?op=documentacion",
        {
            idsolicitud: idsolicitud,
            observacion: 'Documentación cargada',
            observacion_evaluacion: $("#observacion_evaluacion").val()
        },
        function (r) {
            let data = JSON.parse(r);

            if (!data.status) {
                Swal.fire(
                    "Error",
                    data.msg,
                    "error"
                );
                return;
            }

            Swal.fire(
                "Correcto",
                data.msg,
                "success"
            );

            $("#modalDetalleSolicitud").modal("hide");
            tablaSolicitudes.ajax.reload();
            cargarKPIs();
        }
    );

}

function aprobarDocumentacion(idsolicitud) {

    if (archivos.length == 0) {

        Swal.fire({
            title: 'No hay documentos',
            text: 'La solicitud no tiene documentos cargados. ¿Desea continuar de todas formas?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {

            if (result.isConfirmed) {
                ejecutarAprobacion(idsolicitud);
            }

        });

        return;
    }

    ejecutarAprobacion(idsolicitud);
}

function ejecutarAprobacion(idsolicitud) {

    $.post(
        "controladores/solicitudes.php?op=aprobarDocumentacion",
        {
            idsolicitud: idsolicitud,
            observacion: 'Documentación aprobada'
        },
        function (r) {

            let data = JSON.parse(r);

            if (!data.status) {
                Swal.fire("Error", data.msg, "error");
                return;
            }

            Swal.fire("Correcto", data.msg, "success");

            $("#modalDetalleSolicitud").modal("hide");
            tablaSolicitudes.ajax.reload();
            cargarKPIs();
        }
    );
}
function observarSolicitud(idsolicitud) {
    let observacion = prompt("Motivo de la observación:", "Solicitud observada");
    if (observacion === null) {
        return;
    }

    $.post(
        "controladores/solicitudes.php?op=observarSolicitud",
        {
            idsolicitud: idsolicitud,
            observacion: observacion
        },
        function (r) {
            let data = JSON.parse(r);

            if (!data.status) {
                Swal.fire(
                    "Error",
                    data.msg,
                    "error"
                );
                return;
            }

            Swal.fire(
                "Correcto",
                data.msg,
                "success"
            );

            $("#modalDetalleSolicitud").modal("hide");
            tablaSolicitudes.ajax.reload();
            cargarKPIs();
        }
    );

}

function subirDocumento(idsolicitud) {
    let input = document.getElementById('archivoSolicitud_' + idsolicitud);
    if (!input || input.files.length === 0) {
        Swal.fire("Atención", "Seleccione un archivo para subir.", "warning");
        return;
    }

    let formData = new FormData();
    formData.append('archivo', input.files[0]);
    formData.append('idsolicitud', idsolicitud);
    formData.append('tipo_documento', 'Documento de crédito');

    $.ajax({
        url: "controladores/solicitudes.php?op=subirDocumento",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (r) {
            let data = JSON.parse(r);

            if (!data.status) {
                Swal.fire(
                    "Error",
                    data.msg,
                    "error"
                );
                return;
            }

            Swal.fire(
                "Correcto",
                data.msg,
                "success"
            );

            verSolicitud(idsolicitud);
        }
    });
}

function avanzarPasoSolicitud(idsolicitud, idpaso, observacion) {
    observacion = observacion || 'Solicitud movida al paso ' + idpaso;

    $.post(
        "controladores/solicitudes.php?op=avanzarPaso",
        {
            idsolicitud: idsolicitud,
            idpaso: idpaso,
            observacion: observacion
        },
        function (r) {
            let data = JSON.parse(r);

            if (!data.status) {
                Swal.fire(
                    "Error",
                    data.msg,
                    "error"
                );
                return;
            }

            Swal.fire(
                "Correcto",
                data.msg,
                "success"
            );

            $("#modalDetalleSolicitud").modal("hide");
            tablaSolicitudes.ajax.reload();
            cargarKPIs();
        }
    );

}

function aprobarSolicitud(idsolicitud) {

    $.post(
        "controladores/solicitudes.php?op=aprobarSolicitud",
        {
            idsolicitud: idsolicitud,
            observacion: 'Solicitud aprobada'
        },
        function (r) {
            let data = JSON.parse(r);

            if (!data.status) {
                Swal.fire(
                    "Error",
                    data.msg,
                    "error"
                );
                return;
            }

            Swal.fire(
                "Correcto",
                data.msg,
                "success"
            );

            $("#modalDetalleSolicitud").modal("hide");
            tablaSolicitudes.ajax.reload();
            cargarKPIs();
        }
    );

}

function verWorkflow(idsolicitud) {

    $.post(
        "controladores/solicitudes.php?op=workflow",
        { idsolicitud: idsolicitud },
        function (r) {

            $("#timelineWorkflow").html(r);

            $("#modalWorkflow").modal("show");

        }
    );

}

function verArchivos(idsolicitud) {

    $.post(
        "controladores/solicitudes.php?op=archivos",
        { idsolicitud: idsolicitud },
        function (r) {

            let archivos = JSON.parse(r);

            let html = '';

            archivos.forEach(function (item) {

                html += `

                    <div class="card mb-2">

                        <div class="card-body">

                            <a
                                href="files/solicitudes/${item.archivo}"
                                target="_blank">

                                <i class="fa fa-file"></i>

                                ${item.nombre_original}

                            </a>
                            ${item.descripcion ? '<p class="text-muted small mt-2 mb-0"><strong>Descripción:</strong> ' + item.descripcion + '</p>' : ''}
                        </div>

                    </div>

                `;

            });

            $("#listaArchivos").html(html);

            $("#modalArchivos").modal("show");

        }
    );

}

function cargarKPIs() {

    $.get(
        "controladores/solicitudes.php?op=kpis",
        function (r) {

            let data = JSON.parse(r);

            $("#kpiTotalSolicitudes")
                .html(data.total);

            $("#kpiAprobados")
                .html(data.aprobados);

            $("#kpiObservados")
                .html(data.observados);

            $("#kpiRechazados")
                .html(data.rechazados);

        }
    );

}


function seleccionarCliente(idcliente) {
    if (!idcliente) {
        return;
    }
    $.get("controladores/cotizaciones.php?op=cotizacionesCliente", { idcliente: idcliente}, function (r) {
        $("#idcotizacion").html(r);
        $('#idcotizacion').select2('');
    });
}

init();