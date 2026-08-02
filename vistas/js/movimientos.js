$("#navVentasActive").addClass("treeview active");
$("#navVentas").addClass("treeview menu-open");
$("#navMovimientos").addClass("active");

let listarMovimientos = null;


function init() {
    listarMovimientos.load();
    cargarIdAdelanto();
    $.post("controladores/usuario.php?op=selectEmpleado", function (r) {
        $("#idpersonal").html(r);
        $("#idpersonal").select2("");
    });

    $("#myModal").on("submit", function (e) {
        guardaryeditar(e);
    });
}

$("#fecha_inicio, #fecha_fin").change(function () {
    listarMovimientos.load();
});

function validacionDeCampos() {
    var tipoPago = $("#formapago").val(); // Efectivo, Tarjeta, etc.
    var montoPago = $("#montoPagar").val(); // El monto ingresado
    if (tipoPago === "Efectivo" && (!montoPago || parseFloat(montoPago) <= 0)) {
        toastr.warning("El monto en efectivo es requerido");
        return;
    }
    return true;
}

function verificarCaja() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "controladores/venta.php?op=verificar_caja",
            type: "get",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    resolve(response.idcaja); // Devuelve el id de la caja abierta
                } else {
                    resolve(null); // No hay caja abierta
                }
            },
            error: function (error) {
                reject(error);
            },
        });
    });
}

async function guardaryeditar(e) {
    e.preventDefault();
    let validate = validacionDeCampos();
    if (!validate) {
        return;
    }
    // Aseguramos que idcaja esté actualizado antes de enviar
    const idcaja = await verificarCaja();
    if (!idcaja) {
        Swal.fire(
            "Error",
            "Debe tener una caja abierta para realizar la amortización",
            "error",
        );
        return;
    }

    let formData = new FormData($("#formulario")[0]);
    // Añade el id de caja y sucursal al formData

    $.ajax({
        url: "controladores/cajachica.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $('#btnGuardar').attr('disabled', 'disabled');
            $('#btnGuardar').text('Guardando...');
        },
        success: function (response) {
            if (!response.success) {
                Swal.fire({
                    title: "Error!",
                    text: response.message,
                    icon: "error",
                });
                $('#btnGuardar').removeAttr('disabled', 'disabled');
                $('#btnGuardar').text('Guardar');
                return;
            }
            Swal.fire({
                title: "Movimiento",
                icon: "success",
                text: response.message,
            });
            $("#myModal").modal("hide");
            listarMovimientos.load();
            $('#btnGuardar').removeAttr('disabled', 'disabled');
            $('#btnGuardar').text('Guardar');
        },
        error: function (error) {
            $('#btnGuardar').removeAttr('disabled', 'disabled');
            $('#btnGuardar').text('Guardar');
        }
    });
}

$("#btnReporteAdelantos").on("click", function () {
    let desde = $("#fecha_inicio").val();
    let hasta = $("#fecha_fin").val();

    abrirReporteAdelantos(desde, hasta);
});

function abrirReporteAdelantos(desde, hasta) {
    $.get(`controladores/cajachica.php?op=reporteAdelantos&desde=${desde}&hasta=${hasta}`, function (response) {
        const data = response;
        let html = `
            <div id="recibo_print" style="font-family: Arial;">
                <h2 style="text-align:center; margin:0;">MACHI MOTOR'S E.I.R.L.</h2>
                <h4 style="text-align:center; margin-top:0;">RUC: 20610209839</h4>
                <hr>
                <h3 style="text-align:center;">REPORTE DE ADELANTOS</h3>
                <p><b>Periodo:</b> ${desde} al ${hasta}</p>
                
                <!-- TABLA DE ADELANTOS -->
                <h4><b>Adelantos Registrados</b></h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Trabajador</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.detalle.forEach(item => {
            html += `
                <tr>
                    <td>${item.fecha}</td>
                    <td>${item.trabajador}</td>
                    <td>${item.descripcion}</td>
                    <td class="text-right">S/ ${parseFloat(item.monto).toFixed(2)}</td>
                </tr>
            `;
        });

        html += `
                <tr>
                    <td colspan="3" class="text-right"><b>Total Adelantos</b></td>
                    <td class="text-right"><b>S/ ${parseFloat(data.total).toFixed(2)}</b></td>
                </tr>
                </tbody>
                </table>
        `;

        /* -----------------------------------------------------
           NUEVA TABLA: DÍAS TRABAJADOS POR TRABAJADOR
        ----------------------------------------------------- */
        html += `
                <br>
                <h4><b>Días trabajados por trabajador</b></h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Trabajador</th>
                            <th>Días Trabajados</th>
                            <th>Total Pagos</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.dias.forEach(item => {
            html += `
                <tr>
                    <td>${item.trabajador}</td>
                    <td>
                        ${item.dias}
                        <i class="fa fa-calendar text-primary ml-2"
                           style="cursor:pointer;"
                           onclick='verCalendarioTrabajador(${JSON.stringify(item.fechas)})'>
                        </i>
                    </td>
                    <td class="text-right">S/ ${parseFloat(item.total_pago).toFixed(2)}</td>
                </tr>
            `;
        });

        html += `
                </tbody>
                </table>
            </div>
        `;

        $("#recibo_content").html(html);
        $("#modalRecibo").modal("show");
    });
}

let calendario = null;

function verCalendarioTrabajador(fechas) {
    $("#modalCalendario").modal("show");

    setTimeout(() => {

        let eventos = fechas.map(f => ({
            title: "S/ " + parseFloat(f.monto).toFixed(2),
            start: f.fecha,
            color: "#28a745",      // verde
            textColor: "#000",     // negro
            display: "block"       // ← esto muestra el texto en el cuadrito
        }));

        if (calendario) {
            calendario.destroy();
        }

        let calendarioEl = document.getElementById('calendario_trabajo');

        calendario = new FullCalendar.Calendar(calendarioEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            selectable: false,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: eventos,
            eventContent: function (info) {
                return {
                    html: `<div style="
                        font-size:13px; 
                        font-weight:bold; 
                        text-align:center;">
                        ${info.event.title}
                    </div>`
                };
            }
        });

        calendario.render();

    }, 200);
}

function pintarMovimientos(data, permissions) {
    let html = "";

    if (data.length === 0) {
        html = `
      <tr>
        <td colspan="7" class="text-center">No se encontraron registros</td>
      </tr>
    `;
        $("#tbllistado tbody").html(html);
        return;
    }

    data.forEach(item => {

        html += `
            <tr>
                <td>${item.fecha ?? ''}</td>
                <td>${item.descripcion ?? ''}</td>
                <td>${item.tipo === 'egresos'
                ? '<span class="badge bg-danger">EGRESO</span>'
                : '<span class="badge bg-success">INGRESO</span>'
            }</td>
                <td>${item.formapago ?? ''}</td>
                <td>${item.totalefectivo ?? ''}</td>
                <td>${item.totaldeposito ?? ''}</td>
                <td>
                    <button class="btn btn-info btn-xs"
                        onclick='mostrar(${item.idmovimiento})'>
                        <i class="fa fa-list"></i>
                    </button>

                    <button class="btn btn-danger btn-xs"
                        onclick="eliminar(${item.idmovimiento})">
                        <i class="fa fa-trash"></i>
                    </button>

                    <button class="btn btn-primary btn-xs"
                        onclick="abrirRecibo(${item.idmovimiento})">
                        <i class="fa fa-print"></i>
                    </button>
                </td>
            </tr>
            `;

    });


    $("#tbllistado tbody").html(html);
}

listarMovimientos = new FluentPaginator({
    url: "controladores/cajachica.php?op=listar",
    renderTabla: pintarMovimientos,
    tableBody: "#tbodyMovimientos",
    extraParams: () => ({
        fecha_inicio: $("#fecha_inicio").val() || '',
        fecha_fin: $("#fecha_fin").val() || ''
    })
});

function mostrar(idmovimiento) {
    $("#myModal").modal("show");
    limpiar();
    $.post(
        "controladores/cajachica.php?op=mostrar",
        { idmovimiento: idmovimiento },
        function (data, status) {
            data = JSON.parse(data);

            if (data.tipo == "Egresos") {
                $("#egresos").prop("checked", true);
            } else {
                $("#ingresos").prop("checked", true);
            }

            verificarConceptoMovimiento();

            $("#montoPagar").val(data.totalefectivo);
            $("#descripcion").val(data.descripcion);
            $("#idmovimiento").val(data.idmovimiento);
            setTimeout(function () {
                $("#idpersonal").select2().val(data.idpersonal).trigger("change");
                $("#idconcepto_movimiento").select2().val(data.idconcepto_movimiento).trigger("change");
            }, 200)
            $("#formapago").val(data.formapago);
            $("#totaldeposito").val(data.totaldeposito);
            $("#noperacion").val(data.noperacion);
        }
    );
}

function crearMovimiento() {
    $("#myModal").modal("show");
    limpiar();
    verificarConceptoMovimiento();
}

function verificarConceptoMovimiento() {
    let tipo = "";

    if ($("#egresos").is(":checked")) {
        tipo = "egresos";
    } else if ($("#ingresos").is(":checked")) {
        tipo = "ingresos";
    }

    // Cargar los conceptos
    $.post(
        "controladores/cajachica.php?op=coceptoMovimiento&tipo=" + tipo,
        function (r) {
            $("#idconcepto_movimiento").html(r);
            $("#idconcepto_movimiento").select2();
        }
    );
}


function limpiar() {
    $("#idmovimiento").val('');
    $("#egresos").prop("checked", true);
    $("#montoPagar").val("0");
    $("#descripcion").val("");
    $("#formapago").val("Efectivo");
    $("#totaldeposito").val("0");
    $("#noperacion").val("");
    setTimeout(function () {
        $("#idpersonal")
            .select2({
                placeholder: "Seleccione ...",
            })
            .val("")
            .trigger("change");
        $("#idconcepto_movimiento").select2().val("").trigger("change");
    }, 100);
}

function eliminar(idmovimiento) {
    Swal.fire({
        title: "Eliminar?",
        text: "¿Está seguro Que Desea Eliminar el Movimiento?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(
                "controladores/cajachica.php?op=eliminar",
                { idmovimiento: idmovimiento },
                function (response) {
                    if (!response.success) {
                        Swal.fire({
                            title: "Error!",
                            text: response.message,
                            icon: "error",
                        });
                        return;
                    }
                    Swal.fire("!!! Eliminado !!!", response.message, "success");
                    listarMovimientos.load();
                    // mostrarCaja();
                }
            );
        }
    });
}


function abrirRecibo(id) {

    $.get("controladores/cajachica.php?op=getMovimiento&idmovimiento=" + id, function (r) {

        let data = JSON.parse(r);

        let html = `
            <div id="recibo_print" style="font-family: Arial;">

                <h2 style="text-align:center; margin:0;">MACHI MOTOR'S E.I.R.L.</h2>
                <h4 style="text-align:center; margin-top:0;">RUC: 20610209839</h4>
                <hr>

                <h3 style="text-align:center;">RECIBO DE MOVIMIENTO</h3>

                <p><b>Trabajador:</b> ${data.trabajador ?? '---'}</p>
                <p><b>Fecha:</b> ${data.fecha}</p>
                <p><b>Tipo:</b> ${data.tipo}</p>
                <p><b>Descripción:</b> ${data.descripcion}</p>

                <table class="table table-bordered">
                    <tr>
                        <th>Efectivo</th>
                        <td class="text-right">S/ ${data.totalefectivo}</td>
                    </tr>
                    <tr>
                        <th>Transferido</th>
                        <td class="text-right">S/ ${data.totaldeposito}</td>
                    </tr>
                    <tr>
                        <th>Forma de pago</th>
                        <td>${data.formapago}</td>
                    </tr>
                </table>

                <br><br>

                <div style="display:flex; justify-content:space-between;">
                    <div style="text-align:center;">
                        ___________________________<br>
                        ENTREGUÉ CONFORME
                    </div>
                    <div style="text-align:center;">
                        ___________________________<br>
                        RECIBÍ CONFORME
                    </div>
                </div>

            </div>
        `;

        $("#recibo_content").html(html);
        $("#modalRecibo").modal("show");

    });
}

function imprimirModalRecibo() {
    let printContent = document.getElementById("recibo_print").innerHTML;

    let ventana = window.open("", "PRINT", "height=600,width=800");
    ventana.document.write(`
        <html>
            <head>
                <title>Recibo</title>
                <style>
                    body { font-family: Arial; padding:20px; }
                    table { width:100%; border-collapse: collapse; }
                    th, td { padding:8px; border:1px solid #000; }
                </style>
            </head>
            <body>${printContent}</body>
        </html>
    `);

    ventana.document.close();
    ventana.focus();
    ventana.print();
    ventana.close();
}

let ID_ADELANTO = null;

function cargarIdAdelanto() {
    $.get("controladores/cajachica.php?op=getIdConceptoAdelanto", function (r) {
        let data = JSON.parse(r);
        ID_ADELANTO = data.idconcepto_movimiento;
    });
}

function nuevoAdelanto() {
    if (!ID_ADELANTO) {
        alert("No se encontró el concepto de adelanto.");
        return;
    }

    limpiar();
    $("#myModal").modal("show");

    $("#egresos").prop("checked", true);
    verificarConceptoMovimiento();

    setTimeout(() => {
        $("#idconcepto_movimiento").val(ID_ADELANTO).trigger("change");
    }, 200);

    $("#descripcion").val("Adelanto de sueldo");
}

$("#btnExportarExcel").on("click", function () {
    const fechaInicio = $("#fecha_inicio").val();
    const fechaFin = $("#fecha_fin").val();

    window.location = "modelos/exports/exportar_excel.php?inicio=" + fechaInicio + "&fin=" + fechaFin;
});

document.addEventListener("DOMContentLoaded", () => {
    init();
});