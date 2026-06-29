<?php
require "../configuraciones/Conexion.php";

class Configuracion
{
    public function listarConfiguracion($idsucursal)
    {
        if (empty($idsucursal)) {
            return json_encode([
                "status" => false,
                "message" => "No se ha recibido la sucursal."
            ]);
        }

        $sql = "SELECT
                *
            FROM sucursal_configuracion
            WHERE idsucursal = '$idsucursal'
            LIMIT 1";

        $config = ejecutarConsultaSimpleFila($sql);

        if (!$config) {

            $config = [
                "idsucursal" => $idsucursal,
                "is_mora_credito" => 0,
                "valor" => 0,
                "maximo_refinanciamientos" => 0,
                "is_notificacion" => 0,
                "dias_gracia" => 0,
                "interes_defecto" => 0
            ];

        }

        return json_encode([
            "status" => true,
            "data" => [
                "configuracion" => $config
            ]
        ]);
    }


    public function actualizarConfiguracionMora($idsucursal, $is_mora_credito, $valor_mora)
    {
        if (empty($idsucursal)) {
            return json_encode([
                'status' => false,
                'message' => 'No se ha recibido la sucursal'
            ]);
        }

        // Verificar si ya existe configuración
        $sql = "SELECT idsucursal_configuracion FROM sucursal_configuracion
            WHERE idsucursal = '$idsucursal'
            LIMIT 1";

        $config = ejecutarConsultaSimpleFila($sql);

        if ($config) {

            // Actualizar
            $sql = "UPDATE sucursal_configuracion
                SET
                    is_mora_credito = '$is_mora_credito',
                    valor = '$valor_mora'
                WHERE idsucursal = '$idsucursal'";

        } else {

            // Crear
            $sql = "INSERT INTO sucursal_configuracion (
                    idsucursal,
                    is_mora_credito,
                    valor
                ) VALUES (
                    '$idsucursal',
                    '$is_mora_credito',
                    '$valor_mora'
                )";
        }

        if (!ejecutarConsulta($sql)) {
            return json_encode([
                'status' => false,
                'message' => 'No se pudo guardar la configuración de mora.'
            ]);
        }

        return json_encode([
            'status' => true,
            'message' => 'La configuración de mora se guardó correctamente.'
        ]);
    }
}