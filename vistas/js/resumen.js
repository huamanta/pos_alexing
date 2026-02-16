var tabla;
var tablaResumenes;

function init() {
    // Cargar sucursales
    $.post("controladores/venta.php?op=selectSucursal", function(r){
        $("#idsucursal").html(r);
        $('#idsucursal').select2();
        listarBoletas(); 
        listarResumenes();
    });

    // Evento para filtrar por sucursal
    $("#idsucursal").on("change", function() {
        listarBoletas();
    });

    // Eventos para filtrar por rango de fechas
    $("#fecha_inicio_busqueda, #fecha_fin_busqueda").on("change", function() {
        listarBoletas();
    });

    // Evento para el checkbox "select-all"
    $("#select-all").on("click", function() {
        $("input[name='idventa[]']").prop("checked", this.checked);
    });

    $("#navVentasActive").addClass("treeview active");
    $("#navVentas").addClass("treeview menu-open");
    $("#navResumen").addClass("active");
}

function listarBoletas() {
    var fecha_inicio = $("#fecha_inicio_busqueda").val();
    var fecha_fin = $("#fecha_fin_busqueda").val();
    var idsucursal = $("#idsucursal").val();

    if (!fecha_inicio || !fecha_fin || !idsucursal) {
        return;
    }

    if (tabla) {
        tabla.destroy();
    }

    tabla = $('#tblboletas').DataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    "processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
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
            url: 'controladores/resumen.php?op=listar_boletas',
            type: "post",
            data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin, idsucursal: idsucursal },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5,
        "order": [[1, "desc"]]
    });

    listarResumenes();
}

function listarResumenes() {
    var fecha_inicio = $("#fecha_inicio_busqueda").val();
    var fecha_fin = $("#fecha_fin_busqueda").val();
    var idsucursal = $("#idsucursal").val();

    if (!fecha_inicio || !fecha_fin || !idsucursal) {
        return;
    }

    if (tablaResumenes) {
        tablaResumenes.destroy();
    }

    tablaResumenes = $('#tblresumenes').DataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    "processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
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
					},
					{
						extend: 'pdf',
						text: "<i class='fas fa-file-pdf'></i>",
					},
					{
						extend: 'colvis',
						text: "<i class='fas fa-bars'></i>",
						titleAttr: '',
					}],
        "ajax": {
            url: 'controladores/resumen.php?op=listar_resumenes',
            type: "post",
            data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin, idsucursal: idsucursal },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5,
        "order": [[0, "desc"]]
    });
}
function generarResumen() {
    var idventas = [];
    $("input[name='idventa[]']:checked").each(function(){
        idventas.push($(this).val());
    });

    if(idventas.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Sin selección',
            text: 'Debe seleccionar al menos una boleta para generar el resumen.'
        });
        return;
    }

    Swal.fire({
        title: '¿Generar Resumen?',
        text: `Se generará un resumen diario con ${idventas.length} boletas. ¿Continuar?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, generar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            
            var fecha_resumen = $("#fecha_busqueda").val();
            var idsucursal = $("#idsucursal").val();

            $.ajax({
                url: 'controladores/resumen.php?op=generar_resumen',
                type: 'POST',
                dataType: 'json',
                data: {
                    idventas: idventas,
                    fecha_resumen: fecha_resumen,
                    idsucursal: idsucursal,
                    idpersonal: idpersonal_session
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Enviando...',
                        text: 'Por favor espere mientras se genera y envía el resumen.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Resumen Enviado',
                            text: 'Ticket de resumen: ' + response.ticket,
                        });
                        listarBoletas(); // Recargar la tabla
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Ocurrió un error al generar el resumen.'
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Comunicación',
                        text: 'No se pudo conectar con el servidor. Por favor, revise la consola para más detalles.'
                    });
                    console.log(jqXHR.responseText);
                }
            });
        }
    });
}

function consultarTicket(ticket, idresumen) {
    Swal.fire({
        title: 'Consultando Ticket...',
        text: 'Por favor espere mientras se consulta el estado del resumen.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        url: 'controladores/resumen.php?op=consultar_ticket',
        type: 'GET',
        dataType: 'json',
        data: {
            ticket: ticket,
            idresumen: idresumen,
            idpersonal: idpersonal_session
        },
        success: function(response) {
            Swal.close();
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Consulta Exitosa',
                    text: response.message,
                });
                listarBoletas();
                listarResumenes();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en la Consulta',
                    text: response.message || 'Ocurrió un error al consultar el ticket.'
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error de Comunicación',
                text: 'No se pudo conectar con el servidor.'
            });
            console.log(jqXHR.responseText);
        }
    });
}

$(document).ready(function() {
    init();
});
