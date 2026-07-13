var tabla;
var paginaActual = 1;
var limite = 10;
let paginatorSolicitudes = null;
let paginatorMisSolicitudes = null;
let paginatorTraslados = null;
let sucursales = null;


$('#navAlmacenActive').addClass("treeview active");
$('#navAlmacen').addClass("treeview menu-open");
$('#navTraslado').addClass("active");

function init() {
    paginatorSolicitudes.load();
    paginatorMisSolicitudes.load();
    paginatorTraslados.load();

    $("#formTraslado").on("submit", function (e) {
        guardaryeditar(e);
    });

    $.get("controladores/traslado.php?op=listarSucursales", function (response) {
        sucursales = JSON.parse(response);
        let html = '<option value="Todos">Todos</option>';
        sucursales.map((item, i) => {
            html += `<option value="${item.idsucursal}">${item.nombre}</option>`;
        });
        $("#origenSolicitudes").html(html);
    });

    cargarAlmacenes();

    configurarBotones();
}

$("#estadoSolicitudes").on("change", function () {
    paginatorSolicitudes.load();
});

$("#fecha_inicio, #fecha_fin, #estadoMisSolicitudes").on("change", function () {
    paginatorMisSolicitudes.load();
});

//==============================
// GUARDAR O EDITAR
//==============================
function guardaryeditar(e) {
    e.preventDefault();

    const idorigen = $("#idorigen").val();
    const iddestino = $("#iddestino").val();

    if (!iddestino || iddestino === "") {
        Swal.fire("Atención", "Debe seleccionar un almacén destino", "warning");
        return;
    }

    if (iddestino == idorigen) {
        Swal.fire("Atención", "El almacén destino debe ser distinto al de origen", "warning");
        return;
    }

    let productos = [];
    let valida = true;
    $("#tablaDetalle tbody tr").each(function () {
        let idproducto = $(this).data("idproducto");
        let cantidad = parseInt($(this).find(".cantidad").val()) || 0;
        let stock = parseInt($(this).data("stock")) || 0;
        let idserie = parseInt($(this).data("idserie")) || 0;
        let nombre = $(this).find("td").first().text().trim();

        if (!idproducto || cantidad <= 0) {
            Swal.fire("Atención", "Cantidad inválida en algún producto", "warning");
            valida = false;
            return false; // break each
        }

        // if (cantidad > stock) {
        //     Swal.fire("Atención", `La cantidad solicitada (${cantidad}) supera el stock disponible (${stock}) para: ${nombre}`, "warning");
        //     valida = false;
        //     return false;
        // }

        productos.push({ idproducto, idserie, cantidad });
    });

    if (!valida) return;

    if (productos.length === 0) {
        Swal.fire("Atención", "Debe agregar al menos un producto", "warning");
        return;
    }

    var formData = new FormData($("#formTraslado")[0]);
    formData.append("productos", JSON.stringify(productos));

    $.ajax({
        url: "controladores/traslado.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            const data = JSON.parse(response);
            if (data.success != true) {
                Swal.fire({ title: 'Traslado', icon: 'error', text: data.message });
                return;
            }
            Swal.fire({ title: 'Traslado', icon: 'success', text: data.message });
            $('#modalTraslado').modal('hide');
            paginatorTraslados.load();
            limpiar();
        },
        error: function (error) {
            console.log(error.responseText);
            Swal.fire("Error", "Ocurrió un error en el servidor.", "error");
        }
    });
}

//==============================
// BOTONES Y EVENTOS
//==============================
function configurarBotones() {

    $("#btnBuscarProducto").click(function () {
        const texto = $("#buscarProducto").val();
        let tipo = $("#tipoModal").val();
        let idsucursal = $("#iddestino").val();
        if (tipo === 'traslado') {
            idsucursal = $("#idorigen").val();
        }
        listarProductos(texto, idsucursal);
    });

    $("#buscarProducto").keyup(function (e) {
        const texto = $(this).val();
        let tipo = $("#tipoModal").val();
        let idsucursal = $("#iddestino").val();
        if (tipo === 'traslado') {
            idsucursal = $("#idorigen").val();
        }
        listarProductos(texto, idsucursal);
    });

    $("#btnAgregarSeleccionados").click(function () {
        $("#tablaProductos tbody input.chkProducto:checked").each(function () {
            let idproducto = $(this).data('idproducto');
            let idserie = $(this).data('idserie');
            let nombre = $(this).data("nombre");
            let serie = $(this).data("serie") || '';
            let motor = $(this).data("motor") || '';
            let tipo = $("#tipoModal").val();

            // Evitar duplicados en la tabla detalle
            if ($("#tablaDetalleSolicitud tbody tr[data-idproducto='" + idproducto + "']").length > 0) {
                // ya existe -> ignorar. Si quieres sumar cantidad en vez de ignorar, lo cambiamos.
                return;
            }

            let fila = `
                <tr data-idproducto="${idproducto}" data-idserie="${idserie}">
                    <td>
                        ${nombre} - ${serie}- ${motor}
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm cantidad" min="1" value="1">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-xs btnEliminarFila"><i class="fa fa-times"></i></button>
                    </td>
                </tr>`;
            if (tipo === 'traslado') {
                $("#tablaDetalle tbody").append(fila);
            } else {
                $("#tablaDetalleSolicitud tbody").append(fila);
            }
        });
        $("#modalProductos").modal("hide");
    });

    // Delegated event para eliminar fila
    $(document).on("click", ".btnEliminarFila", function () {
        $(this).closest("tr").remove();
    });
} // <-- cierre de configurarBotones()

//==============================
function limpiar() {
    $("#iddestino").val("");
    $("#tablaDetalle tbody").html("");
}

function pintarMisSolicitudes(data) {

    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="7" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbody_mis_solicitudes").html(html);
        return;
    }

    data.forEach(item => {
        let anular = '';
        let imprimir = '';
        if (item.estado != 'Anulado') {
            anular = `<button class="btn btn-danger btn-sm" title="Anular solicitud" onclick="desactivar(${item.idtraslado})"><i class="fa fa-times"></i></button> `;
        }
        if (item.estado == 'Aceptado') {
            imprimir = `<button class="btn btn-primary btn-sm" title="Imprimir solicitud" onclick="imprimirSolicitud(${item.idtraslado})"><i class="fa fa-print"></i></button>`;
        }

        html += `
            <tr>
                <td>${item.idtraslado}</td>
                <td>${item.origen}</td>
                <td>${item.destino}</td>
                <td>${item.fecha}</td>
                <td>${item.estado_str}</td>

                <td>
                    <button class="btn btn-info btn-sm" title="Ver solicitud" onclick="verProductosSolicitud(${item.idtraslado}, true)"><i class="fa fa-eye"></i></button> 
                    ${anular}
                    ${imprimir}
                </td>
            </tr>
        `;

    });

    $("#tbody_mis_solicitudes").html(html);

}


paginatorMisSolicitudes = new FluentPaginator({
    url: "controladores/traslado.php?op=listar",
    renderTabla: pintarMisSolicitudes,
    searchSelector: "#searchMisSolicitudes",
    limitSelector: "#limitMisSolicitudes",
    paginationId: "#paginationMisSolicitudes",
    extraParams: () => ({
        fecha_inicio: '',
        fecha_fin: '',
        estado: $("#estadoMisSolicitudes").val() || 'Todos',
        tipo: 'solicitud',
        origen: 1
    })
});

function pintarSolicitudes(data) {

    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="7" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbody_solicitudes").html(html);
        return;
    }

    data.forEach(item => {
        console.log(item);
        
        let anular = '';
        let imprimir = '';
        if (parseInt(item.estado) === 0) {
            anular = `<button class="btn btn-danger btn-sm" title="Anular solicitud" onclick="desactivar(${item.idtraslado})"><i class="fa fa-times"></i></button> `;
        }
        if (parseInt(item.estado) == 1) {
            imprimir = `<button class="btn btn-primary btn-sm" title="Imprimir solicitud" onclick="imprimirSolicitud(${item.idtraslado})"><i class="fa fa-print"></i></button>`;
        }

        var estado = (parseInt(item.estado) != 0) ? true:false;

        html += `
            <tr>
                <td>${item.idtraslado}</td>
                <td>${item.origen}</td>
                <td>${item.destino}</td>
                <td>${item.fecha}</td>
                <td>${item.estado_str}</td>

                <td>
                    <button class="btn btn-info btn-sm" title="Ver solicitud" onclick="verProductosSolicitud(${item.idtraslado}, ${estado})"><i class="fa fa-eye"></i></button> 
                    ${anular}
                    ${imprimir}
                </td>
            </tr>
        `;

    });

    $("#tbody_solicitudes").html(html);

}


paginatorSolicitudes = new FluentPaginator({
    url: "controladores/traslado.php?op=listar",
    renderTabla: pintarSolicitudes,
    searchSelector: "#searchSolicitudes",
    limitSelector: "#limitSolicitudes",
    paginationId: "#paginationSolicitudes",
    extraParams: () => ({
        fecha_inicio: '',
        fecha_fin: '',
        estado: $("#estadoSolicitudes").val() || 'Todos',
        tipo: 'solicitud'
    })
});

function pintarTraslados(data) {

    let html = "";

    if (data.length === 0) {

        html = `
            <tr>
                <td colspan="7" class="text-center">
                    No se encontraron registros
                </td>
            </tr>
        `;

        $("#tbody_traslados").html(html);
        return;
    }

    data.forEach(item => {
        let anular = '';
        let imprimir = '';
        if (item.estado != 'Anulado') {
            anular = `<button class="btn btn-danger btn-sm" title="Anular solicitud" onclick="desactivar(${item.idtraslado})"><i class="fa fa-times"></i></button> `;
        }
        if (item.estado == 'Aceptado') {
            imprimir = `<button class="btn btn-primary btn-sm" title="Imprimir solicitud" onclick="imprimirSolicitud(${item.idtraslado})"><i class="fa fa-print"></i></button>`;
        }

        html += `
            <tr>
                <td>${item.idtraslado}</td>
                <td>${item.origen}</td>
                <td>${item.destino}</td>
                <td>${item.fecha}</td>
                <td>${item.estado_str}</td>

                <td>
                    <button class="btn btn-info btn-sm" title="Ver solicitud" onclick="verProductosSolicitud(${item.idtraslado}, true)"><i class="fa fa-eye"></i></button> 
                    ${anular}
                    ${imprimir}
                </td>
            </tr>
        `;

    });

    $("#tbody_traslados").html(html);

}


paginatorTraslados = new FluentPaginator({
    url: "controladores/traslado.php?op=listar",
    renderTabla: pintarTraslados,
    searchSelector: "#searchTraslados",
    limitSelector: "#limitTraslados",
    paginationId: "#paginationTraslados",
    extraParams: () => ({
        fecha_inicio: '',
        fecha_fin: '',
        estado: $("#estadoTraslados").val() || 'Todos',
        tipo: 'traslado',
        origen: 1
    })
});


function verProductos(idtraslado) {
    $.ajax({
        url: 'controladores/traslado.php?op=verdetalle',
        type: 'GET',
        data: { idtraslado: idtraslado },
        dataType: 'json',
        success: function (data) {
            let tbody = '';
            data.forEach(item => {
                tbody += `<tr>
                    <td>${item.producto}</td>
                    <td>${item.cantidad}</td>
                    <td>${item.destino}</td>
                </tr>`;
            });
            $('#tablaDetalleProductos tbody').html(tbody);
            $('#modalDetalleProductos').modal('show');
        }
    });
}

function cargarAlmacenesDestino() {
    $.post("controladores/traslado.php?op=almacenesDestino", function (r) {
        $("#iddestino").html(r);
    });
}

//==============================
// LISTAR PRODUCTOS CON PAGINACIÓN Y BUSCADOR
//==============================
function listarProductos(busqueda = '', idsucursal) {
    if (!idsucursal) {
        Swal.fire("Atención", "Seleccione un almacén destino antes de agregar productos", "warning");
        return;
    }

    $.post("controladores/producto.php?op=buscarStockPorSucursales",
        { search: busqueda, idsucursalFiltro: idsucursal },
        function (response) {
            const data = JSON.parse(response);

            if (!data.success) {
                Swal.fire({ title: "Producto", icon: "error", text: data.message });
                return;
            };

            let tbody = "";

            if (data.data.length === 0) {

                tbody = `
                        <tr>
                            <td colspan="6" class="text-center">
                                No se encontraron resultados.
                            </td>
                        </tr>
                    `;

            } else {

                data.data.forEach((row) => {

                    tbody += `
                            <tr>

                                <td>
                                    <input
                                        type="checkbox"
                                        class="chkProducto"
                                        data-idserie="${row.idserie}"
                                        data-idproducto="${row.idproducto}"
                                        data-nombre="${row.nombre}"
                                        data-codigo="${row.codigo}"
                                        data-serie="${row.numero_serie}"
                                        data-motor="${row.numero_motor}"
                                        data-placa="${row.placa}"
                                        data-color="${row.color}"
                                        data-idsucursal="${row.idsucursal}"
                                        data-sucursal="${row.sucursal}">
                                </td>

                                <td>${row.nombre}</td>
                                <td>${row.codigo}</td>
                                <td>${row.numero_serie ?? "-"}</td>
                                <td>${row.placa ?? "-"}</td>
                                <td>${row.sucursal}</td>

                            </tr>
                        `;
                });

            }

            $("#tablaProductos tbody").html(tbody);
        }
    );
}

//==============================
// CAMBIAR PÁGINA DE PRODUCTOS
//==============================
function cambiarPagina(pag) {
    const texto = $("#buscarProducto").val();
    listarProductos(texto, pag);
}

function cargarAlmacenes() {
    // 1️⃣ Mostrar el nombre del almacén de origen (sucursal actual)
    $.getJSON("controladores/traslado.php?op=sucursal_actual", function (data) {
        if (data && data.idsucursal) {
            $("#idorigen").val(data.idsucursal);
            $("#nombre_origen").val(data.nombre);
        }
    });

    // 2️⃣ Cargar lista de almacenes destino
    $.post("controladores/traslado.php?op=almacenesDestino", function (r) {
        $("#iddestino").html(r);
    });
}

////////////////////////////////////////////////////////////////

// Inicialización
$("#formSolicitud").on("submit", function (e) {
    e.preventDefault();
    enviarSolicitud();
});

// Cargar almacenes destino
function cargarAlmacenesSolicitud() {
    $.post("controladores/traslado.php?op=almacenesDestino", function (r) {
        $("#iddestino_solicitud").html(r);
    });
}
cargarAlmacenesSolicitud();

// Botón seleccionar productos
$("#btnAgregarProductosSolicitud").click(function () {
    let idsucursal = $("#iddestino").val();
    listarProductos('', idsucursal);
    $("#modalProductos").modal("show");
    $("#tipoModal").val("solicitud");
});

$("#btnAgregarProductos").click(function () {
    let idsucursal = $("#idorigen").val();
    listarProductos('', idsucursal);
    $("#modalProductos").modal("show");
    $("#tipoModal").val("traslado");
});


// Agregar productos seleccionados a la tabla de solicitud
// $("#btnAgregarSeleccionados").click(function () {
//     $("#tablaProductos tbody input.chkProducto:checked").each(function () {
//         let id = $(this).val();
//         let nombre = $(this).data("nombre");
//         let stock = $(this).data("stock") || 0;

//         if ($("#tablaDetalleSolicitud tbody tr[data-idproducto='" + id + "']").length > 0) return;

//         let fila = `
//         <tr data-idproducto="${id}" data-stock="${stock}">
//             <td>${nombre} <small class="text-muted">(Stock: ${stock})</small></td>
//             <td><input type="number" class="form-control form-control-sm cantidad" min="1" value="1"></td>
//             <td><button type="button" class="btn btn-danger btn-xs btnEliminarFila"><i class="fa fa-times"></i></button></td>
//         </tr>`;
//         $("#tablaDetalleSolicitud tbody").append(fila);
//     });
//     $("#modalProductos").modal("hide");
// });

// Eliminar fila
$(document).on("click", "#tablaDetalleSolicitud .btnEliminarFila", function () {
    $(this).closest("tr").remove();
});

// Enviar solicitud
function enviarSolicitud() {

    let productosSeleccionados = [];

    $("#tablaDetalleSolicitud tbody tr").each(function () {
        let idproducto = $(this).data("idproducto");
        let idserie = $(this).data("idserie");
        let cantidad = parseInt($(this).find(".cantidad").val()) || 0;
        let nombre = $(this).find("td").first().text().trim();

        productosSeleccionados.push({
            idproducto: idproducto,
            idserie: idserie,
            cantidad: cantidad || 1
        });
    });

    if (productosSeleccionados.length === 0) {
        Swal.fire("Atención", "Debe seleccionar al menos un producto.", "warning");
        return;
    }

    const iddestino_solicitud = $("#iddestino_solicitud").val();

    if (!iddestino_solicitud) {
        Swal.fire("Atención", "Debe seleccionar la sucursal destino.", "warning");
        return;
    }

    $("#modalStockSucursales").modal("hide");

    $.ajax({
        url: "controladores/traslado.php?op=guardarSolicitud",
        type: "POST",
        data: {
            productos: JSON.stringify(productosSeleccionados),
            iddestino_solicitud: iddestino_solicitud,
        },
        success: function (resp) {
            console.log("📨 Respuesta guardarSolicitud:", resp);

            // Normalizamos la respuesta
            const r = resp.trim().toLowerCase();

            if (r === "ok" || r.includes("solicitud enviada")) {
                Swal.fire("Solicitud", resp, "success");
                $("#modalSolicitud").modal("hide");
                paginatorMisSolicitudes.load();
                limpiarSolicitud();
            } else {
                Swal.fire("Error", "No se pudo registrar la solicitud.", "error");
            }
        },
        error: function (xhr) {
            console.error("❌ Error en guardarSolicitud:", xhr.responseText);
            Swal.fire("Error", "Ocurrió un error en el servidor.", "error");
        },
    });
}

function limpiarSolicitud() {
    $("#tablaDetalleSolicitud tbody").html("");
    $("#iddestino_solicitud").val("");
}

function cargarSucursalActual() {
    $.ajax({
        url: "controladores/traslado.php?op=sucursal_actual",
        type: "POST",
        dataType: "json",
        success: function (data) {
            if (data && data.nombre) {
                $("#nombre_sucursal_origen").val(data.nombre);
            } else {
                $("#nombre_sucursal_origen").val("No definida");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar sucursal actual:", error);
            $("#nombre_sucursal_origen").val("Error");
        }
    });
}

// Llamar cuando se abre el modal de solicitud
$('#modalSolicitud').on('shown.bs.modal', function () {
    cargarSucursalActual();
    $('#tablaDetalleSolicitud tbody').empty();
});

// 🔹 Cargar productos en el modal
function verProductosSolicitud(idtraslado, soloLectura = false) {
    $.post(
        "controladores/traslado.php?op=verProductosSolicitud",
        { idtraslado: idtraslado, soloLectura: soloLectura },
        function (data) {
            if (!data) {
                console.error(" Respuesta vacía del servidor.");
                alert("No se obtuvo respuesta del servidor.");
                return;
            }

            let json;
            try {
                json = JSON.parse(data);
            } catch (e) {
                console.error(" Error al parsear JSON:", data);
                alert("Error al leer los datos del servidor.");
                return;
            }

            if (json.error) {
                alert(json.error);
                return;
            }

            const productos = json.productos || [];

            let html = "";
            productos.forEach((p) => {
                html += `
                    <tr>
                        <td class="nombreProducto">${p.nombre}</td>
                        <td>
                            <input type="hidden" class="idProductoHidden" value="${p.idproducto}">
                            <input type="number" class="form-control cantidadProducto" value="${p.cantidad}" min="1">
                        </td>
                        <td>
                            <select class="form-control estadoProducto">
                                <option value="pendiente" ${p.estado_detalle === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="aceptado" ${p.estado_detalle === 'aceptado' ? 'selected' : ''}>Aceptar</option>
                                <option value="rechazado" ${p.estado_detalle === 'rechazado' ? 'selected' : ''}>Rechazar</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control observacion" value="${p.observacion ?? ''}">
                        </td>
                    </tr>
                `;
            });

            $("#tablaProductosSolicitud").html(html);
            $("#idtraslado_solicitud").val(idtraslado);

            // Obtener sucursal solicitante
            $.post(
                "controladores/traslado.php?op=obtenerSucursalOrigen",
                { idtraslado: idtraslado },
                function (res) {
                    $("#sucursal_origen_solicitud").val(res.origen || '');
                },
                "json"
            );

            // Mostrar modal
            if (soloLectura === true || soloLectura === "true") {
                // Cambia el título
                $("#tituloSolicitudLabel").html(`
                    <i class="fa fa-eye"></i> <b>Detalle de Solicitud #${idtraslado}</b>
                `);

                // Oculta botones de acción
                $("#modalAprobarSolicitud .btn-success").hide();
                $("#modalAprobarSolicitud .btn-guardar").hide();

                // Desactiva inputs
                $("#modalAprobarSolicitud input, #modalAprobarSolicitud select, #modalAprobarSolicitud textarea")
                    .prop("disabled", true)
                    .addClass("readonly-input"); // estilo visual de lectura

                // Agrega borde azul y fondo más claro para modo lectura
                $("#modalAprobarSolicitud .modal-content")
                    .removeClass("border-success shadow-lg")
                    .addClass("border-info shadow-sm");

                $("#modalAprobarSolicitud .modal-header")
                    .removeClass("bg-success")
                    .addClass("bg-info text-white");

                $("#modalAprobarSolicitud .modal-footer").hide(); // quita los botones de pie si solo lectura

                // Muestra el modal
                $("#modalAprobarSolicitud").modal("show");
            } else {
                // Modo aprobación normal
                $("#tituloSolicitudLabel").html(`
                    <i class="fa fa-check-circle"></i> <b>Aprobar Solicitud #${idtraslado}</b>
                `);

                $("#modalAprobarSolicitud .btn-success").show();
                $("#modalAprobarSolicitud .btn-guardar").show();

                $("#modalAprobarSolicitud input, #modalAprobarSolicitud select, #modalAprobarSolicitud textarea")
                    .prop("disabled", false)
                    .removeClass("readonly-input");

                $("#modalAprobarSolicitud .modal-content")
                    .removeClass("border-info shadow-sm")
                    .addClass("border-success shadow-lg");

                $("#modalAprobarSolicitud .modal-header")
                    .removeClass("bg-info text-white")
                    .addClass("bg-success text-white");

                $("#modalAprobarSolicitud .modal-footer").show();

                $("#modalAprobarSolicitud").modal("show");
            }


        }
    ).fail(function (xhr) {
        console.error("Error AJAX:", xhr.responseText);
        alert("Ocurrió un error en la comunicación con el servidor.");
    });
}

// 🔹 Aprobar o rechazar productos
function aprobarSolicitud() {
    let idtraslado = $("#idtraslado_solicitud").val();
    let productos = [];

    $("#tablaProductosSolicitud tr").each(function () {
        let idproducto = $(this).find(".idProductoHidden").val();
        let nombreProducto = $(this).find(".nombreProducto").text().trim();
        let estado = $(this).find(".estadoProducto").val();
        let cantidad = parseFloat($(this).find(".cantidadProducto").val()) || 0;
        let observacion = $(this).find(".observacion").val().trim();

        if (!idproducto) {
            console.error("ID de producto inválido en la fila:", $(this).html());
            return;
        }

        // Solo productos aceptados o rechazados
        if (!["aceptado", "rechazado"].includes(estado)) return;

        // Validar cantidad > 0 para aceptados
        if (estado === "aceptado" && cantidad <= 0) {
            Swal.fire({
                icon: "warning",
                title: "Cantidad inválida",
                text: "La cantidad del producto '" + nombreProducto + "' debe ser mayor que 0.",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Entendido"
            });
            throw "Cantidad inválida";
        }

        productos.push({
            idproducto,
            nombre: nombreProducto,
            estado,
            cantidad,
            observacion
        });
    });

    if (productos.length === 0) {
        Swal.fire({
            icon: "info",
            title: "Sin productos",
            text: "No hay productos para aprobar o rechazar.",
            confirmButtonColor: "#3085d6"
        });
        return;
    }

    // Confirmación antes de enviar
    Swal.fire({
        title: "¿Aprobar solicitud?",
        text: "Se procesará la solicitud con los cambios realizados.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, aprobar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(
                "controladores/traslado.php?op=aprobarSolicitud",
                { idtraslado: idtraslado, productos: JSON.stringify(productos) },
                function (response) {
                    const data = JSON.parse(response);
                    if (data.success != true) {
                        Swal.fire({ title: 'Traslado', icon: 'error', text: data.message });
                        return;
                    }
                    Swal.fire({ title: 'Traslado', icon: 'success', text: data.message });
                    $("#modalAprobarSolicitud").modal("hide");
                    paginatorSolicitudes.load();
                }
            ).fail(() => {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Ocurrió un problema al procesar la solicitud.",
                    confirmButtonColor: "#d33"
                });
            });
        }
    });
}


// 🔹 Deshabilitar inputs si se rechaza un producto
$("#tablaProductosSolicitud").on("change", ".estadoProducto", function () {
    let estado = $(this).val();
    let row = $(this).closest("tr");
    if (estado === "rechazado") {
        row.find(".cantidadProducto, .observacion").prop("disabled", true);
    } else {
        row.find(".cantidadProducto, .observacion").prop("disabled", false);
    }
});

// 🔹 Ver productos para aprobación
function verProductosAprobacion(idtraslado) {
    $.post("controladores/traslado.php?op=verProductosSolicitud", { idtraslado: idtraslado }, function (resp) {
        try {
            let data = JSON.parse(resp);

            if (data.error) {
                alert(data.error);
                return;
            }

            $("#modalAprobarSolicitud").modal("show");
            $("#tituloSolicitudLabel").text("Aprobar Solicitud #" + idtraslado);
            $("#idtraslado_solicitud").val(idtraslado);

            let html = "";
            data.productos.forEach((p) => {
                html += `
                    <tr>
                        <td>${p.nombre}</td>
                        <td>
                            <input type="hidden" class="idProductoHidden" value="${p.idproducto}">
                            <input type="number" class="form-control cantidadProducto" value="${p.cantidad}" min="1">
                        </td>
                        <td>
                            <select class="form-control estadoProducto">
                                <option value="pendiente" ${p.estado_detalle === 'pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="aceptado" ${p.estado_detalle === 'aceptado' ? 'selected' : ''}>Aceptar</option>
                                <option value="rechazado" ${p.estado_detalle === 'rechazado' ? 'selected' : ''}>Rechazar</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control observacion" value="${p.observacion ?? ''}">
                        </td>
                    </tr>
                `;
            });

            $("#tablaProductosSolicitud").html(html);

        } catch (e) {
            console.error("Respuesta inválida del servidor:", resp);
            alert("Error al obtener los datos de la solicitud.");
        }
    });
}
/*  // Vacía solo el contenido del cuerpo de la tabla de detalle
  $('#tablaDetalleSolicitud tbody').empty();
  $('#tablaDetalle tbody').empty();
  console.log(" Tabla de detalle de solicitud limpiada correctamente");
}
*/
/*function cancelarformT() {
  // Vacía solo el contenido del cuerpo de la tabla de detalle
  $('#tablaDetalle tbody').empty();
   $('#tablaDetalleSolicitud tbody').empty();
  console.log(" Tabla de detalle de solicitud limpiada correctamente");
}*/



function imprimirSolicitud(id) {
    window.open('reportes/exSolicitud.php?id=' + id, '_blank');
}

function imprimirTraslado(id) {
    window.open('reportes/exTraslado.php?id=' + id, '_blank');
}

init();
