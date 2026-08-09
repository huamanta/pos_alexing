

var selectedDepartmentId = "";
var selectedDepartmentName = "";
var selectedProvinceId = "";
var selectedProvinceName = "";
var selectedDistrictId = "";
var selectedDistrictName = "";

var departamentos = null;

function activarMenu(id) {
    $(".nav-stacked li").removeClass("active");
    $("#" + id).addClass("active");

}


function listarConfiguracion() {
    $.ajax({
        url: "controladores/configuracion.php?op=listarConfiguracion",
        type: "GET",
        success: function (response) {
            //pintar configuraciones
            const data = response?.data || {};
            const configuracion = data?.configuracion || {};
            $("#is_mora_credito").prop("checked", configuracion.is_mora_credito == 1);
            $("#valor_mora_credito").val(configuracion.valor_mora_credito);
            $("#is_notificacion").prop("checked", configuracion.is_notificacion == 1);
            $("#dias_gracia").val(configuracion.dias_gracia);
            $("#interes_defecto").val(configuracion.interes_defecto);
            $("#is_refinanciamiento").prop("checked", configuracion.is_refinanciamiento == 1);
            $("#maximo_refinanciamientos").val(configuracion.maximo_refinanciamientos);
            $("#is_descuento_anticipado").prop("checked", configuracion.is_descuento_anticipado == 1);
            $("#valor_descuento_anticipado").val(configuracion.valor_descuento_anticipado);
            $("#dias_anticipacion").val(configuracion.dias_anticipacion);
            $("#is_send_sunat").prop("checked", configuracion.is_send_sunat == 1);
            $("#is_calculo_mes").prop("checked", configuracion.is_calculo_mes == 1);

            const sucursal = data?.sucursal || {}
            $("#idsucursal").val(sucursal.idsucursal);
            $("#nombre").val(sucursal.nombre);
            $("#direccion").val(sucursal.direccion);
            $("#telefono").val(sucursal.telefono);
            $("#email").val(sucursal.email);
            $("#nombre").val(sucursal.nombre);
            // Mostrar logo
            if (sucursal.logo_url) {
                $("#preview_logo").attr("src", sucursal.logo_url);
            } else {
                $("#preview_logo").attr("src", "files/logos/default.png");
            }

            $("#moneda").val(sucursal.moneda);
            $("#ubigeo").val(sucursal.ubigeo);
            const ubigeo = sucursal?.ubigeo || null;
            let depIdToSelect = ubigeo ? ubigeo.substring(0, 2) : null;
            cargarDepartamentos(depIdToSelect);
            let provIdToSelect = ubigeo ? ubigeo.substring(0, 4) : null;
            cargarProvincias(depIdToSelect, provIdToSelect);
            let distIdToSelect = ubigeo ? ubigeo.substring(0, 6) : null;
            cargarDistritos(provIdToSelect, distIdToSelect);
            if (provIdToSelect, distIdToSelect) {
                $("#provincia_select").prop('disabled', false);
                $("#distrito_select").prop('disabled', false);
            }
        }
    });
}

$("#logo").on("change", function () {

    const archivo = this.files[0];

    if (!archivo) {
        return;
    }

    // Validar tipo
    const permitidos = [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/webp"
    ];

    if (!permitidos.includes(archivo.type)) {
        alert("Seleccione una imagen válida.");
        $(this).val("");
        return;
    }

    // Validar tamaño (2 MB)
    if (archivo.size > 2 * 1024 * 1024) {
        alert("La imagen no debe superar los 2 MB.");
        $(this).val("");
        return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {
        $("#preview_logo").attr("src", e.target.result);
    };

    reader.readAsDataURL(archivo);

});

function cargarDepartamentos(id = null) {
    $.post("controladores/sucursal.php?op=listarDepartamentos", function (data) {
        data = JSON.parse(data);
        var select = $("#departamento_select");
        select.html('<option value="">Seleccione Departamento</option>');
        $.each(data, function (index, item) {
            if (item.id == id) {
                $("#departamento").val(item.name);
            }
            select.append(new Option(item.name, item.id, false, item.id == id));
        });
    });
}

// Load Provinces by Department ID
function cargarProvincias(id_department, id = null) {
    $.post("controladores/sucursal.php?op=listarProvinciasPorDepartamento", { id_department: id_department }, function (data) {
        data = JSON.parse(data);
        var select = $("#provincia_select");
        select.html('<option value="">Seleccione Provincia</option>');
        $.each(data, function (index, item) {
            if (item.id == id) {
                $("#provincia").val(item.name);
            }
            select.append(new Option(item.name, item.id, false, item.id == id));
        });
    });
}

// Load Districts by Province ID
function cargarDistritos(id_province, id = null) {
    $.post("controladores/sucursal.php?op=listarDistritosPorProvincia", { id_province: id_province }, function (data) {
        data = JSON.parse(data);
        var select = $("#distrito_select");
        select.html('<option value="">Seleccione Distrito</option>');
        $.each(data, function (index, item) {
            if (item.id == id) {
                $("#distrito").val(item.name);
            }
            select.append(new Option(item.name, item.id, false, item.id == id));
        });
    });
}


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

$("#formConfiguracionGeneral").submit(function (e) {
    e.preventDefault();
    var formData = new FormData($(this)[0]);

    $.ajax({
        url: "controladores/configuracion.php?op=actualizarConfiguracionGeneral",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $("#btnGuardarGeneral")
                .prop("disabled", true)
                .html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
        },
        success: function (response) {
            if (response.status) {
                Swal.fire("Correcto", response.message, "success");
                listarConfiguracion();
            } else {
                Swal.fire("Alerta", response.message, "error");
            }

            $("#btnGuardarGeneral")
                .prop("disabled", false)
                .html('<i class="fa fa-save"></i> Guardar configuración');
        },
        error: function () {

            $("#btnGuardarGeneral")
                .prop("disabled", false)
                .html('<i class="fa fa-save"></i> Guardar configuración');
            Swal.fire("Alerta", "Ocurrió un error.", "error");

        }
    });
})

$("#formConfiguracionMora").submit(function (e) {

    e.preventDefault();

    var data = new FormData(this);

    $.ajax({
        url: "controladores/configuracion.php?op=actualizarConfiguracionMora",
        type: "POST",
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#btnGuardarMora")
                .prop("disabled", true)
                .html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
        },
        success: function (response) {
            if (response.status) {
                Swal.fire("Correcto", response.message, "success");
                listarConfiguracion();
            } else {
                Swal.fire("Alerta", response.message, "error");
            }

            $("#btnGuardarMora")
                .prop("disabled", false)
                .html('<i class="fa fa-save"></i> Guardar configuración');

        },
        error: function () {

            $("#btnGuardarMora")
                .prop("disabled", false)
                .html('<i class="fa fa-save"></i> Guardar configuración');
            Swal.fire("Alerta", "Ocurrió un error.", "error");

        }

    });

});


$("#formConfiguracionCreditos").submit(function (e) {

    e.preventDefault();

    var data = new FormData(this);

    $.ajax({
        url: "controladores/configuracion.php?op=actualizarConfiguracionCreditos",
        type: "POST",
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#btnGuardarCreditos")
                .prop("disabled", true)
                .html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
        },
        success: function (response) {
            if (response.status) {
                Swal.fire("Correcto", response.message, "success");
                listarConfiguracion();
            } else {
                Swal.fire("Alerta", response.message, "error");
            }

            $("#btnGuardarCreditos")
                .prop("disabled", false)
                .html('<i class="fa fa-save"></i> Guardar configuración');

        },
        error: function () {

            $("#btnGuardarCreditos")
                .prop("disabled", false)
                .html('<i class="fa fa-save"></i> Guardar configuración');
            Swal.fire("Alerta", "Ocurrió un error.", "error");

        }

    });

});


$("#formConfiguracionRefinanciamiento").submit(function (e) {

    e.preventDefault();

    var data = new FormData(this);

    $.ajax({
        url: "controladores/configuracion.php?op=actualizarConfiguracionRefinanciamiento",
        type: "POST",
        data: data,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#btnGuardarRefinanciamiento")
                .prop("disabled", true)
                .html('<i class="fa fa-spinner fa-spin"></i> Guardando...');
        },
        success: function (response) {
            if (response.status) {
                Swal.fire("Correcto", response.message, "success");
                listarConfiguracion();
            } else {
                Swal.fire("Alerta", response.message, "error");
            }

            $("#btnGuardarRefinanciamiento")
                .prop("disabled", false)
                .html('<i class="fa fa-save"></i> Guardar configuración');

        },
        error: function () {

            $("#btnGuardarRefinanciamiento")
                .prop("disabled", false)
                .html('<i class="fa fa-save"></i> Guardar configuración');
            Swal.fire("Alerta", "Ocurrió un error.", "error");

        }

    });

});


listarConfiguracion();