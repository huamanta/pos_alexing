<?php

require_once __DIR__ . '/../configuraciones/bootstrap.php';
require_once __DIR__ . '/Queue.php';

$queueName = $argv[1] ?? 'default';
$queueName = preg_replace('/[^A-Za-z0-9_-]/', '', $queueName) ?: 'default';
$redis = Queue::connection();

function cargarClaseJob(string $className): void
{
    if (class_exists($className)) {
        return;
    }

    $simpleName = substr(strrchr('\\' . $className, '\\'), 1);
    $file = dirname(__DIR__) . '/jobs/' . $simpleName . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}

function promoverTrabajosDiferidos(Predis\Client $redis, string $queueName): void
{
    $delayedKey = "queues:{$queueName}:delayed";
    $payloadsKey = "queues:{$queueName}:delayed:payloads";
    $listKey = "queues:{$queueName}";

    $ids = $redis->zrangebyscore($delayedKey, '-inf', (string) time());

    foreach ($ids as $id) {
        $body = $redis->hget($payloadsKey, $id);

        if ($body !== null && $redis->zrem($delayedKey, $id)) {
            $redis->hdel($payloadsKey, [$id]);
            $redis->rpush($listKey, [$body]);
        }
    }
}

function ejecutarJob(array $job): mixed
{
    cargarClaseJob($job['job']);

    if (!class_exists($job['job'])) {
        throw new RuntimeException("Job no encontrado: {$job['job']}");
    }

    if (!empty($job['payload'])) {
        $instance = unserialize(
            base64_decode($job['payload'], true),
            ['allowed_classes' => true]
        );

        if (!is_object($instance)) {
            throw new RuntimeException('El payload del trabajo no contiene un objeto válido.');
        }
    } else {
        $instance = new $job['job']();
    }

    if (!method_exists($instance, 'handle')) {
        throw new RuntimeException("El job {$job['job']} no implementa handle().");
    }

    if (!empty($job['payload'])) {
        $result = $instance->handle();
    } else {
        $result = $instance->handle($job['data'] ?? []);
    }

    return $result;
}

echo "Worker iniciado. Cola: {$queueName}\n";

while (true) {
    promoverTrabajosDiferidos($redis, $queueName);

    $task = $redis->blpop(["queues:{$queueName}"], 1);

    if ($task === null) {
        continue;
    }

    $job = json_decode($task[1], true);

    if (!is_array($job) || empty($job['id']) || empty($job['job'])) {
        $redis->rpush('queues:failed', [$task[1]]);
        continue;
    }

    try {
        $result = ejecutarJob($job);
        if ($result === false) {
            throw new RuntimeException('El job terminó sin completar correctamente.');
        }
        echo "Tarea completada: {$job['id']} ({$job['job']})\n";
    } catch (Throwable $e) {
        $attempts = (int) ($job['attempts'] ?? 0) + 1;
        $maxAttempts = (int) ($job['max_attempts'] ?? 3);
        $retryAfter = (int) ($job['retry_after'] ?? 10);
        $job['attempts'] = $attempts;

        error_log("Error en job {$job['job']} (intento {$attempts}/{$maxAttempts}): {$e->getMessage()}");

        if ($attempts < $maxAttempts) {
            $delayKey = "queues:{$queueName}:delayed";
            $payloadKey = "queues:{$queueName}:delayed:payloads";
            $body = json_encode($job, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $redis->zadd($delayKey, [$job['id'] => time() + $retryAfter]);
            $redis->hset($payloadKey, $job['id'], $body);
        } else {
            $job['failed_at'] = date('Y-m-d H:i:s');
            $job['error'] = $e->getMessage();
            $redis->rpush('queues:failed', [
                json_encode($job, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            ]);
        }
    }
}