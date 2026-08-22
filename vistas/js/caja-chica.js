$("#navCajaChica").addClass("treeview active");
$("#navCajaChica").addClass("active");

function mostrarEstadoCaja(data) {
    const estadoCaja = document.getElementById('estadoCaja');
    const informacionCaja = document.getElementById('informacionCaja');
    const btnCerrarCaja = document.getElementById('btnCerrarCaja');
    const btnDescargar = document.getElementById('btnDescargar');

    if (!estadoCaja || !informacionCaja) {
        return;
    }

    const caja = data.apertura_caja;

    // =====================================================
    // NO HAY CAJA
    // =====================================================
    if (!caja) {
        btnCerrarCaja.disabled = true;
        btnDescargar.disabled = true;
        estadoCaja.style.display = 'block';
        informacionCaja.style.display = 'none';

        estadoCaja.classList.remove('caja-abierta');

        estadoCaja.innerHTML = `
            <div class="caja-status">

                <div class="caja-status-icon">
                    <i class="fa-solid fa-lock"></i>
                </div>

                <div class="caja-status-content">
                    <h3>
                        Caja no abierta
                    </h3>

                    <p>
                        No tienes una caja abierta actualmente.
                        Abre una caja para visualizar los movimientos,
                        ingresos y operaciones.
                    </p>
                </div>

            </div>
        `;

        return;
    }

    // =====================================================
    // CAJA ABIERTA
    // =====================================================
    estadoCaja.style.display = 'block';
    informacionCaja.style.display = 'block';

    estadoCaja.classList.add('caja-abierta');

    estadoCaja.innerHTML = ``;

    // Información
    document.getElementById('cajaNombre').textContent =
        caja.nombre || caja.nombre_caja || `Caja #${caja.idcaja}`;

    document.getElementById('cajaAperturaFecha').textContent =
        caja.fecha_apertura;

    document.getElementById('cajaMontoInicial').textContent =
        caja.efectivo_apertura
            ? `S/ ${parseFloat(caja.efectivo_apertura).toFixed(2)}`
            : 'S/ 0.00';

    document.getElementById('cajaMontoCierre').textContent =
        caja.efectivo_cierre
            ? `S/ ${parseFloat(caja.efectivo_cierre).toFixed(2)}`
            : 'S/ 0.00';
}


async function resumenBancos() {
    try {

        const response = await fetch(
            "controladores/cajachica.php?op=resumenBancos"
        );

        const data = await response.json();
        let r = data.resumen;

        mostrarEstadoCaja(data);
        cargarResumenCaja(data);

        document.querySelector(".caja-total").innerHTML =
            r.total_str;


        document.querySelector(".caja-operaciones").innerHTML =
            `${r.operaciones} operaciones`;


        document.querySelector(".caja-efectivo").innerHTML =
            r.efectivo_str;


        document.querySelector(".caja-transferencias").innerHTML =
            r.transferencias_str;


        document.querySelector(".caja-depositos").innerHTML =
            r.depositos_str;


        document.querySelector(".caja-tarjetas").innerHTML =
            r.tarjetas_str;



        let cantidadCuentas = llenarTabla(
            "tablaCuentasCobrar",
            data.cuentasxcobrar
        );


        let cantidadVentas = llenarTabla(
            "tablaVentas",
            data.ventas
        );



        document.getElementById(
            "cantidadCuentasCobrar"
        ).innerHTML = cantidadCuentas;



        document.getElementById(
            "totalCuentasCobrar"
        ).innerHTML =
            money(data.totales.cuentasxcobrar);



        document.getElementById(
            "cantidadVentas"
        ).innerHTML = cantidadVentas;



        document.getElementById(
            "totalVentas"
        ).innerHTML =
            money(data.totales.ventas);


        // =====================
        // SUMMARY INFERIOR
        // =====================

        document.getElementById(
            "summaryTotal"
        ).innerHTML = r.total_str;


        document.getElementById(
            "summaryOperaciones"
        ).innerHTML = r.operaciones;


        document.getElementById(
            "summaryPromedio"
        ).innerHTML = r.promedio_str;


        document.getElementById(
            "summaryFecha"
        ).innerHTML =
            new Date().toLocaleDateString(
                "es-PE"
            );


    }
    catch (e) {

        console.error(
            "Error cargando caja chica",
            e
        );

    }

}



function llenarTabla(id, data) {


    let html = "";

    let cantidad = 0;


    data.forEach(item => {


        cantidad += Number(item.cantidad);


        html += `

        <tr>

            <td>${item.forma_pago}</td>

            <td>${item.banco || "-"}</td>

            <td>${item.cantidad}</td>

            <td>${item.total_str}</td>

        </tr>

        `;


    });


    document.getElementById(id).innerHTML = html;


    return cantidad;

}

function money(valor) {

    return new Intl.NumberFormat(
        "es-PE",
        {
            style: "currency",
            currency: "PEN"
        }
    ).format(valor);

}


function cargarResumenComprobantes(data) {

    const r = data.resumen;

    $('#cmpCantidad').text(r.comprobantes.cantidad);
    $('#cmpTotal').text(r.comprobantes.total_str);

    $('#cmpContadoCantidad').text(r.contado.cantidad);
    $('#cmpContadoTotal').text(r.contado.total_str);

    $('#cmpCreditoCantidad').text(r.credito.cantidad);
    $('#cmpCreditoTotal').text(r.credito.total_str);

}

function cargarTablaComprobantes(data) {

    let html = '';

    data.comprobantes.forEach(item => {

        let badge = item.ventacredito === 'Si'
            ? '<span class="badge-contado">CRÉDITO</span>'
            : '<span class="badge-contado">CONTADO</span>';

        if (item.ventacredito === 'Si') {
            badge = '<span class="badge-credito">CRÉDITO</span>';
        }

        html += `
                <tr>
                    <td>${item.tipo_comprobante}</td>
                    <td>${badge}</td>
                    <td class="text-center">${item.cantidad}</td>
                    <td class="text-right">${item.total_str}</td>
                </tr>`;

    });

    $('#tablaComprobantes').html(html);

    // Totales de la tabla
    $("#cantidadComprobantes").text(
        data.resumen.comprobantes.cantidad
    );

    $("#totalComprobantes").text(
        data.resumen.comprobantes.total_str
    );


}


// $.getJSON(
//     'controladores/cajachica.php?op=resumenComprobantes',
//     function (data) {

//         cargarResumenComprobantes(data);
//         cargarTablaComprobantes(data);

//     }
// );

async function resumenComprobantes() {
    try {
        const response = await fetch("controladores/cajachica.php?op=resumenComprobantes");
        const data = await response.json();
        cargarResumenComprobantes(data);
        cargarTablaComprobantes(data);
    } catch (error) {
        console.log(error);
    }
}


function cargarResumenCaja(data) {

    const apertura = data.apertura_caja;
    const resumen = data.resumen || {};
    const ingresos = data.ingresos || [];
    const egresos = data.egresos || [];


    // =====================================================
    // RESUMEN DE INGRESOS
    // =====================================================

    let totalIngresos = 0;
    let cantidadIngresos = 0;

    let htmlIngresos = "";

    ingresos.forEach(item => {

        const total = parseFloat(item.total || 0);

        totalIngresos += total;
        cantidadIngresos += parseInt(item.cantidad || 0);

        htmlIngresos += `
            <tr>
                <td>
                    <span class="badge badge-success">
                        ${item.forma_pago || "-"}
                    </span>
                </td>

                <td>
                    ${item.banco || "-"}
                </td>

                <td class="text-center">
                    ${item.cantidad || 0}
                </td>

                <td class="text-right text-success font-weight-bold">
                    ${item.total_str || "S/ 0.00"}
                </td>
            </tr>
        `;
    });

    if (!htmlIngresos) {

        htmlIngresos = `
            <tr>
                <td colspan="4"
                    class="text-center text-muted py-4">

                    <i class="fa-solid fa-inbox fa-2x mb-2"></i>

                    <br>

                    No hay ingresos registrados

                </td>
            </tr>
        `;
    }

    $("#tablaIngresosCaja").html(htmlIngresos);

    $("#cantidadIngresos").text(
        cantidadIngresos
    );

    $("#totalTablaIngresos").text(
        "S/ " + totalIngresos.toFixed(2)
    );

    $("#resumenIngresos").text(
        "S/ " + totalIngresos.toFixed(2)
    );


    // =====================================================
    // RESUMEN DE EGRESOS
    // =====================================================

    let totalEgresos = 0;
    let cantidadEgresos = 0;

    let htmlEgresos = "";

    egresos.forEach(item => {

        const total = parseFloat(item.total || 0);

        totalEgresos += total;
        cantidadEgresos += parseInt(item.cantidad || 0);

        htmlEgresos += `
            <tr>

                <td>
                    <span class="badge badge-danger">
                        ${item.forma_pago || "-"}
                    </span>
                </td>

                <td>
                    ${item.banco || "-"}
                </td>

                <td class="text-center">
                    ${item.cantidad || 0}
                </td>

                <td class="text-right text-danger font-weight-bold">
                    ${item.total_str || "S/ 0.00"}
                </td>

            </tr>
        `;
    });

    if (!htmlEgresos) {

        htmlEgresos = `
            <tr>
                <td colspan="4"
                    class="text-center text-muted py-4">

                    <i class="fa-solid fa-inbox fa-2x mb-2"></i>

                    <br>

                    No hay egresos registrados

                </td>
            </tr>
        `;
    }

    $("#tablaEgresosCaja").html(htmlEgresos);

    $("#cantidadEgresos").text(
        cantidadEgresos
    );

    $("#totalTablaEgresos").text(
        "S/ " + totalEgresos.toFixed(2)
    );

    $("#resumenEgresos").text(
        "S/ " + totalEgresos.toFixed(2)
    );


    // =====================================================
    // OPERACIONES
    // =====================================================

    const operaciones =
        cantidadIngresos +
        cantidadEgresos;

    $("#cantidadOperaciones").text(
        operaciones
    );

}

function obtenerEfectivo(movimientos) {

    if (!Array.isArray(movimientos)) {
        return 0;
    }

    return movimientos.reduce((total, item) => {

        const formaPago = (
            item.forma_pago ||
            item.formapago ||
            ""
        ).toUpperCase().trim();

        if (formaPago === "EFECTIVO") {
            return total + parseFloat(item.total || 0);
        }

        return total;

    }, 0);
}


document.addEventListener("DOMContentLoaded", () => {
    resumenBancos();
    resumenComprobantes();
});