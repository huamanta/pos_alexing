var tabla;
var contador = 0;
var articuloAdd = "";
var cont = 0;
var detalles = 0;

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

  $.post("controladores/usuario.php?op=selectEmpleado", function (r) {
    $("#idpersonal").html(r);
    $("#idpersonal").select2("");
  });

  //cargamos los items al celect comprobantes
  $.post("controladores/venta.php?op=selectComprobante", function (c) {
    $("#tipo_comprobante").html(c);
    $("#tipo_comprobante").select2("");
  });

  //cargamos los items al select cliente
  $.post("controladores/venta.php?op=selectCliente", function (r) {
    $("#idcliente").html(r);
    $("#idcliente").select2("");
  });

  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal").html(r);
    $("#idsucursal").select2("");
  });

  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal3", function (r) {
    $("#idsucursal2").html(r);
  });

  $.post("controladores/venta.php?op=selectProductoFiltro", function (r) {
      $("#idproducto").html(r);
      $("#idproducto").select2();
  });

  $("#fecha_inicio").change(listar);
  $("#fecha_fin").change(listar);
  $("#idsucursal2").change(listar);
  $("#estado").change(listar);
  $("#idproducto").change(listar);
  $("#idsucursal").change(documentosSucursal);

  $("#navVentasActive").addClass("treeview active");
  $("#navVentas").addClass("treeview menu-open");
  $("#navVenta").addClass("active");

  window.addEventListener(
    "keypress",
    function (event) {
      if (event.keyCode == 13) {
        event.preventDefault();
      }
    },
    false
  );
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

function cancelarmodalCelular() {
    // Limpiar el campo de número celular
    $('#numeroCelular').val("");

    // Resetear otros posibles estados (por ejemplo, eliminar clases activas o desactivar botones)
    $('#modalCelular').find('.is-invalid').removeClass('is-invalid'); // Si hay alguna validación
    $('#modalCelular').find('.is-valid').removeClass('is-valid'); // Si hay alguna validación

    // Cerrar el modal
    $('#modalCelular').modal('hide');
}


function EnviarComprobante(idventa) {
    $.post("controladores/venta.php?op=mostrar", { idventa: idventa }, function(data, status) {
        if (status === "success") {
            data = JSON.parse(data);

            // Si el cliente tiene teléfono, agrega el prefijo '51'
            let telefono = data.telefono ? (data.telefono.startsWith("51") ? data.telefono : "51" + data.telefono) : '';
            let urlPdf = window.location.origin + "/reportes/documentos/" + data.tipo_comprobante + "-" + data.num_comprobante + ".pdf";

            // Mostrar el modal para ingresar el número de celular
            $('#modalCelular').modal('show');

            // Si hay teléfono registrado, precargarlo en el modal
            if (telefono) {
                document.getElementById('numeroCelular').value = telefono;
            }

            // Mostrar los datos del comprobante en el modal
            document.getElementById('tipoComprobante').value = data.tipo_comprobante;
            document.getElementById('numComprobante').value = data.num_comprobante;
            document.getElementById('serieComprobante').value = data.serie_comprobante;
            document.getElementById('idventa').value = idventa;
        } else {
            alert("Error al obtener los datos de la venta.");
        }
    });
}


function abrirWhatsApp() {
    let telefono = document.getElementById('numeroCelular').value;
    let tipo_comprobante = document.getElementById('tipoComprobante').value;
    let num_comprobante = document.getElementById('numComprobante').value;
    let serie_comprobante = document.getElementById('serieComprobante').value;
    let idventa = document.getElementById('idventa').value;  // Obtener el idventa desde el modal

    if (telefono) {
        telefono = telefono.startsWith("51") ? telefono : "51" + telefono;

        // Creamos el mensaje con los detalles del comprobante
        let mensaje = `Estimado cliente, por favor cargue su comprabante descargado desde el gestor de descargas:\n\n` +
                      ` ${tipo_comprobante}\n` +
                      `- ${serie_comprobante}\n` +
                      `- ${num_comprobante}\n\n`;

        // Mostramos el SweetAlert con los detalles del comprobante
        Swal.fire({
            title: 'Confirmar envío',
            text: mensaje,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Verificar si el archivo ya fue descargado usando localStorage
                let archivoDescargado = localStorage.getItem(`descargado_${idventa}`);

                if (!archivoDescargado) {
                    // Forzar la descarga del archivo PDF solo si no ha sido descargado
                    let urlPDF = `reportes/factura/generaFactura.php?id=${idventa}`;
                    let link = document.createElement('a');
                    link.href = urlPDF;
                    link.download = `${tipo_comprobante}-${serie_comprobante}-${num_comprobante}.pdf`;  // El nombre del archivo a descargar
                    link.click();  // Inicia la descarga

                    // Marcar el archivo como descargado
                    localStorage.setItem(`descargado_${idventa}`, 'true');
                } else {
                    console.log("El archivo ya ha sido descargado previamente.");
                }

                // Después de que la descarga comience, abrir WhatsApp
                let urlWhatsApp = `https://api.whatsapp.com/send?phone=${telefono}&text=${encodeURIComponent(mensaje)}`;
                window.open(urlWhatsApp);

                $('#modalCelular').modal('hide');  // Cierra el modal
            } else {
                // Si el usuario cancela, solo cierra el modal
                $('#modalCelular').modal('hide');
            }
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor, ingrese un número de celular.'
        });
    }
}



/*function EnviarComprobante(idventa) {
    $.post("controladores/venta.php?op=mostrar", { idventa: idventa }, function(data, status) {
        if (status === "success") {
            data = JSON.parse(data);

            if (data.telefono) {
                let telefono = data.telefono.startsWith("51") ? data.telefono : "51" + data.telefono;
                let urlPdf = window.location.origin + "/reportes/documentos/" + data.tipo_comprobante + "-" + data.num_comprobante + ".pdf";
                
                // Descargar el PDF
                let link = document.createElement("a");
                link.href = urlPdf;
                link.download = data.tipo_comprobante + "-" + data.num_comprobante + ".pdf";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Mensaje para el usuario
                alert("Se ha descargado el comprobante. Adjunta el archivo en WhatsApp.");

                // Abrir WhatsApp con un mensaje predefinido
                let mensaje = encodeURIComponent("Estimado cliente, aquí está su comprobante de pago. Por favor, adjunte el archivo descargado.");
                let urlWhatsApp = `https://api.whatsapp.com/send?phone=${telefono}&text=${mensaje}`;
                window.open(urlWhatsApp);
            } else {
                alert("El cliente no tiene un número de teléfono registrado.");
            }
        } else {
            alert("Error al obtener los datos de la venta.");
        }
    });
}*/


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
                      dat.apellidoMaterno
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
              error: function () {},
            });
          }
        }
      }
    }
  );
}

$("#formapago").change(function () {
  if (
    $("#formapago").val() == "Reposicion" ||
    $("#formapago").val() == "Costo0"
  ) {
    $("#f1").hide();
    $("#n5").hide();
    $("#n6").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
    $("#n1").hide();
    $("#n2").hide();
    $("#n3").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
  } else if (
    $("#formapago").val() == "Efectivo" &&
    $("#tipopago").val() == "No"
  ) {
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
  } else if (
    $("#formapago").val() == "Efectivo" &&
    $("#tipopago").val() == "Si"
  ) {
    $("#n1").show();
    $("#n2").show();
    $("#n3").show();
    $("#f1").hide();
    $("#n5").hide();
    $("#n6").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
    $("#fechadeposito").hide();
    $("#banco").hide();
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
    $("#n1").show();
    $("#n2").show();
    $("#n3").show();
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
    $("#n1").show();
    $("#n2").show();
    $("#n3").show();
  } else {
    // $('#n1').show();
    $("#f1").show();
    $("#n5").show();
    $("#n6").show();
    $("#fechadeposito").show();
    $("#banco").show();
  }
});

$("#tipopago").change(function () {
  if ($("#tipopago").val() == "Si") {
    $("#n1").show();

    $("#n2").show();

    $("#n3").show();
  } else {
    document.getElementById("n1").style.display = "none";

    document.getElementById("n2").style.display = "none";

    document.getElementById("n3").style.display = "none";
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

      Swal.fire({
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
      });
    },
    complete: function () {
      $(".modal").hide();
    },
  });
}

function mostrar(idventa) {
  $("#getCodeModal").modal("show");
  $.post(
    "controladores/venta.php?op=mostrar",
    { idventa: idventa },
    function (data, status) {
      data = JSON.parse(data);

      // Revisa lo que recibes
      console.log(data);

      // Mostrar datos en los campos
      $("#idventam").val(data.idventa);
      $("#cliente").text(data.cliente);
      $("#personalm").text(data.personal);
      $("#tipo_comprobantem").html(data.tipo_comprobante == 'Boleta' ? 
        '<span class="badge badge-primary">'+data.tipo_comprobante+'</span>' : 
        '<span class="badge badge-info">'+data.tipo_comprobante+'</span>');
      $("#correlativo").text(data.serie_comprobante + " - " + data.num_comprobante);
      $("#ventacreditom").html(data.ventacredito == 'Si' ? 
        '<span class="badge badge-success">'+data.ventacredito+'</span>' : 
        '<span class="badge badge-danger">'+data.ventacredito+'</span>');
      $("#fecha_hora").text(data.fecha);
      $("#impuestom").text(data.impuesto);
      $("#observaciones").text(data.observacion);
      $("#formapagom").html('<span class="badge badge-info">'+data.formapago+'</span>');
      $("#nrooperacionm").text(data.numoperacion);
      $("#fechadeposito").text(data.fechadeposito);

      // Validar montopagado
      let montopagado = parseFloat(data.montopagado) || 0;  // Si es null o NaN, asignamos 0
      $("#abonos").text(montopagado.toFixed(2));  // Mostrar la suma de montopagado

      // Calcular la deuda
      let deuda = parseFloat(data.total_venta) - montopagado;

      if (data.ventacredito == 'Si') {
        // Mostrar deuda solo si es venta a crédito
        $("#deuda").text('S/. ' + deuda.toFixed(2));  // Calcular y mostrar la deuda
      } else {
        $("#deuda").text('---');  // Si no es venta a crédito, no mostrar deuda
      }

      // Asignar el ID de la venta
      $("#idventam").val(data.idventa);
    }
  );

  // Listar los detalles de la venta
  $.post(
    "controladores/venta.php?op=listarDetalle&id=" + idventa,
    function (r) {
      $("#detallesm").html(r);
    }
  );
}


/*function mostrar(idventa) {
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
    }
  );

  $.post(
    "controladores/venta.php?op=listarDetalle&id=" + idventa,
    function (r) {
      $("#detallesm").html(r);
    }
  );
}*/

function guardaryeditar(e) {
  e.preventDefault();

  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "controladores/venta.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (datos) {
      $("#ModalTipocomprobante").modal("show");
      $("#pant-imprimir").html(
        `<div onclick="imprimirBoleta(` +
          datos +
          `)" class="col-sm-6 btn btn-rounded btn-success btn-amber waves-effect waves-light">
    		<i class="fas fa-ticket-alt pr-2" aria-hidden="true"></i>TICKET</div>
            <div onclick="imprimirFactura(` +
          datos +
          `)" class="col-sm-6 btn btn-rounded btn-info btn-amber waves-effect waves-light">
    		<i class="fas fa-file-pdf pr-2" aria-hidden="true"></i>PDF</div>`
      );

      // Recargar la tabla DataTables
      var tbllistado = $("#tbllistado").DataTable();
      tbllistado.ajax.reload();
    },
  });
}

function imprimirBoleta(id) {
  $("#ModalTipocomprobante").modal("hide");
  window.open("reportes/exTicket.php?id=" + id, "IMPRIMIR BOLETA");
  mostrarform(true);
}

function imprimirFactura(id) {
  $("#ModalTipocomprobante").modal("hide");
  window.open(
    "reportes/factura/generaFactura.php?id=" + id,
    "IMPRIMIR FACTURA"
  );
  mostrarform(true);
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
        }
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
  numBoleta();
  numSerieBoleta();
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

//mostramos el num_comprobante del ticket
function numTicket() {
  var idsucursal = $("#idsucursal").val();
  console.log(idsucursal);
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
  console.log(idsucursal);
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

//funcion limpiar
function limpiar() {
  $("#idventa").val("");
  $("#idcliente").val("");
  $("#cliente").val("");
  $("#serie_comprobante").val("");
  $("#num_comprobante").val("");
  // $("#impuesto").val("");
  articuloAdd = "";
  no_aplica = 16;

  $("#total_venta").val("");
  $(".filas").remove();
  $("#total").html("0");

  $("#most_total").html("0");
  $("#most_imp").html("0");

  //obtenemos la fecha actual
  var now = new Date();
  var day = ("0" + now.getDate()).slice(-2);
  var month = ("0" + (now.getMonth() + 1)).slice(-2);
  var today = now.getFullYear() + "-" + month + "-" + day;
  $("#fecha").val(today);

  //marcamos el primer tipo_documento

  $("#tipo_comprobante").val("Nota");

  $("#tipo_comprobante").select2("");

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
  $("#vuelto").val("");

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

  $("#formapago").val("Efectivo");

  $("#porcentaje").val("");
  $("#nroOperacion").val("");
  $("#totalrecibido").val("");
  $("#vuelto").val("");
  $("#observaciones").val("");

  $("#tipopago").val("No");
  $("#montoPagado").val("");

  $("#fechaDepostivo").val("");
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
              data.unidadmedida
            );
          }

          $("#idCodigoBarra").val("");
        }
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
}

function calcularVuelto() {
  let totalrecibido = $("#totalrecibido").val();

  let total = $("#total_venta").val();

  let montoPagado = $("#montoPagado").val();

  if (montoPagado > 0 || montoPagado == null) {
    let vuelto = totalrecibido - montoPagado;

    if (vuelto > 0) {
      $("#vuelto").val(vuelto);
    }
  } else {
    let vuelto = totalrecibido - total;

    if (vuelto > 0) {
      $("#vuelto").val(vuelto);
    } else {
      $("#vuelto").val("0.00");
    }
  }
}

function listarArticulos() {
  var idsucursal = $("#idsucursal").val();

  tabla = $("#tblarticulos")
    .dataTable({
      aProcessing: true, //activamos el procedimiento del datatable
      aServerSide: true, //paginacion y filrado realizados por el server
      dom: "Bfrtip", //definimos los elementos del control de la tabla
      buttons: [],
      ajax: {
        url: "controladores/venta.php?op=listarArticulos3",
        data: { idsucursal: idsucursal },
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 25, //paginacion
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
      iDisplayLength: 25, //paginacion
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
        // Cerrar el modal
        $('#getCodeModal').modal('hide');
        
        // Limpiar el contenido del modal
        $('#cliente').text('');
        $('#personalm').text('');
        $('#fecha_hora').text('');
        $('#tipo_comprobantem').text('');
        $('#correlativo').text('');
        $('#formapagom').text('');
        $('#ventacreditom').text('');
        $('#abonos').text('');
        $('#deuda').text('');
        $('#observaciones').text('');
        
        // Limpiar la tabla de detalles
        $('#detallesm tbody').empty();
    }

//Función mostrar formulario
function mostrarform(flag) {
  limpiar();
  numTicket();
  numSerieTicket();
  if (flag) {
    verificarCaja();
  } else {
    $("#listadoregistros").show();
    $("#formularioregistros").hide();
    $("#aperturcaja").hide();
    $("#btnagregar").show();
    $("#btnNuevo").show();
    $("#header").show();
    $("#btnGuardar").show();
  }
}
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

function verificarCaja() {
  $.ajax({
    url: "controladores/pos.php?op=verificarCaja",
    type: "GET",
    data: "",
    success: function (data) {
      if (data == 1) {
        $("#listadoregistros").hide();
        $("#aperturcaja").hide();
        $("#formularioregistros").show();
        $("#btnagregar").hide();

        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        detalles = 0;
        $("#btnAgregarArt").show();
        $("#btnNuevo").hide();
        $("#header").hide();
      } else {
        $("#aperturcaja").show();
        $("#listadoregistros").hide();
        $("#formularioregistros").hide();
        $("#btnNuevo").hide();
        $("#btnGuardar").hide();
        $("#btnCancelar").hide();
        $("#btnAgregarArt").hide();
        listarCajas();
      }
    },
  });
}

function mostrar_impuesto() {
  $.ajax({
    url: "controladores/negocio.php?op=mostrar_impuesto",
    type: "get",
    dataType: "json",
    success: function (i) {
      impuesto = i;
      $("#impuesto").val(impuesto);
    },
  });
}

$("#tipo_comprobante").change(marcarImpuesto);

function marcarImpuesto() {
  var tipo_comprobante = $("#tipo_comprobante option:selected").text();
  if (tipo_comprobante == "Factura") {
    // $("#impuesto").val(impuesto);
    mostrar_impuesto();
    no_aplica = impuesto;
    numFactura();
    numSerie();
    // $("#serie_comprobante").val( "F001" );
  } else if (tipo_comprobante == "Boleta") {
    // $("#impuesto").val(impuesto);
    mostrar_impuesto();
    no_aplica = impuesto;
    numBoleta();
    numSerieBoleta();
    // $("#serie_comprobante").val( "B001" );
  } else {
    $("#impuesto").val("0");
    no_aplica = 0;
    numTicket();
    numSerieTicket();
    // $("#serie_comprobante").val( "P001" );
  }
}

function agregarDetalle(
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
  unidadmedida
) {
  if ($("#tipo_comprobante").val() != "Nota") {
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
  if (articuloAdd.indexOf(idproducto) != -1) {
    //reporta -1 cuando no existe
    // swal( producto +" ya se agrego");

    let cant = document.getElementsByName("cantidad[]");

    let id = document.getElementsByName("idproducto[]");

    for (var i = 0; i < cant.length; i++) {
      if (id[i].value == idproducto) {
        let total = Number(cant[i].value) + 1;

        document.getElementsByName("cantidad[]")[i].value = total;

        modificarSubtotales();
      }
    }
  } else {
    var cantidad = cant;
    var descuento = desc;

    var cad = "";
    var select = "";

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
        '<td><input style="text-align:center; width: 100px;" type="number" step="0.01" oninput="modificarSubtotales()" name="precio_venta[]" id="precio_venta[]" value="' +
        precio_venta +
        '"></td>';
    } else {
      select =
        '<td><input style="text-align:center; width: 100px;" type="number" step="0.01" oninput="modificarSubtotales()" name="precio_venta[]" id="precio_venta[]" value="' +
        precio_venta +
        '"></td>';
    }

    if (idproducto != "") {
      contador = contador + 1;
      var subtotal = cantidad * precio_venta;
      var fila =
        '<tr class="filas" id="fila' +
        cont +
        '">' +
        '<td><input type="hidden" name="idproducto[]" value="' +
        idproducto +
        '">' +
        producto +
        ' - <span class="badge bg-green">' +
        unidadmedida +
        '</span><input style="width: 240px;" type="text" name="nombreProducto[]" value="' +
        producto +
        '" hidden></td>' +
        '<td><input style="text-align:center; width: 50px;" type="number" step="0.01" oninput="modificarSubtotales()" name="cantidad[]" id="cantidad[]" value="' +
        cantidad +
        '"></td>' +
        select +
        '<td style="text-align: center;"><input style="text-align:center; width: 50px;" type="number" step="0.01" oninput="modificarSubtotales()" name="descuento[]" value="' +
        descuento +
        '"></td>' +
        '<td style="text-align: center;"><input style="text-align:center; width: 50px;" type="text" readonly="readonly" name="stock[]" value="' +
        stock +
        '"></td>' +
        '<td style="text-align: center;"><span style="text-align:center" id="subtotal' +
        cont +
        '" name="subtotal">' +
        subtotal.toFixed(2) +
        "</span></td>" +
        '<td style="text-align: center;"><button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(' +
        cont +
        ')"><i class="fa fa-trash"></i></button></td>' +
        '<td hidden><span style="text-align:center" id="proigv' +
        cont +
        '" name="proigv" hidden>' +
        proigv +
        "</span></td>" +
        "</tr>";
      cont++;
      detalles = detalles + 1;
      articuloAdd = articuloAdd + idproducto + "-"; //aca concatemanos los idarticulos xvg: 1-2-5-12-20
      $("#detalles").append(fila);
      modificarSubtotales();
      // $('#tipo_comprobante').attr('disabled',true);
    } else {
      alert("error al ingresar el detalle, revisar las datos del articulo ");
    }
  }
}
function mostrarAlerta(mensaje) {
  toastr.success(mensaje, "Éxito", {
    timeOut: 300, // Establece el tiempo de duración en milisegundos (en este caso, 3000 ms o 3 segundos)
  });
}

function nostock() {
  Swal.fire("Alerta", "Sin Stock", "info");
}

function modificarSubtotales(e) {
  var cant = document.getElementsByName("cantidad[]");
  var prec = document.getElementsByName("precio_venta[]");
  var desc = document.getElementsByName("descuento[]");
  var sub = document.getElementsByName("subtotal");
  var Stoc = document.getElementsByName("stock[]");

  for (var i = 0; i < cant.length; i++) {
    var inpC = cant[i];
    var inpP = prec[i];
    var inpD = desc[i];
    var inpS = sub[i];
    var inpSt = Stoc[i];

    var subtl = (inpS.value =
      inpC.value * inpP.value - inpD.value * inpC.value);
    var subfinal = subtl.toFixed(2);

    if ($("#tipo").val() == "venta") {
      if (Number(inpC.value) > Number(inpSt.value)) {
        Swal.fire({
          title: "Alerta",
          icon: "info",
          text: "No hay suficiente stock",
        });
        inpC.style.backgroundColor = "#00CC00";
        inpSt.style.backgroundColor = "#CC0000";
        $("#btnGuardar").hide();
        e.preventDefault();
      } else {
        inpC.style.backgroundColor = "#FFFFFF";
        inpSt.style.backgroundColor = "#FFFFFF";
        document.getElementsByName("subtotal")[i].innerHTML = subfinal;
      }
    }
  }

  calcularTotales();
  evaluar();
}

function calcularTotales() {
  var sub = document.getElementsByName("subtotal");
  var total = 0.0;
  var total_monto = 0.0;
  var igv_dec = 0.0;
  var totalConIgv = 0.0;

  for (var i = 0; i < sub.length; i++) {
    total += document.getElementsByName("subtotal")[i].value;

    var proigv = document.getElementsByName("proigv")[i].innerHTML;

    if (proigv == "Gravada") {
      totalConIgv += document.getElementsByName("subtotal")[i].value;
      var igv = (totalConIgv * no_aplica) / (no_aplica + 100);
      var total_monto = (totalConIgv - igv).toFixed(2);
      var igv_dec = igv.toFixed(2);
    } else {
    }
  }

  $.ajax({
    url: "controladores/negocio.php?op=mostrar_simbolo",
    type: "get",
    dataType: "json",
    success: function (sim) {
      simbolo = sim;
      total2 = total - igv;

      $("#total").html(total.toFixed(2));
      $("#total_venta").val(total.toFixed(2));
      $("#most_total2").val(total.toFixed(2));
      $("#most_total").html(esnulo(total2).toFixed(2));

      $("#montoDeuda").val(total);

      $("#most_imp").html(igv_dec);
      evaluar();
    },
  });
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
        }
      );
    } else {
      Swal.fire(
        "! Cancelado ¡",
        "Se Cancelo la anulación de la Venta",
        "error"
      );
    }
  });
}

function esnulo(v) {
  if (isNaN(v)) {
    return 0;
  } else {
    return v;
  }
}

function evaluar() {
  if (detalles > 0) {
    $("#btnGuardar").show();
  } else {
    $("#btnGuardar").hide();
    cont = 0;
    igv = 0;
    igv_dec = 0;
    $("#most_total").val("0");
    $("#most_imp").val("0");
  }
}

function eliminarDetalle(indice) {
  $("#fila" + indice).remove();
  calcularTotales();
  detalles = detalles - 1;
  evaluar();
  articuloAdd = "";
}

function notaCredito(idventa, idsucursal) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Se generará una Nota de Crédito para este comprobante",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("controladores/venta.php?op=notacredito", 
                { comprobanteReferencia: idventa, idsucursal: idsucursal, idmotivo: 1 }, 
                function(resp) {
                    Swal.fire({
                        title: 'Nota de Crédito',
                        text: resp,
                        icon: 'success'
                    });
                    listar(); // refrescamos la tabla
                }
            ).fail(function(xhr) {
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un problema al generar la Nota de Crédito',
                    icon: 'error'
                });
            });
        }
    });
}

$("#btnExportarExcel").on("click", function () {

    let fecha_inicio = $("#fecha_inicio").val();
    let fecha_fin = $("#fecha_fin").val();
    let estado = $("#estado").val();
    let idsucursal = $("#idsucursal").val();
    let idproducto = $("#idproducto").val();

    // Construimos la URL con los parámetros
    let url = `controladores/venta.php?op=exportar_excel`
            + `&fecha_inicio=${fecha_inicio}`
            + `&fecha_fin=${fecha_fin}`
            + `&estado=${estado}`
            + `&idsucursal=${idsucursal}`
            + `&idproducto=${idproducto}`;

    window.open(url, '_blank'); // ⬅ descarga directa
});

init();
