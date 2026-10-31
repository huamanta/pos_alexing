<?php
//Incluímos inicialmente la conexión a la base de datos
require_once __DIR__ . "/../configuraciones/Conexion.php";
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";
class Usuario extends Helpers
{

    //Implementamos nuestro constructor
    public function __construct()
    {
        parent::__construct();
    }

    public function obtenerDatosPermisos(int $idusuario = 0): array
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
            $permiso['id'] = (int) $permiso['id'];
            $permisosPorModulo[(int) $permiso['modulo_id']][] = $permiso;
        }

        $modulosPorId = [];
        foreach ($modulos as $modulo) {
            $modulo['id'] = (int) $modulo['id'];
            $modulo['modulo_padre_id'] = isset($modulo['modulo_padre_id'])
                ? (int) $modulo['modulo_padre_id']
                : null;
            $modulo['childrens'] = [];
            $modulo['permisos'] = $permisosPorModulo[$modulo['id']] ?? [];
            $modulosPorId[$modulo['id']] = $modulo;
        }

        $arbolModulos = [];
        foreach (array_keys($modulosPorId) as $moduloId) {
            $padreId = $modulosPorId[$moduloId]['modulo_padre_id'];
            if ($padreId !== null && isset($modulosPorId[$padreId])) {
                $modulosPorId[$padreId]['childrens'][] = &$modulosPorId[$moduloId];
            } else {
                $arbolModulos[] = &$modulosPorId[$moduloId];
            }
        }
        unset($moduloId);

        $roles = (new DBQuery($this->pdo))
            ->select(['id', 'nombre', 'descripcion'])
            ->from('roles')
            ->orderBy('nombre')
            ->get();

        $rolesSeleccionados = [];
        $permisosSeleccionados = [];
        if ($idusuario > 0) {
            $rolesSeleccionados = (new DBQuery($this->pdo))
                ->select('rol_id')
                ->from('usuario_roles')
                ->where('idusuario', '=', $idusuario)
                ->get();
            $rolesSeleccionados = array_map(
                'intval',
                array_column($rolesSeleccionados, 'rol_id')
            );

            $permisosSeleccionados = (new DBQuery($this->pdo))
                ->select('permiso_id')
                ->from('usuario_permisos')
                ->where('idusuario', '=', $idusuario)
                ->get();
            $permisosSeleccionados = array_map(
                'intval',
                array_column($permisosSeleccionados, 'permiso_id')
            );
        }

        return [
            'roles' => $roles,
            'modulos' => $arbolModulos,
            'seleccionados' => [
                'roles' => $rolesSeleccionados,
                'permisos' => $permisosSeleccionados,
            ],
        ];
    }

    //Implementamos un método para insertar registros
    public function insertar(
        $idpersonal,
        $login,
        $clave,
        $idsucursal,
        $roles = [],
        $permisos = []
    ) {
        try {
            $this->pdo->beginTransaction();
            $usuario = (new FluentSaver($this->pdo))
                ->table('usuario')
                ->data([
                    'idpersonal' => $idpersonal,
                    'login' => $login,
                    'clave' => $clave,
                    'condicion' => 1
                ])
                ->save();
            if (!$usuario) {
                throw new Exception('Error al crear el usuario');
            }

            foreach ($idsucursal as $suc) {
                $usuario_sucursal = (new FluentSaver($this->pdo))
                    ->table('usuario_sucursal')
                    ->data(['idusuario' => $usuario, 'idsucursal' => $suc])
                    ->save();
                if (!$usuario_sucursal) {
                    throw new Exception('Error al crear el usuario sucursal');
                }
            }

            $this->sincronizarRolesPermisos($usuario, $roles, $permisos);

            $this->pdo->commit();
            return Response::json([
                'success' => true,
                'message' => 'Se ha creado el usuario exitosamente'
            ]);

        } catch (\Throwable $th) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return Response::error($th->getMessage());
        }
    }

    private function sincronizarRolesPermisos(int $idusuario, array $roles = [], array $permisos = []): void
    {
        $roles = array_values(array_unique(array_filter(array_map('intval', $roles ?? []), fn($rol) => $rol > 0)));
        $permisos = array_values(array_unique(array_filter(array_map('intval', $permisos ?? []), fn($permiso) => $permiso > 0)));

        if ($roles) {
            $rolesExistentes = (new DBQuery($this->pdo))
                ->select('id')
                ->from('roles')
                ->whereIn('id', $roles)
                ->get();

            $rolesValidos = array_map('intval', array_column($rolesExistentes, 'id'));
            sort($rolesValidos);
            sort($roles);

            if ($rolesValidos !== $roles) {
                throw new InvalidArgumentException('Uno o más roles seleccionados no existen.');
            }
        }

        if ($permisos) {
            $permisosExistentes = (new DBQuery($this->pdo))
                ->select('id')
                ->from('permisos')
                ->whereIn('id', $permisos)
                ->get();

            $permisosValidos = array_map('intval', array_column($permisosExistentes, 'id'));
            sort($permisosValidos);
            sort($permisos);

            if ($permisosValidos !== $permisos) {
                throw new InvalidArgumentException('Uno o más permisos seleccionados no existen.');
            }
        }

        (new FluentSaver($this->pdo))
            ->table('usuario_roles')
            ->where('idusuario', '=', $idusuario)
            ->deleteWhere();

        foreach ($roles as $rolId) {
            (new FluentSaver($this->pdo))
                ->table('usuario_roles')
                ->timestamps(false)
                ->data([
                    'idusuario' => $idusuario,
                    'rol_id' => $rolId,
                ])
                ->save();
        }

        (new FluentSaver($this->pdo))
            ->table('usuario_permisos')
            ->where('idusuario', '=', $idusuario)
            ->deleteWhere();

        foreach ($permisos as $permisoId) {
            (new FluentSaver($this->pdo))
                ->table('usuario_permisos')
                ->timestamps(false)
                ->data([
                    'idusuario' => $idusuario,
                    'permiso_id' => $permisoId,
                ])
                ->save();
        }
    }


    public function editar($idusuario, $idpersonal, $login, $clave, $idsucursal, $roles = [], $permisos = [])
    {
        try {
            $this->pdo->beginTransaction();
            $usuarioData = [
                'idusuario' => $idusuario,
                'idpersonal' => $idpersonal,
                'login' => $login,
            ];

            if (isset($clave) && trim($clave) !== '') {
                $usuarioData['clave'] = password_hash(trim($clave), PASSWORD_DEFAULT);
            }

            $actualizado = (new FluentSaver($this->pdo))
                ->table('usuario')
                ->primaryKey('idusuario')
                ->data($usuarioData)
                ->update();

            if (!$actualizado) {
                throw new Exception('No se pudo actualizar el usuario.');
            }

            $idsucursal = array_map('intval', $idsucursal ?? []);
            $idsucursal = array_values(array_unique($idsucursal));

            $sucursalesActuales = (new DBQuery($this->pdo))
                ->select(['idsucursal', 'deleted_at'])
                ->from('usuario_sucursal')
                ->where('idusuario', '=', $idusuario)
                ->get();

            $idsActuales = array_column($sucursalesActuales, 'idsucursal');

            $saverSucursal = new FluentSaver($this->pdo);

            // Reactivar / insertar las sucursales recibidas
            foreach ($idsucursal as $idSucursal) {

                if (in_array($idSucursal, $idsActuales)) {

                    $actualizado = $saverSucursal
                        ->table('usuario_sucursal')
                        ->where('idusuario', '=', $idusuario)
                        ->where('idsucursal', '=', $idSucursal)
                        ->data([
                            'deleted_at' => null
                        ])
                        ->update();

                    if (!$actualizado) {
                        throw new Exception(
                            "No se pudo activar la sucursal {$idSucursal}."
                        );
                    }

                } else {

                    $insertado = $saverSucursal
                        ->table('usuario_sucursal')
                        ->data([
                            'idusuario' => $idusuario,
                            'idsucursal' => $idSucursal,
                        ])
                        ->save();

                    if (!$insertado) {
                        throw new Exception(
                            "No se pudo asignar la sucursal {$idSucursal}."
                        );
                    }
                }
            }

            // Marcar como eliminadas las sucursales
            foreach ($sucursalesActuales as $sucursal) {

                $idSucursal = (int) $sucursal['idsucursal'];

                if (!in_array($idSucursal, $idsucursal)) {

                    $actualizado = $saverSucursal
                        ->table('usuario_sucursal')
                        ->where('idusuario', '=', $idusuario)
                        ->where('idsucursal', '=', $idSucursal)
                        ->data([
                            'deleted_at' => date('Y-m-d H:i:s')
                        ])
                        ->update();

                    if (!$actualizado) {
                        throw new Exception(
                            "No se pudo desactivar la sucursal {$idSucursal}."
                        );
                    }
                }
            }

            $roles = array_values(array_unique(array_filter(array_map('intval', $roles ?? []), fn($rol) => $rol > 0)));
            $permisos = array_values(array_unique(array_filter(array_map('intval', $permisos ?? []), fn($permiso) => $permiso > 0)));

            $this->sincronizarRolesPermisos($idusuario, $roles, $permisos);

            // CONFIRMAR
            $this->pdo->commit();

            return Response::json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente.'
            ]);

        } catch (\Throwable $th) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return Response::error(
                $th->getMessage()
            );
        }
    }

    public function listarSucursalesUsuario(int $idusuario): array
    {
        // Verificar si es superusuario
        $esSuperusuario = Helpers::esSuperusuario($idusuario);

        // Si es admin mostrar todas las sucursales
        if ($esSuperusuario) {
            $data = (new DBQuery($this->pdo))
                ->select('*')
                ->from('sucursal')
                ->softDeletes()
                ->orderBy('nombre')
                ->get();
            return $data;
        }
        // Solo sus sucursales asignadas
        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('sucursal s')
            ->join('usuario_sucursal us', 'us.idsucursal = s.idsucursal')
            ->softDeletes('s.deleted_at')
            ->softDeletes('us.deleted_at')
            ->where('us.idusuario', '=', $idusuario)
            ->orderBy('s.nombre')
            ->get();

        return $data;
    }

    //Implementamos un método para desactivar categorías
    public function desactivar($idusuario)
    {
        try {
            $desctivar = (new FluentSaver($this->pdo))
                ->table('usuario')
                ->primaryKey('idusuario')
                ->data([
                    'idusuario' => $idusuario,
                    'condicion' => 0
                ])
                ->update();
            if (!$desctivar) {
                throw new Exception('No se pudo desactivar el usuario');
            }
            return Response::json([
                'success' => true,
                'message' => 'El usuario ha sido desactivado'
            ]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }

    //Implementamos un método para activar categorías
    public function activar($idusuario)
    {
        try {
            $desctivar = (new FluentSaver($this->pdo))
                ->table('usuario')
                ->primaryKey('idusuario')
                ->data([
                    'idusuario' => $idusuario,
                    'condicion' => 1
                ])
                ->update();
            if (!$desctivar) {
                throw new Exception('No se pudo activar el usuario');
            }
            return Response::json([
                'success' => true,
                'message' => 'El usuario ha sido activado'
            ]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }

    public function verificarUsuario($nombre)
    {
        $sql = "SELECT * FROM usuario WHERE login = '$nombre'";
        return ejecutarConsultaSimpleFila($sql);
    }

    //Implementar un método para mostrar los datos de un registro a modificar
    public function mostrar($idusuario)
    {
        $data = (new DBQuery($this->pdo))
        ->select('*')
        ->from('usuario')
        ->where('idusuario', '=', $idusuario)
        ->first();
        return Response::json($data);
    }

    //Implementar un método para listar los registros
    public function listar()
    {
        $sql = "SELECT a.idusuario,a.idpersonal,c.nombre as trabajador,a.login,a.condicion,
                 GROUP_CONCAT(s.nombre SEPARATOR ', ') as nombre, a.superusuario
          FROM usuario a 
          INNER JOIN personal c ON a.idpersonal=c.idpersonal 
          LEFT JOIN usuario_sucursal us ON a.idusuario=us.idusuario
          LEFT JOIN sucursal s ON us.idsucursal=s.idsucursal
          GROUP BY a.idusuario";
        return ejecutarConsulta($sql);
    }


    //Función para verificar el acceso al sistema
    public function verificar($login, $clave)
    {
        try {
            $data = (new DBQuery($this->pdo))
                ->select([
                    'u.idusuario',
                    'u.idpersonal',
                    'c.imagen',
                    'c.nombre as nombre',
                    'c.cargo',
                    'u.login'
                ])
                ->from('usuario u')
                ->join('personal c', 'u.idpersonal=c.idpersonal')
                ->where('u.login', '=', $login)
                ->where('u.clave', '=', $clave)
                ->where('u.condicion', '=', 1)
                ->first();

            // Datos de IP y user agent
            $ip = self::getClientIP();
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

            if (!$data) {
                self::registrarHistorial(0, $ip, $user_agent, 0);
                throw new Exception('Usuario u contraseña incorrectos');
            }

            // Login exitoso
            $_SESSION['idusuario'] = $data['idusuario'];
            $_SESSION['idpersonal'] = $data['idpersonal'];
            $_SESSION['imagen'] = $data['imagen'];
            $_SESSION['nombre'] = $data['nombre'];
            $_SESSION['login'] = $data['login'];
            $_SESSION['cargo'] = $data['cargo'];
            $_SESSION["iniciarSesion"] = "ok";

            // Registrar historial de login exitoso
            self::registrarHistorial($data['idusuario'], $ip, $user_agent, 1);
            return Response::json([
                'success' => true,
                'message' => 'Las credenciales han sido validadas',
                'data' => $data
            ]);

        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }

    public function registrarHistorial($idusuario, $ip, $user_agent, $exito)
    {
        $sql = "
        INSERT INTO login_historial (idusuario, ip, user_agent, exito, fecha)
        VALUES (?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 5 HOUR))
    ";

        $stmt = $GLOBALS['conexion']->prepare($sql);
        $stmt->bind_param("isss", $idusuario, $ip, $user_agent, $exito);
        return $stmt->execute();
    }



    public function cerrarSesionesPrevias($idusuario)
    {
        $sql = "UPDATE login_historial
            SET logout = NOW()
            WHERE idusuario = '$idusuario'
              AND exito = 1
              AND logout IS NULL";
        return ejecutarConsulta($sql);
    }


    public function seleccionarSucursal($idsucursal)
    {
        $sql = "SELECT * FROM sucursal 
        INNER JOIN empresas ON sucursal.idempresa = empresas.idempresa
        WHERE idsucursal='$idsucursal'";
        return ejecutarConsultaSimpleFila($sql);
    }

    private function getClientIP(): string
    {
        $ip = '';

        $isProduction = env('APP_ENV', 'local') === 'production';
        $checkExternalIP = env('APP_EXTERNAL_IP_CHECK', false);

        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {

            if (!empty($_SERVER[$header])) {

                $ips = explode(',', $_SERVER[$header]);

                foreach ($ips as $i) {

                    $i = trim($i);

                    if (filter_var($i, FILTER_VALIDATE_IP)) {
                        $ip = $i;
                        break 2;
                    }
                }
            }
        }


        // Resolver IP pública solo si está habilitado
        if (
            $isProduction &&
            $checkExternalIP &&
            ($ip === '127.0.0.1' || $ip === '::1' || $ip === '')
        ) {

            try {

                $externalIp = file_get_contents('https://api.ipify.org');

                if (filter_var($externalIp, FILTER_VALIDATE_IP)) {
                    $ip = $externalIp;
                }

            } catch (Exception $e) {
                $ip = '0.0.0.0';
            }
        }


        // Ambiente local
        if (!$isProduction && ($ip === '' || $ip === '::1')) {
            $ip = '127.0.0.1';
        }


        return $ip;
    }

}

?>