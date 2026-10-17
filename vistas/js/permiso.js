const URL_PERMISOS = 'controladores/permiso.php';

let tablaPermisos;
let modulosPermisos = [];

$(init);

function init() {
    cargarModulos();
    listarPermisos();

    $('#btnNuevoPermiso').on('click', nuevoPermiso);
    $('#formularioPermiso').on('submit', guardarPermiso);

    $('#tblListado tbody').on('click', '[data-action="edit"]', function () {
        mostrarPermiso(Number($(this).data('id')));
    });

    $('#tblListado tbody').on('click', '[data-action="delete"]', function () {
        eliminarPermiso(Number($(this).data('id')));
    });

    $('#modalPermiso').on('hidden.bs.modal', function () {
        $('#formularioPermiso')[0].reset();
        $('#idPermiso').val('');
        $('#modalPermisoTitulo').text('Permiso');
        $('#btnGuardarPermiso').prop('disabled', false).text('Guardar permiso');
    });
}

function escaparHtml(valor) {
    return String(valor ?? '').replace(/[&<>"']/g, caracter => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    })[caracter]);
}

function cargarModulos() {
    $('#btnNuevoPermiso').prop('disabled', true);

    $.getJSON(`${URL_PERMISOS}?op=listarModulosNuevos`)
        .done(function (modulos) {
            modulosPermisos = Array.isArray(modulos) ? modulos : [];
            pintarOpcionesModulos();
            $('#btnNuevoPermiso').prop('disabled', false);
        })
        .fail(function (xhr) {
            Swal.fire(
                'Error',
                xhr.responseJSON?.message || 'No se pudieron cargar los módulos.',
                'error'
            );
        });
}

function pintarOpcionesModulos() {
    const modulosPorId = new Map(
        modulosPermisos.map(modulo => [Number(modulo.id), { ...modulo, childrens: [] }])
    );
    const raices = [];

    modulosPermisos.forEach(modulo => {
        const actual = modulosPorId.get(Number(modulo.id));
        const padreId = Number(modulo.modulo_padre_id);
        if (modulo.modulo_padre_id && modulosPorId.has(padreId)) {
            modulosPorId.get(padreId).childrens.push(actual);
        } else {
            raices.push(actual);
        }
    });

    const renderOpciones = (modulos, nivel = 0) => modulos.map(modulo => `
        <option value="${Number(modulo.id)}">
            ${'— '.repeat(nivel)}${escaparHtml(modulo.nombre)}
        </option>
        ${renderOpciones(modulo.childrens, nivel + 1)}
    `).join('');

    $('#moduloPermiso').html(`
        <option value="">Seleccione un módulo...</option>
        ${renderOpciones(raices)}
    `);
}

function listarPermisos() {
    tablaPermisos = $('#tblListado').DataTable({
        processing: true,
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        dom: '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>><"col-sm-12 col-md-4"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [
            [10, 25, 50, 100, -1],
            ['10 filas', '25 filas', '50 filas', '100 filas', 'Mostrar todo']
        ],
        buttons: [
            'pageLength',
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-csv"></i>',
                titleAttr: 'Exportar a Excel'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i>',
                titleAttr: 'Exportar a PDF'
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-bars"></i>',
                titleAttr: 'Mostrar u ocultar columnas'
            }
        ],
        ajax: {
            url: `${URL_PERMISOS}?op=listarNuevos`,
            type: 'GET',
            dataType: 'json',
            dataSrc: 'data',
            error: function (xhr) {
                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message || 'No se pudieron cargar los permisos.',
                    'error'
                );
            }
        },
        columns: [
            {
                data: 'modulo_nombre',
                render: data => escaparHtml(data)
            },
            {
                data: 'codigo',
                render: data => `<code>${escaparHtml(data)}</code>`
            },
            {
                data: 'nombre',
                render: data => escaparHtml(data)
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: id => `
                    <button type="button" class="btn btn-info btn-xs mr-1"
                        data-action="edit" data-id="${Number(id)}"
                        title="Editar permiso">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-xs"
                        data-action="delete" data-id="${Number(id)}"
                        title="Eliminar permiso">
                        <i class="fas fa-trash"></i>
                    </button>
                `
            }
        ],
        destroy: true,
        pageLength: 10,
        order: [[0, 'asc'], [1, 'asc']]
    });
}

function nuevoPermiso() {
    $('#formularioPermiso')[0].reset();
    $('#idPermiso').val('');
    $('#modalPermisoTitulo').text('Nuevo permiso');
    $('#btnGuardarPermiso').text('Guardar permiso');
    $('#modalPermiso').modal('show');
}

function mostrarPermiso(id) {
    $.getJSON(`${URL_PERMISOS}?op=mostrarNuevo&id=${encodeURIComponent(id)}`)
        .done(function (permiso) {
            $('#formularioPermiso')[0].reset();
            $('#idPermiso').val(permiso.id);
            $('#moduloPermiso').val(permiso.modulo_id);
            $('#codigoPermiso').val(permiso.codigo);
            $('#nombrePermiso').val(permiso.nombre);
            $('#modalPermisoTitulo').text('Editar permiso');
            $('#btnGuardarPermiso').text('Guardar cambios');
            $('#modalPermiso').modal('show');
        })
        .fail(function (xhr) {
            Swal.fire(
                'Error',
                xhr.responseJSON?.message || 'No se pudo obtener el permiso.',
                'error'
            );
        });
}

function guardarPermiso(event) {
    event.preventDefault();

    const formulario = this;
    if (!formulario.checkValidity()) {
        formulario.reportValidity();
        return;
    }

    const boton = $('#btnGuardarPermiso');
    const textoOriginal = boton.text();
    boton.prop('disabled', true).html(
        '<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...'
    );

    $.ajax({
        url: `${URL_PERMISOS}?op=guardarNuevo`,
        type: 'POST',
        data: $(formulario).serialize(),
        dataType: 'json'
    })
        .done(function (respuesta) {
            Swal.fire('Correcto', respuesta.message, 'success');
            $('#modalPermiso').modal('hide');
            tablaPermisos.ajax.reload(null, false);
        })
        .fail(function (xhr) {
            Swal.fire(
                'Error',
                xhr.responseJSON?.message || 'No se pudo guardar el permiso.',
                'error'
            );
        })
        .always(function () {
            boton.prop('disabled', false).text(textoOriginal);
        });
}

function eliminarPermiso(id) {
    Swal.fire({
        title: '¿Eliminar permiso?',
        text: 'También se quitará de los roles y usuarios que lo tengan asignado.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then(function (resultado) {
        if (!resultado.isConfirmed) {
            return;
        }

        $.ajax({
            url: `${URL_PERMISOS}?op=eliminarNuevo`,
            type: 'POST',
            dataType: 'json',
            data: { id }
        })
            .done(function (respuesta) {
                Swal.fire('Correcto', respuesta.message, 'success');
                tablaPermisos.ajax.reload(null, false);
            })
            .fail(function (xhr) {
                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message || 'No se pudo eliminar el permiso.',
                    'error'
                );
            });
    });
}
