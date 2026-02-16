var tabla;

//Función que se ejecuta al inicio
function init(){
	 $("#body").addClass("sidebar-collapse sidebar-mini");
	listar();
	//Cargamos los items al select cliente
	$.post("controladores/venta.php?op=selectVendedor", function(r){
	            $("#idcliente").html(r);
	            $('#idcliente').select2('');
	});

    $('#navConsultaVentasActive').addClass("treeview active");
    $('#navConsultaVentas').addClass("treeview menu-open");
    $('#navVentasVendedor').addClass("active");

	//cargamos los items al select almacen
	$.post("controladores/venta.php?op=selectSucursal3", function(r){
		$("#idsucursal2").html(r);
		$('#idsucursal2').selectpicker('refresh');
	});

	origin = window.location.origin

	pathName = window.location.pathname
	arrPath = pathName.split("/")
	lastPath = arrPath[arrPath.length - 3]

}


//Función Listar
function listar()
{
	var fecha_inicio = $("#fecha_inicio").val();
	var fecha_fin = $("#fecha_fin").val();
	var idcliente = $("#idcliente").val();
	var idsucursal = $("#idsucursal2").val();

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
	    dom: '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>><"col-sm-12 col-md-4"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
		lengthMenu: [
            [5,10, 25, 50, 100, -1],
            ['5 filas','10 filas', '25 filas', '50 filas','100 filas', 'Mostrar todo']
        ],
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
					url: 'controladores/consultas.php?op=ventasfechavendedor',
					data:{fecha_inicio: fecha_inicio,fecha_fin: fecha_fin, idcliente: idcliente,idsucursal: idsucursal},
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
				"footerCallback": function ( row, data, start, end, display ) {

					total = this.api()
			            .column(5)//numero de columna a sumar
			            //.column(1, {page: 'current'})//para sumar solo la pagina actual
			            .data()
			            .reduce(function (a, b) {
			                return parseFloat(a) + parseFloat(b);
			            }, 0 );

			        $(this.api().column(5).footer()).html("Total Venta: "+" <span style='color:green;'>"+"S/ " + total.toFixed(2) + "</span>");

			        descuento = this.api()
						.column(6)//numero de columna a sumar
						//.column(1, {page: 'current'})//para sumar solo la pagina actual
						.data()
						.reduce(function (a, b) {
							return parseFloat(a) + parseFloat(b);
						}, 0 );
			
					$(this.api().column(6).footer()).html("Total descuento: "+" <span style='color:purple;'>"+"S/ " + descuento.toFixed(2) + "</span>");
        
					comisionV = this.api()
						.column(7)//numero de columna a sumar
						//.column(1, {page: 'current'})//para sumar solo la pagina actual
						.data()
						.reduce(function (a, b) {
							return parseFloat(a) + parseFloat(b);
						}, 0 );
			
					$(this.api().column(7).footer()).html("Total Comision: "+" <span style='color:purple;'>"+"S/ " + comisionV.toFixed(2) + "</span>");
						
					},
		"bDestroy": true,
		"iDisplayLength":10,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}

init();