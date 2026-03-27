function initializeContratos(){
    listar();
};


function listar() {
  let fecha_inicio = $("#fecha_inicio").val();
  let fecha_fin = $("#fecha_fin").val();
  var estado = $("#estado").val();
  let idsucursal2 = $("#idsucursal2").val();
  let idproducto = $("#idproducto").val();

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
        url: "controladores/contratos.php?op=listar",
        data: {
          fecha_inicio: fecha_inicio,
          fecha_fin: fecha_fin,
          estado: estado,
          idsucursal2: idsucursal2,
          idproducto: idproducto,
        },
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


initializeContratos();

function verContrato(idcontrato) {
  $('#modal-ver-contrato').modal('show');
  $('#idcontrato').val(idcontrato);
}

function encrypt_decrypt(action, string) {
  if (action === 'encrypt') {
    // Encriptación simple pero efectiva para este caso
    const encoded = btoa(string);
    return encoded.replace(/=/g, '').replace(/\//g, '_').replace(/\+/g, '-');
  }
  return string;
}

function descargarContrato(idcontrato) {
  const encryptedId = encrypt_decrypt('encrypt', idcontrato);
  const url = 'public/docs_service/contrato?contrato=' + encryptedId; // Sin .php

  const win = window.open(url, '_blank');
  if (!win) {
    alert('Por favor habilita ventanas emergentes o descarga manualmente: ' + url);
    return;
  }
}

$(document).ready(function () {
  $('#btnDescargarContrato').on('click', function () {
    const idcontrato = $('#idcontrato').val();
    descargarContrato(idcontrato);
  });
});
