let listarBancos = null;
let listarBancoMovimientos = null;
let idBanco = null;
$("#panelMovimientoBancos").hide();
function init() {
    listarBancos.load();
}

function pintarBancos(data, permissions) {
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
                    <td>${item.cuenta || '-'}</td>
                    <td>${item.cci || '-'}</td>
                    <td>${item.saldo || '0'}</td>
                    <td>
                        <button
                            class="btn btn-info btn-xs"
                            onclick='mostar(${JSON.stringify(item)})'
                            data-toggle="tooltip"
                            title="editar movimiento"
                            >
                            <i class="fa fa-edit"></i>
                            </button>
                        <button
                            class="btn btn-dark btn-xs"
                            onclick='verMovimientos(${JSON.stringify(item)})'
                            data-toggle="tooltip"
                            title="Ver movimientos"
                        >
                            <i class="fa fa-list"></i>
                        </button>
                        <button
                            class="btn btn-danger btn-xs"
                            onclick='eliminar(${item.idbanco})'
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

listarBancos = new FluentPaginator({
    url: "controladores/bancos.php?op=listar",
    renderTabla: pintarBancos,
    tableBody: "#tbodyBancos"
});


function verMovimientos(item) {
    $("#panelBancos").hide();
    $('#detalleBancoNombre').text(item.nombre || '-');
    $('#detalleBancoDescripcion').text(item.descripcion || '-');
    $('#detalleBancoCuenta').text(item.cuenta || '-');
    $('#detalleBancoCci').text(item.cci || '-');

    $('#detalleBancoSaldo').text(
        'S/ ' + parseFloat(item.saldo || 0).toFixed(2)
    );
    idBanco = item.idbanco
    listarBancoMovimientos.load();
    $("#panelMovimientoBancos").show();
}


function pintarBancoMovimientos(data, permissions) {
    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbllistadoMovimientos tbody").html(html);
        return;
    }

    data.forEach(item => {


        html += `
            <tr>
                <td>${item.fecha}</td>
                <td>${item.responsable || '-'}</td>
                <td class="${item.tipo === 'Ingresos' ? 'text-success' : ''}">
                    ${item.tipo === 'Ingresos' ? item.monto : '-'}
                </td>
                <td class="${item.tipo === 'Egresos' ? 'text-danger' : ''}">
                    ${item.tipo === 'Egresos' ? item.monto : '-'}
                </td>
            </tr>
        `;

    });

    $("#tbllistadoMovimientos tbody").html(html);
}

listarBancoMovimientos = new FluentPaginator({
    url: "controladores/bancos.php?op=listarMovimientos",
    renderTabla: pintarBancoMovimientos,
    tableBody: "#tbodyBancoMovimientos",
    extraParams: () => ({
        idbanco: idBanco
    })
});

function regresarBancos() {
    $('#panelMovimientoBancos').hide();
    $('#panelBancos').show();
    listarBancos.load();
}

$("#btnNuevo").click(function (e) {
    limpiar();
    $("#idbanco").val('');
    $('#myModal').modal('show');
});

function limpiar() {
    $("#formulario")[0].reset();
}

$("#formulario").submit(function (e) {
    e.preventDefault();

    const data = {
        nombre: $("#nombre").val(),
        descripcion: $("#descripcion").val(),
        cuenta: $("#cuenta").val(),
        cci: $("#cci").val(),
    };

    $.ajax({
        url: "controladores/bancos.php?op=guardaryeditar&idbanco=" + $("#idbanco").val(),
        type: "POST",
        data: JSON.stringify(data),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (response) {
            if (!response.success) {
                Swal.fire({
                    title: 'Bancos',
                    icon: 'error',
                    text: response.message
                });
                return;
            }

            Swal.fire({
                title: 'Bancos',
                icon: 'success',
                text: response.message
            });

            $('#myModal').modal('hide');
            listarBancos.load();
            limpiar();
        },
        error: function (error) {
            console.log(error.responseText);
        }
    });
});


function mostar(data) {
    $('#myModal').modal('show');
    $("#idbanco").val(data.idbanco);
    $("#nombre").val(data.nombre);
    $("#descripcion").val(data.descripcion);
    $("#cuenta").val(data.cuenta);
    $("#cci").val(data.cci);
}


function eliminar(idbanco) {
    console.log(idbanco);
    
    Swal.fire({
        title: '¿Desactivar?',
        text: "¿Está seguro Que Desea Desactivar la Categoría?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.get("controladores/bancos.php?op=eliminar", { idbanco: idbanco }, function (response) {
                if (!response.success) {
                    Swal.fire('Bancos!', response.message || 'No se pudo eliminar', 'error');
                    return;
                }
                Swal.fire('Bancos!', response.message, 'success');
                listarBancos.load();
            });
        }
    })

}


init();