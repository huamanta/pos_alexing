var tablaPagos = null;
var tablaCobros = null;
var tabla = null;

let listarCajas = null;
let historialCajas = null;
let idCaja = null;


function init() {
  $("#body").addClass("sidebar-collapse sidebar-mini");
  listarCajas.load();
  $("#myModal").on("submit", function (e) {
    guardaryeditar(e);
  });
  // $.post("controladores/venta.php?op=selectSucursal3", function (r) {
  //   $("#idsucursal2").html(r);
  // });
  // $("#idsucursal2").change(historial);
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal").html(r);
    $("#idsucursal").select2("");
    $('#idsucursal').prop('disabled', true);
  });
  $("#navVentasActive").addClass("treeview active");
  $("#navVentas").addClass("treeview menu-open");
  $("#navCajas").addClass("active");
  $("#panelHistorial").hide();
  $("#panelCajas").show();
}

function pintarCajas(data, permissions) {
  let html = "";

  if (data.length === 0) {

    html = `
            <tr>
                <td colspan="8" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

    $("#tbllistado tbody").html(html);
    return;
  }

  data.forEach(item => {
    let estado = null;
    const estadoItem = parseInt(item.estado);
    if (estadoItem === 1) {
      estado = '<span class="badge bg-green">ACTIVADO</span>';
    } else if (estadoItem === 2) {
      estado = '<span class="badge bg-blue">ABIERTO</span>';
    } else {
      estado = '<span class="badge bg-red">DESACTIVADO</span>';
    }

    let btnDesactivar = '<button class="btn btn-danger btn-xs" onclick="desactivar(' + item.idcaja + ')"><i class="fas fa-times-circle"></i></button>';
    if (!estadoItem) {
      btnDesactivar = ' <button class="btn btn-primary btn-xs" onclick="activar(' + item.idcaja + ')"><i class="fa fa-check"></i></button>';
    };

    html += `
            <tr>
                <td>${item.numero}</td>
                <td>${item.nombre || '-'}</td>
                <td>${item.personal || '-'}</td>
                <td>${item.almacen || '-'}</td>
                <td>${estado}</td>
                <td>
                  <button class="btn btn-warning btn-xs" onclick="mostrar(${item.idcaja})"><i class="fas fa-edit"></i></button>
                  <button class="btn btn-info btn-xs" onclick="historialCaja(${item.idcaja})"><i class="fas fa-list"></i></button>
                  ${btnDesactivar}
                </td>
            </tr>
        `;

  });

  $("#tbllistado tbody").html(html);
}

listarCajas = new FluentPaginator({
  url: "controladores/cajas.php?op=listar",
  renderTabla: pintarCajas,
  tableBody: "#tbodyCajas",
  extraParams: () => ({
    idcaja: idCaja
  })
});

function guardaryeditar(e) {
  e.preventDefault(); //No se activará la acción predeterminada del evento
  //$("#btnGuardar").prop("disabled",true);
  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "controladores/cajas.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      const data = response;
      if (!data.success) {
        Swal.fire({
          title: "Cajas",
          icon: "error",
          text: data.message,
        });
        return;
      }
      Swal.fire({
        title: "Cajas",
        icon: "success",
        text: data.message,
      });
      $("#myModal").modal("hide");
      listar();
      limpiar();
    },
    error: function (error) {
      console.log(error);
      Swal.fire("Error", error.responseJSON.message || 'Error al realizar la accion', 'error');
    }
  });
}

//Función para desactivar registros
function desactivar(idcaja) {
  Swal.fire({
    title: "¿Desactivar?",
    text: "¿Está seguro Que Desea Desactivar la Caja?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, desactivar",
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("controladores/cajas.php?op=desactivar", { idcaja: idcaja }, function (response) {
        const data = response;
        if (!data.success) {
          Swal.fire({
            title: "Cajas",
            icon: "error",
            text: data.message,
          });
          return;
        }
        Swal.fire({
          title: "Cajas",
          icon: "success",
          text: data.message,
        });
        listarCajas.load();
      }).fail(function (error) {
        Swal.fire({
          title: "Cajas",
          icon: "error",
          text: error.responseJSON.message || "Ocurrio un error",
        });
      });
    }
  })
}

//Función para desactivar registros
function activar(idcaja) {
  Swal.fire({
    title: "Activar?",
    text: "¿Está seguro Que Desea Activar la Caja?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("controladores/cajas.php?op=activar", { idcaja: idcaja }, function (response) {
        const data = response;
        if (!data.success) {
          Swal.fire({
            title: "Cajas",
            icon: "error",
            text: data.message,
          });
          return;
        }
        Swal.fire({
          title: "Cajas",
          icon: "success",
          text: data.message,
        });
        listarCajas.load();
      }).fail(function (error) {
        Swal.fire({
          title: "Cajas",
          icon: "error",
          text: error.responseJSON.message || "Ocurrio un error",
        });
      });;
    }
  });
}

function mostrar(idcaja) {
  $.post(
    "controladores/cajas.php?op=mostrar", {
    idcaja: idcaja
  },
    function (response, status) {
      const data = response;
      $("#myModal").modal("show");

      $("#nombre").val(data.nombre);
      $("#numero").val(data.numero);
      $("#idcaja").val(data.idcaja);
    }
  );
}

function limpiar() {
  $("#numero").val("");
  $("#nombre").val("");
  $("#idcaja").val("");
}

//Función cancelarform
function cancelarform() {
  limpiar();
}

function regrearLista() {
  $("#panelHistorial").hide();
  $("#panelCajas").show();
}

function pintarHistorialCajas(data, permissions) {
  let html = "";

  if (data.length === 0) {

    html = `
            <tr>
                <td colspan="8" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

    $("#tblhistorial tbody").html(html);
    return;
  }

  data.forEach(item => {
    let btnCierre = "";

    if (permissions.superusuario && permissions.cerrar_caja && !item.fecha_cierre) {
      btnCierre = `
                  <button class="btn btn-danger btn-xs"
                          title="Cerrar caja"
                          onclick="showResumenCaja(${item.aperturacajaid})">
                      <i class="fas fa-sign-out-alt"></i>
                  </button>`;
    }

    html += `
            <tr>
                <td>${item.nombre}</td>
                <td>${item.personal || '-'}</td>
                <td>${item.fecha_apertura || '-'}</td>
                <td><span class="badge bg-danger">${item.efectivo_apertura_str || '-'}</span></td>
                <td>${item.fecha_cierre || '<span class="badge bg-primary">ABIERTO</span>'}</td>
                <td><span class="badge bg-success">${item.efectivo_cierre_str || '-'}</span></td>
                <td>${item.cantventas || '-'}</td>
                <td>
                  <button class="btn btn-warning btn-xs"
                          title="Ver registros de caja"
                          onclick="verReportes(${item.aperturacajaid})">
                      <i class="fas fa-eye"></i>
                  </button>
                  ${btnCierre}
                </td>
            </tr>
        `;

  });

  $("#tblhistorial tbody").html(html);
}

historialCajas = new FluentPaginator({
  url: "controladores/cajas.php?op=historialcajas",
  renderTabla: pintarHistorialCajas,
  tableBody: "#tbodyHistorialCajas",
  searchSelector: "#searchHistorialCajas",
  limitSelector: "#limitHistorialCajas",
  paginationId: "#paginationHistorialCajas",
  extraParams: () => ({
    idcaja: idCaja
  })
});

function historialCaja(idcaja) {
  idCaja = idcaja;
  historialCajas.load();
  $("#panelHistorial").show();
  $("#panelCajas").hide();
}

let efectivoEsperado = 0;
let aperturaCajaId = 0;

function showResumenCaja(aperturacajaid) {

  aperturaCajaId = aperturacajaid;

  $.ajax({

    url: "controladores/pos.php?op=showResumenCaja",

    type: "GET",

    data: {
      aperturacajaid: aperturacajaid
    },

    dataType: "json",

    success: function (r) {

      $("#modalCerrarCaja").modal("show");
      $("#aperturacajaid").val(r.aperturacajaid);
      $("#total_ventas").html(r.total_ventas);

      $("#efectivo_apertura").html(r.efectivo_apertura);

      $("#ventas_efectivo").html(r.total_ventas_efectivo);

      $("#ventas_deposito").html(r.total_ventas_deposito);

      $("#abonos_efectivo").html(r.abonos_efectivo);

      $("#abonos_deposito").html(r.abonos_deposito);

      $("#movimientos_efectivo").html(r.total_movimientos_ingreso_efectivo);

      $("#movimientos_deposito").html(r.total_movimientos_ingreso_deposito);

      //gastos
      $("#gastos_efectivo").html(r.total_movimientos_egreso_efectivo);
      $("#gastos_deposito").html(r.total_movimientos_egreso_deposito);

      efectivoEsperado = parseFloat(r.efectivo_esperado).toFixed(2);

      $("#efectivo_esperado").html(
        "<strong>S/ " + efectivoEsperado + "</strong>"
      );

      $("#efectivo_contado").val(efectivoEsperado);

      calcularDiferencia();

    }

  });

}

$("#efectivo_contado").on("keyup change", function () {

  calcularDiferencia();

});

function calcularDiferencia() {

  let contado = parseFloat($("#efectivo_contado").val()) || 0;

  let diferencia = contado - efectivoEsperado;

  $("#diferencia").val(
    "S/ " + diferencia.toFixed(2)
  );

}

function verReportes(aperturacajaid) {
  $("#myModal2").modal("show");
  listarVentas(aperturacajaid);
  listarMovimientos(aperturacajaid);
  listarPagos(aperturacajaid);
  listarCobros(aperturacajaid);
}

function listarPagos(aperturacajaid) {
  tablaPagos = $("#tbllistadoPagos")
    .dataTable({
      //"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      processing: true,
      language: {
        processing: "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
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
        url: "controladores/cajas.php?op=listarPagosPorApertura&aperturacajaid=" +
          aperturacajaid,
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 5, //Paginación
      order: [
        [0, "desc"]
      ], //Ordenar (columna,orden)
    })
    .DataTable();
}

function listarCobros(aperturacajaid) {
  tablaCobros = $("#tbllistadoCobros")
    .dataTable({
      //"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      processing: true,
      language: {
        processing: "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
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
        url: "controladores/cajas.php?op=listarCobrosPorApertura&aperturacajaid=" +
          aperturacajaid,
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 5, //Paginación
      order: [
        [0, "desc"]
      ], //Ordenar (columna,orden)
    })
    .DataTable();
}

function listarVentas(aperturacajaid) {
  tabla = $("#tbllistadoVentas")
    .dataTable({
      //"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      processing: true,
      language: {
        processing: "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
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
        url: "controladores/pos.php?op=listarVentas2&aperturacajaid=" +
          aperturacajaid,
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 5, //Paginación
      order: [
        [0, "desc"]
      ], //Ordenar (columna,orden)
    })
    .DataTable();
}

function listarMovimientos(aperturacajaid) {
  tabla = $("#tbllistadoMovimientos")
    .dataTable({
      //"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      processing: true,
      language: {
        processing: "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
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
        url: "controladores/cajas.php?op=listarMovimientosPorApertura&aperturacajaid=" +
          aperturacajaid,
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 5, //Paginación
      order: [
        [0, "desc"]
      ], //Ordenar (columna,orden)
    })
    .DataTable();
}

$("#formCerrarCaja").submit(function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  $.ajax({
    url: "controladores/pos.php?op=cerrarCaja",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      const data = JSON.parse(response);
      if (!data.success) {
        Swal.fire("error!", data.message, "error");
        return;
      }
      $("#modalCerrarCaja").modal("hide");
      Swal.fire("correcto!", data.message, "success");
      historialCajas.load();
    }
  });

});

init();