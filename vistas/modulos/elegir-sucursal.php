<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Seleccionar Sucursal</h3>
                        </div>
                        <div class="card-body">
                            <form id="formSeleccionarSucursal" method="POST">
                                <div class="form-group">
                                    <label for="sucursal">Elige una sucursal:</label>
                                    <select class="form-control" id="sucursal" name="sucursal" required>
                                        <option value="">-- Seleccionar --</option>
                                        <?php
                                        $idusuario = $_SESSION['idusuario'];
                                        
                                        $conexion = new mysqli('localhost', 'root', '', 'sistema_pos');
                                        if ($conexion->connect_error) {
                                            die("Connection failed: " . $conexion->connect_error);
                                        }
                                        mysqli_query($conexion, 'SET NAMES "utf8"');
                                        
                                        $sql = "SELECT us.idsucursal, s.nombre FROM usuario_sucursal us INNER JOIN sucursal s ON us.idsucursal = s.idsucursal WHERE us.idusuario='$idusuario'";
                                        $result = $conexion->query($sql);
                                        if (!$result) {
                                            die("Query failed: " . $conexion->error);
                                        }
                                        while ($reg = $result->fetch_object()) {
                                            echo '<option value="' . $reg->idsucursal . '">' . $reg->nombre . '</option>';
                                        }
                                        $conexion->close();
                                        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
                                        ?>
                                    </select>

                                    
                                </div>
                                <button type="submit" class="btn btn-primary">Seleccionar</button>
                            </form>
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
                    location.reload();
                } else {
                    alert('Error al seleccionar sucursal');
                }
            });
        }
    });
});
</script>