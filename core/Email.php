<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Email {

    public static function plantilla(
        string $nombre,
        array $datos = []
    ): string {
        $archivo = __DIR__ . "/plantillas/{$nombre}.php";

        if (!file_exists($archivo)) {
            throw new Exception(
                "No existe la plantilla de email: {$nombre}"
            );
        }

        extract($datos, EXTR_SKIP);

        ob_start();

        try {
            require $archivo;
            return ob_get_clean();

        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }
    
    public static function enviarEmail(
        string $origen,
        string $destinatario,
        string $asunto,
        string $contenido,
        ?string $nombreDestinatario = null,
        array $adjuntos = []
    ): bool {
        try {
            $mail = new PHPMailer(true);

            // SMTP
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'] ?? '';
            $mail->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) ($_ENV['MAIL_PORT'] ?? 587);

            // Remitente
            $mail->setFrom(
                $origen ?? $mail->Username,
                $_ENV['MAIL_FROM_NAME'] ?? 'Sistema'
            );

            // Destinatario
            $mail->addAddress(
                $destinatario,
                $nombreDestinatario ?? ''
            );

            // Adjuntos
            foreach ($adjuntos as $adjunto) {
                if (is_string($adjunto) && file_exists($adjunto)) {
                    $mail->addAttachment($adjunto);
                }
            }

            // Contenido
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $asunto;
            $mail->Body    = $contenido;
            $mail->AltBody = strip_tags($contenido);

            return $mail->send();

        } catch (Exception $e) {
            error_log('Error enviando email: ' . $e->getMessage());
            return false;
        }
    }
}