<?php

require_once __DIR__ . '/../../configuraciones/bootstrap.php';
require_once __DIR__ . '/../../core/Queue.php';
require_once __DIR__ . '/../../jobs/EnviarSms.php';
require_once __DIR__ . '/../../modelos/CuentasCobrar.php';
require_once __DIR__ . '/../../modelos/Helpers.php';

$cuentascobrar = new CuentasCobrar();
$helpers = new Helpers();

try {

    $sucursales = $helpers->sucursalesNotification();

    $procesados = 0;
    $duplicados = 0;
    $errores = 0;
    $totalCuotas = 0;

    foreach ($sucursales as $sucursal) {

        $idsucursal = $sucursal['idsucursal'];

        error_log('COBRANZA DIARIA: Procesando sucursal ' . $idsucursal);

        $cuotas = $cuentascobrar->listaCuotasVencidas($idsucursal);

        $totalCuotas += count($cuotas);

        foreach ($cuotas as $cuota) {

            $telefono = preg_replace('/\D/', '', $cuota['telefono']);

            if (strlen($telefono) === 9) {
                $telefono = '51' . $telefono;
            }

            if (!preg_match('/^51\d{9}$/', $telefono)) {
                error_log('Cobranza SMS: teléfono inválido para persona ' . $cuota['idpersona']);
                $errores++;
                continue;
            }

            $fechaVencimiento = new DateTime($cuota['fechavencimiento']);
            $hoy = new DateTime('today');

            $diasAtraso = (int) $fechaVencimiento->diff($hoy)->format('%r%a');

            $monto = number_format((float) $cuota['deuda'], 2);
            $fecha = $fechaVencimiento->format('d/m/Y');
            $venta = $cuota['serie_comprobante'] . '-' . $cuota['num_comprobante'];
            $nombre = trim($cuota['cliente']);
            $numeroCuota = $cuota['numero_cuota'];

            if ($diasAtraso === 0) {
                $mensaje = "VENTASPRO: Hola {$nombre}, hoy vence tu cuota N° {$numeroCuota} de la venta {$venta} por S/ {$monto}. Recuerda realizar tu pago.";
            } else {
                $mensaje = "VENTASPRO: Hola {$nombre}, tu cuota N° {$numeroCuota} de la venta {$venta} por S/ {$monto} venció el {$fecha}. Regulariza tu pago. Gracias.";
            }

            try {

                // Registrar y verificar si ya existe un recordatorio hoy
                $idRecordatorio = $cuentascobrar->registrarRecordatorio(
                    $cuota['idcpc'],
                    'SMS',
                    $telefono,
                    $mensaje
                );

                error_log('Cobranza SMS: registrado recordatorio para cuota ' . $cuota['idcpc'] . ' con ID ' . $idRecordatorio);

                if (empty($idRecordatorio)) {
                    $duplicados++;
                    continue;
                }

                // Enviar a la cola SMS
                Queue::dispatch(
                    new EnviarSMS(
                        $telefono,
                        $mensaje,
                        'transaccional',
                        $idRecordatorio
                    )
                )->onQueue('sms')->dispatch();

                $procesados++;

            } catch (Throwable $e) {

                error_log(
                    'Error despachando SMS de cuota ' .
                    $cuota['idcpc'] . ': ' . $e->getMessage()
                );

                $errores++;
            }
        }
    }

    error_log(
        'COBRANZA DIARIA: ' .
        count($sucursales) . ' sucursales habilitadas | ' .
        $totalCuotas . ' cuotas encontradas | ' .
        $procesados . ' SMS despachados | ' .
        $duplicados . ' duplicados omitidos | ' .
        $errores . ' errores.'
    );

} catch (Throwable $e) {
    error_log('Error en cobranza diaria: ' . $e->getMessage());
}