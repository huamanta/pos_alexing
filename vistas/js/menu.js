let IMPUESTO = null;
let dataSucursal = null;
let label = document.querySelector('#nombreNegocio');
const CLIENTE_GENERAL = 1;
const BOLETA = 1;
const FACTURA = 2;

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