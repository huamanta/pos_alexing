<?php
require_once __DIR__ . "/Helpers.php";

class Roles extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listar()
    {
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 20;
        $search = trim($_GET['search'] ?? '');

        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('roles')
            ->orderBy('nombre')
            ->search($search, ['nombre'])
            ->paginate($page, $limit);

        return Response::json($data);
    }


    public function listarModulos()
    {
        $modulos = (new DBQuery($this->pdo))
            ->select('*')
            ->from('modulos')
            ->whereNotNull('nombre')
            ->get();

        $permisos = (new DBQuery($this->pdo))
            ->select('*')
            ->from('permisos')
            ->get();

        $permisosPorModulo = [];

        foreach ($permisos as $permiso) {
            $permisosPorModulo[$permiso['modulo_id']][] = $permiso;
        }

        $modulosPorId = [];

        foreach ($modulos as $modulo) {
            $modulo['childrens'] = [];
            $modulo['permisos'] = $permisosPorModulo[$modulo['id']] ?? [];
            $modulosPorId[$modulo['id']] = $modulo;
        }

        $resultado = [];

        foreach ($modulosPorId as $id => &$modulo) {
            $padreId = $modulo['modulo_padre_id'] ?? null;

            if ($padreId !== null && isset($modulosPorId[$padreId])) {
                $modulosPorId[$padreId]['childrens'][] = &$modulo;
            } else {
                $resultado[] = &$modulo;
            }
        }

        unset($modulo);

        return Response::json($resultado);
    }

    public function obtener(int $id): ?array
    {
        try {
            if (!$id || $id < 1) {
                throw new InvalidArgumentException('ID de rol inválido.');
            }

            $rol = (new DBQuery($this->pdo))
                ->select('*')
                ->from('roles')
                ->where('id', '=', $id)
                ->first();

            if (!$rol) {
                throw new Exception("El rol no existe.");
            }

            $permisos = (new DBQuery($this->pdo))
                ->select('permiso_id')
                ->from('rol_permisos')
                ->where('rol_id', '=', $id)
                ->get();

            $rol['permisos'] = array_map(
                'intval',
                array_column($permisos, 'permiso_id')
            );

            return Response::json($rol);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }

    }


    public function guardar(
        string $nombre,
        ?string $descripcion,
        array $permisos,
        ?int $id = null
    ) {
        $nombre = trim($nombre);
        $descripcion = trim($descripcion ?? '');

        if ($nombre === '') {
            throw new InvalidArgumentException(
                'El nombre del rol es obligatorio.'
            );
        }

        if (!is_array($permisos)) {
            throw new InvalidArgumentException('Formato de permisos inválido.');
        }

        $permisos = array_values(array_unique(array_filter(
            array_map('intval', $permisos),
            fn($permisoId) => $permisoId > 0
        )));

        $this->pdo->beginTransaction();

        try {
            if ($id !== null) {
                $rol = (new DBQuery($this->pdo))
                    ->select('id')
                    ->from('roles')
                    ->where('id', '=', $id)
                    ->first();

                if (!$rol) {
                    throw new InvalidArgumentException(
                        'El rol seleccionado no existe.'
                    );
                }

                (new FluentSaver($this->pdo))
                    ->table('roles')
                    ->primaryKey('id')
                    ->nullable(['descripcion'])
                    ->data([
                        'id' => $id,
                        'nombre' => $nombre,
                        'descripcion' => $descripcion
                    ])
                    ->update();

                $rolId = $id;

                (new FluentSaver($this->pdo))
                    ->table('rol_permisos')
                    ->where('rol_id', '=', $rolId)
                    ->deleteWhere();

                $message = 'Rol actualizado correctamente.';

            } else {
                $rolId = (new FluentSaver($this->pdo))
                    ->table('roles')
                    ->nullable(['descripcion'])
                    ->data([
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'estado' => 1
                    ])
                    ->save();

                if (!$rolId) {
                    throw new RuntimeException(
                        'No se pudo registrar el rol.'
                    );
                }

                $message = 'Rol registrado correctamente.';
            }

            if ($permisos) {
                $permisosExistentes = (new DBQuery($this->pdo))
                    ->select('id')
                    ->from('permisos')
                    ->whereIn('id', $permisos)
                    ->get();

                $permisosValidos = array_map(
                    'intval',
                    array_column($permisosExistentes, 'id')
                );

                sort($permisosValidos);
                $permisosSolicitados = $permisos;
                sort($permisosSolicitados);

                if ($permisosValidos !== $permisosSolicitados) {
                    throw new InvalidArgumentException(
                        'Uno o más permisos seleccionados no existen.'
                    );
                }

                foreach ($permisos as $permisoId) {
                    (new FluentSaver($this->pdo))
                        ->table('rol_permisos')
                        ->timestamps(false)
                        ->data([
                            'rol_id' => $rolId,
                            'permiso_id' => $permisoId
                        ])
                        ->save();
                }
            }

            $this->pdo->commit();

            Response::json([
                'success' => true,
                'message' => $message,
                'id' => $rolId
            ]);

        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return Response::error($e->getMessage());
        }
    }

    public function eliminar(int $id)
    {
        try {
            if (!$id || $id < 1) {
                throw new InvalidArgumentException('ID de rol inválido.');
            }

            $this->pdo->beginTransaction();

            (new FluentSaver($this->pdo))
                ->table('rol_permisos')
                ->where('rol_id', '=', $id)
                ->deleteWhere();

            (new FluentSaver($this->pdo))
                ->table('roles')
                ->primaryKey('id')
                ->delete($id);

            $this->pdo->commit();

            return Response::json([
                'success' => true,
                'message' => 'Rol eliminado correctamente.'
            ]);
        } catch (\Throwable $th) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return Response::error($th->getMessage());
        }
    }

}