<?php
class Constants {
    const CLIENTE_DEFAULT = 1;
    const MAX_USUARIOS = 100;
    const MESES_RECUPERACION = 3;
    const DIAS_MORA = 90;
    const RECUPERACION_CRITICA = 90;
    const RECUPERACION_ALTO = 60;
    const RECUPERACION_MEDIA = 30;
    const GUIA_REMISION = 'Guia de Remisión';
    const MESES = [
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
        12 => 'Diciembre',
    ];
    const INGRESOS = 'Ingresos';
    const EGRESOS = 'Egresos';
    const OUTPUT_IMAGE_PNG = 'png';
    const OUTPUT_IMAGE_JPG = 'jpg';
    const OUTPUT_IMAGE_JPEG = 'jpeg';
    const OUTPUT_IMAGE_GIF = 'gif';
    const CONTENEDOR_BASE = 1;
    const INGRESO_KARDEX = 1;
    const EGRESO_KARDEX = 2;
    
}