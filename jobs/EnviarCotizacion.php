<?php

require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/../modelos/Cotizaciones.php';

class EnviarCotizacion
{
    public function __construct(
        public int $idcotizacion,
        public array $comprobante,
        public array $clientData,
        public string $fechaHora,
        public int $idsucursal
    ) {
    }

    public function handle(): bool
    {
        $cotizacion = new Cotizacion();

        return $cotizacion->enviarCotizacion(
            $this->idcotizacion,
            $this->comprobante,
            $this->clientData,
            $this->fechaHora,
            $this->idsucursal
        );
    }
}
