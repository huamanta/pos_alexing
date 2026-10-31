<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Predis\Client;

class Queue
{
    private static ?Client $redis = null;

    private static function redis(): Client
    {
        if (!self::$redis) {
            $config = [
                'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
                'port' => $_ENV['REDIS_PORT'] ?? 6379,
            ];

            $password = $_ENV['REDIS_PASSWORD'] ?? '';
            if ($password !== '') {
                $config['password'] = $password;
            }

            $database = (int) ($_ENV['REDIS_DATABASE'] ?? 0);
            if ($database > 0) {
                $config['database'] = $database;
            }

            self::$redis = new Client($config);
        }

        return self::$redis;
    }

    public static function connection(): Client
    {
        return self::redis();
    }

    public static function dispatch(object|string $job, array $data = []): PendingDispatch
    {
        return new PendingDispatch($job, $data);
    }

    public static function push(
        object|string $job,
        array $data = [],
        string $queue = 'default',
        int $delaySeconds = 0
    ): string {
        $id = bin2hex(random_bytes(16));
        $queue = self::normalizeQueue($queue);

        $payload = [
            'id' => $id,
            'job' => is_object($job) ? get_class($job) : $job,
            'data' => is_object($job) ? null : $data,
            'payload' => is_object($job)
                ? base64_encode(serialize($job))
                : null,
            'queue' => $queue,
            'attempts' => 0,
            'max_attempts' => 3,
            'retry_after' => 10,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $redis = self::redis();
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($body === false) {
            throw new RuntimeException('No se pudo serializar el trabajo para Redis.');
        }

        if ($delaySeconds > 0) {
            $redis->zadd("queues:{$queue}:delayed", [
                $id => time() + $delaySeconds
            ]);

            $redis->hset("queues:{$queue}:delayed:payloads", $id, $body);
        } else {
            $redis->rpush("queues:{$queue}", [$body]);
        }

        return $id;
    }

    private static function normalizeQueue(string $queue): string
    {
        $queue = trim($queue);
        if ($queue === '') {
            return 'default';
        }

        if (!preg_match('/^[A-Za-z0-9_-]+$/', $queue)) {
            throw new InvalidArgumentException('Nombre de cola inválido.');
        }

        return $queue;
    }
}

class PendingDispatch
{
    private object|string $job;
    private array $data;
    private string $queue = 'default';
    private int $delaySeconds = 0;
    private ?string $jobId = null;
    private bool $disposed = false;

    public function __construct(object|string $job, array $data = [])
    {
        $this->job = $job;
        $this->data = $data;
    }

    public function delay(int $seconds): self
    {
        $this->delaySeconds = max(0, $seconds);
        return $this;
    }

    public function onQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function dispatch(): string
    {
        if (!$this->disposed) {
            $this->jobId = Queue::push(
                $this->job,
                $this->data,
                $this->queue,
                $this->delaySeconds
            );
            $this->disposed = true;
        }

        return $this->jobId ?? '';
    }

    public function __destruct()
    {
        if (!$this->disposed) {
            try {
                $this->dispatch();
            } catch (Throwable $e) {
                error_log('Error al publicar trabajo en Redis: ' . $e->getMessage());
            }
        }
    }
}