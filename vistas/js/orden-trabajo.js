$('#navTallerActive').addClass("treeview active");
$('#navTaller').addClass("treeview menu-open");
$('#navOrdenTrabajo').addClass("active");
$('#frmOrdenTrabajo').hide();
$("#btnRegresar").hide();
const orderForm = $('#frmOrdenTrabajo');
const wizardSteps = $('.wizard-step');
const stepContents = $('.step-content');
const mechanicsTableBody = $('#mechanicsTableBody');
const partsTableBody = $('#partsTableBody');
let currentStep = 1;
let listarOrdenesTrabajo = null;

function init() {
    listarOrdenesTrabajo.load();
}

$("#fecha_inicio, #fecha_fin").change(function () {
    listarOrdenesTrabajo.load();
});

function pintarOrdenesTrabajo(data, permissions) {
    let html = "";

    if (data.length === 0) {
        html = `<tr>
                    <td colspan="8" class="text-center">No se encontraron registros</td>
                </tr>
                `;
        $("#tbllistado tbody").html(html);
        return;
    }

    data.forEach(item => {

        html += `
            <tr>
                <td>${item.created_at ?? ''}</td>
                <td>${item.numero ?? ''}</td>
                <td>${item.producto_nombre ?? ''}</td>
                <td>${item.tipo ?? ''}</td>
                <td>${item.estado ?? ''}</td>
                <td>${item.fecha_inicio ?? ''}</td>
                <td>${item.fecha_fin ?? ''}</td>
                <td>
                    <button class="btn btn-info btn-xs"
                        onclick='mostrar(${item.idorden})'>
                        <i class="fa fa-edit"></i>
                    </button>

                    <button class="btn btn-primary btn-xs"
                        onclick="abrirRecibo(${item.idorden})">
                        <i class="fa fa-print"></i>
                    </button>
                    
                    <button class="btn btn-danger btn-xs"
                        onclick="eliminar(${item.idorden})">
                        <i class="fa fa-trash"></i>
                    </button>

                </td>
            </tr>
            `;

    });


    $("#tbllistado tbody").html(html);
}

listarOrdenesTrabajo = new FluentPaginator({
    url: "controladores/ordentrabajo.php?op=listar",
    renderTabla: pintarOrdenesTrabajo,
    tableBody: "#tbodyOrdenesTrabajo",
    extraParams: () => ({
        fecha_inicio: $("#fecha_inicio").val() || '',
        fecha_fin: $("#fecha_fin").val() || ''
    })
});

function regresarPanel() {
    $('#panelOrdenesTrabajo').show();
    $('#frmOrdenTrabajo').hide();
    $("#btnRegresar").hide();
    listarOrdenesTrabajo.load();
}

function crearOrden() {
    const fecha = new Date();

    const dia = String(fecha.getDate()).padStart(2, "0");
    const mes = String(fecha.getMonth() + 1).padStart(2, "0");
    const anio = fecha.getFullYear();
    const fechaFormateada = `${anio}-${mes}-${dia}`;
    $("#fecha_registro").val(fechaFormateada);

    orderForm[0].reset();
    orderState.vehicle = null;
    orderState.mechanics = [];
    orderState.parts = [];
    orderState.costs = {};
    $('#panelOrdenesTrabajo').hide();
    $('#frmOrdenTrabajo').show();
    $("#btnRegresar").show();
    renderMechanics();
    renderParts();
    syncPayload();
    renderWizard(1);
}



$("#idcliente").select2({
    placeholder: "Buscar cliente...",
    allowClear: true,
    minimumInputLength: 2,
    ajax: {
        url: "controladores/venta.php?op=selectCliente",
        type: "GET",
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

$("#mechanicSelect").select2({
    with: '100%',
    placeholder: "Buscar personal...",
    allowClear: true,
    minimumInputLength: 2,
    ajax: {
        url: "controladores/ordentrabajo.php?op=selectPersonal",
        type: "GET",
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
                        id: item.idpersonal,
                        text: item.nombre + " - " + item.num_documento,
                        data: item
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

$("#repuestoBuscar").select2({
    width: "100%",
    placeholder: "Buscar producto...",
    allowClear: true,
    minimumInputLength: 2,
    ajax: {
        url: "controladores/producto.php?op=listar",
        type: "GET",
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
                        id: item.idproducto,
                        text: item.codigo + " - " + item.nombre,
                        data: item
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
    vehicle: null,
    mechanics: [],
    parts: [],
    costs: {
        repuestos: 0,
        manoObra: 0,
        otrosGastos: 0,
        serviciosExternos: 0,
        pintura: 0,
        transporte: 0,
        lavado: 0,
        total: 0
    }
};


function updateSummary() {
    const mechanicCount = orderState.mechanics.length;
    const totalHours = orderState.mechanics.reduce((sum, m) => sum + Number(m.hours || 0), 0);
    const laborCost = orderState.mechanics.reduce((sum, m) => sum + Number(m.subtotal || 0), 0);
    const partsCount = orderState.parts.length;
    const repuestosCost = orderState.parts.reduce((sum, p) => sum + Number(p.subtotal || 0), 0);
    // IGV incluido en el precio
    const taxCost = Number(((repuestosCost * IMPUESTO) / (100 + IMPUESTO)).toFixed(2));
    // Total ya incluye IGV
    const partialTotal = Number(repuestosCost.toFixed(2));
    const additionalCosts = {
        otrosGastos: getCostInputValue('otrosGastos'),
        serviciosExternos: getCostInputValue('serviciosExternos'),
        pintura: getCostInputValue('pintura'),
        transporte: getCostInputValue('transporte'),
        lavado: getCostInputValue('lavado')
    };
    const otherCosts = Object.values(additionalCosts).reduce((sum, cost) => sum + cost, 0);
    const totalCost = Number((repuestosCost + laborCost + otherCosts).toFixed(2));

    $('#mechanicsCount').text(mechanicCount);
    $('#hoursTotal').text(formatNumber(totalHours) + ' h');
    $('#laborCost').text(formatCurrency(laborCost));
    $('#productsCount').text(partsCount);
    $('#repuestosCost').text(formatCurrency(repuestosCost));
    $('#taxCost').text(formatCurrency(taxCost));
    $('#partialTotal').text(formatCurrency(partialTotal));

    orderState.costs = {
        repuestos: repuestosCost,
        manoObra: laborCost,
        ...additionalCosts,
        total: totalCost
    };

    $('#costRepuestos').text(formatCurrency(repuestosCost));
    $('#costManoObra').text(formatCurrency(laborCost));
    $('#costOtrosGastos').text(formatCurrency(otherCosts));
    $('#costTotal').text(formatCurrency(totalCost));
    $('#finalRepuestos').text(formatCurrency(repuestosCost));
    $('#finalManoObra').text(formatCurrency(laborCost));
    $('#finalOtrosGastos').text(formatCurrency(otherCosts));
    $('#finalTotal').text(formatCurrency(totalCost));
    $('#costDistributionTotal').text(formatCurrency(totalCost));

    const distributionTotal = totalCost || 1;
    $('#costDistribution').css('background', buildDistributionGradient(
        repuestosCost / distributionTotal,
        laborCost / distributionTotal,
        otherCosts / distributionTotal
    ));
    renderFinalSummary();
}

function getCostInputValue(name) {
    const value = Number($(`[name="${name}"]`).val());
    return Number.isFinite(value) && value > 0 ? Number(value.toFixed(2)) : 0;
}

function formatNumber(value) {
    return Number(value || 0).toFixed(2);
}

function formatCurrency(value) {
    return 'S/. ' + formatNumber(value);
}

function buildDistributionGradient(partsRatio, laborRatio, otherRatio) {
    const partsEnd = partsRatio * 100;
    const laborEnd = partsEnd + laborRatio * 100;
    const otherEnd = laborEnd + otherRatio * 100;
    return `conic-gradient(#0d6efd 0 ${partsEnd}%, #20c997 ${partsEnd}% ${laborEnd}%, #ffc107 ${laborEnd}% ${otherEnd}%, #6c757d ${otherEnd}% 100%)`;
}

function renderFinalSummary() {
    const orderType = $('#tipoOrden').val() || 'REPARACION';
    const vehicle = orderState.vehicle;
    const isNewVehicle = orderType === 'ENSAMBLAJE';
    const vehicleName = isNewVehicle
        ? 'Nueva motocicleta por ensamblar'
        : (vehicle ? (vehicle.nombre || vehicle.codigo || 'Moto seleccionada') : 'No seleccionada');
    const vehicleDetails = isNewVehicle
        ? 'Se creará al finalizar la orden'
        : vehicle
            ? `Placa: ${vehicle.placa || 'Sin placa'} | Serie: ${vehicle.numero_serie || 'Sin serie'}`
            : 'Seleccione una motocicleta en la información general';
    const clientText = $('#idcliente option:selected').text() || 'No seleccionado';
    const commitmentDate = $('input[name="fechaCompromiso"]').val() || 'No definida';
    const mechanics = orderState.mechanics.length
        ? orderState.mechanics.map((mechanic) => `<li>${mechanic.name} - ${mechanic.hours} h - ${formatCurrency(mechanic.subtotal)}</li>`).join('')
        : '<li class="text-muted">Sin mecánicos asignados</li>';
    const parts = orderState.parts.length
        ? orderState.parts.map((part) => `<li>${part.producto} x${part.cantidad} - ${formatCurrency(part.subtotal)}</li>`).join('')
        : '<li class="text-muted">Sin repuestos agregados</li>';

    $('#summaryOrderType').text($('#tipoOrden option:selected').text() || orderType);
    $('#summaryOrderStatus').text($('select[name="estado"] option:selected').text() || 'PENDIENTE');
    $('#summaryOrderPriority').text($('select[name="prioridad"] option:selected').text() || 'No definida');
    $('#summaryVehicleName').text(vehicleName);
    $('#summaryVehicleDetails').text(vehicleDetails);
    $('#summaryClient').text(clientText);
    $('#summaryCommitmentDate').text(commitmentDate);
    $('#summaryMechanics').html(mechanics);
    $('#summaryParts').html(parts);
    $('#summaryBranch').html(dataSucursal?.nombre || 'Principal');
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
                <td>${m.hours} Aprox</td>
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

    const selectedOption = $("#mechanicSelect").select2("data")[0];

    if (!selectedOption) {
        Swal.fire('Orden trabajo', 'Selecciona un mecánico del sistema antes de agregarlo.', 'warning');
        return;
    }

    const personal = selectedOption.data;

    if (orderState.mechanics.some((mechanic) => mechanic.id == personal.idpersonal)) {
        Swal.fire('Orden trabajo', 'Ese mecánico ya está agregado.', 'info');
        return;
    }

    const salary = Number(personal.salario || 30);
    const hourlyRate = salary / 30 / 8;
    const hours = 4;

    orderState.mechanics.push({
        id: personal.idpersonal,
        name: personal.nombre,
        role: personal.cargo,
        document: personal.num_documento,
        phone: personal.telefono,
        email: personal.email,
        percentage: personal.porcentaje,
        salary: personal.salario,
        hours: hours,
        rate: hourlyRate,
        subtotal: Number((hours * hourlyRate).toFixed(2)),
        photo: personal.imagen
            ? `files/personal/${personal.imagen}`
            : "files/personal/user.png",
        data: personal
    });

    renderMechanics();
    syncPayload();
}

function addPart() {
    const selectedOption = $("#repuestoBuscar").select2("data")[0];
    if (!selectedOption || !selectedOption.data) {
        Swal.fire('Orden trabajo', 'Selecciona un repuesto antes de agregarlo.', 'warning');
        return;
    }

    const producto = selectedOption.data;

    orderState.parts.push({
        idproducto: producto.idproducto,
        codigo: producto.codigo,
        producto: producto.nombre,
        stock: producto.stock,
        cantidad: 1,
        precio: producto.precio,
        descuento: 0,
        subtotal: producto.precio
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

$('#mechanicSelect').on('change', addMechanic);
$('#repuestoBuscar').on('change', addPart);

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
        Swal.fire('Orden trabajo', 'Complete la información general antes de continuar.', 'info');
        return;
    }

    renderWizard(currentStep + 1);
});

orderForm.on('input change select', 'input, select, textarea', function () {
    updateSummary();
    syncPayload();
});

orderForm.on('submit', function (e) {
    e.preventDefault();
    syncPayload();

    const payload = buildPayload();
    const endpoint = 'controladores/ordentrabajo.php?op=guardarOrdenTrabajo';

    if (endpoint) {
        $.ajax({
            url: endpoint,
            type: 'POST',
            data: payload,
            success: function (response) {
                if (!response.success) {
                    Swal.fire('Orden trabajo', response.message, 'danger');
                    return;
                }
                Swal.fire('Orden trabajo', response.message, 'success');
                window.location.href = 'orden-trabajo.php';
            },
            error: function (error) {
                Swal.fire('Orden trabajo', error.responseJSON.message || 'Error al enviar la orden al backend.', 'danger');
            }
        });
        return;
    }
});


$("#vehiculoBuscar").select2({
    placeholder: "Buscar producto...",
    allowClear: true,
    minimumInputLength: 2,
    ajax: {
        url: "controladores/venta.php?op=buscarProducto",
        type: "GET",
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
    orderState.vehicle = producto;
    $('#panelProductoEmpty').hide();
    $('#panelProducto').show();

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

    updateSummary();
    syncPayload();

});

$("#vehiculoBuscar").on("select2:clear", function () {
    orderState.vehicle = null;
    updateSummary();
    syncPayload();
});

init();