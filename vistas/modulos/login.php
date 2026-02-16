<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - APP WEB</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />

  <style>
    /* Reset y Estilos Base */
    * { box-sizing: border-box; }
    
    body, html {
      margin: 0;
      padding: 0;
      height: 100vh;
      font-family: 'Poppins', sans-serif;
      /* Fondo moderno con gradiente profundo */
      background-color: #1a1a2e;
      background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                        radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                        radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
      background-size: 200% 200%;
      animation: gradientBG 15s ease infinite;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Contenedor Principal (Tarjeta) */
    .login-container {
      display: flex;
      width: 900px;
      max-width: 95%;
      height: 600px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      overflow: hidden;
      position: relative;
    }

    /* Sección de Imagen (Izquierda) */
    .login-image-section {
      flex: 1.2;
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      /* Overlay de color sobre la imagen */
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .login-image-section::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: url('files/fondosys.png') no-repeat center center/cover;
      opacity: 0.4;
      mix-blend-mode: overlay;
      transition: transform 6s ease;
    }

    .login-container:hover .login-image-section::before {
      transform: scale(1.1);
    }

    .text-overlay {
      position: relative;
      z-index: 2;
      color: white;
      text-align: center;
      padding: 30px;
    }

    .text-overlay h2 { font-weight: 700; font-size: 2rem; margin-bottom: 10px; }
    .text-overlay p { font-size: 0.9rem; opacity: 0.9; font-weight: 300; }

    /* Sección del Formulario (Derecha) */
    .login-form-section {
      flex: 1;
      padding: 50px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
    }

    .logo {
      height: 70px;
      margin: 0 auto 10px;
      display: block;
    }

    .welcome-text {
      text-align: center;
      color: #333;
      margin-bottom: 30px;
      font-size: 0.95rem;
      color: #666;
    }

    /* Inputs Personalizados */
    .custom-input-group {
      position: relative;
      margin-bottom: 25px;
    }

    .custom-input-group i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #a0a5b9;
      transition: color 0.3s;
      z-index: 2;
    }

    .custom-input-group input {
      width: 100%;
      padding: 15px 15px 15px 45px;
      border: 2px solid #f0f2f5;
      background: #f8f9fa;
      border-radius: 12px;
      outline: none;
      font-size: 0.95rem;
      transition: all 0.3s;
      color: #333;
    }

    .custom-input-group input:focus {
      border-color: #667eea;
      background: #fff;
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
    }

    .custom-input-group input:focus + i {
      color: #667eea;
    }

    /* Icono del ojo (Ver password) */
    #togglePassword {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #a0a5b9;
      z-index: 3;
    }
    
    #togglePassword:hover { color: #667eea; }

    /* Checkbox */
    .form-check-input:checked {
      background-color: #667eea;
      border-color: #667eea;
    }

    /* Botón */
    .btn-login {
      width: 100%;
      padding: 14px;
      background: linear-gradient(to right, #667eea, #764ba2);
      border: none;
      border-radius: 12px;
      color: white;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(118, 75, 162, 0.3);
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(118, 75, 162, 0.4);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    /* Links y Alertas */
    .forgot-link {
      color: #764ba2;
      font-size: 0.85rem;
      font-weight: 500;
      transition: color 0.3s;
    }
    .forgot-link:hover { color: #5a367f; text-decoration: underline; }

    .alert {
      font-size: 0.85rem;
      border-radius: 10px;
      display: none; /* Controlado por JS */
    }

    /* Responsive */
    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
        height: auto;
        width: 90%;
      }
      .login-image-section {
        display: none; /* Ocultamos la imagen en móvil para dar prioridad al form */
      }
      .login-form-section {
        padding: 40px 30px;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    
    <div class="login-image-section">
      <div class="text-overlay">
        <h2>Bienvenido</h2>
        <p>Accede a tu panel de control y gestiona tus reportes eficientemente.</p>
      </div>
    </div>

    <div class="login-form-section">
      <img src="reportes/1767058098.png" alt="Logo" class="logo" />
      <p class="welcome-text">Ingresa tus credenciales para continuar</p>

      <form method="post" id="frmAcceso">
        
        <div class="custom-input-group">
          <input type="text" id="logina" name="logina" placeholder="Usuario" required autocomplete="off" />
          <i class="fas fa-user"></i>
        </div>

        <div class="custom-input-group">
          <input type="password" id="clavea" name="clavea" placeholder="Contraseña" required />
          <i class="fas fa-lock"></i>
          <span id="togglePassword">
            <i class="fas fa-eye"></i>
          </span>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember" />
            <label class="form-check-label" for="remember" style="font-size: 0.85rem; color:#666;">Recordarme</label>
          </div>
          <a href="recuperar" class="forgot-link">¿Olvidaste tu contraseña?</a>
        </div>

        <button class="btn-login" type="submit">INGRESAR</button>
        
        <div class="alert alert-danger mt-3 text-center border-0 bg-danger bg-opacity-10 text-danger" id="n1">
          <i class="fas fa-exclamation-circle me-2"></i>Usuario y/o contraseña incorrectos
        </div>

      </form>
    </div>
  </div>

  <script src="vistas/js/login.js"></script>
</body>
</html>