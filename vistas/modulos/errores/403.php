<style>
    body {
        margin: 0;
        min-height: 100vh;
        background: #f4f6f9;
        color: #343a40;
    }

    .error-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 20px;
    }

    .error-container {
        width: 100%;
        max-width: 650px;
        text-align: center;
    }

    .error-code {
        font-size: 120px;
        line-height: 1;
        font-weight: 700;
        color: #dc3545;
        margin-bottom: 10px;
        letter-spacing: -5px;
    }

    .error-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 5px 20px rgba(0, 0, 0, .08);
        font-size: 36px;
    }

    .error-title {
        margin: 0 0 10px;
        font-size: 28px;
        font-weight: 600;
    }

    .error-message {
        margin: 0 auto 28px;
        max-width: 500px;
        color: #6c757d;
        font-size: 16px;
        line-height: 1.6;
    }

    .error-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 140px;
        padding: 11px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: .2s ease;
    }

    .btn-primary {
        background: #007bff;
        color: #fff;
    }

    .btn-primary:hover {
        background: #0069d9;
    }

    .btn-light {
        background: #fff;
        color: #495057;
        border: 1px solid #dee2e6;
    }

    .btn-light:hover {
        background: #f8f9fa;
    }

    @media (max-width: 576px) {
        .error-code {
            font-size: 90px;
        }

        .error-title {
            font-size: 23px;
        }

        .error-icon {
            width: 65px;
            height: 65px;
            font-size: 28px;
        }

        .error-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<section class="content">
    <div class="container-fluid">
        <div class="error-page">
            <div class="error-container">

                <div class="error-code">403</div>

                <div class="error-icon">
                    🔒
                </div>

                <h1 class="error-title">
                    Acceso denegado
                </h1>

                <p class="error-message">
                    No tienes permisos para acceder a este módulo.
                    Si consideras que deberías tener acceso, comunícate con el administrador del sistema.
                </p>

                <div class="error-actions">
                    <a href="inicio" class="btn btn-primary">
                        Ir al inicio
                    </a>

                    <a href="javascript:history.back()" class="btn btn-light">
                        Volver
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>