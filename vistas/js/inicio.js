// Variable global para almacenar la instancia del gráfico de productos más vendidos
let chartProductosMasVendidos = null;

//Función que se ejecuta al inicio
function init() {
    $("#body").addClass("sidebar-collapse sidebar-mini");
    obtenerProductosStockBajo();
    $('#navInicio').addClass("treeview active");
    $('#navInicio').addClass("active");

    $.post("controladores/venta.php?op=selectSucursal3", function (r) {
        $("#idsucursal2").html(r);
        $("#idsucursal2").select2("");

        $("#idsucursal2").on("change", function () {
            cargarVendedoresPorSucursal();
            mostrarInicio();
        });

        cargarVendedoresPorSucursal();

        // Una vez cargada la sucursal, inicializamos las consultas y tablas
        mostrarInicio();
    });

    // Actualizar la alerta cada 30 segundos, pero solo si la tabla está visible
    setInterval(function () {
        let table = $('#stockAlertTable');
        if (table.is(":visible")) {
            obtenerProductosStockBajo();
        }
    }, 30000);

}

$(document).ready(function () {
    // Asegurarse de ocultar otros navbars si estás en la vista de inicio
    if (window.location.pathname === '/inicio.php') {
        // Ocultar cualquier navbar global si no es el del inicio
        $('#navbar-global').hide(); // Aquí pon el ID o clase del navbar predeterminado
        $('#navbar-inicio').show(); // Asegúrate de mostrar el navbar para el inicio
    }
});

function cargarVendedoresPorSucursal() {
    var idsucursal = $("#idsucursal2").val();

    $.post("controladores/venta.php?op=selectVendedor", { idsucursal: idsucursal }, function (r) {
        $("#idcliente").html(r);
        $("#idcliente").select2("");
    });
}

// Función para obtener productos con stock bajo
function obtenerProductosStockBajo() {
    let idsucursal2 = $("#idsucursal2").val(); // Obtener el id de la sucursal seleccionada

    $.get("controladores/producto.php?op=listarStockBajoAlert", { idsucursal2: idsucursal2 }, function (data) {
        let productos = JSON.parse(data);
        let cantidadBaja = productos.length; // La cantidad de productos con stock bajo (0, 1, 2 o 3)

        // Actualizar el contador de productos en el navbar
        $('#stockAlertCount').text(cantidadBaja); // Actualiza el contador

        let listaProductos = $('#stockAlertTableBody');
        listaProductos.html(''); // Limpiar la tabla de productos antes de llenarla

        if (cantidadBaja > 0) {
            // Mostrar los productos con stock bajo
            productos.forEach(function (producto) {
                listaProductos.append(`
                    <tr>
                        <td><img src="files/productos/${producto.imagen}" alt="${producto.nombre}" style="width: 30px; height: 30px;" class="mr-2"> ${producto.nombre}</td>
                        <td class="text-danger">${producto.stock}</td>
                    </tr>
                `);
            });
        } else {
            // Si no hay productos con stock bajo
            listaProductos.append('<tr><td colspan="3" class="text-center">No hay productos con stock bajo.</td></tr>');
        }
    });
}


function mostrarInicio() {

    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    var idvendedor = $("#idcliente").val();
    var idsucursal = $("#idsucursal2").val();

    if (!idsucursal) {
        console.warn('No existe sucursal seleccionada aún en mostrarInicio(), se omiten consultas.');
        return;
    }

    if ($.fn.DataTable.isDataTable('#tblpedidos')) {
        $('#tblpedidos').DataTable().destroy();
    }

    tabla = $('#tblpedidos').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "processing": true,
        "language": {
            "processing": "<img style='width:80px; height:80px;' src='files/plantilla/loading-page.gif' />",
        },
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        dom: '<"row"<"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"<"dt-buttons btn-group flex-wrap"B>><"col-sm-12 col-md-4"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [
            [5, 10, 25, 50, 100, -1],
            ['5 filas', '10 filas', '25 filas', '50 filas', '100 filas', 'Mostrar todo']
        ],
        buttons: [
            'pageLength',
            {
                extend: 'excelHtml5',
                text: "<i class='fas fa-file-csv'></i>",
                titleAttr: 'Exportar a Excel',
                title: 'Lista de Pedidos',
            },
            {
                extend: 'pdf',
                text: "<i class='fas fa-file-pdf'></i>",
                titleAttr: 'Exportar a PDF',
                title: 'Lista de Pedidos',
            }
        ],
        "ajax": {
            url: 'controladores/consultas.php?op=mostrartotalpedidos',
            data: {
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                idsucursal: idsucursal
            },
            type: "get",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5,
        "order": [[1, "desc"]]
    });

    $.post("controladores/consultas.php?op=totalcomprahoy", {
        fecha_inicio,
        fecha_fin,
        idvendedor,
        idsucursal
    }, function (data, status) {
        data = JSON.parse(data);
        var totalCompra = data.total_compra;

        // Formatear el número
        var formattedTotal = new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(totalCompra);

        var label = document.querySelector('#lblComprasHoy');
        label.textContent = formattedTotal;
    });
    
    $.post("controladores/consultas.php?op=totalventahoy", {
        fecha_inicio,
        fecha_fin,
        idvendedor,
        idsucursal
    }, function (data, status) {
        data = JSON.parse(data);
        var totalVenta = data.total_venta;

        // Formatear el número
        var formattedTotal = new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(totalVenta);

        var label = document.querySelector('#lblVentasHoy');
        label.textContent = formattedTotal;
    });

    $.post("controladores/consultas.php?op=totalusuariosr", function (data, status) {

        data = JSON.parse(data);
        var label = document.querySelector('#lblEmpleados');
        label.textContent = data.idpersonal;

    });

    $.post("controladores/consultas.php?op=totalproveedoresr", function (data, status) {

        data = JSON.parse(data);
        var label = document.querySelector('#lblProveedores');
        label.textContent = data.idpersona;

    });

    $.post("controladores/consultas.php?op=totalventachoy", {
        fecha_inicio,
        fecha_fin,
        idvendedor,
        idsucursal
    }, function (data, status) {
        data = JSON.parse(data);
        var totalVentaCredito = data.total_venta;

        var formattedTotal = new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(totalVentaCredito);

        document.querySelector('#lblTotalVentasC').textContent = formattedTotal;
    });

   

    $.post("controladores/consultas.php?op=totalcuentasporcobrar", {
        fecha_inicio,
        fecha_fin,
        idvendedor,
        idsucursal
    }, function (data, status) {
        data = JSON.parse(data);
        var totalCompra = data.totaldeuda;

        var formattedTotal = new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(totalCompra);

        document.querySelector('#lblCuentasCobrar').textContent = formattedTotal;
    });

    $.post("controladores/consultas.php?op=totalcuentasporpagar", {
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin,
        idvendedor: idvendedor,
        idsucursal: idsucursal
    }, function (data, status) {

        data = JSON.parse(data);
        var totalCompra = data.totaldeuda;

        // Formatear el número
        var formattedTotal = new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(totalCompra);

        var label = document.querySelector('#lblCuentasPagar');
        label.textContent = formattedTotal;
    });

   
    $.post("controladores/consultas.php?op=totalcategorias", function (data, status) {

        data = JSON.parse(data);
        var label = document.querySelector('#lblCategorias');
        label.textContent = data.totalca;

    });

    $.post("controladores/consultas.php?op=totalproductos", function (data, status) {

        data = JSON.parse(data);
        var label = document.querySelector('#lblProductos');
        label.textContent = data.totalpro;

    });

    // =======================
    // PRODUCTO MAS VENDIDO
    // =======================
    

    function waitForElement(selector, callback) {
        const el = document.querySelector(selector);
        if (el) {
            callback(el);
        } else {
            setTimeout(() => waitForElement(selector, callback), 100);
        }
    }

    waitForElement('#productosmasvendido2', function (container) {
        $.ajax({
            url: "controladores/consultas.php?op=productosmasvendidos",
            method: "POST",
            dataType: "json",
            success: function (data) {
                if (!Array.isArray(data) || data.length === 0) return;

                const labels = data.map(item => item.nombre);
                const series = data.map(item => parseFloat(item.cantidad));

                const baseColors = [
                    ['#ff6b6b', '#ff9ff3'],
                    ['#48dbfb', '#00a8ff'],
                    ['#feca57', '#ff9f43'],
                    ['#1dd1a1', '#10ac84'],
                    ['#9b59b6', '#8e44ad'],
                    ['#34495e', '#2c3e50']
                ];

                const options = {
                    chart: { type: 'donut', height: 400 },
                    series: series,
                    labels: labels,
                    legend: { position: 'bottom', fontSize: '12px', markers: { radius: 12 }, horizontalAlign: 'center' },
                    dataLabels: { enabled: false },
                    fill: {
                        type: 'gradient',
                        gradient: { shade: 'light', type: 'vertical', gradientToColors: baseColors.map(c => c[1]), stops: [0, 100] }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val, opts) {
                                let label = 'Producto';
                                let total = 0;

                                // Verificar que opts y opts.w existan
                                if (opts && opts.w && opts.w.config) {
                                    label = opts.w.config.labels[opts.seriesIndex];
                                    total = opts.w.config.series.reduce((a, b) => a + b, 0);
                                } else if (opts && Array.isArray(opts.series)) {
                                    label = opts.seriesIndex !== undefined ? `Serie ${opts.seriesIndex + 1}` : 'Producto';
                                    total = opts.series.reduce((a, b) => a + b, 0);
                                }

                                const percentage = total ? ((val / total) * 100).toFixed(1) : 0;
                                return `${label}: ${val} (${percentage}%)`;
                            }
                        }
                    },

                    responsive: [{ breakpoint: 480, options: { chart: { height: 300 }, legend: { position: 'bottom' } } }]
                };

                // Destruir el gráfico anterior si existe
                if (chartProductosMasVendidos !== null) {
                    chartProductosMasVendidos.destroy();
                }

                // Crear el nuevo gráfico y guardarlo en la variable global
                chartProductosMasVendidos = new ApexCharts(container, options);
                chartProductosMasVendidos.render();
            },
            error: function (xhr, status, error) {
                console.error("Error al obtener datos:", error);
            }
        });
    });


    // =======================
    // GRAFICO INGRESOS/EGRESOS
    // =======================
    $.post("controladores/consultas.php?op=ingresos_egresos", function (data) {
        data = JSON.parse(data);
        //console.log("Respuesta del servidor:", data);

        const ctx = document.getElementById("graficoIngresosEgresos").getContext("2d");

        // Crear gradientes
        const gradientIngresos = ctx.createLinearGradient(0, 0, 0, 300);
        gradientIngresos.addColorStop(0, "rgba(46, 204, 113, 0.5)");
        gradientIngresos.addColorStop(1, "rgba(46, 204, 113, 0.05)");

        const gradientEgresos = ctx.createLinearGradient(0, 0, 0, 300);
        gradientEgresos.addColorStop(0, "rgba(231, 76, 60, 0.5)");
        gradientEgresos.addColorStop(1, "rgba(231, 76, 60, 0.05)");

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: "Ingresos",
                        data: data.ingresos,
                        borderColor: "#2ecc71",
                        backgroundColor: gradientIngresos,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: "#27ae60",
                        pointBorderColor: "#fff",
                        pointHoverRadius: 6,
                        pointRadius: 4
                    },
                    {
                        label: "Egresos",
                        data: data.egresos,
                        borderColor: "#e74c3c",
                        backgroundColor: gradientEgresos,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: "#c0392b",
                        pointBorderColor: "#fff",
                        pointHoverRadius: 6,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        labels: {
                            color: "#2c3e50",
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: "#34495e",
                        titleColor: "#ecf0f1",
                        bodyColor: "#ecf0f1",
                        padding: 12,
                        callbacks: {
                            label: function (context) {
                                let value = context.raw;
                                return `${context.dataset.label}: S/ ${value.toLocaleString('es-PE')}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: "#2c3e50",
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: "rgba(0,0,0,0.05)"
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: "#2c3e50",
                            font: {
                                size: 12
                            },
                            callback: function (value) {
                                return `S/ ${value}`;
                            }
                        },
                        grid: {
                            color: "rgba(0,0,0,0.05)"
                        }
                    }
                }
            }
        });
    });



    // =======================
    // GRAFICO UTILIDADES
    // =======================
    const ctxUtilidades = document.getElementById('uti12m').getContext('2d');

    // Crear degradado vertical moderno
    const gradientUtil = ctxUtilidades.createLinearGradient(0, 0, 0, 400);
    gradientUtil.addColorStop(0, 'rgba(0, 123, 255, 0.4)');
    gradientUtil.addColorStop(0.5, 'rgba(0, 200, 150, 0.3)');
    gradientUtil.addColorStop(1, 'rgba(255, 255, 255, 0)');

    $.ajax({
        url: 'controladores/consultas.php?op=utilidades12meses',
        method: 'GET',
        data: { idvendedor: idvendedor, idsucursal: idsucursal },
        dataType: 'json',
        success: function (response) {
            if (!response.labels || !response.labels.length) {
                console.warn("No hay datos para mostrar el gráfico de utilidades.");
                return;
            }

            new Chart(ctxUtilidades, {
                type: 'line',
                data: {
                    labels: response.labels,
                    datasets: [{
                        label: 'Utilidad',
                        data: response.data,
                        backgroundColor: gradientUtil,
                        borderColor: '#007bff',
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#007bff',
                        pointHoverBackgroundColor: '#007bff',
                        pointHoverBorderColor: '#fff',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#333',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.7)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function (context) {
                                    return ` ${context.dataset.label}: S/ ${context.formattedValue}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#555' },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#555' },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                }
            });
        },
        error: function (xhr, status, error) {
            console.error("Error al obtener utilidades:", error);
        }
    });


}

init();