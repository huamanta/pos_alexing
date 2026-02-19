var tabla;

//Función que se ejecuta al inicio
function init(){
	listar();
	listarSaldos();
    enviarRecordatoriosAutomatico();
	$("#body").addClass("sidebar-collapse sidebar-mini");

	$("#getCodeModal").on("submit",function(e)
	{
		guardaryeditar(e);	
	})

	$("#fecha_inicio").change(function (e) {
		e.preventDefault();
		listar();
		listarSaldos();
	});
	$("#fecha_fin").change(function (e) {
		e.preventDefault();
		listar();
		listarSaldos();
	});
	$("#idcliente").change(function (e) {
		e.preventDefault();
		listar();
		listarSaldos();
        toggleBtnEstadoCuenta();
	});
    
    $("#idsucursal2").change(function (e) {
        e.preventDefault();
        listar();
        listarSaldos();
    });

    $('#navCuentasPorCobrar').addClass("treeview active");
    $('#navCuentasPorCobrar').addClass("active");

	//cargamos los items al select almacen
	$.post("controladores/venta.php?op=selectSucursal3", function(r){
		$("#idsucursal2").html(r);
		$('#idsucursal2').select2('');
	});

	//Cargamos los items al select cliente
	$.post("controladores/venta.php?op=selectCliente2", function(r){
		$("#idcliente").html(r);
		$('#idcliente').select2('');
         toggleBtnEstadoCuenta();
	});
	
    $("#btnEstadoCuentaAccion").on("click", function () {
        let idcliente = $("#idcliente").val();
        let fecha_inicio = $("#fecha_inicio").val();
        let fecha_fin = $("#fecha_fin").val();

        if (!idcliente || idcliente === "Todos") {
            alert("Seleccione un cliente válido");
            return;
        }

        verEstadoCuentaCliente(idcliente, fecha_inicio, fecha_fin);
    });
}

function toggleBtnEstadoCuenta() {
    let idcliente = $("#idcliente").val();

    if (idcliente && idcliente !== "Todos") {
        $("#btnEstadoCuenta").show();
    } else {
        $("#btnEstadoCuenta").hide();
    }
}



document.getElementById('formapago').addEventListener('change', function() {
        const montoInput = document.getElementById('montoPagarTarjeta');
        if (this.value !== 'Efectivo') {
            montoInput.removeAttribute('readonly');
        } else {
            montoInput.setAttribute('readonly', true);
            montoInput.value = ''; // Opcional: limpiar el campo al volver a "Efectivo"
        }
    });

function enviarRecordatoriosMasivo() {
    Swal.fire({
        title: '¿Enviar recordatorios a todas las cuotas vencidas?',
        text: 'Se notificará a todos los clientes con cuotas vencidas.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (!result.isConfirmed) return;

        const $btn = $('#btnEnviarRecordatorioSemana');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');

        $.ajax({
            url: 'controladores/cuentascobrar.php?op=enviar_recordatorio',
            method: 'POST',
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(originalHtml);

                if (!res || !res.success) {
                    let errorMsg = res && res.response ? res.response : 'No se pudo completar el envío. Revisa los logs del servidor.';
                    Swal.fire('Error', 'Ocurrió un error: ' + errorMsg, 'error');
                    console.error('Respuesta del API:', res);
                    return;
                }

                let html = `<p>Total recordatorios enviados: <strong>${res.message.split(' ')[0]}</strong></p>`;
                html += `<p>Se enviaron a todas las cuotas vencidas automáticamente.</p>`;
                
                // Mostrar respuesta completa del API (opcional, útil para depuración)
                html += `<pre>Respuesta API: ${JSON.stringify(res.response, null, 2)}</pre>`;

                $('#recordatorioResultadosContenido').html(html);
                $('#modalRecordatorioResultados').modal('show');

                // Recargar tabla si existe
                if (typeof tabla !== 'undefined') tabla.ajax.reload(null, false);
            },
            error: function(xhr, status, err) {
                $btn.prop('disabled', false).html(originalHtml);
                console.error('XHR Error:', xhr.responseText);
                Swal.fire('Error', 'Ocurrió un error durante el envío: ' + err, 'error');
            }
        });
    });
}

$("#btnEnviarRecordatorioSemana").on("click", function(e) {
    e.preventDefault();
    enviarRecordatoriosMasivo();
});

function enviarRecordatoriosAutomatico() {
    $.ajax({
        url: 'controladores/cuentascobrar.php?op=enviar_recordatorio',
        method: 'POST',
        dataType: 'json',
        success: function(res) {
            if (!res || !res.success) return;
            console.log("Recordatorios automáticos enviados:", res.message);
        },
        error: function(xhr, status, err) {
            console.error('Error envío automático:', xhr.responseText);
        }
    });
}

function listar()
{

	var fecha_inicio = $("#fecha_inicio").val();
	var fecha_fin = $("#fecha_fin").val();
	var idcliente = $("#idcliente").val();
	var idsucursal = $("#idsucursal2").val();
	 // Verificar si fecha de inicio es mayor que fecha de fin
    var fechaInicio = new Date(fecha_inicio);
    var fechaFin = new Date(fecha_fin);

	if (fechaInicio > fechaFin) {
        // Establecer fecha de fin en la fecha actual
        var hoy = new Date();
        var dd = String(hoy.getDate()).padStart(2, '0');
        var mm = String(hoy.getMonth() + 1).padStart(2, '0');
        var yyyy = hoy.getFullYear();

        fecha_fin = yyyy + '-' + mm + '-' + dd;
        $("#fecha_fin").val(fecha_fin);
    }

	tabla=$('#tbllistadocuentasxcobrar').dataTable(
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
        buttons: [
			{
                extend: 'pageLength',
                orientation: 'landscape',
                pageSize: 'LEGAL'
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
				title: 'Lista de documentos pendientes por cobrar',
                pageSize: 'LEGAL'
            },
			{
                extend: 'copy',
                orientation: 'landscape',
                pageSize: 'LEGAL'
            },
			{
                extend: 'excel',
                orientation: 'landscape',
				title: 'Lista de documentos pendientes por cobrar',
                pageSize: 'LEGAL'
            }],
		"ajax":
				{
					url: 'controladores/cuentascobrar.php?op=listar',
					data:{fecha_inicio: fecha_inicio,fecha_fin: fecha_fin,idcliente: idcliente,idsucursal: idsucursal},
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}

				},
		"bDestroy": true,
		"iDisplayLength":10,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}


function listarSaldos() {
	var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    var idcliente = $("#idcliente").val();
    var idsucursal = $("#idsucursal2").val();

     // Verificar si fecha de inicio es mayor que fecha de fin
    var fechaInicio = new Date(fecha_inicio);
    var fechaFin = new Date(fecha_fin);

	if (fechaInicio > fechaFin) {
        // Establecer fecha de fin en la fecha actual
        var hoy = new Date();
        var dd = String(hoy.getDate()).padStart(2, '0');
        var mm = String(hoy.getMonth() + 1).padStart(2, '0');
        var yyyy = hoy.getFullYear();

        fecha_fin = yyyy + '-' + mm + '-' + dd;
        $("#fecha_fin").val(fecha_fin);
    }

	$.ajax({
		url: 'controladores/cuentascobrar.php?op=listar_saldos',
        data:{fecha_inicio: fecha_inicio,fecha_fin: fecha_fin,idcliente: idcliente,idsucursal: idsucursal},
        type : "get",
        dataType : "json",
		success : function (data) {
			var saldos = 0
			if (data.abonototal != null && data.deudatotal != null) {
				saldos = parseFloat(data.deudatotal)+parseFloat(data.abonototal);
			}
			$("#saldos").text('S/. '+parseFloat(saldos).toFixed(2));
			// Corrige la evaluación condicional para #abonos
			$("#abonos").text('S/. ' + ((data.abonototal != null) ? parseFloat(data.abonototal).toFixed(2) : '0.00'));

			// Corrige la evaluación condicional para #deudas
			$("#deudas").text('S/. ' + ((data.deudatotal != null) ? parseFloat(data.deudatotal).toFixed(2) : '0.00'));

			if(idcliente != "Todos" && idcliente != null && data.deudatotal != 0 && data.deudatotal != null){
				$('#panel_amortizar').html(`
                    <div class="btn-group">
                        <button class="btn btn-success btn-sm"
                            onclick="amortizarDeuda(${data.deudatotal}, ${idcliente}, '${fecha_inicio}', '${fecha_fin}')">
                             Amortizar
                        </button>

                        
                    </div>
                `);
			}else{
				$('#panel_amortizar').html('<i class="fas fa-money-bill fa-lg" style="font-size: 20px !important"></i>');
			}
		},                        
        error: function(e){
            console.log(e.responseText);    
        }
	});
}

async function amortizarDeuda(deuda, idcliente, fecha_inicio, fecha_fin) {
    // Verificamos la caja abierta
    const idcaja = await verificarCaja();

    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar la amortización', 'error');
        return;
    }

    $('#idcaja').val(idcaja); // Cargamos idcaja en el modal
    $('#modalAmortizar').modal('show');
    $('#montoAdeudadoAmortizar').val(parseFloat(deuda).toFixed(2));
    $('#deudaTotalAmortizar').html(parseFloat(deuda).toFixed(2));
    $('#idcliente_amortizar').val(idcliente);
    $('#fecha_inicio_amortizar').val(fecha_inicio);
    $('#fecha_fin_amortizar').val(fecha_fin);
}


$('#formulario-amortizar').submit(async function(e) {
    e.preventDefault();

    // Verificamos la caja abierta antes de enviar
    const idcaja = await verificarCaja();
    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar la amortización', 'error');
        return;
    }

    var formData = new FormData(this);
    formData.set('idcaja', idcaja); // Aseguramos que idcaja esté en los datos enviados

    $.ajax({
        url: 'controladores/cuentascobrar.php?op=amortizar_deuda',
        data: formData,
        type: "POST",
        contentType: false,
        processData: false,
        success: function(data) {
		    var data = JSON.parse(data);
		    if (data.success) {
		        Swal.fire('Éxito', data.message, 'success'); // ✅ ahora sí sale
		        listar();
		        listarSaldos();
		        $('#modalAmortizar').modal('hide');
		        $('#montoAdeudadoAmortizar').val('');
		        $('#deudaTotalAmortizar').html('');
		        $('#idcliente_amortizar').val('');
		        $('#fecha_inicio_amortizar').val('');
		        $('#fecha_fin_amortizar').val('');
		    } else {
		        Swal.fire('Error', data.message, 'error');
		    }
		},
		        error: function(e) {
            console.log(e.responseText);
        }
    });
});


function verificarCaja() {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "controladores/venta.php?op=verificar_caja",
      type: "get",
      dataType: "json",
      success: function(response) {
        if (response.success) {
          resolve(response.idcaja); // Devuelve el id de la caja abierta
        } else {
          resolve(null); // No hay caja abierta
        }
      },
      error: function(error) {
        reject(error);
      }
    });
  });
}


async function guardaryeditar(e) {
    e.preventDefault();

    const idcaja = await verificarCaja(); // Verifica caja abierta antes de enviar
    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar abonos', 'error');
        return;
    }

    var formData = new FormData($("#formulario")[0]);
    formData.append('idcaja', idcaja); // Asegura idcaja en el formulario

    $.ajax({
        url: "controladores/cuentascobrar.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            let res = JSON.parse(datos);
            if (res.success) {
                Swal.fire('Éxito', res.message, 'success');
                $('#getCodeModal').modal('hide');
                $("#formulario")[0].reset();
                limpiar();
                listar();
                listarSaldos();
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        }
    });
}


function limpiar() {
    // Aquí deberías implementar la lógica para limpiar los campos del formulario
    // Puedes resetear campos, ocultar/mostrar elementos, etc.
    // Por ejemplo, si tienes campos específicos, podrías hacer algo como:
    // $('#campo1').val('');
    // $('#campo2').val('');
}


async function mostrar(idcpc) {

    const idcaja = await verificarCaja(); // Verifica la caja abierta

    if (!idcaja) {
        Swal.fire('Error', 'Debe tener una caja abierta para realizar abonos', 'error');
        return;
    }

    $("#idcaja").val(idcaja); 
    $("#getCodeModal").modal('show');

    // 🔹 1. Actualizar la mora en BD antes de mostrar el formulario
    $.post("controladores/cuentascobrar.php?op=actualizar_mora_diaria", 
    { 
        idcpc: idcpc 
    }, 
    function() {

        // 🔹 2. Obtener datos actualizados
        $.post("controladores/cuentascobrar.php?op=mostrar", 
        { 
            idcpc: idcpc 
        }, 
        function(data) {

            data = JSON.parse(data);
            console.log(data);
            

            $('#documento').text(data.tipo_comprobante + " : " + data.serie_comprobante + " - " + data.num_comprobante);
            $("#deutaTotal").text(data.deuda);
            $("#valorVenta").text(data.total_venta);
            $("#valorInteres").text(data.total_venta * (data.interes/100));
            $("#montoAdeudado").val(data.deudatotal);
            $("#idcpc").val(data.idcpc);
            
            $("#idventa").val(data.idventa);
            $("#fechavencimiento").text(data.fechavencimiento);

        });
    });
}


function mostrarAbonos(idcpc){

	$("#getCodeModal2").modal('show');

	$.post("controladores/cuentascobrar.php?op=mostrar",{idcpc : idcpc}, function(data,status)
	{

		data=JSON.parse(data);

		var label=document.querySelector('#abonoTotal2');
		label.textContent=data.deuda;

		var label=document.querySelector('#abonoTotal');
		label.textContent=data.abonototal;

	});

	tabla=$('#tbllistado').dataTable(
	{
		//"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
		buttons: [		        
		            'excelHtml5',
		            'pdf'
		        ],
		"ajax":
				{
					url: 'controladores/cuentascobrar.php?op=listarDetalle',
					data:{idcpc: idcpc},
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength":10,//Paginación
	}).DataTable();

}

function verEstadoCuenta(idcpc){
  $.get(
    "controladores/cuentascobrar.php?op=estado_cuenta",
    { idcpc: idcpc },
    function(data){
      $("#estadoCuentaContenido").html(data);
      $("#modalEstadoCuenta").modal("show");
    }
  );
}

function verEstadoCuentaCliente(idcliente, fecha_inicio, fecha_fin) {

    $("#estadoCuentaContenido").html(
        "<div class='text-center'><i class='fas fa-spinner fa-spin'></i> Cargando...</div>"
    );

    $("#modalEstadoCuenta").modal("show");

    $.get(
        "controladores/cuentascobrar.php?op=estado_cuenta_cliente",
        {
            idcliente: idcliente,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin
        },
        function (data) {
            $("#estadoCuentaContenido").html(data);
        }
    );
}


init();
