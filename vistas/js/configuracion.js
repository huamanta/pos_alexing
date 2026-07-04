function activarMenu(id) {
    $(".nav-stacked li").removeClass("active");
    $("#" + id).addClass("active");

}

listarConfiguracion();

function listarConfiguracion() {
    $.ajax({
        url: "controladores/configuracion.php?op=listarConfiguracion",
        type: "GET",
        success: function (res) {
            const response = JSON.parse(res);
            const configuracion = response.data.configuracion;
            $("#is_mora_credito").prop("checked", configuracion.is_mora_credito == 1);
            $("#valor_mora_credito").val(configuracion.valor_mora_credito);
            $("#is_notificacion").prop("checked", configuracion.is_notificacion == 1);
            $("#dias_gracia").val(configuracion.dias_gracia);
            $("#interes_defecto").val(configuracion.interes_defecto);
            $("#maximo_refinanciamientos").val(configuracion.maximo_refinanciamientos);
            $("#is_descuento_anticipado").prop("checked", configuracion.is_descuento_anticipado == 1);
            $("#valor_descuento_anticipado").val(configuracion.valor_descuento_anticipado);
            $("#dias_anticipacion").val(configuracion.dias_anticipacion);
        }
    });
}


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
        success: function (res) {

            const data = JSON.parse(res);

            if (data.status) {
                Swal.fire("Correcto", data.message, "success");
                listarConfiguracion();
            } else {
                Swal.fire("Alerta", data.message, "error");
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
        success: function (res) {

            const data = JSON.parse(res);

            if (data.status) {
                Swal.fire("Correcto", data.message, "success");
                listarConfiguracion();
            } else {
                Swal.fire("Alerta", data.message, "error");
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