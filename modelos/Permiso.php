<?php
require_once "../configuraciones/Conexion.php";
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";

class Permiso extends Helpers
{
    public function listarPermisosNuevos(): array
    {
        return (new DBQuery($this->pdo))
            ->query("
                SELECT
                    p.id,
                    p.modulo_id,
                    p.codigo,
                    p.nombre,
                    m.nombre AS modulo_nombre
                FROM permisos p
                INNER JOIN modulos m ON m.id = p.modulo_id
                ORDER BY m.orden, m.nombre, p.nombre
            ")
            ->get();
    }

    public function listarModulosNuevos(): array
    {
        return (new DBQuery($this->pdo))
            ->select(['id', 'nombre', 'modulo_padre_id'])
            ->from('modulos')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
    }

    public function obtenerPermisoNuevo(int $id): ?array
    {
        if ($id < 1) {
            throw new InvalidArgumentException('ID de permiso inválido.');
        }

        $permiso = (new DBQuery($this->pdo))
            ->select(['id', 'modulo_id', 'codigo', 'nombre'])
            ->from('permisos')
            ->where('id', '=', $id)
            ->first();

        if (!$permiso) {
            throw new InvalidArgumentException('El permiso seleccionado no existe.');
        }

        return $permiso;
    }

    public function guardarPermisoNuevo(
        string $nombre,
        string $codigo,
        int $moduloId,
        ?int $id = null
    ): array {
        $nombre = trim($nombre);
        $codigo = trim($codigo);

        if ($nombre === '') {
            throw new InvalidArgumentException('El nombre del permiso es obligatorio.');
        }
        if ($codigo === '') {
            throw new InvalidArgumentException('El código del permiso es obligatorio.');
        }
        if ($moduloId < 1) {
            throw new InvalidArgumentException('Seleccione un módulo válido.');
        }

        $modulo = (new DBQuery($this->pdo))
            ->select('id')
            ->from('modulos')
            ->where('id', '=', $moduloId)
            ->first();
        if (!$modulo) {
            throw new InvalidArgumentException('El módulo seleccionado no existe.');
        }

        $duplicado = (new DBQuery($this->pdo))
            ->select('id')
            ->from('permisos')
            ->where('codigo', '=', $codigo);
        if ($id !== null) {
            $duplicado->where('id', '!=', $id);
        }
        if ($duplicado->exists()) {
            throw new InvalidArgumentException('Ya existe un permiso con ese código.');
        }

        if ($id !== null) {
            $actual = (new DBQuery($this->pdo))
                ->select('id')
                ->from('permisos')
                ->where('id', '=', $id)
                ->first();
            if (!$actual) {
                throw new InvalidArgumentException('El permiso seleccionado no existe.');
            }

            $actualizado = (new FluentSaver($this->pdo))
                ->table('permisos')
                ->primaryKey('id')
                ->timestamps(false)
                ->data([
                    'id' => $id,
                    'modulo_id' => $moduloId,
                    'codigo' => $codigo,
                    'nombre' => $nombre,
                ])
                ->update();
            if (!$actualizado) {
                throw new RuntimeException('No se pudo actualizar el permiso.');
            }

            return [
                'id' => $id,
                'message' => 'Permiso actualizado correctamente.',
            ];
        }

        $newId = (new FluentSaver($this->pdo))
            ->table('permisos')
            ->timestamps(false)
            ->data([
                'modulo_id' => $moduloId,
                'codigo' => $codigo,
                'nombre' => $nombre,
            ])
            ->save();

        if (!$newId) {
            throw new RuntimeException('No se pudo registrar el permiso.');
        }

        return [
            'id' => $newId,
            'message' => 'Permiso registrado correctamente.',
        ];
    }

    public function eliminarPermisoNuevo(int $id): void
    {
        if ($id < 1) {
            throw new InvalidArgumentException('ID de permiso inválido.');
        }

        $this->pdo->beginTransaction();
        try {
            $permiso = (new DBQuery($this->pdo))
                ->select('id')
                ->from('permisos')
                ->where('id', '=', $id)
                ->first();
            if (!$permiso) {
                throw new InvalidArgumentException('El permiso seleccionado no existe.');
            }

            (new FluentSaver($this->pdo))
                ->table('rol_permisos')
                ->where('permiso_id', '=', $id)
                ->deleteWhere();

            (new FluentSaver($this->pdo))
                ->table('usuario_permisos')
                ->where('permiso_id', '=', $id)
                ->deleteWhere();

            $eliminado = (new FluentSaver($this->pdo))
                ->table('permisos')
                ->primaryKey('id')
                ->delete($id);
            if (!$eliminado) {
                throw new RuntimeException('No se pudo eliminar el permiso.');
            }

            $this->pdo->commit();
        } catch (\Throwable $th) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $th;
        }
    }

}