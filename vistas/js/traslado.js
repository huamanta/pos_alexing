var tabla;
var paginaActual = 1;
var limite = 10;

function init() {
  listar();

  $("#formTraslado").on("submit", function (e) {
    guardaryeditar(e);
  });
  $.post("controladores/venta.php?op=selectSucursal3", function (r) {
      $("#idsucursal2").html(r);
      $("#idsucursal2").select2("");
    });

  $("#fecha_inicio").change(listar);
  $("#fecha_fin").change(listar);
  $("#estado").change(listar);

  $('#navAlmacenActive').addClass("treeview active");
  $('#navAlmacen').addClass("treeview menu-open");
  $('#navTraslado').addClass("active");

  cargarAlmacenes();

  configurarBotones();
}

//==============================
// GUARDAR O EDITAR
//==============================
function guardaryeditar(e) {
  e.preventDefault();

  const idorigen = $("#idorigen").val();
  const iddestino = $("#iddestino").val();

  if (!iddestino || iddestino === "") {
    Swal.fire("Atención", "Debe seleccionar un almacén destino", "warning");
    return;
  }

  if (iddestino == idorigen) {
    Swal.fire("Atención", "El almacén destino debe ser distinto al de origen", "warning");
    return;
  }

  let productos = [];
  let valida = true;
  $("#tablaDetalle tbody tr").each(function () {
    let idproducto = $(this).data("idproducto");
    let cantidad = parseInt($(this).find(".cantidad").val()) || 0;
    let stock = parseInt($(this).data("stock")) || 0;
    let nombre = $(this).find("td").first().text().trim();

    if (!idproducto || cantidad <= 0) {
      Swal.fire("Atención", "Cantidad inválida en algún producto", "warning");
      valida = false;
      return false; // break each
    }

    if (cantidad > stock) {
      Swal.fire("Atención", `La cantidad solicitada (${cantidad}) supera el stock disponible (${stock}) para: ${nombre}`, "warning");
      valida = false;
      return false;
    }

    productos.push({ idproducto, cantidad });
  });

  if (!valida) return;

  if (productos.length === 0) {
    Swal.fire("Atención", "Debe agregar al menos un producto", "warning");
    return;
  }

  var formData = new FormData($("#formTraslado")[0]);
  formData.append("productos", JSON.stringify(productos));

  $.ajax({
    url: "controladores/traslado.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (datos) {
      Swal.fire({ title: 'Traslado', icon: 'success', text: datos });
      $('#modalTraslado').modal('hide');
      tabla.ajax.reload();
      limpiar();
    },
    error: function (error) {
      console.log(error.responseText);
      Swal.fire("Error", "Ocurrió un error en el servidor.", "error");
    }
  });
}

//==============================
// BOTONES Y EVENTOS
//==============================
function configurarBotones() {
  $("#btnAgregarProductos").click(function () {
    listarProductos('', 1, 'traslado');
    $("#modalProductos").modal("show");
  });

  $("#btnBuscarProducto").click(function () {
    const texto = $("#buscarProducto").val();
    listarProductos(texto, 1);
  });

  $("#buscarProducto").keyup(function (e) {
    const texto = $(this).val();
    listarProductos(texto, 1);
  });

  $("#btnAgregarSeleccionados").click(function () {
    $("#tablaProductos tbody input.chkProducto:checked").each(function () {
      let id = $(this).val();
      let nombre = $(this).data("nombre");
      let stock = $(this).data("stock") || 0;

      // Evitar duplicados en la tabla detalle
      if ($("#tablaDetalle tbody tr[data-idproducto='" + id + "']").length > 0) {
        // ya existe -> ignorar. Si quieres sumar cantidad en vez de ignorar, lo cambiamos.
        return;
      }

      let fila = `
        <tr data-idproducto="${id}" data-stock="${stock}">
          <td>${nombre} <small class="text-muted"> (Stock: ${stock})</small></td>
          <td><input type="number" class="form-control form-control-sm cantidad" min="1" value="1"></td>
          <td><button type="button" class="btn btn-danger btn-xs btnEliminarFila"><i class="fa fa-times"></i></button></td>
        </tr>`;
      $("#tablaDetalle tbody").append(fila);
    });
    $("#modalProductos").modal("hide");
  });

  // Delegated event para eliminar fila
  $(document).on("click", ".btnEliminarFila", function () {
    $(this).closest("tr").remove();
  });
} // <-- cierre de configurarBotones()

//==============================
function limpiar() {
  $("#iddestino").val("");
  $("#tablaDetalle tbody").html("");
}

function listar() {
  let fecha_inicio = $("#fecha_inicio").val();
  let fecha_fin = $("#fecha_fin").val();
  var estado = $("#estado").val();
  let idsucursal2 = $("#idsucursal2").val();
  tabla = $('#tbllistado').DataTable({
    "aProcessing": true,
    "aServerSide": true,
    "language": {
      "processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
    },
    "responsive": true,
    "lengthChange": false,
    "autoWidth": false,
    dom: 'Bfrtip',
    buttons: ['pageLength', 'excelHtml5', 'pdf', 'colvis'],
    "ajax": {
      url: 'controladores/traslado.php?op=listar',
      data: {
          fecha_inicio: fecha_inicio,
          fecha_fin: fecha_fin,
          estado: estado,
          idsucursal2: idsucursal2,
        },
      type: "get",
      dataType: "json",
      error: function (e) { console.log(e.responseText); }
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "desc"]]
  });
}


function verProductos(idtraslado) {
    $.ajax({
        url: 'controladores/traslado.php?op=verdetalle',
        type: 'GET',
        data: { idtraslado: idtraslado },
        dataType: 'json',
        success: function(data) {
            let tbody = '';
            data.forEach(item => {
                tbody += `<tr>
                    <td>${item.producto}</td>
                    <td>${item.cantidad}</td>
                    <td>${item.destino}</td>
                </tr>`;
            });
            $('#tablaDetalleProductos tbody').html(tbody);
            $('#modalDetalleProductos').modal('show');
        }
    });
}

function cargarAlmacenesDestino() {
  $.post("controladores/traslado.php?op=almacenesDestino", function (r) {
    $("#iddestino").html(r);
  });
}

//==============================
// LISTAR PRODUCTOS CON PAGINACIÓN Y BUSCADOR
//==============================
function listarProductos(busqueda = '', pagina = 1, tipo = 'traslado') {
  const iddestino = $("#iddestino").val();

  if (!iddestino) {
    Swal.fire("Atención", "Seleccione un almacén destino antes de agregar productos", "warning");
    return;
  }

  $.post("controladores/traslado.php?op=listarProductos", 
    { busqueda, pagina, limite, iddestino, tipo }, 
    function (r) {
      const data = JSON.parse(r);
      $("#tablaProductos tbody").html(data.html);
      $("#paginacionProductos").html(data.paginacion);
    }
  );
}

//==============================
// CAMBIAR PÁGINA DE PRODUCTOS
//==============================
function cambiarPagina(pag) {
  const texto = $("#buscarProducto").val();
  listarProductos(texto, pag);
}

function cargarAlmacenes() {
  // 1️⃣ Mostrar el nombre del almacén de origen (sucursal actual)
  $.getJSON("controladores/traslado.php?op=sucursal_actual", function (data) {
    if (data && data.idsucursal) {
      $("#idorigen").val(data.idsucursal);
      $("#nombre_origen").val(data.nombre);
    }
  });

  // 2️⃣ Cargar lista de almacenes destino
  $.post("controladores/traslado.php?op=almacenesDestino", function (r) {
    $("#iddestino").html(r);
  });
}

////////////////////////////////////////////////////////////////

// Inicialización
$("#formSolicitud").on("submit", function(e){
    e.preventDefault();
    enviarSolicitud();
});

// Cargar almacenes destino
function cargarAlmacenesSolicitud() {
    $.post("controladores/traslado.php?op=almacenesDestino", function(r){
        $("#iddestino_solicitud").html(r);
    });
}
cargarAlmacenesSolicitud();

// Botón seleccionar productos
$("#btnAgregarProductosSolicitud").click(function(){
    listarProductos('', 1, 'solicitud'); // reutiliza tu función existente para listar productos
    $("#modalProductos").modal("show");
});

// Agregar productos seleccionados a la tabla de solicitud
$("#btnAgregarSeleccionados").click(function(){
    $("#tablaProductos tbody input.chkProducto:checked").each(function () {
        let id = $(this).val();
        let nombre = $(this).data("nombre");
        let stock = $(this).data("stock") || 0;

        if ($("#tablaDetalleSolicitud tbody tr[data-idproducto='" + id + "']").length > 0) return;

        let fila = `
        <tr data-idproducto="${id}" data-stock="${stock}">
            <td>${nombre} <small class="text-muted">(Stock: ${stock})</small></td>
            <td><input type="number" class="form-control form-control-sm cantidad" min="1" value="1"></td>
            <td><button type="button" class="btn btn-danger btn-xs btnEliminarFila"><i class="fa fa-times"></i></button></td>
        </tr>`;
        $("#tablaDetalleSolicitud tbody").append(fila);
    });
    $("#modalProductos").modal("hide");
});

// Eliminar fila
$(document).on("click", "#tablaDetalleSolicitud .btnEliminarFila", function(){
    $(this).closest("tr").remove();
});

// Enviar solicitud
function enviarSolicitud() {
    const iddestino = $("#iddestino_solicitud").val();
    if (!iddestino) {
        Swal.fire("Atención", "Seleccione un almacén destino", "warning");
        return;
    }

    let productos = [];
    let valida = true;
    $("#tablaDetalleSolicitud tbody tr").each(function () {
        let idproducto = $(this).data("idproducto");
        let cantidad = parseInt($(this).find(".cantidad").val()) || 0;
        let nombre = $(this).find("td").first().text().trim();

        if (!idproducto || cantidad <= 0) {
            Swal.fire("Atención", `Cantidad inválida en ${nombre}`, "warning");
            valida = false;
            return false;
        }
        productos.push({idproducto, cantidad});
    });

    if (!valida || productos.length === 0) return;

    let formData = new FormData($("#formSolicitud")[0]);
    formData.append("productos", JSON.stringify(productos));

    $.ajax({
        url: "controladores/traslado.php?op=guardarSolicitud",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(res) {
            Swal.fire("Solicitud", res, "success");
            $("#modalSolicitud").modal("hide");
            tabla.ajax.reload();
            limpiarSolicitud();
        },
        error: function(err){
            Swal.fire("Error", "Ocurrió un error en el servidor", "error");
            console.log(err.responseText);
        }
    });
}

function limpiarSolicitud() {
    $("#tablaDetalleSolicitud tbody").html("");
    $("#iddestino_solicitud").val("");
}

function cargarSucursalActual() {
    $.ajax({
        url: "controladores/traslado.php?op=sucursal_actual",
        type: "POST",
        dataType: "json",
        success: function(data) {
            if (data && data.nombre) {
                $("#nombre_sucursal_origen").val(data.nombre);
            } else {
                $("#nombre_sucursal_origen").val("No definida");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar sucursal actual:", error);
            $("#nombre_sucursal_origen").val("Error");
        }
    });
}

// Llamar cuando se abre el modal de solicitud
$('#modalSolicitud').on('shown.bs.modal', function () {
    cargarSucursalActual();
    $('#tablaDetalleSolicitud tbody').empty();
});

// 🔹 Cargar productos en el modal
function verProductosSolicitud(idtraslado, soloLectura = false) {
    $.post(
        "controladores/traslado.php?op=verProductosSolicitud",
        { idtraslado: idtraslado, soloLectura: soloLectura },
        function (data) {
            if (!data) {
                console.error(" Respuesta vacía del servidor.");
                alert("No se obtuvo respuesta del servidor.");
                return;
            }

            let json;
            try {
                json = JSON.parse(data);
            } catch (e) {
                console.error(" Error al parsear JSON:", data);
                alert("Error al leer los datos del servidor.");
                return;
            }

            if (json.error) {
                alert(json.error);
                return;
            }

            const productos = json.productos || [];

            let html = "";
            productos.forEach((p) => {
                html += `
                    <tr>
                        <td class="nombreProducto">${p.nombre}</td>
                        <td>
                            <input type="hidden" class="idProductoHidden" value="${p.idproducto}">
                            <input type="number" class="form-control cantidadProducto" value="${p.cantidad}" min="1">
                        </td>
                        <td>
                            <select class="form-control estadoProducto">
                                <option value="pendiente" ${p.estado_detalle === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="aceptado" ${p.estado_detalle === 'aceptado' ? 'selected' : ''}>Aceptar</option>
                                <option value="rechazado" ${p.estado_detalle === 'rechazado' ? 'selected' : ''}>Rechazar</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control observacion" value="${p.observacion ?? ''}">
                        </td>
                    </tr>
                `;
            });

            $("#tablaProductosSolicitud").html(html);
            $("#idtraslado_solicitud").val(idtraslado);

            // Obtener sucursal solicitante
            $.post(
                "controladores/traslado.php?op=obtenerSucursalOrigen",
                { idtraslado: idtraslado },
                function (res) {
                    $("#sucursal_origen_solicitud").val(res.origen || '');
                },
                "json"
            );

            // Mostrar modal
            if (soloLectura === true || soloLectura === "true") {
                // Cambia el título
                $("#tituloSolicitudLabel").html(`
                    <i class="fa fa-eye"></i> <b>Detalle de Solicitud #${idtraslado}</b>
                `);

                // Oculta botones de acción
                $("#modalAprobarSolicitud .btn-success").hide();
                $("#modalAprobarSolicitud .btn-guardar").hide();

                // Desactiva inputs
                $("#modalAprobarSolicitud input, #modalAprobarSolicitud select, #modalAprobarSolicitud textarea")
                    .prop("disabled", true)
                    .addClass("readonly-input"); // estilo visual de lectura

                // Agrega borde azul y fondo más claro para modo lectura
                $("#modalAprobarSolicitud .modal-content")
                    .removeClass("border-success shadow-lg")
                    .addClass("border-info shadow-sm");

                $("#modalAprobarSolicitud .modal-header")
                    .removeClass("bg-success")
                    .addClass("bg-info text-white");

                $("#modalAprobarSolicitud .modal-footer").hide(); // quita los botones de pie si solo lectura

                // Muestra el modal
                $("#modalAprobarSolicitud").modal("show");
            } else {
                // Modo aprobación normal
                $("#tituloSolicitudLabel").html(`
                    <i class="fa fa-check-circle"></i> <b>Aprobar Solicitud #${idtraslado}</b>
                `);

                $("#modalAprobarSolicitud .btn-success").show();
                $("#modalAprobarSolicitud .btn-guardar").show();

                $("#modalAprobarSolicitud input, #modalAprobarSolicitud select, #modalAprobarSolicitud textarea")
                    .prop("disabled", false)
                    .removeClass("readonly-input");

                $("#modalAprobarSolicitud .modal-content")
                    .removeClass("border-info shadow-sm")
                    .addClass("border-success shadow-lg");

                $("#modalAprobarSolicitud .modal-header")
                    .removeClass("bg-info text-white")
                    .addClass("bg-success text-white");

                $("#modalAprobarSolicitud .modal-footer").show();

                $("#modalAprobarSolicitud").modal("show");
            }


        }
    ).fail(function (xhr) {
        console.error("Error AJAX:", xhr.responseText);
        alert("Ocurrió un error en la comunicación con el servidor.");
    });
}

// 🔹 Aprobar o rechazar productos
function aprobarSolicitud() {
    let idtraslado = $("#idtraslado_solicitud").val();
    let productos = [];

    $("#tablaProductosSolicitud tr").each(function() {
        let idproducto = $(this).find(".idProductoHidden").val();
        let nombreProducto = $(this).find(".nombreProducto").text().trim();
        let estado = $(this).find(".estadoProducto").val();
        let cantidad = parseFloat($(this).find(".cantidadProducto").val()) || 0;
        let observacion = $(this).find(".observacion").val().trim();

        if (!idproducto) {
            console.error("ID de producto inválido en la fila:", $(this).html());
            return;
        }

        // Solo productos aceptados o rechazados
        if (!["aceptado", "rechazado"].includes(estado)) return;

        // Validar cantidad > 0 para aceptados
        if (estado === "aceptado" && cantidad <= 0) {
            Swal.fire({
                icon: "warning",
                title: "Cantidad inválida",
                text: "La cantidad del producto '" + nombreProducto + "' debe ser mayor que 0.",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Entendido"
            });
            throw "Cantidad inválida";
        }

        productos.push({ 
            idproducto, 
            nombre: nombreProducto,
            estado, 
            cantidad, 
            observacion 
        });
    });

    if (productos.length === 0) {
        Swal.fire({
            icon: "info",
            title: "Sin productos",
            text: "No hay productos para aprobar o rechazar.",
            confirmButtonColor: "#3085d6"
        });
        return;
    }

    // Confirmación antes de enviar
    Swal.fire({
        title: "¿Aprobar solicitud?",
        text: "Se procesará la solicitud con los cambios realizados.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, aprobar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(
                "controladores/traslado.php?op=aprobarSolicitud", 
                { idtraslado: idtraslado, productos: JSON.stringify(productos) },
                function(resp) {
                    Swal.fire({
                        icon: "success",
                        title: "Solicitud procesada",
                        text: resp,
                        confirmButtonColor: "#3085d6"
                    }).then(() => {
                        $("#modalAprobarSolicitud").modal("hide");
                        tabla.ajax.reload();
                    });
                }
            ).fail(() => {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Ocurrió un problema al procesar la solicitud.",
                    confirmButtonColor: "#d33"
                });
            });
        }
    });
}


// 🔹 Deshabilitar inputs si se rechaza un producto
$("#tablaProductosSolicitud").on("change", ".estadoProducto", function() {
    let estado = $(this).val();
    let row = $(this).closest("tr");
    if (estado === "rechazado") {
        row.find(".cantidadProducto, .observacion").prop("disabled", true);
    } else {
        row.find(".cantidadProducto, .observacion").prop("disabled", false);
    }
});

// 🔹 Ver productos para aprobación
function verProductosAprobacion(idtraslado) {
    $.post("controladores/traslado.php?op=verProductosSolicitud", { idtraslado: idtraslado }, function (resp) {
        try {
            let data = JSON.parse(resp);

            if (data.error) {
                alert(data.error);
                return;
            }

            $("#modalAprobarSolicitud").modal("show");
            $("#tituloSolicitudLabel").text("Aprobar Solicitud #" + idtraslado);
            $("#idtraslado_solicitud").val(idtraslado);

            let html = "";
            data.productos.forEach((p) => {
                html += `
                    <tr>
                        <td>${p.nombre}</td>
                        <td>
                            <input type="hidden" class="idProductoHidden" value="${p.idproducto}">
                            <input type="number" class="form-control cantidadProducto" value="${p.cantidad}" min="1">
                        </td>
                        <td>
                            <select class="form-control estadoProducto">
                                <option value="pendiente" ${p.estado_detalle === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="aceptado" ${p.estado_detalle === 'aceptado' ? 'selected' : ''}>Aceptar</option>
                                <option value="rechazado" ${p.estado_detalle === 'rechazado' ? 'selected' : ''}>Rechazar</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control observacion" value="${p.observacion ?? ''}">
                        </td>
                    </tr>
                `;
            });

            $("#tablaProductosSolicitud").html(html);

        } catch (e) {
            console.error("Respuesta inválida del servidor:", resp);
            alert("Error al obtener los datos de la solicitud.");
        }
    });
}
/*  // Vacía solo el contenido del cuerpo de la tabla de detalle
  $('#tablaDetalleSolicitud tbody').empty();
  $('#tablaDetalle tbody').empty();
  console.log(" Tabla de detalle de solicitud limpiada correctamente");
}
*/
/*function cancelarformT() {
  // Vacía solo el contenido del cuerpo de la tabla de detalle
  $('#tablaDetalle tbody').empty();
   $('#tablaDetalleSolicitud tbody').empty();
  console.log(" Tabla de detalle de solicitud limpiada correctamente");
}*/



function imprimirSolicitud(id) {
    window.open('reportes/exSolicitud.php?id=' + id, '_blank');
}

function imprimirTraslado(id) {
    window.open('reportes/exTraslado.php?id=' + id, '_blank');
}

init();
