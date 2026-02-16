var tabla;

//Función que se ejecuta al inicio
function init(){
	$("#body").addClass("sidebar-collapse sidebar-mini");
    listar();
	$("#fecha_inicio").change(listar);
	$("#fecha_fin").change(listar);
	$("#idsucursal2").change(listar);
	$("#idproveedor").change(listar);

	$.post("controladores/venta.php?op=selectProveedor", function(r){
	$("#idproveedor").html(r);
	$('#idproveedor').select2();
	});

    $('#navConsultaComprasActive').addClass("treeview active");
    $('#navConsultaCompras').addClass("treeview menu-open");
    $('#navConsultaComprasII').addClass("active");

    //cargamos los items al select almacen
	$.post("controladores/venta.php?op=selectSucursal3", function(r){
		$("#idsucursal2").html(r);
		$('#idsucursal2').select2('');
	});

}

//Función Listar
function listar() {
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    var idsucursal = $("#idsucursal2").val();
    var idproveedor = $("#idproveedor").val() || "Todos";  // Asignar "Todos" si no tiene valor
    var idproducto = $("#idproducto").val() || "Todos";  // Asignar "Todos" si no tiene valor

   $.post("controladores/consultas.php?op=totalcompracantidad", { 
		    fecha_inicio: fecha_inicio, 
		    fecha_fin: fecha_fin, 
		    idproveedor: idproveedor, 
		    idsucursal: idsucursal 
		}, function(data, status) {

		    data = JSON.parse(data);
		    var totalCompra = data.total_compra;

		    // Formatear el número sin el símbolo de moneda
		    var formattedTotal = new Intl.NumberFormat('es-PE', {
		        minimumFractionDigits: 2,
		        maximumFractionDigits: 2
		    }).format(totalCompra);

		    // Determinar si se usa "unidad" o "unidades"
		    var unidadTexto = (totalCompra === 1) ? 'unidad' : 'unidades';

		    // Crear el nuevo texto para mostrar
		    var textoFinal = `${formattedTotal} ${unidadTexto}`;

		    // Actualizar el contenido del label
		    var label = document.querySelector('#lblComprasCantidad');
		    label.textContent = textoFinal;  
		});


    $.post("controladores/consultas.php?op=totalcompraproveedor", { 
	    fecha_inicio: fecha_inicio, 
	    fecha_fin: fecha_fin, 
	    idproveedor: idproveedor, 
	    idsucursal: idsucursal 
	}, function(data, status) {

	    data = JSON.parse(data);
	    var totalCompra = data.total_compra;

	    // Formatear el número
	    var formattedTotal = new Intl.NumberFormat('es-PE', {
	        style: 'currency',
	        currency: 'PEN',
	        minimumFractionDigits: 2,
	        maximumFractionDigits: 2
	    }).format(totalCompra);

	    var label = document.querySelector('#lblComprasProveedor');
	    label.textContent = formattedTotal;  
	});

    tabla = $('#tbllistado').dataTable({
        "aProcessing": true, // Activamos el procesamiento del datatables
        "aServerSide": true, // Paginación y filtrado realizados por el servidor
        "processing": true,
        "language": {
            "processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
        },
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        dom: '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>><"col-sm-12 col-md-4"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [
            [5, 10, 25, 50, 100, -1],
            ['5 filas', '10 filas', '25 filas', '50 filas', '100 filas', 'Mostrar todo']
        ],
        buttons: ['pageLength',
            {
                extend: 'excelHtml5',
                text: "<i class='fas fa-file-csv'></i>",
                titleAttr: 'Exportar a Excel',
            },
            {
                extend: 'pdf',
                text: "<i class='fas fa-file-pdf'></i>",
                titleAttr: 'Exportar a PDF',
            },
            {
                extend: 'colvis',
                text: "<i class='fas fa-bars'></i>",
                titleAttr: '',
            }],
        "ajax": {
            url: 'controladores/consultas.php?op=ventasfechaproductoproveedor',
            data: {
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                idsucursal: idsucursal,
                idproveedor: idproveedor,
                idproducto: idproducto
            },
            type: "get",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 10, // Paginación
        "order": [[0, "desc"]] // Ordenar (columna, orden)
    }).DataTable();
}


init();