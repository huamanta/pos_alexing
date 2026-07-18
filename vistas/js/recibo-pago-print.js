function abrirReciboPagoTicket(ticketData, formaPagoHtml) {
    const win = window.open('', '_blank', 'width=800,height=700');

    if (!win) {
        return false;
    }

    const html = `<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Recibo de Pago</title>
<style>
@page {
  size: 80mm auto;
  margin: 0;
}
body {
  width: 80mm;
  margin: 0;
  font-family: 'Courier New', monospace;
  font-size: 11px;
}
.ticket {
  width: 76mm;
  padding: 2mm;
}
.center { text-align: center; }
.right { text-align: right; }
.bold { font-weight: bold; }
.line {
  border-top: 1px dashed #000;
  margin: 5px 0;
}
table {
  width: 100%;
  border-collapse: collapse;
}
td {
  padding: 2px 0;
  vertical-align: top;
}
.small { font-size: 10px; }
.total { font-size: 14px; }
</style>
</head>
<body>
<div class="ticket">
  <div class="center bold">
    ${ticketData.empresa || ''}
    <br>
    ${ticketData.sucursal || ''}
    <br>
    RUC: ${ticketData.ruc || ''}
    <br>
    ${ticketData.direccion || ''}
  </div>

  <div class="line"></div>

  <div class="center bold">
    RECIBO DE PAGO
    <br>
    N° ${ticketData.idpago || ''}
  </div>

  <div class="line"></div>

  <table>
    <tr>
      <td>Fecha:</td>
      <td class="right">${ticketData.fecha || ''}</td>
    </tr>
    <tr>
      <td>Venta:</td>
      <td class="right">${ticketData.idventa || ''}</td>
    </tr>
    <tr>
      <td>Cuota:</td>
      <td class="right">${ticketData.idcpc || ''}</td>
    </tr>
    <tr>
      <td>Cliente:</td>
      <td class="right">${ticketData.cliente || '-'}</td>
    </tr>
  </table>

  <div class="line"></div>

  <div class="center bold">DETALLE DEL PAGO</div>

  <table>
    <tr>
      <td>Capital:</td>
      <td class="right">S/ ${Number(ticketData.capital_pagado || 0).toFixed(2)}</td>
    </tr>
    <tr>
      <td>Mora:</td>
      <td class="right">S/ ${Number(ticketData.mora_pagada || 0).toFixed(2)}</td>
    </tr>
    <tr>
      <td>Descuento:</td>
      <td class="right">S/ ${Number(ticketData.descuento || 0).toFixed(2)}</td>
    </tr>
    <tr class="bold total">
      <td>TOTAL PAGADO:</td>
      <td class="right">S/ ${Number(ticketData.monto_pagado || 0).toFixed(2)}</td>
    </tr>
  </table>

  <div class="line"></div>

  <div class="center bold">FORMA DE PAGO</div>
  <table>${formaPagoHtml || ''}</table>

  <div class="line"></div>

  <div class="center bold">SALDO PENDIENTE</div>
  <table>
    <tr>
      <td>Capital:</td>
      <td class="right">S/ ${Number(ticketData.saldo || 0).toFixed(2)}</td>
    </tr>
    <tr>
      <td>Mora:</td>
      <td class="right">S/ ${Number(ticketData.mora_pendiente || 0).toFixed(2)}</td>
    </tr>
  </table>

  <div class="line"></div>

  <div>
    <b>Observación:</b><br>
    ${ticketData.observacion || '-'}
  </div>

  <div class="line"></div>

  <div class="center small">
    Gracias por su pago
    <br>
    Vendedor: ${ticketData.personal || '-'}
    <br><br>
    Documento interno de cobranza
  </div>
</div>

<script>
window.onload = function () {
  window.focus();
  window.print();
};
window.onafterprint = function () {
  window.close();
};
</script>
</body>
</html>`;

    win.document.open();
    win.document.write(html);
    win.document.close();

    return true;
}
