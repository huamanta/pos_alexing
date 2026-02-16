<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// RUTAS ABSOLUTAS CORRECTAS
require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

// Incluir la configuración de correo electrónico
require_once __DIR__ . '/../configuraciones/email.config.php';

function enviarCorreo($para, $nombre, $asunto, $html)
{
    global $smtp_host, $smtp_port, $smtp_secure, $smtp_username, $smtp_password, $smtp_from_email, $smtp_from_name;
    
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
