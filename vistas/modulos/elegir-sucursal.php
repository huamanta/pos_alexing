<?php
require_once __DIR__ . "/../../modelos/Usuario.php";
require_once __DIR__ . "/../../modelos/Helpers.php";
$usuario = new Usuario();
$sucursales = $usuario->listarSucursalesUsuario($_SESSION['idusuario']);
$baseUrl = rtrim($_ENV['APP_URL'], '/');
$count = count($sucursales);
$esAdmin = $usuario->esSuperusuario();
?>

<style>
    .setup-card {
        background: #fff;
        border: 1px solid #e6e8ec;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .06);
    }

    .setup-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 25px 28px;
        border-bottom: 1px solid #edf0f3;
    }

    .setup-icon {
        width: 55px;
        height: 55px;
        min-width: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        background: #f0f2ff;
        color: #2c2fa5;
        font-size: 24px;
    }

    .setup-header h3 {
        margin: 0;
        font-size: 21px;
        font-weight: 700;
        color: #292d32;
    }

    .setup-header p {
        margin: 4px 0 0;
        font-size: 13px;
        color: #8a9099;
    }


    /* PROGRESO */

    .setup-progress {
        display: flex;
        align-items: center;
        padding: 20px 28px;
        background: #fafbfc;
    }

    .setup-step {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .setup-step span {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #e9ecef;
        color: #7b8189;
        font-size: 12px;
        font-weight: 700;
    }

    .setup-step.active span {
        background: #2c2fa5;
        color: #fff;
    }

    .setup-step label {
        margin: 0;
        font-size: 12px;
        font-weight: 600;
        color: #6c737d;
    }

    .setup-line {
        width: 55px;
        height: 1px;
        margin: 0 12px;
        background: #dfe2e6;
    }


    /* INFO */

    .setup-info {
        display: flex;
        gap: 12px;
        margin: 25px 28px 5px;
        padding: 14px 16px;
        border-radius: 10px;
        background: #f5f7ff;
        border: 1px solid #e3e6ff;
    }

    .setup-info-icon {
        color: #2c2fa5;
        font-size: 18px;
        padding-top: 2px;
    }

    .setup-info strong {
        display: block;
        color: #343a40;
        font-size: 13px;
    }

    .setup-info p {
        margin: 3px 0 0;
        color: #707782;
        font-size: 12px;
        line-height: 1.5;
    }


    /* SECCIONES */

    .setup-section {
        padding: 25px 28px 5px;
    }

    .setup-section-title {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 20px;
    }

    .section-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #f3f4ff;
        color: #2c2fa5;
        font-size: 14px;
    }

    .setup-section-title h5 {
        margin: 0;
        color: #343a40;
        font-size: 15px;
        font-weight: 700;
    }

    .setup-section-title span {
        display: block;
        margin-top: 2px;
        color: #969ba3;
        font-size: 11px;
    }


    /* FORMULARIOS */

    .setup-card .form-group {
        margin-bottom: 18px;
    }

    .setup-card label {
        display: block;
        margin-bottom: 6px;
        color: #555b64;
        font-size: 12px;
        font-weight: 600;
    }

    .required {
        color: #dc3545;
    }

    .input-icon {
        position: relative;
    }

    .input-icon>i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #9298a1;
        font-size: 13px;
        z-index: 2;
    }

    .input-icon .form-control {
        height: 42px;
        padding-left: 38px;
        padding-right: 35px;
        border: 1px solid #dfe2e6;
        border-radius: 8px;
        font-size: 13px;
        box-shadow: none;
        transition: all .2s ease;
    }

    .input-icon .form-control:focus {
        border-color: #2c2fa5;
        box-shadow: 0 0 0 3px rgba(44, 47, 165, .08);
    }

    .input-suffix {
        position: absolute;
        right: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #888e97;
        font-size: 12px;
        font-weight: 600;
    }


    /* FOOTER */

    .setup-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-top: 10px;
        padding: 20px 28px;
        border-top: 1px solid #edf0f3;
        background: #fafbfc;
    }

    .setup-security {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #8a9099;
        font-size: 11px;
    }

    .setup-security i {
        color: #198754;
    }

    .setup-submit {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 10px 18px;
        border: 0;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    .setup-submit i:last-child {
        font-size: 10px;
        transition: transform .2s ease;
    }

    .setup-submit:hover i:last-child {
        transform: translateX(3px);
    }

    .sucursal-empty {
        max-width: 500px;
        margin: 60px auto;
        padding: 45px 35px;
        text-align: center;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 18px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .06);
    }

    .sucursal-empty-icon {
        width: 85px;
        height: 85px;
        margin: 0 auto 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 22px;
        background: #fff7e6;
        color: #f0ad4e;
        font-size: 34px;
    }

    .sucursal-empty h4 {
        margin: 0 0 10px;
        color: #292d32;
        font-size: 20px;
        font-weight: 700;
    }

    .sucursal-empty p {
        max-width: 390px;
        margin: 0 auto 22px;
        color: #7b818a;
        font-size: 13px;
        line-height: 1.6;
    }

    .sucursal-empty-info {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border-radius: 20px;
        background: #f7f8fa;
        color: #6c737d;
        font-size: 12px;
    }

    .sucursal-empty-info i {
        color: #2c2fa5;
    }

    .sucursal-loading {
        width: 100%;
        max-width: 430px;
        margin: 70px auto;
        padding: 40px 30px;
        text-align: center;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 18px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .07);
    }

    .sucursal-loading-icon {
        position: relative;
        width: 85px;
        height: 85px;
        margin: 0 auto 22px;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f3ff;
        color: #2c2fa5;
        font-size: 34px;
    }

    .sucursal-loading-icon i {
        animation: sucursalPulse 1.5s infinite;
    }

    .sucursal-loading-spinner {
        position: absolute;
        inset: -5px;
        border: 3px solid transparent;
        border-top-color: #2c2fa5;
        border-right-color: #2c2fa5;
        border-radius: 50%;
        animation: sucursalSpin .9s linear infinite;
    }

    .sucursal-loading h4 {
        margin: 0;
        color: #292d32;
        font-size: 20px;
        font-weight: 700;
    }

    .sucursal-loading p {
        margin: 8px 0 20px;
        color: #8a8f98;
        font-size: 13px;
    }

    .sucursal-loading-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 32px;
        padding: 7px 14px;
        border-radius: 20px;
        background: #f6f7fb;
        color: #626873;
        font-size: 12px;
        font-weight: 500;
    }

    .sucursal-loading-status i {
        color: #2c2fa5;
    }

    .sucursal-loading-status.error {
        color: #dc3545;
        background: #fff1f2;
    }

    .sucursal-loading-status.error i {
        color: #dc3545;
    }

    @keyframes sucursalSpin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes sucursalPulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.08);
            opacity: .7;
        }
    }

    .sucursales-heading {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        padding: 18px 20px;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
    }

    .sucursales-heading-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f1f3ff;
        color: #2c2fa5;
        font-size: 20px;
        margin-right: 14px;
    }

    .sucursales-heading-content {
        flex: 1;
    }

    .sucursales-heading-content h3 {
        margin: 0;
        font-size: 19px;
        font-weight: 700;
        color: #252525;
    }

    .sucursales-heading-content p {
        margin: 3px 0 0;
        font-size: 13px;
        color: #8a8f98;
    }

    .sucursales-count {
        text-align: center;
        padding-left: 25px;
        border-left: 1px solid #e9ecef;
    }

    .sucursales-count strong {
        display: block;
        font-size: 20px;
        line-height: 20px;
        color: #2c2fa5;
    }

    .sucursales-count span {
        font-size: 11px;
        color: #8a8f98;
    }


    /* TARJETA */

    .sucursal-card {
        border: 1px solid #e9ecef;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 3px 15px rgba(0, 0, 0, .05);
        transition: all .25s ease;
    }

    .sucursal-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, .10);
        border-color: #d9dcff;
    }


    /* CABECERA */

    .sucursal-header {
        height: 95px;
        padding: 15px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8f9ff;
    }

    .sucursal-icon {
        width: 62px;
        height: 62px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
        overflow: hidden;
    }

    .sucursal-icon i {
        font-size: 28px;
        color: #2c2fa5;
    }

    .sucursal-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .sucursal-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        border-radius: 20px;
        background: #e9f8ef;
        color: #198754;
        font-size: 11px;
        font-weight: 600;
    }

    .sucursal-status i {
        font-size: 7px;
    }


    /* TITULO */

    .sucursal-title {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .sucursal-title h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #252525;
    }

    .sucursal-title span {
        display: block;
        margin-top: 4px;
        font-size: 11px;
        color: #999;
    }


    /* INFORMACION */

    .sucursal-info {
        display: flex;
        flex-direction: column;
        gap: 13px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .info-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4ff;
        color: #2c2fa5;
    }

    .info-icon i {
        font-size: 13px;
    }

    .info-content {
        min-width: 0;
    }

    .info-content small {
        display: block;
        color: #9a9da5;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-bottom: 2px;
    }

    .info-content strong {
        display: block;
        color: #3d4147;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.3;
        word-break: break-word;
    }


    /* FOOTER */

    .sucursal-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .sucursal-currency span {
        display: block;
        font-size: 10px;
        color: #999;
        text-transform: uppercase;
    }

    .sucursal-currency strong {
        display: block;
        margin-top: 2px;
        font-size: 14px;
        color: #343a40;
    }

    .sucursal-currency small {
        color: #888;
        font-size: 11px;
        font-weight: 400;
    }

    .sucursal-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 0;
        border-radius: 8px;
        padding: 9px 16px;
        font-size: 12px;
        font-weight: 600;
        transition: all .2s ease;
    }

    .sucursal-btn i {
        font-size: 11px;
        transition: transform .2s ease;
    }

    .sucursal-btn:hover i {
        transform: translateX(3px);
    }
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <!-- ADMIN SIN SUCURSALES -->
                    <?php if ($count == 0 && $esAdmin): ?>

                        <div class="row justify-content-center">
                            <div class="col-xl-7 col-lg-8 col-md-10 mt-4">

                                <div class="setup-card">

                                    <div class="setup-header">
                                        <div class="setup-icon">
                                            <i class="fas fa-building"></i>
                                        </div>

                                        <div>
                                            <h3>Configura tu empresa</h3>
                                            <p>Completa la información para comenzar a utilizar Syspider</p>
                                        </div>
                                    </div>

                                    <div class="setup-progress">
                                        <div class="setup-step active">
                                            <span>1</span>
                                            <label>Empresa</label>
                                        </div>

                                        <div class="setup-line"></div>

                                        <div class="setup-step active">
                                            <span>2</span>
                                            <label>Sucursal</label>
                                        </div>
                                    </div>

                                    <div class="setup-info">
                                        <div class="setup-info-icon">
                                            <i class="fas fa-info-circle"></i>
                                        </div>

                                        <div>
                                            <strong>Configuración inicial</strong>
                                            <p>
                                                Registra los datos de tu empresa y crea tu primera sucursal.
                                                Estos datos serán utilizados en tus operaciones y comprobantes.
                                            </p>
                                        </div>
                                    </div>

                                    <form id="formCrearSucursal">

                                        <div class="setup-section">
                                            <div class="setup-section-title">
                                                <div class="section-icon">
                                                    <i class="fas fa-building"></i>
                                                </div>

                                                <div>
                                                    <h5>Datos de la empresa</h5>
                                                    <span>Información tributaria de tu empresa</span>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="ruc">
                                                            RUC
                                                            <span class="required">*</span>
                                                        </label>

                                                        <div class="input-icon">
                                                            <i class="fas fa-id-card"></i>
                                                            <input type="text" class="form-control" id="ruc" name="ruc"
                                                                maxlength="11" placeholder="Ingrese el RUC" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-7">
                                                    <div class="form-group">
                                                        <label for="razon_social">
                                                            Razón Social
                                                            <span class="required">*</span>
                                                        </label>

                                                        <div class="input-icon">
                                                            <i class="fas fa-file-signature"></i>
                                                            <input type="text" class="form-control" id="razon_social"
                                                                name="razon_social" placeholder="Razón social de la empresa"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="nombre_impuesto">
                                                            Impuesto
                                                            <span class="required">*</span>
                                                        </label>

                                                        <div class="input-icon">
                                                            <i class="fas fa-percent"></i>

                                                            <select class="form-control" id="nombre_impuesto"
                                                                name="nombre_impuesto" required>
                                                                <option value="IGV" selected>IGV</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="monto_impuesto">
                                                            Tasa del impuesto
                                                            <span class="required">*</span>
                                                        </label>

                                                        <div class="input-icon">
                                                            <i class="fas fa-percentage"></i>

                                                            <input type="number" step="0.01" class="form-control"
                                                                id="monto_impuesto" name="monto_impuesto" value="18.00"
                                                                required>

                                                            <span class="input-suffix">%</span>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="setup-section">

                                            <div class="setup-section-title">
                                                <div class="section-icon">
                                                    <i class="fas fa-store"></i>
                                                </div>

                                                <div>
                                                    <h5>Primera sucursal</h5>
                                                    <span>Información del establecimiento principal</span>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="nombre">
                                                            Nombre de la sucursal
                                                            <span class="required">*</span>
                                                        </label>

                                                        <div class="input-icon">
                                                            <i class="fas fa-store"></i>

                                                            <input type="text" class="form-control" id="nombre"
                                                                name="nombre" placeholder="Ej. Tienda Principal" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="telefono">
                                                            Teléfono
                                                        </label>

                                                        <div class="input-icon">
                                                            <i class="fas fa-phone"></i>

                                                            <input type="text" class="form-control" id="telefono"
                                                                name="telefono" placeholder="Número de teléfono">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="direccion">
                                                            Dirección
                                                        </label>

                                                        <div class="input-icon">
                                                            <i class="fas fa-map-marker-alt"></i>

                                                            <input type="text" class="form-control" id="direccion"
                                                                name="direccion" placeholder="Dirección de la sucursal">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="setup-footer">

                                            <div class="setup-security">
                                                <i class="fas fa-shield-alt"></i>

                                                <span>
                                                    Puedes modificar esta información posteriormente.
                                                </span>
                                            </div>

                                            <button type="submit" class="btn btn-primary setup-submit">
                                                <i class="fas fa-check-circle"></i>
                                                Crear y continuar
                                                <i class="fas fa-arrow-right"></i>
                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>
                        </div>

                    <?php elseif ($count == 0 && !$esAdmin): ?>

                        <div class="sucursal-empty">
                            <div class="sucursal-empty-icon">
                                <i class="fas fa-store-slash"></i>
                            </div>

                            <h4>No tienes sucursales asignadas</h4>

                            <p>
                                Actualmente no tienes ninguna sucursal disponible para trabajar.
                                Solicita a un administrador que te asigne una sucursal.
                            </p>

                            <div class="sucursal-empty-info">
                                <i class="fas fa-info-circle"></i>
                                <span>Una vez asignada, podrás acceder desde este panel.</span>
                            </div>
                        </div>

                    <?php elseif ($count == 1): ?>

                        <div class="sucursal-loading">
                            <div class="sucursal-loading-icon">
                                <i class="fas fa-store"></i>
                                <span class="sucursal-loading-spinner"></span>
                            </div>

                            <h4>Seleccionando sucursal</h4>

                            <p>
                                Estamos preparando tu sesión de trabajo...
                            </p>

                            <div class="sucursal-loading-status">
                                <i class="fas fa-circle-notch fa-spin"></i>
                                <span>Conectando con la sucursal</span>
                            </div>
                        </div>

                        <script>
                            $(document).ready(function () {

                                const loadingStatus = $('.sucursal-loading-status span');

                                setTimeout(function () {
                                    loadingStatus.text('Configurando sesión...');
                                }, 700);

                                setTimeout(function () {
                                    loadingStatus.text('Cargando información...');
                                }, 1400);

                                $.post(
                                    '<?php echo $baseUrl; ?>/controladores/usuario.php?op=seleccionarSucursal',
                                    {
                                        idsucursal: '<?php echo (int) $sucursales[0]['idsucursal']; ?>'
                                    },
                                    function (response) {

                                        if (response === 'ok') {

                                            loadingStatus
                                                .html('<i class="fas fa-check-circle"></i> Sucursal seleccionada');

                                            setTimeout(function () {
                                                window.location.href = '<?php echo $baseUrl; ?>/inicio';
                                            }, 400);

                                            return;
                                        }

                                        loadingStatus
                                            .html('<i class="fas fa-exclamation-circle"></i> No se pudo seleccionar la sucursal')
                                            .addClass('error');

                                    }
                                ).fail(function () {

                                    loadingStatus
                                        .html('<i class="fas fa-wifi"></i> Error de conexión')
                                        .addClass('error');

                                });

                            });
                        </script>


                    <?php else: ?>

                        <!-- VARIAS SUCURSALES EN CARDS -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="sucursales-heading">
                                    <div class="sucursales-heading-icon">
                                        <i class="fas fa-building"></i>
                                    </div>

                                    <div class="sucursales-heading-content">
                                        <h3>Seleccionar sucursal</h3>
                                        <p>Elige la sucursal con la que deseas trabajar</p>
                                    </div>

                                    <div class="sucursales-count">
                                        <strong><?php echo count($sucursales); ?></strong>
                                        <span>Sucursales</span>
                                    </div>
                                </div>
                            </div>

                            <?php foreach ($sucursales as $suc): ?>
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                    <div class="card sucursal-card h-100">

                                        <div class="sucursal-header">
                                            <div class="sucursal-icon">
                                                <?php if (!empty($suc['logo'])): ?>
                                                    <img src="<?php echo htmlspecialchars($suc['logo']); ?>"
                                                        alt="<?php echo htmlspecialchars($suc['nombre']); ?>">
                                                <?php else: ?>
                                                    <i class="fas fa-store"></i>
                                                <?php endif; ?>
                                            </div>

                                            <div class="sucursal-status">
                                                <i class="fas fa-circle"></i>
                                                Activa
                                            </div>
                                        </div>

                                        <div class="card-body">

                                            <div class="sucursal-title">
                                                <div>
                                                    <h4><?php echo htmlspecialchars($suc['nombre']); ?></h4>
                                                    <span>
                                                        Sucursal #<?php echo (int) $suc['idsucursal']; ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="sucursal-info">

                                                <?php if (!empty($suc['direccion'])): ?>
                                                    <div class="info-item">
                                                        <div class="info-icon">
                                                            <i class="fas fa-map-marker-alt"></i>
                                                        </div>

                                                        <div class="info-content">
                                                            <small>Dirección</small>
                                                            <strong>
                                                                <?php echo htmlspecialchars($suc['direccion']); ?>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($suc['telefono'])): ?>
                                                    <div class="info-item">
                                                        <div class="info-icon">
                                                            <i class="fas fa-phone"></i>
                                                        </div>

                                                        <div class="info-content">
                                                            <small>Teléfono</small>
                                                            <strong>
                                                                <?php echo htmlspecialchars($suc['telefono']); ?>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                            </div>

                                            <div class="sucursal-footer">

                                                <div class="sucursal-currency">
                                                    <span>Moneda</span>
                                                    <strong>
                                                        <?php echo htmlspecialchars($suc['moneda']); ?>
                                                        <small>
                                                            <?php echo htmlspecialchars($suc['simbolo']); ?>
                                                        </small>
                                                    </strong>
                                                </div>

                                                <button type="button" class="btn btn-primary sucursal-btn"
                                                    data-id="<?php echo (int) $suc['idsucursal']; ?>">
                                                    Ingresar
                                                    <i class="fas fa-arrow-right"></i>
                                                </button>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>


                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    var baseUrl = '<?php echo $baseUrl; ?>';
    $(document).ready(function () {
        $(document).on('click', '.sucursal-btn', function () {

            let idsucursal = $(this).data('id');

            $.post(
                baseUrl + '/controladores/usuario.php?op=seleccionarSucursal', {
                idsucursal: idsucursal
            },
                function (response) {

                    if (response == "ok") {
                        window.location.href = baseUrl + '/inicio';
                    } else {
                        alert("Error al seleccionar sucursal");
                    }

                }
            );

        });

        $('#formCrearSucursal').on('submit', function (e) {
            e.preventDefault();
            var ruc = $('#ruc').val();
            var razon_social = $('#razon_social').val();
            var nombre_impuesto = $('#nombre_impuesto').val();
            var monto_impuesto = $('#monto_impuesto').val();
            var nombre = $('#nombre').val();
            var direccion = $('#direccion').val();
            var telefono = $('#telefono').val();
            if (ruc && razon_social && nombre) {
                $.post(baseUrl + '/controladores/usuario.php?op=crearSucursal', {
                    ruc: ruc,
                    razon_social: razon_social,
                    nombre_impuesto: nombre_impuesto,
                    monto_impuesto: monto_impuesto,
                    nombre: nombre,
                    direccion: direccion,
                    telefono: telefono
                }, function (response) {
                    if (response == 'ok') {
                        window.location.href = baseUrl + '/inicio';
                    } else {
                        alert('Error: ' + response);
                    }
                });
            }
        });
    });
</script>