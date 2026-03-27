var tabla;

function init(){
	 $("#body").addClass("sidebar-collapse sidebar-mini");
    listar();
	limpiar();

    $("#myModal").on("submit",function(e)
	{
		guardaryeditar(e);	
	});

    //Mostramos los permisos
	$.post("controladores/usuario.php?op=permisos&id=",function(r){
	    $("#permisos").html(r);
	});

	//cargamos los items al select almacen
	$.post("controladores/venta.php?op=selectSucursal2", function(r){
		$("#idsucursal").html(r);
		$('#idsucursal').select2();
	});

	//Cargamos los items al select categoria
	$.post("controladores/usuario.php?op=selectEmpleado", function(r){
        $("#idpersonal").html(r);
        $('#idpersonal').select2();
	});
	
    $('#navPersonalActive').addClass("treeview active");
    $('#navPersonal').addClass("treeview menu-open");
    $('#navUsuario').addClass("active");

}

//Función limpiar
function limpiar()
{
	$("#idpersonal").val("");
	$("#login").val("");
	$("#clave").val("");
    $("#idsucursal").val(null).trigger('change');
	$("#idusuario").val("");
}

//Función cancelarform
function cancelarform()
{
	limpiar();
	$.post("controladores/usuario.php?op=permisos&id=",function(r){
		$("#permisos").html(r);
	});
}
function nuevoUsuario() {
    limpiar(); // Limpia todos los campos
    $('#myModal').modal('show');
    $('#n1').hide(); // Oculta el aviso de login en uso
    $('#idusuario').val(""); // Asegura que es nuevo
    $('#idpersonal').val('').trigger('change');

    // Carga permisos vacíos
    $.post("controladores/usuario.php?op=permisos&id=", function (r) {
        $("#permisos").html(r);
    });
}

function verificarUsuario(nombre){
    const idusuario = $("#idusuario").val();
    if(idusuario !== "") return; // No verificar login si estás editando

    $.post("controladores/usuario.php?op=verificarLogin&nombre=" + nombre, function(data) {
        data = JSON.parse(data);

        if (data != null) {
            $('#n1').show();
            $('#login').val("").focus();
        } else {
            $('#n1').hide();
        }
    });
}


function guardaryeditar(e)
{
	e.preventDefault(); //No se activará la acción predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "controladores/usuario.php?op=guardaryeditar",
	    type: "POST",
	    data: formData,
	    contentType: false,
	    processData: false,

	    success: function(datos)
	    {                  
	          Swal.fire({
				  title: 'Usuario',
				  icon: 'success',
					text:datos
				});
				
              $('#myModal').modal('hide');
	          tabla.ajax.reload();


	    }

	});
	limpiar();
}

function mostrar(idusuario)
{
    $.post("controladores/usuario.php?op=mostrar",{idusuario : idusuario}, function(data)
    {
        data = JSON.parse(data);

        $('#myModal').modal('show');
        $("#idpersonal").val(data.idpersonal).trigger('change');
        $("#login").val(data.login);
        $("#clave").val('');
        $("#idusuario").val(data.idusuario);

        // Cargar sucursales asignadas
        $.post("controladores/usuario.php?op=listarSucursalesUsuario&idusuario="+idusuario, function(r){
            let sucursales = JSON.parse(r);
            $("#idsucursal").val(sucursales).trigger('change');
        });
    }).always(function(){
        $.post("controladores/usuario.php?op=permisos&id="+idusuario,function(r){
            $("#permisos").html(r);
        });
    });
}

//Función Listar
function listar()
{
	tabla=$('#tbllistado').dataTable(
	{
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
		"ajax":
				{
					url: 'controladores/usuario.php?op=listar',
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"iDisplayLength": 5,//Paginación
	    "order": [[ 0, "desc" ]]//Ordenar (columna,orden)
	}).DataTable();
}

//Función para desactivar registros
function desactivar(idusuario)
{

	Swal.fire({
		title: '¿Desactivar?',
		text: "¿Está seguro Que Desea Desactivar el Usuario?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si'
		}).then((result) => {
		if (result.isConfirmed) {
			$.post("controladores/usuario.php?op=desactivar", {idusuario : idusuario}, function(e){
				Swal.fire(
					'Desactivado!',
					e,
					'success'
					)
				tabla.ajax.reload();
			});
		}else{
			Swal.fire(
				'Aviso!',
				"Se Cancelo la desactivacion de el Usuario",
				'info'
				)
		}
		})
		
}

//Función para desactivar registros
function activar(idusuario)
{

	Swal.fire({
		title: 'Activar?',
		text: "¿Está seguro Que Desea Activar el Usuario?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si'
		}).then((result) => {
		if (result.isConfirmed) {
			$.post("controladores/usuario.php?op=activar", {idusuario : idusuario}, function(e){
				Swal.fire(
					'Activado!',
					e,
					'success'
					)
				tabla.ajax.reload();
			});
		}else{
			Swal.fire(
				'Aviso!',
				"Se Cancelo la activación de el Usuario",
				'info'
				)
		}
		})
		
}

init();

