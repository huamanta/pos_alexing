<?php

use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;

class SunatGenerateToken
{
	protected $sunatClientId;
	protected $sunatClientSecret;
	protected $username;
	protected $password;

	public function __construct($args)
	{
		$this->sunatClientId = isset($args['sunat_client_id']) ? $args['sunat_client_id'] : '';
		$this->sunatClientSecret = isset($args['sunat_client_secret']) ? $args['sunat_client_secret'] : '';
		$this->username = (isset($args['ruc']) ? $args['ruc'] : '') . (isset($args['usuario_sol']) ? $args['usuario_sol'] : '');
		$this->password = isset($args['clave_sol']) ? $args['clave_sol'] : '';
	}

	public function generateSunatToken()
	{

		$util = Util::getInstance();
		$conexion = $util->abrirConexion();
		$datosNegocio = mysqli_query($conexion, "SELECT * FROM datos_negocio LIMIT 1");
		$datosNegocioRow = $datosNegocio ? $datosNegocio->fetch_assoc() : null;
		$estadocertificado = isset($datosNegocioRow['estado_certificado']) ? $datosNegocioRow['estado_certificado'] : 'BETA';
		$clientId = !empty($this->sunatClientId) ? $this->sunatClientId : (isset($datosNegocioRow['client_id']) ? $datosNegocioRow['client_id'] : '');
		$clientSecret = !empty($this->sunatClientSecret) ? $this->sunatClientSecret : (isset($datosNegocioRow['client_secret']) ? $datosNegocioRow['client_secret'] : '');
		$username = !empty($this->username) ? $this->username : ((isset($datosNegocioRow['documento']) ? $datosNegocioRow['documento'] : '') . (isset($datosNegocioRow['usuario_sol']) ? $datosNegocioRow['usuario_sol'] : ''));
		$password = !empty($this->password) ? $this->password : (isset($datosNegocioRow['clave_sol']) ? $datosNegocioRow['clave_sol'] : '');
		$response = [];

		$logRequest = [
			'timestamp' => date('c'),
			'event' => 'SunatGenerateToken request',
			'estado_certificado' => $estadocertificado,
			'endpoint' => null,
			'payload' => [
				'grant_type' => 'password',
				'scope' => 'https://api-cpe.sunat.gob.pe',
				'client_id' => $clientId,
				'client_id_present' => !empty($clientId),
				'client_secret_present' => !empty($clientSecret),
				'username' => $username,
				'password_present' => !empty($password),
			],
			'source' => [
				'args' => [
					'sunat_client_id_set' => !empty($this->sunatClientId),
					'sunat_client_secret_set' => !empty($this->sunatClientSecret),
					'ruc_set' => !empty($this->username),
					'usuario_sol_set' => !empty($this->username),
					'clave_sol_set' => !empty($this->password),
				],
				'db' => [
					'client_id' => isset($datosNegocioRow['client_id']) ? $datosNegocioRow['client_id'] : null,
					'client_secret_present' => isset($datosNegocioRow['client_secret']) && $datosNegocioRow['client_secret'] !== '',
					'documento' => isset($datosNegocioRow['documento']) ? $datosNegocioRow['documento'] : null,
					'usuario_sol' => isset($datosNegocioRow['usuario_sol']) ? $datosNegocioRow['usuario_sol'] : null,
					'clave_sol' => isset($datosNegocioRow['clave_sol']) ? $datosNegocioRow['clave_sol'] : null,
				],
			],
		];

		$missing = [];
		if (empty($clientId)) {
			$missing[] = 'client_id';
		}
		if (empty($clientSecret)) {
			$missing[] = 'client_secret';
		}
		if (empty($username)) {
			$missing[] = 'username';
		}
		if (empty($password)) {
			$missing[] = 'password';
		}

		if (!empty($missing)) {
			$logData = [
				'timestamp' => date('c'),
				'error' => 'Missing SUNAT credentials',
				'fields' => $missing,
				'values' => [
					'client_id' => !empty($clientId),
					'client_secret' => !empty($clientSecret),
					'username' => !empty($username),
					'password' => !empty($password),
					'constructed_username' => $username,
				],
				'db' => [
					'client_id' => isset($datosNegocioRow['client_id']) ? $datosNegocioRow['client_id'] : null,
					'client_id_len' => isset($datosNegocioRow['client_id']) ? strlen($datosNegocioRow['client_id']) : 0,
					'client_secret' => isset($datosNegocioRow['client_secret']) ? $datosNegocioRow['client_secret'] : null,
					'client_secret_len' => isset($datosNegocioRow['client_secret']) ? strlen($datosNegocioRow['client_secret']) : 0,
					'documento' => isset($datosNegocioRow['documento']) ? $datosNegocioRow['documento'] : null,
					'usuario_sol' => isset($datosNegocioRow['usuario_sol']) ? $datosNegocioRow['usuario_sol'] : null,
					'clave_sol' => isset($datosNegocioRow['clave_sol']) ? $datosNegocioRow['clave_sol'] : null,
				],
				'source' => [
					'args' => [
						'sunat_client_id_set' => !empty($this->sunatClientId),
						'sunat_client_secret_set' => !empty($this->sunatClientSecret),
						'ruc_set' => !empty($this->username),
						'usuario_sol_set' => !empty($this->username),
						'clave_sol_set' => !empty($this->password),
					],
				],
			];
			$this->writeLog($logData);

			return ['success' => false, 'response' => [
				'error' => 'Missing SUNAT credentials',
				'fields' => $missing,
				'values' => [
					'client_id' => !empty($clientId),
					'client_secret' => !empty($clientSecret),
					'username' => !empty($username),
					'password' => !empty($password),
					'constructed_username' => $username,
				],
				'source' => [
					'args' => [
						'sunat_client_id_set' => !empty($this->sunatClientId),
						'sunat_client_secret_set' => !empty($this->sunatClientSecret),
						'ruc_set' => !empty($this->username),
						'usuario_sol_set' => !empty($this->username),
						'clave_sol_set' => !empty($this->password),
					],
					'db' => [
						'client_id' => isset($datosNegocioRow['client_id']) && $datosNegocioRow['client_id'] !== '',
						'client_secret' => isset($datosNegocioRow['client_secret']) && $datosNegocioRow['client_secret'] !== '',
						'documento' => isset($datosNegocioRow['documento']) && $datosNegocioRow['documento'] !== '',
						'usuario_sol' => isset($datosNegocioRow['usuario_sol']) && $datosNegocioRow['usuario_sol'] !== '',
						'clave_sol' => isset($datosNegocioRow['clave_sol']) && $datosNegocioRow['clave_sol'] !== '',
					],
				],
			]];
		}

		if ($estadocertificado == "BETA") {
			/**
			 * PARA PRUEBAS USANDO NUBEFACT
			 */

			$endpoint = 'https://gre-test.nubefact.com/v1/clientessol/' . $clientId . '/oauth2/token';
			$logRequest['endpoint'] = $endpoint;
			$logRequest['payload']['username'] = $username;
			$this->writeLog($logRequest);

			$array = array(
				"grant_type" => 'password',
				"scope" => 'https://api-cpe.sunat.gob.pe',
				"client_id" => $clientId,
				"client_secret" => $clientSecret,
				"username" => $username,
				"password" => $password
			);

			$data = http_build_query($array);

			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => $endpoint,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $data,
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded'
			  ),
			));

			$responseBody = curl_exec($curl);
			$curlError = curl_error($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);

			$logResponse = [
				'timestamp' => date('c'),
				'event' => 'SunatGenerateToken response',
				'estado_certificado' => $estadocertificado,
				'endpoint' => $endpoint,
				'http_code' => $httpCode,
				'curl_error' => $curlError,
				'response_body' => $responseBody,
			];
			$this->writeLog($logResponse);

			if ($responseBody === false || $curlError !== '') {
				return ['success' => false, 'response' => ['error' => 'Token request failed', 'details' => $curlError]];
			}

			$res = json_decode($responseBody, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				return ['success' => false, 'response' => ['error' => 'Token response JSON decode failed', 'body' => $responseBody]];
			}

			$response = array(
				"access_token" => $res["access_token"] ?? null,
				"token_type" => $res["token_type"] ?? null,
				"expires_in" => $res["expires_in"] ?? null,
				"raw" => $res
			);

			if (empty($response['access_token'])) {
				return ['success' => false, 'response' => $res];
			}

		} elseif ($estadocertificado == "PRODUCCION") {

			// $sunatUri = "https://api-seguridad.sunat.gob.pe/v1/clientessol/{$this->sunatClientId}/oauth2/token/";
			// $params = [
			//     'grant_type' => 'password',
			//     'scope' => 'https://api-cpe.sunat.gob.pe',
			//     'client_id' => $this->sunatClientId,
			//     'client_secret' => $this->sunatClientSecret,
			//     'username' => $this->username,
			//     'password' => $this->password
			// ];

			$endpoint = 'https://api-seguridad.sunat.gob.pe/v1/clientessol/' . $clientId . '/oauth2/token/';
			$logRequest['endpoint'] = $endpoint;
			$logRequest['payload']['username'] = $username;
			$this->writeLog($logRequest);

			$array = array(
				"grant_type" => 'password',
				"scope" => 'https://api-cpe.sunat.gob.pe',
				"client_id" => $clientId,
				"client_secret" => $clientSecret,
				"username" => $username,
				"password" => $password
			);

			$data = http_build_query($array);

			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => $endpoint,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $data,
			  CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded'
			  ),
			));

			$responseBody = curl_exec($curl);
			$curlError = curl_error($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);

			$logResponse = [
				'timestamp' => date('c'),
				'event' => 'SunatGenerateToken response',
				'estado_certificado' => $estadocertificado,
				'endpoint' => $endpoint,
				'http_code' => $httpCode,
				'curl_error' => $curlError,
				'response_body' => $responseBody,
			];
			$this->writeLog($logResponse);

			if ($responseBody === false || $curlError !== '') {
				return ['success' => false, 'response' => ['error' => 'Token request failed', 'details' => $curlError]];
			}

			$res = json_decode($responseBody, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				return ['success' => false, 'response' => ['error' => 'Token response JSON decode failed', 'body' => $responseBody]];
			}

			$response = array(
				"access_token" => $res["access_token"] ?? null,
				"token_type" => $res["token_type"] ?? null,
				"expires_in" => $res["expires_in"] ?? null,
				"raw" => $res
			);

			if (empty($response['access_token'])) {
				return ['success' => false, 'response' => $res];
			}

		}

		if (!is_array($response)) {
			$response = [];
		}

		return ['success' => true, 'response' => $response];
	}

	private function writeLog(array $data)
	{
		$path = __DIR__ . '/guia_remision.log';
		$entry = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
		$result = @file_put_contents($path, $entry, FILE_APPEND | LOCK_EX);
		if ($result === false) {
			$error = sprintf("SunatGenerateToken log write failed to %s", $path);
			error_log($error);
			error_log(print_r($data, true));
			$fallbackPath = __DIR__ . '/guia_remision_fallback.log';
			@file_put_contents($fallbackPath, $entry, FILE_APPEND | LOCK_EX);
		}
	}
}
