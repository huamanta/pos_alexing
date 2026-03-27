var status = true;
var sessionChecker = setInterval(() => {
  if (Boolean(status) === true) {
    loadSesionsApp();
  } else {
    clearInterval(sessionChecker); // Detiene el intervalo
  }
}, 3000);

function loadSesionsApp(params) {
  $.ajax({
    url: "controladores/negocio.php?op=sesions",
    type: "GET",
    contentType: false,
    processData: false,
    success: function (datos) {
      var data = JSON.parse(datos);
      if (!data.status) {
        sessionExpired();
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(
        "Error en la verificación de sesión:",
        textStatus,
        errorThrown,
      );
    },
  });
}

function sessionExpired() {
  if (!status) return; // evita doble ejecución

  status = false;
  clearInterval(sessionChecker);

  Swal.fire({
    title: "Sesión expirada",
    text: "Tu sesión ha expirado. Por favor, inicia sesión nuevamente.",
    icon: "warning",
    confirmButtonText: "OK",
    allowOutsideClick: false,
    allowEscapeKey: false,
  }).then(() => {
    $.ajax({
      url: "controladores/auth.php",
      type: "POST",
      complete: function () {
        window.location.href = "ingreso";
      },
    });
  });
}

var tabla;
var contador = 0;
var articuloAdd = "";
var cont = 0;
var detalles = 0;
toastr.options = {
  closeButton: true,
  progressBar: true,
  positionClass: "toast-bottom-right",
  timeOut: "3000",
};
function init() {
  verificarCaja();
  marcarImpuesto();
  listarClientes();
  $("#body").addClass("sidebar-collapse");
  $.post("controladores/venta.php?op=selectSucursal3", function (r) {
    $("#idsucursal2").html(r);
    $("#idsucursal2").select2("");
    $('#idsucursal2').prop('disabled', true);
  });
  $.post("controladores/usuario.php?op=selectEmpleado", function (r) {
    $("#idpersonal02").html(r);
    $("#idpersonal02").select2("");
  });
  $("#formularioMovimiento2").on("submit", function (e) {
    guardaryeditarmovimiento2(e);
  });
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal02").html(r);
    $("#idsucursal02").select2("");
  });

  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal").html(r);
    $("#idsucursal").select2("");

    documentosSucursal(); // <- Esto selecciona el comprobante correcto automáticamente
    agregarCards("Todos");
  });

  $("#idsucursal").change(documentosSucursal);
  $("#navPosActive").addClass("treeview active");
  $("#navPos").addClass("treeview menu-open");
  $("#navPos1").addClass("active");
  verificarConceptoMovimiento();

  /*document.getElementById("input-visa").readOnly = true;
  document.getElementById("input-yape").readOnly = true;
  document.getElementById("input-plin").readOnly = true;
  document.getElementById("input-mastercard").readOnly = true;
  document.getElementById("input-deposito").readOnly = true;*/
}

function documentosSucursal() {
  // Detectar el tipo de comprobante visible y seleccionar el primero
  let primerComprobante = $("#tipo_comprobante option:first").val();
  $("#tipo_comprobante").val(primerComprobante);

  // Marcar impuesto y cargar serie/número
  marcarImpuesto();
}

window.addEventListener(
  "keypress",
  function (event) {
    if (event.keyCode == 13) {
      event.preventDefault();
    }
  },
  false,
);

function verReportes() {
  $("#myModal2").modal("show");
  listar();
}

$("#estado").change(listar);
$("#idsucursal2").change(listar);

$("#idsucursal").change(function () {
  var categoria = $(this).val(); // Obtener el valor seleccionado
  agregarCards("Todos");
});

function verificarCaja() {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "controladores/pos.php?op=verificarCaja",
      type: "GET",
      data: "",
      success: function (data) {
        var data = JSON.parse(data);
        if (data.idcaja != 0) {
          $("#idcaja").val(data.idcaja);
          $("#pos-venta").removeAttr("hidden", "hidden");
          $("#pos-caja").attr("hidden", "hidden");
          agregarCards("Todos");
          agregarCategorias();
          listarCarrito();
          $.post("controladores/venta.php?op=selectSucursal3", function (r) {
            $("#idsucursal2").html(r);
          });
          $("#navbar-pos").html(`
            <li class="nav-item" style="margin-right: 10px;">
              <a class="nav-link" onclick="cerrarCaja()" title="Cerrar caja" style="background-color: #FA7A31; border-radius: 5px; color: white; font-weight:bold;" href="#" role="button">
                <i class="fas fa-arrow-left"></i>
              </a>
            </li>
            <li class="nav-item"  style="margin-right: 10px;">
              <a class="nav-link" title="Ver reportes" onclick="verReportes()" style="background-color: #FA7A31; border-radius: 5px; color: white; font-weight:bold;" href="#" role="button">
                <i class="fas fa-chart-bar"></i>
              </a>
            </li>
            <li class="nav-item"  style="margin-right: 10px;">
              <a class="nav-link" title="Crear Movimientos" onclick="CrearMov()" style="background-color: #FA7A31; border-radius: 5px; color: white; font-weight:bold;" href="#" role="button">
                <i class="fas fa-money-bill"></i>
              </a>
            </li>
            <li class="nav-item" style="margin-right: 10px;">
              <a class="nav-link" href="inicio" title="Ir al inicio" style="background-color: #FA7A31; border-radius: 5px; color: white; font-weight:bold;" role="button">
                <i class="fas fa-home"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
              </a>
            </li>
          `);
          resolve(true); // Caja abierta, resuelve la promesa con true
        } else {
          $("#pos-venta").attr("hidden", "hidden");
          $("#pos-caja").removeAttr("hidden", "hidden");
          listarCajas();
          $("#navbar-pos").html(`
            <li class="nav-item">
              <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
              </a>
            </li>
          `);
          resolve(false); // Caja cerrada, resuelve la promesa con false
        }
      },
      error: function (err) {
        reject(err); // Si hay un error, rechaza la promesa
      },
    });
  });
}

// Agrega el CSS para el efecto de agrandar los iconos al pasar el mouse sobre los enlaces
$("<style>")
  .text(
    ".nav-link i { " +
      "   transition: transform 0.3s ease; " +
      "} " +
      ".nav-link:hover i { " +
      "   transform: scale(1.2); " +
      "}",
  )
  .appendTo("head");

function listarCajas() {
  $.ajax({
    url: "controladores/pos.php?op=listarCajas",
    type: "GET",
    data: "",
    success: function (data) {
      var data = JSON.parse(data);
      var html = '<option value="" selected hidden>Seleccionar...</option>';
      $.each(data, function (i, item) {
        html +=
          '<option value="' +
          data[i].idcaja +
          '">' +
          data[i].nombre +
          "</option>";
      });
      $("#input-caja").html(html);
    },
  });
}

$("#form-apertura-caja").submit(function (e) {
  e.preventDefault();
  var data = new FormData(this);
  $.ajax({
    url: "controladores/pos.php?op=aperturarCaja",
    type: "POST",
    data: data,
    contentType: false,
    processData: false,
    success: function (data) {
      var data = JSON.parse(data);
      if (data.status == 1) {
        verificarCaja();
      }
    },
  });
});

function cerrarCaja() {
  verificarCarrito(function (carritoVacio) {
    if (carritoVacio) {
      var idcaja = $("#idcaja").val();
      $.ajax({
        url: "controladores/pos.php?op=showResumenCaja&idcaja=" + idcaja,
        type: "POST",
        data: "",
        contentType: false,
        processData: false,
        success: function (data) {
          let resumen = JSON.parse(data);

          // Resumen de ventas (efectivo vs no efectivo)
          let ventasHtml = `
            <b>VENTAS EFECTIVO:</b> S/. ${parseFloat(resumen.ventas_efectivo).toFixed(2)} (${resumen.cantidad_ventas_efectivo} ventas)<br>
            <b>VENTAS NO EFECTIVO:</b> S/. ${parseFloat(resumen.ventas_no_efectivo).toFixed(2)} (${resumen.cantidad_ventas_no_efectivo} ventas)<br>
            <b>VENTAS CRÉDITO (NO SUMAN):</b> S/. ${parseFloat(resumen.ventas_credito).toFixed(2)} (${resumen.cantidad_ventas_credito} ventas)<br>
          `;

          // Resumen de ingresos y egresos (efectivo vs no efectivo)
          let movimientosHtml = `
            <table style="width:100%;font-size:13px;">
              <tr><th></th><th>Efectivo</th><th>No efectivo</th></tr>
              <tr>
                <td><b>Ingresos</b></td>
                <td>S/. ${parseFloat(resumen.ingresos_efectivo).toFixed(2)}</td>
                <td>S/. ${parseFloat(resumen.ingresos_no_efectivo).toFixed(2)}</td>
              </tr>
            <tr>
              <td><b>Abonos</b></td>
              <td>S/. ${parseFloat(resumen.abonos_efectivo).toFixed(2)}</td>
              <td>S/. ${parseFloat(resumen.abonos_no_efectivo).toFixed(2)}</td>
            </tr>
              <tr>
                <td><b>Egresos</b></td>
                <td>S/. ${parseFloat(resumen.egresos_efectivo).toFixed(2)}</td>
                <td>S/. ${parseFloat(resumen.egresos_no_efectivo).toFixed(2)}</td>
              </tr>
            </table>
          `;

          // Mostrar resumen
          Swal.fire({
            title: "Cierre de caja",
            html: `<b>Efectivo apertura:</b> S/. ${parseFloat(
              resumen.efectivo_apertura,
            ).toFixed(2)}<br>
              ${ventasHtml}
              <b>Resumen de ingresos y egresos:</b><br>${movimientosHtml}
              <b>Efectivo final esperado (para cierre):</b> <span style="color: red; font-size:20px; font-weight:bold">S/. ${parseFloat(
                resumen.total_efectivo,
              ).toFixed(2)}</span>
              <hr>
              <label>Verifique la cantidad del sistema con la de su caja física</label>`,
            input: "number",
            input: "number",
            inputAttributes: {
              autocapitalize: "off",
              required: true,
              step: "0.01", // o "0.001" si quieres 3 decimales
            },
            inputValue: parseFloat(resumen.total_efectivo).toFixed(2),
            showCancelButton: true,
            confirmButtonText: "Cerrar caja",
            showLoaderOnConfirm: true,
            preConfirm: async (efectivo) => {
              try {
                const url =
                  `controladores/pos.php?op=cerrarCaja&efectivo_cierre=` +
                  efectivo +
                  `&idcaja=` +
                  idcaja;
                const response = await fetch(url);
                if (!response.status) {
                  return Swal.showValidationMessage(
                    `${JSON.stringify(await response.json())}`,
                  );
                }
                return response.json();
              } catch (error) {
                Swal.showValidationMessage(`Request failed: ${error}`);
              }
            },
            allowOutsideClick: () => !Swal.isLoading(),
          }).then((result) => {
            if (result.isConfirmed) {
              verificarCaja();
              Swal.fire({
                title: "Cerrado",
                icon: "success",
                text: "¡Vuelva a abrir una caja!",
                showConfirmButton: false,
                timer: 1500,
              });
            }
          });
        },
      });
    } else {
      Swal.fire({
        title: "Advertencia",
        text: "No puedes cerrar caja con el carrito lleno.",
        icon: "warning",
        confirmButtonText: "OK",
      });
    }
  });
}

function verificarCarrito(callback) {
  $.ajax({
    url: "controladores/pos.php?op=listarCarrito",
    type: "GET",
    success: function (data) {
      var carrito = JSON.parse(data);
      if (carrito.length === 0) {
        // Carrito vacío
        callback(true);
      } else {
        // Carrito no vacío
        callback(false);
      }
    },
  });
}

function listar() {
  var estado = $("#estado").val();
  var idcaja = $("#idcaja").val();
  var idsucursal = $("#idsucursal2").val();

  tabla = $("#tbllistado")
    .dataTable({
      //"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      processing: true,
      language: {
        processing:
          "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
      },
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      dom: '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>><"col-sm-12 col-md-4"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      lengthMenu: [
        [5, 10, 25, 50, 100, -1],
        [
          "5 filas",
          "10 filas",
          "25 filas",
          "50 filas",
          "100 filas",
          "Mostrar todo",
        ],
      ],
      buttons: [
        "pageLength",
        {
          extend: "excelHtml5",
          text: "<i class='fas fa-file-csv'></i>",
          titleAttr: "Exportar a Excel",
          // className: 'btn btn-success'
        },
        {
          extend: "pdf",
          text: "<i class='fas fa-file-pdf'></i>",
          titleAttr: "Exportar a PDF",
          // className: 'btn btn-danger'
        },
        {
          extend: "colvis",
          text: "<i class='fas fa-bars'></i>",
          titleAttr: "",
          // className: 'btn btn-danger'
        },
      ],
      ajax: {
        url:
          "controladores/pos.php?op=listarVentas&estado=" +
          estado +
          "&idcaja=" +
          idcaja +
          "&idsucursal=" +
          idsucursal,
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 5, //Paginación
      order: [[0, "desc"]], //Ordenar (columna,orden)
    })
    .DataTable();
}

//cargamos los items al select cliente
function listarClientes() {
  $("#tipo_comprobante").on("change", function () {
    var tipo_comprobante = $(this).val();
    var filtro = tipo_comprobante === "Factura" ? "RUC" : "";
    var es_factura = tipo_comprobante === "Factura" ? "1" : "0";

    $.post(
      "controladores/venta.php?op=selectCliente",
      { tipo_documento: filtro, es_factura: es_factura },
      function (r) {
        $("#idcliente").html(r);
        $("#idcliente").select2();

        if (es_factura === "1") {
          $("#alerta-cliente").show();
        } else {
          $("#alerta-cliente").hide();
        }
      },
    );
  });

  // Carga inicial (sin filtro y sin alerta)
  $.post(
    "controladores/venta.php?op=selectCliente",
    { tipo_documento: "", es_factura: "0" },
    function (r) {
      $("#idcliente").html(r);
      $("#idcliente").select2();
    },
  );
}

function comprobantevista() {
  $.post("controladores/venta.php?op=selectComprobante", function (c) {
    $("#tipo_comprobante").html(c);
    $("#tipo_comprobante").select2("Nota de Venta");
  });
}

function toggleCard() {
  var card = document.getElementById("datosgenerales");
  card.hidden = !card.hidden;
}

$(document).ready(function () {
  const type = parseInt(window.localStorage.getItem("type_search") || 1);
  activeSearch(type); // Establecer estado visual según el localStorage
});

function activeSearch(index) {
  window.localStorage.setItem("type_search", index);
  if (index === 1) {
    $("#btn_text_search").addClass("active-search");
    $("#btn_barcode_search").removeClass("active-search");
    $("#search-producto").attr("placeholder", "Buscar producto por nombre");
  }
  if (index === 2) {
    $("#btn_barcode_search").addClass("active-search");
    $("#btn_text_search").removeClass("active-search");
    $("#search-producto").attr(
      "placeholder",
      "Buscar producto por código de barras",
    );
  }
}

$("#btn_text_search").on("click", function () {
  activeSearch(1);
  $("#search-producto").focus();
});

$("#btn_barcode_search").on("click", function () {
  activeSearch(2);
  $("#search-producto").focus();
});

// Función para agregar cards dinámicamente
function agregarCards(categoria) {
  var cardContainer = document.getElementById("cardContainer");
  var idsucursal = $("#idsucursal").val(); // Obtener la sucursal actual
  $.ajax({
    url:
      "controladores/pos.php?op=listarProductos&idsucursal=" +
      idsucursal +
      "&categoria=" +
      categoria,
    type: "GET",
    data: "",
    beforeSend: function () {
      cardContainer.innerHTML = `
        <div style="margin: 0 auto">
          <div class="loader-wrapper" style="text-align: center">
              <div class="spinner"></div>
              <div class="loader-text">Cargando...</div>
          </div>
        </div>
      `;
    },
    success: function (data) {
      var data = JSON.parse(data);
      var cardHtml = "";
      if (data.length === 0) {
        cardContainer.innerHTML = `<div style="margin: 0 auto">
          <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:24px;min-height:220px;">
            <svg width="120" height="120" viewBox="0 0 128 128" role="img" aria-label="Sin resultados">
              <defs>
                <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0" stop-color="#e9ecef"></stop>
                  <stop offset="1" stop-color="#f8f9fa"></stop>
                </linearGradient>
              </defs>

              <!-- sombra -->
              <ellipse cx="64" cy="108" rx="34" ry="10" fill="#000" opacity="0.06"></ellipse>

              <!-- caja -->
              <path d="M30 44 L64 28 L98 44 L64 60 Z" fill="url(#g)" stroke="#ced4da" stroke-width="2" />
              <path d="M30 44 V86 L64 102 V60 Z" fill="#f1f3f5" stroke="#ced4da" stroke-width="2" />
              <path d="M98 44 V86 L64 102 V60 Z" fill="#ffffff" stroke="#ced4da" stroke-width="2" />

              <!-- cinta -->
              <path d="M64 28 V60" stroke="#adb5bd" stroke-width="2" />
              <path d="M45 40 L64 50 L83 40" fill="none" stroke="#adb5bd" stroke-width="2" />

              <!-- carita triste -->
              <circle cx="52" cy="74" r="2.5" fill="#868e96"></circle>
              <circle cx="76" cy="74" r="2.5" fill="#868e96"></circle>
              <path d="M54 86 Q64 78 74 86" fill="none" stroke="#868e96" stroke-width="2" stroke-linecap="round" />

              <!-- estrellitas -->
              <path d="M104 30 l3 6 l6 3 l-6 3 l-3 6 l-3-6 l-6-3 l6-3 z" fill="#dee2e6"/>
              <path d="M20 30 l2 4 l4 2 l-4 2 l-2 4 l-2-4 l-4-2 l4-2 z" fill="#dee2e6"/>
            </svg>

            <div style="margin-top:10px;font-size:14px;color:#495057;">
              No se encontraron productos en el almacén...
            </div>
          </div>
        </div>`;

        return;
      }
      $.each(data, function (i, item) {
        // Calcular el stock disponible en términos de contenedores
        let stockEnContenedores =
          item.stock_lote_fifo / item.cantidad_contenedor;
        if (stockEnContenedores < 0) stockEnContenedores = 0; // Asegurar que no sea negativo

        cardHtml += `
          <div class="col-6 col-md-3 mb-3">
            <div class="pos-card"
                 onclick="seleccionarProducto('${btoa(JSON.stringify(item))}')">

              <div class="position-relative">
                <img src="files/productos/${item.imagen}" class="pos-img w-100">

                <div class="pos-stock" style="background:${
                  stockEnContenedores < 5
                    ? "#dc3545"
                    : stockEnContenedores < 15
                      ? "#fd7e14"
                      : "#198754"
                }">
                  Stock ${stockEnContenedores.toFixed(1)}
                </div>

                <div class="pos-price">
                  S/ ${parseFloat(item.precio_venta_fifo).toFixed(2)}
                </div>
              </div>

              <div class="p-2 text-center">
                <div class="pos-title">
                  ${item.nombre}
                </div>
                <div class="mt-1">
                  <span class="badge bg-purple">${item.contenedor}</span> x
                  <span class="badge bg-primary">${item.unidadmedida}</span>
                </div>
              </div>

            </div>
          </div>
          `;
      });
      cardContainer.innerHTML = cardHtml;
    },
  });
}

function agregarCategorias() {
  var cardContainer = document.getElementById("cardCategorias");
  $.ajax({
    url: "controladores/pos.php?op=listarCategorias",
    type: "GET",
    data: "",
    success: function (data) {
      var data = JSON.parse(data);
      var cardHtml = "";
      $.each(data, function (i, item) {
        cardHtml +=
          `<button type="button" style="border-radius:10px;background: linear-gradient(to bottom, #86CFF7, #67E6F8);border: none;" class="btn btn-primary" onclick="agregarCards(` +
          data[i].idcategoria +
          `)">` +
          data[i].nombre +
          `</button> `;
      });
      cardContainer.innerHTML = cardHtml;
    },
  });
}

$("#search-producto").keyup(function (e) {
  e.preventDefault();
  const valor = $(this).val();

  if (valor.length > 0) {
    searchProductos(valor); // Ya obtiene el modo internamente
  } else {
    agregarCards("Todos");
  }
});

function escapeJSString(str) {
  return String(str)
    .replace(/\\/g, "\\\\") // barra invertida
    .replace(/'/g, "\\'") // comilla simple
    .replace(/"/g, '\\"') // comilla doble
    .replace(/\n/g, "\\n") // salto de línea
    .replace(/\r/g, "") // retorno de carro
    .replace(/</g, "\\u003C") // previene inyecciones de HTML
    .replace(/>/g, "\\u003E");
}

// Variable para almacenar el temporizador de demora (debouncing)
var debounceTimer;

function searchProductos(producto) {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(function () {
    const cardContainer = document.getElementById("cardContainer");
    const type = parseInt(window.localStorage.getItem("type_search") || 1); // 1=nombre, 2=código
    const idsucursal = $("#idsucursal").val(); // Obtener la sucursal actual

    $.ajax({
      url: "controladores/pos.php?op=searchProductos",
      type: "GET",
      data: {
        producto: producto,
        type: type,
        idsucursal: idsucursal, // Pasar idsucursal a la búsqueda
      },
      success: function (data) {
        const dataParsed = JSON.parse(data);
        let cardHtml = "";

        if (dataParsed.length === 1 && type === 2) {
          seleccionarProducto(btoa(JSON.stringify(dataParsed[0])));
          return;
        }

        if (dataParsed.length > 0) {
          $.each(dataParsed, function (i, item) {
            // Calcular el stock disponible en términos de contenedores
            let stockEnContenedores =
              item.stock_lote_fifo / item.cantidad_contenedor;
            if (stockEnContenedores < 0) stockEnContenedores = 0; // Asegurar que no sea negativo

            cardHtml += `<div class="col-6 col-md-3 mb-3">
            <div class="pos-card"
                 onclick="seleccionarProducto('${btoa(JSON.stringify(item))}')">

              <div class="position-relative">
                <img src="files/productos/${item.imagen}" class="pos-img w-100">

                <div class="pos-stock" style="background:${
                  stockEnContenedores < 5
                    ? "#dc3545"
                    : stockEnContenedores < 15
                      ? "#fd7e14"
                      : "#198754"
                }">
                  Stock ${stockEnContenedores.toFixed(1)}
                </div>

                <div class="pos-price">
                  S/ ${parseFloat(item.precio_venta_fifo).toFixed(2)}
                </div>
              </div>

              <div class="p-2 text-center">
                <div class="pos-title">
                  ${item.nombre}
                </div>
                <div class="mt-1">
                  <span class="badge bg-purple">${item.contenedor}</span> x
                  <span class="badge bg-primary">${item.unidadmedida}</span>
                </div>
              </div>

            </div>
          </div>`;
          });
        } else {
          cardHtml = `<div class="col-12 text-center text-danger">No existen productos que coincidan con la búsqueda</div>`;
        }

        cardContainer.innerHTML = cardHtml;
      },
    });
  }, 300);
}

$("#campoDeBusqueda").on("input", function () {
  var searchTerm = $(this).val();
  searchProductos(searchTerm);
});

function seleccionarProducto(data) {
  var data = JSON.parse(atob(data));
  // 🔎 Validar según el tipo de contenedor usando stock_lote_fifo
  let stockDisponibleUnidades = parseFloat(data.stock_lote_fifo) || 0;
  let cantidadContenedor = parseFloat(data.cantidad_contenedor) || 1;
  let stockDisponibleEnContenedores = Math.floor(
    stockDisponibleUnidades / cantidadContenedor,
  );

  if (stockDisponibleEnContenedores < 1) {
    var audioError = new Audio("files/audio/error.mp3");
    audioError.play();
    Swal.fire({
      title: "Stock insuficiente para " + data.contenedor,
      icon: "error",
      timer: 1200,
      showConfirmButton: false,
    });
    return;
  }

  // 👉 Determinar la cantidad a agregar según el tipo de contenedor
  let cantidadAAgregar = 1; // Por defecto, agregar 1 contenedor/unidad

  // Enviar al servidor
  $.ajax({
    url: "controladores/pos.php?op=seleccionarProducto",
    type: "POST",
    data: {
      id: data.id_producto_config, // id de producto_configuracion
      idproducto: data.id_producto_real, // id de producto (real)
      producto: data.id_producto_real, // también se usa el id real en tdc.producto
      nombre: data.nombre,
      precio: parseFloat(data.precio_venta_fifo).toFixed(2), // Precio del lote FIFO
      contenedor: data.contenedor,
      cantidad_contenedor: data.cantidad_contenedor,
      cantidad: cantidadAAgregar,
      stock_disponible: stockDisponibleUnidades, // Stock disponible en unidades
      id_fifo: data.id_fifo, // ID del lote FIFO
    },
    success: function (resp) {
      var result = JSON.parse(resp);
      if (result.status == 1) {
        var audioSuccess = new Audio("files/audio/vip.mp3");
        audioSuccess.play();
        listarCarrito();
        agregarCards("Todos"); // Recargar tarjetas para reflejar cambios en stock
      } else {
        var audioError = new Audio("files/audio/error.mp3");
        audioError.play();
        Swal.fire({
          title: result.message || "No se pudo agregar el producto",
          icon: "error",
          timer: 1500,
          showConfirmButton: false,
        });
      }
    },
    error: function () {
      Swal.fire({
        title: "Error en la comunicación con el servidor",
        icon: "error",
        timer: 1500,
        showConfirmButton: false,
      });
    },
  });

  $("#search-producto").val("");
  $("#search-producto").focus();
}

function formapago(formapago) {
  if (formapago == "Si") {
    document.getElementById("input-visa").readOnly = true;
    document.getElementById("input-yape").readOnly = true;
    document.getElementById("input-plin").readOnly = true;
    document.getElementById("input-mastercard").readOnly = true;
    document.getElementById("input-deposito").readOnly = true;
  } else if (formapago == "No") {
    document.getElementById("input-efectivo").readOnly = true;
    document.getElementById("input-visa").readOnly = false;
    document.getElementById("input-yape").readOnly = false;
    document.getElementById("input-plin").readOnly = false;
    document.getElementById("input-mastercard").readOnly = false;
    document.getElementById("input-deposito").readOnly = false;
  } else {
    document.getElementById("input-efectivo").readOnly = false;
    document.getElementById("input-visa").readOnly = true;
    document.getElementById("input-yape").readOnly = true;
    document.getElementById("input-plin").readOnly = true;
    document.getElementById("input-mastercard").readOnly = true;
    document.getElementById("input-deposito").readOnly = true;
  }
}

function listarCarrito() {
  $.ajax({
    url: "controladores/pos.php?op=listarCarrito",
    type: "GET",
    data: "",
    success: function (data) {
      var data = JSON.parse(data);
      var html = "";
      var total = 0;
      var totalV = 0;
      if (data != "") {
        $.each(data, function (i, item) {
          let subtotal = item.precio * item.cantidad;
          total += subtotal;
          totalV += item.comisionV * item.cantidad;

          // 🔹 CALCULA EL STOCK SEGÚN EL TIPO DE CONTENEDOR USANDO FIFO
          let stockDisponibleUnidades = parseFloat(item.stock_lote_fifo) || 0;
          let stockDisponibleEnContenedores;
          if (item.cantidad_contenedor > 1) {
            // Para cajas: mostrar cuántas cajas completas hay
            stockDisponibleEnContenedores = Math.floor(
              stockDisponibleUnidades / item.cantidad_contenedor,
            );
          } else {
            // Para unidades: mostrar el stock tal cual (puede ser fraccionado)
            stockDisponibleEnContenedores = stockDisponibleUnidades;
          }

          html += `
<tr class="cart-row" id="fila${cont}">
  <td>
    <div class="cart-remove" onclick="eliminarProductoCarrito(${item.idproducto})" style="cursor:pointer;">
      <i class="fas fa-trash-alt"></i>
    </div>
  </td>
  <td>
    <div class="cart-info">
      <div class="cart-product-name">${item.producto}</div>
      <div class="cart-meta">
        <span class="badge bg-success">${item.contenedor}</span>
        <span class="badge bg-primary">${item.unidad_medida}</span>
      </div>
    </div>
  </td>
  <td>
    <!-- precio -->
<div style="position: relative; display: inline-block;">
  <input type="text"
         class="form-control form-control-sm"
         id="precio-${item.idproducto}"
         value="${item.precio}"
         style="padding-right: 2rem;"
         oninput="modificarSubtotales(${item.idproducto})"
         onblur="actualizarDataItem(${item.idproducto}, this.value, 'precio')">
  <i class="fas fa-eye" 
     onclick="verPreciosItem(${item.idproducto})" 
     style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); cursor:pointer; font-size: 0.8em; color: #6c757d;"></i>
</div>
  </td>
  <td>
    <!-- cantidad -->
    <div class="cart-qty-box">
      <input type="number" 
             class="form-control form-control-sm"
             min="0"
             max="${stockDisponibleEnContenedores}"
             step="${item.cantidad_contenedor > 1 ? "1" : "0.01"}"
             id="cantidad-${item.idproducto}"
             value="${item.cantidad}"
             data-stock="${stockDisponibleEnContenedores}"
             data-cantidad-contenedor="${item.cantidad_contenedor}"
             oninput="handleCantidadChange(${item.idproducto})"
             onblur="actualizarDataItem(${item.idproducto}, this.value, 'cantidad')">
    </div>
  </td>
  <td>
    <!-- subtotal -->
    <div class="cart-subtotal subtotal-item" id="subtotal-${item.idproducto}">
      S/. ${subtotal.toFixed(2)}
    </div>
  </td>
</tr>`;
        });

        $("#subtotal-venta").html("S/. " + total.toFixed(2));
        $("#subtotal-ventaC").html("S/. " + totalV.toFixed(2));
        $("#igv-venta").html("S/. " + (total * 0.0).toFixed(2));
        $("#total-venta").html("S/. " + total.toFixed(2));
        $("#total_comision").val(totalV.toFixed(2));
        $("#input-total-venta").val(total.toFixed(2));
      } else {
        html += `<tr>
          <td colspan="6" class="text-center text-muted">
            El carrito está vacío
          </td>
        </tr>`;
        $("#subtotal-venta").html("S/. 0.00");
        $("#subtotal-ventaC").html("S/. 0.00");
        $("#igv-venta").html("S/. 0.00");
        $("#total-venta").html("S/. 0.00");
        $("#input-total-venta").val(0);
      }
      $("#agregarcarrito tbody").html(html);
    },
  });
}

function actualizarDataItem(idproducto, value, campo) {
  $.ajax({
    url: "controladores/pos.php?op=actualizarDataItem",
    type: "POST",
    data: {
      idproducto: idproducto,
      campo: campo, // nombre del campo, ej. "nombre"
      value: value,
    },
    success: function (data) {},
    error: function (error) {
      toastr.error("Error al procesar la solicitud", "Error");
    },
  });
}

function verPreciosItem(idproducto) {
  $.ajax({
    url: "controladores/pos.php?op=verPreciosItem&idproducto=" + idproducto,
    type: "GET",
    data: "",
    success: function (data) {
      var data = JSON.parse(data);

      var html = "";
      if (data.length > 0) {
        $("#ModalPrecios").modal("show");
        html += `<table class="table table-hovered table-striped">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Precio</th>
              <th></th>
            </tr>
          </thead>
          <tbody>`;
        $.each(data, function (i, item) {
          html += `<tr>
            <td>${item.descripcion}</td>
            <td>S/. ${item.precio}</td>
            <td>
              <button class="btn btn-primary btn-sm" onclick="actualizarPrecio(${item.precio}, ${idproducto})">Seleccionar</button>
          </tr>`;
        });
        html += `</tbody></table>`;
      } else {
        toastr.warning("Este producto no tiene precios registrados", "Aviso");
      }
      $("#tabla-precios").html(html);
    },
  });
}

function actualizarPrecio(precio, idproducto) {
  // Actualiza el precio en el campo correspondiente
  $(`#precio-${idproducto}`).val(precio);

  // Llama a la función para modificar los subtotales
  actualizarDataItem(idproducto, precio, "precio");
  modificarSubtotales(idproducto);

  // Cierra el modal de precios
  $("#ModalPrecios").modal("hide");
}

function modificarSubtotales(idproducto) {
  let cantidad = parseFloat($(`#cantidad-${idproducto}`).val()) || 0;
  let precioVenta = parseFloat($(`#precio-${idproducto}`).val()) || 0;
  let subtotal = precioVenta * cantidad;

  $(`#subtotal-${idproducto}`).text("S/. " + subtotal.toFixed(2));

  // Recalcular todos los subtotales
  let total = 0;
  $(".subtotal-item").each(function () {
    total += parseFloat($(this).text().replace("S/. ", "")) || 0;
  });

  $("#subtotal-venta").html("S/. " + total.toFixed(2));
  $("#total-venta").html("S/. " + total.toFixed(2));
  $("#input-total-venta").val(total.toFixed(2));
}

// Define tus estilos como una cadena de texto
var estilos = `
  /* Estilos para el ícono de suma */
  .circle {
    display: inline-block;
    width: 30px; /* Modificado el tamaño del círculo */
    height: 30px; /* Modificado el tamaño del círculo */
    color: #fff; /* Color del ícono */
    border-radius: 50%; /* Hace que el contenedor sea circular */
    text-align: center;
    line-height: 40px; /* Ajustado el tamaño de línea para centrar el icono */
    cursor: pointer;
    margin-right: 10px; /* Espaciado entre los íconos */
  }

  /* Estilos para el ícono de basura */
  .trash-icon {
    display: inline-block;
    width: 30px;
    height: 30px;
    color: #fff; /* Color del ícono */
    border-radius: 50%; /* Hace que el contenedor sea circular */
    text-align: center;
    line-height: 30px;
    cursor: pointer;
  }
`;

// Agrega los estilos al encabezado del documento
document.head.insertAdjacentHTML("beforeend", "<style>" + estilos + "</style>");

function eliminarProductoCarrito(idproducto) {
  $.ajax({
    url:
      "controladores/pos.php?op=eliminarProductoCarrito&idproducto=" +
      idproducto,
    type: "POST",
    data: "",
    contentType: false,
    processData: false,
    success: function (data) {
      var data = JSON.parse(data);
      if (data.status == 1) {
        listarCarrito();
      }
    },
  });
}

function handleCantidadChange(idproducto) {
  const input = document.getElementById("cantidad-" + idproducto);
  let cantidad = parseFloat(input.value) || 0;
  let stock = parseFloat(input.dataset.stock) || 0;
  let cantidadContenedor = parseInt(input.dataset.cantidadContenedor) || 1;

  if (cantidad > stock) {
    let unidadTexto =
      cantidadContenedor > 1
        ? `${input.dataset.contenedor || "contenedor"}(es)`
        : "unidad(es)";
    Swal.fire({
      title: "Cantidad mayor al stock disponible",
      text: `Solo hay ${stock} ${unidadTexto} disponibles.`,
      icon: "warning",
      confirmButtonText: "Aceptar",
    });
    input.value = stock;
    cantidad = stock;
  }

  if (cantidad < 0) {
    input.value = cantidadContenedor > 1 ? 1 : 0.01;
    return;
  }

  modificarSubtotales(idproducto);
}

// 3) Validación en +:
function sumarProductoCarrito(idproducto) {
  const input = document.getElementById("cantidad-" + idproducto);
  let cantidad = parseInt(input.value) || 0;
  let stock = parseInt(input.dataset.stock) || 0;
  if (cantidad >= stock) {
    Swal.fire({
      title: "No queda más productos en almacén",
      icon: "warning",
      timer: 700,
      showConfirmButton: false,
    });
    return;
  }
  // Si pasa la validación, llamamos al servidor:
  $.ajax({
    url: `controladores/pos.php?op=sumarProductoCarrito&idproducto=${idproducto}`,
    type: "POST",
    contentType: false,
    processData: false,
    success: function (data) {
      var data = JSON.parse(data);
      if (data.status == 1) {
        listarCarrito();
      } else {
        Swal.fire({
          title: "No queda más productos en almacén",
          icon: "warning",
          timer: 700,
          showConfirmButton: false,
        });
      }
    },
    error: function (error) {
      console.log(error.responseText);
    },
  });
}

// 4) Validación en –:
function restarProductoCarrito(idproducto) {
  const input = document.getElementById("cantidad-" + idproducto);
  let cantidad = parseInt(input.value) || 0;
  if (cantidad <= 1) {
    // opcional: mostrar un aviso o simplemente no hacer nada
    return;
  }
  $.ajax({
    url: `controladores/pos.php?op=restarProductoCarrito&idproducto=${idproducto}`,
    type: "POST",
    contentType: false,
    processData: false,
    success: function (data) {
      var data = JSON.parse(data);
      if (data.status == 1) {
        listarCarrito();
      }
    },
  });
}

// 5) Validación al teclear (keyUp):
function keyUpProductoCarrito(idproducto, cantidadStr) {
  let cantidad = parseInt(cantidadStr) || 0;
  const input = document.getElementById("cantidad-" + idproducto);
  let stock = parseInt(input.dataset.stock) || 0;

  if (cantidad > stock) {
    Swal.fire({
      title: "Cantidad mayor al stock disponible",
      text: `Solo hay ${stock} unidad(es) disponibles.`,
      icon: "warning",
      confirmButtonText: "Aceptar",
    });
    input.value = stock;
    return false;
  }
  if (cantidad < 1) {
    input.value = 1;
    return false;
  }

  // si pasa validación, enviamos:
  $.ajax({
    url: `controladores/pos.php?op=keyUpProductoCarrito&idproducto=${idproducto}&cantidad=${cantidad}`,
    type: "POST",
    contentType: false,
    processData: false,
    success: function (data) {
      var data = JSON.parse(data);
      if (data.status == 1) {
        listarCarrito();
      } else {
        Swal.fire({
          title: "No queda más productos en almacén",
          icon: "warning",
          timer: 700,
          showConfirmButton: false,
        });
      }
    },
    error: function (error) {
      console.log(error.responseText);
    },
  });
}

$("#modal-default").on("hidden.bs.modal", function () {
  // Resetear inputs
  $(this).find('input[type="text"], input[type="hidden"]').val("");
  $(this).find("textarea").val("");

  // Resetear spans
  $(this).find("div.h5").text("0.00");

  // Resetear selects
  $(this).find("select").prop("selectedIndex", 0);
});

$("#pasar-caja").click(function (e) {
  e.preventDefault();
  var total = parseFloat($("#input-total-venta").val()) || 0;

  if (total > 0) {
    $("#modal-default").modal("show");
    $("#total-pedido").text(total.toFixed(2));
    $("#input-total-venta").val(total.toFixed(2));
  } else {
    Swal.fire({
      title: "El carrito está vacío",
      icon: "info",
      text: "El carrito está vacío",
      timer: 1000,
      timerProgressBar: true,
      showConfirmButton: false,
    });
  }
});

$(document).ready(function () {
  $("#cancelar-btn").click(function () {
    $("#modal-default").find("input, textarea").val(""); // Restablecer campos de texto
    $("#modal-default").find("span").html(""); // Restablecer contenido de los spans
    $("#modal-default").find("select").prop("selectedIndex", 0); // Restablecer selecciones de opción
  });
});

//mostramos el num_comprobante de la boleta
function numBoleta() {
  var idsucursal = $("#idsucursal").val();
  $.ajax({
    url: "controladores/venta.php?op=mostrar_num_boleta",
    data: { idsucursal: idsucursal },
    type: "get",
    dataType: "json",
    success: function (d) {
      iva = d;
      $("#porcentaje").attr("disabled", true);
      $("#num_comprobante").val(("0000000" + iva).slice(-7)); // "0001"
      $("#nFacturas").html(("0000000" + iva).slice(-7)); // "0001"
    },
  });
}
//mostramos la serie_comprobante de la boleta
function numSerieBoleta() {
  var idsucursal = $("#idsucursal").val();
  $.ajax({
    url: "controladores/venta.php?op=mostrar_serie_boleta",
    data: { idsucursal: idsucursal },
    type: "get",
    dataType: "json",
    success: function (s) {
      series = s;
      $("#numeros").html(("000" + series).slice(-3)); // "0001"
      $("#serie_comprobante").val("B" + ("000" + series).slice(-3)); // "0001"
    },
  });
}

//mostramos el num_comprobante de la fatura
function numFactura() {
  var idsucursal = $("#idsucursal").val();

  $.ajax({
    url: "controladores/venta.php?op=mostrarf",
    data: { idsucursal: idsucursal },
    type: "get",
    dataType: "json",
    success: function (d) {
      iva = d;
      $("#porcentaje").attr("disabled", true);
      $("#num_comprobante").val(("0000000" + iva).slice(-7)); // "0001"
      $("#nFacturas").html(("0000000" + iva).slice(-7)); // "0001"
    },
  });
}
//mostramos la serie_comprobante de la factura
function numSerie() {
  var idsucursal = $("#idsucursal").val();
  $.ajax({
    url: "controladores/venta.php?op=mostrars",
    type: "get",
    data: { idsucursal: idsucursal },
    dataType: "json",
    success: function (s) {
      series = s;
      $("#numeros").html(("000" + series).slice(-3)); // "0001"
      $("#serie_comprobante").val("F" + ("000" + series).slice(-3)); // "0001"
    },
  });
}

$("#tipo_comprobante").change(marcarImpuesto);

function marcarImpuesto() {
  var tipo_comprobante = $("#tipo_comprobante option:selected").text();
  if (tipo_comprobante == "Factura") {
    // $("#impuesto").val(impuesto);
    numFactura();
    numSerie();
    // $("#serie_comprobante").val( "F001" );
  } else if (tipo_comprobante == "Boleta") {
    // $("#impuesto").val(impuesto);
    numBoleta();
    numSerieBoleta();
    // $("#serie_comprobante").val( "B001" );
  } else if (tipo_comprobante == "Nota de Venta") {
    $("#impuesto").val("0");
    no_aplica = 0;
    numTicket();
    numSerieTicket();
    // $("#serie_comprobante").val( "P001" );
  }
}

//mostramos el num_comprobante del ticket
function numTicket() {
  var idsucursal = $("#idsucursal").val();
  $.ajax({
    url: "controladores/venta.php?op=mostrar_num_ticket",
    data: { idsucursal: idsucursal },
    type: "get",
    dataType: "json",
    success: function (d) {
      iva = d;
      $("#porcentaje").attr("disabled", false);
      $("#num_comprobante").val(("0000000" + iva).slice(-7)); // "0001"
      $("#nFacturas").html(("0000000" + iva).slice(-7)); // "0001"
    },
  });
}
//mostramos la serie_comprobante de la ticket
function numSerieTicket() {
  var idsucursal = $("#idsucursal").val();
  $.ajax({
    url: "controladores/venta.php?op=mostrar_s_ticket",
    data: { idsucursal: idsucursal },
    type: "get",
    dataType: "json",
    success: function (s) {
      series = s;
      $("#numeros").html(("000" + series).slice(-3)); // "0001"
      $("#serie_comprobante").val("P" + ("000" + series).slice(-3)); // "0001"
    },
  });
}

// =======================
// FUNCIONES DE CÁLCULO
// =======================

// Convierte cualquier valor a número seguro
// ------------------- helpers -------------------
function toNumber(valor) {
  var num = parseFloat(valor);
  return isNaN(num) ? 0 : num;
}

/* -------------------- sumPagos -------------------- */
function sumPagos() {
  // suma visible: principal (lo que escribió el cajero) + pagos dinámicos
  let suma = 0;
  suma += toNumber($("#input-efectivo").val());
  $(".pago-dinamico .pago-input").each(function () {
    suma += toNumber($(this).val());
  });
  return suma;
}

/* -------------------- tipos de pago -------------------- */
const tiposPago = [
  { id: "visa", label: "Visa", icon: "visa.ico" },
  { id: "yape", label: "Yape", icon: "yape.ico" },
  { id: "plin", label: "Plin", icon: "plin.ico" },
  { id: "mastercard", label: "MasterCard", icon: "master.ico" },
  { id: "deposito", label: "Depósito", icon: "deposito.ico" },
];

/* -------------------- actualizarPagos (actualiza UI + hidden) -------------------- */
function actualizarPagos() {
  var totalVenta = toNumber($("#input-total-venta").val());
  var suma = sumPagos();

  // Totales y vuelto (UI)
  $("#total-pagado").text("S/. " + suma.toFixed(2));
  $("#pagado-total").val(suma.toFixed(2));

  var vuelto = suma - totalVenta;
  if (vuelto < 0) vuelto = 0;
  $("#vuelto").text("S/. " + vuelto.toFixed(2));
  $("#input-vuelto").val(vuelto.toFixed(2));

  // Totales efectivo / otros (considerando el tipo seleccionado en el pago principal)
  var tipoPrincipal = $("#tipo-principal").val();
  var montoPrincipal = toNumber($("#input-efectivo").val());

  var totalEfectivo = 0;
  var totalOtros = 0;

  if (tipoPrincipal === "Efectivo") totalEfectivo += montoPrincipal;
  else totalOtros += montoPrincipal;

  // sumar pagos dinámicos según su tipo
  $(".pago-dinamico").each(function () {
    var tipo = $(this).find(".pago-hidden").val();
    var monto = toNumber($(this).find(".pago-input").val());
    if (tipo === "Efectivo") totalEfectivo += monto;
    else totalOtros += monto;
  });

  // actualizar campos de resumen en UI
  $("#total-efectivo").val(totalEfectivo.toFixed(2));
  $("#total-otros").val(totalOtros.toFixed(2));

  // Actualizar hidden para enviar al backend
  $("#hidden-totalrecibido").val((totalEfectivo + totalOtros).toFixed(2));
  $("#hidden-totaldeposito").val(totalOtros.toFixed(2));
  $("#hidden-vuelto").val(vuelto.toFixed(2));
}

/* -------------------- formateo al perder foco -------------------- */
$(document).on(
  "blur",
  "#input-efectivo, .pago-dinamico .pago-input",
  function () {
    var val = toNumber($(this).val());
    $(this).val(val.toFixed(2));
    actualizarPagos();
  },
);

/* -------------------- listener en tiempo real -------------------- */
$(document).on(
  "input",
  "#input-efectivo, .pago-dinamico .pago-input",
  actualizarPagos,
);

/* -------------------- cambiar icono del pago principal -------------------- */
$(document).on("change", "#tipo-principal", function () {
  let tipo = $(this).val();
  let pago = tiposPago.find((p) => p.id === tipo);

  if (pago) {
    $("#icono-principal").attr("src", "files/icons/" + pago.icon);
    $("#label-principal").text(pago.label); // <<< cambia el texto
  } else {
    $("#icono-principal").attr("src", "files/icons/efectivo.ico");
    $("#label-principal").text("Efectivo");
  }

  // mostrar/ocultar extras
  if (tipo === "Efectivo") {
    $("#extras-principal").addClass("d-none");
  } else {
    $("#extras-principal").removeClass("d-none");
  }

  actualizarPagos();
});

/* -------------------- al abrir el modal -------------------- */
$("#modal-default").on("shown.bs.modal", function () {
  var total = toNumber($("#input-total-venta").val());
  $("#total-pedido").text("S/. " + total.toFixed(2));

  // Si no hay pagos, cargar total en el pago principal (visible)
  if (sumPagos() === 0 && !$("#input-efectivo").val()) {
    $("#input-efectivo").val(total.toFixed(2));
    $("#tipo-principal").val("Efectivo");
    $("#icono-principal").attr("src", "files/icons/efectivo.ico");
  }
  actualizarPagos();
});

/* -------------------- pagos dinámicos -------------------- */
let contadorPagos = 0;

$("#agregar-pago-btn").on("click", function () {
  let opciones = tiposPago
    .map((p) => `<option value="${p.id}">${p.label}</option>`)
    .join("");

  // calcular saldo pendiente antes de crear el pago
  let totalVenta = toNumber($("#input-total-venta").val());
  let sumaActual = sumPagos(); // incluye principal visible
  let saldo = totalVenta - sumaActual;
  if (saldo < 0) saldo = 0;

  // default tipo
  let tipoDefault = tiposPago[0].id;
  let iconDefault = tiposPago[0].icon;

  let html = `
    <div class="d-flex flex-column mb-2 p-2 border rounded bg-white shadow-sm pago-dinamico" data-contador="${contadorPagos}">
      <div class="d-flex align-items-center mb-1">
        <img src="files/icons/${iconDefault}" class="pago-icon mr-2" style="height:32px;">
        <label class="pago-label flex-grow-1 mb-0 font-weight-bold">${tiposPago[0].label}</label>
        <select class="form-control form-control-sm mr-2 pago-tipo">${opciones}</select>
        <input type="text" class="form-control form-control-sm text-right pago-input" name="pagado[]" placeholder="Monto" value="${saldo.toFixed(2)}">
        <input type="hidden" class="pago-hidden" name="metodo_pago[]" value="${tipoDefault}">
        <button type="button" class="btn btn-danger btn-sm ml-2 quitar-pago"><i class="fas fa-times"></i></button>
      </div>
      <div class="d-flex gap-1 mt-1 extras-dinamico d-none">
        <input type="text" class="form-control form-control-sm" name="nroOperacion[]" placeholder="Nro Operación">
        <input type="text" class="form-control form-control-sm" name="banco[]" placeholder="Banco">
        <input type="date" class="form-control form-control-sm" name="fechaDeposito[]">
      </div>
    </div>
  `;

  $(".payment-methods").append(html);
  contadorPagos++;
  actualizarPagos();
});

$(document).on("change", ".pago-tipo", function () {
  let tipo = $(this).val();
  let pago = tiposPago.find((p) => p.id === tipo);
  let container = $(this).closest(".pago-dinamico");

  if (pago) {
    container.find(".pago-icon").attr("src", "files/icons/" + pago.icon);
    container.find(".pago-label").text(pago.label); // <<< cambia el nombre
    container.find(".pago-hidden").val(tipo);
  }

  // mostrar/ocultar extras en dinámicos
  if (tipo === "Efectivo") {
    container.find(".extras-dinamico").addClass("d-none");
  } else {
    container.find(".extras-dinamico").removeClass("d-none");
  }

  actualizarPagos();
});

$(document).on("click", ".quitar-pago", function () {
  $(this).closest(".pago-dinamico").remove();
  actualizarPagos();
});

function prepararPagosParaGuardar() {
  // 1) eliminar inyecciones anteriores
  $("#procesar-venta").find("input[data-injected='1']").remove();

  // 2) leer totales / montos desde la UI (NO desde los inputs inyectados)
  let totalVenta = toNumber($("#input-total-venta").val());
  let tipoPrincipal = $("#tipo-principal").val();
  let montoPrincipal = toNumber($("#input-efectivo").val()); // lo que el cajero escribió en el campo principal

  // 3) calcular total efectivo y total otros (deposito) leyendo DOM
  let totalEfectivo = 0;
  let totalOtros = 0;

  if (tipoPrincipal === "Efectivo") totalEfectivo += montoPrincipal;
  else totalOtros += montoPrincipal;

  // sumar pagos dinámicos según su tipo
  let sumaDinamicos = 0; // suma total de dinámicos (todos los tipos)
  $(".pago-dinamico").each(function () {
    let tipo = $(this).find(".pago-hidden").val();
    let monto = toNumber($(this).find(".pago-input").val());
    sumaDinamicos += monto;
    if (tipo === "Efectivo") totalEfectivo += monto;
    else totalOtros += monto;
  });

  // 4) calcular vuelto (basado en total recibido real = efectivo + otros)
  let totalRecibidoReal = totalEfectivo + totalOtros;
  let vuelto = totalRecibidoReal - totalVenta;
  if (vuelto < 0) vuelto = 0;

  // 5) actualizar los hidden que se enviarán al backend
  $("#pagado-total").val(totalRecibidoReal.toFixed(2)); // suma total pagada (para montoPagado)
  $("#hidden-totalrecibido").val(totalEfectivo.toFixed(2)); // SOLO efectivo
  $("#hidden-totaldeposito").val(totalOtros.toFixed(2)); // SOLO otros pagos (yape/plin/visa/dep)
  $("#hidden-vuelto").val(vuelto.toFixed(2));

  // 6) calcular cuánto del pago principal se debe registrar realmente en venta_pago (limitado al faltante)
  // cubiertoPorDinamicos = cuanto cubren los pagos dinámicos (máx totalVenta)
  let cubiertoPorDinamicos = Math.min(totalVenta, sumaDinamicos);
  let faltante = Math.max(0, totalVenta - cubiertoPorDinamicos);
  // principalRegistrable = la parte del monto principal que debemos insertar en venta_pago
  let principalRegistrable = Math.min(montoPrincipal, faltante);

  // 7) inyectar pagado[] y metodo_pago[] para el pago principal, SOLO si principalRegistrable > 0
  // (esto NO afecta los hidden de totales porque ya calculamos desde la UI)
  if (principalRegistrable > 0) {
    $("<input>", {
      type: "hidden",
      name: "pagado[]",
      value: principalRegistrable.toFixed(2),
      "data-injected": "1",
    }).appendTo("#procesar-venta");
    $("<input>", {
      type: "hidden",
      name: "metodo_pago[]",
      value: tipoPrincipal,
      "data-injected": "1",
    }).appendTo("#procesar-venta");

    // placeholders para alinear índices si tu backend espera nroOperacion[], banco[], fechaDeposito[]
    $("<input>", {
      type: "hidden",
      name: "nroOperacion[]",
      value: "",
      "data-injected": "1",
    }).appendTo("#procesar-venta");
    $("<input>", {
      type: "hidden",
      name: "banco[]",
      value: "",
      "data-injected": "1",
    }).appendTo("#procesar-venta");
    $("<input>", {
      type: "hidden",
      name: "fechaDeposito[]",
      value: "",
      "data-injected": "1",
    }).appendTo("#procesar-venta");
  }

  // Nota: No devolvemos nada — simplemente preparamos el formulario. El envío debe venir después.
}

$("#procesar-venta").submit(function (e) {
  e.preventDefault();
  prepararPagosParaGuardar();
  const data = new FormData(this);
  $.ajax({
    url: "controladores/pos.php?op=procesarVenta",
    type: "POST",
    data: data,
    contentType: false,
    processData: false,
    success: function (data) {
      var data = JSON.parse(data);
      if (data.status == 1) {
        $("#modal-default").modal("hide");
        listarCarrito();
        marcarImpuesto();
        agregarCards("Todos");

        const url =
          "reportes/exTicket.php?id=" + encodeURIComponent(data.idventa);
        const newWindow = window.open(url, "_blank");

        if (!newWindow) {
          alert(
            "Tu navegador bloqueó la ventana emergente. Habilita popups para imprimir.",
          );
          return;
        }

        newWindow.onload = function () {
          // imprimir cuando ya cargó el HTML
          newWindow.focus();
          newWindow.print();

          // cuando termina de imprimir (o cancela)
          newWindow.onafterprint = function () {
            // cerrar solo el popup
            newWindow.close();

            // Resetear el formulario después de imprimir
            $("#procesar-venta")[0].reset();
            $(".pago-dinamico").remove();
            actualizarPagos();
            marcarImpuesto();
            limpiar();
          };
        };
      }
    },
    error: function (error) {
      console.log(error.responseText);
    },
  });
});

// Crear una etiqueta <style>
const style = document.createElement("style");
style.type = "text/css";

// Añadir los estilos CSS a la etiqueta <style>
style.innerHTML = `
  .fas.fa-spinner {
    font-size: 16px;
    margin-right: 8px;
  }
`;

// Insertar la etiqueta <style> en el <head> del documento
document.head.appendChild(style);

$("#guardar-sin-imprimir").click(function () {
  const $button = $(this);
  const $modal = $("#modal-default");
  const $spinner = $("<i class='fas fa-spinner fa-spin'></i>"); // Icono de carga
  const originalButtonText = $button.html(); // Guardar el texto original del botón

  // Mostrar el icono de carga y deshabilitar el botón
  $button.html($spinner).attr("disabled", true);
  prepararPagosParaGuardar();
  const data = new FormData($("#procesar-venta")[0]); // Obtener datos del formulario
  $.ajax({
    url: "controladores/pos.php?op=procesarVenta",
    type: "POST",
    data: data,
    contentType: false,
    processData: false,
    success: function (data) {
      var responseData = JSON.parse(data);
      if (responseData.status == 1) {
        $("#modal-default").modal("hide");
        listarCarrito();
        marcarImpuesto();
        agregarCards("Todos");
        const url =
          "reportes/exTicket.php?id=" +
          encodeURIComponent(responseData.idventa.idventa);
        const newWindow = window.open(url, "_blank");

        if (!newWindow) {
          alert(
            "Tu navegador bloqueó la ventana emergente. Habilita popups para imprimir.",
          );
          return;
        }

        newWindow.onload = function () {
          // imprimir cuando ya cargó el HTML
          newWindow.focus();
          newWindow.print();

          // cuando termina de imprimir (o cancela)
          newWindow.onafterprint = function () {
            // cerrar solo el popup
            newWindow.close();

            // Resetear el formulario después de imprimir
            $("#procesar-venta")[0].reset();
            $(".pago-dinamico").remove();
            actualizarPagos();
            marcarImpuesto();
            limpiar();
          };
        };
      } else {
        Swal.fire({
          title: "Error",
          text: responseData.message || "No se pudo guardar la venta",
          icon: "error",
          confirmButtonText: "Aceptar",
        });
      }
    },
    error: function (error) {
      Swal.fire({
        title: "Error",
        text: "Ocurrió un error al procesar la venta",
        icon: "error",
        confirmButtonText: "Aceptar",
      });
    },
    complete: function () {
      // Restaurar el botón después de completar la solicitud
      $button.html(originalButtonText).attr("disabled", false);
    },
  });
});

function limpiar() {
  $("#input-efectivo").val("");
  $("#totalDescuento").val(0);
  $("#input-visa").val("");
  $("#input-yape").val("");
  $("#input-plin").val("");
  $("#input-mastercard").val("");
  $("#input-deposito").val("");
  $("#input-total-venta").val("");
  $("#total-pagado").html("S/. 0.0");
  $("#pagado-total").val("");
  $("#vuelto").html("S/. 0.00");
  $("#input-vuelto").val("");
  $("#totalDescuento").val("");
}

var HayFocoEfectivo = false;
var HayFocoVisa = false;
var HayFocoYape = false;
var HayFocoPlin = false;
var HayFocoMastercard = false;
var HayFocoDeposito = false;

function GanoFocoEfectivo() {
  HayFocoEfectivo = true;
  HayFocoVisa = false;
  HayFocoYape = false;
  HayFocoPlin = false;
  HayFocoMastercard = false;
  HayFocoDeposito = false;
}
function GanoFocoVisa() {
  HayFocoVisa = true;
  HayFocoEfectivo = false;
  HayFocoYape = false;
  HayFocoPlin = false;
  HayFocoMastercard = false;
  HayFocoDeposito = false;
}

function GanoFocoYape() {
  HayFocoYape = true;
  HayFocoEfectivo = false;
  HayFocoVisa = false;
  HayFocoPlin = false;
  HayFocoMastercard = false;
  HayFocoDeposito = false;
}
function GanoFocoPlin() {
  HayFocoPlin = true;
  HayFocoEfectivo = false;
  HayFocoVisa = false;
  HayFocoYape = false;
  HayFocoMastercard = false;
  HayFocoDeposito = false;
}
function GanoFocoMastercard() {
  HayFocoMastercard = true;
  HayFocoEfectivo = false;
  HayFocoVisa = false;
  HayFocoYape = false;
  HayFocoPlin = false;
  HayFocoDeposito = false;
}
function GanoFocoDeposito() {
  HayFocoDeposito = true;
  HayFocoEfectivo = false;
  HayFocoVisa = false;
  HayFocoYape = false;
  HayFocoPlin = false;
  HayFocoMastercard = false;
}

function keyDataSet(data) {
  if (HayFocoEfectivo) {
    var e = $("#input-efectivo").val() + data;
    $("#input-efectivo").val(e);
    $("#input-efectivo").focus();
  } else if (HayFocoVisa) {
    var v = $("#input-visa").val() + data;
    $("#input-visa").val(v);
    $("#input-visa").focus();
  } else if (HayFocoYape) {
    var v = $("#input-yape").val() + data;
    $("#input-yape").val(v);
    $("#input-yape").focus();
  } else if (HayFocoPlin) {
    var v = $("#input-plin").val() + data;
    $("#input-plin").val(v);
    $("#input-plin").focus();
  } else if (HayFocoMastercard) {
    var v = $("#input-mastercard").val() + data;
    $("#input-mastercard").val(v);
    $("#input-mastercard").focus();
  } else if (HayFocoDeposito) {
    var v = $("#input-deposito").val() + data;
    $("#input-deposito").val(v);
    $("#input-deposito").focus();
  }
  sumrDinero();
}

function deleteDataSet(data) {
  if (data == 1) {
    if (HayFocoEfectivo) {
      var e = $("#input-efectivo").val();
      e = e.substring(0, e.length - 1);
      $("#input-efectivo").val(e);
      $("#input-efectivo").focus();
    } else if (HayFocoVisa) {
      var v = $("#input-visa").val();
      v = v.substring(0, v.length - 1);
      $("#input-visa").val(v);
      $("#input-visa").focus();
    } else if (HayFocoYape) {
      var v = $("#input-yape").val();
      v = v.substring(0, v.length - 1);
      $("#input-yape").val(v);
      $("#input-yape").focus();
    } else if (HayFocoPlin) {
      var v = $("#input-plin").val();
      v = v.substring(0, v.length - 1);
      $("#input-plin").val(v);
      $("#input-plin").focus();
    } else if (HayFocoMastercard) {
      var v = $("#input-mastercard").val();
      v = v.substring(0, v.length - 1);
      $("#input-mastercard").val(v);
      $("#input-mastercard").focus();
    } else if (HayFocoDeposito) {
      var v = $("#input-deposito").val();
      v = v.substring(0, v.length - 1);
      $("#input-deposito").val(v);
      $("#input-deposito").focus();
    }
  } else if (data == 2) {
    $("#input-efectivo").val("");
    $("#input-visa").val("");
    $("#input-yape").val("");
    $("#input-plin").val("");
    $("#input-mastercard").val("");
    $("#input-deposito").val("");
  }
  sumrDinero();
}

// Obtiene el elemento span por su id
var fechaSpan = document.getElementById("fechaActual");

// Obtiene la fecha actual
var fechaActual = new Date();

// Días de la semana en español
var diasSemana = [
  "Domingo",
  "Lunes",
  "Martes",
  "Miércoles",
  "Jueves",
  "Viernes",
  "Sábado",
];

// Meses en español
var meses = [
  "Enero",
  "Febrero",
  "Marzo",
  "Abril",
  "Mayo",
  "Junio",
  "Julio",
  "Agosto",
  "Septiembre",
  "Octubre",
  "Noviembre",
  "Diciembre",
];

// Formatea la fecha según el formato deseado
var formatoFecha =
  diasSemana[fechaActual.getDay()] +
  ", " +
  fechaActual.getDate() +
  " de " +
  meses[fechaActual.getMonth()] +
  " de " +
  fechaActual.getFullYear() +
  ", " +
  (fechaActual.getHours() < 10 ? "0" : "") +
  fechaActual.getHours() +
  ":" +
  (fechaActual.getMinutes() < 10 ? "0" : "") +
  fechaActual.getMinutes();

// Inserta la fecha formateada en el elemento span
fechaSpan.innerHTML = formatoFecha;

$("#formularioClientes").submit(function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  $.ajax({
    url: "controladores/persona.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (datos) {
      Swal.fire({
        title: "Cliente",
        icon: "success",
        text: datos,
      });
      $("#ModalClientes").modal("hide");
      listarClientes();
    },
  });
});

function mostrar(idventa) {
  $("#getCodeModal").modal("show");
  $.post(
    "controladores/venta.php?op=mostrar",
    { idventa: idventa },
    function (data, status) {
      data = JSON.parse(data);
      //mostrarform(true);

      $("#idventam").val(data.idventa);
      $("#cliente").val(data.cliente);
      $("#tipo_comprobantem").val(data.tipo_comprobante);
      $("#serie_comprobantem").val(data.serie_comprobante);
      $("#num_comprobantem").val(data.num_comprobante);
      $("#fecha_horam").val(data.fecha);
      $("#impuestom").val(data.impuesto);
      $("#formapagom").val(data.formapago);
      $("#nrooperacionm").val(data.numoperacion);
      $("#fechadeposito").val(data.fechadeposito);
      $("#idventam").val(data.idventa);
    },
  );

  $.post(
    "controladores/venta.php?op=listarDetalle&id=" + idventa,
    function (r) {
      $("#detallesm").html(r);
    },
  );
}

$("#getCodeModal").on("hidden.bs.modal", function () {
  $("body").addClass("modal-open");
});

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
              error: function () {},
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
              error: function () {},
            });
          }
        }
      }
    },
  );
}

function CrearMov() {
  verificarCaja()
    .then((cajaAbierta) => {
      if (cajaAbierta) {
        // Abre el modal
        $("#myModal").modal("show");
      } else {
        // Opcional: alerta si la caja no está abierta
        Swal.fire({
          icon: "warning",
          title: "Caja cerrada",
          text: "Primero debes abrir la caja para poder registrar movimientos.",
        });
      }
    })
    .catch((err) => {
      console.error("Error al verificar caja:", err);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "No se pudo verificar el estado de la caja.",
      });
    });
}

function guardaryeditarmovimiento2(e) {
  e.preventDefault();
  let formData = new FormData($("#formularioMovimiento2")[0]);
  formData.set("idcaja", $("#idcaja").val());
  formData.set("idsucursal", $("#idsucursal02").val());
  for (let pair of formData.entries()) {
    console.log(pair[0] + ": " + pair[1]);
  }
  $.ajax({
    url: "controladores/cajachica.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      Swal.fire({
        title: "Movimiento",
        icon: "success",
        text: datos,
      });

      $("#myModal").modal("hide");
      verificarCaja();
    },
  });
  limpiarmov();
}

function verificarConceptoMovimiento() {
  let tipo = "";

  if ($("#egresos").is(":checked")) {
    tipo = "egresos";
  } else if ($("#ingresos").is(":checked")) {
    tipo = "ingresos";
  }

  // Cargar los conceptos
  $.post(
    "controladores/cajachica.php?op=coceptoMovimiento&tipo=" + tipo,
    function (r) {
      $("#idconcepto_movimiento").html(r);
      $("#idconcepto_movimiento").select2();
    },
  );
}

function limpiarmov() {
  $("#formularioMovimiento")[0].reset();
  $("#idmovimiento").val("");
}

/* ====== Integración scanner cámara (Quagga) ======
   Usa las funciones existentes: searchProductos(...) y seleccionarProducto(...)
   No modifica tu servidor ni funciones PHP.
*/
(function () {
  var scannerRunning = false;
  var lastScanned = null;
  var scanCooldownMs = 400; // tiempo mínimo entre lecturas (no muy necesario si solo una lectura)
  var cooldownTimer = null;
  var readZoneRatio = 0.45; // aumentar zona para celulares

  function startScanner() {
    if (scannerRunning) return;
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      alert("El navegador no permite acceso a la cámara.");
      return;
    }

    // Pedimos la cámara trasera con la resolución más alta posible
    const constraints = {
      video: {
        facingMode: { ideal: "environment" },
        width: { ideal: 1280 },
        height: { ideal: 720 },
        focusMode: "continuous", // algunos navegadores lo reconocen
      },
    };

    Quagga.init(
      {
        inputStream: {
          name: "Live",
          type: "LiveStream",
          target: document.querySelector("#interactive-scanner"),
          constraints: constraints,
        },
        decoder: {
          readers: [
            "ean_reader",
            "ean_8_reader",
            "code_128_reader",
            "upc_reader",
            "code_39_reader",
          ],
        },
        locate: true,
        numOfWorkers: navigator.hardwareConcurrency
          ? Math.max(2, Math.floor(navigator.hardwareConcurrency / 2))
          : 2,
      },
      function (err) {
        if (err) {
          console.error(err);
          return;
        }
        Quagga.start();
        scannerRunning = true;
        lastScanned = null;
      },
    );

    Quagga.onProcessed(drawOverlay);
    Quagga.onDetected(onDetected);
  }

  function stopScanner() {
    if (!scannerRunning) return;
    try {
      Quagga.offDetected(onDetected);
      Quagga.stop();
    } catch (e) {}
    scannerRunning = false;
    lastScanned = null;
    if (cooldownTimer) {
      clearTimeout(cooldownTimer);
      cooldownTimer = null;
    }
    clearOverlay();
  }

  function drawOverlay(result) {
    var canvas = document.getElementById("scannerOverlay");
    var video = document.querySelector("#interactive-scanner video");
    if (!canvas || !video) return;
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    var ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    ctx.strokeStyle = "lime";
    ctx.lineWidth = 3;
    var yStart = canvas.height * (1 - readZoneRatio);
    ctx.strokeRect(0, yStart, canvas.width, canvas.height * readZoneRatio);
  }

  function clearOverlay() {
    var canvas = document.getElementById("scannerOverlay");
    if (canvas)
      canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
  }

  function onDetected(result) {
    if (!result || !result.codeResult || !result.codeResult.code) return;
    var code = result.codeResult.code;

    // Validar zona inferior
    if (result.line) {
      var yAvg = (result.line[0].y + result.line[1].y) / 2;
      var video = document.querySelector("#interactive-scanner video");
      if (video && yAvg < video.videoHeight * (1 - readZoneRatio)) return;
    }

    if (lastScanned === code) return;
    lastScanned = code;
    if (cooldownTimer) clearTimeout(cooldownTimer);
    cooldownTimer = setTimeout(function () {
      lastScanned = null;
    }, scanCooldownMs);

    handleScannedCode(code);
  }

  function handleScannedCode(code) {
    try {
      new Audio("files/audio/vip.mp3").play();
    } catch (e) {}

    // Buscar directamente el producto por código
    $.ajax({
      url: "controladores/pos.php?op=searchProductos",
      type: "GET",
      data: { producto: code, type: 2 }, // type=2 → búsqueda por código
      success: function (data) {
        var d = JSON.parse(data || "[]");

        if (d.length === 0) {
          Swal.fire({
            title: "Producto no encontrado",
            icon: "error",
            timer: 1200,
            showConfirmButton: false,
          });
          stopScanner();
          $("#cameraScannerModal").fadeOut(150);
          return;
        }

        // Si existe al menos un producto, agregamos el primero
        seleccionarProducto(btoa(JSON.stringify(d[0])));

        // Cerramos el scanner después de agregar
        setTimeout(function () {
          stopScanner();
          $("#cameraScannerModal").fadeOut(150);
        }, 300); // un pequeño delay para que se reproduzca el audio
      },
      error: function () {
        Swal.fire({
          title: "Error al buscar producto",
          icon: "error",
          timer: 1200,
          showConfirmButton: false,
        });
        stopScanner();
        $("#cameraScannerModal").fadeOut(150);
      },
    });
  }

  $(document).ready(function () {
    $("#btn_camera_search").on("click", function (e) {
      e.preventDefault();
      $("#cameraScannerModal").css("display", "flex").hide().fadeIn(120);
      startScanner();
    });
    $("#btn_stop_scanner").on("click", function (e) {
      e.preventDefault();
      stopScanner();
      $("#cameraScannerModal").fadeOut(120);
    });
    $("#cameraScannerModal").on("click", function (e) {
      if (e.target.id === "cameraScannerModal") {
        stopScanner();
        $("#cameraScannerModal").fadeOut(120);
      }
    });
  });
})();

init();
