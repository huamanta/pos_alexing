var tabla;

function init() {
    listar();
    $("#myModal").on("submit", function(e) {
        guardaryeditar(e);
    });
    $('#navAlmacenActive').addClass("treeview active");
    $('#navAlmacen').addClass("treeview menu-open");
    $('#navCondicionVenta').addClass("active");
}

function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#formulario")[0]);
    $.ajax({
        url: "controladores/condicionventa.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            Swal.fire({
                title: 'Condición de Venta',
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

function desactivar(idcondicionventa) {
    Swal.fire({
        title: '¿Desactivar?',
        text: "¿Está seguro Que Desea Desactivar la Condición de Venta?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("controladores/condicionventa.php?op=desactivar", {idcondicionventa: idcondicionventa}, function(e) {
                Swal.fire('Desactivado!', e, 'success');
                tabla.ajax.reload();
            });
        } else {
            Swal.fire('Aviso!', "Se Cancelo la desactivacion de la Condición de Venta", 'info');
        }
    });
}

function activar(idcondicionventa) {
    Swal.fire({
        title: '¿Activar?',
        text: "¿Está seguro Que Desea Activar la Condición de Venta?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("controladores/condicionventa.php?op=activar", {idcondicionventa: idcondicionventa}, function(e) {
                Swal.fire('Activado!', e, 'success');
                tabla.ajax.reload();
            });
        } else {
            Swal.fire('Aviso!', "Se Cancelo la activación de la Condición de Venta", 'info');
        }
    });
}

function limpiar() {
    $("#nombre").val("");
    $("#idcondicionventa").val("");
}

function cancelarform() {
    limpiar();
}

function mostrar(idcondicionventa) {
    $.post("controladores/condicionventa.php?op=mostrar", {idcondicionventa: idcondicionventa}, function(data, status) {
        data = JSON.parse(data);
        $('#myModal').modal('show');
        $("#nombre").val(data.nombre);
        $("#idcondicionventa").val(data.idcondicionventa);
    });
}

function listar() {
    tabla = $('#tbllistado').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "processing": true,
        "language": {
            "processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />"
        },
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
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
            }
        ],
        "ajax": {
            url: 'controladores/condicionventa.php?op=listar',
            type: "get",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5,
        "order": [[0, "desc"]]
    }).DataTable();
}

init();