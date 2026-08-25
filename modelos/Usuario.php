<?php
//Incluímos inicialmente la conexión a la base de datos
require_once __DIR__ . "/../configuraciones/Conexion.php";
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";
date_default_timezone_set('America/Lima');
class Usuario extends Helpers
{

    //Implementamos nuestro constructor
    public function __construct()
    {
        parent::__construct();
    }

    //Implementamos un método para insertar registros
    public function insertar($idpersonal, $login, $clave, $idsucursal, $permisos, $subpermisos, $acciones)
    {
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

            // Insertar usuario sucursal
            foreach ($idsucursal as $suc) {
                $usuario_sucursal = (new FluentSaver($this->pdo))
                    ->table('usuario_sucursal')
                    ->data(['idusuario' => $usuario, 'idsucursal' => $suc])
                    ->save();
                if (!$usuario_sucursal) {
                    throw new Exception('Error al crear el usuario sucursal');
                }

            }

            // Insertar permisos
            foreach ($permisos as $permiso) {
                $usuario_permiso = (new FluentSaver($this->pdo))
                    ->table('usuario_permiso')
                    ->data(['idusuario' => $usuario, 'idpermiso' => $permiso])
                    ->save();
                if (!$usuario_permiso) {
                    throw new Exception('Error al crear el usuario permiso');
                }
            }

            // Insertar subpermisos
            foreach ($subpermisos as $subpermiso) {
                $permiso = (new DBQuery($this->pdo))
                    ->select('idpermiso')
                    ->from('subpermiso')
                    ->where('idsubpermiso', '=', $subpermiso)
                    ->first();
                if ($permiso) {
                    $idpermiso = $permiso['idpermiso'];
                    $usuario_sub_permiso = (new FluentSaver($this->pdo))
                        ->table('usuario_permiso')
                        ->data([
                            'idusuario' => $usuario,
                            'idpermiso' => $idpermiso,
                            'idsubpermiso' => $subpermiso
                        ])
                        ->save();
                    if (!$usuario_sub_permiso) {
                        throw new Exception('Error al crear el usuario sub permiso');
                    }
                }
            }

            // Insertar acciones
            foreach ($acciones as $accion) {
                $usuario_accion = (new FluentSaver($this->pdo))
                    ->table('usuario_accion')
                    ->data([
                        'idusuario' => $usuario,
                        'idaccion_permiso' => $accion,
                    ])
                    ->save();
                if (!$usuario_accion) {
                    throw new Exception('Error al crear el usuario accion');
                }
            }

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


    public function editar($idusuario, $idpersonal, $login, $clave, $idsucursal, $permisos, $subpermisos, $acciones)
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

            // OBTENER PERMISOS ACTUALES
            $permisos = array_map('intval', $permisos ?? []);
            $permisos = array_values(array_unique($permisos));

            $subpermisos = array_map('intval', $subpermisos ?? []);
            $subpermisos = array_values(array_unique($subpermisos));

            $acciones = array_map('intval', $acciones ?? []);
            $acciones = array_values(array_unique($acciones));

            // PERMISOS PRINCIPALES
            $permisosActuales = (new DBQuery($this->pdo))
                ->select([
                    'idusuario_permiso',
                    'idpermiso',
                    'idsubpermiso',
                    'deleted_at'
                ])
                ->from('usuario_permiso')
                ->where('idusuario', '=', $idusuario)
                ->get();

            // Reactivar / insertar permisos principales
            foreach ($permisos as $idPermiso) {

                $encontrado = null;

                foreach ($permisosActuales as $permiso) {

                    if (
                        (int) $permiso['idpermiso'] === $idPermiso &&
                        empty($permiso['idsubpermiso'])
                    ) {
                        $encontrado = $permiso;
                        break;
                    }
                }

                if ($encontrado) {
                    $actualizado = (new FluentSaver($this->pdo))
                        ->table('usuario_permiso')
                        ->primaryKey('idusuario_permiso')
                        ->data([
                            'idusuario_permiso' => $encontrado['idusuario_permiso'],
                            'deleted_at' => null
                        ])
                        ->update();

                    if (!$actualizado) {
                        throw new Exception(
                            "No se pudo activar el permiso {$idPermiso}."
                        );
                    }

                } else {

                    $insertado = (new FluentSaver($this->pdo))
                        ->table('usuario_permiso')
                        ->data([
                            'idusuario' => $idusuario,
                            'idpermiso' => $idPermiso,
                            'deleted_at' => null
                        ])
                        ->save();

                    if (!$insertado) {
                        throw new Exception(
                            "No se pudo asignar el permiso {$idPermiso}."
                        );
                    }
                }
            }

            // Marcar permisos principales que ya no vienen
            foreach ($permisosActuales as $permiso) {

                if (!empty($permiso['idsubpermiso'])) {
                    continue;
                }

                $idPermiso = (int) $permiso['idpermiso'];

                if (!in_array($idPermiso, $permisos)) {

                    (new FluentSaver($this->pdo))
                        ->table('usuario_permiso')
                        ->primaryKey('idusuario_permiso')
                        ->data([
                            'idusuario_permiso' => $permiso['idusuario_permiso'],
                            'deleted_at' => date('Y-m-d H:i:s')
                        ])
                        ->update();
                }
            }

            // Cada subpermiso pertenece a un permiso principal.
            foreach ($subpermisos as $idSubpermiso) {

                $subpermiso = (new DBQuery($this->pdo))
                    ->select(['idpermiso'])
                    ->from('subpermiso')
                    ->where('idsubpermiso', '=', $idSubpermiso)
                    ->first();

                if (!$subpermiso) {
                    throw new Exception(
                        "El subpermiso {$idSubpermiso} no existe."
                    );
                }

                $idPermiso = (int) $subpermiso['idpermiso'];

                $existente = null;

                foreach ($permisosActuales as $permiso) {

                    if (
                        (int) $permiso['idpermiso'] === $idPermiso &&
                        (int) $permiso['idsubpermiso'] === $idSubpermiso
                    ) {
                        $existente = $permiso;
                        break;
                    }
                }

                if ($existente) {

                    (new FluentSaver($this->pdo))
                        ->table('usuario_permiso')
                        ->primaryKey('idusuario_permiso')
                        ->data([
                            'idusuario_permiso' => $existente['idusuario_permiso'],
                            'deleted_at' => null
                        ])
                        ->update();

                } else {

                    (new FluentSaver($this->pdo))
                        ->table('usuario_permiso')
                        ->data([
                            'idusuario' => $idusuario,
                            'idpermiso' => $idPermiso,
                            'idsubpermiso' => $idSubpermiso,
                            'deleted_at' => null
                        ])
                        ->save();
                }
            }

            // Desactivar subpermisos que ya no vienen
            foreach ($permisosActuales as $permiso) {

                if (empty($permiso['idsubpermiso'])) {
                    continue;
                }

                $idSubpermiso = (int) $permiso['idsubpermiso'];

                if (!in_array($idSubpermiso, $subpermisos)) {

                    (new FluentSaver($this->pdo))
                        ->table('usuario_permiso')
                        ->primaryKey('idusuario_permiso')
                        ->data([
                            'idusuario_permiso' => $permiso['idusuario_permiso'],
                            'deleted_at' => date('Y-m-d H:i:s')
                        ])
                        ->update();
                }
            }

            // ACCIONES
            $accionesActuales = (new DBQuery($this->pdo))
                ->select([
                    'idusuario_accion',
                    'idaccion_permiso',
                    'deleted_at'
                ])
                ->from('usuario_accion')
                ->where('idusuario', '=', $idusuario)
                ->get();

            // Reactivar / insertar acciones
            foreach ($acciones as $idAccion) {

                $existente = null;

                foreach ($accionesActuales as $accion) {

                    if ((int) $accion['idaccion_permiso'] === $idAccion) {
                        $existente = $accion;
                        break;
                    }
                }

                if ($existente) {

                    (new FluentSaver($this->pdo))
                        ->table('usuario_accion')
                        ->primaryKey('idusuario_accion')
                        ->data([
                            'idusuario_accion' => $existente['idusuario_accion'],
                            'deleted_at' => null
                        ])
                        ->update();

                } else {

                    (new FluentSaver($this->pdo))
                        ->table('usuario_accion')
                        ->data([
                            'idusuario' => $idusuario,
                            'idaccion_permiso' => $idAccion,
                            'deleted_at' => null
                        ])
                        ->save();
                }
            }

            //  Desactivar acciones que ya no vienen
            foreach ($accionesActuales as $accion) {

                $idAccion = (int) $accion['idaccion_permiso'];

                if (!in_array($idAccion, $acciones)) {

                    (new FluentSaver($this->pdo))
                        ->table('usuario_accion')
                        ->primaryKey('idusuario_accion')
                        ->data([
                            'idusuario_accion' => $existente['idusuario_accion'],
                            'deleted_at' => date('Y-m-d H:i:s')
                        ])
                        ->update();
                }
            }

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


    //Implementar un método para listar los permisos marcados
    public function listarmarcados($idusuario)
    {
        return (new DBQuery($this->pdo))
            ->select('idpermiso, idsubpermiso')
            ->from('usuario_permiso')
            ->where('idusuario', '=', $idusuario)
            ->get();
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

    public function listarsubpermisos($idusuario)
    {
        $sql = "SELECT sp.idpermiso, sp.nombre 
	            FROM usuario_permiso up
	            INNER JOIN subpermiso sp ON up.idsubpermiso = sp.idsubpermiso
	            WHERE up.idusuario = '$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function listaraccionesmarcadas($idusuario)
    {
        $sql = "SELECT idaccion_permiso FROM usuario_accion WHERE idusuario = '$idusuario'";
        return ejecutarConsulta($sql);
    }
    public function listaracciones($idusuario)
    {
        $sql = "SELECT 
	                p.nombre AS modulo,
	                sp.nombre AS submodulo,
	                ap.nombre AS accion
	            FROM usuario_accion ua
	            INNER JOIN accion_permiso ap ON ua.idaccion_permiso = ap.idaccion_permiso
	            INNER JOIN subpermiso sp ON ap.idsubpermiso = sp.idsubpermiso
	            INNER JOIN permiso p ON sp.idpermiso = p.idpermiso
	            WHERE ua.idusuario = '$idusuario'";
        return ejecutarConsulta($sql);
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