var tabla;
const API_KEY = "AIzaSyAEfzrVHyxezdBMPmKlF8Hs-of68DzrRFY";

let map;
let marker;
let listarPersonas = null;
function initMap() {
  const latInput = Number($("#latitude").val());
  const lngInput = Number($("#longitude").val());

  const latitude = isNaN(latInput) ? -6.487595468705555 : latInput;
  const longitude = isNaN(lngInput) ? -76.3601303100586 : lngInput;

  map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: latitude, lng: longitude },
    zoom: 13,
  });

  if (!isNaN(latInput) && !isNaN(lngInput)) {
    placeMarker({ lat: latitude, lng: longitude });
  }

  map.addListener("click", function (event) {
    placeMarker({
      lat: event.latLng.lat(),
      lng: event.latLng.lng(),
    });
  });
}

function buscarDireccion() {
  const direccion = document.getElementById("direccion").value;

  if (!direccion) {
    alert("Ingrese una dirección");
    return;
  }

  fetch(
    `https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(direccion)}&key=${API_KEY}`,
  )
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "OK") {
        const result = data.results[0];

        const lat = Number(result.geometry.location.lat);
        const lng = Number(result.geometry.location.lng);
        const address = result.formatted_address;

        const latLng = { lat, lng };
        map.setCenter(latLng);

        placeMarker(latLng);

        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;
        document.getElementById("direccion").value = address;

      } else {
        alert("No se encontró la dirección");
      }
    });
}

function placeMarker(location) {
  if (marker) marker.setMap(null);

  marker = new google.maps.Marker({
    position: location,
    map: map,
  });

  $("#latitude").val(location.lat);
  $("#longitude").val(location.lng);

  getAddressFromCoords(location.lat, location.lng);
}

function getAddressFromCoords(lat, lng) {
  fetch(
    `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${API_KEY}`,
  )
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "OK") {
        const address = data.results[0].formatted_address;
        document.getElementById("direccion").value = address;
      }
    });
}

//Función que se ejecuta al inicio
function init() {
  limpiar();
  listarPersonas.load();
  $("#myModal").on("submit", function (e) {
    guardaryeditar(e);
  });
  $("#navClienteActive").addClass("active");
}

//Función limpiar
function limpiar() {
  $("#nombre").val("");
  $("#num_documento").val("");
  $("#direccion").val("");
  $("#telefono").val("");
  $("#email").val("");
  $("#idpersona").val("");
  $("#latitude").val("-6.487595468705555");
  $("#longitude").val("-76.3601303100586");
  $("#proveedor").prop("checked", false);
}

function mostrar(idpersona) {
  limpiar();
  $.post(
    "controladores/persona.php?op=mostrar",
    { idpersona: idpersona },
    function (data, status) {
      data = JSON.parse(data);
      $("#myModal").modal("show");

      $("#nombre").val(data.nombre);
      $("#tipo_documento").val(data.tipo_documento);
      $("#num_documento").val(data.num_documento);
      $("#direccion").val(data.direccion);
      $("#telefono").val(data.telefono);
      $("#email").val(data.email);
      $("#idpersona").val(data.idpersona);
      $("#latitude").val(data.latitude ? data.latitude : '-6.487595468705555');
      $("#longitude").val(data.longitude ? data.longitude : '-76.3601303100586');
      if (data.isproveedor == 1) {
        $("#proveedor").prop("checked", true);
      } else {
        $("#proveedor").prop("checked", false);
      }
      initMap();
    },
  );
}

function guardaryeditar(e) {
  e.preventDefault(); //No se activará la acción predeterminada del evento
  //$("#btnGuardar").prop("disabled",true);
  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "controladores/persona.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    beforeSend: function () {
      $("#btnGuardar").prop("disabled", true).text("Guardando...");
    },
    success: function (datos) {
      datos = JSON.parse(datos);
      if (!datos.success) {
        Swal.fire({
          title: "Cliente",
          icon: "error",
          text: datos.message,
        });
        return;
      }
      Swal.fire({
        title: "Cliente",
        icon: "success",
        text: datos.message,
      });
      $("#myModal").modal("hide");
      listarPersonas.load();
    },
    complete: function () {
      $("#btnGuardar").prop("disabled", false).text("Guardar");
    }
  });
  limpiar();
}

function BuscarCliente() {
  let numero = $("#num_documento").val();

  $.post(
    "controladores/venta.php?op=selectCliente3&numero=" + numero,
    function (data, status) {
      data = JSON.parse(data);

      if (data != null) {
        Swal.fire({
          title: "¡Aviso!",
          icon: "info",
          text: "El Cliente ya se encuentra registrado",
        });

        $("#num_documento").val("");
      } else {
        if ($("#tipo_documento").val() == "DNI") {
          var cod = $.trim($("#tipo_documento").val());
          $numero = $("#num_documento").val();
          if ($numero.length < 8) {
            Swal.fire({
              title: "Falta Números en el DNI",
              icon: "info",
              text: "El DNI debe tener 8 Carácteres",
            });
          } else {
            $("#Buscar_Cliente").hide();
            var numdni = $("#num_documento").val();
            var url =
              "https://dniruc.apisperu.com/api/v1/dni/" +
              numdni +
              "?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Ik1hbnVlbF8xM18xOTk4QGhvdG1haWwuY29tIn0.pNHFyJ3fT4JgofrxzINaJWlqh3_fC9bCzfwSP4N_dMo";

            $("#cargando").show();
            $.ajax({
              type: "GET",
              url: url,
              success: function (dat) {
                if (dat.success == false) {
                  Swal.fire({
                    title: "DNI Inválido",
                    icon: "error",
                    text: "¡No Existe DNI!",
                  });
                } else {
                  //$('#nombre').val(dat.success[0]);
                  $("#nombre").val(
                    dat.nombres +
                    " " +
                    dat.apellidoPaterno +
                    " " +
                    dat.apellidoMaterno,
                  );
                  $("#Buscar_Cliente").hide();
                  $("#cargando").hide();
                }
              },
              complete: function () {
                $("#Buscar_Cliente").show();
                $("#cargando").hide();
              },
              error: function () { },
            });
          }
        } else {
          var cod = $.trim($("#tipo_documento").val());
          $numero = $("#num_documento").val();
          if ($numero.length < 11) {
            Swal.fire({
              title: "Falta Números en el RUC",
              icon: "info",
              text: "El DNI debe tener 11 Carácteres",
            });
          } else {
            $("#Buscar_Cliente").hide();
            var numdni = $("#num_documento").val();
            var url =
              "https://dniruc.apisperu.com/api/v1/ruc/" +
              numdni +
              "?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Ik1hbnVlbF8xM18xOTk4QGhvdG1haWwuY29tIn0.pNHFyJ3fT4JgofrxzINaJWlqh3_fC9bCzfwSP4N_dMo";
            $("#cargando").show();
            $.ajax({
              type: "GET",
              url: url,
              success: function (dat) {
                console.log(dat);
                if (dat.success == false) {
                  Swal.fire({
                    title: "Ruc Inválido",
                    icon: "info",
                    text: "¡No Existe RUC!",
                  });
                } else {
                  $("#nombre").val(dat.razonSocial);
                  $("#direccion").val(dat.direccion);
                  document.getElementById("estado2").innerHTML = dat.estado;
                  document.getElementById("condicion").innerHTML =
                    dat.condicion;
                  $("#Buscar_Cliente").hide();
                  $("#cargando").hide();
                }
              },
              complete: function () {
                $("#Buscar_Cliente").show();
                $("#cargando").hide();
              },
              error: function () { },
            });
          }
        }
      }
    },
  );
}

function pintarPersonas(data, permissions) {

  let html = "";

  if (data.length === 0) {
    html = `
      <tr>
        <td colspan="6" class="text-center">No se encontraron registros</td>
      </tr>
    `;
    $("#tbody_personas").html(html);
    return;
  }

  data.forEach(item => {

    html += `
            <tr>
                <td>${item.nombre ?? ''}</td>
                <td>${item.tipo_documento ?? ''}</td>
                <td>${item.num_documento ?? ''}</td>
                <td>${item.telefono ?? ''}</td>
                <td>${item.email ?? ''}</td>
                <td>
                  ${permissions.editar ? `<button class="btn btn-warning btn-xs" onclick="mostrar(${item.idpersona})"><i class="fas fa-edit"></i></button>` : ''}
                  ${permissions.historial ? `<button class="btn btn-info btn-xs" onclick="ListarReportesClientes(${item.idpersona})"><i class="fa fa-list"></i></button>` : ''}
                  ${permissions.puntuacion ? `<button class="btn btn-info btn-xs" onclick="ScoreCrediticioCliente(${item.idpersona})"><i class="fa fa-star"></i></button>` : ''}
                  ${permissions.eliminar ? `<button class="btn btn-danger btn-xs" onclick="eliminar(${item.idpersona})"><i class="fa fa-trash"></i></button>` : ''}
                </td>
            </tr>
        `;

  });


  $("#tbody_personas").html(html);
}

//Función Listar


listarPersonas = new FluentPaginator({
  url: "controladores/persona.php?op=listarc",
  tableBody: "#tbody_personas",
  renderTabla: pintarPersonas
});


//Función cancelarform
function cancelarform() {
  limpiar();
}

//Función para desactivar registros
function eliminar(idpersona) {
  Swal.fire({
    title: "Eliminar?",
    text: "¿Está seguro Que Desea Eliminar el Cliente?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "controladores/persona.php?op=eliminar",
        { idpersona: idpersona },
        function (response) {
          const data = JSON.parse(response);
          if (!data.success) {
            Swal.fire({
              title: "Cliente",
              icon: "error",
              text: data.message,
            });
            return;
          }
          Swal.fire({
            title: "Cliente",
            icon: "success",
            text: data.message,
          });
          $("#myModal").modal("hide");
          listarPersonas.load();
        }
      );
    } else {
      Swal.fire("Aviso!", "Se Cancelo la eliminación del Cliente", "info");
    }
  });
}

// Función para cerrar el modal
function cerrarModal() {
  $("#listarReporteCliente").modal("hide");
}

function imprimir() {
  // Obtener el contenido de las tablas generadas
  var data_compras = $("#data_compras").html();
  var data_cuentas_pagar = $("#data_cuentas_pagar").html();
  var data_proveedor = $("#data_proveedor").html();
  var data_proveedor_pagar = $("#data_proveedor_pagar").html();

  // Obtener las fechas de inicio y fin seleccionadas
  var fecha_inicio = $("#fecha_inicio").val();
  var fecha_fin = $("#fecha_fin").val();

  // Crear un contenido HTML para la impresión
  var contenido = `
        <html>
        <head>
            <title>Reporte de Clientes</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 40px; 
                    color: #333; 
                    background-color: #f4f4f4; 
                }
                h2, h3 { 
                    text-align: center; 
                    color: #333; 
                    font-weight: bold; /* Encabezados en negrita */
                }
                p { 
                    font-size: 14px; 
                    margin: 10px 0; 
                }
                .table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-bottom: 30px; 
                }
                .table, .table th, .table td { 
                    border: 1px solid #ddd; 
                }
                .table th, .table td { 
                    padding: 6px 10px; /* Ajuste del padding para mejor ajuste al contenido */
                    text-align: left; 
                    font-size: 14px; 
                }
                .table th {  
                    font-weight: bold; /* Encabezados en negrita */
                }
                .table tbody tr:nth-child(even) { 
                    background-color: #f9f9f9; 
                }
                .table tbody tr:nth-child(odd) { 
                    background-color: #ffffff; 
                }
                .table td { 
                    color: #555; 
                }
                .table td, .table th { 
                    font-size: 10px; 
                }
                .section-title {
                    text-align: center;
                    font-size: 18px;
                    color: #2e6da4;
                    margin-top: 30px;
                }
            </style>
        </head>
        <body>
			<div class="row">
				<div class="col-md-12">
					<h2>Reporte de Historial de Compras y Cuentas</h2>
					<p><strong>Fecha de Inicio:</strong> ${fecha_inicio}</p>
					<p><strong>Fecha de Fin:</strong> ${fecha_fin}</p>
				</div>
			</div>

            <div class="section-title">
                <h3 style="color: green;">Historial de Compras y Cuentas por Cobrar como Cliente</h3>
            </div>
            <table class="table">
                ${data_compras}
                ${data_cuentas_pagar}
            </table>

            <div class="section-title">
                <h3 style="color: red;">Historial de Compras y Cuentas por Pagar como Proveedor</h3>
            </div>
            <table class="table">
                ${data_proveedor}
                ${data_proveedor_pagar}
            </table>
        </body>
        </html>
    `;

  // Crear una ventana para la impresión
  var ventana = window.open("", "", "width=800,height=600");
  ventana.document.write(contenido);
  ventana.document.close();

  // Esperar que el contenido se cargue y luego ejecutar la impresión
  ventana.onload = function () {
    ventana.print();
    ventana.close();
  };
}

$("#fecha_inicio").change(function () {
  var clientes01 = $("#clientesreporte").val();
  var fecha_inicio = $("#fecha_inicio").val();
  var fecha_fin = $("#fecha_fin").val();
  ListarReportesClientes(clientes01, fecha_inicio, fecha_fin);
});

$("#fecha_fin").change(function () {
  var clientes01 = $("#clientesreporte").val();
  var fecha_inicio = $("#fecha_inicio").val();
  var fecha_fin = $("#fecha_fin").val();
  ListarReportesClientes(clientes01, fecha_inicio, fecha_fin);
});

function ListarReportesClientes(idcliente) {

  $("#data_compras").html("");
  $("#data_cuentas_pagar").html("");
  $("#data_proveedor").html("");
  $("#data_proveedor_pagar").html("");
  $("#clientesreporte").val(idcliente);

  var fecha_inicio = $("#fecha_inicio").val();
  var fecha_fin = $("#fecha_fin").val();

  $("#listarReporteCliente").modal("show");

  $.ajax({
    url:
      "controladores/venta.php?op=listarhistorialcliente&idcliente=" +
      idcliente +
      "&fecha_inicio=" +
      fecha_inicio +
      "&fecha_fin=" +
      fecha_fin,
    type: "GET",
    contentType: false,
    processData: false,

    success: function (response) {

      const data = response;
      const symbol = data.symbol || "";

      function money(value) {
        return symbol + Number(value || 0).toFixed(2);
      }

      function number(value) {
        return Number(value || 0);
      }

      function empty(message) {
        return `
          <div class="text-center py-4 text-muted">
            <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
            <span>${message}</span>
          </div>
        `;
      }

      function badge(text, type) {
        return `<span class="badge badge-${type}">${text}</span>`;
      }

      /* =========================================================
         1. VENTAS A CLIENTE
      ========================================================= */

      const ventas = data.ventas || [];

      let totalVenta = 0;
      let totalPagado = 0;
      let totalInteres = 0;
      let totalSaldo = 0;

      let htmlVentas = `
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead class="thead-light">
              <tr>
                <th style="width:110px;">Fecha</th>
                <th style="width:130px;">Comprobante</th>
                <th>Detalle</th>
                <th class="text-right">Venta</th>
                <th class="text-right">Inicial</th>
                <th class="text-right">Interés</th>
                <th class="text-right">Total</th>
                <th class="text-center">Cuotas</th>
              </tr>
            </thead>
            <tbody>
      `;

      if (ventas.length === 0) {

        htmlVentas += `
          <tr>
            <td colspan="8">
              ${empty("No existen ventas a crédito en el período seleccionado")}
            </td>
          </tr>
        `;

      } else {

        $.each(ventas, function (i, venta) {

          const ventaSinInteres = number(venta.venta_sin_interes);
          const pagado = number(venta.totalrecibido);
          const interes = number(venta.interes);
          const total = number(venta.total_venta);
          const saldo = total - pagado;

          totalVenta += ventaSinInteres;
          totalPagado += pagado;
          totalInteres += interes;
          totalSaldo += saldo;

          const detalle = venta.detalle || [];

          let detalleHtml = `
            <div class="bg-light border rounded p-2 mt-2">
              <div class="d-flex align-items-center mb-2">
                <i class="fa fa-shopping-cart text-primary mr-2"></i>
                <strong class="text-dark">Detalle de productos</strong>
              </div>

              <div class="table-responsive">
                <table class="table table-sm table-bordered bg-white mb-0">
                  <thead>
                    <tr class="text-muted">
                      <th>Producto</th>
                      <th class="text-center" style="width:140px;">Cantidad</th>
                      <th class="text-right" style="width:130px;">Precio</th>
                    </tr>
                  </thead>
                  <tbody>
          `;

          if (detalle.length === 0) {

            detalleHtml += `
              <tr>
                <td colspan="3" class="text-center text-muted">
                  Sin detalle de productos
                </td>
              </tr>
            `;

          } else {

            $.each(detalle, function (a, item) {

              detalleHtml += `
                <tr>
                  <td>
                    <span class="text-primary font-weight-bold">
                      ${item.nombre_producto}
                    </span>
                  </td>
                  <td class="text-center">
                    ${item.cantidad}
                  </td>
                  <td class="text-right">
                    ${item.precio_venta}
                  </td>
                </tr>
              `;

            });
          }

          detalleHtml += `
                  </tbody>
                </table>
              </div>
            </div>
          `;

          htmlVentas += `
            <tr>
              <td>
                <small class="text-muted">${venta.fecha_hora}</small>
              </td>

              <td>
                ${badge(venta.serie_comprobante, "primary")}
              </td>

              <td>
                <button
                  type="button"
                  class="btn btn-link btn-sm p-0"
                  data-toggle="collapse"
                  data-target="#detalleVenta${i}"
                  aria-expanded="false"
                >
                  <i class="fa fa-list mr-1"></i>
                  Ver productos
                </button>

                <div
                  id="detalleVenta${i}"
                  class="collapse"
                >
                  ${detalleHtml}
                </div>
              </td>

              <td class="text-right">
                ${money(ventaSinInteres)}
              </td>

              <td class="text-right text-success">
                ${money(pagado)}
              </td>

              <td class="text-right">
                ${money(interes)}
              </td>

              <td class="text-right font-weight-bold">
                ${money(total)}
              </td>

              <td class="text-center">
                ${venta.meses || 0}
              </td>
            </tr>
          `;
        });
      }

      htmlVentas += `
            </tbody>

            <tfoot>
              <tr class="bg-light font-weight-bold">
                <td colspan="3" class="text-right">
                  TOTAL
                </td>

                <td class="text-right">
                  ${money(totalVenta)}
                </td>

                <td class="text-right text-success">
                  ${money(totalPagado)}
                </td>

                <td class="text-right">
                  ${money(totalInteres)}
                </td>

                <td class="text-right text-danger">
                  ${money(totalVenta + totalInteres)}
                </td>

                <td></td>
              </tr>
            </tfoot>

          </table>
        </div>
      `;

      $("#data_compras").html(htmlVentas);


      /* =========================================================
         2. CUENTAS POR COBRAR
      ========================================================= */

      const cuentasxcobrar = data.cuentasxcobrar || [];

      let totalDeuda = 0;
      let totalInteresCobrar = 0;
      let totalMora = 0;
      let totalDescuento = 0;
      let totalAbonado = 0;

      let htmlCobrar = `
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">

            <thead class="thead-light">
              <tr>
                <th style="width:120px;">Fecha</th>
                <th>Cuenta</th>
                <th class="text-right">Deuda</th>
                <th class="text-right">Interés</th>
                <th class="text-right">Mora</th>
                <th class="text-right">Descuento</th>
                <th class="text-right">Abonado</th>
                <th class="text-center" style="width:90px;">Detalle</th>
              </tr>
            </thead>

            <tbody>
      `;

      if (cuentasxcobrar.length === 0) {

        htmlCobrar += `
          <tr>
            <td colspan="8">
              ${empty("No existen cuentas por cobrar en el período seleccionado")}
            </td>
          </tr>
        `;

      } else {

        $.each(cuentasxcobrar, function (i, cuenta) {

          const deuda = number(cuenta.deudatotal);
          const interes = number(cuenta.interes);
          const mora = number(cuenta.mora_pagada);
          const descuento = number(cuenta.descuento);
          const abonado = number(cuenta.abonototal);

          totalDeuda += deuda;
          totalInteresCobrar += interes;
          totalMora += mora;
          totalDescuento += descuento;
          totalAbonado += abonado;

          const detalle = cuenta.detalle || [];

          let detalleHtml = `
            <div class="bg-light border rounded p-2 mt-2">

              <div class="d-flex align-items-center mb-2">
                <i class="fa fa-money text-success mr-2"></i>
                <strong>Detalle de amortizaciones</strong>
              </div>

              <div class="table-responsive">
                <table class="table table-sm table-bordered bg-white mb-0">

                  <thead>
                    <tr>
                      <th>Concepto</th>
                      <th class="text-right">Efectivo</th>
                      <th class="text-right">Transferencia</th>
                      <th class="text-right">Total</th>
                    </tr>
                  </thead>

                  <tbody>
          `;

          if (detalle.length === 0) {

            detalleHtml += `
              <tr>
                <td colspan="4" class="text-center text-muted">
                  Sin amortizaciones registradas
                </td>
              </tr>
            `;

          } else {

            $.each(detalle, function (a, item) {

              detalleHtml += `
                <tr>
                  <td>${item.tipo}</td>
                  <td class="text-right">
                    ${money(item.montopagado)}
                  </td>
                  <td class="text-right">
                    ${money(item.montotarjeta)}
                  </td>
                  <td class="text-right font-weight-bold">
                    ${money(item.total)}
                  </td>
                </tr>
              `;

            });
          }

          detalleHtml += `
                  </tbody>
                </table>
              </div>
            </div>
          `;

          htmlCobrar += `
            <tr>

              <td>
                <small>${cuenta.fecha_hora}</small>
              </td>

              <td>
                ${badge(cuenta.tipo, "info")}

                ${cuenta.dias_mora
              ? `<i
                        class="fa fa-info-circle text-danger ml-1"
                        data-toggle="popover"
                        data-trigger="hover"
                        data-placement="top"
                        data-content="${cuenta.dias_mora}"
                        style="cursor:pointer;"
                      ></i>`
              : ""
            }
              </td>

              <td class="text-right font-weight-bold">
                ${money(deuda)}
              </td>

              <td class="text-right">
                ${money(interes)}
              </td>

              <td class="text-right ${mora > 0 ? "text-danger font-weight-bold" : ""
            }">
                ${money(mora)}
              </td>

              <td class="text-right text-success">
                ${money(descuento)}
              </td>

              <td class="text-right font-weight-bold">
                ${money(abonado)}
              </td>

              <td class="text-center">

                <button
                  type="button"
                  class="btn btn-outline-primary btn-sm"
                  data-toggle="collapse"
                  data-target="#detalleCobrar${i}"
                  title="Ver amortizaciones"
                >
                  <i class="fa fa-eye"></i>
                </button>

              </td>

            </tr>

            <tr
              id="detalleCobrar${i}"
              class="collapse"
            >
              <td colspan="8">
                ${detalleHtml}
              </td>
            </tr>
          `;
        });
      }

      htmlCobrar += `
            </tbody>

            <tfoot>
              <tr class="bg-light font-weight-bold">

                <td colspan="2" class="text-right">
                  TOTAL
                </td>

                <td class="text-right text-danger">
                  ${money(totalDeuda)}
                </td>

                <td class="text-right">
                  ${money(totalInteresCobrar)}
                </td>

                <td class="text-right">
                  ${money(totalMora)}
                </td>

                <td class="text-right text-success">
                  ${money(totalDescuento)}
                </td>

                <td class="text-right">
                  ${money(totalAbonado)}
                </td>

                <td></td>

              </tr>
            </tfoot>

          </table>
        </div>
      `;

      $("#data_cuentas_pagar").html(htmlCobrar);


      /* =========================================================
         3. COMPRAS A PROVEEDORES
      ========================================================= */

      const compras = data.compras || [];

      let totalCompra = 0;
      let totalCompraPagado = 0;
      let totalCompraInteres = 0;

      let htmlCompras = `
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">

            <thead class="thead-light">
              <tr>
                <th style="width:120px;">Fecha</th>
                <th style="width:140px;">Comprobante</th>
                <th>Detalle</th>
                <th class="text-right">Importe</th>
                <th class="text-right">Interés</th>
                <th class="text-right">Total</th>
                <th class="text-center">Cuotas</th>
              </tr>
            </thead>

            <tbody>
      `;

      if (compras.length === 0) {

        htmlCompras += `
          <tr>
            <td colspan="7">
              ${empty("No existen compras a crédito en el período seleccionado")}
            </td>
          </tr>
        `;

      } else {

        $.each(compras, function (i, compra) {

          const importe = number(compra.totalrecibido);
          const interes = number(compra.interes);
          const total = number(compra.total_venta);

          totalCompra += total;
          totalCompraPagado += importe;
          totalCompraInteres += interes;

          const detalle = compra.detalle || [];

          let detalleHtml = `
            <div class="bg-light border rounded p-2 mt-2">

              <div class="d-flex align-items-center mb-2">
                <i class="fa fa-shopping-basket text-primary mr-2"></i>
                <strong>Detalle de productos</strong>
              </div>

              <div class="table-responsive">
                <table class="table table-sm table-bordered bg-white mb-0">

                  <thead>
                    <tr>
                      <th>Producto</th>
                      <th class="text-center">Cantidad</th>
                      <th class="text-right">Precio</th>
                    </tr>
                  </thead>

                  <tbody>
          `;

          if (detalle.length === 0) {

            detalleHtml += `
              <tr>
                <td colspan="3" class="text-center text-muted">
                  Sin detalle de productos
                </td>
              </tr>
            `;

          } else {

            $.each(detalle, function (a, item) {

              detalleHtml += `
                <tr>
                  <td>${item.nombre_producto}</td>
                  <td class="text-center">${item.cantidad}</td>
                  <td class="text-right">
                    ${symbol}${Number(item.precio_venta || 0).toFixed(2)}
                  </td>
                </tr>
              `;

            });
          }

          detalleHtml += `
                  </tbody>
                </table>
              </div>

            </div>
          `;

          htmlCompras += `
            <tr>

              <td>
                <small>${compra.fecha_hora}</small>
              </td>

              <td>
                ${badge(compra.serie_comprobante, "secondary")}
              </td>

              <td>

                <button
                  type="button"
                  class="btn btn-link btn-sm p-0"
                  data-toggle="collapse"
                  data-target="#detalleCompra${i}"
                >
                  <i class="fa fa-list mr-1"></i>
                  Ver productos
                </button>

                <div
                  id="detalleCompra${i}"
                  class="collapse"
                >
                  ${detalleHtml}
                </div>

              </td>

              <td class="text-right">
                ${money(importe)}
              </td>

              <td class="text-right">
                ${money(interes)}
              </td>

              <td class="text-right font-weight-bold">
                ${money(total)}
              </td>

              <td class="text-center">
                ${compra.meses || 0}
              </td>

            </tr>
          `;
        });
      }

      htmlCompras += `
            </tbody>

            <tfoot>
              <tr class="bg-light font-weight-bold">

                <td colspan="3" class="text-right">
                  TOTAL
                </td>

                <td class="text-right">
                  ${money(totalCompraPagado)}
                </td>

                <td class="text-right">
                  ${money(totalCompraInteres)}
                </td>

                <td class="text-right text-danger">
                  ${money(totalCompra)}
                </td>

                <td></td>

              </tr>
            </tfoot>

          </table>
        </div>
      `;

      $("#data_proveedor").html(htmlCompras);


      /* =========================================================
         4. CUENTAS POR PAGAR
      ========================================================= */

      const cuentasxpagar = data.cuentasxpagar || [];

      let totalPagar = 0;
      let totalInteresPagar = 0;
      let totalPagadoPagar = 0;

      let htmlPagar = `
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">

            <thead class="thead-light">
              <tr>
                <th style="width:120px;">Fecha</th>
                <th>Cuenta</th>
                <th class="text-right">Deuda</th>
                <th class="text-right">Interés</th>
                <th class="text-right">Abonado</th>
                <th class="text-right">Pagado</th>
                <th class="text-center">Detalle</th>
              </tr>
            </thead>

            <tbody>
      `;

      if (cuentasxpagar.length === 0) {

        htmlPagar += `
          <tr>
            <td colspan="7">
              ${empty("No existen cuentas por pagar en el período seleccionado")}
            </td>
          </tr>
        `;

      } else {

        $.each(cuentasxpagar, function (i, cuenta) {

          const deuda = number(cuenta.deudatotal);
          const interes = number(cuenta.interes);
          const abonado = number(cuenta.abonototal);

          totalPagar += deuda;
          totalInteresPagar += interes;

          const detalle = cuenta.detalle || [];

          let detalleHtml = `
            <div class="bg-light border rounded p-2 mt-2">

              <div class="d-flex align-items-center mb-2">
                <i class="fa fa-money text-success mr-2"></i>
                <strong>Detalle de pagos</strong>
              </div>

              <div class="table-responsive">
                <table class="table table-sm table-bordered bg-white mb-0">

                  <thead>
                    <tr>
                      <th>Concepto</th>
                      <th class="text-right">Monto pagado</th>
                    </tr>
                  </thead>

                  <tbody>
          `;

          if (detalle.length === 0) {

            detalleHtml += `
              <tr>
                <td colspan="2" class="text-center text-muted">
                  Sin pagos registrados
                </td>
              </tr>
            `;

          } else {

            $.each(detalle, function (a, item) {

              const monto = number(item.montopagado);

              totalPagadoPagar += monto;

              detalleHtml += `
                <tr>
                  <td>${item.tipo}</td>
                  <td class="text-right font-weight-bold">
                    ${money(monto)}
                  </td>
                </tr>
              `;

            });
          }

          detalleHtml += `
                  </tbody>
                </table>
              </div>

            </div>
          `;

          htmlPagar += `
            <tr>

              <td>
                <small>${cuenta.fecha_hora}</small>
              </td>

              <td>
                ${badge(cuenta.tipo, "warning")}
              </td>

              <td class="text-right font-weight-bold">
                ${money(deuda)}
              </td>

              <td class="text-right">
                ${money(interes)}
              </td>

              <td class="text-right">
                ${money(abonado)}
              </td>

              <td class="text-right text-success font-weight-bold">
                ${money(
            detalle.reduce(function (total, item) {
              return total + number(item.montopagado);
            }, 0)
          )}
              </td>

              <td class="text-center">

                <button
                  type="button"
                  class="btn btn-outline-primary btn-sm"
                  data-toggle="collapse"
                  data-target="#detallePagar${i}"
                >
                  <i class="fa fa-eye"></i>
                </button>

              </td>

            </tr>

            <tr
              id="detallePagar${i}"
              class="collapse"
            >
              <td colspan="7">
                ${detalleHtml}
              </td>
            </tr>
          `;
        });
      }

      htmlPagar += `
            </tbody>

            <tfoot>
              <tr class="bg-light font-weight-bold">

                <td colspan="2" class="text-right">
                  TOTAL
                </td>

                <td class="text-right text-danger">
                  ${money(totalPagar)}
                </td>

                <td class="text-right">
                  ${money(totalInteresPagar)}
                </td>

                <td></td>

                <td class="text-right text-success">
                  ${money(totalPagadoPagar)}
                </td>

                <td></td>

              </tr>
            </tfoot>

          </table>
        </div>
      `;

      $("#data_proveedor_pagar").html(htmlPagar);


      /* =========================================================
         POPOVERS
      ========================================================= */

      $('[data-toggle="popover"]').popover({
        trigger: "hover",
        container: "body"
      });

    },

    error: function () {

      $("#data_compras").html(
        empty("No se pudo obtener el historial del cliente")
      );

      $("#data_cuentas_pagar").html("");
      $("#data_proveedor").html("");
      $("#data_proveedor_pagar").html("");

    }
  });
}


function ScoreCrediticioCliente(idcliente) {

  $("#scoreCliente").modal("show");

  $("#scoreNumero").html(
    '<i class="fa fa-spinner fa-spin"></i>'
  );

  $.ajax({

    url:
      "controladores/persona.php?op=scorecrediticiocliente&idcliente="
      + idcliente,

    type: "GET",

    success: function (r) {

      let data = JSON.parse(r);

      let score = parseInt(data.score);

      $("#scoreNumero").text(score);

      $("#scoreBar")
        .css("width", score + "%")
        .text(score + " / 100");

      $("#totalCreditos").text(
        data.total_creditos
      );

      $("#cuotasVencidas").text(
        data.cuotas_pagadas_tarde
      );

      $("#diasAtraso").text(
        data.dias_atraso_historico
      );

      $("#moraTotal").text(
        "S/ " + parseFloat(data.mora_total)
          .toFixed(2)
      );

      $("#porcentajePagado").text(
        data.porcentaje_pagado + "%"
      );

      let badge = "";
      let recomendacion = "";
      let colorBar = "";

      switch (data.riesgo) {

        case "BAJO":

          badge =
            '<span class="badge badge-success p-2">RIESGO BAJO</span>';

          colorBar = "bg-success";

          recomendacion =
            "Cliente con excelente comportamiento de pago.";

          break;

        case "MEDIO":

          badge =
            '<span class="badge badge-warning p-2">RIESGO MEDIO</span>';

          colorBar = "bg-warning";

          recomendacion =
            "Se recomienda seguimiento preventivo.";

          break;

        case "ALTO":

          badge =
            '<span class="badge badge-danger p-2">RIESGO ALTO</span>';

          colorBar = "bg-danger";

          recomendacion =
            "Cliente requiere gestión de cobranza.";

          break;

        case "CRITICO":

          badge =
            '<span class="badge badge-dark p-2">RIESGO CRÍTICO</span>';

          colorBar = "bg-dark";

          recomendacion =
            "No se recomienda otorgar nuevos créditos.";

          break;

        default:

          badge =
            '<span class="badge badge-dark p-2">SIN HISTORIAL</span>';

          colorBar = "bg-dark";

          recomendacion =
            "El cliente no tiene historial de créditos.";

          break;
      }

      $("#scoreBar")
        .removeClass(
          "bg-success bg-warning bg-danger bg-dark"
        )
        .addClass(colorBar);

      $("#riesgoBadge").html(badge);

      $("#recomendacionScore").html(
        recomendacion
      );

    }

  });

}

init();
