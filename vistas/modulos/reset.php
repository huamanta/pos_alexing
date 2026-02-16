<?php
$token = $_GET['token'] ?? '';

if (!$token) {
    echo "<div class='alert alert-danger text-center'>Token inválido</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Restablecer contraseña</title>

  <style>
    body{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      background: linear-gradient(135deg,#f8fafc,#eef2ff);
      font-family: 'Inter', sans-serif;
    }
    .auth-card{
      background:#fff;
      width:100%;
      max-width:420px;
      padding:35px;
      border-radius:16px;
      box-shadow:0 10px 25px rgba(0,0,0,.08);
      animation: fadeUp .4s ease;
    }
    @keyframes fadeUp{
      from{opacity:0;transform:translateY(15px)}
      to{opacity:1;transform:none}
    }
    .auth-card h4{
      font-weight:600;
    }
    .form-control{
      border-radius:10px;
      padding:12px;
    }
    .btn-primary{
      border-radius:10px;
      padding:12px;
    }
  </style>
</head>
<body>

<div class="auth-card">
  <div class="text-center mb-4">
    <img src="reportes/1743005576.png" height="60">
    <h4 class="mt-3">Nueva contraseña</h4>
    <p class="text-muted text-sm">
      Ingresa tu nueva contraseña para continuar
    </p>
  </div>

  <form id="frmReset">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

    <div class="mb-3">
      <div class="input-group">
        <span class="input-group-text">
          <i class="fas fa-lock"></i>
        </span>
        <input type="password" name="clave" class="form-control"
               placeholder="Nueva contraseña" required>
      </div>
    </div>

    <button class="btn btn-primary w-100">
      Actualizar contraseña
    </button>

    <div id="msg" class="mt-3"></div>
  </form>

  <div class="text-center mt-3">
    <a href="login" class="text-muted text-decoration-none">
      ← Volver al login
    </a>
  </div>
</div>

<script>
$(function(){

  // 🔒 VALIDAR TOKEN AL CARGAR
  $.get(
    "controladores/usuario.php?op=validar_token&token=<?= htmlspecialchars($token) ?>",
    function(r){
      let res = JSON.parse(r);

      if(!res.status){
        $(".auth-card").html(`
          <div class="text-center">
            <h5 class="text-danger fw-bold">
              Este enlace ya fue utilizado o expiró
            </h5>
            <a href="login" class="btn btn-primary mt-3">
              Ir al login
            </a>
          </div>
        `);
      }
    }
  );

  // 🔁 ENVIAR RESET
  $("#frmReset").on("submit", function(e){
    e.preventDefault();

    $("#msg").html("<div class='text-center text-muted'>Actualizando...</div>");

    $.post(
      "controladores/usuario.php?op=reset",
      $(this).serialize(),
      function(r){
        $("#msg").html(r);
      }
    );
  });

});
</script>

</body>
</html>
