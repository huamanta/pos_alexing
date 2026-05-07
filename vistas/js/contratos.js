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
    if (!latitude && !longitude){
        notificacionToast('warning', 'El cliente no tiene ubicacion configurada');
        return;
    };

    if (direccion){

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




