let IMPUESTO = null;
let dataSucursal = null;
function init(){
    listar();
}

function listar(){

    $.post("controladores/negocio.php?op=mostrarNombreNegocio", function(response ,status)
	{
		const data = response;
        let label=document.querySelector('#nombreNegocio');
		label.textContent=data?.nombre || 'Empresa';
		dataSucursal = data;
		IMPUESTO = data?.monto_impuesto ?? 18;
	});

}

init();