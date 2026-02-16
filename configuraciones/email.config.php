<?php

// =================================================================
// CONFIGURACIÓN DE CORREO ELECTRÓNICO (SMTP)
// =================================================================
// Completa los siguientes campos con los datos de tu servidor de correo.
// Si usas Gmail, recuerda generar una "Contraseña de aplicación" 
// en la configuración de seguridad de tu cuenta de Google.

// Configuración del servidor SMTP
$smtp_host      = 'smtp.gmail.com';     // E.g., 'smtp.gmail.com' o el de tu proveedor
$smtp_port      = 587;                  // Puerto: 587 para TLS, 465 para SSL
$smtp_secure    = 'tls';                // 'tls' o 'ssl'

// Credenciales de autenticación
$smtp_username  = 'samvtware@gmail.com'; // Tu dirección de correo completa
$smtp_password  = 'ybxr ulfz bxjr dvmb';    // La contraseña de aplicación que generaste

// Información del remitente
$smtp_from_email = 'tucorreo@gmail.com'; // El correo que se mostrará como remitente
$smtp_from_name  = 'Soporte del Sistema';  // El nombre que se mostrará como remitente
