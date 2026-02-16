var tabla;

//Función que se ejecuta al inicio
function init(){

	origin = window.location.origin

	pathName = window.location.pathname
	arrPath = pathName.split("/")
	lastPath = arrPath[arrPath.length - 2]

}

function generarReporte(){
	var fecha_inicio = $("#fecha_inicio").val();
	var fecha_fin = $("#fecha_fin").val();
	var idcliente = $("#idcliente").val();
	var idsucursal = $("#idsucursal2").val();
	window.open(origin+'/'+lastPath+'/'+'reportes/rptventasxcliente.php?fechai='+fecha_inicio + '&fechaf='+fecha_fin + '&idcliente='+idcliente + '&idsucursal='+idsucursal);
}


init();