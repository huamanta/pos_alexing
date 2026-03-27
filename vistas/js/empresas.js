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
        $('#myModal').modal('show');

        $('#myModal').off('shown.bs.modal').on('shown.bs.modal', function () {


            if ($("#idsucursal").val() === "") {
                obtenerSerieIncrementada();
            }

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

        success: function (datos) {
            var jsonData = JSON.parse(datos);

            Swal.fire({
                title: 'Empresa',
                icon: jsonData.status,
                text: jsonData.message
            });

            if (jsonData.code != 200) {
                return;
            }

            $('#myModal').modal('hide');

            mostrarform(false);
            tabla.ajax.reload();


        }

    });
    limpiar();
    //location.reload();
}

function mostrar(idempresa) {
    $.post("controladores/empresas.php?op=mostrarEmpresa",
        { idempresa: idempresa },
        function (data) {
            data = JSON.parse(data);
            console.log(data);
            

            // 👉 abrir modal SIN autogenerar serie
            limpiar();
            $("#detalles tbody").html("");
            $('#myModal').modal('show');

            // 👉 setear idsucursal ANTES
            $("#idempresa").val(data.idempresa);
            // datos generales
            $("#ruc").val(data.ruc);
            $("#razon_social").val(data.razon_social);
            $("#usuario_sol").val(data.usuario_sol);
            $("#clave_sol").val(data.clave_sol);
            $("#ruta_certificado").val(data.ruta_certificado);
            $("#clave_certificado").val(data.clave_certificado);
            $("#estado_certificado").val(data.estado_certificado);
            $("#client_id").val(data.client_id);
            $("#client_secret").val(data.client_secret);
            $("#nombre_impuesto").val(data.nombre_impuesto);
            $("#monto_impuesto").val(data.monto_impuesto);
        });
}


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
        reverseButtons: true,
        preConfirm: () => {
            return $.post(
                "controladores/empresas.php?op=activar_descativar",
                { idempresa: idempresa, estado: 0 }
            ).then(response => {
                return response;
            }).catch(() => {
                Swal.showValidationMessage("Error en la solicitud");
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            var jsonData = JSON.parse(result.value);
            Swal.fire({
                title: 'Empresa',
                icon: jsonData.status,
                text: jsonData.message
            });
            if (jsonData.code != 200) {
                return;
            }
            tabla.ajax.reload();
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
            $.post("controladores/empresas.php?op=activar_descativar", { idempresa: idempresa, estado: 1 }, function (e) {
                var jsonData = JSON.parse(e);
                Swal.fire({
                    title: 'Empresa',
                    icon: jsonData.status,
                    text: jsonData.message
                });
                if (jsonData.code != 200) {
                    return;
                }
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