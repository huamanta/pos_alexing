var tabla;

function init() {
  $("#body").addClass("sidebar-collapse sidebar-mini");
  listar();
  $("#asistenciaModal").on("submit", function (e) {
    guardaryeditar(e);
  });
  $("#navPersonalActive").addClass("treeview active");
  $("#navPersonal").addClass("treeview menu-open");
  $("#navAsistencia").addClass("active");
  $("#fecha_inicio").change(historial);
  $("#fecha_fin").change(historial);
}

function registrarasis(idpersonal) {
  // Limpiar los campos del formulario
  $("#formulario")[0].reset();
  $("#idpersonal, #fecha, #hora_entrada, #hora_salida, #estado").prop(
    "readonly",
    false
  );
  $("#idpersonal").val(idpersonal);

  // Establecer la fecha actual para la zona horaria de Lima, Perú
  const options = {
    timeZone: "America/Lima",
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  };
  const today = new Intl.DateTimeFormat("es-PE", options).format(new Date());

  // Formatear la fecha de manera que se ajuste al formato ISO (YYYY-MM-DD)
  const [day, month, year] = today.split("/");
  const formattedDate = `${year}-${month}-${day}`;

  // Establecer la fecha en el campo de fecha
  $("#fecha").val(formattedDate);
  $("#hora_entrada").val("07:00");

  // Obtener la hora actual
  const now = new Date();
  const currentTime = now.getHours() * 60 + now.getMinutes(); // Hora actual en minutos
  const entradaTime = 7 * 60; // Hora de entrada a las 07:00 en minutos

  // Verificar si la hora actual es mayor que la hora de entrada
  if (currentTime > entradaTime) {
    const delayMinutes = currentTime - entradaTime; // Calcular los minutos de retraso
    Swal.fire({
      title: "Tardanza",
      icon: "warning",
      text: `La hora de entrada es a las 7:00 AM y llevas ${delayMinutes} minuto(s) de retraso.`,
    });
  }

  // Mostrar el modal
  $("#asistenciaModal").modal("show");
}

function cancelarform() {
  $("#formulario")[0].reset(); // quitar campo oculto si fue añadido dinámicamente
  $("#asistenciaModal").modal("hide");
}

function guardaryeditar(e) {
  e.preventDefault();
  var formData = new FormData($("#formulario")[0]);

  var idpersonal = $("#idpersonal").val();
  var estado = $("#estado").val();
  var hora_salida = $("#hora_salida").val();

  if (!idpersonal) {
    Swal.fire({
      title: "Error",
      icon: "error",
      text: "Por favor, seleccione un empleado.",
    });
    return;
  }

  // 🚨 Validación: si el estado es "asistio", hora_salida debe estar llena
  if (estado === "asistio" && (!hora_salida || hora_salida === "00:00")) {
    Swal.fire({
      title: "Hora de salida requerida",
      icon: "warning",
      text: "Debes seleccionar la hora de salida si el estado es 'asistió'.",
    });
    return; // 🛑 No se envía el formulario ni se cierra el modal
  }

  $.ajax({
    url: "controladores/asistencia.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (datos) {
      Swal.fire({
        title: "Asistencia",
        icon: "success",
        text: datos,
      });

      $("#asistenciaModal").modal("hide");
      tabla.ajax.reload();
    },
    error: function (error) {
      console.log(error.responseText);
    },
  });
}

$("#estado").on("change", function () {
  const estado = $(this).val();
  const horaSalidaInput = $("#hora_salida");

  if (estado === "asistio") {
    horaSalidaInput.prop("readonly", false);
    horaSalidaInput.prop("required", true);
  } else {
    horaSalidaInput.val(""); // limpia el campo
    horaSalidaInput.prop("readonly", true);
    horaSalidaInput.prop("required", false);
  }
});


function cargarAsistenciaParaEditar(idpersonal, fecha) {
  $.ajax({
    url: "controladores/asistencia.php?op=obtenerAsistencia",
    type: "POST",
    dataType: "json",
    data: { idpersonal: idpersonal, fecha: fecha },
    success: function (data) {
      if (data && !data.error) {
        // 1) Poblar valores
        $("#idasistencia").val(data.idasistencia);
        $("#idpersonal").val(data.idpersonal);
        $("#fecha").val(data.fecha);
        $("#hora_entrada").val(data.hora_entrada);
        $("#hora_salida").val(data.hora_salida);
        $("#estado").val(data.estado).trigger("change");
        $("#permiso").val(data.permiso).trigger("change");
        $("#vacaciones").val(data.vacaciones).trigger("change"); // Asegurarse de que el trigger sea 'change'
        $("#monto").val(data.monto);

        $("#asistenciaModal").modal("show");
      } else {
        Swal.fire(
          "Error",
          data.error || "No se encontró la asistencia",
          "error"
        );
      }
    },
    error: function (xhr, status, err) {
      console.error("AJAX error:", xhr.responseText);
      Swal.fire("Error", "No se pudo obtener la asistencia", "error");
    },
  });
}

function listar() {
  tabla = $("#tbllistado")
    .dataTable({
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
        url: "controladores/asistencia.php?op=listarpersonal",
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

function historial() {
  let fecha_inicio = $("#fecha_inicio").val();
  let fecha_fin = $("#fecha_fin").val();

  tabla = $("#tbllistado2")
    .dataTable({
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
        ["5 filas", "10 filas", "25 filas", "50 filas", "100 filas", "Mostrar todo"],
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
          titleAttr: "Mostrar/Ocultar Columnas",
        },
      ],
      ajax: {
        url: "controladores/asistencia.php?op=listarehistorial",
        type: "get",
        data: {
          fecha_inicio: fecha_inicio,
          fecha_fin: fecha_fin,
        },
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      columnDefs: [
        {
          targets: 0, // La primera columna es el checkbox
          orderable: false,
          searchable: false,
        },
      ],

      // 👉 Aquí se pinta la fila completa según el estado
      createdRow: function (row, data, dataIndex) {
        const estadoHTML = data[7]; // Ahora la columna 7 es donde va el estado con HTML

        if (estadoHTML.includes("Asistió")) {
          $(row).css("background-color", "#d4edda"); // Verde claro
        } else if (estadoHTML.includes("Faltó")) {
          $(row).css("background-color", "#f8d7da"); // Rojo claro
        }
      },

      bDestroy: true,
      iDisplayLength: 5,
      order: [[1, "desc"]], // Ahora se ordena por la segunda columna (Nombre)
    })
    .DataTable();
}


function pagarDia(idasistencia, idpersonal, idsucursal) {

  $.ajax({
      url: "controladores/pos.php?op=verificarCaja",
      type: "GET",
      data: "",
      success: function (data) {
        var json = JSON.parse(data);
        $('#idcaja').val(json.idcaja);
      }
  })
  
  $("#pagarAsistenciaModal").modal("show");
  $.post("controladores/venta.php?op=selectSucursal3", function (r) {
    $("#idsucursal2").html(r);
    $("#idsucursal2").select2();
    setTimeout(function () {
      $('#idsucursal2').val(idsucursal).trigger('change');
    }, 100);
  });

  $.post("controladores/usuario.php?op=selectEmpleado", function (r) {
    
    $("#idpersonal2").html(r);
    $("#idpersonal2").select2();

    setTimeout(function () {
        $('#idpersonal2').val(idpersonal).trigger('change');
      }, 100);
  });

  $.post(
    "controladores/cajachica.php?op=coceptoMovimiento&tipo=egresos",
    function (r) {
      $("#idconcepto_movimiento").html(r);
      $("#idconcepto_movimiento").select2();
    }
  );

  $('#idasistenciaEI').val(idasistencia);
}

// Nueva función para mostrar una asistencia específica (para edición)
function mostrarAsistencia(idasistencia) {
  $("#formulario")[0].reset();
  $.post(
    "controladores/asistencia.php?op=obtener_por_idasistencia",
    { idasistencia: idasistencia },
    function (data, status) {
      data = JSON.parse(data);
      if (data) {
        $("#idasistencia").val(data.idasistencia);
        $("#idpersonal").val(data.idpersonal);
        $("#fecha").val(data.fecha);
        $("#hora_entrada").val(data.hora_entrada);
        $("#hora_salida").val(data.hora_salida);
        $("#estado").val(data.estado).trigger("change");
        $("#hora_tardanza").val(data.tardanza);
        $("#permiso").val(data.permiso).trigger("change");
        $("#vacaciones").val(data.vacaciones).trigger("change");
        $("#monto").val(data.monto);

        $("#asistenciaModal").modal("show");
      } else {
        Swal.fire("Error", "No se encontró la asistencia", "error");
      }
    }
  );
}

// Nueva función para eliminar una asistencia
function eliminarAsistencia(idasistencia) {
  Swal.fire({
    title: "¿Está seguro de eliminar esta asistencia?",
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "controladores/asistencia.php?op=eliminar",
        { idasistencia: idasistencia },
        function (e) {
          Swal.fire("¡Eliminado!", e, "success");
          historial(); // Recargar la tabla
        }
      );
    }
  });
}

// Función para eliminar múltiples asistencias
function eliminarAsistenciasSeleccionadas() {
  const idasistencias = [];
  $("input[type='checkbox'].asistencia-checkbox:checked").each(function () {
    idasistencias.push($(this).val());
  });

  if (idasistencias.length === 0) {
    Swal.fire("Advertencia", "Seleccione al menos una asistencia para eliminar.", "warning");
    return;
  }

  Swal.fire({
    title: `¿Está seguro de eliminar ${idasistencias.length} asistencias seleccionadas?`,
    text: "¡No podrás revertir esto!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "controladores/asistencia.php?op=eliminar_multiple",
        { idasistencias: idasistencias.join(',') },
        function (e) {
          Swal.fire("¡Eliminado!", e, "success");
          historial(); // Recargar la tabla
          $("#btnEliminarSeleccionados").hide(); // Ocultar botón
          $("#selectAllAsistencias").prop("checked", false); // Desmarcar "Seleccionar todo"
        }
      );
    }
  });
}

$('#formularioPago').submit(function (e) {
    e.preventDefault();
    var formData = new FormData($(this)[0]);
    formData.set("idcaja", $("#idcaja").val());
    formData.set("idsucursal", $("#idsucursal").val());
    $.ajax({
      url: "controladores/cajachica.php?op=guardarPagoDiario",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      beforeSend: function () {
        $('#btnGuardar').attr('disabled', 'disabled');
        $('#btnGuardar').text('Guardando...');
      },
      success: function (datos) {
        console.log(datos);
        
        var jsonData = JSON.parse(datos);
        Swal.fire({
          title: "Movimiento",
          icon: jsonData.tipo,
          text: jsonData.mensaje,
        });
        $("#pagarAsistenciaModal").modal("hide");
        historial();
        mostrarCaja();
        $('#btnGuardar').removeAttr('disabled', 'disabled');
        $('#btnGuardar').text('Guardar');
      },
      error: function (error) {
        $('#btnGuardar').removeAttr('disabled', 'disabled');
        $('#btnGuardar').text('Guardar');
      }
    });
})

// Event listener para el botón de eliminar seleccionados
$("#btnEliminarSeleccionados").on("click", eliminarAsistenciasSeleccionadas);

// Event listener para el checkbox "Seleccionar todo"
$("#selectAllAsistencias").on("change", function () {
  const isChecked = $(this).prop("checked");
  $("input[type='checkbox'].asistencia-checkbox").prop("checked", isChecked);
  if (isChecked) {
    $("#btnEliminarSeleccionados").show();
  } else {
    $("#btnEliminarSeleccionados").hide();
  }
});

// Event listener para los checkboxes individuales
$("#tbllistado2 tbody").on("change", "input[type='checkbox'].asistencia-checkbox", function () {
  const anyChecked = $("input[type='checkbox'].asistencia-checkbox:checked").length > 0;
  if (anyChecked) {
    $("#btnEliminarSeleccionados").show();
  } else {
    $("#btnEliminarSeleccionados").hide();
  }
  // Si todos los checkboxes individuales están marcados, marcar también el "Seleccionar todo"
  const allChecked = $("input[type='checkbox'].asistencia-checkbox").length === $("input[type='checkbox'].asistencia-checkbox:checked").length;
  $("#selectAllAsistencias").prop("checked", allChecked);
});

function mostrarResumenAsistencia() {
  var fecha_inicio = $("#fecha_inicio").val();
  var fecha_fin = $("#fecha_fin").val();

  $.ajax({
    url: "controladores/asistencia.php?op=resumen_por_personal",
    type: "get",
    dataType: "json",
    data: {
      fecha_inicio: fecha_inicio,
      fecha_fin: fecha_fin,
    },
    success: function(response) {
      let data = response.aaData;
      let html = "";

      window.resumenAsistenciaDatos = [];

      data.forEach((row, index) => {
        html += `<tr>
          <td>${row[0]}</td>
          <td>${row[1]}</td>
          <td>${row[2]}</td>
          <td>${row[3]}</td>
          <td>${row[4]}</td>
          <td>${row[5]}</td>
          <td>${row[6]}</td>
          <td>
            <button class="btn btn-info btn-sm" onclick="verCalendarioAsistencia(${index})">
              <i class="fas fa-calendar-alt"></i>
            </button>
          </td>
        </tr>`;

        window.resumenAsistenciaDatos[index] = {
          nombre: row[0],
          dias_asistidos: row[7],
          horas_segundos: row[8]
        };
      });

      $("#tablaResumenAsistencia tbody").html(html);
      $("#modalResumenAsistencia").modal("show");
    },
    error: function(xhr) {
      console.log("Error:", xhr.responseText);
    }
  });
}
function verCalendarioAsistencia(index) {
  const datos = window.resumenAsistenciaDatos[index];
  const dias = datos.dias_asistidos.split(',');
  const horas = datos.horas_segundos.split(',').map(seg => Math.floor(seg / 3600));

  const fechaEjemplo = new Date(dias[0]);
  const year = fechaEjemplo.getFullYear();
  const month = fechaEjemplo.getMonth();

  let calendario = `<table class="table table-bordered text-center"><thead><tr>`;
  const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
  diasSemana.forEach(d => calendario += `<th>${d}</th>`);
  calendario += `</tr></thead><tbody><tr>`;

  const inicio = new Date(year, month, 1).getDay();
  for (let i = 0; i < inicio; i++) calendario += `<td></td>`;

  const totalDias = new Date(year, month + 1, 0).getDate();
  for (let d = 1; d <= totalDias; d++) {
    const fecha = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    const indexDia = dias.indexOf(fecha);
    const horasTrabajadas = indexDia !== -1 ? horas[indexDia] + 'h' : '';

    calendario += `<td class="${indexDia !== -1 ? 'bg-success text-white' : ''}">
      ${d}<br><small>${horasTrabajadas}</small>
    </td>`;

    if ((inicio + d) % 7 === 0) calendario += `</tr><tr>`;
  }

  calendario += `</tr></tbody></table>`;

  $("#tituloCalendario").html(`Calendario de Asistencia - ${datos.nombre}`);
  $("#contenedorCalendario").html(calendario);
  $("#modalCalendarioAsistencia").modal("show");
}



init();
