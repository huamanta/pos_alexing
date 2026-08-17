<?php
require_once __DIR__ . "/../Helpers.php";
use Carbon\Carbon;

class SisCompra extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertar(
        $idsucursal,
        $idproveedor,
        $idpersonal,
        $tipo_c,
        $tipo_comprobante,
        $serie_comprobante,
        $num_comprobante,
        $fecha_hora,
        $impuesto,
        $tipo_igv,
        $monto_gravado,
        $monto_exonerado,
        $monto_igv,
        $total_compra,
        $formapago,
        $lugar_entrega,
        $motivo_compra,
        $documento,
        $nota,
        $comprobanteReferencia,
        $idproducto,
        $nombre_producto,
        $cantidad,
        $precio_compra,
        $precio_venta,
        $nlote,
        $fvencimiento,
        $tipopago,
        $fechaOperacion,
        $input_cuotas,
        $montoPagado,
        $montoDeuda,
        $fecha_pago,
        $totaldeposito,
        $noperacion,
        $totalrecibido,
        $fecha_deposito,
        $tipo_pago,
        $monto_pago,
        $operacion_pago
    ) {

        try {

            $this->pdo->beginTransaction();

            $estadoCompra = $this->obtenerEstadoCompra(
                $tipo_c
            );

            $this->actualizarComprobanteReferencia(
                $comprobanteReferencia
            );

            $productos = $this->obtenerProductos(
                $idproducto,
                $nombre_producto,
                $cantidad,
                $precio_compra,
                $precio_venta,
                $nlote,
                $fvencimiento
            );

            $pagos = $this->validarPagos(
                $tipo_pago,
                $monto_pago,
                $operacion_pago,
                $montoPagado,
                $total_compra
            );

            $idcompra = $this->guardarCabecera(
                $idsucursal,
                $idproveedor,
                $idpersonal,
                $tipo_c,
                $tipo_comprobante,
                $serie_comprobante,
                $num_comprobante,
                $fecha_hora,
                $impuesto,
                $tipo_igv,
                $monto_gravado,
                $monto_exonerado,
                $monto_igv,
                $total_compra,
                $formapago,
                $lugar_entrega,
                $motivo_compra,
                $documento,
                $nota,
                $estadoCompra,
                $comprobanteReferencia,
                $tipopago,
                $montoPagado,
                $totaldeposito,
                $noperacion,
                $totalrecibido,
                $fecha_deposito
            );

            $this->guardarDetalleCompra(
                $idcompra,
                $idsucursal,
                $tipo_c,
                $productos
            );

            $this->guardarCuentasPorPagar(
                $idcompra,
                $tipopago,
                $fecha_hora,
                $montoDeuda,
                $fecha_pago
            );

            $this->guardarPagos(
                $idcompra,
                $pagos
            );

            $this->pdo->commit();

            return json_encode([
                'success' => true,
                'id_venta' => $idcompra,
                'message' => 'Compra guardda correctamente.'
            ]);

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);

        }

    }

    private function obtenerEstadoCompra($tipo_c): string
    {
        return ($tipo_c == 'Orden Compra')
            ? 'POR APROBACIÓN'
            : 'REGISTRADO';
    }

    private function actualizarComprobanteReferencia($comprobanteReferencia): void
    {
        if (empty($comprobanteReferencia)) {
            return;
        }

        $ok = ejecutarConsulta("
        UPDATE compra
        SET estadoC='COMPRADO'
        WHERE idcompra='$comprobanteReferencia'
    ");

        if (!$ok) {
            throw new Exception(
                "Error al actualizar la orden de compra de referencia."
            );
        }
    }

    private function obtenerProductos(
        $idproducto,
        $nombre_producto,
        $cantidad,
        $precio_compra,
        $precio_venta,
        $nlote,
        $fvencimiento
    ): array {

        if (
            !is_array($idproducto)
            || count($idproducto) == 0
        ) {
            throw new Exception(
                "Debe agregar al menos un producto."
            );
        }

        $productos = [];

        foreach ($idproducto as $i => $id) {

            if ( empty($id) || empty($cantidad[$i]) || $cantidad[$i] <= 0) {
                continue;
            }

            $productos[] = [
                'idproducto' => $id,
                'nombre_producto' => $nombre_producto[$i] ?? '',
                'cantidad' => (float) $cantidad[$i],
                'precio_compra' => (float) $precio_compra[$i],
                'precio_venta' => (float) $precio_venta[$i],
                'nlote' => $nlote[$i] ?? '',
                'fvencimiento' => $fvencimiento[$i] ?? null

            ];

        }

        if (count($productos) == 0) {

            throw new Exception(
                "Debe agregar al menos un producto válido."
            );

        }

        return $productos;
    }

    private function validarPagos(
        $tipo_pago,
        $monto_pago,
        $operacion_pago,
        $montoPagado,
        $total_compra
    ): array {

        $pagos = [];

        if (is_array($tipo_pago) && is_array($monto_pago)) {

            foreach ($tipo_pago as $i => $tipo) {

                if (
                    empty($tipo)
                    || !isset($monto_pago[$i])
                ) {
                    continue;
                }

                $monto = is_numeric($monto_pago[$i])
                    ? floatval($monto_pago[$i])
                    : 0;

                if ($monto <= 0) {
                    continue;
                }

                $pagos[] = [

                    'tipo' => trim($tipo),

                    'monto' => $monto,

                    'operacion' => !empty($operacion_pago[$i])
                        ? trim($operacion_pago[$i])
                        : null

                ];
            }
        }

        $totalPagos = array_sum(
            array_column(
                $pagos,
                'monto'
            )
        );

        if ($totalPagos > floatval($total_compra)) {

            throw new Exception(
                "La suma de los pagos excede el total de la compra."
            );

        }

        if (
            !empty($montoPagado)
            &&
            floatval($montoPagado) > 0
        ) {

            $dif = abs(
                $totalPagos -
                floatval($montoPagado)
            );

            if ($dif > 0.01) {

                throw new Exception(
                    "El monto total pagado no coincide con la suma de los pagos individuales."
                );

            }

        }

        return $pagos;
    }

    private function guardarCabecera(
        $idsucursal,
        $idproveedor,
        $idpersonal,
        $tipo_c,
        $tipo_comprobante,
        $serie_comprobante,
        $num_comprobante,
        $fecha_hora,
        $impuesto,
        $tipo_igv,
        $monto_gravado,
        $monto_exonerado,
        $monto_igv,
        $total_compra,
        $formapago,
        $lugar_entrega,
        $motivo_compra,
        $documento,
        $nota,
        $estadoCompra,
        $comprobanteReferencia,
        $tipopago,
        $montoPagado,
        $totaldeposito,
        $noperacion,
        $totalrecibido,
        $fecha_deposito
    ) {

        $fechaActual = date('Y-m-d H:i:s');
        return (new FluentSaver($this->pdo))
            ->table("compra")
            ->nullable([
                'fecha_deposito'
            ])
            ->data([
                "idsucursal" => $idsucursal,
                "idproveedor" => $idproveedor,
                "idpersonal" => $idpersonal,
                "tipo_c" => $tipo_c,
                "tipo_comprobante" => $tipo_comprobante,
                "serie_comprobante" => $serie_comprobante,
                "num_comprobante" => $num_comprobante,
                "fecha_hora" => $fecha_hora,
                "impuesto" => $impuesto,
                "tipo_igv" => $tipo_igv,
                "monto_gravado" => $monto_gravado,
                "monto_exonerado" => $monto_exonerado,
                "monto_igv" => $monto_igv,
                "total_compra" => $total_compra,
                "compracredito" => $tipopago,
                "motoPagado" => $montoPagado,
                "formapago" => $formapago,
                "lugar_entrega" => $lugar_entrega,
                "motivo_compra" => $motivo_compra,
                "documento" => $documento,
                "nota" => $nota,
                "estado" => "REGISTRADO",
                "estadoC" => $estadoCompra,
                "documento_rel" => $comprobanteReferencia,
                "totaldeposito" => $totaldeposito,
                "noperacion" => $noperacion,
                "totalrecibido" => $totalrecibido,
                "fecha_deposito" => $fecha_deposito,
                "fecha_kardex" => $fechaActual
            ])

            ->save();

    }

    private function guardarDetalleCompra(
        $idcompra,
        $idsucursal,
        $tipo_c,
        array $productos
    ) {

        $fechaActual = Carbon::now();

        foreach ($productos as $producto) {

            self::guardarDetalle(
                $idcompra,
                $idsucursal,
                $tipo_c,
                $producto
            );

            $nuevoStock = $this->actualizarStock(
                $idsucursal,
                $producto
            );

            $this->registrarKardex(
                $idsucursal,
                $producto,
                $nuevoStock,
                $fechaActual
            );

            $this->actualizarProducto(
                $idsucursal,
                $producto
            );

            $this->actualizarConfiguracionProducto(
                $producto
            );

        }

    }

    /*private function guardarDetalle(
        $idcompra,
        $idsucursal,
        $tipo_c,
        array $producto
    ) {
        $detalle = (new FluentSaver($this->pdo))
            ->table("detalle_compra")
            ->nullable([
                'fvencimiento'
            ])
            ->data([
                "idsucursal" => $idsucursal,
                "idcompra" => $idcompra,
                "idproducto" => $producto["idproducto"],
                "nombre_producto" => $producto["nombre_producto"],
                "cantidad" => $producto["cantidad"],
                "precio_compra" => $producto["precio_compra"],
                "precio_venta" => $producto["precio_venta"],
                "nlote" => $producto["nlote"],
                "fvencimiento" => $producto["fvencimiento"],
                "tipo_c" => $tipo_c,
                "stock_lote" => $producto["cantidad"]
            ])
            ->save();

        if($producto["nlote"]){

        }
    }*/

    private function guardarDetalle(
        $idcompra,
        $idsucursal,
        $tipo_c,
        array $producto
    ) {
        $flag = true;
        (new FluentSaver($this->pdo))
            ->table("detalle_compra")
            ->nullable([
                'fvencimiento'
            ])
            ->data([
                "idsucursal" => $idsucursal,
                "idcompra" => $idcompra,
                "idproducto" => $producto["idproducto"],
                "nombre_producto" => $producto["nombre_producto"],
                "cantidad" => $producto["cantidad"],
                "precio_compra" => $producto["precio_compra"],
                "precio_venta" => $producto["precio_venta"],
                "nlote" => $producto["nlote"],
                "fvencimiento" => $producto["fvencimiento"],
                "tipo_c" => $tipo_c,
                "stock_lote" => $producto["cantidad"]
            ])
            ->save();

        // Si la compra tiene lote
        if (!empty($producto["nlote"])) {
            // Buscar si ya existe el lote
            $lote = (new DBQuery($this->pdo))
                ->from("inventario_lote")
                ->where("idproducto", "=", $producto["idproducto"])
                ->where("idsucursal", "=", $idsucursal)
                ->where("codigo_lote", "=", $producto["nlote"])
                ->where("fecha_vencimiento", "=", $producto["fvencimiento"])
                ->first();

            if ($lote) {
                // Ya existe → acumular stock
                (new FluentSaver($this->pdo))
                    ->table("inventario_lote")
                    ->where("idinventario_lote", "=", $lote["idinventario_lote"])
                    ->data([
                        "stock" => $lote["stock"] + $producto["cantidad"],
                        "stock_original" => $lote["stock_original"] + $producto["cantidad"],
                        "updated_at" => date("Y-m-d H:i:s")
                    ])
                    ->update();

            } else {
                // No existe → crear lote
                (new FluentSaver($this->pdo))
                    ->table("inventario_lote")
                    ->data([
                        "idproducto" => $producto["idproducto"],
                        "idsucursal" => $idsucursal,
                        "codigo_lote" => $producto["nlote"],
                        "fecha_vencimiento" => $producto["fvencimiento"],
                        "stock" => $producto["cantidad"],
                        "stock_original" => $producto["cantidad"]
                    ])
                    ->save();
            }
        }
        return $flag;
    }

    private function actualizarStock(
        $idsucursal,
        array $producto
    ) {

        $inventario = (new DBQuery($this->pdo))
            ->from("inventario_producto")
            ->where(
                "idproducto",
                "=",
                $producto["idproducto"]
            )
            ->where(
                "idsucursal",
                "=",
                $idsucursal
            )
            ->first();


        if (!$inventario) {
            throw new Exception("No existe inventario para actualizar");
        }


        $nuevoStock = $inventario["stock"] + $producto["cantidad"];


        (new FluentSaver($this->pdo))
            ->table("inventario_producto")
            ->where(
                "idinventario",
                "=",
                $inventario["idinventario"]
            )
            ->data([
                "stock" => $nuevoStock
            ])
            ->update();

        return $nuevoStock;
    }

    private function registrarKardex(
        $idsucursal,
        array $producto,
        $stockActual,
        $fecha
    ) {
        (new FluentSaver($this->pdo))
            ->table("kardex")
            ->data([
                "idsucursal" => $idsucursal,
                "idproducto" => $producto["idproducto"],
                "cantidad" => $producto["cantidad"],
                "precio_unitario" => $producto["precio_compra"],
                "stock_actual" => $stockActual,
                "tipo_movimiento" => 1,
                "motivo" => "Compra",
                "fecha_kardex" => $fecha


            ])
            ->save();

    }

    private function actualizarProducto(
        $idsucursal,
        array $producto
    ) {

        (new FluentSaver($this->pdo))
            ->table("producto")
            ->where(
                "idproducto",
                "=",
                $producto["idproducto"]
            )
            ->where(
                "idsucursal",
                "=",
                $idsucursal
            )
            ->data([
                // "precio_compra" => $producto["precio_compra"],
                "precio" => $producto["precio_venta"]
            ])
            ->update();

    }

    private function actualizarConfiguracionProducto(
        array $producto
    ) {

        (new FluentSaver($this->pdo))
            ->table("producto_configuracion")
            ->where("idproducto", "=", $producto["idproducto"])
            ->where("cantidad_contenedor", "=", 1.00)
            ->data([
                "precio_venta" => $producto["precio_venta"]
            ])
            ->update();
    }

    private function registrarFIFO(
        $iddetalle,
        $idsucursal,
        array $producto,
        $fecha
    ) {

        (new FluentSaver($this->pdo))
            ->table("stock_fifo")
            ->data([
                "idsucursal" => $idsucursal,
                "idproducto" => $producto["idproducto"],
                "origen" => "COMPRA",
                "referencia_id" => $iddetalle,
                "cantidad_ingreso" => $producto["cantidad"],
                "cantidad_restante" => $producto["cantidad"],
                "precio_compra" => $producto["precio_compra"],
                "precio_venta" => $producto["precio_venta"],
                "fecha_ingreso" => $fecha,
                "fvencimiento" => $producto["fvencimiento"]
            ])
            ->save();

    }

    private function guardarCuentasPorPagar(
        $idcompra,
        $tipopago,
        $fecha_hora,
        $montoDeuda,
        $fecha_pago
    ) {

        if (
            $tipopago != 'Si'
            || empty($fecha_pago)
            || !is_array($fecha_pago)
        ) {
            return;
        }

        $numCuotas = count($fecha_pago);

        if (
            $numCuotas == 0
            || floatval($montoDeuda) <= 0
        ) {
            return;
        }

        $montoCuota = round(
            $montoDeuda / $numCuotas,
            2
        );

        $sumaCuotas = 0;

        foreach ($fecha_pago as $i => $fechaVencimiento) {

            $montoActual = ($i == $numCuotas - 1)
                ? round(
                    $montoDeuda - $sumaCuotas,
                    2
                )
                : $montoCuota;

            (new FluentSaver($this->pdo))
                ->table("cuentas_por_pagar")
                ->data([
                    "idcompra" => $idcompra,
                    "fecharegistro" => $fecha_hora,
                    "deudatotal" => $montoActual,
                    "fechavencimiento" => $fechaVencimiento
                ])
                ->save();

            $sumaCuotas += $montoActual;

        }

    }

    private function guardarPagos(
        $idcompra,
        array $pagos
    ) {
        if (empty($pagos)) {
            return;
        }
        $fechaActual = date("Y-m-d H:i:s");
        foreach ($pagos as $pago) {
            (new FluentSaver($this->pdo))
                ->table("compra_pago")
                ->data([
                    "idcompra" => $idcompra,
                    "tipo_pago" => $pago["tipo"],
                    "monto" => $pago["monto"],
                    "nro_operacion" => $pago["operacion"],
                    "fecha_pago" => $fechaActual
                ])
                ->save();

        }

    }

}