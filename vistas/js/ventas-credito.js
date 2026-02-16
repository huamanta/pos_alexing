var tabla;

//Función que se ejecuta al inicio
function init(){
	 $("#body").addClass("sidebar-collapse sidebar-mini");
	listar();
	listar2();
	//Cargamos los items al select cliente
	$.post("controladores/venta.php?op=selectProductoV", function(r){
	$("#idproducto").html(r);
	$('#idproducto').select2('');
	});

  /*$.post("controladores/venta.php?op=selectVendedor", function(r){
	$("#idvendedor").html(r);
	$('#idvendedor').select2('');
	});*/

	$.post("controladores/venta.php?op=selectCliente2", function(r){
		$("#idvendedor").html(r);
		$('#idvendedor').select2('');
	});

    $('#navConsultaVentasActive').addClass("treeview active");
    $('#navConsultaVentas').addClass("treeview menu-open");
    $('#navVentasCredito').addClass("active");

	//cargamos los items al select almacen
	$.post("controladores/venta.php?op=selectSucursal3", function(r){
		$("#idsucursal2").html(r);
		$('#idsucursal2').select2('');
	});

	//cargamos los items al celect comprobantes
  $.post("controladores/venta.php?op=selectComprobante", function (c) {
    $("#tipo_comprobante").html(c);
    $("#tipo_comprobante").select2("");
  });

	origin = window.location.origin

	pathName = window.location.pathname
	arrPath = pathName.split("/")
	lastPath = arrPath[arrPath.length - 3]

}

$("#fecha_inicio").change(function() {
  listar();
  listar2();
});
$("#fecha_fin").change(function() {
  listar();
  listar2();
});

$("#idproducto").change(function() {
  listar();
});

$("#idvendedor").change(function() {
  listar();
  listar2();
});

$("#idsucursal2").change(function() {
  listar();
  listar2();
});

$(document).ready(function() {
    $('#idsucursal2').change(function() { // Cambia esto al evento que necesites
        var idsucursal = $(this).val(); // Obtener el valor seleccionado

        $.post("controladores/venta.php?op=selectProductoV", { idsucursal2: idsucursal }, function(r) {
            $("#idproducto").html(r);
            $('#idproducto').select2(); // Inicializa select2
        });
    });
});


//Función Listar
function listar()
{
	var fecha_inicio = $("#fecha_inicio").val();
	var fecha_fin = $("#fecha_fin").val();
	var idproducto = $("#idproducto").val() || "Todos";
	var idvendedor = $("#idvendedor").val() || "Todos";
	var idsucursal = $("#idsucursal2").val() || "Todos";

	$.post("controladores/consultas.php?op=totalcantidadpv2", { 
	    fecha_inicio: fecha_inicio, 
	    fecha_fin: fecha_fin, 
	    idvendedor: idvendedor,
	    idproducto: idproducto, 
	    idsucursal: idsucursal 
	}, function(data, status) {

	    data = JSON.parse(data);
	    var totalCompra = data.total_cantidad;

	    // Formatear el número sin el símbolo de moneda
	    var formattedTotal = new Intl.NumberFormat('es-PE', {
	        minimumFractionDigits: 2,
	        maximumFractionDigits: 2
	    }).format(totalCompra);

	    var label = document.querySelector('#lblCantidadPV2');
	    label.textContent = formattedTotal;  
	});

	$.post("controladores/consultas.php?op=totalcomprapv2", { 
	    fecha_inicio: fecha_inicio, 
	    fecha_fin: fecha_fin, 
	    idvendedor: idvendedor,
	    idproducto: idproducto, 
	    idsucursal: idsucursal 
	}, function(data, status) {

	    data = JSON.parse(data);
	    var totalCompra = data.total_precioCompra;

	    // Formatear el número
	    var formattedTotal = new Intl.NumberFormat('es-PE', {
	        style: 'currency',
	        currency: 'PEN',
	        minimumFractionDigits: 2,
	        maximumFractionDigits: 2
	    }).format(totalCompra);

	    var label = document.querySelector('#lblCompraPV2');
	    label.textContent = formattedTotal;  
	});


	$.post("controladores/consultas.php?op=totalventapv2", { 
	    fecha_inicio: fecha_inicio, 
	    fecha_fin: fecha_fin, 
	    idvendedor: idvendedor,
	    idproducto: idproducto, 
	    idsucursal: idsucursal 
	}, function(data, status) {

	    data = JSON.parse(data);
	    var totalCompra = data.total_precio;

	    // Formatear el número
	    var formattedTotal = new Intl.NumberFormat('es-PE', {
	        style: 'currency',
	        currency: 'PEN',
	        minimumFractionDigits: 2,
	        maximumFractionDigits: 2
	    }).format(totalCompra);

	    var label = document.querySelector('#lblVentaPV2');
	    label.textContent = formattedTotal;  
	});

	$.post("controladores/consultas.php?op=totalutilidadpv2", { 
	    fecha_inicio: fecha_inicio, 
	    fecha_fin: fecha_fin, 
	    idvendedor: idvendedor,
	    idproducto: idproducto, 
	    idsucursal: idsucursal 
	}, function(data, status) {

	    data = JSON.parse(data);
	    var totalCompra = data.total_utilidad;

	    // Formatear el número
	    var formattedTotal = new Intl.NumberFormat('es-PE', {
	        style: 'currency',
	        currency: 'PEN',
	        minimumFractionDigits: 2,
	        maximumFractionDigits: 2
	    }).format(totalCompra);

	    var label = document.querySelector('#lblUtilidadPV2');
	    label.textContent = formattedTotal;  
	});

	tabla=$('#tbllistado').dataTable(
	{
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    "processing": true,
	    "language": 
		{          
		"processing": "<img style='width:80px; height:80px;' src='../files/plantilla/loading-page.gif' />",
		},
	    "responsive": true, "lengthChange": false, "autoWidth": false,
        buttons: ['pageLength',
					{
						extend: 'excelHtml5', 
						text: "<i class='fas fa-file-csv'></i>", 
						titleAttr: 'Exportar a Excel', 
						// className: 'btn btn-success'
					},
					{
						extend: 'pdf', 
						text: "<i class='fas fa-file-pdf'></i>", 
						titleAttr: 'Exportar a PDF', 
						// className: 'btn btn-danger'
					},
					{
						extend: 'colvis', 
						text: "<i class='fas fa-bars'></i>", 
						titleAttr: '', 
						// className: 'btn btn-danger'
					}],
		"ajax":
				{
					url: 'controladores/consultas.php?op=ventasfechaproducto2',
					data:{fecha_inicio: fecha_inicio,fecha_fin: fecha_fin, idproducto: idproducto, idvendedor: idvendedor, idsucursal: idsucursal},
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
				"footerCallback": function ( row, data, start, end, display ) {
        },
		"bDestroy": true,
		"iDisplayLength":10,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}

function listar2() {
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    var idproducto = $("#idproducto").val() || "Todos";
    var idvendedor = $("#idvendedor").val() || "Todos";
    var idsucursal = $("#idsucursal2").val() || "Todos";

    // Nota: Se envía el valor del vendedor en "idcliente"
    tabla = $('#tbllistado2').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "processing": true,
        "language": { "processing": "<img style='width:80px; height:80px;' src='../files/plantilla/loading-page.gif' />" },
        "responsive": true, "lengthChange": false, "autoWidth": false,
        
        buttons: [
            'pageLength',
            { extend: 'excelHtml5', text: "<i class='fas fa-file-csv'></i>", titleAttr: 'Exportar a Excel' },
            { extend: 'pdf', text: "<i class='fas fa-file-pdf'></i>", titleAttr: 'Exportar a PDF' },
            { extend: 'colvis', text: "<i class='fas fa-bars'></i>", titleAttr: '' }
        ],
        "ajax": {
            url: 'controladores/consultas.php?op=ventadetallecomprobante2',
            data: {
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                idproducto: idproducto,
                idvendedor: idvendedor,  // Se envía el valor de idvendedor como idcliente
                idsucursal: idsucursal
            },
            type: "get",
            dataType: "json",
            error: function(e) { console.log(e.responseText); }
        },
        "footerCallback": function(row, data, start, end, display) { },
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    }).DataTable();
}


init();