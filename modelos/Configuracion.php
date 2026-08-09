<?php
require_once __DIR__ . "/Helpers.php";
header('Content-Type: application/json; charset=utf-8');

class Configuracion extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listarConfiguracion(int $idsucursal): string
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

            return json_encode([
                "status" => true,
                "data" => [
                    "configuracion" => $config,
                    "sucursal" => $sucursal,
                ]
            ]);

        } catch (Throwable $e) {

            return json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);

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

            return json_encode([
                'status' => true,
                'message' => 'Configuración actualizada correctamente.'
            ]);

        } catch (Throwable $e) {

            return json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }

    public function actualizarConfiguracionMora(
        int $idsucursal,
        int $is_mora_credito,
        float $valor_mora_credito,
        int $dias_gracia
    ): string {

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

            return json_encode([
                'status' => true,
                'message' => 'La configuración de mora se guardó correctamente.'
            ]);

        } catch (Throwable $e) {

            return json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);

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
    ): string {

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

            return json_encode([
                'status' => true,
                'message' => 'La configuración de crédito se guardó correctamente.'
            ]);

        } catch (Throwable $e) {

            return json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }


    public function actualizarConfiguracionRefinanciamiento(
        int $idsucursal,
        int $is_refinanciamiento,
        int $maximo_refinanciamientos
    ): string {

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

            return json_encode([
                'status' => true,
                'message' => 'La configuración de refinanciamiento se guardó correctamente.'
            ]);

        } catch (Throwable $e) {

            return json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);

        }
    }
}