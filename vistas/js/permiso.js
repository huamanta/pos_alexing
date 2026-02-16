var tabla;

function init() {
    listar();

    $("#formulario").on("submit", function(e) {
        guardaryeditar(e);
    });
    $('.modal').on('hidden.bs.modal', function () {
      if ($('.modal.show').length > 0) {
        $('body').addClass('modal-open');
      }
    });

}

function limpiar() {
    $("#idpermiso").val("");
    $("#nombre").val("");
    $("#nombreSubpermiso").val("");
    $("#bloqueSubpermisos").hide();
}

function listar() {
    tabla = $('#tblListado').DataTable({
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
                titleAttr: 'Exportar a Excel'
            },
            {
                extend: 'pdf',
                text: "<i class='fas fa-file-pdf'></i>",
                titleAttr: 'Exportar a PDF'
            },
            {
                extend: 'colvis',
                text: "<i class='fas fa-bars'></i>",
                titleAttr: ''
            }],
        "ajax": {
            url: 'controladores/permiso.php?op=listar',
            type: "get",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5,
        "order": [[0, "desc"]]
    });
}

function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "controladores/permiso.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function(datos) {
            Swal.fire({
                title: 'Permiso',
                icon: 'success',
                text: datos,
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#modalFormulario').modal('hide');
                    limpiar();
                    tabla.ajax.reload(null, false);
                }
            });
        },
        error: function(xhr, status, error) {
            console.log("Error en la solicitud AJAX:", error);
        }
    });
}

function mostrar(idpermiso) {
    limpiar();
    $("#modalFormulario").modal("show");

    $.post("controladores/permiso.php?op=mostrar", { idpermiso: idpermiso }, function(data) {
        data = JSON.parse(data);
        $("#idpermiso").val(data.idpermiso);
        $("#nombre").val(data.nombre);

        // Mostrar subpermisos
        $("#bloqueSubpermisos").show();
        $("#idpermiso_sub").val(data.idpermiso);
        listarSubpermisos(data.idpermiso);
    });
}

function eliminar(idpermiso) {
    Swal.fire({
        title: '¿Está seguro de eliminar el permiso?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("controladores/permiso.php?op=eliminar", { idpermiso: idpermiso }, function(respuesta) {
                Swal.fire({
                    title: 'Eliminado',
                    icon: 'success',
                    text: respuesta,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    tabla.ajax.reload(null, false);
                });
            });
        }
    });
}

// SUBPERMISOS

function mostrarSubpermisos(idpermiso) {
    cargarPermisosEnSelect();
    $('#selectModulo').val(idpermiso);
    listarSubpermisos(idpermiso);
    $('#bloqueSubpermisos').show();
}

function registrarSubpermiso(idpermiso) {
  var nombreSubpermiso = $("#nombre_subpermiso").val();

  if (nombreSubpermiso.trim() === "") {
    Swal.fire({
      icon: 'warning',
      title: 'Campo vacío',
      text: 'Ingrese un nombre para el subpermiso.'
    });
    return;
  }

  $.post("controladores/permiso.php?op=insertarsubpermiso", {
    idpermiso: idpermiso,
    nombre: nombreSubpermiso
  }, function (respuesta) {
    Swal.fire({
      icon: 'success',
      title: 'Registro exitoso',
      text: respuesta
    });
    listarSubpermisos(idpermiso); // Refresca la tabla
    $("#nombre_subpermiso").val(""); // Limpia el campo
  });
}

function listarSubpermisos(idpermiso) {
    $.post("controladores/permiso.php?op=listarsubpermiso", { idpermiso }, function(data) {
        let tabla = $("#tablaSubpermisos tbody");
        tabla.empty();
        let lista = JSON.parse(data);
        lista.forEach(function(s) {
            tabla.append(`
                <tr>
                    <td>${s.idsubpermiso}</td>
                    <td>${s.modulo}</td>
                    <td>${s.nombre}</td>
                    <td>
                      <button type="button" class="btn btn-info btn-sm" onclick="abrirModalAcciones(${s.idsubpermiso})">Acciones</button>
                      <button class="btn btn-danger btn-sm" onclick="eliminarSubpermiso(${s.idsubpermiso}, ${idpermiso})">Eliminar</button>
                    </td>
                </tr>
            `);
        });
    });
}

function eliminarSubpermiso(idsubpermiso, idpermiso) {
    if (confirm("¿Seguro que deseas eliminar este subpermiso?")) {
        $.post("controladores/permiso.php?op=eliminarsubpermiso", { idsubpermiso }, function(response) {
            alert(response);
            listarSubpermisos(idpermiso);
        });
    }
}

function abrirModalAcciones(idsubpermiso) {
  event?.stopPropagation(); // <- solo si lo necesitas

  $("#idsubpermiso_accion").val(idsubpermiso);
  $("#formularioAccion")[0].reset();
  listarAcciones(idsubpermiso);
  $("#modalAcciones").modal("show");
}

function registrarAccion(e) {
  e.preventDefault();
  let formData = new FormData($("#formularioAccion")[0]);
  $.ajax({
    url: "controladores/permiso.php?op=insertaraccion",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function(response) {
      listarAcciones($("#idsubpermiso_accion").val());
      $("#formularioAccion")[0].reset();
    }
  });
}


function listarAcciones(idsubpermiso) {
  $.post("controladores/permiso.php?op=listaracciones", { idsubpermiso }, function(data) {
    let tbody = $("#tablaAcciones tbody");
    tbody.empty();
    let acciones = JSON.parse(data);
    acciones.forEach(function(a) {
      tbody.append(`
        <tr>
          <td>${a.idaccion_permiso}</td>
          <td>${a.nombre}</td>
          <td>${a.descripcion}</td>
          <td>
            <button class="btn btn-danger btn-sm" onclick="eliminarAccion(${a.idaccion_permiso}, ${idsubpermiso})">Eliminar</button>
          </td>
        </tr>
      `);
    });
  });
}

function eliminarAccion(idaccion_permiso, idsubpermiso) {
  Swal.fire({
    title: '¿Eliminar esta acción?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar'
  }).then(result => {
    if (result.isConfirmed) {
      $.post("controladores/permiso.php?op=eliminaraccion", { idaccion_permiso: idaccion_permiso }, function(resp) {
        Swal.fire({ icon: 'success', title: 'Acción eliminada', text: resp });
        listarAcciones(idsubpermiso);
      });
    }
  });
}


init();
