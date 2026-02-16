function init() {
    $.post("controladores/venta.php?op=selectSucursal3", function(r){
        $("#idsucursal2").html(r);
        $('#idsucursal2').select2('');
        cargarReporte();
    });

    $('#navReportesActive').addClass("treeview active");
    $('#navReportes').addClass("treeview menu-open");
    $('#navReporteConsolidado').addClass("active");

    $("#fecha_inicio, #fecha_fin, #idsucursal2").change(function(){
        cargarReporte();
    });
}

// ===============================
//   FUNCIÓN PRINCIPAL
// ===============================
function cargarReporte() {
    let fecha_inicio = $("#fecha_inicio").val();
    let fecha_fin    = $("#fecha_fin").val();
    let idsucursal2  = $("#idsucursal2").val();

    $.post("controladores/reporte.php?op=listar", {
        fecha_inicio,
        fecha_fin,
        idsucursal2
    }, function(data) {
        data = JSON.parse(data);

        // Totales principales
        let ventas   = parseFloat(data.resumen.ventas) || 0;
        let compras  = parseFloat(data.resumen.compras) || 0;
        let ingresos = parseFloat(data.resumen.ingresos) || 0;
        let egresos  = parseFloat(data.resumen.egresos) || 0;
        let utilidadReal = parseFloat(data.resumen.utilidad_real) || 0;
        let amortizaciones = data.amortizaciones || [];

        // Mostrar totales
        $("#lblCompraPV").html(compras.toFixed(2));
        $("#lblVentaPV").html(ventas.toFixed(2));
        $("#lblIngresos").html(ingresos.toFixed(2));
        $("#lblEgresos").html(egresos.toFixed(2));

        // ===== Cálculo de utilidad neta =====
        let utilidadNeta = utilidadReal;
        amortizaciones.forEach(a => {
            let totalAmortizado = parseFloat(a.monto_pagado) + parseFloat(a.monto_tarjeta);
            let totalCompra = parseFloat(a.total_compra);
            let ajuste = totalAmortizado - totalCompra;
            if (ajuste > 0) utilidadNeta += ajuste;
        });
        utilidadNeta -= egresos;
        $("#lblUtilidadNetaPV").html(utilidadNeta.toFixed(2));
        // ====================================

        // Tablas
        mostrarConsolidado(
            data.ventas || [],
            data.compras || [],
            data.ingresos || [],
            data.egresos || [],
            amortizaciones
        );

        let empresaNombre = data.empresa?.nombre || "Empresa";

        cargarTablaResumen(
            data.resumen_meses || [],
            empresaNombre,
            fecha_inicio,
            fecha_fin
        );

        // ============================
        // Botón exportar Excel completo
        // ============================
        $('#btnExportExcel').off('click').on('click', function () {
            exportarExcelCompleto(
                data.resumen_meses || [],             // listaResumen
                empresaNombre,                        // empresa
                fecha_inicio,
                fecha_fin,
                tablaConsolidado ? tablaConsolidado.data().toArray() : [] // datosConsolidado
            );
        });
    });
}

// ===============================
// TABLAS DETALLADAS
// ===============================
var tablaConsolidado;

function mostrarConsolidado(ventas, compras, ingresos, egresos, amortizaciones) {
    // Unificar todos los datos
    let data = [];

    ventas.forEach(x => {
        data.push({
            fecha: x.fecha_hora,
            tipo: "Venta",
            detalle: x.tipo_comprobante,
            producto: x.producto,
            cantidad: x.cantidad,
            total: parseFloat(x.subtotal).toFixed(2)
        });
    });

    compras.forEach(x => {
        data.push({
            fecha: x.fecha_hora,
            tipo: "Compra",
            detalle: x.comprobante,
            producto: x.producto,
            cantidad: x.cantidad,
            total: parseFloat(x.subtotal).toFixed(2)
        });
    });

    ingresos.forEach(x => {
        data.push({
            fecha: x.fecha,
            tipo: "Ingreso",
            detalle: x.descripcion,
            producto: "-",
            cantidad: "-",
            total: parseFloat(x.monto).toFixed(2)
        });
    });

    egresos.forEach(x => {
        data.push({
            fecha: x.fecha,
            tipo: "Egreso",
            detalle: x.descripcion,
            producto: "-",
            cantidad: "-",
            total: parseFloat(x.monto).toFixed(2)
        });
    });

    amortizaciones.forEach(x => {
        let fechas = x.lista_fechas_amortizacion ? x.lista_fechas_amortizacion.split(', ') : [];
        let montos = x.lista_montos_amortizacion ? x.lista_montos_amortizacion.split(', ') : [];

        data.push({
            fecha:
                "<i class='fas fa-calendar-alt' title='Ver fechas de pagos' style='cursor:pointer;' " +
                "onclick='mostrarFechasAmortizacion(" + JSON.stringify(fechas) + ", " + JSON.stringify(montos) + ")'></i> "
                + x.fecha_ultima_amortizacion,
            tipo: "Amortización",
            detalle: x.comprobante,
            producto: "-",
            cantidad: "-",
            total: parseFloat(x.monto_pagado + x.monto_tarjeta).toFixed(2)
        });
    });

    if (tablaConsolidado) {
        tablaConsolidado.clear().destroy();
    }

    // =============================
    // Tabla DataTables moderna
    // =============================
    tablaConsolidado = $('#tblDetalleConsolidado').DataTable({
        data: data,
        aProcessing: true,
        aServerSide: false,
        processing: true,
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        dom: '<"row"<"col-md-4"l><"col-md-4"B><"col-md-4"f>>t<"row"<"col-md-6"i><"col-md-6"p>>',
        lengthMenu: [
            [5, 10, 25, 50, 100, -1],
            ['5 filas', '10 filas', '25 filas', '50 filas', '100 filas', 'Mostrar todo']
        ],
        buttons: [
            'pageLength',
            { extend: 'excelHtml5', text: "<i class='fas fa-file-csv'></i>" },
            { extend: 'pdf', text: "<i class='fas fa-file-pdf'></i>" },
            { extend: 'colvis', text: "<i class='fas fa-bars'></i>" }
        ],
        columns: [
            { data: "fecha" },
            { data: "tipo" },
            { data: "detalle" },
            { data: "producto" },
            { data: "cantidad" },
            { data: "total" }
        ],
        rowCallback: function(row, data) {
            let neonStyles = {
                Venta: { color: "#007BFF", glow: "0 0 3px #007BFF" },
                Compra: { color: "#28A745", glow: "0 0 3px #28A745" },
                Ingreso: { color: "#6F42C1", glow: "0 0 3px #6F42C1" },
                Egreso: { color: "#DC3545", glow: "0 0 3px #DC3545" },
                Amortización: { color: "#FFC107", glow: "0 0 3px #FFC107" }
            };

            let style = neonStyles[data.tipo];
            if (style) {
                // Solo aplicamos el estilo a la columna "Tipo" (índice 1)
                $('td:eq(1)', row).css({
                    "color": style.color,
                    "text-shadow": style.glow,
                    "font-weight": "600"
                });
            }
        },
        createdRow: function(row) {
            $(row).hover(
                function() { $(this).css("background-color", "#f1f1f1"); },
                function() { $(this).css("background-color", "transparent"); }
            );
        },
        bDestroy: true,
        iDisplayLength: 5,
        order: [[0, "desc"]]
    });

    // Estilo general de tabla
    $('#tblDetalleConsolidado').css({
        "border-collapse": "separate",
        "border-spacing": "0 5px",
        "width": "100%",
        "font-family": "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
        "font-size": "0.95rem"
    });

    $('#tblDetalleConsolidado thead th').css({
        "background-color": "#343A40",
        "color": "#fff",
        "border-radius": "5px",
        "text-align": "center",
        "padding": "8px"
    });

    $('#tblDetalleConsolidado tbody td').css({
        "padding": "8px",
        "text-align": "center"
    });
}

// Mostrar las fechas y montos de amortización al hacer clic en el icono
function mostrarFechasAmortizacion(fechas, montos) {
    let contenido = "<ul>";
    for (let i = 0; i < fechas.length; i++) {
        contenido += `<li><b>Fecha:</b> ${fechas[i]} <b>Monto:</b> ${montos[i]}</li>`;
    }
    contenido += "</ul>";
    
    // Mostrar en un modal o algún elemento con un tooltip
    Swal.fire({
        title: 'Fechas y Montos de Amortización',
        html: contenido,
        showCloseButton: true,
        focusConfirm: false,
        confirmButtonText: 'Cerrar',
        confirmButtonColor: '#3085d6'
    });
}

// ===============================
// TABLA RESUMEN (MESES)
// ===============================
// --- Función para cargar la tabla en la web ---
function cargarTablaResumen(lista, empresa, fechaInicio, fechaFin) {
    $('#tbllistado').DataTable({
        destroy: true,
        data: lista,
        empresa: empresa, // guardamos empresa en configuración
        processing: true,
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        language: {
            processing: "<img style='width:70px;' src='files/plantilla/loading-page.gif' />",
            searchPlaceholder: "Buscar...",
        },
        dom:
            "<'row mb-3'" +
                "<'col-md-4 d-flex align-items-center'l>" +
                "<'col-md-4 text-center'B>" +
                "<'col-md-4'f>" +
            ">" +
            "rt" +
            "<'row mt-3'" +
                "<'col-md-6'i>" +
                "<'col-md-6'p>" +
            ">",
        lengthMenu: [
            [5, 10, 25, 50, 100, -1],
            ['5 filas','10 filas','25 filas','50 filas','100 filas','Mostrar todo']
        ],
        buttons: [
            {
                extend: 'pdfHtml5',
                text: "<i class='fas fa-file-pdf'></i> PDF",
                title: `Reporte Financiero – ${fechaInicio} al ${fechaFin}`,
                messageTop: `Empresa: ${empresa}`,
                footer: true,
                orientation: "landscape",
                pageSize: "A4",
                exportOptions: { columns: ":visible" },
                className: "btn btn-danger btn-sm px-3 rounded-pill shadow-sm"
            },
            {
                extend: 'colvis',
                text: "<i class='fas fa-bars'></i> Columnas",
                className: "btn btn-secondary btn-sm px-3 rounded-pill shadow-sm"
            }
        ],
        columns: [
            { data: "mes_nombre" },
            { data: "total_compras", render: d => numberFormat(d) },
            { data: "total_ventas", render: d => numberFormat(d) },
            { data: "total_ingresos", render: d => numberFormat(d) },
            { data: "total_egresos", render: d => numberFormat(d) },
            { data: "amortizaciones", render: d => numberFormat(d) },
            { data: "utilidad", render: d => numberFormat(d) },
        ],
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();
            api.columns([1,2,3,4,5,6], { page: 'current' }).every(function(i) {
                var sum = api.column(i, { page:'current' }).data()
                    .reduce((a,b) => (parseFloat(a)||0) + (parseFloat(b)||0), 0);
                $(api.column(i).footer()).html(numberFormat(sum));
            });
        },
        iDisplayLength: 5,
        order: [[0,"desc"]]
    });
}

function numberFormat(num){
    let n = parseFloat(num || 0);
    return n.toLocaleString("es-PE", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// --- Función para exportar Excel profesional ---
async function exportarExcelCompleto(listaResumen, empresa, fechaInicio, fechaFin, datosConsolidado) {
    const workbook = new ExcelJS.Workbook();

    // =============================
    // HOJA 1: Resumen Mensual
    // =============================
    const sheetResumen = workbook.addWorksheet('Resumen Mensual');

    // Encabezado principal
    sheetResumen.mergeCells('A1:G1');
    sheetResumen.getCell('A1').value = `Reporte Financiero – ${fechaInicio} al ${fechaFin}`;
    sheetResumen.getCell('A1').font = { size: 14, bold: true };
    sheetResumen.getCell('A1').alignment = { horizontal: 'center' };

    sheetResumen.mergeCells('A2:G2');
    sheetResumen.getCell('A2').value = `Empresa: ${empresa}`;
    sheetResumen.getCell('A2').font = { size: 12, bold: true };
    sheetResumen.getCell('A2').alignment = { horizontal: 'center' };

    // Encabezados de columna
    sheetResumen.addRow(['Mes', 'Compras', 'Ventas', 'Ingresos', 'Egresos', 'Amortizaciones', 'Utilidad']);
    sheetResumen.getRow(3).eachCell(cell => {
        cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF4472C4' } };
        cell.border = { top: { style: 'thin' }, left: { style: 'thin' }, bottom: { style: 'thin' }, right: { style: 'thin' } };
        cell.alignment = { horizontal: 'center' };
    });

    // Datos de resumen
    listaResumen.forEach(item => {
        sheetResumen.addRow([
            item.mes_nombre,
            parseFloat(item.total_compras || 0),
            parseFloat(item.total_ventas || 0),
            parseFloat(item.total_ingresos || 0),
            parseFloat(item.total_egresos || 0),
            parseFloat(item.amortizaciones || 0),
            parseFloat(item.utilidad || 0)
        ]);
    });

    // Formato de celdas numéricas
    sheetResumen.columns.forEach((col, index) => {
        if(index > 0) col.numFmt = '#,##0.00';
        col.width = 15;
    });

    // Totales
    const totalRowResumen = sheetResumen.addRow([
        'Totales',
        { formula: `SUM(B4:B${listaResumen.length+3})` },
        { formula: `SUM(C4:C${listaResumen.length+3})` },
        { formula: `SUM(D4:D${listaResumen.length+3})` },
        { formula: `SUM(E4:E${listaResumen.length+3})` },
        { formula: `SUM(F4:F${listaResumen.length+3})` },
        { formula: `SUM(G4:G${listaResumen.length+3})` }
    ]);
    totalRowResumen.eachCell(cell => {
        cell.font = { bold: true };
        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD9D9D9' } };
        cell.border = { top: { style: 'thin' }, left: { style: 'thin' }, bottom: { style: 'thin' }, right: { style: 'thin' } };
        cell.alignment = { horizontal: 'center' };
    });

    // =============================
    // HOJA 2: Consolidado Detallado
    // =============================
    const sheetConsolidado = workbook.addWorksheet('Consolidado Detallado');

    // Encabezados
    sheetConsolidado.addRow(['Fecha', 'Tipo', 'Detalle', 'Producto', 'Cantidad', 'Total']);
    sheetConsolidado.getRow(1).eachCell(cell => {
        cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF4472C4' } };
        cell.border = { top: { style: 'thin' }, left: { style: 'thin' }, bottom: { style: 'thin' }, right: { style: 'thin' } };
        cell.alignment = { horizontal: 'center' };
    });

    // Datos de consolidado
    datosConsolidado.forEach(item => {
        sheetConsolidado.addRow([
            item.fecha,
            item.tipo,
            item.detalle,
            item.producto,
            item.cantidad,
            parseFloat(item.total || 0)
        ]);
    });

    // Formato numérico
    sheetConsolidado.columns.forEach((col, index) => {
        if(index === 5) col.numFmt = '#,##0.00'; // columna Total
        col.width = 15;
    });

    // Totales
    const totalRowConsolidado = sheetConsolidado.addRow([
        'Totales', '', '', '', '', { formula: `SUM(F2:F${datosConsolidado.length+1})` }
    ]);
    totalRowConsolidado.eachCell(cell => {
        cell.font = { bold: true };
        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFD9D9D9' } };
        cell.border = { top: { style: 'thin' }, left: { style: 'thin' }, bottom: { style: 'thin' }, right: { style: 'thin' } };
        cell.alignment = { horizontal: 'center' };
    });

    // =============================
    // Descargar Excel
    // =============================
    const buf = await workbook.xlsx.writeBuffer();
    const blob = new Blob([buf], { type: 'application/octet-stream' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Reporte_Financiero_${fechaInicio}_al_${fechaFin}.xlsx`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}


init();
