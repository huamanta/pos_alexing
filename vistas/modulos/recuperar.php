<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recuperar contraseña</title>
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
    <h4 class="mt-3">Recuperar contraseña</h4>
    <p class="text-muted text-sm">
      Ingresa tu correo registrado y te enviaremos un enlace
    </p>
  </div>

  <form id="frmRecuperar">
    <div class="mb-3">
      <input type="email" name="email" class="form-control"
             placeholder="Correo electrónico" required>
    </div>

    <button class="btn btn-primary w-100">
      Enviar enlace
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
$("#frmRecuperar").on("submit", function(e){
  e.preventDefault();

  $("#msg").html("<div class='text-center text-muted'>Enviando...</div>");

  $.post("controladores/usuario.php?op=recuperar", $(this).serialize(), function(r){
    $("#msg").html(r);
  });
});
</script>

</body>
</html>
