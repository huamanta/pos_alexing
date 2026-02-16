function init(){

    listar();

}

function listar(){

    $.post("controladores/negocio.php?op=mostrarNombreNegocio", function(data,status)
	{

		data=JSON.parse(data);

        let label=document.querySelector('#nombreNegocio');
		label.textContent=data.nombre;
			

	});

}



init();