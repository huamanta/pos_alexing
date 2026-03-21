var tabla;

var selectedDepartmentId = "";
var selectedDepartmentName = "";
var selectedProvinceId = "";
var selectedProvinceName = "";
var selectedDistrictId = "";
var selectedDistrictName = "";


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

    comprobantes();
    cargarDepartamentos();

    // Event listeners for Ubigeo dropdowns
    $("#departamento_select").change(function () {
        selectedDepartmentId = $(this).val();
        selectedDepartmentName = $(this).find("option:selected").text();
        $("#departamento").val(selectedDepartmentName); // Set hidden input with name

        $("#provincia_select").html('<option value="">Seleccione Provincia</option>').prop('disabled', true);
        $("#distrito_select").html('<option value="">Seleccione Distrito</option>').prop('disabled', true);
        $("#provincia").val("");
        $("#distrito").val("");
        selectedProvinceId = "";
        selectedProvinceName = "";
        selectedDistrictId = "";
        selectedDistrictName = "";

        if (selectedDepartmentId) {
            $("#provincia_select").prop('disabled', false);
            cargarProvincias(selectedDepartmentId);
        }
        updateUbigeoDisplay();
    });

    $("#provincia_select").change(function () {
        selectedProvinceId = $(this).val();
        selectedProvinceName = $(this).find("option:selected").text();
        $("#provincia").val(selectedProvinceName); // Set hidden input with name

        $("#distrito_select").html('<option value="">Seleccione Distrito</option>').prop('disabled', true);
        $("#distrito").val("");
        selectedDistrictId = "";
        selectedDistrictName = "";

        if (selectedProvinceId) {
            $("#distrito_select").prop('disabled', false);
            cargarDistritos(selectedProvinceId);
        }
        updateUbigeoDisplay();
    });

    $("#distrito_select").change(function () {
        selectedDistrictId = $(this).val();
        selectedDistrictName = $(this).find("option:selected").text();
        $("#distrito").val(selectedDistrictName); // Set hidden input with name
        updateUbigeoDisplay();
    });


    $.post("controladores/sucursal.php?op=selectEmpresas", function (r) {

        var data = JSON.parse(r);

        $("#idempresa").select2({
            placeholder: "Seleccionar Almacén ...",
            allowClear: true,
            data: data.map(function (item) {
                return {
                    id: item.idempresa,
                    text: `${item.razon_social} - ${item.ruc}`
                };
            })
        }).val(null).trigger("change");
    });

}

//Función limpiar
function limpiar() {
    $("#nombre").val("");
    $("#idsucursal").val("");
    $("#direccion").val("");
    $("#telefono").val("");

    // Clear and reset Ubigeo dropdowns
    $("#departamento_select").val("").trigger("change"); // Trigger change to reset provinces/districts
    $("#provincia_select").html('<option value="">Seleccione Provincia</option>').prop('disabled', true);
    $("#distrito_select").html('<option value="">Seleccione Distrito</option>').prop('disabled', true);

    $("#departamento").val("");
    $("#provincia").val("");
    $("#distrito").val("");
    $("#ubigeo").val("");
    $("#ubigeo_display").text("");
    $("#idempresa").val(null).trigger("change");
    $("#moneda").val("");
    $("#simbolo").val("");

    selectedDepartmentId = "";
    selectedDepartmentName = "";
    selectedProvinceId = "";
    selectedProvinceName = "";
    selectedDistrictId = "";
    selectedDistrictName = "";
}

//Function to update the hidden ubigeo field and display span
function updateUbigeoDisplay() {
    let ubigeoCode = "";
    let ubigeoDisplayValue = ""; // Changed ubigeoText to ubigeoDisplayValue

    // The full ubigeo code comes from the district ID
    if (selectedDistrictId) {
        ubigeoCode = selectedDistrictId; // Use the district's ID directly as it's the full code
        ubigeoDisplayValue = selectedDistrictId; // Display the 6-digit code
    } else if (selectedProvinceId) {
        ubigeoCode = "";
        ubigeoDisplayValue = ""; // If only province is selected, display nothing for ubigeo code
    } else if (selectedDepartmentId) {
        ubigeoCode = "";
        ubigeoDisplayValue = ""; // If only department is selected, display nothing
    } else {
        ubigeoCode = "";
        ubigeoDisplayValue = "";
    }

    $("#ubigeo").val(ubigeoCode);
    $("#ubigeo_display").text(ubigeoDisplayValue);
}

// Load Departments
function cargarDepartamentos() {
    $.post("controladores/sucursal.php?op=listarDepartamentos", function (data) {
        data = JSON.parse(data);
        var select = $("#departamento_select");
        select.html('<option value="">Seleccione Departamento</option>');
        $.each(data, function (index, item) {
            select.append('<option value="' + item.id + '">' + item.name + '</option>');
        });
    });
}

// Load Provinces by Department ID
function cargarProvincias(id_department) {
    $.post("controladores/sucursal.php?op=listarProvinciasPorDepartamento", { id_department: id_department }, function (data) {
        data = JSON.parse(data);
        var select = $("#provincia_select");
        select.html('<option value="">Seleccione Provincia</option>');
        $.each(data, function (index, item) {
            select.append('<option value="' + item.id + '">' + item.name + '</option>');
        });
    });
}

// Load Districts by Province ID
function cargarDistritos(id_province) {
    $.post("controladores/sucursal.php?op=listarDistritosPorProvincia", { id_province: id_province }, function (data) {
        data = JSON.parse(data);
        var select = $("#distrito_select");
        select.html('<option value="">Seleccione Distrito</option>');
        $.each(data, function (index, item) {
            select.append('<option value="' + item.id + '">' + item.name + '</option>');
        });
    });
}

//Función mostrar formulario
function mostrarform(flag) {
    limpiar();

    if (flag) {
        $("#listadoregistros").show();
        $("#detalles tbody").html("");
        comprobantes();

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


function comprobantes() {

    var fila = '<tr class="filas" id="fila">' +
        '<td><input class="form-control" style="text-align:center; width: 150px;" type="text" name="nombreSucursal[]" id="nombreSucursal[]" value="Boleta"></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="serie[]" id="serie[]" value=""></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="numero[]" id="numero[]" value="9999999"></td>' +
        '</tr>';

    var fila1 = '<tr class="filas" id="fila">' +
        '<td><input class="form-control" style="text-align:center; width: 150px;" type="text" name="nombreSucursal[]" id="nombreSucursal[]" value="Factura"></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="serie[]" id="serie[]" value=""></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="numero[]" id="numero[]" value="9999999"></td>' +
        '</tr>';

    var fila2 = '<tr class="filas" id="fila">' +
        '<td><input class="form-control" style="text-align:center; width: 150px;" type="text" name="nombreSucursal[]" id="nombreSucursal[]" value="Nota de Venta"></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="serie[]" id="serie[]" value=""></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="numero[]" id="numero[]" value="9999999"></td>' +
        '</tr>';

    var fila3 = '<tr class="filas" id="fila">' +
        '<td><input class="form-control" style="text-align:center; width: 150px;" type="text" name="nombreSucursal[]" id="nombreSucursal[]" value="Cotización"></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="serie[]" id="serie[]" value=""></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="numero[]" id="numero[]" value="9999999"></td>' +
        '</tr>';

    var fila4 = '<tr class="filas" id="fila">' +
        '<td><input class="form-control" style="text-align:center; width: 150px;" type="text" name="nombreSucursal[]" id="nombreSucursal[]" value="NC"></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="serie[]" id="serie[]" value=""></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="numero[]" id="numero[]" value="9999999"></td>' +
        '</tr>';

    var fila5 = '<tr class="filas" id="fila">' +
        '<td><input class="form-control" style="text-align:center; width: 150px;" type="text" name="nombreSucursal[]" id="nombreSucursal[]" value="NCB"></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="serie[]" id="serie[]" value=""></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="numero[]" id="numero[]" value="9999999"></td>' +
        '</tr>';

    var fila6 = '<tr class="filas" id="fila">' +
        '<td><input class="form-control" style="text-align:center; width: 150px;" type="text" name="nombreSucursal[]" id="nombreSucursal[]" value="Orden Compra"></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="serie[]" id="serie[]" value=""></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="numero[]" id="numero[]" value="9999999"></td>' +
        '</tr>';

    var fila7 = '<tr class="filas" id="fila">' +
        '<td><input class="form-control" style="text-align:center; width: 150px;" type="text" name="nombreSucursal[]" id="nombreSucursal[]" value="Ticket"></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="serie[]" id="serie[]" value=""></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="numero[]" id="numero[]" value="9999999"></td>' +
        '</tr>';

    var fila8 = '<tr class="filas" id="fila">' +
        '<td><input class="form-control" style="text-align:center; width: 150px;" type="text" name="nombreSucursal[]" id="nombreSucursal[]" value="Guia de Remision"></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="serie[]" id="serie[]" value=""></td>' +
        '<td><input class="form-control" style="text-align:center; width: 80px;" type="text" name="numero[]" id="numero[]" value="9999999"></td>' +
        '</tr>';

    $('#detalles').append(fila + fila1 + fila2 + fila3 + fila4 + fila5 + fila6 + fila7 + fila8);

}

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
                url: 'controladores/sucursal.php?op=listarSucursales',
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
        url: "controladores/sucursal.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function (datos) {

            Swal.fire({
                title: 'Sucursal',
                icon: 'success',
                text: datos
            });

            $('#myModal').modal('hide');

            mostrarform(false);
            tabla.ajax.reload();


        }

    });
    limpiar();
    //location.reload();
}

function mostrar(idsucursal) {
    $.post("controladores/sucursal.php?op=mostrarSucursal",
        { idsucursal: idsucursal },
        function (data) {
            data = JSON.parse(data);
            console.log(data);
            

            // 👉 abrir modal SIN autogenerar serie
            limpiar();
            $("#detalles tbody").html("");
            $('#myModal').modal('show');

            // 👉 setear idsucursal ANTES
            $("#idsucursal").val(data[0].idsucursal);

            // datos generales
            $("#nombre").val(data[0].nombre);
            $("#direccion").val(data[0].direccion);
            $("#telefono").val(data[0].telefono);

            // Store fetched Ubigeo data
            let fetchedDepartamento = data[0].departamento;
            let fetchedProvincia = data[0].provincia;
            let fetchedDistrito = data[0].distrito;
            let fetchedUbigeo = data[0].ubigeo;

            // Populate global variables for names immediately
            selectedDepartmentName = fetchedDepartamento;
            selectedProvinceName = fetchedProvincia;
            selectedDistrictName = fetchedDistrito;

            // Set hidden inputs (these should reflect the fetched names/ID)
            $("#departamento").val(fetchedDepartamento);
            $("#provincia").val(fetchedProvincia);
            $("#distrito").val(fetchedDistrito);
            $("#ubigeo").val(fetchedUbigeo);
            $("#ubigeo_display").text(fetchedDepartamento + " - " + fetchedProvincia + " - " + fetchedDistrito);
            $("#idempresa").val(data[0].idempresa).trigger("change");
            $("#moneda").val(data[0].moneda);
            $("#simbolo").val(data[0].simbolo);
            // Load and select Department
            $.post("controladores/sucursal.php?op=listarDepartamentos", function (depsData) {
                depsData = JSON.parse(depsData);
                var selectDep = $("#departamento_select");
                selectDep.html('<option value="">Seleccione Departamento</option>');
                $.each(depsData, function (index, item) {
                    selectDep.append('<option value="' + item.id + '">' + item.name + '</option>');
                });

                // Select the fetched department
                let depIdToSelect = fetchedUbigeo.substring(0, 2);
                selectDep.val(depIdToSelect);
                selectedDepartmentId = depIdToSelect; // Set ID here too

                // Load and select Province (nested within department success)
                if (depIdToSelect) {
                    $("#provincia_select").prop('disabled', false);
                    $.post("controladores/sucursal.php?op=listarProvinciasPorDepartamento", { id_department: depIdToSelect }, function (provsData) {
                        provsData = JSON.parse(provsData);
                        var selectProv = $("#provincia_select");
                        selectProv.html('<option value="">Seleccione Provincia</option>');
                        $.each(provsData, function (index, item) {
                            selectProv.append('<option value="' + item.id + '">' + item.name + '</option>');
                        });

                        // Select the fetched province - Use 4 digits for province ID
                        let provIdToSelect = fetchedUbigeo.substring(0, 4);
                        selectProv.val(provIdToSelect);
                        selectedProvinceId = provIdToSelect; // Set ID here too

                        // Load and select District (nested within province success)
                        if (provIdToSelect) {
                            $("#distrito_select").prop('disabled', false);
                            $.post("controladores/sucursal.php?op=listarDistritosPorProvincia", { id_province: provIdToSelect }, function (distsData) {
                                distsData = JSON.parse(distsData);
                                var selectDist = $("#distrito_select");
                                selectDist.html('<option value="">Seleccione Distrito</option>');
                                $.each(distsData, function (index, item) {
                                    selectDist.append('<option value="' + item.id + '">' + item.name + '</option>');
                                });
                                // Select the fetched district - Use 6 digits for district ID
                                let distIdToSelect = fetchedUbigeo.substring(0, 6);
                                selectDist.val(distIdToSelect);
                                selectedDistrictId = distIdToSelect; // Set ID here too

                                // Now all global variables are correctly set, so update the display
                                updateUbigeoDisplay();

                            });
                        }
                    });
                }
            });

            // cargar comprobantes reales de BD
            data.forEach(function (item) {
                var fila = '<tr class="filas">' +
                    '<td><input class="form-control" style="width:150px" name="nombreSucursal[]" value="' + item.comprobantes.nombre + '"></td>' +
                    '<td><input class="form-control" style="width:80px" name="serie[]" value="' + item.comprobantes.serie + '"></td>' +
                    '<td><input class="form-control" style="width:80px" name="numero[]" value="' + item.comprobantes.numero + '"></td>' +
                    '</tr>';
                $("#detalles tbody").append(fila);
            });
        });
}


//Función para desactivar registros
function desactivar(idcategoria) {
    swal({
        title: "¿Desactivar?",
        text: "¿Está seguro Que Desea Desactivar la Categoria?",
        type: "warning",
        showCancelButton: true,
        cancelButtonText: "No",
        cancelButtonColor: '#FF0000',
        confirmButtonText: "Si",
        confirmButtonColor: "#0004FA",
        closeOnConfirm: false,
        closeOnCancel: false,
        showLoaderOnConfirm: true
    }, function (isConfirm) {
        if (isConfirm) {
            $.post("controladores/categoria.php?op=desactivar", { idcategoria: idcategoria }, function (e) {
                swal(
                    '!!! Desactivada !!!', e, 'success')
                tabla.ajax.reload();
            });
        } else {
            swal("! Cancelado ¡", "Se Cancelo la desactivacion de la Categoria", "error");
        }
    });
}

//Función para activar registros
function activar(idcategoria) {
    swal({
        title: "¿Activar?",
        text: "¿Está seguro Que desea Activar la Categoria?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#0004FA',
        confirmButtonText: "Si",
        cancelButtonText: "No",
        cancelButtonColor: '#FF0000',
        closeOnConfirm: false,
        closeOnCancel: false,
        showLoaderOnConfirm: true
    }, function (isConfirm) {
        if (isConfirm) {
            $.post("controladores/categoria.php?op=activar", { idcategoria: idcategoria }, function (e) {
                swal("!!! Activada !!!", e, "success");
                tabla.ajax.reload();
            });
        } else {
            swal("! Cancelado ¡", "Se Cancelo la activacion de la Categoria", "error");
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

function obtenerSerieIncrementada() {
    $.getJSON("controladores/sucursal.php?op=obtenerUltimaSerie", function (data) {

        let ultimaSerie = data.ultima_serie ? parseInt(data.ultima_serie) : 0;
        let nuevaSerie = (ultimaSerie + 1).toString().padStart(3, '0');

        // solo inputs serie del modal activo
        $('#myModal').find("input[name='serie[]']").val(nuevaSerie);
    });
}


init();