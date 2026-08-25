let listarBancos = null;
let listarBancoMovimientos = null;
let idBanco = null;
$("#panelMovimientoBancos").hide();
function init() {
    listarBancos.load();
}

function pintarBancos(data, permissions) {
    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbllistado tbody").html(html);
        return;
    }

    data.forEach(item => {
        html += `
                <tr>
                    <td>${item.nombre}</td>
                    <td>${item.descripcion || '-'}</td>
                    <td>${item.cuenta || '-'}</td>
                    <td>${item.cci || '-'}</td>
                    <td>${item.saldo || '0'}</td>
                    <td>
                        <button
                            class="btn btn-dark btn-xs"
                            onclick='verMovimientos(${JSON.stringify(item)})'
                            data-toggle="tooltip"
                            title="Ver movimientos"
                        >
                            <i class="fa fa-list"></i>
                        </button>
                    </td>
                </tr>
            `;
    });

    $("#tbllistado tbody").html(html);
}

listarBancos = new FluentPaginator({
    url: "controladores/bancos.php?op=listar",
    renderTabla: pintarBancos,
    tableBody: "#tbodyBancos"
});


function verMovimientos(item) {
    $("#panelBancos").hide();
    $('#detalleBancoNombre').text(item.nombre || '-');
    $('#detalleBancoDescripcion').text(item.descripcion || '-');
    $('#detalleBancoCuenta').text(item.cuenta || '-');
    $('#detalleBancoCci').text(item.cci || '-');

    $('#detalleBancoSaldo').text(
        'S/ ' + parseFloat(item.saldo || 0).toFixed(2)
    );
    idBanco = item.idbanco
    listarBancoMovimientos.load();
    $("#panelMovimientoBancos").show();
}


function pintarBancoMovimientos(data, permissions) {
    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbllistado tbody").html(html);
        return;
    }

    data.forEach(item => {


        html += `
            <tr>
                <td>${item.fecha}</td>
                <td>${item.responsable || '-'}</td>
                <td class="${item.tipo === 'Ingresos' ? 'text-success' : ''}">
                    ${item.tipo === 'Ingresos' ? item.monto : '-'}
                </td>
                <td class="${item.tipo === 'Egresos' ? 'text-danger' : ''}">
                    ${item.tipo === 'Egresos' ? item.monto : '-'}
                </td>
            </tr>
        `;

    });

    $("#tbllistadoMovimientos tbody").html(html);
}

listarBancoMovimientos = new FluentPaginator({
    url: "controladores/bancos.php?op=listarMovimientos",
    renderTabla: pintarBancoMovimientos,
    tableBody: "#tbodyBancoMovimientos",
    extraParams: () => ({
        idbanco: idBanco
    })
});

function regresarBancos() {
    $('#panelMovimientoBancos').hide();
    $('#panelBancos').show();
}


init();