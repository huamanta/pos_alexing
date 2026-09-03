<?php
require_once __DIR__ . "/../Helpers.php";
require_once __DIR__ . "/../../core/Constants.php";
use Carbon\Carbon;
class SisVenta extends Helpers
{

    public function __construct()
    {
        parent::__construct();
    }

    public function insertar(
        $idusuario,
        $idsucursal,
        $idcliente,
        $idpersonal,
        $idcaja,
        $idtipo_comprobante,
        $serie_comprobante,
        $num_comprobante,
        $impuesto,
        $total_venta,
        $tipopago,
        $formapago,
        $nroOperacion,
        $fechaDepostivo,
        $porcentaje,
        $totalrecibido,
        $totaldeposito,
        $vuelto,
        $tipo,
        $banco,
        $idproducto,
        $nombre,
        $cantidad,
        $precio_venta,
        $descuento,
        $fechaOperacion,
        $montoDeuda,
        $montoPagado,
        $comprobanteReferencia,
        $idmotivo,
        $observaciones,
        $fecha_pago,
        $interes,
        $input_cuotas,
        $input_frecuencia,
        $cantidad_contenedor,
        $contenedor,
        $idp,
        $check_precio,
        $id_fifo_lote,
        $idcategoria,
        $idgarante,
        $idacompanante,
        $idtipoacompanante,
        $idserie
    ) {
        $this->pdo->beginTransaction();
        try {
            $idcliente = Helpers::clienteDefault($idcliente);
            
            // Datos por defecto
            $fechaActual = Carbon::now();
            $idmotivo = $idmotivo ?: 0;
            $porcentaje = $porcentaje ?: 0;
            $fechaDepostivo = $fechaDepostivo ?: $fechaActual;
            $input_cuotas = $input_cuotas ?: 0;

            if ($idtipo_comprobante == 1) {
                $estado = "Activado";
                $dovEstado = "ACEPTADO";
            } else {
                $estado = "Por Enviar";
                $dovEstado = "";
            }

            // Correlativo
            $config = Helpers::actualizarCorrelativo($idtipo_comprobante, $idsucursal);

            // Forma de pago real
            $formapagoVenta = $formapago;

            if (!empty($_POST['metodo_pago'])) {

                $metodos = array_filter($_POST['metodo_pago']);

                if (count($metodos) > 1) {

                    $formapagoVenta = "Mixto";

                } elseif (count($metodos) == 1) {

                    $formapagoVenta = reset($metodos);
                }
            }

            // Cabecera
            $idVenta = $this->guardarCabecera(
                $idsucursal,
                $idcaja,
                $idcliente,
                $idpersonal,
                $idmotivo,
                $idtipo_comprobante,
                $config['serie_comprobante'],
                $config['num_comprobante'],
                $fechaActual,
                $impuesto,
                $total_venta,
                $tipopago,
                $interes,
                $formapagoVenta,
                $input_frecuencia,
                $input_cuotas,
                $nroOperacion,
                $fechaDepostivo,
                $porcentaje,
                $totalrecibido,
                $totaldeposito,
                $vuelto,
                $banco,
                $montoPagado,
                $estado,
                $comprobanteReferencia,
                $dovEstado,
                $observaciones,
                $fechaActual,
                $idgarante,
                $idacompanante,
                $idtipoacompanante
            );

            // Pagos Mixtos
            $this->guardarPagos($idVenta, $idsucursal, $idusuario);

            // Detalles
            $this->guardarDetalles(
                $idVenta,
                $idsucursal,
                $idproducto,
                $idserie,
                $nombre,
                $cantidad,
                $precio_venta,
                $descuento,
                $cantidad_contenedor,
                $contenedor,
                $idp,
                $check_precio,
                $id_fifo_lote,
                $idcategoria,
                $tipo,
                $num_comprobante,
                $fechaActual
            );

            //=========================
            // Crédito
            if ($tipopago == "Si") {
                $this->crearCredito($idVenta, $fechaActual, $montoDeuda, $interes, $input_cuotas, $fecha_pago);
            }

            // Cotización
            if (!empty($comprobanteReferencia) && $tipo == "venta") {
                $this->actualizarCotizacion($comprobanteReferencia);
            }

            // Documentación=
            if ($tipopago == "Si") {
                $this->crearDocumentacion($idVenta);
            }

            $this->pdo->commit();

            return json_encode([
                'success' => true,
                'id_venta' => $idVenta,
                'enviar_sunat' => Helpers::verificarEnvioSunat($idsucursal),
                'message' => 'Venta registrada correctamente.'
            ]);

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    private function crearDocumentacion(
        int $idventa
    ): bool {

        $tipos = [1]; // 1 = contrato crédito
        foreach ($tipos as $tipo) {

            /*
             * Obtener correlativo siguiente
             */
            $row = (new DBQuery($this->pdo))
                ->query("
                SELECT 
                    COALESCE(MAX(correlativo),0) + 1 AS correlativo
                FROM documentacion
                WHERE tipo = :tipo
            ", [
                    'tipo' => $tipo
                ])
                ->first();

            $correlativo = $row['correlativo'] ?? 1;
            /*
             * Insertar documento
             */
            $fecha_kardex = Carbon::now();
            $resultado = (new FluentSaver($this->pdo))
                ->table('documentacion')
                ->data([
                    'fecha_contrato' => $fecha_kardex,
                    'tipo' => $tipo,
                    'correlativo' => $correlativo,
                    'estado' => 1,
                    'idventa' => $idventa
                ])
                ->save();

            if (!$resultado) {
                return false;
            }

        }

        return true;
    }

    private function actualizarCotizacion(
        int $idcotizacion
    ): bool {

        if ($idcotizacion <= 0) {
            return true;
        }

        $resultado = (new FluentSaver($this->pdo))
            ->table('cotizacion')
            ->primaryKey('idcotizacion')
            ->data([
                'idcotizacion' => $idcotizacion,
                'estado' => 'VENDIDO'
            ])
            ->update();

        return $resultado !== false;
    }


    private function crearCredito(
        int $idventa,
        string $fechaRegistro,
        float $montoDeuda,
        float $interes,
        int $cantidadCuotas,
        array $fechasPago
    ): bool {


        if ($cantidadCuotas <= 0) {
            $cantidadCuotas = 1;
        }

        $interesTotal = round($montoDeuda * ($interes / 100), 2);
        $capitalCuotaBase = round($montoDeuda / $cantidadCuotas, 2);
        $interesCuotaBase = round($interesTotal / $cantidadCuotas, 2);
        $capitalAcumulado = 0;
        $interesAcumulado = 0;

        foreach ($fechasPago as $index => $fechaVencimiento) {

            # Valores base
            $capitalCuota = $capitalCuotaBase;
            $interesCuota = $interesCuotaBase;

            # Ajuste última cuota
            if ($index == ($cantidadCuotas - 1)) {
                $capitalCuota = round($montoDeuda - $capitalAcumulado, 2);
                $interesCuota = round($interesTotal - $interesAcumulado, 2);
            }

            $totalCuota = round($capitalCuota + $interesCuota, 2);

            $insert = (new FluentSaver($this->pdo))
                ->table('cuentas_por_cobrar')
                ->data([
                    'idventa' => $idventa,
                    'fecharegistro' => $fechaRegistro,
                    'deudatotal' => $totalCuota,
                    'deuda_base' => $capitalCuota,
                    'mora' => 0,
                    'mora_pagada' => 0,
                    'fechavencimiento' => $fechaVencimiento,
                    'abonototal' => 0,
                    'deuda' => $totalCuota,
                    'interes' => $interesCuota,
                    'estado_pago' => 1
                ])
                ->save();

            if (!$insert) {
                throw new Exception('No se pudo guardar la cuota .');
            }

            $capitalAcumulado += $capitalCuota;
            $interesAcumulado += $interesCuota;
        }


        return true;
    }

    private function guardarCabecera(
        $idsucursal,
        $idcaja,
        $idcliente,
        $idpersonal,
        $idmotivo,
        $idtipo_comprobante,
        $serie_comprobante,
        $num_comprobante,
        $fecha_hora,
        $impuesto,
        $total_venta,
        $ventacredito,
        $interes,
        $formapago,
        $frecuencia,
        $meses,
        $numoperacion,
        $fechadeposito,
        $descuento,
        $totalrecibido,
        $totaldeposito,
        $vuelto,
        $banco,
        $montoPagado,
        $estado,
        $documento_rel,
        $dovEstado,
        $observacion,
        $fechaActual,
        $idgarante,
        $idacompanante,
        $idtipoacompanante
    ): int {
        $venta = (new FluentSaver($this->pdo))
            ->table('venta')
            ->data([
                'idsucursal' => $idsucursal,
                'idcaja' => $idcaja,
                'idcliente' => $idcliente,
                'idpersonal' => $idpersonal,
                'idmotivo_nota' => $idmotivo,
                'idcomprobante_pago' => $idtipo_comprobante,
                'serie_comprobante' => $serie_comprobante,
                'num_comprobante' => $num_comprobante,
                'fecha_hora' => $fecha_hora,
                'impuesto' => $impuesto,
                'total_venta' => $total_venta,
                'ventacredito' => $ventacredito,
                'interes' => $interes,
                'formapago' => $formapago,
                'frecuencia' => $frecuencia,
                'meses' => $meses,
                'numoperacion' => $numoperacion,
                'fechadeposito' => $fechadeposito,
                'descuento' => $descuento,
                'totalrecibido' => $totalrecibido,
                'totaldeposito' => $totaldeposito,
                'vuelto' => $vuelto,
                'montoPagado' => $montoPagado,
                'estado' => $estado,
                'documento_rel' => $documento_rel,
                'dov_Estado' => $dovEstado,
                'observacion' => $observacion,
                'fecha_kardex' => $fechaActual,
                'idgarante' => $idgarante ?: null,
                'idacompanante' => $idacompanante ?: null,
                'idtipoacompanante' => $idtipoacompanante ?: null
            ])
            ->save();

        if (!$venta) {
            throw new Exception("No se pudo registrar la venta");
        }

        return (int) $venta;

    }

    private function guardarPagos(int $idVenta, int $idsucursal, int $idusuario): void
    {
        // Si no viene pagos mixtos no hacemos nada
        if (empty($_POST['metodo_pago']) || !is_array($_POST['metodo_pago'])) {
            return;
        }

        $metodos = $_POST['metodo_pago'];
        $montos = $_POST['monto_real_pago'] ?? [];
        $operaciones = $_POST['nroOperacion_pago'] ?? [];
        $bancos = $_POST['banco_pago'] ?? [];
        $fechas = $_POST['fecha_deposito_pago'] ?? [];

        foreach ($metodos as $i => $metodo) {
            if (empty($metodo) || !isset($montos[$i])) {
                continue;
            }

            $monto = floatval($montos[$i]);
            if ($monto <= 0) {
                continue;
            }


            (new FluentSaver($this->pdo))
                ->table('venta_pago')
                ->data([
                    'idventa' => $idVenta,
                    'metodo_pago' => $metodo,
                    'monto' => $monto,
                    'nroOperacion' => !empty($operaciones[$i]) ? $operaciones[$i] : null,
                    'idbanco' => !empty($bancos[$i]) ? $bancos[$i] : null,
                    'fechaDeposito' => !empty($fechas[$i]) ? $fechas[$i] : null
                ])
                ->save();

            // sumar montos de bancos si llega banco 
            if ($metodo != 'Efectivo' && $monto > 0 && !empty($bancos[$i])) {
                $sumarBanco = Helpers::incrementarBanco($bancos[$i], $monto);
                if (!$sumarBanco) {
                    throw new Exception("Error al ingrmentar saldo banco");
                }
            }

            // sumar caja aperturada 
            if ($metodo === 'Efectivo' && $monto > 0) {
                $caja = Helpers::cajaAperturada($idsucursal, $idusuario);

                if (!$caja) {
                    throw new Exception("No existe una caja abierta para el usuario.");
                }

                $sumarCaja = Helpers::incrementarCajaApertura($caja['aperturacajaid'], $monto);
                if (!$sumarCaja) {
                    throw new Exception("Error al incrementar el efectivo de la caja.");
                }
            }
        }
    }

    private function guardarDetalles(
        int $idVenta,
        int $idsucursal,
        array $idproducto,
        array $idserie,
        array $nombre,
        array $cantidad,
        array $precio_venta,
        array $descuento,
        array $cantidad_contenedor,
        array $contenedor,
        array $idp,
        array $check_precio,
        array $id_fifo_lote,
        array $idcategoria,
        string $tipo,
        string $num_comprobante,
        string $fechaActual
    ): void {

        foreach ($idp as $i => $idProductoConfig) {
            $idProductoCongiguracion = (int) ($idp[$i] ?? 0);
            $idProducto = (int) ($idproducto[$i] ?? 0);
            $idSerie = (int) ($idserie[$i] ?? null);
            $cant = floatval($cantidad[$i] ?? 0);
            $precio = floatval($precio_venta[$i] ?? 0);
            $desc = floatval($descuento[$i] ?? 0);
            $factor = floatval($cantidad_contenedor[$i] ?? 1);
            if ($factor <= 0) {
                $factor = 1;
            }

            //Buscar configuración stock
            $producto = (new DBQuery($this->pdo))
                ->from('producto p')
                ->select(['p.controla_stock', 'p.nombre', 'p.idcategoria', 'p.tipo_producto'])
                ->where('p.idproducto', '=', $idProducto)
                ->first();

            if (!$producto) {
                throw new Exception("Producto no existe");
            }

            //Motos con serie
            $serie = (new DBQuery($this->pdo))
                ->from('producto_serie')
                ->where('idserie', '=', $idSerie)
                ->where('idsucursal', '=', $idsucursal)
                ->first();

            if ($serie['estado'] != 'DISPONIBLE') {
                throw new Exception("El producto seleccionado ya no se ecnuentra disponible");
            }


            if ($serie && ($producto['tipo_producto'] == 'Vehiculo')) {
                (new DBQuery($this->pdo))
                    ->query(
                        "UPDATE producto_serie SET estado='VENDIDO', updated_at=NOW() WHERE idserie=:id",
                        [
                            'id' => $idSerie
                        ]
                    )
                    ->get();
            }

            // Detalle venta
            $this->insertarDetalleVenta(
                $idsucursal,
                $idVenta,
                $idProducto,
                $idProductoCongiguracion,
                $idSerie,
                $nombre[$i],
                $cant,
                $contenedor[$i] ?? '',
                $factor,
                $precio,
                $desc,
                $tipo,
                $check_precio[$i] ?? ''
            );
        }

    }

    private function movimientoSalida(
        $idDetalleVenta,
        $rowProduct,
        $idProductoCongiguracion,
        $idsucursal,
        $idserie,
        $cantidad,
        $precioVenta,
        $motivo = '',
        $idventa = null
    ) {

        //BLOQUEAR INVENTARIO GENERAL
        $inventario = (new DBQuery($this->pdo))
            ->from('inventario_producto')
            ->where('idproducto', '=', $rowProduct['idproducto'])
            ->where('idsucursal', '=', $idsucursal)
            ->forUpdate()
            ->first();

        if (!$inventario) {
            throw new Exception(
                "No existe inventario del producto."
            );
        }

        //VALIDAR STOCK GENERAL
        if ((float)$inventario['stock'] < $cantidad) {
            throw new Exception(
                "Stock insuficiente. Disponible: {$inventario['stock']}"
            );
        }

        //DESCONTAR LOTES FEFO
        $verificarLote = Helpers::verificarVentaLotes($idsucursal);
        if($verificarLote['activo'] && $rowProduct['is_venta_lote']){
            $this->descontarLotesFEFO(
                $idDetalleVenta,
                $rowProduct['idproducto'],
                $idsucursal,
                $cantidad,
                $idventa
            );
        }

        //CONFIGURACIÓN DEL PRODUCTO
        $rowConfiguracion = (new DBQuery($this->pdo))
            ->from('producto_configuracion')
            ->where('idproducto_configuracion', '=', $idProductoCongiguracion)
            ->first();

        if (!$rowConfiguracion) {
            throw new Exception(
                "No existe configuración del producto."
            );
        }

        // ACTUALIZAR STOCK GENERAL
        $nuevoStock = (float)$inventario['stock'] - $cantidad;
        $updateInventario = (new FluentSaver($this->pdo))
            ->table('inventario_producto')
            ->primaryKey('idinventario')
            ->data([
                'idinventario' => $inventario['idinventario'],
                'stock' => $nuevoStock
            ])
            ->update();

        if (!$updateInventario) {
            throw new Exception(
                "No se pudo actualizar el inventario."
            );
        }
        if($rowProduct['controla_stock'] === 'Si') {
            Helpers::updateKardexSucursal(
                $idsucursal,
                $rowProduct['idproducto'],
                $rowConfiguracion['idproducto_configuracion'],
                $cantidad,
                $rowConfiguracion['cantidad_contenedor'],
                $precioVenta,
                $nuevoStock,
                Constants::EGRESO_KARDEX,
                'Salida por venta',
                $motivo
            );
        }

        return [
            "success" => true,
            "message" => ""
        ];
    }


    private function insertarDetalleVenta(
        int $idsucursal,
        int $idventa,
        int $idProducto,
        int $idProductoCongiguracion,
        int $idSerie,
        string $nombreProducto,
        float $cantidad,
        string $contenedor,
        float $cantidadContenedor,
        float $precioVenta,
        float $descuento,
        string $tipo,
        string $checkPrecio
    ): void {
        $idDetalleVenta = (new FluentSaver($this->pdo))
            ->table('detalle_venta')
            ->data([
                'idsucursal' => $idsucursal,
                'idventa' => $idventa,
                'idproducto' => $idProducto,
                'idproducto_configuracion' => $idProductoCongiguracion,
                'idserie' => $idSerie,
                'nombre_producto' => $nombreProducto,
                'cantidad' => $cantidad,
                'contenedor' => $contenedor,
                'cantidad_contenedor' => $cantidadContenedor,
                'precio_venta' => $precioVenta,
                'descuento' => $descuento,
                'tipo' => $tipo,
                'check_precio' => $checkPrecio
            ])
            ->save();

        if (!$idDetalleVenta) {
            throw new Exception("No se pudo guardar el detalle de la venta.");
        }

        $sqlProduct = "SELECT * FROM producto WHERE idproducto=:idproducto AND idsucursal=:idsucursal";
        $stmtProduct = $this->pdo->prepare($sqlProduct);
        $stmtProduct->execute([
            'idproducto' => $idProducto,
            'idsucursal' => $idsucursal
        ]);
        $rowProduct = $stmtProduct->fetch(PDO::FETCH_ASSOC);
        $motivo = "Salida generada por la venta #{$idventa}";
        $this->movimientoSalida(
            $idDetalleVenta,
            $rowProduct,
            $idProductoCongiguracion,
            $idsucursal,
            $idSerie,
            $cantidad,
            $precioVenta,
            $motivo
        );

    }

    private function descontarLotesFEFO(
        int $idDetalleVenta,
        int $idproducto,
        int $idsucursal,
        float $cantidad,
        ?int $idventa = null
    ): void {

        $pendiente = $cantidad;

        $lotes = (new DBQuery($this->pdo))
                ->from('inventario_lote')
                ->where('idproducto', '=', $idproducto)
                ->where('idsucursal', '=', $idsucursal)
                ->where('stock', '>', 0)
                ->softDeletes()
                ->orderBy('idinventario_lote', 'ASC')
                ->orderBy('fecha_vencimiento', 'ASC')
                ->forUpdate()
                ->get();

        if (!$lotes) {
            throw new Exception(
                "El producto no tiene lotes disponibles."
            );
        }

        foreach ($lotes as $lote) {

            if ($pendiente <= 0) {
                break;
            }

            $stockLote = (float)$lote['stock'];

            /*
            * ¿Cuánto sacar de este lote?
            */
            $salida = min($pendiente, $stockLote);

            (new FluentSaver($this->pdo))
                ->table('detalle_venta_lote')
                ->data([
                    'iddetalle_venta' => $idDetalleVenta,
                    'idinventario_lote' => $lote['idinventario_lote'],
                    'codigo_lote' => $lote['codigo_lote'],
                    'fecha_vencimiento' => $lote['fecha_vencimiento'],
                    'cantidad' => $salida
                ])
                ->save();

            $nuevoStockLote = $stockLote - $salida;

            /*
            * Actualizar lote
            */
            $update = (new FluentSaver($this->pdo))
                ->table('inventario_lote')
                ->primaryKey('idinventario_lote')
                ->data([
                    'idinventario_lote' => $lote['idinventario_lote'],
                    'stock' => $nuevoStockLote,
                    'updated_at' => date('Y-m-d H:i:s')
                ])
                ->update();

            if (!$update) {
                throw new Exception(
                    "No se pudo actualizar el lote {$lote['codigo_lote']}."
                );
            }

            //Lo que todavía falta vender
            $pendiente -= $salida;
        }

        /*
        * Si todavía queda cantidad pendiente,
        * no hay suficiente stock por lotes.
        */
        if ($pendiente > 0) {

            throw new Exception(
                "Stock insuficiente en los lotes. " .
                "Faltan {$pendiente} unidades."
            );
        }
    }

}
