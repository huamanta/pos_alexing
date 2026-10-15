const URL_ROLES = 'controladores/roles.php';

let modulosDisponibles = [];
let rolesData = [];
let listarRoles = null;

function init() {
    cargarModulos();
    listarRoles.load();

    $('#myModal').on('show.bs.modal', function () {
        if (modulosDisponibles.length === 0) {
            cargarModulos();
        }
    });

    $('#myModal').on('hidden.bs.modal', function () {
        $('#formulario')[0].reset();
        $('#idrol').val('');
        $('#btnGuardar').prop('disabled', false).html('Guardar');
        $('#listaModulos').find(':checkbox').prop('checked', false);
    });

    $('#formulario').on('submit', guardarRol);

    $('#btnSeleccionarTodos').on('click', function () {
        $('#listaModulos').find('.check-modulo, .check-submodulo, .check-permiso')
            .prop('checked', true);
    });

    $('#btnDeseleccionarTodos').on('click', function () {
        $('#listaModulos').find(':checkbox').prop('checked', false);
    });

    $(document).on('change', '.check-modulo', function () {
        $(this).closest('.modulo-card')
            .find('.check-submodulo, .check-permiso')
            .prop('checked', this.checked);

        actualizarEstadosModulos();
    });

    $(document).on('change', '.check-submodulo', function () {
        $(this).closest('.submodulo')
            .find('.check-permiso')
            .prop('checked', this.checked);

        actualizarEstadosModulos();
    });

    $(document).on('change', '.check-permiso', function () {
        actualizarEstadosModulos();
    });
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

function cargarModulos() {
    $('#listaModulos').html(`
        <div class="text-center py-4 text-muted">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Cargando módulos y permisos...
        </div>
    `);

    $.getJSON(`${URL_ROLES}?op=listarModulos`)
        .done(function (data) {
            modulosDisponibles = Array.isArray(data) ? data : [];
            pintarModulos();
        })
        .fail(function (xhr) {
            $('#listaModulos').html(`
                <div class="alert alert-danger">
                    No se pudieron cargar los módulos y permisos.
                    ${escapar(xhr.responseJSON?.error || '')}
                </div>
            `);
        });
}

function pintarModulos() {
    if (!modulosDisponibles.length) {
        $('#listaModulos').html(`
            <div class="alert alert-info">
                No hay módulos activos registrados.
            </div>
        `);
        return;
    }

    const html = modulosDisponibles.map(modulo => `
        <div class="card mb-2 modulo-card" data-modulo-id="${modulo.id}">
            <div class="card-header py-2">
                <div class="d-flex align-items-center">
                    <input type="checkbox"
                        class="check-modulo mr-2"
                        id="modulo_${modulo.id}">

                    <button type="button"
                        class="btn btn-link text-left flex-grow-1 p-0 font-weight-bold"
                        data-toggle="collapse"
                        data-target="#collapseModulo${modulo.id}"
                        aria-expanded="false">
                        ${modulo.icono
            ? `<i class="${escapar(modulo.icono)} mr-2"></i>`
            : '<i class="fas fa-folder mr-2"></i>'}
                        ${escapar(modulo.nombre)}
                    </button>

                    <span class="badge badge-secondary">
                        ${contarPermisos(modulo)} permisos
                    </span>
                </div>
            </div>

            <div id="collapseModulo${modulo.id}" class="collapse">
                <div class="card-body py-2">
                    ${pintarPermisos(modulo.permisos, 'permisos-modulo')}
                    ${(modulo.childrens || []).map(pintarSubmodulo).join('')}
                </div>
            </div>
        </div>
    `).join('');

    $('#listaModulos').html(html);
}

function pintarSubmodulo(submodulo) {
    return `
        <div class="border rounded p-2 mb-2 submodulo"
            data-modulo-id="${submodulo.id}">

            <div class="d-flex align-items-center mb-2">
                <input type="checkbox"
                    class="check-submodulo mr-2"
                    id="submodulo_${submodulo.id}">

                <label class="mb-0 font-weight-bold"
                    for="submodulo_${submodulo.id}">
                    ${escapar(submodulo.nombre)}
                </label>

                <span class="badge badge-light ml-auto">
                    ${(submodulo.permisos || []).length}
                </span>
            </div>

            ${pintarPermisos(submodulo.permisos, 'permisos-submodulo')}
        </div>
    `;
}

function pintarPermisos(permisos, clase) {
    if (!permisos || permisos.length === 0) {
        return '';
    }

    return `
        <div class="row ${clase} pl-3">
            ${permisos.map(permiso => `
                <div class="col-sm-6 col-md-4 col-lg-3 mb-2">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                            class="custom-control-input check-permiso"
                            name="permisos[]"
                            value="${permiso.id}"
                            id="permiso_${permiso.id}">

                        <label class="custom-control-label"
                            for="permiso_${permiso.id}"
                            title="${escapar(permiso.codigo)}">
                            ${escapar(permiso.nombre)}
                        </label>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

function contarPermisos(modulo) {
    let total = (modulo.permisos || []).length;

    (modulo.childrens || []).forEach(submodulo => {
        total += (submodulo.permisos || []).length;
    });

    return total;
}

function actualizarEstadosModulos() {
    $('#listaModulos .submodulo').each(function () {
        const permisos = $(this).find('.check-permiso');
        const marcados = permisos.filter(':checked').length;

        $(this).find('.check-submodulo')
            .prop('checked', permisos.length > 0 && marcados === permisos.length)
            .prop('indeterminate', marcados > 0 && marcados < permisos.length);
    });

    $('#listaModulos .modulo-card').each(function () {
        const permisos = $(this).find('.check-permiso');
        const marcados = permisos.filter(':checked').length;

        $(this).find('.check-modulo')
            .prop('checked', permisos.length > 0 && marcados === permisos.length)
            .prop('indeterminate', marcados > 0 && marcados < permisos.length);
    });
}


function pintarRoles(data, permissions) {
    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbllistado tbody").html(html);
        return;
    }

    data.forEach(item => {
        html += `
                <tr>
                    <td>${item.nombre}</td>
                    <td>${item.descripcion || '-'}</td>
                    <td>
                        <button
                            class="btn btn-info btn-xs"
                            onclick='mostrarRol(${item.id})'
                            data-toggle="tooltip"
                            title="editar movimiento"
                            >
                            <i class="fa fa-edit"></i>
                            </button>
                        <button
                            class="btn btn-danger btn-xs"
                            onclick='eliminar(${item.id})'
                            data-toggle="tooltip"
                            title="Eliminar movimiento"
                        >
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
    });

    $("#tbllistado tbody").html(html);
}

listarRoles = new FluentPaginator({
    url: "controladores/roles.php?op=listar",
    renderTabla: pintarRoles,
    tableBody: "#tbodyRoles"
});


function mostrarRol(id) {
    $('#formulario')[0].reset();
    $('#idrol').val('');
    $('#listaModulos').find(':checkbox').prop('checked', false);

    $.getJSON(`${URL_ROLES}?op=mostrar&id=${encodeURIComponent(id)}`)
        .done(function (rol) {
            $('#idrol').val(rol.id);
            $('#nombre').val(rol.nombre);
            $('#descripcion').val(rol.descripcion || '');

            const seleccionados = new Set((rol.permisos || []).map(Number));

            $('#listaModulos .check-permiso').each(function () {
                $(this).prop('checked', seleccionados.has(Number(this.value)));
            });

            actualizarEstadosModulos();
            $('#myModal').modal('show');
        })
        .fail(function (xhr) {
            Swal.fire(
                'Error',
                xhr.responseJSON?.error || 'No se pudo obtener el rol.',
                'error'
            );
        });
}

function guardarRol(e) {
    e.preventDefault();

    const formulario = this;

    if (!formulario.checkValidity()) {
        formulario.reportValidity();
        return;
    }

    const $boton = $('#btnGuardar');
    const textoOriginal = $boton.html();

    $boton.prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...');

    $.ajax({
        url: `${URL_ROLES}?op=guardar`,
        type: 'POST',
        data: $(formulario).serialize(),
        dataType: 'json'
    })
        .done(function (respuesta) {
            Swal.fire('Correcto', respuesta.message, 'success');
            $('#myModal').modal('hide');
            listarRoles.load();
        })
        .fail(function (xhr) {
            Swal.fire(
                'Error',
                xhr.responseJSON?.error || 'No se pudo guardar el rol.',
                'error'
            );
        })
        .always(function () {
            $boton.prop('disabled', false).html(textoOriginal);
        });
}

function eliminar(id) {
    Swal.fire({
        title: '¿Eliminar rol?',
        text: 'Se eliminarán también sus asignaciones de permisos.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then(function (resultado) {
        if (!resultado.isConfirmed) return;

        $.ajax({
            url: `${URL_ROLES}?op=eliminar`,
            type: 'POST',
            dataType: 'json',
            data: { id }
        })
            .done(function (respuesta) {
                Swal.fire('Correcto', respuesta.message, 'success');
                listarRoles.load();
            })
            .fail(function (xhr) {
                Swal.fire(
                    'Error',
                    xhr.responseJSON?.error || 'No se pudo eliminar el rol.',
                    'error'
                );
            });
    });
};

$(init);