<!-- Content Wrapper. Contains page content -->
<?php
date_default_timezone_set('America/Lima');
?>

<style>

  .fila-retenida {
    background-color: #ffe5e5 !important;
    /* rojo suave */
    color: #a77170;
  }

  .fila-cuota-vencida {
    background-color: #ffd6d6 !important;
  }

  .fila-cuota-proxima {
    background-color: #fff4cc !important;
  }

  .btn-comment {
    background-color: blue !important;
    color: white !important;
  }

  .btn-descargar {
    background-color: red !important;
    color: white !important;
  }

  .btn-amortiar {
    background-color: green !important;
    color: white !important;
  }
</style>


<style type="text/css">
  /* =========================================================
   POS PROFESIONAL - DESIGN SYSTEM
   ========================================================= */

  :root {
    --pos-primary: #2563eb;
    --pos-primary-dark: #1d4ed8;
    --pos-primary-soft: #eff6ff;

    --pos-success: #16a34a;
    --pos-danger: #dc2626;
    --pos-warning: #d97706;
    --pos-info: #0891b2;

    --pos-bg: #f5f7fb;
    --pos-card: #ffffff;
    --pos-border: #e5e7eb;
    --pos-border-dark: #d1d5db;

    --pos-text: #1f2937;
    --pos-text-soft: #6b7280;
    --pos-text-muted: #9ca3af;

    --pos-radius: 10px;
    --pos-radius-sm: 7px;

    --pos-shadow: 0 2px 8px rgba(15, 23, 42, .05);
    --pos-shadow-hover: 0 8px 24px rgba(15, 23, 42, .09);

    --pos-font: "Source Sans Pro", -apple-system, BlinkMacSystemFont,
      "Segoe UI", sans-serif;
  }

  /* =========================================================
   BASE
   ========================================================= */

  body {
    background: var(--pos-bg);
    color: var(--pos-text);
    font-family: var(--pos-font);
  }

  .content-wrapper {
    background: var(--pos-bg);
  }

  .content {
    padding-bottom: 30px;
  }

  .content-header {
    padding: 15px 0 10px;
  }

  .content-header h1 {
    font-size: 22px;
    font-weight: 700;
    color: #111827;
    margin: 0;
  }

  .breadcrumb {
    background: transparent;
    margin: 0;
    font-size: 12px;
  }

  .breadcrumb-item a {
    color: var(--pos-primary);
  }

  /* =========================================================
   CARDS
   ========================================================= */

  .card {
    border: 1px solid var(--pos-border) !important;
    border-radius: var(--pos-radius) !important;
    box-shadow: var(--pos-shadow);
    background: var(--pos-card);
  }

  .card-header {
    border-bottom: 1px solid var(--pos-border);
  }

  .card-title {
    font-size: 16px;
    font-weight: 600;
  }

  .card-body {
    background: #fff;
  }

  .card-footer {
    background: #fff;
    border-top: 1px solid var(--pos-border);
  }

  /* =========================================================
   FILTROS / LISTADO
   ========================================================= */

  #header {
    background: #fff;
    padding: 14px 16px;
  }

  #header label {
    font-size: 11px;
    font-weight: 600;
    color: #4b5563;
    margin-bottom: 4px;
  }

  #header .form-group {
    margin-bottom: 0;
  }

  #header .form-control {
    height: 34px;
  }

  #header .input-group-text {
    height: 34px;
    background: #f8fafc;
    border-color: var(--pos-border);
    color: var(--pos-text-soft);
  }

  #search {
    border-radius: 8px;
  }

  #search:focus {
    border-color: var(--pos-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
  }

  /* =========================================================
   INPUTS
   ========================================================= */

  .form-control {
    border: 1px solid var(--pos-border-dark);
    border-radius: var(--pos-radius-sm);
    color: var(--pos-text);
    font-size: 13px;
    transition: border-color .18s ease, box-shadow .18s ease;
  }

  .form-control:hover {
    border-color: #b8c0cc;
  }

  .form-control:focus {
    border-color: var(--pos-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
  }

  textarea.form-control {
    resize: vertical;
  }

  .form-group label {
    font-size: 12px;
    font-weight: 600;
    color: #374151;
  }

  .input-group-text {
    border-radius: var(--pos-radius-sm);
    background: #f8fafc;
    border-color: var(--pos-border-dark);
    color: #64748b;
  }

  /* =========================================================
   SELECT2
   ========================================================= */

  .select2-container {
    width: 100% !important;
  }

  .select2-container--default .select2-selection--single {
    height: 34px !important;
    border: 1px solid var(--pos-border-dark) !important;
    border-radius: var(--pos-radius-sm) !important;
    display: flex;
    align-items: center;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 32px !important;
    font-size: 13px;
    color: var(--pos-text);
    padding-left: 10px;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px !important;
  }

  .select2-container--default.select2-container--focus .select2-selection--single {
    border-color: var(--pos-primary) !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
  }

  .select2-dropdown {
    border: 1px solid var(--pos-border) !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .12);
    overflow: hidden;
  }

  .select2-results__option {
    font-size: 13px;
    padding: 8px 10px;
  }

  .select2-results__option--highlighted {
    background: var(--pos-primary) !important;
  }

  /* =========================================================
   FORMULARIO POS
   ========================================================= */

  #formularioregistros.pos-form-shell {
    margin-top: 0px;
  }

  #formularioregistros.pos-form-shell .row.mb-3 {
    margin-bottom: 0 !important;
  }

  #formularioregistros.pos-form-shell .col-lg-6[style*="margin-top: -20px"] {
    margin-top: 0 !important;
  }

  #formularioregistros.pos-form-shell .panel-heading {
    border: none !important;
  }

  #formularioregistros.pos-form-shell .card.card-outline.card-danger {
    border: none !important;
    background: transparent;
    margin-top: 0 !important;
    box-shadow: none;
  }

  #formularioregistros.pos-form-shell .card.shadow.mb-4 {
    margin-bottom: 12px !important;
  }

  /* =========================================================
   ENCABEZADO NUEVA VENTA
   ========================================================= */

  #formularioregistros .card-header.bg-white {
    background: #fff !important;
  }

  #formularioregistros .card-header.bg-white.border-bottom-primary {
    border-bottom: 1px solid var(--pos-border) !important;
    background: #fff !important;
  }

  #formularioregistros .card-title.text-primary {
    color: #111827 !important;
    font-size: 17px;
    font-weight: 700;
  }

  #fechaActual {
    color: var(--pos-text-muted) !important;
  }

  /* =========================================================
   BOTONES
   ========================================================= */

  .btn {
    border-radius: var(--pos-radius-sm);
    font-size: 12px;
    font-weight: 600;
    transition: all .18s ease;
  }

  .btn-primary {
    background: var(--pos-primary);
    border-color: var(--pos-primary);
  }

  .btn-primary:hover,
  .btn-primary:focus {
    background: var(--pos-primary-dark);
    border-color: var(--pos-primary-dark);
    box-shadow: 0 4px 12px rgba(37, 99, 235, .20);
  }

  .btn-success {
    background: var(--pos-success);
    border-color: var(--pos-success);
  }

  .btn-danger {
    background: var(--pos-danger);
    border-color: var(--pos-danger);
  }

  .btn-outline-info {
    color: var(--pos-info);
    border-color: #a5dfe8;
  }

  .btn-outline-info:hover {
    background: var(--pos-info);
    border-color: var(--pos-info);
    color: #fff;
  }

  .btn-xs {
    padding: 4px 8px;
    font-size: 11px;
  }

  .btn-sm {
    border-radius: 7px;
  }

  /* =========================================================
   DATOS CLIENTE
   ========================================================= */

  #formularioregistros fieldset {
    border: 1px solid var(--pos-border) !important;
    background: #fafbfc;
    border-radius: 9px;
    padding: 12px !important;
  }

  #formularioregistros fieldset legend {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--pos-primary) !important;
    background: #fff;
    border-radius: 5px;
  }

  #formularioregistros fieldset label {
    color: #374151;
  }

  #formularioregistros fieldset .text-info {
    color: var(--pos-info) !important;
    font-size: 11px;
  }

  #formularioregistros fieldset .text-success {
    color: var(--pos-success) !important;
    font-size: 11px;
  }

  /* =========================================================
   COLLAPSE DATOS CLIENTE
   ========================================================= */

  .collapse-section {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    width: 100%;
    z-index: 1050;

    background: #fff;
    border: 1px solid var(--pos-border);
    border-radius: 10px;

    box-shadow: 0 15px 40px rgba(15, 23, 42, .14);

    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);

    transition:
      opacity .18s ease,
      transform .18s ease,
      visibility .18s ease;
  }

  .collapse-section.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }

  /* =========================================================
   TABLA DETALLE VENTA
   ========================================================= */

  #detalles-wrapper {
    max-height: 320px;
    overflow-y: auto;
    width: 100%;
    border: 1px solid var(--pos-border);
    border-radius: 9px 9px 0px 0px;
    background: #fff;
  }

  #detalles {
    width: 100% !important;
    min-width: 100% !important;
    margin: 0 !important;
    font-size: 11px;
    table-layout: fixed;
  }

  #detalles thead {
    position: sticky;
    top: 0;
    z-index: 5;
  }

  #detalles thead th {
    background: #f8fafc !important;
    color: #475569 !important;
    border: none !important;
    border-bottom: 1px solid var(--pos-border) !important;
    padding: 8px 5px !important;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
  }

  #detalles tbody td {
    padding: 7px 5px !important;
    vertical-align: middle;
    border-color: #f0f2f5;
  }

  #detalles tbody tr:hover {
    background: #f8fbff;
  }

  #detalles tbody .fila-vacia-detalles td {
    width: 100% !important;
    padding: 25px 10px !important;
    color: var(--pos-text-muted);
    text-align: center;
  }

  /* Scroll */

  #detalles-wrapper::-webkit-scrollbar,
  #floating-body::-webkit-scrollbar,
  #datafechas::-webkit-scrollbar {
    width: 6px;
  }

  #detalles-wrapper::-webkit-scrollbar-track,
  #floating-body::-webkit-scrollbar-track,
  #datafechas::-webkit-scrollbar-track {
    background: #f8fafc;
  }

  #detalles-wrapper::-webkit-scrollbar-thumb,
  #floating-body::-webkit-scrollbar-thumb,
  #datafechas::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
  }

  #detalles-wrapper::-webkit-scrollbar-thumb:hover,
  #floating-body::-webkit-scrollbar-thumb:hover,
  #datafechas::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }

  /* =========================================================
   TABLAS PRODUCTOS
   ========================================================= */

  #tblarticulos,
  #tblarticulos2,
  #tbllistado,
  #tbllistadoVentas {
    font-size: 11.5px;
  }

  #tblarticulos thead th,
  #tblarticulos2 thead th {
    background: #f8fafc !important;
    color: #475569;
    border-top: none;
    border-bottom: 1px solid var(--pos-border);
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
  }

  #tblarticulos td,
  #tblarticulos th,
  #tblarticulos2 td,
  #tblarticulos2 th {
    padding: 6px 5px;
    vertical-align: middle;
  }

  #tblarticulos tbody tr:hover,
  #tblarticulos2 tbody tr:hover {
    background: #f8fbff;
  }

  /* =========================================================
   TABLA LISTADO DE VENTAS
   ========================================================= */

  #tbllistado {
    margin-bottom: 0;
  }

  #tbllistado thead th,
  #tbllistadoVentas thead th {
    background: #f8fafc;
    color: #475569;
    border-top: none;
    border-bottom: 1px solid var(--pos-border);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .25px;
  }

  #tbllistado tbody td,
  #tbllistadoVentas tbody td {
    padding: 7px 6px;
    vertical-align: middle;
  }

  #tbllistado tbody tr,
  #tbllistadoVentas tbody tr {
    transition: background .15s ease;
  }

  #tbllistado tbody tr:hover,
  #tbllistadoVentas tbody tr:hover {
    background: #f8fbff;
  }

  /* =========================================================
   TABS PRODUCTO / SERVICIO
   ========================================================= */

  .card-tabs .nav-tabs {
    border-bottom: 1px solid var(--pos-border);
  }

  .card-tabs .nav-tabs .nav-link {
    border: none;
    color: #64748b;
    font-size: 12px;
    font-weight: 600;
    padding: 9px 15px;
    border-bottom: 2px solid transparent;
  }

  .card-tabs .nav-tabs .nav-link:hover {
    color: var(--pos-primary);
  }

  .card-tabs .nav-tabs .nav-link.active {
    color: var(--pos-primary);
    background: transparent;
    border-bottom: 2px solid var(--pos-primary);
  }

  /* =========================================================
   BUSCADOR PRODUCTOS
   ========================================================= */

  #div_search {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
  }

  #div_search input {
    height: 36px;
    border-radius: 8px;
  }

  #div_search .btn {
    height: 36px;
    min-width: 36px;
  }

  .active-search {
    background: var(--pos-primary) !important;
    color: #fff !important;
    border-color: var(--pos-primary) !important;
  }

  /* =========================================================
   TOTALES
   ========================================================= */

  #formularioregistros .border.rounded.shadow-sm {
    border: 1px solid var(--pos-border) !important;
    border-radius: 0px 0px 10px 10px !important;
    overflow: hidden;
    box-shadow: none !important;
    background: #fff;
  }

  #formularioregistros .border.rounded.shadow-sm .d-flex {
    border-bottom: 1px solid #f0f2f5;
  }

  #formularioregistros .border.rounded.shadow-sm .d-flex:last-child {
    background: #f8fbff;
    border-bottom: none;
  }

  #formularioregistros .border.rounded.shadow-sm .fw-bold {
    font-size: 12px;
  }

  #sp-impuesto,
  #sp-subtotal {
    color: #475569;
  }

  #total {
    color: var(--pos-success) !important;
    font-size: 22px !important;
    font-weight: 800 !important;
  }

  /* =========================================================
   PAGOS
   ========================================================= */

  #pagosMixtosContainer {
    background: #f8fafc;
    border: 1px solid var(--pos-border);
    border-radius: 9px;
    padding: 10px 8px 3px;
  }

  .pagoItem {
    background: #fff;
    border: 1px solid var(--pos-border);
    border-radius: 8px;
    padding: 8px 4px;
    margin-bottom: 8px !important;
  }

  .pagoItem:hover {
    border-color: #cbd5e1;
  }

  .pagoItem .form-control {
    font-size: 11px;
  }

  /* =========================================================
   BOTONES FLOTANTES
   ========================================================= */

  .btn-flotante,
  .btn-flotante2 {
    position: fixed;
    bottom: 22px;

    height: 48px;
    padding: 0 20px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 10px;
    border: none;

    color: #fff;
    font-size: 12px;
    font-weight: 700;

    letter-spacing: .3px;

    box-shadow: 0 8px 20px rgba(15, 23, 42, .18);

    transition:
      transform .18s ease,
      box-shadow .18s ease,
      background .18s ease;

    z-index: 999;
  }

  .btn-flotante {
    right: 25px;
    background: var(--pos-success);
  }

  .btn-flotante2 {
    right: 190px;
    background: #64748b;
  }

  .btn-flotante:hover,
  .btn-flotante2:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(15, 23, 42, .22);
    color: #fff;
  }

  .btn-flotante:hover {
    background: #15803d;
  }

  .btn-flotante2:hover {
    background: #475569;
  }

  /* =========================================================
   HISTORIAL FLOTANTE
   ========================================================= */

  #floating-history {
    display: none;

    position: fixed;
    top: 85px;
    right: 20px;

    width: 600px;
    max-width: calc(100vw - 30px);

    background: #fff;

    border: 1px solid var(--pos-border);
    border-radius: 12px;

    box-shadow: 0 20px 50px rgba(15, 23, 42, .20);

    overflow: hidden;

    z-index: 99999;

    font-family: var(--pos-font);

    will-change: top, left;
  }

  #floating-header {
    background: #fff;
    color: #111827;

    padding: 12px 15px;

    border-bottom: 1px solid var(--pos-border);

    cursor: grab;
    user-select: none;

    display: flex;
    justify-content: space-between;
    align-items: center;

    font-weight: 700;
    font-size: 13px;
  }

  #floating-header:active {
    cursor: grabbing;
  }

  #floating-header i {
    color: var(--pos-primary);
  }

  #floating-header button {
    color: #64748b !important;
  }

  .search-box-historial {
    padding: 9px;
    background: #f8fafc;
    border-bottom: 1px solid var(--pos-border);
  }

  .search-box-historial input {
    border-radius: 20px !important;
    padding-left: 14px;
    border: 1px solid var(--pos-border);
  }

  #floating-body {
    max-height: 450px;
    overflow-y: auto;
    background: #fff;
  }

  .table-historial {
    margin-bottom: 0 !important;
  }

  .table-historial th {
    background: #f8fafc;
    color: #64748b;
    font-weight: 700;
    border: none !important;
    border-bottom: 1px solid var(--pos-border) !important;
    font-size: 12px;
    text-transform: uppercase;
    position: sticky;
    top: 0;
    z-index: 10;
  }

  .table-historial td {
    vertical-align: middle !important;
    font-size: 11px;
    padding: 7px 5px !important;
    border-bottom: 1px solid #f1f5f9;
  }

  .table-historial tbody tr:hover {
    background: #f8fbff;
  }

  .resaltado-carrito {
    background: #fffbeb !important;
    border-left: 3px solid #f59e0b;
  }

  .resaltado-carrito td {
    color: #92400e;
    font-weight: 600;
  }

  /* =========================================================
   MODALES
   ========================================================= */

  .modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(15, 23, 42, .20);
    overflow: hidden;
  }

  .modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, .12);
  }

  .modal-title {
    font-size: 16px;
    font-weight: 700;
  }

  .modal-body {
    background: #fff;
  }

  .modal-footer {
    background: #f8fafc;
    border-top: 1px solid var(--pos-border);
  }

  /* Modal detalle */

  #getCodeModal22 .card {
    box-shadow: none;
    border: 1px solid var(--pos-border) !important;
  }

  #getCodeModal22 .card-header {
    background: #f8fafc !important;
    color: #374151;
  }

  #getCodeModal22 small.text-muted {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .4px;
  }

  #getCodeModal22 h5 {
    font-size: 14px;
  }

  /* =========================================================
   MODAL CLIENTES
   ========================================================= */

  #ModalClientes .modal-header,
  #ModalPrecios .modal-header,
  #ModalTipocomprobante .modal-header {
    background: #fff;
    color: #111827;
    border-bottom: 1px solid var(--pos-border);
  }

  #ModalClientes .modal-title,
  #ModalPrecios .modal-title,
  #ModalTipocomprobante .modal-title {
    color: #111827;
  }

  /* =========================================================
   MODAL MOVIMIENTO CAJA
   ========================================================= */

  #myModal .modal-body {
    background: #f8fafc !important;
    border-radius: 0 !important;
  }

  #myModal .form-group label {
    font-size: 11px;
  }

  #myModal input[readonly] {
    background: #f1f5f9 !important;
    border-color: var(--pos-border) !important;
    color: #475569;
  }

  /* =========================================================
   APERTURA DE CAJA
   ========================================================= */

  #aperturarcaja {
    background: transparent;
  }

  #aperturarcaja .card {
    border-radius: 14px !important;
    box-shadow: 0 10px 35px rgba(15, 23, 42, .08);
  }

  #aperturarcaja h1 {
    font-size: 22px;
    font-weight: 800;
    color: #111827;
    letter-spacing: .5px;
  }

  /* =========================================================
   IMÁGENES PRODUCTO
   ========================================================= */

  .img-thumbnail {
    border: 1px solid var(--pos-border);
    border-radius: 8px;
  }

  .img-producto {
    cursor: pointer;
    transition: transform .2s ease, box-shadow .2s ease;
  }

  .img-producto:hover {
    transform: scale(1.04);
    box-shadow: 0 5px 15px rgba(15, 23, 42, .12);
  }

  /* =========================================================
   PRODUCTO DETALLE
   ========================================================= */

  #modalDetalleProducto .nav-tabs {
    border-bottom: 1px solid var(--pos-border);
  }

  #modalDetalleProducto .nav-tabs .nav-link {
    border: none;
    color: #64748b;
    font-size: 12px;
    font-weight: 600;
  }

  #modalDetalleProducto .nav-tabs .nav-link.active {
    color: var(--pos-primary);
    border-bottom: 2px solid var(--pos-primary);
  }

  #detalleImagenProducto {
    padding: 15px;
  }

  /* =========================================================
   FECHAS DE CUOTAS
   ========================================================= */

  #datafechas {
    display: block;
    max-height: 300px;
    overflow-y: auto;
    width: 100%;
  }

  #datafechas tr {
    display: table;
    width: 100%;
    table-layout: fixed;
  }

  #datafechas td {
    width: 25%;
    font-size: 11px;
  }

  /* =========================================================
   SEPARADORES
   ========================================================= */

  #formularioregistros hr {
    border: 0;
    border-top: 1px solid var(--pos-border);
    margin: 14px 0;
  }

  /* =========================================================
   ZOOM GLOBAL
   ========================================================= */

  .scale-global {
    zoom: .85;
    transform-origin: top center;
  }

  @supports not (zoom: 1) {
    .scale-global {
      transform: scale(.85);
      transform-origin: top center;
    }
  }

  /* =========================================================
   RESPONSIVE
   ========================================================= */

  @media (max-width: 991px) {

    #formularioregistros.pos-form-shell {
      padding: 10px;
    }

    #formularioregistros.pos-form-shell #datosgenerales,
    #formularioregistros.pos-form-shell #datosgenerales2 {
      padding: 10px !important;
    }

    #floating-history {
      width: calc(100vw - 20px);
      right: 10px;
      top: 65px;
    }

    .btn-flotante,
    .btn-flotante2 {
      bottom: 15px;
      height: 44px;
    }

    .btn-flotante {
      right: 15px;
    }

    .btn-flotante2 {
      right: 165px;
    }
  }

  @media (max-width: 600px) {

    .content-header h1 {
      font-size: 18px;
    }

    #header {
      padding: 10px;
    }

    #detalles-wrapper {
      max-height: 260px;
    }

    .btn-flotante,
    .btn-flotante2 {
      font-size: 11px;
      padding: 0 14px;
    }

    .btn-flotante {
      right: 10px;
    }

    .btn-flotante2 {
      right: 145px;
    }

    #floating-history {
      width: calc(100vw - 16px);
      right: 8px;
    }

    #total {
      font-size: 19px !important;
    }
  }

  /* =========================================================
   ESTADOS / BADGES
   ========================================================= */

  .badge {
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 8px;
  }

  /* =========================================================
   UTILIDADES
   ========================================================= */

  .text-primary {
    color: var(--pos-primary) !important;
  }

  .bg-primary {
    background-color: var(--pos-primary) !important;
  }

  .text-success {
    color: var(--pos-success) !important;
  }

  .text-danger {
    color: var(--pos-danger) !important;
  }

  /* =========================================================
   FOCUS ACCESIBLE
   ========================================================= */

  button:focus,
  a:focus,
  select:focus,
  input:focus {
    outline: none;
  }

  /* =========================================================
   TABLA RESPONSIVE
   ========================================================= */

  .table-responsive {
    overflow-x: auto;
    max-width: 100%;
  }

  /* =========================================================
   EVITAR EXCESO DE MARGENES DEL TEMPLATE
   ========================================================= */

  #formularioregistros.pos-form-shell .card.card-outline.card-danger .card.shadow {
    margin-top: 0 !important;
  }

  #formularioregistros.pos-form-shell .col-lg-6[style*="margin-top"] {
    margin-top: 0 !important;
  }

  /* =========================================================
   TRANSICIONES GENERALES
   ========================================================= */

  .card,
  .form-control,
  .btn,
  .table tbody tr,
  .select2-selection {
    transition: all .18s ease;
  }
</style>
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Cuentas por Cobrar</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Creditos</li>
            <li class="breadcrumb-item active">Cuentas por Cobrar</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div id="panelSuperiorCxC">
                <div class="col-md-3">

                </div>
                <div class="row">

                  <div class="form-group col-lg-2 col-md-3 col-sm-4 col-xs-12">
                    <label>Fecha Inicio:</label>

                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="far fa-calendar-alt"></i>
                        </span>
                      </div>
                      <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" />
                    </div>
                  </div>

                  <div class="form-group col-lg-2 col-md-3 col-sm-4 col-xs-12">
                    <label>Fecha Fin:</label>

                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="far fa-calendar-alt"></i>
                        </span>
                      </div>
                      <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" />
                    </div>
                  </div>

                  <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <label>Cliente:</label>

                    <div class="input-group ">
                      <select id="idcliente" name="idcliente" class="form-control select2" required>
                      </select>
                    </div>
                    <div id="btnEstadoCuenta" style="display:none;">
                      <button type="button" class="btn btn-info btn-sm" id="btnEstadoCuentaAccion">
                        Estado de Cuenta
                      </button>
                    </div>
                  </div>

                  <div class="form-group col-lg-5 col-md-3 col-sm-4 col-xs-12 text-right">
                    <br>
                    <div style="display:flex; justify-content:flex-end; gap:8px; flex-wrap:nowrap;">
                      <button class="btn btn-danger" id="btnGenerarReporte" onclick="generarReporte();"><i
                          class="fa fa-file"></i>
                        Consolidado
                      </button>
                      <button class="btn btn-warning" id="btnEnviarRecordatorioSemana">
                        <i class="fas fa-paper-plane"></i> Recordatorios
                      </button>
                      <button onclick="descragarResumen()" type="button" class="btn btn-success"
                        id="btnEstadoCuentaAccion">
                        <i class="fas fa-file-excel"></i> RESUMEN
                      </button>
                    </div>
                  </div>

                </div>
                <!-- row Tarjetas Informativas -->
                <div class="row">
                  <div class="col-lg-4" style="color: blue; font-weight: 900; font-size: 25px">
                    <!-- small box -->
                    <div class="small-box ">
                      <div class="inner">
                        <h4 id=""></h4>
                        <p>Total: <span id="saldos"></span></p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i>
                        <!-- Utilizando la clase fa-lg -->
                      </div>
                    </div>
                  </div>

                  <!-- TARJETA TOTAL COMPRAS -->
                  <div class="col-lg-4" style="color: green; font-weight: 900; font-size: 25px">
                    <!-- small box -->
                    <div class="small-box ">
                      <div class="inner">
                        <h4 id=""></h4>
                        <p>Abono: <span id="abonos"></span></p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-money-bill fa-lg" style="font-size:20px !important"></i>
                        <!-- Utilizando la clase fa-lg -->
                      </div>
                    </div>
                  </div>

                  <!-- TARJETA TOTAL VENTAS -->
                  <div class="col-lg-4">
                    <!-- small box -->
                    <div class="small-box ">
                      <div class="inner" style="color: red; font-weight: 900; font-size: 25px">
                        <h4 id=""></h4>
                        <p>Deuda: <span id="deudas"></span></p>
                      </div>
                      <div class="icon" id="panel_amortizar">

                      </div>
                    </div>
                  </div>
                </div>
                <!-- ./row Tarjetas Informativas -->
              </div>
              <div class="row" id="vistaListaClientes">
                <div class="col-md-6 d-flex align-items-center">
                  <span class="mr-2">Mostrar</span>
                  <select id="limit" class="form-control" style="width:100px">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>

                  <span class="ml-2">Registros</span>

                </div>
                <div class="col-md-6">
                  <input type="text" id="search" class="form-control" placeholder="Buscar...">
                </div>
                <div class="col-md-12 mt-2">
                  <div class="table-responsive">
                    <table id="tbllistadocuentasxcobrar" class="table table-striped">
                      <thead>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>N° documento</th>
                        <th>Total creditos</th>
                        <th>Deuda total</th>
                        <th>Total pagado</th>
                        <th>Saldo pendiente</th>
                        <th>Acciones</th>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-6">
                  <div id="pagination"></div>
                </div>
              </div>
              <div id="vistaCreditosCliente" style="display:none;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h5 style="margin:0;">Créditos / Ventas de <span id="detalleClienteTitulo"></span></h5>
                  <button type="button" class="btn btn-secondary btn-sm" onclick="volverListaClientes()">
                    <i class="fas fa-arrow-left"></i> Volver
                  </button>
                </div>

                <table id="tbllistadoCreditosCliente" class="table table-striped table-bordered" width="100%">
                  <thead>
                    <th>Fecha Venta</th>
                    <th>Documento</th>
                    <th>Total Venta</th>
                    <th>Inicial</th>
                    <th>N° Cuotas</th>
                    <th>Interes</th>
                    <th>Total Abonado</th>
                    <th>Saldo Pendiente</th>
                    <th>Refinanciado</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->

        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>

<div class="modal fade" id="modalCuotasCredito">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Cuentas por Cobrar del Crédito: <strong id="tituloCreditoCuotas"></strong></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row table-responsive">
          <table id="tbllistadoCuotasCredito" class="table table-striped table-bordered" width="100%">
            <thead>
              <th>Fecha Registro</th>
              <th>Fecha Vencimiento</th>
              <th>Abonado</th>
              <th>Deuda</th>
              <th>Saldo</th>
              <th>Estado</th>
              <th>Acciones</th>
            </thead>
            <tbody></tbody>
            <tfoot>
              <th>Fecha Registro</th>
              <th>Fecha Vencimiento</th>
              <th>Abonado</th>
              <th>Deuda</th>
              <th>Saldo</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalComentario">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Lista de seguimiento de credito</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row table-responsive">
          <table id="tbllistadohistorial" class="table table-striped table-bordered" width="100%">
            <thead>
              <th>#</th>
              <th>Tipo</th>
              <th>Descripcion</th>
              <th>Detalle</th>
              <th>Fecha programada</th>
              <th>Estado</th>
              <th>Prioridad</th>
              <th>Acciones</th>
            </thead>
            <tbody></tbody>
            <tfoot>
              <th>#</th>
              <th>Tipo</th>
              <th>Descripcion</th>
              <th>Detalle</th>
              <th>Fecha programada</th>
              <th>Estado</th>
              <th>Prioridad</th>
              <th>Acciones</th>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalIncidencias">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Lista de seguimiento de credito</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row table-responsive">
          <table id="tbllistadohistorialIncidencias" class="table table-striped table-bordered" width="100%">
            <thead>
              <th>#</th>
              <th style="width: 300px;">Detalle</th>
              <th>F. compromiso</th>
              <th>Monto</th>
              <th>F. cumplimiento</th>
              <th>Descp.</th>
              <th>Acciones</th>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalAdjuntos">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          Archivos Adjuntos
        </h5>

        <button type="button" class="close" data-dismiss="modal">

          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">

        <div id="contenidoAdjuntos"></div>

      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="getCodeModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header modal-header-custom">
        <h5 class="modal-title"><i class="fa fa-money"></i> Registro de Pago / Abono</h5>
        <button type="button" class="close text-white" data-dismiss="modal">×</button>
      </div>

      <form class="form-horizontal" role="form" id="formulario" method="POST">

        <div class="modal-body">

          <!-- Campos ocultos -->
          <input type="hidden" name="idcpc" id="idcpc">
          <input type="hidden" id="idcaja" name="idcaja">
          <input type="hidden" name="idventa" id="idventa">
          <style>
            .doc-card {
              border: 1px solid #e9edf3;
              background: #fff;
              border-radius: 12px;
              padding: 14px;
              box-shadow: 0 6px 18px rgba(16, 24, 40, .06);
              max-width: 720px;
            }

            .doc-head {
              display: flex;
              gap: 12px;
              align-items: center;
              padding-bottom: 12px;
              border-bottom: 1px dashed #e9edf3;
            }

            .doc-icon {
              width: 40px;
              height: 40px;
              border-radius: 10px;
              display: grid;
              place-items: center;
              background: rgba(25, 118, 210, .10);
              color: #1976d2;
              font-size: 18px;
            }

            .doc-title strong {
              display: block;
              font-size: 15px;
              color: #0f172a;
            }

            .doc-sub {
              margin-top: 2px;
              font-size: 13px;
              color: #64748b;
            }

            .doc-body {
              padding-top: 12px;
              display: grid;
              gap: 12px;
            }

            .doc-alert {
              background: #f8fafc;
              border: 1px solid #eef2f7;
              border-radius: 12px;
              padding: 12px;
            }

            .doc-alert-row {
              display: flex;
              justify-content: space-between;
              align-items: center;
              gap: 10px;
              padding: 4px 0;
              font-size: 14px;
              color: #334155;
            }

            .money {
              color: #0f172a;
            }

            .doc-totals {
              display: grid;
              grid-template-columns: repeat(2, minmax(0, 1fr));
              gap: 12px;
            }

            .total-box {
              border: 1px solid #eef2f7;
              border-radius: 12px;
              padding: 12px;
              background: #ffffff;
            }

            .total-label {
              font-size: 12px;
              color: #64748b;
              margin-bottom: 6px;
            }

            .total-value {
              font-size: 18px;
              font-weight: 700;
              color: #0f172a;
            }

            /* Responsive */
            @media (max-width: 520px) {
              .doc-totals {
                grid-template-columns: 1fr;
              }
            }
          </style>

          <div class="doc-body mb-2">
            <div class="doc-totals">
              <div class="total-box">
                <div class="total-label">Total venta</div>
                <div class="total-value">S/ <span id="valorVenta"></span></div>
              </div>

              <div class="total-box">
                <div class="total-label">Total interés</div>
                <div class="total-value">S/ <span id="valorInteres"></span></div>
              </div>
            </div>
          </div>


          <!-- Caja de información -->
          <div class="info-box-custom">
            <strong><i class="fa fa-info-circle"></i> Información del Documento</strong><br>
            El documento <b><span id="documento"></span></b> tiene un pago pendiente de
            <b>S/ <span id="deutaTotal"></span></b>.
            Debe pagarse como máximo el día <b><span id="fechavencimiento"></span></b>.
          </div>

          <div class="warning-box-custom" id="panelMora">
            <strong><i class="fa fa-exclamation-triangle"></i> Tiene mora </strong><br>
            La cuota ha generado <b>S/<span id="montoMora"></span></b> de mora por <b><span id="diasRetraso"></span></b>
            dias de retraso en el pago programado de los cuales falta pagar <b>S/<span id="montoMoraPagar"></span></b>.
          </div>

          <div class="success-box-custom" id="panelDescuento">
            <strong>
              <i class="fas fa-hand-holding-usd"></i>
              ¡Descuento por pago anticipado!
            </strong>
            <br>

            Has obtenido un descuento del <strong> <span id="porcentajeDescuento"></span>%</strong>
            con valor de <strong>S/ <span id="montoDescuento"></span></strong>
            por realizar el pago
            <strong><span id="diasAnticipacion"></span> días antes</strong>
            de la fecha de vencimiento.
          </div>

          <div class="section-title"><i class="fa fa-credit-card"></i> Datos del Pago</div>

          <div class="row">

            <div class="col-sm-3">
              <div class="form-group">
                <label>Condición de Pago:</label>
                <select id="formapago" name="formapago" class="form-control" required>
                  <option value="Efectivo">Efectivo</option>
                  <option value="Transferencia">Transferencia o Tarjeta</option>
                  <option value="Yape">Yape</option>
                  <option value="Plin">Plin</option>
                  <option value="Deposito">Depósito</option>
                </select>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label>Monto Efectivo:</label>
                <input type="text" class="form-control" id="montoPagar" name="montoPagar" required>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label>Monto Tarjeta:</label>
                <input type="text" class="form-control" id="montoPagarTarjeta" name="montoPagarTarjeta" value="0"
                  readonly>
              </div>
            </div>

            <div class="col-sm-3">
              <div class="form-group">
                <label>Total a pagar:</label>
                <input class="form-control" type="text" name="montoAdeudado" id="montoAdeudado" readonly>
              </div>
            </div>

          </div>

          <div class="section-title"><i class="fa fa-pencil"></i> Observación</div>

          <div class="row">
            <div class="col-sm-12">
              <textarea class="form-control" name="observacion" id="observacion" rows="2"></textarea>
            </div>
          </div>

          <div class="section-title"><i class="fa fa-building"></i> Pago Bancario</div>

          <div class="row">

            <div class="col-sm-4">
              <div class="form-group">
                <label>Banco:</label>
                <select id="banco" name="banco" class="form-control selectpicker" data-live-search="true">
                  <option value="">Seleccione...</option>
                </select>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label>Número de Operación (OP):</label>
                <input class="form-control" type="text" name="op" id="op">
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label>Fecha de Pago:</label>
                <input class="form-control" type="datetime-local" name="fechaPago" id="fechaPago"
                  value="<?php echo date('Y-m-d H:i:s') ?>">
              </div>
            </div>

          </div>

        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarform()" class="btn btn-secondary" data-dismiss="modal">
            <i class="fa fa-times"></i> Cerrar
          </button>
          <button class="btn btn-primary" type="submit" id="btnGuardarPago">
            <i class="fa fa-check"></i> Guardar Pago
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<div class="modal fade" id="getCodeModal2">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><span id="titulo-formulario">Lista de</span> abonos</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario" id="formulario" method="POST">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="alert" style="background: #E0F7FA;">
                <strong><i class="fa fa-info"></i> Info!</strong> El monto total del documento electrónico es de <label
                  for="abonoTotal2" id="abonoTotal2"></label>, y se han registrado abonos por un total de <label
                  for="abonoTotal" id="abonoTotal"></label>.
              </div>
            </div>
          </div>
          <input type="hidden" name="idcpc" id="idcpc">
          <input type="hidden" id="idcaja" name="idcaja">
          <input type="hidden" name="idventa" id="idventa">

          <div class="col-md-12">
            <div class="table-responsive">
              <table id="tbllistado" class="table table-striped table-hover" width="100%">
                <thead>
                  <th>Fecha</th>
                  <th>Metodo</th>
                  <th>Documento</th>
                  <th>Eectivo</th>
                  <th>Deposito/trans.</th>
                  <th>N° op.</th>
                  <th>Banco</th>
                  <th>Acciones</th>
                </thead>
                <tbody id="listaComprobantes">
                </tbody>
              </table>
            </div>
          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" onclick="cancelarform()" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnGuardar">Guardar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>


<div class="modal fade" id="modalAmortizar">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><span id="titulo-formulario-amortizar">Lista de</span> Abonos</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" role="form" name="formulario-amortizar" id="formulario-amortizar" method="POST">
        <div class="modal-body">

          <input type="hidden" name="idcliente_amortizar" id="idcliente_amortizar">
          <input type="hidden" name="idventa_amortizar" id="idventa_amortizar">
          <input type="hidden" id="idcaja" name="idcaja">
          <input type="hidden" name="fecha_inicio_amortizar" id="fecha_inicio_amortizar">
          <input type="hidden" name="fecha_fin_amortizar" id="fecha_fin_amortizar">

          <div class="alert" style="background: #E0F7FA;">
            <strong><i class="fa fa-info"></i> Info!</strong> Amortizacion: tiene un pago pendiente de S/ <label
              for="deudaTotalAmortizar" id="deudaTotalAmortizar"></label>, el cuál se esta realizando una amortizacion;
            A continuación Ingresa el total de dinero abonado y luego haz click en Guardar.
          </div>

          <div class="success-box-custom" id="panelDescuentoAmortizar">
            <strong>
              <i class="fas fa-hand-holding-usd"></i>
              ¡Descuento por pago anticipado!
            </strong>
            <br>
            Has obtenido un descuento de <strong>S/ <span id="montoDescuentoAmortizar"></span></strong>
            por realizar el pagoantes de la fecha de vencimiento tu crédito.
          </div>

          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Condición de Pago:</label>
                <select id="formapagoAmortizar" name="formapagoAmortizar" class="form-control selectpicker"
                  data-live-search="true" required>
                  <option value="Efectivo">Efectivo</option>
                  <option value="Transferencia">Transferencia o Tarjeta</option>
                  <option value="Yape">Yape</option>
                  <option value="Plin">Plin</option>
                  <option value="Deposito">Depósito</option>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Monto a Pagar: </label>
                <input type="text" class="form-control" id="montoPagarAmortizar" name="montoPagarAmortizar" required="">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="name" class="control-label">Monto Adeudado:</label>
                <input class="form-control pull-right" type="text" name="montoAdeudadoAmortizar"
                  id="montoAdeudadoAmortizar" readonly="">
              </div>
            </div>
          </div>
          <div class="row" id="panelTransferencia" style="display: none;">
            <div class="col-sm-4">
              <div class="form-group">
                <label>Banco:</label>
                <select id="bancoAmortizar" name="bancoAmortizar" class="form-control selectpicker"
                  data-live-search="true">
                  <option value="">Seleccione...</option>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label>Monto transferido:</label>
                <input class="form-control" type="text" name="montoTransferenciaAmortizar" id="opAmortizar">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label>Número de Operación (OP):</label>
                <input class="form-control" type="text" name="opAmortizar" id="opAmortizar">
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label>Fecha de Pago:</label>
                <input class="form-control" type="datetime-local" name="fechaPagoAmortizar" id="fechaPagoAmortizar"
                  value="<?php echo date('Y-m-d H:i:s') ?>">
              </div>
            </div>
          </div>
          <style>
            .card-cuotas {
              background: #ffffff;
              border-radius: 16px;
              padding: 25px;
              box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
              margin: auto;
              font-family: Arial, sans-serif;
            }

            .titulo-cuotas {
              margin-bottom: 20px;
              color: #333;
              text-align: center;
            }

            .range-container {
              margin-bottom: 25px;
            }

            #rangeCuotas {
              width: 100%;
              accent-color: #2563eb;
              cursor: pointer;
            }

            .info-cuotas {
              display: flex;
              gap: 15px;
            }

            .box-info {
              flex: 1;
              background: #f5f7fb;
              padding: 15px;
              border-radius: 12px;
              text-align: center;
            }

            .box-info .label {
              display: block;
              font-size: 14px;
              color: #666;
              margin-bottom: 8px;
            }

            .box-info .valor {
              font-size: 24px;
              font-weight: bold;
              color: #222;
            }

            .box-info.total {
              background: #2563eb;
            }

            .box-info.total .label,
            .box-info.total .valor {
              color: white;
            }
          </style>
          <div class="row" id="panel-pagar-cuotas">
            <div class="col-sm-12">
              <div class="card-cuotas">

                <h3 class="titulo-cuotas">
                  Seleccionar cuotas
                </h3>

                <div id="contenedorRange" class="range-container"></div>

                <div class="info-cuotas">

                  <div class="box-info">
                    <span class="label">Cuotas a pagar</span>
                    <input class="valor" id="cantidadSeleccionada" style="width: 80px; text-align: center;" />
                  </div>

                  <div class="box-info total">
                    <span class="label">Total a pagar</span>
                    <span class="valor">S/ <span id="totalPagar">0.00</span></span>
                  </div>

                </div>

              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button class="btn btn-primary" type="submit" id="btnAmortizarDeuda">Guardar</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- Modal para mostrar resultados -->
<div class="modal fade" id="modalRecordatorioResultados" tabindex="-1" role="dialog"
  aria-labelledby="modalRecordatorioResultadosLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Resultados de envío de recordatorios</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="recordatorioResultadosContenido">
        <!-- Aquí se mostrará la tabla con resultados -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEstadoCuenta">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Estado de Cuenta del Cliente</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
        <div id="estadoCuentaContenido"></div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button class="btn btn-primary" onclick="imprimirEstadoCuenta()">Imprimir</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL PROGRAMAR VISITA -->
<div class="modal fade" id="modalProgramarVisita" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header bg-warning">
        <h5 class="modal-title">
          <i class="fas fa-calendar-check"></i>
          Programar evento
        </h5>

        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <form id="formProgramarVisita" enctype="multipart/form-data">

        <div class="modal-body">

          <input type="hidden" name="idcpc" id="idcpc_visita">
          <input type="hidden" name="idventa" id="idventa_visita">
          <input type="hidden" name="idcliente" id="idcliente_visita">

          <div class="row">
            <div class="col-md-7">
              <div class="row">

                <!-- RESPONSABLE -->
                <div class="form-group col-md-6">
                  <label>
                    Responsable
                  </label>

                  <select class="form-control" name="idpersonal" id="idpersonal">

                    <option value="">Seleccione</option>

                    <!-- CARGAR USUARIOS -->
                  </select>
                </div>

                <!-- TIPO -->
                <div class="form-group col-md-6">
                  <label>Tipo visita</label>

                  <select class="form-control" name="tipo_visita" id="tipo_visita">
                    <option value="REUNION">
                      REUNION
                    </option>
                    <option value="VISITA">
                      VISITA
                    </option>
                    <option value="COBRANZA">
                      COBRANZA
                    </option>
                    <option value="REUNION">
                      REUNION
                    </option>
                    <option value="LLAMADA">
                      LLAMADA
                    </option>
                    <option value="WHATSAPP">
                      WHATSAPP
                    </option>
                    <option value="CORREO">
                      CORREO
                    </option>
                    <option value="VERIFICACION">
                      VERIFICACION
                    </option>
                    <option value="SEGUIMIENTO">
                      SEGUIMIENTO
                    </option>

                    <option value="NEGOCIACION">
                      NEGOCIACION
                    </option>
                    <option value="COBRANZA">
                      OTRO
                    </option>

                  </select>
                </div>

                <!-- PRIORIDAD -->
                <div class="form-group col-md-6">
                  <label>Prioridad</label>
                  <select class="form-control" name="prioridad" id="prioridad">
                    <option value="BAJA">Baja</option>
                    <option value="MEDIA">Media</option>
                    <option value="ALTA">Alta</option>
                    <option value="URGENTE">Urgente</option>
                  </select>
                </div>

                <!-- PRIORIDAD -->
                <div class="form-group col-md-6">
                  <label>Estado</label>

                  <select class="form-control" name="estado" id="estado">

                    <option value="PENDIENTE">PENDIENTE</option>
                    <option value="REALIZADO">REALIZADO</option>
                    <option value="NO_RESPONDE">NO RESPONDE</option>
                    <option value="REPROGRAMADO">REPROGRAMADO</option>

                  </select>
                </div>


                <!-- FECHA -->
                <div class="form-group col-md-6">
                  <label>
                    Fecha visita <span class="text-danger">*</span>
                  </label>

                  <input type="datetime-local" class="form-control" name="fecha_programada" id="fecha_programada"
                    required>
                </div>


                <!-- FECHA -->
                <div class="form-group col-md-6">
                  <label>
                    Fecha final
                  </label>
                  <input type="datetime-local" class="form-control" name="fecha_final" id="fecha_final">
                </div>

                <!-- DIRECCION -->
                <div class="form-group col-md-12">
                  <label>Dirección</label>

                  <input type="text" class="form-control" name="direccion" id="direccion"
                    placeholder="Ingrese dirección de visita">
                </div>

                <!-- OBSERVACION -->
                <div class="form-group col-md-12">
                  <label>Observación</label>

                  <textarea class="form-control" name="descripcion" id="descripcion" rows="4"
                    placeholder="Detalle de la visita..."></textarea>
                </div>
              </div>
            </div>

            <style>
              .upload-box {
                border: 2px dashed #f0ad4e;
                border-radius: 15px;
                background: #fffaf2;
                cursor: pointer;
                transition: .3s;
              }

              .upload-box:hover {
                background: #fff3df;
                border-color: #ec971f;
              }

              .preview-item {
                border: 1px solid #eee;
                border-radius: 12px;
                padding: 10px;
                background: white;
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 10px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, .05);
              }

              .preview-left {
                display: flex;
                align-items: center;
                gap: 10px;
              }

              .preview-left i {
                font-size: 30px;
              }

              .btn-delete-file {
                border: none;
                background: #dc3545;
                color: white;
                width: 30px;
                height: 30px;
                border-radius: 50%;
              }
            </style>
            <div class="col-md-5">
              <div class="row">
                <!-- ADJUNTOS -->
                <div class="form-group col-md-12">

                  <label>
                    Adjuntar archivos
                  </label>

                  <div class="custom-file-upload">

                    <input type="file" id="adjuntos" class="d-none" multiple accept="
                .pdf,
                .doc,
                .docx,
                .xls,
                .xlsx,
                .jpg,
                .jpeg,
                .png,
                .webp,
                .mp4,
                .mp3
            ">

                    <label for="adjuntos" class="upload-box w-100">

                      <div class="text-center p-4">

                        <i class="fas fa-cloud-upload-alt fa-3x text-warning mb-3"></i>

                        <h5 class="mb-2">
                          Subir documentos
                        </h5>

                        <p class="text-muted mb-2">
                          Agrega múltiples archivos
                        </p>

                        <span class="badge badge-warning p-2">
                          PDF · Word · Excel · Imágenes · Audio · Video
                        </span>

                      </div>

                    </label>

                  </div>

                  <!-- LISTA -->
                  <div id="previewArchivos" class="row mt-3"></div>

                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-secondary" data-dismiss="modal">

            <i class="fas fa-times"></i>
            Cerrar
          </button>

          <button type="submit" class="btn btn-warning">

            <i class="fas fa-save"></i>
            Guardar programación
          </button>

        </div>

      </form>
      <!-- ADJUNTOS -->

    </div>
  </div>
</div>

<div class="modal fade" id="modalCompromisoPago" tabindex="-1" role="dialog" aria-labelledby="modalCompromisoPagoLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">

      <div class="modal-header bg-warning">
        <h4 class="modal-title">
          <i class="fas fa-file-signature"></i>
          Registrar Compromiso de Pago
        </h4>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <form id="formCompromisoPago">

        <div class="modal-body">

          <input type="hidden" id="idcpcProgramado" name="idcpc">
          <input type="hidden" id="idventaProgramado" name="idventa">
          <input type="hidden" id="idclienteProgramado" name="idcliente">
          <div id="contenedorMensajeMora"></div>
          <div class="form-group">
            <label>
              Fecha de Compromiso <span class="text-danger">*</span>
            </label>
            <input type="date" class="form-control" id="fecha_compromiso" name="fecha_compromiso" required>
          </div>

          <div class="form-group">
            <label>
              Monto Comprometido <span class="text-danger">*</span>
            </label>
            <input type="number" step="0.01" min="0" class="form-control" id="monto" name="monto" placeholder="0.00"
              required>
          </div>

          <div class="form-group">
            <label>Observación</label>
            <textarea class="form-control" id="observacion" name="observacion" rows="4"
              placeholder="Detalle del compromiso realizado con el cliente"></textarea>
          </div>

        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cancelar
          </button>

          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Guardar Compromiso
          </button>

        </div>

      </form>

    </div>
  </div>
</div>


<div class="modal fade" id="modalCalendario" tabindex="-1" role="dialog" aria-labelledby="modalCompromisoPagoLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header bg-warning">
        <h4 class="modal-title">
          <i class="fas fa-file-signature"></i>
          Calendario de pagos
        </h4>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <form id="formCompromisoPago">

        <div class="modal-body">

          <div id="calendario"></div>

        </div>

        <div class="modal-footer">

          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cancelar
          </button>

          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Guardar Compromiso
          </button>

        </div>

      </form>

    </div>
  </div>
</div>


<div class="modal fade" id="modalHistorialCredito">

  <div class="modal-dialog modal-xl">

    <div class="modal-content">

      <div class="modal-header bg-primary">

        <h4 class="modal-title">
          <i class="fa fa-history"></i>
          Historial del Crédito y Refinanciamientos
        </h4>

        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>

      </div>

      <div class="modal-body" style="background:#f4f6f9;">

        <!-- Resumen -->

        <div class="row">

          <div class="col-md-3">

            <div class="info-box bg-aqua">

              <span class="info-box-icon">
                <i class="fa fa-user"></i>
              </span>

              <div class="info-box-content">

                <span class="info-box-text">
                  Cliente
                </span>

                <span class="info-box-number" id="hisCliente"></span>

              </div>

            </div>

          </div>

          <div class="col-md-3">

            <div class="info-box bg-green">

              <span class="info-box-icon">
                <i class="fa fa-id-card"></i>
              </span>

              <div class="info-box-content">

                <span class="info-box-text">
                  Documento
                </span>

                <span class="info-box-number" id="hisDocumento"></span>

              </div>

            </div>

          </div>

          <div class="col-md-3">

            <div class="info-box bg-yellow">

              <span class="info-box-icon">
                <i class="fa fa-calendar"></i>
              </span>

              <div class="info-box-content">

                <span class="info-box-text">
                  Fecha Crédito
                </span>

                <span class="info-box-number" id="hisFecha"></span>

              </div>

            </div>

          </div>

          <div class="col-md-3">

            <div class="info-box bg-red">

              <span class="info-box-icon">
                <i class="fa fa-money"></i>
              </span>

              <div class="info-box-content">

                <span class="info-box-text">
                  Total Crédito
                </span>

                <span class="info-box-number" id="hisTotal"></span>

              </div>

            </div>

          </div>

        </div>

        <!-- Timeline -->

        <div class="box box-solid">

          <div class="box-header with-border">

            <h3 class="box-title">

              <i class="fa fa-stream"></i>

              Línea de Tiempo del Crédito

            </h3>

          </div>

          <div class="box-body">

            <div id="timelineCredito"></div>

          </div>

        </div>

      </div>

      <div class="modal-footer">

        <button class="btn btn-default" data-dismiss="modal">

          <i class="fa fa-times"></i>

          Cerrar

        </button>

      </div>

    </div>

  </div>

</div>

<div class="modal fade" id="modalAjuntarComp">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAjuntarCompLabel">Adjuntar comprobante</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formAdjuntarComp" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="idcpcShow">
          <input type="hidden" id="iddcpc">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="comprobanteAdjunto">Documento</label>
                <input
                  type="file"
                  class="form-control"
                  id="comprobanteAdjunto"
                  name="comprobante"
                  accept="image/*,.pdf">
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-upload"></i> Adjuntar
          </button>

          <button
            type="button"
            class="btn btn-secondary"
            onclick="cerarrAjuntarComp()">
            Cerrar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript" src="vistas/js/cuentascobrar.js"></script>
<script type="text/javascript" src="vistas/js/ventasfechacliente2.js"></script>