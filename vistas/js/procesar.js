function init(){

    $("#body").addClass("sidebar-collapse sidebar-mini");
    $("#formXml").on("submit", function(e){
        procesarXml(e);
    });

    // Activar menú (ajusta según tu menú real)
    $('#navProcesar').addClass("treeview active");
    $('#navProcesar').addClass("active");

    // Limpiar preview y formulario
	$("#btnLimpiar").on("click", function(){
	    // Limpiar preview del ticket
	    $("#previewTicket").html(`
	        <center class="text-muted">
	            <p>Sube un archivo XML para visualizar el ticket</p>
	        </center>
	    `);

	    // Reiniciar formulario
	    $("#formXml")[0].reset();
	});

}

document.getElementById('xml').addEventListener('change', function(e){
    const label = document.getElementById('xmlLabel');
    const fileName = e.target.files.length ? e.target.files[0].name : 'Ningún archivo seleccionado';
    label.textContent = fileName;
});
/**
 * Procesar XML y mostrar ticket
 */
function procesarXml(e)
{
    e.preventDefault();

    let formato = $('input[name="formato"]:checked').val();

    if (formato === 'pdf') {
        // 👉 PDF: abrir en nueva pestaña (NO AJAX)
        let form = document.getElementById('formXml');
        form.target = '_blank';
        form.action = 'controladores/procesar.php?op=convertir_xml';
        form.method = 'POST';
        form.submit();

        // restaurar
        form.target = '';
        return;
    }

    // 👉 HTML: AJAX normal
    let formData = new FormData($("#formXml")[0]);

    Swal.fire({
        title: 'Procesando XML',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    $.ajax({
        url: "controladores/procesar.php?op=convertir_xml",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            Swal.close();
            $("#previewTicket").html(response);
        },
        error: function() {
            Swal.close();
            Swal.fire('Error', 'No se pudo procesar el XML', 'error');
        }
    });
}


/**
 * Imprimir ticket
 */
function imprimirTicket() {
    let contenido = document.getElementById("previewTicket").innerHTML;

    if (contenido.trim() === '') {
        Swal.fire('Aviso', 'No hay ticket para imprimir', 'info');
        return;
    }

    let ventana = window.open('', 'PRINT', 'width=800,height=600,top=0,left=0,scrollbars=no,resizable=no');

    ventana.document.write(`
        <html>
            <head>
                <title>Ticket</title>
                <style>
                    @media print {
                        @page {
                            size: 80mm auto;
                            margin: 0;
                        }
                        body {
                            margin: 0;
                            padding: 0;
                        }
                        .ticket-container {
                            width: 80mm;
                            box-shadow: none;
                            border-radius: 0;
                            margin: 0;
                            padding: 5mm 2mm;
                        }
                    }

                    html, body {
                        margin: 0;
                        padding: 0;
                        font-family: monospace;
                        background: #f2f2f2;
                        display: flex;
                        justify-content: center;
                    }

                    .ticket-container {
                        width: 80mm;
                        background: #fff;
                        padding: 10px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        border-radius: 8px;
                    }
                </style>
            </head>
            <body>
                <div class="ticket-container">
                    ${contenido}
                </div>
                <script>
                    window.onload = function() {
                        window.focus();
                        window.print();
                        window.close();
                    };
                </script>
            </body>
        </html>
    `);

    ventana.document.close();
}

$('input[name="formato"]').on('change', function () {
    if (this.value === 'pdf') {
        $('#previewTicket').html(`
            <center class="text-muted">
                <p>El ticket PDF se generará sin vista previa</p>
            </center>
        `);
    }
});

init();
