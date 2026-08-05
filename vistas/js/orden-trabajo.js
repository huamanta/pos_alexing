$('#navTallerActive').addClass("treeview active");
$('#navTaller').addClass("treeview menu-open");
$('#navOrdenTrabajo').addClass("active");

const orderForm = $('#frmOrdenTrabajo');
const wizardSteps = $('.wizard-step');
const stepContents = $('.step-content');
const mechanicsTableBody = $('#mechanicsTableBody');
const partsTableBody = $('#partsTableBody');
const wizardAlert = $('#wizardAlert');
let currentStep = 1;


$("#idcliente").select2({
    placeholder: "Buscar cliente...",
    allowClear: true,
    minimumInputLength: 2,
    ajax: {
        url: "controladores/venta.php?op=selectCliente",
        type: "POST",
        dataType: "json",
        delay: 250,
        data: function (params) {
            return {
                search: params.term,
                page: params.page || 1,
                only_client: 1,
            };
        },
        processResults: function (data, params) {
            params.page = params.page || 1;
            return {
                results: data.data.map(function (item) {
                    return {
                        id: item.idpersona,
                        text: item.nombre + " - " + item.num_documento,
                    };
                }),
                pagination: {
                    more: data.meta.current_page < data.meta.last_page,
                },
            };
        },
        cache: true,
    },
});

function populateMechanicSelect() {
    const select = $('#mechanicSelect');
    const personal = (window.ordenTrabajoPersonal || []).filter(Boolean);

    if (!select.length) {
        return;
    }

    select.empty().append('<option value="">Seleccione un mecánico...</option>');

    personal.forEach((empleado) => {
        const photo = empleado.imagen ? './files/personal/' + empleado.imagen : 'https://via.placeholder.com/44';
        const label = `${empleado.nombre} - ${empleado.cargo || 'Personal'}`;
        select.append(`<option value="${empleado.idpersonal}" data-id="${empleado.idpersonal}" data-name="${empleado.nombre}" data-role="${empleado.cargo || 'Personal'}" data-rate="${Number(empleado.salario || 30).toFixed(2)}" data-photo="${photo}">${label}</option>`);
    });
}

function initDefaultDates() {
    const defaults = window.ordenTrabajoDefaults || {};
    $('input[name="fecha"]').val(defaults.fecha || '');
    $('input[name="fechaCompromiso"]').val(defaults.fechaCompromiso || '');
}

const orderState = {
    mechanics: [
        { id: 1, name: 'Juan Pérez', role: 'Mecánico Senior', hours: 6, rate: 40, subtotal: 240, photo: 'https://via.placeholder.com/44' },
        { id: 2, name: 'Marcos Rojas', role: 'Ayudante', hours: 4, rate: 25, subtotal: 100, photo: 'https://via.placeholder.com/44' }
    ],
    parts: [
        { codigo: 'RP-001', producto: 'Filtro de aceite', stock: 24, cantidad: 1, precio: 18, descuento: 0, subtotal: 18 },
        { codigo: 'RP-004', producto: 'Kit de frenos', stock: 7, cantidad: 1, precio: 120, descuento: 5, subtotal: 114 }
    ],
    costs: {
        repuestos: 132,
        manoObra: 340,
        otrosGastos: 95,
        total: 567
    }
};

function showAlert(message, type = 'info') {
    if (!message) {
        wizardAlert.addClass('d-none').empty();
        return;
    }

    wizardAlert.removeClass('d-none alert-info alert-success alert-danger alert-warning')
        .addClass('alert-' + type)
        .html(message);
}

function updateSummary() {
    const mechanicCount = orderState.mechanics.length;
    const totalHours = orderState.mechanics.reduce((sum, m) => sum + Number(m.hours || 0), 0);
    const laborCost = orderState.mechanics.reduce((sum, m) => sum + Number(m.subtotal || 0), 0);
    const partsCount = orderState.parts.length;
    const repuestosCost = orderState.parts.reduce((sum, p) => sum + Number(p.subtotal || 0), 0);
    const taxCost = Number((repuestosCost * 0.18).toFixed(2));
    const partialTotal = Number((repuestosCost + taxCost).toFixed(2));

    $('#mechanicsCount').text(mechanicCount);
    $('#hoursTotal').text(totalHours + ' h');
    $('#laborCost').text('S/. ' + laborCost);
    $('#productsCount').text(partsCount);
    $('#repuestosCost').text('S/. ' + repuestosCost);
    $('#taxCost').text('S/. ' + taxCost.toFixed(2));
    $('#partialTotal').text('S/. ' + partialTotal.toFixed(2));

    orderState.costs = {
        repuestos: repuestosCost,
        manoObra: laborCost,
        otrosGastos: 95,
        total: Number((repuestosCost + laborCost + 95).toFixed(2))
    };
}

function renderMechanics() {
    if (!orderState.mechanics.length) {
        mechanicsTableBody.html('<tr><td colspan="7" class="text-center text-muted py-4">Sin mecánicos asignados.</td></tr>');
        updateSummary();
        return;
    }

    const rows = orderState.mechanics.map((m) => {
        return `
            <tr>
                <td><img src="${m.photo}" alt="Mecánico" class="rounded-circle" width="44"></td>
                <td>${m.name}</td>
                <td><span class="badge bg-info">${m.role}</span></td>
                <td>${m.hours}</td>
                <td>S/. ${m.rate}</td>
                <td><strong>S/. ${m.subtotal}</strong></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger btn-delete-mechanic" data-id="${m.id}"><i class="fas fa-trash"></i></button></td>
            </tr>`;
    }).join('');

    mechanicsTableBody.html(rows);
    updateSummary();
}

function renderParts() {
    if (!orderState.parts.length) {
        partsTableBody.html('<tr><td colspan="8" class="text-center text-muted py-4">Sin repuestos agregados.</td></tr>');
        updateSummary();
        return;
    }

    const rows = orderState.parts.map((p) => {
        return `
            <tr>
                <td>${p.codigo}</td>
                <td>${p.producto}</td>
                <td>${p.stock}</td>
                <td>${p.cantidad}</td>
                <td>S/. ${p.precio}</td>
                <td>${p.descuento}%</td>
                <td><strong>S/. ${p.subtotal}</strong></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger btn-delete-part" data-code="${p.codigo}"><i class="fas fa-trash"></i></button></td>
            </tr>`;
    }).join('');

    partsTableBody.html(rows);
    updateSummary();
}

function addMechanic() {
    const selectedOption = $('#mechanicSelect option:selected');
    const selectedId = selectedOption.val();

    if (!selectedId) {
        showAlert('<i class="fas fa-user me-2"></i> Selecciona un mecánico del sistema antes de agregarlo.', 'warning');
        return;
    }

    if (orderState.mechanics.some((mechanic) => String(mechanic.id) === String(selectedId))) {
        showAlert('<i class="fas fa-info-circle me-2"></i> Ese mecánico ya está agregado.', 'info');
        return;
    }

    const rate = Number(selectedOption.data('rate') || 30);
    const hours = 4;

    orderState.mechanics.push({
        id: Number(selectedId),
        name: selectedOption.data('name') || 'Mecánico',
        role: selectedOption.data('role') || 'Personal',
        hours: hours,
        rate: rate,
        subtotal: Number((hours * rate).toFixed(2)),
        photo: selectedOption.data('photo') || 'https://via.placeholder.com/44'
    });

    renderMechanics();
    syncPayload();
    showAlert('', 'info');
}

function addPart() {
    orderState.parts.push({
        codigo: 'RP-0' + (orderState.parts.length + 1),
        producto: 'Repuesto nuevo',
        stock: 10,
        cantidad: 1,
        precio: 50,
        descuento: 0,
        subtotal: 50
    });
    renderParts();
    syncPayload();
}

function removeMechanic(id) {
    orderState.mechanics = orderState.mechanics.filter((m) => m.id !== Number(id));
    renderMechanics();
    syncPayload();
}

function removePart(code) {
    orderState.parts = orderState.parts.filter((p) => p.codigo !== String(code));
    renderParts();
    syncPayload();
}

function buildPayload() {
    const formValues = orderForm.serializeArray().reduce((acc, item) => {
        if (item.name === 'payloadJson') {
            return acc;
        }
        acc[item.name] = item.value;
        return acc;
    }, {});

    return {
        ...formValues,
        mechanics: orderState.mechanics,
        parts: orderState.parts,
        costs: orderState.costs
    };
}

function syncPayload() {
    $('#payloadJson').val(JSON.stringify(buildPayload()));
}

function renderWizard(step) {
    currentStep = Math.min(5, Math.max(1, step));

    stepContents.addClass('d-none');
    stepContents.filter('[data-step="' + currentStep + '"]').removeClass('d-none');

    wizardSteps.removeClass('active completed');
    wizardSteps.each(function () {
        const stepNumber = parseInt($(this).data('step'), 10);
        if (stepNumber < currentStep) {
            $(this).addClass('completed');
        } else if (stepNumber === currentStep) {
            $(this).addClass('active');
        }
    });

    $('.prev-step').prop('disabled', currentStep === 1);
    $('.next-step').html(currentStep === 5
        ? 'Guardar Orden <i class="fas fa-check ms-2"></i>'
        : 'Siguiente <i class="fas fa-arrow-right ms-2"></i>');
}

populateMechanicSelect();
initDefaultDates();
renderMechanics();
renderParts();
updateSummary();
syncPayload();

$('#btnAddMechanic').on('click', addMechanic);
$('#btnAddProduct').on('click', addPart);

mechanicsTableBody.on('click', '.btn-delete-mechanic', function () {
    removeMechanic($(this).data('id'));
});

partsTableBody.on('click', '.btn-delete-part', function () {
    removePart($(this).data('code'));
});

$('.prev-step').on('click', function () {
    renderWizard(currentStep - 1);
});

$('.next-step').on('click', function () {
    if (currentStep === 5) {
        orderForm.trigger('submit');
        return;
    }

    if (currentStep === 1 && $('#tipoOrden').val() === '') {
        showAlert('<i class="fas fa-lightbulb me-2"></i> Complete la información general antes de continuar.', 'info');
        return;
    }

    showAlert('', 'info');
    renderWizard(currentStep + 1);
});

orderForm.on('input change select', 'input, select, textarea', function () {
    syncPayload();
});

orderForm.on('submit', function (e) {
    e.preventDefault();
    syncPayload();

    const payload = buildPayload();
    const endpoint = orderForm.data('endpoint') || window.ordenTrabajoEndpoint || '';

    if (endpoint) {
        $.ajax({
            url: endpoint,
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(payload),
            success: function (response) {
                showAlert('<strong>Orden enviada al backend correctamente.</strong><br>' + JSON.stringify(response, null, 2), 'success');
                console.log('[orden-trabajo] backend response:', response);
            },
            error: function (xhr) {
                showAlert('<strong>No se pudo enviar la orden al backend.</strong><br>Revisa la URL o el endpoint configurado.', 'danger');
                console.error('[orden-trabajo] backend error:', xhr);
            }
        });
        return;
    }

    console.log('[orden-trabajo] payload listo para backend:', payload);
    showAlert('<strong>Payload listo para conectar tu backend.</strong><br>El formulario está preparado para enviar la orden cuando definas el endpoint.', 'success');
});


$("#vehiculoBuscar").select2({
    placeholder: "Buscar producto...",
    allowClear: true,
    minimumInputLength: 2,
    ajax: {
        url: "controladores/venta.php?op=buscarProducto",
        type: "POST",
        dataType: "json",
        delay: 250,
        data: function (params) {
            return {
                search: params.term,
                page: params.page || 1
            };
        },
        processResults: function (data, params) {

            params.page = params.page || 1;

            return {
                results: data.data.map(function (item) {

                    return {
                        id: item.idproducto,
                        text: item.codigo + " - " + item.nombre,

                        // Guardas toda la fila
                        producto: item
                    };

                }),
                pagination: {
                    more: data.meta.current_page < data.meta.last_page
                }
            };

        },
        cache: true
    }
});

$("#vehiculoBuscar").on("select2:select", function (e) {

    const producto = e.params.data.producto;
    $('#panelProductoEmpty').hide();
    $('#panelProducto').show();
    console.log(producto);

    // Ejemplo
    $("#productoNombre").text(producto.codigo + ' - ' + producto.nombre);
    $("#prooductoDescripcion").text(producto.descripcion);
    $("#productoEstado").text(producto.estado);
    $("#productoSerie").text(producto.numero_serie);
    $("#productoPlaca").text(producto.placa);
    $("#prodcutoColor").text(producto.color);
    $("#productoAnio").text(producto.anio_fabricacion);
    $("#productoKilometraje").text(producto.kilometraje);
    $("#productoPrecio").text(producto.precio);
    $("#imagenmuestra")
        .show()
        .attr("src", "files/productos/" + producto.imagen);

});

renderMechanics();
renderParts();
syncPayload();
renderWizard(1);