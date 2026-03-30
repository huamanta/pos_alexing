<?php
class Helpers
{
    public static function getDataFrecuencia($frecuencia)
    {
        $frecuenciaTexto = "";
        $frecuenciaSm = "";

        switch ($frecuencia) {
            case '1':
                $frecuenciaTexto = "diarias";
                $frecuenciaSm = "dias";
                break;
            case '2':
                $frecuenciaTexto = "semanales";
                $frecuenciaSm = "semanas";
                break;
            case '3':
                $frecuenciaTexto = "quincenales";
                $frecuenciaSm = "quincenas";
                break;
            case '4':
                $frecuenciaTexto = "mensuales";
                $frecuenciaSm = "meses";
                break;
            case "5":
                $frecuenciaTexto = "bimestrales";
                $frecuenciaSm = "bimestres";
                break;
            case "6":
                $frecuenciaTexto = "trimestrales";
                $frecuenciaSm = "trimestres";
                break;
            case '7':
                $frecuenciaTexto = "semestrales";
                $frecuenciaSm = "semestres";
                break;
            case "8":
                $frecuenciaTexto = "anuales";
                $frecuenciaSm = "años";
                break;
            default:
                $frecuenciaTexto = "mensuales";
                $frecuenciaSm = "meses";
        }

        return (object) [
            "texto" => $frecuenciaTexto,
            "short" => $frecuenciaSm
        ];
    }

    public static function encryptDecrypt($action, $string)
    {
        if ($action == 'encrypt') {
            $output = base64_encode($string);
            $output = str_replace(['=', '/', '+'], ['', '_', '-'], $output);
            return $output;
        } else if ($action == 'decrypt') {
            $string = str_replace(['_', '-'], ['/', '+'], $string);
            $mod4 = strlen($string) % 4;
            if ($mod4) {
                $string .= substr('====', $mod4);
            }
            return base64_decode($string);
        }
        return false;
    }

    function numeroALetrasMoneda($numero, $langauge = 'es', $moneda = 'SOLES')
    {
        $formatter = new NumberFormatter($langauge, NumberFormatter::SPELLOUT);

        $partes = explode('.', number_format($numero, 2, '.', ''));

        $entero = (int)$partes[0];
        $decimal = $partes[1];

        $letras = strtoupper($formatter->format($entero));

        return "$letras CON $decimal/100 $moneda";
    }


    function fechaLetras($fecha)
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
            4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $timestamp = strtotime($fecha);

        $dia = date('d', $timestamp);
        $mes = $meses[(int)date('m', $timestamp)];
        $anio = date('Y', $timestamp);

        return "$dia de $mes del $anio";
    }
}