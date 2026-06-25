var tabla;
var contador = 0;
var articuloAdd = "";
var cont = 0;
var detalles = 0;
var modoEditar = false;
var impuesto = 0;
var no_aplica = 1;

function actualizarResumenVenta(total, subtotal, impuestoCalculado) {
  const totalFmt = (parseFloat(total) || 0).toFixed(2);
  const subtotalFmt = (parseFloat(subtotal) || 0).toFixed(2);
  const impuestoFmt = (parseFloat(impuestoCalculado) || 0).toFixed(2);

  $("#total").html(totalFmt);
  $("#total_venta").val(totalFmt);
  $("#most_total2").val(totalFmt);
  $("#montoDeuda").val(totalFmt);

  $("#most_total, #sp-subtotal").html(subtotalFmt);
  $("#subtotal").val(subtotalFmt);

  $("#most_imp, #sp-impuesto").html(impuestoFmt);
  $("#impuesto").val(impuestoFmt);
}
function actualizarFilaVaciaDetalles() {
  var tbody = $("#detalles tbody");
  if (!tbody.length) return;

  var totalFilas = tbody.find("tr.filas").length;
  tbody.find("tr.fila-vacia-detalles").remove();

  if (totalFilas === 0) {
    tbody.append(
      '<tr class="fila-vacia-detalles"><td colspan="7" class="text-center text-muted">No hay productos agregados</td></tr>',
    );
  }
}

// Función para limpiar y reiniciar completamente el carrito
function limpiarCarrito() {
  $("#detalles tbody").empty();
  articuloAdd = "";

  // Reconstruir articuloAdd basándose en lo que realmente está en el carrito (post-limpieza)
  document
    .querySelectorAll('input[name="idproducto[]"]')
    .forEach(function (input) {
      articuloAdd = articuloAdd + input.value + "-";
    });

  contador = 0;
  cont = 0;
  detalles = 0;
  actualizarFilaVaciaDetalles();
  evaluar();
}

function setNavbarPosVisible(visible) {
  if (visible) {
    $("#navbar-pos").prop("hidden", false).show();
    return;
  }

  $("#navbar-pos").prop("hidden", true).hide();
}

function init() {
  $("#body").addClass("sidebar-collapse sidebar-mini");
  marcarImpuesto();
  mostrarform(false);
  listar();

  $("#formulario").on("submit", function (e) {
    guardaryeditar(e);
  });

  $("#formularioClientes").on("submit", function (e) {
    guardarCliente(e);
  });

  $("#formularioMovimiento").on("submit", function (e) {
    guardaryeditarmovimiento(e);
  });
  //cargamos los items al select comprobantes
  $.post("controladores/cotizaciones.php?op=selectCotizaciones", {is_aprobated: 1}, function (c) {
    $("#comprobanteReferencia").html(c);
    $("#comprobanteReferencia").select2("");
  });

  $.post("controladores/usuario.php?op=selectEmpleado", function (r) {
    $("#idpersonal").html(r);
    $("#idpersonal").select2("");
  });

  $.post("controladores/usuario.php?op=selectEmpleado", function (r) {
    $("#idpersonal02").html(r);
    $("#idpersonal02").select2("");
  });

  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal02").html(r);
    $("#idsucursal02").select2("");
  });

  // Cargar productos en el filtro
  $.post("controladores/venta.php?op=selectProductoFiltro", function (r) {
    $("#idproducto").html(r);
    $("#idproducto").select2();
  });

  //cargamos los items al celect comprobantes
  $.post("controladores/venta.php?op=selectTipoAcompanante", function (c) {
    $("#idtipoacompanante").html(c);
  });

  $("#tipo_comprobante").on("change", function () {
    var tipo_comprobante = $(this).val();
    var es_factura = tipo_comprobante === "Factura" ? "1" : "0";
    var cliente_actual = $("#idcliente").val(); // Guardar cliente seleccionado

    $.post(
      "controladores/venta.php?op=selectCliente",
      { tipo_documento: "", es_factura: es_factura },
      function (r) {
        $("#idcliente").html(r);

        // Solo restaurar si había un cliente seleccionado
        if (cliente_actual && cliente_actual !== "") {
          $("#idcliente").val(cliente_actual);
        }

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

  verificarConceptoMovimiento();
  cargarSucursales();
  $("#fecha_inicio").change(listar);
  $("#fecha_fin").change(listar);
  $("#idsucursal2").change(function () {
    listar();
    // Update notifications with new sucursal
    verificarNuevasNotificaciones();
    cargarNotificacionesCXCNavbar();
    // Limpiar y reiniciar completamente el carrito
    limpiarCarrito();
  });
  $("#estado").change(listar);
  $("#idproducto").change(listar);

  $("#idsucursal").change(documentosSucursal);

  $("#navPos").addClass("treeview active");
  $("#navPos").addClass("menu-open");
  $("#navCrearVenta").addClass("active");

  $("form").keypress(function (e) {
    if (e == 13) {
      return false;
    }
  });

  $("input").keypress(function (e) {
    if (e.which == 13) {
      return false;
    }
  });

  window.addEventListener(
    "keypress",
    function (event) {
      if (event.keyCode == 13) {
        event.preventDefault();
      }
    },
    false,
  );

  generarCuotas(100);
  actualizarFilaVaciaDetalles();
}

function initSelectAcompananteGarante() {
  const $modal = $("#modalAcompananteGarante");

  ["#idacompanante", "#idgarante"].forEach(function (selector) {
    const $select = $(selector);
    if (!$select.length) return;

    if ($select.hasClass("select2-hidden-accessible")) {
      $select.select2("destroy");
    }

    $select.select2({
      placeholder:
        selector === "#idacompanante"
          ? "Buscar acompañante..."
          : "Buscar garante...",
      allowClear: true,
      width: "100%",
      dropdownParent: $modal,
      minimumInputLength: 1,
      ajax: {
        url: "controladores/venta.php?op=buscarGarante",
        dataType: "json",
        delay: 250,
        data: function (params) {
          return {
            search: params.term,
          };
        },
        processResults: function (data) {
          return {
            results: data,
          };
        },
        cache: true,
      },
    });
  });
}

initSelectAcompananteGarante();

$("#modalAcompananteGarante").on("shown.bs.modal", function () {
  initSelectAcompananteGarante();
  $("#idacompanante").select2("open");
});

function generarCuotas(max = 100) {
  let select = $("#input_cuotas");
  let html = '<option value="" selected hidden>Seleccionar...</option>';

  for (let i = 1; i <= max; i++) {
    html += `<option value="${i}">${i}</option>`;
  }

  select.html(html);
}

function calcularCuotasDesdeNumeroMeses() {
  const frecuencia = parseInt($("#input_frecuencia").val(), 10);

  if (isNaN(frecuencia) || frecuencia <= 0) {
    swal.fire("Error", "La frecuencia ingresada no es válida.", "error");
    $("#numeroMeses").val("");
    return;
  }
  const mesesRaw = ("" + $("#numeroMeses").val() || "")
    .replace(",", ".")
    .trim();

  if (!mesesRaw || !frecuencia) {
    $("#input_cuotas").val("");
    return;
  }

  const numeroMeses = parseFloat(mesesRaw);
  if (isNaN(numeroMeses) || numeroMeses <= 0) {
    $("#input_cuotas").val("");
    return;
  }

  const mesesPorCuota = {
    1: 1 / 30,
    2: 1 / 4,
    3: 1 / 2,
    4: 1,
    5: 2,
    6: 3,
    7: 6,
    8: 12,
  }[frecuencia];

  if (!mesesPorCuota) return;

  const cuotasCalculadas = Math.max(1, Math.ceil(numeroMeses / mesesPorCuota));
  const maxCuotasActuales = $("#input_cuotas option").length - 1;

  if (cuotasCalculadas > maxCuotasActuales) {
    generarCuotas(cuotasCalculadas);
  }

  $("#input_cuotas").val(String(cuotasCalculadas));
}

$("#numeroMeses").on("input", calcularCuotasDesdeNumeroMeses);
$("#input_frecuencia").on("change", calcularCuotasDesdeNumeroMeses);

$("#comprobanteReferencia").on("change", function () {
  if (!$(this).val()) return; // prevenir ejecución automática
  mostrarE();
});

function cargarSucursales() {
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal").html(r);
    $("#idsucursal").select2("");

    $("#idsucursal2").html(r);
    $("#idsucursal2").select2("");

    $("#idsucursalVentas").html(r);
    $("#idsucursalVentas").select2("");
  });
}

document.addEventListener("DOMContentLoaded", function () {
  // Petición AJAX para obtener el cargo del usuario
  fetch("controladores/empleado.php?op=verificarAdmin") //
    .then((response) => response.json())
    .then((data) => {
      console.log("Respuesta del servidor:", data); // Depuración en la consola

      if (data.error) {
        console.error("Error:", data.error);
        return;
      }

      if (data.es_admin) {
        // Mostrar márgenes
        document.querySelectorAll('[id^="margen"]').forEach((elemento) => {
          elemento.style.display = "table-cell";
        });

        // Mostrar solo elementos con id que comiencen con "util" para el administrador
        document.querySelectorAll('[id^="util"]').forEach((elemento) => {
          elemento.style.display = "table-cell";
        });
      } else {
        // Ocultar márgenes
        document.querySelectorAll('[id^="margen"]').forEach((elemento) => {
          elemento.style.display = "none";
        });

        // Ocultar elementos con id que comiencen con "util" para los no administradores
        document.querySelectorAll('[id^="util"]').forEach((elemento) => {
          elemento.style.display = "none";
        });
      }
    })
    .catch((error) =>
      console.error("Error obteniendo el cargo del usuario:", error),
    );
});

function cargarItemsAlSelect() {
  // Cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal").html(r);
    $("#idsucursal").select2();
  });
}

//Función limpiar
function limpiarCliente() {
  $("#nombre").val("");
  $("#num_documento").val("");
  $("#direccion").val("");
  $("#telefono").val("");
  $("#email").val("");
  $("#fecha_hora").val("");
  $("#idpersona").val("");
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

$("#formapago").change(function () {
  verificarTipoPago();
});

function verificarTipoPago() {
  if (
    $("#formapago").val() == "Reposicion" ||
    $("#formapago").val() == "Costo0"
  ) {
    // $("#n1").hide();
    $("#f1").hide();
    $("#n5").hide();
    $("#n6").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
    $("#n0").hide();
    $("#b1").hide();
    $("#n1").hide();
    $("#n2").hide();
    $("#n3").hide();
    $("#n4").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
    $("#panel1").show();
  } else if (
    $("#formapago").val() == "Efectivo" &&
    $("#tipopago").val() == "No"
  ) {
    $("#n0").hide();
    $("#n1").hide();
    $("#n2").hide();
    $("#n3").hide();
    $("#f1").hide();
    $("#n5").hide();
    $("#n6").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
    $("#panel1").show();
  } else if (
    $("#formapago").val() == "Efectivo" &&
    $("#tipopago").val() == "Si"
  ) {
    $("#n0").show();
    $("#b1").show();
    $("#n1").show();
    $("#n2").show();
    $("#n3").show();
    $("#n4").show();
    $("#f1").hide();
    $("#n5").show();
    $("#n6").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
    $("#panel1").show();
  } else if (
    $("#formapago").val() == "Tarjeta" &&
    $("#tipopago").val() == "No"
  ) {
    $("#n6").show();
    $("#f1").hide();
    $("#n5").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
  } else if (
    $("#formapago").val() == "Reposicion" &&
    $("#tipopago").val() == "Si"
  ) {
    $("#f1").hide();
    $("#n5").hide();
    $("#n6").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
    $("#n0").show();
    $("#b1").show();
    $("#n1").show();
    $("#n2").show();
    $("#n3").show();
    $("#n4").show();
    $("#panel1").show();
  } else if (
    $("#formapago").val() == "Tarjeta" &&
    $("#tipopago").val() == "Si"
  ) {
    $("#f1").show();
    $("#n5").show();
    $("#n6").show();
    $("#fechadeposito").show();
    $("#banco").show();
    $("#banco").hide();
    $("#n0").show();
    $("#b1").show();
    $("#n1").show();
    $("#n2").show();
    $("#n3").show();
    $("#n4").show();
    $("#panel1").show();
  } else {
    // $('#n1').show();
    $("#f1").show();
    $("#n5").show();
    $("#n6").show();
    $("#fechadeposito").show();
    $("#banco").show();
  }
};

function obtenerMontoTotalPagar() {
  var totalInput = parseFloat($("#total_venta").val());
  if (!isNaN(totalInput) && totalInput > 0) return totalInput;

  var totalMostrado = parseFloat($("#most_total2").val());
  if (!isNaN(totalMostrado) && totalMostrado > 0) return totalMostrado;

  var totalReal = obtenerTotalVentaReal();
  if (!isNaN(totalReal) && totalReal > 0) return totalReal;

  return 0;
}

$("#tipopago").change(function () {
  if ($("#tipopago").val() == "Si") {
    if (detalles <= 0) {
      Swal.fire("Aun no ha seleccionado productos", "", "warning");
      $("#tipopago").val("No");
      return false;
    }

    // Reinicio estricto al entrar en crédito para no arrastrar cuotas anteriores
    $("#datafechas").html(
      '<tr><td colspan="4" class="text-center">No se han calculado las fechas de pago</td></tr>',
    );
    document.getElementById("input_cuotas").selectedIndex = 0;
    document.getElementById("input_frecuencia").selectedIndex = 0;
    $("#numeroMeses").val("");
    $("#inputInteres").val("0");
    $("#fechaOperacion").val(obtenerFechaHoyISO());

    $("#n0").show();
    $("#b1").show();
    $("#n1").show();

    $("#n2").show();

    $("#n3").show();
    $("#n4").show();
    $("#n5").show();
    $("#panel1").show();

    // Mostrar solo deuda a crédito
    var totalVentaCredito = obtenerMontoTotalPagar();
    $("#montoDeuda").val(totalVentaCredito.toFixed(2));

    // En crédito, iniciar pagos mixtos en cero
    if ($(".pagoItem").length === 0) {
      resetearPagos();
    }
    $(".pagoItem:first .metodoPago").val("Efectivo");
    $(".pagoItem:first .montoPago").val("0.00");
    $(".pagoItem:first .montoRealPago").val("0.00");
    $(".pagoItem:gt(0) .montoPago").val("0.00");
    $(".pagoItem:gt(0) .montoRealPago").val("0.00");
    $("#totalrecibido").val("0.00");
    $("#totaldeposito").val("0.00");
    recalcularPagos();

    // $('#fp2').show();

    // document.getElementById("n1").style.display = "none";
    // document.getElementById("f1").style.display = "none";
  } else {
    // $("#formapagoocultar").show();

    // Guardar deuda actual antes de limpiar para usarla como respaldo
    var deudaActual =
      parseFloat(("" + $("#montoDeuda").val()).replace(",", ".")) || 0;

    document.getElementById("panel1").style.display = "none";
    document.getElementById("b1").style.display = "none";
    document.getElementById("n0").style.display = "none";
    document.getElementById("n1").style.display = "none";

    document.getElementById("n2").style.display = "none";

    document.getElementById("n3").style.display = "none";
    document.getElementById("n4").style.display = "none";
    document.getElementById("n5").style.display = "none";

    // Limpiar cuotas calculadas y resetear campos de crédito
    $("#datafechas").html(
      '<tr><td colspan="4" class="text-center">No se han calculado las fechas de pago</td></tr>',
    );
    document.getElementById("input_cuotas").selectedIndex = 0;
    document.getElementById("input_frecuencia").selectedIndex = 0;
    $("#numeroMeses").val("");
    $("#inputInteres").val("0");
    var hoy = new Date();
    var yyyy = hoy.getFullYear();
    var mm = ("0" + (hoy.getMonth() + 1)).slice(-2);
    var dd = ("0" + hoy.getDate()).slice(-2);
    $("#fechaOperacion").val(yyyy + "-" + mm + "-" + dd);
    $("#montoDeuda").val("");

    // Restaurar pago mixto al contado con el total de venta
    var totalVenta = obtenerMontoTotalPagar();
    if (totalVenta <= 0 && deudaActual > 0) {
      totalVenta = deudaActual;
    }
    if ($(".pagoItem").length === 0) {
      $("#pagosMixtosContainer").html(`
        <div class="row mb-2 pagoItem">
          <div class="col-md-3">
            <select class="form-control metodoPago" name="metodo_pago[]">
              <option value="Efectivo" selected>Efectivo</option>
              <option value="Transferencia">Transferencia bancaria</option>
              <option value="Tarjeta">Tarjeta POS</option>
              <option value="Deposito">Depósito</option>
              <option value="Yape">Yape</option>
              <option value="Plin">Plin</option>
            </select>
          </div>
          <div class="col-md-2">
            <input type="text" class="form-control montoPago" name="monto_pago[]" placeholder="Monto" value="${totalVenta.toFixed(2)}">
            <input type="hidden" class="montoRealPago" name="monto_real_pago[]" value="${totalVenta.toFixed(2)}">
          </div>
          <div class="col-md-2">
            <input type="text" class="form-control nroOperacion" name="nroOperacion_pago[]" placeholder="N° Operación">
          </div>
          <div class="col-md-2 bancoContainer" style="display:none;">
            <input type="text" class="form-control bancoPago" name="banco_pago[]" placeholder="Banco">
          </div>
          <div class="col-md-3 fechaContainer" style="display:none;">
            <input type="date" class="form-control fechaDeposito" name="fecha_deposito_pago[]" placeholder="Fecha">
          </div>
          <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm removePago"><i class="fa fa-trash"></i></button>
          </div>
        </div>`);
    } else {
      $(".pagoItem:first .metodoPago").val("Efectivo");
      $(".pagoItem:first .montoPago").val(totalVenta.toFixed(2));
      $(".pagoItem:first .montoRealPago").val(totalVenta.toFixed(2));
      $(".pagoItem:gt(0) .montoPago").val("0.00");
      $(".pagoItem:gt(0) .montoRealPago").val("0.00");
    }
    $("#totalrecibido").val(totalVenta.toFixed(2));
    $("#totaldeposito").val("0.00");
    recalcularPagos();
  }
});

function comprobarEstado(idventa, idcol) {
  $url = "public/FACT_WebService/Facturacion/consultacdr.php?idventa=";

  $.ajax({
    url: $url + idventa + "&codColab=" + idcol,

    type: "get",
    dataType: "text",
    beforeSend: function () {
      $(".modal").show();
    },
    success: function (resp) {
      listar();

      swal({
        title: "SUNAT",
        icon: "success",
        text: resp,
      });
    },
    complete: function () {
      $(".modal").hide();
    },
  });
}

function EnviarSunat(tipoc, idventa, idcol) {
  if (tipoc == 1) {
    $url = "public/FACT_WebService/Facturacion/boleta.php?idventa=";
  } else {
    $url = "public/FACT_WebService/Facturacion/factura.php?idventa=";
  }

  $.ajax({
    url: $url + idventa + "&codColab=" + idcol,

    type: "get",
    dataType: "text",
    beforeSend: function () {
      $(".modal").show();
    },
    success: function (resp) {
      listar();

      Swal.fire({
        title: "SUNAT",
        icon: "success",
        text: resp,
        timerProgressBar: true,
        onClose: function () { },
      });
    },
    complete: function () {
      $(".modal").hide();
    },
  });
}

// Variable global para guardar la venta a enviar a Sunat
var ventaAGenerarSunat = null;

function guardaryeditar(e) {
  //  Protección contra null
  if (e) e.preventDefault();

  if (detalles <= 0) {
    Swal.fire("Agrega productos a la venta", "", "warning");
    return false;
  }

  const idcliente = parseInt($("#idcliente").val());
  const tipopago = $("#tipopago").val().trim();
  const lista = $("#datafechas");

  if (idcliente === 6 && tipopago === "Si") {
    Swal.fire(
      "Selecciona un cliente para realizar una venta a credito",
      "",
      "warning",
    );
    return false;
  }

  if (lista.children().length === 0 && tipopago === "Si") {
    Swal.fire(
      "No se ha calculado el numero de cuotas y fechas de pago",
      "",
      "warning",
    );
    return false;
  }

  if (tipopago === "No") {
    const totalVenta = parseFloat($("#total_venta").val()) || 0;
    let totalPagado = 0;

    $(".montoPago").each(function () {
      const monto = parseFloat(("" + $(this).val()).replace(",", ".")) || 0;
      totalPagado += monto;
    });

    if (totalPagado + 0.0001 < totalVenta) {
      Swal.fire(
        "Monto insuficiente",
        "La suma de pagos (efectivo y depósito) es menor al total de la venta.",
        "warning",
      );
      return false;
    }
  }

  var formData = new FormData($("#formulario")[0]);

  Swal.fire({
    title: "Procesando venta...",
    text: "Por favor, espera un momento",
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading(),
  });

  $.ajax({
    url: "controladores/venta.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (datos) {
      console.log("Respuesta del servidor:", datos); // Depuración en la consola
      Swal.close();

      if (!datos || datos.trim() === "") {
        Swal.fire(
          "Error en la venta",
          "No se pudo registrar la venta.",
          "error",
        );
        return;
      }

      // ============================================================
      // VALIDAR SI LA RESPUESTA ES UN ERROR DE PERMISOS DE FECHA
      // ============================================================
      try {
        var response = JSON.parse(datos);
        if (response.status === "error") {
          Swal.fire({
            title: "Permiso Denegado",
            text: response.mensaje,
            icon: "error",
            confirmButtonText: "Entendido",
          });
          return;
        }
      } catch (e) {
        // Si no es JSON, continúa con el flujo normal
        console.log(e);
      }
      // ============================================================

      if ($("#tipo_comprobante option:selected").text() !== "Nota de Venta") {
        ventaAGenerarSunat = {
          idventa: datos,
          tipo:
            $("#tipo_comprobante option:selected").text() == "Boleta" ? 1 : 2,
          idpersonal: $("#idpersonal").val(),
        };
      } else {
        ventaAGenerarSunat = null;
      }

      $("#ModalTipocomprobante").modal("show");
      $("#pant-imprimir").html(`
        <div onclick="imprimirBoleta(${datos}, true)" class="col-sm-6 btn btn-success">
          <i class="fas fa-ticket-alt"></i> TICKET
        </div>
        <div onclick="imprimirFactura(${datos}, true)" class="col-sm-6 btn btn-info">
          <i class="fas fa-file-pdf"></i> PDF
        </div>
      `);

      $("#formulario")[0].reset();
      marcarImpuesto();
      resetearPagos();
      $("#tbllistado").DataTable().ajax.reload();
      $("#datafechas").empty();
      cargarItemsAlSelect();
    },
    error: function () {
      Swal.close();
      Swal.fire(
        "Error de conexión",
        "No se pudo conectar con el servidor.",
        "error",
      );
    },
  });
}

// Enganchar evento cuando el modal de imprimir se cierre
$("#ModalTipocomprobante").on("hidden.bs.modal", function () {
  if (ventaAGenerarSunat) {
    EnviarSunat(
      ventaAGenerarSunat.tipo,
      ventaAGenerarSunat.idventa,
      ventaAGenerarSunat.idpersonal,
    );
    ventaAGenerarSunat = null; // Limpiar para evitar reenvíos
  }
});

$("#btnGuardar").on("click", function (e) {
  guardaryeditar(e);
});

function resetearPagos() {
  // Limpiar contenedor de pagos
  $("#pagosMixtosContainer").empty();

  // Agregar la fila de pago inicial con monto 0
  let filaInicial = `
    <div class="row mb-2 pagoItem">
        <div class="col-md-3">
            <select class="form-control metodoPago" name="metodo_pago[]">
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia bancaria</option>
                <option value="Tarjeta">Tarjeta POS</option>
                <option value="Deposito">Depósito</option>
                <option value="Yape">Yape</option>
                <option value="Plin">Plin</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control montoPago" name="monto_pago[]" placeholder="Monto" value="0">
            <input type="hidden" class="montoRealPago" name="monto_real_pago[]" value="0">
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control nroOperacion" name="nroOperacion_pago[]" placeholder="N° Operación">
        </div>
        <div class="col-md-2 bancoContainer" style="display:none;">
            <input type="text" class="form-control bancoPago" name="banco_pago[]" placeholder="Banco">
        </div>
        <div class="col-md-3 fechaContainer" style="display:none;">
            <input type="date" class="form-control fechaDeposito" name="fecha_deposito_pago[]" placeholder="Fecha">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm removePago"><i class="fa fa-trash"></i></button>
        </div>
    </div>`;

  $("#pagosMixtosContainer").append(filaInicial);

  // Recalcular pagos
  recalcularPagos();
}

// Recalcular al cambiar monto o método
// --- Funciones para calcular ---
function calcularTotalRecibido() {

  let totalRecibido = 0;

  $(".pagoItem").each(function () {
    let metodo = $(this).find(".metodoPago").val();
    let monto = Number(
      ($(this).find(".montoPago").val() || "0").replace(",", ".")
    );

    if (isNaN(monto)) monto = 0;
    if (metodo === "Efectivo") {
      totalRecibido += monto;
    }
  });

  $("#totalrecibido").val(totalRecibido.toFixed(2));

  let montoDeudaOriginal =
    parseFloat($("#total_venta").val()) || 0;

  if ($("#tipopago").val() === "Si") {
    let montoRestante = montoDeudaOriginal - totalRecibido;
    if (montoRestante < 0) {
      montoRestante = 0;
    }
    $("#montoDeuda").val(montoRestante.toFixed(2));
  }

  return totalRecibido;
}

function calcularTotalDeposito() {
  let totalDeposito = 0;
  $(".pagoItem").each(function () {
    let metodo = $(this).find(".metodoPago").val();
    let monto =
      parseFloat($(this).find(".montoPago").val().replace(",", ".")) || 0;
    if (metodo !== "Efectivo") totalDeposito += monto;
  });
  $("#totaldeposito").val(totalDeposito.toFixed(2));
  return totalDeposito;
}

$(document).ready(function () {
  // Inicializar primer pago
  precargarPrimerPago();

  // Recalcular cuando cambia monto o método
  $(document).on("keyup change", ".montoPago, .metodoPago", recalcularPagos);

  // Recalcular cuando cambia cantidad o precio de los detalles
  $(document).on(
    "keyup change",
    "input[name='cantidad[]'], input[name='precio_venta[]']",
    function () {
      actualizarMontoPrimerPago();
      recalcularPagos();
    },
  );

  $(document).on("change", ".metodoPago", function () {
    let metodo = $(this).val();
    let fila = $(this).closest(".pagoItem");

    if (metodo === "Deposito" || metodo === "Transferencia") {
      fila.find(".bancoContainer, .fechaContainer").show();
    } else {
      fila.find(".bancoContainer, .fechaContainer").hide();
      fila.find(".bancoPago, .fechaDeposito").val("");
    }

    recalcularPagos();
  });

  // Agregar nuevo pago
  $("#addPago").click(function () {
    let totalVenta = calcularTotalVenta();
    let totalPagado = 0;

    $(".montoPago").each(function () {
      totalPagado += parseFloat($(this).val().replace(",", ".")) || 0;
    });

    let montoRestante = totalVenta - totalPagado;
    if (montoRestante < 0) montoRestante = 0;

    let nuevaFila = `
        <div class="row mb-2 pagoItem">
            <div class="col-md-3">
                <select class="form-control metodoPago" name="metodo_pago[]">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia">Transferencia bancaria</option>
                    <option value="Tarjeta">Tarjeta POS</option>
                    <option value="Deposito">Depósito</option>
                    <option value="Yape">Yape</option>
                    <option value="Plin">Plin</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control montoPago" name="monto_pago[]" placeholder="Monto" value="${montoRestante.toFixed(2)}">
                <input type="hidden" class="montoRealPago" name="monto_real_pago[]" value="${montoRestante.toFixed(2)}">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control nroOperacion" name="nroOperacion_pago[]" placeholder="N° Operación">
            </div>
            <div class="col-md-2 bancoContainer" style="display:none;">
                <input type="text" class="form-control bancoPago" name="banco_pago[]" placeholder="Banco">
            </div>
            <div class="col-md-3 fechaContainer" style="display:none;">
                <input type="date" class="form-control fechaDeposito" name="fecha_deposito_pago[]" placeholder="Fecha">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm removePago"><i class="fa fa-trash"></i></button>
            </div>
        </div>`;

    $("#pagosMixtosContainer").append(nuevaFila);
    recalcularPagos();
  });

  // Eliminar pago
  $(document).on("click", ".removePago", function () {
    if ($(".pagoItem").length > 1) {
      $(this).closest(".pagoItem").remove();
      recalcularPagos();
    }
  });
});

// ------------------ FUNCIONES ------------------

// Precargar primer pago
function precargarPrimerPago() {
  actualizarMontoPrimerPago();
  recalcularPagos();
}

// Calcular total de venta desde los detalles existentes
function calcularTotalVenta() {
  return obtenerTotalVentaReal();
}

function obtenerTotalVentaReal() {
  let total = 0;

  $("#detalles tbody tr").each(function () {
    const idp = $(this).find('input[name="idp[]"]').val();
    const chk = document.getElementById("chkPrecioSegunCantidad-" + idp);

    const cantidad =
      parseFloat($(this).find('input[name="cantidad[]"]').val()) || 0;
    const precio =
      parseFloat($(this).find('input[name="precio_venta[]"]').val()) || 0;

    // ⛔ Aqui estaba el problema: debes obtener el descuento POR FILA
    const descuento =
      parseFloat($(this).find('input[name="descuento[]"]').val()) || 0;

    let subtotal = 0;

    if (chk && chk.checked) {
      // precio fijo
      subtotal = precio;
    } else {
      // descuento aplicado solo a esta fila
      subtotal = cantidad * precio - descuento;

      if (subtotal < 0) subtotal = 0;
    }

    total += subtotal;
  });

  return parseFloat(total.toFixed(2));
}

// Actualizar el primer pago automáticamente
function actualizarMontoPrimerPago() {

  let totalVenta = obtenerTotalVentaReal();

  // MONTO INICIAL
  let inicial = parseFloat($("#montoPagado").val()) || 0;

  let primeraFila = $(".pagoItem").first();

  let montoInput = primeraFila.find(".montoPago");

  // FORZAR PRIMER PAGO COMO EFECTIVO
  primeraFila.find(".metodoPago").val("Efectivo");

  // Solo actualizar si el usuario no editó manualmente
  if (!montoInput.data("editado")) {

    // SI HAY INICIAL USARLA
    let montoMostrar = inicial > 0 ? inicial : totalVenta;

    montoInput.val(montoMostrar.toFixed(2));

    primeraFila
      .find(".montoRealPago")
      .val(montoMostrar.toFixed(2));
  }

  montoInput.off("keyup").on("keyup", function () {
    $(this).data("editado", true);
  });
}

// Función principal de recalculo de pagos
function recalcularPagos() {
  let totalVenta = obtenerTotalVentaReal();
  let totalRecibido = 0;
  let totalDeposito = 0;
  let totalPagadoCliente = 0;

  $(".pagoItem").each(function () {
    let metodo = $(this).find(".metodoPago").val();
    let monto =
      parseFloat($(this).find(".montoPago").val().replace(",", ".")) || 0;
    totalPagadoCliente += monto;

    if (metodo === "Efectivo") {
      totalRecibido += monto;
    } else {
      totalDeposito += monto;
    }
  });

  $("#totalrecibido").val(totalRecibido.toFixed(2));
  $("#totaldeposito").val(totalDeposito.toFixed(2));

  // Si en modo crédito, actualiza el monto restante automáticamente usando todos los pagos mixtos
  if ($("#tipopago").val() === "Si") {
    let montoOriginalDeuda = parseFloat($("#total_venta").val()) || 0;
    let montoPagado = totalPagadoCliente;
    let montoRestante = montoOriginalDeuda - montoPagado;
    if (montoRestante < 0) montoRestante = 0;
    $("#montoDeuda").val(montoRestante.toFixed(2));
  }

  // Calcular vuelto
  let vuelto = totalPagadoCliente - totalVenta;
  if (vuelto < 0) vuelto = 0;
  $("#vuelto").val(vuelto.toFixed(2));
}

//Función limpiar
function limpiardatafecha() {
  $("#datafechas").val("");
}

function imprimirBoleta(id, status = false) {
  $("#ModalTipocomprobante").modal("hide");
  window.open("reportes/exTicket.php?id=" + id, "IMPRIMIR BOLETA");
  mostrarform(status);
}

function imprimirFactura(id, status = false) {
  $("#ModalTipocomprobante").modal("hide");
  window.open(
    "reportes/factura/generaFactura.php?id=" + id,
    "IMPRIMIR FACTURA",
  );
  mostrarform(status);
}

function sinComprobante() {
  $("#ModalTipocomprobante").modal("hide");
  mostrarform(true);
}

//funcion para Guardar Clientes
function guardarCliente(e) {
  e.preventDefault(); //no se activara la accion predeterminada
  //$("#btnGuardar").prop("disabled",true);
  var formData = new FormData($("#formularioClientes")[0]);

  $.ajax({
    url: "controladores/venta.php?op=guardarCliente",
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
      //cargamos los items al select cliente
      $.post("controladores/venta.php?op=selectCliente", function (r) {
        $("#idcliente").html(r);
        $("#idcliente").select2("");
      });

      $.post(
        "controladores/venta.php?op=mostrarUltimoCliente",
        function (data, status) {
          data = JSON.parse(data);

          seleccionarCliente(data.nombre, data.idpersona);
        },
      );
    },
  });

  $("#ModalClientes").modal("hide");

  limpiarCliente();
}

function seleccionarCliente(nombre, idcliente) {
  $("#idcliente").val(idcliente);
  $("#idcliente").select2("");
}

function documentosSucursal() {
  // No forzar tipo comprobante aquí
  let firstOption = $("#tipo_comprobante option:first").val();
  if (firstOption) {
    $("#tipo_comprobante").val(firstOption).trigger("change");
    marcarImpuesto(); // Esto ya llama a numTicket, numBoleta, etc.
  }
}

function limpiar() {
  $("#idventa").val("");
  $("#idcliente").val("");
  $("#cliente").val("");
  $("#serie_comprobante").val("");
  $("#num_comprobante").val("");
  // $("#impuesto").val("");
  articuloAdd = "";
  impuesto = 0;
  no_aplica = 0;

  $("#total_venta").val("");
  $(".filas").remove();
  actualizarResumenVenta(0, 0, 0);

  //obtenemos la fecha actual
  var now = new Date();
  var day = ("0" + now.getDate()).slice(-2);
  var month = ("0" + (now.getMonth() + 1)).slice(-2);
  var today = now.getFullYear() + "-" + month + "-" + day;
  $("#fecha").val(today);

  let firstOption = $("#tipo_comprobante option:first").val();
  if (firstOption) {
    $("#tipo_comprobante").val(firstOption).trigger("change");
    marcarImpuesto(); // Carga serie y número correspondientes
  }

  $("#idcliente").val("6");

  $("#idcliente").select2("");

  $("#porcentaje").val("");

  $("#observaciones").val("");

  $("#comprobanteReferencia").val("");

  $("#comprobanteReferencia")
    .select2({
      placeholder: "Seleccionar Comprobante ...",
      allowClear: true,
    })
    .val(null)
    .trigger("change");

  $("#totalrecibido").val(0);
  $("#totaldeposito").val(0);
  $("#vuelto").val(0);
  $("#montoDeuda").val(0);
  $("#input_cuotas").val("");
  $("#numeroMeses").val("");

  $("#panel1").hide();
  $("#b1").hide();
  $("#n0").hide();
  $("#n1").hide();
  $("#n2").hide();
  $("#n3").hide();
  $("#n4").hide();
  $("#f1").hide();
  $("#n5").hide();
  $("#n6").hide();
  $("#fechadeposito").hide();
  $("#banco").hide();
  $("#fechadeposito").hide();
  $("#banco").hide();

  $("#formapago").val("Efectivo");

  $("#porcentaje").val(0);
  $("#nroOperacion").val("");
  $("#totalrecibido").val(0);
  $("#totaldeposito").val(0);
  $("#vuelto").val(0);
  $("#observaciones").val("");

  $("#tipopago").val("No");
  $("#montoPagado").val(0);

  $("#fechaDepostivo").val("");

  mostrar_impuesto();
}

function buscarProductoCod(e, codigo) {
  if (e.keyCode === 13) {
    if (codigo.length > 0) {
      $.post(
        "controladores/venta.php?op=buscarProducto",
        { codigo: codigo },
        function (data, status) {
          data = JSON.parse(data);

          if (data == null) {
            alert("Producto no encontrado");
          } else {
            agregarDetalle(
              data.idproducto,
              data.nombre,
              1,
              0,
              data.precio,
              data.preciocigv,
              data.precioB,
              data.precioC,
              data.precioD,
              data.stock,
              data.proigv,
              data.unidadmedida,
            );
          }

          $("#idCodigoBarra").val("");
        },
      );
    }
  }
}

function limpiarDetalle() {
  detalles = 0;

  evaluar();

  if (contador != 0) {
    for (var i = 0; i <= contador; i++) {
      $("#fila" + i).remove();
      calcularTotales();
      evaluar();
      articuloAdd = "";
    }
  }
  actualizarFilaVaciaDetalles();
}

$("#idsucursal").change(function () {
  listarArticulosSearchFIFO();
  listarArticulos2(); // Llama a la función para actualizar los artículos al cambiar de sucursal
  // Limpiar el carrito al cambiar de sucursal para evitar productos no disponibles
  limpiarCarrito();
});

function verificarProductosDisponibles() {
  var idsucursal = $("#idsucursal").val();

  // Obtener todos los IDs de productos actualmente agregados
  var productosAgregados = [];
  $("input[name='idproducto[]']").each(function () {
    productosAgregados.push($(this).val());
  });

  // Verificar cada producto si está disponible en la nueva sucursal
  $.post(
    "controladores/venta.php?op=verificarProductos",
    { idsucursal: idsucursal, productos: productosAgregados },
    function (response) {
      if (response.no_disponibles.length > 0) {
        // Eliminar los productos no disponibles de la tabla
        response.no_disponibles.forEach(function (idproducto) {
          eliminarProductoDeTabla(idproducto);
          Swal.fire(
            "Advertencia",
            "El producto con ID " +
            idproducto +
            " no existe en el almacén seleccionado.",
            "warning",
          );
        });
        evaluar();
      }
    },
    "json",
  );
}

function eliminarProductoDeTabla(idproducto) {
  $("#detalles tr").each(function () {
    var id = $(this).find("input[name='idproducto[]']").val();
    if (id === idproducto) {
      $(this).remove(); // Eliminar la fila
      // Actualizar el contador de detalles
      detalles--;
      modificarSubtotales(); // Actualizar subtotales después de eliminar
    }
  });
  actualizarFilaVaciaDetalles();
}

$("#search_product").keyup(function (e) {
  var search = $(this).val();
  listarArticulosSearchFIFO(search);
});

if (window.localStorage.getItem("type_search")) {
  var search = window.localStorage.getItem("type_search");
  if (search === "1") {
    $("#btn_text_search").addClass("active-search");
    $("#btn_barcode_search").removeClass("active-search");
    $("#search_product").attr("placeholder", "Buscar producto por nombre");
  }
  if (search === "2") {
    $("#btn_barcode_search").addClass("active-search");
    $("#btn_text_search").removeClass("active-search");
    $("#search_product").attr(
      "placeholder",
      "Buscar producto por codigo de barras",
    );
  }
} else {
  $("#btn_text_search").addClass("active-search");
  $("#search_product").attr("placeholder", "Buscar producto por nombre");
}

function activeSearch(index) {
  window.localStorage.setItem("type_search", index);
  if (index === 1) {
    $("#btn_text_search").addClass("active-search");
    $("#btn_barcode_search").removeClass("active-search");
    $("#search_product").attr("placeholder", "Buscar producto por nombre");
  }
  if (index === 2) {
    $("#btn_barcode_search").addClass("active-search");
    $("#btn_text_search").removeClass("active-search");
    $("#search_product").attr(
      "placeholder",
      "Buscar producto por codigo de barras",
    );
  }
}

function listarArticulosSearchFIFO(search = "") {
  var idsucursal = $("#idsucursal").val();
  var type = window.localStorage.getItem("type_search") || 1;

  $.ajax({
    url: "controladores/venta.php?op=listarArticulosSearchFIFO",
    type: "GET",
    dataType: "json",
    data: { idsucursal, search, type },
    success: function (data) {
      if (!Array.isArray(data)) return;

      let html = "";
      data.forEach((item) => {
        html += `
          <tr>
            <td>${item.stock}</td>
            <td>${item.product}</td>
            <td>${item.cat}</td>
            <td>${item.code}</td>
            <td>${item.quantity}</td>
            <td>${item.price}</td>
          </tr>`;
      });

      $("#tbody_articulos").html(html);
    },
  });
}

function listarArticulosSearch(search) {
  var idsucursal = $("#idsucursal").val();
  var type = window.localStorage.getItem("type_search");

  $.ajax({
    url: "controladores/venta.php?op=listarArticulosSearch",
    data: { idsucursal: idsucursal, search: search, type: type },
    type: "get",
    dataType: "json",
    success: function (data) {
      console.log(data);

      // Validamos que sea un array
      if (!Array.isArray(data)) {
        console.error("Respuesta inesperada del servidor:", data);
        return;
      }
      if (data.length === 1 && type == 2) {
        let producto = data[0];
        $("#search_product").val("");

        if (parseFloat(producto.stock_num) > 0) {
          agregarDetalle(
            producto.id,
            producto.idproducto,
            producto.nombre,
            1,
            0,
            producto.precio_venta,
            producto.preciocigv,
            producto.precioB,
            producto.precioC,
            producto.precioD,
            producto.stock_num,
            producto.proigv,
            producto.cantidad_contenedor,
            producto.contenedor,
            producto.idcategoria,
          );
        } else {
          Swal.fire({
            icon: "error",
            title: "No hay suficiente stock",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
          });
        }
      }
      var html = "";
      data.forEach(function (item) {
        html += `<tr class="odd">
          <td>${item.stock}</td>
          <td class="sorting_1">${item.product}</td>
          <td class="sorting_2">${item.cat}</td>
          <td class="sorting_3">${item.code}</td>
          <td>${item.quantity}</td>
          <td>${item.price}</td>
        </tr>`;
      });

      $("#tbody_articulos").html(html);
    },
    error: function (e) {
      console.log(e.responseText);
    },
  });
}

function selectTab(index) {
  if (index == 1) {
    $("#div_search").attr("hidden", false);
  }

  if (index == 2) {
    $("#div_search").attr("hidden", true);
  }
}

function listarArticulos() {
  var idsucursal = $("#idsucursal").val();
  console.log(idsucursal);
  tabla = $("#tblarticulos")
    .dataTable({
      aProcessing: true, //activamos el procedimiento del datatable
      aServerSide: true, //paginacion y filrado realizados por el server
      dom: "Bfrtip", //definimos los elementos del control de la tabla
      buttons: [],
      ajax: {
        url: "controladores/venta.php?op=listarArticulos",
        data: { idsucursal: idsucursal },
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 5, //paginacion
      order: [
        [1, "asc"],
        [2, "asc"],
      ], //ordenar (columna, orden)
    })
    .DataTable();
}

function listarArticulos2() {
  var idsucursal = $("#idsucursal").val();

  tabla = $("#tblarticulos2")
    .dataTable({
      language: {
        emptyTable: "No se encontraron productos en el almacén.",
        zeroRecords: "No se encontraron productos.",
        infoEmpty: "Sin registros disponibles",
      },
      aProcessing: true, //activamos el procedimiento del datatable
      aServerSide: true, //paginacion y filrado realizados por el server
      dom: "Bfrtip", //definimos los elementos del control de la tabla
      buttons: [],
      ajax: {
        url: "controladores/venta.php?op=listarArticulos2",
        data: { idsucursal: idsucursal },
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 5, //paginacion
      order: [
        [1, "asc"],
        [2, "asc"],
      ], //ordenar (columna, orden)
    })
    .DataTable();
}

function verimagen(idproducto, imagen, nombre, stock, precio) {
  $("#modalDetalleProducto").modal("show");

  // Mostrar imagen
  $("#detalleImagenProducto").attr("src", "files/productos/" + imagen);

  // Construir contenido
  let html = `
  <div class="col-md-6 mb-2">
    <div class="border rounded p-2"><strong>Nombre:</strong> ${nombre}</div>
  </div>
  <div class="col-md-6 mb-2">
    <div class="border rounded p-2"><strong>Stock:</strong> ${stock}</div>
  </div>
  <div class="col-md-6 mb-2">
    <div class="border rounded p-2"><strong>Precio:</strong> S/ ${precio}</div>
  </div>
`;
  $("#detalleProductoContenido").html(html);

  // Obtener precios adicionales
  $.post(
    "controladores/producto.php?op=precios_adicionales",
    { idproducto: idproducto },
    function (data) {
      $("#detallePreciosAdicionales").html(data);
    },
  );
}

// Cerrar y resetear el modal al hacer clic en el botón
$(document).on("click", "#btnCerrarModalProducto", function () {
  $("#modalDetalleProducto").modal("hide");

  // Esperar a que termine la animación antes de resetear
  setTimeout(() => {
    // Resetear imagen
    $("#detalleImagenProducto").attr("src", "");

    // Vaciar contenido de detalles y precios
    $("#detalleProductoContenido").html("");
    $("#detallePreciosAdicionales").html("<i>Cargando...</i>");

    // Volver a activar el tab de imagen
    $('#detalleProductoTabs a[href="#tab-imagen"]').tab("show");
  }, 300); // espera a que el modal se oculte completamente
});

function listarArticulos2() {
  var idsucursal = $("#idsucursal").val();

  tabla = $("#tblarticulos2")
    .dataTable({
      aProcessing: true, //activamos el procedimiento del datatable
      aServerSide: true, //paginacion y filrado realizados por el server
      dom: "Bfrtip", //definimos los elementos del control de la tabla
      buttons: [],
      ajax: {
        url: "controladores/venta.php?op=listarArticulos2",
        data: { idsucursal: idsucursal },
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 5, //paginacion
      order: [
        [1, "asc"],
        [2, "asc"],
      ], //ordenar (columna, orden)
    })
    .DataTable();
}

//Función Listar
function listar() {
  let fecha_inicio = $("#fecha_inicio").val();
  let fecha_fin = $("#fecha_fin").val();
  var estado = $("#estado").val();
  let idsucursal2 = $("#idsucursal2").val();
  let idproducto = $("#idproducto").val();

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
        url: "controladores/venta.php?op=listar",
        data: {
          fecha_inicio: fecha_inicio,
          fecha_fin: fecha_fin,
          estado: estado,
          idsucursal2: idsucursal2,
          idproducto: idproducto,
        },
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

//cancelar form
function cancelarform() {
  // Limpiar todos los campos del formulario
  limpiar(); // tu función que limpia inputs y selects
  mostrarform(false); // oculta el formulario

  // Resetear inputs tipo text, number, textarea y selects
  $("#formulario")[0].reset(); // reemplaza 'formulario' por el id de tu <form>

  // Resetear variables JS
  detalles = 0;
  articuloAdd = "";
  modoEditar = false;

  // Limpiar tablas de detalles
  $("#detalles tbody").empty();

  // 🔹 Limpiar los pagos dinámicos del modal
  $("#pagosMixtosContainer").empty();

  // Opcional: si quieres dejar al menos 1 fila por defecto (Efectivo, monto 0)
  let filaDefault = `
        <div class="row mb-2 pagoItem">
            <div class="col-md-3">
                <select class="form-control metodoPago" name="metodo_pago[]">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Transferencia">Transferencia bancaria</option>
                    <option value="Tarjeta">Tarjeta POS</option>
                    <option value="Deposito">Depósito</option>
                    <option value="Yape">Yape</option>
                    <option value="Plin">Plin</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control montoPago" name="monto_pago[]" placeholder="Monto" value="0.00">
                <input type="hidden" class="montoRealPago" name="monto_real_pago[]" value="0.00">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control nroOperacion" name="nroOperacion_pago[]" placeholder="N° Operación">
            </div>
            <div class="col-md-2 bancoContainer" style="display:none;">
                <input type="text" class="form-control bancoPago" name="banco_pago[]" placeholder="Banco">
            </div>
            <div class="col-md-3 fechaContainer" style="display:none;">
                <input type="date" class="form-control fechaDeposito" name="fecha_deposito_pago[]" placeholder="Fecha">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm removePago"><i class="fa fa-trash"></i></button>
            </div>
        </div>`;
  $("#pagosMixtosContainer").append(filaDefault);
}

//cancelar form
function cancelarform2() {
  // Cerrar el modal
  limpiar();
  mostrarform(false);
}

//Función mostrar formulario
async function mostrarform(flag) {
  limpiar();

  if (flag) {
    const idsucursalSeleccionada = $("#idsucursal2").val();
    const nombreSucursal = $("#idsucursal2 option:selected").text();

    if (!idsucursalSeleccionada) {
      Swal.fire(
        "Atención",
        "Seleccione una sucursal antes de crear una venta.",
        "warning",
      );
      return;
    }

    try {
      const tieneCaja = await verificarCajaPorSucursal(idsucursalSeleccionada);

      if (tieneCaja) {
        setNavbarPosVisible(true);
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#aperturarcaja").hide();
        $("#btnagregar").hide();
        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        $("#btnAgregarArt").show();
        $("#btnNuevo").hide();
        $("#header").hide();
        //$("body").addClass("sidebar-collapse");
        $("#idsucursal").val(idsucursalSeleccionada).trigger("change.select2");
        marcarImpuesto(idsucursalSeleccionada);

        listarArticulosSearchFIFO();
        listarArticulos2();
        verificarCaja();
      } else {
        $("#listadoregistros").hide();
        $("#formularioregistros").hide();
        $("#aperturarcaja").show();
        $("#btnagregar").hide();
        $("#btnGuardar").hide();
        $("#btnCancelar").hide();
        $("#btnAgregarArt").hide();
        $("#btnNuevo").hide();
        $("#header").hide();
        listarCajas();
      }
    } catch (error) {
      console.error("Error al verificar caja:", error);
      Swal.fire("Error", "No se pudo verificar la caja.", "error");
    }
  } else {
    $("#listadoregistros").show();
    $("#formularioregistros").hide();
    $("#aperturarcaja").hide();
    $("#btnagregar").show();
    $("#btnNuevo").show();
    $("#header").show();
    $("#btnGuardar").show();
    //$("body").removeClass("sidebar-collapse");
    $("#navbar-pos2").hide().empty();
    setNavbarPosVisible(false);
    $("#navbar-poss").show();
  }
}

$("#idsucursal2").on("change", async function () {
  const idsucursal = $(this).val();
  if (!idsucursal) return;

  const tieneCaja = await verificarCajaPorSucursal(idsucursal);
  if (!tieneCaja) {
    Swal.fire({
      icon: "info",
      title: "Caja no abierta",
      text: "No tienes una caja abierta en esta sucursal. Deberás aperturar una antes de vender.",
      timer: 2500,
      showConfirmButton: false,
    });
  }
});

function verificarCaja() {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "controladores/venta.php?op=verificar_caja",
      type: "get",
      dataType: "json",
      success: function (response) {
        $("#navbar-poss").hide(); // ocultas el navbar general del cabezote
        $("#navbar-pos2").show().empty();
        if (response.success) {
          $("#idcaja").val(response.idcaja);
          setNavbarPosVisible(true);
          $("#navbar-pos2")
            .html(`<li class="nav-item" style="margin-right: 10px;">
            <a class="nav-link"  onclick="cerrarcaja()" title="Cerrar caja" style="background-color: #FA7A31; border-radius: 5px; color: white; font-weight:bold;" href="#" role="button">
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
          </li>`);
          resolve(true);
        } else {
          $("#navbar-pos2").html(`<li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>`);
          resolve(false);
        }
      },
      error: function (error) {
        reject(error);
      },
    });
  });
}

function verReportes() {
  $("#myModal2").modal("show");
  listarVentas();
}

function listarVentas() {
  var estado = $("#estadoVentas").val();
  var idcaja = $("#idcaja").val();
  var idsucursal = $("#idsucursalVentas").val();

  tabla = $("#tbllistadoVentas")
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

// =======================================
// Verificar si hay caja abierta por sucursal
// =======================================
$("#estadoVentas").change(listarVentas);
function verificarCajaPorSucursal(idsucursal) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "controladores/venta.php?op=verificar_caja_por_sucursal",
      type: "GET",
      data: { idsucursal },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $("#idcaja").val(response.idcaja);
          resolve(true);
        } else {
          resolve(false);
        }
      },
      error: function (error) {
        console.error("Error al verificar caja:", error);
        reject(error);
      },
    });
  });
}

function listarCajas() {
  // Obtenemos la sucursal seleccionada del combo principal
  const idsucursal = $("#idsucursal2").val();

  // Si no se ha seleccionado ninguna sucursal, mostramos mensaje
  if (!idsucursal) {
    $("#cajas").html(
      "<option value=''>Seleccione una sucursal primero</option>",
    );
    return;
  }

  $.ajax({
    url: "controladores/venta.php?op=listar_cajas",
    type: "GET",
    data: { idsucursal: idsucursal }, // 👈 enviamos la sucursal seleccionada
    dataType: "json",
    success: function (response) {
      let html = "";

      if (response.length > 0) {
        $.each(response, function (i, item) {
          html += `<option value="${item.idcaja}">${item.nombre}</option>`;
        });
      } else {
        html =
          "<option value=''>No hay cajas activas en esta sucursal</option>";
      }

      $("#cajas").html(html);
    },
    error: function (xhr, status, error) {
      console.error("Error al listar cajas:", error);
      $("#cajas").html("<option value=''>Error al cargar cajas</option>");
    },
  });
}

// $("#formularioappcaja").submit(function (e) {
//   e.preventDefault();
//   var $form = $(this); // guardamos una referencia al form
//   var data = new FormData(this);

//   $.ajax({
//     url: "controladores/venta.php?op=aperturar_caja",
//     type: "POST",
//     data: data,
//     contentType: false,
//     processData: false,

//     success: function (resp) {
//       var json = JSON.parse(resp);
//       if (json.success) {
//         $form[0].reset();
//         listarCajas();
//         mostrarform(true);
//         setNavbarPosVisible(true);
//       } else {
//         Swal.fire("Error", "No se pudo aperturar la caja.", "error");
//       }
//     },
//     error: function () {
//       Swal.fire("Error", "Fallo en la petición de apertura.", "error");
//     },
//   });
// });

function cerrarcaja() {
  var idcaja = $("#idcaja").val();
  var idsucursal2 = $("#idsucursal2").val(); // Tomamos la sucursal seleccionada

  $.ajax({
    url:
      "controladores/pos.php?op=showResumenCaja&idcaja=" +
      idcaja +
      "&idsucursal=" +
      idsucursal2,
    type: "POST",
    contentType: false,
    processData: false,
    success: function (data) {
      let resumen;

      try {
        resumen = JSON.parse(data);
      } catch (err) {
        console.error("Error al parsear JSON:", err, data);
        Swal.fire(
          "Error",
          "No se pudo obtener el resumen de caja correctamente.",
          "error",
        );
        return;
      }

      if (resumen.error) {
        Swal.fire("Atención", resumen.error, "warning");
        return;
      }

      // Construye el HTML del resumen
      let ventasHtml = `
                <b>VENTAS EFECTIVO:</b>
                S/. <span>${parseFloat(resumen.ventas_efectivo).toFixed(2)}</span>
                <span class="badge bg-primary">${resumen.cantidad_ventas_efectivo}</span><br>
                <b>VENTAS NO EFECTIVO:</b>
                S/. <span>${parseFloat(resumen.ventas_no_efectivo).toFixed(2)}</span>
                <span class="badge bg-primary">${resumen.cantidad_ventas_no_efectivo}</span><br>
                <b>VENTAS CRÉDITO (NO SUMAN):</b>
                S/. <span>${parseFloat(resumen.ventas_credito).toFixed(2)}</span>
                <span class="badge bg-primary">${resumen.cantidad_ventas_credito}</span><br>
            `;

      let movimientosHtml = `
                <table style="width:100%;font-size:13px;">
                    <tr><th></th><th>Efectivo</th><th>No efectivo</th></tr>
                    <tr>
                        <td><b>Ingresos</b></td>
                        <td>S/. ${parseFloat(resumen.ingresos_efectivo).toFixed(2)}</td>
                        <td>S/. ${parseFloat(resumen.ingresos_no_efectivo).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td><b>Amortizaciones</b></td>
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

      Swal.fire({
        title: "Cierre de caja",
        html: `
          <div style="font-family: Arial, sans-serif; text-align:left">

              <h3 style="margin-bottom:10px;">🧾 Resumen de Caja</h3>

              <div style="padding:10px; border:1px solid #ddd; border-radius:8px; margin-bottom:10px;">
                  <b>Efectivo de Apertura:</b><br>
                  <span style="font-size:18px; color:#2c7be5; font-weight:bold">
                      S/. ${parseFloat(resumen.efectivo_apertura).toFixed(2)}
                  </span>
              </div>

              <div style="padding:10px; border:1px solid #ddd; border-radius:8px; margin-bottom:10px;">
                  <b>💰 Ventas:</b><br>
                  ${ventasHtml}
              </div>

              <div style="padding:10px; border:1px solid #ddd; border-radius:8px; margin-bottom:10px;">
                  <b>📊 Ingresos y Egresos:</b><br>
                  ${movimientosHtml}
              </div>

              <div style="padding:15px; border:2px solid #dc3545; border-radius:10px; background:#fff5f5; text-align:center;">
                  <b style="display:block; margin-bottom:5px;">Efectivo Final Esperado</b>
                  <span style="color:#dc3545; font-size:24px; font-weight:bold">
                      S/. ${parseFloat(resumen.total_efectivo).toFixed(2)}
                  </span>
              </div>

              <hr style="margin:15px 0;">

              <div style="text-align:center; font-size:14px; color:#555;">
                  ⚠️ Verifique que el monto del sistema coincida con su caja física
              </div>

          </div>
          `,
        input: "number",
        inputAttributes: {
          autocapitalize: "off",
          required: true,
          step: "0.01",
        },
        inputValue: parseFloat(resumen.total_efectivo).toFixed(2),
        showCancelButton: true,
        confirmButtonText: "Cerrar caja",
        showLoaderOnConfirm: true,
        preConfirm: async (efectivo_cierre) => {
          try {
            const resp = await fetch(
              `controladores/pos.php?op=cerrarCaja&efectivo_cierre=${efectivo_cierre}&idcaja=${idcaja}&idsucursal=${idsucursal2}`,
            );
            if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
            return resp.json();
          } catch (err) {
            Swal.showValidationMessage(`Error al cerrar caja: ${err}`);
          }
        },
        allowOutsideClick: () => !Swal.isLoading(),
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: "¡Caja cerrada!",
            icon: "success",
            text: "Vuelva a abrir una caja cuando lo necesite.",
            showConfirmButton: false,
            timer: 1500,
          }).then(() => location.reload());
        }
      });
    },
    error: function (err) {
      console.error("Error al cargar resumen de caja:", err);
      Swal.fire("Error", "No se pudo obtener el resumen de caja.", "error");
    },
  });
}

$("#formularioappcaja").submit(function (e) {
  e.preventDefault();
  var formData = new FormData(this);
  $.ajax({
    url: "controladores/venta.php?op=aperturar_caja",
    type: "post",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      var response = JSON.parse(response);
      if (response.success) {
        $("#idcaja").val(response.idcaja);
        $("#listadoregistros").hide();
        $("#aperturarcaja").hide();
        $("#formularioregistros").show();
        $("#btnagregar").hide();
        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        detalles = 0;
        $("#btnAgregarArt").show();
        $("#btnNuevo").hide();
        $("#header").hide();
        $("body").addClass("sidebar-collapse");
        listarArticulosSearchFIFO();
        listarArticulos2();
        verificarCaja();
      } else {
        alert("No se pudo aperturar");
      }
    },
  });
});

function mostrar_impuesto() {
  $.ajax({
    url: "controladores/negocio.php?op=mostrar_impuesto",
    type: "get",
    dataType: "json",
    success: function (i) {
      impuesto = parseFloat(i) || 0;
      no_aplica = impuesto;
      calcularTotales();
    },
  });
}

$("#tipo_comprobante").change(function () {
  var idsucursalSeleccionada = $("#idsucursal2").val();
  marcarImpuesto(idsucursalSeleccionada);
});

// Función unificada para número y serie
function cargarNumeroSerie(tipoComprobante, idsucursal) {
  let opNum = "",
    opSerie = "",
    prefijo = "";

  switch (tipoComprobante) {
    case "Factura":
      opNum = "mostrarf";
      opSerie = "mostrars";
      prefijo = "F";
      break;
    case "Boleta":
      opNum = "mostrar_num_boleta";
      opSerie = "mostrar_serie_boleta";
      prefijo = "B";
      break;
    case "Ticket":
      opNum = "mostrar_num_ticket";
      opSerie = "mostrar_s_ticket";
      prefijo = "P";
      break;
    default:
      console.warn("Tipo de comprobante no reconocido:", tipoComprobante);
      return;
  }

  // Obtener número de comprobante
  $.ajax({
    url: "controladores/venta.php?op=" + opNum,
    type: "get",
    data: { idsucursal: idsucursal },
    dataType: "json",
    success: function (num) {
      num = parseInt(num) || 1; // fallback seguro
      $("#num_comprobante").val(("0000000" + num).slice(-7));
      $("#nFacturas").html(("0000000" + num).slice(-7));

      // Para Ticket habilitar porcentaje, para otros deshabilitar
      $("#porcentaje").attr("disabled", tipoComprobante !== "Ticket");
    },
  });

  // Obtener serie de comprobante
  $.ajax({
    url: "controladores/venta.php?op=" + opSerie,
    type: "get",
    data: { idsucursal: idsucursal },
    dataType: "json",
    success: function (serie) {
      $("#numeros").html(("000" + serie).slice(-3));
      $("#serie_comprobante").val(prefijo + ("000" + serie).slice(-3));
    },
  });
}

// Nueva función marcarImpuesto usando la unificada
function marcarImpuesto(idsucursalSeleccionada) {
  var tipo_comprobante = $("#tipo_comprobante option:selected").text().trim();
  if (!idsucursalSeleccionada) {
    idsucursalSeleccionada = $("#idsucursal").val(); // fallback
  }

  if (
    tipo_comprobante === "Factura" ||
    tipo_comprobante === "Boleta" ||
    tipo_comprobante === "Ticket"
  ) {
    mostrar_impuesto();
    cargarNumeroSerie(tipo_comprobante, idsucursalSeleccionada);
  } else {
    // Evita resetear el IGV por coincidencias de texto del comprobante.
    // Si el tipo no coincide exactamente, seguimos usando la tasa del negocio.
    mostrar_impuesto();
    cargarNumeroSerie("Ticket", idsucursalSeleccionada);
  }
}

// Evento change para actualizar cuando se cambie tipo de comprobante
$("#tipo_comprobante").change(function () {
  const idsucursalSeleccionada = $("#idsucursal").val();
  marcarImpuesto(idsucursalSeleccionada);
});

function handlePrecioChange(input, idpc) {
  const checkbox = document.getElementById("chkPrecioSegunCantidad-" + idpc);
  const activarAuto = checkbox?.checked;

  const valorInput = input.value.trim();
  if (valorInput === "") return;

  const precioNuevo = parseFloat(valorInput);
  if (isNaN(precioNuevo) || precioNuevo <= 0) {
    modificarSubtotales();
    return;
  }

  // Guardar el precio original al primer cambio
  if (!input.hasAttribute("data-precio-original")) {
    input.setAttribute(
      "data-precio-original",
      input.getAttribute("data-previo") || precioNuevo,
    );
  }

  // Buscar la fila del producto
  const filas = document.querySelectorAll("#detalles tbody tr");
  filas.forEach((fila) => {
    const idProducto = fila.querySelector('input[name="idp[]"]').value;
    if (idProducto == idpc) {
      const cantidadInput = fila.querySelector('input[name="cantidad[]"]');
      const subtotalSpan = fila.querySelector('span[name="subtotal"]');

      // Si el check NO está activo → comportamiento normal
      if (!activarAuto) {
        modificarSubtotales();
        return;
      }

      // --- Guardamos precio base la primera vez que se activa ---
      let precioBase = parseFloat(input.getAttribute("data-precio-base"));
      if (isNaN(precioBase) || precioBase <= 0) {
        precioBase =
          parseFloat(input.getAttribute("data-previo")) || precioNuevo;
        input.setAttribute("data-precio-base", precioBase);
      }

      // --- Guardamos cantidad base la primera vez ---
      let cantidadBase =
        parseFloat(cantidadInput.getAttribute("data-cantidad-base")) ||
        parseFloat(cantidadInput.value) ||
        1;
      cantidadInput.setAttribute("data-cantidad-base", cantidadBase);

      // --- Calcular nueva cantidad ---
      const nuevaCantidad = (precioNuevo / precioBase) * cantidadBase;
      cantidadInput.value = nuevaCantidad.toFixed(3);

      // Subtotal = precio ingresado
      subtotalSpan.innerText = precioNuevo.toFixed(2);
      subtotalSpan.value = precioNuevo.toFixed(2);
    }
  });

  // Guardar nuevo precio como referencia
  input.setAttribute("data-previo", precioNuevo);
  calcularTotales();
}

function toggleCheckPrecio(idpc, checkbox) {
  const hidden = document.getElementById("check_precio_" + idpc);
  const input = document.getElementById("precio-" + idpc);
  const fila = [...document.querySelectorAll("#detalles tbody tr")].find(
    (f) => f.querySelector('input[name="idp[]"]').value == idpc,
  );

  const cantidadInput = fila?.querySelector('input[name="cantidad[]"]');
  const subtotalSpan = fila?.querySelector('span[name="subtotal"]');

  if (hidden) {
    hidden.value = checkbox.checked ? 1 : 0;
  }

  // Si se activa el check
  if (checkbox.checked) {
    Swal.fire({
      title: "Modo Balanza Activado",
      text: "El modo balanza está activo para este producto.",
      icon: "info",
      timer: 1500,
      showConfirmButton: false,
      position: "top-end",
      toast: true,
      background: "#e0f7fa",
      color: "#00796b",
    });

    // Guardar cantidad original la primera vez
    if (!cantidadInput.hasAttribute("data-cantidad-original")) {
      cantidadInput.setAttribute("data-cantidad-original", cantidadInput.value);
    }

    // Guardar precio original si no existe
    if (!input.hasAttribute("data-precio-original")) {
      input.setAttribute("data-precio-original", input.value);
    }

    // Restaurar el precio base a su valor original
    const precioOriginal = parseFloat(
      input.getAttribute("data-precio-original"),
    );
    if (!isNaN(precioOriginal)) {
      input.value = precioOriginal.toFixed(2);
      input.setAttribute("data-previo", precioOriginal.toFixed(2));
      input.setAttribute("data-precio-base", precioOriginal.toFixed(2));
    }

    // Restaurar la cantidad original también
    const cantidadOriginal = parseFloat(
      cantidadInput.getAttribute("data-cantidad-original"),
    );
    if (!isNaN(cantidadOriginal)) {
      cantidadInput.value = cantidadOriginal.toFixed(3);
    }
  } else {
    // Si se desactiva, restauramos también cantidad y precio originales
    Swal.fire({
      title: "Modo Balanza Desactivado",
      icon: "warning",
      timer: 1500,
      showConfirmButton: false,
      position: "top-end",
      toast: true,
      background: "#fff3e0",
      color: "#e65100",
    });

    const precioOriginal = parseFloat(
      input.getAttribute("data-precio-original"),
    );
    const cantidadOriginal = parseFloat(
      cantidadInput.getAttribute("data-cantidad-original"),
    );

    if (!isNaN(precioOriginal)) {
      input.value = precioOriginal.toFixed(2);
      input.setAttribute("data-previo", precioOriginal.toFixed(2));
      input.setAttribute("data-precio-base", precioOriginal.toFixed(2));
    }

    if (!isNaN(cantidadOriginal)) {
      cantidadInput.value = cantidadOriginal.toFixed(3);
    }
  }

  modificarSubtotales();
}

function agregarDetalle(
  idpc,
  idproducto,
  producto,
  cant,
  desc,
  precio_venta,
  preciocigv,
  precioB,
  precioC,
  precioD,
  stock,
  proigv,
  cantidad_contenedor,
  contenedor,
  idcategoria,
  unidadmedida,
  id_detalle_compra_lote,
  fabricante = "",
  modelo = "",
  color = "",
  controla_stock = "",
) {
  if (precio_venta == 0) {
    Swal.fire({
      title: "Alerta",
      text: "El precio de venta no puede ser 0. Por favor, modifica el precio.",
      icon: "warning",
      showCancelButton: false, // No mostrar botón de cancelar
      confirmButtonText: "Entendido", // Texto personalizado en el botón
      confirmButtonColor: "#3085d6", // Color del botón
      background: "#f8f9fa", // Fondo claro para hacerla más elegante
      position: "center", // Centrado en la pantalla
      customClass: {
        popup: "swal-custom-popup", // Estilo personalizado para la ventana emergente
        title: "swal-title", // Estilo personalizado para el título
        content: "swal-content", // Estilo personalizado para el contenido
      },
      willClose: () => {
        // Añadir animación de desvanecimiento cuando se cierre
        document.querySelector(".swal2-popup").classList.add("fade-out");
      },
    });
  }

  if (idcategoria != 1 && controla_stock == "Si") {
    // no aplica a servicios
    if (cantidad_contenedor > 1) {
      // Caso caja u otro contenedor
      let stockDisponible = stock / cantidad_contenedor;
      if (stockDisponible < 1) {
        Swal.fire(
          "Stock insuficiente",
          "No hay stock suficiente para el contenedor: " + contenedor,
          "error",
        );
        return false;
      }
    } else {
      // Caso unidad
      if (stock <= 0) {
        Swal.fire(
          "Stock insuficiente",
          "No hay stock suficiente para el contenedor: " + contenedor,
          "error",
        );
        return false;
      }

      // Caso especial: stock entre 0 y 1 → permitir fraccionado
      if (stock > 0 && stock < 1) {
        cant = stock; // solo lo que hay
        // Ajustar precio proporcionalmente
        precio_venta = (precio_venta * stock).toFixed(2);

        Swal.fire(
          "Aviso",
          "El stock es menor a 1, se agregará solo la cantidad disponible (" +
          stock +
          "), ajustando el precio proporcionalmente.",
          "info",
        );
      }
    }
  }

  if ($("#tipo_comprobante").val() != "Nota de Venta") {
    precio_venta = precio_venta;

    if (precioB != "") {
      precioB = (precioB * 1.18).toFixed(2);
    }
    if (precioC != "") {
      precioC = (precioC * 1.18).toFixed(2);
    }
    if (precioD != "") {
      precioD = (precioD * 1.18).toFixed(2);
    }
  } else {
    precio_venta = precio_venta;
  }

  //aquí preguntamos si el idarticulo ya fue agregado
  // SOLO evitar duplicados si NO es servicio
  if (
    idcategoria != 1 &&
    articuloAdd.split("-").indexOf(idproducto.toString()) !== -1
  ) {
    //reporta -1 cuando no existe
    // swal( producto +" ya se agrego");

    let cant = document.getElementsByName("cantidad[]");

    let id = document.getElementsByName("idproducto[]");

    for (var i = 0; i < cant.length; i++) {
      if (id[i].value == idproducto) {
        let total = Number(cant[i].value) + 1;
        let stockverify = Number(cant[i].value) + Number(cantidad_contenedor);
        if (idcategoria != 1) {
          if (stock < stockverify && controla_stock == "Si") {
            Swal.fire("Alerta", "No hay suficiente stock!", "error");
            return false;
          }
        }
        document.getElementsByName("cantidad[]")[i].value = total;

        modificarSubtotales();
      }
    }
  } else {
    var cantidad = cant;
    var stockverify = cant * cantidad_contenedor;

    if (idcategoria != 1 && controla_stock == "Si" && stock < stockverify) {
      Swal.fire("Alerta", "No hay suficiente stock!", "error");
      return false;
    }

    /*if (idcategoria == 1) {
      stock = "Servicio";
    } else {
      stock = stock;
    }*/
    var detail = "";
    if (cantidad_contenedor != undefined && unidadmedida != undefined) {
      detail =
        unidadmedida +
        ' <span style="color:#d9534f;font-weight:bold;padding:0 3px;">x</span> ' +
        cantidad_contenedor;
    }

    var descuento = desc;

    var cad = "";
    var select = "";
    // Campo de precio con evento y checkbox por producto
    var precioInput =
      "<input " +
      'class="form-control form-control-sm" style="width:80px" ' +
      'type="number" step="0.01" ' +
      "oninput=\"handlePrecioChange(this, '" +
      idpc +
      "')\" " +
      'name="precio_venta[]" ' +
      'id="precio-' +
      idpc +
      '" ' +
      'value="' +
      precio_venta +
      '" ' +
      'data-previo="' +
      precio_venta +
      '">';

    var btnVerPrecios =
      "<i " +
      'class="fas fa-eye" ' +
      'onclick="verPreciosItem(' +
      idpc +
      ')" ' +
      'style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); cursor:pointer; font-size: 0.8em; color: #6c757d;"' +
      'title="Mostrar precios">' +
      "</i>";

    if (precioB != "0.00" || precioC != "0.00" || precioD != "0.00") {
      cad =
        '<option value="' + precio_venta + '">' + precio_venta + "</option>";

      if (precioB != "0.00") {
        cad = cad + '<option value="' + precioB + '">' + precioB + "</option>";
      }

      if (precioC != "0.00") {
        cad = cad + '<option value="' + precioC + '">' + precioC + "</option>";
      }

      if (precioD != "0.00") {
        cad = cad + '<option value="' + precioD + '">' + precioD + "</option>";
      }

      select =
        //'<select style="width:100px;height:35px;" oninput="modificarSubtotales()" name="precio_venta[]" id="precio_venta[]" class="form-control" required>' +
        ///cad +
        //"</select>";
        '<input class="form-control" style="text-align:center; width: 80px;" type="number" step="0.01" oninput="modificarSubtotales()" name="precio_venta[]" id="precio_venta[]" value="' +
        precio_venta +
        '">';
    } else {
      select =
        '<input class="form-control" style="text-align:center; width: 80px;" type="number" step="0.01" oninput="modificarSubtotales()" name="precio_venta[]" id="precio_venta[]" value="' +
        precio_venta +
        '">';
    }

    if (idpc !== "") {
      contador = contador + 1;
      var fila =
        '<tr class="filas custom-row" id="fila' +
        cont +
        '" style="margin-bottom:-10px;border-radius:10px;box-shadow:0 0 5px rgba(0,0,0,0.3);">' +
        '<td style="text-align:center;vertical-align:middle;">' +
        '<input type="hidden" name="contenedor[]" value="' +
        contenedor +
        '">' +
        '<input type="hidden" name="cantidad_contenedor[]" value="' +
        cantidad_contenedor +
        '">' +
        '<input type="hidden" name="idp[]" value="' +
        idpc +
        '">' +
        '<input type="hidden" name="check_precio[]" id="check_precio_' +
        idpc +
        '" value="0">' +
        '<input type="hidden" name="idproducto[]" value="' +
        idproducto +
        '">' +
        '<input type="hidden" name="idcategoria[]" value="' +
        idcategoria +
        '">' +
        '<input type="hidden" name="id_detalle_compra_lote[]" value="' +
        id_detalle_compra_lote +
        '">' +
        '<div style="display:flex;align-items:center;justify-content:center;gap:5px;">' +
        (modoEditar
          ? ""
          : '<i class="fa fa-trash" style="color:red;cursor:pointer;" onclick="eliminarDetalle(' +
          cont +
          ')"></i>') +
        '<textarea class="form-control nombre-producto" ' +
        'name="nombreProducto[]" rows="1" ' +
        'oninput="autoResize(this)" onfocus="this.select()" ' +
        'style="font-weight:bold;width:300px;resize:none;overflow:hidden;white-space:pre-wrap;word-break:break-word;overflow-wrap:break-word;line-height:1.2;">' +
        producto +
        (fabricante || modelo || color
          ? " " +
          (fabricante ? fabricante + " " : "") +
          (modelo ? modelo + " " : "") +
          (color ? color : "")
          : "") +
        "</textarea>" +
        "</div>" +
        "</td>" +
        '<td style="text-align:center;vertical-align:middle;">' +
        '<span class="badge bg-green" style="white-space:nowrap;font-size:11px;">' +
        detail +
        "</span>" +
        "</td>" +
        '<td class="text-center align-middle">' +
        '<div style="position:relative;display:inline-block;">' +
        precioInput +
        btnVerPrecios +
        "</div>" +
        "</td>" +
        '<td style="text-align:center; vertical-align:middle;">' +
        '<div style="display:flex; justify-content:center; align-items:center; width:100%;">' +
        '<input type="checkbox" ' +
        'id="chkPrecioSegunCantidad-' +
        idpc +
        '" ' +
        'onchange="toggleCheckPrecio(' +
        idpc +
        ', this)">' +
        "</div>" +
        "</td>" +
        '<td style="text-align:center;vertical-align:middle;">' +
        '<input class="form-control" ' +
        'style="text-align:center;width:80px;background-color:transparent;color:blue;font-weight:bold;" ' +
        'type="number" step="0.001" min="0" ' +
        'oninput="validarCantidad(this,' +
        stock +
        "," +
        cantidad_contenedor +
        ", '" +
        controla_stock +
        "');modificarSubtotales()\" " +
        'name="cantidad[]" value="' +
        cantidad +
        '">' +
        "</td>" +
        '<td style="text-align:center;vertical-align:middle;">' +
        '<input class="form-control" ' +
        'style="text-align:center;width:70px;background-color:#fff3cd;font-weight:bold;" ' +
        'type="number" step="0.01" ' +
        'oninput="modificarSubtotales(' +
        cont +
        ')" ' +
        'name="descuento[]" value="' +
        descuento +
        '">' +
        "</td>" +
        '<td style="display:none;text-align:center;vertical-align:middle;">' +
        '<input type="hidden" name="stock[]" value="' +
        stock +
        '">' +
        '<span class="btn btn-warning" style="font-size:12px;font-weight:bold;">' +
        stock +
        "</span>" +
        "</td>" +
        '<td style="text-align:center;vertical-align:middle;width:100px;">' +
        'S/. <span id="subtotal' +
        cont +
        '" name="subtotal" style="font-size:14px;font-weight:bold;"></span>' +
        "</td>" +
        '<td style="display:none;">' +
        '<span id="proigv' +
        cont +
        '" name="proigv">' +
        proigv +
        "</span>" +
        "</td>" +
        "</tr>";
      cont++;
      detalles = detalles + 1;
      articuloAdd = articuloAdd + idproducto + "-";
      $("#detalles tbody").append(fila);
      actualizarFilaVaciaDetalles();
      modificarSubtotales();
      evaluar();
    } else {
      alert("Error al ingresar el detalle, revisar los datos del artículo");
    }
  }
}

function autoResize(textarea) {
  textarea.style.height = "auto";
  textarea.style.height = textarea.scrollHeight + "px";
}

$(document).on("keydown", ".nombre-producto", function (e) {
  if (e.key === "Tab") {
    e.preventDefault(); // evita salir del campo

    let start = this.selectionStart;
    let end = this.selectionEnd;

    // Insertar salto de línea en la posición del cursor
    this.value =
      this.value.substring(0, start) + "\n" + this.value.substring(end);

    // Mover cursor a la siguiente línea
    this.selectionStart = this.selectionEnd = start + 1;

    // Ajustar altura automáticamente
    autoResize(this);
  }
});

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

function validarCantidad(input, stock, cantidad_contenedor, controla_stock) {
  const max = Math.floor(stock / cantidad_contenedor);
  let val = parseInt(input.value, 10);

  if (isNaN(val) || val < 0) {
    input.value = 1;
    return;
  }

  if (val > max && controla_stock === "Si") {
    Swal.fire("Alerta", "No hay suficiente stock disponible", "warning");
    input.value = max;
  }
}

// 1) Función para actualizar en BD
function actualizarDataItem(idproducto, campo, value) {
  var token = $("#token").val(); // si lo necesitas
  $.post(
    "controladores/pos.php?op=actualizarDataItem",
    {
      idproducto: idproducto,
      campo: campo,
      value: value,
    },
    function (res) {
      if (res.status == 1) {
        console.log("Precio actualizado.");
      } else {
        toastr.error("No se pudo guardar el cambio.");
      }
    },
    "json",
  );
}

// 2) Función que se ejecuta al hacer clic en “Seleccionar”
function actualizarPrecio(precio, idproducto) {
  $(`#precio-${idproducto}`).val(precio);
  // llama a la función recién creada, con orden correcto
  actualizarDataItem(idproducto, "precio", precio);
  modificarSubtotales(idproducto);
  $("#ModalPrecios").modal("hide");
}

// customStyles.js

document.addEventListener("DOMContentLoaded", function () {
  var style = document.createElement("style");
  style.innerHTML = `
        .custom-row {
            border: 1px solid #ddd;
        }

        .custom-cell {
            font-size: 14px;
        }

        .custom-input {
            width: 50px;
        }

        .custom-stock {
            font-size: 12px;
        }

        .custom-btn {
            /* Agrega estilos adicionales para el botón si es necesario */
        }
    `;

  document.head.appendChild(style);
});

function nostock() {
  Swal.fire("Alerta", "Sin Stock", "info");
}
function modificarSubtotales(e) {
  const cant = document.getElementsByName("cantidad[]");
  const prec = document.getElementsByName("precio_venta[]");
  const desc = document.getElementsByName("descuento[]");
  const sub = document.getElementsByName("subtotal");
  const Stoc = document.getElementsByName("stock[]");

  for (let i = 0; i < cant.length; i++) {
    const inpC = cant[i];
    const inpP = prec[i];
    const inpD = desc[i];
    const inpS = sub[i];
    const inpSt = Stoc[i];

    const fila = inpC.closest("tr");
    const idp = fila.querySelector('input[name="idp[]"]').value;
    const chk = document.getElementById("chkPrecioSegunCantidad-" + idp);

    let subtotal = 0;
    const cantidad = parseFloat(inpC.value) || 0;
    const precio = parseFloat(inpP.value) || 0;
    const descuento = parseFloat(inpD.value) || 0;

    if (chk && chk.checked) {
      // Modo precio directo
      subtotal = precio;
    } else {
      // Modo normal con descuento RESTANDO AL SUBTOTAL FINAL
      subtotal = cantidad * precio - descuento;

      // Evitar negativos
      if (subtotal < 0) subtotal = 0;
    }

    subtotal = subtotal.toFixed(2);

    // Guarda el valor y actualiza el texto
    inpS.value = subtotal;
    inpS.textContent = subtotal;
    inpS.innerText = subtotal;

    // Validación de stock solo en venta
    if ($("#tipo").val() === "venta") {
      if (Number(inpC.value) > Number(inpSt.value)) {
        inpC.style.backgroundColor = "#00CC00";
        inpSt.style.backgroundColor = "#CC0000";
        $("#btnGuardar").hide();
        if (e) e.preventDefault();
      } else {
        inpC.style.backgroundColor = "#FFFFFF";
        inpSt.style.backgroundColor = "#FFFFFF";
      }
    }
  }

  calcularTotales();
  evaluar();
  actualizarMontoPrimerPago();
  calcularTotalRecibido();
  calcularTotalDeposito();
}

function calcularTotales() {
  const sub = document.querySelectorAll('#detalles span[name="subtotal"]');
  const proigvNodes = document.querySelectorAll(
    '#detalles span[name="proigv"]',
  );
  let total = 0.0;
  let totalConIgv = 0.0;
  let igv = 0.0;

  for (let i = 0; i < sub.length; i++) {
    const val = parseFloat(sub[i].innerText || sub[i].textContent || 0);
    if (isNaN(val)) continue;

    total += val;

    const proigvNode = proigvNodes[i];
    const proigv = (proigvNode?.textContent || "").trim();

    if (
      proigv.toLowerCase() === "gravada" ||
      proigv.toLowerCase() === "gravado"
    ) {
      totalConIgv += val;
    }
  }

  if (totalConIgv > 0 && parseFloat(no_aplica) > 0) {
    igv = (totalConIgv * parseFloat(no_aplica)) / (parseFloat(no_aplica) + 100);
  }

  const subtotal = total - igv;
  actualizarResumenVenta(total, esnulo(subtotal), igv);
  evaluar();
}

function esnulo(v) {
  if (isNaN(v)) {
    return 0;
  } else {
    return v;
  }
}

function evaluar() {
  // Contar las filas de detalle que tienen la clase "filas" dentro del contenedor #detalles
  var totalFilas = $("#detalles tr.filas").length;
  actualizarFilaVaciaDetalles();
  if (totalFilas > 0) {
    $("#btnGuardar").show();
  } else {
    $("#btnGuardar").hide();
    cont = 0;
    actualizarResumenVenta(0, 0, 0);
  }
}

function generarTabla(cuotas, frecuencia, fechaBase, deuda, interes) {
  let html = "";
  let fechaTemp = new Date(fechaBase);

  const interesTotal = deuda * (interes / 100);
  const montoBase = deuda / cuotas;
  const interesPorCuota = interesTotal / cuotas;
  const totalCuota = montoBase + interesPorCuota;

  for (let i = 1; i <= cuotas; i++) {
    fechaTemp = sumarFrecuencia(fechaTemp, frecuencia);
    let fecha = formatearFecha(fechaTemp);

    html += `
      <tr>
        <td><input type="date" class="form-control" name="fecha_pago[]" value="${fecha}"></td>
        <td>S/. ${montoBase.toFixed(2)}</td>
        <td>S/. ${interesPorCuota.toFixed(2)}</td>
        <td>S/. ${totalCuota.toFixed(2)}</td>
      </tr>`;
  }

  return html;
}

function formatearFecha(fecha) {
  return (
    fecha.getFullYear() +
    "-" +
    ("0" + (fecha.getMonth() + 1)).slice(-2) +
    "-" +
    ("0" + fecha.getDate()).slice(-2)
  );
}

function sumarFrecuencia(fecha, frecuencia) {
  let nuevaFecha = new Date(fecha);

  switch (frecuencia) {
    case 1:
      nuevaFecha.setDate(nuevaFecha.getDate() + 1);
      break;
    case 2:
      nuevaFecha.setDate(nuevaFecha.getDate() + 7);
      break;
    case 3:
      nuevaFecha.setDate(nuevaFecha.getDate() + 15);
      break;
    case 4:
      nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);
      break;
    case 5:
      nuevaFecha.setMonth(nuevaFecha.getMonth() + 2);
      break;
    case 6:
      nuevaFecha.setMonth(nuevaFecha.getMonth() + 3);
      break;
    case 7:
      nuevaFecha.setMonth(nuevaFecha.getMonth() + 6);
      break;
    case 8:
      nuevaFecha.setFullYear(nuevaFecha.getFullYear() + 1);
      break;
  }

  return nuevaFecha;
}

function calcularMontos(deuda, interes, cuotas) {
  let interesTotal = deuda * (interes / 100);
  let deudaTotal = deuda + interesTotal;
  let montoCuota = (deudaTotal / cuotas).toFixed(2);

  return {
    interesTotal,
    deudaTotal,
    montoCuota,
  };
}

function validarDatos({ cuotas, frecuencia, deuda }) {
  if (!frecuencia) {
    Swal.fire("Selecciona frecuencia de pago", "", "warning");
    return false;
  }

  if (!cuotas) {
    Swal.fire("Selecciona número de cuotas", "", "warning");
    return false;
  }

  if (!deuda || deuda <= 0) {
    Swal.fire("El crédito no puede ser menor o igual a 0", "", "warning");
    return false;
  }

  return true;
}

function obtenerFechaHoyISO() {
  const hoy = new Date();
  const yyyy = hoy.getFullYear();
  const mm = ("0" + (hoy.getMonth() + 1)).slice(-2);
  const dd = ("0" + hoy.getDate()).slice(-2);
  return yyyy + "-" + mm + "-" + dd;
}


$("#calcular_cuotas").click(function (e) {
  e.preventDefault();
  calcularCuotas();
});


function calcularCuotas() {

  // Si no se eligio cuotas manualmente, intentar autocalcular desde N° meses + frecuencia.
  if (!$("#input_cuotas").val()) {
    calcularCuotasDesdeNumeroMeses();
  }

  if ($('#tipopago').val() === 'Si') {
    calcularCuotasDesdeNumeroMeses();
  }

  let fechaOperacion = ("" + $("#fechaOperacion").val() || "").trim();
  if (!fechaOperacion) {
    fechaOperacion = obtenerFechaHoyISO();
    $("#fechaOperacion").val(fechaOperacion);
  }

  let data = {
    cuotas: parseInt($("#input_cuotas").val()),
    frecuencia: parseInt($("#input_frecuencia").val()),
    interes: parseFloat($("#inputInteres").val()),
    deuda: parseFloat($("#montoDeuda").val()),
    fechaBase: new Date(fechaOperacion),
  };

  if (!validarDatos(data)) return;

  let { montoCuota } = calcularMontos(data.deuda, data.interes, data.cuotas);

  let html = generarTabla(
    data.cuotas,
    data.frecuencia,
    data.fechaBase,
    data.deuda,
    data.interes,
  );

  $("#datafechas").html(html);
};

function calcularDeuda() {
  $("#totalrecibido").val(0);

  $("#vuelto").val(0);

  montoDeuda = $("#total_venta").val();

  montoPagado = $("#montoPagado").val();

  totalDeuda = montoDeuda - montoPagado;

  $("#montoDeuda").val(totalDeuda);

  if (montoPagado == "0" || montoPagado == "") {
    $("#montoDeuda").val($("#total_venta").val());
  }
}

function calcularPorcentaje() {
  total = $("#most_total2").val();

  porcentaje = $("#porcentaje").val();

  tp1 = total - porcentaje;

  $("#total").html(tp1.toFixed(2));

  $("#total_venta").val(tp1.toFixed(2));

  $("#montoDeuda").val(tp1.toFixed(2));

  if (porcentaje == "0") {
    calcularTotales();
  }
}

function calcularVuelto() {
  let totalVenta = parseFloat($("#total_venta").val()) || 0;
  let totalRecibido = 0;

  // Obtener todos los métodos de pago
  let metodos = [];
  $(".metodoPago").each(function () {
    let v = $(this).val();
    if (v) metodos.push(v);
  });

  // Si hay un solo método
  if (metodos.length === 1) {
    let metodo = metodos[0];
    if (metodo === "Efectivo") {
      totalRecibido = parseFloat($("#totalrecibido").val()) || 0;
    } else if (
      metodo === "Yape" ||
      metodo === "Deposito" ||
      metodo === "Transferencia"
    ) {
      totalRecibido = parseFloat($("#totaldeposito").val()) || 0;
    }
    $("#formapago").val(metodo);
  } else if (metodos.length > 1) {
    // Mixto: sumar todos los montos de pago
    $(".montoPago").each(function () {
      let val = parseFloat($(this).val()) || 0;
      totalRecibido += val;
    });
    $("#formapago").val("Mixto");
  }

  let montoPagado = parseFloat($("#montoPagado").val()) || 0;

  // Calcular vuelto
  let vuelto =
    montoPagado > 0 ? totalRecibido - montoPagado : totalRecibido - totalVenta;
  if (vuelto < 0) vuelto = 0;

  $("#vuelto").val(vuelto.toFixed(2));
  $("#totalrecibido").val(totalRecibido.toFixed(2));
}

function eliminarDetalle(indice) {
  $("#fila" + indice).remove();
  calcularTotales();
  detalles = detalles - 1;
  actualizarFilaVaciaDetalles();
  evaluar();
  articuloAdd = "";
}

function toggleCard() {
  var card = document.getElementById("datosgenerales");
  card.hidden = !card.hidden;

  if (card.hidden) {
    $("#chevron-down").show();
    $("#chevron-up").hide();
  } else {
    $("#chevron-down").hide();
    $("#chevron-up").show();
  }
}

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
////////////////////////////

function generarComprobante(idventa) {
  modoEditar = true;
  mostrarform(true);

  // Mostrar el panel principal
  var card = document.getElementById("datosgenerales");
  card.hidden = false;
  var cardBody = document.getElementById("datosgenerales2");
  cardBody.style.display = "block";

  // Limpiar detalles y variables
  $("#detalles tbody").empty();
  actualizarFilaVaciaDetalles();
  detalles = 0;
  articuloAdd = "";

  //Cargar los detalles de la venta
  $.post(
    "controladores/venta.php?op=listarDetalleVenta",
    { idventa: idventa },
    function (data, status) {
      try {
        data = JSON.parse(data);

        for (var i = 0; i < data.length; i++) {
          let cantidadVendida =
            parseFloat(data[i][3]) * parseFloat(data[i][13]);
          let stockDisponible = parseFloat(data[i][10]) + cantidadVendida;

          agregarDetalle(
            data[i][0], // idpc
            data[i][1], // idproducto
            data[i][2], // producto
            data[i][3], // cantidad
            data[i][4], // descuento
            data[i][5], // precio_venta
            data[i][6],
            data[i][7],
            data[i][8],
            data[i][9],
            stockDisponible, // stock corregido
            data[i][12], // proigv
            data[i][13], // cantidad_contenedor
            data[i][14], // contenedor
          );
        }

        // Recalcular totales después de cargar todos los detalles
        calcularTotales();
        evaluar();
      } catch (e) {
        console.error("Error en listarDetalleVenta:", e);
      }
    },
  );

  // Cargar datos generales de la venta
  $.post(
    "controladores/venta.php?op=mostraredit",
    { idventa: idventa },
    function (data, status) {
      try {
        data = JSON.parse(data);
        $("#tipo_comprobante").val(data.tipo_comprobante).trigger("change");

        // Asegurar que el select de clientes ya tenga opciones
        setTimeout(function () {
          $("#idcliente").val(data.idcliente).trigger("change");
        }, 300);

        $("#tipopago").val(data.ventacredito);
        $("#idventa").val(data.idventa);
        $("#idsucursal").val(data.sucursal).trigger("change");
        $("#fecha").val(data.fecha);

        if (data.ventacredito === "Si") {
          $("#n0, #n1, #n2, #n3, #n4, #n5, #b1, #panel1").show();
          $("#input_cuotas").val(data.meses);
        } else {
          $("#n0, #n1, #n2, #n3, #n4, #n5, #b1, #panel1").hide();
        }

        setTimeout(function () {
          $("#serie_comprobante").val(data.serie_comprobante);
          $("#num_comprobante").val(data.num_comprobante);
          $("#btnGuardar").show();
        }, 1000);

        // Cargar los pagos dinámicos
        $("#pagosMixtosContainer").empty(); // limpiar los pagos anteriores

        if (data.pagos && data.pagos.length > 0) {
          for (var i = 0; i < data.pagos.length; i++) {
            let pago = data.pagos[i];
            let nuevaFila = `
              <div class="row mb-2 pagoItem">
                  <div class="col-md-3">
                      <select class="form-control metodoPago" name="metodo_pago[]">
                          <option value="Efectivo" ${pago.metodo_pago === "Efectivo" ? "selected" : ""}>Efectivo</option>
                          <option value="Transferencia" ${pago.metodo_pago === "Transferencia" ? "selected" : ""}>Transferencia bancaria</option>
                          <option value="Tarjeta" ${pago.metodo_pago === "Tarjeta" ? "selected" : ""}>Tarjeta POS</option>
                          <option value="Deposito" ${pago.metodo_pago === "Deposito" ? "selected" : ""}>Depósito</option>
                          <option value="Yape" ${pago.metodo_pago === "Yape" ? "selected" : ""}>Yape</option>
                          <option value="Plin" ${pago.metodo_pago === "Plin" ? "selected" : ""}>Plin</option>
                      </select>
                  </div>
                  <div class="col-md-2">
                      <input type="text" class="form-control montoPago" name="monto_pago[]" value="${pago.monto}">
                      <input type="hidden" class="montoRealPago" name="monto_real_pago[]" value="${pago.monto}">
                  </div>
                  <div class="col-md-2">
                      <input type="text" class="form-control nroOperacion" name="nroOperacion_pago[]" value="${pago.nroOperacion || ""}" placeholder="N° Operación">
                  </div>
                  <div class="col-md-2 bancoContainer" style="${pago.banco ? "" : "display:none;"}">
                      <input type="text" class="form-control bancoPago" name="banco_pago[]" value="${pago.banco || ""}" placeholder="Banco">
                  </div>
                  <div class="col-md-3 fechaContainer" style="${pago.fechaDeposito ? "" : "display:none;"}">
                      <input type="date" class="form-control fechaDeposito" name="fecha_deposito_pago[]" value="${pago.fechaDeposito || ""}">
                  </div>
                  <div class="col-md-2">
                      <button type="button" class="btn btn-danger btn-sm removePago"><i class="fa fa-trash"></i></button>
                  </div>
              </div>`;
            $("#pagosMixtosContainer").append(nuevaFila);
          }
        } else {
          $("#pagosMixtosContainer").html(
            "<div class='text-muted'>Sin pagos registrados</div>",
          );
        }

        recalcularPagos();
      } catch (error) {
        console.error("Error al procesar datos de venta:", error);
      }
    },
  );

  //Cargar cuotas de la venta
  $.post(
    "controladores/venta.php?op=listarCuotas",
    { idventa: idventa },
    function (data, status) {
      try {
        var cuotas = JSON.parse(data);
        var html = "";
        for (var i = 0; i < cuotas.length; i++) {
          html +=
            "<tr><td>" +
            cuotas[i].fechavencimiento +
            "</td><td>" +
            cuotas[i].deudatotal +
            "</td></tr>";
        }
        $("#datafechas").html(html);
        $("#input_cuotas").val(cuotas.length);
      } catch (e) {
        console.error("Error al cargar cuotas:", e);
      }
    },
  );

  // Ocultar botón guardar hasta que cargue todo
  $("#btnGuardar").hide();
}

function mostrarE() {
  let idcotizacion = $("#comprobanteReferencia").val();

  if (!idcotizacion) {
    console.log("No se ha seleccionado una cotización, no se ejecuta mostrarE");
    return;
  }
  $.post(
    "controladores/cotizaciones.php?op=mostrar",
    { idcotizacion: idcotizacion },
    function (data) {
      const dataCotizacion = JSON.parse(data);
      console.log("Respuesta mostrar:", dataCotizacion);

      if (dataCotizacion && dataCotizacion.idcliente) {
        $("#idcliente").val(dataCotizacion.idcliente).trigger("change");
      } else {
        console.error("No se recibió idcliente:", dataCotizacion);
      }
      $("#tipopago").val(dataCotizacion.formapago);
      if ($("#tipopago").val() == "Si") {
        $("#input_frecuencia").val(dataCotizacion.frecuencia);
        $("#inputInteres").val(dataCotizacion.interes);
        $("#numeroMeses").val(dataCotizacion.meses);
        $("#montoPagado").val(dataCotizacion.inicial);
        const deuda = parseFloat(dataCotizacion.total_venta) - parseFloat(dataCotizacion.inicial);
        $("#montoDeuda").val(deuda.toFixed(2));
        verificarTipoPago();
      }
    },
  );
  $.post(
    "controladores/cotizaciones.php?op=listarDetalleCotizacion",
    { idcotizacion: idcotizacion },
    function (data, status) {
      data = JSON.parse(data);

      for (var y = 0; y < contador; y++) {
        eliminarDetalle(y);
      }

      for (var i = 0; i < data.length; i++) {
        agregarDetalle(
          data[i][0],
          data[i][1],
          data[i][2],
          data[i][3],
          data[i][4],
          data[i][5],
          data[i][6],
          data[i][7],
          data[i][8],
          data[i][9],
          data[i][10],
          data[i][12],
          data[i][13],
          data[i][14],
        );
      }

      setTimeout(() => {
        calcularCuotas();
      }, 300);
    },
  );
}

function anularComprobante(idventa) {
  Swal.fire({
    title: "¿Anular?",
    text: "¿Está seguro Que Desea anular la Venta?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "controladores/venta.php?op=anular",
        { idventa: idventa },
        function (e) {
          Swal.fire("!!! Anulado !!!", e, "success");
          tabla.ajax.reload();
        },
      );
    } else {
      Swal.fire(
        "! Cancelado ¡",
        "Se Cancelo la anulación de la Venta",
        "error",
      );
    }
  });
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

function guardaryeditarmovimiento(e) {
  e.preventDefault(); //No se actiletá la acción predeterminada del evento
  //$("#btnGuardar").prop("disabled",true);
  let formData = new FormData($("#formularioMovimiento")[0]);
  formData.set("idcaja", $("#idcaja").val());
  formData.set("idsucursal", $("#idsucursal02").val());
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

function notaCredito(idventa, idsucursal) {
  Swal.fire({
    title: "¿Está seguro?",
    text: "Se generará una Nota de Crédito para este comprobante",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, continuar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "controladores/venta.php?op=notacredito",
        { comprobanteReferencia: idventa, idsucursal: idsucursal, idmotivo: 1 },
        function (resp) {
          Swal.fire({
            title: "Nota de Crédito",
            text: resp,
            icon: "success",
          });
          listar(); // refrescamos la tabla
        },
      ).fail(function (xhr) {
        Swal.fire({
          title: "Error",
          text: "Ocurrió un problema al generar la Nota de Crédito",
          icon: "error",
        });
      });
    }
  });
}

function mostrar(idventa) {
  $("#getCodeModal22").modal("show");

  $.post(
    "controladores/venta.php?op=mostrar",
    { idventa: idventa },
    function (data, status) {
      data = JSON.parse(data);

      console.log(data);

      // Mostrar datos
      $("#idventam").val(data.idventa);
      $("#cliente").text(data.cliente);
      $("#personalm").text(data.personal);
      $("#tipo_comprobantem").html(
        data.tipo_comprobante == "Boleta"
          ? '<span class="badge badge-primary">' +
          data.tipo_comprobante +
          "</span>"
          : '<span class="badge badge-info">' +
          data.tipo_comprobante +
          "</span>",
      );
      $("#correlativo").text(
        data.serie_comprobante + " - " + data.num_comprobante,
      );
      $("#ventacreditom").html(
        data.ventacredito == "Si"
          ? '<span class="badge badge-success">' + data.ventacredito + "</span>"
          : '<span class="badge badge-danger">' + data.ventacredito + "</span>",
      );
      $("#fecha_hora").text(data.fecha);
      $("#impuestom").text(data.impuesto);
      $("#observaciones").text(data.observacion);
      $("#formapagom").html(
        '<span class="badge badge-info">' + data.formapago + "</span>",
      );

      // Montos
      let montopagado = parseFloat(data.montopagado) || 0;
      $("#abonos").text(montopagado.toFixed(2));
      let deuda = parseFloat(data.total_venta) - montopagado;
      $("#deuda").text(
        data.ventacredito == "Si" ? "S/. " + deuda.toFixed(2) : "---",
      );
      $("#subtotalm").text(parseFloat(data.subtotal || 0).toFixed(2));
      $("#impuestom").text(parseFloat(data.impuesto || 0).toFixed(2));
      $("#totalm").text(parseFloat(data.total_venta || 0).toFixed(2));
    },
  );

  // Cargar detalles en la tabla del modal
  $.post(
    "controladores/venta.php?op=listarDetalle&id=" + idventa,
    function (r) {
      $("#detallesm tbody").html(r);
    },
  );
}

function cancelarform02() {
  // Cerrar el modal (asegúrate que coincida con tu HTML)
  $("#getCodeModal22").modal("hide");

  // Limpiar el contenido del modal
  $("#cliente").text("");
  $("#personalm").text("");
  $("#fecha_hora").text("");
  $("#tipo_comprobantem").text("");
  $("#correlativo").text("");
  $("#formapagom").text("");
  $("#ventacreditom").text("");
  $("#abonos").text("");
  $("#deuda").text("");
  $("#observaciones").text("");
  $("#nrooperacionm").text("");
  $("#banco").text("");
  $("#fechadeposito").text("");
  $("#subtotal").text("");
  $("#impuesto").text("");
  $("#total").text("");

  // Limpiar la tabla de detalles
  $("#detallesm tbody").empty();
}

actualizarFilaVaciaDetalles();
function cambiarComprobante(idventa, idsucursal) {
  Swal.fire({
    title: "Convertir Nota de Venta",
    html: `
            <select id="nuevoComprobante" class="form-control">
                <option value="Boleta">Boleta</option>
                <option value="Factura">Factura</option>
            </select>
        `,
    showCancelButton: true,
    confirmButtonText: "Continuar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (!result.isConfirmed) return;
    let tipo = $("#nuevoComprobante").val();
    if (tipo === "Factura") {
      seleccionarClienteFactura(idventa, idsucursal);
    } else {
      ejecutarCambioComprobante(idventa, tipo, idsucursal);
    }
  });
}

function seleccionarClienteFactura(idventa, idsucursal) {
  $.post(
    "controladores/venta.php?op=selectClienteRUC",
    function (htmlClientes) {
      Swal.fire({
        title: "Seleccionar Cliente (RUC)",
        html: `<select id="clienteFactura" class="form-control">${htmlClientes}</select><br>
                   <button class="btn btn-primary btn-block" id="btnNuevoCliente">
                        <i class="fa fa-user-plus"></i> Nuevo Cliente
                   </button>`,
        showCancelButton: true,
        confirmButtonText: "Usar este cliente",
        cancelButtonText: "Cancelar",
        didOpen: () => {
          $("#clienteFactura").select2({
            dropdownParent: $(".swal2-container"),
          });
          $("#btnNuevoCliente").on("click", function () {
            $("#ModalClientes").modal("show");
            Swal.close();
          });
        },
      }).then((r) => {
        if (!r.isConfirmed) return;
        let idcliente = $("#clienteFactura").val();
        $.post(
          "controladores/venta.php?op=actualizarClienteVentaFactura",
          { idventa: idventa, idcliente: idcliente },
          function (resp) {
            ejecutarCambioComprobante(idventa, "Factura", idsucursal);
          },
        );
      });
    },
  );
}

function ejecutarCambioComprobante(idventa, tipo, idsucursal) {
  $.post(
    "controladores/venta.php?op=cambiar_comprobante",
    { idventa: idventa, tipo: tipo, idsucursal: idsucursal },
    function (resp) {
      if (resp.trim() === "ok") {
        Swal.fire("Correcto", "Comprobante actualizado", "success");
        tabla.ajax.reload();
      } else {
        Swal.fire("Error", resp, "error");
      }
    },
  );
}

// --- 1. LÓGICA DE CARGA DE DATOS ---
function verHistorialCliente() {
  var idcliente = $("#idcliente").val();
  // 1. OBTENER ID SUCURSAL
  var idsucursal = $("#idsucursal").val();

  if (!idcliente || idcliente == "6" || idcliente == "1") {
    return;
  }

  var productosEnCarrito = [];
  $("input[name='idproducto[]']").each(function () {
    if ($(this).val()) productosEnCarrito.push($(this).val());
  });

  // ... (Tu código de loading y fade in sigue igual) ...
  $("#body_historial_flotante").html(
    '<tr><td colspan="6" class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x text-info"></i><p class="mt-2 text-muted">Consultando historial...</p></td></tr>',
  );

  if ($("#floating-history").is(":hidden")) {
    $("#floating-history").fadeIn();
  }

  $.ajax({
    url: "controladores/venta.php?op=listarProductosCliente",
    type: "POST",
    // 2. ENVIAR ID SUCURSAL AL CONTROLADOR
    data: {
      idcliente: idcliente,
      idsucursal: idsucursal,
      ids_carrito: productosEnCarrito,
    },
    dataType: "json",
    success: function (data) {
      // ... (Tu código success sigue exactamente igual) ...
      // (Pega aquí toda la lógica de pintar la tabla y los 8 items que ya tienes)
      var html = "";
      if (data.length > 0) {
        $.each(data, function (i, item) {
          // ... tu lógica de filas ...
          let claseExtra = item.coincide ? "resaltado-carrito" : "";
          let icono = item.coincide
            ? '<i class="fas fa-star text-warning mr-1"></i> '
            : "";
          let colorDesc =
            item.descuento !== "-"
              ? "text-danger font-weight-bold"
              : "text-muted";
          let estiloFila = i >= 8 ? 'style="display:none;"' : "";

          html += `<tr class="${claseExtra}" ${estiloFila}>
                                <td title="${item.producto}">
                                    ${icono}${item.producto.substring(0, 35).toLowerCase()}
                                </td>
                                <td class="text-center">${item.cantidad}</td>
                                <td class="text-right">${item.precio}</td>
                                <td class="text-right ${colorDesc}">${item.descuento}</td>
                                <td class="text-right font-weight-bold text-info">${item.subtotal}</td>
                                <td class="text-center text-muted" title="${item.comprobante}">${item.fecha}</td>
                             </tr>`;
        });
      } else {
        html =
          '<tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-shopping-basket fa-2x mb-2"></i><br>Sin historial reciente en esta sucursal.</td></tr>';
      }
      $("#body_historial_flotante").html(html);
    },
  });
}
// Búsqueda instantánea INTELIGENTE
$("#inputBusquedaHistorial").on("keyup", function () {
  var value = $(this).val().toLowerCase();

  if (value === "") {
    // OPCIÓN A: Si el buscador está vacío, restauramos la vista de "Solo 8"
    $("#body_historial_flotante tr").each(function (index) {
      if (index < 8) {
        $(this).show(); // Muestra los primeros 8
      } else {
        $(this).hide(); // Oculta el resto
      }
    });
  } else {
    // OPCIÓN B: Si hay texto, buscamos en TODOS los registros (incluso los ocultos)
    $("#body_historial_flotante tr").filter(function () {
      // toggle(true) muestra, toggle(false) oculta basado en la coincidencia
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  }
});

// --- 2. LÓGICA PARA ARRASTRAR (DRAG & DROP) ---
// Inicializar la función de arrastre
hacerArrastrable(document.getElementById("floating-history"));

function hacerArrastrable(elmnt) {
  var pos1 = 0,
    pos2 = 0,
    pos3 = 0,
    pos4 = 0;
  var header = document.getElementById("floating-header");

  if (header) {
    // Si existe el header, arrastramos desde ahí
    header.onmousedown = dragMouseDown;
  } else {
    // Si no, desde cualquier parte del div (no recomendado)
    elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();

    // 1. Obtener posición inicial del mouse
    pos3 = e.clientX;
    pos4 = e.clientY;

    // 2. Agregar listeners al DOCUMENTO (no al elemento) para seguir el mouse
    // Usamos addEventListener para no romper otros scripts
    document.addEventListener("mouseup", closeDragElement);
    document.addEventListener("mousemove", elementDrag);
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();

    // 1. Calcular cuánto se movió el cursor
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;

    // 2. Guardar nueva posición del cursor para el siguiente frame
    pos3 = e.clientX;
    pos4 = e.clientY;

    // 3. Aplicar nueva posición al elemento
    // Nota: Al movernos, convertimos la posición a 'top/left' fijos
    // para evitar conflictos si usabas 'bottom' o 'right' en CSS.
    elmnt.style.top = elmnt.offsetTop - pos2 + "px";
    elmnt.style.left = elmnt.offsetLeft - pos1 + "px";

    // Eliminamos 'right' si existía para que 'left' tome el control total
    elmnt.style.right = "auto";
  }

  function closeDragElement() {
    // IMPORTANTE: Eliminar los listeners para liberar memoria del sistema
    document.removeEventListener("mouseup", closeDragElement);
    document.removeEventListener("mousemove", elementDrag);
  }
}
function cancelarmodalCelular() {
  // Limpiar el campo de número celular
  $("#numeroCelular").val("");

  // Resetear otros posibles estados (por ejemplo, eliminar clases activas o desactivar botones)
  $("#modalCelular").find(".is-invalid").removeClass("is-invalid"); // Si hay alguna validación
  $("#modalCelular").find(".is-valid").removeClass("is-valid"); // Si hay alguna validación

  // Cerrar el modal
  $("#modalCelular").modal("hide");
}

function abrirWhatsApp() {
  let nombre_empresa = '';
  let telefono_empresa = '';
  let telefono = document.getElementById("numeroCelular").value;
  let tipo_comprobante = document.getElementById("tipoComprobante").value;
  let num_comprobante = document.getElementById("numComprobante").value;
  let serie_comprobante = document.getElementById("serieComprobante").value;
  let idventa = document.getElementById("idventa").value; // Obtener el idventa desde el modal

  if (telefono) {
    telefono = telefono.startsWith("51") ? telefono : "51" + telefono;

    // Creamos el mensaje con los detalles del comprobante
    let dominio = window.location.origin; // Obtiene el dominio actual (ejemplo: https://tudominio.com)
    let url = `${dominio}/factura/generaFactura.php?id=${idventa}`;
    let qr = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(url)}`;

    let mensaje =
      `🏢 *${nombre_empresa || "Mi Empresa"}*\n` +
      `━━━━━━━━━━━━━━━━━━\n` +
      `🧾 *Comprobante Electrónico*\n\n` +
      `Estimado cliente 👋,\n` +
      `Su comprobante ya está disponible.\n\n` +
      `📄 *Detalles:*\n` +
      `• Tipo: ${tipo_comprobante}\n` +
      `• Serie: ${serie_comprobante}\n` +
      `• Número: ${num_comprobante}\n\n` +
      `🔗 *Descargar aquí:*\n${url}\n\n` +
      `📷 *Escanee el QR:*\n${qr}\n\n` +
      `🙏 Gracias por confiar en nosotros.\n` +
      `📞 Soporte: ${telefono_empresa || "999 999 999"}\n` +
      `━━━━━━━━━━━━━━━━━━`;

    // Mostramos el SweetAlert con los detalles del comprobante
    Swal.fire({
      title: "Confirmar envío",
      text: '¿Desea enviar el comprobante al número de WhatsApp proporcionado?',
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sí, enviar",
      cancelButtonText: "Cancelar",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        // Verificar si el archivo ya fue descargado usando localStorage
        let archivoDescargado = localStorage.getItem(`descargado_${idventa}`);

        if (!archivoDescargado) {
          // Forzar la descarga del archivo PDF solo si no ha sido descargado
          let urlPDF = `reportes/factura/generaFactura.php?id=${idventa}`;
          let link = document.createElement("a");
          link.href = urlPDF;
          link.download = `${tipo_comprobante}-${serie_comprobante}-${num_comprobante}.pdf`; // El nombre del archivo a descargar
          link.click(); // Inicia la descarga

          // Marcar el archivo como descargado
          localStorage.setItem(`descargado_${idventa}`, "true");
        } else {
          console.log("El archivo ya ha sido descargado previamente.");
        }

        // Después de que la descarga comience, abrir WhatsApp
        let urlWhatsApp = `https://api.whatsapp.com/send?phone=${telefono}&text=${encodeURIComponent(mensaje)}`;
        window.open(urlWhatsApp);

        $("#modalCelular").modal("hide"); // Cierra el modal
      } else {
        // Si el usuario cancela, solo cierra el modal
        $("#modalCelular").modal("hide");
      }
    });
  } else {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Por favor, ingrese un número de celular.",
    });
  }
}

function EnviarComprobante(idventa) {
  $.post(
    "controladores/venta.php?op=mostrar",
    { idventa: idventa },
    function (data, status) {
      if (status === "success") {
        data = JSON.parse(data);

        // Si el cliente tiene teléfono, agrega el prefijo '51'
        let telefono = data.telefono
          ? data.telefono.startsWith("51")
            ? data.telefono
            : "51" + data.telefono
          : "";
        let urlPdf =
          window.location.origin +
          "/reportes/documentos/" +
          data.tipo_comprobante +
          "-" +
          data.num_comprobante +
          ".pdf";

        // Mostrar el modal para ingresar el número de celular
        $("#modalCelular").modal("show");

        // Si hay teléfono registrado, precargarlo en el modal
        if (telefono) {
          document.getElementById("numeroCelular").value = telefono;
        }

        // Mostrar los datos del comprobante en el modal
        document.getElementById("tipoComprobante").value =
          data.tipo_comprobante;
        document.getElementById("numComprobante").value = data.num_comprobante;
        document.getElementById("serieComprobante").value =
          data.serie_comprobante;
        document.getElementById("idventa").value = idventa;
      } else {
        alert("Error al obtener los datos de la venta.");
      }
    },
  );
}

function encrypt_decrypt(action, string) {
  if (action === 'encrypt') {
    // Encriptación simple pero efectiva para este caso
    const encoded = btoa(string);
    return encoded.replace(/=/g, '').replace(/\//g, '_').replace(/\+/g, '-');
  }
  return string;
}

function verCronogramPago(idventa) {
  const encryptedId = encrypt_decrypt('encrypt', idventa);
  const url = 'public/docs_service/cronograma_pagos?idventa=' + encryptedId;
  const win = window.open(url, '_blank');
  if (!win) {
    alert('Por favor habilita ventanas emergentes o descarga manualmente: ' + url);
    return;
  }
}

// --- 3. TRIGGER AUTOMÁTICO (OPCIONAL) ---
// Si quieres que se actualice cada vez que agregas un producto:
// Busca tu función 'agregarDetalle' y al final añade:
/* if ($('#floating-history').is(':visible')) {
       verHistorialCliente();
   }
*/
init();
