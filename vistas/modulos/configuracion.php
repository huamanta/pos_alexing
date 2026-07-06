<div class="content-wrapper">
    <style>
        /* ===== MENU ===== */

        .settings-menu {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            overflow: hidden;
        }

        .settings-menu .nav>li {
            width: 100%;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-bottom: 0;
            list-style: none;
        }

        .settings-menu .nav>li>a {
            padding: 18px 20px;
            color: #555;
            font-size: 15px;
            font-weight: 600;
            border-left: 4px solid transparent;
        }

        .settings-menu .nav>li>a i {
            width: 25px;
            text-align: center;
            font-size: 18px;
        }

        .settings-menu .nav>li.active>a {
            width: 100%;
            background: #f4f8fb;
            border-left: 4px solid #3c8dbc;
            color: #3c8dbc;
        }

        /* ===== CARD ===== */

        .setting-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            overflow: hidden;
        }

        .setting-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .setting-header h3 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }

        .setting-body {
            padding: 0;
        }

        .setting-row {
            padding: 25px;
            border-bottom: 1px solid #f3f3f3;
        }

        .setting-row:last-child {
            border-bottom: none;
        }

        .setting-title {
            font-size: 17px;
            font-weight: bold;
        }

        .setting-desc {
            color: #888;
            margin-top: 5px;
        }

        .setting-value {
            text-align: right;
            padding-top: 10px;
        }

        /* ===== SWITCH ===== */

        .switch {
            position: relative;
            display: inline-block;
            width: 62px;
            height: 34px;
        }

        .switch input {
            display: none;
        }

        .slider {
            position: absolute;
            inset: 0;
            cursor: pointer;
            background: #d8d8d8;
            border-radius: 40px;
            transition: .3s;
        }

        .slider:before {
            position: absolute;
            content: "";
            width: 28px;
            height: 28px;
            left: 3px;
            top: 3px;
            background: #fff;
            border-radius: 50%;
            transition: .3s;
        }

        .switch input:checked+.slider {
            background: #28a745;
        }

        .switch input:checked+.slider:before {
            transform: translateX(28px);
        }

        .setting-footer {
            padding: 20px;
            background: #fafafa;
            text-align: right;
        }
    </style>
    <!-- HEADER -->
    <section class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>
                        Configuración
                    </h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Configuración</li>
                    </ol>
                </div>

            </div>

        </div>
    </section>

    <section class="content">

        <div class="container-fluid">
            <div class="row">

                <!-- MENU -->

                <div class="col-md-3">

                    <div class="settings-menu">

                        <ul class="nav nav-pills nav-stacked">
                            <?php if (Helpers::getUserPermisoModulo('Mora', "Configuracion general")): ?>
                                <li class="active" id="itemMenuMora">
                                    <a href="#mora" data-toggle="tab" onclick="activarMenu('itemMenuMora')">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        Mora
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (Helpers::getUserPermisoModulo('Credito', "Configuracion general")): ?>
                                <li id="itemMenuCredito">
                                    <a href="#credito" data-toggle="tab" onclick="activarMenu('itemMenuCredito')">
                                        <i class="fa fa-credit-card"></i>
                                        Créditos
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (Helpers::getUserPermisoModulo('Refinanciamiento', "Configuracion general")): ?>
                                <li id="itemMenuRef">
                                    <a href="#ref" data-toggle="tab" onclick="activarMenu('itemMenuRef')">
                                        <i class="fa fa-user"></i>
                                        Refinanciamiento
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>

                    </div>

                </div>


                <!-- CONTENIDO -->

                <div class="col-md-9">

                    <div class="tab-content">

                        <?php if (Helpers::getUserPermisoModulo('Mora', "Configuracion general")): ?>
                            <!-- MORA -->
                            <div class="tab-pane active" id="mora">

                                <div class="setting-card">

                                    <div class="setting-header">

                                        <h3>

                                            Configuración de Mora

                                        </h3>

                                    </div>
                                    <form id="formConfiguracionMora">
                                        <div class="setting-body">

                                            <!-- FILA -->

                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <div class="setting-title">

                                                            Activar cobro de mora

                                                        </div>

                                                        <div class="setting-desc">

                                                            El sistema calculará automáticamente la mora cuando una cuota se
                                                            encuentre vencida.

                                                        </div>

                                                    </div>

                                                    <div class="col-md-4 setting-value">

                                                        <label class="switch">

                                                            <input type="checkbox" id="is_mora_credito"
                                                                name="is_mora_credito">

                                                            <span class="slider"></span>

                                                        </label>

                                                    </div>

                                                </div>

                                            </div>

                                            <!-- FILA -->

                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <div class="setting-title">

                                                            Porcentaje diario

                                                        </div>

                                                        <div class="setting-desc">

                                                            Porcentaje que se cobrará por cada día de retraso.

                                                        </div>

                                                    </div>

                                                    <div class="col-md-4">

                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <input id="valor_mora_credito" name="valor_mora_credito"
                                                                    type="number" step="0.01" class="form-control input-lg">
                                                                <span class="input-group-text">
                                                                    %
                                                                </span>
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="setting-footer">

                                            <button class="btn btn-primary btn-lg" id="btnGuardarMora">

                                                <i class="fa fa-save"></i>

                                                Guardar Configuración

                                            </button>

                                        </div>
                                    </form>

                                </div>

                            </div>

                        <?php endif; ?>
                        <?php if (Helpers::getUserPermisoModulo('Configuracion general', "Credito")): ?>
                            <!-- CREDITO -->
                            <div class="tab-pane" id="credito">

                                <div class="setting-card">

                                    <div class="setting-header">
                                        <h3>
                                            Configuración de Crédito
                                        </h3>
                                    </div>

                                    <form id="formConfiguracionCreditos">
                                        <div class="setting-body">

                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <div class="setting-title">

                                                            Notificacions automaticas

                                                        </div>

                                                        <div class="setting-desc">
                                                            El sistema enviara automáticamente notificaciones de una cuota
                                                            se
                                                            encuentre por vencer.

                                                        </div>

                                                    </div>

                                                    <div class="col-md-4 setting-value">

                                                        <label class="switch">

                                                            <input type="checkbox" id="is_notificacion"
                                                                name="is_notificacion">

                                                            <span class="slider"></span>

                                                        </label>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <div class="setting-title">

                                                            Días de gracia

                                                        </div>

                                                        <div class="setting-desc">

                                                            Días antes de empezar el cálculo de mora.

                                                        </div>

                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="input-group">
                                                            <input id="dias_gracia" name="dias_gracia" type="number"
                                                                step="0.01" class="form-control input-lg">
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>



                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <div class="setting-title">

                                                            Interés por defecto

                                                        </div>

                                                        <div class="setting-desc">

                                                            Interés sugerido al crear un crédito.

                                                        </div>

                                                    </div>

                                                    <div class="col-md-4">

                                                        <div class="input-group-prepend">
                                                            <input id="interes_defecto" name="interes_defecto" type="number"
                                                                step="0.01" class="form-control input-lg">
                                                            <span class="input-group-text">
                                                                %
                                                            </span>
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="setting-header">
                                            <h3>
                                                Amortizaciones
                                            </h3>
                                        </div>

                                        <div class="setting-body">

                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <div class="setting-title">

                                                            Descuento por pago anticipado

                                                        </div>

                                                        <div class="setting-desc">
                                                            Descuento aplicable cuando se realiza un pago anticipado.
                                                        </div>

                                                    </div>

                                                    <div class="col-md-4 setting-value">

                                                        <label class="switch">

                                                            <input type="checkbox" id="is_descuento_anticipado"
                                                                name="is_descuento_anticipado">

                                                            <span class="slider"></span>

                                                        </label>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <div class="setting-title">

                                                            Dias minimos de anticipo

                                                        </div>

                                                        <div class="setting-desc">

                                                            Numero de dias minimos a los cuales se ralizara descuento de
                                                            credito.

                                                        </div>

                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="input-group-prepend">
                                                            <input id="dias_anticipacion" name="dias_anticipacion"
                                                                type="number" step="0.01" class="form-control input-lg">
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <div class="setting-title">

                                                            Porcentaje de descuento

                                                        </div>

                                                        <div class="setting-desc">

                                                            Porcentaje de descuento aplicable al realizar un pago
                                                            anticipado.

                                                        </div>

                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="input-group-prepend">
                                                            <input id="valor_descuento_anticipado"
                                                                name="valor_descuento_anticipado" type="number" step="0.01"
                                                                class="form-control input-lg">
                                                            <span class="input-group-text">
                                                                %
                                                            </span>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="setting-footer">

                                            <button class="btn btn-primary btn-lg" id="btnGuardarCreditos">

                                                <i class="fa fa-save"></i>

                                                Guardar Configuración

                                            </button>

                                        </div>
                                    </form>
                                </div>

                            </div>
                        <?php endif; ?>
                        <?php if (Helpers::getUserPermisoModulo('Configuracion general', "Refinanciamiento")): ?>
                            <!-- REFINANCIAMIENTO -->
                            <div class="tab-pane" id="ref">

                                <div class="setting-card">

                                    <div class="setting-header">

                                        <h3>

                                            <i class="fa fa-refresh text-warning"></i>

                                            Configuración de Refinanciamiento

                                        </h3>

                                    </div>

                                    <form id="formConfiguracionRefinanciamiento">
                                        <div class="setting-body">
                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <div class="setting-title">

                                                            Activar refinanciamientos

                                                        </div>

                                                        <div class="setting-desc">
                                                            El sistema mostrara el modulo de rfinanciamientos para el
                                                            usuario .
                                                        </div>

                                                    </div>

                                                    <div class="col-md-4 setting-value">

                                                        <label class="switch">

                                                            <input type="checkbox" id="is_refinanciamiento"
                                                                name="is_refinanciamiento">

                                                            <span class="slider"></span>

                                                        </label>

                                                    </div>

                                                </div>

                                            </div>

                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <div class="setting-title">

                                                            Máximo refinanciamientos

                                                        </div>

                                                        <div class="setting-desc">

                                                            Número máximo de veces que un crédito podrá refinanciarse.

                                                        </div>

                                                    </div>

                                                    <div class="col-md-4">

                                                        <input class="form-control input-lg" type="number"
                                                            id="maximo_refinanciamientos" name="maximo_refinanciamientos">

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="setting-footer">

                                            <button class="btn btn-primary btn-lg" id="btnGuardarRefinanciamiento">

                                                <i class="fa fa-save"></i>

                                                Guardar Configuración

                                            </button>

                                        </div>
                                    </form>
                                </div>

                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>

        </div>

    </section>

</div>

<script src="vistas/js/configuracion.js"></script>