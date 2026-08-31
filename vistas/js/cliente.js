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
                  ${permissions.editar ? `<button class="btn btn-warning btn-xs" onclick="mostrar(${item.idpersona})"><i class="fas fa-edit"></i></button>`:''}
                  ${permissions.historial ? `<button class="btn btn-info btn-xs" onclick="ListarReportesClientes(${item.idpersona})"><i class="fa fa-list"></i></button>`:''}
                  ${permissions.puntuacion ? `<button class="btn btn-info btn-xs" onclick="ScoreCrediticioCliente(${item.idpersona})"><i class="fa fa-star"></i></button>`:''}
                  ${permissions.eliminar ? `<button class="btn btn-danger btn-xs" onclick="eliminar(${item.idpersona})"><i class="fa fa-trash"></i></button>`:''}
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
    success: function (datos) {
      var data = JSON.parse(datos);
      var symbol = data.symbol;
      // Tabla de Compras
      var ventas = data.ventas;
      var total_sin_interes = 0;
      var total = 0;
      var pagado = 0;
      var interes = 0;
      var html = `
			<table class="table table-bordered table-striped table-hover table-sm">
			  <thead>
			    <tr>
			      <th>Fecha</th>
			      <th>Recibo</th>
			      <th>Detalle</th>
			      <th>Valor venta</th>
			      <th>Inicial</th>
			      <th>Interes</th>
			      <th>Total</th>
			      <th>Cuotas</th>
			    </tr>
			  </thead>
			  <tbody>`;

      $.each(ventas, function (i, item) {
        total_sin_interes += parseFloat(ventas[i].venta_sin_interes);
        total += parseFloat(ventas[i].total_venta);
        interes += ventas[i].interes;
        pagado += parseFloat(ventas[i].totalrecibido);
        html +=
          `<tr>
					<td>` +
          ventas[i].fecha_hora +
          `</td>
					<td>` +
          ventas[i].serie_comprobante +
          `</td>
					<td></td>
          <td>` +
          symbol +
          ventas[i].venta_sin_interes +
          `</td>
					<td>` +
          symbol +
          ventas[i].totalrecibido +
          `</td>
					<td>` +
          symbol +
          ventas[i].interes +
          `</td>
					<td>` +
          symbol +
          ventas[i].total_venta +
          `</td>
					<td>` +
          ventas[i].meses +
          `</td>
				</tr>`;

        var detalle = ventas[i].detalle;
        html += `<tr>
					<td colspan="2" ></td>
					<td style="font-weight:bold !important">Producto</td>
					<td style="font-weight:bold !important">Cantidad</td>
					<td style="font-weight:bold !important">Precio</td>
				</tr>`;

        $.each(detalle, function (a, item) {
          html +=
            `<tr>
						<td colspan="2"></td>
						<td>` +
            detalle[a].nombre_producto +
            `</td>
						<td>` +
            detalle[a].cantidad +
            `</td>
						<td>` +
            detalle[a].precio_venta +
            `</td>
					</tr>`;
        });
      });

      html +=
        `<tr>
				<td style="color: blue; text-align: right;" colspan="3">TOTAL</td>
        <td style="color: blue">` +
        symbol +
        total_sin_interes +
        `</td>
				<td style="color: blue">` +
        symbol +
        pagado +
        `</td>
				<td style="color: blue">` +
        symbol +
        interes +
        `</td>
				<td style="color: red">` +
        symbol +
        total +
        `</td>
				<td></td>
			</tr>
			  </tbody>
			</table>`;
      $("#data_compras").html(html);

      // Tabla de Cuentas por Cobrar
      var cuentasxcobrar = data.cuentasxcobrar;
      var totalc = 0;
      var interesc = 0;
      var morac = 0;
      var descuentoc = 0;

      var recibidoc = 0;
      var htmlform = `
			<table class="table table-bordered table-hover table-sm">
			  <thead>
			    <tr>
			      <th>Fecha</th>
			      <th>Tipo</th>
			      <th>Deuda Total</th>
			      <th>Interes</th>
			      <th>Mora</th>
			      <th>Descuento</th>
			      <th>Abono Total</th>
			    </tr>
			  </thead>
			  <tbody>`;

      $.each(cuentasxcobrar, function (i, item) {
        totalc += parseFloat(cuentasxcobrar[i].deudatotal);
        interesc += parseFloat(cuentasxcobrar[i].interes);
        morac += parseFloat(cuentasxcobrar[i].mora_pagada);
        descuentoc += parseFloat(cuentasxcobrar[i].descuento);
        recibidoc += parseFloat(cuentasxcobrar[i].abonototal);
        htmlform +=
          `<tr style="background: #dee2e6">
					<td>` +
          cuentasxcobrar[i].fecha_hora +
          `</td>
					<td>` +
          cuentasxcobrar[i].tipo +
          `</td>
					<td>` +
          symbol +
          Number(cuentasxcobrar[i].deudatotal).toFixed(2) +
          `</td>
					<td>` +
          symbol +
          Number(cuentasxcobrar[i].interes).toFixed(2) +
          `</td>
          <td>
    ${symbol}${Number(cuentasxcobrar[i].mora_pagada).toFixed(2)}
    ${cuentasxcobrar[i].dias_mora
            ? `<i class="fa fa-info-circle text-primary ml-1"
                data-toggle="popover"
                data-trigger="hover"
                data-placement="top"
                data-content="${cuentasxcobrar[i].dias_mora}"
                style="cursor:pointer;"></i>`
            : ''
          }
</td>
          <td>` +
          symbol +
          Number(cuentasxcobrar[i].descuento).toFixed(2) +
          `</td>
					<td>` +
          symbol +
          Number(cuentasxcobrar[i].abonototal).toFixed(2) +
          `</td>
				</tr>`;

        var detallecuentasxcobrar = cuentasxcobrar[i].detalle;
        htmlform += `<tr>
              <th colspan="2"></th>
              <th colspan="2">Detalle</th>
              <th>Efectivo</th>
              <th>Transferencia</th>
              <th>Total abono</th>
            </tr>`;

        $.each(detallecuentasxcobrar, function (a, item) {
          htmlform +=
            `<tr>
						<td colspan="2"></td>
						<td colspan="2">` +
            detallecuentasxcobrar[a].tipo +
            `</td>
            <td>` +
            symbol +
            detallecuentasxcobrar[a].montopagado +
            `</td>
            <td>` +
            symbol +
            detallecuentasxcobrar[a].montotarjeta +
            `</td>
						<td>` +
            symbol +
            detallecuentasxcobrar[a].total +
            `</td>
					</tr>`;
        });
      });

      htmlform +=
        `<tr>
				<td style="color: blue; text-align: right;" colspan="2">TOTAL</td>
				<td style="color: red">` +
        symbol +
        totalc.toFixed(2) +
        `</td>
				<td style="color: blue">` +
        symbol +
        interesc.toFixed(2) +
        `</td>
				<td style="color: blue">` +
        symbol +
        morac.toFixed(2) +
        `</td>
        <td style="color: blue">` +
        symbol +
        descuentoc.toFixed(2) +
        `</td>
				<td style="color: blue">` +
        symbol +
        recibidoc.toFixed(2) +
        `</td>
			</tr>
			  </tbody>
			</table>`;
      $("#data_cuentas_pagar").html(htmlform);

      // Tabla de Proveedores
      var ventas = data.compras;
      var total = 0;
      var pagado = 0;
      var interes = 0;
      var html = `
			<table class="table table-bordered  table-sm">
			  <thead>
			    <tr>
			      <th>Fecha</th>
			      <th>Recibo</th>
			      <th>Detalle</th>
			      <th>Importe</th>
			      <th>Interes</th>
			      <th>Total</th>
			      <th>Mes</th>
			    </tr>
			  </thead>
			  <tbody>`;

      $.each(ventas, function (i, item) {
        total += parseFloat(ventas[i].total_venta);
        interes += ventas[i].interes;
        pagado += parseFloat(ventas[i].totalrecibido);
        html +=
          `<tr>
					<td>` +
          ventas[i].fecha_hora +
          `</td>
					<td>` +
          ventas[i].serie_comprobante +
          `</td>
					<td></td>
					<td>` +
          symbol +
          ventas[i].totalrecibido +
          `</td>
					<td>` +
          symbol +
          ventas[i].interes +
          `</td>
					<td>` +
          symbol +
          ventas[i].total_venta +
          `</td>
					<td>` +
          ventas[i].meses +
          `</td>
				</tr>`;

        var detalle = ventas[i].detalle;
        html += `<tr>
					<td colspan="2"></td>
					<td style="font-weight:bold !important">Producto</td>
					<td style="font-weight:bold !important">Cantidad</td>
					<td style="font-weight:bold !important">Precio</td>
				</tr>`;

        $.each(detalle, function (a, item) {
          html +=
            `<tr>
						<td colspan="2"></td>
						<td>` +
            detalle[a].nombre_producto +
            `</td>
						<td>` +
            detalle[a].cantidad +
            `</td>
						<td>` +
            symbol +
            detalle[a].precio_venta +
            `</td>
					</tr>`;
        });
      });

      html +=
        `<tr>
				<td colspan="2"></td>
				<td style="color: blue; text-align: right;">TOTAL</td>
				<td style="color: blue">` +
        symbol +
        pagado +
        `</td>
				<td style="color: blue">` +
        symbol +
        interes +
        `</td>
				<td style="color: red">` +
        symbol +
        total +
        `</td>
				<td></td>
			</tr>
			  </tbody>
			</table>`;
      $("#data_proveedor").html(html);

      // Tabla de Cuentas por Pagar
      var cuentasxcobrar = data.cuentasxpagar;
      var totalc = 0;
      var interesc = 0;
      var recibidoc = 0;
      var htmlform = `
			<table class="table table-bordered table-striped table-hover table-sm">
			  <thead>
			    <tr>
			      <th>Fecha</th>
			      <th>Tipo</th>
			      <th>Deuda Total</th>
			      <th>Interes</th>
			      <th>Abono Total</th>
			      <th>Monto Pagado</th>
			    </tr>
			  </thead>
			  <tbody>`;

      $.each(cuentasxcobrar, function (i, item) {
        totalc += parseFloat(cuentasxcobrar[i].deudatotal);
        interesc += parseFloat(cuentasxcobrar[i].interes);
        htmlform +=
          `<tr>
					<td>` +
          cuentasxcobrar[i].fecha_hora +
          `</td>
					<td>` +
          cuentasxcobrar[i].tipo +
          `</td>
					<td>` +
          symbol +
          Number(cuentasxcobrar[i].deudatotal).toFixed(2) +
          `</td>
					<td>` +
          symbol +
          Number(cuentasxcobrar[i].interes).toFixed(2) +
          `</td>
					<td>` +
          symbol +
          Number(cuentasxcobrar[i].abonototal).toFixed(2) +
          `</td>
					<td>` +
          symbol +
          0 +
          `</td>
				</tr>`;

        var detallecuentasxcobrar = cuentasxcobrar[i].detalle;
        $.each(detallecuentasxcobrar, function (a, item) {
          recibidoc += parseFloat(detallecuentasxcobrar[a].montopagado);
          htmlform +=
            `<tr>
						<td colspan="2"></td>
						<td>` +
            detallecuentasxcobrar[a].tipo +
            `</td>
						<td></td>
						<td></td>
						<td>` +
            symbol +
            detallecuentasxcobrar[a].montopagado +
            `</td>
					</tr>`;
        });
      });

      htmlform +=
        `<tr>
				<td style="color: blue; text-align: right;" colspan="2">TOTAL</td>
				<td style="color: red">` +
        symbol +
        totalc.toFixed(2) +
        `</td>
				<td style="color: blue">` +
        symbol +
        interesc.toFixed(2) +
        `</td>
				<td style="color: blue">` +
        symbol +
        recibidoc.toFixed(2) +
        `</td>
				<td style="color: blue">` +
        symbol +
        recibidoc.toFixed(2) +
        `</td>
			</tr>
			  </tbody>
			</table>`;
      $("#data_proveedor_pagar").html(htmlform);

      $('[data-toggle="popover"]').popover({
        trigger: 'hover',
        container: 'body'
      });

    },
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
