<?php
class Helpers
{
    public static function get_currency_symbol($monto, $currency = 'PEN', $locale = "es_PE")
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

    public static function get_symbol($currency = 'PEN', $locale = "es_PE")
    {
        if (!class_exists('NumberFormatter')) {
            return $currency;
        }

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        // Truco: formatear 0 y extraer símbolo
        $formatted = $formatter->formatCurrency(0, $currency);

        // Quitar números y dejar solo símbolo
        return trim(preg_replace('/[0-9\.\,\s]/', '', $formatted));
    }

    public static function get_currency_code($idsucursal)
    {
        $sql = "SELECT moneda FROM sucursal WHERE idsucursal = '$idsucursal'";
        $result = ejecutarConsultaSimpleFila($sql);
        return $result['moneda'] ?? 'PEN'; // Devuelve 'PEN' por defecto si no se encuentra la moneda
    }
}