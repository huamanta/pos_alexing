$("#navCajaChica").addClass("treeview active");
$("#navCajaChica").addClass("active");
async function resumenBancos() {
    try {

        const response = await fetch(
            "controladores/cajachica.php?op=resumenBancos"
        );

        const data = await response.json();
        let r = data.resumen;


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
        const response = await fetch(
            "controladores/cajachica.php?op=resumenComprobantes"
        );
        const data = await response.json();
        console.log(data);
        cargarResumenComprobantes(data);
        cargarTablaComprobantes(data);

    } catch (error) {

    }
}


document.addEventListener("DOMContentLoaded", () => {
    resumenBancos();
    resumenComprobantes();
});