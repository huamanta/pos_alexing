<?php
class HelpersService
{
    public static function getDocumentHeaderStyles()
    {
        return '
        .header {
            text-align: center;
        }

        .empresa {
            color: #5b8db8;
            font-weight: bold;
            font-size: 16px;
        }

        .subempresa {
            color: #7a7a7a;
            font-weight: bold;
            font-size: 15px;
        }

        .ruc {
            color: #7a7a7a;
            font-size: 13px;
        }

        .line {
            border-top: 1px solid #999;
            margin: 10px 0;
        }

        .titulo {
            font-weight: bold;
            text-align: center;
            font-size: 13px;
        }

        .numero {
            text-align: center;
            font-weight: bold;
            margin-bottom: 13px;
        }
        ';
    }

    public static function renderDocumentHeader($nombreEmpresa, $ruc, $titulo, $numero = '', $subtitulo = 'ALQUILER VENTA DE VEHICULOS MOTORIZADOS', $tituloClasses = 'titulo')
    {
        $empresa = strtoupper(trim((string) $nombreEmpresa));
        $rucTexto = trim((string) $ruc);
        $tituloTexto = trim((string) $titulo);
        $numeroTexto = trim((string) $numero);
        $subtituloTexto = strtoupper(trim((string) $subtitulo));
        $clases = trim((string) $tituloClasses);

        $html = '<div class="header">';
        $html .= '<div class="empresa">' . htmlspecialchars($empresa, ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<div class="subempresa">' . htmlspecialchars($subtituloTexto, ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<div class="ruc">R.U.C. ' . htmlspecialchars($rucTexto, ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<div class="line"></div>';
        $html .= '<br>';
        $html .= '<div class="' . htmlspecialchars($clases, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($tituloTexto, ENT_QUOTES, 'UTF-8') . '</div>';

        if ($numeroTexto !== '') {
            $html .= '<br>';
            $html .= '<div class="numero">N° ' . htmlspecialchars($numeroTexto, ENT_QUOTES, 'UTF-8') . '</div>';
        }

        $html .= '</div>';

        return $html;
    }

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

    function numeroALetrasMoneda($numero, $language = 'es_PE', $currency = 'PEN')
    {
        $formatter = new NumberFormatter($language, NumberFormatter::SPELLOUT);

        // Formatear a 2 decimales
        $partes = explode('.', number_format($numero, 2, '.', ''));

        $entero = (int) $partes[0];
        $decimal = $partes[1];

        // Convertir a letras
        $letras = strtoupper($formatter->format($entero));

        // Definir moneda
        switch ($currency) {
            case 'PEN':
                $moneda = ($entero == 1) ? 'SOL' : 'SOLES';
                break;
            case 'USD':
                $moneda = ($entero == 1) ? 'DÓLAR' : 'DÓLARES';
                break;
            case 'EUR':
                $moneda = ($entero == 1) ? 'EURO' : 'EUROS';
                break;
            default:
                $moneda = $currency;
                break;
        }

        return "$letras CON $decimal/100 $moneda";
    }


    function fechaLetras($fecha)
    {
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        $timestamp = strtotime($fecha);

        $dia = date('d', $timestamp);
        $mes = $meses[(int) date('m', $timestamp)];
        $anio = date('Y', $timestamp);

        return "$dia de $mes del $anio";
    }


    public function tiposDocumentacion($tipo)
    {
        if ($tipo == 1) {
            return "C";
        } elseif ($tipo == 2) {
            return "AE";
        } elseif ($tipo == 3) {
            return "OR";
        } elseif ($tipo == 4) {
            return "CP";
        } elseif ($tipo == 5) {
            return "CV";
        } else {
            return "";
        }
    }

    public static function monedaFormt($monto, $currency = 'PEN', $locale = "es_PE")
    {
        // Validar monto
        if (!is_numeric($monto)) {
            $monto = 0;
        }

        // Crear formateador
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        // Formatear moneda correctamente (usa código ISO: PEN, USD, EUR, etc.)
        $resultado = $formatter->formatCurrency($monto, $currency);

        return $resultado;
    }

    public static function getCurrencyCode($idsucursal)
    {
        $sql = "SELECT moneda FROM sucursal WHERE idsucursal = '$idsucursal'";
        $result = ejecutarConsultaSimpleFila($sql);
        return $result['moneda'] ?? 'PEN'; // Devuelve 'PEN' por defecto si no se encuentra la moneda
    }
}