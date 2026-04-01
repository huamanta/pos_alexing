function initializeContratos() {
    $.post("controladores/venta.php?op=selectSucursal", function (r) {
        $("#idsucursal").html(r);
        $("#idsucursal").select2("");
        listar();
    });

    // Agregar event listeners para filtros
    $("#fecha_inicio, #fecha_fin, #estado, #idsucursal").on('change', function () {
        tabla.ajax.reload();
    });
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
    recargarTabla();
}


function listar() {

    tabla = $("#tbllistado")
        .dataTable({
            //"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
            aProcessing: true, //Activamos el procesamiento del datatables
            aServerSide: true, //Paginación y filtrado realizados por el servidor
            processing: true,
            language: {
                processing:
                    "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
            },
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            dom: '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>><"col-sm-12 col-md-4"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
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
                    // className: 'btn btn-success'
                },
                {
                    extend: "pdf",
                    text: "<i class='fas fa-file-pdf'></i>",
                    titleAttr: "Exportar a PDF",
                    // className: 'btn btn-danger'
                },
                {
                    extend: "colvis",
                    text: "<i class='fas fa-bars'></i>",
                    titleAttr: "",
                    // className: 'btn btn-danger'
                },
            ],
            ajax: {
                url: "controladores/contratos.php?op=listar",
                data: function (d) {
                    d.fecha_inicio = $("#fecha_inicio").val();
                    d.fecha_fin = $("#fecha_fin").val();
                    d.estado = $("#estado").val();
                    d.idsucursal = $("#idsucursal").val();
                },
                type: "get",
                dataType: "json",
                error: function (e) {
                    console.log(e.responseText);
                },
            },
            bDestroy: true,
            iDisplayLength: 5, //Paginación
            order: [[0, "desc"]], //Ordenar (columna,orden)
        })
        .DataTable();
}


initializeContratos();

function verContrato(idventa) {
    $('#modal-ver-contrato').modal('show');
    $('#idventa').val(idventa);
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
});


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
