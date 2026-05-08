var ventaActualCuotas = null;
var saldoActualCuotas = 0;
var tabla;
var tablaCuotasCredito;
var tbllistadoAbonos;

function initializeContratos() {
    $.post("controladores/venta.php?op=selectSucursal", function (r) {
        $("#idsucursal").html(r);
        $("#idsucursal").select2("");
        listar();
    });

    // Agregar event listeners para filtros
    $("#fecha_inicio, #fecha_fin, #estado, #idsucursal, #condicion, #input_frecuencia").on('change', function () {
        tabla.ajax.reload();
    });

    $('#navPosActive').addClass("treeview active");
    $('#navPos').addClass("treeview menu-open");
    $('#navContratos').addClass("active");
};

function recargarTabla() {
    if (typeof tabla !== 'undefined') {
        tabla.ajax.reload();
    }
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

function toNumber(valor) {
    if (valor === null || valor === undefined) return 0;
    return parseFloat((valor + '').replace(/,/g, '')) || 0;
}

function agregarComentario(comentario) {
    $('#modalComentario').modal('show');
    $('#comentarioCredito').val(comentario);
}

function verCuotasCredito(idventa, saldoPendiente, documento, nota) {
    let comentario = nota;
    $('#idventacuentacobrar').val(idventa);
    ventaActualCuotas = idventa;
    saldoActualCuotas = toNumber(saldoPendiente);
    $('#tituloCreditoCuotas').text(documento ? documento : '');

    $("#modalCuotasCredito").modal("show");
    let text_btn_coment = 'Agregar nota';
    if (comentario) {
        text_btn_coment = 'Actualizar nota';
    }
    let botones = [
        {
            text: '<i class="fas fa-comment-dots"></i> ' + text_btn_coment,
            className: 'btn btn-info btn-sm btn-comment',
            action: function () {
                agregarComentario(comentario);
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
            '<"row mb-2"' +
            '<"col-md-12 msgComentario">' +
            '>' +
            '<"row"' +
            '<"col-md-4"l>' +
            '<"col-md-4"B>' +
            '<"col-md-4"f>' +
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

        initComplete: function () {
            if (comentario) {
                $('.msgComentario').html(`<div class="alert alert-primary d-flex align-items-center" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <div style="margin-left: 10px">
                        `+ comentario + `
                    </div>
                </div>`);
            }
        }
    });
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
            }
        });
    });
}

async function amortizar(idventa) {
    const idcaja = await verificarCaja();
    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar la amortización', 'error');
        return;
    }
    
    $("#panel-pagar-cuotas").hide();

    $.ajax({
        url: 'controladores/cuentascobrar.php?op=cuotasPorPagar',
        data: { idventa: idventa },
        type: "GET",
        success: function (response) {
            let cuotas = JSON.parse(response);

            let totalCuotas = cuotas.length;

            let html = `
                <input 
                    type="range"
                    id="rangeCuotas"
                    min="1"
                    max="${totalCuotas}"
                    value="1"
                    step="1"
                >
            `;

            $("#contenedorRange").html(html);

            calcularTotal(1);

            $("#rangeCuotas").on("input", function () {

                let cantidad = parseInt($(this).val());

                calcularTotal(cantidad);
            });

            
            $("#montoPagarAmortizar").val('');
            function calcularTotal(cantidad) {

                $("#cantidadSeleccionada").text(cantidad);

                let total = 0;

                for (let i = 0; i < cantidad; i++) {
                    total += parseFloat(cuotas[i].deudatotal);
                }

                inicialCuota = cuotas[0].deudatotal

                $("#totalPagar").text(total.toFixed(2));
                $("#montoPagarAmortizar").val(total.toFixed(2));
            }
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
    $('#modalAmortizar').modal('show');
};

$("#btn-seleccionar-cuotas").click(function(e){
    e.preventDefault();
    $("#panel-pagar-cuotas").show();
    $("#montoPagarAmortizar").val(inicialCuota);
});
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
        success: function (data) {
            var data = JSON.parse(data);
            if (data.success) {
                Swal.fire('Éxito', data.message, 'success');
                tabla.ajax.reload();
                tablaCuotasCredito.ajax.reload();
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
        },
        error: function (e) {
            console.log(e.responseText);
        }
    });
});

function limpiarFiltros() {
    $("#fecha_inicio").val('');
    $("#fecha_fin").val('');
    $("#estado").val('Todos');
    $("#idsucursal").trigger('change');
    $("#condicion").val('Todos');
    $("#input_frecuencia").val('');
    recargarTabla();
}


function listar() {

    tabla = $("#tbllistado")
        .dataTable({
            aProcessing: true,
            aServerSide: true,
            processing: true,
            language: {
                processing:
                    "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
            },
            responsive: true,
            lengthChange: false,
            autoWidth: false,

            // 🔥 DOM PERSONALIZADO CON LEYENDA
            dom:
                '<"row"' +
                '<"col-sm-12 col-md-7 text-center"<"leyenda">>' +
                '<"col-sm-12 col-md-5 d-flex justify-content-end gap-2"<"dt-buttons btn-group flex-wrap"B>f>' +
                '>' +
                't' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

            lengthMenu: [
                [5, 10, 25, 50, 100, -1],
                [
                    "5 filas",
                    "10 filas",
                    "25 filas",
                    "50 filas",
                    "100 filas",
                    "Mostrar todo",
                ],
            ],

            buttons: [
                "pageLength",
                {
                    extend: "excelHtml5",
                    text: "<i class='fas fa-file-csv'></i>",
                    titleAttr: "Exportar a Excel",
                },
                {
                    extend: "pdf",
                    text: "<i class='fas fa-file-pdf'></i>",
                    titleAttr: "Exportar a PDF",
                },
                {
                    extend: "colvis",
                    text: "<i class='fas fa-bars'></i>",
                    titleAttr: "",
                },
            ],

            ajax: {
                url: "controladores/contratos.php?op=listar",
                data: function (d) {
                    d.fecha_inicio = $("#fecha_inicio").val();
                    d.fecha_fin = $("#fecha_fin").val();
                    d.idsucursal = $("#idsucursal").val();
                    d.estado = $("#estado").val();
                    d.condicion = $("#condicion").val();
                    d.frecuencia = $("#input_frecuencia").val();
                },
                type: "get",
                dataType: "json",
                error: function (e) {
                    console.log(e.responseText);
                },
            },

            bDestroy: true,
            iDisplayLength: 5,
            order: [[0, "desc"]],
            initComplete: function () {
                $(".leyenda").html(`
                    <div class="d-flex gap-3 flex-wrap align-items-center">
    
    <div class="d-flex align-items-center gap-1">
        <span class="badge bg-success d-inline-block" style="width: 15px; height: 15px;"></span>
        <small>Normal</small>
    </div>

    <div class="d-flex align-items-center gap-1">
        <span class="badge bg-warning d-inline-block" style="width: 15px; height: 15px;"></span>
        <small>1 - 30% Letras atrasadas</small>
    </div>

    <div class="d-flex align-items-center gap-1">
        <span class="badge bg-orange d-inline-block" style="width: 15px; height: 15px;"></span>
        <small>31 - 60% Letras atrasadas</small>
    </div>

    <div class="d-flex align-items-center gap-1">
        <span class="badge bg-danger d-inline-block" style="width: 15px; height: 15px;"></span>
        <small>+60% Letras atrasadas</small>
    </div>

</div>
                `);
            }
        })
        .DataTable();
}


initializeContratos();

function verContrato(idventa, idcliente, nombre) {
    $('#modal-ver-contrato').modal('show');
    $('#idventa').val(idventa);
    $('#idcliente').val(idcliente);
    $('#comprador').val(nombre);

}

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

function limpiarModalClienteCompraVenta() {
    $('#cliente_idpersona').val('');
    $('#cliente_tipo_persona').val('Cliente');
    $('#cliente_nombre').val('');
    $('#cliente_tipo_documento').val('DNI');
    $('#cliente_num_documento').val('');
    $('#cliente_telefono').val('');
    $('#cliente_direccion').val('');
    $('#cliente_email').val('');
}

function abrirModalNuevoComprador() {
    limpiarModalClienteCompraVenta();
    $('#modalClienteCompraVentaLabel').text('Nuevo cliente');
    $('#modal-cliente-compra-venta').modal('show');
}

function abrirModalEditarComprador() {
    const idcliente = $('#idcliente').val();

    if (!idcliente) {
        Swal.fire('Aviso', 'Primero selecciona un comprador para poder editarlo.', 'info');
        return;
    }

    limpiarModalClienteCompraVenta();
    $('#modalClienteCompraVentaLabel').text('Editar cliente');

    $.post('controladores/persona.php?op=mostrar', { idpersona: idcliente }, function (data) {
        let cliente = null;
        try {
            cliente = JSON.parse(data);
        } catch (e) {
            Swal.fire('Error', 'No se pudo cargar la información del cliente.', 'error');
            return;
        }

        if (!cliente || !cliente.idpersona) {
            Swal.fire('Error', 'Cliente no encontrado.', 'error');
            return;
        }

        $('#cliente_idpersona').val(cliente.idpersona || '');
        $('#cliente_tipo_persona').val(cliente.tipo_persona || 'Cliente');
        $('#cliente_nombre').val(cliente.nombre || '');
        $('#cliente_tipo_documento').val(cliente.tipo_documento || 'DNI');
        $('#cliente_num_documento').val(cliente.num_documento || '');
        $('#cliente_telefono').val(cliente.telefono || '');
        $('#cliente_direccion').val(cliente.direccion || '');
        $('#cliente_email').val(cliente.email || '');

        $('#modal-cliente-compra-venta').modal('show');
    });
}

function seleccionarCompradorEnFormulario(idpersona, nombre) {
    $('#idcliente').val(idpersona);
    $('#comprador').val(nombre);
}

function obtenerClientePorDocumento(numeroDocumento, callback) {
    $.post('controladores/venta.php?op=selectCliente3&numero=' + numeroDocumento, function (data) {
        let cliente = null;
        try {
            cliente = JSON.parse(data);
        } catch (e) {
            callback(null);
            return;
        }
        callback(cliente);
    }).fail(function () {
        callback(null);
    });
}

function encrypt_decrypt(action, string) {
    if (action === 'encrypt') {
        // Encriptación simple pero efectiva para este caso
        const encoded = btoa(string);
        return encoded.replace(/=/g, '').replace(/\//g, '_').replace(/\+/g, '-');
    }
    return string;
}

function descargarContrato(idventa) {
    const encryptedId = encrypt_decrypt('encrypt', idventa);
    const url = 'public/docs_service/contrato?idventa=' + encryptedId; // Sin .php

    const win = window.open(url, '_blank');
    if (!win) {
        alert('Por favor habilita ventanas emergentes o descarga manualmente: ' + url);
        return;
    }
}

function descargarActaEntrega(idventa) {
    const encryptedId = encrypt_decrypt('encrypt', idventa);
    const url = 'public/docs_service/acta_entrega?idventa=' + encryptedId; // Sin .php

    const win = window.open(url, '_blank');
    if (!win) {
        alert('Por favor habilita ventanas emergentes o descarga manualmente: ' + url);
        return;
    }
}

function descargarOrdenRecojo(idventa) {
    const encryptedId = encrypt_decrypt('encrypt', idventa);
    const url = 'public/docs_service/orden_recojo?idventa=' + encryptedId; // Sin .php
    const win = window.open(url, '_blank');
    if (!win) {
        alert('Por favor habilita ventanas emergentes o descarga manualmente: ' + url);
        return;
    }
}

function descargarCronogramaPagos(idventa) {
    const encryptedId = encrypt_decrypt('encrypt', idventa);
    const url = 'public/docs_service/cronograma_pagos?idventa=' + encryptedId;
    const win = window.open(url, '_blank');
    if (!win) {
        alert('Por favor habilita ventanas emergentes o descarga manualmente: ' + url);
        return;
    }
}

function descargarCompraVenta(idventa, idvendedor, idcliente, monto) {
    const encryptedId = encrypt_decrypt('encrypt', idventa);
    const url = 'public/docs_service/compra_venta?idventa=' + encryptedId + '&idvendedor=' + idvendedor + '&idcliente=' + idcliente + '&monto=' + monto;
    const win = window.open(url, '_blank');
    if (!win) {
        alert('Por favor habilita ventanas emergentes o descarga manualmente: ' + url);
        return;
    }
}

$(document).ready(function () {
    $('#btnDescargarContrato').on('click', function () {
        const idventa = $('#idventa').val();
        descargarContrato(idventa);
    });

    $('#btnDescargarActaEntrega').on('click', function () {
        const idventa = $('#idventa').val();
        descargarActaEntrega(idventa);
    });

    $('#btnDescargarOrdenRecojo').on('click', function () {
        const idventa = $('#idventa').val();
        descargarOrdenRecojo(idventa);
    });

    $('#btnDescargarCronogramaPagos').on('click', function () {
        const idventa = $('#idventa').val();
        descargarCronogramaPagos(idventa);
    });

    $('#btnDescargarCompraVenta').on('click', function () {
        $('#modal-compra-venta').modal('show');
        const idventa = $('#idventa').val();
        $('#idventa_compra_venta').val(idventa);
        listarUsuarios();
    });

    $('#btnNuevoComprador').on('click', function () {
        abrirModalNuevoComprador();
    });

    $('#btnEditarComprador').on('click', function () {
        abrirModalEditarComprador();
    });

    $('#form-cliente-compra-venta').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const idpersonaActual = $('#cliente_idpersona').val();
        const numeroDocumento = $('#cliente_num_documento').val();

        $.ajax({
            url: 'controladores/persona.php?op=guardaryeditar',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (resp) {
                if (idpersonaActual) {
                    seleccionarCompradorEnFormulario(idpersonaActual, $('#cliente_nombre').val());
                    $('#modal-cliente-compra-venta').modal('hide');
                    Swal.fire('Cliente', resp, 'success');
                    return;
                }

                obtenerClientePorDocumento(numeroDocumento, function (cliente) {
                    if (cliente && cliente.idpersona) {
                        seleccionarCompradorEnFormulario(cliente.idpersona, cliente.nombre || $('#cliente_nombre').val());
                        $('#modal-cliente-compra-venta').modal('hide');
                        Swal.fire('Cliente', resp, 'success');
                    } else {
                        Swal.fire('Aviso', 'Se guardó el cliente, pero no se pudo seleccionar automáticamente. Selecciónalo manualmente.', 'warning');
                    }
                });
            },
            error: function () {
                Swal.fire('Error', 'No se pudo guardar el cliente.', 'error');
            }
        });
    });
});


function listarUsuarios() {
    const idventa = $('#idventa').val();
    const idsucursal = $('#idsucursal').val();
    $.post("controladores/contratos.php?op=selectUsuarios", { idventa: idventa, idsucursal: idsucursal }, function (r) {
        $("#idvendedor").html(r);
        $("#idvendedor").select2("");
        listar();
    });
}


function retenerContrato(idventa) {
    $("#modal-retener-contrato").modal("show");
    $("#idventa_retenida").val(idventa);
}

$("#form-retener-contrato").on("submit", function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: "controladores/contratos.php?op=retener",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            var res = JSON.parse(response);
            if (res.status) {
                Swal.fire("¡Contrato retenido!", res.message, "success");
                $("#modal-retener-contrato").modal("hide");
                recargarTabla();
            } else {
                Swal.fire("Error", res.message, "error");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la solicitud AJAX:", error);
            Swal.fire("Error", "Ocurrió un error al procesar la solicitud.", "error");
        },
    });
});


function quitarRetencion(idventa, idretencion) {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Esta acción quitará la retención del contrato.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, quitar retención",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "controladores/contratos.php?op=quitar_retencion",
                type: "POST",
                data: { idventa: idventa, idretencion: idretencion },
                success: function (response) {
                    var res = JSON.parse(response);
                    if (res.status) {
                        Swal.fire("¡Retención quitada!", res.message, "success");
                        recargarTabla();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error en la solicitud AJAX:", error);
                    Swal.fire("Error", "Ocurrió un error al procesar la solicitud.", "error");
                }
            });
        }
    });
}


$("#form-compra-venta").on("submit", function (e) {
    e.preventDefault();
    var idventa = $("#idventa_compra_venta").val();
    var idvendedor = $("#idvendedor").val();
    var idcliente = $("#idcliente").val();
    var monto = $("#monto_compra_venta").val();

    if (!idvendedor) {
        Swal.fire("Error", "Por favor selecciona un vendedor.", "error");
        return;
    }

    if (!idcliente) {
        Swal.fire("Error", "Por favor selecciona un comprador.", "error");
        return;
    }

    if (!monto || isNaN(monto) || parseFloat(monto) <= 0) {
        Swal.fire("Error", "Por favor ingresa un monto válido.", "error");
        return;
    }

    descargarCompraVenta(idventa, idvendedor, idcliente, monto);
});

function mostrarAbonos(idcpc) {

    $("#getCodeModal2").modal('show');

    $.post("controladores/cuentascobrar.php?op=mostrar", { idcpc: idcpc }, function (data, status) {

        data = JSON.parse(data);

        var label = document.querySelector('#abonoTotal2');
        label.textContent = data.deuda;

        var label = document.querySelector('#abonoTotal');
        label.textContent = data.abonototal;

    });

    tbllistadoAbonos = $('#tbllistadoAbonos').dataTable(
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


async function mostrar(idcpc) {

    const idcaja = await verificarCaja(); // Verifica la caja abierta

    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar abonos', 'error');
        return;
    }

    $("#idcaja2").val(idcaja);
    $("#getCodeModal").modal('show');

    // 🔹 1. Actualizar la mora en BD antes de mostrar el formulario
    $.post("controladores/cuentascobrar.php?op=actualizar_mora_diaria",
        {
            idcpc: idcpc
        },
        function () {

            // 🔹 2. Obtener datos actualizados
            $.post("controladores/cuentascobrar.php?op=mostrar",
                {
                    idcpc: idcpc
                },
                function (data) {

                    var data = JSON.parse(data);
                    var total_venta = parseFloat(data.total_venta);
                    var interes = total_venta * (data.interes / 100);
                    var deuda = parseFloat(data.deuda);
                    $('#documento2').text(data.tipo_comprobante + " : " + data.serie_comprobante + " - " + data.num_comprobante);
                    $("#deutaTotal").text(deuda.toFixed(2));
                    $("#valorVenta").text(total_venta.toFixed(2));
                    $("#valorInteres").text(interes.toFixed(2));
                    $("#montoAdeudado").val(deuda.toFixed(2));
                    $("#idcpc2").val(data.idcpc);

                    $("#idventa2").val(data.idventa);
                    $("#fechavencimiento").text(data.fechavencimiento);

                });
        });
}

$('#formulario-pagar').submit(function (e) {
    e.preventDefault();
    guardaryeditar(e);
});

async function guardaryeditar(e) {
    e.preventDefault();

    const idcaja = await verificarCaja(); // Verifica caja abierta antes de enviar
    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar abonos', 'error');
        return;
    }

    var formData = new FormData($("#formulario-pagar")[0]);
    formData.append('idcaja', idcaja); // Asegura idcaja en el formulario

    $.ajax({
        url: "controladores/cuentascobrar.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            let res = JSON.parse(datos);
            if (res.success) {
                Swal.fire('Éxito', res.message, 'success');
                $('#getCodeModal').modal('hide');
                $("#formulario-pagar")[0].reset();
                limpiar();
                tabla.ajax.reload();
                if (tablaCuotasCredito) {
                    tablaCuotasCredito.ajax.reload();
                }
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
}