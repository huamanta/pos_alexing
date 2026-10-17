var tabla;
const URL_USUARIO = 'controladores/usuario.php';

let datosPermisosUsuario = null;

function init() {
	$("#body").addClass("sidebar-collapse sidebar-mini");
	listar();
	limpiar();

	$("#myModal").on("submit", function (e) {
		guardaryeditar(e);
	});

	//cargamos los items al select almacen
	$.post("controladores/venta.php?op=selectSucursal2", function (r) {
		$("#idsucursal").html(r);
		$('#idsucursal').select2();
	});

	//Cargamos los items al select categoria
	$.post("controladores/usuario.php?op=selectEmpleado", function (r) {
		$("#idpersonal").html(r);
		$('#idpersonal').select2();
	});

	$(document).on('input', '#buscarPermisos', function () {
		filtrarPermisos();
	});

	$('#seleccionarTodosPermisos').on('click', function () {
		$('#permisos input[name="permisos[]"]').prop('checked', true);
		actualizarEstadosModulos();
	});

	$('#deseleccionarTodosPermisos').on('click', function () {
		$('#permisos input[name="permisos[]"]').prop('checked', false);
		actualizarEstadosModulos();
	});

	$(document).on('change', '.check-modulo-usuario', function () {
		$(this).closest('.modulo-card-usuario')
			.find('.check-permiso-usuario')
			.prop('checked', this.checked);

		actualizarEstadosModulos();
	});

	$(document).on('change', '.check-permiso-usuario', function () {
		actualizarEstadosModulos();
	});

	$('#navPersonalActive').addClass("treeview active");
	$('#navPersonal').addClass("treeview menu-open");
	$('#navUsuario').addClass("active");

}

//Función limpiar
function limpiar() {
	$("#idpersonal").val("");
	$("#login").val("");
	$("#clave").val("");
	$("#idsucursal").val(null).trigger('change');
	$("#idusuario").val("");
	$("#buscarPermisos").val('');
	$("#noPermisosFound").hide();
	datosPermisosUsuario = null;
}

function escapar(valor) {
	return String(valor ?? '').replace(/[&<>"']/g, caracter => ({
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;'
	})[caracter]);
}

function cargarPermisosUsuario(idusuario, onComplete) {
	$('#permisos').html(`
		<div class="text-center text-muted py-4">
			<i class="fas fa-spinner fa-spin mr-2"></i>
			Cargando roles, módulos y permisos...
		</div>
	`);

	$.getJSON(`${URL_USUARIO}?op=permisos&id=${encodeURIComponent(idusuario || 0)}`)
		.done(function (data) {
			datosPermisosUsuario = data;
			pintarPermisosUsuario(data);
			if (typeof onComplete === 'function') {
				onComplete();
			}
		})
		.fail(function (xhr) {
			datosPermisosUsuario = null;
			$('#permisos').html(`
				<div class="alert alert-danger mb-0">
					No se pudieron cargar roles y permisos.
					${escapar(xhr.responseJSON?.message || '')}
				</div>
			`);
		});
}

function pintarPermisosUsuario(data) {
	const seleccionados = data.seleccionados || { roles: [], permisos: [] };
	const rolesSeleccionados = new Set((seleccionados.roles || []).map(Number));
	const permisosSeleccionados = new Set((seleccionados.permisos || []).map(Number));
	const roles = Array.isArray(data.roles) ? data.roles : [];
	const modulos = Array.isArray(data.modulos) ? data.modulos : [];

	const htmlRoles = roles.length
		? roles.map(rol => `
			<div class="col-sm-6 col-md-4 col-lg-3 mb-2 role-search-item"
				data-search="${escapar(rol.nombre)}">
				<div class="custom-control custom-checkbox">
					<input type="checkbox"
						class="custom-control-input check-rol-usuario"
						name="roles[]"
						value="${Number(rol.id)}"
						id="usuario_rol_${Number(rol.id)}"
						${rolesSeleccionados.has(Number(rol.id)) ? 'checked' : ''}>
					<label class="custom-control-label"
						for="usuario_rol_${Number(rol.id)}">
						${escapar(rol.nombre)}
					</label>
				</div>
			</div>
		`).join('')
		: '<div class="col-12 text-muted">No hay roles registrados. Cree un rol para poder asignarlo a este usuario.</div>';

	const htmlModulos = modulos.map(modulo => pintarModuloUsuario(modulo, permisosSeleccionados)).join('');
	$('#permisos').html(`
		<div class="card mb-3 border">
			<div class="card-header py-2 bg-white">
				<div class="d-flex align-items-center">
					<i class="fas fa-user-tag text-primary mr-2"></i>
					<strong>Roles del usuario</strong>
					<span class="badge badge-secondary ml-auto">${roles.length}</span>
				</div>
			</div>
			<div class="card-body py-3">
				<div class="row role-list">${htmlRoles}</div>
			</div>
		</div>
		<div class="card border">
			<div class="card-header py-2 bg-white">
				<div class="d-flex align-items-center">
					<i class="fas fa-key text-primary mr-2"></i>
					<strong>Permisos directos</strong>
					<span class="badge badge-secondary ml-auto">${contarPermisosUsuario(modulos)}</span>
				</div>
			</div>
			<div class="card-body py-2">
				${htmlModulos || '<div class="alert alert-info mb-0">No hay módulos con permisos disponibles.</div>'}
			</div>
		</div>
	`);

	actualizarEstadosModulos();
	filtrarPermisos();
}

function pintarModuloUsuario(modulo, permisosSeleccionados, nivel = 0) {
	const permisos = Array.isArray(modulo.permisos) ? modulo.permisos : [];
	const hijos = Array.isArray(modulo.childrens) ? modulo.childrens : [];
	if (!permisos.length && !hijos.length) {
		return '';
	}

	const moduloId = Number(modulo.id);
	const collapseId = `usuario_modulo_${moduloId}`;
	const icono = modulo.icono
		? `<i class="${escapar(modulo.icono)} mr-2"></i>`
		: '<i class="fas fa-folder mr-2"></i>';

	return `
		<div class="card mb-2 modulo-card-usuario" data-modulo-id="${moduloId}">
			<div class="card-header py-2">
				<div class="d-flex align-items-center">
					<input type="checkbox"
						class="check-modulo-usuario mr-2"
						id="check_usuario_modulo_${moduloId}">
					<button type="button"
						class="btn btn-link text-left flex-grow-1 p-0 font-weight-bold"
						data-toggle="collapse"
						data-target="#${collapseId}"
						aria-expanded="false">
						${icono}${escapar(modulo.nombre)}
					</button>
					<span class="badge badge-secondary">${contarPermisosUsuario([modulo])} permisos</span>
				</div>
			</div>
			<div id="${collapseId}" class="collapse">
				<div class="card-body py-2">
					${pintarListaPermisosUsuario(permisos, modulo.nombre, permisosSeleccionados)}
					${hijos.map(hijo => pintarModuloUsuario(hijo, permisosSeleccionados, nivel + 1)).join('')}
				</div>
			</div>
		</div>
	`;
}

function pintarListaPermisosUsuario(permisos, nombreModulo, permisosSeleccionados) {
	if (!permisos.length) {
		return '';
	}

	return `
		<div class="row pl-3 mb-2">
			${permisos.map(permiso => `
				<div class="col-sm-6 col-md-4 col-lg-3 mb-2 permission-search-item"
					data-search="${escapar(`${nombreModulo} ${permiso.nombre} ${permiso.codigo || ''}`)}">
					<div class="custom-control custom-checkbox">
						<input type="checkbox"
							class="custom-control-input check-permiso-usuario"
							name="permisos[]"
							value="${Number(permiso.id)}"
							id="usuario_permiso_${Number(permiso.id)}"
							${permisosSeleccionados.has(Number(permiso.id)) ? 'checked' : ''}>
						<label class="custom-control-label"
							for="usuario_permiso_${Number(permiso.id)}"
							title="${escapar(permiso.codigo || '')}">
							${escapar(permiso.nombre)}
						</label>
					</div>
				</div>
			`).join('')}
		</div>
	`;
}

function contarPermisosUsuario(modulos) {
	return modulos.reduce((total, modulo) => {
		return total + (modulo.permisos || []).length
			+ contarPermisosUsuario(modulo.childrens || []);
	}, 0);
}

function actualizarEstadosModulos() {
	$('#permisos .modulo-card-usuario').each(function () {
		const permisos = $(this).find('.check-permiso-usuario');
		const marcados = permisos.filter(':checked').length;
		$(this).children('.card-header').find('.check-modulo-usuario')
			.prop('checked', permisos.length > 0 && marcados === permisos.length)
			.prop('indeterminate', marcados > 0 && marcados < permisos.length);
	});
}

function filtrarPermisos() {
	const busqueda = $('#buscarPermisos').val().toLowerCase().trim();
	let encontrados = 0;

	$('#permisos .role-search-item').each(function () {
		const texto = `${$(this).attr('data-search') || ''} ${$(this).text()}`.toLowerCase();
		const visible = busqueda === '' || texto.includes(busqueda);
		$(this).toggle(visible);
		if (visible) {
			encontrados++;
		}
	});

	$('#permisos .permission-search-item').each(function () {
		const texto = `${$(this).attr('data-search') || ''} ${$(this).text()}`.toLowerCase();
		const moduloCoincide = $(this)
			.parents('.modulo-card-usuario')
			.toArray()
			.some(modulo => $(modulo).children('.card-header').text().toLowerCase().includes(busqueda));
		const visible = busqueda === '' || texto.includes(busqueda) || moduloCoincide;
		$(this).toggle(visible);
		if (visible) {
			encontrados++;
		}
	});

	$('#permisos .modulo-card-usuario').each(function () {
		const coincidenciaModulo = busqueda !== ''
			&& $(this).children('.card-header').text().toLowerCase().includes(busqueda);
		const coincidenciaPadre = $(this)
			.parents('.modulo-card-usuario')
			.toArray()
			.some(modulo => $(modulo).children('.card-header').text().toLowerCase().includes(busqueda));
		const tieneCoincidencias = $(this).find('.permission-search-item').filter(function () {
			return this.style.display !== 'none';
		}).length > 0;
		const visible = busqueda === '' || coincidenciaModulo || coincidenciaPadre || tieneCoincidencias;
		$(this).toggle(visible);
		$(this).children('.collapse').toggleClass('show', busqueda !== '' && visible);
	});

	$('#noPermisosFound').toggle(busqueda !== '' && encontrados === 0);
}

//Función cancelarform
function cancelarform() {
	limpiar();
}
function nuevoUsuario() {
	limpiar(); // Limpia todos los campos
	$('#myModal').modal('show');
	$('#n1').hide(); // Oculta el aviso de login en uso
	$('#idusuario').val(""); // Asegura que es nuevo
	$('#idpersonal').val('').trigger('change');
	cargarPermisosUsuario(0);
}

function verificarUsuario(nombre) {
	const idusuario = $("#idusuario").val();
	if (idusuario !== "") return; // No verificar login si estás editando

	$.post("controladores/usuario.php?op=verificarLogin&nombre=" + nombre, function (data) {
		data = JSON.parse(data);

		if (data != null) {
			$('#n1').show();
			$('#login').val("").focus();
		} else {
			$('#n1').hide();
		}
	});
}


function guardaryeditar(e) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "controladores/usuario.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (response) {
			if (!response.success) {
				Swal.fire({
					title: 'Usuario',
					icon: 'error',
					text: response.message
				});
				return;
			}
			Swal.fire({
				title: 'Usuario',
				icon: 'success',
				text: response.message
			});

			$('#myModal').modal('hide');
			tabla.ajax.reload();
			limpiar();
		},
		error: function (error) {
			console.log(error.responseJSON.message || 'Error al guardar usuario');
		}

	});
}

function mostrar(idusuario) {
	$.post(`${URL_USUARIO}?op=mostrar`, { idusuario: idusuario }, function (response) {
		const data = response;

		$('#myModal').modal('show');
		$("#idpersonal").val(data.idpersonal).trigger('change');
		$("#login").val(data.login);
		$("#clave").val('');
		$("#idusuario").val(data.idusuario);

		// Cargar sucursales asignadas
		$.post("controladores/usuario.php?op=listarSucursalesUsuario&idusuario=" + idusuario, function (response) {
			let sucursales = JSON.parse(response);
			let idsSucursales = sucursales.map(item => item.idsucursal);
			$("#idsucursal").val(idsSucursales).trigger('change');
		});
		cargarPermisosUsuario(idusuario);
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
				url: 'controladores/usuario.php?op=listar',
				type: "get",
				dataType: "json",
				error: function (e) {
					console.log(e.responseText);
				}
			},
			"bDestroy": true,
			"iDisplayLength": 5,//Paginación
			"order": [[0, "desc"]]//Ordenar (columna,orden)
		}).DataTable();
}

//Función para desactivar registros
function desactivar(idusuario) {

	Swal.fire({
		title: '¿Desactivar?',
		text: "¿Está seguro Que Desea Desactivar el Usuario?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si'
	}).then((result) => {
		if (result.isConfirmed) {
			$.post("controladores/usuario.php?op=desactivar", { idusuario: idusuario }, function (response) {
				if (!response.success) {
					Swal.fire({
						title: 'Usuario',
						icon: 'error',
						text: response.message
					});
					return;
				}
				Swal.fire({
					title: 'Usuario',
					icon: 'success',
					text: response.message
				});
				tabla.ajax.reload();
			});

		}
	})

}

//Función para desactivar registros
function activar(idusuario) {

	Swal.fire({
		title: 'Activar?',
		text: "¿Está seguro Que Desea Activar el Usuario?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si'
	}).then((result) => {
		if (result.isConfirmed) {
			$.post("controladores/usuario.php?op=activar", { idusuario: idusuario }, function (response) {
				if (!response.success) {
					Swal.fire({
						title: 'Usuario',
						icon: 'error',
						text: response.message
					});
					return;
				}
				Swal.fire({
					title: 'Usuario',
					icon: 'success',
					text: response.message
				});
				tabla.ajax.reload();
			});
		}
	})

}

init();
