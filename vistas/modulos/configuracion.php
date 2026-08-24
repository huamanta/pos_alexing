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

        .logo-upload {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .logo-preview {
            width: 220px;
            height: 220px;
            border-radius: 15px;
            border: 2px dashed #ced4da;
            object-fit: contain;
            padding: 5px;
            background: #fff;
            transition: .3s;
        }

        .logo-upload:hover .logo-preview {
            opacity: .2;
        }

        .overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #0d6efd;
            font-weight: bold;
            opacity: 0;
            transition: .3s;
        }

        .logo-upload:hover .overlay {
            opacity: 1;
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
                            <?php if ($helpers->getUserPermisoModulo('Configuracion general', 'Configuracion')): ?>
                                <li class="active" id="itemMenuGeneral">
                                    <a href="#general" data-toggle="tab" onclick="activarMenu('itemMenuGeneral')">
                                        <i class="fa fa-home"></i>
                                        General
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($helpers->getUserPermisoModulo('Configuracion credito', 'Configuracion')): ?>
                                <li id="itemMenuCredito">
                                    <a href="#credito" data-toggle="tab" onclick="activarMenu('itemMenuCredito')">
                                        <i class="fa fa-credit-card"></i>
                                        Créditos
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($helpers->getUserPermisoModulo('Configuracion mora', 'Configuracion')): ?>
                                <li id="itemMenuMora">
                                    <a href="#mora" data-toggle="tab" onclick="activarMenu('itemMenuMora')">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        Mora
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($helpers->getUserPermisoModulo('Configuracion refinanciamiento', 'Configuracion')): ?>
                                <li id="itemMenuRef">
                                    <a href="#ref" data-toggle="tab" onclick="activarMenu('itemMenuRef')">
                                        <i class="fa fa-user"></i>
                                        Refinanciamiento
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($helpers->getUserPermisoModulo('Configuracion facturacion', 'Configuracion')): ?>
                                <li id="itemMenuFacturacion">
                                    <a href="#facturacion" data-toggle="tab" onclick="activarMenu('itemMenuFacturacion')">
                                        <i class="fa fa-folder-open"></i>
                                        Facturacion
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>

                    </div>

                </div>

                <!-- CONTENIDO -->

                <div class="col-md-9">

                    <div class="tab-content">
                        <?php if ($helpers->getUserPermisoModulo('Configuracion general', 'Configuracion')): ?>
                            <div class="tab-pane active" id="general">
                                <div class="setting-card">
                                    <div class="setting-header">

                                        <h3>

                                            Configuración general

                                        </h3>

                                    </div>
                                    <form id="formConfiguracionGeneral">
                                        <div class="setting-body">
                                            <!-- FILA -->
                                            <div class="setting-row">
                                                <div class="row">
                                                    <input type="hidden" id="idsucursal" name="idsucursal">
                                                    <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                                        <label><strong> Datos generales</strong></label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="name" class="control-label">Logo de la empresa:</label>
                                                        <div class="text-center">
                                                            <label for="logo" class="logo-upload">
                                                                <img id="preview_logo" src="files/logo/default.png"
                                                                    class="logo-preview">

                                                                <div class="overlay">
                                                                    <i class="fa fa-camera fa-2x"></i>
                                                                    <br>
                                                                    Cambiar Logo
                                                                </div>

                                                            </label>

                                                            <input type="file" id="logo" name="logo" accept="image/*"
                                                                hidden>

                                                        </div>

                                                        <div class="text-center mt-3 text-muted">
                                                            JPG, PNG o WEBP (Máximo 2 MB)
                                                        </div>

                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label for="name" class="control-label">Nombre:</label>
                                                                    <input type="text" class="form-control" name="nombre"
                                                                        id="nombre" placeholder="Nombre" required="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="name"
                                                                        class="control-label">Telefono:</label>
                                                                    <input type="text" class="form-control" name="telefono"
                                                                        id="telefono" maxlength="50" placeholder="Telefono"
                                                                        required="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label for="name"
                                                                        class="control-label">Dirección:</label>
                                                                    <input type="text" class="form-control" name="direccion"
                                                                        id="direccion" placeholder="Direccion" required="">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="email" class="control-label">Email:</label>
                                                                    <input type="email" class="form-control" name="email"
                                                                        id="email" maxlength="50" required="" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="setting-row">
                                                <div class="row">
                                                    <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                                        <label><strong> Ubicacion</strong></label>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="departamento_select"
                                                                class="col-sm-12 control-label">Departamento:</label>
                                                            <div class="col-sm-12">
                                                                <select class="form-control" name="departamento_select"
                                                                    id="departamento_select">
                                                                    <option value="">Seleccione Departamento</option>
                                                                </select>
                                                                <input type="hidden" name="departamento" id="departamento">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="provincia_select"
                                                                class="col-sm-12 control-label">Provincia:</label>
                                                            <div class="col-sm-12">
                                                                <select class="form-control" name="provincia_select"
                                                                    id="provincia_select" disabled>
                                                                    <option value="">Seleccione Provincia</option>
                                                                </select>
                                                                <input type="hidden" name="provincia" id="provincia">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="distrito_select"
                                                                class="col-sm-12 control-label">Distrito:</label>
                                                            <div class="col-sm-12">
                                                                <select class="form-control" name="distrito_select"
                                                                    id="distrito_select" disabled>
                                                                    <option value="">Seleccione Distrito</option>
                                                                </select>
                                                                <input type="hidden" name="distrito" id="distrito">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="ubigeo" class="control-label">Ubigeo:</label>
                                                            <input type="text" class="form-control" name="ubigeo"
                                                                id="ubigeo" maxlength="50" required="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="setting-row">
                                                <div class="row">
                                                    <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                                        <label><strong> Moneda</strong></label>
                                                    </div>
                                                    <div class="form-group col-lg-3 col-md-6 col-xs-12">
                                                        <label for="name" class="control-label">Moneda:</label>
                                                        <select class="form-control" type="text" name="moneda" id="moneda">
                                                            <option value="" selected>Seleccionar...</option>
                                                            <option value="PEN">SOLES</option>
                                                            <option value="USD">Dolares</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="setting-footer">
                                            <button class="btn btn-primary btn-lg" id="btnGuardarGeneral">
                                                <i class="fa fa-save"></i>
                                                Guardar Configuración
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($helpers->getUserPermisoModulo('Configuracion facturacion', 'Configuracion')): ?>
                            <div class="tab-pane" id="facturacion">
                                <div class="setting-card">
                                    <div class="setting-header">

                                        <h3>

                                            Facturacion con sunat

                                        </h3>

                                    </div>
                                    <form id="formConfiguracionFacturacion">
                                        <div class="setting-body">

                                            <!-- FILA -->

                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="col-md-8">

                                                        <div class="setting-title">

                                                            Activar envio de comprobantes automaticos

                                                        </div>

                                                        <div class="setting-desc">

                                                            El sistema enviara automáticamente el comprobante a
                                                            sunat
                                                            una vez generada la venta.

                                                        </div>

                                                    </div>

                                                    <div class="col-md-4 setting-value">

                                                        <label class="switch">

                                                            <input type="checkbox" id="is_send_sunat" name="is_send_sunat">

                                                            <span class="slider"></span>

                                                        </label>

                                                    </div>
                                                </div>

                                            </div>

                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="row m-0 mt-3">
                                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                                            <label><strong>Datos generales</strong></label>
                                                        </div>

                                                        <div class="form-group col-lg-4 col-md-12 col-xs-12">
                                                            <label for="ruc" class="control-label">RUC:</label>
                                                            <input type="text" class="form-control" name="ruc" id="ruc">
                                                        </div>

                                                        <div class="form-group col-lg-8 col-md-12 col-xs-12">
                                                            <label for="razon_social" class="control-label">Razon
                                                                social:</label>
                                                            <input class="form-control" type="text" name="razon_social"
                                                                id="razon_social" placeholder="Razon social">
                                                        </div>
                                                        <div class="form-group col-lg-4 col-md-12 col-xs-12">
                                                            <label for="monto_impuesto" class="control-label">Valor
                                                                impuesto (%):</label>
                                                            <input type="text" class="form-control" name="monto_impuesto"
                                                                id="monto_impuesto">
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="setting-row">

                                                <div class="row">

                                                    <div class="row m-0 mt-3">
                                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                                            <label><strong>Certificado electrónico y
                                                                    contraseña</strong></label>
                                                        </div>
                                                        <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                                            <label for="usuario_sol" class="control-label">Usuario
                                                                SOL:</label>
                                                            <input class="form-control" type="text" name="usuario_sol"
                                                                id="usuario_sol" placeholder="Usuario sol">
                                                        </div>
                                                        <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                                            <label for="clave_sol" class="control-label">Clave
                                                                SOL:</label>
                                                            <input class="form-control" type="password" name="clave_sol"
                                                                id="clave_sol" placeholder="**********">
                                                        </div>

                                                        <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                                            <label for="ruta_certificado" class="control-label">Certificado
                                                                Digital:</label>
                                                            <input type="file" class="form-control" name="ruta_certificado"
                                                                id="rutac_ertificado">
                                                        </div>

                                                        <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                                            <label for="clave_certificado"
                                                                class="control-label">Contraseña:</label>
                                                            <input class="form-control" type="passwod"
                                                                name="clave_certificado" id="clave_certificado"
                                                                placeholder="**********">
                                                        </div>

                                                        <div class="form-group col-lg-6 col-md-12 col-xs-12">
                                                            <label for="estado_certificado"
                                                                class="control-label">Estado:</label>
                                                            <select class="form-control" name="estado_certificado"
                                                                id="estado_certificado">
                                                                <option value="BETA">BETA</option>
                                                                <option value="PRODUCCION">PRODUCCIÓN</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="setting-footer">

                                            <button class="btn btn-primary btn-lg" id="btnGuardarFacturacion">

                                                <i class="fa fa-save"></i>

                                                Guardar Configuración

                                            </button>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($helpers->getUserPermisoModulo('Configuracion mora', 'Configuracion')): ?>
                            <!-- MORA -->
                            <div class="tab-pane" id="mora">

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

                                                            El sistema calculará automáticamente la mora cuando una
                                                            cuota se
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
                        <?php if ($helpers->getUserPermisoModulo('Configuracion credito', 'Configuracion')): ?>
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
                                                            El sistema enviará automáticamente notificaciones de una cuota
                                                            que se encuentre por vencer.
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
                                                            Cálculo mes por 30 dias
                                                        </div>

                                                        <div class="setting-desc">
                                                            El sistema calculará las cuotas y sus plazos considerando cada
                                                            mes como un período de 30 días.
                                                        </div>

                                                    </div>

                                                    <div class="col-md-4 setting-value">

                                                        <label class="switch">

                                                            <input type="checkbox" id="is_calculo_mes"
                                                                name="is_calculo_mes">

                                                            <span class="slider"></span>

                                                        </label>

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
                        <?php if ($helpers->getUserPermisoModulo('Configuracion refinanciamiento', 'Configuracion')): ?>
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