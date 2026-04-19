var tabla;

function init() {
    listar();
    $("#myModal").on("submit", function(e) {
        guardaryeditar(e);
    });
    $('#navAlmacenActive').addClass("treeview active");
    $('#navAlmacen').addClass("treeview menu-open");
    $('#navModelo').addClass("active");
}

function guardaryeditar(e) {
    e.preventDefault(); //No se activará la acción predeterminada del evento
    //$("#btnGuardar").prop("disabled",true);
    var formData = new FormData($("#formulario")[0]);
    $.ajax({
        url: "controladores/modelo.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            Swal.fire({
                title: 'Modelo',
                icon: 'success',
                text: datos
            });
            $('#myModal').modal('hide');
            tabla.ajax.reload();
        },
        error: function(error) {
            console.log(error.responseText);
        }
    });
    limpiar();
}

//Función para desactivar registros
function desactivar(idmodelo) {
    Swal.fire({
        title: '¿Desactivar?',
        text: "¿Está seguro Que Desea Desactivar el Modelo?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("controladores/modelo.php?op=desactivar", {idmodelo: idmodelo}, function(e) {
                Swal.fire(
                    'Desactivado!',
                    e,
                    'success'
                )
                tabla.ajax.reload();
            });
        } else {
            Swal.fire(
                'Aviso!',
                "Se Cancelo la desactivacion del Modelo",
                'info'
            )
        }
    })
}

//Función para activar registros
function activar(idmodelo) {
    Swal.fire({
        title: '¿Activar?',
        text: "¿Está seguro Que Desea Activar el Modelo?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("controladores/modelo.php?op=activar", {idmodelo: idmodelo}, function(e) {
                Swal.fire(
                    'Activado!',
                    e,
                    'success'
                )
                tabla.ajax.reload();
            });
        } else {
            Swal.fire(
                'Aviso!',
                "Se Cancelo la activación del Modelo",
                'info'
            )
        }
    })
}

function limpiar() {
    $("#nombre").val("");
    $("#descripcion").val("");
    $("#idmodelo").val("");
}

//Función cancelarform
function cancelarform() {
    limpiar();
}

function mostrar(idmodelo) {
    $.post("controladores/modelo.php?op=mostrar", {idmodelo: idmodelo}, function(data, status) {
        data = JSON.parse(data);
        $('#myModal').modal('show');
        $("#nombre").val(data.nombre);
        $("#descripcion").val(data.descripcion);
        $("#idmodelo").val(data.idmodelo);
    })
}

//Función Listar
function listar() {
    tabla = $('#tbllistado').dataTable({
        //"lengthMenu": [ 5, 10, 25, 75, 100],//mostramos el menú de registros a revisar
        "aProcessing": true, //Activamos el procesamiento del datatables
        "aServerSide": true, //Paginación y filtrado realizados por el servidor
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
                titleAttr: 'Exportar a Excel',
                // className: 'btn btn-success'
            },
            {
                extend: 'pdf',
                text: "<i class='fas fa-file-pdf'></i>",
                titleAttr: 'Exportar a PDF',
                // className: 'btn btn-danger'
            },
            {
                extend: 'colvis',
                text: "<i class='fas fa-bars'></i>",
                titleAttr: '',
                // className: 'btn btn-danger'
            }],
        "ajax": {
            url: 'controladores/modelo.php?op=listar',
            type: "get",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5, //Paginación
        "order": [[0, "desc"]] //Ordenar (columna,orden)
    }).DataTable();
}

init();