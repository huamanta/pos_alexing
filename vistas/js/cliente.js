var tabla;

//Función que se ejecuta al inicio
function init(){
    limpiar();
	listar();
 $("#body").addClass("sidebar-collapse sidebar-mini");
	$("#myModal").on("submit",function(e)
	{
		guardaryeditar(e);	
	})

    $('#navVentasActive').addClass("treeview active");
    $('#navVentas').addClass("treeview menu-open");
    $('#navCliente').addClass("active");

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
	$( "#proveedor" ).prop( "checked", false );
}

function mostrar(idpersona)
{
    limpiar();
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
 		if(data.isproveedor == 1){
			$( "#proveedor" ).prop( "checked", true );
		}else{
			$( "#proveedor" ).prop( "checked", false );
		}

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
				  title: 'Cliente',
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

    $.post("controladores/venta.php?op=selectCliente3&numero="+numero,function(data, status){

		data=JSON.parse(data);

		if(data != null){

            Swal.fire({
                title: '¡Aviso!',
                icon: 'info',
                  text:'El Cliente ya se encuentra registrado'
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
					url: 'controladores/persona.php?op=listarc',
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

//Función para desactivar registros
function eliminar(idpersona)
{

	Swal.fire({
		title: 'Eliminar?',
		text: "¿Está seguro Que Desea Eliminar el Cliente?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si'
		}).then((result) => {
		if (result.isConfirmed) {
			$.post("controladores/persona.php?op=eliminar", {idpersona : idpersona}, function(e){

				if(e == 2){
					Swal.fire(
						'!!! Alerta !!!',
						'Cliente asociado a una Operación',
						'error'
						)
				}else if(e == 1){
					Swal.fire(
						'!!! Eliminado !!!',
						'Cliente Eliminado',
						'success'
						)
				}else{
					Swal.fire(
						'!!! Eliminado !!!',
						'Cliente Eliminado',
						'success'
						)
				}

				
				tabla.ajax.reload();
			});
		}else{
			Swal.fire(
				'Aviso!',
				"Se Cancelo la eliminación del Cliente",
				'info'
				)
		}
		})
		
}

// Función para cerrar el modal
    function cerrarModal() {
        $('#listarReporteCliente').modal('hide');
    }


function imprimir() {
    // Obtener el contenido de las tablas generadas
    var data_compras = $('#data_compras').html();
    var data_cuentas_pagar = $('#data_cuentas_pagar').html();
    var data_proveedor = $('#data_proveedor').html();
    var data_proveedor_pagar = $('#data_proveedor_pagar').html();

    // Obtener las fechas de inicio y fin seleccionadas
    var fecha_inicio = $('#fecha_inicio').val();
    var fecha_fin = $('#fecha_fin').val();

    // Crear un contenido HTML para la impresión
    var contenido = `
        <html>
        <head>
            <title>Reporte de Clientes</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 40px; 
                    color: #333; 
                    background-color: #f4f4f4; 
                }
                h2, h3 { 
                    text-align: center; 
                    color: #333; 
                    font-weight: bold; /* Encabezados en negrita */
                }
                p { 
                    font-size: 14px; 
                    margin: 10px 0; 
                }
                .table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-bottom: 30px; 
                }
                .table, .table th, .table td { 
                    border: 1px solid #ddd; 
                }
                .table th, .table td { 
                    padding: 6px 10px; /* Ajuste del padding para mejor ajuste al contenido */
                    text-align: left; 
                    font-size: 14px; 
                }
                .table th {  
                    font-weight: bold; /* Encabezados en negrita */
                }
                .table tbody tr:nth-child(even) { 
                    background-color: #f9f9f9; 
                }
                .table tbody tr:nth-child(odd) { 
                    background-color: #ffffff; 
                }
                .table td { 
                    color: #555; 
                }
                .table td, .table th { 
                    font-size: 10px; 
                }
                .section-title {
                    text-align: center;
                    font-size: 18px;
                    color: #2e6da4;
                    margin-top: 30px;
                }
            </style>
        </head>
        <body>
            <h2>Reporte de Historial de Compras y Cuentas</h2>
            <p><strong>Fecha de Inicio:</strong> ${fecha_inicio}</p>
            <p><strong>Fecha de Fin:</strong> ${fecha_fin}</p>

            <div class="section-title">
                <h3 style="color: green;">Historial de Compras y Cuentas por Cobrar como Cliente</h3>
            </div>
            <table class="table">
                ${data_compras}
                ${data_cuentas_pagar}
            </table>

            <div class="section-title">
                <h3 style="color: red;">Historial de Compras y Cuentas por Pagar como Proveedor</h3>
            </div>
            <table class="table">
                ${data_proveedor}
                ${data_proveedor_pagar}
            </table>
        </body>
        </html>
    `;

    // Crear una ventana para la impresión
    var ventana = window.open('', '', 'width=800,height=600');
    ventana.document.write(contenido);
    ventana.document.close();

    // Esperar que el contenido se cargue y luego ejecutar la impresión
    ventana.onload = function() {
        ventana.print();
        ventana.close();
    };
}




$("#fecha_inicio").change(function() {
	var clientes01 =  $('#clientesreporte').val()
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    ListarReportesClientes(clientes01, fecha_inicio, fecha_fin);
});

$("#fecha_fin").change(function() {
	var clientes01 =  $('#clientesreporte').val()
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    ListarReportesClientes(clientes01, fecha_inicio, fecha_fin);
});


function ListarReportesClientes(idcliente) {
	 $('#data_compras').html('');  
    $('#data_cuentas_pagar').html('');  
    $('#data_proveedor').html(''); 
    $('#data_proveedor_pagar').html(''); 
    $('#clientesreporte').val(idcliente)
	var fecha_inicio = $("#fecha_inicio").val();
  	var fecha_fin = $("#fecha_fin").val();

	$('#listarReporteCliente').modal('show');
	$.ajax({
		url: "controladores/venta.php?op=listarhistorialcliente&idcliente=" + idcliente + "&fecha_inicio=" + fecha_inicio + "&fecha_fin=" + fecha_fin,
	    type: "GET",
	    contentType: false,
	    processData: false,
	    success: function(datos) {                    
	    	console.log(datos);
	       var data = JSON.parse(datos);

	       // Tabla de Compras
	       var ventas = data.ventas;
			var total = 0;
			var pagado = 0;
			var interes = 0;
			var html = `
			<table class="table table-bordered table-striped table-hover table-sm">
			  <thead>
			    <tr>
			      <th>Fecha</th>
			      <th>Recibo</th>
			      <th>Detalle</th>
			      <th>Importe</th>
			      <th>Interes</th>
			      <th>Total</th>
			      <th>Mes</th>
			    </tr>
			  </thead>
			  <tbody>`;
			
			$.each(ventas, function (i, item) {
				total += parseFloat(ventas[i].total_venta);
				interes += ventas[i].interes;
				pagado += parseFloat(ventas[i].totalrecibido);
				html += `<tr>
					<td>`+ventas[i].fecha_hora+`</td>
					<td>`+ventas[i].serie_comprobante+`</td>
					<td></td>
					<td>`+ventas[i].totalrecibido+`</td>
					<td>`+ventas[i].interes+`</td>
					<td>`+ventas[i].total_venta+`</td>
					<td>`+ventas[i].meses+`</td>
				</tr>`;

				var detalle = ventas[i].detalle;
				html += `<tr>
					<td colspan="2" ></td>
					<td style="font-weight:bold !important">Producto</td>
					<td style="font-weight:bold !important">Cantidad</td>
					<td style="font-weight:bold !important">Precio</td>
				</tr>`;
				
				$.each(detalle, function (a, item) {
					html += `<tr>
						<td colspan="2"></td>
						<td>`+detalle[a].nombre_producto+`</td>
						<td>`+detalle[a].cantidad+`</td>
						<td>`+detalle[a].precio_venta+`</td>
					</tr>`;
				});
			});

			html += `<tr>
				<td colspan="2"></td>
				<td style="color: blue">Total</td>
				<td style="color: blue">`+pagado+`</td>
				<td style="color: blue">`+interes+`</td>
				<td style="color: red">`+total+`</td>
				<td></td>
			</tr>
			  </tbody>
			</table>`;
			$('#data_compras').html(html);

			// Tabla de Cuentas por Cobrar
			var cuentasxcobrar = data.cuentasxcobrar;
			var totalc = 0;
			var interesc = 0;
			var recibidoc = 0;
			var htmlform = `
			<table class="table table-bordered table-striped table-hover table-sm">
			  <thead>
			    <tr>
			      <th>Fecha</th>
			      <th>Tipo</th>
			      <th>Deuda Total</th>
			      <th>Interes</th>
			      <th>Abono Total</th>
			      <th>Monto Pagado</th>
			    </tr>
			  </thead>
			  <tbody>`;
			
			$.each(cuentasxcobrar, function (i, item) {
				totalc += parseFloat(cuentasxcobrar[i].deudatotal);
				interesc += parseFloat(cuentasxcobrar[i].interes);
				htmlform += `<tr>
					<td>`+cuentasxcobrar[i].fecha_hora+`</td>
					<td>`+cuentasxcobrar[i].tipo+`</td>
					<td>`+Number(cuentasxcobrar[i].deudatotal).toFixed(2)+`</td>
					<td>`+Number(cuentasxcobrar[i].interes).toFixed(2)+`</td>
					<td>`+Number(cuentasxcobrar[i].abonototal).toFixed(2)+`</td>
					<td>0</td>
				</tr>`;

				var detallecuentasxcobrar = cuentasxcobrar[i].detalle;
				$.each(detallecuentasxcobrar, function (a, item) {
					recibidoc += parseFloat(detallecuentasxcobrar[a].montopagado);
					htmlform += `<tr>
						<td colspan="2"></td>
						<td>`+detallecuentasxcobrar[a].tipo+`</td>
						<td></td>
						<td></td>
						<td>`+detallecuentasxcobrar[a].montopagado+`</td>
					</tr>`;
				});
			});

			htmlform += `<tr>
				<td colspan="2"></td>
				<td style="color: blue">Total</td>
				<td style="color: blue">`+recibidoc.toFixed(2)+`</td>
				<td style="color: blue">`+interesc.toFixed(2)+`</td>
				<td style="color: red">`+totalc.toFixed(2)+`</td>
			</tr>
			  </tbody>
			</table>`;
			$('#data_cuentas_pagar').html(htmlform);

			// Tabla de Proveedores
			var ventas = data.compras;
			var total = 0;
			var pagado = 0;
			var interes = 0;
			var html = `
			<table class="table table-bordered  table-sm">
			  <thead>
			    <tr>
			      <th>Fecha</th>
			      <th>Recibo</th>
			      <th>Detalle</th>
			      <th>Importe</th>
			      <th>Interes</th>
			      <th>Total</th>
			      <th>Mes</th>
			    </tr>
			  </thead>
			  <tbody>`;
			
			$.each(ventas, function (i, item) {
				total += parseFloat(ventas[i].total_venta);
				interes += ventas[i].interes;
				pagado += parseFloat(ventas[i].totalrecibido);
				html += `<tr>
					<td>`+ventas[i].fecha_hora+`</td>
					<td>`+ventas[i].serie_comprobante+`</td>
					<td></td>
					<td>`+ventas[i].totalrecibido+`</td>
					<td>`+ventas[i].interes+`</td>
					<td>`+ventas[i].total_venta+`</td>
					<td>`+ventas[i].meses+`</td>
				</tr>`;

				var detalle = ventas[i].detalle;
				html += `<tr>
					<td colspan="2"></td>
					<td style="font-weight:bold !important">Producto</td>
					<td style="font-weight:bold !important">Cantidad</td>
					<td style="font-weight:bold !important">Precio</td>
				</tr>`;
				
				$.each(detalle, function (a, item) {
					html += `<tr>
						<td colspan="2"></td>
						<td>`+detalle[a].nombre_producto+`</td>
						<td>`+detalle[a].cantidad+`</td>
						<td>`+detalle[a].precio_venta+`</td>
					</tr>`;
				});
			});

			html += `<tr>
				<td colspan="2"></td>
				<td style="color: blue">Total</td>
				<td style="color: blue">`+pagado+`</td>
				<td style="color: blue">`+interes+`</td>
				<td style="color: red">`+total+`</td>
				<td></td>
			</tr>
			  </tbody>
			</table>`;
			$('#data_proveedor').html(html);

			// Tabla de Cuentas por Pagar
			var cuentasxcobrar = data.cuentasxpagar;
			var totalc = 0;
			var interesc = 0;
			var recibidoc = 0;
			var htmlform = `
			<table class="table table-bordered table-striped table-hover table-sm">
			  <thead>
			    <tr>
			      <th>Fecha</th>
			      <th>Tipo</th>
			      <th>Deuda Total</th>
			      <th>Interes</th>
			      <th>Abono Total</th>
			      <th>Monto Pagado</th>
			    </tr>
			  </thead>
			  <tbody>`;
			
			$.each(cuentasxcobrar, function (i, item) {
				totalc += parseFloat(cuentasxcobrar[i].deudatotal);
				interesc += parseFloat(cuentasxcobrar[i].interes);
				htmlform += `<tr>
					<td>`+cuentasxcobrar[i].fecha_hora+`</td>
					<td>`+cuentasxcobrar[i].tipo+`</td>
					<td>`+Number(cuentasxcobrar[i].deudatotal).toFixed(2)+`</td>
					<td>`+Number(cuentasxcobrar[i].interes).toFixed(2)+`</td>
					<td>`+Number(cuentasxcobrar[i].abonototal).toFixed(2)+`</td>
					<td>0</td>
				</tr>`;

				var detallecuentasxcobrar = cuentasxcobrar[i].detalle;
				$.each(detallecuentasxcobrar, function (a, item) {
					recibidoc += parseFloat(detallecuentasxcobrar[a].montopagado);
					htmlform += `<tr>
						<td colspan="2"></td>
						<td>`+detallecuentasxcobrar[a].tipo+`</td>
						<td></td>
						<td></td>
						<td>`+detallecuentasxcobrar[a].montopagado+`</td>
					</tr>`;
				});
			});

			htmlform += `<tr>
				<td colspan="2"></td>
				<td style="color: blue">Total</td>
				<td style="color: blue">`+recibidoc.toFixed(2)+`</td>
				<td style="color: blue">`+interesc.toFixed(2)+`</td>
				<td style="color: red">`+totalc.toFixed(2)+`</td>
			</tr>
			  </tbody>
			</table>`;
			$('#data_proveedor_pagar').html(htmlform);
	    }

	});
}


init();