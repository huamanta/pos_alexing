let nombreMetodoPago = "";
let listarDetalleMetodoPago = null;

const iconosMetodosPago = {
  Efectivo: "fas fa-money-bill-wave",
  Transferencia: "fas fa-university",
  Tarjeta: "fas fa-credit-card",
  Deposito: "fas fa-piggy-bank",
  Yape: "fas fa-mobile-alt",
  Plin: "fas fa-mobile-alt",
};

function escaparHtml(valor) {
  return $("<div>")
    .text(valor ?? "")
    .html();
}

function textoPlano(valor) {
  if (valor === null || valor === undefined) {
    return "";
  }

  return $("<div>").html(String(valor)).text().trim();
}

function obtenerIconoMetodo(nombre) {
  return iconosMetodosPago[nombre] || "fas fa-wallet";
}

function cargarMetodosPago() {
  $.get("controladores/metodospago.php?op=listar")
    .done(function (metodos) {
      const lista = Array.isArray(metodos) ? metodos : [];

      if (lista.length === 0) {
        $("#listaMetodosPago").html(`
                    <div class="col-12 text-center text-muted py-4">
                        No hay métodos de pago registrados
                    </div>
                `);
        return;
      }

      let html = "";
      lista.forEach(function (metodo) {
        const nombre = metodo.nombre || "-";
        const icono = obtenerIconoMetodo(nombre);
        html += `
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                      <div class="metodo-card h-100"
                          role="button"
                          onclick='verDetalleMetodoPago(${JSON.stringify(nombre)})'>

                          <div class="metodo-card-content">
                              <div class="metodo-icon">
                                  <i class="${icono}"></i>
                              </div>

                              <div class="metodo-info">
                                  <span class="metodo-label">MÉTODO DE PAGO</span>
                                  <h6>${escaparHtml(nombre)}</h6>
                                  <span class="metodo-action">Ver movimientos</span>
                              </div>

                              <div class="metodo-arrow">
                                  <i class="fas fa-arrow-right"></i>
                              </div>
                          </div>
                      </div>
                  </div>
                `;
      });
      $("#listaMetodosPago").html(html);
    })
    .fail(function () {
      $("#listaMetodosPago").html(`
                <div class="col-12 text-center text-danger py-4">
                    No se pudieron cargar los métodos de pago
                </div>
            `);
    });
}

function verDetalleMetodoPago(nombre) {
  nombreMetodoPago = nombre;
  const icono = obtenerIconoMetodo(nombre);

  $("#detalleMetodoNombre").text(nombre);
  $("#detalleMetodoTitulo").text(nombre);
  $("#detalleMetodoIcono").attr("class", icono + " mr-1");
  $("#detalleMetodoResumenIcono").attr("class", icono + " fa-lg text-primary");
  $("#resumenTotalMovimientos").text("0");
  $("#resumenIngresos").text("S/ 0.00");
  $("#resumenEgresos").text("S/ 0.00");
  $("#resumenNeto").text("S/ 0.00");

  $("#panelMetodosPago").hide();
  $("#panelDetalleMetodoPago").show();
  listarDetalleMetodoPago.load();
}

function regresarMetodosPago() {
  $("#panelDetalleMetodoPago").hide();
  $("#panelMetodosPago").show();
  nombreMetodoPago = "";
}

function pintarDetalleMetodoPago(data, permissions, meta) {
  const summary = meta.summary || {};
  const ingresos = Number(summary.ingresos || 0);
  const egresos = Number(summary.egresos || 0);
  const neto = Number(summary.neto || ingresos - egresos);

  $("#resumenTotalMovimientos").text(
    Number(summary.movimientos || 0).toLocaleString("es-PE"),
  );
  $("#resumenIngresos").text(
    "S/ " +
    ingresos.toLocaleString("es-PE", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }),
  );
  $("#resumenEgresos").text(
    "S/ " +
    egresos.toLocaleString("es-PE", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }),
  );
  $("#resumenNeto").text(
    "S/ " +
    neto.toLocaleString("es-PE", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }),
  );

  if (!Array.isArray(data) || data.length === 0) {
    $("#tbodyDetalleMetodosPago").html(`
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    No hay movimientos registrados para este método de pago
                </td>
            </tr>
        `);
    return;
  }

  let html = "";
  data.forEach(function (item) {
    const tipo = item.tipo || "-";
    const esIngreso = tipo.toLowerCase() === "ingresos";
    const monto = parseFloat(item.monto || 0).toFixed(2);

    html += `
            <tr>
                <td>${escaparHtml(textoPlano(item.fecha))}</td>
                <td>${escaparHtml(textoPlano(item.responsable))}</td>
                <td>${escaparHtml(textoPlano(item.origen))}</td>
                <td>${escaparHtml(textoPlano(item.cliente))}</td>
                <td>${escaparHtml(textoPlano(item.descripcion))}</td>
                <td>
                    <span class="badge ${esIngreso ? "badge-success" : "badge-danger"}">
                        ${escaparHtml(tipo)}
                    </span>
                </td>
                <td class="text-right font-weight-bold ${esIngreso ? "text-success" : "text-danger"}">
                    S/ ${monto}
                </td>
            </tr>
        `;
  });
  $("#tbodyDetalleMetodosPago").html(html);
}

listarDetalleMetodoPago = new FluentPaginator({
  url: "controladores/metodospago.php?op=listarDetalleMetodo",
  renderTabla: pintarDetalleMetodoPago,
  searchSelector: "#searchDetalleMetodosPago",
  limitSelector: "#limitDetalleMetodosPago",
  paginationId: "#paginationDetalleMetodosPago",
  tableBody: "#tbodyDetalleMetodosPago",
  extraParams: function () {
    return { nombre: nombreMetodoPago };
  },
});

$(function () {
  cargarMetodosPago();
});
