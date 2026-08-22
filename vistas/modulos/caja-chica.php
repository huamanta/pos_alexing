<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Resumen caja</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Resumen caja</li>
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
                    margin: 0px auto;
                }

                .caja-status {
                    display: flex;
                    align-items: center;
                    padding: 25px;
                    gap: 20px;
                }

                .caja-status-icon {
                    width: 65px;
                    height: 65px;
                    border-radius: 50%;
                    background: #dc3545;
                    color: #fff;

                    display: flex;
                    align-items: center;
                    justify-content: center;

                    font-size: 28px;
                }

                .caja-status-content h3 {
                    margin: 0;
                    font-size: 22px;
                    font-weight: 600;
                    color: #dc3545;
                }

                .caja-status-content p {
                    margin: 5px 0 0;
                    color: #777;
                    font-size: 15px;
                }

                /* Cuando la caja está abierta */
                .caja-abierta .caja-status-icon {
                    background: #28a745;
                }

                .caja-abierta .caja-status-content h3 {
                    color: #28a745;
                }

                .caja-info-panel {
                    background: #ffffff;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
                    margin-bottom: 25px;
                    border: 1px solid #edf0f2;
                }


                /* HEADER */

                .caja-info-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 20px 25px;

                    background: linear-gradient(135deg,
                            #ffffff 0%,
                            #f8fafc 100%);

                    border-bottom: 1px solid #edf0f2;
                }


                .caja-info-title {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }


                .caja-info-icon {
                    width: 52px;
                    height: 52px;

                    border-radius: 12px;

                    display: flex;
                    align-items: center;
                    justify-content: center;

                    background: #e8f1ff;
                    color: #0d6efd;

                    font-size: 23px;
                }


                .caja-info-title h3 {
                    margin: 0;

                    font-size: 20px;
                    font-weight: 700;

                    color: #212529;
                }


                .caja-info-title span {
                    display: block;

                    margin-top: 3px;

                    font-size: 13px;

                    color: #8a94a6;
                }


                /* ESTADO */

                .caja-estado-abierta {

                    display: flex;
                    align-items: center;
                    gap: 8px;

                    padding: 8px 14px;

                    border-radius: 20px;

                    background: #eaf8ef;

                    color: #198754;

                    font-size: 12px;
                    font-weight: 700;

                    letter-spacing: .3px;
                }


                .caja-estado-punto {

                    width: 8px;
                    height: 8px;

                    border-radius: 50%;

                    background: #28a745;

                    box-shadow: 0 0 0 4px rgba(40, 167, 69, .12);
                }


                /* BODY */

                .caja-info-body {

                    display: grid;

                    grid-template-columns:
                        repeat(4, 1fr);

                    gap: 0;

                    padding: 10px 0;
                }


                /* DATO */

                .caja-dato {

                    display: flex;

                    align-items: center;

                    gap: 14px;

                    padding: 18px 24px;

                    border-right: 1px solid #edf0f2;
                }


                .caja-dato:last-child {
                    border-right: none;
                }


                /* ICONOS */

                .caja-dato-icon {

                    width: 45px;
                    height: 45px;

                    min-width: 45px;

                    border-radius: 11px;

                    display: flex;
                    align-items: center;
                    justify-content: center;

                    font-size: 18px;
                }


                .caja-icon-blue {
                    background: #e8f1ff;
                    color: #0d6efd;
                }


                .caja-icon-purple {
                    background: #f0eafa;
                    color: #6f42c1;
                }


                .caja-icon-green {
                    background: #e8f7ee;
                    color: #198754;
                }


                .caja-icon-orange {
                    background: #fff0e3;
                    color: #fd7e14;
                }


                /* TEXTO */

                .caja-dato-content {
                    min-width: 0;
                }


                .caja-dato-label {

                    display: block;

                    font-size: 11px;

                    font-weight: 700;

                    color: #8a94a6;

                    letter-spacing: .5px;

                    margin-bottom: 4px;
                }


                .caja-dato-content strong {

                    display: block;

                    font-size: 16px;

                    color: #212529;

                    white-space: nowrap;

                    overflow: hidden;

                    text-overflow: ellipsis;
                }


                .caja-dato-content .estado-texto {
                    color: #198754;
                }


                /* MONTO */

                .caja-monto .caja-dato-content strong {

                    color: #198754;

                    font-size: 18px;
                }


                /* =========================================================
   RESPONSIVE
========================================================= */

                @media (max-width: 1100px) {

                    .caja-info-body {
                        grid-template-columns: repeat(2, 1fr);
                    }

                    .caja-dato:nth-child(2) {
                        border-right: none;
                    }

                    .caja-dato:nth-child(-n+2) {
                        border-bottom: 1px solid #edf0f2;
                    }
                }


                @media (max-width: 650px) {

                    .caja-info-header {
                        align-items: flex-start;
                        flex-direction: column;
                        gap: 15px;
                    }

                    .caja-info-body {
                        grid-template-columns: 1fr;
                    }

                    .caja-dato {
                        border-right: none;
                        border-bottom: 1px solid #edf0f2;
                    }

                    .caja-dato:last-child {
                        border-bottom: none;
                    }

                }

                .header {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 20px;
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
                    width: 50px;
                    height: 50px;
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
                <!-- ESTADO DE CAJA -->
                <div id="estadoCaja" class="box mb-4" style="display:none;">
                    <div class="box-body">
                        <div class="caja-status">
                            <div class="caja-status-icon">
                                <i class="fa-solid fa-lock"></i>
                            </div>

                            <div class="caja-status-content">
                                <h3 id="estadoCajaTitulo">
                                    Caja no abierta
                                </h3>

                                <p id="estadoCajaMensaje">
                                    No tienes una caja abierta actualmente.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INFORMACIÓN DE LA CAJA -->
                <div id="informacionCaja" style="display:none;">

                    <div class="caja-info-panel">

                        <!-- HEADER -->
                        <div class="caja-info-header">

                            <div class="caja-info-title">

                                <div class="caja-info-icon">
                                    <i class="fa-solid fa-cash-register"></i>
                                </div>

                                <div>
                                    <h3>Información de la caja</h3>
                                    <span>Detalle de la apertura actual</span>
                                </div>

                            </div>

                            <div class="caja-estado-abierta">
                                <span class="caja-estado-punto"></span>
                                CAJA ABIERTA
                            </div>

                        </div>


                        <!-- DATOS -->
                        <div class="caja-info-body">

                            <!-- CAJA -->
                            <div class="caja-dato">

                                <div class="caja-dato-icon caja-icon-blue">
                                    <i class="fa-solid fa-store"></i>
                                </div>

                                <div class="caja-dato-content">

                                    <span class="caja-dato-label">
                                        CAJA
                                    </span>

                                    <strong id="cajaNombre">
                                        -
                                    </strong>

                                </div>

                            </div>


                            <!-- APERTURA -->
                            <div class="caja-dato">

                                <div class="caja-dato-icon caja-icon-purple">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </div>

                                <div class="caja-dato-content">

                                    <span class="caja-dato-label">
                                        FECHA DE APERTURA
                                    </span>

                                    <strong id="cajaAperturaFecha">
                                        -
                                    </strong>

                                </div>

                            </div>

                            <!-- MONTO INICIAL -->
                            <div class="caja-dato caja-monto">

                                <div class="caja-dato-icon caja-icon-orange">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </div>

                                <div class="caja-dato-content">

                                    <span class="caja-dato-label">
                                        MONTO INICIAL
                                    </span>

                                    <strong id="cajaMontoInicial">
                                        S/ 0.00
                                    </strong>

                                </div>

                            </div>


                            <!-- MONTO INICIAL -->
                            <div class="caja-dato caja-monto">

                                <div class="caja-dato-icon caja-icon-blue">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </div>

                                <div class="caja-dato-content">

                                    <span class="caja-dato-label">
                                        MONTO CIERRE
                                    </span>

                                    <strong id="cajaMontoCierre">
                                        S/ 0.00
                                    </strong>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <div class="header">
                    <div class="filters">
                        <input type="date">
                        <input type="date">
                    </div>

                    <div>
                        <button class="btn btn-primary" id="btnDescargar"><i class="fa fa-download"></i>
                            Descargar</button>
                        <button class="btn btn-info" data-toggle="modal" data-target="#modalResumenCaja"><i
                                class="fa fa-hand-holding-usd"></i> Caja chica</button>
                        <button class="btn btn-danger" id="btnCerrarCaja"><i class="fa fa-close"></i> Cerar
                            caja</button>
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
    <!-- MODAL RESUMEN DE CAJA CHICA -->
    <div class="modal fade" id="modalResumenCaja" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-cash-register mr-2"></i>
                        Resumen de Caja Chica
                    </h5>

                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- RESUMEN -->
                    <div class="row">


                        <!-- INGRESOS -->
                        <div class="col-md-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3 id="resumenIngresos">
                                        S/ 0.00
                                    </h3>
                                    <p>Total ingresos</p>

                                    <div class="icon">
                                        <i class="fa-solid fa-arrow-trend-up"></i>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <!-- EGRESOS -->
                        <div class="col-md-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3 id="resumenEgresos">
                                        S/ 0.00
                                    </h3>
                                    <p>Total egresos</p>

                                <div class="icon">
                                    <i class="fa-solid fa-arrow-trend-down"></i>
                                </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- OPERACIONES -->
                    <div class="row mb-4">

                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fa-solid fa-arrow-down"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Operaciones de ingreso
                                    </span>

                                    <span class="info-box-number" id="cantidadIngresos">
                                        0
                                    </span>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Operaciones de egreso
                                    </span>

                                    <span class="info-box-number" id="cantidadEgresos">
                                        0
                                    </span>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fa-solid fa-list-check"></i>
                                </span>

                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        Total operaciones
                                    </span>

                                    <span class="info-box-number" id="cantidadOperaciones">
                                        0
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- DETALLE -->
                    <div class="row">

                        <!-- INGRESOS -->
                        <div class="col-md-6">

                            <div class="card card-success">

                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fa-solid fa-arrow-trend-up mr-2"></i>
                                        Ingresos
                                    </h3>
                                </div>

                                <div class="card-body p-0">

                                    <table class="table table-hover mb-0">

                                        <thead>
                                            <tr>
                                                <th>Forma pago</th>
                                                <th>Banco</th>
                                                <th>Cantidad</th>
                                                <th class="text-right">
                                                    Monto
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody id="tablaIngresosCaja">

                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th colspan="3">
                                                    INGRESOS
                                                </th>

                                                <th class="text-right text-success" id="totalTablaIngresos">
                                                    S/ 0.00
                                                </th>
                                            </tr>
                                        </tfoot>

                                    </table>

                                </div>

                            </div>

                        </div>


                        <!-- EGRESOS -->
                        <div class="col-md-6">

                            <div class="card card-danger">

                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fa-solid fa-arrow-trend-down mr-2"></i>
                                        Egresos
                                    </h3>
                                </div>

                                <div class="card-body p-0">

                                    <table class="table table-hover mb-0">

                                        <thead>
                                            <tr>
                                                <th>Forma pago</th>
                                                <th>Banco</th>
                                                <th>Cantidad</th>
                                                <th class="text-right">
                                                    Monto
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody id="tablaEgresosCaja">

                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th colspan="3">
                                                    TOTAL
                                                </th>

                                                <th class="text-right text-danger" id="totalTablaEgresos">
                                                    S/ 0.00
                                                </th>
                                            </tr>
                                        </tfoot>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">

                        <i class="fa-solid fa-xmark mr-1"></i>
                        Cerrar

                    </button>

                </div>

            </div>
        </div>
    </div>
</div>



<script src="vistas/js/caja-chica.js"></script>