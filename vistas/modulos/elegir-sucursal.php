<?php
require_once __DIR__.'/../../configuraciones/local.php'
?>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title" id="cardTitle">Seleccionar Sucursal</h3>
                        </div>
                        <div class="card-body">
                            <?php
                                $idusuario = $_SESSION['idusuario'];
                                
                                $conexion = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);
                                if ($conexion->connect_error) {
                                    die("Connection failed: " . $conexion->connect_error);
                                }
                                mysqli_query($conexion, 'SET NAMES "utf8"');
                                
                                $sql_super = "SELECT superusuario FROM usuario WHERE idusuario='$idusuario' LIMIT 1";
                                $res_super = $conexion->query($sql_super);
                                $isSuper = false;
                                if ($res_super) {
                                    $row_super = $res_super->fetch_object();
                                    if ($row_super && isset($row_super->superusuario) && $row_super->superusuario == 1) {
                                        $isSuper = true;
                                    }
                                }

                                if ($isSuper) {
                                    $sql = "SELECT idsucursal, nombre FROM sucursal";
                                } else {
                                    $sql = "SELECT us.idsucursal, s.nombre FROM usuario_sucursal us INNER JOIN sucursal s ON us.idsucursal = s.idsucursal WHERE us.idusuario='$idusuario'";
                                }
                                $result = $conexion->query($sql);
                                if (!$result) {
                                    die("Query failed: " . $conexion->error);
                                }
                                $sucursales = [];
                                while ($reg = $result->fetch_object()) {
                                    $sucursales[] = $reg;
                                }
                                $conexion->close();
                                $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
                                $count = count($sucursales);
                            ?>

                            <?php if ($count == 0): ?>
                                <!-- Formulario para crear empresa y sucursal -->
                                <form id="formCrearSucursal">
                                    <h5>Datos de la Empresa</h5>
                                    <div class="form-group">
                                        <label for="ruc">RUC:</label>
                                        <input type="text" class="form-control" id="ruc" name="ruc" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="razon_social">Razón Social:</label>
                                        <input type="text" class="form-control" id="razon_social" name="razon_social" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="nombre_impuesto">Nombre Impuesto:</label>
                                        <input type="text" class="form-control" id="nombre_impuesto" name="nombre_impuesto" value="IGV" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="monto_impuesto">Monto Impuesto (%):</label>
                                        <input type="number" step="0.01" class="form-control" id="monto_impuesto" name="monto_impuesto" value="18.00" required>
                                    </div>
                                    <h5>Datos de la Sucursal</h5>
                                    <div class="form-group">
                                        <label for="nombre">Nombre de la Sucursal:</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="direccion">Dirección:</label>
                                        <input type="text" class="form-control" id="direccion" name="direccion">
                                    </div>
                                    <div class="form-group">
                                        <label for="telefono">Teléfono:</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Crear y Continuar</button>
                                </form>
                            <?php elseif ($count == 1): ?>
                                <!-- Automático: seleccionar la única sucursal -->
                                <p>Seleccionando sucursal automáticamente...</p>
                                <script>
                                    $(document).ready(function() {
                                        $.post('<?php echo $baseUrl; ?>/controladores/usuario.php?op=seleccionarSucursal', { idsucursal: '<?php echo $sucursales[0]->idsucursal; ?>' }, function(response) {
                                            if (response == 'ok') {
                                                window.location.href = '<?php echo $baseUrl; ?>/inicio';
                                            } else {
                                                alert('Error al seleccionar sucursal');
                                            }
                                        });
                                    });
                                </script>
                            <?php else: ?>
                                <!-- Seleccionar entre múltiples -->
                                <div class="alert alert-info">
                                    <i class="fas fa-building"></i> Tienes <strong><?php echo $count; ?></strong> sucursales asignadas. Selecciona una:
                                </div>
                                <form id="formSeleccionarSucursal" method="POST">
                                    <div class="form-group">
                                        <label for="sucursal">Elige una sucursal:</label>
                                        <select class="form-control" id="sucursal" name="sucursal" required>
                                            <option value="">-- Seleccionar --</option>
                                            <?php foreach ($sucursales as $suc): ?>
                                                <option value="<?php echo $suc->idsucursal; ?>"><?php echo $suc->nombre; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Seleccionar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
var baseUrl = '<?php echo $baseUrl; ?>';
$(document).ready(function() {
    $('#formSeleccionarSucursal').on('submit', function(e) {
        e.preventDefault();
        var selectedSuc = $('#sucursal').val();
        if (selectedSuc) {
            $.post(baseUrl + '/controladores/usuario.php?op=seleccionarSucursal', { idsucursal: selectedSuc }, function(response) {
                if (response == 'ok') {
                    window.location.reload();
                } else {
                    alert('Error al seleccionar sucursal');
                }
            });
        }
    });

    $('#formCrearSucursal').on('submit', function(e) {
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
            }, function(response) {
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