$(function () {
	const URL_PERFIL = 'controladores/usuario.php';

	function mostrarMensaje(mensaje, tipo) {
		Swal.fire(
			'Perfil',
			mensaje,
			tipo
		);
	}

	function escaparPerfil(valor) {
		return String(valor ?? '').replace(/[&<>"']/g, caracter => ({
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#39;'
		})[caracter]);
	}

	function manejarError(xhr) {
		mostrarMensaje(xhr.responseJSON?.message || 'No se pudieron cargar los módulos.', 'error');
	}

	function listarPerfil() {
		$.getJSON(`${URL_PERFIL}?op=perfil`)
			.done(function (respuesta) {
				const usuario = respuesta.data.usuario;
				$('#perfilNombre').val(usuario.nombre || '');
				$('#perfilTipoDocumento').val(usuario.tipo_documento || '');
				$('#perfilNumeroDocumento').val(usuario.num_documento || '');
				$('#perfilDireccion').val(usuario.direccion || '');
				$('#perfilTelefono').val(usuario.telefono || '');
				$('#perfilEmail').val(usuario.email || '');
				$('#perfilCargo').val(usuario.cargo || '');
				$('#perfilLogin').val(usuario.login || '');

				const roles = respuesta.data.roles || [];
				if (Number(usuario.superusuario) === 1) {
					roles.unshift('Superusuario');
				}
				$('#perfilRoles').html(roles.length
					? roles.map(rol => `<span class="badge badge-info mr-2 mb-2">${escaparPerfil(rol)}</span>`).join('')
					: '<span class="text-muted">Sin roles asignados</span>');
			})
			.fail(manejarError);
	}


	$('#formPerfil').on('submit', function (evento) {
		evento.preventDefault();
		const boton = $('#btnGuardarPerfil').prop('disabled', true);
		$.ajax({
			url: `${URL_PERFIL}?op=actualizarPerfil`,
			method: 'POST',
			data: $(this).serialize(),
			dataType: 'json'
		})
			.done(function (respuesta) {
				mostrarMensaje(respuesta.message, 'success');
				listarPerfil();
			})
			.fail(manejarError)
			.always(function () {
				boton.prop('disabled', false);
			});
	});

	$('#formClave').on('submit', function (evento) {
		evento.preventDefault();
		if ($('#claveNueva').val() !== $('#confirmarClave').val()) {
			mostrarMensaje('La confirmación no coincide con la nueva contraseña.', 'danger');
			return;
		}

		const boton = $('#btnCambiarClave').prop('disabled', true);
		$.ajax({
			url: `${URL_PERFIL}?op=cambiarClave`,
			method: 'POST',
			data: $(this).serialize(),
			dataType: 'json'
		})
			.done(function (respuesta) {
				$('#formClave')[0].reset();
				mostrarMensaje(respuesta.message, 'success');
			})
			.fail(manejarError)
			.always(function () {
				boton.prop('disabled', false);
			});
	});

	listarPerfil();
});
