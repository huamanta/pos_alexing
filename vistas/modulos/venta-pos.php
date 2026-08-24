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
<?php
date_default_timezone_set('America/Lima');
?>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Punto de venta</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Ventas</a></li>
                        <li class="breadcrumb-item active">Punto de venta</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" id="header">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-2 mt-4">
                                    <button type="button" class="btn btn-outline-primary btn-block" id="btnNuevo"
                                        onclick="mostrarform(true)"><i class="fa fa-plus"></i>
                                        Nuevo</button>
                                </div>
                                <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Fecha Inicio:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio"
                                            value="">
                                    </div>
                                </div>

                                <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Fecha Fin:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin"
                                            value="">
                                    </div>
                                </div>

                                <!--div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Almacén:</label>

                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-store-alt"></i>
                                            </span>
                                        </div>
                                        <select id="idsucursal2" name="idsucursal2" class="form-control select2">
                                        </select>
                                    </div>
                                </div-->
                                <div class="form-group col-lg-4 col-md-2 col-sm-4 col-xs-12">
                                    <label>Producto:</label>
                                    <select id="idproducto" name="idproducto" class="form-control select2">
                                    </select>
                                </div>
                                <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                    <label>Estado:</label>

                                    <div class="input-group">
                                        <select id="estado" name="estado" class="form-control select2">
                                            <option value="">Todos</option>
                                            <option value="Aceptado">Aceptado</option>
                                            <option value="Por Enviar">Por Enviar</option>
                                            <option value="Nota Credito">Nota de Crédito</option>
                                            <option value="Rechazado">Rechazado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center mt-3">
                                    <span class="mr-2">Mostrar</span>
                                    <select id="limit" class="form-control" style="width:100px"
                                        onchange="cambiarLimit()">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>

                                    <span class="ml-2">Registros</span>

                                </div>
                                <div class="col-md-6 mt-3">
                                    <input type="text" id="search" class="form-control" placeholder="Buscar...">
                                </div>

                            </div>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body" id="listadoregistros">
                            <table id="tbllistado" class="table table-tailpanel dt-responsive">
                                <thead>
                                    <th>Fecha</th>
                                    <th>Cliente / N° Documento</th>
                                    <th>Número</th>
                                    <th>Total Venta</th>
                                    <th>Forma de pago</th>
                                    <th>Tipo Pago</th>
                                    <th>Estado</th>
                                    <th>Sunat</th>
                                    <th style="text-align: center;"><i class="fa fa-shield" aria-hidden="true"
                                            title="Comprobar estado"></i></th>
                                    <th>Acciones</th>
                                </thead>
                                <tbody id="tbody_ventas">
                                </tbody>
                            </table>
                            <div class="row mt-1">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div id="pagination"></div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <div class="pos-form-shell" id="formularioregistros">

                        <form name="formulario" id="formulario" method="POST">
                            <input type="hidden" name="idventa" id="idventa">

                            <input type="hidden" name="tipo" id="tipo" value="venta">

                            <div class="row">

                                <div class="col-md-6" id="btnAgregarArt">

                                    <!-- <button id="btnCancelar" class="btn btn-danger btn-sm" onclick="cancelarform()" type="button">
                                                <i class="fas fa-window-close"></i> Cancelar
                                            </button> -->

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-lg-6">

                                    <div class="panel-heading">

                                        <div class="card card-outline card-danger">

                                            <div class="card shadow mb-4">
                                                <!-- Encabezado principal -->
                                                <div class="card-header bg-white border-bottom-primary">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4 class="card-title m-0 font-weight-bold text-primary">
                                                            Nueva Venta</h4>
                                                        <small id="fechaActual" class="text-muted"
                                                            style="font-size:11.5px;"></small>
                                                    </div>
                                                </div>

                                                <!-- Botón para desplegar datos -->
                                                <div class="card-header bg-light py-2 d-flex justify-content-end gap-2">
                                                    <div class="ms-auto d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-primary shadow-sm"
                                                            onclick="toggleCollapse(event,this)" data-target="detalle1"
                                                            title="Completa los datos de tu pedido">
                                                            <i class="fas fa-info-circle"></i> Datos cliente
                                                            <i class="fas fa-chevron-down" id="chevron-down"></i>
                                                            <i class="fas fa-chevron-up" id="chevron-up"
                                                                style="display:none;"></i>
                                                        </button>

                                                        <button type="button" class="btn btn-outline-info btn-sm"
                                                            data-toggle="modal" data-target="#modalAcompananteGarante">
                                                            <i class="fas fa-user-friends"></i> Datos
                                                            adicionales
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Cuerpo del formulario (oculto inicialmente) -->
                                                <div class="position-relative">
                                                    <div class="p-2 collapse collapse-section" id="detalle1">
                                                        <div class="card-body">

                                                            <!-- Sección de Personal (oculta) -->
                                                            <div class="form-group mb-3" hidden>
                                                                <label for="idpersonal" class="font-weight-bold">
                                                                    <i class="fas fa-users"></i> Personal
                                                                </label>
                                                                <select id="idpersonal" name="idpersonal"
                                                                    class="form-control select2" required></select>
                                                            </div>

                                                            <!-- Almacén y Cliente (estructura mejorada) -->
                                                            <fieldset class="border p-2 rounded mb-3">
                                                                <legend class="w-auto px-2 small font-weight-bold text-primary"> Datos principales</legend>
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-12 mb-2">
                                                                        <label for="idsucursal"
                                                                            class="font-weight-bold">
                                                                            <i class="fas fa-map-marked-alt"></i>
                                                                            Almacén
                                                                        </label>
                                                                        <select id="idsucursal" name="idsucursal"
                                                                            class="form-control"></select>
                                                                    </div>
                                                                    <div class="col-md-8 col-sm-12 mb-2">
                                                                        <div class="d-flex align-items-center justify-content-between">
                                                                            <label for="idcliente" class="font-weight-bold">
                                                                                <i class="fas fa-users"></i> Cliente
                                                                            </label>
                                                                            <div>
                                                                                <a class="text-info"
                                                                                    style="cursor:pointer;"
                                                                                    data-toggle="modal"
                                                                                    data-target="#ModalClientes">
                                                                                    <i class="fa fa-plus"></i> Nuevo
                                                                                </a>
                                                                                <a class="ml-3 text-success"
                                                                                    style="cursor:pointer;"
                                                                                    onclick="verHistorialCliente()">
                                                                                    <i class="fas fa-history"></i>
                                                                                    Historial
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <select id="idcliente" name="idcliente"
                                                                            class="form-control" required></select>
                                                                    </div>
                                                                </div>
                                                            </fieldset>

                                                            <fieldset class="border p-2 rounded mb-3">
                                                                <legend
                                                                    class="w-auto px-2 small font-weight-bold text-primary">
                                                                    Documento</legend>
                                                                <div class="row">
                                                                    <div class="col-md-4 col-sm-12 mb-2">
                                                                        <label for="tipo_comprobante"
                                                                            class="font-weight-bold">
                                                                            <i class="fas fa-file-alt"></i> Tipo
                                                                            Documento
                                                                        </label>
                                                                        <select class="form-control"
                                                                            name="tipo_comprobante"
                                                                            id="tipo_comprobante">

                                                                        </select>
                                                                        <small id="validate_categoria"
                                                                            class="text-danger d-none">Debe seleccionar
                                                                            documento</small>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-12 mb-2">
                                                                        <label for="serie_comprobante"
                                                                            class="font-weight-bold">
                                                                            <i class="fas fa-store-alt"></i> Serie
                                                                        </label>
                                                                        <input type="text"
                                                                            class="form-control form-control text-center bg-warning"
                                                                            name="serie_comprobante"
                                                                            id="serie_comprobante" maxlength="7"
                                                                            placeholder="Serie" readonly>
                                                                    </div>
                                                                    <div class="col-md-4 col-sm-12 mb-2">
                                                                        <label for="num_comprobante"
                                                                            class="font-weight-bold">
                                                                            <i class="fas fa-file-alt"></i> Nº Orden
                                                                        </label>
                                                                        <input type="text"
                                                                            class="form-control form-control text-center bg-warning"
                                                                            name="num_comprobante" id="num_comprobante"
                                                                            maxlength="10" placeholder="Número"
                                                                            readonly>
                                                                    </div>
                                                                </div>
                                                            </fieldset>

                                                            <!-- Fecha (oculta) -->
                                                            <!-- Fecha -->
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">
                                                                    <i class="far fa-calendar-alt"></i> Fecha
                                                                </label>
                                                                <input type="date" class="form-control text-center"
                                                                    name="fecha" id="fecha"
                                                                    value="<?php echo date('Y-m-d'); ?>" <?php if ($_SESSION['cargo'] !== 'Administrador'): ?>
                                                                        min="<?php echo date('Y-m-d', strtotime('-1 day')); ?>"
                                                                        max="<?php echo date('Y-m-d'); ?>" <?php endif; ?>
                                                                    required>
                                                                <?php if ($_SESSION['cargo'] !== 'Administrador'): ?>
                                                                    <small class="text-muted">Solo puedes seleccionar hoy o
                                                                        un día atrás</small>
                                                                <?php endif; ?>
                                                            </div>

                                                            <!-- Importar Cotizaciones -->
                                                            <div class="form-group mb-3">
                                                                <label for="comprobanteReferencia"
                                                                    class="font-weight-bold">
                                                                    <i class="fas fa-money-bill-alt"></i> Importar
                                                                    Cotizaciones
                                                                </label>
                                                                <select id="comprobanteReferencia"
                                                                    name="comprobanteReferencia"
                                                                    class="form-control select2"
                                                                    onchange="mostrarE();"></select>
                                                            </div>

                                                            <!-- Observaciones -->
                                                            <div class="form-group">
                                                                <label for="observaciones" class="font-weight-bold">
                                                                    <i class="fas fa-file-alt"></i> Observaciones
                                                                </label>
                                                                <textarea class="form-control" name="observaciones"
                                                                    id="observaciones" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="">Detalle de la venta</label>
                                                <input type="hidden" name="idcaja" id="idcaja" style="right: -15px;">
                                                <div id="detalles-wrapper">
                                                    <table id="detalles" class="table table-striped table-hover">
                                                        <thead class="bg-info">
                                                            <th>Producto</th>
                                                            <th>UM</th>
                                                            <th>Precio</th>
                                                            <th>Blz</th>
                                                            <th>Cantidad</th>
                                                            <th>Descuento</th>
                                                            <th>Subtotal</th>
                                                        </thead>
                                                        <tfoot>
                                                        </tfoot>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- IMPUESTOS Y TOTAL -->
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="border rounded shadow-sm overflow-hidden">

                                                    <!-- Fila Impuesto -->
                                                    <div class="d-flex">
                                                        <div class="col-9 py-2 fw-bold text-right">
                                                            Impuesto
                                                        </div>
                                                        <div class="col-3 text-right py-2">
                                                            <span class="fw-bold fs-4">S/. </span>
                                                            <span id="sp-impuesto" class="fw-bold fs-4">0.00</span>
                                                            <input type="hidden" name="impuesto" id="impuesto">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex">
                                                        <div class="col-9 py-2 fw-bold text-right">
                                                            Subtotal
                                                        </div>
                                                        <div class="col-3 text-right py-2">
                                                            <span class="fw-bold fs-4">S/. </span>
                                                            <span id="sp-subtotal" class="fw-bold fs-4">0.00</span>
                                                            <input type="hidden" name="subtotal" id="subtotal">
                                                        </div>
                                                    </div>

                                                    <!-- Fila Total -->
                                                    <div class="d-flex">
                                                        <div class="col-9 py-2 fw-bold text-right">
                                                            Total
                                                        </div>
                                                        <div class="col-3 text-right py-2">
                                                            <span class="fw-bold fs-4">S/. </span>
                                                            <span id="total" class="fw-bold fs-4">0.00</span>
                                                            <input type="hidden" name="total_venta" id="total_venta">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>

                                            <!-- MÉTODO DE PAGO -->
                                            <!--//***************************************************************************//-->

                                            <div class="card-body p-2" id="datosgenerales2">
                                                <label for="">Opciones de pago</label>
                                                <div class="row col-md-12">
                                                    <div class="col-md-2">
                                                        <label style=" font-size: 11px;">¿Crédito?</label>
                                                        <select id="tipopago" name="tipopago" class="form-control"
                                                            data-live-search="true" required>
                                                            <option value="No">No</option>
                                                            <option value="Si">Sí</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label style="font-size: 11px;">Total Depósito</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="totaldeposito"
                                                                name="totaldeposito" placeholder="Monto recibido"
                                                                value="0" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2" hidden>
                                                        <label style="font-size: 11px;">Descuento:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" name="porcentaje" id="porcentaje"
                                                                maxlength="7" placeholder="Descuento"
                                                                onkeyup="calcularPorcentaje();" disabled="disabled">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label style="font-size: 11px;">Total efectivo</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="totalrecibido"
                                                                name="totalrecibido" placeholder="Monto recibido"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label style="font-size: 11px;">Vuelto S/.</label>
                                                        <div class="d-flex">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="vuelto" name="vuelto"
                                                                readonly="">

                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label style="font-size: 11px;"></label>
                                                        <div class="d-flex">
                                                            <button type="button" class="btn btn-primary btn-sm ms-4"
                                                                id="addPago">Agregar </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row col-md-12">
                                                    <div class="col-md-12">
                                                        <label style="font-size: 11px;">Pagos Mixtos:</label>
                                                        <div id="pagosMixtosContainer">
                                                            <div class="row mb-2 pagoItem">
                                                                <div class="col-md-3">
                                                                    <select class="form-control metodoPago"
                                                                        name="metodo_pago[]">
                                                                        <option value="Efectivo">Efectivo
                                                                        </option>
                                                                        <option value="Transferencia">
                                                                            Transferencia bancaria</option>
                                                                        <option value="Tarjeta">Tarjeta POS
                                                                        </option>
                                                                        <option value="Deposito">Depósito
                                                                        </option>
                                                                        <option value="Yape">Yape</option>
                                                                        <option value="Plin">Plin</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <input type="text" class="form-control montoPago"
                                                                        name="monto_pago[]" placeholder="Monto">
                                                                    <input type="hidden" class="montoRealPago"
                                                                        name="monto_real_pago[]" value="0">
                                                                </div>
                                                                <div class="col-md-2 bancoContainer"
                                                                    style="display:none;">
                                                                    <select class="form-control bancoPago"
                                                                        name="banco_pago[]">
                                                                        <option value="">Seleccione banco</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <input type="text" class="form-control nroOperacion"
                                                                        name="nroOperacion_pago[]"
                                                                        placeholder="N° Operación">
                                                                </div>
                                                                <div class="col-md-3 fechaContainer"
                                                                    style="display:none;">
                                                                    <input type="date"
                                                                        class="form-control fechaDeposito"
                                                                        name="fecha_deposito_pago[]"
                                                                        placeholder="Fecha">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-sm removePago"><i
                                                                            class="fa fa-trash"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr style="border: 1 px solid #007bff;" />
                                                <div class="row col-md-12 mt-4">
                                                    <div class="form-group col-lg-3" id="n0">
                                                        <label>Frecuencia:</label>
                                                        <select name="input_frecuencia" id="input_frecuencia"
                                                            class="form-control" placeholder="Frecuencia">
                                                            <option value="" selected hidden>Seleccionar...
                                                            </option>
                                                            <option value="1">Diario</option>
                                                            <option value="2">Semanal</option>
                                                            <option value="3">Quincenal</option>
                                                            <option value="4">Mensual</option>
                                                            <option value="5">Bimestral</option>
                                                            <option value="6">Trimestral</option>
                                                            <option value="7">Semestral</option>
                                                            <option value="8">Anual</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-lg-3" id="n1">
                                                        <label>N° de cuotas:</label>
                                                        <select name="input_cuotas" id="input_cuotas"
                                                            class="form-control" placeholder="Cuotas">
                                                            <option value="" selected hidden>Seleccionar...
                                                            </option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                            <option value="8">8</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-lg-3" id="n2">
                                                        <label style="font-size: 11px;">N° meses:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="numeroMeses"
                                                                name="numeroMeses">
                                                        </div>
                                                    </div>

                                                    <div class="form-group col-lg-3" id="n3">
                                                        <label style="font-size: 11px;">Fecha Inicio:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="date"
                                                                class="form-control" id="fechaOperacion"
                                                                name="fechaOperacion"
                                                                value="<?php echo date("Y-m-d"); ?>">
                                                        </div>
                                                    </div>

                                                    <!--<div class="form-group col-lg-2" style="display: none;" id="n1">

                                                                    <label style="font-size: 11px;">Fecha de Pago:</label>
                                                                    <div class="input-group">
                                                                        <input style="text-align:center" type="date" class="form-control" id="fechaOperacion" name="fechaOperacion" value="<?php echo date("Y-m-d"); ?>">
                                                                    </div>

                                                                </div>-->

                                                    <div class="form-group col-lg-2" style="display: none;" id="n2">

                                                        <label style="font-size: 11px;">Monto Pagado:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="montoPagado" name="montoPagado"
                                                                value="0" onkeyup="calcularDeuda();">
                                                        </div>

                                                    </div>

                                                    <div class="form-group col-lg-2" style="display: none;" id="n4">

                                                        <label style="font-size: 11px;">Monto Deuda:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="montoDeuda" name="montoDeuda"
                                                                readonly="">
                                                        </div>

                                                    </div>

                                                    <div class="form-group col-lg-2" style="display: none;" id="n5">

                                                        <label style="font-size: 11px;">Interes %:</label>
                                                        <div class="input-group">
                                                            <input style="border-color: #FFC7BB; text-align:center"
                                                                type="text" class="form-control" id="inputInteres"
                                                                name="inputInteres" value="0">
                                                        </div>

                                                    </div>

                                                    <div class="form-group col-lg-1" style="display: none;" id="b1">
                                                        <br>
                                                        <button type="button" class="btn btn-success"
                                                            id="calcular_cuotas">Calcular</button>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="modalAcompananteGarante" tabindex="-1"
                                                    role="dialog" aria-labelledby="modalAcompananteGaranteLabel"
                                                    aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="modalAcompananteGaranteLabel">
                                                                    Datos de
                                                                    acompañante y garante</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="text-muted mb-3">Si el cliente tiene
                                                                    un acompañante o garante, por favor ingresa
                                                                    sus datos aquí.</p>
                                                                <div class="form-group">
                                                                    <label for="idtipoacompanante">Tipo de
                                                                        acompañante</label>
                                                                    <select class="form-control" id="idtipoacompanante"
                                                                        name="idtipoacompanante">
                                                                    </select>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="idacompanante">Acompañante</label>
                                                                    <select class="form-control select2"
                                                                        id="idacompanante"
                                                                        name="idacompanante"></select>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="idgarante">Garante</label>
                                                                    <select class="form-control select2" id="idgarante"
                                                                        name="idgarante"></select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cerrar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--div class="row col-md-12 mt-4" style="display: none;" id="n6">
                                                    <div class="col-lg-2">
                                                        <label style="font-size: 11px;"># de Operación:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="text"
                                                                class="form-control" id="nroOperacion"
                                                                name="nroOperacion">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2" style="display: none;" id="fechadeposito">
                                                        <label style="font-size: 11px;">Fecha Depósito:</label>
                                                        <div class="input-group">
                                                            <input style="text-align:center" type="date"
                                                                class="form-control" id="fechaDepostivo"
                                                                name="fechaDepostivo">
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-2" style="display: none;" id="banco">
                                                        <label style="font-size: 11px;">Banco:</label>
                                                        <select id="banco" name="banco" class="form-control"
                                                            data-live-search="true" title="Seleccione Banco">
                                                            <option value="BCP">BCP</option>
                                                            <option value="BBVA">BBVA</option>
                                                            <option value="INTERBANK">INTERBANK</option>
                                                            <option value="OTRO">OTRO</option>
                                                        </select>
                                                    </div>
                                                </div-->
                                                <div class="row col-md-12 mt-4" id="panel1" style="display: none;">
                                                    <table class="table" style="width:100%;">
                                                        <thead
                                                            style="display: table; width: 100%; table-layout: fixed;">
                                                            <tr>
                                                                <th>Fecha de pagos</th>
                                                                <th>Monto</th>
                                                                <th>Interés</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>

                                                        <style>
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
                                                            }
                                                        </style>

                                                        <tbody id="datafechas">
                                                            <tr>
                                                                <td colspan="4" class="text-center">
                                                                    No se han calculado las fechas de pago
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div id="fechasHiddenContainer" style="display:none;"></div>
                                            </div>
                                            <!--//***************************************************************************//-->
                                        </div>

                                        <div class="card-footer">

                                            <div class="col-md-6">
                                                <button type="button" class="btn-flotante" id="btnGuardar">
                                                    <i class="fas fa-shopping-cart"></i> Realizar Venta
                                                </button>
                                                <button id="btnCancelar" class="btn-flotante2" onclick="cancelarform()"
                                                    type="button">
                                                    <i class="fas fa-window-close"></i> Cancelar
                                                </button>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <!-- INICIO DE TABLE PRODUCTO Y SERVICIOS-->
                                <div class="col-lg-6 hidden-md hidden-sm hidden-xs">
                                    <div class="card  card-tabs">
                                        <div class="card-header p-0 pt-1">
                                            <div class="card-header p-0 pt-1">
                                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                                    <li class="nav-item" onclick="selectTab(1)">
                                                        <a class="nav-link active" id="custom-tabs-two-home-tab"
                                                            data-toggle="pill" href="#custom-tabs-two-home" role="tab"
                                                            aria-controls="custom-tabs-two-home"
                                                            aria-selected="true">Producto</a>
                                                    </li>
                                                    <li class="nav-item" onclick="selectTab(2)">
                                                        <a class="nav-link" id="custom-tabs-two-profile-tab"
                                                            data-toggle="pill" href="#custom-tabs-two-profile"
                                                            role="tab" aria-controls="custom-tabs-two-profile"
                                                            aria-selected="false">Servicio</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="card-body" style="margin-top: -35px; overflow-x: auto;">
                                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                                    <br>
                                                    <style>
                                                        .active-search {
                                                            background: #007bff;
                                                            color: white;

                                                        }

                                                        .active-search:hover {
                                                            background: #007bff;
                                                            color: white;
                                                        }
                                                    </style>
                                                    <div style="display: flex; align-items: center; gap: 8px;"
                                                        id="div_search">
                                                        <button type="button" class="btn btn-default"
                                                            id="btn_text_search" onclick="activeSearch(1)"><span
                                                                class="fas fa-keyboard"></span></button>
                                                        <button type="button" class="btn btn-default"
                                                            id="btn_barcode_search" onclick="activeSearch(2)"><span
                                                                class="fas fa-barcode"></span></button>
                                                        <input type="search" placeholder="Buscar producto"
                                                            class="form-control" id="searchProductos">
                                                        <button type="button"
                                                            class="btn btn-default mb-3 d-xl-none d-lg-none btnAgregarProducto">
                                                            Agregar producto
                                                        </button>
                                                    </div>
                                                    <div class="tab-pane fade show active" id="custom-tabs-two-home"
                                                        role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                                                        <table id="tblarticulos"
                                                            class="table table-striped table-responsive-lg"
                                                            width="100%">
                                                            <thead class="bg-info">
                                                                <th>Op</th>
                                                                <th>Codigo</th>
                                                                <th>Nombre</th>
                                                                <th>Stock</th>
                                                                <th>P Venta</th>
                                                                <th>Color</th>
                                                            </thead>
                                                            <tbody id="tbody_articulos">
                                                            </tbody>
                                                        </table>
                                                        <div class="col-md-6"></div>
                                                        <div class="col-md-6">
                                                            <div id="paginationProductos"></div>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="custom-tabs-two-profile"
                                                        role="tabpanel" aria-labelledby="custom-tabs-two-profile-tab">
                                                        <table id="tblarticulos2"
                                                            class="table table-striped table-responsive-lg"
                                                            width="100%">
                                                            <thead class="bg-info">
                                                                <th>Op</th>
                                                                <th width="200px">Nombre</th>
                                                                <th style="text-align: center;">Stock</th>
                                                                <th>P Venta</th>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                            <tfoot>
                                                                <th>Op</th>
                                                                <th>Nombre</th>
                                                                <th>Stock</th>
                                                                <th>P Venta</th>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- FIN DE TABLE PRODUCTO Y SERVICIOS-->
                            </div>
                        </form>
                    </div>

                    <div id="floating-history">
                        <div id="floating-header">
                            <span><i class="fas fa-shopping-bag mr-2"></i> Historial de Cliente</span>
                            <button type="button" class="btn btn-sm text-white"
                                onclick="$('#floating-history').fadeOut()" title="Cerrar">
                                <i class="fas fa-times fa-lg"></i>
                            </button>
                        </div>

                        <div class="search-box-historial">
                            <div class="input-group input-group-sm">
                                <input type="text" id="inputBusquedaHistorial" class="form-control"
                                    placeholder="Escribe para buscar producto...">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-white border-left-0"><i
                                            class="fas fa-search text-muted"></i></span>
                                </div>
                            </div>
                        </div>

                        <div id="floating-body">
                            <table class="table table-hover table-historial mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center" width="50">Cant.</th>
                                        <th class="text-right" width="70">Precio</th>
                                        <th class="text-right" width="60">Desc.</th>
                                        <th class="text-right" width="70">Subtotal</th>
                                        <th class="text-center" width="80">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody id="body_historial_flotante">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-body row" id="aperturarcaja">
                        <div class="col-sm-4" style="margin: 0 auto;">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card shadow" style="margin-top: -10px;">
                                        <div class="card-body">
                                            <h1 class="text-center">APERTURAR CAJA</h1>
                                            <div class="col-md-12" style="margin-bottom: 10px;">
                                                <div class="scrollmenu" for="selCategoriaReg"
                                                    style="background-color: transparent;">
                                                </div>
                                            </div>
                                            <form action="" id="formularioappcaja">
                                                <div class="col-md-12 md-1">
                                                    <div class="form-group">
                                                        <label for="">Caja</label>
                                                        <select class="form-control" name="cajas" id="cajas" required>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 md-1">
                                                    <div class="form-group">
                                                        <label for="">Efectivo</label>
                                                        <input step="0.001" type="number" class="form-control"
                                                            name="monto_apertura" id="monto_apertura" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 md-1 text-center">
                                                    <button type="submit" class="btn btn-success">Aperturar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                    <!-- /.col -->
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<div class="modal fade" id="myModal2">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Lista de ventas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">

                    </div>

                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">

                    </div>

                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <label>Almacén:</label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-store-alt"></i>
                                </span>
                            </div>
                            <select id="idsucursalVentas" name="idsucursalVentas" class="form-control" readonly>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <label>Estado:</label>

                        <div class="input-group">
                            <select id="estadoVentas" name="estadoVentas" class="form-control select2" required>
                                <option value="">Todos</option>
                                <option value="Aceptado">Aceptado</option>
                                <option value="Por Enviar">Por Enviar</option>
                                <option value="Nota Credito">Nota de Crédito</option>
                                <option value="Rechazado">Rechazado</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="tale-resposive">
                    <table id="tbllistadoVentas" class="table table-striped">
                        <thead>
                            <th>ID</th>
                            <th>Cliente / N° Documento</th>
                            <th>Sucursal</th>
                            <th>Número</th>
                            <th>Total Venta</th>
                            <th>Tipo Pago</th>
                            <th>Estado</th>
                            <th width="70px;">Sunat</th>
                            <th style="text-align: center;"><i class="fa fa-shield" aria-hidden="true"
                                    title="Comprobar estado"></i></th>
                            <th width="180px;">Acciones</th>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Sucursal</th>
                            <th>Número</th>
                            <th>Total Venta</th>
                            <th>Tipo Pago</th>
                            <th>Estado</th>
                            <th>Sunat</th>
                            <th></th>
                            <th>Acciones</th>
                        </tfoot>
                    </table>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <div></div>
                <button type="button" class="btn btn-primary" data-dismiss="modal">CERRAR</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h4 class="modal-title"><i class="fas fa-cash-register"></i> Caja Chica - Movimiento</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form class="form-horizontal" role="form" name="formularioMovimiento" id="formularioMovimiento"
                method="POST">
                <input type="hidden" name="idmovimiento" id="idmovimiento">

                <div class="modal-body" style="background-color: #f8f9fa; border-radius: 10px;">
                    <!-- Fila de selección de tipo de movimiento (Ingresos/Egresos) -->
                    <div class="row text-center">
                        <div class="form-group col-6">
                            <div class="col-sm-12 text-danger" style="text-align: center;">
                                <input type="radio" id="egresos" name="opcionEI" value="Egresos" checked=""
                                    onchange="verificarConceptoMovimiento()">
                                <label for="male">Egresos (-)</label>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <div class="col-sm-12 text-success" style="text-align: center;">
                                <input type="radio" id="ingresos" name="opcionEI" value="Ingresos"
                                    onchange="verificarConceptoMovimiento()">
                                <label for="male">Ingresos (+)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Selección de almacén y personal -->
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="name" class="control-label">Almacen <span class="text-danger">*</span></label>
                            <select id="idsucursal02" name="idsucursal02" class="form-control select2"
                                data-live-search="true">
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Concepto movimiento <span class="text-danger">*</span></label>
                            <select id="idconcepto_movimiento" name="idconcepto_movimiento" class="form-control"
                                data-live-search="true" required>
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="col-form-label">
                                <i class="fas fa-users fs-6"></i>
                                <span class="small">Personal</span>
                            </label>
                            <select id="idpersonal02" name="idpersonal02" class="form-control select2"></select>
                        </div>
                    </div>

                    <!-- Detalles de pago y monto -->
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="formapago" class="col-form-label">Forma de pago:</label>
                            <select id="formapago" name="formapago" class="form-control" required>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia bancaria</option>
                                <option value="Tarjeta">Tarjeta POS</option>
                                <option value="Deposito">Depósito</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                            </select>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="totaldeposito" class="col-form-label">Total Monto tarjeta S/.</label>
                            <input
                                style="text-align:center; background-color:#E1FEFF; border-color: #38F0F9; border-radius:10px;"
                                type="text" class="form-control" id="totaldeposito" name="totaldeposito" value="0"
                                readonly>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="noperacion" class="col-form-label"># Operación:</label>
                            <input
                                style="text-align:center; background-color:#E1FEFF; border-color: #38F0F9; border-radius:10px;"
                                type="text" class="form-control" name="noperacion" id="noperacion" maxlength="7"
                                value="0" readonly>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="montoPagar" class="col-form-label">Monto:</label>
                            <input type="number" step="any" class="form-control" id="montoPagar" name="montoPagar"
                                required>
                        </div>
                    </div>

                    <!-- Descripción del movimiento -->
                    <div class="form-group">
                        <label for="descripcion" class="col-form-label">Descripción:</label>
                        <input type="text" class="form-control" name="descripcion" id="descripcion"
                            placeholder="Descripción del movimiento (opcional)">
                    </div>
                </div>

                <!-- Footer con botones -->
                <div class="modal-footer" style="background-color: #f1f1f1;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i>
                        Cerrar</button>
                    <button class="btn btn-success" type="submit" id="btnGuardar"><i class="fas fa-save"></i>
                        Guardar Movimiento</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="getCodeModal22" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-receipt mr-2"></i>Detalle de Venta
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- Información General -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <strong><i class="fas fa-info-circle"></i> Información General</strong>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Cliente</small>
                                <h5 id="cliente" class="font-weight-bold mb-0"></h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Vendedor</small>
                                <h5 id="personalm" class="mb-0"></h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Fecha</small>
                                <h5 id="fecha_hora" class="mb-0"></h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Comprobante</small>
                                <h5 id="tipo_comprobantem" class="mb-0"></h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Correlativo</small>
                                <h5 id="correlativo" class="mb-0"></h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Forma de Pago</small>
                                <span id="formapagom" class="badge badge-success p-2"></span>
                            </div>

                            <div class="col-12">
                                <small class="text-muted">Observaciones</small>
                                <div id="observaciones" class="border rounded p-3 bg-light text-secondary">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Detalle -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <strong><i class="fas fa-shopping-cart"></i> Productos</strong>
                    </div>

                    <div class="card-body p-0">
                        <table id="detallesm" class="table table-hover table-striped mb-0" style="width: 100%;">

                        </table>
                    </div>
                </div>

                <!-- Totales -->
                <div class="card mt-3 shadow-sm">
                    <div class="card-body">

                        <div class="row text-center">

                            <div class="col-md-4">
                                <small class="text-muted d-block">Subtotal</small>
                                <h4 id="subtotalm" class="mb-0 text-primary"></h4>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">IGV</small>
                                <h4 id="impuestom" class="mb-0 text-warning"></h4>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted d-block">Total</small>
                                <h3 id="totalm" class="mb-0 text-success font-weight-bold"></h3>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>

        </div>
    </div>
</div>




<div class="modal fade" id="ModalClientes">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cliente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="limpiarCliente()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" role="form" name="formularioClientes" id="formularioClientes" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Nombre:</label>
                                <input type="hidden" name="idpersona" id="idpersona">
                                <input type="hidden" name="tipo_persona" id="tipo_persona" value="Cliente">
                                <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100"
                                    placeholder="Nombre del proveedor" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Tipo Documento:</label>
                                <select class="form-control select-picker" name="tipo_documento" id="tipo_documento"
                                    required>
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="CEDULA">CEDULA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="name" class="control-label">Número Documento:</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" name="num_documento" id="num_documento"
                                    maxlength="20" placeholder="Documento">
                                <div class="input-group-append">
                                    <span class="input-group-text" style="cursor: pointer;" id="Buscar_Cliente"
                                        onclick="BuscarCliente()" title="Buscar Cliente" type="button"><i
                                            class="fa fa-search"></i></span>
                                    <span class="input-group-text" id="cargando" title="Cargando" type="button"
                                        style="display: none;"><i><img src="files/plantilla/cargando.gif"
                                                width="15px"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Dirección:</label>
                                <input type="text" class="form-control" name="direccion" id="direccion" maxlength="70"
                                    placeholder="Dirección">
                                Estado:<label for="" id="estado2">-</label>
                                Condición:<label for="" id="condicion">-</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Teléfono:</label>
                                <input type="text" class="form-control" name="telefono" id="telefono" maxlength="20"
                                    placeholder="Teléfono">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Email:</label>
                                <input type="email" class="form-control" name="email" id="email" maxlength="50"
                                    placeholder="Email">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" onclick="limpiarCliente()" class="btn btn-default"
                        data-dismiss="modal">Cerrar</button>
                    <button class="btn btn-primary" type="submit" id="">Guardar</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- Modal para registrar número de celular -->
<div class="modal fade" id="modalCelular" tabindex="-1" role="dialog" aria-labelledby="modalCelularLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCelularLabel">Registrar Número de Celular</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="numeroCelular">Número de Celular:</label>
                <input type="text" name="numeroCelular" id="numeroCelular" class="form-control"
                    placeholder="Ingrese número de celular">
                <!-- Campos ocultos para tipo de comprobante, serie y número -->
                <input type="hidden" id="idventa">
                <input type="hidden" id="tipoComprobante">
                <input type="hidden" id="numComprobante">
                <input type="hidden" id="serieComprobante">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cancelarmodalCelular()">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="abrirWhatsApp()">Abrir WhatsApp</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Detalle Producto -->
<div class="modal fade" id="modalDetalleProducto" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel">Detalle del Producto</h5>
                <button type="button" class="btn btn-sm btn-danger ml-2" id="btnCerrarModalProducto">
                    Cerrar
                </button>
            </div>

            <div class="modal-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" id="detalleProductoTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-imagen-tab" data-toggle="tab" href="#tab-imagen" role="tab"
                            aria-controls="tab-imagen" aria-selected="true">
                            Imagen
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-detalles-tab" data-toggle="tab" href="#tab-detalles" role="tab"
                            aria-controls="tab-detalles" aria-selected="false">
                            Detalles del producto
                        </a>
                    </li>
                </ul>

                <!-- Contenido de las tabs -->
                <div class="tab-content" id="detalleProductoTabsContent">
                    <!-- TAB 1: Imagen -->
                    <div class="tab-pane fade show active text-center" id="tab-imagen" role="tabpanel"
                        aria-labelledby="tab-imagen-tab">
                        <div class="d-flex justify-content-center align-items-center border rounded shadow mb-3"
                            style="height: 600px; background-color: #f8f9fa;">
                            <img id="detalleImagenProducto" src="" alt="Producto"
                                style="max-height: 100%; max-width: 100%; object-fit: contain;">
                        </div>
                    </div>

                    <!-- TAB 2: Detalles -->
                    <div class="tab-pane fade" id="tab-detalles" role="tabpanel" aria-labelledby="tab-detalles-tab">
                        <div class="row" id="detalleProductoContenido">
                            <!-- Contenido dinámico generado por JS -->
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5 class="text-primary">Configuraciones del producto</h5>
                                <div class="accordion" id="acordeonConfiguraciones">
                                    <!-- Aquí irá el contenido generado por AJAX -->
                                    <div id="detallePreciosAdicionales">
                                        <i>Cargando...</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /.modal-body -->
        </div>
    </div>
</div>

<div class="modal fade" id="ModalPrecios">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Lista de precios</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="limpiarCliente()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive" id="tabla-precios">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalTipocomprobante">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">IMPRIMIR COMPROBANTE</h4>
                <button type="button" class="close" aria-label="Close" onclick="limpiarCliente()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" id="pant-imprimir">

                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary pull-right" type="button" onclick="sinComprobante()">SIN
                    COMPROBANTE</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
</div>
<script>
    document.addEventListener("click", cerrarCollapse);

    function toggleCollapse(e, btn) {
        e.stopPropagation();

        const panel = document.getElementById(btn.dataset.target);
        panel.classList.toggle("show");

        const icon = btn.querySelector("i");
        icon.classList.toggle("fa-chevron-down");
        icon.classList.toggle("fa-chevron-up");
    }

    function cerrarCollapse(e) {

        // Si el click fue dentro de Select2 no cerrar
        if (
            e.target.closest(".select2-container") ||
            e.target.closest(".select2-dropdown")
        ) {
            return;
        }


        document.querySelectorAll(".collapse.show").forEach(panel => {

            const btn = document.querySelector(`[data-target="${panel.id}"]`);

            if (panel.contains(e.target) || (btn && btn.contains(e.target))) {
                return;
            }

            panel.classList.remove("show");

            if (btn) {
                const icon = btn.querySelector("i");
                icon.classList.remove("fa-chevron-up");
                icon.classList.add("fa-chevron-down");
            }

        });

    }
</script>
<script src="vistas/js/venta-pos.js"></script>