var tabla;

//Función que se ejecuta al inicio
function init(){
	$("#body").addClass("sidebar-collapse sidebar-mini");
	listar();

	$("#imagenmuestra").show();
	$("#imagenmuestra").attr("src","files/productos/anonymous.png");
	$("#imagenactual").val("anonymous.png");

	$("#myModal").on("submit",function(e)
	{
		guardaryeditar(e);	
	});

	//cargamos los items al select almacen
	$.post("controladores/venta.php?op=selectSucursal", function(r){
		$("#idsucursal").html(r);
		$('#idsucursal').select2('');
	});

	//Cargamos los items al select categoria
	$.post("controladores/producto.php?op=selectCategoria2", function(r){

	    $("#idcategoria").html(r);
	    $('#idcategoria').select2('');

	});

	//Cargamos los items al select categoria
 
  $.post("controladores/producto.php?op=selectUnidadMedida", function (r) {
    $("#idunidad_medida").html(r);
    $("#idunidad_medida").select2("");
  });

  $.post("controladores/producto.php?op=selectRubro", function (r) {
    $("#idrubro").html(r);
    $("#idrubro").select2("");
  });

  $.post("controladores/producto.php?op=selectCondicionVenta", function (r) {
    $("#idcondicionventa").html(r);
    $("#idcondicionventa").select2("");
  });

	//Mostramos los sucursales
	$.post("controladores/producto.php?op=sucursales",function(r){
	    $("#sucursales").html(r);
	});

	//cargamos los items al select almacen
	$.post("controladores/venta.php?op=selectSucursal3", function(r){
		$("#idsucursal2").html(r);
		$('#idsucursal2').select2('');
	});

	$("#idsucursal2").change(listar);

	$('#navAlmacenActive').addClass("treeview active");
    $('#navAlmacen').addClass("treeview menu-open");
    $('#navServicio').addClass("active");

}

function limpiar() {
  $("#codigo").val("");
  $("#nombre").val("");
  $("#descripcion").val("");
  $("#stock").val("1");
  $("#stockMinimo").val("0");
  $("#precio").val("");
  $("#precioB").val("");
  $("#precioC").val("");
  $("#precioD").val("");
  $("#fecha").val("");
  $("#fecha_hora").val("");
  $("#imagenmuestra").attr("src", "files/productos/anonymous.png");
  $("#imagenactual").val("anonymous.png");
  $("#print").hide();
  $("#idproducto").val("");
  $("#idcategoria").val("");
  $("#idcategoria").select2("");
  $("#idunidad_medida").val("");
  $("#idunidad_medida").select2("");
  $("#idrubro").val("");
  $("#idrubro").select2("");
  $("#idcondicionventa").val("");
  $("#idcondicionventa").select2("");
  $("#registrosan").val("");
  $("#fabricante").val("");
  $("#modelo").val("");
  $("#nserie").val("");
  $("#porc").val("");
  $("#precioCompra").val("");
  $("#margenUtilidad").val("");
  

  $("#preciocigv").val("");

  $("#comisionV").val("");
}

//Función cancelarform
function cancelarform()
{
	limpiar();
}

function mostrar(idproducto)
{
	$.post("controladores/producto.php?op=mostrar",{idproducto : idproducto}, function(data, status)
	{
		data = JSON.parse(data);
		
        $('#myModal').modal('show');

		$("#idsucursal").val(data.idsucursal);
		$('#idsucursal').select2('');
		$("#idcategoria").val(data.idcategoria);
		$('#idcategoria').select2('');
		$("#idunidad_medida").val(data.idunidad_medida);
		$('#idunidad_medida').select2('');
		$("#codigo").val(data.codigo);
		$("#nombre").val(data.nombre);
		$("#stock").val(data.stock);
		$("#stockMinimo").val(data.stock_minimo
			);
		$("#precio").val(data.precio);
		$("#preciocigv").val(data.preciocigv);
		$("#precioB").val(data.precioB);
		$("#precioC").val(data.precioC);
		$("#precioD").val(data.precioD);
		 $("#idrubro").val(data.idrubro);
		  $("#idrubro").select2('');
		  $("#idcondicionventa").val(data.idcondicionventa);
		  $("#idcondicionventa").select2('');
		  $("#registrosan").val(data.idregistrosan);
		  $("#fabricante").val(data.fabricante);
		$("#precioCompra").val(data.precio_compra);
		$("#fecha_hora").val(data.fecha);
		$("#descripcion").val(data.descripcion);
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src","files/productos/"+data.imagen);
		$("#imagenactual").val(data.imagen);
 		$("#idproducto").val(data.idproducto);
 		$("#modelo").val(data.modelo);
 		$("#nserie").val(data.numserie);
 		$("#tipoigv").val(data.proigv);
 		generarbarcode();

 	})
}

function calcularPrecioIGV(){

	var numero = $('#precio').val();

	var numIgv = numero * 1.18;

	$('#preciocigv').val(numIgv.toFixed(2));

}

//Función Listar
function listar()
{

	let idsucursal2 = $("#idsucursal2").val();

	tabla=$('#tbllistado').dataTable(
	{
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
						title: 'Lista de Servicios', 
						// className: 'btn btn-success'
					},
					{
						extend: 'pdf', 
						text: "<i class='fas fa-file-pdf'></i>", 
						titleAttr: 'Exportar a PDF',
						title: 'Lista de Servicios', 
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
					url: 'controladores/producto.php?op=listarServicio',
					data:{idsucursal2: idsucursal2},
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 5,//Paginación
	    "order": [[ 1, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}

function guardaryeditar(e) {
  e.preventDefault(); //No se activará la acción predeterminada del evento
  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "controladores/producto.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      Swal.fire({
        title: "Servicio",
        icon: "success",
        text: datos,
      });

      $("#myModal").modal("hide");
      tabla.ajax.reload();
      limpiarFormulario();
      limpiarProducto(); // Llama a la función para limpiar el formulario
    },
  });
}

function limpiarFormulario() {
  // Resetea el formulario
  document.getElementById("formulario").reset();

}function limpiarProducto() {
  $("#idproducto").val("");
  
}


//Función para desactivar registros
function desactivar(idproducto)
{

	Swal.fire({
		title: '¿Desactivar?',
		text: "¿Está seguro Que Desea Desactivar el Servicio?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si'
		}).then((result) => {
		if (result.isConfirmed) {
			$.post("controladores/producto.php?op=desactivar", {idproducto : idproducto}, function(e){
				Swal.fire(
					'Desactivado!',
					e,
					'success'
					)
				tabla.ajax.reload();
			});
		}else{
			Swal.fire(
				'Aviso!',
				"Se Cancelo la desactivacion de el Servicio",
				'info'
				)
		}
		})
		
}

//Función para desactivar registros
function activar(idproducto)
{

	Swal.fire({
		title: 'Activar?',
		text: "¿Está seguro Que Desea Activar el Servicio?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si'
		}).then((result) => {
		if (result.isConfirmed) {
			$.post("controladores/producto.php?op=activar", {idproducto : idproducto}, function(e){
				Swal.fire(
					'Activado!',
					e,
					'success'
					)
				tabla.ajax.reload();
			});
		}else{
			Swal.fire(
				'Aviso!',
				"Se Cancelo la activación de el Servicio",
				'info'
				)
		}
		})
		
}

//función para generar el código de barras
function generarbarcode()
{
	codigo=$("#codigo").val();
	JsBarcode("#barcode", codigo);
	$("#print").show();
}

//Función para imprimir el Código de barras
function imprimir()
{
	$("#print").printArea();
}

/*=============================================
SUBIENDO LA FOTO DEL PRODUCTO
=============================================*/

$("#imagen").change(function(){

	var imagen = this.files[0];
	
	/*=============================================
	  VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
	  =============================================*/
  
	  if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){
  
		$(".nuevaImagen").val("");
  
		 swal({
			title: "Error al subir la imagen",
			text: "¡La imagen debe estar en formato JPG o PNG!",
			type: "error",
			confirmButtonText: "¡Cerrar!"
		  });
  
	  }else if(imagen["size"] > 2000000){
  
		$(".nuevaImagen").val("");
  
		 swal({
			title: "Error al subir la imagen",
			text: "¡La imagen no debe pesar más de 2MB!",
			type: "error",
			confirmButtonText: "¡Cerrar!"
		  });
  
	  }else{
  
		var datosImagen = new FileReader;
		datosImagen.readAsDataURL(imagen);
  
		$(datosImagen).on("load", function(event){
  
		  var rutaImagen = event.target.result;
  
		  $("#imagenmuestra").attr("src", rutaImagen);
  
		})
  
	  }
  })

function config(idproducto, nombre, precio) {
  $("#ModalConfigProducto").modal("show");
  $("#p-producto").html('<span class="badge bg-info" style="font-size:20px">' + nombre + '</span>');
  $("#idproductoconfig").val(idproducto);
  $("#p-unitario").html('<span class="badge bg-info" style="font-size:20px">S/. ' + precio + '</span>');
  listarDataCofig(idproducto);
}

function listarDataCofig(idproducto) {
  $.ajax({
    url:
      "controladores/producto.php?op=listCofiguration&idproducto=" + idproducto,
    type: "GET",
    data: "",
    contentType: false,
    processData: false,
    beforeSend: function () {
      m = 0;
      p = 0;
    },
    success: function (response) {
      var response = JSON.parse(response);
      var html = "";
      if (response != "") {
        $.each(response, function (i, item) {
          p = p + 1;
          html +=
            `<tr id="fila` +
            m +
            `">
          <td style="width: 30%;"><input type="hidden" class="form-control" name="id[]" value="` +
            response[i].id +
            `"><input type="text" class="form-control" name="codigo_extra_config[]" value="` +
            response[i].codigo_extra +
            `" required></td>
          <td style="width: 30%;"><input type="text" class="form-control" name="contenedor_config[]" value="` +
            response[i].contenedor +
            `" required></td>
          <td style="width: 10%;"><input type="text" class="form-control" name="cant_contenedor_config[]" value="` +
            response[i].cantidad_contenedor +
            `" required></td>
          <td style="width: 10%;"><input type="text" class="form-control" name="precio_config[]" value="` +
            response[i].precio_venta +
            `" required></td>
          <td style="width: 10%;"><input type="text" class="form-control" name="precio_promocion_config[]" value="` +
            response[i].precio_promocion +
            `"></td>
          <td style="width: 10%;" class="text-center"><i class="fa fa-trash"  onclick="eliminarFila(` +
            m +
            `,` +
            response[i].id +
            `)"></i></td>
        </tr>`;
          m = m + 1;
        });
        $("#detalle").html(html);
      } else {
        comprobarData();
      }
    },
  });
}

function comprobarData() {
  if (p == 0) {
    $("#detalle").html(`<tr id="filax">
        <td colspan="6" class="text-center">No existe cofigurciones para el prodcto.</td>
      </tr>`);
  }else{
    $("#filax").remove();
  }
}

function agregarCofiguracion() {
  p = p + 1;
  $("#detalle").append(
    `<tr id="fila` +
      m +
      `">
		<td style="width: 30%;"><input type="hidden" class="form-control" name="id[]" value="0"><input type="text" class="form-control" name="codigo_extra_config[]" required></td>
		<td style="width: 30%;"><select class="form-control" name="contenedor_config[]" required>
        <option value=" ">SELECCIONE CONTENDOR</option>
        <option value="UNIDAD">UNIDAD</option>
        <option value="BLEASTER">BLEASTER</option>
        <option value="CAJA">CAJA</option>
    </select></td>
		<td style="width: 10%;"><input type="text" class="form-control" name="cant_contenedor_config[]" required></td>
		<td style="width: 10%;"><input type="text" class="form-control" name="precio_config[]" required></td>
		<td style="width: 10%;"><input type="text" class="form-control" name="precio_promocion_config[]"></td>
		<td style="width: 10%;" class="text-center"><i class="fa fa-trash"  onclick="eliminarFila(` +
      m +
      `,'')"></i></td>
	</tr>`
  );
  m = m + 1;
  comprobarData();
}

function eliminarFila(m, id) {
  p = p - 1;
  $("#fila" + m).remove();
  if (id != "") {
    $.ajax({
      url: "controladores/producto.php?op=eliminarCofiguration&idconfig=" + id,
      type: "GET",
      data: "",
      contentType: false,
      processData: false,
      beforeSend: function () {},
      success: function (data) {
        console.log("Eliminado correctamente");
      },
      error: function (error) {
        console.log(error.responseText);
      },
    });
  }
  comprobarData();
}

$("#saveCofigurtion").submit(function (e) {
  e.preventDefault();
  var data = new FormData(this);
  $.ajax({
    url: "controladores/producto.php?op=saveCofiguration",
    type: "POST",
    data: data,
    contentType: false,
    processData: false,
    success: function (response) {
      Swal.fire({
        title: "¡Configuración guardada!",
        text: "La configuración se ha guardado correctamente.",
        icon: "success",
        timer: 2000, // Tiempo en milisegundos (2 segundos en este caso)
        showConfirmButton: false
      });
      listarDataCofig($("#idproductoconfig").val());
      listar();
      // Cierra automáticamente el modal aquí (reemplaza el selector y el método con los adecuados)
      $("#ModalConfigProducto").modal("hide");
    },
    error: function (xhr, status, error) {
      Swal.fire("Error", "Ocurrió un error al guardar la configuración.", "error");
    }
  });
});


function fechaVencimiento(id) {
  $('#fechavencimiento-modal').modal('show');
  $.ajax({
    url:'controladores/producto.php?op=listarvencimiento&id='+id,
    type: 'get',
    data:'',
    contentType:false,
    processData:false,
    success:function(resp){
      var json = JSON.parse(resp);
      $('#dataVencimiento').html(json.data);
      $('#totareal').html(json.suma+' Unid.');
    }
  });
}

function imprimirCodigosBarras() {
  $('#ModalCodigosProducto').modal('show');
  $.ajax({
    url:
      "controladores/producto.php?op=listCofiguration&idproducto=" + $("#idproductoconfig").val(),
    type: "GET",
    data: "",
    contentType: false,
    processData: false,
    beforeSend: function () {
      m = 0;
      p = 0;
    },
    success: function (response) {
      var response = JSON.parse(response);
      console.log(response);
      var html = "";
      if (response != "") {
        $.each(response, function (i, item) {
          html += '<div class="col-md-4"><p>'+response[i].contenedor+'</p>';
          html += '<svg id="codigo'+i+'"></svg></div>';
        });
        $("#codigos").html(html);
        $.each(response, function (i, item) {
          JsBarcode("#codigo"+i, response[i].codigo_extra);
        });
      } else {
      }
    },
  });
}

function imprSelec(nombre) {
  var ficha = document.getElementById(nombre);
  var ventimp = window.open(' ', 'popimpr');
  ventimp.document.write( ficha.innerHTML );
  ventimp.document.close();
  ventimp.print();
  ventimp.close();
  $('#ModalCodigosProducto').modal('hide');
}
function cancelarForm() {
    // Obtener todos los campos de entrada del modal
    var campos = document.getElementById("myModal").querySelectorAll("input");

    // Iterar sobre cada campo y establecer su valor en vacío
    campos.forEach(function(campo) {
        campo.value = "";
    });
}
init();