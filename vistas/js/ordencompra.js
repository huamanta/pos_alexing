var tabla;

function init(){

	mostrar_impuesto();

	mostrarform(false);

    listar();

    $("#formulario").on("submit",function(e)
	{
		guardaryeditar(e);	
	});

    //cargamos los items al select almacen
	$.post("controladores/venta.php?op=selectSucursal", function(r){
		$("#idsucursal").html(r);
		// $('#idsucursal').select2('');
	});

	//Cargamos los items al select proveedor
	$.post("controladores/compra.php?op=selectProveedor", function(r){
	            $("#idproveedor").html(r);
	            $('#idproveedor').select2({
					placeholder: 'Seleccionar Proveedor ...',
					allowClear: true
				}).val(null).trigger('change');
	});

	//cargamos los items al select almacen
	$.post("controladores/venta.php?op=selectSucursal3", function(r){
		$("#idsucursal2").html(r);
	});
	
	$('#navComprasActive').addClass("treeview active");
    $('#navCompras').addClass("treeview menu-open");
    $('#navOrdenCompra').addClass("active");

	$("#fecha_inicio").change(listar);
	$("#fecha_fin").change(listar);
	$("#idsucursal2").change(listar);

}

function limpiar()
{
	$("#idproveedor").val("");
	$('#idproveedor').select2({
		placeholder: 'Seleccionar Proveedor ...',
		allowClear: true
	}).val(null).trigger('change');
	$("#proveedor").val("");
	$("#serie_comprobante").val("");
	$("#num_comprobante").val("");
	articuloAdd="";
	no_aplica=16;

	$("#total_compra").val("");
	$(".filas").remove();
	$("#total").html("0");

	$("#most_total").html("0");
	$("#most_imp").html("0");

	//Obtenemos la fecha actual
	var now = new Date();
	var day = ("0" + now.getDate()).slice(-2);
	var month = ("0" + (now.getMonth() + 1)).slice(-2);
	var today = now.getFullYear()+"-"+(month)+"-"+(day) ;
    $('#fecha').val(today);

    //Marcamos el primer tipo_documento
    $("#tipo_comprobante").val("Boleta");
	$("#tipo_comprobante").select2('');

	$("#formapago").select2('');

	$("#lugar_entrega").val("");
	$("#motivo_compra").val("");
}

//Función cancelarform
function cancelarform()
{
	limpiar();
	mostrarform(false);
}

function guardaryeditar(e)
{
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "controladores/compra.php?op=guardaryeditar",
	    type: "POST",
	    data: formData,
	    contentType: false,
	    processData: false,

	    success: function(datos)
	    {                    
	          Swal.fire({
				  title: 'Orden de Compra',
				  icon: 'success',
					text:datos
				});
			  mostrarform(false);
			  listar();
	    }

	});
	limpiar();
}

function mostrar_impuesto(){

	$.ajax({
		url: 'controladores/negocio.php?op=mostrar_impuesto',
		type:'get',
		dataType:'json',
		success: function(i){

			 impuesto=i;

			 $("#impuesto").val(impuesto);

		}

	});

}

//Función mostrar formulario
function mostrarform(flag)
{
	limpiar();
	numTicket();
	numSerieTicket();
	if (flag)
	{
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnagregar").hide();

		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		detalles=0;
		$("#btnAgregarArt").show();
		$("#btnNuevo").hide();
		$("#header").hide();

	}
	else
	{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
		$("#btnNuevo").show();
		$("#header").show();
		$("#btnGuardar").show();
	}
}

function mostrarE(idcompra){
	
	mostrarform(true);

	$.post("controladores/compra.php?op=mostrar2",{idcompra : idcompra}, function(data,status)
		{
			data=JSON.parse(data);

			$("#eliminar").val('Si');
			
			$("#idcompra").val(data.idcompra);
			$("#estadoC").val(data.estadoC);
			$("#idsucursal").val(data.idsucursal);
			$("#idproveedor").val(data.idproveedor);
			$('#idproveedor').select2('');
			$("#fecha").val(data.fecha);	
			$("#tipo_comprobantem").val(data.tipo_comprobante);
			$("#impuesto").val(data.impuesto);
			$("#serie_comprobante").val(data.serie_comprobante);
			$("#num_comprobante").val(data.num_comprobante);

			$("#formapago").val(data.formapago);
			$('#formapago').select2('');
			$("#lugar_entrega").val(data.lugar_entrega);
			$("#motivo_compra").val(data.motivo_compra);
			$("#documento").val(data.documento);

		});
		
		$.post("controladores/compra.php?op=listarDetalleCompra",{idcompra : idcompra}, function(data,status)
		{
			data=JSON.parse(data);

			for(var i=0; i < data.length; i++){
				
				agregarDetalle(data[i][0],data[i][1],data[i][4],data[i][3],data[i][5],data[i][2])


			}

		});

		$("#btnEliminarD").hide();

}

function mostrar(idcompra)
{
	$("#getCodeModal").modal('show');
	$.post("controladores/compra.php?op=mostrar",{idcompra : idcompra}, function(data, status)
	{
		data = JSON.parse(data);		
		//mostrarform(true);

		$("#idproveedorm").val(data.proveedor);
		$("#tipo_comprobantem").val('Orden de Compra');
		$("#serie_comprobantem").val(data.serie_comprobante);
		$("#num_comprobantem").val(data.num_comprobante);
		$("#fecha_horam").val(data.fecha);
		$("#impuestom").val(data.impuesto);
		$("#idingresom").val(data.idingreso);


 	});
	//enviar mediante get listar detalle a la varible op de ajax
 	$.post("controladores/compra.php?op=listarDetalle&id="+idcompra,function(r){
	        $("#detallesm").html(r);
	});
}

//mostramos el num_comprobante del ticket
function numTicket(){
	var idsucursal = $("#idsucursal").val();
	$.ajax({
	url: 'controladores/compra.php?op=mostrar_num_ticket',
	data:{idsucursal: idsucursal},
	type:'get',
	dataType:'json',
	success: function(d){
			 iva=d;
	$("#num_comprobante").val( ('0000000' + iva).slice(-7) ); // "0001"
	$("#nFacturas").html( ('0000000' + iva).slice(-7) ); // "0001"
	}
		});}
	//mostramos la serie_comprobante de la ticket
	function numSerieTicket(){
	var idsucursal = $("#idsucursal").val();
	$.ajax({
	url: 'controladores/compra.php?op=mostrar_s_ticket',
	data:{idsucursal: idsucursal},
	type:'get',
	dataType:'json',
	success: function(s){
		 series=s;
	$("#numeros").html( ('000' + series).slice(-3) ); // "0001"
	$("#serie_comprobante").val( ('000' + series).slice(-3) ); // "0001"
	}

	});
}

function listarArticulos(){

	var idsucursal = $("#idsucursal").val();

	tabla=$('#tblarticulos').dataTable({
	"aProcessing": true,//activamos el procedimiento del datatable
	"aServerSide": true,//paginacion y filrado realizados por el server
	dom: 'Bfrtip',//definimos los elementos del control de la tabla
	buttons: [

	],
	"ajax":
	{
		url:'controladores/compra.php?op=listarArticulos',
		data:{idsucursal: idsucursal},
		type: "get",
		dataType : "json",
		error:function(e){
			console.log(e.responseText);
		}
	},
	"bDestroy":true,
	"iDisplayLength":5,//paginacion
	"order":[[0,"desc"]]//ordenar (columna, orden)
}).DataTable();
}

//Función Listar
function listar()
{

    let fecha_inicio = $("#fecha_inicio").val();
	let fecha_fin = $("#fecha_fin").val();
	let idsucursal2 = $("#idsucursal2").val();

	tabla=$('#tbllistado').dataTable(
	{
		//"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    "processing": true,
	    "language": 
		{          
		"processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
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
					url: 'controladores/compra.php?op=listar2',
                    data:{fecha_inicio: fecha_inicio,fecha_fin: fecha_fin,idsucursal2: idsucursal2},
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 5,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}

var articuloAdd="";
//para contar cuantos detalles le agregamos a la compra
var cont=0;
//cantidad de detalles que tiene la compra
var detalles=0;

function agregarDetalle(idproducto,producto,precioVenta,precioCompra,unidadmedida,cantidad)
{
  	//aquí preguntamos si el idarticulo ya fue agregado
    if(articuloAdd.indexOf(idproducto)!= -1)
    { //reporta -1 cuando no existe
		Swal.fire({
				  title: '',
				  icon: 'info',
					text: producto + ' ya se agregó'
				});
    }
    else
    {
    var precio_compra=1;

    if (idproducto!="")
    {
    	var subtotal=cantidad*precio_compra;
		var fila='<tr class="filas" id="fila'+cont+'">'+
        '<td><input type="hidden" name="idproducto[]" value="'+idproducto+'"><input style="width: 200px;" type="text" name="nombreProducto[]" value="'+producto+'"></td>'+
        '<td><input style="text-align:center" type="hidden">'+unidadmedida+'</td>'+
        '<td><input style="text-align:center; width: 50px;" type="number" step="0.01" onchange="modificarSubtotales()" name="cantidad[]" id="cantidad[]" value="'+cantidad+'"></td>'+
        '<td><input style="text-align:center; width: 80px;" type="number" step="0.01" onchange="modificarSubtotales()" name="precio_compra[]" id="precio_compra[]" value="'+precioCompra+'"></td>'+
        '<td><input style="text-align:center; width: 80px;" type="number" step="0.01" name="precio_venta[]" value="'+precioVenta+'"></td>'+
        '<td><span style="text-align:center; width: 40px;" id="subtotal'+cont+'" name="subtotal">'+subtotal+'</span></td>'+
        '<td><center><button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle('+cont+')"><i class="fa fa-trash"></i></button></center></td>'+
		'</tr>';
    	cont++;
    	detalles=detalles+1;
    	articuloAdd= articuloAdd + idproducto + "-"; //aca concatemanos los idarticulos xvg: 1-2-5-12-20
    	//agregar fila a la tabla
    	$('#detalles').append(fila);
    	modificarSubtotales();
    }
    else
    {
    	Swal.fire("","Error al ingresar el detalle, revisar los datos del producto","info");
    }
	}
}

function evaluar(){

	if (detalles>0) 
	{
		$("#btnGuardar").show();
	}
	else
	{
		$("#btnGuardar").hide();
		cont=0;
		igv=0;
		igv_dec=0;
		$('#most_total').val('0');
		$('#most_imp').val('0');
	}
}

function modificarSubtotales()
{
  	//tres array para almacenar las cantidades, precios de compra y subtotales
  	//leer del documento
  	var cant = document.getElementsByName("cantidad[]");
    var prec = document.getElementsByName("precio_compra[]");
    var sub = document.getElementsByName("subtotal");
    //recorrer los detalles y calcular los subtotales
    //recorrer hasta la cantidad de indices que tiene cant
    for (var i = 0; i <cant.length; i++) {
    	var inpC=cant[i];
    	var inpP=prec[i];
    	var inpS=sub[i];

    	inpS.value=inpC.value * inpP.value;
    	document.getElementsByName("subtotal")[i].innerHTML = inpS.value;
    }
    //Permitir calcular los totales en base a los subtotales
    calcularTotales();
	evaluar();

}

function calcularTotales(){
	var sub = document.getElementsByName("subtotal");
	var total=0.0;
	var total_monto=0.0;
	var igv_dec =0.0;

	//ejecutar el for tanto subtotales tenga
	for (var i = 0; i <sub.length; i++) {
	  total += document.getElementsByName("subtotal")[i].value;
	  var igv=total*(no_aplica)/(no_aplica+100);
	  var total_monto=(total-(igv)).toFixed(2);
	  var igv_dec=igv.toFixed(2);
		  
  }
  //mostrar total
  $("#total").html("S/. " + total.toFixed(2));
  $("#total_compra").val(total.toFixed(2));
  $("#most_total").html(total_monto);
  $("#most_imp").html(igv_dec);
  let label=document.querySelector('#totalVenta');
  label.textContent=total.toFixed(2);
  //permite mostrar los botones de guardar si tenemos almenos un detalle 
  evaluar();
}

//Función para desactivar registros
function aprobar(idcompra)
{

		Swal.fire({
			title: 'Aprobar?',
			text: "¿Está seguro Que Desea Aprobar la Aprobar?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Si'
			}).then((result) => {
			if (result.isConfirmed) {
				$.post("controladores/compra.php?op=aprobar", {idcompra : idcompra}, function(e){
					Swal.fire(
						'Aprobado!',
						e,
						'success'
						)
					tabla.ajax.reload();
				});
			}else{
				Swal.fire(
					'Aviso!',
					"Se Cancelo la aprobación de la Orden de Compra",
					'info'
					)
			}
			})
		
}

//Función para desactivar registros
function anular(idcompra)
{

	Swal.fire({
		title: '¿Anular?',
		text: "¿Está seguro Que Desea anular la Compra?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si'
		}).then((result) => {
		if (result.isConfirmed) {
			$.post("controladores/compra.php?op=anular", {idcompra :idcompra}, function(e){
				Swal.fire(
					'Anulado!',
					e,
					'success'
					)
				tabla.ajax.reload();
			});
		}else{
			Swal.fire(
				'Aviso!',
				"Se Cancelo la anulación de la Compra",
				'error'
				)
		}
		})
		
}

//funcion que espera el id de la fila a eliminar
function eliminarDetalle(indice){
	//id fila mas el indice
	$("#fila" + indice).remove();
	calcularTotales();
	detalles=detalles-1;
	evaluar();
	articuloAdd="";
}

init();