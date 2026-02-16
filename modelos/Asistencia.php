<?php
// Incluimos la conexión a la base de datos
require "../configuraciones/Conexion.php";
class Asistencia
{
    // Constructor
    public function __construct() {}

    // Método para insertar nueva asistencia
   public function insertar($idpersonal, $fecha, $hora_entrada, $hora_salida, $estado, $hora_tardanza, $permiso, $vacaciones, $monto)
{
    if ($permiso == "si") {
        $estado = "falto";
    }

    if ($estado == "asistio" && empty($hora_salida)) {
        return false;
    }

    if ($estado != "falto" && (empty($idpersonal) || empty($fecha) || empty($hora_entrada) || empty($hora_salida))) {
        return false;
    }

    if ($estado == "falto") {
        $hora_entrada = null;
        $hora_salida = null;
        $hora_tardanza = null;
        $monto = 0;
    }

    // Insertar asistencia con MONTO
    $sql = "INSERT INTO asistencias (idpersonal, fecha, hora_entrada, hora_salida, estado, tardanza, permiso, vacaciones, monto)
            VALUES ('$idpersonal', '$fecha', '$hora_entrada', '$hora_salida', '$estado', '$hora_tardanza', '$permiso', '$vacaciones', '$monto')";
    
    $idasistencia = ejecutarConsulta_retornarID($sql);

    if ($idasistencia && $estado == "asistio" && $monto > 0) {

        // Registrar el pago del día
        $sqlPago = "INSERT INTO pagos_asistencia (idasistencia, idpersonal, fecha, monto_pago, observacion)
                    VALUES ('$idasistencia', '$idpersonal', '$fecha', '$monto', 'Pago automático por asistencia')";
        ejecutarConsulta($sqlPago);
    }

    return $idasistencia;
}

    // Método para editar asistencia
    public function editar($idasistencia, $idpersonal, $fecha, $hora_entrada, $hora_salida, $estado, $hora_tardanza, $permiso, $vacaciones, $monto)
    {
        $sql = "UPDATE asistencias SET idpersonal = '$idpersonal', fecha = '$fecha', hora_entrada = '$hora_entrada', hora_salida = '$hora_salida', estado = '$estado',
                tardanza = '$hora_tardanza', permiso = '$permiso', vacaciones = '$vacaciones', monto = '$monto'
                WHERE idasistencia = '$idasistencia'";
        return ejecutarConsulta($sql);
    }

    // Método para comprobar si ya existe una asistencia para un personal en una fecha
    public function existeAsistencia($idpersonal, $fecha)
    {
        $sql = "SELECT * FROM asistencias WHERE idpersonal = '$idpersonal' AND fecha = '$fecha'";
        $query = ejecutarConsultaSimpleFila($sql); // Asumiendo que ejecutarConsultaSimpleFila devuelve una fila si existe el registro
        return $query;
    }

    public function obtenerAsistencia($idpersonal, $fecha)
    {
        $sql = "SELECT * FROM asistencias WHERE idpersonal = '$idpersonal' AND fecha = '$fecha'";
        return ejecutarConsultaSimpleFila($sql);  // Asumiendo que este método retorna un solo registro
    }

    public function obtenerAsistenciaPorId($idasistencia)
    {
        $sql = "SELECT * FROM asistencias WHERE idasistencia = '$idasistencia'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Otros métodos
    public function listarpersonal($fecha = null)
    {
        // Si no se pasa fecha, usamos hoy en Lima (zona horaria del servidor)
        $hoy = $fecha ?: date('Y-m-d');

        $sql = "SELECT
                    p.idpersonal,
                    p.nombre,
                    p.tipo_documento,
                    p.num_documento,
                    p.telefono,
                    p.email,
                    p.imagen,
                    p.condicion,
                    -- Si no hay asistencia para hoy, traerá cadena vacía
                    IFNULL(a.idasistencia, '') AS idasistencia
                FROM personal p
                LEFT JOIN asistencias a
                  ON p.idpersonal = a.idpersonal
                 AND a.fecha = '$hoy' 
                 WHERE p.cargo != 'Administrador'
                ORDER BY p.nombre";
        return ejecutarConsulta($sql);
    }

    public function listarHistorialAsistencias($fecha_inicio, $fecha_fin) {
        $sql = "SELECT
                    a.idasistencia,
                    p.idpersonal,
                    p.nombre,
                    a.fecha,
                    a.hora_entrada,
                    a.hora_salida,
                    a.tardanza,
                    a.permiso,
                    a.vacaciones,
                    p.salario,
                    a.monto,
                    a.estado,
                    a.estado_pago,
                    IF(a.hora_salida IS NULL, 0, 
                        IF(TIMESTAMPDIFF(HOUR, a.hora_entrada, a.hora_salida) < 0, 0, TIMESTAMPDIFF(HOUR, a.hora_entrada, a.hora_salida))) AS horas_trabajadas,
                    p.salario,
                    (p.salario / (7 * 8 * 4)) AS costo_por_hora,
                    -- Cálculo de los minutos de retraso (tardanza - hora_entrada)
                    IF(a.tardanza IS NOT NULL AND a.hora_entrada IS NOT NULL, 
                        TIMESTAMPDIFF(MINUTE, a.hora_entrada, a.tardanza), 
                        0) AS minutos_retraso,
                    -- Cálculo de horas y minutos de retraso
                    CASE
                        WHEN TIMESTAMPDIFF(MINUTE, a.hora_entrada, a.tardanza) < 60 THEN 
                            CONCAT(TIMESTAMPDIFF(MINUTE, a.hora_entrada, a.tardanza), ' minuto(s)')
                        ELSE
                            CONCAT(
                                FLOOR(TIMESTAMPDIFF(MINUTE, a.hora_entrada, a.tardanza) / 60), ' hora(s) ',
                                MOD(TIMESTAMPDIFF(MINUTE, a.hora_entrada, a.tardanza), 60), ' minuto(s)'
                            )
                    END AS retraso_formateado
                FROM asistencias a
                INNER JOIN personal p ON a.idpersonal = p.idpersonal
                WHERE DATE(a.fecha) >= '$fecha_inicio' AND DATE(a.fecha) <= '$fecha_fin'
                ORDER BY a.fecha DESC";
        return ejecutarConsulta($sql);
    }

    public function resumenPorPersonal($fecha_inicio, $fecha_fin) {
  $sql = "SELECT 
            p.idpersonal,
            p.nombre,
            p.num_documento,
            COUNT(a.idasistencia) AS total_asistencias,
            SEC_TO_TIME(SUM(TIMESTAMPDIFF(SECOND, a.hora_entrada, a.hora_salida))) AS total_horas,
            COUNT(IF(a.tardanza IS NOT NULL AND a.tardanza != '00:00:00', 1, NULL)) AS tardanzas,
            COUNT(IF(a.permiso = 'si', 1, NULL)) AS permisos,
            COUNT(IF(a.vacaciones = 'si', 1, NULL)) AS vacaciones,
            GROUP_CONCAT(DATE(a.fecha) ORDER BY a.fecha ASC) AS dias_asistidos,
            GROUP_CONCAT(TIMESTAMPDIFF(SECOND, a.hora_entrada, a.hora_salida)) AS horas_segundos
          FROM asistencias a
          INNER JOIN personal p ON a.idpersonal = p.idpersonal
          WHERE DATE(a.fecha) BETWEEN '$fecha_inicio' AND '$fecha_fin' AND a.estado = 'asistio'
          GROUP BY a.idpersonal";
  return ejecutarConsulta($sql);
}

public function guardarMontoDia($idpersonal, $fecha, $monto)
{
    $sql = "UPDATE asistencias 
            SET monto = '$monto'
            WHERE idpersonal = '$idpersonal' AND fecha = '$fecha'";
    return ejecutarConsulta($sql);
}

public function registrarPagoDia($idasistencia, $idpersonal, $fecha, $monto_pago, $observacion)
{
    $sql = "INSERT INTO pagos_asistencia (idasistencia, idpersonal, fecha, monto_pago, observacion)
            VALUES ('$idasistencia', '$idpersonal', '$fecha', '$monto_pago', '$observacion')";

    return ejecutarConsulta($sql);
}
    public function listarPagosPorFechas($desde, $hasta)
    {
        $sql = "SELECT 
                    p.nombre AS trabajador,
                    pa.fecha,
                    pa.monto_pago,
                    pa.observacion
                FROM pagos_asistencia pa
                INNER JOIN personal p ON p.idpersonal = pa.idpersonal
                WHERE pa.fecha BETWEEN '$desde' AND '$hasta'
                ORDER BY pa.fecha ASC";

        return ejecutarConsulta($sql);
    }
    
    // Método para eliminar una asistencia por su ID
    public function eliminar($idasistencia)
    {
        // Primero, eliminar los pagos asociados a esta asistencia
        $sqlDeletePagos = "DELETE FROM pagos_asistencia WHERE idasistencia = '$idasistencia'";
        ejecutarConsulta($sqlDeletePagos);

        // Luego, eliminar la asistencia
        $sql = "DELETE FROM asistencias WHERE idasistencia = '$idasistencia'";
        return ejecutarConsulta($sql);
    }

    // Método para eliminar múltiples asistencias
    public function eliminarMultiple($idasistencias)
    {
        // Convertir el array de IDs a una cadena para la consulta SQL
        $ids = implode(",", array_map('intval', $idasistencias));

        // Primero, eliminar los pagos asociados a estas asistencias
        $sqlDeletePagos = "DELETE FROM pagos_asistencia WHERE idasistencia IN ($ids)";
        ejecutarConsulta($sqlDeletePagos);

        // Luego, eliminar las asistencias
        $sql = "DELETE FROM asistencias WHERE idasistencia IN ($ids)";
        return ejecutarConsulta($sql);
    }

    public function eliminarAsis($idasistencia)
    {
        ejecutarConsulta("DELETE FROM pagos_asistencia WHERE idasistencia='$idasistencia'");
        return ejecutarConsulta("DELETE FROM asistencias WHERE idasistencia='$idasistencia'");
    }
}

?>
