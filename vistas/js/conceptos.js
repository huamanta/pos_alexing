$("#navVentasActive").addClass("treeview active");
$("#navVentas").addClass("treeview menu-open");
$("#navConceptos").addClass("active");

let listarConceptos = null;

function init() {
    listarConceptos.load();
}
function pintarConceptos(data, permissions) {
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
                <td>${item.descripcion ?? ''}</td>
                <td>${item.tipo === 'egresos'
                ? '<span class="badge bg-danger">EGRESO</span>'
                : '<span class="badge bg-success">INGRESO</span>'
            }</td>
                <td>${item.categoria_concepto ?? ''}</td>
                <td>
                    <button class="btn btn-info btn-xs"
                        onclick='mostrarConcepto(${JSON.stringify(item)})'>
                        <i class="fa fa-list"></i>
                    </button>

                    <button class="btn btn-danger btn-xs"
                        onclick="eliminarConcepto(${item.idconcepto_movimiento})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            `;

    });


    $("#tbllistado tbody").html(html);
}

listarConceptos = new FluentPaginator({
    url: "controladores/cajachica.php?op=listarConceptos",
    renderTabla: pintarConceptos,
    tableBody: "#tbodyConceptos"
});

function crearConcepto() {
    $("#myModalCocepto").modal("show");
    $("#formularioConcepto")[0].reset();
    $("#categoria_concepto").html('<option value="">Seleccione...</option>');
    $("#divCategoriaMov").attr("hidden", "hidden");
}

function mostrarConcepto(obj) {
    $("#myModalCocepto").modal("show");

    // reset real
    $("#formularioConcepto")[0].reset();

    // primero seteamos el tipo
    $("#tipo").val(obj.tipo);

    // luego cargamos las categorías correctas
    condicioMovimiento(obj.tipo);

    // rellenamos datos
    $("#idconcepto_movimiento_form").val(obj.idconcepto_movimiento);
    $("#descripcion_concepto").val(obj.descripcion);

    // asignar categoría SOLO después de que existan las opciones
    $("#categoria_concepto").val(obj.categoria_concepto);
}

$("#tipo").change(function (e) {
    e.preventDefault();
    var tipo = $(this).val();
    condicioMovimiento(tipo);
    $("#idconcepto_movimiento_form").val("");
});

function condicioMovimiento(tipo) {
    var data = [];
    if (tipo === "ingresos") {
        //aqui para ingrsos
        $("#divCategoriaMov").attr("hidden", "hidden");
    } else {
        //aqui para egresos
        $("#divCategoriaMov").removeAttr("hidden", "hidden");
        data.push(
            {
                id: "presentado",
                name: "Presentado",
            },
            {
                id: "operativo",
                name: "Operativo",
            },
            {
                id: "personal",
                name: "Personal",
            }
        );
    }
    var html = '<option value="">Seleccione...</option>';
    data.forEach(function (item) {
        $("#idconcepto_movimiento").append(
            (html += `<option value="${item.id}">${item.name}</option>`)
        );
    });
    $("#categoria_concepto").html(html);
}

$("#formularioConcepto").submit(function (e) {
    e.preventDefault();
    let formData = new FormData(this);
    $.ajax({
        url: "controladores/cajachica.php?op=guardaryeditarConcepto",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            if (!response.success) {
                Swal.fire({
                    title: "Error!",
                    text: response.message,
                    icon: "error",
                });
                return;
            }
            Swal.fire({
                title: "Exito!",
                text: response.message,
                icon: "success",
            });
            $("#myModalCocepto").modal("hide");
            listarConceptos.load();
            $("#formularioConcepto")[0].reset();
        },
        error: function (error) {
            console.error(error);
            Swal.fire({
                title: "Error!",
                text: error.responseJSON.message || "Ocurrió un error al guardar el concepto.",
                icon: "error",
            });
        }
    });
});

document.addEventListener("DOMContentLoaded", () => {
    init();
});