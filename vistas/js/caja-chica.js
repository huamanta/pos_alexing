var tabla;
toastr.options = {
  closeButton: true,
  progressBar: true,
  positionClass: "toast-top-right",
  timeOut: "3000",
};
function init() {
  $("#body").addClass("sidebar-collapse sidebar-mini");
  listar();
  listarConceptos();
    mostrarCaja();
  
  verificarCaja();
  cargarIdAdelanto();
  cargarAsistenciaRapida();
  initTablaAsistencia();
  $("#myModal").on("submit", function (e) {
    guardaryeditar(e);
  });

  $("#navCajaChica").addClass("treeview active");
  $("#navCajaChica").addClass("active");

  $.post("controladores/venta.php?op=selectSucursal3", function (r) {
    $("#idsucursal2").html(r);
    $("#idsucursal2").select2("");
    mostrarCaja();
    $("#idsucursal2").on("change", function() {
      cargarVendedoresPorSucursal();
    });
    cargarVendedoresPorSucursal();
  });

  $.post("controladores/venta.php?op=selectVendedor", function (r) {
    $("#idvendedor").html(r);
    $("#idvendedor").select2("");
  });

  $.post("controladores/venta.php?op=selectSucursal", function (r) {
    $("#idsucursal").html(r);
    $("#idsucursal").select2("");
  });

  $.post("controladores/usuario.php?op=selectEmpleado", function (r) {
    $("#idpersonal").html(r);
    $("#idpersonal").select2("");
  });

  verificarConceptoMovimiento();

  $("#fecha_inicio").change(mostrarCaja);
  $("#fecha_fin").change(mostrarCaja);
  $("#idsucursal2").change(mostrarCaja);
  $("#idvendedor").change(mostrarCaja);

   $("#btnReporteAdelantos").on("click", function() {
    let desde = $("#fecha_inicio").val();
    let hasta = $("#fecha_fin").val();

    if (!desde || !hasta) {
      toastr.error("Debe seleccionar un rango de fechas.");
      return;
    }

    abrirReporteAdelantos(desde, hasta);
  });
}

function cargarVendedoresPorSucursal(runMostrarCaja = false) {
  var idsucursal = $("#idsucursal2").val();

  $.post("controladores/venta.php?op=selectVendedor", { idsucursal: idsucursal }, function (r) {
    $("#idvendedor").html(r);
    $("#idvendedor").select2("");

    if(runMostrarCaja) {
        mostrarCaja();
    }
  });
}


function crearMovimiento() {
  $("#myModal").modal("show");
  $("#idmovimiento").val('');
  limpiar();
}

function verificarConceptoMovimiento() {
  let tipo = "";

  if ($("#egresos").is(":checked")) {
    tipo = "egresos";
  } else if ($("#ingresos").is(":checked")) {
    tipo = "ingresos";
  }

  // Cargar los conceptos
  $.post(
    "controladores/cajachica.php?op=coceptoMovimiento&tipo=" + tipo,
    function (r) {
      $("#idconcepto_movimiento").html(r);
      $("#idconcepto_movimiento").select2();
    }
  );
}

function limpiar() {
  $("#egresos").prop("checked", true);
  $("#montoPagar").val("0");
  $("#descripcion").val("");
  $("#formapago").val("Efectivo");
  $("#totaldeposito").val("0");
  $("#noperacion").val("");
  setTimeout(function () {
    $("#idsucursal")
      .select2({
        placeholder: "Seleccione ...",
      })
      .val(21)
      .trigger("change");
    $("#idpersonal")
      .select2({
        placeholder: "Seleccione ...",
      })
      .val("")
      .trigger("change");
    $("#idconcepto_movimiento").select2().val("").trigger("change");
  }, 100);
}

/*function mostrar(idmovimiento)
{

  $("#myModal").modal('show');

  $.post("controladores/cajachica.php?op=mostrar",{idmovimiento : idmovimiento}, function(data, status)
  {
    data = JSON.parse(data);

    if (data.tipo == 'Egresos') {

      $('#egresos').prop("checked", true);

    } else {

      $('#ingresos').prop("checked", true);

    }

    $('#montoPagar').val(data.monto);
    $('#descripcion').val(data.descripcion);
    $("#idmovimiento").val(data.idmovimiento);

  })
}*/

function mostrar(idmovimiento) {
  $("#myModal").modal("show");
  limpiar();
  $.post(
    "controladores/cajachica.php?op=mostrar",
    { idmovimiento: idmovimiento },
    function (data, status) {
      data = JSON.parse(data);

      if (data.tipo == "Egresos") {
        $("#egresos").prop("checked", true);
      } else {
        $("#ingresos").prop("checked", true);
      }

      verificarConceptoMovimiento();

      $("#montoPagar").val(data.monto);
      $("#descripcion").val(data.descripcion);
      $("#idmovimiento").val(data.idmovimiento);
      setTimeout(function () {
          $("#idsucursal").select2().val(data.idsucursal).trigger("change");
          $("#idpersonal").select2().val(data.idpersonal).trigger("change");
          $("#idconcepto_movimiento").select2().val(data.idconcepto_movimiento).trigger("change");
        }, 200)
      $("#formapago").val(data.formapago);
      $("#totaldeposito").val(data.totaldeposito);
      $("#noperacion").val(data.noperacion);
    }
  );
}

function listar() {
  tabla = $("#tbllistado").DataTable({
    processing: true,
    serverSide: true,
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
      },
    ],
    ajax: {
      url: "controladores/cajachica.php?op=listar",
      type: "GET",
      dataType: "json",
      data: function (d) {
        d.fecha_inicio = $("#fecha_inicio").val();
        d.fecha_fin = $("#fecha_fin").val();
        d.idsucursal = $("#idsucursal2").val();
      },
      error: function (e) {
        console.log(e.responseText);
      },
    },
    bDestroy: true,
    iDisplayLength: 10,
    order: [[0, "desc"]],
  });
}

function verificarCaja() {
  return $.ajax({
    url: "controladores/venta.php?op=verificar_caja",
    type: "get",
    dataType: "json",
    success: function (response) {
      if (response.success) {
        // Asigna el id de la caja al campo oculto
        $("#idcaja").val(response.idcaja);
      } else {
        // Si no hay caja abierta, limpia el campo
        $("#idcaja").val("");
      }
    },
    error: function (err) {
      console.error("Error verificando caja:", err);
    },
  });
}

function validacionDeCampos() {
  var tipoPago = $("#formapago").val(); // Efectivo, Tarjeta, etc.
  var montoPago = $("#montoPagar").val(); // El monto ingresado
  if (tipoPago === "Efectivo" && (!montoPago || parseFloat(montoPago) <= 0)) {
    toastr.warning("El monto en efectivo es requerido");
    return;
  }
  return true;
}

function guardaryeditar(e) {
  e.preventDefault();
  let validate = validacionDeCampos();
  if (!validate) {
    return;
  }
  // Aseguramos que idcaja esté actualizado antes de enviar
  verificarCaja().done(function () {
    let formData = new FormData($("#formulario")[0]);
    // Añade el id de caja y sucursal al formData

    $.ajax({
      url: "controladores/cajachica.php?op=guardaryeditar",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      beforeSend: function () {
        $('#btnGuardar').attr('disabled', 'disabled');
        $('#btnGuardar').text('Guardando...');
      },
      success: function (datos) {
        Swal.fire({
          title: "Movimiento",
          icon: "success",
          text: datos,
        });
        $("#myModal").modal("hide");
        listar();
        mostrarCaja();
        $('#btnGuardar').removeAttr('disabled', 'disabled');
        $('#btnGuardar').text('Guardar');
      },
      error: function (error) {
        $('#btnGuardar').removeAttr('disabled', 'disabled');
        $('#btnGuardar').text('Guardar');
      }
    });
  });
}

function eliminar(idmovimiento) {
  Swal.fire({
    title: "Eliminar?",
    text: "¿Está seguro Que Desea Eliminar el Movimiento?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "controladores/cajachica.php?op=eliminar",
        { idmovimiento: idmovimiento },
        function (e) {
          Swal.fire("!!! Eliminado !!!", e, "success");
          tabla.ajax.reload();
          mostrarCaja();
        }
      );
    } else {
      Swal.fire(
        "! Cancelado ¡",
        "Se Cancelo la eliminación del Movimiento",
        "error"
      );
    }
  });
}

function mostrarCaja() {
  limpiar();
  let totalefectivo = 0;
  let totalefectivoTr = 0;
  $("#getCodeModal").modal("show");

  let fecha_inicio = $("#fecha_inicio").val();
  let fecha_fin = $("#fecha_fin").val();
  let idsucursal = $("#idsucursal2").val();
  idsucursal = idsucursal === "0" || idsucursal === null ? "Todos" : idsucursal;

  let idvendedor = $("#idvendedor").val();
  idvendedor = idvendedor === "0" || idvendedor === null ? "Todos" : idvendedor;

  $.post(
    "controladores/consultas.php?op=mostrarTotalSalidaTarjeta",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalSalTar");
      label.textContent = Number(data.total).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      totalTarjeta("res", data.total);
      // calcularTotalEnCaja();
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalSalidaEfectivo",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalSalEf");
      label.textContent = Number(data.total).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      totlEfectivo("res", data.total);
      // calcularTotalEnCaja();
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalTarjeta",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalTar");
      label.textContent = Number(data.total).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });

      totalTarjeta("sum", data.total);
      // calcularTotalEnCaja();
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalEgresosTar",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalETar");
      label.textContent = Number(data.totalEgresos).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      totalTarjeta("res", data.totalEgresos);
      // calcularTotalEnCaja();
    }
  );

  $.post(
    "controladores/consultas.php?op=totalTcomprass2",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },

    function (data, status) {
      data = JSON.parse(data);
      let label = document.querySelector("#totalSalidaT2");
      label.textContent = Number(data.total_compra).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalEfectivoSalida",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalEfectivoSalida");
      label.textContent = Number(data.total_compra).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalTransferenciaSalida",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalTransferenciaSalida");
      label.textContent = Number(data.total_compra).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });

      // calcularTotales();
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalCuentasPagarVentaCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#cuentasPagar");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalCuentasPagarVentaTCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#cuentasPagarT");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalBoletasCajaSalida",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#boletassalida");
      label.textContent = Number(data.total_compra).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalBoletasTCajaSalida",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#boletassalidaT");
      label.textContent = Number(data.total_compra).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalFacturasCajaSalida",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#facturassalida");
      label.textContent = Number(data.total_compra).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalFacturasTCajaSalida",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);
      let label = document.querySelector("#facturassalidaT");
      label.textContent = Number(data.total_compra).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalBoletasCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#boletas");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalBoletasTCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#boletasT");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalFacturasCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#facturas");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalFacturasTCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#facturasT");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalNotasVentaCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#notasVenta");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalNotasVentaTCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#notasVentaT");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );
  /////////////////////

  $.post(
    "controladores/consultas.php?op=mostrarTotalNotasCompraCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#notasCompra");
      label.textContent = Number(data.total_compra).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalNotasCompraTCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#notasCompraT");
      label.textContent = Number(data.total_compra).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=totalTickets",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
    },

    function (data, status) {
      data = JSON.parse(data);

      if (data.totalcuentacompra != 0) {
        let label = document.querySelector("#boleta_total_documentos_tick");
        label.textContent = data.totalcuentacompra;
      } else {
        let label = document.querySelector("#boleta_total_documentos_tick");
        label.textContent = null;
      }
    }
  );

  $.post(
    "controladores/consultas.php?op=totalFacturascount",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
    },

    function (data, status) {
      data = JSON.parse(data);

      if (data.totalcuentacompraf != 0) {
        let label = document.querySelector("#boleta_total_documentos_fac2");
        label.textContent = data.totalcuentacompraf;
      } else {
        let label = document.querySelector("#boleta_total_documentos_fac2");
        label.textContent = null;
      }
    }
  );

  $.post(
    "controladores/consultas.php?op=totalBoletascount",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
    },

    function (data, status) {
      data = JSON.parse(data);

      if (data.totalcuentacompraf != 0) {
        let label = document.querySelector("#boleta_total_documentos_bol2");
        label.textContent = data.totalcuentacompraf;
      } else {
        let label = document.querySelector("#boleta_total_documentos_bol2");
        label.textContent = null;
      }
    }
  );

  /////////////////////

  $.post(
    "controladores/consultas.php?op=mostrarTotalCuentasCobrarVentaCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#cuentasCobrar");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalCuentasCobrarVentaTCaja",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#cuentasCobrarT");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalEfectivo",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalEfectivo");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalTransferencia",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalTransferencia");
      label.textContent = Number(data.total_venta).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });

      // calcularTotales();
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalIngresos",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalI");
      label.textContent = Number(data.totalIngresos).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      totlEfectivo("sum", data.totalIngresos);
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalEgresos",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalE");
      label.textContent = Number(data.totalEgresos).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      totlEfectivo("res", data.totalEgresos);
      // calcularTotalEnCaja();
    }
  );

  $.post(
    "controladores/consultas.php?op=mostrarTotalIngresosTar",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalITar");
      label.textContent = Number(data.totalIngresos).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      totalTarjeta("sum", data.totalIngresos);
    }
  );

  $.post(
    "controladores/consultas.php?op=totalFacturas",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
    },
    function (data, status) {
      data = JSON.parse(data);

      if (data.totalcuentaventa != 0) {
        let label = document.querySelector("#boleta_total_documentos_fac");
        label.textContent = data.totalcuentaventa;
      } else {
        let label = document.querySelector("#boleta_total_documentos_fac");
        label.textContent = null;
      }
    }
  );

  $.post(
    "controladores/consultas.php?op=totalBoletas",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
    },
    function (data, status) {
      data = JSON.parse(data);

      if (data.totalcuentaventa != 0) {
        let label = document.querySelector("#boleta_total_documentos_bol");
        label.textContent = data.totalcuentaventa;
      } else {
        let label = document.querySelector("#boleta_total_documentos_bol");
        label.textContent = null;
      }
    }
  );

  $.post(
    "controladores/consultas.php?op=totalNotas",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
    },
    function (data, status) {
      data = JSON.parse(data);

      if (data.totalcuentaventa != 0) {
        let label = document.querySelector("#boleta_total_documentos_not");
        label.textContent = data.totalcuentaventa;
      } else {
        let label = document.querySelector("#boleta_total_documentos_not");
        label.textContent = null;
      }
    }
  );

  $.post(
    "controladores/consultas.php?op=totalCuentas",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
    },
    function (data, status) {
      data = JSON.parse(data);

      if (data.totalcuentacobrar != 0) {
        let label = document.querySelector("#boleta_total_documentos_cuentas");
        label.textContent = data.totalcuentacobrar;
      } else {
        let label = document.querySelector("#boleta_total_documentos_cuentas");
        label.textContent = null;
      }
    }
  );

  // XDDDDDDDDDDD

  $.post(
    "controladores/consultas.php?op=totalT",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalT");
      label.textContent = Number(data.totalI).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    }
  );

  /*$.post("controladores/consultas.php?op=totalEC",{fecha_inicio : fecha_inicio, fecha_fin : fecha_fin, idsucursal : idsucursal}, function(data,status)
  {

    data=JSON.parse(data);

    let label=document.querySelector('#totalEC');
    label.textContent=data.totalEC;

  });*/

  $.post(
    "controladores/consultas.php?op=mostrarTotalEfectivoC",
    {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
      idsucursal: idsucursal,
      idvendedor: idvendedor,
    },
    function (data, status) {
      data = JSON.parse(data);

      let label = document.querySelector("#totalEf");
      label.textContent = Number(data.total).toLocaleString("es-ES", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });

      totlEfectivo("sum", data.total);
      // calcularTotalEnCaja();
    }
  );

  function totlEfectivo(tipo, total) {
    if (tipo == "sum") {
      totalefectivo = parseFloat(totalefectivo) + parseFloat(total);
    } else if (tipo == "res") {
      totalefectivo = parseFloat(totalefectivo) - parseFloat(total);
    }

    let label = document.querySelector("#totalEC");
    label.textContent = parseFloat(totalefectivo).toFixed(2);
  }

  function totalTarjeta(tipo, total) {
    if (tipo == "sum") {
      totalefectivoTr = parseFloat(totalefectivoTr) + parseFloat(total);
    } else if (tipo == "res") {
      totalefectivoTr = parseFloat(totalefectivoTr) - parseFloat(total);
    }

    let label = document.querySelector("#totalET");
    label.textContent = parseFloat(totalefectivoTr).toFixed(2);
  }

  listar();
}

function calcularTotales() {
  let TotalFacturas = document.getElementById("facturas").innerHTML;

  let TotalFacturasT = document.getElementById("facturasT").innerHTML;

  let TotalF;

  TotalF = Number(TotalFacturas) + Number(TotalFacturasT);

  let labelTotalFacturas = document.querySelector("#totalF");
  labelTotalFacturas.textContent = TotalF.toFixed(2);

  if (TotalFacturas == 0.0 && TotalFacturasT == 0.0) {
    let c = 0;

    labelTotalFacturas.textContent = c.toFixed(2);
  }

  let TotalBoletas = document.getElementById("boletas").innerHTML;

  let TotalBoletasT = document.getElementById("boletasT").innerHTML;

  let TotalB;

  TotalB = Number(TotalBoletas) + Number(TotalBoletasT);

  let labelTotalBoletas = document.querySelector("#totalB");
  labelTotalBoletas.textContent = TotalB.toFixed(2);

  if (TotalBoletas == 0.0 && TotalBoletasT == 0.0) {
    let c = 0;

    labelTotalBoletas.textContent = c.toFixed(2);
  }

  let TotalNotas = document.getElementById("notasVenta").innerHTML;

  let TotalNotasT = document.getElementById("notasVentaT").innerHTML;

  let TotalN;

  TotalN = Number(TotalNotas) + Number(TotalNotasT);

  let labelTotalNotas = document.querySelector("#totalNotas");
  labelTotalNotas.textContent = TotalN.toFixed(2);

  if (TotalNotas == 0.0 && TotalNotasT == 0.0) {
    let c = 0;

    labelTotalNotas.textContent = c.toFixed(2);
  }

  let TotalCuentasCobrar = document.getElementById("cuentasCobrar").innerHTML;

  let TotalCuentasCobrarT = document.getElementById("cuentasCobrarT").innerHTML;

  let TotalCuentasC = 0;

  TotalCuentasC = Number(TotalCuentasCobrar) + Number(TotalCuentasCobrarT);

  let labelCuentasCobrar = document.querySelector("#totalCuentasCobrar");
  labelCuentasCobrar.textContent = TotalCuentasC.toFixed(2);

  if (TotalCuentasCobrar == 0.0 && TotalCuentasCobrarT == 0.0) {
    let c = 0;

    labelCuentasCobrar.textContent = c.toFixed(2);
  }

  let TotalEfectivo = document.getElementById("totalEfectivo").innerHTML;

  let TotalEfectivoT = document.getElementById("totalTransferencia").innerHTML;

  console.log("Eyyyy " + TotalEfectivo);

  let Total = 0;

  Total = Number(TotalEfectivo) + Number(TotalEfectivoT);

  let label = document.querySelector("#totalT");
  label.textContent = Total.toFixed(2);

  if (TotalEfectivo == 0.0 && TotalEfectivoT == 0.0) {
    let c = 0;

    label.textContent = c.toFixed(2);
  }
}

$("#formapago").change(function (e) {
  if ($(this).val() != "Efectivo") {
    $("#totaldeposito").removeAttr("readonly", "readonly");
    $("#noperacion").removeAttr("readonly", "readonly");
    $("#fecha_deposito").removeAttr("readonly", "readonly");
  } else {
    $("#totaldeposito").attr("readonly", "readonly");
    $("#noperacion").attr("readonly", "readonly");
    $("#fecha_deposito").attr("readonly", "readonly");
    $("#totaldeposito").val("0");
    $("#noperacion").val("0");
    $("#fecha_deposito").val("");
  }
});

// conceptos
function crearConcepto() {
  $("#myModalCocepto").modal("show");
  $("#formularioConcepto")[0].reset();
  $("#categoria_concepto").html('<option value="">Seleccione...</option>');
  $("#divCategoriaMov").attr("hidden", "hidden");
}


$("#tipo").change(function (e) {
  e.preventDefault();
  var tipo = $(this).val();
  condicioMovimiento(tipo);
  $("#idconcepto_movimiento_form").val("");
});

function condicioMovimiento(tipo) {
  var data = [];
  if (tipo === "ingresos") {
    //aqui para ingrsos
    $("#divCategoriaMov").attr("hidden", "hidden");
  } else {
    //aqui para egresos
    $("#divCategoriaMov").removeAttr("hidden", "hidden");
    data.push(
      {
        id: "presentado",
        name: "Presentado",
      },
      {
        id: "operativo",
        name: "Operativo",
      },
      {
        id: "personal",
        name: "Personal",
      }
    );
  }
  var html = '<option value="">Seleccione...</option>';
  data.forEach(function (item) {
    $("#idconcepto_movimiento").append(
      (html += `<option value="${item.id}">${item.name}</option>`)
    );
  });
  $("#categoria_concepto").html(html);
}

function listarConceptos() {
  tabla = $("#tbllistadoconceptos").DataTable({
    processing: true,
    serverSide: true,
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
      },
    ],
    ajax: {
      url: "controladores/cajachica.php?op=listarConceptos",
      type: "GET",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText);
      },
    },
    bDestroy: true,
    iDisplayLength: 10,
    order: [[0, "desc"]],
  });
}

function mostrarConcepto(obj) {
  $("#myModalCocepto").modal("show");

  // reset real
  $("#formularioConcepto")[0].reset();

  // primero seteamos el tipo
  $("#tipo").val(obj.tipo);

  // luego cargamos las categorías correctas
  condicioMovimiento(obj.tipo);

  // rellenamos datos
  $("#idconcepto_movimiento_form").val(obj.idconcepto_movimiento);
  $("#descripcion_concepto").val(obj.descripcion);

  // asignar categoría SOLO después de que existan las opciones
  $("#categoria_concepto").val(obj.categoria_concepto);
}


$("#formularioConcepto").submit(function (e) {
  e.preventDefault();
  let formData = new FormData(this);
  $.ajax({
    url: "controladores/cajachica.php?op=guardaryeditarConcepto",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      Swal.fire({
        title: "Exito!",
        text: response,
        icon: "success",
      });
      $("#myModalCocepto").modal("hide");
      listarConceptos();
      $("#formularioConcepto")[0].reset();
    },
  });
});

let ID_ADELANTO = null;

function cargarIdAdelanto() {
    $.get("controladores/cajachica.php?op=getIdConceptoAdelanto", function(r){
        let data = JSON.parse(r);
        ID_ADELANTO = data.idconcepto_movimiento;
    });
}

function nuevoAdelanto() {
    if (!ID_ADELANTO) {
        alert("No se encontró el concepto de adelanto.");
        return;
    }

    limpiar();
    $("#myModal").modal("show");

    $("#egresos").prop("checked", true);
    verificarConceptoMovimiento();

    setTimeout(() => {
        $("#idconcepto_movimiento").val(ID_ADELANTO).trigger("change");
    }, 200);

    $("#descripcion").val("Adelanto de sueldo");
}

function cargarIngresosSemana() {
    $.get("controladores/cajachica.php?op=listarIngresosSemana", {
        idpersonal: $("#idpersonal_semana").val(),
        desde: $("#fecha_ini").val(),
        hasta: $("#fecha_fin").val()
    }, function(r){

        let data = JSON.parse(r);
        let html = "";
        let total = data.total;

        data.detalle.forEach(d => {
            html += `
                <tr>
                    <td>${d.fecha}</td>
                    <td>${d.descripcion}</td>
                    <td>S/ ${d.monto}</td>
                </tr>
            `;
        });

        $("#detalle_ingresos").html(html);
        $("#total_ingresos").val(total);

        calcularTotales();
    });
}
function abrirRecibo(id) {

    $.get("controladores/cajachica.php?op=getMovimiento&idmovimiento=" + id, function(r){

        let data = JSON.parse(r);

        let html = `
            <div id="recibo_print" style="font-family: Arial;">

                <h2 style="text-align:center; margin:0;">MACHI MOTOR'S E.I.R.L.</h2>
                <h4 style="text-align:center; margin-top:0;">RUC: 20610209839</h4>
                <hr>

                <h3 style="text-align:center;">RECIBO DE MOVIMIENTO</h3>

                <p><b>Trabajador:</b> ${data.trabajador ?? '---'}</p>
                <p><b>Fecha:</b> ${data.fecha}</p>
                <p><b>Tipo:</b> ${data.tipo}</p>
                <p><b>Descripción:</b> ${data.descripcion}</p>

                <table class="table table-bordered">
                    <tr>
                        <th>Monto</th>
                        <td class="text-right">S/ ${data.monto}</td>
                    </tr>
                    <tr>
                        <th>Forma de pago</th>
                        <td>${data.formapago}</td>
                    </tr>
                </table>

                <br><br>

                <div style="display:flex; justify-content:space-between;">
                    <div style="text-align:center;">
                        ___________________________<br>
                        ENTREGUÉ CONFORME
                    </div>
                    <div style="text-align:center;">
                        ___________________________<br>
                        RECIBÍ CONFORME
                    </div>
                </div>

            </div>
        `;

        $("#recibo_content").html(html);
        $("#modalRecibo").modal("show");

    });
}
function imprimirModalRecibo() {
    let printContent = document.getElementById("recibo_print").innerHTML;

    let ventana = window.open("", "PRINT", "height=600,width=800");
    ventana.document.write(`
        <html>
            <head>
                <title>Recibo</title>
                <style>
                    body { font-family: Arial; padding:20px; }
                    table { width:100%; border-collapse: collapse; }
                    th, td { padding:8px; border:1px solid #000; }
                </style>
            </head>
            <body>${printContent}</body>
        </html>
    `);

    ventana.document.close();
    ventana.focus();
    ventana.print();
    ventana.close();
}

function mostrarReporteAdelantos(desde, hasta) {
    $.get(`controladores/cajachica.php?op=reporteAdelantos&desde=${desde}&hasta=${hasta}`, function(r) {
        let data = JSON.parse(r);

        // Cabecera de la tabla
        let html = `
            <h4>Reporte de Adelantos del ${desde} al ${hasta}</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Trabajador</th>
                        <th>Adelanto</th>
                        <th>Días Trabajados</th>
                    </tr>
                </thead>
                <tbody>
        `;

        data.detalle.forEach(item => {
            // Validar que monto sea número antes de usar toFixed
            let montoAdelanto = (item.adelanto && !isNaN(item.adelanto)) ? `S/ ${parseFloat(item.adelanto).toFixed(2)}` : "-";
            
            // Validar días trabajados
            let diasTrabajados = (item.dias_trabajados !== undefined && item.dias_trabajados !== null) ? item.dias_trabajados : "-";

            html += `
                <tr>
                    <td>${item.fecha}</td>
                    <td>${item.trabajador}</td>
                    <td class="text-right">${montoAdelanto}</td>
                    <td class="text-center">${diasTrabajados}</td>
                </tr>
            `;
        });

        // Total de adelantos
        let totalAdelantos = (data.total && !isNaN(data.total)) ? `S/ ${parseFloat(data.total).toFixed(2)}` : "S/ 0.00";

        html += `
                <tr>
                    <td colspan="2" class="text-right"><b>Total Adelantos</b></td>
                    <td class="text-right"><b>${totalAdelantos}</b></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        `;

        $("#recibo_content").html(html);
        $("#modalRecibo").modal("show");
    }).fail(function(err) {
        console.error("Error al cargar el reporte:", err);
        Swal.fire("Error", "No se pudo cargar el reporte de adelantos.", "error");
    });
}

function abrirReporteAdelantos(desde, hasta) {
    $.get(`controladores/cajachica.php?op=reporteAdelantos&desde=${desde}&hasta=${hasta}`, function(r) {
        let data = JSON.parse(r);

        let html = `
            <div id="recibo_print" style="font-family: Arial;">
                <h2 style="text-align:center; margin:0;">MACHI MOTOR'S E.I.R.L.</h2>
                <h4 style="text-align:center; margin-top:0;">RUC: 20610209839</h4>
                <hr>
                <h3 style="text-align:center;">REPORTE DE ADELANTOS</h3>
                <p><b>Periodo:</b> ${desde} al ${hasta}</p>
                
                <!-- TABLA DE ADELANTOS -->
                <h4><b>Adelantos Registrados</b></h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Trabajador</th>
                            <th>Descripción</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.detalle.forEach(item => {
            html += `
                <tr>
                    <td>${item.fecha}</td>
                    <td>${item.trabajador}</td>
                    <td>${item.descripcion}</td>
                    <td class="text-right">S/ ${parseFloat(item.monto).toFixed(2)}</td>
                </tr>
            `;
        });

        html += `
                <tr>
                    <td colspan="3" class="text-right"><b>Total Adelantos</b></td>
                    <td class="text-right"><b>S/ ${parseFloat(data.total).toFixed(2)}</b></td>
                </tr>
                </tbody>
                </table>
        `;

        /* -----------------------------------------------------
           NUEVA TABLA: DÍAS TRABAJADOS POR TRABAJADOR
        ----------------------------------------------------- */
        html += `
                <br>
                <h4><b>Días trabajados por trabajador</b></h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Trabajador</th>
                            <th>Días Trabajados</th>
                            <th>Total Pagos</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.dias.forEach(item => {
            html += `
                <tr>
                    <td>${item.trabajador}</td>
                    <td>
                        ${item.dias}
                        <i class="fa fa-calendar text-primary ml-2"
                           style="cursor:pointer;"
                           onclick='verCalendarioTrabajador(${JSON.stringify(item.fechas)})'>
                        </i>
                    </td>
                    <td class="text-right">S/ ${parseFloat(item.total_pago).toFixed(2)}</td>
                </tr>
            `;
        });

        html += `
                </tbody>
                </table>
            </div>
        `;

        $("#recibo_content").html(html);
        $("#modalRecibo").modal("show");
    });
}

let calendario = null;

function verCalendarioTrabajador(fechas) {
    $("#modalCalendario").modal("show");

    setTimeout(() => {

        let eventos = fechas.map(f => ({
            title: "S/ " + parseFloat(f.monto).toFixed(2),
            start: f.fecha,
            color: "#28a745",      // verde
            textColor: "#000",     // negro
            display: "block"       // ← esto muestra el texto en el cuadrito
        }));

        if (calendario) {
            calendario.destroy();
        }

        let calendarioEl = document.getElementById('calendario_trabajo');

        calendario = new FullCalendar.Calendar(calendarioEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            selectable: false,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: eventos,
            eventContent: function(info) {
                return {
                    html: `<div style="
                        font-size:13px; 
                        font-weight:bold; 
                        text-align:center;">
                        ${info.event.title}
                    </div>`
                };
            }
        });

        calendario.render();

    }, 200);
}

let tablaAsistencia;

function initTablaAsistencia() {

    tablaAsistencia = $("#tablaAsistenciaRapida").DataTable({
        "aProcessing": true,//Activamos el procesamiento del datatables
      "aServerSide": true,//Paginación y filtrado realizados por el servidor
      "processing": true,
      "language": 
    {          
    "processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
    },
        "responsive": true, "lengthChange": false, "autoWidth": false,
      dom: '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>><"col-sm-12 col-md-4"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    lengthMenu: [
            [5,10, 25, 50, 100, -1],
            ['5 filas','10 filas', '25 filas', '50 filas','100 filas', 'Mostrar todo']
        ],
        buttons: [
            "pageLength",
            {
                extend: "excelHtml5",
                text: "<i class='fas fa-file-csv'></i>",
                className: "btn btn-success btn-sm",
                titleAttr: "Exportar a Excel",
            },
            {
                extend: "pdf",
                text: "<i class='fas fa-file-pdf'></i>",
                className: "btn btn-danger btn-sm",
                titleAttr: "Exportar a PDF",
            },
            {
                extend: "colvis",
                text: "<i class='fas fa-bars'></i>",
                className: "btn btn-secondary btn-sm",
                titleAttr: "Mostrar / ocultar columnas",
            }
        ],

        pageLength: 10,
        ordering: false,

        language: {
    lengthMenu: "Mostrar _MENU_ registros",
    zeroRecords: "No se encontraron resultados",
    search: "Buscar:",
    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
    infoEmpty: "No hay registros",
    infoFiltered: "(filtrado de _MAX_ registros totales)",
    paginate: {
        first: "Primero",
        last: "Último",
        next: "<",
        previous: ">"
    }
}

    });

}


function cargarAsistenciaRapida() {
    let fecha = $("#fecha").val();

    $.post("controladores/asistencia.php?op=listarpersonal", { fecha }, function(resp) {

        let data = JSON.parse(resp);
        tablaAsistencia.clear();

        data.aaData.forEach(emp => {

            let nombre = emp[0];
            let activo = emp[5].includes("ACTIVADO");
            let idpersonal = emp[7];
            let idasistencia = emp[8];

            let checkboxUI = "";
            let estadoUI = "";
            let entradaUI = "";
            let salidaUI = "";
            let fechaUI = obtenerFechaActual();
            let montoUI = `<input type="number" class="form-control monto_dia" placeholder="0.00" step="0.10">`;

            if (!activo) {
                checkboxUI = `<span class="badge bg-danger">Inactivo</span>`;
                estadoUI = `<span class="badge bg-danger">No disponible</span>`;
                entradaUI = "-";
                salidaUI = "-";
                fechaUI = "-";
                montoUI = "-";

            } else if (idasistencia !== "") {
                checkboxUI = `<span class="badge bg-primary">Registrado</span>`;
                estadoUI = `<span class="badge bg-info">OK</span>`;
                entradaUI = "-";
                salidaUI = "-";
                fechaUI = `<span class="badge bg-info">Ya registrado</span>`;
                montoUI = "-";

            } else {
                checkboxUI = `<input type="checkbox" class="checkAsistencia">`;
                estadoUI = `
                    <select class="form-control estadoAsistencia">
                        <option value="asistio">Asistió</option>
                        <option value="falto">Faltó</option>
                    </select>`;
                entradaUI = `<input type="time" class="form-control hora_entrada" value="08:00">`;
                salidaUI = `<input type="time" class="form-control hora_salida" value="17:00">`;
                fechaUI = `<input type="date" class="form-control fecha_asistencia" value="${obtenerFechaActual()}">`;
            }

            let rowNode = tablaAsistencia.row.add([
                checkboxUI,
                nombre,
                estadoUI,
                fechaUI,
                entradaUI,
                salidaUI,
                montoUI        // NUEVO
            ]).draw(false).node();

            rowNode.dataset.idpersonal = idpersonal;
        });

        $("#tablaAsistenciaRapida tbody .checkAsistencia").off("change").on("change", function() {
            let total = $("#tablaAsistenciaRapida tbody .checkAsistencia").length;
            let checked = $("#tablaAsistenciaRapida tbody .checkAsistencia:checked").length;
            $("#seleccionarTodos").prop("checked", total === checked);
        });
    });
}

function obtenerFechaActual() {
    const f = new Date();
    return `${f.getFullYear()}-${String(f.getMonth() + 1).padStart(2, "0")}-${String(f.getDate()).padStart(2, "0")}`;
}

$("#guardarAsistenciaRapida").click((e) => {
    e.preventDefault();

    let total = 0;
    let totalChecks = $("#tablaAsistenciaRapida tbody .checkAsistencia:checked").length;

    if (totalChecks === 0) {
        Swal.fire("Aviso", "No seleccionaste ningún empleado", "warning");
        return;
    }

    $("#tablaAsistenciaRapida tbody tr").each(function() {

        let chk = $(this).find(".checkAsistencia");
        if (!chk.length || !chk.is(":checked")) return;

        let idpersonal = tablaAsistencia.row(this).node().dataset.idpersonal;
        let estado = $(this).find(".estadoAsistencia").val();
        let entrada = $(this).find(".hora_entrada").val();
        let salida  = $(this).find(".hora_salida").val();
        let fecha = $(this).find(".fecha_asistencia").val();
        let monto = $(this).find(".monto_dia").val();

        if (estado === "asistio") {
            if (!salida) {
                Swal.fire("Falta hora de salida", "Debe llenar la hora de salida", "warning");
                return;
            }
            if (!monto || monto <= 0) {
                Swal.fire("Monto requerido", "Debe ingresar el pago del día", "warning");
                return;
            }
        }

        $.ajax({
            url: "controladores/asistencia.php?op=guardaryeditar",
            type: "POST",
            data: {
                idpersonal: idpersonal,
                fecha: fecha,
                estado: estado,
                hora_entrada: entrada,
                hora_salida: salida,
                permiso: "no",
                vacaciones: "no",
                monto: monto     // NUEVO
            },
            success: function() {
                total++;
                if (total === totalChecks) {
                    Swal.fire("Éxito", "Asistencias registradas correctamente", "success");
                    cargarAsistenciaRapida();
                }
            }
        });
    });
});

$("#marcarTodos").click(() => {
    $(".checkAsistencia:enabled").prop("checked", true);
    $("#seleccionarTodos").prop("checked", true);
});

$("#desmarcarTodos").click(() => {
    $(".checkAsistencia").prop("checked", false);
    $("#seleccionarTodos").prop("checked", false);
});

$("#seleccionarTodos").click(function() {
    $(".checkAsistencia").prop("checked", $(this).is(":checked"));
});

// Solución al warning aria-hidden + foco retenido
$(document).on('hide.bs.modal', function () {
    setTimeout(() => document.activeElement.blur(), 10);
});

$("#btnExportarExcel").on("click", function () {
    let fechaInicio = $("#fecha_inicio").val();
    let fechaFin = $("#fecha_fin").val();

    if (!fechaInicio || !fechaFin) {
        Swal.fire("Faltan fechas", "Debe seleccionar fecha inicio y fin", "warning");
        return;
    }

    window.location = "controladores/exportar_excel.php?inicio=" + fechaInicio + "&fin=" + fechaFin;
});

init();
