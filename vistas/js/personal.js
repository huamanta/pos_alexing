var tabla;
let archivosSeleccionados = [];
let archivosEliminados = [];
var calendar;
let idpersonalGlobal = null;

function init() {
	$("#body").addClass("sidebar-collapse sidebar-mini");
	listar();
	limpiar();

	$("#myModal").on("submit", function (e) {
		guardaryeditar(e);
	});

	$('#navPersonalActive').addClass("treeview active");
	$('#navPersonal').addClass("treeview menu-open");
	$('#navPersonalI').addClass("active");

	$("#imagenmuestra").show();
	$("#imagenmuestra").attr("src", "files/personal/user.png");
	$("#imagenactual").val("user.png");

}

//Función limpiar
function limpiar() {
	$("#nombre").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#cargo").val("Administrador");
	$("#imagenmuestra").attr("src", "files//personal/user.png");
	$("#imagenactual").val("user.png");
	$("#imagen").val("");
	$("#idpersonal").val("");
}

//Función cancelarform
function cancelarform() {
	limpiar();
}

function guardaryeditar(e) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "controladores/empleado.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (response) {
			const data = JSON.parse(response);
			if (!data.success) {
				Swal.fire({
					title: "Pesonal",
					icon: "error",
					text: data.message,
				});
				return;
			}
			Swal.fire({
				title: "Pesonal",
				icon: "success",
				text: data.message,
			});
			$('#myModal').modal('hide');
			limpiar();
			tabla.ajax.reload();
		}

	});
}

function mostrar(idpersonal) {
	$.post("controladores/empleado.php?op=mostrar", { idpersonal: idpersonal }, function (data, status) {
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
		$("#imagenmuestra").attr("src", "files/personal/" + data.imagen);
		$("#imagenactual").val(data.imagen);
		$("#idpersonal").val(data.idpersonal);

	});
}

//Función Listar
function listar() {
	tabla = $('#tbllistado').dataTable(
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
				[5, 10, 25, 50, 100, -1],
				['5 filas', '10 filas', '25 filas', '50 filas', '100 filas', 'Mostrar todo']
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
				type: "get",
				dataType: "json",
				error: function (e) {
					console.log(e.responseText);
				}
			},
			"bDestroy": true,
			"iDisplayLength": 5,//Paginación
		}).DataTable();
}

//Función para desactivar registros
function desactivar(idpersonal) {

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
			$.post("controladores/empleado.php?op=desactivar", { idpersonal: idpersonal }, function (e) {
				Swal.fire(
					'Desactivado!',
					e,
					'success'
				)
				tabla.ajax.reload();
			});
		} else {
			Swal.fire(
				'Aviso!',
				"Se Cancelo la desactivacion de el Personal",
				'info'
			)
		}
	})

}

//Función para desactivar registros
function activar(idpersonal) {

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
			$.post("controladores/empleado.php?op=activar", { idpersonal: idpersonal }, function (e) {
				Swal.fire(
					'Activado!',
					e,
					'success'
				)
				tabla.ajax.reload();
			});
		} else {
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

$("#imagen").change(function () {

	var imagen = this.files[0];

	/*=============================================
	  VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
	  =============================================*/

	/*
	if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {

		$(".nuevaImagen").val("");

		swal({
			title: "Error al subir la imagen",
			text: "¡La imagen debe estar en formato JPG o PNG!",
			type: "error",
			confirmButtonText: "¡Cerrar!"
		});

	} else */

	if (imagen["size"] > 2000000) {

		$(".nuevaImagen").val("");

		Swal.fire({
			title: "Error al subir la imagen",
			text: "¡La imagen no debe pesar más de 2MB!",
			type: "error",
			confirmButtonText: "¡Cerrar!"
		});

	} else {

		var datosImagen = new FileReader;
		datosImagen.readAsDataURL(imagen);

		$(datosImagen).on("load", function (event) {

			var rutaImagen = event.target.result;

			$("#imagenmuestra").attr("src", rutaImagen);

		})

	}
})

function listarEventos(idpersonal, only_personal) {
	var calendarEl = document.getElementById('calendar');

	let condicion = '';
	if (idpersonal && idpersonal != undefined) {
		condicion = '&idpersonal=' + idpersonal
	}
	calendar = new FullCalendar.Calendar(calendarEl, {

		locale: 'es',

		initialView: 'dayGridMonth',

		navLinks: true,

		headerToolbar: {
			left: 'prev,next today',
			center: 'title',
			right: 'dayGridMonth,timeGridWeek,timeGridDay'
		},

		events: 'controladores/empleado.php?op=eventosCalendario' + condicion,

		// Clic en un día vacío
		dateClick: function (info) {
			$("#modalProgramarVisita").modal("show");
			$("#formProgramarVisita")[0].reset();
			archivosSeleccionados = [];
			$("#fecha_programada").val(info.dateStr + "T08:00");
			listarDatosExtra(idpersonal, only_personal);
		},

		// Clic en un evento
		eventClick: function (info) {

			let e = info.event;

			Swal.fire({
				title: e.title,
				icon: 'info',
				showCancelButton: true,
				showDenyButton: true,
				confirmButtonText: '<i class="fa fa-eye"></i> Ver',
				denyButtonText: '<i class="fa fa-edit"></i> Editar',
				cancelButtonText: '<i class="fa fa-trash"></i> Eliminar',
				confirmButtonColor: '#17a2b8',
				denyButtonColor: '#ffc107',
				cancelButtonColor: '#dc3545'

			}).then((result) => {
				if (result.isConfirmed) {
					$.post(
						"controladores/cuentascobrar.php?op=mostrarSeguimiento",
						{ idseguimiento: e.extendedProps.idseguimiento },
						function (response) {
							verSeguimiento(response);
						}
					);
				}
				else if (result.isDenied) {
					editarSeguimiento(
						e.extendedProps, only_personal
					);
				}
				else if (result.dismiss === Swal.DismissReason.cancel) {
					eliminarSeguimiento(
						e.extendedProps.idseguimiento, only_personal
					);
				}

			});

		},

		// Clic en el número del día
		navLinkDayClick: function (date) {

			calendar.changeView(
				'timeGridDay',
				date
			);

		}

	});

	setTimeout(function () {

		calendar.render();

	}, 300);
}


function verEventos(idpersonal, only_personal) {
	$("#myModalEventos").modal('show');
	idpersonalGlobal = idpersonal;
	listarEventos(idpersonal, only_personal);
}


function listarDatosExtra(idpersonal, only_personal) {
	$.post("controladores/usuario.php?op=selectEmpleado", { only_personal: only_personal, idpersonal: idpersonal }, function (r) {
		$("#idpersonal_edit").html(r);
		$("#idpersonal_edit").select2();
		if (idpersonal) {
			$("#idpersonal_edit")
				.val(idpersonal)
				.trigger("change");
		}
	});
}

function editarSeguimiento(data, only_personal) {
	listarDatosExtra(data.idpersonal, only_personal);
	$("#id_visita").val(data.idseguimiento);
	$("#idcpc_visita").val(data.idcpc);
	$("#idventa_visita").val(data.idventa);
	$("#idcliente_visita").val(data.idcliente);
	$("#tipo_visita").val(data.tipo);
	$("#prioridad").val(data.prioridad);
	$("#estado").val(data.estado);
	$("#fecha_programada").val(data.fecha_proxima);
	$("#fecha_final").val(data.fecha_final);
	$("#direccion_edit").val(data.direccion);
	$("#descripcion").val(data.descripcion);
	$("#modalProgramarVisita").modal("show");
	if (data.archivos) {
		if (typeof data.archivos === 'string') {
			archivosSeleccionados = JSON.parse(data.archivos);
		} else {
			archivosSeleccionados = data.archivos;
		}

	}
	setTimeout(function () {
		renderArchivos();
	}, 300);

}

function eliminarSeguimiento(idseguimiento, only_personal) {

	Swal.fire({
		title: '¿Eliminar seguimiento?',
		text: 'Esta acción no se puede deshacer',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Sí, eliminar'
	}).then((r) => {
		if (r.isConfirmed) {
			$.ajax({
				url: `controladores/cuentascobrar.php?op=eliminarSeguimiento&idseguimiento=${idseguimiento}`,
				method: 'GET',
				success: function (res) {
					var data = JSON.parse(res);
					if (!data.status) {
						Swal.fire('Error', data.msg, 'error');
						return;
					}
					Swal.fire('Hecho', data.msg, 'success');
					listarEventos(idpersonalGlobal, only_personal);
				}
			})
		}

	});

}


$("#formProgramarVisita").submit(function (e) {

	e.preventDefault();

	var formData = new FormData(this);

	archivosSeleccionados.forEach(function (file) {
		formData.append('adjuntos[]', file);
	});

	archivosEliminados.forEach(function (file) {
		formData.append('archivos_eliminados[]', file);
	});

	$.ajax({
		url: "controladores/cuentascobrar.php?op=guardarVisita",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (r) {

			const data = JSON.parse(r);
			console.log(data);
			
			if (!data.success) {
				Swal.fire('Error', data.message, 'error');
				return;
			}

			$("#formProgramarVisita")[0].reset();

			Swal.fire('Hecho', data.message, 'success');

			$("#modalProgramarVisita").modal("hide");

			listarEventos(idpersonalGlobal, 0);
		}
	});

});


$("#adjuntos").on("change", function (e) {

	let nuevosArchivos = Array.from(e.target.files);

	nuevosArchivos.forEach(file => {

		archivosSeleccionados.push(file);

	});

	renderArchivos();

	$("#adjuntos").val("");
});

function renderArchivos() {

	$("#previewArchivos").html("");

	archivosSeleccionados.forEach((archivo, index) => {

		let nombre = '';
		let size = '';
		let icono = "fa-file";

		// Archivo nuevo (File)
		if (archivo instanceof File) {

			nombre = archivo.name;
			size = (archivo.size / 1024 / 1024).toFixed(2) + ' MB';

		} else {

			nombre = archivo.nombre_original || archivo.archivo;
			size = 'Archivo guardado';

		}

		if (nombre.match(/\.(jpg|jpeg|png|webp)$/i)) {
			icono = "fa-file-image text-primary";
		}
		else if (nombre.match(/\.(pdf)$/i)) {
			icono = "fa-file-pdf text-danger";
		}
		else if (nombre.match(/\.(doc|docx)$/i)) {
			icono = "fa-file-word text-info";
		}
		else if (nombre.match(/\.(xls|xlsx)$/i)) {
			icono = "fa-file-excel text-success";
		}
		else if (nombre.match(/\.(mp4)$/i)) {
			icono = "fa-file-video text-warning";
		}

		$("#previewArchivos").append(`
            <div class="col-md-12 archivo-item-${index}">
                <div class="preview-item">

                    <div class="preview-left">

                        <i class="fas ${icono}"></i>

                        <div>

                            <div style="font-size:13px;font-weight:600;">
                                ${nombre}
                            </div>

                            <small class="text-muted">
                                ${size}
                            </small>

                        </div>

                    </div>

                    <div>

                        ${archivo.archivo ? `
                            <a
                                href="files/seguimientos/${archivo.archivo}"
                                target="_blank"
                                class="btn btn-sm btn-info mr-1">
                                <i class="fa fa-eye"></i>
                            </a>
                        ` : ''}

                        <button
                            type="button"
                            class="btn-delete-file"
                            onclick="eliminarArchivo(${index})">

                            <i class="fas fa-times"></i>

                        </button>

                    </div>

                </div>

            </div>
        `);

	});

}

function eliminarArchivo(index) {

	let archivo = archivosSeleccionados[index];

	if (archivo.idadjunto) {
		archivosEliminados.push(archivo.idadjunto);
	}

	archivosSeleccionados.splice(index, 1);

	renderArchivos();
}

function verSeguimiento(data) {
	$("#ver_cliente").html(data.cliente || '-');
	$("#ver_tipo").html(data.tipo || '-');
	$("#ver_estado").html(data.estado || '-');
	$("#ver_responsable").html(data.personal || '-');
	$("#ver_prioridad").html(data.prioridad || '-');

	$("#ver_cuota").html(
		data.numero_cuota
			? 'Cuota ' + data.numero_cuota + ' del comp.: ' + data.serie_comprobante + '-' + data.numero_comprobante
			: '-'
	);

	$("#ver_fecha_programada").html(data.fecha_proxima || '-');
	$("#ver_fecha_final").html(data.fecha_final || '-');
	$("#ver_direccion").html(data.direccion || '-');
	$("#ver_descripcion").html(data.descripcion || '-');

	let htmlAdjuntos = '';
	var adjuntos = data.adjuntos;
	if (adjuntos && adjuntos.length > 0) {

		adjuntos.forEach(function (item) {

			htmlAdjuntos += `
                <a href="files/seguimientos/${item.archivo}"
                   target="_blank"
                   class="btn btn-outline-primary btn-sm m-1">

                    <i class="fa fa-paperclip"></i>
                    ${item.nombre_original}

                </a>
            `;
		});
	} else {
		htmlAdjuntos = `
            <div class="text-muted">
                No existen adjuntos
            </div>
        `;
	}

	$("#ver_adjuntos").html(htmlAdjuntos);

	$("#modalVerSeguimiento").modal("show");
}

init();

