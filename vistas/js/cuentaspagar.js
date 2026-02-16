var tabla;

//Función que se ejecuta al inicio
function init() {
  $("body").addClass("sidebar-mini sidebar-collapse");
  listar();
  listarSaldos();

  $("#getCodeModal").on("submit", function (e) {
    guardaryeditar(e);
  });

  $("#fecha_inicio").change(function (e) {
    e.preventDefault();
    listar();
    listarSaldos();
  });
  $("#fecha_fin").change(function (e) {
    e.preventDefault();
    listar();
    listarSaldos();
  });
  $("#idcliente").change(function (e) {
    e.preventDefault();
    listar();
    listarSaldos();
  });

  $("#navCuentasPorPagar").addClass("treeview active");
  $("#navCuentasPorPagar").addClass("active");

  //cargamos los items al select almacen
  $.post("controladores/venta.php?op=selectSucursal3", function (r) {
    $("#idsucursal2").html(r);
    $("#idsucursal2").select2("");
  });

  //Cargamos los items al select cliente
  $.post("controladores/compra.php?op=selectProveedor", function (r) {
    $("#idcliente").html(r);
    $("#idcliente").select2("");
  });
}

function listarSaldos() {
  var fecha_inicio = $("#fecha_inicio").val();
  var fecha_fin = $("#fecha_fin").val();
  var idcliente = $("#idcliente").val();
  var idsucursal = $("#idsucursal2").val();

  // Verificar si fecha de inicio es mayor que fecha de fin
  var fechaInicio = new Date(fecha_inicio);
  var fechaFin = new Date(fecha_fin);

  if (fechaInicio > fechaFin) {
    // Establecer fecha de fin en la fecha actual
    var hoy = new Date();
    var dd = String(hoy.getDate()).padStart(2, "0");
    var mm = String(hoy.getMonth() + 1).padStart(2, "0");
    var yyyy = hoy.getFullYear();

    fecha_fin = yyyy + "-" + mm + "-" + dd;
    $("#fecha_fin").val(fecha_fin);
  }

  $.ajax({
    url: "controladores/cuentaspagar.php?op=listar_saldos",
    data: {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idcliente: idcliente,
      idsucursal: idsucursal,
    },
    type: "get",
    dataType: "json",
    success: function (data) {
      console.log(data);
      var saldos = 0;
      if (data.abonototal != null && data.deudatotal != null) {
        saldos = parseFloat(data.deudatotal) + parseFloat(data.abonototal);
      }
      $("#saldos").text("S/. " + parseFloat(saldos).toFixed(2));
      // Corrige la evaluación condicional para #abonos
      $("#abonos").text(
        "S/. " +
          (data.abonototal != null
            ? parseFloat(data.abonototal).toFixed(2)
            : "0.00")
      );

      // Corrige la evaluación condicional para #deudas
      $("#deudas").text(
        "S/. " +
          (data.deudatotal != null
            ? parseFloat(data.deudatotal).toFixed(2)
            : "0.00")
      );

      if (
        idcliente != "Todos" &&
        idcliente != null &&
        data.deudatotal != 0 &&
        data.deudatotal != null
      ) {
        $("#panel_amortizar").html(
          `<i class="btn btn-success" style="font-size: 15px !important; margin-top: -10px" onclick="amortizarDeuda(${data.deudatotal}, ${idcliente}, '${fecha_inicio}', '${fecha_fin}')"> Amortizar</i>`
        );
      } else {
        $("#panel_amortizar").html(
          '<i class="fas fa-money-bill fa-lg" style="font-size: 20px !important"></i>'
        );
      }
    },
    error: function (e) {
      console.log(e.responseText);
    },
  });
}

function amortizarDeuda(deuda, idcliente, fecha_inicio, fecha_fin) {
	$('#modalAmortizar').modal('show');
	$('#montoAdeudadoAmortizar').val(parseFloat(deuda).toFixed(2));
	$('#deudaTotalAmortizar').html(parseFloat(deuda).toFixed(2));
	$('#idcliente_amortizar').val(idcliente);
	$('#fecha_inicio_amortizar').val(fecha_inicio);
	$('#fecha_fin_amortizar').val(fecha_fin);
	
}

$('#formulario-amortizar').submit(function(e){
	e.preventDefault();
	var formData = new FormData(this);
	$.ajax({
		url: 'controladores/cuentaspagar.php?op=amortizar_deuda',
        data:formData,
        type : "POST",
	    contentType: false,
	    processData: false,
        success : function (data) {
			var data = JSON.parse(data);
			if (data.success) {
				listar();
				listarSaldos();
				$('#modalAmortizar').modal('hide');
				$('#montoAdeudadoAmortizar').val('');
				$('#deudaTotalAmortizar').html('');
				$('#idcliente_amortizar').val('');
				$('#fecha_inicio_amortizar').val('');
				$('#fecha_fin_amortizar').val('');
			}
        },                        
        error: function(e){
            console.log(e.responseText);    
        }
	})
});

//Función limpiar
function limpiar() {
  $("#montoPagar").val("");
  $("#op").val("");
  $("#observacion").val("");
  $("#montoPagarTarjeta").attr("readonly", "readonly");
  $("#banco").attr("readonly", "readonly");
  $("#op").attr("readonly", "readonly");
  $("#montoPagarTarjeta").val("");
  $("#op").val("");
}

//Función Listar
function listar() {
  var fecha_inicio = $("#fecha_inicio").val();
  var fecha_fin = $("#fecha_fin").val();
  var idcliente = $("#idcliente").val();
  var idsucursal = $("#idsucursal2").val();

  tabla = $("#tbllistadocuentasxcobrar")
    .dataTable({
      //"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      processing: true,
      language: {
        processing:
          "<img style='width:80px; height:80px;' src='../files/plantilla/loading-page.gif' />",
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
        {
          extend: "pageLength",
          orientation: "landscape",
          pageSize: "LEGAL",
        },
        {
          extend: "pdfHtml5",
          orientation: "landscape",
          title: "Lista de documentos pendientes por pagar",
          pageSize: "LEGAL",
        },
        {
          extend: "copy",
          orientation: "landscape",
          pageSize: "LEGAL",
        },
        {
          extend: "excel",
          orientation: "landscape",
          title: "Lista de documentos pendientes por pagar",
          pageSize: "LEGAL",
        },
      ],
      ajax: {
        url: "controladores/cuentaspagar.php?op=listar",
        data: {
          fecha_inicio: fecha_inicio,
          fecha_fin: fecha_fin,
          idcliente: idcliente,
          idsucursal: idsucursal,
        },
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 10, //Paginación
      order: [[0, "desc"]], //Ordenar (columna,orden)
    })
    .DataTable();
}

function guardaryeditar(e) {
  e.preventDefault();

  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "controladores/cuentaspagar.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      Swal.fire({
                title: 'Abono Registrado Correctamente',
                icon: 'success',
                text: datos
            });
      $("#getCodeModal").modal("hide");

      listar();
    },
  });
  limpiar();
}

function mostrar(idcpc) {
  $("#getCodeModal").modal("show");
  $.post(
    "controladores/cuentaspagar.php?op=mostrar",
    { idcpc: idcpc },
    function (data, status) {
      data = JSON.parse(data);

      var label = document.querySelector("#documento");
      label.textContent =
        data.tipo_comprobante +
        " : " +
        data.serie_comprobante +
        " - " +
        data.num_comprobante;

      var label = document.querySelector("#deutaTotal");
      label.textContent = data.deudatotal;

      var label = document.querySelector("#fechavencimiento");
      label.textContent = data.fechavencimiento;

      $("#montoAdeudado").val(data.deudatotal);

      $("#idcpc").val(data.idcpp);

      $("#idventa").val(data.idcompra);

      $("#fechaPago").val(data.fechavencimiento);
    }
  );
}

function mostrarAbonos(idcpc) {
  $("#getCodeModal2").modal("show");

  $.post(
    "controladores/cuentaspagar.php?op=mostrar",
    { idcpc: idcpc },
    function (data, status) {
      data = JSON.parse(data);

      var label = document.querySelector("#abonoTotal2");
      label.textContent = data.deuda;

      var label = document.querySelector("#abonoTotal");
      label.textContent = data.abonototal;
    }
  );

  tabla = $("#tbllistado")
    .dataTable({
      //"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      dom: "Bfrtip", //Definimos los elementos del control de tabla
      buttons: ["excelHtml5", "pdf"],
      ajax: {
        url: "controladores/cuentaspagar.php?op=listarDetalle",
        data: { idcpc: idcpc },
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 10, //Paginación
    })
    .DataTable();
}

$("#formapago").change(function (e) {
  if ($(this).val() != "Efectivo") {
    $("#montoPagarTarjeta").removeAttr("readonly", "readonly");
    $("#banco").removeAttr("readonly", "readonly");
    $("#op").removeAttr("readonly", "readonly");
  } else {
    $("#montoPagarTarjeta").attr("readonly", "readonly");
    $("#banco").attr("readonly", "readonly");
    $("#op").attr("readonly", "readonly");
    $("#banco").val("");
    $("#banco").select2("");
    $("#montoPagarTarjeta").val("");
    $("#op").val("");
  }
});

init();
