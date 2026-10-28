
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización no encontrada</title>
    <style>
        :root {
            --color-primary: #7B1F2D;
            --color-text: #333333;
            --color-muted: #777777;
            --color-border: #E5E7EB;
            --color-background: #F8F9FA;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: var(--color-background);
            color: var(--color-text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            background: #FFFFFF;
            border: 1px solid var(--color-border);
            border-radius: 12px;
            padding: 40px 30px;
            max-width: 440px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        }

        .error-icon {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: #F8E9EC;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 36px;
            font-weight: bold;
        }

        .error-code {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: var(--color-primary);
            margin-bottom: 10px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 12px;
        }

        .error-message {
            color: var(--color-muted);
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .btn-back {
            display: inline-block;
            background: var(--color-primary);
            color: #FFFFFF;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            transition: opacity 0.2s;
        }

        .btn-back:hover {
            opacity: 0.9;
        }

        .footer {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid var(--color-border);
            font-size: 12px;
            color: var(--color-muted);
        }
    </style>
</head>
<body>
    <main class="error-container">
        <div class="error-icon">!</div>

        <div class="error-code">ERROR 404</div>

        <h1>Cotización no encontrada</h1>

        <p class="error-message">
            La cotización que intentas consultar no existe,
            ha vencido o ya no se encuentra disponible.
            Verifica el enlace e inténtalo nuevamente.
        </p>

        <a href="javascript:history.back()" class="btn-back">
            Volver
        </a>

        <div class="footer">
            Si necesitas ayuda, comunícate con nosotros.
        </div>
    </main>
</body>
</html>
