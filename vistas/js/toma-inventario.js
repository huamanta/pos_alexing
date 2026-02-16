var tabla;
toastr.options = {
  closeButton: true,
  progressBar: true,
  positionClass: "toast-bottom-right",
  timeOut: "3000",
};
function init() {
  listar();

  $("#myModal").on("submit", function (e) {
    guardaryeditar(e);
  });

  // Ocultar formulario y botón regresar al iniciar
  document.getElementById("formularioregistros").style.display = "none";
  document.getElementById("btnregresar").style.display = "none";

  $("#navInventarioActive").addClass("treeview active");
  $("#navInventario").addClass("treeview menu-open");
  $("#navtoma-inventario").addClass("active");

  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal3", function (r) {
    $("#idsucursal").html(r);
    $("#idsucursal").select2("");

    $("#idsucursal2").html(r);
    $("#idsucursal2").select2("");
  });
}

function nuevo() {
  var sucursal = $("#idsucursal2").val();
  if (sucursal === "Todos") {
    toastr.warning("Seleccione un almacén");
    return;
  }
  $("#idsucursal_save").val(sucursal);
  $("#myModal").modal("show");
}

async function mostrarform(flag, id, sucursal, fecha_cierre) {
  if (flag) {
    document.getElementById("formularioregistros").style.display = "block";
    document.getElementById("listadoregistros").style.display = "none";
    document.getElementById("btnregresar").style.display = "inline-block";
    document.getElementById("btnnuevo").style.display = "none";
    $("#idsucursal").val(sucursal).trigger("change");
    $("#idinventario").val(id);

    // **No limpiar tabla temporal**, en lugar de eso listamos los productos existentes
    listarTemporales();

    // Limpiar lista de productos buscados
    $("#data_productos").html(`<tr>
        <td colspan="4" style="text-align: center;">Lista de productos vacía</td>
    </tr>`);

    // Limpiar inputs de búsqueda
    $("#nombre").val("");
    $("#codigo").val("");
    $("#categoria").val("");

    if (fecha_cierre) {
      disabledInputs();
      $("#message_inventario").html(`
        <div class="alert alert-warning" role="alert">
          El inventario ya fue cerrado el <strong>${fecha_cierre}</strong>
        </div>
      `);
    } else {
      enabledInputs();
      $("#message_inventario").html(``);
    }
  } else {
    cancelarform();
  }
}


function disabledInputs() {
  $("#nombre").attr("readonly", "readonly");
  $("#codigo").attr("readonly", "readonly");
  $("#categoria").attr("readonly", "readonly");
  $("#buscar_producto").attr("disabled", "disabled");
  $("#btncerrarinventario").attr("hidden", "hidden");
}

function enabledInputs() {
  $("#nombre").removeAttr("readonly", "readonly");
  $("#codigo").removeAttr("readonly", "readonly");
  $("#categoria").removeAttr("readonly", "readonly");
  $("#buscar_producto").removeAttr("disabled", "disabled");
  $("#btncerrarinventario").removeAttr("hidden", "hidden");
}

function cancelarform() {
  document.getElementById("formularioregistros").style.display = "none";
  document.getElementById("listadoregistros").style.display = "block";
  document.getElementById("btnregresar").style.display = "none";
  document.getElementById("btnnuevo").style.display = "inline-block";
}

//Función Listar
function listar() {
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
        url: "controladores/inventario.php?op=listar",
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 5, //Paginación
    })
    .DataTable();
}



function guardaryeditar(e) {
  e.preventDefault();
  var formData = new FormData($("#formulario")[0]);
  $.ajax({
    url: "controladores/inventario.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (datos) {
      Swal.fire({
        title: "Inventarios",
        icon: "success",
        text: datos,
      });
      $("#myModal").modal("hide");
      tabla.ajax.reload();
    },
  });
}

$("#buscar_producto").click(function (e) {
  e.preventDefault();
  var nombre = $("#nombre").val() || "";
  var codigo = $("#codigo").val() || "";
  var categoria = $("#categoria").val() || "";

  if (nombre.length === 0 && codigo.length === 0 && categoria.length === 0) {
    toastr.warning("Rellene al menos algún campo de búsqueda");
    $("#data_productos").html(`<tr>
        <td colspan="4" style="text-align: center;">Lista de productos vacia</td>
    </tr>`);
    return;
  }

  $.ajax({
    url: "controladores/inventario.php?op=buscar_producto",
    type: "GET",
    data: { nombre: nombre, codigo: codigo, categoria: categoria },
    beforeSend: function () {
      $("#data_productos").html(`<tr>
          <td colspan="4" style="text-align: center;">Buscando...</td>
      </tr>`);
    },
    success: function (datos) {
      var datos = JSON.parse(datos);
      var html = "";
      if (datos.length === 0) {
        html += `<tr>
          <td colspan="4" style="text-align: center;">Lista de productos vacia</td>
        </tr>`;
      }
      $.each(datos, function (i, item) {
        html += `<tr>
          <td><input type="hidden" name="idproducto[]" value="${item.idproducto}"/>${item.producto}</td>
          <td>${item.codigo}</td>
          <td>${item.unidad_medida}</td>
          <td><input type="text" name="cantidad[]" class="form-control cantidad_input" data-idproducto="${item.idproducto}"></td>
        </tr>`;
      });
      $("#data_productos").html(html);

      function debounce(func, wait) {
        let timeout;
        return function() {
          const context = this, args = arguments;
          clearTimeout(timeout);
          timeout = setTimeout(() => func.apply(context, args), wait);
        };
      }

      $(".cantidad_input").on("input", debounce(function() {
        var cantidad = $(this).val();
        var idproducto = $(this).data("idproducto");

        if (cantidad === "" || isNaN(cantidad) || parseFloat(cantidad) <= 0) return;

        $.ajax({
          url: "controladores/inventario.php?op=insertar_cantidad",
          type: "POST",
          data: { idproducto: idproducto, cantidad: cantidad },
          success: function(res) {
            var res = JSON.parse(res);
            if (!res.status) {
              toastr.error(res.message || "Error al guardar cantidad");
            } else {
              toastr.success("Cantidad actualizada correctamente");
            }
          },
          error: function(err) {
            console.log(err.responseText);
          }
        });
      }, 500));
    },
    error: function (error) {
      console.log(error.responseText);
    },
  });
});


$("#guardar_registros").submit(function (e) {
  e.preventDefault();
  var formData = new FormData($(this)[0]);
  $.ajax({
    url: "controladores/inventario.php?op=guardar_registros",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    beforeSend: function () {
      $("#btn_guardar_products").text("GUARDANDO...");
      $("#btn_guardar_products").attr("disabled", "disabled");
    },
    success: function (data) {
      var data = JSON.parse(data);
      if (!data.status) {
        Swal.fire({
          title: "Error",
          icon: "error",
          text: data.message,
        });
        $("#btn_guardar_products").text("GUARDAR");
        $("#btn_guardar_products").removeAttr("disabled", "disabled");
        return;
      }
      $("#nombre").val("");
      $("#codigo").val("");
      $("#categoria").val("");
      $("#data_productos").html(`<tr>
			<td colspan="4" style="text-align: center;">Lista de productos vacia</td>
		</tr>`);
      Swal.fire({
        title: "Inventarios",
        icon: "success",
        text: data.message,
      });

      $("#btn_guardar_products").text("GUARDAR");
      $("#btn_guardar_products").removeAttr("disabled", "disabled");
    },
  });
});

// Función para “debounce”
function debounce(func, wait) {
  let timeout;
  return function() {
    const context = this, args = arguments;
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(context, args), wait);
  };
}

function guardarProductoAutomaticamente() {
  var formData = new FormData($("#guardar_registros")[0]);

  $.ajax({
    url: "controladores/inventario.php?op=guardar_registros",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    beforeSend: function () {
      $("#btn_guardar_products").text("GUARDANDO...");
      $("#btn_guardar_products").attr("disabled", "disabled");
    },
    success: function (data) {
      var data = JSON.parse(data);
      if (!data.status) {
        toastr.error(data.message || "Error al guardar");
      } else {
        toastr.success("Cantidad guardada correctamente");
      }
      $("#btn_guardar_products").text("GUARDAR");
      $("#btn_guardar_products").removeAttr("disabled");
    },
    error: function (err) {
      console.log(err.responseText);
      $("#btn_guardar_products").text("GUARDAR");
      $("#btn_guardar_products").removeAttr("disabled");
    }
  });
}

$(document).on("input", ".cantidad_input", debounce(function() {
  var cantidad = $(this).val();
  
  // Si está vacío, no hace nada
  if (cantidad === "" || isNaN(cantidad) || parseFloat(cantidad) <= 0) return;

  // Ejecuta la función para guardar automáticamente
  guardarProductoAutomaticamente();

}, 500));


function agregarTemporal(idproducto, cantidad) {
    var idinventario = $("#idinventario").val();
    if(!idinventario) return;

    $.ajax({
        url: "controladores/inventario.php?op=agregar_temporal",
        type: "POST",
        data: { idinventario: idinventario, idproducto: idproducto, cantidad: cantidad },
        success: function(resp) {
            var data = JSON.parse(resp);
            if(data.status) {
                listarTemporales(); // Actualiza la tabla temporal en tiempo real
            } else {
                toastr.error(data.message);
            }
        }
    });
}

// Función debounce
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// Evento input automático para insertar/actualizar/eliminar
$(document).on('input', '.cantidad_input', debounce(function() {
    var $row = $(this).closest('tr');
    var cantidad = $(this).val().trim();
    var idproducto = $row.find('input[name="idproducto[]"]').val();
    var idinventario = $("#idinventario").val();

    if (!idinventario || !idproducto) return;

    if (cantidad === "" || isNaN(cantidad) || parseFloat(cantidad) <= 0) {
        // Eliminar automáticamente
        $.ajax({
            url: "controladores/inventario.php?op=eliminar_temporal",
            type: "POST",
            data: { idinventario: idinventario, idproducto: idproducto },
            success: function(resp) {
                var data = JSON.parse(resp);
                if (data.status) {
                    toastr.info("Producto eliminado del inventario");
                    listarTemporales(); // refresca tabla temporal
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(err) { console.log(err.responseText); }
        });
    } else {
        // Insertar o actualizar automáticamente
        $.ajax({
            url: "controladores/inventario.php?op=guardar_registros",
            type: "POST",
            data: { idinventario: idinventario, idproducto: [idproducto], cantidad: [cantidad] },
            success: function(resp) {
                var data = JSON.parse(resp);
                if (data.status) {
                    listarTemporales(); // refresca tabla temporal
                    toastr.success("Cantidad actualizada");
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(err) { console.log(err.responseText); }
        });
    }
}, 500));


// Función para listar temporales
/*function listarTemporales() {
    var idinventario = $("#idinventario").val();
    if(!idinventario) return;

    $.getJSON("controladores/inventario.php?op=listar_temporales&idinventario=" + idinventario, function(data) {
        var html = '';
        if(data.length === 0) {
            html = `<tr><td colspan="5" style="text-align:center;">No hay productos agregados</td></tr>`;
        } else {
            $.each(data, function(i, item){
                html += `<tr>
                    <td>${item.nombre}</td>
                    <td>${item.codigo}</td>
                    <td>${item.unidad}</td>
                    <td>${item.cantidad}</td>
                    <td>${item.stock}</td>
                </tr>`;
            });
        }
        $("#tabla_seleccionados tbody").html(html);
    });
}
*/


function editar(id, observacion_apertura) {
  $("#myModal").modal("show");
  $("#idinventario_edit").val(id);
  $("#observacion_apertura").val(observacion_apertura);
}

function cerrarInventario() {
  $("#idinventario_cierre").val($("#idinventario").val());
  $("#myModalCierre").modal("show");
}

$("#formulario_cierre").submit(function (e) {
  e.preventDefault();

  var formData = new FormData($("#formulario_cierre")[0]);
  $.ajax({
    url: "controladores/inventario.php?op=cerrar_inventario",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (datos) {
      Swal.fire({
        title: "Inventarios",
        icon: "success",
        text: datos,
      });
      $("#myModalCierre").modal("hide");
      cancelarform();
      tabla.ajax.reload();
    },
  });
});

function ver(id) {
  $.post("controladores/inventario.php?op=ver", { id: id }, function (data) {
    try {
      data = JSON.parse(data);
    } catch (e) {
      toastr.error("Error al procesar los datos del inventario");
      return;
    }

    if (!data) {
      toastr.error("No se encontró información del inventario");
      return;
    }

    // Llenamos los campos del modal
    $("#ver_fecha_apertura").text(data.fecha_apertura);
    $("#ver_obs_apertura").text(data.observacion_apertura);
    $("#ver_fecha_cierre").text(data.fecha_cierre ? data.fecha_cierre : "Aún abierto");
    $("#ver_obs_cierre").text(data.observacion_cierre ? data.observacion_cierre : "Sin observaciones");

    // Reiniciar / Inicializar DataTable
    if (typeof tablaVerProductos === 'undefined') {
      tablaVerProductos = $("#tablaVerProductos").DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50],
        language: {
          url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        dom: 'Bfrtip',
        buttons: [
          { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm' },
          { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-danger btn-sm' },
          { extend: 'print', text: '<i class="fas fa-print"></i> Imprimir', className: 'btn btn-primary btn-sm' }
        ]
      });
    } else {
      tablaVerProductos.clear();
    }

    // Agregar filas
    if (data.productos && data.productos.length > 0) {
      data.productos.forEach((p, i) => {
        tablaVerProductos.row.add([
          i + 1,
          p.producto,
          p.unidad_medida,
          p.cantidad,
          p.cantidad_real,
          p.diferencia
        ]);
      });
    } else {
      tablaVerProductos.row.add([
        "", "No hay productos registrados", "", "", "", ""
      ]);
    }

    tablaVerProductos.draw();

    // Abrir modal y ajustar DataTable al mostrarlo
    $("#modalVerInventario").modal("show");

  }).fail(function () {
    toastr.error("Error al conectarse con el servidor");
  });
}

// Ajuste para que DataTable se redibuje correctamente dentro del modal
$(document).on('shown.bs.modal', '#modalVerInventario', function () {
  if (typeof tablaVerProductos !== 'undefined') {
    tablaVerProductos.columns.adjust().responsive.recalc();
  }
});


let tablaVerProductos;

$(document).ready(function () {
  tablaVerProductos = $("#tablaVerProductos").DataTable({
    responsive: true,
    autoWidth: false,
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
    },
    dom: 'Bfrtip',
    buttons: [
      { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success btn-sm' },
      { extend: 'pdfHtml5', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-danger btn-sm' },
      { extend: 'print', text: '<i class="fas fa-print"></i> Imprimir', className: 'btn btn-primary btn-sm' }
    ]
  });
});
//********************************//////

// --------------------
// Debounce
// --------------------
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// --------------------
// Arreglo de productos eliminados para deshacer
// --------------------
let eliminados = [];

// --------------------
// Evento input en búsqueda / cantidad de productos
// --------------------
$(document).on('input', '.cantidad_input', debounce(function() {
    var $row = $(this).closest('tr');
    var cantidad = $(this).val().trim();
    var idproducto = $row.find('input[name="idproducto[]"]').val();
    var stock = parseFloat($row.data('stock'));
    var idinventario = $("#idinventario").val();

    if (!idinventario || !idproducto) return;

    // Resaltar stock
    resaltarStock($row, cantidad, stock);

    if (cantidad === "" || isNaN(cantidad) || parseFloat(cantidad) <= 0) {
        // Guardamos para deshacer
        eliminados.push({idproducto: idproducto, rowHtml: $row.prop('outerHTML')});

        // Eliminamos automáticamente
        $.ajax({
            url: "controladores/inventario.php?op=eliminar_temporal",
            type: "POST",
            data: { idinventario: idinventario, idproducto: idproducto },
            success: function(resp) {
                var data = JSON.parse(resp);
                if(data.status){
                    toastr.info("Producto eliminado");
                    $row.remove();
                    listarTemporales();
                    mostrarBotonDeshacer();
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(err){ console.log(err.responseText); }
        });

    } else {
        // Insertar o actualizar automáticamente
        $.ajax({
            url: "controladores/inventario.php?op=guardar_registros",
            type: "POST",
            data: { idinventario: idinventario, idproducto: [idproducto], cantidad: [cantidad] },
            success: function(resp) {
                var data = JSON.parse(resp);
                if(data.status){
                    listarTemporales();
                    toastr.success("Cantidad actualizada");
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(err){ console.log(err.responseText); }
        });
    }
}, 500));

// --------------------
// Resaltar stock
// --------------------
function resaltarStock($row, cantidad, stock){
    $row.removeClass("bg-danger bg-warning");

    if(stock == 0){
        $row.addClass("bg-danger"); // rojo si no hay stock
    } else if(parseFloat(cantidad) > stock){
        $row.addClass("bg-warning"); // amarillo si supera stock
    }
}

// --------------------
// Botón deshacer eliminación
// --------------------
function mostrarBotonDeshacer() {
    if(eliminados.length > 0) $("#btnDeshacer").show();
    else $("#btnDeshacer").hide();
}

$("#btnDeshacer").click(function(){
    if(eliminados.length == 0) return;
    let ultimo = eliminados.pop();
    $("#data_productos").append(ultimo.rowHtml);
    listarTemporales();
    mostrarBotonDeshacer();
});

// --------------------
// Listar productos temporales
// --------------------
function listarTemporales() {
    var idinventario = $("#idinventario").val();
    if(!idinventario) return;

    $.getJSON("controladores/inventario.php?op=listar_temporales&idinventario=" + idinventario, function(data) {
        var html = '';
        if(data.length === 0){
            html = `<tr><td colspan="5" style="text-align:center;">No hay productos agregados</td></tr>`;
        } else {
            $.each(data, function(i, item){
                html += `<tr data-idproducto="${item.idproducto}" data-stock="${item.stock}">
                    <td>${item.nombre}</td>
                    <td>${item.codigo}</td>
                    <td>${item.unidad}</td>
                    <td><input type="text" class="form-control cantidad_temp" value="${item.cantidad}"></td>
                    <td>${item.stock}</td>
                </tr>`;
            });
        }
        $("#tabla_seleccionados tbody").html(html);
    });
}

// --------------------
// Editar directamente desde tabla temporal
// --------------------
$(document).on('input', '#tabla_seleccionados input.cantidad_temp', debounce(function(){
    var $row = $(this).closest('tr');
    var cantidad = $(this).val().trim();
    var idproducto = $row.data('idproducto');
    var stock = parseFloat($row.data('stock'));
    var idinventario = $("#idinventario").val();

    resaltarStock($row, cantidad, stock);

    if(cantidad === "" || isNaN(cantidad) || parseFloat(cantidad) <= 0) return;

    $.ajax({
        url: "controladores/inventario.php?op=guardar_registros",
        type: "POST",
        data: { idinventario: idinventario, idproducto: [idproducto], cantidad: [cantidad] },
        success: function(resp){
            var data = JSON.parse(resp);
            if(data.status){
                listarTemporales();
                toastr.success("Cantidad actualizada");
            } else {
                toastr.error(data.message);
            }
        },
        error: function(err){ console.log(err.responseText); }
    });
}));

function actualizarCantidad(idinventario, idproducto, cantidad, $inputActual){
    $.ajax({
        url: "controladores/inventario.php?op=guardar_registros",
        type: "POST",
        data: { idinventario: idinventario, idproducto: [idproducto], cantidad: [cantidad] },
        success: function(resp){
            var data = JSON.parse(resp);
            if(data.status){
                // Sincronizamos solo los otros inputs, sin tocar el que estamos escribiendo
                $(`input[data-idproducto="${idproducto}"]`).not($inputActual).val(cantidad);
                $(`#tabla_seleccionados tr[data-idproducto="${idproducto}"] input.cantidad_temp`).not($inputActual).val(cantidad);
            }
        }
    });
}

// Input de lista de productos
$(document).on('input', '.cantidad_input', debounce(function(){
    var $input = $(this);
    var cantidad = $input.val().trim();
    var idproducto = $input.data('idproducto');
    var idinventario = $("#idinventario").val();
    if(cantidad === "" || isNaN(cantidad) || parseFloat(cantidad) <= 0) return;

    actualizarCantidad(idinventario, idproducto, cantidad, $input);
}));

// Input de tabla temporal
$(document).on('input', '.cantidad_temp', debounce(function(){
    var $input = $(this);
    var cantidad = $input.val().trim();
    var idproducto = $input.closest('tr').data('idproducto');
    var idinventario = $("#idinventario").val();
    if(cantidad === "" || isNaN(cantidad) || parseFloat(cantidad) <= 0) return;

    actualizarCantidad(idinventario, idproducto, cantidad, $input);
}));

init();
