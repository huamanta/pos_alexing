var tabla;

//Función que se ejecuta al inicio
function init(){
	listar();
	//Cargamos los items al select cliente
	$.post("controladores/venta.php?op=selectProducto", function(r){
	            $("#idproducto").html(r);
	            $('#idproducto').select2('');
	});

    $.post("controladores/venta.php?op=selectVendedor", function(r){
	            $("#idvendedor").html(r);
	            $('#idvendedor').select2('');
	});

    $('#navConsultaVentasActive').addClass("treeview active");
    $('#navConsultaVentas').addClass("treeview menu-open");
    $('#navVentasDetalle').addClass("active");

	//cargamos los items al select almacen
	$.post("controladores/venta.php?op=selectSucursal3", function(r){
		$("#idsucursal2").html(r);
		$('#idsucursal2').select2('');
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
	var idproducto = $("#idproducto").val();
	var idvendedor = $("#idvendedor").val();
	var idsucursal = $("#idsucursal2").val();
	var tipo_comprobante = $("#tipo_comprobante").val();

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
					url: 'controladores/consultas.php?op=ventadetallecomprobante',
					data:{fecha_inicio: fecha_inicio,fecha_fin: fecha_fin, idproducto: idproducto, idvendedor: idvendedor, idsucursal: idsucursal, tipo_comprobante: tipo_comprobante},
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
			"footerCallback": function ( row, data, start, end, display ) {
        
			total = this.api()
            .column(3)//numero de columna a sumar
            //.column(1, {page: 'current'})//para sumar solo la pagina actual
            .data()
            .reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0 );

        $(this.api().column(3).footer()).html("S/ " + total.toFixed(2));

        cantidad = this.api()
            .column(2)//numero de columna a sumar
            //.column(1, {page: 'current'})//para sumar solo la pagina actual
            .data()
            .reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0 );

        $(this.api().column(2).footer()).html("S/ " + cantidad);

		totalCompra = this.api()
			.column(4)
			.data()
			.reduce(function (a, b){
				return parseFloat(a) + parseFloat(b);
			}, 0 );

        $(this.api().column(4).footer()).html("S/ " + totalCompra.toFixed(2));

		utilidad = this.api()
			.column(5)
			.data()
			.reduce(function (a, b){

				return parseFloat(a) + parseFloat (b);

			}, 0 );

        $(this.api().column(5).footer()).html("S/ " + utilidad.toFixed(2));
            
        },
		"bDestroy": true,
		"iDisplayLength":10,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}

init();