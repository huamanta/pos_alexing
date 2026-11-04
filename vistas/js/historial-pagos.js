let listarHistorialPagos = null;

function init() {
    listarHistorialPagos.load();
}

$("#fecha_inicio, #fecha_fin").change(function (e) {
    e.preventDefault();
    listarHistorialPagos.load();
});

function pintarHistorialPagos(data, permissions) {
    let html = "";

    if (data.length === 0) {
        $("#tbodyHistorialPagos").html(`
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `);
        return;
    }

    data.forEach(item => {
        html += `
            <tr>
                <td>${item.created_at_str || ''}</td>
                <td>${item.cliente || ''}</td>
                <td>${item.fechapago_str || ''}</td>
                <td>${item.descripcion || "-"}</td>
                <td><span class="badge badge-primary">${item.formapago || "-"}</span></td>
                <td>${item.montopagado_str || "0"}</td>
                <td>${item.montotarjeta_str || "0"}</td>
                <td>${item.total_pagado_str || "0"}</td>
                <td>${item.op || "-"}</td>
                <td>${item.banco || "-"}</td>
                <td>
                    ${item.comprobante ? `<a class="btn btn-outline-primary" href="files/cuentasporcobrar/${item.comprobante}" target="_blank" title="Ver Comprobante">
                        <i class="fas fa-eye"></i>
                    </a>` : ''}
                </td>
                <td>
                </td>
            </tr>
        `;
    });

    $("#tbodyHistorialPagos").html(html);
}


listarHistorialPagos = new FluentPaginator({
    url: "controladores/historialpagos.php?op=listar",
    renderTabla: pintarHistorialPagos,
    tableBody: "#tbodyHistorialPagos",
    extraParams: () => ({
        fecha_inicio: $("#fecha_inicio").val() || "",
        fecha_fin: $("#fecha_fin").val() || "",
    }),
});

init();