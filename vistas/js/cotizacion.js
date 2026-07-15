var tabla;
var contador = 0;
var articuloAdd = "";
var cont = 0;
var detalles = 0;
var updateTimeout;
let listarProductos = null;

function init() {
    $("#body").addClass("sidebar-collapse sidebar-mini");
    mostrarform(false);
    listar();
    $.post("controladores/cotizaciones.php?op=selectComprobante", function (c) {
        $("#tipo_comprobante").html(c);
        $("#tipo_comprobante").select2('');
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
function setNavbarPosVisible(visible) {
    if (visible) {
        $("#navbar-pos").prop("hidden", false).show();
        return;
    }

    $("#navbar-pos").prop("hidden", true).hide();
}

$("#idcliente").select2({
    placeholder: "Buscar cliente...",
    allowClear: true,
    minimumInputLength: 2,

    ajax: {
        url: "controladores/venta.php?op=selectCliente",
        type: "POST",
        dataType: "json",
        delay: 250,

        data: function (params) {
            return {
                search: params.term,
                page: params.page || 1,
                only_client: 1
            };
        },

        processResults: function (data, params) {

            params.page = params.page || 1;

            return {
                results: data.data.map(function (item) {
                    return {
                        id: item.idpersona,
                        text: item.nombre + " - " + item.num_documento
                    };
                }),

                pagination: {
                    more: data.meta.current_page < data.meta.last_page
                }
            };
        },

        cache: true
    }
});

function nostock() {
    Swal.fire("Alerta", "Sin Stock", "info");
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
    $("#titulo").val("");
    $("#saludo").val("");
    $("#formapago").val('No').trigger('change');
    $("#tiempoproduccion").val('').trigger('change');
    $("#nota").val('').trigger('change');
    $("#igv").val('').trigger('change');
}


$("#numeroMeses").on("input", calcularCuotasDesdeNumeroMeses);
$("#input_frecuencia").on("change", calcularCuotasDesdeNumeroMeses);

function calcularCuotasDesdeNumeroMeses() {

    if ($("#formapago").val() !== "Si") {
        return;
    }

    const frecuencia = parseInt($("#input_frecuencia").val(), 10);

    if (!frecuencia || isNaN(frecuencia) || frecuencia <= 0) {
        return;
    }

    const mesesRaw = ("" + $("#numeroMeses").val() || "")
        .replace(",", ".")
        .trim();

    if (!mesesRaw) {
        $("#input_cuotas").val("");
        return;
    }

    const numeroMeses = parseFloat(mesesRaw);
    if (isNaN(numeroMeses) || numeroMeses <= 0) {
        $("#input_cuotas").val("");
        return;
    }

    const mesesPorCuota = {
        1: 1 / 30,
        2: 1 / 4,
        3: 1 / 2,
        4: 1,
        5: 2,
        6: 3,
        7: 6,
        8: 12,
    }[frecuencia];

    if (!mesesPorCuota) return;

    const cuotasCalculadas = Math.max(1, Math.ceil(numeroMeses / mesesPorCuota));
    const maxCuotasActuales = $("#input_cuotas option").length - 1;

    if (cuotasCalculadas > maxCuotasActuales) {
        generarCuotas(cuotasCalculadas);
    }

    $("#input_cuotas").val(String(cuotasCalculadas));
}


function obtenerFechaHoyISO() {
    const hoy = new Date();
    const yyyy = hoy.getFullYear();
    const mm = ("0" + (hoy.getMonth() + 1)).slice(-2);
    const dd = ("0" + hoy.getDate()).slice(-2);
    return yyyy + "-" + mm + "-" + dd;
}

function parseFecha(fecha) {
    if (fecha instanceof Date) {
        return new Date(fecha);
    }

    if (typeof fecha !== "string") {
        return new Date(fecha);
    }

    let texto = fecha.trim();
    if (!texto) {
        return new Date();
    }

    // MySQL datetime: 2024-05-17 14:30:00
    if (/^\d{4}-\d{2}-\d{2}(\s+\d{2}:\d{2}:\d{2})?$/.test(texto)) {
        return new Date(texto.replace(" ", "T"));
    }

    // Fecha con barras: 17/05/2024
    const partes = texto.split("/");
    if (partes.length === 3) {
        const [d, m, y] = partes;
        return new Date(`${y}-${m}-${d}`);
    }

    return new Date(texto);
}

function validarDatos({ cuotas, frecuencia, deuda }) {
    if (!frecuencia) {
        Swal.fire("Selecciona frecuencia de pago", "", "warning");
        return false;
    }

    if (!cuotas) {
        Swal.fire("Selecciona número de cuotas", "", "warning");
        return false;
    }

    if (!deuda || deuda <= 0) {
        Swal.fire("El crédito no puede ser menor o igual a 0", "", "warning");
        return false;
    }

    return true;
}

function calcularMontos(deuda, interes, cuotas) {
    let interesTotal = deuda * (interes / 100);
    let deudaTotal = deuda + interesTotal;
    let montoCuota = (deudaTotal / cuotas).toFixed(2);

    return {
        interesTotal,
        deudaTotal,
        montoCuota,
    };
}

function calcularCuotas() {

    // Si no se eligio cuotas manualmente, intentar autocalcular desde N° meses + frecuencia.
    if (!$("#input_cuotas").val()) {
        calcularCuotasDesdeNumeroMeses();
    }

    if ($('#formapago').val() === 'Si') {

    }

    let fechaOperacion = ("" + $("#fechaOperacion").val() || "").trim();
    if (!fechaOperacion) {
        fechaOperacion = obtenerFechaHoyISO();
        $("#fechaOperacion").val(fechaOperacion);
    }

    let data = {
        cuotas: parseInt($("#input_cuotas").val()),
        frecuencia: parseInt($("#input_frecuencia").val()),
        interes: parseFloat($("#inputInteres").val()),
        deuda: parseFloat($("#montoDeuda").val()),
        fechaBase: parseFecha(fechaOperacion),
    };

    if (!validarDatos(data)) return;

    let { montoCuota } = calcularMontos(data.deuda, data.interes, data.cuotas);

    let html = generarTabla(
        data.cuotas,
        data.frecuencia,
        data.fechaBase,
        data.deuda,
        data.interes,
    );

    $("#datafechas").html(html);
}

$("#calcular_cuotas").click(function (e) {
    e.preventDefault();
    calcularCuotas();
});

function sumarFrecuencia(fecha, frecuencia) {
    let nuevaFecha = new Date(fecha);
    frecuencia = parseInt(frecuencia, 10);

    switch (frecuencia) {
        case 1:
            nuevaFecha.setDate(nuevaFecha.getDate() + 1);
            break;
        case 2:
            nuevaFecha.setDate(nuevaFecha.getDate() + 7);
            break;
        case 3:
            nuevaFecha.setDate(nuevaFecha.getDate() + 15);
            break;
        case 4:
            nuevaFecha.setMonth(nuevaFecha.getMonth() + 1);
            break;
        case 5:
            nuevaFecha.setMonth(nuevaFecha.getMonth() + 2);
            break;
        case 6:
            nuevaFecha.setMonth(nuevaFecha.getMonth() + 3);
            break;
        case 7:
            nuevaFecha.setMonth(nuevaFecha.getMonth() + 6);
            break;
        case 8:
            nuevaFecha.setFullYear(nuevaFecha.getFullYear() + 1);
            break;
    }

    return nuevaFecha;
}

function generarCuotas(max = 100) {
    let select = $("#input_cuotas");
    let html = '<option value="" selected hidden>Seleccionar...</option>';

    for (let i = 1; i <= max; i++) {
        html += `<option value="${i}">${i}</option>`;
    }

    select.html(html);
}

function formatearFecha(fecha) {
    return (
        fecha.getFullYear() +
        "-" +
        ("0" + (fecha.getMonth() + 1)).slice(-2) +
        "-" +
        ("0" + fecha.getDate()).slice(-2)
    );
}


function generarTabla(cuotas, frecuencia, fechaBase, deuda, interes, input = true) {
    let html = "";
    frecuencia = parseInt(frecuencia, 10);
    let fechaTemp = parseFecha(fechaBase);
    const interesTotal = deuda * (interes / 100);
    const montoBase = deuda / cuotas;
    const interesPorCuota = interesTotal / cuotas;
    const totalCuota = montoBase + interesPorCuota;

    for (let i = 1; i <= cuotas; i++) {
        fechaTemp = sumarFrecuencia(fechaTemp, frecuencia);
        let fecha = formatearFecha(fechaTemp);

        html += `
      <tr>
        <td>${input ? `<input type="date" class="form-control" name="fecha_pago[]" value="${fecha}">` : fecha}</td>
        <td>S/. ${montoBase.toFixed(2)}</td>
        <td>S/. ${interesPorCuota.toFixed(2)}</td>
        <td>S/. ${totalCuota.toFixed(2)}</td>
      </tr>`;
    }

    return html;
}


$("#inicial").keyup(function () {
    var texto = parseFloat($(this).val() || 0);
    var total = parseFloat($("#total_venta").val()) || 0;
    if (texto > total) {
        $(this).val(total.toFixed(2));
        texto = total;
    };

    var restante = total - texto;
    $("#montoDeuda").val(restante.toFixed(2));
});


$("#formapago").change(function () {
    if ($(this).val() === "Si") {
        if (detalles === 0) {
            Swal.fire("Alerta", "Agrega al menos un producto para habilitar los datos de crédito.", "warning");
            $(this).val("No").trigger("change");
            return;
        }
        $("#datosCredito").removeAttr("hidden");
    } else {
        $("#datosCredito").attr("hidden", true);
    }
});

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
        $("#formapagom").val(data.formapago).trigger('change');
        $("#nrooperacionm").val(data.numoperacion);
        $("#fechadeposito").val(data.fechadeposito);
        $("#titulo").val(data.titulo);
        $("#nota").val(data.nota);
        $("#idventam").val(data.idventa);
        $("#inicial").val(data.inicial);
        $("#input_frecuencia").val(data.frecuencia);
        $("#numeroMeses").val(data.meses);
        $("#inputInteres").val(data.interes);

        if (data.formapago === "Si") {

            $("#panelCredito").removeAttr("hidden");
            $("#panel2").removeAttr("hidden");

            let total = parseFloat(data.total_venta) || 0;
            let inicial = parseFloat(data.inicial) || 0;
            let interes = parseFloat(data.interes) || 0;

            let deuda = total - inicial;

            if (deuda < 0) {
                deuda = 0;
            }

            const cuotas = generarNumCuotas(data.frecuencia, data.meses);
            const fechaInicio = parseFecha(data.fecha_h);
            const fechaInicioISO = `${fechaInicio.getFullYear()}-${("0" + (fechaInicio.getMonth() + 1)).slice(-2)}-${("0" + fechaInicio.getDate()).slice(-2)}`;

            $("#fechaOperacion").val(fechaInicioISO);
            $("#input_cuotas").val(cuotas);

            const tabla = generarTabla(cuotas, data.frecuencia, fechaInicio, deuda, interes, false);
            $("#textCredito").text(`S/ ${deuda.toFixed(2)}`);
            $("#textInicial").text(`S/ ${inicial.toFixed(2)}`);
            $("#textInteres").text(`${interes}%`);
            $("#textCuotas").text(`${cuotas}`);
            $("#dataCuotasCredito").html(tabla);
        } else {
            $("#dataCuotasCredito").html('');
            $("#panelCredito").attr("hidden", true);
            $("#panel2").attr("hidden", true);
        }
    });

    $.post("controladores/cotizaciones.php?op=listarDetalle&id=" + idcotizacion, function (r) {
        $("#detallesm").html(r);
    });
}


function generarNumCuotas(frecuencia, meses) {
    meses = parseInt(meses) || 0;

    let cuotas = 0;

    switch (parseInt(frecuencia)) {

        // DIARIO
        case 1:
            cuotas = meses * 30;
            break;

        // SEMANAL
        case 2:
            cuotas = meses * 4;
            break;

        // QUINCENAL
        case 3:
            cuotas = meses * 2;
            break;

        // MENSUAL
        case 4:
            cuotas = meses;
            break;

        // BIMESTRAL
        case 5:
            cuotas = Math.ceil(meses / 2);
            break;

        // TRIMESTRAL
        case 6:
            cuotas = Math.ceil(meses / 3);
            break;

        // SEMESTRAL
        case 7:
            cuotas = Math.ceil(meses / 6);
            break;

        // ANUAL
        case 8:
            cuotas = Math.ceil(meses / 12);
            break;

        default:
            cuotas = 1;
            break;
    }

    return cuotas;
}

function mostrarEditar(idcotizacion) {
    mostrarform(true);
    $.post("controladores/cotizaciones.php?op=mostrar", { idcotizacion: idcotizacion }, function (data, status) {
        data = JSON.parse(data);

        $.post("controladores/cotizaciones.php?op=listarDetalleCotizacion",
            { idcotizacion: idcotizacion },
            function (detalleData, status) {
                detalleData = JSON.parse(detalleData);
                detalleData.forEach(element => {

                    agregarDetalle(
                        element[0],   // idpc
                        element[1],   // idproducto
                        element[2],   // producto
                        element[3],   // cantidad
                        element[4],   // descuento
                        element[5],   // precio_venta
                        element[9],   // preciocigv
                        element[6],   // precioB
                        element[7],   // precioC
                        element[8],   // precioD
                        element[10],  // stock
                        element[11],  // proigv
                        element[13],  // cantidad_contenedor
                        element[14],  // contenedor
                        1             // idcategoria
                    );

                });
                detalles = detalleData.length;

                $("#listadoregistros").hide();
                $("#formularioregistros").show();
                listarProductos.load();
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
                $("#tiempoproduccion").val(data.tiempo_pro).trigger('change');
                $("#igv").val(data.igv).trigger('change');
                $("#inputInteres").val(data.interes).trigger('change');
                $("#inicial").val(data.inicial).trigger('change');
                $("#input_frecuencia").val(data.frecuencia).trigger('change');
                $("#numeroMeses").val(data.meses).trigger('change');
                $("#formapago").val(data.formapago);
                const fechaInicioEdicion = parseFecha(data.fecha_h || data.fecha);
                const fechaInicioEdicionISO = `${fechaInicioEdicion.getFullYear()}-${("0" + (fechaInicioEdicion.getMonth() + 1)).slice(-2)}-${("0" + fechaInicioEdicion.getDate()).slice(-2)}`;
                $("#fechaOperacion").val(fechaInicioEdicionISO);
                const deuda = parseFloat(data.total_venta) - parseFloat(data.inicial);
                $("#montoDeuda").val(deuda);
                if (data.formapago === "Si") {
                    $("#datosCredito").removeAttr("hidden");
                    calcularCuotas();
                }
            });
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
        success: function (response) {
            const data = JSON.parse(response);
            if (!data.success) {
                Swal.fire({
                    title: 'Cotización',
                    icon: 'error',
                    text: data.message
                });
                return;
            }
            Swal.fire({
                title: 'Cotización',
                icon: 'success',
                text: data.message
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
        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarTmp(0, ${filaId})">
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


const listarConfiguracionCreditos = () => {
    var idsucursal = $("#idsucursal").val();
    $.ajax({
        url: 'controladores/configuracion.php?op=listarConfiguracion',
        type: 'get',
        data: { idsucursal: idsucursal },
        dataType: 'json',
        success: function (s) {
            const configuracion = s.data.configuracion;
            $("#inputInteres").val(configuracion.interes_defecto);
        }
    });
}

// function listarArticulos() {
//     var idsucursal = $("#idsucursal").val();
//     tabla = $("#tblarticulos").dataTable({
//         aProcessing: true,
//         aServerSide: true,
//         dom: "Bfrtip",
//         buttons: [],
//         ajax: {
//             url: "controladores/cotizaciones.php?op=listarArticulos",
//             data: { idsucursal: idsucursal },
//             type: "get",
//             dataType: "json",
//             error: function (e) { console.log(e.responseText); },
//         },
//         bDestroy: true,
//         iDisplayLength: 5,
//         order: [[1, "asc"], [2, "asc"]]
//     }).DataTable();
// }

function pintarProductos(data, permissions) {

    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="10" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbody_productos").html(html);
        return;
    }

    data.forEach(item => {
        let btnActivarDesactivar = (permissions.desactivar) ?
            (item.condicion === 1) ?
                `<button class="btn btn-danger btn-xs" onclick="desactivar(${item.idproducto})"><i class="fas fa-times-circle"></i></button>` :
                `<button class="btn btn-info btn-xs" onclick="activar(${item.idproducto})"><i class="fas fa-check"></i></button>`
            : ''

        html += `
            <tr>
                <td>
                <button
                    class="btn btn-success"
                    onclick="agregarDetalle(
                        ${item.idproducto_configuracion},
                        ${item.idproducto},
                        '${item.nombre}',
                        1,
                        0,
                        ${item.precio},
                        '${item.preciocigv}',
                        '${item.precioB}',
                        '${item.precioC}',
                        '${item.precioD}',
                        '${item.stock}',
                        '${item.proigv}',
                        '${item.cantidad_contenedor}',
                        '${item.contenedor}',
                        ${item.idcategoria}
                    )"
                    ${parseFloat(item.stock) <= 0 ? 'disabled' : ''}>
                    <i class="fas fa-shopping-cart"></i>
                </button>
                <td>${item.codigo || ''}</td>
                <td style="text-align: left">${item.nombre || ''} M:${item.numero_serie || ''} S:${item.numero_motor || ''}</td>
                <td>${item.stock}</td>
                <td>S/ ${parseFloat(item.precio).toFixed(2)}</td>
                <td>
                    ${item.color || 'S/N'}
                </td>

            </tr>
        `;

    });

    $("#tbody_productos").html(html);

}


listarProductos = new FluentPaginator({
    url: "controladores/cotizaciones.php?op=listarArticulos",
    renderTabla: pintarProductos,
    searchSelector: "#searchProductos",
    limitSelector: "#limitProductos",
    paginationId: "#paginationProductos",
});

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
        $("#detalles_empty").hide();
    } else {
        $("#btnGuardar").hide();
        $("#detalles_empty").show();
        cont = 0;
    }
}

function eliminarTmp(idtmp, filaId) {
    if (idtmp === 0) {
        cont = cont - 1;
        detalles = detalles - 1;
        $(filaId).closest('tr').remove();
        evaluar();
        modificarSubtotales();
        return;
    }
}

function mostrarform(flag) {
    if (!flag) {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar, #btnNuevo, #header, #btnGuardar").show();
        return;
    }
    limpiar();

    $("#listadoregistros").hide();
    $("#formularioregistros").show();
    $("#btnagregar").hide();
    $("#btnGuardar").hide();
    $("#btnCancelar").show();
    detalles = 0;
    $("#btnAgregarArt, #btnAgregarArt2").show();
    $("#btnNuevo, #header").hide();
    listarProductos.load();
    listarArticulos2();
    setNavbarPosVisible(true);
    // cargarDatosTemporales();
    esperarSelect("#idsucursal", $("#idsucursal").val());
    setTimeout(() => {
        let idsucursal = $("#idsucursal").val();
        if (idsucursal) {
            numSerieTicket();
            numTicket();
            listarConfiguracionCreditos();
        }
    }, 300);

}
init();
