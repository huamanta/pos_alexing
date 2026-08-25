<?php
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";

class Configuracion extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listarConfiguracion(int $idsucursal)
    {
        try {

            if ($idsucursal <= 0) {
                throw new Exception("No se ha recibido la sucursal.");
            }

            # configuracion
            $config = (new DBQuery($this->pdo))
                ->from('sucursal_configuracion')
                ->where('idsucursal', '=', $idsucursal)
                ->first();

            if (!$config) {
                $config = [
                    "idsucursal" => $idsucursal,
                    "is_mora_credito" => 0,
                    "valor_mora_credito" => 0,
                    "is_refinanciamiento" => 0,
                    "maximo_refinanciamientos" => 0,
                    "is_notificacion" => 0,
                    "dias_gracia" => 0,
                    "interes_defecto" => 0,
                    "is_descuento_anticipado" => 0,
                    "valor_descuento_anticipado" => 0,
                    "dias_anticipacion" => 0,
                    "is_send_sunat" => 0
                ];
            }

            #sucursal
            $sucursal = (new DBQuery($this->pdo))
                ->from('sucursal')
                ->where('idsucursal', '=', $idsucursal)
                ->first();

            if (!empty($sucursal['logo'])) {
                $sucursal['logo_url'] = 'files/logos/' . $sucursal['logo'];
            } else {
                $sucursal['logo_url'] = null;
            }

            #facturacion
            $facturacion = (new DBQuery($this->pdo))
                ->select('*')
                ->from('empresas')
                ->where('idempresa', '=', $sucursal['idempresa'])
                ->first();

            return Response::json([
                "status" => true,
                "data" => [
                    "configuracion" => $config,
                    "sucursal" => $sucursal,
                    "facturacion" => $facturacion,
                ]
            ]);

        } catch (Throwable $e) {

            return Response::error($e->getMessage());

        }
    }


    public function actualizarConfiguracionGeneral(
        $idsucursal,
        $nombre,
        $telefono,
        $direccion,
        $email,
        $departamento,
        $provincia,
        $distrito,
        $ubigeo,
        $moneda
    ) {
        try {

            if (empty($idsucursal)) {
                throw new Exception("No se ha encontrado la sucursal.");
            }

            $data = [
                'idsucursal' => $idsucursal,
                'nombre' => $nombre,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'email' => $email,
                'departamento' => $departamento,
                'provincia' => $provincia,
                'distrito' => $distrito,
                'ubigeo' => $ubigeo,
                'moneda' => $moneda
            ];

            // Si se subió un logo
            $ruta = "../files/logos/";

            if (!is_dir($ruta)) {
                mkdir($ruta, 0777, true);
            }

            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {

                $anterior = (new DBQuery($this->pdo))
                    ->from('sucursal')
                    ->select(['logo'])
                    ->where('idsucursal', '=', $idsucursal)
                    ->first();

                if (!empty($anterior['logo'])) {
                    $archivo = $ruta . $anterior['logo'];

                    if (file_exists($archivo)) {
                        unlink($archivo);
                    }
                }

                $extension = strtolower(pathinfo(
                    $_FILES['logo']['name'],
                    PATHINFO_EXTENSION
                ));

                $permitidas = ['png', 'jpg', 'jpeg', 'webp'];

                if (!in_array($extension, $permitidas)) {
                    throw new Exception("Formato de imagen no permitido.");
                }

                $nombreLogo = "logo_" . $idsucursal . "." . $extension;

                if (
                    !move_uploaded_file(
                        $_FILES['logo']['tmp_name'],
                        $ruta . $nombreLogo
                    )
                ) {
                    throw new Exception("No se pudo guardar el logo.");
                }

                $data['logo'] = $nombreLogo;
            }

            $resultado = (new FluentSaver($this->pdo))
                ->table('sucursal')
                ->primaryKey('idsucursal')
                ->data($data)
                ->update();

            if (!$resultado) {
                throw new Exception("No se pudo actualizar la configuración.");
            }

            return Response::json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente.'
            ]);

        } catch (Throwable $e) {
            return Response::error($e->getMessage());
        }
    }

    public function actualizarConfiguracionMora(
        int $idsucursal,
        int $is_mora_credito,
        float $valor_mora_credito,
        int $dias_gracia
    ) {

        try {

            if ($idsucursal <= 0) {
                throw new Exception("No se ha recibido la sucursal.");
            }

            $config = Helpers::sucursalConfiguracion($idsucursal);

            if ($config) {

                $resultado = (new FluentSaver($this->pdo))
                    ->table('sucursal_configuracion')
                    ->primaryKey('idsucursal_configuracion')
                    ->data([
                        'idsucursal_configuracion' => $config['idsucursal_configuracion'],
                        'is_mora_credito' => $is_mora_credito,
                        'dias_gracia' => $dias_gracia,
                        'valor_mora_credito' => $valor_mora_credito
                    ])
                    ->update();

            } else {

                $resultado = (new FluentSaver($this->pdo))
                    ->table('sucursal_configuracion')
                    ->data([
                        'idsucursal' => $idsucursal,
                        'is_mora_credito' => $is_mora_credito,
                        'dias_gracia' => $dias_gracia,
                        'valor_mora_credito' => $valor_mora_credito
                    ])
                    ->save();

            }

            if (!$resultado) {
                throw new Exception("No se pudo guardar la configuración de mora.");
            }

            return Response::json([
                'success' => true,
                'message' => 'La configuración de mora se guardó correctamente.'
            ]);

        } catch (Throwable $e) {
            return Response::error($e->getMessage());

        }
    }

    public function actualizarConfiguracionCreditos(
        int $idsucursal,
        int $is_notificacion,
        int $is_calculo_mes,
        float $interes_defecto,
        int $is_descuento_anticipado,
        float $valor_descuento_anticipado,
        int $dias_anticipacion
    ) {

        try {

            if ($idsucursal <= 0) {
                throw new Exception("No se ha recibido la sucursal.");
            }

            $config = Helpers::sucursalConfiguracion($idsucursal);

            if ($config) {

                (new FluentSaver($this->pdo))
                    ->table('sucursal_configuracion')
                    ->primaryKey('idsucursal_configuracion')
                    ->data([
                        'idsucursal_configuracion' => $config['idsucursal_configuracion'],
                        'is_notificacion' => $is_notificacion,
                        'is_calculo_mes' => $is_calculo_mes,
                        'interes_defecto' => $interes_defecto,
                        'is_descuento_anticipado' => $is_descuento_anticipado,
                        'valor_descuento_anticipado' => $valor_descuento_anticipado,
                        'dias_anticipacion' => $dias_anticipacion
                    ])
                    ->update();

            } else {

                (new FluentSaver($this->pdo))
                    ->table('sucursal_configuracion')
                    ->data([
                        'idsucursal' => $idsucursal,
                        'is_notificacion' => $is_notificacion,
                        'is_calculo_mes' => $is_calculo_mes,
                        'interes_defecto' => $interes_defecto,
                        'is_descuento_anticipado' => $is_descuento_anticipado,
                        'valor_descuento_anticipado' => $valor_descuento_anticipado,
                        'dias_anticipacion' => $dias_anticipacion
                    ])
                    ->save();

            }

            return Response::json([
                'success' => true,
                'message' => 'La configuración de crédito se guardó correctamente.'
            ]);

        } catch (Throwable $e) {
            return Response::error($e->getMessage());
        }
    }


    public function actualizarConfiguracionRefinanciamiento(
        int $idsucursal,
        int $is_refinanciamiento,
        int $maximo_refinanciamientos
    ) {

        try {

            if ($idsucursal <= 0) {
                throw new Exception("No se ha recibido la sucursal.");
            }

            $config = Helpers::sucursalConfiguracion($idsucursal);

            if ($config) {

                (new FluentSaver($this->pdo))
                    ->table('sucursal_configuracion')
                    ->primaryKey('idsucursal_configuracion')
                    ->data([
                        'idsucursal_configuracion' => $config['idsucursal_configuracion'],
                        'is_refinanciamiento' => $is_refinanciamiento,
                        'maximo_refinanciamientos' => $maximo_refinanciamientos
                    ])
                    ->update();

            } else {

                (new FluentSaver($this->pdo))
                    ->table('sucursal_configuracion')
                    ->data([
                        'idsucursal' => $idsucursal,
                        'is_refinanciamiento' => $is_refinanciamiento,
                        'maximo_refinanciamientos' => $maximo_refinanciamientos
                    ])
                    ->save();

            }

            return Response::json([
                'success' => true,
                'message' => 'La configuración de refinanciamiento se guardó correctamente.'
            ]);

        } catch (Throwable $e) {
            return Response::error($e->getMessage());
        }
    }


    public function actualizarConfiguracionFacturacion(
        int $idsucursal,
        int $is_send_sunat,
        string $ruc,
        string $razon_social,
        string $monto_impuesto,
        string $usuario_sol,
        string $clave_sol,
        $ruta_certificado,
        string $clave_certificado,
        string $estado_certificado
    ) {
        try {
            $this->pdo->beginTransaction();
            if (!$idsucursal) {
                throw new Exception("No se ha recibido la sucursal.");
            }

            $config = Helpers::sucursalConfiguracion($idsucursal);

            if ($config) {
                (new FluentSaver($this->pdo))
                    ->table('sucursal_configuracion')
                    ->primaryKey('idsucursal_configuracion')
                    ->data([
                        'idsucursal_configuracion' => $config['idsucursal_configuracion'],
                        'is_send_sunat' => $is_send_sunat
                    ])
                    ->update();

            } else {
                (new FluentSaver($this->pdo))
                    ->table('sucursal_configuracion')
                    ->data([
                        'idsucursal' => $idsucursal,
                        'is_send_sunat' => $is_send_sunat
                    ])
                    ->save();
            }

            $idEmpresa = Helpers::getEmpresa($idsucursal);

            if (!$idEmpresa) {
                throw new Exception("La sucursal no tiene empresa asignada.");
            }
            $data = [
                'idempresa' => $idEmpresa,
                'ruc' => $ruc,
                'razon_social' => $razon_social,
                'usuario_sol' => $usuario_sol,
                'monto_impuesto' => $monto_impuesto,
                'ruta_certificado' => self::guardarCertificado($ruta_certificado, $idsucursal),
                'estado_certificado' => $estado_certificado,
            ];

            if (!empty($clave_sol)) {
                $data['clave_sol'] = $clave_sol;
            }

            if (!empty($clave_certificado)) {
                $data['clave_certificado'] = $clave_certificado;
            }
            (new FluentSaver($this->pdo))
                ->table('empresas')
                ->primaryKey('idempresa')
                ->data($data)
                ->update();

            $this->pdo->commit();

            return Response::json(['success' => true, 'message' => 'Se ha actualizado correctamente la facturacion']);
        } catch (\Throwable $th) {
            $this->pdo->rollBack();
            return Response::error($th->getMessage());
        }
    }


    public function guardarCertificado($archivo, $idsucursal)
    {
        if (!isset($archivo) || !isset($archivo['error']) || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al cargar el certificado.');
        }

        $extension = strtolower(
            pathinfo($archivo['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, ['p12', 'pfx'], true)) {
            throw new Exception('El certificado debe ser .p12 o .pfx.');
        }

        if ($archivo['size'] > 5 * 1024 * 1024) {
            throw new Exception('El certificado no puede superar los 5 MB.');
        }

        $directorio = __DIR__ . '/../public/FACT_WebService/Facturacion/src/';

        if (!is_dir($directorio) && !mkdir($directorio, 0755, true)) {
            throw new Exception('No se pudo crear el directorio de certificados.');
        }

        $nombre = 'certificado_' . $idsucursal . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

        $destino = $directorio . $nombre;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            throw new Exception('No se pudo guardar el certificado.');
        }

        return $nombre;

    }
}