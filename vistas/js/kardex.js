var tabla;

function init() {
  $("#body").addClass("sidebar-collapse sidebar-mini");

  // Nav actual
  $('#navKardex').addClass("treeview active active");

  // Selects iniciales
  $.post("controladores/venta.php?op=selectSucursal3", function (r) {
    $("#idsucursal2").html(r);
    $('#idsucursal2').select2({ width: '100%' });

    // Cargar productos de la sucursal seleccionada
    cargarProductos($('#idsucursal2').val());
  });

  $.post("controladores/venta.php?op=selectVendedor", function (r) {
    $("#idvendedor").html(r);
    $('#idvendedor').select2({ width: '100%' });
  });

  // Producto general (fallback)
  $.post("controladores/venta.php?op=selectProducto", function (r) {
    $("#idproducto").html(r);
    $('#idproducto').select2({ width: '100%' });
  });

  // Eventos de filtros
  $("#idsucursal2, #fecha_inicio, #fecha_fin, #idproducto").on("change", listar);

  // Botón limpiar
  $("#btnLimpiar").on("click", function () {
    $("#fecha_inicio").val(getHoy());
    $("#fecha_fin").val(getHoy());
    $('#idsucursal2').val($('#idsucursal2 option:first').val()).trigger('change');
    $('#idproducto').val($('#idproducto option:first').val()).trigger('change');
    listar();
  });

  // Primera carga
  listar();
}

function getHoy() {
  var d = new Date();
  var m = ("0" + (d.getMonth() + 1)).slice(-2);
  var day = ("0" + d.getDate()).slice(-2);
  return d.getFullYear() + "-" + m + "-" + day;
}

function cargarProductos(idsucursal) {
  // Si es "Todos", mandar 'all' al backend (como lo manejabas)
  if (idsucursal === 'Todos') idsucursal = 'all';

  $.post("controladores/venta.php?op=selectProducto", { idsucursal2: idsucursal }, function (r) {
    $("#idproducto").html(r);
    $('#idproducto').select2({ width: '100%' });
  });
}

function listar() {
  var fecha_inicio = $("#fecha_inicio").val();
  var fecha_fin = $("#fecha_fin").val();
  var idproducto = $("#idproducto").val();
  var idvendedor = $("#idvendedor").val();
  var idsucursal = $("#idsucursal2").val();

  // Destruir instancia previa antes de crear
  if ($.fn.DataTable.isDataTable('#tbllistado')) {
    $('#tbllistado').DataTable().destroy();
  }

  tabla = $('#tbllistado').DataTable({
    aProcessing: true,
    aServerSide: true,
    processing: true,
    language: {
      processing: "<div class='p-3 text-center'><img style='width:80px;height:80px;' src='../files/plantilla/loading-page.gif' /><div class='mt-2'>Cargando...</div></div>",
      url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
    },
    responsive: true,
    lengthChange: false,
    autoWidth: false,
    deferRender: true,
    scrollX: true,
    order: [[0, 'desc']],
    dom:
      '<"row mb-2"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4 text-center"B><"col-sm-12 col-md-4"f>>' +
      't' +
      '<"row mt-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    lengthMenu: [
      [5, 10, 25, 50, 100, -1],
      ['5 filas', '10 filas', '25 filas', '50 filas', '100 filas', 'Mostrar todo']
    ],
    buttons: [
      'pageLength',
      {
        extend: 'excelHtml5',
        text: "<i class='fas fa-file-excel'></i> Excel",
        titleAttr: 'Exportar a Excel',
        className: 'btn btn-sm btn-excel',
        filename: function () {
          return 'Kardex_' + (idproducto || 'todos') + '_' + fecha_inicio + '_a_' + fecha_fin;
        }
      },
      {
        extend: 'pdfHtml5',
        text: "<i class='fas fa-file-pdf'></i> PDF",
        titleAttr: 'Exportar a PDF',
        className: 'btn btn-sm btn-pdf',
        orientation: 'landscape',
        pageSize: 'A4',
        filename: function () {
          return 'Kardex_' + (idproducto || 'todos') + '_' + fecha_inicio + '_a_' + fecha_fin;
        },
        exportOptions: { columns: ':visible' }
      },
      {
        extend: 'colvis',
        text: "<i class='fas fa-bars'></i> Columnas",
        className: 'btn btn-sm btn-colvis'
      }
    ],
    columnDefs: [
      { targets: [5, 6, 7, 8, 9], className: 'text-right' }
    ],
    ajax: {
      url: 'controladores/consultas.php?op=kardex',
      data: {
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin,
        idproducto: idproducto,
        idvendedor: idvendedor,
        idsucursal: idsucursal
      },
      type: "get",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText);
      }
    },
    bDestroy: true,
    iDisplayLength: 10
  });
}

$(document).ready(init);
