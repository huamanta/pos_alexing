<?php
require_once __DIR__ . "/../Helpers.php";
class SisVenta extends Helpers
{

    public function __construct()
    {
        parent::__construct();
    }

    public function insertar(
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
        if (empty($_SESSION['idpersonal'])) {
            throw new Exception('Sesión no válida.');
        }


        $this->pdo->beginTransaction();

        try {
            $idcliente = Helpers::clienteDefault($idcliente);
            //=========================
            // Datos por defecto
            //=========================
            $fechaActual = date('Y-m-d H:i:s');
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

            //=========================
            // Correlativo
            //=========================
            $config = Helpers::actualizarCorrelativo($idtipo_comprobante, $idsucursal);

            //=========================
            // Forma de pago real
            //=========================

            $formapagoVenta = $formapago;

            if (!empty($_POST['metodo_pago'])) {

                $metodos = array_filter($_POST['metodo_pago']);

                if (count($metodos) > 1) {

                    $formapagoVenta = "Mixto";

                } elseif (count($metodos) == 1) {

                    $formapagoVenta = reset($metodos);
                }
            }

            //=========================
            // Cabecera
            //=========================
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

            //=========================
            // Pagos Mixtos
            //=========================
            $this->guardarPagos($idVenta);

            //=========================
            // Detalles
            //=========================
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
            //=========================
            if ($tipopago == "Si") {
                $this->crearCredito(
                    $idVenta,
                    $fechaActual,
                    $montoDeuda,
                    $interes,
                    $input_cuotas,
                    $fecha_pago
                );
            }

            //=========================
            // Cotización
            //=========================
            if (!empty($comprobanteReferencia) && $tipo == "venta") {
                $this->actualizarCotizacion($comprobanteReferencia);
            }

            //=========================
            // Documentación
            //=========================
            if ($tipopago == "Si") {
                $this->crearDocumentacion(
                    $idVenta
                );
            }

            $this->pdo->commit();

            return json_encode([
                'success' => true,
                'id_venta' => $idVenta,
                'enviar_sunat' => Helpers::verificarEnvioSunat($idsucursal),
                'message' => 'Cotizacion registrado correctamente.'
            ]);

        } catch (Throwable $e) {
            $this->pdo->rollBack();
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
            $resultado = (new FluentSaver($this->pdo))
                ->table('documentacion')
                ->data([
                    'fecha_contrato' => date('Y-m-d H:i:s'),
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


    // private function generarNumeroComprobante(
    //     int $idsucursal,
    //     string $tipoComprobante
    // ): array {

    //     $row = (new DBQuery($this->pdo))

    //         ->query("
    //         SELECT
    //             serie_comprobante,
    //             num_comprobante
    //         FROM venta
    //         WHERE idsucursal = :idsucursal
    //         AND tipo_comprobante = :tipo
    //         ORDER BY idventa DESC
    //         LIMIT 1
    //         FOR UPDATE
    //     ", [
    //             'idsucursal' => $idsucursal,
    //             'tipo' => $tipoComprobante
    //         ])

    //         ->first();

    //     if (!$row) {

    //         return [
    //             'serie' => '001',
    //             'numero' => '0000001'
    //         ];
    //     }

    //     return [

    //         'serie' => $row['serie_comprobante'],

    //         'numero' => str_pad(
    //             ((int) $row['num_comprobante']) + 1,
    //             7,
    //             '0',
    //             STR_PAD_LEFT
    //         )

    //     ];
    // }


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


        /*
         * Interés total
         */
        $interesTotal = round(
            $montoDeuda * ($interes / 100),
            2
        );


        /*
         * Capital e interés por cuota
         */
        $capitalCuotaBase = round(
            $montoDeuda / $cantidadCuotas,
            2
        );


        $interesCuotaBase = round(
            $interesTotal / $cantidadCuotas,
            2
        );


        $capitalAcumulado = 0;
        $interesAcumulado = 0;



        foreach ($fechasPago as $index => $fechaVencimiento) {


            /*
             * Valores base
             */
            $capitalCuota = $capitalCuotaBase;
            $interesCuota = $interesCuotaBase;



            /*
             * Ajuste última cuota
             */
            if ($index == ($cantidadCuotas - 1)) {


                $capitalCuota = round(
                    $montoDeuda - $capitalAcumulado,
                    2
                );


                $interesCuota = round(
                    $interesTotal - $interesAcumulado,
                    2
                );
            }



            $totalCuota = round(
                $capitalCuota + $interesCuota,
                2
            );



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
                return false;
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
                'banco' => $banco,
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
            throw new Exception(
                "No se pudo registrar la venta"
            );

        }

        return (int) $venta;

    }

    private function guardarPagos(int $idVenta): void
    {
        // Si no viene pagos mixtos no hacemos nada
        if (
            empty($_POST['metodo_pago']) ||
            !is_array($_POST['metodo_pago'])
        ) {
            return;
        }

        $metodos = $_POST['metodo_pago'];
        $montos = $_POST['monto_real_pago'] ?? [];
        $operaciones = $_POST['nroOperacion_pago'] ?? [];
        $bancos = $_POST['banco_pago'] ?? [];
        $fechas = $_POST['fecha_deposito_pago'] ?? [];

        foreach ($metodos as $i => $metodo) {
            if (
                empty($metodo) ||
                !isset($montos[$i])
            ) {
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
                    'nroOperacion' =>
                        !empty($operaciones[$i])
                        ? $operaciones[$i]
                        : null,
                    'banco' =>
                        !empty($bancos[$i])
                        ? $bancos[$i]
                        : null,
                    'fechaDeposito' =>
                        !empty($fechas[$i])
                        ? $fechas[$i]
                        : null
                ])
                ->save();
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
            $idProducto = (int) ($idproducto[$i] ?? 0);
            $idSerie = (int) ($idserie[$i] ?? null);
            $cant = floatval(
                $cantidad[$i] ?? 0
            );
            $precio = floatval(
                $precio_venta[$i] ?? 0
            );
            $desc = floatval(
                $descuento[$i] ?? 0
            );
            $factor = floatval(
                $cantidad_contenedor[$i] ?? 1
            );
            if ($factor <= 0) {
                $factor = 1;
            }

            /*
            |--------------------------------------------------------------------------
            | Buscar configuración stock
            |--------------------------------------------------------------------------
            */
            $producto = (new DBQuery($this->pdo))
                ->from('producto p')
                ->select([
                    'p.controla_stock',
                    'p.nombre',
                    'p.idcategoria',
                    'p.tipo_producto',
                ])
                ->where(
                    'p.idproducto',
                    '=',
                    $idProducto
                )
                ->first();

            if (!$producto) {
                throw new Exception(
                    "Producto no existe"
                );
            }
            /*
            |--------------------------------------------------------------------------
            | Motos con serie
            |--------------------------------------------------------------------------
            */

            $serie = (new DBQuery($this->pdo))
                ->from('producto_serie')
                ->where(
                    'idserie',
                    '=',
                    $idSerie
                )
                ->where(
                    'idsucursal',
                    '=',
                    $idsucursal
                )
                ->first();

            if ($serie['estado'] != 'DISPONIBLE') {
                throw new Exception(
                    "El producto seleccionado ya no se ecnuentra disponible"
                );
            }


            if ($serie && ($producto['tipo_producto'] == 'Vehiculo')) {
                (new DBQuery($this->pdo))

                    ->query("
                        UPDATE producto_serie
                        SET estado='VENDIDO',
                            updated_at=NOW()
                        WHERE idserie=:id
                    ", [
                        'id' => $idSerie
                    ])
                    ->get();

            }

            /*
            |--------------------------------------------------------------------------
            | Detalle venta
            |--------------------------------------------------------------------------
            */
            $this->insertarDetalleVenta(
                $idsucursal,
                $idVenta,
                $idProducto,
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
        $rowProduct,
        $idsucursal,
        $idserie,
        $cantidad,
        $motivo = ''
    ) {
        // 1. Cambiar estado de la serie
        // $updateSerie = (new FluentSaver($this->pdo))
        //     ->table('producto_serie')
        //     ->primaryKey('idserie')
        //     ->data([
        //         'idserie' => $idserie,
        //         'estado' => 'VENDIDO'
        //     ])
        //     ->update();

        // if (!$updateSerie) {
        //     throw new Exception("La serie de producto no ha sido actualizado");
        // }
        // 2. Obtener inventario de la sucursal
        $sql = "SELECT *
            FROM inventario_producto
            WHERE idproducto = :idproducto
              AND idsucursal = :idsucursal
            FOR UPDATE";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'idproducto' => $rowProduct['idproducto'],
            'idsucursal' => $idsucursal
        ]);

        $inventario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$inventario) {
            throw new Exception("No existe inventario del producto.");
        }

        if ($inventario['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente.");
        }

        // 3. Actualizar stock
        $updateInventario = (new FluentSaver($this->pdo))
            ->table('inventario_producto')
            ->primaryKey('idinventario')
            ->data([
                'idinventario' => $inventario['idinventario'],
                'stock' => $inventario['stock'] - $cantidad
            ])
            ->update();

        if (!$updateInventario) {
            throw new Exception("La serie de producto no ha sido actualizado");
        }
        // Actualizar kardex si es necesario
        $config = $this->pdo->prepare("
            SELECT *
            FROM producto_configuracion
            WHERE idproducto = :idproducto
        ");
        $config->execute([
            'idproducto' => $rowProduct['idproducto']
        ]);

        $rowConfiguracion = $config->fetch(PDO::FETCH_ASSOC);
        $salida = 0;
        if ($rowProduct['controla_stock'] === 'Si') {
            Helpers::updateKardexSucursal(
                $idsucursal,
                $rowProduct['idproducto'],
                $rowConfiguracion['idproducto_configuracion'],
                $cantidad,
                $cantidad * $rowConfiguracion['cantidad_contenedor'],
                $rowProduct['precio'],
                0,
                $salida,
                'Salida por transferencia',
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
        $detalleVenta = (new FluentSaver($this->pdo))
            ->table('detalle_venta')
            ->data([
                'idsucursal' => $idsucursal,
                'idventa' => $idventa,
                'idproducto' => $idProducto,
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

        if (!$detalleVenta) {
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
            $rowProduct,
            $idsucursal,
            $idSerie,
            $cantidad,
            $motivo
        );

    }


    private function procesarFIFO(
        int $idventa,
        int $idsucursal,
        int $idproducto,
        array $datosDetalle,
        float $cantidadSolicitada,
        float $precio
    ): bool {

        $cantidadPendiente = $cantidadSolicitada;

        /*
         * Buscar lotes FIFO disponibles
         */
        $lotes = (new DBQuery($this->pdo))
            ->query("
            SELECT 
                idfifo,
                cantidad_restante
            FROM stock_fifo
            WHERE idproducto = :idproducto
            AND idsucursal = :idsucursal
            AND cantidad_restante > 0
            AND estado = 1
            ORDER BY fecha_ingreso ASC
        ", [
                'idproducto' => $idproducto,
                'idsucursal' => $idsucursal
            ])
            ->get();


        if (empty($lotes)) {
            return false;
        }


        $totalDescontado = 0;


        foreach ($lotes as $lote) {


            if ($cantidadPendiente <= 0) {
                break;
            }


            $disponible = floatval($lote['cantidad_restante']);

            $cantidadTomar = min(
                $cantidadPendiente,
                $disponible
            );


            /*
             * Actualizar FIFO
             */
            $stmt = $this->pdo->prepare("
            UPDATE stock_fifo
            SET cantidad_restante = cantidad_restante - :cantidad
            WHERE idfifo = :idfifo
        ");

            $stmt->execute([
                'cantidad' => $cantidadTomar,
                'idfifo' => $lote['idfifo']
            ]);



            /*
             * Insertar detalle venta
             */

            $detalle = $datosDetalle;

            $detalle['id_fifo'] = $lote['idfifo'];

            $detalle['cantidad'] = $cantidadTomar;


            (new FluentSaver($this->pdo))
                ->table('detalle_venta')
                ->data($detalle)
                ->save();



            $cantidadPendiente -= $cantidadTomar;

            $totalDescontado += $cantidadTomar;
        }



        /*
         * No hubo suficiente stock
         */
        if ($cantidadPendiente > 0) {
            return false;
        }



        /*
         * Actualizar inventario_producto
         * (nuevo esquema)
         */

        $stmt = $this->pdo->prepare("
        UPDATE inventario_producto
        SET stock = stock - :cantidad
        WHERE idproducto = :idproducto
        AND idsucursal = :idsucursal
    ");


        $stmt->execute([
            'cantidad' => $totalDescontado,
            'idproducto' => $idproducto,
            'idsucursal' => $idsucursal
        ]);




        /*
         * Obtener stock actual
         */

        $inventario = (new DBQuery($this->pdo))
            ->query("
            SELECT stock
            FROM inventario_producto
            WHERE idproducto = :idproducto
            AND idsucursal = :idsucursal
        ", [
                'idproducto' => $idproducto,
                'idsucursal' => $idsucursal
            ])
            ->first();



        /*
         * Kardex
         */

        (new FluentSaver($this->pdo))
            ->table('kardex')
            ->data([
                'idsucursal' => $idsucursal,
                'idproducto' => $idproducto,
                'cantidad' => $totalDescontado,
                'cantidad_contenedor' => $datosDetalle['cantidad_contenedor'],
                'precio_unitario' => $precio,
                'stock_actual' => $inventario['stock'] ?? 0,
                'tipo_movimiento' => 1,
                'motivo' => 'Venta',
                'descripcion' => "Venta #" . $idventa,
                'fecha_kardex' => date('Y-m-d H:i:s')
            ])
            ->save();



        return true;
    }

    private function registrarKardex(
        int $idsucursal,
        int $idproducto,
        float $cantidad,
        float $cantidad_contenedor,
        float $precio,
        string $motivo,
        string $descripcion
    ): bool {

        // Obtener stock actual después del movimiento
        $inventario = (new DBQuery($this->pdo))
            ->query("
            SELECT stock
            FROM inventario_producto
            WHERE idproducto = :idproducto
            AND idsucursal = :idsucursal
            LIMIT 1
        ", [
                'idproducto' => $idproducto,
                'idsucursal' => $idsucursal
            ])
            ->first();


        if (!$inventario) {
            return false;
        }


        $resultado = (new FluentSaver($this->pdo))
            ->table('kardex')
            ->data([
                'idsucursal' => $idsucursal,
                'idproducto' => $idproducto,
                'cantidad' => $cantidad,
                'cantidad_contenedor' => $cantidad_contenedor,
                'precio_unitario' => $precio,
                'stock_actual' => $inventario['stock'],
                'tipo_movimiento' => 1, // 1 = salida venta
                'motivo' => $motivo,
                'descripcion' => $descripcion,
                'fecha_kardex' => date('Y-m-d H:i:s')
            ])
            ->save();


        return $resultado !== false;
    }
}
