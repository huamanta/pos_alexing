var tabla;
toastr.options = {
  closeButton: true,
  progressBar: true,
  positionClass: "toast-bottom-right",
  timeOut: "3000",
};

function init() {
  $("#navInventarioActive").addClass("treeview active");
  $("#navInventario").addClass("treeview menu-open");
  $("#navajuste-inventario").addClass("active");

  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal3", function (r) {
    $("#idsucursal2").html(r);
    $("#idsucursal2").select2("");
  });

  //Cargamos los items al select categoria
  $.post("controladores/producto.php?op=selectCategoria", function (r) {
    $("#idcategoria").html(r);
    $("#idcategoria").select2("");
  });

  $.post("controladores/inventario.php?op=listar_inventarios", function (r) {
    $("#idinventario").html(r);
    $("#idinventario").select2("");
  });
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
        url: "",
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

// 1. Declarar la variable global
var tabla_productos = $("#tbllistado").DataTable({
  aProcessing: true,
  aServerSide: true,
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
    },
    {
      extend: "pdf",
      text: "<i class='fas fa-file-pdf'></i>",
      titleAttr: "Exportar a PDF",
    },
    {
      extend: "colvis",
      text: "<i class='fas fa-bars'></i>",
      titleAttr: "Mostrar columnas",
    },
  ],
  createdRow: function (row, data, dataIndex) {
    let diferencia = parseFloat(data[6]); // la diferencia está en la columna 6
    $(row).attr("data-dif", diferencia);  // 👈 aquí guardamos la diferencia en el <tr>

    if (diferencia > 0) {
      $(row).css("background-color", "#d4edda"); // verde claro
    } else if (diferencia < 0) {
      $(row).css("background-color", "#f8d7da"); // rojo claro
    }
  },
  ajax: {
    url: "controladores/inventario.php?op=buscar_productos_inventario",
    type: "GET",
    data: function (d) {
      d.idsucursal = $("#idsucursal2").val();
      d.idinventario = $("#idinventario").val();
      d.idcategoria = $("#idcategoria").val();
      d.tipo_ajuste = $("#tipo_ajuste").val();
    },
    error: function (xhr) {
      console.error("Error en DataTable:", xhr.responseText);
    },
  },
  bDestroy: true,
  iDisplayLength: 10,
});

// 2. Recarga con los filtros al hacer clic
$("#btn_buscar_products").click(function () {
  var idsucursal = $("#idsucursal2").val();
  var idinventario = $("#idinventario").val();

  if (idsucursal === "Todos") {
    toastr.warning("Seleccione un almacén");
    return;
  }
  if (!idinventario) {
    toastr.warning("Seleccione un inventario");
    return;
  }

  $("#btn_buscar_products").html("BUSCANDO...");
  // ✅ Recargar tabla con los nuevos filtros
  tabla_productos.ajax.reload();
  $("#btn_buscar_products").html("BUSCAR");
});

function buscarProductos(){
  
}

$("#btn-ajustar-inventario").click(function (e) {
  e.preventDefault();
  var idsucursal = $("#idsucursal2").val();
  var idinventario = $("#idinventario").val();
  if (idsucursal === "Todos") {
    toastr.warning("Seleccione un almacén");
    return;
  }
  if (!idinventario) {
    toastr.warning("Seleccione un inventario");
    return;
  }

  var tipo_ajuste = $("#tipo_ajuste").val();
  $("#idtipoajuste").val(tipo_ajuste);
  $("#modal-ajustar-inventario").modal("show");
  loadDataTipos(tipo_ajuste);
  const input = document.getElementById("fecha_ajuste");
  // Obtener fecha y hora actuales
  const now = new Date();
  // Convertir a formato compatible con datetime-local: "YYYY-MM-DDTHH:MM"
  const fechaLocal = now.toISOString().slice(0, 16);
  // Establecer el valor
  input.value = fechaLocal;
});

$("#idtipoajuste").change(function (e) {
  loadDataTipos($("#idtipoajuste").val());
});

function loadDataTipos(tipo_ajuste) {
  var tipo = "";
  if (tipo_ajuste === "1") {
    tipo = "entrada";
  } else if (tipo_ajuste === "2") {
    tipo = "salida";
  } else {
    // Si es 0 (Todos), puedes devolver todos los conceptos
    tipo = "todos";
  }

  $.ajax({
    url: "controladores/inventario.php?op=listar_tipos_ajuste",
    type: "GET",
    data: { tipo: tipo },
    success: function (data) {
      $("#idconcepto").html(data);
    },
  });
}


// Seleccionar/Deseleccionar todos
$(document).on("click", "#checkAll", function () {
  $(".checkItem").prop("checked", this.checked);
});

// Si desmarco uno, desactiva "seleccionar todos"
$(document).on("click", ".checkItem", function () {
  if (!this.checked) {
    $("#checkAll").prop("checked", false);
  }
});


$("#formulario").submit(function (e) {
  e.preventDefault();

  var idtipo_ajuste = $("#idtipoajuste").val();
  var fecha_ajuste = $("#fecha_ajuste").val();
  var idconcepto = $("#idconcepto").val();
  var observacion_ajuste = $("#observacion_ajuste").val();

  if (idtipo_ajuste === "0") {
    toastr.warning("Seleccione un tipo de ajuste");
    return;
  }

  // ✅ obtener productos seleccionados
  let productos = [];
  $(".checkItem:checked").each(function () {
    productos.push($(this).val());
  });

  if (productos.length === 0) {
    toastr.warning("Seleccione al menos un producto para ajustar");
    return;
  }

  const data = {
    idinventario: $("#idinventario").val(),
    idsucursal: $("#idsucursal2").val(),
    idtipo_ajuste,
    fecha_ajuste,
    idconcepto,
    observacion_ajuste,
    productos: productos, // ✅ enviar lista de productos seleccionados
  };

  $.ajax({
    url: "controladores/inventario.php?op=ajustar_inventario",
    type: "POST",
    data: data,
    success: function (resp) {
      Swal.fire({
        title: "Ajuste de inventario",
        icon: "success",
        text: resp,
      });
      $("#modal-ajustar-inventario").modal("hide");
      tabla_productos.ajax.reload();
    },
  });
});

$(document).ready(function () {
  $("#btn-ajustar-inventario").off("click").on("click", function (e) {
    e.preventDefault();

    let seleccionados = $(".checkItem:checked").length;

    if (seleccionados === 0) {
      Swal.fire({
        icon: "warning",
        title: "Atención",
        text: "Debe seleccionar al menos un producto para ajustar el inventario.",
        confirmButtonText: "Aceptar"
      });
      return; // 🚫 No debe abrir el modal
    }

    // ✅ Solo si hay seleccionados, mostramos el modal
    $("#modal-ajustar-inventario").modal("show");
  });
});

// Cuando se abre el modal de ajustar inventario
$("#btn-ajustar-inventario").on("click", function () {
  validarTipoAjusteUnico();
});

function validarTipoAjusteUnico() {
  let seleccionados = $(".checkItem:checked");

  if (seleccionados.length === 1) {
    let row = seleccionados.closest("tr");
    let diferencia = parseFloat(row.attr("data-dif"));

    if (diferencia > 0) {
      // ENTRADA
      $("#idtipoajuste").val("1");
      $("#idtipoajuste option[value='2']").prop("disabled", true);
      $("#idtipoajuste option[value='1']").prop("disabled", false);
      $("#idtipoajuste").prop("disabled", true);
      loadDataTipos("1");
    } else if (diferencia < 0) {
      // SALIDA
      $("#idtipoajuste").val("2");
      $("#idtipoajuste option[value='1']").prop("disabled", true);
      $("#idtipoajuste option[value='2']").prop("disabled", false);
      $("#idtipoajuste").prop("disabled", true);
      loadDataTipos("2");
    } else {
      // sin diferencia
      $("#idtipoajuste").val("0");
      $("#idtipoajuste option").prop("disabled", true);
      $("#idtipoajuste").prop("disabled", true);
      $("#idconcepto").html('<option value="" selected>Seleccionar...</option>');
    }
  } else {
    // Varios: habilitar todo
    $("#idtipoajuste").val("0");
    $("#idtipoajuste option").prop("disabled", false);
    $("#idtipoajuste").prop("disabled", false);
  }
}


/*************************************************/
/* ---------------- Sección nueva: manejo de selección y apertura del modal ---------------- */

// Helper: parseo seguro de diferencia
function safeParseFloat(val) {
  if (val === null || val === undefined) return NaN;
  if (typeof val === "number") return val;
  const cleaned = String(val).replace(/[^\d\-\.,]/g, "").replace(",", ".");
  const f = parseFloat(cleaned);
  return isNaN(f) ? NaN : f;
}

// Obtener diferencia de un checkbox (usa data-dif del checkbox primero, si no usa el <tr>)
function getDifFromCheckbox($chk) {
  let dif = safeParseFloat($chk.attr("data-dif"));
  if (!isFinite(dif)) {
    // fallback: leer del tr
    try {
      dif = safeParseFloat($chk.closest("tr").attr("data-dif"));
    } catch (e) {
      dif = NaN;
    }
  }
  return dif;
}

// Actualiza el estado del checkbox "checkAll" según visible
function updateCheckAllState() {
  const totalVisible = $(".checkItem:visible").length;
  const totalChecked = $(".checkItem:visible:checked").length;
  $("#checkAll").prop("checked", totalVisible > 0 && totalVisible === totalChecked);
}

// Evaluar selección y ajustar el select idtipoajuste (no abre modal)
function evaluateSelectionUI() {
  const $selected = $(".checkItem:checked");
  const n = $selected.length;

  // Restaurar select por defecto
  $("#idtipoajuste option").prop("disabled", false);
  $("#idtipoajuste").prop("disabled", false);
  $("#idtipoajuste").val("0");

  if (n === 0) {
    // nada seleccionado: dejar todo por defecto
    return { n: 0 };
  }

  if (n === 1) {
    const dif = getDifFromCheckbox($selected.first());
    if (!isFinite(dif)) return { n: 1, dif: NaN };

    if (dif > 0) {
      // Diferencia positiva → ENTRADA
      $("#idtipoajuste").val("1");
      $("#idtipoajuste option[value='0'], #idtipoajuste option[value='2']").prop("disabled", true);
      $("#idtipoajuste").prop("disabled", true);
      $("#idtipoajuste").trigger("change");
      loadDataTipos("2"); // cargar conceptos de Entrada
      return { n: 1, dif: dif, forced: 2 };
    } else if (dif < 0) {
      // Diferencia negativa → SALIDA
      $("#idtipoajuste").val("2");
      $("#idtipoajuste option[value='0'], #idtipoajuste option[value='1']").prop("disabled", true);
      $("#idtipoajuste").prop("disabled", true);
      $("#idtipoajuste").trigger("change");
      loadDataTipos("1"); // cargar conceptos de Salida
      return { n: 1, dif: dif, forced: 1 };
    } else {
      // dif == 0 -> bloquear
      $("#idtipoajuste").val("0");
      $("#idtipoajuste option[value='1'], #idtipoajuste option[value='2']").prop("disabled", true);
      $("#idtipoajuste").prop("disabled", true);
      $("#idtipoajuste").trigger("change");
      loadDataTipos("0"); // sin conceptos
      return { n: 1, dif: 0, forced: 0 };
    }
  }

  // n > 1 -> contar signos
  let pos = 0, neg = 0, zero = 0;
  $selected.each(function () {
    const d = getDifFromCheckbox($(this));
    if (d > 0) pos++;
    else if (d < 0) neg++;
    else zero++;
  });

  if (pos > 0 && neg === 0 && zero === 0) {
    // todos positivos → preseleccionar ENTRADA
    $("#idtipoajuste").val("1");
    $("#idtipoajuste option").prop("disabled", false);
    $("#idtipoajuste").prop("disabled", false);
    $("#idtipoajuste").trigger("change");
    loadDataTipos("2");
    return { n, pos, neg, zero, suggested: 2 };
  }
  if (neg > 0 && pos === 0 && zero === 0) {
    // todos negativos → preseleccionar SALIDA
    $("#idtipoajuste").val("2");
    $("#idtipoajuste option").prop("disabled", false);
    $("#idtipoajuste").prop("disabled", false);
    $("#idtipoajuste").trigger("change");
    loadDataTipos("1");
    return { n, pos, neg, zero, suggested: 1 };
  }

  // mezcla → dejar libre
  $("#idtipoajuste").val("0");
  $("#idtipoajuste option").prop("disabled", false);
  $("#idtipoajuste").prop("disabled", false);
  $("#idtipoajuste").trigger("change");
  loadDataTipos("0");
  return { n, pos, neg, zero, mixed: true };
}


// Filtrar selección: dejar checked solo los positivos o solo los negativos
function filtrarSeleccion(tipo) {
  // tipo = 'positivos' | 'negativos'
  $(".checkItem:checked").each(function () {
    const $c = $(this);
    const d = getDifFromCheckbox($c);
    if (tipo === "positivos") {
      if (!(d > 0)) $c.prop("checked", false).trigger("change");
    } else if (tipo === "negativos") {
      if (!(d < 0)) $c.prop("checked", false).trigger("change");
    }
  });
  updateCheckAllState();
  evaluateSelectionUI();
}

// Click en "select all" (solo afecta checkboxes visibles en DOM)
$(document).off("click", "#checkAll").on("click", "#checkAll", function () {
  $(".checkItem:visible").prop("checked", this.checked).trigger("change");
  updateCheckAllState();
});

// Delegado: al cambiar cualquier checkbox, actualizar UI
$(document).off("change", ".checkItem").on("change", ".checkItem", function () {
  updateCheckAllState();
  evaluateSelectionUI();
});

// Botón Ajustar: controla apertura del modal y lógica para mezclas
$(document).off("click", "#btn-ajustar-inventario").on("click", "#btn-ajustar-inventario", function (e) {
  e.preventDefault();

  const $sel = $(".checkItem:checked");
  const n = $sel.length;
  if (n === 0) {
    Swal.fire({
      icon: "warning",
      title: "Atención",
      text: "Debe seleccionar al menos un producto para ajustar el inventario.",
      confirmButtonText: "Aceptar"
    });
    return;
  }

  // Primero evaluar la selección y ver si es mixta
  const info = evaluateSelectionUI();

  if (info.mixed) {
    // Selección mixta -> preguntar acción
    Swal.fire({
      title: 'Selección mixta',
      html: 'Seleccionaste productos con diferencias positivas y negativas.<br>¿Deseas ajustar solo positivos o solo negativos?',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: 'Solo positivos (Salida)',
      denyButtonText: 'Solo negativos (Entrada)',
      cancelButtonText: 'Cancelar'
    }).then((res) => {
      if (res.isConfirmed) {
        filtrarSeleccion('positivos');
        // fijar tipo salida y cargar conceptos
        $("#idtipoajuste").val("1");
        $("#idtipoajuste option").prop("disabled", false);
        loadDataTipos("1");
        $("#modal-ajustar-inventario").modal("show");
      } else if (res.isDenied) {
        filtrarSeleccion('negativos');
        $("#idtipoajuste").val("2");
        $("#idtipoajuste.option").prop("disabled", false);
        loadDataTipos("2");
        $("#modal-ajustar-inventario").modal("show");
      } else {
        // cancelar: no abrir modal
        return;
      }
    });
    return;
  }

  // No es mezcla:
  // Si info.suggested o info.forced existe: cargar conceptos según valor actual
  let tipoToLoad = $("#idtipoajuste").val();
  // si queda en 0 (todos) y hay >1 pero todos del mismo signo, evaluateSelectionUI habrá puesto suggested
  if (tipoToLoad === "0" && info.suggested) {
    tipoToLoad = String(info.suggested);
    $("#idtipoajuste").val(tipoToLoad);
  }
  // cargar conceptos antes de mostrar modal
  if (tipoToLoad === "1" || tipoToLoad === "2") {
    loadDataTipos(tipoToLoad);
  } else {
    loadDataTipos("todos");
  }

  // setear fecha local
  const now = new Date();
  const tz = now.getTimezoneOffset() * 60000;
  $("#fecha_ajuste").val(new Date(Date.now() - tz).toISOString().slice(0, 16));

  // abrir modal
  $("#modal-ajustar-inventario").modal("show");
});

// Al cerrar modal: restaurar select y conceptos
$("#modal-ajustar-inventario").off("hidden.bs.modal").on("hidden.bs.modal", function () {
  $("#idtipoajuste").prop("disabled", false).val("0");
  $("#idtipoajuste option").prop("disabled", false);
  $("#idconcepto").html('<option value="" selected>Seleccionar...</option>');
});

/*************************************************/

/*************************************************/
$("#btn-consultar-inventarios").click(function () {
  var idsucursal = $("#idsucursal2").val();
  var idinventario = $("#idinventario").val();

  if (idsucursal === "Todos" || !idsucursal) {
    toastr.warning("Seleccione un almacén");
    return;
  }
  if (!idinventario) {
    toastr.warning("Seleccione un inventario");
    return;
  }

  $.ajax({
    url: "controladores/inventario.php?op=resumen_inventario",
    type: "GET",
    data: { idsucursal: idsucursal, idinventario: idinventario },
    dataType: "json",
    success: function (resp) {
      if (resp.status) {
        Swal.fire({
          title: "Resumen de Inventario",
          html: `
            <b>Inventario:</b> ${resp.inventario}<br>
            <b>Almacén:</b> ${resp.sucursal}<br>
            <b>Estado:</b> ${resp.estado}<br>
            <b>Apertura:</b> ${resp.fecha_apertura}<br>
            <b>Cierre:</b> ${resp.fecha_cierre ?? "-"}<br><br>
            <b>Total productos:</b> ${resp.total_productos}<br>
            <b>Diferencias positivas:</b> ${resp.total_positivos}<br>
            <b>Diferencias negativas:</b> ${resp.total_negativos}
          `,
          icon: "info"
        });
      } else {
        toastr.error(resp.message);
      }
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      toastr.error("Error al consultar inventario");
    }
  });
});

/*************************************************/

init();
