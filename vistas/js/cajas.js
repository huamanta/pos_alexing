function init() {
  $("#body").addClass("sidebar-collapse sidebar-mini");
  listar();
  $("#myModal").on("submit", function (e) {
    guardaryeditar(e);
  });
  $.post("controladores/venta.php?op=selectSucursal3", function (r) {
    $("#idsucursal2").html(r);
  });
  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal").html(r);
    $("#idsucursal").select2("");
  });
  $("#navVentasActive").addClass("treeview active");
  $("#navVentas").addClass("treeview menu-open");
  $("#navCajas").addClass("active");
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
    confirmButtonText: "Si",
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "controladores/cajas.php?op=desactivar",
        { idcaja: idcaja },
        function (e) {
          Swal.fire("Desactivado!", e, "success");
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
        function (e) {
          Swal.fire("Activado!", e, "success");
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
$("#fecha_inicio").change(historial);
$("#fecha_fin").change(historial);
function historial() {
  let fecha_inicio = $("#fecha_inicio").val();
  let fecha_fin = $("#fecha_fin").val();
  let idsucursal = $("#idsucursal2").val();
  $.ajax({
    url: "controladores/cajas.php?op=historialcajas",
    data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin },
    type: "get",
    dataType: "json",
    success: function (data) {
      console.log(data);
      var html = "";
      if (data != '') {
        $.each(data, function (i, item) {
          html +=
            `<tr>
                <td>` + data[i].nombre + `</td>
                <td>` + data[i].personal + `</td>
                <td>` + data[i].fecha_apertura + `</td>
                <td>` + data[i].efectivo_apertura + `</td>
                <td>` + (data[i].fecha_cierre != null ? data[i].fecha_cierre : '<span class="badge bg-blue">ABIERTO</span>') + `</td>
                <td>` + (data[i].efectivo_cierre != null ? data[i].efectivo_cierre : '<span class="badge bg-blue">ABIERTO</span>') + `</td>
                <td>` + data[i].cantventas + `</td>
                <td><a href="#" onclick="verReportes(` + data[i].aperturacajaid + `)"><i class="fa fa-eye"></i></a></td>
              </tr>`;
        });
      }else{
        html +=
            `<tr>
            <td colspan="5">No se encontraron resultados</td>
        </tr>`;
      }
      $("#tblhistorial").html(html);
    },
    error: function (e) {
      console.log(e.responseText);
    },
  });
}

function verReportes(aperturacajaid) {
  $("#myModal2").modal("show");
  listarVentas(aperturacajaid);
  listarMovimientos(aperturacajaid);
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

init();
