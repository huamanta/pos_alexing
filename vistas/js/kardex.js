var tabla;
let listarKardex = null;
function init() {
  $("#body").addClass("sidebar-collapse sidebar-mini");

  // Nav actual
  $("#navKardex").addClass("treeview active active");

  // Primera carga
  listarKardex.load();
}

$("#fecha_inicio, #fecha_fin, #idproducto").change(function (e) {
  listarKardex.load();
});

$("#idproducto").select2({
  placeholder: "Buscar producto...",
  allowClear: true,
  minimumInputLength: 2,
  ajax: {
    url: "controladores/venta.php?op=selectProducto",
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
            id: item.idproducto,
            text: item.codigo + " - " + item.nombre,
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

function getHoy() {
  var d = new Date();
  var m = ("0" + (d.getMonth() + 1)).slice(-2);
  var day = ("0" + d.getDate()).slice(-2);
  return d.getFullYear() + "-" + m + "-" + day;
}

function cargarProductos(idsucursal) {
  // Si es "Todos", mandar 'all' al backend (como lo manejabas)
  if (idsucursal === "Todos") idsucursal = "all";

  $.post(
    "controladores/venta.php?op=selectProducto",
    { idsucursal2: idsucursal },
    function (r) {
      $("#idproducto").html(r);
      $("#idproducto").select2({ width: "100%" });
    },
  );
}

listarKardex = new FluentPaginator({
  url: "controladores/consultas.php?op=kardex",
  tableBody: "#tbodyData",
  renderTabla: pintarProductos,
  extraParams: () => ({
    fecha_inicio: $("#fecha_inicio").val(),
    fecha_fin: $("#fecha_fin").val(),
    idproducto: $("#idproducto").val() || "",
  }),
});

function pintarProductos(data, permissions) {
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

  data.forEach((item) => {
    let btnActivarDesactivar = permissions.desactivar
      ? item.condicion === 1
        ? `<button class="btn btn-danger btn-xs" onclick="desactivar(${item.idproducto})"><i class="fas fa-times-circle"></i></button>`
        : `<button class="btn btn-info btn-xs" onclick="activar(${item.idproducto})"><i class="fas fa-check"></i></button>`
      : "";

    html += `
            <tr>
                <td>${item.fecha_kardex}</td>
                <td style="text-align:left;">
                    <strong>${item.nombre || ""}</strong><br>
                </td>
                <td>${item.motivo}</td>
                <td>${item.tipo_movimiento == 1 ? '<span class="badge badge-neon neon-green">Entrada</span>' : '<span class="badge badge-neon neon-red">Salida</span>'}</td>
                <td>
                    ${item.cantidad || 0} Und.
                </td>
                <td>
                    ${item.precio_unitario || "S/N"}
                </td>
                <td>${(item.cantidad / item.cantidad_contenedor) * item.precio_unitario}</td>
                <td>${item.cantidad_contenedor == 1 ? item.stock_actual : item.stock_actual - item.stock_actual / item.cantidad_contenedor}</td>
                <td>${(item.stock_actual / item.cantidad_contenedor) * item.precio_unitario}</td>

            </tr>
        `;
  });

  $("#tbllistado tbody").html(html);
}

$(document).ready(init);
