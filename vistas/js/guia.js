var tabla;
var tablaDetalles; // New global variable for tabla_detalles
var cont = 0;
var detalles = 0;

function init() {
  $("#body").addClass("sidebar-collapse sidebar-mini");

  // Initialize tabla_detalles as DataTable BEFORE any call to limpiar()
  tablaDetalles = $('#tabla_detalles').DataTable({
    "aProcessing": true,
    "aServerSide": false, // This is an input table, not loaded via AJAX initially
    "processing": true,
    "language": {
      "processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
    },
    "responsive": true,
    "lengthChange": false,
    "autoWidth": false,
    "searching": false,   // Disable search for this input table
    "paging": false,      // Disable pagination for this input table
    "info": false,        // Disable table information display
    "ordering": false,    // Disable ordering
    "buttons": [],        // No buttons for an input table
    "columnDefs": [ // Define column properties
        { "targets": 0, "orderable": false }, // Item - not orderable
        { "targets": 1, "orderable": false }, // Código
        { "targets": 2, "orderable": false }, // Artículo
        { "targets": 3, "orderable": false }, // Cantidad
        { "targets": 4, "orderable": false }, // Unidad
        { "targets": 5, "orderable": false }, // Peso
        { "targets": 6, "orderable": false }, // Bultos
        { "targets": 7, "orderable": false }, // Lotes
        { "targets": 8, "orderable": false }  // Quitar
    ],
    "bDestroy": true,
    "iDisplayLength": 100 // Show all entries, no pagination
  });

  mostrarform(false); // Now limpiar() will find tablaDetalles initialized
  listar();

  $("#formulario").on("submit", function (e) {
    guardaryeditar(e);
  });

  $.post("controladores/guia.php?op=selectCliente", function (r) {
    $("#idcliente").html(r);
    $('#idcliente').select2();
  });

  $.post("controladores/guia.php?op=selectTransportista", function (r) {
    $("#idtransportista").html(r);
    $('#idtransportista').select2();
  });

  $.post("controladores/guia.php?op=selectPersonal", function (r) {
    $("#idtrabajador").html(r);
    $('#idtrabajador').select2();
  });

  $.post("controladores/guia.php?op=selectMotivo", function (r) {
    $("#idmotivo").html(r);
    $('#idmotivo').select2();
  });

  $.post("controladores/venta.php?op=selectSucursal3", function (r) {
    $("#idsucursal2").html(r);
    $("#idsucursal2").select2();
  });

  $.post("controladores/guia.php?op=selectComprobante", function (r) {
    $("#idcomprobante").html(r);
    $('#idcomprobante').select2();
  });

  $.post("controladores/guia.php?op=getSeries", { idsucursal: $("#idsucursal").val() }, function (r) {
    console.log("Response from getSeries:", r);
    $("#serie_comprobante").html(r);
    $('#serie_comprobante').select2();
    get_numeracion();
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

  $("#serie_comprobante").change(function () {
    get_numeracion();
  });

  $("#fecha_inicio, #fecha_fin, #estado, #idsucursal2").change(function () {
    listar();
  });

  $("#idcomprobante").change(function () {
    var idventa = $(this).val();
    if (idventa) {
      $.post("controladores/guia.php?op=getComprobante", { idventa: idventa }, function (data) {
        data = JSON.parse(data);
        $("#idcliente").val(data.cabecera.idcliente);
        $("#idcliente").select2();
        $("#punto_llegada").val(data.cabecera.punto_llegada);
        $("#factura_ref").val(data.cabecera.serie_comprobante + '-' + data.cabecera.num_comprobante);
        $("#fecha_factura_ref").val(data.cabecera.fecha);

        $("#tabla_detalles tbody").html("");
        cont = 0;
        detalles = 0;
        data.detalles.forEach(function (detalle) {
          var fila = '<tr class="filas" id="fila' + cont + '">' +
            '<td>' + (cont + 1) + '</td>' +
            '<td><input type="hidden" name="idproducto[]" value="' + detalle.idproducto + '">' + detalle.codigo + '</td>' +
            '<td>' + detalle.nombre_producto + '</td>' +
            '<td><input class="form-control" type="number" name="cantidad[]" value="' + detalle.cantidad + '"></td>' +
            '<td>' + detalle.unidad + '</td>' +
            '<td><input class="form-control" type="number" name="peso_det[]" value="' + detalle.peso + '"></td>' +
            '<td><input class="form-control" type="number" name="bultos[]" value="' + detalle.bultos + '"></td>' +
            '<td><input class="form-control" type="text" name="lotes[]" value="' + detalle.lotes + '"></td>' +
            '<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ')"><i class="fa fa-trash"></i></button></td>' +
            '</tr>';
          cont++;
          detalles++;
          $('#tabla_detalles').append(fila);
        });
      });
    }
  });

  $("#navPos").addClass("treeview active");
  $("#navPos").addClass("menu-open");
  $("#navGuia").addClass("active");
}

function get_numeracion() {
  let idsucursal = $("#idsucursal").val();
  let serie = $("#serie_comprobante").val();
  $.post("controladores/guia.php?op=get_numeracion", { idsucursal: idsucursal, serie: serie }, function (data, status) {
    data = JSON.parse(data);
    $("#num_comprobante").val(data.numero);
  });
}

function mostrarform(flag) {
  limpiar();
  if (flag) {
    $("#listadoregistros").hide();
    $("#formularioregistros").show();
    $("#btnGuardar").prop("disabled", false);
    $("#btnagregar").show();
    get_numeracion();
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

    success: function (datos) {
      Swal.fire({
        icon: 'success',
        title: datos,
        showConfirmButton: false,
        timer: 1500
      });
      mostrarform(false);
      listar();
    }
  });
  limpiar();
}

function mostrar(idguia) {
  $.post("controladores/guia.php?op=mostrar", { idguia: idguia }, function (data, status) {
    data = JSON.parse(data);
    mostrarform(true);

    $("#idguia").val(data.idguia);
    $("#idsucursal").val(data.idsucursal);
    $("#idcliente").val(data.idcliente);
    $("#idcliente").select2();
    $("#serie_comprobante").val(data.serie_comprobante);
    $("#serie_comprobante").select2();
    $("#num_comprobante").val(data.num_comprobante);
    $("#fecha_emision").val(data.fecha_emision);
    $("#fecha_traslado").val(data.fecha_traslado);
    $("#factura_ref").val(data.factura_ref);
    $("#fecha_factura_ref").val(data.fecha_factura_ref);
    $("#tipo_transporte").val(data.tipo_transporte);
    $("#tipo_transporte").select2();
    $("#idtransportista").val(data.idtransportista);
    $("#idtransportista").select2();
    $("#peso").val(data.peso);
    $("#punto_partida").val(data.punto_partida);
    $("#ubigeo_partida").val(data.ubigeo_partida);
    $("#punto_llegada").val(data.punto_llegada);
    $("#ubigeo_llegada").val(data.ubigeo_llegada);
    $("#atencion").val(data.atencion);
    $("#referencia").val(data.referencia);
    $("#idtrabajador").val(data.idtrabajador);
    $("#idtrabajador").select2();
    $("#idmotivo").val(data.idmotivo);
    $("#idmotivo").select2();
    $("#ord_compra").val(data.ord_compra);
    $("#ord_pedido").val(data.ord_pedido);
    $("#observacion").val(data.observacion);

    // Detalle
    $.post("controladores/guia.php?op=listarDetalles", { idguia: idguia }, function (r) {
        r = JSON.parse(r);
        tablaDetalles.clear().draw(false); // Clear existing rows from DataTable
        cont = 0;
        detalles = 0;
        r.forEach(function (detalle) {
            var rowNode = tablaDetalles.row.add([
              (cont + 1), // Item
              '<input type="hidden" name="idproducto[]" value="' + detalle.idproducto + '">' +
              '<input type="hidden" name="codigo[]" value="' + detalle.codigo + '"><div class="text-center">' + detalle.codigo + '</div>', // Código
              '<input type="hidden" name="nombre_producto[]" value="' + detalle.nombre_producto + '"><div class="text-left">' + detalle.nombre_producto + '</div>', // Artículo
              '<input class="form-control" type="number" name="cantidad[]" value="' + detalle.cantidad + '">', // Cantidad
              '<input type="hidden" name="unidad[]" value="' + detalle.unidad + '"><div class="text-center">' + detalle.unidad + '</div>', // Unidad
              '<input class="form-control" type="number" name="peso_det[]" value="' + detalle.peso + '">', // Peso
              '<input class="form-control" type="number" name="bultos[]" value="' + detalle.bultos + '">', // Bultos
              '<input class="form-control" type="text" name="lotes[]" value="' + detalle.lotes + '">', // Lotes
              '<button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ')"><i class="fa fa-trash"></i></button>' // Quitar
            ]).draw(false).node(); // Add the row and get the DOM node

            $(rowNode).attr('id', 'fila' + cont); // Assign a unique ID to the row's DOM node

            cont++;
            detalles++;
        });
        tablaDetalles.draw(); // Redraw the table after all rows are added
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

function abrirModalProductos() {
  $('#modalProductos').modal('show');
  let idsucursal = $("#idsucursal").val();
  console.log("idsucursal for listarArticulos:", idsucursal);
  tabla = $('#tabla_productos_modal').dataTable({
    "aProcessing": true,
    "aServerSide": true,
    "processing": true,
    "language": {
      "processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
    },
    "responsive": true, "lengthChange": false, "autoWidth": false,
    dom: '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>><"col-sm-12 col-md-4"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    lengthMenu: [
      [5, 10, 25, 50, 100, -1],
      ['5 filas', '10 filas', '25 filas', '50 filas', '100 filas', 'Mostrar todo']
    ],
    buttons: ['pageLength',
      {
        extend: 'excelHtml5',
        text: "<i class='fas fa-file-csv'></i>",
        titleAttr: 'Exportar a Excel',
      },
      {
        extend: 'pdf',
        text: "<i class='fas fa-file-pdf'></i>",
        titleAttr: 'Exportar a PDF',
      },
      {
        extend: 'colvis',
        text: "<i class='fas fa-bars'></i>",
        titleAttr: '',
      }],
    "ajax": {
      url: 'controladores/guia.php?op=listarArticulos',
      data: { idsucursal: idsucursal },
      type: "post",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText);
      }
    },
    "bDestroy": true,
    "iDisplayLength": 5,
    "order": [[0, "desc"]]
  }).DataTable();
}

function agregarDetalle(idproducto, codigo, nombre, unidad) {
  var cantidad = 1;
  var peso = 1;
  var bultos = 1;
  var lotes = "";

  if (idproducto != "") {
    var rowNode = tablaDetalles.row.add([
      (cont + 1), // Item
      '<input type="hidden" name="idproducto[]" value="' + idproducto + '">' +
      '<input type="hidden" name="codigo[]" value="' + codigo + '"><div class="text-center">' + codigo + '</div>', // Código
      '<input type="hidden" name="nombre_producto[]" value="' + nombre + '"><div class="text-left">' + nombre + '</div>', // Artículo
      '<input class="form-control" type="number" name="cantidad[]" value="' + cantidad + '">', // Cantidad
      '<input type="hidden" name="unidad[]" value="' + unidad + '"><div class="text-center">' + unidad + '</div>', // Unidad
      '<input class="form-control" type="number" name="peso_det[]" value="' + peso + '">', // Peso
      '<input class="form-control" type="number" name="bultos[]" value="' + bultos + '">', // Bultos
      '<input class="form-control" type="text" name="lotes[]" value="' + lotes + '">', // Lotes
      '<button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ')"><i class="fa fa-trash"></i></button>' // Quitar
    ]).draw(false).node(); // Add the row and get the DOM node

    // Assign a unique ID to the row's DOM node
    $(rowNode).attr('id', 'fila' + cont);

    cont++;
    detalles++;
  } else {
    alert("Error al ingresar el detalle, revisar los datos del artículo");
  }
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
  
tablaDetalles.clear().draw(); // Clear all rows from the DataTable
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
