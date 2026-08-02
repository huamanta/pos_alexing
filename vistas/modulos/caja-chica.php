<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Caja Chica</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Caja Chica</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif;
                }

                body {
                    background: #f4f6f9;
                    color: #333;
                }

                .container {
                    max-width: 1400px;
                    margin: 25px auto;
                    padding: 15px;
                }

                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                }

                .header h2 {
                    font-size: 30px;
                }

                .filters {
                    display: flex;
                    gap: 10px;
                }

                .filters select,
                .filters input {
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                }

                .cards {
                    display: grid;
                    grid-template-columns: repeat(5, 1fr);
                    gap: 20px;
                    margin-bottom: 25px;
                }

                .card {
                    background: #fff;
                    border-radius: 15px;
                    padding: 20px;
                    box-shadow: 0 3px 12px rgba(0, 0, 0, .08);
                    transition: .3s;
                }

                .card:hover {
                    transform: translateY(-5px);
                }

                .card-top {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }

                .icon {
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    font-size: 25px;
                    color: #fff;
                }

                .blue {
                    background: #0d6efd;
                }

                .green {
                    background: #28a745;
                }

                .orange {
                    background: #fd7e14;
                }

                .purple {
                    background: #6f42c1;
                }

                .red {
                    background: #dc3545;
                }

                .value {
                    margin-top: 10px;
                    font-size: 20px;
                    color: #198754;
                    font-weight: bold;
                }

                .subtitle {
                    color: #888;
                    margin-top: 5px;
                }

                .tables {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                }

                .box {
                    background: #fff;
                    border-radius: 15px;
                    box-shadow: 0 3px 12px rgba(0, 0, 0, .08);
                    overflow: hidden;
                }

                .box-header {
                    padding: 18px;
                    font-size: 20px;
                    font-weight: bold;
                    border-bottom: 1px solid #eee;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                th {
                    background: #f8f9fa;
                    padding: 12px;
                }

                td {
                    padding: 12px;
                    border-bottom: 1px solid #eee;
                }

                tfoot {
                    background: #eaf8ec;
                    font-weight: bold;
                }

                tfoot td {
                    color: #198754;
                }

                .summary {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 20px;
                    margin-top: 25px;
                }

                .summary .card {
                    text-align: center;
                }

                .summary h3 {
                    font-size: 35px;
                    color: #0d6efd;
                }

                @media(max-width:1100px) {

                    .cards {
                        grid-template-columns: repeat(2, 1fr);
                    }

                    .tables {
                        grid-template-columns: 1fr;
                    }

                    .summary {
                        grid-template-columns: repeat(2, 1fr);
                    }

                }

                @media(max-width:700px) {

                    .cards,
                    .summary {
                        grid-template-columns: 1fr;
                    }

                }

                /* ===========================
   TABLA COMPROBANTES
=========================== */

                #tablaComprobantes td:nth-child(1) {
                    font-weight: 600;
                }

                #tablaComprobantes td:nth-child(2) {
                    text-align: center;
                }

                #tablaComprobantes td:nth-child(3),
                #tablaComprobantes td:nth-child(4) {
                    text-align: right;
                }

                #tablaComprobantes tr:hover {
                    background: #f8f9fa;
                }

                #tablaComprobantes td {
                    vertical-align: middle;
                }

                #cantidadComprobantes,
                #totalComprobantes {
                    font-weight: bold;
                    color: #198754;
                }

                .badge-contado {
                    background: #28a745;
                    color: #fff;
                    padding: 4px 10px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 600;
                }

                .badge-credito {
                    background: #fd7e14;
                    color: #fff;
                    padding: 4px 10px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 600;
                }
            </style>

            <div class="container">

                <div class="header">


                    <div class="filters">

                        <input type="date">

                        <input type="date">

                    </div>

                </div>

                <div class="cards">

                    <div class="card">

                        <div class="card-top">
                            <div class="icon blue">
                                <i class="fa-solid fa-wallet"></i>
                            </div>

                            <div>
                                <h4>Total Ingresos</h4>
                                <div class="value caja-total">S/0.00</div>
                                <div class="subtitle caja-operaciones">0 operaciones</div>
                            </div>
                        </div>

                    </div>

                    <div class="card">

                        <div class="card-top">
                            <div class="icon green">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>

                            <div>
                                <h4>Efectivo</h4>
                                <div class="value caja-efectivo">S/0.00</div>
                                <div class="subtitle">Efectivo</div>
                            </div>
                        </div>

                    </div>

                    <div class="card">

                        <div class="card-top">
                            <div class="icon purple">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>

                            <div>
                                <h4>Transferencias</h4>
                                <div class="value caja-transferencias">S/0.00</div>
                                <div class="subtitle">Transferencias</div>
                            </div>
                        </div>

                    </div>

                    <div class="card">

                        <div class="card-top">
                            <div class="icon orange">
                                <i class="fa-solid fa-money-check-dollar"></i>
                            </div>

                            <div>
                                <h4>Depósitos</h4>
                                <div class="value caja-depositos">S/0.00</div>
                                <div class="subtitle">Depósitos</div>
                            </div>
                        </div>

                    </div>

                    <div class="card">

                        <div class="card-top">
                            <div class="icon red">
                                <i class="fa-solid fa-credit-card"></i>
                            </div>

                            <div>
                                <h4>Tarjetas</h4>
                                <div class="value caja-tarjetas">S/0.00</div>
                                <div class="subtitle">Tarjetas</div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="tables">

                    <div class="box">

                        <div class="box-header">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                            Cuentas por Cobrar
                        </div>

                        <table>

                            <thead>

                                <tr>

                                    <th>Forma Pago</th>
                                    <th>Banco</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>

                                </tr>

                            </thead>

                            <tbody id="tablaCuentasCobrar">
                            </tbody>

                            <tfoot>

                                <tr>

                                    <td colspan="2">TOTAL</td>

                                    <td id="cantidadCuentasCobrar">
                                        0
                                    </td>

                                    <td id="totalCuentasCobrar">
                                        S/0.00
                                    </td>

                                </tr>

                            </tfoot>

                        </table>

                    </div>

                    <div class="box">

                        <div class="box-header">
                            <i class="fa-solid fa-cart-shopping"></i>
                            Ventas
                        </div>

                        <table>

                            <thead>

                                <tr>

                                    <th>Forma Pago</th>
                                    <th>Banco</th>
                                    <th>Cantidad</th>
                                    <th>Total</th>

                                </tr>

                            </thead>

                            <tbody id="tablaVentas">
                            </tbody>

                            <tfoot>

                                <tr>

                                    <td colspan="2">TOTAL</td>

                                    <td id="cantidadVentas">
                                        0
                                    </td>

                                    <td id="totalVentas">
                                        S/0.00
                                    </td>

                                </tr>

                            </tfoot>

                        </table>

                    </div>

                </div>

                <div class="summary">

                    <div class="card">
                        <h4>Total General</h4>
                        <h3 id="summaryTotal">
                            S/0.00
                        </h3>
                    </div>


                    <div class="card">
                        <h4>Operaciones</h4>
                        <h3 id="summaryOperaciones">
                            0
                        </h3>
                    </div>


                    <div class="card">
                        <h4>Promedio</h4>
                        <h3 id="summaryPromedio">
                            S/0.00
                        </h3>
                    </div>


                    <div class="card">
                        <h4>Última actualización</h4>

                        <h3 id="summaryFecha" style="font-size:22px">
                            -
                        </h3>

                    </div>

                </div>


                <div class="row mt-4">

                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header">
                                <i class="fa-solid fa-file-invoice"></i>
                                Resumen de Comprobantes
                            </div>
                            <div class="box-body p-3">

                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon">
                                                <i class="fa-solid fa-file-lines"></i>
                                            </span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">Comprobantes</span>
                                                <span class="info-box-number" id="cmpCantidad">0</span>
                                                <small id="cmpTotal">S/ 0.00</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon">
                                                <i class="fa-solid fa-money-bill-wave"></i>
                                            </span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">Contado</span>
                                                <span class="info-box-number" id="cmpContadoCantidad">0</span>
                                                <small id="cmpContadoTotal">S/ 0.00</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon">
                                                <i class="fa-solid fa-credit-card"></i>
                                            </span>

                                            <div class="info-box-content">
                                                <span class="info-box-text">Crédito</span>
                                                <span class="info-box-number" id="cmpCreditoCantidad">0</span>
                                                <small id="cmpCreditoTotal">S/ 0.00</small>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="box">
                                <div class="box-header">
                                    <i class="fa-solid fa-file-invoice"></i>
                                    Resumen de Comprobantes
                                </div>

                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Comprobante</th>
                                            <th>Credito</th>
                                            <th>Cantidad</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tablaComprobantes"></tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="2"><strong>TOTAL</strong></td>
                                            <td class="text-center" id="cantidadComprobantes">0</td>
                                            <td class="text-right" id="totalComprobantes">S/0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<script src="vistas/js/caja-chica.js"></script>