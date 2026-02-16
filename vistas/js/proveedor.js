var tabla;

//Función que se ejecuta al inicio
function init(){
	$("#body").addClass("sidebar-collapse sidebar-mini");
	listar();

	$("#myModal").on("submit",function(e)
	{
		guardaryeditar(e);	
	})

    $('#navComprasActive').addClass("treeview active");
    $('#navCompras').addClass("treeview menu-open");
    $('#navProveedor').addClass("active");

}

//Función limpiar
function limpiar()
{
	$("#nombre").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#idpersona").val("");
}

function mostrar(idpersona)
{
	$.post("controladores/persona.php?op=mostrar",{idpersona : idpersona}, function(data, status)
	{
		data = JSON.parse(data);		
        $('#myModal').modal('show');

		$("#nombre").val(data.nombre);
		$("#tipo_documento").val(data.tipo_documento);
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
 		$("#idpersona").val(data.idpersona);

 	})
}

function guardaryeditar(e)
{
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "controladores/persona.php?op=guardaryeditar",
	    type: "POST",
	    data: formData,
	    contentType: false,
	    processData: false,

	    success: function(datos)
	    {                    
	          Swal.fire({
				  title: 'Proveedor',
				  icon: 'success',
					text:datos
				});
				
              $('#myModal').modal('hide');
	          tabla.ajax.reload();


	    }

	});
	limpiar();
}

function BuscarCliente(){

    let numero=$("#num_documento").val();

    $.post("controladores/venta.php?op=selectCliente5&numero="+numero,function(data, status){

		data=JSON.parse(data);

		if(data != null){

            Swal.fire({
                title: '¡Aviso!',
                icon: 'info',
                  text:'El Proveedor ya se encuentra registrado'
              });

			$("#num_documento").val('');

		}else{

			if ($('#tipo_documento').val()=='DNI'){
    var cod = $.trim($('#tipo_documento').val());
    $numero=$("#num_documento").val();
    if($numero.length<8)
    {
        Swal.fire({
            title: 'Falta Números en el DNI',
            icon: 'info',
              text:'El DNI debe tener 8 Carácteres'
          });	
    }else{
    	$('#Buscar_Cliente').hide();
    	var numdni=$('#num_documento').val();
        var url = 'https://dniruc.apisperu.com/api/v1/dni/'+numdni+'?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Ik1hbnVlbF8xM18xOTk4QGhvdG1haWwuY29tIn0.pNHFyJ3fT4JgofrxzINaJWlqh3_fC9bCzfwSP4N_dMo';

    	$('#cargando').show();
    	$.ajax({
            type:'GET',
            url:url,
            success: function(dat){
              	if(dat.success == false){

                    Swal.fire({
                        title: 'DNI Inválido',
                        icon: 'error',
                          text:'¡No Existe DNI!'
                      });
                  
                    }else{
                        //$('#nombre').val(dat.success[0]);
                        $('#nombre').val(dat.nombres + " " + dat.apellidoPaterno + " " + dat.apellidoMaterno);
                        $('#Buscar_Cliente').hide();
                        $('#cargando').hide();
                  }
                }, complete: function(){

                	$('#Buscar_Cliente').show();
                	$('#cargando').hide();
                	
                }, error: function(){
                	
                }
        });
      }

  	}else{
    	var cod = $.trim($('#tipo_documento').val());
        $numero=$("#num_documento").val();
        if($numero.length<11){
            Swal.fire({
                title: 'Falta Números en el RUC',
                icon: 'info',
                  text:'El DNI debe tener 11 Carácteres'
              });
        }else{
    		$('#Buscar_Cliente').hide();          
            var numdni=$('#num_documento').val();
            var url = 'https://dniruc.apisperu.com/api/v1/ruc/'+numdni+'?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Ik1hbnVlbF8xM18xOTk4QGhvdG1haWwuY29tIn0.pNHFyJ3fT4JgofrxzINaJWlqh3_fC9bCzfwSP4N_dMo';
    		$('#cargando').show();
            $.ajax({
	            type:'GET',
	            url:url,
	            success: function(dat){
					console.log(dat);
	                if(dat.success == false){
                        Swal.fire({
                            title: 'Ruc Inválido',
                            icon: 'info',
                              text:'¡No Existe RUC!'
                          });
	                }else{
	                    $('#nombre').val(dat.razonSocial);
	                    $('#direccion').val(dat.direccion);
						document.getElementById('estado2').innerHTML= dat.estado;
						document.getElementById('condicion').innerHTML= dat.condicion;
	                    $('#Buscar_Cliente').hide();
                        $('#cargando').hide();         
	        		}
	            }, complete: function(){

                	$('#Buscar_Cliente').show();
                	$('#cargando').hide();
	            	
	            }, error: function(){
	            	
	            }            
            });
        }
  	}


		}
		
	});

}

//Función Listar
function listar()
{
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
					url: 'controladores/persona.php?op=listarp',
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

//Función cancelarform
function cancelarform()
{
	limpiar();
}

init();