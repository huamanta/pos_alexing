<?php
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";
require_once __DIR__ . "/Configuracion.php";

class Empresa extends Helpers
{
    //Implementamos nuestro constructor
    public function __construct()
    {
        parent::__construct();
    }

    //Implementamos un método para listar los registros
    public function listarEmpresas()
    {
        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('empresas')
            ->get();
        return $data;
    }

    public function guardarPrimeraSucursal(
        $ruc,
        $razon_social,
        $usuario_sol,
        $clave_sol,
        $ruta_certificado,
        $clave_certificado,
        $client_id,
        $client_secret,
        $estado_certificado,
        $nombre_impuesto,
        $monto_impuesto,
        $estado,
        $nombreComprobante,
        $serie_comprobante,
        $num_comprobante,
        $nombre,
        $direccion,
        $telefono
    ) {
        try {
            $this->pdo->beginTransaction();
            $idempresa = (new FluentSaver($this->pdo))
                ->table('empresas')
                ->data([
                    'ruc' => $ruc,
                    'razon_social' => $razon_social,
                    'usuario_sol' => $usuario_sol,
                    'clave_sol' => $clave_sol,
                    'ruta_certificado' => $ruta_certificado,
                    'clave_certificado' => $clave_certificado,
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'estado_certificado' => $estado_certificado,
                    'nombre_impuesto' => $nombre_impuesto,
                    'monto_impuesto' => $monto_impuesto,
                    'estado' => $estado
                ])
                ->save();

            if (!$idempresa) {
                throw new Exception("Error al insertar en la BD");
            }

            $num_elementos = 0;
            while ($num_elementos < count($nombreComprobante)) {
                $current_nombre = $nombreComprobante[$num_elementos];
                $current_serie = $serie_comprobante[$num_elementos];
                $current_numero = $num_comprobante[$num_elementos];
                (new FluentSaver($this->pdo))
                    ->table('comp_pago')
                    ->data([
                        'nombre' => $current_nombre,
                        'serie_comprobante' => $current_serie,
                        'num_comprobante' => $current_numero,
                        'idempresa' => $idempresa,
                        'condicion' => 1
                    ])
                    ->save();
                $num_elementos += 1;
            }

            // Obtener idempresa
            $idsucursal = self::crearSucursal(
                $nombre,
                $direccion,
                $telefono,
                '',
                '',
                '',
                '',
                $idempresa,
                'PEN',
                'S/'
            );

            $res = Helpers::dataSucursal($idsucursal);
            if (!$res) {
                throw new Exception("error al seleccionar sucursal");
            }
            $this->pdo->commit();
            $_SESSION['idsucursal'] = $res['idsucursal'];
            $_SESSION['nombre_impuesto'] = $res['nombre_impuesto'];
            $_SESSION['monto_impuesto'] = $res['monto_impuesto'];
            return Response::json([
                'success' => true,
                'message' => 'Creado correctamente'
            ]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }

    }


    public function crearSucursal($nombre, $direccion, $telefono, $distrito, $provincia, $departamento, $ubigeo, $idempresa, $moneda, $simbolo)
    {
        $idsucursal = (new FluentSaver($this->pdo))
            ->table('sucursal')
            ->data([
                'nombre' => $nombre,
                'direccion' => $direccion,
                'telefono' => $telefono,
                'distrito' => $distrito,
                'provincia' => $provincia,
                'departamento' => $departamento,
                'ubigeo' => $ubigeo,
                'idempresa' => $idempresa,
                'moneda' => $moneda,
                'simbolo' => $simbolo
            ])
            ->save();

        if (!$idsucursal) {
            throw new Exception("No se pudo guardar correctamente la sucyrasl");
        }

        return $idsucursal;
    }

    //Implementamos un método para insertar o editar registros
    public function guardaryeditar(
        $idsucursal,
        $idempresa,
        $ruc,
        $razon_social,
        $usuario_sol,
        $clave_sol,
        $ruta_certificado,
        $clave_certificado,
        $client_id,
        $client_secret,
        $estado_certificado,
        $nombre_impuesto,
        $monto_impuesto,
        $nombreComprobante,
        $serie_comprobante,
        $num_comprobante
    ) {
        try {
            $this->pdo->beginTransaction();
            $configuracion = new Configuracion();
            $ruta_certificado = $configuracion->guardarCertificado($ruta_certificado, $idsucursal);
            if (empty($idempresa)) {
                $idempresa = (new FluentSaver($this->pdo))
                    ->table('empresas')
                    ->nullable([
                        'usuario_sol',
                        'clave_sol',
                        'ruta_certificado',
                        'clave_certificado',
                        'client_id',
                        'client_secret',
                    ])
                    ->data([
                        'ruc' => $ruc,
                        'razon_social' => $razon_social,
                        'usuario_sol' => $usuario_sol,
                        'clave_sol' => $clave_sol,
                        'ruta_certificado' => $ruta_certificado,
                        'clave_certificado' => $clave_certificado,
                        'client_id' => $client_id,
                        'client_secret' => $client_secret,
                        'estado_certificado' => $estado_certificado,
                        'nombre_impuesto' => $nombre_impuesto,
                        'monto_impuesto' => $monto_impuesto,
                    ])
                    ->save();

                if (!$idempresa) {
                    throw new Exception("Error al insertar en la BD");
                }

                $num_elementos = 0;
                while ($num_elementos < count($nombreComprobante)) {
                    $current_nombre = $nombreComprobante[$num_elementos];
                    $current_serie = $serie_comprobante[$num_elementos];
                    $current_numero = $num_comprobante[$num_elementos];
                    (new FluentSaver($this->pdo))
                        ->table('comp_pago')
                        ->data([
                            'nombre' => $current_nombre,
                            'serie_comprobante' => $current_serie,
                            'num_comprobante' => $current_numero,
                            'idempresa' => $idempresa,
                            'condicion' => 1
                        ])
                        ->save();
                    $num_elementos += 1;
                }

                $this->pdo->commit();
                return Response::json([
                    "success" => true,
                    "message" => "Empresa registrada correctamente"
                ]);

            }
            $res = (new FluentSaver($this->pdo))
                ->table('empresas')
                ->primaryKey('idempresa')
                ->data([
                    'idempresa' => $idempresa,
                    'ruc' => $ruc,
                    'razon_social' => $razon_social,
                    'usuario_sol' => $usuario_sol,
                    'clave_sol' => $clave_sol,
                    'ruta_certificado' => $ruta_certificado,
                    'clave_certificado' => $clave_certificado,
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'estado_certificado' => $estado_certificado,
                    'nombre_impuesto' => $nombre_impuesto,
                    'monto_impuesto' => $monto_impuesto
                ])
                ->update();

            if (!$res) {
                throw new Exception("Error al actualizar en la BD");
            }
            $this->pdo->commit();
            return Response::json([
                "success" => true,
                "message" => "Empresa actualizada correctamente"
            ]);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return Response::error($e->getMessage());
        }
    }

    public function mostrarComprobantesEmpresa($idempresa)
    {
        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('comp_pago')
            ->where('idempresa', '=', $idempresa)
            ->get();
        return Response::json($data);
    }



    //Implementamos un método para mostrar los datos de un registro a modificar
    public function mostrarEmpresa($idempresa)
    {
        $data = (new DBQuery($this->pdo))
            ->select('*')
            ->from('empresas')
            ->where('idempresa', '=', $idempresa)
            ->first();
        return Response::json($data);
    }

    //Implementamos un método para activar o desactivar categorías
    public function activarDesactivar($idempresa, $estado)
    {
        try {
            $res = (new FluentSaver($this->pdo))
                ->table('empresas')
                ->primaryKey('idempresa')
                ->data([
                    'idempresa' => $idempresa,
                    'estado' => $estado,
                ])
                ->update();
            if (!$res) {
                throw new Exception("Error al actualizar el estado en la BD");
            }
            return Response::json([
                "success" => true,
                "message" => $estado ? "Empresa activada" : "Empresa desactivada"
            ]);
        } catch (Exception $e) {
            return Response::error($e->getMessage());
        }
    }
}
