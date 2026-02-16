<?php

$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    ? 'https'
    : 'http';

$host = $_SERVER['HTTP_HOST'];

// 👉 detectar entorno local
if ($host === 'localhost' || strpos($host, '127.0.0.1') !== false) {
    // LOCAL (xampp / wamp)
    define('APP_URL', $protocolo . '://' . $host . '/test');
} else {
    // HOSTING / SUBDOMINIO
    define('APP_URL', $protocolo . '://' . $host);
}
