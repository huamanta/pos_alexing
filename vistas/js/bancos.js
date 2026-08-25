let listarBancos = null;

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
					<button class="btn btn-dark btn-xs" onclick="verMovimientos(${item.idbanco})" data-toggle="tooltip" title="Ver movimientos">
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


function verMovimientos(idbanco) {
    $.get('controladores/bancos.php?op=listarMovimientos', { idbanco: idbanco }, function (response) {
        console.log(response);
    })
}


init();