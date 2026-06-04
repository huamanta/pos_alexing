$('#navPosActive').addClass("treeview active");
$('#navPos').addClass("treeview menu-open");
$('#navSolicitudes').addClass("active");

var tablaSolicitudes;

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

    $.get(
        "controladores/solicitudes.php?op=mostrarSolicitud&idsolicitud="+idsolicitud,
        function (r) {

            let data = JSON.parse(r);

            $("#detalleSolicitud").html(`

                <table class="table table-bordered">

                    <tr>
                        <th width="200">
                            Cliente
                        </th>
                        <td>
                            ${data.cliente}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            Score
                        </th>
                        <td>
                            ${data.score}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            Riesgo
                        </th>
                        <td>
                            ${data.riesgo}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            Estado
                        </th>
                        <td>
                            ${data.estado}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            Observación
                        </th>
                        <td>
                            ${data.observacion ?? ''}
                        </td>
                    </tr>

                </table>

            `);

            $("#modalDetalleSolicitud")
                .modal("show");

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
    $.post("controladores/cotizaciones.php?op=cotizacionesCliente", { idcliente: idcliente }, function (r) {
        $("#idcotizacion").html(r);
        $('#idcotizacion').select2('');
    });
}

init();