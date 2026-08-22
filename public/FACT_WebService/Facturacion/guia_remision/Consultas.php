<?php

use Carbon\Exceptions\Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;

class Consultas{
    const SUNAT_CONSULT_API_ENDPOINT = 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/envios/';
    const SUNAT_CONSULT_API_ENDPOINT_TEST = "https://gre-test.nubefact.com/v1/contribuyente/gem/comprobantes/envios/";

    public function ConsultaTicket($token,$ticket) {
    
        $client = new GuzzleClient();

        $util = Util::getInstance();
		$conexion = $util->abrirConexion();
		$datosNegocio = mysqli_query($conexion, "SELECT * FROM datos_negocio LIMIT 1");
		$datosNegocioRow = $datosNegocio ? $datosNegocio->fetch_assoc() : null;
		$estadocertificado = isset($datosNegocioRow['estado_certificado']) ? $datosNegocioRow['estado_certificado'] : 'BETA';

		$urlSend = self::SUNAT_CONSULT_API_ENDPOINT_TEST;
		if ($estadocertificado === "PRODUCCION") {
		    $urlSend = self::SUNAT_CONSULT_API_ENDPOINT;
		}
        
        try {
            $res = $client->request('GET',
            $urlSend.$ticket,
            [
                'headers' => [
                    'User-Agent' => 'GyOManager/1.0',
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer ".$token,
                ],
            ]);
            return json_decode($res->getBody()->getContents(), true);
        } catch (ClientException $e) {
            $responseBody = null;
            try {
                $responseBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null;
            } catch (\Exception $inner) {
                $responseBody = null;
            }

            $errorData = [
                'error' => 'Consulta de ticket fallida',
                'message' => $e->getMessage(),
                'response' => null,
            ];
            if (!empty($responseBody)) {
                $decoded = json_decode($responseBody, true);
                $errorData['response'] = $decoded !== null ? $decoded : $responseBody;
            }

            return ['success' => false, 'data' => $errorData];
        } catch (\Exception $e) {
            return ['success' => false, 'data' => ['error' => 'Consulta de ticket fallida', 'message' => $e->getMessage()]];
        }

    }
}
