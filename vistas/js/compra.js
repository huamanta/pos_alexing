var tabla;
var sucursalActual;
function init(){
	 $("#body").addClass("sidebar-collapse sidebar-mini");
	mostrar_impuesto();
	
	mostrarform(false);
	
    listar();
	
    $("#formulario").on("submit",function(e)
	{
		guardaryeditar(e);	
	});

	$("#formularioGuardarImagen").on("submit",function(e)
	{
		guardarImagen(e);	
	});
	
	$("#formularioProveedores").on("submit", function (e) {
    guardarProveedor(e);
  });

    $.post("controladores/venta.php?op=selectSucursal", function(r){
        $("#idsucursal").html(r);
        $('#idsucursal').select2('');

        // 1. Guardamos la sucursal inicial (la que viene por defecto)
        sucursalActual = $("#idsucursal").val();

        // 2. Cargamos los artículos de esa sucursal inicial
        listarArticulos();

        // 3. EVENTO ESPECIAL DE SELECT2: "select2:selecting"
        // Este evento ocurre ANTES de que el valor cambie visualmente
        $('#idsucursal').on('select2:selecting', function (e) {
            
            // Verificamos si hay filas agregadas en la compra (detalles > 0)
            // Nota: Asegúrate de que la variable 'detalles' esté definida globalmente en tu archivo
            if (typeof detalles !== 'undefined' && detalles > 0) {
                
                // DETENEMOS el cambio automático del select
                e.preventDefault(); 
                
                // Guardamos el ID de la sucursal que el usuario INTENTÓ seleccionar
                var nuevaSucursal = e.params.args.data.id;

                Swal.fire({
                    title: '¿Cambiar de Sucursal?',
                    text: "Tienes productos agregados en el carrito. Si cambias de sucursal, se limpiará toda la compra actual. ¿Deseas continuar?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cambiar y limpiar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // SI EL USUARIO DICE QUE SÍ:
                        
                        // 1. Limpiamos formulario y variables (Tu función limpiar)
                        limpiar(); 
                        
                        // 2. Forzamos el cambio de valor manualmente (porque lo bloqueamos antes)
                        $("#idsucursal").val(nuevaSucursal).trigger('change.select2');
                        
                        // 3. Actualizamos la variable de control
                        sucursalActual = nuevaSucursal;
                        
                        // 4. Recargamos la tabla de artículos con la nueva sucursal
                        listarArticulos(); 
                        
                        Swal.fire('¡Cambiado!', 'Ahora estás comprando en la nueva sucursal.', 'success');
                    }
                    // Si dice que NO, no hacemos nada, y el select se queda como estaba gracias al e.preventDefault()
                });

            } else {
                // SI NO HAY DETALLES (Carrito vacío):
                // Dejamos que el cambio ocurra, pero necesitamos actualizar la tabla
                // Usamos un pequeño timeout para asegurar que el valor ya cambió
                setTimeout(function(){
                    sucursalActual = $("#idsucursal").val();
                    listarArticulos();
                }, 100);
            }
        });
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
		$("#idsucursal2").select2("");
	});
	
	$('#navComprasActive').addClass("treeview active");
    $('#navCompras').addClass("treeview menu-open");
    $('#navCompra').addClass("active");

	$("#fecha_inicio").change(listar);
	$("#fecha_fin").change(listar);
	$("#idsucursal2").change(listar);


}

//Función limpiar
function limpiarProveedor() {
    $("#nombre").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#idpersona").val("");
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
	
	// Limpiar todos los valores mostrados y ocultos de totales
	$("#total").html("0.00");
	$("#most_total").html("0.00");
	$("#most_imp").html("0.00");
	$("#most_gravado").html("0.00");        // ← NUEVO
	$("#most_exonerado").html("0.00");      // ← NUEVO
	
	// Limpiar inputs hidden
	$("#monto_gravado").val("0");           // ← NUEVO
	$("#monto_exonerado").val("0");         // ← NUEVO
	$("#monto_igv").val("0");               // ← NUEVO
	$("#impuesto").val("0");                // ← NUEVO
	
	// Resetear tipo_igv a EXONERADA por defecto
	$("#tipo_igv").val("EXONERADA");        // ← NUEVO
	
	//Obtenemos la fecha actual
	var now = new Date();
	var day = ("0" + now.getDate()).slice(-2);
	var month = ("0" + (now.getMonth() + 1)).slice(-2);
	var today = now.getFullYear()+"-"+(month)+"-"+(day);
	$('#fecha').val(today);
	
	//Marcamos el primer tipo_documento
	$("#tipo_comprobante").val("Boleta");
	$("#tipo_comprobante").select2('');
	$("#formapago").select2('');
	$("#lugar_entrega").val("");
	$("#motivo_compra").val("");
	$("#totaldeposito").val("");
	$("#totalrecibido").val("");
	
	// Limpiar campos de crédito si existen
	$("#tipopago").val("No");               // ← OPCIONAL
	$("#montoPagado").val("0");             // ← OPCIONAL
	$("#montoDeuda").val("0");              // ← OPCIONAL
	$("#input_cuotas").val("");             // ← OPCIONAL
	$("#datafechas").html("");              // ← OPCIONAL (limpiar tabla de cuotas)
	
	// Ocultar paneles de crédito si están visibles
	$("#n0, #n1, #n2, #n3, #n4").hide();   // ← OPCIONAL
	$("#panel1").hide();                    // ← OPCIONAL
}

function limpiarImagen(){

	$("#imagenmuestra").attr("src","files/productos/anonymous.png");
	$("#imagenactual").val("anonymous.png");
	$("#imagen").val("");
	$("#idcompraI").val("");

}

//Función cancelarform
function cancelarform()
{
	
	limpiar();
	mostrarform(false);
}



//funcion para Guardar Clientes
function guardarProveedor(e) {
  e.preventDefault(); //no se activara la accion predeterminada
  //$("#btnGuardar").prop("disabled",true);
  var formData = new FormData($("#formularioProveedores")[0]);

  $.ajax({
    url: "controladores/compra.php?op=guardarProveedor",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      Swal.fire({
        title: "Proveedor",
        icon: "success",
        text: datos,
      });
      
      	$.post("controladores/compra.php?op=selectProveedor", function(r){
	            $("#idproveedor").html(r);
	            $('#idproveedor').select2({
					placeholder: 'Seleccionar Proveedor ...',
					allowClear: true
				}).val(null).trigger('change');
	});

      $.post(
        "controladores/venta.php?op=mostrarUltimoCliente",
        function (data, status) {
          data = JSON.parse(data);

          seleccionarCliente(data.nombre, data.idpersona);
        }
      );
    },
  });

  $("#myModalProveedor").modal("hide");
  // Resetear el formulario
  document.getElementById("formularioProveedores").reset();

  limpiarCliente();
}

//Función limpiar
function limpiarCliente()
{
	$("#nombre").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#estado2").val("");
	$("#condicion").val("");
	$("#idpersona").val("");
}


function seleccionarCliente(nombre, idcliente) {
  $("#idcliente").val(idcliente);
  $("#idcliente").select2("");
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
  
		 Swal.fire({
			title: 'Error al subir la imagen',
			icon: '¡La imagen debe estar en formato JPG o PNG!',
			  text:datos
		  });
  
	  }else if(imagen["size"] > 2000000){
  
		$(".nuevaImagen").val("");
		
		 Swal.fire({
			title: 'Error al subir la imagen',
			icon: '¡La imagen no debe pesar más de 2MB!',
			  text:datos
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

//funcion para Guardar Clientes
function guardarImagen(e){
	e.preventDefault();//no se activara la accion predeterminada 
	//$("#btnGuardar").prop("disabled",true);
	var formData=new FormData($("#formularioGuardarImagen")[0]);

	$.ajax({
		url: "controladores/compra.php?op=guardarImagen",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function(datos){
			Swal.fire({
				title: 'Imagen Guardada',
				icon: 'success',
				  text:datos
			  });
		}
	});

	$("#myModalP").modal('hide');

	limpiarImagen();

	listar();

}

$(document).ready(function() {
    $('#formulario').on('keypress', function(e) {
        if (e.which === 13) { // Código de tecla Enter
            e.preventDefault(); // Evita el envío del formulario
        }
    });

    $('#formulario').on('', function(e) {
        guardaryeditar(e);
    });
});

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
			
			console.log(datos);
	          Swal.fire({
				  title: 'Compra',
				  icon: 'success',
					text:datos
				});
			  mostrarform(false);
			  listar();
	    },
		error: function(error){
			console.log(error.responseText);
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
    if (flag)
    {
        $('#body').addClass('sidebar-collapse');
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnagregar").hide();
        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        detalles=0;
        $("#btnAgregarArt").show();
        $("#btnNuevo").hide();
        $("#header").hide();

        listarArticulos();
        $("#pagos_wrapper").html("");
        initPagos();
        
        // ✅ RESET
        $('#detalles tbody').empty();
        articuloAdd = "";
        cont = 0;
        detalles = 0;
        
        // ⚠️ SOLO CARGAR SI NO HAY idcompra (modo nuevo)
        if (!$("#idcompra").val()) {
            listarDetalleTmp();
        }
    }
    else
    {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
        $("#btnNuevo").show();
        $("#header").show();
        $("#btnGuardar").show();
        
        // Restaurar texto del botón al salir
        $("#btnGuardar").html('<i class="fas fa-check-circle"></i> Realizar Compra');
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

function subirImagen(idcompra,imagen){
	
	if(imagen != ""){
		
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src","files/compras/"+imagen);
		$("#imagenactual").val(imagen);

	}else{

		$("#imagenmuestra").attr("src","files/productos/anonymous.png");

	}

	$("#myModalP").modal('show');
	$("#idcompraI").val(idcompra);
	
}

function mostrar(idcompra)
{
	$("#getCodeModal").modal('show');
	$.post("controladores/compra.php?op=mostrar",{idcompra : idcompra}, function(data, status)
	{
		data = JSON.parse(data);		
		//mostrarform(true);

		$("#idproveedorm").val(data.proveedor);
		$("#tipo_comprobantem").val('Compra');
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

/*
$("#idsucursal").change(function() {
  listarArticulos();
});
*/

function listarArticulos(){
    var idsucursal = $("#idsucursal").val();
    
    // Destruir tabla si existe
    if ($.fn.DataTable.isDataTable('#tblarticulos')) {
        $('#tblarticulos').DataTable().destroy();
    }
    
    tabla = $('#tblarticulos').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 5,
        "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
        "order": [[0, "asc"]],
        "searchDelay": 500,
        "dom": 'frtip',
        "language": {
            "processing": "Procesando...",
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros)",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "No hay productos disponibles",
            "loadingRecords": "Cargando...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "ajax": {
            "url": 'controladores/compra.php?op=listarArticulos',
            "data": function(d) {
                d.idsucursal = idsucursal;
            },
            "type": "GET",
            "dataType": "json",
            "error": function(xhr, error, thrown){
                console.error('Error al cargar productos:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudieron cargar los productos. Intenta recargar la página.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
    });
    
    // Delegación de eventos para botones dinámicos
    $('#tblarticulos tbody').off('click', '.btn-agregar');
    $('#tblarticulos tbody').on('click', '.btn-agregar', function(){
        var id = $(this).data('id');
        var input = $('#cantidaC_' + id);
        var cantidad = parseFloat(input.val()) || 0;
        
        if (cantidad <= 0) {
            Swal.fire({
                title: 'Cantidad inválida',
                text: 'Por favor ingrese una cantidad válida',
                icon: 'warning',
                confirmButtonText: 'Aceptar'
            });
            input.focus();
            return;
        }
        
        // Solo llamar a tu función agregarDetalle existente
        agregarDetalle(
            id,
            input.data('nombre'),
            input.data('precio'),
            input.data('precio-compra'),
            input.data('unidad'),
            cantidad
        );
        
        // Limpiar input
        input.val('');
    });
    
    // Enter para agregar
    $('#tblarticulos tbody').off('keypress', '.cantidad-input');
    $('#tblarticulos tbody').on('keypress', '.cantidad-input', function(e){
        if (e.which === 13) {
            e.preventDefault();
            $(this).closest('tr').find('.btn-agregar').click();
        }
    });
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
					url: 'controladores/compra.php?op=listar',
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

function agregarDetalle(idproducto, producto, precioVenta, precioCompra, unidadmedida, cantidad) {
    if (event) {
        event.preventDefault();
    }

    if (articuloAdd.split('-').indexOf(idproducto.toString()) !== -1) {
        Swal.fire({
            title: '',
            icon: 'info',
            text: producto + ' ya se agregó'
        });
        return;
    }

    if (idproducto == "") {
        Swal.fire("", "Error al ingresar el detalle, revisar los datos del producto", "info");
        return;
    }

    // Limpiar decimales innecesarios
    precioCompra = parseFloat(precioCompra);
    precioVenta = parseFloat(precioVenta);

    // Calcular subtotal
    var subtotal = cantidad * precioCompra;
    subtotal = parseFloat(subtotal).toFixed(2);

    // Truncar nombre si es muy largo
    var productoCompleto = producto + ' x ' + unidadmedida;
    var productoCorto = productoCompleto;
    
    if (productoCompleto.length > 45) {
        productoCorto = productoCompleto.substring(0, 42) + '...';
    }

    var fila = '<tr class="filas" id="fila' + cont + '">' +
        '<td>' +
            '<input type="hidden" name="idproducto[]" value="' + idproducto + '">' +
            '<div class="nombre-producto-wrapper">' +
                '<input class="form-control form-control-sm producto-nombre-input" ' +
                'style="width: 100%; max-width: 350px; font-weight: bold;" ' +
                'type="text" name="nombreProducto[]" ' +
                'value="' + productoCorto + '" ' +
                'title="' + productoCompleto + '" ' +
                'readonly>' +
                (productoCompleto.length > 45 ? 
                    '<div class="nombre-producto-full">' +
                        '<small class="text-muted">Nombre completo:</small><br>' +
                        '<strong>' + productoCompleto + '</strong>' +
                    '</div>' 
                : '') +
            '</div>' +
        '</td>' +
        '<td>' +
            '<input class="form-control form-control-sm text-center" style="width: 100px;" ' +
            'type="number" step="0.01" name="cantidad[]" id="cantidad[]" value="' + cantidad + '" ' +
            'oninput="modificarSubtotales(); datosCambiados(this);">' +
        '</td>' +
        '<td>' +
            '<input class="form-control form-control-sm text-center" style="width: 100px;" ' +
            'type="number" step="0.00000001" name="precio_compra[]" id="precio_compra[]" value="' + precioCompra + '" ' +
            'oninput="modificarSubtotales(); datosCambiados(this);">' +
        '</td>' +
        '<td>' +
            '<input class="form-control form-control-sm text-center" style="width: 100px;" ' +
            'type="number" step="0.00000001" name="precio_venta[]" value="' + precioVenta + '" ' +
            'oninput="datosCambiados(this);">' +
        '</td>' +
        '<td>' +
            '<span class="badge bg-success" style="text-align:center; width: 90px; font-size: 15px;" ' +
            'id="subtotal' + cont + '" name="subtotal">' + subtotal + '</span>' +
        '</td>' +
        '<td>' +
            '<input class="form-control form-control-sm" type="text" name="nlote[]" oninput="datosCambiados(this)" onblur="datosCambiados(this)">' +
        '</td>' +
        '<td>' +
            '<input class="form-control form-control-sm" type="date" name="fvencimiento[]" onchange="datosCambiados(this)" onblur="datosCambiados(this)">' +
        '</td>' +
        '<td class="text-center">' +
            '<button type="button" class="btn btn-danger btn-sm" ' +
            'onclick="eliminarDetalle(' + cont + ', ' + idproducto + ')">' +
                '<i class="fa fa-trash"></i>' +
            '</button>' +
        '</td>' +
    '</tr>';

    cont++;
    detalles = detalles + 1;
    articuloAdd = articuloAdd + idproducto + "-";
    $('#detalles').append(fila);
    modificarSubtotales();
    actualizarMontoPagoDefault();
    
    var nlote = '';
    var fvencimiento = '';
    
    agregarDetalleTmp(
        idproducto,
        producto,
        cantidad,
        precioCompra,
        precioVenta,
        unidadmedida,
        nlote,
        fvencimiento
    );
}

function datosCambiados(inputElement) {
    var fila = $(inputElement).closest('tr');

    var idproducto     = fila.find('input[name="idproducto[]"]').val();
    var cantidad       = fila.find('input[name="cantidad[]"]').val();
    var precioCompra   = fila.find('input[name="precio_compra[]"]').val();
    var precioVenta    = fila.find('input[name="precio_venta[]"]').val();
    var nlote          = fila.find('input[name="nlote[]"]').val();
    var fvencimiento   = fila.find('input[name="fvencimiento[]"]').val();

    actualizarDetalleTmp(
        idproducto,
        cantidad,
        precioCompra,
        precioVenta,
        nlote,
        fvencimiento
    );
}

function actualizarDetalleTmp(idproducto, cantidad, precioCompra, precioVenta, nlote, fvencimiento) {
    $.post("controladores/compra.php?op=actualizar_tmp", {
        idsucursal: $("#idsucursal").val(),
        idproducto: idproducto,
        cantidad: cantidad,
        precio_compra: precioCompra,
        precio_venta: precioVenta,
        nlote: nlote,
        fvencimiento: fvencimiento
    }, function (r) {
        console.log("Update TMP:", r);
    });
}


function agregarDetalleTmp(idproducto, producto, cantidad, precioCompra, precioVenta, unidadmedida, nlote, fvencimiento) {
    $.post("controladores/compra.php?op=agregar_tmp", {
        idsucursal: $("#idsucursal").val(),
        idproducto: idproducto,
        nombreProducto: producto,
        cantidad: cantidad,
        precio_compra: precioCompra,
        precio_venta: precioVenta,
        unidadmedida: unidadmedida,
        nlote: nlote,
        fvencimiento: fvencimiento
    }, function (r) {
        console.log("TMP OK:", r);
    });
}

function listarDetalleTmp() {
    $.post("controladores/compra.php?op=listar_tmp", {
        idsucursal: $("#idsucursal").val()
    }, function (data) {

        data = JSON.parse(data);

        if (data.length === 0) return;

        data.forEach(function (p) {
            pintarDetalle(
                p.idproducto,
                p.nombre_producto,
                p.precio_venta,
                p.precio_compra,
                p.unidadmedida,
                p.cantidad,
                p.nlote,
                p.fvencimiento
            );
        });
        modificarSubtotales();
        actualizarMontoPagoDefault();
    });
}

function pintarDetalle(idproducto, producto, precioVenta, precioCompra, unidadmedida, cantidad, nlote, fvencimiento) {

    precioCompra = parseFloat(precioCompra);
    precioVenta  = parseFloat(precioVenta);
    var subtotal = parseFloat(cantidad * precioCompra).toFixed(2);

    var productoCompleto = producto + ' x ' + unidadmedida;
    var productoCorto = productoCompleto;
    if (productoCompleto.length > 45) {
        productoCorto = productoCompleto.substring(0, 42) + '...';
    }

    // Cantidad mínima = lo que ya fue vendido (no se puede bajar de ahí)
    var cantidadVendida = (window._cantidadVendida && window._cantidadVendida[idproducto])
                         ? window._cantidadVendida[idproducto]
                         : 0;
    var cantidadMin = cantidadVendida > 0 ? cantidadVendida : 0.01;
    var avisoMin    = cantidadVendida > 0
                     ? '<small class="text-warning"><i class="fa fa-exclamation-triangle"></i> ' +
                       'Mín: ' + cantidadVendida + ' (ya vendido)</small>'
                     : '';

    var fila =
        '<tr class="filas" id="fila' + cont + '">' +
            '<td>' +
                '<input type="hidden" name="idproducto[]" value="' + idproducto + '">' +
                '<div class="nombre-producto-wrapper">' +
                    '<input class="form-control form-control-sm producto-nombre-input" ' +
                    'style="width:100%;max-width:350px;font-weight:bold;" ' +
                    'type="text" name="nombreProducto[]" ' +
                    'value="' + productoCorto + '" ' +
                    'title="' + productoCompleto + '" readonly>' +
                '</div>' +
                avisoMin +
            '</td>' +
            '<td>' +
                '<input class="form-control form-control-sm text-center" style="width:100px;" ' +
                'type="number" step="0.01" ' +
                'min="' + cantidadMin + '" ' +       // ← mínimo bloqueado
                'name="cantidad[]" value="' + cantidad + '" ' +
                'oninput="validarCantidadMin(this, ' + cantidadMin + '); modificarSubtotales(); datosCambiados(this);">' +
            '</td>' +
            '<td>' +
                '<input class="form-control form-control-sm text-center" style="width:100px;" ' +
                'type="number" step="0.00000001" name="precio_compra[]" value="' + precioCompra + '" ' +
                'oninput="modificarSubtotales(); datosCambiados(this);">' +
            '</td>' +
            '<td>' +
                '<input class="form-control form-control-sm text-center" style="width:100px;" ' +
                'type="number" step="0.00000001" name="precio_venta[]" value="' + precioVenta + '" ' +
                'oninput="datosCambiados(this);">' +
            '</td>' +
            '<td>' +
                '<span class="badge bg-success" style="width:90px;font-size:15px;" ' +
                'id="subtotal'  + cont + '" name="subtotal">' + subtotal + '</span>' +
            '</td>' +
            '<td>' +
                '<input class="form-control form-control-sm" style="width:90px;" ' +
                'type="text" name="nlote[]" ' +
                'value="' + (nlote ? nlote : '') + '" ' +
                'oninput="datosCambiados(this)">' +
            '</td>' +
            '<td>' +
                '<input class="form-control form-control-sm" style="width:150px;" ' +
                'type="date" name="fvencimiento[]" ' +
                'value="' + (fvencimiento ? fvencimiento : '') + '" ' +
                'onchange="datosCambiados(this)">' +
            '</td>' +
            '<td class="text-center">' +
                '<button type="button" class="btn btn-danger btn-sm" ' +
                'onclick="eliminarDetalle(' + cont + ', ' + idproducto + ')">' +
                    '<i class="fa fa-trash"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';

    $('#detalles').append(fila);
    articuloAdd += idproducto + "-";
    cont++;
    detalles++;
}

function validarCantidadMin(input, min) {
    var val = parseFloat(input.value);
    if (val < min) {
        input.value = min;
        Swal.fire({
            icon: 'warning',
            title: 'Cantidad no permitida',
            text: 'No puedes reducir la cantidad por debajo de ' + min + ' porque ya fue vendido.',
            timer: 2500,
            showConfirmButton: false
        });
    }
}
    
function agregarConEnter(event, idproducto, producto, precioVenta, precioCompra, unidadmedida) {
    if (event.key === 'Enter') {
        var cantidad = document.getElementById('cantidaC_' + idproducto).value;
        agregarDetalle(idproducto, producto, precioVenta, precioCompra, unidadmedida, cantidad);
        mostrarAlerta('Se agregó correctamente al carrito');
        document.getElementById('cantidaC_' + idproducto).value = '';

        // Enfocar el campo de búsqueda de la tabla
        $('#tblarticulos_filter input').focus().val(''); // Enfocar y limpiar el campo de búsqueda
    }
}


function mostrarAlerta(mensaje) {
    toastr.success(mensaje, 'Éxito', {
        timeOut: 300 // Establece el tiempo de duración en milisegundos (en este caso, 3000 ms o 3 segundos)
    });
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

function modificarSubtotales() {
    // Arrays de cantidades, precios y subtotales
    var cant = document.getElementsByName("cantidad[]");
    var prec = document.getElementsByName("precio_compra[]");
    var sub = document.getElementsByName("subtotal");

    var totalProductos = 0;

    // Calcular total de productos
    for (var i = 0; i < cant.length; i++) {
        var inpC = parseFloat(cant[i].value) || 0;
        var inpP = parseFloat(prec[i].value) || 0;
        var inpS = inpC * inpP;
        sub[i].innerHTML = inpS.toFixed(2);
        totalProductos += inpS;
    }

    // --- CÁLCULO SEGÚN TIPO DE IGV ---
    var tipoIGV = $('#tipo_igv').val();
    var monto_exonerado = 0;
    var monto_gravado = 0;
    var monto_igv = 0;
    var total_compra = 0;

    if (tipoIGV === 'GRAVADA') {
        // Si es GRAVADA: el total de productos incluye IGV
        monto_gravado = totalProductos / 1.18;   // Base imponible
        monto_igv = monto_gravado * 0.18;        // IGV 18%
        monto_exonerado = 0;                     // No hay monto exonerado
        total_compra = totalProductos;            // Total con IGV
    } else {
        // Si es EXONERADA: el total de productos NO incluye IGV
        monto_exonerado = totalProductos;         // Todo es exonerado
        monto_gravado = 0;                        // No hay base gravada
        monto_igv = 0;                            // No hay IGV
        total_compra = totalProductos;            // Total sin IGV
    }

    // Mostrar en pantalla
    $("#most_exonerado").html(monto_exonerado.toFixed(2));
    $("#most_gravado").html(monto_gravado.toFixed(2));
    $("#most_imp").html(monto_igv.toFixed(2));
    $("#total").html(total_compra.toFixed(2));

    // Guardar en inputs hidden
    $("#monto_exonerado").val(monto_exonerado.toFixed(2));
    $("#monto_gravado").val(monto_gravado.toFixed(2));
    $("#monto_igv").val(monto_igv.toFixed(2));
    $("#total_compra").val(total_compra.toFixed(2));
    
    // Actualizar el campo impuesto (debe ser 0 si es exonerada)
    $("#impuesto").val(monto_igv.toFixed(2));

    // Actualizar cálculos relacionados
    if ($('#tipopago').val() == 'Si') {
        calcularDeuda();
    }

    actualizarTotales();
    actualizarMontoPagoDefault();
    evaluar();
}

// Evento para recalcular cuando cambie el tipo de IGV
$(document).ready(function() {
    // Establecer EXONERADA por defecto
    $('#tipo_igv').val('EXONERADA');
    
    // Recalcular cuando cambie el tipo de IGV
    $('#tipo_igv').on('change', function() {
        modificarSubtotales();
    });
});

function actualizarTotales() {
    let totalEfectivo = 0;
    let totalDeposito = 0;

    $("input[name='monto_pago[]']").each(function(index){
        let monto = parseFloat($(this).val()) || 0;
        let tipo  = $("select[name='tipo_pago[]']").eq(index).val();

        if (tipo === "Efectivo") {
            totalEfectivo += monto;
        } else {
            // TODO lo que NO sea efectivo es depósito
            totalDeposito += monto;
        }
    });

    $("#totalrecibido").val(totalEfectivo.toFixed(2));
    $("#totaldeposito").val(totalDeposito.toFixed(2));
}

$(document).on("change", "select[name='tipo_pago[]']", function () {
    actualizarTotales();
});


function actualizarMontoPagoDefault() {
    let totalCompra = parseFloat($('#total_compra').val()) || 0;
    let pagos = $("input[name='monto_pago[]']");

    // SOLO si existe un solo pago
    if (pagos.length === 1) {
        pagos.first().val(totalCompra.toFixed(2));
    }

    actualizarTotales();
}


 function limpiarsubtotales(){
 	$("#most_total").html("0");
	$("#most_imp").html("0");
	$("#totalVenta").html("0");
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
					"Se Cancelo la aprobación de la Compra",
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
function eliminarDetalle(indice, idproducto){
	//id fila mas el indice
	$("#fila" + indice).remove();
	modificarSubtotales();
	detalles=detalles-1;

	// Remover el idproducto de la lista de control articuloAdd
    articuloAdd = articuloAdd.replace(idproducto + "-", "");

	evaluar();

	// Llamada AJAX para eliminar el registro de la tabla temporal
    $.post("controladores/compra.php?op=eliminar_tmp", {
        idsucursal: $("#idsucursal").val(),
        idproducto: idproducto
    }, function (r) {
        console.log("Delete TMP OK:", r);
    });
}

function toggleCard() {
  var card = document.getElementById("datosgenerales");
  card.hidden = !card.hidden;
}

var fechaSpan = document.getElementById("fechaActual");

// Obtiene la fecha actual
var fechaActual = new Date();

// Días de la semana en español
var diasSemana = [
  "Domingo",
  "Lunes",
  "Martes",
  "Miércoles",
  "Jueves",
  "Viernes",
  "Sábado",
];

// Meses en español
var meses = [
  "Enero",
  "Febrero",
  "Marzo",
  "Abril",
  "Mayo",
  "Junio",
  "Julio",
  "Agosto",
  "Septiembre",
  "Octubre",
  "Noviembre",
  "Diciembre",
];

// Formatea la fecha según el formato deseado
var formatoFecha =
  diasSemana[fechaActual.getDay()] +
  ", " +
  fechaActual.getDate() +
  " de " +
  meses[fechaActual.getMonth()] +
  " de " +
  fechaActual.getFullYear() +
  ", " +
  (fechaActual.getHours() < 10 ? "0" : "") +
  fechaActual.getHours() +
  ":" +
  (fechaActual.getMinutes() < 10 ? "0" : "") +
  fechaActual.getMinutes();

// Inserta la fecha formateada en el elemento span
fechaSpan.innerHTML = formatoFecha;

function toggleCard2() {
    var cardBody = document.getElementById('');
    if (cardBody.style.display === 'none' || cardBody.style.display === '') {
        cardBody.style.display = 'block';
    } else {
        cardBody.style.display = 'none';
    }
}

window.onload = function() {
    const input = document.getElementById('totalrecibido');
    input.classList.add('animate');

    // Opción: Retirar la animación después de un tiempo
    setTimeout(() => {
        input.classList.remove('animate');
    }, 3000); // 3000 ms = 3 segundos
};

function actualizarDepositoReadonly() {
    document.querySelectorAll('select[name="tipo_pago[]"]').forEach(select => {
        select.addEventListener('change', function() {
            const fila = this.closest('.pago_item');
            const montoInput = fila.querySelector('input[name="monto_pago[]"]');
            
            if(this.value === 'Efectivo'){
                montoInput.readOnly = true;
                montoInput.style.backgroundColor = 'lightcoral';
            } else {
                montoInput.readOnly = false;
                montoInput.style.backgroundColor = '';
            }
        });
    });
}


$('#tipopago').change(function (e) {
    e.preventDefault();
    
    if ($(this).val() == 'Si') {
        // Mostrar campos de crédito
        $('#n0, #n1, #n2, #n3, #n4').show();
        
        // Readonly en campos de depósito
        $('#totaldeposito').attr('readonly', 'readonly');
        $('#noperacion').attr('readonly', 'readonly');
        $('#fecha_deposito').attr('readonly', 'readonly');
        $('#totalrecibido').attr('readonly', 'readonly');
        $('#formapago').attr('readonly', 'readonly');
        
        // Valores por defecto
        $('#formapago').val('Efectivo');
        $('#formapago').select2();
        $('#totaldeposito').val('0');
        $('#noperacion').val('0');
        $('#fecha_deposito').val('');
        
        // ← INICIALIZAR VALORES DE CRÉDITO
        var totalCompra = parseFloat($('#total_compra').val()) || 0;
        $('#montoPagado').val('0');
        $('#montoDeuda').val(totalCompra.toFixed(2));
        $('#fechaOperacion').val(new Date().toISOString().split('T')[0]);
        
    } else {
        // Ocultar campos de crédito
        $('#n0, #n1, #n2, #n3, #n4').hide();
        $('#panel1').hide();
        
        // Quitar readonly
        $('#totaldeposito').removeAttr('readonly');
        $('#totalrecibido').removeAttr('readonly');
        $('#noperacion').removeAttr('readonly');
        $('#fecha_deposito').removeAttr('readonly');
        $('#formapago').removeAttr('readonly');
        
        // Limpiar valores
        $('#montoPagado').val('0');
        $('#montoDeuda').val('0');
        $('#input_cuotas').val('');
        $('#datafechas').html('');
    }
});


$('#montoPagado').on('keyup change', function (e) {
    e.preventDefault();
    calcularDeuda();
});

$("#calcular_cuotas").click(function (e) {
    e.preventDefault();
    
    var cuotas = parseInt($("#input_cuotas").val());
    var fechaOperacion = $("#fechaOperacion").val();
    var montoDeuda = parseFloat($("#montoDeuda").val()) || 0;
    
    // ← VALIDACIONES
    if (!cuotas || cuotas <= 0) {
        Swal.fire('Advertencia', 'Debe seleccionar el número de cuotas', 'warning');
        return;
    }
    
    if (!fechaOperacion) {
        Swal.fire('Advertencia', 'Debe seleccionar la fecha de pago', 'warning');
        return;
    }
    
    if (montoDeuda <= 0) {
        Swal.fire('Advertencia', 'El monto de la deuda debe ser mayor a 0', 'warning');
        return;
    }
    
    var e = new Date(fechaOperacion);
    $('#panel1').show();
    
    var html = "";
    var montoPorCuota = (montoDeuda / cuotas).toFixed(2);
    
    for (let index = 0; index < cuotas; index++) {
        e.setMonth(e.getMonth() + 1);
        
        var fechaFormateada = e.getFullYear() + 
            `-` + ("0" + (e.getMonth() + 1)).slice(-2) + 
            `-` + ("0" + e.getDate()).slice(-2);
        
        html += `
            <tr>
                <td>
                    <input type="date" class="form-control" 
                           name="fecha_pago[]" 
                           value="${fechaFormateada}">
                </td>
                <td>
                    <input type="number" class="form-control" 
                           name="monto_cuota[]" 
                           value="${montoPorCuota}" 
                           step="0.01" readonly>
                </td>
            </tr>`;
    }
    
    $("#datafechas").html(html);
});

$('#formapago').change(function(e){
	if($(this).val() != 'Efectivo'){
		$('#totaldeposito').removeAttr('readonly', 'readonly');
		$('#noperacion').removeAttr('readonly', 'readonly');
		$('#fecha_deposito').removeAttr('readonly', 'readonly');
	}else{
		$('#totaldeposito').attr('readonly', 'readonly');
		$('#noperacion').attr('readonly', 'readonly');
		$('#fecha_deposito').attr('readonly', 'readonly');
		
    $('#totaldeposito').val('0');
		$('#noperacion').val('0');
		$('#fecha_deposito').val('');
	};
})

function addPago() {
    const container = document.getElementById("pagos_container");

    // Obtener TOTAL COMPRA
    const totalCompra = parseFloat($("#total_compra").val()) || 0;

    // Calcular cuánto ya se ha ingresado en pagos
    let totalPagosActuales = 0;
    $("input[name='monto_pago[]']").each(function(){
        totalPagosActuales += parseFloat($(this).val()) || 0;
    });

    // Calcular restante
    let restante = totalCompra - totalPagosActuales;
    if (restante < 0) restante = 0;

    // Crear div contenedor del pago
    const div = document.createElement("div");
    div.className = "row pago_item mt-2";

    div.innerHTML = `
        <div class="col-md-3">
            <label>Tipo pago</label>
            <select name="tipo_pago[]" class="form-control">
                <option value="Efectivo" selected>Efectivo</option>
                <option value="Transferencia">Transferencia</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Deposito">Depósito</option>
                <option value="Yape">Yape</option>
                <option value="Plin">Plin</option>
                <option value="Reposicion">Reposición</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Monto</label>
            <input type="number" class="form-control" name="monto_pago[]" step="0.01" value="${restante.toFixed(2)}">
        </div>
        <div class="col-md-3">
            <label>N° operación</label>
            <input type="text" class="form-control" name="operacion_pago[]" value="">
        </div>
        <div class="col-md-2 d-flex align-items-end" style="gap:5px;">
            <button type="button" class="btn btn-danger btn-sm" onclick="removePago(this)">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    `;

    container.appendChild(div);

    // Reubica el botón + en el último item
    updateAddButton();

    // Actualiza totales
    actualizarTotales();
}

//  Recalcular totales cuando cambia MONTO o TIPO DE PAGO
$(document).on(
    "input change",
    "input[name='monto_pago[]'], select[name='tipo_pago[]']",
    function () {
        actualizarTotales();
    }
);

function updateAddButton() {
    let addBtn = document.getElementById("btn_add_pago");
    if (!addBtn) {
        addBtn = document.createElement("button");
        addBtn.type = "button";
        addBtn.id = "btn_add_pago";
        addBtn.className = "btn btn-success btn-sm"; // mismo tamaño que eliminar
        addBtn.innerHTML = `<i class="fas fa-plus"></i>`; // ícono +
        addBtn.style.transition = "all 0.2s"; // efecto hover
        addBtn.onmouseover = () => addBtn.style.transform = "scale(1.2)";
        addBtn.onmouseout = () => addBtn.style.transform = "scale(1)";
        addBtn.onclick = addPago;
    }

    const container = document.getElementById("pagos_container");
    const pagoItems = container.querySelectorAll(".pago_item");
    if (pagoItems.length === 0) return;

    const lastPago = pagoItems[pagoItems.length - 1];
    const actionCol = lastPago.querySelector("div.col-md-2");
    if (!actionCol.contains(addBtn)) {
        actionCol.appendChild(addBtn);
    }
}

function removePago(btn) {
    const pagoItem = btn.closest(".pago_item");
    pagoItem.remove();
    // Mover el botón "+" al último pago
    updateAddButton();
}

function initPagos() {
    const wrapper = document.getElementById("pagos_wrapper");
    const containerDiv = document.createElement("div");
    containerDiv.id = "pagos_container";
    wrapper.appendChild(containerDiv);

    // Agregar un método de pago por defecto
    addPago();

    // Actualizar el monto del primer pago al subtotal inicial (si ya hay detalles)
    actualizarMontoPagoDefault();
}

// Escucha cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", function() {
    initPagos();

    // Listener para actualizar totales cada vez que se cambie monto o tipo de pago
    $(document).on("input change", "input[name='monto_pago[]'], select[name='tipo_pago[]']", function() {
        actualizarTotales();
    });
});

function calcularDeuda() {
    var montoPagado = parseFloat($('#montoPagado').val()) || 0;
    var totalCompra = parseFloat($('#total_compra').val()) || 0;
    
    var montoDeuda = totalCompra - montoPagado;
    
    // Asegurar que la deuda no sea negativa
    if (montoDeuda < 0) {
        montoDeuda = 0;
        $('#montoPagado').val(totalCompra.toFixed(2));
    }
    
    $('#montoDeuda').val(montoDeuda.toFixed(2));
}

$("#btnExportarExcel").on("click", function () {
    let fecha_inicio = $("#fecha_inicio").val();
    let fecha_fin = $("#fecha_fin").val();
    let idsucursal = $("#idsucursal2").val(); // 👈 CORRECTO

    let url = `controladores/compra.php?op=exportar_excel`
        + `&fecha_inicio=${fecha_inicio}`
        + `&fecha_fin=${fecha_fin}`
        + `&idsucursal=${idsucursal}`;

    window.open(url, '_blank');
});

function mostrarEditar(idcompra)
{
    Swal.fire({
        title: '¿Editar Compra?',
        text: "Se cargarán los datos para edición. Los cambios afectarán el stock.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, editar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // 1. PRIMERO: Limpiar la tabla temporal
            limpiarDetalleTemporal(function() {
                
                // 2. SEGUNDO: Cargar los datos de la compra
                $.post("controladores/compra.php?op=mostrarEditar", {idcompra: idcompra}, function(data, status) {
                    data = JSON.parse(data);
                    
                    // 3. TERCERO: Mostrar formulario SIN cargar detalles automáticamente
                    mostrarFormularioEdicion();
                    
                    // 4. CUARTO: Cargar datos de la cabecera
                    $("#idcompra").val(data.idcompra);
                    $("#idsucursal").val(data.idsucursal).trigger('change');
                    $("#idproveedor").val(data.idproveedor).trigger('change');
                    $("#tipo_comprobante").val(data.tipo_comprobante);
                    $("#serie_comprobante").val(data.serie_comprobante);
                    $("#num_comprobante").val(data.num_comprobante);
                    $("#fecha").val(data.fecha);
                    $("#tipo_igv").val(data.tipo_igv);
                    $("#formapago").val(data.formapago);
                    
                    // 5. QUINTO: Cargar detalles de la compra a la tabla temporal
                    cargarDetallesParaEdicion(data.idcompra, data.idsucursal);
                    
                    // 6. SEXTO: Cambiar el botón
                    $("#btnGuardar").html('<i class="fas fa-sync-alt"></i> Actualizar Compra');
                    $("#btnGuardar").show();
                    
                    Swal.fire(
                        'Modo Edición',
                        'Puedes modificar los precios, cantidades o eliminar productos.',
                        'info'
                    );
                });
            });
        }
    });
}

// Nueva función para mostrar formulario sin cargar detalles automáticamente
function mostrarFormularioEdicion()
{
    $('#body').addClass('sidebar-collapse');
    $("#listadoregistros").hide();
    $("#formularioregistros").show();
    $("#btnagregar").hide();
    $("#btnGuardar").hide();
    $("#btnCancelar").show();
    $("#btnAgregarArt").show();
    $("#btnNuevo").hide();
    $("#header").hide();
    
    // Limpiar variables
    detalles = 0;
    articuloAdd = "";
    cont = 0;
    
    // Limpiar tabla visual
    $('#detalles tbody').empty();
    
    // Inicializar pagos
    $("#pagos_wrapper").html("");
    initPagos();
    
    // Cargar artículos disponibles
    listarArticulos();
}

function cargarDetallesParaEdicion(idcompra, idsucursal)
{
    $.post("controladores/compra.php?op=listarDetalleEdicion", {idcompra: idcompra}, function(data) {
        let detalles = JSON.parse(data);

        if (detalles.length === 0) return;

        let promesas = [];

        detalles.forEach(function(detalle) {

            // Totalmente vendido → bloqueado
            if (parseFloat(detalle.fifo_restante) <= 0) {
                pintarDetalleBloqueado(
                    detalle.idproducto,
                    detalle.nombre_producto,
                    detalle.precio_venta,
                    detalle.precio_compra,
                    detalle.idunidad_medida,
                    detalle.cantidad,
                    detalle.nlote,
                    detalle.fvencimiento
                );
                return;
            }

            // Parcialmente vendido o sin ventas → editable pero con cantidad mínima
            let promesa = $.post("controladores/compra.php?op=agregar_tmp", {
                idsucursal: idsucursal,
                idproducto: detalle.idproducto,
                nombreProducto: detalle.nombre_producto,
                cantidad: detalle.cantidad,
                precio_compra: detalle.precio_compra,
                precio_venta: detalle.precio_venta,
                unidadmedida: detalle.idunidad_medida,
                nlote: detalle.nlote || '',
                fvencimiento: detalle.fvencimiento || ''
            });

            promesas.push({ promesa: promesa, cantidad_vendida: parseFloat(detalle.cantidad_vendida) });
        });

        if (promesas.length === 0) {
            modificarSubtotales();
            actualizarMontoPagoDefault();
            return;
        }

        let soloPromesas = promesas.map(p => p.promesa);

        $.when.apply($, soloPromesas).done(function() {
            setTimeout(function() {
                // Guardar cantidad_vendida por producto para usarla al pintar
                window._cantidadVendida = {};
                detalles.forEach(function(d) {
                    window._cantidadVendida[d.idproducto] = parseFloat(d.cantidad_vendida);
                });
                listarDetalleTmp();
            }, 300);
        });

    }).fail(function(error) {
        Swal.fire('Error', 'No se pudieron cargar los detalles de la compra', 'error');
    });
}

function pintarDetalleBloqueado(idproducto, producto, precioVenta, precioCompra, unidadmedida, cantidad, nlote, fvencimiento) {
    precioCompra = parseFloat(precioCompra);
    precioVenta  = parseFloat(precioVenta);
    var subtotal = parseFloat(cantidad * precioCompra).toFixed(2);
    var productoCompleto = producto + ' x ' + unidadmedida;

    var fila =
        '<tr class="filas table-secondary" id="fila' + cont + '" style="opacity:0.65;">' +
            '<td>' +
                // idproducto vacío → el backend lo ignora al guardar
                '<input type="hidden" name="idproducto[]" value="">' +
                '<input class="form-control form-control-sm" type="text" ' +
                'value="' + productoCompleto + '" readonly ' +
                'style="font-weight:bold; background:#e9ecef; color:#6c757d;">' +
                '<small class="text-danger"><i class="fa fa-lock"></i> Ya vendido — no editable</small>' +
            '</td>' +
            '<td><input class="form-control form-control-sm text-center" style="width:100px;" type="number" value="' + cantidad + '" readonly></td>' +
            '<td><input class="form-control form-control-sm text-center" style="width:100px;" type="number" value="' + precioCompra + '" readonly></td>' +
            '<td><input class="form-control form-control-sm text-center" style="width:100px;" type="number" value="' + precioVenta + '" readonly></td>' +
            '<td><span class="badge bg-secondary" style="width:90px;font-size:15px;">' + subtotal + '</span></td>' +
            '<td><input class="form-control form-control-sm" style="width:90px;" type="text" value="' + (nlote || '') + '" readonly></td>' +
            '<td><input class="form-control form-control-sm" style="width:150px;" type="date" value="' + (fvencimiento || '') + '" readonly></td>' +
            '<td class="text-center"><span class="text-danger"><i class="fa fa-lock"></i></span></td>' +
        '</tr>';

    $('#detalles').append(fila);
    articuloAdd += idproducto + "-"; // Evita que se agregue de nuevo desde el buscador
    cont++;
    detalles++;
}
function limpiarDetalleTemporal(callback)
{
    let idsucursal = $("#idsucursal").val();
    
    // Si no hay sucursal seleccionada, solo ejecutar callback
    if (!idsucursal) {
        if (callback) callback();
        return;
    }
    
    $.post("controladores/compra.php?op=limpiar_tmp", {
        idsucursal: idsucursal
    }, function(r) {
        console.log("Temporal limpiado:", r);
        
        // Limpiar también la tabla visual
        $('#detalles tbody').empty();
        articuloAdd = "";
        cont = 0;
        detalles = 0;
        
        // Ejecutar callback cuando termine
        if (callback) callback();
    }).fail(function(error) {
        console.error("Error al limpiar temporal:", error);
        if (callback) callback(); // Ejecutar callback aunque falle
    });
}

init();