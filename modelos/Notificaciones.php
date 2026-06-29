<?php

require_once __DIR__ . "/../configuraciones/Conexion.php";
require_once __DIR__ . "./Helpers.php";

class Notificaciones extends Helpers
{
    public function ejecutar()
    {
        echo "[" . date("Y-m-d H:i:s") . "] Iniciando notificaciones...\n";

        //$this->notificarCuotasPorVencer();
        //$this->notificarCuotasVencidas();
        $this->enviarWhatsapp('51948236955', 'Mensaje enviado de preuba desde evolution api');

        echo "[" . date("Y-m-d H:i:s") . "] Finalizado.\n";
    }

    private function yaFueNotificado($idcpc)
    {
        $sql = "SELECT idnotificacion
            FROM notificaciones
            WHERE idcpc = '$idcpc'
            AND canal = 'WHATSAPP'
            AND DATE(fecha_envio) = CURDATE()
            LIMIT 1";

        $rspta = ejecutarConsulta($sql);

        return $rspta->num_rows > 0;
    }

    private function guardarNotificacion($idcliente, $idcpc, $telefono, $mensaje, $respuesta)
    {
        $estado = isset($respuesta['key']) ? 'ENVIADO' : 'ERROR';

        $respuestaApi = json_encode($respuesta);

        $sql = "INSERT INTO notificaciones(
                idcliente,
                idcpc,
                telefono,
                mensaje,
                canal,
                estado,
                respuesta_api,
                fecha_envio
            )
            VALUES(
                '$idcliente',
                '$idcpc',
                '$telefono',
                '$mensaje',
                'WHATSAPP',
                '$estado',
                '$respuestaApi',
                NOW()
            )";

        ejecutarConsulta($sql);
    }

    /**
     * Notifica cuotas que vencen en 3 días
     */
    private function notificarCuotasPorVencer()
    {
        $sql = "SELECT
                    cc.idcpc,
                    cc.idventa,
                    cc.fechavencimiento,
                    cc.deuda,
                    v.idcliente,
                    c.nombre AS cliente,
                    c.telefono
                FROM cuentas_por_cobrar cc
                INNER JOIN venta v ON v.idventa = cc.idventa
                INNER JOIN persona c ON c.idpersona = v.idcliente
                WHERE DATE(cc.fechavencimiento) = DATE_ADD(CURDATE(), INTERVAL 3 DAY)
                AND cc.estado_pago = 1";

        $rspta = ejecutarConsulta($sql);

        while ($row = $rspta->fetch_object()) {

            if ($this->yaFueNotificado($row->idcpc)) {
                continue;
            }

            $mensaje = "Estimado {$row->cliente}, le recordamos que su cuota vence el {$row->fechavencimiento}. Saldo pendiente: S/ {$row->deuda}.";

            $respuesta = $this->enviarWhatsapp($row->telefono, $mensaje);

            $this->guardarNotificacion(
                $row->idcliente,
                $row->idcpc,
                $row->telefono,
                $mensaje,
                $respuesta
            );
        }
    }

    /**
     * Notifica cuotas vencidas
     */
    private function notificarCuotasVencidas()
    {
        $sql = "SELECT
                    cc.idcpc,
                    cc.idventa,
                    cc.fechavencimiento,
                    cc.deuda,
                    v.idcliente,
                    c.nombre AS cliente,
                    c.telefono
                FROM cuentas_por_cobrar cc
                INNER JOIN venta v ON v.idventa = cc.idventa
                INNER JOIN persona c ON c.idpersona = v.idcliente
                WHERE DATE(cc.fechavencimiento) < CURDATE()
                AND cc.estado_pago = 1";

        $rspta = ejecutarConsulta($sql);

        while ($row = $rspta->fetch_object()) {
            if ($this->yaFueNotificado($row->idcpc)) {
                continue;
            }
            $mensaje = "Estimado {$row->cliente}, su cuota con vencimiento {$row->fechavencimiento} se encuentra pendiente. Saldo: S/ {$row->deuda}.";

            echo "Enviando a {$row->telefono}: {$mensaje}\n";

            $respuesta = $this->enviarWhatsapp($row->telefono, $mensaje);

            $this->guardarNotificacion(
                $row->idcliente,
                $row->idcpc,
                $row->telefono,
                $mensaje,
                $respuesta
            );
        }
    }


    public function enviarWhatsapp($numero, $mensaje)
    {
        $url = "http://localhost:8081/message/sendText/mywhatasapp";

        $headers = [
            "Content-Type: application/json",
            "apikey: MiClaveMuySegura123"
        ];

        $data = [
            "number" => $numero,
            "text" => $mensaje
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}