let IMPUESTO = null;
let dataSucursal = null;
let label = document.querySelector('#nombreNegocio');

function init() {
    listar();
}

function listar() {
    $.post("controladores/negocio.php?op=mostrarNombreNegocio", function(response, status) {

        const data = response;

        if (label) {
            label.textContent = data?.nombre || 'Empresa';
        }

        dataSucursal = data;
        IMPUESTO = data?.monto_impuesto ?? 18;
    });
}

init();