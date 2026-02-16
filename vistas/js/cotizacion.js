var tabla;
var contador = 0;
var articuloAdd = "";
var cont = 0;
var detalles = 0;
var updateTimeout;

function init() {
    $("#body").addClass("sidebar-collapse sidebar-mini");
    mostrarform(false);
    listar();
    $.post("controladores/cotizaciones.php?op=selectComprobante", function (c) {
        $("#tipo_comprobante").html(c);
        $("#tipo_comprobante").select2('');
    });
    $.post("controladores/venta.php?op=selectCliente", function (r) {
        $("#idcliente").html(r);
        $('#idcliente').select2('');
    });
    $.post("controladores/venta.php?op=selectSucursal", function (r) {
        $("#idsucursal").html(r);
        $('#idsucursal').select2('');
    });
    $.post("controladores/venta.php?op=selectSucursal3", function (r) {
        $("#idsucursal2").html(r);
    });
    $("#fecha_inicio").change(listar);
    $("#fecha_fin").change(listar);
    $("#idsucursal2").change(listar);
    $("#navPosActive").addClass("treeview active");
    $("#navPos").addClass("treeview menu-open");
    $('#navCotizaciones').addClass("active");
    window.addEventListener("keypress", function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    }, false);
}

function cargarDatosTemporales() {
    $.getJSON("controladores/cotizaciones.php?op=obtenerDatosTmp", function (data) {
        if (!data || !data.idcliente) {
            console.warn("No se encontraron datos temporales.");
            return;
        }
        $("#idsucursal").val(data.idsucursal).trigger("change");
        esperarSelect("#idcliente", data.idcliente);
        esperarSelect("#tipo_comprobante", data.tipo_comprobante);
        esperarSelect("#formapago", data.formapago);
        esperarSelect("#nota", data.nota);
        $("#serie_comprobante").val(data.serie_comprobante);
        $("#num_comprobante").val(data.num_comprobante);
        $("#titulo").val(data.titulo);
        $("#saludo").val(data.saludo);
        $("#igv").val(data.igv);
        $("#observaciones").val(data.observacion);
        $("#tiempoproduccion").val(data.tiempoproduccion);
        $("#total_venta").val(data.total_venta);
    });
}

function esperarSelect(selector, valor) {
    const $select = $(selector);
    if ($select.find("option[value='" + valor + "']").length > 0) {
        $select.val(valor).trigger("change");
    } else {
        setTimeout(() => esperarSelect(selector, valor), 100);
    }
}

$(function () {
    let timeoutGuardar;
    $(document).on("change keyup", "#idsucursal, #idcliente, #tipo_comprobante, #formapago, #titulo, #saludo, #nota, #observaciones, #tiempoproduccion, #total_venta",
        function () {
            clearTimeout(timeoutGuardar);
            timeoutGuardar = setTimeout(() => {
                const idsucursal = $("#idsucursal").val();
                const idcliente = $("#idcliente").val();
                const tipo_comprobante = $("#tipo_comprobante").val();

                if (!idsucursal || !idcliente || !tipo_comprobante) return;

                const datos = {
                    idsucursal,
                    idcliente,
                    tipo_comprobante,
                    serie_comprobante: $("#serie_comprobante").val() || "",
                    num_comprobante: $("#num_comprobante").val() || "",
                    titulo: $("#titulo").val() || "",
                    saludo: $("#saludo").val() || "",
                    nota: $("#nota").val() || "",
                    igv: $("#igv").val() || "0.00",
                    formapago: $("#formapago").val() || "",
                    observacion: $("#observaciones").val() || "",
                    tiempoproduccion: $("#tiempoproduccion").val() || "",
                    total_venta: $("#total_venta").val() || "0.00",
                };

                $.ajax({
                    url: "controladores/cotizaciones.php?op=guardarDatosTmp",
                    type: "POST",
                    data: datos,
                    success: function (response) { console.log("✅ Datos temporales guardados:", response); },
                    error: function (xhr, status, error) { console.error("Error al guardar datos temporales:", error); }
                });
            }, 500);
        });
});

function toggleCard() {
    var card = document.getElementById("datosgenerales");
    card.hidden = !card.hidden;
}

var fechaSpan = document.getElementById("fechaActual");
var fechaActual = new Date();
var diasSemana = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
var meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
var formatoFecha = diasSemana[fechaActual.getDay()] + ", " + fechaActual.getDate() + " de " + meses[fechaActual.getMonth()] + " de " + fechaActual.getFullYear() + ", " + (fechaActual.getHours() < 10 ? "0" : "") + fechaActual.getHours() + ":" + (fechaActual.getMinutes() < 10 ? "0" : "") + fechaActual.getMinutes();
fechaSpan.innerHTML = formatoFecha;

function toggleCard2() {
    var cardBody = document.getElementById("datosgenerales2");
    if (cardBody.style.display === "none" || cardBody.style.display === "") {
        cardBody.style.display = "block";
    } else {
        cardBody.style.display = "none";
    }
}

function limpiar() {
    $("#idcotizacion").val("");
    $("#serie_comprobante").val("");
    $("#num_comprobante").val("");
    articuloAdd = "";
    $("#total_venta").val("");
    $(".filas").remove();
    $("#total").html("0");
    $("#most_total").html("0");
    $("#most_imp").html("0");
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + (month) + "-" + (day);
    $("#fecha").val(today);
    $("#tipo_comprobante").val('Cotización').trigger('change');
    seleccionarCliente("PUBLICO EN GENERAL", 6);
    $("#titulo").val("");
    $("#saludo").val("");
    $("#formapago").val('Contado').trigger('change');
    $("#tiempoproduccion").val('').trigger('change');
    $("#nota").val('').trigger('change');
    $("#igv").val('').trigger('change');
}

function buscarProductoCod(e, codigo) {
    if (e.keyCode === 13) {
        if (codigo.length > 0) {
            $.post("controladores/venta.php?op=buscarProducto", { codigo: codigo }, function (data, status) {
                data = JSON.parse(data);
                if (data == null) {
                    alert("Producto no encontrado");
                } else {
                    agregarDetalle(data.idproducto, data.nombre, 1, 0, data.precio, data.preciocigv, data.precioB, data.precioC, data.precioD, data.stock, data.unidadmedida);
                }
                $("#idCodigoBarra").val("");
            });
        }
    }
}

function mostrar(idcotizacion) {
    $("#getCodeModal").modal('show');
    $.post("controladores/cotizaciones.php?op=mostrar", { idcotizacion: idcotizacion }, function (data, status) {
        data = JSON.parse(data);
        $("#cliente").val(data.cliente);
        $("#tipo_comprobantem").val(data.tipo_comprobante);
        $("#serie_comprobantem").val(data.serie_comprobante);
        $("#num_comprobantem").val(data.num_comprobante);
        $("#fecha_horam").val(data.fecha);
        $("#impuestom").val(data.impuesto);
        $("#formapagom").val(data.formapago);
        $("#nrooperacionm").val(data.numoperacion);
        $("#fechadeposito").val(data.fechadeposito);
        $("#titulo").val(data.titulo);
        $("#nota").val(data.nota);
        $("#idventam").val(data.idventa);
    });
    $.post("controladores/cotizaciones.php?op=listarDetalle&id=" + idcotizacion, function (r) {
        $("#detallesm").html(r);
    });
}

function mostrarEditar(idcotizacion) {
    mostrarform(true);
    $.post("controladores/cotizaciones.php?op=mostrar", { idcotizacion: idcotizacion }, function (data, status) {
        data = JSON.parse(data);
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        listarArticulos();
        listarArticulos2();
        $('#idcotizacion').val(data.idcotizacion);
        $('#nuevoVendedor').val(data.personal);
        $('#idcliente').val(data.idcliente).trigger('change');
        $('#fecha_hora').val(data.fecha);
        $('#serie_comprobante').val(data.serie_comprobante);
        $('#num_comprobante').val(data.num_comprobante);
        $('#tipo_comprobante').val(data.tipo_comprobante).trigger('change');
        $("#titulo").val(data.titulo);
        $("#nota").val(data.nota).trigger('change');
        $("#formapago").val(data.formapago).trigger('change');
        $("#tiempoproduccion").val(data.tiempo_pro).trigger('change');
        $("#igv").val(data.igv).trigger('change');
    });
    $.post("controladores/cotizaciones.php?op=listarDetalleCotizacion", { idcotizacion: idcotizacion }, function (data, status) {
        data = JSON.parse(data);
        for (var i = 0; i < data.length; i++) {
            agregarDetalle(data[i][0], data[i][1], data[i][2], data[i][3], data[i][4], data[i][5], data[i][6], data[i][7], data[i][8], data[i][9], data[i][10], data[i][12], data[i][13], data[i][14]);
        }
    });
}

function guardaryeditar() {
    var formData = new FormData($("#formulario")[0]);
    $.ajax({
        url: "controladores/cotizaciones.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            Swal.fire({
                title: 'Cotización',
                icon: 'success',
                text: datos
            });
            mostrarform(false);
            listar();
        }
    });
    limpiar();
}

function listar() {
    let fecha_inicio = $("#fecha_inicio").val();
    let fecha_fin = $("#fecha_fin").val();
    let idsucursal2 = $("#idsucursal2").val();
    tabla = $('#tbllistado').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "processing": true,
        "language": { "processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />" },
        "responsive": true, "lengthChange": false, "autoWidth": false,
        dom: '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>><"col-sm-12 col-md-4"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [[5, 10, 25, 50, 100, -1], ['5 filas', '10 filas', '25 filas', '50 filas', '100 filas', 'Mostrar todo']],
        buttons: ['pageLength', { extend: 'excelHtml5', text: "<i class='fas fa-file-csv'></i>", titleAttr: 'Exportar a Excel' }, { extend: 'pdf', text: "<i class='fas fa-file-pdf'></i>", titleAttr: 'Exportar a PDF' }, { extend: 'colvis', text: "<i class='fas fa-bars'></i>", titleAttr: '' }],
        "ajax": {
            url: 'controladores/cotizaciones.php?op=listar',
            data: { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin, idsucursal2: idsucursal2 },
            type: "get",
            dataType: "json",
            error: function (e) { console.log(e.responseText); }
        },
        "bDestroy": true,
        "iDisplayLength": 5,
        "order": [[0, "desc"]]
    }).DataTable();
}

function handleRowInput(element) {
    modificarSubtotales(); 

    clearTimeout(updateTimeout);
    updateTimeout = setTimeout(() => {
        const row = $(element).closest('tr');
        const idtmp = row.find('input[name="idtmp[]"]').val();
        const cantidad = row.find('input[name="cantidad[]"]').val();
        const precio_venta = row.find('input[name="precio_venta[]"]').val();

        if (idtmp && idtmp > 0) { 
            $.post("controladores/cotizaciones.php?op=actualizarTmp", {
                idtmp: idtmp,
                cantidad: cantidad,
                precio_venta: precio_venta
            }, function (response) {
                console.log("Sync:", response);
            });
        }
    }, 800);
}

function agregarDetalle(idpc, idproducto, producto, cant, desc, precio_venta, preciocigv, precioB, precioC, precioD, stock, proigv, cantidad_contenedor, contenedor, idcategoria) {
    if (articuloAdd.indexOf(idpc) != -1) {
        let cantInputs = document.getElementsByName("cantidad[]");
        let idpInputs = document.getElementsByName("idp[]");
        for (var i = 0; i < cantInputs.length; i++) {
            if (idpInputs[i].value == idpc) {
                let currentCant = parseFloat(cantInputs[i].value);
                cantInputs[i].value = currentCant + 1;
                handleRowInput(cantInputs[i]);
                return;
            }
        }
    }

    let cantidad = cant;
    if (idcategoria != 1 && stock < (cant * cantidad_contenedor)) {
        Swal.fire("Alerta", "No hay suficiente stock!", "error");
        return false;
    }

    let detail = contenedor ? contenedor + " x " + cantidad_contenedor + " Und." : "";
    let filaId = "fila" + cont;

    let fila = `
    <tr class="filas custom-row" id="${filaId}">
      <td>
        <input type="hidden" name="idtmp[]" value="">
        <input type="hidden" name="idproducto[]" value="${idproducto}">
        <input type="hidden" name="idp[]" value="${idpc}">
        <input type="hidden" name="contenedor[]" value="${contenedor}">
        <input type="hidden" name="cantidad_contenedor[]" value="${cantidad_contenedor}">
        <input class="form-control" type="text" name="nombreProducto[]" value="${producto}" style="font-weight:bold; width:300px;" onfocus="this.select()" />
      </td>
      <td style="text-align:center; vertical-align:middle;"><span class="badge bg-green">${detail}</span></td>
      <td class="text-center align-middle">
        <input class="form-control text-center" type="number" step="0.01" name="precio_venta[]" value="${precio_venta}" oninput="handleRowInput(this)" style="width:100px;">
      </td>
      <td style="text-align:center; vertical-align:middle;">
        <input class="form-control" type="number" min="1" name="cantidad[]" value="${cantidad}" style="text-align:center; width:80px; font-weight:bold; background-color:transparent; color:blue;" oninput="handleRowInput(this)">
      </td>
      <td hidden><input type="number" step="0.01" name="descuento[]" value="${desc}" hidden></td>
      <td style="text-align:center; vertical-align:middle;">S/. <span name="subtotal" style="font-weight:bold;"></span></td>
      <td style="text-align:center; vertical-align:middle;">
        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarTmp(0)">
          <i class="fa fa-trash"></i>
        </button>
      </td>
    </tr>
  `;

    $("#detalles").append(fila);
    modificarSubtotales();

    articuloAdd += idpc + "-";
    cont++;
    detalles++;
    evaluar();

    $.post("controladores/cotizaciones.php?op=agregarTmp", {
        idproducto: idproducto, cantidad: cantidad, precio_venta: precio_venta, descuento: desc, contenedor: contenedor, cantidad_contenedor: cantidad_contenedor, idp: idpc
    }, function (idtmp) {
        if (idtmp && !isNaN(idtmp)) {
            $("#" + filaId).find('input[name="idtmp[]"]').val(idtmp);
            $("#" + filaId).find('button').attr('onclick', `eliminarTmp(${idtmp})`);
        } else {
            toastr.error("Error al agregar item.");
            $("#" + filaId).remove();
        }
    });
}

function desistir(idcotizacion) {
    Swal.fire({
        title: "Desistir?",
        text: "¿Está seguro Que Desea Desistir la Cotización?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("controladores/cotizaciones.php?op=desistir", { idcotizacion: idcotizacion }, function (e) {
                Swal.fire('! Operación Exitosa !', e, 'success');
                tabla.ajax.reload();
            });
        } else {
            Swal.fire('! Cancelado ¡', "Se Cancelo la anulación de la Cotización", 'error')
        }
    })
}

function cancelarform() {
    limpiar();
    mostrarform(false);
}

function seleccionarCliente(nombre, idcliente) {
    $("#idcliente").val(idcliente);
    $("#idcliente").select2('');
}

function numTicket() {
    var idsucursal = $("#idsucursal").val();
    $.ajax({
        url: 'controladores/cotizaciones.php?op=mostrar_num_ticket',
        type: 'get',
        data: { idsucursal: idsucursal },
        dataType: 'json',
        success: function (d) {
            $("#num_comprobante").val(('0000000' + d).slice(-7));
            $("#nFacturas").html(('0000000' + d).slice(-7));
        }
    });
}

function numSerieTicket() {
    var idsucursal = $("#idsucursal").val();
    $.ajax({
        url: 'controladores/cotizaciones.php?op=mostrar_s_ticket',
        type: 'get',
        data: { idsucursal: idsucursal },
        dataType: 'json',
        success: function (s) {
            $("#numeros").html(('000' + s).slice(-3));
            $("#serie_comprobante").val(('000' + s).slice(-3));
        }
    });
}

function listarArticulos() {
    var idsucursal = $("#idsucursal").val();
    tabla = $("#tblarticulos").dataTable({
        aProcessing: true,
        aServerSide: true,
        dom: "Bfrtip",
        buttons: [],
        ajax: {
            url: "controladores/cotizaciones.php?op=listarArticulos",
            data: { idsucursal: idsucursal },
            type: "get",
            dataType: "json",
            error: function (e) { console.log(e.responseText); },
        },
        bDestroy: true,
        iDisplayLength: 5,
        order: [[1, "asc"], [2, "asc"]]
    }).DataTable();
}

function listarArticulos2() {
    var idsucursal = $("#idsucursal").val();
    tabla = $("#tblarticulos2").dataTable({
        aProcessing: true,
        aServerSide: true,
        dom: "Bfrtip",
        buttons: [],
        ajax: {
            url: "controladores/venta.php?op=listarArticulos2",
            data: { idsucursal: idsucursal },
            type: "get",
            dataType: "json",
            error: function (e) { console.log(e.responseText); },
        },
        bDestroy: true,
        iDisplayLength: 5,
        order: [[1, "asc"], [2, "asc"]]
    }).DataTable();
}

function modificarSubtotales() {
    let filas = document.querySelectorAll('.filas');
    let total = 0;
    filas.forEach(fila => {
        let cantidad = parseFloat(fila.querySelector('[name="cantidad[]"]').value) || 0;
        let precio = parseFloat(fila.querySelector('[name="precio_venta[]"]').value) || 0;
        let subtotal = cantidad * precio;
        let subtotalSpan = fila.querySelector('span[name="subtotal"]');
        if (subtotalSpan) {
            subtotalSpan.textContent = subtotal.toFixed(2);
        }
        total += subtotal;
    });
    $("#total").html(total.toFixed(2));
    $("#total_venta").val(total.toFixed(2));
    $("#most_total2").val(total.toFixed(2));
    $("#most_total").html(total.toFixed(2));
    $("#montoDeuda").val(total.toFixed(2));
    evaluar();
}

function evaluar() {
    if (detalles > 0) {
        $("#btnGuardar").show();
    } else {
        $("#btnGuardar").hide();
        cont = 0;
    }
}

function eliminarTmp(idtmp) {
    if (idtmp === 0) {
        // This case is for an item not yet saved, we can just remove from DOM
        // However, the button is disabled until the ajax call returns, so this is a fallback.
         $(this).closest('tr').remove();
         modificarSubtotales();
        return;
    }
    Swal.fire({
        title: "¿Eliminar producto?",
        text: "Se quitará del carrito temporal.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("controladores/cotizaciones.php?op=eliminarTmp", { idtmp: idtmp }, function (respuesta) {
                if (respuesta.includes("Eliminado")) {
                    toastr.warning(respuesta);
                } else {
                    toastr.error(respuesta);
                }
                listarTmp();
            }).fail(function (xhr) {
                toastr.error("Error de conexión: " + xhr.statusText);
            });
        }
    });
}

function mostrarform(flag) {
    limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnagregar").hide();
        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        detalles = 0;
        $("#btnAgregarArt, #btnAgregarArt2").show();
        $("#btnNuevo, #header").hide();
        listarArticulos();
        listarArticulos2();
        cargarDatosTemporales();
        listarTmp();
        esperarSelect("#idsucursal", $("#idsucursal").val());
        setTimeout(() => {
            let idsucursal = $("#idsucursal").val();
            if (idsucursal) {
                numSerieTicket();
                numTicket();
            }
        }, 300);
    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar, #btnNuevo, #header, #btnGuardar").show();
    }
}

function listarTmp() {
    $.getJSON("controladores/cotizaciones.php?op=listarTmp", function (data) {
        $("#detalles").html("");
        detalles = 0;
        articuloAdd = "";
        cont = 0;
        $.each(data.aaData, function (i, item) {
            let filaId = "fila" + cont;
            let fila = `
                <tr class="filas custom-row" id="${filaId}">
                  <td>
                    <input type="hidden" name="idtmp[]" value="${item.idtmp}">
                    <input type="hidden" name="idproducto[]" value="${item.idproducto}">
                    <input type="hidden" name="idp[]" value="${item.idp}">
                    <input type="hidden" name="contenedor[]" value="${item.contenedor ?? ''}">
                    <input type="hidden" name="cantidad_contenedor[]" value="${item.cantidad_contenedor ?? 0}">
                    <input class="form-control" type="text" name="nombreProducto[]" value="${item.nombre}" style="font-weight: bold; width: 300px;"/>
                  </td>
                  <td style="text-align: center; vertical-align: middle;"><span class="badge bg-green">${item.contenedor ?? ""}</span></td>
                  <td class="text-center align-middle">
                    <input class="form-control text-center" style="width:80px" type="number" step="0.01" name="precio_venta[]" value="${item.precio_venta}" oninput="handleRowInput(this)">
                    <input type="hidden" name="descuento[]" value="${item.descuento ?? 0}">
                  </td>
                  <td style="text-align: center; vertical-align: middle;">
                    <input class="form-control" style="text-align:center; width: 80px; background-color:transparent; color: blue; font-weight: bold;" type="number" min="1" name="cantidad[]" value="${item.cantidad}" oninput="handleRowInput(this)">
                  </td>
                  <td style="text-align: center; vertical-align: middle; width:100px">
                    S/. <span name="subtotal" style="text-align:center;font-size:14px;font-weight:bold"></span>
                  </td>
                  <td style="text-align: center; vertical-align: middle;">
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarTmp(${item.idtmp})"><i class="fa fa-trash"></i></button>
                  </td>
                </tr>
            `;
            $("#detalles").append(fila);
            detalles++;
            cont++;
            articuloAdd += item.idp + "-";
        });
        modificarSubtotales();
    });
}

init();
