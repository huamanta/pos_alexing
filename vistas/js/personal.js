var tabla;

function init(){
 $("#body").addClass("sidebar-collapse sidebar-mini");
    listar();
    limpiar();

    $("#myModal").on("submit",function(e)
	{
		guardaryeditar(e);	
	});
	
    $('#navPersonalActive').addClass("treeview active");
    $('#navPersonal').addClass("treeview menu-open");
    $('#navPersonalI').addClass("active");

    $("#imagenmuestra").show();
	$("#imagenmuestra").attr("src","files/personal/user.png");
	$("#imagenactual").val("user.png");

}

//Función limpiar
function limpiar()
{
	$("#nombre").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#cargo").val("Administrador");
	$("#imagenmuestra").attr("src","files//personal/user.png");
	$("#imagenactual").val("user.png");
	$("#imagen").val("");
	$("#idpersonal").val("");
}

//Función cancelarform
function cancelarform()
{
	limpiar();
}

function guardaryeditar(e)
{
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "controladores/empleado.php?op=guardaryeditar",
	    type: "POST",
	    data: formData,
	    contentType: false,
	    processData: false,

	    success: function(datos)
	    {                    
	          Swal.fire({
				  title: 'Personal',
				  icon: 'success',
					text:datos
				});
              $('#myModal').modal('hide');
	          tabla.ajax.reload();
	    }

	});
	limpiar();
}

function mostrar(idpersonal)
{
	$.post("controladores/empleado.php?op=mostrar",{idpersonal : idpersonal}, function(data, status)
	{
		data = JSON.parse(data);
        $('#myModal').modal('show');
        $("#nombre").val(data.nombre);
		$("#tipo_documento").val(data.tipo_documento);
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#cargo").val(data.cargo);
		$("#salario").val(data.salario);
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src","files/personal/"+data.imagen);
		$("#imagenactual").val(data.imagen);
		$("#idpersonal").val(data.idpersonal);

 	});
}

//Función Listar
function listar()
{
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
					url: 'controladores/empleado.php?op=listar',
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

//Función para desactivar registros
function desactivar(idpersonal)
{

	Swal.fire({
		title: '¿Desactivar?',
		text: "¿Está seguro Que Desea Desactivar el Personal?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si'
		}).then((result) => {
		if (result.isConfirmed) {
			$.post("controladores/empleado.php?op=desactivar", {idpersonal : idpersonal}, function(e){
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
				"Se Cancelo la desactivacion de el Personal",
				'info'
				)
		}
		})
		
}

//Función para desactivar registros
function activar(idpersonal)
{

	Swal.fire({
		title: 'Activar?',
		text: "¿Está seguro Que Desea Activar el Personal?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si'
		}).then((result) => {
		if (result.isConfirmed) {
			$.post("controladores/empleado.php?op=activar", {idpersonal : idpersonal}, function(e){
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
				"Se Cancelo la activación de el Personal",
				'info'
				)
		}
		})
		
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

init();

