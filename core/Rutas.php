<?php
require_once __DIR__ . "/../modelos/Helpers.php";
require_once __DIR__ . "/../core/FluentQuery.php";
require_once __DIR__ . '/../core/Response.php';

class Rutas extends Helpers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function obtener(string $url): ?array
    {
        foreach ($this->listaRutas() as $modulo) {
            if (($modulo['ruta'] ?? null) === $url) {
                return $modulo;
            }

            foreach ($modulo['childrens'] ?? [] as $hijo) {
                if (($hijo['ruta'] ?? null) === $url) {
                    $hijo['submodulo'] = true;
                    $hijo['modulo'] = $modulo['nombre'];

                    return $hijo;
                }
            }
        }

        return null;
    }

    public function verificarPermiso(int $id): bool
    {
        $idusuario = (int) ($_SESSION['idusuario'] ?? 0);

        if (Helpers::esSuperusuario($idusuario)) {
            return true;
        }

        $resultado = (new DBQuery($this->pdo))
            ->query("
                    SELECT EXISTS (
                        SELECT 1
                        FROM permisos p
                        WHERE p.modulo_id = :modulo_id
                            AND p.codigo LIKE '%.ver'
                            AND (
                                EXISTS (
                                    SELECT 1
                                    FROM rol_permisos rp
                                    INNER JOIN usuario_roles ur
                                        ON ur.rol_id = rp.rol_id
                                    WHERE rp.permiso_id = p.id
                                        AND ur.idusuario = :usuario_rol
                                )
                                OR EXISTS (
                                    SELECT 1
                                    FROM usuario_permisos up
                                    WHERE up.permiso_id = p.id
                                        AND up.idusuario = :usuario_directo
                                )
                            )
                    ) AS autorizado
                ", 
                [
                'modulo_id' => $id,
                'usuario_rol' => $idusuario,
                'usuario_directo' => $idusuario,
            ])
            ->first();

        return (bool) ($resultado['autorizado'] ?? false);
    }

    public function listaRutas(): array
    {
        $modulos = (new DBQuery($this->pdo))
            ->select('*')
            ->from('modulos')
            ->orderBy('orden', 'ASC')
            ->get();

        $resultado = [];
        $padres = [];
        $autorizados = [];

        foreach ($modulos as $modulo) {
            $id = (int) $modulo['id'];

            $autorizados[$id] = $this->verificarPermiso($id);

            $resultado[$id] = [
                'id' => $id,
                'nombre' => $modulo['nombre'],
                'ruta' => $modulo['ruta'],
                'icono' => $modulo['icono'],
                'menu' => $modulo['menu'],
                'childrens' => [],
            ];

            $padres[$id] = !empty($modulo['modulo_padre_id'])
                ? (int) $modulo['modulo_padre_id']
                : null;
        }

        foreach (array_keys($resultado) as $id) {
            $padreId = $padres[$id];

            if ($padreId !== null && isset($resultado[$padreId])) {
                $resultado[$padreId]['childrens'][] = $resultado[$id];
            }
        }

        $filtrar = function (array $modulo) use (&$filtrar, $autorizados): ?array {
            $hijosAutorizados = [];

            foreach ($modulo['childrens'] as $hijo) {
                $hijoFiltrado = $filtrar($hijo);

                if ($hijoFiltrado !== null) {
                    $hijosAutorizados[] = $hijoFiltrado;
                }
            }

            $id = (int) $modulo['id'];

            // Mantener el padre si tiene permiso propio o hijos autorizados.
            if (!($autorizados[$id] ?? false) && empty($hijosAutorizados)) {
                return null;
            }

            $modulo['childrens'] = $hijosAutorizados;

            return $modulo;
        };

        $rutas = [];

        foreach ($resultado as $id => $modulo) {
            if ($padres[$id] !== null) {
                continue;
            }

            $moduloFiltrado = $filtrar($modulo);

            if ($moduloFiltrado !== null) {
                $rutas[] = $moduloFiltrado;
            }
        }

        return $rutas;
    }
}