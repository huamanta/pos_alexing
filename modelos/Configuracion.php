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
                "valor_mora_credito" => 0,
                "is_refinanciamientos" => 0,
                "maximo_refinanciamientos" => 0,
                "is_notificacion" => 0,
                "dias_gracia" => 0,
                "interes_defecto" => 0,
                "is_descuento_anticipado" => 0,
                "valor_descuento_anticipado" => 0,
                "dias_anticipacion" => 0
            ];

        }

        return json_encode([
            "status" => true,
            "data" => [
                "configuracion" => $config
            ]
        ]);
    }


    public function actualizarConfiguracionMora($idsucursal, $is_mora_credito, $valor_mora_credito)
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
                    valor_mora_credito = '$valor_mora_credito'
                WHERE idsucursal = '$idsucursal'";

        } else {

            // Crear
            $sql = "INSERT INTO sucursal_configuracion (
                    idsucursal,
                    is_mora_credito,
                    valor_mora_credito
                ) VALUES (
                    '$idsucursal',
                    '$is_mora_credito',
                    '$valor_mora_credito'
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

    public function actualizarConfiguracionCreditos($idsucursal, $is_notificacion, $dias_gracia, $interes_defecto, $is_descuento_anticipado, $valor_descuento_anticipado, $dias_anticipacion)
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
                    is_notificacion = '$is_notificacion',
                    dias_gracia = '$dias_gracia',
                    interes_defecto = '$interes_defecto',
                    is_descuento_anticipado = '$is_descuento_anticipado',
                    valor_descuento_anticipado = '$valor_descuento_anticipado',
                    dias_anticipacion = '$dias_anticipacion'
                WHERE idsucursal = '$idsucursal'";

        } else {

            // Crear
            $sql = "INSERT INTO sucursal_configuracion (
                    idsucursal,
                    is_notificacion,
                    dias_gracia,
                    interes_defecto,
                    is_descuento_anticipado,
                    valor_descuento_anticipado,
                    dias_anticipacion
                ) VALUES (
                    '$idsucursal',
                    '$is_notificacion',
                    '$dias_gracia',
                    '$interes_defecto',
                    '$is_descuento_anticipado',
                    '$valor_descuento_anticipado',
                    '$dias_anticipacion'
                )";
        }

        if (!ejecutarConsulta($sql)) {
            return json_encode([
                'status' => false,
                'message' => 'No se pudo guardar la configuración de credito.'
            ]);
        }

        return json_encode([
            'status' => true,
            'message' => 'La configuración de credito se guardó correctamente.'
        ]);
    }


    public function actualizarConfiguracionRefinanciamiento($idsucursal, $is_refinanciamiento, $maximo_refinanciamientos)
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
                    is_refinanciamiento = '$is_refinanciamiento',
                    maximo_refinanciamientos = '$maximo_refinanciamientos'
                WHERE idsucursal = '$idsucursal'";

        } else {

            // Crear
            $sql = "INSERT INTO sucursal_configuracion (
                    idsucursal,
                    is_refinanciamiento,
                    maximo_refinanciamientos
                ) VALUES (
                    '$idsucursal',
                    '$is_refinanciamiento',
                    '$maximo_refinanciamientos'
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