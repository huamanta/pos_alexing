var tabla;
let listarProveedores = null;
let GOOGLE_MAPS_API_KEY = null;
let GOOGLE_MAPS_API_URL = null;
let APIS_PERU_API_URL = null;
let APIS_PERU_API_KEY = null;

function initProveedor() {
  $.ajax({
    url: "controladores/persona.php?op=initCliente",
    type: "POST",
    success: function (response) {
      const data = response;
	  GOOGLE_MAPS_API_KEY = data.maps_api_key || null;
	  GOOGLE_MAPS_API_URL = data.maps_api_url || null;
	  APIS_PERU_API_URL = data.apis_peru_api_url || null;
	  APIS_PERU_API_KEY = data.apis_peru_api_key || null;
      const tipo_documento = data.tipo_documento || [];
      $("#idtipo_documento").html(
        tipo_documento.map((item) => `<option value="${item.idtipo_documento}">${item.nombre}</option>`).join(""),
      );
    }
  });
}

//Función que se ejecuta al inicio
function init() {
	initProveedor();
	$("#body").addClass("sidebar-collapse sidebar-mini");
	listarProveedores.load();

	$("#myModal").on("submit", function (e) {
		guardaryeditar(e);
	})

	$('#navComprasActive').addClass("treeview active");
	$('#navCompras').addClass("treeview menu-open");
	$('#navProveedor').addClass("active");

}


listarProveedores = new FluentPaginator({
	url: "controladores/persona.php?op=listarp",
	renderTabla: pintarProveedores,
	tableBody: "#tbodyProveedores"
});

function pintarProveedores(data, permissions) {

	let html = "";

	if (data.length === 0) {
		html = `
      <tr>
        <td colspan="6" class="text-center">No se encontraron registros</td>
      </tr>
    `;
		$("#tbllistado tbody").html(html);
		return;
	}

	data.forEach(item => {

		html += `
            <tr>
                <td>${item.nombre ?? ''}</td>
                <td>${item.tipo_documento ?? ''}</td>
                <td>${item.num_documento ?? ''}</td>
                <td>${item.telefono ?? ''}</td>
                <td>${item.email ?? ''}</td>
                <td>
                  ${permissions.editar ? `<button class="btn btn-warning btn-xs" onclick="mostrar(${item.idpersona})"><i class="fas fa-edit"></i></button>` : ''}
                  ${permissions.historial ? `<button class="btn btn-info btn-xs" onclick="ListarReportesClientes(${item.idpersona})"><i class="fa fa-list"></i></button>` : ''}
                  ${permissions.eliminar ? `<button class="btn btn-danger btn-xs" onclick="eliminar(${item.idpersona})"><i class="fa fa-trash"></i></button>` : ''}
                </td>
            </tr>
        `;

	});


	$("#tbllistado tbody").html(html);
}

//Función limpiar
function limpiar() {
	$("#nombre").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#idpersona").val("");
}

function mostrar(idpersona) {
	$.post("controladores/persona.php?op=mostrar", { idpersona: idpersona }, function (data, status) {
		data = JSON.parse(data);
		$('#myModal').modal('show');

		$("#nombre").val(data.nombre);
		$("#idtipo_documento").val(data.idtipo_documento);
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#idpersona").val(data.idpersona);

	})
}

function guardaryeditar(e) {
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "controladores/persona.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function (datos) {
			const response = JSON.parse(datos);
			if (!response.success) {
				Swal.fire({
					title: 'Proveedor',
					icon: 'error',
					text: response.message
				});
				return;
			}
			Swal.fire({
				title: 'Proveedor',
				icon: 'success',
				text: response.message
			});
			$('#myModal').modal('hide');
			listarProveedores.load();
		}

	});
	limpiar();
}

function BuscarCliente() {

	let numero = $("#num_documento").val();

	$.post("controladores/venta.php?op=selectCliente5&numero=" + numero, function (data, status) {

		data = JSON.parse(data);

		if (data != null) {

			Swal.fire({
				title: '¡Aviso!',
				icon: 'info',
				text: 'El Proveedor ya se encuentra registrado'
			});

			$("#num_documento").val('');

		} else {

			if (parseInt($('#idtipo_documento').val()) === 1) {
				$numero = $("#num_documento").val();
				if ($numero.length < 8) {
					Swal.fire({
						title: 'Falta Números en el DNI',
						icon: 'info',
						text: 'El DNI debe tener 8 Carácteres'
					});
				} else {
					$('#Buscar_Cliente').hide();
					var numdni = $('#num_documento').val();
					var url = `${APIS_PERU_API_URL}/dni/${numdni}?token=${APIS_PERU_API_KEY}`;

					$('#cargando').show();
					$.ajax({
						type: 'GET',
						url: url,
						success: function (dat) {
							if (dat.success == false) {

								Swal.fire({
									title: 'DNI Inválido',
									icon: 'error',
									text: '¡No Existe DNI!'
								});

							} else {
								//$('#nombre').val(dat.success[0]);
								$('#nombre').val(dat.nombres + " " + dat.apellidoPaterno + " " + dat.apellidoMaterno);
								$('#Buscar_Cliente').hide();
								$('#cargando').hide();
							}
						}, complete: function () {

							$('#Buscar_Cliente').show();
							$('#cargando').hide();

						}, error: function () {

						}
					});
				}

			} else {
				$numero = $("#num_documento").val();
				if ($numero.length < 11) {
					Swal.fire({
						title: 'Falta Números en el RUC',
						icon: 'info',
						text: 'El RUC debe tener 11 Carácteres'
					});
				} else {
					$('#Buscar_Cliente').hide();
					var numruc = $('#num_documento').val();
					var url = `${APIS_PERU_API_URL}/ruc/${numruc}?token=${APIS_PERU_API_KEY}`;
					$('#cargando').show();
					$.ajax({
						type: 'GET',
						url: url,
						success: function (dat) {
							console.log(dat);
							if (dat.success == false) {
								Swal.fire({
									title: 'Ruc Inválido',
									icon: 'info',
									text: '¡No Existe RUC!'
								});
							} else {
								$('#nombre').val(dat.razonSocial);
								$('#direccion').val(dat.direccion);
								document.getElementById('estado2').innerHTML = dat.estado;
								document.getElementById('condicion').innerHTML = dat.condicion;
								$('#Buscar_Cliente').hide();
								$('#cargando').hide();
							}
						}, complete: function () {

							$('#Buscar_Cliente').show();
							$('#cargando').hide();

						}, error: function () {

						}
					});
				}
			}


		}

	});

}

//Función eliminar
function eliminar(idpersona) {
	Swal.fire({
		title: "Eliminar?",
		text: "¿Está seguro que desea eliminar el proveedor?",
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Si",
		reverseButtons: true
	}).then((result) => {
		if (result.isConfirmed) {
			$.post(
				"controladores/persona.php?op=eliminar",
				{ idpersona: idpersona },
				function (response) {
					const data = JSON.parse(response);
					if (!data.success) {
						Swal.fire({
							title: "Proveedor",
							icon: "error",
							text: data.message,
						});
						return;
					}
					Swal.fire({
						title: "Proveedor",
						icon: "success",
						text: data.message,
					});
					$("#myModal").modal("hide");
					listarProveedores.load();
				}
			);
		}
	});
}

//Función cancelarform
function cancelarform() {
	limpiar();
}

init();