$("#frmAcceso").on('submit', function(e) {
    e.preventDefault();

    let logina = $("#logina").val();
    let clavea = $("#clavea").val();

    // Feedback visual en el botón para evitar doble click
    let btn = $(".btn-login"); // Asegúrate que tu botón tenga esta clase o usa 'button[type="submit"]'
    let originalText = btn.html();
    btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Validando...');

    let inicioCarga = performance.now();

    $.post("controladores/usuario.php?op=verificar", { logina, clavea }, function(data) {
        let finCarga = performance.now();
        let tiempoServidor = finCarga - inicioCarga;
        
        try {
            let usuario = JSON.parse(data);

            if (usuario != null && usuario.nombre) {
                mostrarMensajeBienvenida(usuario.nombre, tiempoServidor);
            } else {
                $('#n1').slideDown(); // Usamos slideDown que es más elegante
                btn.prop("disabled", false).html(originalText); // Restaurar botón
                setTimeout(() => { $('#n1').slideUp(); }, 3000);
            }
        } catch(err) {
            console.log("Error de respuesta", err);
            btn.prop("disabled", false).html(originalText);
        }
    });
});

function mostrarMensajeBienvenida(nombre, tiempoServidor) {
    // Calculamos tiempo (mínimo 2.5s para disfrutar la animación)
    let duracionAnimacion = Math.min(Math.max(tiempoServidor + 1500, 2000), 6000);

    // 1. Ocultamos el formulario
    $(".login-container").fadeOut(300, function() {
        
        // 2. Reseteo y limpieza
        $(this).attr("style", "display: none !important");
        $(this).removeClass("login-container");
        
        // 3. Overlay con animaciones mejoradas
        $('body').append(`
            <div id="loading-overlay">
                <div class="loading-card animate__animated animate__zoomIn">
                    
                    <div class="loader-icons">
                        <div class="icon-circle step-1">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="icon-circle step-2">
                            <i class="fas fa-gears"></i>
                        </div>
                        <div class="icon-circle step-3">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="icon-circle step-4">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>

                    <h3 class="loading-title"></h3>
                    <p class="loading-text">Sincronizando sus preferencias...</p>
                    
                    <div class="progress-bar-wrapper">
                        <div class="progress-bar-fill"></div>
                    </div>
                </div>
            </div>

            <style>
                /* Overlay centrado */
                #loading-overlay {
                    position: fixed;
                    top: 0; left: 0; width: 100%; height: 100vh;
                    background: rgba(15, 23, 42, 0.9); /* Fondo más oscuro para resaltar el brillo */
                    backdrop-filter: blur(15px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                }

                /* Tarjeta */
                .loading-card {
                    background: rgba(255, 255, 255, 0.95);
                    padding: 50px;
                    border-radius: 30px;
                    text-align: center;
                    box-shadow: 0 30px 60px rgba(0,0,0,0.5);
                    border: 1px solid rgba(255,255,255,0.4);
                    max-width: 500px;
                    width: 90%;
                }

                /* --- NUEVA ANIMACIÓN DE ICONOS --- */
                .loader-icons {
                    display: flex;
                    justify-content: center;
                    gap: 20px;
                    margin-bottom: 30px;
                }

                .icon-circle {
                    width: 60px;
                    height: 60px;
                    background: #f1f5f9;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.5rem;
                    color: #94a3b8; /* Gris apagado inicial */
                    position: relative;
                    transition: all 0.3s ease;
                    box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
                }

                /* La magia: Animación 'Pop & Glow' secuencial */
                .icon-circle {
                    animation: popAndGlow 2s infinite ease-in-out;
                }

                /* Retardos para crear la "Ola" */
                .step-1 { animation-delay: 0s; }
                .step-2 { animation-delay: 0.25s; }
                .step-3 { animation-delay: 0.5s; }
                .step-4 { animation-delay: 0.75s; }

                /* Keyframes de la animación principal */
                @keyframes popAndGlow {
                    0%, 100% { 
                        transform: scale(1); 
                        background: #f1f5f9; 
                        color: #94a3b8;
                        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
                    }
                    50% { 
                        transform: scale(1.25) translateY(-10px); 
                        background: #fff;
                        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4); /* Sombra de brillo (Glow) */
                    }
                }

                /* Colores individuales cuando se activan (al 50% de la animación) */
                .step-1 { animation-name: popBlue; }
                .step-2 { animation-name: popPurple; }
                .step-3 { animation-name: popPink; }
                .step-4 { animation-name: popGreen; }

                @keyframes popBlue {
                    0%, 100% { transform: scale(1); color: #94a3b8; background: #f1f5f9; }
                    50% { transform: scale(1.25) translateY(-5px); color: #3b82f6; background: #eff6ff; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4); }
                }
                @keyframes popPurple {
                    0%, 100% { transform: scale(1); color: #94a3b8; background: #f1f5f9; }
                    50% { transform: scale(1.25) translateY(-5px); color: #8b5cf6; background: #f5f3ff; box-shadow: 0 10px 20px rgba(139, 92, 246, 0.4); }
                }
                @keyframes popPink {
                    0%, 100% { transform: scale(1); color: #94a3b8; background: #f1f5f9; }
                    50% { transform: scale(1.25) translateY(-5px); color: #ec4899; background: #fdf2f8; box-shadow: 0 10px 20px rgba(236, 72, 153, 0.4); }
                }
                @keyframes popGreen {
                    0%, 100% { transform: scale(1); color: #94a3b8; background: #f1f5f9; }
                    50% { transform: scale(1.25) translateY(-5px); color: #10b981; background: #ecfdf5; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.4); }
                }

                /* Rotación extra para los engranajes */
                .step-2 i {
                    animation: spinGears 2s infinite linear;
                }
                @keyframes spinGears {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(180deg); }
                }

                /* Tipografía */
                .loading-title {
                    font-family: 'Poppins', sans-serif;
                    font-weight: 700;
                    color: #1e293b;
                    margin-top: 10px;
                    font-size: 1.5rem;
                }
                .loading-text {
                    font-family: 'Poppins', sans-serif;
                    color: #64748b;
                    margin-bottom: 25px;
                }

                /* Barra de progreso */
                .progress-bar-wrapper {
                    width: 100%;
                    height: 6px;
                    background: #e2e8f0;
                    border-radius: 10px;
                    overflow: hidden;
                }
                .progress-bar-fill {
                    height: 100%;
                    width: 0%;
                    background: linear-gradient(90deg, #3b82f6, #8b5cf6, #ec4899);
                    border-radius: 10px;
                    animation: fillProgress ${duracionAnimacion}ms ease-out forwards;
                }
                @keyframes fillProgress { 0% { width: 0%; } 100% { width: 100%; } }
            </style>
        `);
    });

    // Redirección
    setTimeout(() => {
        $("#loading-overlay").fadeOut(500, function() {
            window.location.href = "inicio";
        });
    }, duracionAnimacion);
}

// Lógica de Recordarme y Ojo de contraseña (sin cambios)
function rememberMe(event) {
    if (event.target.checked) {
        localStorage.setItem("loginRemember", $('[name="logina"]').val());
        localStorage.setItem("checkRemember", true);
    } else {
        localStorage.removeItem("loginRemember");
        localStorage.removeItem("checkRemember");
    }
}

$(document).ready(function() {
    if (localStorage.getItem("loginRemember")) {
        $('[name="logina"]').val(localStorage.getItem("loginRemember"));
    }
    if (localStorage.getItem("checkRemember")) {
        $('#remember').attr("checked", true);
    }
});

const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("clavea");

if(togglePassword){ // Verificamos que exista para evitar errores
    togglePassword.addEventListener("click", function () {
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);
        const icon = this.querySelector("i");
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
    });
}