<?php
ob_start(); // evita que cualquier salida accidental rompa el JSON
require_once "../modelos/CuentasCobrar.php";
require_once "../modelos/Negocio.php";

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$cuentas = new CuentasCobrar();
$negocio = new Negocio();

$infoNegocio = $negocio->mostrarNombreNegocio(); 
$nombreNegocio = $infoNegocio ? $infoNegocio['nombre'] : 'Su negocio';

$resultados = [];
$rspta = $cuentas->listarRecordatorioSemana();

$token    = "jnwevzsedjw6kxax";
$instance = "instance142923";

$clientesEnviadosHoy = $cuentas->listarClientesEnviadosHoy(); 
$clientesProcesados = []; // evita duplicados en esta ejecución

while ($reg = $rspta->fetch_object()) {
    $telefono = $reg->telefono;
    if (!$telefono) continue;

    if (in_array($reg->idcliente, $clientesEnviadosHoy) || in_array($reg->idcliente, $clientesProcesados)) {
        $resultados[] = [
            "cliente"   => $reg->nombre,
            "telefono"  => $telefono,
            "respuesta" => "Ya se envió hoy"
        ];
        continue;
    }

    if (substr($telefono, 0, 2) != "51") $telefono = "51" . $telefono;

    $mensaje = "Estimado {$reg->nombre}, le recordamos que su deuda de S/. {$reg->deudatotal} vence el {$reg->fechavencimiento}. 
Atentamente: $nombreNegocio.";

    $url = "https://api.ultramsg.com/$instance/messages/chat?token=$token";
    $data = json_encode(["to" => $telefono, "body" => $mensaje]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if (!$err) {
        $sqlInsert = "INSERT INTO recordatorio_envios (idcpc, idcliente,fecha_envio) VALUES ('{$reg->idcpc}', '{$reg->idcliente}',NOW()))";
        ejecutarConsulta($sqlInsert);
        $clientesProcesados[] = $reg->idcliente;
    }

    $resultados[] = [
        "cliente"   => $reg->nombre,
        "telefono"  => $telefono,
        "respuesta" => $err ?: $response
    ];
}

echo json_encode(["success" => true, "data" => $resultados]);
