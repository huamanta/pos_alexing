<?php
require_once "../configuraciones/Conexion.php";

class Permiso
{
    public function insertar($nombre)
    {
        $sql = "INSERT INTO permiso (nombre) VALUES ('$nombre')";
        return ejecutarConsulta($sql);
    }

    public function editar($idpermiso, $nombre)
    {
        $sql = "UPDATE permiso SET nombre = '$nombre' WHERE idpermiso = '$idpermiso'";
        return ejecutarConsulta($sql);
    }

    public function eliminar($idpermiso)
    {
        $sql = "DELETE FROM permiso WHERE idpermiso = '$idpermiso'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($idpermiso)
    {
        $sql = "SELECT * FROM permiso WHERE idpermiso = '$idpermiso'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar()
    {
        $sql = "SELECT * FROM permiso";
        return ejecutarConsulta($sql);
    }
    // ================= SUBPERMISOS ==================

	// Insertar subpermiso
	public function insertarSubpermiso($idpermiso, $nombre) {
        $sql = "INSERT INTO subpermiso (idpermiso, nombre) VALUES ('$idpermiso', '$nombre')";
        return ejecutarConsulta($sql) ? "Subpermiso registrado" : "Error al registrar";
    }

    public function listarSubpermiso($idpermiso) {
        $sql = "SELECT s.idsubpermiso, s.nombre, p.nombre as modulo
                FROM subpermiso s
                INNER JOIN permiso p ON s.idpermiso = p.idpermiso
                WHERE s.idpermiso = '$idpermiso'";
        return ejecutarConsulta($sql);
    }

    public function eliminarSubpermiso($idsubpermiso) {
        $sql = "DELETE FROM subpermiso WHERE idsubpermiso = '$idsubpermiso'";
        return ejecutarConsulta($sql) ? "Subpermiso eliminado" : "Error al eliminar";
    }

    // ================= ACCIONES ==================

    public function insertarAccion($idsubpermiso, $nombre, $descripcion = '')
    {
        $sql = "INSERT INTO accion_permiso (idsubpermiso, nombre, descripcion) 
                VALUES ('$idsubpermiso', '$nombre', '$descripcion')";
        return ejecutarConsulta($sql) ? "Acción registrada" : "Error al registrar acción";
    }

    public function listarAcciones($idsubpermiso)
    {
        $sql = "SELECT * FROM accion_permiso WHERE idsubpermiso = '$idsubpermiso'";
        return ejecutarConsulta($sql);
    }

    public function eliminarAccion($idaccion_permiso)
    {
        // Primero eliminar dependencias en usuario_accion
        $sql1 = "DELETE FROM usuario_accion WHERE idaccion_permiso = '$idaccion_permiso'";
        $ok1 = ejecutarConsulta($sql1);

        // Luego eliminar de accion_permiso
        $sql2 = "DELETE FROM accion_permiso WHERE idaccion_permiso = '$idaccion_permiso'";
        $ok2 = ejecutarConsulta($sql2);

        return ($ok1 && $ok2) ? "Acción eliminada" : "Error al eliminar acción";
    }



}
?>
