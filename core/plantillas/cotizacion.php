<?php

$cliente = htmlspecialchars($cliente ?? '');
$numero = htmlspecialchars($numero ?? '');
$fecha = htmlspecialchars($fecha ?? '');
$empresa = htmlspecialchars($empresa ?? 'Mi Empresa');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización</title>
</head>

<body style="margin:0;padding:0;font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 0;">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0"
                   style="background:#ffffff;border-radius:10px;overflow:hidden;">

                <!-- CABECERA -->
                <tr>
                    <td style="background:#1f2937;padding:25px 30px;color:#ffffff;">
                        <h2 style="margin:0;">
                            <?= $empresa ?>
                        </h2>

                        <p style="margin:8px 0 0;color:#d1d5db;">
                            Nueva cotización registrada
                        </p>
                    </td>
                </tr>

                <!-- CONTENIDO -->
                <tr>
                    <td style="padding:30px;">

                        <p style="font-size:16px;">
                            Estimado(a)
                            <strong><?= $cliente ?></strong>,
                        </p>

                        <p style="color:#4b5563;line-height:1.6;">
                            Le informamos que se ha registrado una nueva
                            cotización con los siguientes datos:
                        </p>

                        <table width="100%" cellpadding="10"
                               style="border-collapse:collapse;margin-top:20px;">

                            <tr>
                                <td style="border-bottom:1px solid #e5e7eb;">
                                    <strong>Cotización</strong>
                                </td>

                                <td align="right"
                                    style="border-bottom:1px solid #e5e7eb;">
                                    <?= $numero ?>
                                </td>
                            </tr>

                            <tr>
                                <td style="border-bottom:1px solid #e5e7eb;">
                                    <strong>Fecha</strong>
                                </td>

                                <td align="right"
                                    style="border-bottom:1px solid #e5e7eb;">
                                    <?= $fecha ?>
                                </td>
                            </tr>

                        </table>

                        <p style="margin-top:25px;color:#4b5563;">
                            Adjuntamos a este correo el PDF correspondiente
                            a la cotización.
                        </p>

                        <p style="margin-top:30px;">
                            Saludos cordiales.
                        </p>

                    </td>
                </tr>

                <!-- PIE -->
                <tr>
                    <td style="background:#f9fafb;padding:20px 30px;text-align:center;">
                        <small style="color:#6b7280;">
                            Este correo fue generado automáticamente.
                        </small>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>