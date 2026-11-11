let listarConciliaciones = null;
let conciliacionSeleccionada = 0;
let movimientoBancarioSeleccionado = null;
let saldoCalculadoConciliacion = 0;

$.ajaxSetup({ cache: false });

function escaparHtmlConciliacion(valor) {
    if (valor === null || valor === undefined) return '';
    return $('<div>').text(String(valor)).html();
}

function formatoFechaConciliacion(valor) {
    if (!valor) return '-';
    const fecha = new Date(String(valor).replace(' ', 'T'));
    if (Number.isNaN(fecha.getTime())) return String(valor);
    return fecha.toLocaleString('es-PE', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatoMonedaConciliacion(valor) {
    return 'S/ ' + (Number(valor || 0)).toLocaleString('es-PE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function pintarConciliaciones(data, permissions) {
    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="5" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tblConciliaciones tbody").html(html);
        return;
    }

    data.forEach(item => {
        const estadoClass = item.estado === 'cerrada'
            ? 'success'
            : item.estado === 'anulada'
                ? 'danger'
                : 'warning';

        html += `
            <tr>
                <td>
                    <strong>${escaparHtmlConciliacion(item.banco_nombre)}</strong><br>
                    <small class="text-muted">${escaparHtmlConciliacion(item.banco_cuenta || '-')}</small>
                </td>
                <td>${escaparHtmlConciliacion(item.periodo_inicio)} al ${escaparHtmlConciliacion(item.periodo_fin)}</td>
                <td>${formatoMonedaConciliacion(item.saldo_inicial)}</td>
                <td><span class="badge badge-${estadoClass}">${escaparHtmlConciliacion(item.estado)}</span></td>
                <td>
                    <button type="button" class="btn btn-info btn-sm btn-ver-conciliacion"
                        data-id="${Number(item.idconciliacion)}">
                        <i class="fas fa-eye"></i> Ver
                    </button>
                </td>
            </tr>`;

    });

    $("#tblConciliaciones tbody").html(html);
}

listarConciliaciones = new FluentPaginator({
    url: "controladores/conciliaciones.php?op=listar",
    renderTabla: pintarConciliaciones,
    tableBody: "#tbodyConciliaciones",
});


function cargarBancosConciliacion() {
    $.getJSON('controladores/conciliaciones.php', { op: 'listarBancos' })
        .done(function (response) {
            let html = '<option value="">Seleccione una cuenta</option>';
            (response.data || []).forEach(function (banco) {
                html += `<option value="${Number(banco.idbanco)}">${escaparHtmlConciliacion(banco.nombre)} - ${escaparHtmlConciliacion(banco.cuenta || '-')}</option>`;
            });
            $('#conciliacionBanco').html(html).trigger('change');
        })
        .fail(function (xhr) {
            mostrarErrorConciliacion(xhr);
        });
}

function seleccionarMovimientoBancario(fila) {
    if (!fila.length || fila.attr('data-estado') !== 'pendiente') {
        return;
    }

    $('.bank-movement-row').removeClass('manual');
    $('.btn-seleccionar-bancario').removeClass('btn-primary').addClass('btn-outline-primary');

    fila.addClass('manual');
    fila.find('.btn-seleccionar-bancario')
        .removeClass('btn-outline-primary')
        .addClass('btn-primary');

    movimientoBancarioSeleccionado = Number(
        fila.attr('data-id') || fila.find('.btn-seleccionar-bancario').attr('data-id')
    );

    if (!movimientoBancarioSeleccionado) {
        Swal.fire('Seleccione primero', 'No se pudo identificar el movimiento bancario.', 'info');
        return;
    }

    const descripcion = $.trim(fila.find('td').eq(1).text());
    const importe = $.trim(fila.find('td').eq(4).text());
    $('#movimientoBancarioSeleccionado')
        .html(
            '<i class="fas fa-link text-primary mr-1"></i>' +
            '<strong>Seleccionado:</strong> ' +
            escaparHtmlConciliacion(descripcion) +
            ' - ' + escaparHtmlConciliacion(importe)
        )
        .show();
}

function abrirConciliacion(id) {
    movimientoBancarioSeleccionado = null;
    $('#movimientoBancarioSeleccionado').hide();
    $.getJSON('controladores/conciliaciones.php', {
        op: 'mostrar',
        idconciliacion: id
    }).done(function (response) {
        conciliacionSeleccionada = id;
        const conciliacion = response.conciliacion;
        $('#panelListadoConciliaciones').hide();
        $('#panelDetalleConciliacion').show();
        $('#detalleBancoConciliacion').text(
            `${conciliacion.banco_nombre || 'Banco'} - ${conciliacion.banco_cuenta || '-'}`
        );
        $('#detallePeriodoConciliacion').text(
            `${conciliacion.periodo_inicio} al ${conciliacion.periodo_fin} | Estado: ${conciliacion.estado}`
        );
        $('#detalleSaldoInicial').text(formatoMonedaConciliacion(conciliacion.saldo_inicial));
        $('#detalleSaldoFinal').text(formatoMonedaConciliacion(conciliacion.saldo_final_banco));

        const bancarios = response.movimientos_bancarios || [];
        const saldoInicialCentimos = Math.round(Number(conciliacion.saldo_inicial || 0) * 100);
        const saldoMovimientosCentimos = bancarios.reduce(function (saldo, item) {
            const importeCentimos = Math.round(Number(item.importe || 0) * 100);
            return saldo + (item.tipo === 'abono' ? importeCentimos : -importeCentimos);
        }, 0);
        saldoCalculadoConciliacion = (saldoInicialCentimos + saldoMovimientosCentimos) / 100;
        $('#detalleSaldoCalculado').text(formatoMonedaConciliacion(saldoCalculadoConciliacion));

        const pendientes = bancarios.filter(function (item) {
            return ['pendiente', 'diferencia', 'no_identificado'].includes(item.estado);
        }).length;
        $('#detalleTotalBancario').text(bancarios.length);
        $('#detalleTotalPendientes').text(pendientes);

        let htmlBancarios = '';
        if (!bancarios.length) {
            htmlBancarios = '<tr><td colspan="6" class="text-center text-muted">No hay movimientos importados</td></tr>';
        } else {
            bancarios.forEach(function (item) {
                const seleccionable = item.estado === 'pendiente' && conciliacion.estado !== 'cerrada' && conciliacion.estado !== 'anulada';
                const classEstado = seleccionable ? 'pending' : '';
                htmlBancarios += `
                    <tr class="bank-movement-row ${classEstado}" data-id="${Number(item.idmovimiento_bancario)}"
                        data-estado="${escaparHtmlConciliacion(item.estado)}">
                        <td>
                            <button type="button" ${seleccionable ? '' : 'disabled'}
                                class="btn btn-outline-primary btn-sm btn-seleccionar-bancario mr-1"
                                data-id="${Number(item.idmovimiento_bancario)}"
                                title="Seleccionar movimiento bancario">
                                <i class="fas fa-check"></i>
                            </button>
                            ${formatoFechaConciliacion(item.fecha_operacion)}
                        </td>
                        <td>${escaparHtmlConciliacion(item.descripcion || '-')}</td>
                        <td>${escaparHtmlConciliacion(item.tipo)}</td>
                        <td>${escaparHtmlConciliacion(item.numero_operacion)}</td>
                        <td class="text-right">${formatoMonedaConciliacion(item.importe)}</td>
                        <td><span class="badge badge-light">${escaparHtmlConciliacion(item.estado)}</span></td>
                    </tr>`;
            });
        }
        $('#tbodyMovimientosBancarios').html(htmlBancarios);
        const primerPendiente = $('#tbodyMovimientosBancarios .bank-movement-row.pending').first();
        if (primerPendiente.length) {
            seleccionarMovimientoBancario(primerPendiente);
        } else {
            movimientoBancarioSeleccionado = null;
            $('#movimientoBancarioSeleccionado').hide();
        }

        const sistema = response.movimientos_sistema || [];
        let htmlSistema = '';
        if (!sistema.length) {
            htmlSistema =
                '<tr><td colspan="6" class="text-center text-muted">' +
                'No hay movimientos del sistema para la cuenta y el periodo seleccionado.' +
                '</td></tr>';
        } else {
            sistema.forEach(function (item) {
                htmlSistema += `
                    <tr>
                        <td>${formatoFechaConciliacion(item.fecha)}</td>
                        <td>${escaparHtmlConciliacion(item.origen)}</td>
                        <td>${escaparHtmlConciliacion(item.tipo)}</td>
                        <td>${escaparHtmlConciliacion(item.numero_operacion)}</td>
                        <td class="text-right">${formatoMonedaConciliacion(item.importe)}</td>
                        <td>
                            <button type="button" class="btn btn-outline-primary btn-sm btn-conciliar-sistema"
                                ${conciliacion.estado === 'cerrada' || conciliacion.estado === 'anulada' ? 'disabled' : ''}
                                data-origen="${escaparHtmlConciliacion(item.origen)}"
                                data-id="${Number(item.id)}"
                                data-importe="${Number(item.importe || 0)}">
                                <i class="fas fa-link"></i>
                            </button>
                        </td>
                    </tr>`;
            });
        }
        $('#tbodyMovimientosSistema').html(htmlSistema);
        const editable = conciliacion.estado !== 'cerrada' && conciliacion.estado !== 'anulada';
        $('#btnImportarEstado, #btnAgregarMovimientoBancario, #btnAutoConciliar').toggle(editable);
        $('#btnCerrarConciliacion').toggle(editable);
    }).fail(function (xhr) {
        mostrarErrorConciliacion(xhr);
    });
}

function mostrarErrorConciliacion(xhr) {
    let mensaje = 'Ocurrio un error inesperado.';
    try {
        mensaje = JSON.parse(xhr.responseText).message || mensaje;
    } catch (error) {
        if (xhr.responseText) mensaje = xhr.responseText;
    }
    Swal.fire('Conciliaciones bancarias', mensaje, 'error');
}

$(function () {
    listarConciliaciones.load();
    if ($.fn.select2) $('.select2').select2();

    $('#btnNuevaConciliacion').on('click', function () {
        cargarBancosConciliacion();
        $('#modalConciliacion').modal('show');
    });

    $(document).on('click', '.btn-ver-conciliacion', function () {
        abrirConciliacion(Number($(this).data('id')));
    });

    $('#btnRegresarConciliaciones').on('click', function () {
        $('#panelDetalleConciliacion').hide();
        $('#panelListadoConciliaciones').show();
        listarConciliaciones.load();
    });

    $('#formConciliacion').off('submit').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url: 'controladores/conciliaciones.php?op=crear',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                idbanco: $('#conciliacionBanco').val(),
                periodo_inicio: $('#periodoInicio').val(),
                periodo_fin: $('#periodoFin').val(),
                saldo_inicial: $('#saldoInicial').val(),
                observaciones: $('#observacionesConciliacion').val()
            })
        }).done(function (response) {
            $('#modalConciliacion').modal('hide');
            Swal.fire('Conciliacion creada', response.message, 'success');
            listarConciliaciones.load();
        }).fail(mostrarErrorConciliacion);
    });

    $('#btnImportarEstado').on('click', function () {
        $('#formImportarConciliacion')[0].reset();
        $('#modalImportarConciliacion').modal('show');
    });

    $('#formImportarConciliacion').on('submit', function (event) {
        event.preventDefault();
        const form = new FormData();
        form.append('archivo', $('#archivoConciliacion')[0].files[0]);
        const mapping = {};
        $('#formImportarConciliacion [name]').each(function () {
            mapping[this.name] = $(this).val();
        });
        form.append('mapping', JSON.stringify(mapping));

        $.ajax({
            url: 'controladores/conciliaciones.php?op=importar&idconciliacion=' + conciliacionSeleccionada,
            type: 'POST',
            data: form,
            processData: false,
            contentType: false,
            dataType: 'json'
        }).done(function (response) {
            $('#modalImportarConciliacion').modal('hide');
            Swal.fire('Importacion', response.message, 'success');
            abrirConciliacion(conciliacionSeleccionada);
        }).fail(mostrarErrorConciliacion);
    });

    $('#btnAgregarMovimientoBancario').on('click', function () {
        $('#formMovimientoBancario')[0].reset();
        $('#modalMovimientoBancario').modal('show');
    });

    $('#formMovimientoBancario').off('submit').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            url: 'controladores/conciliaciones.php?op=crearMovimientoBancario&idconciliacion=' + conciliacionSeleccionada,
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                fecha_operacion: $('#movimientoBancarioFecha').val(),
                tipo: $('#movimientoBancarioTipo').val(),
                importe: $('#movimientoBancarioImporte').val(),
                descripcion: $('#movimientoBancarioDescripcion').val(),
                numero_operacion: $('#movimientoBancarioOperacion').val(),
                referencia: $('#movimientoBancarioReferencia').val()
            })
        }).done(function (response) {
            $('#modalMovimientoBancario').modal('hide');
            Swal.fire('Movimiento agregado', response.message, 'success');
            abrirConciliacion(conciliacionSeleccionada);
        }).fail(mostrarErrorConciliacion);
    });

    $(document).on('click', '.btn-seleccionar-bancario', function (event) {
        event.preventDefault();
        event.stopPropagation();
        seleccionarMovimientoBancario($(this).closest('.bank-movement-row'));
    });

    $(document).on('click', '.bank-movement-row.pending', function (event) {
        if ($(event.target).closest('.btn-conciliar-sistema').length) return;
        seleccionarMovimientoBancario($(this));
    });

    $(document).on('click', '.btn-conciliar-sistema', function () {
        if (!movimientoBancarioSeleccionado) {
            Swal.fire('Seleccione primero', 'Seleccione un movimiento bancario en la tabla izquierda.', 'info');
            return;
        }

        const button = $(this);
        $.ajax({
            url: 'controladores/conciliaciones.php?op=conciliarManual&idconciliacion=' + conciliacionSeleccionada,
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                idmovimiento_bancario: movimientoBancarioSeleccionado,
                origen_sistema: button.data('origen'),
                idmovimiento_sistema: button.data('id'),
                importe_sistema: button.data('importe')
            })
        }).done(function (response) {
            Swal.fire('Conciliacion', response.message, 'success');
            abrirConciliacion(conciliacionSeleccionada);
            movimientoBancarioSeleccionado = null;
        }).fail(mostrarErrorConciliacion);
    });

    $('#btnAutoConciliar').on('click', function () {
        Swal.fire({
            title: 'Conciliar coincidencias exactas',
            text: 'Solo se vincularán coincidencias únicas por número de operación, importe y sentido. Las demás quedarán pendientes.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Buscar coincidencias',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: 'controladores/conciliaciones.php?op=conciliarAutomatico&idconciliacion=' + conciliacionSeleccionada,
                type: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({})
            }).done(function (response) {
                const conciliados = Number(response.data && response.data.conciliados || 0);
                const mensaje = conciliados
                    ? 'Se conciliaron ' + conciliados + ' movimiento(s).'
                    : 'No se encontraron coincidencias únicas que cumplan todos los criterios.';
                Swal.fire(
                    'Conciliación automática',
                    mensaje,
                    conciliados ? 'success' : 'info'
                );
                abrirConciliacion(conciliacionSeleccionada);
            }).fail(mostrarErrorConciliacion);
        });
    });

    function enviarCierreConciliacion(saldoFinal, confirmarDiferencia) {
        $.ajax({
            url: 'controladores/conciliaciones.php?op=cerrar&idconciliacion=' + conciliacionSeleccionada,
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                saldo_final_banco: saldoFinal,
                confirmar_diferencia: confirmarDiferencia
            })
        }).done(function (response) {
            const diferencia = Number(response.data && response.data.diferencia || 0);
            const mensaje = diferencia === 0
                ? response.message
                : response.message + ' Diferencia respecto al saldo calculado: ' + formatoMonedaConciliacion(diferencia) + '.';
            Swal.fire('Conciliación cerrada', mensaje, 'success');
            abrirConciliacion(conciliacionSeleccionada);
        }).fail(mostrarErrorConciliacion);
    }

    $('#btnCerrarConciliacion').on('click', function () {
        Swal.fire({
            title: 'Cerrar conciliacion',
            text: 'Saldo calculado por movimientos: ' + formatoMonedaConciliacion(saldoCalculadoConciliacion) +
                '. Ingrese el saldo final del estado de cuenta.',
            input: 'number',
            inputValue: saldoCalculadoConciliacion.toFixed(2),
            inputAttributes: { step: '0.01' },
            showCancelButton: true,
            confirmButtonText: 'Cerrar',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (!result.isConfirmed || result.value === '') return;
            const saldoFinal = Number(result.value);
            if (!Number.isFinite(saldoFinal)) {
                Swal.fire('Saldo no válido', 'Ingrese un saldo final numérico.', 'warning');
                return;
            }
            const diferencia = Math.round((saldoFinal - saldoCalculadoConciliacion) * 100) / 100;
            if (diferencia === 0) {
                enviarCierreConciliacion(saldoFinal, false);
                return;
            }
            Swal.fire({
                title: 'Hay una diferencia de saldo',
                text: 'El saldo ingresado difiere del calculado en ' + formatoMonedaConciliacion(diferencia) +
                    '. ¿Desea cerrar de todos modos?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Cerrar con diferencia',
                cancelButtonText: 'Revisar'
            }).then(function (confirmacion) {
                if (confirmacion.isConfirmed) {
                    enviarCierreConciliacion(saldoFinal, true);
                }
            });
        });
    });
});
