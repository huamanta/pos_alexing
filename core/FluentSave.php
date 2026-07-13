<?php

declare(strict_types=1);

class FluentSaver
{
    private PDO $pdo;
    private string $table;
    private array $data = [];
    private string $primaryKey = 'id';
    private array $casts = [];
    private array $nullable = [];

    private bool $timestamps = true;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function data(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Define la clave primaria (por defecto 'id')
     */
    public function primaryKey(string $key): self
    {
        $this->primaryKey = $key;
        return $this;
    }

    /**
     * Fuerza el tipo de dato antes de guardar.
     * Ejemplo: ['is_active' => 'bool', 'amount' => 'float', 'age' => 'int']
     */
    public function cast(array $types): self
    {
        $this->casts = $types;
        return $this;
    }

    /**
     * Define qué campos se convierten de string vacío "" a NULL en la BD
     */
    public function nullable(array $fields): self
    {
        $this->nullable = $fields;
        return $this;
    }

    /**
     * Ejecuta el INSERT o UPDATE
     * Retorna el ID insertado (int) o true si fue una actualización exitosa
     */
    public function save(): int|bool
    {
        $this->sanitizeData();
        $this->manageTimestamps();

        // ¿Viene un ID? -> Es UPDATE. Si no -> Es INSERT.
        $isUpdate = isset($this->data[$this->primaryKey]) && !empty($this->data[$this->primaryKey]);

        try {
            if ($isUpdate) {
                return $this->executeUpdate();
            } else {
                return $this->executeInsert();
            }
        } catch (PDOException $e) {
            // En producción esto debería ir a un log real, no mostrarse al usuario
            error_log("Error FluentSaver: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    private function sanitizeData(): void
    {
        foreach ($this->data as $key => $value) {
            // 1. Convertir vacíos a NULL si están en la lista nullable
            if (in_array($key, $this->nullable) && $value === '') {
                $this->data[$key] = null;
                continue;
            }

            // 2. Aplicar Casteo si está definido
            if (isset($this->casts[$key])) {
                $this->data[$key] = match (strtolower($this->casts[$key])) {
                    'int', 'integer' => (int) $value,
                    'float', 'double', 'decimal' => (float) $value,
                    'bool', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
                    default => (string) $value
                };
            }
        }
    }

    public function timestamps(bool $enabled = true): self
    {
        $this->timestamps = $enabled;

        return $this;
    }

    private function manageTimestamps(): void
    {
        if (!$this->timestamps) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        // INSERT
        if (
            !isset($this->data[$this->primaryKey]) &&
            !isset($this->data['created_at'])
        ) {
            $this->data['created_at'] = $now;
        }

        // INSERT y UPDATE
        if (!isset($this->data['updated_at'])) {
            $this->data['updated_at'] = $now;
        }
    }
    private function executeInsert(): int
    {
        // Quitamos el PK si por error se envió vacío
        unset($this->data[$this->primaryKey]);

        $columns = implode(', ', array_keys($this->data));
        $placeholders = ':' . implode(', :', array_keys($this->data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->pdo->prepare($sql);
        $this->bindValues($stmt);
        $stmt->execute();

        return (int) $this->pdo->lastInsertId();
    }

    private function executeUpdate(): bool
    {
        $id = $this->data[$this->primaryKey];
        unset($this->data[$this->primaryKey]); // No actualizamos el PK

        $setClauses = [];
        foreach (array_keys($this->data) as $column) {
            $setClauses[] = "{$column} = :{$column}";
        }
        $setSql = implode(', ', $setClauses);

        $sql = "UPDATE {$this->table} SET {$setSql} WHERE {$this->primaryKey} = :pk_id";

        $stmt = $this->pdo->prepare($sql);
        $this->bindValues($stmt);
        $stmt->bindValue(':pk_id', $id, PDO::PARAM_INT); // Asumimos que el PK es entero
        $stmt->execute();

        return true;
    }

    private function bindValues(PDOStatement $stmt): void
    {
        foreach ($this->data as $key => $value) {
            // Si es null, bindParam necesita el tipo PDO::PARAM_NULL
            if (is_null($value)) {
                $stmt->bindValue(":{$key}", $value, PDO::PARAM_NULL);
            } elseif (is_int($value)) {
                $stmt->bindValue(":{$key}", $value, PDO::PARAM_INT);
            } elseif (is_bool($value)) {
                $stmt->bindValue(":{$key}", $value, PDO::PARAM_BOOL);
            } else {
                $stmt->bindValue(":{$key}", $value, PDO::PARAM_STR);
            }
        }
    }
}