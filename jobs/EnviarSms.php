<?php

require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../modelos/CuentasCobrar.php';

class EnviarSMS
{
    public function __construct(
        public string $numero,
        public string $mensaje,
        public string $tipo = 'transaccional',
        public ?int $idRecordatorio = null
    ) {}

    private function actualizarEstado(
        string $estado,
        ?string $respuesta = null,
        ?string $referencia = null
    ): void {
        if (!$this->idRecordatorio) {
            return;
        }

        try {
            $cuentasCobrar = new CuentasCobrar();

            $cuentasCobrar->actualizarEstadoRecordatorio(
                $this->idRecordatorio,
                $estado,
                $respuesta,
                $referencia
            );

        } catch (Throwable $e) {
            error_log('Error actualizando recordatorio SMS: ' . $e->getMessage());
        }
    }

    public function handle(): bool
    {
        try {

            $baseUrl = rtrim(trim($_ENV['INFOBIP_BASE_URL'] ?? ''), '/');
            $apiKey = trim($_ENV['INFOBIP_API_KEY'] ?? '');
            $sender = trim($_ENV['INFOBIP_SENDER'] ?? 'InfoSMS');

            if (!$baseUrl || !$apiKey || !$sender) {
                throw new RuntimeException('Configuración Infobip incompleta.');
            }

            $numero = preg_replace('/\D/', '', $this->numero);

            if (!preg_match('/^51\d{9}$/', $numero)) {
                throw new InvalidArgumentException('Número celular peruano inválido.');
            }

            $payload = [
                'messages' => [
                    [
                        'destinations' => [
                            ['to' => $numero]
                        ],
                        'from' => $sender,
                        'text' => $this->mensaje
                    ]
                ]
            ];

            $ch = curl_init($baseUrl . '/sms/2/text/advanced');

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode(
                    $payload,
                    JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
                ),
                CURLOPT_HTTPHEADER => [
                    'Authorization: App ' . $apiKey,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            curl_close($ch);

            if ($response === false) {
                throw new RuntimeException('Error de conexión Infobip: ' . $error);
            }

            $result = json_decode($response, true);

            if ($status < 200 || $status >= 300) {
                throw new RuntimeException(
                    'Infobip HTTP ' . $status . ': ' . $response
                );
            }

            $message = $result['messages'][0] ?? null;

            if (empty($message['messageId'])) {
                throw new RuntimeException(
                    'Infobip no devolvió un ID de mensaje: ' . $response
                );
            }

            // Actualizar recordatorio como enviado
            $this->actualizarEstado(
                'ENVIADO',
                $response,
                $message['messageId']
            );

            return true;

        } catch (Throwable $e) {

            // Actualizar recordatorio como error
            $this->actualizarEstado(
                'ERROR',
                $e->getMessage()
            );

            throw $e;
        }
    }
}
