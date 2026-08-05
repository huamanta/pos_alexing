<?php
require_once __DIR__ . "/../../modelos/Usuario.php";
require_once __DIR__ . "/../../modelos/Helpers.php";
$usuario = new Usuario();
$sucursales = $usuario->listarSucursalesUsuario($_SESSION['idusuario']);
$baseUrl = rtrim($_ENV['APP_URL'], '/');
$count = count($sucursales);
$esAdmin = $usuario->esSuperusuario();
?>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <!-- ADMIN SIN SUCURSALES -->
                    <?php if ($count == 0 && $esAdmin): ?>

                        <div class="row justify-content-center">
                            <div class="col-md-6 mt-3">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3 class="card-title" id="cardTitle">Crear nueva sucursal</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info border-left border-info">
                                            <h5><i class="fas fa-info-circle mr-2"></i>Bienvenido a Syspider</h5>
                                            <p class="mb-2">
                                                Para comenzar a utilizar el sistema, primero debe registrar la información
                                                de su
                                                <strong>empresa</strong> y crear su <strong>primera sucursal</strong>.
                                            </p>
                                            <ul class="mb-0 pl-3">
                                                <li>Ingrese el <strong>RUC</strong> de la empresa.</li>
                                                <li>Complete la <strong>Razón Social</strong> y los datos tributarios.</li>
                                                <li>Registre el nombre y la dirección de la sucursal principal.</li>
                                                <li>Presione <strong>"Crear y Continuar"</strong> para finalizar la
                                                    configuración inicial.</li>
                                            </ul>
                                        </div>
                                        <form id="formCrearSucursal">
                                            <h5>Datos de la Empresa</h5>
                                            <div class="form-group">
                                                <label for="ruc">RUC:</label>
                                                <select class="form-control" id="ruc" name="ruc">
                                                    <option value="RUC" selected>RUC</option>
                                                </select>
                                            </div>
                                            <div class="form-group"> <label for="razon_social">Razón Social:</label> <input
                                                    type="text" class="form-control" id="razon_social" name="razon_social"
                                                    required>
                                            </div>
                                            <div class="form-group"> <label for="nombre_impuesto">Nombre Impuesto:</label>
                                                <input type="text" class="form-control" id="nombre_impuesto"
                                                    name="nombre_impuesto" value="IGV" required>
                                            </div>
                                            <div class="form-group"> <label for="monto_impuesto">Monto Impuesto (%):</label>
                                                <input type="number" step="0.01" class="form-control" id="monto_impuesto"
                                                    name="monto_impuesto" value="18.00" required>
                                            </div>
                                            <h5>Datos de la Sucursal</h5>
                                            <div class="form-group"> <label for="nombre">Nombre de la Sucursal:</label>
                                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                                            </div>
                                            <div class="form-group"> <label for="direccion">Dirección:</label> <input
                                                    type="text" class="form-control" id="direccion" name="direccion"> </div>
                                            <div class="form-group"> <label for="telefono">Teléfono:</label> <input
                                                    type="text" class="form-control" id="telefono" name="telefono"> </div>
                                            <button type="submit" class="btn btn-primary">Crear y Continuar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($count == 0 && !$esAdmin): ?>

                        <!-- USUARIO SIN SUCURSAL -->
                        <div class="alert alert-warning text-center mt-3">

                            <i class="fas fa-exclamation-triangle"></i>

                            <h5>No tienes sucursales asignadas</h5>

                            <p>
                                Comunícate con un administrador para que te asigne una sucursal.
                            </p>

                        </div>


                    <?php elseif ($count == 1): ?>

                        <!-- UNA SOLA SUCURSAL -->
                        <div class="text-center">

                            <p>
                                Seleccionando sucursal automáticamente...
                            </p>

                        </div>

                        <script>
                            $(document).ready(function () {

                                $.post(
                                    '<?php echo $baseUrl; ?>/controladores/usuario.php?op=seleccionarSucursal', {
                                    idsucursal: '<?php echo $sucursales[0]->idsucursal; ?>'
                                },
                                    function (response) {

                                        if (response == "ok") {
                                            window.location.href = '<?php echo $baseUrl; ?>/inicio';
                                        }

                                    }
                                );

                            });
                        </script>


                    <?php else: ?>

                        <!-- VARIAS SUCURSALES EN CARDS -->

                        <style>
                            .sucursal-card {
                                transition: all 0.3s ease;
                                border: 1px solid #dee2e6;
                            }

                            .sucursal-card:hover {
                                transform: translateY(-5px);
                                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
                                border-color: #007bff;
                                background: #f8fbff;
                            }

                            .sucursal-card:hover .fa-store {
                                transform: scale(1.1);
                                color: #007bff;
                            }

                            .sucursal-card .fa-store {
                                transition: transform 0.3s ease;
                            }
                        </style>

                        <div class="row mt-3">
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-info">
                                    <i class="fas fa-building"></i>
                                    Selecciona una sucursal
                                </div>
                            </div>
                            <?php foreach ($sucursales as $suc): ?>

                                <div class="col-md-3 mb-3">

                                    <div class="card shadow sucursal-card" style="cursor:pointer">

                                        <div class="card-body text-center">

                                            <i class="fas fa-store fa-3x text-primary"></i>

                                            <h5 class="mt-3">
                                                <?php echo $suc->nombre; ?>
                                            </h5>

                                            <button class="btn btn-primary btn-sm sucursal-btn"
                                                data-id="<?php echo $suc->idsucursal; ?>">
                                                Ingresar
                                            </button>

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