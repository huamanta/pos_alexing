var tabla;
var contador = 0;
var articuloAdd="";
var cont=0;
var detalles=0;

function init(){
	
	mostrar_impuesto();
	mostrarform(false);
    listar();

    $("#formulario").on("submit",function(e){
        guardaryeditar(e);
    });

    $.post("controladores/usuario.php?op=selectEmpleado", function(r){
		$("#idpersonal").html(r);
		$('#idpersonal').select2('');
	});

    //cargamos los items al celect comprobantes
    $.post("controladores/venta.php?op=selectComprobante2", function(c){ 
        $("#tipo_comprobante").html(c);
        $("#tipo_comprobante").select2('');
    });

    //cargamos los items al select cliente
    $.post("controladores/venta.php?op=selectCliente", function(r){
        $("#idcliente").html(r);
        $('#idcliente').select2('');
    });

	$.post("controladores/venta.php?op=selectMotivos", function(c){ 
		$("#idmotivo").html(c);
		$("#idmotivo").select2('');
	});

	$("#fecha_inicio").change(listar);
    $("#fecha_fin").change(listar);
    $("#idsucursal2").change(listar);
	
	$('#navPosActive').addClass("treeview active");
    $('#navPos').addClass("treeview menu-open");
    $('#navNotasCredito').addClass("active");
    cargarSucursales();
    
    $("#tipo_comprobante").on("change", function () {
    let tipo = $(this).val();

    if (tipo === "NC") { // Nota de crédito de factura
        mostrar_serie_nc();
        numFactura();
    } else if (tipo === "NCB") { // Nota de crédito de boleta
        mostrar_serie_ncb();
        numBoleta();
    } else {
        $("#serie_comprobante").val("");
        $("#num_comprobante").val("");
    }
});


}

function cargarSucursales() {
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal").html(r);
    $("#idsucursal").select2();

    $("#idsucursal2").html(r);
    $("#idsucursal2").select2();

    // Esperar un pequeño delay para que select2 cargue correctamente el valor por defecto
    setTimeout(() => {
      const idsucursal2 = $("#idsucursal2").val();
      if (idsucursal2) {
        CargarDocumentosReferencia();
        listar(); // Si quieres que también liste por defecto
      }
    }, 200);
  });
}


function cargarItemsAlSelect() {
  // Cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal").html(r);
    $("#idsucursal").select2();
  });
}

function CargarDocumentosReferencia() {
    let idsucursal2 = $("#idsucursal2").val();

    $.post("controladores/venta.php?op=selectDocumentos", { idsucursal2: idsucursal2 }, function(c) {
        $("#comprobanteReferencia").html(c);
        $("#comprobanteReferencia").select2({
            placeholder: 'Seleccionar comprobante ...',
            allowClear: true
        }).val(null).trigger('change');
    });
}

$("#idsucursal2").on('change', function() {
    // Obtener la sucursal seleccionada
    let idsucursal2 = $(this).val();

    // Sincronizar con el select principal
    $("#idsucursal").val(idsucursal2).trigger('change');

    // Cargar documentos de referencia (como ya haces)
    CargarDocumentosReferencia();

    // Asegurarse de que las series y números se generen desde el backend
    let tipo = $("#tipo_comprobante").val();

    if (tipo === "NC") { // Nota de crédito de factura
        mostrar_serie_nc(); // ← obtiene serie desde servidor
        numFactura();       // ← obtiene número desde servidor
    } else if (tipo === "NCB") { // Nota de crédito de boleta
        mostrar_serie_ncb(); // ← obtiene serie desde servidor
        numBoleta();         // ← obtiene número desde servidor
    }

    // (Opcional) Si quieres refrescar la lista de ventas al cambiar sucursal
    listar();
});



function EnviarSunat(tipoc,idventa, idcol){

	$url='public/FACT_WebService/Facturacion/NotaCredito.php?idnc=';

	$.ajax({

		url: $url+idventa+'&codColab='+idcol,

		type: 'get',
			dataType: 'text',
			beforeSend: function(){

				$ ( ".modal" ) .show ();

    	},
    	success: function(resp){

	    	listar();

	    	Swal.fire({
				  title: 'SUNAT',
				  icon: 'success',
					text:resp
				});

		},
            complete: function () {
                $ ( ".modal" ) .hide ();
        }

	});


}

//funcion para guardaryeditar
function guardaryeditar(e) {
	e.preventDefault();//no se activara la accion predeterminada 
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "controladores/venta.php?op=notacredito",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function(datos) {
			console.log(datos);
			Swal.fire({
				title: 'Nota de Crédito',
				icon: 'success',
				  text:datos
			  });
			mostrarform(false);
			listar();
		}
 	});

	limpiar();
}

//__________________________________________________________________________
//mostramos el num_comprobante de la boleta
function numFactura(){

	var idsucursal = $("#idsucursal").val();

$.ajax({
	url: 'controladores/venta.php?op=mostrar_num_nc',
	data:{idsucursal: idsucursal},
	type:'get',
	dataType:'json',
	success: function(d){
			 iva=d;
	$("#num_comprobante").val( ('0000000' + iva).slice(-7) ); // "0001"
	$("#nFacturas").html( ('0000000' + iva).slice(-7) ); // "0001"
}
});}
//mostramos la serie_comprobante de la boleta
function mostrar_serie_nc(){
	var idsucursal = $("#idsucursal").val();
$.ajax({
	url: 'controladores/venta.php?op=mostrar_serie_nc',
	data:{idsucursal: idsucursal},
	type:'get',
	dataType:'json',
	success: function(s){
		 series=s;
	$("#numeros").html( ('000' + series).slice(-3) ); // "0001"
	$("#serie_comprobante").val('FN' + ('0' + series).slice(-3) ); // "0001"
}
});}

//mostramos el num_comprobante de la boleta
function numBoleta(){
	var idsucursal = $("#idsucursal").val();
$.ajax({
	url: 'controladores/venta.php?op=mostrar_num_ncb',
	data:{idsucursal: idsucursal},
	type:'get',
	dataType:'json',
	success: function(d){
			 iva=d;
	$("#num_comprobante").val( ('0000000' + iva).slice(-7) ); // "0001"
	$("#nFacturas").html( ('0000000' + iva).slice(-7) ); // "0001"
}
});}
//mostramos la serie_comprobante de la boleta
function mostrar_serie_ncb(){
	var idsucursal = $("#idsucursal").val();
$.ajax({
	url: 'controladores/venta.php?op=mostrar_serie_ncb',
	data:{idsucursal: idsucursal},
	type:'get',
	dataType:'json',
	success: function(s){
		 series=s;
	$("#numeros").html( ('000' + series).slice(-3) ); // "0001"
	$("#serie_comprobante").val('BN' + ('0' + series).slice(-3) ); // "0001"
}
});}
//_______________________________________________________________________________________________


let idventa;
let cargandoVenta = false;
function mostrarE(){

	$("#btnGuardar").show();

	idventa=$("#comprobanteReferencia").val();
	if (!idventa) {
        console.warn("No hay comprobante seleccionado");
        return;
    }
	$.post("controladores/venta.php?op=mostrar", { idventa: idventa }, function (data) {

    if (!data) {
        console.warn("No se recibió data de la venta");
        return;
    }

    data = JSON.parse(data);

    if (!data || !data.idpersonal) {
        console.warn("Venta sin personal asignado:", data);
        return;
    }

    $('#idpersonal').val(data.idpersonal).trigger('change');
    $('#idcliente').val(data.idcliente).trigger('change');

    let num = data.serie_comprobante?.substr(0,1) || '';

    if (num === "P") {
        $('#serie_comprobante').val("-");
        $('#num_comprobante').val("-");
    } 
    else if (num === "B") {
        numBoleta();
        mostrar_serie_ncb();
        $("#tipo_comprobante").val('NCB').trigger('change');
    } 
    else {
        numFactura();
        mostrar_serie_nc();
        $("#tipo_comprobante").val('NC').trigger('change');
    }

});

		
	$.post("controladores/venta.php?op=listarDetalleVenta",{idventa : idventa}, function(data,status)
{
    data = JSON.parse(data);
    console.log("DETALLE CARGADO:", data);

    if (contador != 0){
        limpiarDetalle();
    }

    cargandoVenta = true;

    for (var i = 0; i < data.length; i++) {
        agregarDetalle(
            data[i][0],  // iddetalle_venta
            data[i][1],  // idproducto (configuración)
            data[i][2],  // producto
            data[i][3],  // cantidad
            data[i][4],  // descuento
            data[i][5],  // precio_venta
            data[i][9],  // preciocigv
            data[i][6],  // precioB
            data[i][7],  // precioC
            data[i][8],  // precioD
            data[i][10], // stock
            data[i][11], // proigv
            data[i][13], // cantidad_contenedor
            data[i][14], // contenedor
            data[i][16]  // idcategoria
        );
    }
    cargandoVenta = false;
});


}
function agregarDetalle(idpc,idproducto,producto,cant,desc,precio_venta,preciocigv,precioB,precioC
  ,precioD,stock,proigv,cantidad_contenedor,contenedor,idcategoria) {
	
  if ($("#tipo_comprobante").val() != "Nota de Venta") {
    precio_venta = precio_venta;

    if (precioB != "") {
      precioB = (precioB * 1.18).toFixed(2);
    }
    if (precioC != "") {
      precioC = (precioC * 1.18).toFixed(2);
    }
    if (precioD != "") {
      precioD = (precioD * 1.18).toFixed(2);
    }
  } else {
    precio_venta = precio_venta;
  }

  //aquí preguntamos si el idarticulo ya fue agregado
  if (!cargandoVenta && articuloAdd.indexOf(idpc) != -1) {
    //reporta -1 cuando no existe
    // swal( producto +" ya se agrego");

    let cant = document.getElementsByName("cantidad[]");

    let id = document.getElementsByName("idproducto[]");

    for (var i = 0; i < cant.length; i++) {
      if (id[i].value == idproducto) {
        let total = Number(cant[i].value) + 1;
        let stockverify = Number(cant[i].value) + Number(cantidad_contenedor);
        if (idcategoria != 1) {
          if (stock < stockverify) {
            Swal.fire("Alerta", "No hay suficiente stock!", "error");
            return false;
          }
        }
        document.getElementsByName("cantidad[]")[i].value = total;

        modificarSubtotales();
      }
    }
  } else {
    var cantidad = cant;
    var stockverify = cant * cantidad_contenedor;

    /*if (idcategoria != 1) {
      if (stock < stockverify) {
        Swal.fire("Alerta", "No hay suficiente stock!", "error");
        return false;
      }
    }*/

    if (idcategoria == 1) {
      stock = "Servicio";
    } else {
      stock = stock;
    }
    var detail = "";
    if (contenedor != undefined) {
      detail = contenedor + " x " + cantidad_contenedor + " Und.";
    }

    var descuento = desc;

    var cad = "";
    var select = "";

    if (precioB != "0.00" || precioC != "0.00" || precioD != "0.00") {
      cad =
        '<option value="' + precio_venta + '">' + precio_venta + "</option>";

      if (precioB != "0.00") {
        cad = cad + '<option value="' + precioB + '">' + precioB + "</option>";
      }

      if (precioC != "0.00") {
        cad = cad + '<option value="' + precioC + '">' + precioC + "</option>";
      }

      if (precioD != "0.00") {
        cad = cad + '<option value="' + precioD + '">' + precioD + "</option>";
      }

      select =
        //'<select style="width:100px;height:35px;" oninput="modificarSubtotales()" name="precio_venta[]" id="precio_venta[]" class="form-control" required>' +
        ///cad +
        //"</select>";
      '<input style="text-align:center; width: 100px;" type="number" step="0.01" oninput="modificarSubtotales()" name="precio_venta[]" id="precio_venta[]" value="' +
        precio_venta +
        '">';
    } else {
      select =
        '<input style="text-align:center; width: 100px;" type="number" step="0.01" oninput="modificarSubtotales()" name="precio_venta[]" id="precio_venta[]" value="' +
        precio_venta +
        '">';
    }

    if (idpc !== "") {
      contador = contador + 1;
      var fila =
        '<tr class="filas custom-row" id="fila' + cont +'" style="margin-bottom: -10px !important; border-radius: 10px; box-shadow: 0 0 5px rgba(0,0,0,0.3);">' +
        '<td><input type="hidden" name="contenedor[]" value="' + contenedor + '"><input type="text" name="cantidad_contenedor[]" value="' + cantidad_contenedor +'" style="display: none;"><input type="hidden" name="idp[]" value="' + idpc +
        '"><input type="hidden" name="idproducto[]" value="' +idproducto + '">' +producto +' - <span class="badge bg-green">' +
        detail + "</span> - S/. " + select +'<input style="width: 240px;" type="text" name="nombreProducto[]" value="' + producto + '" hidden></td>' +
        '<td><input style="text-align:center; width: 100px;border:none;backgroundColor:none;" type="number" oninput="modificarSubtotales()" name="cantidad[]" id="cantidad[]" value="' +
        cantidad +
        '"></td>' +
        '<td style="text-align: center; display:none"><input  style="text-align:center; width: 50px;border:none;backgroundColor:none;" type="number" step="0.01" onchange="modificarSubtotales(' +
        cont +
        ')" name="descuento[]" value="' +
        descuento +
        '" hidden></td>' +
        '<td style="text-align: center; display:none"><input style="text-align:center; width: 50px; font-size:10px" type="text" readonly="readonly" name="stock[]" value="' +
        stock +
        '" hidden><span class="btn btn-warning" style="font-size:12px;font-weight:bold">' +
        stock +
        "</span></td>" +
        '<td style="width:100px">S/. <span id="subtotal' +
        cont +
        '" name="subtotal" style="text-align:center;font-size:14px;font-weight:bold"></span></td>' +
        '<td style="display: none;"><input style="text-align:center; width: 50px;" type="number" step="0.01" onchange="modificarSubtotales(' +
        cont +
        ')" name="descuento[]" value="' +
        descuento +
        '"></td>' +
        '<td style="text-align: center;"><button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(' +
        cont +
        ')"><i class="fa fa-trash"></i></button></td>' +
        '<td style="display: none;"	><span style="text-align:center" id="proigv' +
        cont +
        '" name="proigv" hidden>' +
        proigv +
        "</span></td>" +
        "</tr>";
      cont++;
      detalles = detalles + 1;
      articuloAdd += idproducto + "-";
      $("#detalles").append(fila);
      modificarSubtotales();
    } else {
      alert("Error al ingresar el detalle, revisar los datos del artículo");
    }
  }
}

//funcion limpiar
function limpiar(){

	$("#idventa").val("");
	$("#idcliente").val("");
	$("#cliente").val("");
	$("#serie_comprobante").val("");
	$("#num_comprobante").val("");
	// $("#impuesto").val("");
	articuloAdd="";
	no_aplica=16;

	$("#total_venta").val("");
	$(".filas").remove();
	$("#total").html("0");

	$("#most_total").html("0");
	$("#most_imp").html("0");

	//obtenemos la fecha actual
	var now = new Date();
	var day =("0"+now.getDate()).slice(-2);
	var month=("0"+(now.getMonth()+1)).slice(-2);
	var today=now.getFullYear()+"-"+(month)+"-"+(day);
	$("#fecha").val(today);

	$("#idcliente").val('6');

	$("#idcliente").select2('');

	$("#porcentaje").val("");

	$("#observaciones").val("");

	$("#totalrecibido").val(0);
	$("#vuelto").val("");

	$('#n1').hide();
	$('#n2').hide();
	$('#n3').hide();
	$("#f1").hide();
	$('#n5').hide();
	$('#n6').hide();
	$('#fechadeposito').hide();
	$('#banco').hide();
	$('#fechadeposito').hide();
	$('#banco').hide();

	$('#formapago').val('Efectivo');

	$("#porcentaje").val("");
	$("#nroOperacion").val("");
	$("#totalrecibido").val("");
	$("#vuelto").val("");
	$("#observaciones").val("");

	$("#tipopago").val("No");
	$("#montoPagado").val("");

	$("#fechaDepostivo").val("");
	
}

function limpiarDetalle(){

	detalles = 0;

	evaluar();

	if(contador!=0){

		for(var i=0; i<=contador;i++){

			$("#fila"+i).remove();
			calcularTotales();
			evaluar();
			articuloAdd="";

		}
		
	}

}


//Función Listar
function listar()
{

    let fecha_inicio = $("#fecha_inicio").val();
	let fecha_fin = $("#fecha_fin").val();
    var estado = $("#estado").val();
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
					url: 'controladores/venta.php?op=listarNC',
                    data:{fecha_inicio: fecha_inicio,fecha_fin: fecha_fin,estado: estado,idsucursal2: idsucursal2},
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

//cancelar form
function cancelarform(){
	limpiar();
	mostrarform(false);
}

//Función mostrar formulario
function mostrarform(flag)
{
	limpiar();
	numFactura();
	mostrar_serie_nc();
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


function modificarSubtotales(e)
{
	var cant = document.getElementsByName("cantidad[]");
    var prec = document.getElementsByName("precio_venta[]");
    var desc = document.getElementsByName("descuento[]");
    var sub = document.getElementsByName("subtotal");
    var Stoc =document.getElementsByName("stock[]");


	for (var i = 0; i < cant.length; i++) {
		var inpC=cant[i];
    	var inpP=prec[i];
    	var inpD=desc[i];
    	var inpS=sub[i];
        var inpSt=Stoc[i];


		var subtl =inpS.value=(inpC.value * inpP.value)-(inpD.value*inpC.value);
        var subfinal= subtl.toFixed(2);

		if($('#tipo').val() == 'venta'){

			if(Number(inpC.value) > Number(inpSt.value)){
            
				swal("No hay suficiente stock!");
				 inpC.style.backgroundColor="#00CC00";
				 inpSt.style.backgroundColor="#CC0000";
			   $("#btnGuardar").hide(); 
				e.preventDefault();
			
			}
			else{
			
				 inpC.style.backgroundColor="#FFFFFF";
				 inpSt.style.backgroundColor="#FFFFFF";
			document.getElementsByName("subtotal")[i].innerHTML=subfinal;
			}

		}
        
	}

	calcularTotales();
	evaluar();
}

function calcularTotales(){

	var sub = document.getElementsByName("subtotal");
	var total=0.0;
  	var total_monto=0.0;
  	var igv_dec =0.0;
  	var totalConIgv=0.0;

	for (var i = 0; i < sub.length; i++) {
		
		total += document.getElementsByName("subtotal")[i].value;

		var proigv = document.getElementsByName("proigv")[i].innerHTML;

		if(proigv == "Gravada"){
			totalConIgv += document.getElementsByName("subtotal")[i].value;
			var igv=totalConIgv*(no_aplica)/(no_aplica+100);
			var total_monto=(totalConIgv-(igv)).toFixed(2);
			var igv_dec=igv.toFixed(2);
		}else{
		}


	}

	$.ajax({
	url: 'controladores/negocio.php?op=mostrar_simbolo',
	type:'get',
	dataType:'json',
	success: function(sim){

		simbolo=sim;
		total2=total-igv;

		$("#total").html(total.toFixed(2));
		$("#total_venta").val(total.toFixed(2));
		$("#most_total2").val(total.toFixed(2));
		$("#most_total").html(esnulo(total2).toFixed(2));

		$("#montoDeuda").val(total);

		$("#most_imp").html(igv_dec);
		evaluar();


		}

	});
	
}

function esnulo(v){
    if(isNaN(v)){
         return 0;
    }else{
        return v;
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

function eliminarDetalle(indice){
	$("#fila"+indice).remove();
	calcularTotales();
	detalles=detalles-1;
	evaluar();
	articuloAdd="";
}

init();