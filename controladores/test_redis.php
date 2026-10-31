<?php

require_once __DIR__ . '/../core/Queue.php';

$id = Queue::dispatch('EnviarCorreo', [
    'origen' => 'alexhe406@gmail.com',
    'destinatario' => 'alexhe406@gmail.com',
    'asunto' => 'Prueba Redis',
    'contenido' => '<h1>Redis funcionando</h1>'
]);

echo "Tarea enviada: " . $id . PHP_EOL;