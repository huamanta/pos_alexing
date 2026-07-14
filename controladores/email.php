<?php
// RUTAS ABSOLUTAS CORRECTAS
require_once __DIR__ . '/../configuraciones/bootstrap.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function enviarCorreo($para, $nombre, $asunto, $html)
{
    $smtp_host = env('MAIL_HOST');
    $smtp_port = env('MAIL_PORT');
    $smtp_secure = env('MAIL_ENCRYPTION');
    $smtp_username = env('MAIL_USERNAME');
    $smtp_password = env('MAIL_PASSWORD');
    $smtp_from_email = env('MAIL_FROM_ADDRESS');
    $smtp_from_name = env('MAIL_FROM_NAME');
    
    $mail = new PHPMailer(true);

    try {
        // ===============================
        // CONFIGURACIÓN DEL SERVIDOR
        // ===============================
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_username;
        $mail->Password   = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port       = $smtp_port;

        $mail->CharSet = 'UTF-8';

        // Remitente
        $mail->setFrom($smtp_from_email, $smtp_from_name);

        // Destinatario
        $mail->addAddress($para, $nombre);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $html;

        $mail->send();
        return true;

    } catch (Exception $e) {
        echo "<pre>";
        echo "Mailer Error: " . $mail->ErrorInfo;
        echo "</pre>";
        return false;
    }

}
