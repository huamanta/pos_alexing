<?php
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
class SunatSendDocument
{
	const SUNAT_SEND_API_ENDPOINT = 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/';
    const SUNAT_SEND_API_ENDPOINT_TEST = "https://gre-test.nubefact.com/v1/contribuyente/gem/comprobantes/";
    const SUNAT_CONSULT_API_ENDPOINT = 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/envios/';
    const SUNAT_CONSULT_API_ENDPOINT_TEST = "https://gre-test.nubefact.com/v1/contribuyente/gem/comprobantes/envios/";

	private $fileName;
    private $ruc;
    private $token;
	
	public function send($args,$fileName) {

        $generador = new SunatGenerateToken($args);
        $token = $generador->generateSunatToken();

        if (!$token['success'] || empty($token['response']['access_token'])) {
            return ['success' => false, 'data' => $token['response']];
        }

        $this->fileName = $fileName;
        $this->token = $token['response']['access_token'];
        return $this->request();
    }

	private function request()
    {
        if (empty($this->token)) {
            return ['success' => false, 'data' => ['error' => 'Empty access token']];
        }

        $client = new GuzzleClient();
        $Operacion = new Operaciones();
        $zipPath = __DIR__ . '/../files/' . $this->fileName . ".zip";
        if (!file_exists($zipPath)) {
            return ['success' => false, 'data' => ['error' => 'Zip file not found', 'path' => $zipPath]];
        }

        try {
            $data = [
                'archivo' => [
                    'nomArchivo' => "$this->fileName.zip",
                    'arcGreZip' => $Operacion->ConvertirBase64($this->fileName),
                    'hashZip' => $Operacion->ConvertirSHA256($this->fileName),
                ],
            ];

            $data = json_encode($data);

            $util = Util::getInstance();
            $conexion = $util->abrirConexion();
            $datosNegocio = mysqli_query($conexion, "SELECT * FROM datos_negocio LIMIT 1");
            $datosNegocioRow = $datosNegocio ? $datosNegocio->fetch_assoc() : null;
            $estadocertificado = isset($datosNegocioRow['estado_certificado']) ? $datosNegocioRow['estado_certificado'] : 'BETA';
            $urlSend = self::SUNAT_SEND_API_ENDPOINT_TEST;

            if ($estadocertificado === "PRODUCCION") {
                $urlSend = self::SUNAT_SEND_API_ENDPOINT;
            }

            $res = $client->request('POST', $urlSend . $this->fileName, [
                    'headers' => [
                        'User-Agent' => 'GyOManager/1.0',
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer {$this->token}",
                    ],
                    'body' => $data,
                ]);

            $response = json_decode($res->getBody(), true);
            return ['success' => true, 'token' => $this->token, 'data' => $response];
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);
            return ['success' => false, 'data' => $response];
        } catch (\Exception $e) {
            return ['success' => false, 'data' => ['error' => $e->getMessage()]];
        }
    }
}