<?php

require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../modelos/CuentasCobrar.php';

class EnviarComprobantePago
{
    public function __construct(
        public int $idcpc,
        public string $comprobante,
        public float $monto,
        public array $clientData,
        public string $fechaPago,
        public int $idsucursal
    ) {
    }

    public function handle(): bool
    {
        $cuentasCobrar = new CuentasCobrar();

        return $cuentasCobrar->enviarComprobantePago(
            $this->idcpc,
            $this->comprobante,
            $this->monto,
            $this->clientData,
            $this->fechaPago,
            $this->idsucursal
        );
    }
}
