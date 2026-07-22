var tabla;
let listarCandidatos = null;
let listarPendientes = null;
let listarRecuperados = null;
let listarCompromisos = null;
let listarHistorial = null;
let archivosSeleccionados = [];

$(function () {
    listarCandidatos.load();
});

function initListarCandidatos() {
    listarCandidatos.load();
}

function initListarPendientes() {
    listarPendientes.load();
}

function initListarCompromisos() {
    listarCompromisos.load();
}

function initListarRecuperados() {
    listarRecuperados.load();
}

function initListarHistorial() {
    listarHistorial.load();
}


listarCandidatos = new FluentPaginator({
    url: "controladores/recuperacion.php?op=listarCandidatos",
    renderTabla: pintarCandidatos,
    tableBody: "#tbodyCandidatos",
    searchSelector: "#searchCandidatos",
    limitSelector: "#limitCandidatos",
    paginationId: "#paginationCandidatos",
});


listarPendientes = new FluentPaginator({
    url: "controladores/recuperacion.php?op=listarRecuperaciones",
    renderTabla: pintarPendientes,
    tableBody: "#tbodyPendientes",
    searchSelector: "#searchPendientes",
    limitSelector: "#limitPendientes",
    paginationId: "#paginationPendientes",
    extraParams: () => ({
        estado: 'PENDIENTE'
    })
});

listarCompromisos = new FluentPaginator({
    url: "controladores/recuperacion.php?op=listarCompromisos",
    renderTabla: pintarCompromisos,
    tableBody: "#tbodyCompromisos",
    searchSelector: "#searchCompromisos",
    limitSelector: "#limitCompromisos",
    paginationId: "#paginationCompromisos",
});

listarRecuperados = new FluentPaginator({
    url: "controladores/recuperacion.php?op=listarRecuperaciones",
    renderTabla: pintarRecuperados,
    tableBody: "#tbodyRecuperados",
    searchSelector: "#searchRecuperados",
    limitSelector: "#limitRecuperados",
    paginationId: "#paginationRecuperados",
    extraParams: () => ({
        estado: 'RECUPERADO'
    })
});

listarHistorial = new FluentPaginator({
    url: "controladores/recuperacion.php?op=listarRecuperaciones",
    renderTabla: pintarHistorial,
    tableBody: "#tbodyHistorial",
    searchSelector: "#searchHistorial",
    limitSelector: "#limitHistorial",
    paginationId: "#paginationHistorial",
});

function pintarCandidatos(data) {

    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbllistado_candidatos tbody").html(html);
        return;
    }

    data.forEach((row, index) => {

        let riesgo = "";

        if (row.nivel_riesgo == "CRITICO") {
            riesgo = '<span class="badge badge-danger">CRÍTICO</span>';
        }
        else if (row.nivel_riesgo == "ALTO") {
            riesgo = '<span class="badge badge-warning">ALTO</span>';
        }
        else if (row.nivel_riesgo == "MEDIO") {
            riesgo = '<span class="badge badge-info">MEDIO</span>';
        }
        else {
            riesgo = '<span class="badge badge-success">BAJO</span>';
        }


        html += `
            <tr>

                <td>${index + 1}</td>

                <td>${row.cliente}</td>

                <td>${row.num_documento}</td>

                <td>${row.vehiculo}</td>

                <td>${row.placa}</td>

                <td class="text-center">
                    ${row.cuotas_vencidas}
                </td>

                <td class="text-center">
                    ${row.dias_mora}
                </td>

                <td class="text-right">
                    ${parseFloat(row.deuda_vencida).toFixed(2)}
                </td>

                <td class="text-center">
                    ${riesgo}
                </td>

                <td class="text-center">

                    <button 
                        class="btn btn-sm btn-primary"
                        onclick="verCandidato(${row.idventa})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button 
                        class="btn btn-sm btn-danger"
                        onclick="enviarRecuperacion(${row.idventa}, ${row.idpersona}, ${row.idserie})">

                        <i class="fas fa-car-crash"></i>
                    </button>

                </td>

            </tr>
        `;
    });


    $("#tbllistado_candidatos tbody").html(html);
}


function verCandidato(idventa) {

    $.ajax({

        url: "controladores/recuperacion.php?op=verCandidato&idventa=" + idventa,
        type: "GET",
        dataType: "json",

        success: function (data) {
            if (!data.success) {
                alert(data.message);
                return;
            }

            let venta = data.venta;
            let cuentas = data.cuentas;
            let resumen = data.resumen;

            // =====================
            // DATOS DEL CRÉDITO
            // =====================
            $("#d_cliente").text(venta.cliente);
            $("#d_documento").text(venta.num_documento);
            $("#d_telefono").text(venta.telefono);
            $("#d_vehiculo").text(venta.vehiculo);
            $("#d_placa").text(venta.placa);
            $("#d_serie").text(venta.numero_serie);
            $("#d_motor").text(venta.numero_motor);
            $("#d_total").text(
                parseFloat(venta.total_venta)
                    .toFixed(2)
            );

            // =====================
            // RESUMEN
            // =====================
            $("#d_cuotas").text(
                resumen.total_cuotas
            );
            $("#d_saldo").text(
                parseFloat(resumen.saldo_pendiente)
                    .toFixed(2)
            );
            $("#d_mora").text(
                parseFloat(resumen.mora_total)
                    .toFixed(2)
            );
            $("#d_abonado").text(
                parseFloat(resumen.total_abonado)
                    .toFixed(2)
            );

            // =====================
            // CUENTAS POR COBRAR
            // =====================
            let html = "";
            cuentas.forEach(function (c, i) {
                let estado;
                if (c.estado_pago == 0) {
                    estado =
                        '<span class="badge badge-success">Pagado</span>';
                } else {
                    estado =
                        '<span class="badge badge-danger">Pendiente</span>';
                }
                html += `
                        <tr>
                        <td>
                        ${i + 1}
                        </td>
                        <td>
                        ${c.fechavencimiento}
                        </td>
                        <td class="text-center">
                        ${c.dias_vencido > 0
                        ?
                        `<span class="badge badge-danger">
                        ${c.dias_vencido} días vencido
                        </span>`
                        :
                        `<span class="badge badge-success">
                            Vigente
                            </span>`
                    }
                            </td>
                            <td class="text-right">
                            ${parseFloat(c.deudatotal).toFixed(2)}
                            </td>
                            <td class="text-right font-weight-bold">
                            ${parseFloat(c.deuda).toFixed(2)}
                            </td>
                            <td class="text-right">
                            ${parseFloat(c.abonototal).toFixed(2)}
                            </td>
                            <td class="text-center">
                            ${c.estado_pago == 0
                        ?
                        '<span class="badge badge-success">Pagado</span>'
                        :
                        '<span class="badge badge-danger">Pendiente</span>'

                    }
                </td>   
            </tr>

            `;
            });
            $("#tablaCuotas").html(html);
            $("#modalCandidato").modal("show");
        },
        error: function (e) {
            console.log(e.responseText);
        }

    });

}


function enviarRecuperacion(idventa, idpersona, idserie) {

    Swal.fire({

        title: "Enviar a recuperación",

        text: "¿Desea registrar este crédito en cartera de recuperación?",

        icon: "warning",

        showCancelButton: true,

        confirmButtonText: "Sí, enviar"

    }).then((result) => {


        if (result.isConfirmed) {


            $.ajax({

                url: "controladores/recuperacion.php?op=registrar",

                type: "POST",

                data: {
                    idventa: idventa,
                    idpersona: idpersona,
                    idserie: idserie
                },

                dataType: "json",

                success: function (resp) {


                    if (resp.success) {

                        Swal.fire(
                            "Registrado",
                            "El crédito fue enviado a recuperación",
                            "success"
                        );


                    } else {

                        Swal.fire(
                            "Aviso",
                            resp.message,
                            "warning"
                        );

                    }

                }

            });


        }


    });

}

function pintarRecuperados(data) {

    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbllistado_recuperados tbody").html(html);
        return;
    }

    data.forEach(function (row, i) {
        console.log(row);

        let riesgo = "";
        let estado = "";


        switch (row.nivel_riesgo) {

            case "CRITICO":
                riesgo = '<span class="badge badge-danger">CRÍTICO</span>';
                break;

            case "ALTO":
                riesgo = '<span class="badge badge-warning">ALTO</span>';
                break;

            case "MEDIO":
                riesgo = '<span class="badge badge-info">MEDIO</span>';
                break;

            default:
                riesgo = '<span class="badge badge-success">BAJO</span>';

        }



        switch (row.estado) {

            case "PENDIENTE":
                estado = '<span class="badge badge-secondary">Pendiente</span>';
                break;

            case "CONTACTADO":
                estado = '<span class="badge badge-primary">Contactado</span>';
                break;

            case "NEGOCIACION":
                estado = '<span class="badge badge-warning">Negociación</span>';
                break;

            case "VISITA_PROGRAMADA":
                estado = '<span class="badge badge-dark">Visita</span>';
                break;

            case "RECUPERADO":
                estado = '<span class="badge badge-success">Recuperado</span>';
                break;

            case "CERRADO":
                estado = '<span class="badge badge-danger">Cerrado</span>';
                break;

            default:
                estado = row.estado;

        }



        html += `

        <tr>

            <td>${i + 1}</td>

            <td>

                <strong>${row.cliente}</strong><br>

                <small class="text-muted">

                    ${row.num_documento}

                </small>

            </td>

            <td>

                ${row.vehiculo}<br>

                <small class="text-muted">

                    ${row.placa}

                </small>

            </td>

            <td class="text-center">

                ${row.dias_mora}

            </td>

            <td class="text-right">

                S/ ${parseFloat(row.deuda_vencida).toFixed(2)}

            </td>

            <td class="text-right text-danger">

                S/ ${parseFloat(row.mora).toFixed(2)}

            </td>

            <td class="text-center">

                ${riesgo}

            </td>

            <td class="text-center">

                ${estado}

            </td>

            <td>

                ${row.gestor ?? "-"}

            </td>

            <td class="text-center">

            <div class="btn-group">

                <button
                    class="btn btn-sm btn-info"
                    onclick="verRecuperacion(${row.idrecuperacion})"
                    title="Ver expediente">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

        </td>

        </tr>

        `;

    });


    $("#tbllistado_recuperados tbody").html(html);
}


function pintarHistorial(data) {

    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbllistado_historial tbody").html(html);
        return;
    }

    data.forEach(function (row, i) {
        let riesgo = "";
        let estado = "";


        switch (row.nivel_riesgo) {

            case "CRITICO":
                riesgo = '<span class="badge badge-danger">CRÍTICO</span>';
                break;

            case "ALTO":
                riesgo = '<span class="badge badge-warning">ALTO</span>';
                break;

            case "MEDIO":
                riesgo = '<span class="badge badge-info">MEDIO</span>';
                break;

            default:
                riesgo = '<span class="badge badge-success">BAJO</span>';

        }



        switch (row.estado) {

            case "PENDIENTE":
                estado = '<span class="badge badge-secondary">Pendiente</span>';
                break;

            case "CONTACTADO":
                estado = '<span class="badge badge-primary">Contactado</span>';
                break;

            case "NEGOCIACION":
                estado = '<span class="badge badge-warning">Negociación</span>';
                break;

            case "VISITA_PROGRAMADA":
                estado = '<span class="badge badge-dark">Visita</span>';
                break;

            case "RECUPERADO":
                estado = '<span class="badge badge-success">Recuperado</span>';
                break;

            case "CERRADO":
                estado = '<span class="badge badge-danger">Cerrado</span>';
                break;

            default:
                estado = row.estado;

        }



        html += `

        <tr>

            <td>${i + 1}</td>

            <td>

                <strong>${row.cliente}</strong><br>

                <small class="text-muted">

                    ${row.num_documento}

                </small>

            </td>

            <td>

                ${row.vehiculo}<br>

                <small class="text-muted">

                    ${row.placa}

                </small>

            </td>

            <td class="text-center">

                ${row.dias_mora}

            </td>

            <td class="text-right">

                S/ ${parseFloat(row.deuda_vencida).toFixed(2)}

            </td>

            <td class="text-right text-danger">

                S/ ${parseFloat(row.mora).toFixed(2)}

            </td>

            <td class="text-center">

                ${riesgo}

            </td>

            <td class="text-center">

                ${estado}

            </td>

            <td>

                ${row.gestor ?? "-"}

            </td>

            <td class="text-center">

            <div class="btn-group">

                <button
                    class="btn btn-sm btn-info"
                    onclick="verRecuperacion(${row.idrecuperacion})"
                    title="Ver expediente">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

        </td>

        </tr>

        `;

    });


    $("#tbllistado_historial tbody").html(html);
}


function pintarPendientes(data) {

    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbllistado_pendientes tbody").html(html);
        return;
    }

    data.forEach(function (row, i) {
        console.log(row);

        let riesgo = "";
        let estado = "";


        switch (row.nivel_riesgo) {

            case "CRITICO":
                riesgo = '<span class="badge badge-danger">CRÍTICO</span>';
                break;

            case "ALTO":
                riesgo = '<span class="badge badge-warning">ALTO</span>';
                break;

            case "MEDIO":
                riesgo = '<span class="badge badge-info">MEDIO</span>';
                break;

            default:
                riesgo = '<span class="badge badge-success">BAJO</span>';

        }



        switch (row.estado) {

            case "PENDIENTE":
                estado = '<span class="badge badge-secondary">Pendiente</span>';
                break;

            case "CONTACTADO":
                estado = '<span class="badge badge-primary">Contactado</span>';
                break;

            case "NEGOCIACION":
                estado = '<span class="badge badge-warning">Negociación</span>';
                break;

            case "VISITA_PROGRAMADA":
                estado = '<span class="badge badge-dark">Visita</span>';
                break;

            case "RECUPERADO":
                estado = '<span class="badge badge-success">Recuperado</span>';
                break;

            case "CERRADO":
                estado = '<span class="badge badge-danger">Cerrado</span>';
                break;

            default:
                estado = row.estado;

        }



        html += `

        <tr>

            <td>${i + 1}</td>

            <td>

                <strong>${row.cliente}</strong><br>

                <small class="text-muted">

                    ${row.num_documento}

                </small>

            </td>

            <td>

                ${row.vehiculo}<br>

                <small class="text-muted">

                    ${row.placa}

                </small>

            </td>

            <td class="text-center">

                ${row.dias_mora}

            </td>

            <td class="text-right">

                S/ ${parseFloat(row.deuda_vencida).toFixed(2)}

            </td>

            <td class="text-right text-danger">

                S/ ${parseFloat(row.mora).toFixed(2)}

            </td>

            <td class="text-center">

                ${riesgo}

            </td>

            <td class="text-center">

                ${estado}

            </td>

            <td>

                ${row.gestor ?? "-"}

            </td>

            <td class="text-center">

            <div class="btn-group">

                <button
                    class="btn btn-sm btn-info"
                    onclick="verRecuperacion(${row.idrecuperacion})"
                    title="Ver expediente">
                    <i class="fas fa-eye"></i>
                </button>
                <button
                    class="btn btn-sm btn-primary"
                    onclick="abrirModalEstado(
                        ${row.idrecuperacion},
                        '${row.estado}',
                        '${row.observacion ?? ""}'
                    )"
                    title="Actualizar estado">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button
                    class="btn btn-sm btn-warning"
                    onclick='adjuntarArchivo(
                        ${row.idrecuperacion},
                        ${row.idventa},
                        ${row.idpersona},
                        ${JSON.stringify(row.direccion ?? "")}
                    )'
                    title="Adjuntar documento">
                    <i class="fas fa-paperclip"></i>
                </button>
                <button
                    class="btn btn-sm btn-success"
                    onclick='nuevoSeguimiento(
                        ${row.idrecuperacion},
                        ${row.idventa},
                        ${row.idpersona},
                        ${JSON.stringify(row.direccion ?? "")}
                    )'
                    title="Registrar seguimiento">
                    <i class="fas fa-comments"></i>
                </button>

            </div>

        </td>

        </tr>

        `;

    });


    $("#tbllistado_pendientes tbody").html(html);

}

function pintarCompromisos(data) {

    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="9" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbllistado_compromisos tbody").html(html);
        return;
    }

    data.forEach(function (row, i) {

        let estado = "";

        switch (row.estado) {

            case "CUMPLIDO":
                estado = '<span class="badge badge-success">Cumplido</span>';
                break;

            case "VENCIDO":
                estado = '<span class="badge badge-danger">Vencido</span>';
                break;

            default:
                estado = '<span class="badge badge-warning">Pendiente</span>';

        }

        html += `

        <tr>

            <td>${i + 1}</td>

            <td>

                <strong>${row.cliente}</strong><br>

                <small class="text-muted">

                    ${row.num_documento}

                </small>

            </td>

            <td>

                ${row.vehiculo}<br>

                <small class="text-muted">

                    ${row.placa}

                </small>

            </td>

            <td>

                ${row.detalle || '-'}

            </td>

            <td>

                ${row.fecha_compromiso}

            </td>

            <td class="text-right">

                S/ ${parseFloat(row.monto).toFixed(2)}

            </td>

            <td class="text-right text-danger">

                S/ ${parseFloat(row.deuda).toFixed(2)}

            </td>

            <td class="text-center">

                ${estado}

            </td>

            <td>

                ${row.usuario}

            </td>

            <td class="text-center">

                <button
                    class="btn btn-sm btn-info"
                    onclick="verCompromiso(${row.idcompromiso_pago})"
                    title="Ver">

                    <i class="fas fa-eye"></i>

                </button>

                ${row.estado == "PENDIENTE" ? `

                    <button
                        class="btn btn-sm btn-success"
                        onclick="cumplirCompromiso(${row.idcompromiso_pago})"
                        title="Marcar como cumplido">

                        <i class="fas fa-check"></i>

                    </button>

                    <button
                        class="btn btn-sm btn-danger"
                        onclick="cancelarCompromiso(${row.idcompromiso_pago})"
                        title="Cancelar compromiso">

                        <i class="fas fa-times"></i>

                    </button>

                ` : ""}

            </td>

        </tr>

        `;

    });

    $("#tbllistado_compromisos tbody").html(html);

}

function cumplirCompromiso(id) {

    Swal.fire({

        title: "¿Confirmar?",

        text: "¿Desea marcar este compromiso como cumplido?",

        icon: "question",

        showCancelButton: true,

        confirmButtonText: "Sí"

    }).then((result) => {

        if (!result.isConfirmed) return;

        $.post(

            "controladores/recuperacion.php?op=cumplirCompromiso",

            {
                idcompromiso_pago: id
            },

            function (resp) {

                if (resp.success) {

                    Swal.fire(
                        "Correcto",
                        resp.message,
                        "success"
                    );

                    listarCompromisos.buscar();

                } else {

                    Swal.fire(
                        "Error",
                        resp.message,
                        "error"
                    );

                }

            },

            "json"

        );

    });

}


function cancelarCompromiso(id) {

    Swal.fire({

        title: "Cancelar compromiso",

        text: "¿Desea eliminar este compromiso?",

        icon: "warning",

        showCancelButton: true,

        confirmButtonText: "Sí"

    }).then((result) => {

        if (!result.isConfirmed) return;

        $.post(

            "controladores/recuperacion.php?op=eliminarCompromiso",

            {
                idcompromiso_pago: id
            },

            function (resp) {

                if (resp.success) {

                    Swal.fire(
                        "Correcto",
                        resp.message,
                        "success"
                    );

                    listarCompromisos.buscar();

                }

            },

            "json"

        );

    });

}

function verCompromiso(idcompromiso) {

    $.get(

        "controladores/recuperacion.php?op=verCompromiso",

        {
            idcompromiso_pago: idcompromiso
        },

        function (resp) {

            $("#v_cliente").text(resp.cliente);

            $("#v_documento").text(resp.num_documento);

            $("#v_vehiculo").text(resp.vehiculo);

            $("#v_placa").text(resp.placa);

            $("#v_usuario").text(resp.usuario);

            $("#v_fecha").text(resp.fecha_compromiso);

            $("#v_monto").text(
                "S/ " + parseFloat(resp.monto).toFixed(2)
            );

            $("#v_deuda").text(
                "S/ " + parseFloat(resp.deuda).toFixed(2)
            );

            $("#v_detalle").text(
                resp.detalle || "-"
            );

            $("#v_observacion").text(
                resp.observacion || "-"
            );

            $("#v_cumplimiento").text(
                resp.fecha_cumplimiento ?? "Pendiente"
            );

            let estado = "";

            switch (resp.estado) {

                case "CUMPLIDO":

                    estado = '<span class="badge badge-success">Cumplido</span>';

                    break;

                case "VENCIDO":

                    estado = '<span class="badge badge-danger">Vencido</span>';

                    break;

                default:

                    estado = '<span class="badge badge-warning">Pendiente</span>';

            }

            $("#v_estado").html(estado);

            $("#modalVerCompromiso").modal("show");

        },

        "json"

    );

}

function verRecuperacion(idrecuperacion) {

    $.get(

        "controladores/recuperacion.php?op=verRecuperacion",

        {
            idrecuperacion: idrecuperacion
        },

        function (resp) {

            if (!resp.success) {

                Swal.fire(
                    "Error",
                    resp.message,
                    "error"
                );

                return;

            }

            let d = resp.data;

            // Cliente
            $("#r_cliente").text(d.cliente);
            $("#r_documento").text(d.num_documento);
            $("#r_telefono").text(d.telefono);

            // Vehículo
            $("#r_vehiculo").text(d.vehiculo);
            $("#r_placa").text(d.placa);
            $("#r_serie").text(d.numero_serie);
            $("#r_motor").text(d.numero_motor);

            // Recuperación
            $("#r_estado").html(
                badgeEstadoRecuperacion(d.estado)
            );

            $("#r_riesgo").html(
                badgeRiesgo(d.nivel_riesgo)
            );

            $("#r_dias").text(d.dias_mora);

            $("#r_deuda").text(
                "S/ " +
                parseFloat(d.deuda_vencida).toFixed(2)
            );

            $("#r_mora").text(
                "S/ " +
                parseFloat(d.mora).toFixed(2)
            );

            $("#r_observacion").text(
                d.observacion || "-"
            );

            $("#r_gestor").text(
                d.gestor || "-"
            );

            pintarSeguimientos(
                resp.seguimientos
            );

            pintarCompromisosExpediente(
                resp.compromisos
            );

            pintarAdjuntos(
                resp.adjuntos
            );

            $("#modalRecuperacion").modal("show");

        },

        "json"

    );

}

function badgeEstadoRecuperacion(estado) {

    switch (estado) {

        case "CONTACTADO":
            return '<span class="badge badge-primary">Contactado</span>';

        case "NEGOCIACION":
            return '<span class="badge badge-warning">Negociación</span>';

        case "VISITA_PROGRAMADA":
            return '<span class="badge badge-dark">Visita</span>';

        case "RECUPERADO":
            return '<span class="badge badge-success">Recuperado</span>';

        case "CERRADO":
            return '<span class="badge badge-danger">Cerrado</span>';

        default:
            return '<span class="badge badge-secondary">Pendiente</span>';

    }

}

function badgeRiesgo(riesgo) {

    switch (riesgo) {

        case "CRITICO":
            return '<span class="badge badge-danger">CRÍTICO</span>';

        case "ALTO":
            return '<span class="badge badge-warning">ALTO</span>';

        case "MEDIO":
            return '<span class="badge badge-info">MEDIO</span>';

        default:
            return '<span class="badge badge-success">BAJO</span>';

    }

}

function pintarSeguimientos(data) {

    let html = "";

    if (data.length == 0) {

        html = `
            <tr>
                <td colspan="6" class="text-center text-muted">
                    No existen seguimientos registrados
                </td>
            </tr>
        `;

        $("#tblSeguimientos tbody").html(html);

        return;

    }

    data.forEach(function (row, i) {

        html += `

        <tr>

            <td>${i + 1}</td>

            <td>${row.fecha_registro}</td>
            <td>
            ${row.idcpc
                ? '<span class="badge badge-primary">Letra</span>'
                : '<span class="badge badge-warning">Recuperación</span>'
            }
            </td>
            <td>

                <span class="badge badge-info">

                    ${row.tipo}

                </span>

            </td>

            <td>${row.descripcion ?? "-"}</td>

            <td>${row.usuario}</td>

            <td>

                <span class="badge badge-secondary">

                    ${row.estado}

                </span>

            </td>

        </tr>

        `;

    });

    $("#tblSeguimientos tbody").html(html);

}

function pintarCompromisosExpediente(data) {

    let html = "";

    if (data.length == 0) {

        html = `
            <tr>
                <td colspan="6" class="text-center text-muted">
                    No existen compromisos
                </td>
            </tr>
        `;

        $("#tblCompromisos tbody").html(html);

        return;

    }

    data.forEach(function (row, i) {

        let estado = row.fecha_cumplimiento
            ? '<span class="badge badge-success">Cumplido</span>'
            : '<span class="badge badge-warning">Pendiente</span>';

        html += `

        <tr>

            <td>${i + 1}</td>

            <td>${row.fecha_compromiso}</td>

            <td>${row.detalle || '-'}</td>

            <td class="text-right">

                S/ ${parseFloat(row.monto).toFixed(2)}

            </td>

            <td>${estado}</td>

            <td>${row.usuario}</td>

        </tr>

        `;

    });

    $("#tblCompromisos tbody").html(html);

}

function pintarAdjuntos(data) {

    let html = "";

    if (data.length == 0) {

        html = `
            <tr>
                <td colspan="4" class="text-center text-muted">
                    No existen archivos adjuntos
                </td>
            </tr>
        `;

        $("#tblAdjuntos tbody").html(html);

        return;

    }

    data.forEach(function (row, i) {

        html += `

        <tr>

            <td>${i + 1}</td>

            <td>

                <i class="fas fa-file-alt text-primary mr-2"></i>

                ${row.nombre_original}

            </td>

            <td>${row.fecha_registro}</td>

            <td class="text-center">

                <a
                    href="files/seguimientos/${row.archivo}"
                    target="_blank"
                    class="btn btn-sm btn-primary">

                    <i class="fas fa-download"></i>

                </a>

            </td>

        </tr>

        `;

    });

    $("#tblAdjuntos tbody").html(html);

}


function nuevoSeguimiento(idrecuperacion, idventa, idcliente, direccion) {
    $("#idventa_visita").val(idventa);
    $("#idcliente_visita").val(idcliente);
    $("#idrecuperacion_visita").val(idrecuperacion);
    $("#direccion").val(direccion);
    $("#modalProgramarVisita").modal('show');
    $.get("controladores/usuario.php?op=selectEmpleado", function (r) {
        $("#idpersonal").html(r);
        $('#idpersonal').select2();
    });
}

$("#adjuntos").on("change", function (e) {
    let nuevosArchivos = Array.from(e.target.files);
    nuevosArchivos.forEach(file => {
        archivosSeleccionados.push(file);
    });
    renderArchivos();
    $("#adjuntos").val("");
});

function renderArchivos() {
    $("#previewArchivos").html("");
    archivosSeleccionados.forEach((archivo, index) => {
        let nombre = archivo.name;
        let size = (archivo.size / 1024 / 1024).toFixed(2);
        let icono = "fa-file";
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
        else if (nombre.match(/\.(mp3)$/i)) {
            icono = "fa-file-audio text-secondary";
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
                                ${size} MB
                            </small>

                        </div>

                    </div>

                    <button 
                        type="button"
                        class="btn-delete-file"
                        onclick="eliminarArchivo(${index})">

                        <i class="fas fa-times"></i>

                    </button>

                </div>

            </div>
        `);

    });

}

function eliminarArchivo(index) {
    archivosSeleccionados.splice(index, 1);
    renderArchivos();
}

$("#formProgramarVisita").submit(function (e) {

    e.preventDefault();

    var formData = new FormData(this);

    archivosSeleccionados.forEach(function (file) {
        formData.append('adjuntos[]', file);
    });

    $.ajax({
        url: "controladores/cuentascobrar.php?op=guardarVisita",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function (response) {
            if (!response.success) {
                Swal.fire({
                    title: "Cliente",
                    icon: "error",
                    text: response.message,
                });
                return;
            }
            Swal.fire({
                title: "Cliente",
                icon: "success",
                text: response.message,
            });
            $("#formProgramarVisita")[0].reset();
            $("#modalProgramarVisita").modal("hide");
            listarPendientes.load();
        }
    });

});

function actualizarEstadoRecuperacion(idrecuperacion, estado, observacion = "") {
    $.post(
        "controladores/recuperacion.php?op=actualizarEstadoRecuperacion",
        {
            idrecuperacion,
            estado,
            observacion
        },
        function (response) {
            if (!response.success) {
                Swal.fire({
                    title: "Cliente",
                    icon: "error",
                    text: response.message,
                });
                return;
            }
            Swal.fire({
                title: "Cliente",
                icon: "success",
                text: response.message,
            });
            $("#modalEstadoRecuperacion").modal("hide");
            listarPendientes.load();

        }
    );

}

function abrirModalEstado(idrecuperacion, estado, observacion = "") {
    $("#idrecuperacion_estado").val(idrecuperacion);
    $("#estadoRecuperacion").val(estado);
    $("#observacionRecuperacion").val(observacion);
    $("#modalEstadoRecuperacion").modal("show");
}

function guardarEstadoRecuperacion() {
    actualizarEstadoRecuperacion(
        $("#idrecuperacion_estado").val(),
        $("#estadoRecuperacion").val(),
        $("#observacionRecuperacion").val()
    );

}