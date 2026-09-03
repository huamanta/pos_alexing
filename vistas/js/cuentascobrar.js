var tabla;
var tablaCreditosCliente;
var tablaCuotasCredito;
var ventaActualCuotas = null;
var saldoActualCuotas = 0;
var tbllistadohistorial;
let archivosSeleccionados = [];
let calendar = null;
let listaCreditos = null;

//Función que se ejecuta al inicio
function init() {
    // listar();
    listaCreditos.load();
    listarSaldos();
    enviarRecordatoriosAutomatico();
    $("#body").addClass("sidebar-collapse sidebar-mini");

    $("#getCodeModal").on("submit", function (e) {
        guardaryeditar(e);
    })

    $("#fecha_inicio").change(function (e) {
        e.preventDefault();
        listaCreditos.load();
        listarSaldos();
    });
    $("#fecha_fin").change(function (e) {
        e.preventDefault();
        listaCreditos.load();
        listarSaldos();
    });
    $("#idcliente").change(function (e) {
        e.preventDefault();
        listaCreditos.load();
        listarSaldos();
        toggleBtnEstadoCuenta();
    });


    $('#navCobros').addClass("treeview menu-open");
    $('#navCobrosActive').addClass("treeview active");
    $('#navCuentasPorCobrar').addClass("active");


    //cargamos los items al select almacen
    $.post("controladores/venta.php?op=selectSucursal3", function (r) {
        $("#idsucursal2").html(r);
        $('#idsucursal2').select2('');
    });

    $("#btnEstadoCuentaAccion").on("click", function () {
        let idcliente = $("#idcliente").val();
        let fecha_inicio = $("#fecha_inicio").val();
        let fecha_fin = $("#fecha_fin").val();

        if (!idcliente || idcliente === "Todos") {
            alert("Seleccione un cliente válido");
            return;
        }

        verEstadoCuentaCliente(idcliente, fecha_inicio, fecha_fin);
    });

    listarBancos();
}

function listarBancos() {
    $.get('controladores/consultas.php?op=listarBancos', function (response) {
        const bancos = response || [];
        let html = '<option value="">Seleccione...</option>';
        html += bancos.map(banco => `
                <option value="${banco.idbanco}">
                    ${banco.nombre}
                </option>
            `).join("");
        $("#banco").html(html);
        $("#bancoAmortizar").html(html);
    })
}

$("#idcliente").select2({
    placeholder: "Buscar cliente...",
    allowClear: true,
    minimumInputLength: 2,

    ajax: {
        url: "controladores/venta.php?op=selectCliente2",
        type: "GET",
        dataType: "json",
        delay: 250,

        data: function (params) {
            return {
                search: params.term,
                page: params.page || 1,
                only_client: 1
            };
        },

        processResults: function (data, params) {

            params.page = params.page || 1;

            return {
                results: data.data.map(function (item) {
                    return {
                        id: item.idpersona,
                        text: item.nombre + " - " + item.num_documento
                    };
                }),

                pagination: {
                    more: data.meta.current_page < data.meta.last_page
                }
            };
        },

        cache: true
    }
});

function toggleBtnEstadoCuenta() {
    let idcliente = $("#idcliente").val();

    if (idcliente && idcliente !== "Todos") {
        $("#btnEstadoCuenta").show();
    } else {
        $("#btnEstadoCuenta").hide();
    }
}



document.getElementById('formapago').addEventListener('change', function () {
    const montoInput = document.getElementById('montoPagarTarjeta');
    if (this.value !== 'Efectivo') {
        montoInput.removeAttribute('readonly');
    } else {
        montoInput.setAttribute('readonly', true);
        montoInput.value = ''; // Opcional: limpiar el campo al volver a "Efectivo"
    }
});

function enviarRecordatoriosMasivo() {
    Swal.fire({
        title: '¿Enviar recordatorios a todas las cuotas vencidas?',
        text: 'Se notificará a todos los clientes con cuotas vencidas.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (!result.isConfirmed) return;

        const $btn = $('#btnEnviarRecordatorioSemana');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');

        $.ajax({
            url: 'controladores/cuentascobrar.php?op=enviar_recordatorio',
            method: 'POST',
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html(originalHtml);

                if (!res || !res.success) {
                    let errorMsg = res && res.response ? res.response : 'No se pudo completar el envío. Revisa los logs del servidor.';
                    Swal.fire('Error', 'Ocurrió un error: ' + errorMsg, 'error');
                    return;
                }

                let html = `<p>Total recordatorios enviados: <strong>${res.message.split(' ')[0]}</strong></p>`;
                html += `<p>Se enviaron a todas las cuotas vencidas automáticamente.</p>`;

                // Mostrar respuesta completa del API (opcional, útil para depuración)
                html += `<pre>Respuesta API: ${JSON.stringify(res.response, null, 2)}</pre>`;

                $('#recordatorioResultadosContenido').html(html);
                $('#modalRecordatorioResultados').modal('show');

                // Recargar tabla si existe
                if (typeof tabla !== 'undefined') tabla.ajax.reload(null, false);
            },
            error: function (xhr, status, err) {
                $btn.prop('disabled', false).html(originalHtml);
                Swal.fire('Error', 'Ocurrió un error durante el envío: ' + err, 'error');
            }
        });
    });
}

$("#btnEnviarRecordatorioSemana").on("click", function (e) {
    e.preventDefault();
    enviarRecordatoriosMasivo();
});

function enviarRecordatoriosAutomatico() {
    $.ajax({
        url: 'controladores/cuentascobrar.php?op=enviar_recordatorio',
        method: 'POST',
        dataType: 'json',
        success: function (res) {
            if (!res || !res.success) return;
        },
        error: function (xhr, status, err) {
            console.error('Error envío automático:', xhr.responseText);
        }
    });
}

function pintarCreditos(data, permissions) {
    $('#vistaListaClientes').show();
    $('#vistaCreditosCliente').hide();
    $('#panelSuperiorCxC').show();
    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbllistadocuentasxcobrar tbody").html(html);
        return;
    }

    data.forEach((item, i) => {

        html += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${item.cliente}</td>
                    <td>${item.num_documento || ''}</td>
                    <td>${item.total_creditos || ''}</td>
                    <td>${item.deuda_total_str}</td>
                    <td>${item.total_pagado_str}</td>
                    <td>${item.saldo_pendiente_str}</td>
                    <td>
                        <button class="btn btn-sm btn-success"
                            onclick='verDetalleCliente(${item.idpersona}, ${JSON.stringify(item.cliente)})'>
                            <i class="fas fa-eye"></i> Ver Detalle
                        </button>

                        <button class="btn btn-info btn-sm"
                            onclick='verUbicacionCliente(
                                ${JSON.stringify(item.latitude)},
                                ${JSON.stringify(item.longitude)},
                                ${JSON.stringify(item.direccion)}
                            )'
                            title="Ver ubicación del cliente">
                            <i class="fas fa-search-location"></i> Ubicación
                        </button>
                    </td>
                </tr>
                `;
    });

    $("#tbllistadocuentasxcobrar tbody").html(html);

}


listaCreditos = new FluentPaginator({
    url: "controladores/cuentascobrar.php?op=listaCreditos",
    renderTabla: pintarCreditos,
    tableBody: "#tbodyCreditos",
    extraParams: () => ({
        fecha_inicio: $("#fecha_inicio").val() || "",
        fecha_fin: $("#fecha_fin").val() || "",
        idcliente: $("#idcliente").val() || ""
    })
});


function listarSaldos() {
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    var idcliente = $("#idcliente").val();
    var idsucursal = $("#idsucursal2").val();

    // Verificar si fecha de inicio es mayor que fecha de fin
    var fechaInicio = new Date(fecha_inicio);
    var fechaFin = new Date(fecha_fin);

    if (fechaInicio > fechaFin) {
        // Establecer fecha de fin en la fecha actual
        var hoy = new Date();
        var dd = String(hoy.getDate()).padStart(2, '0');
        var mm = String(hoy.getMonth() + 1).padStart(2, '0');
        var yyyy = hoy.getFullYear();

        fecha_fin = yyyy + '-' + mm + '-' + dd;
        $("#fecha_fin").val(fecha_fin);
    }

    $.ajax({
        url: 'controladores/cuentascobrar.php?op=listar_saldos',
        data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin, idcliente: idcliente, idsucursal: idsucursal },
        type: "get",
        dataType: "json",
        success: function (data) {
            $("#saldos").text(data.deudatotal);
            $("#abonos").text(data.abonototal);
            $("#deudas").text(data.saldo);
            $('#panel_amortizar').html('<i class="fas fa-money-bill fa-lg" style="font-size: 20px !important"></i>');

        },
        error: function (e) {
            console.log(e.responseText);
        }
    });
}

async function amortizarDeuda(deuda, idcliente, fecha_inicio, fecha_fin) {
    // Verificamos la caja abierta
    const idcaja = await verificarCaja();
    alert();
    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar la amortización', 'error');
        return;
    }

    $('#idcaja').val(idcaja); // Cargamos idcaja en el modal
    $('#modalAmortizar').modal('show');
    $('#montoAdeudadoAmortizar').val(parseFloat(deuda).toFixed(2));
    $('#deudaTotalAmortizar').html(parseFloat(deuda).toFixed(2));
    $('#idcliente_amortizar').val(idcliente);
    $('#idventa_amortizar').val('');
    $('#fecha_inicio_amortizar').val(fecha_inicio);
    $('#fecha_fin_amortizar').val(fecha_fin);
}

function amortizarCuotasCredito(idventa, saldoPendiente, documento, nota) {
    $('#idventacuentacobrar').val(idventa);
    ventaActualCuotas = idventa;
    saldoActualCuotas = toNumber(saldoPendiente);
    $('#tituloCreditoCuotas').text(documento ? documento : '');
    amortizar(idventa);
}

function calendarioCuotasCredito(idventa, saldoPendiente, documento, nota) {
    $("#modalCalendario").modal('show');
    var calendarEl = document.getElementById("calendario");

    if (calendar) {
        calendar.destroy();
    }

    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: "es",
        initialView: "dayGridMonth",
        height: "auto",

        events: function (info, successCallback, failureCallback) {
            $.ajax({
                url: "controladores/cuentascobrar.php",
                data: {
                    op: "calendarioCuotasCredito",
                    idventa: idventa
                },
                dataType: "json",
                success: successCallback,
                error: failureCallback
            });
        },

        eventClick: function (info) {
            console.log(info.event);
            console.log(info.event.title);
            console.log(info.event.start);
            console.log(info.event.extendedProps);
        }
    });

    setTimeout(function () {
        calendar.render();
    }, 300);
}


function historialCreditoRefinanciamiento(idventa, saldoPendiente, documento, nota) {

    $.getJSON(
        "controladores/refinanciamiento.php",
        {
            op: "historialCreditoRefinanciamiento",
            idventa: idventa
        },
        function (r) {

            if (!r.estado) {
                alert(r.mensaje);
                return;
            }

            $("#hisCliente").val(r.venta.cliente);
            $("#hisDocumento").val(r.venta.documento);
            $("#hisFecha").val(r.venta.fecha_hora);
            $("#hisTotal").val("S/ " + parseFloat(r.venta.total_venta).toFixed(2));

            let html = "";

            $.each(r.historial, function (i, h) {

                if (h.tipo == "ORIGINAL") {

                    html += `
                    <div class="box box-primary">

                        <div class="box-header with-border bg-light-blue">

                            <h3 class="box-title">
                                <i class="fa fa-file-text"></i>
                                Crédito Original
                            </h3>

                            <span class="pull-right label label-primary">
                                Inicio del crédito
                            </span>

                        </div>

                        <div class="box-body">

                            <div class="row text-center" style="margin-bottom:20px">

                                <div class="col-md-3">
                                    <h5>Total Crédito</h5>
                                    <h3 class="text-primary">
                                        S/ ${parseFloat(r.venta.total_venta).toFixed(2)}
                                    </h3>
                                </div>

                                <div class="col-md-3">
                                    <h5>Cliente</h5>
                                    <b>${r.venta.cliente}</b>
                                </div>

                                <div class="col-md-3">
                                    <h5>Documento</h5>
                                    <b>${r.venta.documento}</b>
                                </div>

                                <div class="col-md-3">
                                    <h5>Fecha</h5>
                                    <b>${r.venta.fecha_hora}</b>
                                </div>

                            </div>

                            ${tablaCuotas(h.cuotas)}

                        </div>

                    </div>
                    `;

                } else {

                    html += `

                    <div class="text-center" style="margin:15px 0">
                        <i class="fa fa-arrow-down fa-2x text-warning"></i>
                    </div>

                    <div class="box box-warning">

                        <div class="box-header with-border bg-yellow">

                            <h3 class="box-title">

                                <i class="fa fa-sync"></i>

                                Refinanciamiento #${h.idrefinanciamiento}

                            </h3>

                            <span class="pull-right label label-warning">
                                Nuevo Cronograma
                            </span>

                        </div>

                        <div class="box-body">

                            <div class="row text-center">

                                <div class="col-md-2">

                                    <div class="small-box bg-aqua">

                                        <div class="inner">
                                            <h3>S/ ${parseFloat(h.saldo_original).toFixed(2)}</h3>
                                            <p>Saldo</p>
                                        </div>

                                        <div class="icon">
                                            <i class="fa fa-wallet"></i>
                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-2">

                                    <div class="small-box bg-green">

                                        <div class="inner">
                                            <h3>S/ ${parseFloat(h.interes).toFixed(2)}</h3>
                                            <p>Interés</p>
                                        </div>

                                        <div class="icon">
                                            <i class="fa fa-percent"></i>
                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-2">

                                    <div class="small-box bg-purple">

                                        <div class="inner">
                                            <h3>S/ ${parseFloat(h.inicial).toFixed(2)}</h3>
                                            <p>Inicial</p>
                                        </div>

                                        <div class="icon">
                                            <i class="fa fa-money-bill"></i>
                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="small-box bg-orange">

                                        <div class="inner">
                                            <h3>S/ ${parseFloat(h.total_refinanciado).toFixed(2)}</h3>
                                            <p>Total Refinanciado</p>
                                        </div>

                                        <div class="icon">
                                            <i class="fa fa-handshake"></i>
                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-3">

                                    <div class="small-box bg-red">

                                        <div class="inner">
                                            <h3>${h.fecha}</h3>
                                            <p>Fecha Refinanciamiento</p>
                                        </div>

                                        <div class="icon">
                                            <i class="fa fa-calendar"></i>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            ${tablaCuotas(h.cuotas)}

                        </div>

                    </div>

                    `;

                }

            });

            $("#timelineCredito").html(html);

            $("#modalHistorialCredito").modal("show");

        }
    );

}



function tablaCuotas(cuotas) {

    let html = `

    <table class="table table-bordered table-striped table-hover">

        <thead style="background:#3c8dbc;color:#fff">

            <tr>

                <th width="60">#</th>
                <th>Vence</th>
                <th>Monto</th>
                <th>Abonado</th>
                <th>Saldo</th>
                <th width="130">Estado</th>

            </tr>

        </thead>

        <tbody>

    `;

    $.each(cuotas, function (i, c) {

        let badge = "";
        let clase = "";

        switch (c.estado) {

            case "PAGADA":
                badge = '<span class="label label-success">Pagada</span>';
                clase = "success";
                break;

            case "PENDIENTE":
                badge = '<span class="label label-warning">Pendiente</span>';
                clase = "warning";
                break;

            case "REFINANCIADA":
                badge = '<span class="label label-info">Refinanciada</span>';
                clase = "info";
                break;

        }

        html += `

            <tr class="${clase}">

                <td><b>${i + 1}</b></td>

                <td>${c.fechavencimiento}</td>

                <td class="text-right">
                    S/ ${parseFloat(c.deudatotal).toFixed(2)}
                </td>

                <td class="text-right">
                    S/ ${parseFloat(c.abonototal).toFixed(2)}
                </td>

                <td class="text-right">
                    <b>S/ ${parseFloat(c.deuda).toFixed(2)}</b>
                </td>

                <td class="text-center">
                    ${badge}
                </td>

            </tr>

        `;

    });

    html += `

        </tbody>

    </table>

    `;

    return html;

}

$('#formulario-amortizar').submit(async function (e) {
    e.preventDefault();

    // Verificamos la caja abierta antes de enviar
    const idcaja = await verificarCaja();
    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar la amortización', 'error');
        return;
    }

    var formData = new FormData(this);
    formData.set('idcaja', idcaja); // Aseguramos que idcaja esté en los datos enviados

    $.ajax({
        url: 'controladores/cuentascobrar.php?op=amortizar_deuda',
        data: formData,
        type: "POST",
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#btnAmortizarDeuda")
                .prop("disabled", true)
                .html('Guardando...');
        },
        success: function (data) {
            var data = JSON.parse(data);
            if (data.success) {
                Swal.fire('Éxito', data.message, 'success');
                listarSaldos();
                tablaCreditosCliente.ajax.reload();
                if (tablaCuotasCredito) {
                    tablaCuotasCredito.ajax.reload();
                }
                $('#modalAmortizar').modal('hide');
                $('#montoAdeudadoAmortizar').val('');
                $('#deudaTotalAmortizar').html('');
                $('#idcliente_amortizar').val('');
                $('#idventa_amortizar').val('');
                $('#fecha_inicio_amortizar').val('');
                $('#fecha_fin_amortizar').val('');
            } else {
                Swal.fire('Error', data.message, 'error');
            }

            $("#btnAmortizarDeuda")
                .prop("disabled", false)
                .html('Guardar');
        },
        error: function (e) {
            console.log(e.responseText);
            $("#btnAmortizarDeuda")
                .prop("disabled", false)
                .html('Guardar');
        }
    });
});


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
            }
        });
    });
}


async function guardaryeditar(e) {
    e.preventDefault();

    const idcaja = await verificarCaja(); // Verifica caja abierta antes de enviar
    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar abonos', 'error');
        return;
    }

    var formData = new FormData($("#formulario")[0]);
    formData.append('idcaja', idcaja);

    $.ajax({
        url: "controladores/cuentascobrar.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#btnGuardarPago").text("Guardando...").prop('disabled', true);
        },
        success: function (datos) {
            let res = JSON.parse(datos);
            $("#btnGuardarPago").text("Guardar pago").prop('disabled', false);
            if (!res.success) {
                Swal.fire("Error", res.message, "error");
                return;
            }

            const t = res.ticket;
            let formaPago = "";
            if (Number(t.monto_efectivo) > 0) {
                formaPago += `
                    <tr>
                        <td>Efectivo</td>
                        <td style="text-align:right">S/ ${Number(t.monto_efectivo).toFixed(2)}</td>
                    </tr>`;
            }

            if (Number(t.monto_tarjeta) > 0) {
                formaPago += `
                    <tr>
                        <td>Tarjeta</td>
                        <td style="text-align:right">S/ ${Number(t.monto_tarjeta).toFixed(2)}</td>
                    </tr>

                    <tr>
                        <td>Banco</td>
                        <td style="text-align:right">${t.banco || "-"}</td>
                    </tr>

                    <tr>
                        <td>Operación</td>
                        <td style="text-align:right">${t.operacion || "-"}</td>
                    </tr>`;
            }

            abrirReciboPagoTicket(t, formaPago);

            window.document.close();

            Swal.fire("Éxito", res.message, "success");

            $('#getCodeModal').modal('hide');
            $("#formulario")[0].reset();

            limpiar();

            listarSaldos();

            tablaCreditosCliente.ajax.reload();

            tablaCuotasCredito.ajax.reload();
        },
        error: function (e) {
            $("#btnGuardarPago").text("Guardar pago").prop('disabled', false);
        }
    });
}


function limpiar() {
    // Aquí deberías implementar la lógica para limpiar los campos del formulario
    // Puedes resetear campos, ocultar/mostrar elementos, etc.
    // Por ejemplo, si tienes campos específicos, podrías hacer algo como:
    // $('#campo1').val('');
    // $('#campo2').val('');
}


async function mostrar(idcpc) {
    $('#panelMora').hide();
    $('#panelDescuento').hide();
    const idcaja = await verificarCaja(); // Verifica la caja abierta

    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar abonos', 'error');
        return;
    }

    $("#idcaja").val(idcaja);
    $("#getCodeModal").modal('show');

    // 🔹 2. Obtener datos actualizados
    $.post("controladores/cuentascobrar.php?op=mostrar",
        {
            idcpc: idcpc
        },
        function (data) {
            data = JSON.parse(data);
            var total_venta = parseFloat(data.total_venta);
            var interes = total_venta * (data.interes / 100);
            $('#documento').text(data.tipo_comprobante + " : " + data.serie_comprobante + " - " + data.num_comprobante);
            $("#deutaTotal").text(parseFloat(data.deuda).toFixed(2));
            $("#valorVenta").text(total_venta.toFixed(2));
            $("#valorInteres").text(interes.toFixed(2));
            $("#montoAdeudado").val(parseFloat(data.total_pagar).toFixed(2));
            $("#idcpc").val(data.idcpc);
            if (data.dias_mora) {
                $('#panelMora').show();
            }
            $("#montoMora").text(data.mora_total);
            $("#montoMoraPagar").text(data.mora);
            $("#diasRetraso").text(data.dias_mora);
            if (data.descuento_total > 0) {
                $('#panelDescuento').show();
            }
            $("#porcentajeDescuento").text(data.porcentaje_descuento);
            $("#montoDescuento").text(data.descuento_total);
            $("#diasAnticipacion").text(data.dias_descuento);

            $("#idventa").val(data.idventa);
            $("#fechavencimiento").text(data.fechavencimiento);

        });
}


function mostrarAbonos(idcpc) {

    $("#getCodeModal2").modal('show');

    $.post("controladores/cuentascobrar.php?op=mostrar", { idcpc: idcpc }, function (data, status) {

        data = JSON.parse(data);

        var label = document.querySelector('#abonoTotal2');
        label.textContent = data.deuda;

        var label = document.querySelector('#abonoTotal');
        label.textContent = data.abonototal;

    });

    tabla = $('#tbllistado').dataTable(
        {
            //"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
            "aProcessing": true,//Activamos el procesamiento del datatables
            "aServerSide": true,//Paginación y filtrado realizados por el servidor
            dom: 'Bfrtip',//Definimos los elementos del control de tabla
            buttons: [
                'excelHtml5',
                'pdf'
            ],
            "ajax":
            {
                url: 'controladores/cuentascobrar.php?op=listarDetalle',
                data: { idcpc: idcpc },
                type: "get",
                dataType: "json",
                error: function (e) {
                    console.log(e.responseText);
                }
            },
            "bDestroy": true,
            "iDisplayLength": 10,//Paginación
        }).DataTable();

}

function toNumber(valor) {
    if (valor === null || valor === undefined) return 0;
    return parseFloat((valor + '').replace(/,/g, '')) || 0;
}

function verDetalleCliente(idcliente, nombreCliente) {
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    var idsucursal = $("#idsucursal2").val();

    if (!nombreCliente || nombreCliente === "Todos") {
        nombreCliente = "Cliente";
    }

    $("#detalleClienteTitulo").text(nombreCliente);
    $('#panelSuperiorCxC').hide();
    $('#vistaListaClientes').hide();
    $('#vistaCreditosCliente').show();

    tablaCreditosCliente = $("#tbllistadoCreditosCliente").dataTable({
        "aProcessing": true,
        "aServerSide": false,
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "ajax": {
            url: "controladores/cuentascobrar.php?op=listar_creditos_cliente",
            data: {
                idcliente: idcliente,
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                idsucursal: idsucursal
            },
            type: "get",
            dataType: "json",
            dataSrc: function (json) {
                return json && json.aaData ? json.aaData : [];
            },
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 10
    }).DataTable();
}

function volverListaClientes() {
    $('#vistaCreditosCliente').hide();
    $('#vistaListaClientes').show();
    $('#panelSuperiorCxC').show();
    listaCreditos.load();
}


function listarHistorialSeguimiento(idventa) {
    $('#modalComentario').modal('show');
    tbllistadohistorial = $("#tbllistadohistorial").dataTable({
        "aProcessing": true,
        "aServerSide": false,
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "ajax": {
            url: "controladores/cuentascobrar.php?op=listarHistorialSeguimiento",
            data: {
                idventa: idventa
            },
            type: "get",
            dataType: "json",
            dataSrc: function (json) {
                return json && json.aaData ? json.aaData : [];
            },
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 10
    }).DataTable();
}


function listarHistorialIncidncias(idventa) {
    $('#modalIncidencias').modal('show');
    tbllistadohistorial = $("#tbllistadohistorialIncidencias").dataTable({
        "aProcessing": true,
        "aServerSide": false,
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "ajax": {
            url: "controladores/cuentascobrar.php?op=listarHistorialIncidencias",
            data: {
                idventa: idventa
            },
            type: "get",
            dataType: "json",
            dataSrc: function (json) {
                return json && json.aaData ? json.aaData : [];
            },
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 10
    }).DataTable();
}

function guardarComentarioCredito() {
    let comentario = $('#comentarioCredito').val();

    if (comentario === '') {
        notificacionToast('warning', 'Escribe un comentario');
        return;
    }

    $.post("controladores/cuentascobrar.php?op=guardar_comentario", {
        idventacuentacobrar: $('#idventacuentacobrar').val(),
        comentario: comentario
    }, function (response) {
        var data = JSON.parse(response);
        if (data.status) {
            notificacionToast('success', data.mensaje);
            $('#modalComentario').modal('hide');
            $('#comentarioCredito').val('');
            verCuotasCredito(data.idventa, data.saldoPendiente, data.documento, data.nota)
        } else {
            notificacionToast('error', data.mensaje);
        }

    });
}

function verCuotasCredito(idventa, saldoPendiente, documento, nota) {
    let comentario = nota;
    $('#idventacuentacobrar').val(idventa);
    ventaActualCuotas = idventa;
    $('#tituloCreditoCuotas').text(documento ? documento : '');

    $("#modalCuotasCredito").modal("show");

    let botones = [
        {
            text: '<i class="fas fa-comment-dots"></i> Fechas programadas',
            className: 'btn btn-info btn-sm btn-comment',
            action: function () {
                listarHistorialSeguimiento(idventa);
            }
        },
        {
            text: '<i class="fas fa-comment-dots"></i> Incidencias',
            className: 'btn btn-info btn-sm btn-incidencias',
            action: function () {
                listarHistorialIncidncias(idventa);
            }
        },
        {
            text: '<i class="fas fa-download"></i> Descargar informe',
            className: 'btn btn-warning btn-sm btn-descargar',
            action: function () {
                dscargarHistorial(idventa);
            }
        }
    ];

    if (saldoActualCuotas > 0) {
        botones.push({
            text: '<i class="fas fa-hand-holding-usd"></i> Amortizar',
            className: 'btn btn-success btn-sm btn-amortiar',
            action: function () {
                amortizar(idventa);
            }
        });
    }

    tablaCuotasCredito = $("#tbllistadoCuotasCredito").DataTable({
        aProcessing: true,
        aServerSide: true,
        responsive: true,
        lengthChange: false,
        autoWidth: true,

        dom:
            '<"row mb-1"' +
            '<"col-md-12 msgComentario">' +
            '>' +
            '<"row"' +
            '<"col-md-7"B>' +
            '<"col-md-5 text-right"f>' +
            '>' +
            't' +
            '<"row"' +
            '<"col-md-6"i>' +
            '<"col-md-6"p>' +
            '>',
        buttons: botones,
        ajax: {
            url: "controladores/cuentascobrar.php?op=listar_cuotas_credito",
            data: { idventa: idventa },
            type: "get",
            dataType: "json"
        },

        bDestroy: true,
        iDisplayLength: 10,
    });
}
let inicialCuota = 0;

function encrypt_decrypt(action, string) {
    if (action === 'encrypt') {
        // Encriptación simple pero efectiva para este caso
        const encoded = btoa(string);
        return encoded.replace(/=/g, '').replace(/\//g, '_').replace(/\+/g, '-');
    }
    return string;
}

function dscargarHistorial(idventa) {
    const encryptedId = encrypt_decrypt('encrypt', idventa);
    const url = 'public/docs_service/cronograma_pagos?idventa=' + encryptedId;
    const win = window.open(url, '_blank');
    if (!win) {
        alert('Por favor habilita ventanas emergentes o descarga manualmente: ' + url);
        return;
    }
}

$("#formapagoAmortizar").change(function (e) {
    if ($(this).val() != 'Efectivo') {
        $("#panelTransferencia").show();
    } else {
        $("#panelTransferencia").hide();
    }
});

async function amortizar(idventa) {
    const idcaja = await verificarCaja();
    $("#panelDescuentoAmortizar").hide();
    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar la amortización', 'error');
        return;
    }

    $.ajax({
        url: 'controladores/cuentascobrar.php?op=cuotasPorPagar',
        data: { idventa: idventa },
        type: "GET",
        success: function (response) {
            let cuotas = JSON.parse(response);
            let tieneMora = cuotas.some(c => parseFloat(c.mora_calculada || 0) > 0);
            if (tieneMora) {
                Swal.fire('Error', 'No se puede amortizar porque existen cuotas con mora.', 'error');
                return;
            }
            let totalCuotas = cuotas.length;

            $("#contenedorRange").html(`
                <input type="range"
                    id="rangeCuotas"
                    min="1"
                    max="${totalCuotas}"
                    value="${totalCuotas}"
                    step="1">
            `);

            function calcularTotal(cantidad) {

                let total = 0;
                let descuento = 0;

                for (let i = 0; i < cantidad; i++) {
                    total += parseFloat(cuotas[i].deuda);
                    descuento += parseFloat(cuotas[i].descuento_calculado || 0);
                }

                let final = total - descuento;
                if (descuento > 0) {
                    $("#panelDescuentoAmortizar").show();
                }
                $("#montoDescuentoAmortizar").html(descuento.toFixed(2));
                $("#cantidadSeleccionada").val(cantidad);
                $("#totalPagar").text(final.toFixed(2));
                $("#montoPagarAmortizar").val(final.toFixed(2));
            }

            // INIT
            calcularTotal(totalCuotas);

            $("#rangeCuotas").off("input").on("input", function () {
                let cantidad = parseInt($(this).val());

                calcularTotal(cantidad);
            });

            // INPUT manual (si quieres permitirlo)
            $("#cantidadSeleccionada").off("input").on("input", function () {
                let cantidad = parseInt($(this).val());

                if (!cantidad || cantidad < 1) return;

                if (cantidad > totalCuotas) {
                    cantidad = totalCuotas;
                }

                $("#rangeCuotas").val(cantidad); // 🔥 sincroniza slider
                calcularTotal(cantidad);
            });

            $('#modalAmortizar').modal('show');
        }
    })

    $('#idcaja').val(idcaja);
    $('#idventa_amortizar').val(ventaActualCuotas);
    $('#idcliente_amortizar').val('');
    $('#fecha_inicio_amortizar').val('');
    $('#fecha_fin_amortizar').val('');
    $('#montoPagarAmortizar').val('');
    $('#montoAdeudadoAmortizar').val(saldoActualCuotas.toFixed(2));
    $('#deudaTotalAmortizar').html(saldoActualCuotas.toFixed(2));
};


function verEstadoCuenta(idcpc) {
    $.get(
        "controladores/cuentascobrar.php?op=estado_cuenta",
        { idcpc: idcpc },
        function (data) {
            $("#estadoCuentaContenido").html(data);
            $("#modalEstadoCuenta").modal("show");
        }
    );
}


// programar compromiso de pago
const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);

document.getElementById("fecha_compromiso").min =
    tomorrow.toISOString().split("T")[0];

function programarCompromiso(idcpc, idventa, idcliente, saldo, dias_mora, mora) {
    // 1. Limpiar por si el modal ya se había abierto antes
    $("#contenedorMensajeMora").empty();

    // 2. Llenar los campos ocultos
    $("#idcpcProgramado").val(idcpc);
    $("#idventaProgramado").val(idventa);
    $("#idclienteProgramado").val(idcliente);
    $("#monto").val(saldo);
    $("#fecha_compromiso").val('');
    $("#observacion").val('');

    // 3. Construir el mensaje de alerta (Usando clases de Bootstrap)
    // Formateamos la mora a 2 decimales asumiendo que es una moneda (ej. Soles/Dólares)
    let moraFormateada = parseFloat(mora).toFixed(2);

    let htmlAlerta = `
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i>¡Cuenta Pendiente con Mora!</h5>
            <p class="mb-0">
                La cuota presenta <strong>${dias_mora} días de mora</strong> acumulados, 
                generando un monto de penalidad de <strong>S/ ${moraFormateada}</strong>. 
                Por favor, registre una fecha de compromiso para gestionar el cobro.
            </p>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    // 4. Inyectar el mensaje en el modal
    $("#contenedorMensajeMora").html(htmlAlerta);

    // 5. Abrir el modal
    $("#modalCompromisoPago").modal("show");
}

$("#formCompromisoPago").submit(function (e) {

    e.preventDefault();
    if ($("#fecha_compromiso").val() === "") {
        return Swal.fire("Error", "Seleccione una fecha", "warning");
    }

    if ($("#monto").val() <= 0) {
        return Swal.fire("Error", "Ingrese un monto válido", "warning");
    }
    const data = new FormData(this);

    $.ajax({
        url: "controladores/cuentascobrar.php?op=guardarCompromisoPago",
        type: "POST",
        data: data,
        contentType: false,
        processData: false,

        beforeSend: function () {

            Swal.fire({
                title: 'Guardando...',
                text: 'Espere un momento',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

        },

        success: function (response) {

            Swal.close();

            let r = JSON.parse(response);

            if (r.status) {

                Swal.fire({
                    icon: 'success',
                    title: 'Correcto',
                    text: r.msg
                });

                $("#modalCompromisoPago").modal("hide");
                $("#formCompromisoPago")[0].reset();

                tabla.ajax.reload(null, false);

            } else {

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: r.msg
                });

            }

        },

        error: function () {

            Swal.close();

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al procesar la solicitud'
            });

        }

    });

});

function verEstadoCuentaCliente(idcliente, fecha_inicio, fecha_fin) {

    $("#estadoCuentaContenido").html(
        "<div class='text-center'><i class='fas fa-spinner fa-spin'></i> Cargando...</div>"
    );

    $("#modalEstadoCuenta").modal("show");

    $.get(
        "controladores/cuentascobrar.php?op=estado_cuenta_cliente",
        {
            idcliente: idcliente,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin
        },
        function (data) {
            $("#estadoCuentaContenido").html(data);
        }
    );
}

// Ajuste para modales apilados: asegura que el modal nuevo quede al frente.
$(document).on('show.bs.modal', '.modal', function () {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function () {
        $('.modal-backdrop').not('.modal-stack')
            .css('z-index', zIndex - 1)
            .addClass('modal-stack');
    }, 0);
});

$(document).on('hidden.bs.modal', '.modal', function () {
    if ($('.modal:visible').length) {
        $('body').addClass('modal-open');
    }
});

function verUbicacionCliente(latitude, longitude, direccion) {
    if (!latitude && !longitude) {
        notificacionToast('warning', 'El cliente no tiene ubicacion configurada');
        return;
    };

    if (direccion) {

        const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(direccion)}`;

        window.open(url, '_blank');
    }

    const url = `https://www.google.com/maps?q=${latitude},${longitude}`;

    window.open(url, '_blank');
}

function programarVisita(idcpc, idventa, idcliente, direccion) {
    $("#idcpc_visita").val(idcpc);
    $("#idventa_visita").val(idventa);
    $("#idcliente_visita").val(idcliente);
    $("#direccion").val(direccion);

    $("#modalProgramarVisita").modal("show");
    $.post("controladores/usuario.php?op=selectEmpleado", function (r) {
        $("#idpersonal").html(r);
        $('#idpersonal').select2();
    });
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
            const data = JSON.parse(response);
            if (!data.success) {
                Swal.fire({
                    title: "Cliente",
                    icon: "error",
                    text: data.message,
                });
                return;
            }
            Swal.fire({
                title: "Cliente",
                icon: "success",
                text: data.message,
            });
            $("#formProgramarVisita")[0].reset();
            $("#modalProgramarVisita").modal("hide");
            tabla.ajax.reload();
        }
    });

});

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

function verArchivosAdjuntos(data) {

    if (typeof data === 'string') {
        data = JSON.parse(data);
    }

    let html = '';

    if (data.length === 0) {

        html = `
            <div class="alert alert-warning mb-0">
                No existen archivos adjuntos.
            </div>
        `;

    } else {

        data.forEach(function (item) {

            let extension = item.archivo.split('.').pop().toLowerCase();

            let icono = 'fa-file';

            if (['pdf'].includes(extension)) {
                icono = 'fa-file-pdf text-danger';
            }
            else if (['doc', 'docx'].includes(extension)) {
                icono = 'fa-file-word text-primary';
            }
            else if (['xls', 'xlsx'].includes(extension)) {
                icono = 'fa-file-excel text-success';
            }
            else if (['jpg', 'jpeg', 'png', 'webp'].includes(extension)) {
                icono = 'fa-file-image text-info';
            }
            else if (['mp4'].includes(extension)) {
                icono = 'fa-file-video text-warning';
            }

            html += `
                <div class="card mb-2">
                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>
                            <i class="fas ${icono} fa-lg mr-2"></i>
                            ${item.nombre_original}
                        </div>

                        <a href="files/seguimientos/${item.archivo}"
                           target="_blank"
                           class="btn btn-sm btn-primary">

                            <i class="fa fa-eye"></i> Ver
                        </a>

                    </div>
                </div>
            `;
        });
    }

    $("#contenidoAdjuntos").html(html);

    $("#modalAdjuntos").modal("show");
}

function descragarResumen() {
    const idcliente = $("#idcliente").val() || '';
    const fecha_inicio = $("#fecha_inicio").val();
    const fecha_fin = $("#fecha_fin").val();
    console.log(fecha_inicio, fecha_fin);

    const params = new URLSearchParams({
        idcliente: idcliente,
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin
    });
    console.log(`modelos/exports/exportar_cuentas_cobrar.php?${params.toString()}`);

    window.location.href = `modelos/exports/exportar_cuentas_cobrar.php?${params.toString()}`;
}


init();
