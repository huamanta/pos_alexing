$('#navCobros').addClass("treeview menu-open");
$('#navCobrosActive').addClass("treeview active");
$('#navRefinanciarDeuda').addClass("active");


let idventa = 0;

$("#btnBuscar").click(function () {

    let buscar = $("#buscar").val();

    if (buscar == "") {
        swal.fire("Error", "Ingrese un documento o cliente.", "error");
        return;
    }

    $.ajax({
        url: "controladores/refinanciamiento.php",
        type: "GET",
        data: {
            op: 'buscarCredito',
            buscar: buscar
        },
        dataType: "json",
        beforeSend: function () {
            $("#listaCreditos").hide();
            $("#emptyCreditos").hide();
            $("#preload-carga").html(`
                <div class="text-center p-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 text-muted">Cargando...</p>
                </div>
            `);
        },
        success: function (r) {

            $("#preload-carga").html('');
            if (!r.estado || r.creditos.length == 0) {

                $("#listaCreditos").hide();
                $("#emptyCreditos").show();

                return;
            }

            $("#listaCreditos").show();
            $("#emptyCreditos").hide();

            let html = "";

            $.each(r.creditos, function (i, c) {

                html += `
            <tr>

                <td>${c.cliente}</td>

                <td>${c.documento_venta}</td>

                <td>${c.fecha}</td>

                <td>S/ ${parseFloat(c.total).toFixed(2)}</td>

                <td>S/ ${parseFloat(c.pagado).toFixed(2)}</td>

                <td>S/ ${parseFloat(c.saldo).toFixed(2)}</td>
                <td>${c.refinanciado ? "<span class='badge bg-green'>Sí</span>": "<span class='badge bg-default'>No</span>"}</td>

                <td>

                    <button class="btn btn-primary btn-xs"
                        onclick="seleccionarCredito(${c.idventa})">

                        <i class="fa fa-check"></i> Seleccionar

                    </button>

                </td>

            </tr>
        `;

            });

            $("#tblCreditos").html(html);

        }
    });

});

function seleccionarCredito(idv) {
    idventa = idv;
    $.ajax({

        url: "controladores/refinanciamiento.php",

        data: {
            op: "detalleCredito",
            idventa: idventa
        },

        dataType: "json",

        success: function (r) {

            $("#panelBusqueda").hide();
            $("#panelDetalle").show();

            $("#cliente").val(r.venta.cliente);
            $("#documento").val(r.venta.documento);
            $("#credito").val(r.venta.total);
            $("#pagado").val(r.venta.pagado);
            $("#saldo").val(r.venta.saldo);
            $("#montoDeuda").val(r.venta.saldo);

            let html = "";

            $.each(r.cuotas, function (i, cuota) {

                let estado = cuota.estado_pago == 0
                    ? '<span class="badge badge-success">Pagado</span>'
                    : '<span class="badge badge-warning">Pendiente</span>';

                html += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${cuota.fechavencimiento}</td>
                        <td>S/ ${parseFloat(cuota.deudatotal).toFixed(2)}</td>
                        <td>${estado}</td>
                    </tr>
                `;

            });
            generarCuotas(100);

            $("#tblCuotas").html(html);
        }

    });

}

function generarCuotas(max = 100) {
    let select = $("#input_cuotas");
    let html = '<option value="" selected hidden>Seleccionar...</option>';

    for (let i = 1; i <= max; i++) {
        html += `<option value="${i}">${i}</option>`;
    }

    select.html(html);
}

function calcularCuotasDesdeNumeroMeses() {
    const frecuencia = parseInt($("#input_frecuencia").val(), 10);

    if (isNaN(frecuencia) || frecuencia <= 0) {
        swal.fire("Error", "La frecuencia ingresada no es válida.", "error");
        $("#numeroMeses").val("");
        return;
    }
    const mesesRaw = ("" + $("#numeroMeses").val() || "")
        .replace(",", ".")
        .trim();

    if (!mesesRaw || !frecuencia) {
        $("#input_cuotas").val("");
        return;
    }

    const numeroMeses = parseFloat(mesesRaw);
    if (isNaN(numeroMeses) || numeroMeses <= 0) {
        $("#input_cuotas").val("");
        return;
    }

    const mesesPorCuota = {
        1: 1 / 30,
        2: 1 / 4,
        3: 1 / 2,
        4: 1,
        5: 2,
        6: 3,
        7: 6,
        8: 12,
    }[frecuencia];

    if (!mesesPorCuota) return;

    const cuotasCalculadas = Math.max(1, Math.ceil(numeroMeses / mesesPorCuota));
    const maxCuotasActuales = $("#input_cuotas option").length - 1;

    if (cuotasCalculadas > maxCuotasActuales) {
        generarCuotas(cuotasCalculadas);
    }

    $("#input_cuotas").val(String(cuotasCalculadas));
}

$("#numeroMeses").on("input", calcularCuotasDesdeNumeroMeses);
$("#input_frecuencia").on("change", calcularCuotasDesdeNumeroMeses);


$("#btnGenerar").click(function (e) {
    e.preventDefault();
    calcularCuotas();

});

$("#btnVolver").click(function () {

    $("#panelDetalle").hide();
    $("#panelBusqueda").show();

    // opcional: limpiar selección
    $("#tblCuotas").html("");
    $("#cronograma").html('');
    limpiarSimulacion();
});

function obtenerFechaHoyISO() {
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = ("0" + (hoy.getMonth() + 1)).slice(-2);
    const dd = ("0" + hoy.getDate()).slice(-2);
    return yyyy + "-" + mm + "-" + dd;
}


function validarDatos({ cuotas, frecuencia, deuda }) {
    if (!frecuencia) {
        Swal.fire("Selecciona frecuencia de pago", "", "warning");
        return false;
    }

    if (!cuotas) {
        Swal.fire("Selecciona número de cuotas", "", "warning");
        return false;
    }

    if (!deuda || deuda <= 0) {
        Swal.fire("El crédito no puede ser menor o igual a 0", "", "warning");
        return false;
    }

    return true;
}

function sumarFrecuencia(fecha, frecuencia) {
    let nuevaFecha = new Date(fecha);

    switch (frecuencia) {
        case 1:
            nuevaFecha.setDate(nuevaFecha.getDate() + 1);
            break;
        case 2:
            nuevaFecha.setDate(nuevaFecha.getDate() + 7);
            break;
        case 3:
            nuevaFecha.setDate(nuevaFecha.getDate() + 15);
            break;
        case 4:
            nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);
            break;
        case 5:
            nuevaFecha.setMonth(nuevaFecha.getMonth() + 2);
            break;
        case 6:
            nuevaFecha.setMonth(nuevaFecha.getMonth() + 3);
            break;
        case 7:
            nuevaFecha.setMonth(nuevaFecha.getMonth() + 6);
            break;
        case 8:
            nuevaFecha.setFullYear(nuevaFecha.getFullYear() + 1);
            break;
    }

    return nuevaFecha;
}

function formatearFecha(fecha) {
    return (
        fecha.getFullYear() +
        "-" +
        ("0" + (fecha.getMonth() + 1)).slice(-2) +
        "-" +
        ("0" + fecha.getDate()).slice(-2)
    );
}

function calcularMontos(deuda, interes, cuotas) {
    let interesTotal = deuda * (interes / 100);
    let deudaTotal = deuda + interesTotal;
    let montoCuota = (deudaTotal / cuotas).toFixed(2);

    return {
        interesTotal,
        deudaTotal,
        montoCuota,
    };
}

function calcularCuotas() {

    // Si no se eligio cuotas manualmente, intentar autocalcular desde N° meses + frecuencia.
    if (!$("#input_cuotas").val()) {
        calcularCuotasDesdeNumeroMeses();
    }

    if ($('#tipopago').val() === 'Si') {
        calcularCuotasDesdeNumeroMeses();
    }

    let fechaOperacion = ("" + $("#fechaOperacion").val() || "").trim();
    if (!fechaOperacion) {
        fechaOperacion = obtenerFechaHoyISO();
        $("#fechaOperacion").val(fechaOperacion);
    }

    let data = {
        cuotas: parseInt($("#input_cuotas").val()),
        frecuencia: parseInt($("#input_frecuencia").val()),
        interes: parseFloat($("#inputInteres").val()),
        deuda: parseFloat($("#montoDeuda").val()),
        fechaBase: new Date(fechaOperacion),
    };

    if (!validarDatos(data)) return;

    let { montoCuota } = calcularMontos(data.deuda, data.interes, data.cuotas);

    let html = generarTabla(
        data.cuotas,
        data.frecuencia,
        data.fechaBase,
        data.deuda,
        data.interes,
    );

    $("#cronograma").html(html);
};

function generarTabla(cuotas, frecuencia, fechaBase, deuda, interes) {
    let html = "";
    let fechaTemp = new Date(fechaBase);

    const interesTotal = deuda * (interes / 100);
    const montoBase = deuda / cuotas;
    const interesPorCuota = interesTotal / cuotas;
    const totalCuota = montoBase + interesPorCuota;

    for (let i = 1; i <= cuotas; i++) {
        fechaTemp = sumarFrecuencia(fechaTemp, frecuencia);
        let fecha = formatearFecha(fechaTemp);

        html += `
      <tr>
        <td><input type="date" class="form-control" name="fecha_pago[]" value="${fecha}"></td>
        <td>S/. ${montoBase.toFixed(2)}</td>
        <td>S/. ${interesPorCuota.toFixed(2)}</td>
        <td>S/. ${totalCuota.toFixed(2)}</td>
      </tr>`;
    }

    return html;
}


$("#inicial").on("keyup", function () {

    let inicial = parseFloat($(this).val());
    let deudaBase = parseFloat($("#saldo").val());

    // validaciones
    if (isNaN(inicial) || inicial < 0) {
        inicial = 0;
    }

    if (inicial > deudaBase) {
        inicial = deudaBase;
        $(this).val(deudaBase);
    }

    let nuevaDeuda = deudaBase - inicial;

    if (nuevaDeuda < 0) {
        nuevaDeuda = 0;
    }

    $("#montoDeuda").val(nuevaDeuda.toFixed(2));

});


$("#btnGuardar").click(function () {

    if (idventa == 0 || !idventa) {
        alert("Seleccione un crédito.");
        return;
    }

    const data = {
        idventa: idventa,
        interes: $("#inputInteres").val(),
        inicial: $("#inicial").val(),
        frecuencia: $("#input_frecuencia").val(),
        cuotas: $("#input_cuotas").val(),
        fecha: $("#fechaOperacion").val()
    };

    $.ajax({
        url: "controladores/refinanciamiento.php?op=guardarRefinanciamiento",
        type: "POST",
        data: data,
        dataType: "json",

        beforeSend: function () {

            $("#btnGuardar")
                .prop("disabled", true)
                .html('<i class="fa fa-spinner fa-spin"></i> Guardando...');

        },

        success: function (r) {

            if (r.estado) {

                alert("Refinanciamiento registrado correctamente.");
                $("#btnBuscar").click();
                // Opcional: volver al buscador
                $("#btnVolver").click();
                $("#cronograma").html('');
                limpiarSimulacion();
            } else {

                alert(r.mensaje);

            }

        },

        error: function () {

            alert("Ocurrió un error al guardar.");

        },

        complete: function () {

            $("#btnGuardar")
                .prop("disabled", false)
                .html('<i class="fa fa-save"></i> Guardar Refinanciamiento');

        }

    });

});


function limpiarSimulacion() {
    $("#inputInteres").val(0);
    $("#inicial").val(0);
    $("#input_frecuencia").val('');
    $("#input_cuotas").val('');
    $("#fechaOperacion").val('');
}