var tablaPagos = null;
var tablaCobros = null;
var tabla = null;

let paginaActual = 1;
let limite = 10;
let totalRegistros = 0;


function init() {
  $("#body").addClass("sidebar-collapse sidebar-mini");
  listar();
  $("#myModal").on("submit", function (e) {
    guardaryeditar(e);
  });
  // $.post("controladores/venta.php?op=selectSucursal3", function (r) {
  //   $("#idsucursal2").html(r);
  // });
  // $("#idsucursal2").change(historial);
  // $.post("controladores/venta.php?op=selectSucursal", function (r) {
  //   $("#idsucursal").html(r);
  //   $("#idsucursal").select2("");
  // });
  $("#navVentasActive").addClass("treeview active");
  $("#navVentas").addClass("treeview menu-open");
  $("#navCajas").addClass("active");
  $("#panelHistorial").hide();
  $("#panelCajas").show();
}

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

    success: function (datos) {
      Swal.fire({
        title: "Correcto",
        icon: "success",
        text: datos,
      });

      $("#myModal").modal("hide");
      listar();
    },
  });
  limpiar();
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
        url: "controladores/cajas.php?op=listar",
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
      $.post(
        "controladores/cajas.php?op=desactivar",
        { idcaja: idcaja },
        function (response) {
          const data = JSON.parse(response);
          if (!data.success) {
            Swal.fire("error!", data.message, "error");
            return;
          }
          Swal.fire("Desactivado!", data.message, "success");
          tabla.ajax.reload();
        }
      );
    }
  });
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
      $.post(
        "controladores/cajas.php?op=activar",
        { idcaja: idcaja },
        function (response) {
          const data = JSON.parse(response);
          if (!data.success) {
            Swal.fire("error!", data.message, "error");
            return;
          }
          Swal.fire("Desactivado!", data.message, "success");
          tabla.ajax.reload();
        }
      );
    }
  });
}

function mostrar(idcaja) {
  $.post(
    "controladores/cajas.php?op=mostrar",
    { idcaja: idcaja },
    function (data, status) {
      data = JSON.parse(data);
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

$("#limitHistorial").change(function () {

  limite = parseInt($(this).val());

  paginaActual = 1;

  historialCaja(idCajaSeleccionada);

});

function historialCaja(idcaja) {
  $("#panelHistorial").show();
  $("#panelCajas").hide();
  $.ajax({

    url: "controladores/cajas.php?op=historialcajas",

    data: {

      idcaja: idcaja,

      limit: limite,

      offset: (paginaActual - 1) * limite

    },

    type: "GET",

    dataType: "json",

    success: function (response) {

      totalRegistros = response.total;

      cargarTabla(response.rows);

      crearPaginador(idcaja);

    }

  });

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

function cargarTabla(rows) {

  let html = "";

  if (rows.length > 0) {

    $.each(rows, function (i, item) {

      let btnCierre = "";

      if (item.fecha_cierre === null && item.puede_cerrar_caja) {
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
                    <td>${item.personal}</td>
                    <td>${item.fecha_apertura}</td>
                    <td>${item.efectivo_apertura}</td>
                    <td>${item.fecha_cierre ?? '<span class="badge bg-blue">ABIERTO</span>'}</td>
                    <td>${item.efectivo_cierre ?? '<span class="badge bg-blue">ABIERTO</span>'}</td>
                    <td>${item.cantventas}</td>
                    <td>
                        <button class="btn btn-warning btn-xs"
                                title="Ver registros de caja"
                                onclick="verReportes(${item.aperturacajaid})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${btnCierre}
                    </td>
                </tr>`;
    });

  } else {

    html = `
            <tr>
                <td colspan="8" class="text-center">
                    No se encontraron resultados
                </td>
            </tr>`;
  }

  $("#tblhistorial").html(html);
}


function crearPaginador(idcaja) {

  let totalPaginas = Math.ceil(totalRegistros / limite);

  let html = "";

  html += `
        <li class="page-item ${paginaActual == 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual - 1},${idcaja})">
                Anterior
            </a>
        </li>
    `;

  for (let i = 1; i <= totalPaginas; i++) {

    html += `
            <li class="page-item ${i == paginaActual ? 'active' : ''}">
                <a class="page-link" href="#" onclick="cambiarPagina(${i},${idcaja})">
                    ${i}
                </a>
            </li>
        `;

  }

  html += `
        <li class="page-item ${paginaActual == totalPaginas ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual + 1},${idcaja})">
                Siguiente
            </a>
        </li>
    `;

  $("#paginadorHistorial").html(html);

}


function cambiarPagina(pagina, idcaja) {

  let totalPaginas = Math.ceil(totalRegistros / limite);

  if (pagina < 1 || pagina > totalPaginas)
    return;

  paginaActual = pagina;

  historialCaja(idcaja);

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
          "controladores/cajas.php?op=listarPagosPorApertura&aperturacajaid=" +
          aperturacajaid,
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

function listarCobros(aperturacajaid) {
  tablaCobros = $("#tbllistadoCobros")
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
          "controladores/cajas.php?op=listarCobrosPorApertura&aperturacajaid=" +
          aperturacajaid,
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

function listarVentas(aperturacajaid) {
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
          "controladores/pos.php?op=listarVentas2&aperturacajaid=" +
          aperturacajaid,
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

function listarMovimientos(aperturacajaid) {
  tabla = $("#tbllistadoMovimientos")
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
          "controladores/cajas.php?op=listarMovimientosPorApertura&aperturacajaid=" +
          aperturacajaid,
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
      historialCaja(data.idcaja);
    }
  });

});

init();
