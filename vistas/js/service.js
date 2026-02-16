let tabla;
let productosSeleccionados = [];

function init() {
    $("#body").addClass("sidebar-collapse sidebar-mini");
    listar();

    $("#formularioServicio").on("submit", function(e) {
        guardaryeditar(e);
    });
    // Restablecer overflow cuando se cierre modalProducto
    $('#modalProducto').on('hidden.bs.modal', function () {
        $('#modalServicio').css('overflow', 'auto');
    });
    $.post("controladores/venta.php?op=selectSucursal3", function (r) {
        $("#idsucursal2").html(r);
        $("#idsucursal2").select2("");
    });
    $.post("controladores/venta.php?op=selectSucursal", function (r) {
        $("#idsucursal").html(r);
        $("#idsucursal").select2("");
      });
    $.post("controladores/usuario.php?op=selectEmpleadoServicio", function (r) {
        $("#idtecnico").html(r);
        $("#idtecnico").select2("");
      });
    $.post("controladores/venta.php?op=selectCliente", function (r) {
        $("#idcliente").html(r);
        $("#idcliente").select2("");
      });
    $("#formularioClientes").on("submit", function (e) {
        guardarCliente(e);
      });
    $('#modalProducto').on('hidden.bs.modal', function () {
        if ($('#modalServicio').hasClass('show')) {
            $('body').addClass('modal-open'); // ← importante
        }

        // Por si acaso quieres que el modalServicio sea scrollable
        $('#modalServicio').css('overflow-y', 'auto');
    });


    $("#fecha_inicio").change(listar);
    $("#fecha_fin").change(listar);
    $("#idsucursal2").change(listar);
    $("#estadofiltro").change(listar);
}

$('#modalServicio').on('show.bs.modal', function () {
    // Solo generar serie y número si es un nuevo servicio (no se está editando)
    if (!$("#idservicio").val()) {
        numSerieTicket();
        numTicket();
    } else {
        // Si estamos editando, no hacer nada, ya que num_comprobante y serie_comprobante ya están cargados
    }
});


function mostrarmodal() {
    limpiarFormulario();

    // Esperar hasta que se cargue el select de sucursal (si es por AJAX)
    setTimeout(() => {
        if (!$("#idservicio").val()) { // ← Confirma que sea nuevo
            numSerieTicket();
            numTicket();
            let ahora = new Date();
            ahora.setMinutes(ahora.getMinutes() - ahora.getTimezoneOffset());
            let fechaFormateada = ahora.toISOString().slice(0,16); // Para input datetime-local
            $("#fecha_ingreso").val(fechaFormateada);
             // Cargar select de técnicos (incluye opción "-- Seleccionar --")
            $.post("controladores/usuario.php?op=selectEmpleadoServicio", function(r){
                $("#idtecnico").html(r).val("").trigger("change"); // ← Asegura que se seleccione la opción vacía
            });
        }
        $('#modalServicio').modal('show');
        $("#estado").removeClass('estado-entregado'); 
    }, 200); // Ajusta el delay si es necesario
}

//mostramos el num_comprobante del ticket
function numTicket() {
  var idsucursal = $("#idsucursal").val();
  console.log(idsucursal);
  $.ajax({
    url: "controladores/servicio.php?op=mostrar_num_ticket2",
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
    url: "controladores/servicio.php?op=mostrar_s_ticket2",
    data: { idsucursal: idsucursal },
    type: "get",
    dataType: "json",
    success: function (s) {
      series = s;
      $("#numeros").html(("000" + series).slice(-3)); // "0001"
      $("#serie_comprobante").val("S" + ("000" + series).slice(-3)); // "0001"
    },
  });
}

function listar() {
  let fecha_inicio = $("#fecha_inicio").val();
  let fecha_fin = $("#fecha_fin").val();
  var estado = $("#estadofiltro").val();
  let idsucursal2 = $("#idsucursal2").val();
  tabla = $("#tbllistado")
    .dataTable({
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
      dom: '<"row"<"col-sm-12 col-md-4"<"col-sm-12 col-md-6"f>l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
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
          title: "Lista de Servicios",
          // className: 'btn btn-success'
        },
        {
          extend: "pdf",
          text: "<i class='fas fa-file-pdf'></i>",
          titleAttr: "Exportar a PDF",
          title: "Lista de Servicios",
          // className: 'btn btn-danger'
        },
        {
          extend: "colvis",
          text: "<i class='fas fa-bars'></i>",
          titleAttr: "",
          // className: 'btn btn-danger'
        },
      ],
       "ajax": {
            url: 'controladores/servicio.php?op=listar',
            data: {
              fecha_inicio: fecha_inicio,
              fecha_fin: fecha_fin,
              estado: estado,
              idsucursal2: idsucursal2,
            },
            type: "get",
            dataType: "json"
        
      },
      bDestroy: true,
      iDisplayLength: 5, //Paginación
      order: [[1, "desc"]], //Ordenar (columna,orden)
    })
    .DataTable();
}

function guardaryeditar(e) {
    e.preventDefault();

    let productos = [];
    let total = 0;

    $("#tablaServicios tbody tr").each(function () {
        let idproducto = $(this).find("input[name='idproducto']").val();
        let nombre = $(this).find("input[name='nombre']").val();
        let precio = parseFloat($(this).find("td:eq(1) input").val()) || 0;
        let cantidad = parseInt($(this).find("td:eq(2) input").val()) || 1;
        total += precio * cantidad;

        productos.push({ idproducto, nombre, precio, cantidad });
    });

    $("#total").val(total.toFixed(2));

    var formData = new FormData($("#formularioServicio")[0]);
    formData.append("productos", JSON.stringify(productos));
    formData.append("total", total.toFixed(2));

    $.ajax({
        url: "controladores/servicio.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            Swal.fire("Servicio Técnico", datos, "success");
            $('#modalServicio').modal('hide');
            tabla.ajax.reload();

            // Limpiar si fue nuevo
            if (!$("#idservicio").val()) {
                limpiarFormulario();
                numSerieTicket();
                numTicket();
            } else {
                limpiarFormulario(false); // parámetro para no limpiar num_comprobante
            }
            $("#estado").removeClass('estado-entregado');
        }
    });
}

function cancelarform() {
    // Cierra el modal
    $('#modalServicio').modal('hide');

    // Limpia el formulario
    limpiarFormulario();

    // Limpia errores, clases o validaciones si las usas (opcional)
    $("#formularioServicio").removeClass("was-validated");

    // Limpia tabla de servicios asociados
    $("#tablaServicios tbody").empty();
}

function cargarEstados(callback) {
    const estados = ["Recibido", "En proceso", "Terminado", "Entregado"];
    const select = $("#estado_modal");
    select.empty();
    estados.forEach(estado => {
        select.append(`<option value="${estado}">${estado}</option>`);
    });

    if (callback) callback(); // ejecuta después
}


function mostrar(idservicio) {
    $.post("controladores/servicio.php?op=mostrar", { idservicio: idservicio }, function(data) {
        data = JSON.parse(data);
        $("#modalServicio").modal("show");

        $("#idservicio").val(data.idservicio);
        setTimeout(function() {
          $("#idsucursal").val(data.idsucursal).trigger('change');
        }, 100);
        $("#tipo_comprobante").val(data.tipo_comprobante);
        setTimeout(function() {
          $("#serie_comprobante").val(data.serie_comprobante);
          $("#num_comprobante").val(data.num_comprobante);
        }, 100);
        setTimeout(function() {
          $("#idcliente").val(data.idcliente).trigger('change');
        }, 100);
        $("#equipo").val($("<textarea>").html(data.equipo).text());
        setTimeout(function() {
          $("#idtecnico").val(data.idtecnico).trigger('change');
        }, 100);
        $("#fecha_ingreso").val(data.fecha_ingreso.replace(" ", "T"));
        $("#fecha_reparacion").val(data.fecha_reparacion?.replace(" ", "T"));
        $("#fecha_entrega").val(data.fecha_entrega?.replace(" ", "T"));
        $("#descripcion_problema").val(data.descripcion_problema);
        $("#descripcion_solucion").val(data.descripcion_solucion);
        cargarEstados(function () {
            $("#estado").val(data.estado);
        });
        if (data.estado === "Entregado") {
            $("#estado").addClass('estado-entregado');  // Añadir clase roja
        } else {
            $("#estado").removeClass('estado-entregado'); // Eliminar clase roja si no es "Entregado"
        }
        $("#total").val(data.total);
        
        // Cargar detalle de servicios
        cargarServiciosAsociados(data.idservicio);
    });
}

function cargarServiciosAsociados(idservicio) {
    $.post("controladores/servicio.php?op=listarDetalle", { idservicio: idservicio }, function(data) {
        data = JSON.parse(data);
        $("#tablaServicios tbody").empty();
        data.forEach(p => {
            const fila = `
                <tr>
                    <td>
                        <input type="hidden" name="idproducto" value="${p.idproducto}">
                        <input type="text" class="form-control form-control-sm" name="nombre" value="${p.nombre}">
                    </td>
                    <td><input type="number" class="form-control form-control-sm" value="${p.precio}" step="0.01"></td>
                    <td><input type="number" class="form-control form-control-sm" value="${p.cantidad}" min="1"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">Eliminar</button></td>
                </tr>`;
            $("#tablaServicios tbody").append(fila);
        });
    });
}



// Abrir modal para seleccionar productos (servicios)
function abrirModalProductos() {
    $("#modalProducto").modal("show");

    $.getJSON("controladores/producto.php?op=listarservice", function(data) {
        let filas = "";
        data.forEach(p => {
            filas += `
                <tr>
                    <td>${p.nombre}</td>
                    <td>${p.precio}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick='agregarServicio(${JSON.stringify(p)})'>Agregar</button>
                    </td>
                </tr>`;
        });
        $("#tablaProductos tbody").html(filas);
    });
}

function agregarServicio(producto) {
    const fila = `
        <tr>
            <td>
                <input type="hidden" name="idproducto" value="${producto.idproducto}">
                <input type="text" class="form-control form-control-sm" name="nombre" value="${producto.nombre}">
            </td>
            <td><input type="number" class="form-control form-control-sm" value="${producto.precio}" step="0.01" oninput="calcularTotal()"></td>
            <td><input type="number" class="form-control form-control-sm" value="1" min="1" oninput="calcularTotal()"></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">Eliminar</button></td>
        </tr>`;
    $("#tablaServicios tbody").append(fila);
    //$("#modalProducto").modal("hide");
    calcularTotal();
}

// Función para calcular el total de los servicios
function calcularTotal() {
    let total = 0;
    $("#tablaServicios tbody tr").each(function () {
        let precio = parseFloat($(this).find("td:eq(1) input").val()) || 0;  // Obtener precio de la columna 2
        let cantidad = parseInt($(this).find("td:eq(2) input").val()) || 0;  // Obtener cantidad de la columna 3
        total += precio * cantidad;  // Sumar al total
    });

    // Actualizar el total en el campo correspondiente del formulario
    $("#total").val(total.toFixed(2));  // Se redondea a 2 decimales
}


// Eliminar fila de servicio
function eliminarFila(btn) {
    $(btn).closest('tr').remove();
}

// Limpiar el formulario
function limpiarFormulario() {
    $("#formularioServicio")[0].reset();
    $("#idservicio").val("");
    $("#idcliente").val("6").trigger('change'); 
    $("#serie_comprobante").val("");
    $("#tablaServicios tbody").empty();
}

function ver(idservicio) {
  $.post("controladores/servicio.php?op=mostrar", { idservicio }, function(data) {
    data = JSON.parse(data);

    $("#ticket-num-comprobante").text(data.tipo_comprobante + " " + data.serie_comprobante + "-" + data.num_comprobante);
    $("#ticket-cliente").text(data.cliente);
    $("#ticket-equipo").text(data.equipo);
    $("#ticket-tecnico").text(data.tecnico);
    $("#ticket-fecha-ingreso").text(data.fecha_ingreso.replace(" ", "T"));
    $("#ticket-fecha-entrega").text(data.fecha_entrega ? data.fecha_entrega.replace(" ", "T") : "Sin entrega");
    $("#ticket-estado").text(data.estado);
    $("#ticket-descripcion-problema").text(data.descripcion_problema);
    $("#ticket-descripcion-solucion").text(data.descripcion_solucion);
    $("#ticket-total").text(parseFloat(data.total).toFixed(2));

    // Luego obtenemos los servicios asociados
    $.post("controladores/servicio.php?op=listarDetalle", { idservicio }, function(detalles) {
      let servicios = JSON.parse(detalles);
      let html = "";
      servicios.forEach(item => {
        let subtotal = item.precio * item.cantidad;
        html += `
          <tr>
            <td>${item.nombre}</td>
            <td>${item.cantidad}</td>
            <td>S/ ${parseFloat(item.precio).toFixed(2)}</td>
            <td>S/ ${subtotal.toFixed(2)}</td>
          </tr>`;
      });
      $("#ticket-detalle-servicios").html(html);
    });

    // Mostrar el modal
    $('#modalTicket').modal('show');
  });
}
function imprimirTicket() {
  window.print();
}

function cerrarmodalticket(){
    $('#modalTicket').modal('hide');
}

function eliminarservicio(idservicio) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "¡Esta acción eliminará el servicio y sus detalles!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("controladores/servicio.php?op=eliminar", { idservicio }, function (respuesta) {
        Swal.fire("Eliminado", respuesta, "success");
        tabla.ajax.reload();
      });
    }
  });
}

function guardarCliente(e) {
  e.preventDefault(); //no se activara la accion predeterminada
  //$("#btnGuardar").prop("disabled",true);
  var formData = new FormData($("#formularioClientes")[0]);

  $.ajax({
    url: "controladores/servicio.php?op=guardarCliente",
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
      $.post("controladores/servicio.php?op=selectCliente", function (r) {
        $("#idcliente").html(r);
        $("#idcliente").select2("");
      });

      $.post(
        "controladores/servicio.php?op=mostrarUltimoCliente",
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
    "controladores/servicio.php?op=selectCliente3&numero=" + numero,
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

function seleccionarCliente(nombre, idcliente) {
  $("#idcliente").val(idcliente);
  $("#idcliente").select2("");
}

$(document).on('change', '#estado', function () {
    const estado = $(this).val();
    const fechaActual = new Date();
    fechaActual.setMinutes(fechaActual.getMinutes() - fechaActual.getTimezoneOffset());
    const fechaLima = fechaActual.toISOString().slice(0, 16); // formato para datetime-local

    if (estado === "Terminado") {
        $("#fecha_reparacion").val(fechaLima);
    } else if (estado === "Entregado") {
        $("#fecha_entrega").val(fechaLima);

        // Mostrar alerta o mensaje visual
        Swal.fire({
            icon: 'info',
            title: 'Equipo entregado',
            text: 'El estado ya no puede ser modificado.',
            timer: 3000
        });

        // Pintar el select de rojo cuando el estado es "Entregado"
        $(this).addClass('estado-entregado');
    } else {
        // Si el estado cambia a otro diferente a "Entregado", quitar la clase roja
        $(this).removeClass('estado-entregado');
    }
});
// Crear un elemento <style> y agregarlo al <head> del documento
function agregarEstilosDinamicos() {
    const estilo = `
        .estado-entregado {
            border: 2px solid red; /* Cambia el borde del select a rojo */
            background-color: #f8d7da; /* Fondo rosado claro para el select */
        }
    `;

    // Crear la etiqueta <style>
    const style = document.createElement('style');
    style.type = 'text/css';

    // Añadir el estilo a la etiqueta <style>
    if (style.styleSheet) {
        style.styleSheet.cssText = estilo; // Para IE
    } else {
        style.appendChild(document.createTextNode(estilo)); // Para navegadores modernos
    }

    // Agregar la etiqueta <style> al <head> del documento
    document.head.appendChild(style);
}

// Llamar a la función para agregar los estilos dinámicamente cuando se cargue la página
agregarEstilosDinamicos();

init();
