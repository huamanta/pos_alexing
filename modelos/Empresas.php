<?php

require "../configuraciones/Conexion.php";

class Empresa
{
    //Implementamos nuestro constructor
    public function __construct()
    {
    }

    //Implementamos un método para listar los registros
    public function listarEmpresas()
    {
        $sql="SELECT * FROM empresas";
		return ejecutarConsulta($sql);	
    }

    //Implementamos un método para insertar o editar registros
    public function guardaryeditar($idempresa, $ruc, $razon_social, $usuario_sol, $clave_sol, $ruta_certificado, $clave_certificado, $client_id, $client_secret, $estado_certificado, $nombre_impuesto, $monto_impuesto, $estado)
    {
        try {

            if (empty($idempresa)) {

                $sql = "INSERT INTO empresas (ruc, razon_social, usuario_sol, clave_sol, ruta_certificado, clave_certificado, client_id, client_secret, estado_certificado, nombre_impuesto, monto_impuesto, estado)
                VALUES ('$ruc', '$razon_social', '$usuario_sol', '$clave_sol', '$ruta_certificado', '$clave_certificado', '$client_id', '$client_secret', '$estado_certificado', '$nombre_impuesto', '$monto_impuesto', '$estado')";

                $res = ejecutarConsulta($sql);

                if (!$res) {
                    throw new Exception("Error al insertar en la BD");
                }

                return [
                    "status" => "success",
                    "code" => 200,
                    "message" => "Empresa registrada"
                ];

            } else {

                $sql = "UPDATE empresas SET 
                    ruc='$ruc',
                    razon_social='$razon_social',
                    usuario_sol='$usuario_sol',
                    clave_sol='$clave_sol',
                    ruta_certificado='$ruta_certificado',
                    clave_certificado='$clave_certificado',
                    client_id='$client_id',
                    client_secret='$client_secret',
                    estado_certificado='$estado_certificado',
                    nombre_impuesto='$nombre_impuesto',
                    monto_impuesto='$monto_impuesto',
                    estado='$estado'
                    WHERE idempresa='$idempresa'";

                $res = ejecutarConsulta($sql);

                if (!$res) {
                    throw new Exception("Error al actualizar en la BD");
                }

                return [
                    "status" => "success",
                    "code" => 200,
                    "message" => "Empresa actualizada"
                ];
            }

        } catch (Exception $e) {

            return [
                "status" => "error",
                "code" => 500,
                "message" => $e->getMessage()
            ];
        }
    }



    //Implementamos un método para mostrar los datos de un registro a modificar
    public function mostrarEmpresa($idempresa)
    {
        $sql="SELECT * FROM empresas WHERE idempresa='$idempresa'";
        return ejecutarConsultaSimpleFila($sql);
    }

    //Implementamos un método para activar o desactivar categorías
    public function activarDesactivar($idempresa, $estado)
    {
        try {
            $sql = "UPDATE empresas SET estado='$estado' WHERE idempresa='$idempresa'";
            $res = ejecutarConsulta($sql);
            if (!$res) {
                throw new Exception("Error al actualizar el estado en la BD");
            }
            return [
                "status" => "success",
                "code" => 200,
                "message" => $estado ? "Empresa activada" : "Empresa desactivada"
            ];
        } catch (Exception $e) {
            return [
                "status" => "error",
                "code" => 500,
                "message" => $e->getMessage()
            ];
        }
    }
}
    