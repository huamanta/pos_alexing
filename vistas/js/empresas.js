var tabla;



//Función que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#myModal").on("submit", function (e) {
        guardaryeditar(e);
    });

    $('#navConfiguracionActive').addClass("treeview active");
    $('#navConfiguracion').addClass("treeview menu-open");
    $('#navSucursal').addClass("active");

}

//Función limpiar
function limpiar() {
    $("#nombre").val("");
    $("#idempresa").val("");
    $("#ruc").val("");
    $("#razon_social").val("");
    $("#departamento").val("");
    $("#provincia").val("");
    $("#distrito").val("");
    $("#ubigeo").val("");
    $("#ubigeo_display").text("");
    $("#moneda").val("");
    $("#simbolo").val("");
}

//Función mostrar formulario
function mostrarform(flag) {
    limpiar();

    if (flag) {
        $("#listadoregistros").show();
        $("#detalles tbody").html("");

        // Llenar tabla con comprobantes por defecto para nueva empresa
        var comprobantes = ['Factura', 'Boleta', 'Nota de Venta', 'Cotización', 'NC', 'NCB', 'Orden Compra', 'Ticket', 'Guia de Remisión'];
        var series = ['F001', 'B001', 'NV01', 'Q001', 'NC01', 'ND01', 'OC01', 'TK01', 'T001'];

        comprobantes.forEach(function (comp, index) {
            var fila = '<tr>' +
                '<td><input class="form-control" type="text" name="nombreSucursal[]" value="' + comp + '"></td>' +
                '<td><input class="form-control" type="text" name="serie[]" value="' + series[index] + '"></td>' +
                '<td><input class="form-control" type="text" name="numero[]" value="1"></td>' +
                '</tr>';
            $("#detalles tbody").append(fila);
        });

        $('#myModal').modal('show');

        $('#myModal').off('shown.bs.modal').on('shown.bs.modal', function () {
            // Resetear al tab de información general
            $('#tab-general').tab('show');
        });
    }
}



$("#btnNuevoSucursal").on("click", function () {
    mostrarform(true);
});

//Función cancelarform
function cancelarform() {
    limpiar();
    mostrarform(false);
}

//Función Listar
function listar() {
    tabla = $('#tbllistado').dataTable(
        {
            //"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
            "aProcessing": true,//Activamos el procesamiento del datatables
            "aServerSide": true,//Paginación y filtrado realizados por el servidor
            "processing": true,
            "language":
            {
                "processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
            },
            "responsive": true, "lengthChange": false, "autoWidth": false,
            dom: '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>><"col-sm-12 col-md-4"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [
                [5, 10, 25, 50, 100, -1],
                ['5 filas', '10 filas', '25 filas', '50 filas', '100 filas', 'Mostrar todo']
            ],
            buttons: ['pageLength',
                {
                    extend: 'excelHtml5',
                    text: "<i class='fas fa-file-csv'></i>",
                    titleAttr: 'Exportar a Excel',
                    // className: 'btn btn-success'
                },
                {
                    extend: 'pdf',
                    text: "<i class='fas fa-file-pdf'></i>",
                    titleAttr: 'Exportar a PDF',
                    // className: 'btn btn-danger'
                },
                {
                    extend: 'colvis',
                    text: "<i class='fas fa-bars'></i>",
                    titleAttr: '',
                    // className: 'btn btn-danger'
                }],
            "ajax":
            {
                url: 'controladores/empresas.php?op=listarEmpresas',
                type: "get",
                dataType: "json",
                error: function (e) {
                    console.log(e.responseText);
                }
            },
            "bDestroy": true,
            "iDisplayLength": 10,//Paginación
            "order": [[0, "desc"]]//Ordenar (columna,orden)
        }).DataTable();
}
//Función para guardar o editar

function guardaryeditar(e) {
    e.preventDefault(); //No se activará la acción predeterminada del evento
    //$("#btnGuardar").prop("disabled",true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "controladores/empresas.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function (response) {
            const data = response;
            if (!data.success) {
                Swal.fire({
                    title: 'Empresa',
                    icon: 'error',
                    text: data.message
                });
                return;
            }
            Swal.fire({
                title: 'Empresa',
                icon: 'success',
                text: data.message
            });
            $('#myModal').modal('hide');
            mostrarform(false);
            tabla.ajax.reload();
            limpiar();
        },
        error: function (error) {
            Swal.fire({
                title: 'Empresa',
                icon: 'error',
                text: error.responseJSON.message || 'Erorrs'
            });
        }
    });
    //location.reload();
}

function mostrar(idempresa) {
    $.post("controladores/empresas.php?op=mostrarEmpresa",
        { idempresa: idempresa },
        function (response) {
            const data = response;
            // 👉 abrir modal
            limpiar();
            $("#detalles tbody").html("");
            $('#myModal').modal('show');

            // 👉 setear idempresa
            $("#idempresa").val(data.idempresa);
            // datos generales
            $("#ruc").val(data.ruc);
            $("#razon_social").val(data.razon_social);
            $("#usuario_sol").val(data.usuario_sol);
            $("#clave_sol").val(data.clave_sol);
            $("#clave_certificado").val(data.clave_certificado);
            $("#estado_certificado").val(data.estado_certificado);
            $("#client_id").val(data.client_id);
            $("#client_secret").val(data.client_secret);
            $("#nombre_impuesto").val(data.nombre_impuesto);
            $("#monto_impuesto").val(data.monto_impuesto);

            // Cargar comprobantes
            cargarComprobantes(data.idempresa);
        });
}

// Función para cargar comprobantes desde servidor
function cargarComprobantes(idempresa) {
    $.post("controladores/empresas.php?op=mostrarComprobantes",
        { idempresa: idempresa },
        function (response) {
            const data = response;
            $("#detalles tbody").html("");

            if (data && data.length > 0) {
                data.forEach(function (comp) {
                    var fila = '<tr>' +
                        '<td><input class="form-control" type="text" name="nombreSucursal[]" value="' + comp.nombre + '"></td>' +
                        '<td><input class="form-control" type="text" name="serie[]" value="' + comp.serie_comprobante + '"></td>' +
                        '<td><input class="form-control" type="text" name="numero[]" value="' + comp.num_comprobante + '"></td>' +
                        '</tr>';
                    $("#detalles tbody").append(fila);
                });
            }
        }
    );
}

// Event listener para el tab de comprobantes
$(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    if ($(e.target).attr('href') === '#comprobantes-content') {
        var idempresa = $("#idempresa").val();
        if (idempresa) {
            cargarComprobantes(idempresa);
        }
    }
});


//Función para desactivar registros
function desactivar(idempresa) {
    Swal.fire({
        title: "¿Desactivar?",
        text: "¿Está seguro que desea desactivar la empresa?",
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "No",
        confirmButtonText: "Sí, desactivar",
        confirmButtonColor: "#0004FA",
        cancelButtonColor: "#FF0000",
        showLoaderOnConfirm: true,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("controladores/empresas.php?op=activar_descativar", { idempresa: idempresa, estado: 0 }, function (response) {
                const data = response;
                if (!data.success) {
                    Swal.fire({
                        title: 'Empresa',
                        icon: 'error',
                        text: data.message
                    });
                    return;
                }
                Swal.fire({
                    title: 'Empresa',
                    icon: 'success',
                    text: data.message
                });
                tabla.ajax.reload();
            });
        }
    });
}

//Función para activar registros
function activar(idempresa) {
    Swal.fire({
        title: "¿Activar?",
        text: "¿Está seguro que desea activar la empresa?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, activar",
        cancelButtonText: "No",
        confirmButtonColor: "#0004FA",
        cancelButtonColor: "#FF0000",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("controladores/empresas.php?op=activar_descativar", { idempresa: idempresa, estado: 1 }, function (response) {
                const data = response;
                if (!data.success) {
                    Swal.fire({
                        title: 'Empresa',
                        icon: 'error',
                        text: data.message
                    });
                    return;
                }
                Swal.fire({
                    title: 'Empresa',
                    icon: 'success',
                    text: data.message
                });
                tabla.ajax.reload();
            });
        }
    });
}

//Función para eliminar registros
function eliminar(idsucursal) {
    Swal.fire({
        title: '¿Eliminar?',
        text: '¿Está seguro que desea eliminar la sucursal y sus comprobantes de pago asociados?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0004FA',
        cancelButtonColor: '#FF0000',
        confirmButtonText: 'Sí',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(
                "controladores/sucursal.php?op=eliminar",
                { idsucursal: idsucursal },
                function (e) {
                    Swal.fire(
                        'Eliminada',
                        'La sucursal ha sido eliminada correctamente',
                        'success'
                    );
                    tabla.ajax.reload();
                }
            );
        }
    });
}


init();