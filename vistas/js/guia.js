var tabla;
var tablaDetalles; // New global variable for tabla_detalles
var cont = 0;
var detalles = 0;
let listarArticulos = null;

$("#navPos").addClass("treeview active");
$("#navPos").addClass("menu-open");
$("#navGuia").addClass("active");

function init() {
  $("#body").addClass("sidebar-collapse sidebar-mini");


  mostrarform(false); // Now limpiar() will find tablaDetalles initialized
  listar();

  $("#formulario").on("submit", function (e) {
    guardaryeditar(e);
  });

  // Aplicar estado inicial
  validarMotivoOtro();
}

$.post("controladores/venta.php?op=selectSucursal3", function (r) {
  $("#idsucursal2").html(r);
  $("#idsucursal2").select2();
});

// Cargar departamentos
$.post("controladores/guia.php?op=selectDepartamento", function (r) {
  $("#departamento_partida").html(r);
  $('#departamento_partida').select2();
  $("#departamento_llegada").html(r);
  $('#departamento_llegada').select2();
});

// Cargar provincias al seleccionar departamento
$("#departamento_partida").change(function () {
  $("#provincia_partida").html('<option value="">Seleccione</option>');
  $("#distrito_partida").html('<option value="">Seleccione</option>');
  $.post("controladores/guia.php?op=selectProvincia", { iddepartamento: $(this).val() }, function (r) {
    $("#provincia_partida").html(r);
    $('#provincia_partida').select2();
  });
});

$("#departamento_llegada").change(function () {
  $("#provincia_llegada").html('<option value="">Seleccione</option>');
  $("#distrito_llegada").html('<option value="">Seleccione</option>');
  $.post("controladores/guia.php?op=selectProvincia", { iddepartamento: $(this).val() }, function (r) {
    $("#provincia_llegada").html(r);
    $('#provincia_llegada').select2();
  });
});

// Cargar distritos al seleccionar provincia
$("#provincia_partida").change(function () {
  $("#distrito_partida").html('<option value="">Seleccione</option>');
  $.post("controladores/guia.php?op=selectDistrito", { idprovincia: $(this).val() }, function (r) {
    $("#distrito_partida").html(r);
    $('#distrito_partida').select2();
  });
});

$("#provincia_llegada").change(function () {
  $("#distrito_llegada").html('<option value="">Seleccione</option>');
  $.post("controladores/guia.php?op=selectDistrito", { idprovincia: $(this).val() }, function (r) {
    $("#distrito_llegada").html(r);
    $('#distrito_llegada').select2();
  });
});

// Asignar ubigeo y poblar punto_partida
$("#distrito_partida").change(function () {
  $("#ubigeo_partida").val($(this).val());
  var departamento_nombre = $("#departamento_partida option:selected").text();
  var provincia_nombre = $("#provincia_partida option:selected").text();
  var distrito_nombre = $("#distrito_partida option:selected").text();
  if (departamento_nombre && provincia_nombre && distrito_nombre) {
    $("#punto_partida").val(departamento_nombre + " - " + provincia_nombre + " - " + distrito_nombre);
  } else {
    $("#punto_partida").val("");
  }
});

// Asignar ubigeo y poblar punto_llegada
$("#distrito_llegada").change(function () {
  $("#ubigeo_llegada").val($(this).val());
  var departamento_nombre = $("#departamento_llegada option:selected").text();
  var provincia_nombre = $("#provincia_llegada option:selected").text();
  var distrito_nombre = $("#distrito_llegada option:selected").text();
  if (departamento_nombre && provincia_nombre && distrito_nombre) {
    $("#punto_llegada").val(departamento_nombre + " - " + provincia_nombre + " - " + distrito_nombre);
  } else {
    $("#punto_llegada").val("");
  }
});


$("#fecha_inicio, #fecha_fin, #estado, #idsucursal2").change(function () {
  listar();
});

$("#idcomprobante").change(function () {
  const idventa = $(this).val();

  if (!idventa) return;

  $.get(
    "controladores/guia.php?op=getComprobante",
    { idventa: idventa },
    function (response) {
      console.log(response);

      const data = response;

      // ==============================
      // DATOS DEL CLIENTE
      // ==============================
      const idcliente = data.cabecera.idcliente;
      const nombreCliente = data.cabecera.cliente;
      const documentoCliente = data.cabecera.num_documento || "";

      const option = new Option(
        `${nombreCliente}${documentoCliente ? " - " + documentoCliente : ""}`,
        idcliente,
        true,
        true
      );

      $("#idcliente")
        .append(option)
        .trigger("change")
        .select2();

      // ==============================
      // DATOS DEL COMPROBANTE
      // ==============================
      $("#punto_llegada").val(data.cabecera.punto_llegada);

      $("#factura_ref").val(
        `${data.cabecera.serie_comprobante}-${data.cabecera.num_comprobante}`
      );

      $("#fecha_factura_ref").val(
        data.cabecera.fecha_hora.substring(0, 10)
      );

      // ==============================
      // LIMPIAR DETALLES
      // ==============================
      $("#tabla_detalles tbody").empty();

      cont = 0;
      detalles = 0;

      // ==============================
      // RECORRER PRODUCTOS
      // ==============================
      data.detalles.forEach(function (detalle) {

        agregarDetalle({
          idproducto: detalle.idproducto,
          idproducto_configuracion: detalle.idproducto_configuracion,
          idserie: detalle.idserie,

          codigo: detalle.codigo,
          nombre_producto: detalle.nombre_producto,

          cantidad: detalle.cantidad,
          unidad: detalle.unidad,

          peso: detalle.peso,
          bultos: detalle.bultos,

          lotes: detalle.lotes || []
        });

      });

    },
    "json"
  );
});

function listarMotivos() {
  $.post("controladores/guia.php?op=selectMotivo", function (response) {

    const motivos = response;

    const $select = $("#idmotivo");

    $select.empty();
    $select.append('<option value="">Seleccione motivo</option>');

    motivos.forEach(motivo => {
      $select.append(
        new Option(motivo.nombre, motivo.id)
      );
    });

    $select.select2({
      placeholder: "Seleccione motivo",
      width: "100%"
    });

  });
}

function validarMotivoOtro() {
  if ($("#idmotivo").val() === "13") {
    $("#motivo_traslado_otro").show().focus();
  } else {
    $("#motivo_traslado_otro").hide().val("");
  }
}

$("#idmotivo").on("change", validarMotivoOtro);

$("#idcliente").select2({
  placeholder: "Buscar cliente...",
  allowClear: true,
  minimumInputLength: 2,
  ajax: {
    url: "controladores/guia.php?op=selectCliente",
    type: "GET",
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
            id: item.idpersona,
            text: item.nombre + " - " + item.num_documento,
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


$("#idcomprobante").select2({
  placeholder: "Buscar comprobante...",
  allowClear: true,
  minimumInputLength: 2,
  ajax: {
    url: "controladores/guia.php?op=selectComprobante",
    type: "GET",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        search: params.term,
        page: params.page || 1
      };
    },
    processResults: function (data, params) {
      params.page = params.page || 1;
      return {
        results: data.data.map(function (item) {
          return {
            id: item.idventa,
            text: item.nombre + " " + item.serie_comprobante + " - " + item.num_comprobante,
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

$("#idtransportista").select2({
  placeholder: "Buscar transportista...",
  allowClear: true,
  minimumInputLength: 2,
  ajax: {
    url: "controladores/guia.php?op=selectTransportista",
    type: "GET",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        search: params.term,
        page: params.page || 1
      };
    },
    processResults: function (data, params) {
      params.page = params.page || 1;
      return {
        results: data.data.map(function (item) {
          return {
            id: item.idpersonal,
            text: item.nombre,
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

$("#idtrabajador").select2({
  placeholder: "Buscar trabajador...",
  allowClear: true,
  minimumInputLength: 2,
  ajax: {
    url: "controladores/guia.php?op=selectPersonal",
    type: "GET",
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        search: params.term,
        page: params.page || 1
      };
    },
    processResults: function (data, params) {
      params.page = params.page || 1;
      return {
        results: data.data.map(function (item) {
          return {
            id: item.idpersonal,
            text: item.nombre,
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


function get_numeracion() {
  $.get("controladores/guia.php?op=get_numeracion",
    function (response, status) {
      try {
        const data = response;
        $("#serie_comprobante").val(data.serie);
        $("#num_comprobante").val(data.numero);
      } catch (error) {
        Swal.fire('Guia de remisión', error, 'error');
      }
    }
  ).fail(function (jqXHR, textStatus, errorThrown) {
    Swal.fire('Guia de remisión', jqXHR.responseJSON.message, 'error');
  });
}

function mostrarform(flag) {
  limpiar();
  if (flag) {
    $("#listadoregistros").hide();
    $("#formularioregistros").show();
    $("#btnGuardar").prop("disabled", false);
    $("#btnagregar").show();
    $("#btnNuevo").hide();
    get_numeracion();
    listarMotivos();
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + (month) + "-" + (day);
    $('#fecha_emision').val(today);
    $('#fecha_traslado').val(today);
  } else {
    $("#listadoregistros").show();
    $("#formularioregistros").hide();
    $("#btnagregar").show();
    $("#btnNuevo").show();
  }
}

function cancelarform() {
  limpiar();
  mostrarform(false);
}

function listar() {
  let fecha_inicio = $("#fecha_inicio").val();
  let fecha_fin = $("#fecha_fin").val();
  let estado = $("#estado").val();
  let idsucursal2 = $("#idsucursal2").val();

  tabla = $('#tbllistado').dataTable({
    "aProcessing": true,
    "aServerSide": true,
    "processing": true,
    "responsive": true,
    "lengthChange": false,
    "autoWidth": false,
    "dom": '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>><"col-sm-12 col-md-4"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    "buttons": ["pageLength", "excelHtml5", "pdf", "colvis"],
    "ajax": {
      url: 'controladores/guia.php?op=listar',
      data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin, estado: estado, idsucursal2: idsucursal2 },
      type: "get",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText);
      }
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "desc"]]
  }).DataTable();
}

function guardaryeditar(e) {
  e.preventDefault();
  $("#btnGuardar").prop("disabled", true);
  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "controladores/guia.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      $("#btnGuardar").prop("disabled", false);
      if (!response.success) {
        Swal.fire({
          icon: 'error',
          title: response.message,
          showConfirmButton: false,
          timer: 1500
        });
        return;
      }
      Swal.fire({
        icon: 'success',
        title: response.message,
        showConfirmButton: false,
        timer: 1500
      });
      mostrarform(false);
      listar();
      limpiar();
    },
    error: function (error) {
      $("#btnGuardar").prop("disabled", false);
      Swal.fire({
        icon: 'error',
        title: error.responseJSON.message || 'Error al guardar los datos',
        showConfirmButton: false,
        timer: 1500
      });
    }
  });
}

function mostrar(idguia) {
  $.get("controladores/guia.php?op=mostrar", { idguia: idguia }, function (response, status) {
    const data = response;
    const guia = data.guia;
    mostrarform(true);

    $("#idguia").val(guia.idguia);
    $("#idsucursal").val(guia.idsucursal);
    $("#idcliente").val(guia.idcliente);
    $("#idcliente").select2();
    $("#serie_comprobante").val(guia.serie_comprobante);
    $("#num_comprobante").val(guia.num_comprobante);
    $("#fecha_emision").val(guia.fecha_emision);
    $("#fecha_traslado").val(guia.fecha_traslado);
    $("#factura_ref").val(guia.factura_ref);
    $("#fecha_factura_ref").val(guia.fecha_factura_ref);
    $("#tipo_transporte").val(guia.tipo_transporte);
    $("#tipo_transporte").select2();
    $("#idtransportista").val(guia.idtransportista);
    $("#idtransportista").select2();
    $("#peso").val(guia.peso);
    $("#punto_partida").val(guia.punto_partida);
    $("#ubigeo_partida").val(guia.ubigeo_partida);
    $("#punto_llegada").val(guia.punto_llegada);
    $("#ubigeo_llegada").val(guia.ubigeo_llegada);
    $("#atencion").val(guia.atencion);
    $("#referencia").val(guia.referencia);
    $("#idtrabajador").val(guia.idtrabajador);
    $("#idtrabajador").select2();
    $("#idmotivo").val(guia.idmotivo);
    $("#idmotivo").select2();
    $("#ord_compra").val(guia.ord_compra);
    $("#ord_pedido").val(guia.ord_pedido);
    $("#observacion").val(guia.observacion);

    // Detalle
    data.detalles.forEach(function (detalle) {

      agregarDetalle({
        idproducto: detalle.idproducto,
        idproducto_configuracion: detalle.idproducto_configuracion,
        idserie: detalle.idserie,

        codigo: detalle.codigo,
        nombre_producto: detalle.nombre_producto,

        cantidad: detalle.cantidad,
        unidad: detalle.unidad,

        peso: detalle.peso,
        bultos: detalle.bultos,

        lotes: detalle.lotes || []
      });

    });
  });
}

function anular(idguia) {
  Swal.fire({
    title: '¿Está seguro de anular la guía?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, anular!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("controladores/guia.php?op=anular", { idguia: idguia }, function (e) {
        Swal.fire(
          'Anulado!',
          e,
          'success'
        );
        listar();
      });
    }
  })
}

function send_sunat(idguia) {
  Swal.fire({
    title: '¿Está seguro de enviar la guía a SUNAT?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, enviar!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("controladores/guia.php?op=send_sunat", { idguia: idguia }, function (e) {
        Swal.fire(
          'Enviado!',
          e,
          'success'
        );
        listar();
      });
    }
  })
}

function baja_sunat(idguia) {
  Swal.fire({
    title: '¿Está seguro de dar de baja a la guía en SUNAT?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, dar de baja!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("controladores/guia.php?op=baja_sunat", { idguia: idguia }, function (e) {
        Swal.fire(
          'Dado de baja!',
          e,
          'success'
        );
        listar();
      });
    }
  })
}

listarArticulos = new FluentPaginator({
  url: "controladores/guia.php?op=listarArticulos",
  renderTabla: pintarProductos,
  tableBody: "#tbody_productos",
  searchSelector: "#searchProductos",
  limitSelector: "#limitProductos",
  paginationId: "#paginationProductos",
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

    $("#tbody_productos").html(html);
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
                <td>
                <button type="button"
                    class="btn btn-success"
                    onclick='agregarDetalle({
                        idproducto: ${item.idproducto},
                        idproducto_configuracion: ${item.idproducto_configuracion || "null"},
                        idserie: ${item.idserie || "null"},

                        codigo: ${JSON.stringify(item.codigo || "")},
                        nombre_producto: ${JSON.stringify(item.nombre || "")},

                        cantidad: 1,
                        unidad: ${JSON.stringify(item.unidad || "NIU")},

                        peso: 0,
                        bultos: 0,

                        lotes: []
                    })'
                    ${parseFloat(item.stock) <= 0 || item.estado_serie != "DISPONIBLE" ? "disabled" : ""}>
                    <i class="fas fa-shopping-cart"></i>
                </button>
                <td>${item.codigo || ""}</td>
                <td style="text-align:left;">
                    <strong>${item.nombre || ""} ${item.marca || ''} ${item.modelo || ''}</strong> </strong> <span class="badge bg-blue">${item.contenedor} x ${item.cantidad_contenedor}</span><br>
                    <small>
                        <strong>Motor:</strong> ${item.numero_motor || "-"} &nbsp;&nbsp;|&nbsp;&nbsp;
                        <strong>Serie:</strong> ${item.numero_serie || "-"}
                    </small>
                </td>
                <td>${item.stock}</td>
                <td>S/ ${parseFloat(item.precio_venta).toFixed(2)}</td>
                <td>
                    ${item.color || "S/N"}
                </td>

            </tr>
        `;
  });

  $("#tabla_productos_modal").html(html);
}

function abrirModalProductos() {
  $('#modalProductos').modal('show');
  // let idsucursal = $("#idsucursal").val();
  listarArticulos.load();
}

// function agregarDetalle(idproducto, codigo, nombre, unidad) {
//   var cantidad = 1;
//   var peso = 1;
//   var bultos = 1;
//   var lotes = "";

//   if (idproducto != "") {
//     var rowNode = tablaDetalles.row.add([
//       (cont + 1), // Item
//       '<input type="hidden" name="idproducto[]" value="' + idproducto + '">' +
//       '<input type="hidden" name="codigo[]" value="' + codigo + '"><div class="text-center">' + codigo + '</div>', // Código
//       '<input type="hidden" name="nombre_producto[]" value="' + nombre + '"><div class="text-left">' + nombre + '</div>', // Artículo
//       '<input class="form-control" type="number" name="cantidad[]" value="' + cantidad + '">', // Cantidad
//       '<input type="hidden" name="unidad[]" value="' + unidad + '"><div class="text-center">' + unidad + '</div>', // Unidad
//       '<input class="form-control" type="number" name="peso_det[]" value="' + peso + '">', // Peso
//       '<input class="form-control" type="number" name="bultos[]" value="' + bultos + '">', // Bultos
//       '<input class="form-control" type="text" name="lotes[]" value="' + lotes + '">', // Lotes
//       '<button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ')"><i class="fa fa-trash"></i></button>' // Quitar
//     ]).draw(false).node(); // Add the row and get the DOM node

//     // Assign a unique ID to the row's DOM node
//     $(rowNode).attr('id', 'fila' + cont);

//     cont++;
//     detalles++;
//   } else {
//     alert("Error al ingresar el detalle, revisar los datos del artículo");
//   }
// }

function agregarDetalle(detalle) {

  if (!detalle || !detalle.idproducto) {
    alert("Error: datos del producto incompletos.");
    return;
  }

  // ----------------------------------------
  // NORMALIZAR DATOS
  // ----------------------------------------

  const item = {
    idproducto: detalle.idproducto,
    idproducto_configuracion: detalle.idproducto_configuracion || "",
    idserie: detalle.idserie || "",

    codigo: detalle.codigo || "",
    nombre_producto: detalle.nombre_producto || detalle.nombre || "",

    cantidad: parseFloat(detalle.cantidad) || 1,
    unidad: detalle.unidad || "NIU",

    peso: parseFloat(detalle.peso) || 0,
    bultos: parseInt(detalle.bultos) || 0,

    lotes: Array.isArray(detalle.lotes)
      ? detalle.lotes
      : []
  };

  // ----------------------------------------
  // EVITAR PRODUCTO DUPLICADO
  // ----------------------------------------

  let existe = false;

  $("#tabla_detalles tbody tr").each(function () {

    const id = $(this)
      .find('input[name="idproducto[]"]')
      .val();

    if (String(id) === String(item.idproducto)) {
      existe = true;
      return false;
    }
  });

  if (existe) {
    Swal.fire('Guia de remisión', 'El producto ya fue agregado a la guía.', 'warning');
    return;
  }

  // ----------------------------------------
  // HTML DE LOTES
  // ----------------------------------------

  let lotesHtml = "";

  if (item.lotes.length > 0) {

    lotesHtml = item.lotes.map(function (lote) {

      return `
                <span class="badge badge-info mr-1">
                    ${lote.codigo_lote || ""}
                </span>
            `;

    }).join("");

  } else {

    lotesHtml = `
            <span class="text-muted">
                Sin lote
            </span>
        `;
  }

  // ----------------------------------------
  // CREAR FILA
  // ----------------------------------------

  const fila = `
        <tr
            class="filas"
            id="fila${cont}"
        >

            <!-- ITEM -->
            <td class="text-center">
                ${cont + 1}
            </td>

            <!-- PRODUCTO -->
            <td>

                <input
                    type="hidden"
                    name="idproducto[]"
                    value="${item.idproducto}"
                >

                <input
                    type="hidden"
                    name="idproducto_configuracion[]"
                    value="${item.idproducto_configuracion}"
                >

                <input
                    type="hidden"
                    name="idserie[]"
                    value="${item.idserie}"
                >

                <input
                    type="hidden"
                    name="codigo[]"
                    value="${item.codigo}"
                >

                <strong>
                    ${item.codigo}
                </strong>

            </td>

            <!-- DESCRIPCIÓN -->
            <td>

                <input
                    type="hidden"
                    name="nombre_producto[]"
                    value="${item.nombre_producto}"
                >

                ${item.nombre_producto}

            </td>

            <!-- CANTIDAD -->
            <td>

                <input
                    class="form-control"
                    type="number"
                    name="cantidad[]"
                    min="0.001"
                    step="0.001"
                    value="${item.cantidad}"
                >

            </td>

            <!-- UNIDAD -->
            <td class="text-center">

                <input
                    type="hidden"
                    name="unidad[]"
                    value="${item.unidad}"
                >

                ${item.unidad}

            </td>

            <!-- PESO -->
            <td>

                <input
                    class="form-control"
                    type="number"
                    name="peso_det[]"
                    min="0"
                    step="0.001"
                    value="${item.peso}"
                >

            </td>

            <!-- BULTOS -->
            <td>

                <input
                    class="form-control"
                    type="number"
                    name="bultos[]"
                    min="0"
                    step="1"
                    value="${item.bultos}"
                >

            </td>

            <!-- LOTES -->
            <td>

                <div class="lotes-container">
                    ${lotesHtml}
                </div>

                <input
                    type="hidden"
                    name="lotes[]"
                    value='${JSON.stringify(item.lotes)}'
                >

            </td>

            <!-- ACCIONES -->
            <td class="text-center">

                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    onclick="eliminarDetalle(${cont})"
                >
                    <i class="fa fa-trash"></i>
                </button>

            </td>

        </tr>
    `;

  $("#tabla_detalles tbody").append(fila);

  cont++;
  detalles++;
}
function eliminarDetalle(indice) {
  tablaDetalles.row($('#fila' + indice)).remove().draw(false);
  detalles--;
}

function limpiar() {
  $("#idguia").val("");
  $("#idcliente").val("");
  $("#serie_comprobante").val("");
  $("#num_comprobante").val("");
  $("#fecha_emision").val("");
  $("#fecha_traslado").val("");
  $("#factura_ref").val("");
  $("#fecha_factura_ref").val("");
  $("#idtransportista").val("");
  $("#peso").val("");
  $("#punto_partida").val("");
  $("#ubigeo_partida").val("");
  $("#punto_llegada").val("");
  $("#ubigeo_llegada").val("");
  $("#atencion").val("");
  $("#referencia").val("");
  $("#idtrabajador").val("");
  $("#idmotivo").val("");
  $("#ord_compra").val("");
  $("#ord_pedido").val("");
  $("#observacion").val("");

  cont = 0; // Reset counter
  detalles = 0; // Reset details count

  $("#departamento_partida").val("");
  $("#provincia_partida").val("");
  $("#distrito_partida").val("");
  $("#departamento_llegada").val("");
  $("#provincia_llegada").val("");
  $("#distrito_llegada").val("");
  $("#punto_partida").val("");
  $("#punto_llegada").val("");
}

init();
