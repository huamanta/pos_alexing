<?php
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";
require_once "Helpers.php";

class Empleado extends Helpers
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $imagen, $porcentaje, $salario)
	{
		$sql = "INSERT INTO personal (nombre,tipo_documento,num_documento,direccion,telefono,email,cargo,imagen,porcentaje,condicion,salario)
		VALUES ('$nombre','$tipo_documento','$num_documento','$direccion','$telefono','$email','$cargo','$imagen','$porcentaje','1','$salario')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idpersonal, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $imagen, $porcentaje, $salario)
	{
		$sql = "UPDATE personal SET nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email',cargo='$cargo',imagen='$imagen',porcentaje='$porcentaje', salario='$salario' WHERE idpersonal='$idpersonal'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para desactivar registros
	public function desactivar($idpersonal)
	{
		$sql = "UPDATE personal SET condicion='0' WHERE idpersonal='$idpersonal'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar registros
	public function activar($idpersonal)
	{
		$sql = "UPDATE personal SET condicion='1' WHERE idpersonal='$idpersonal'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idpersonal)
	{
		$sql = "SELECT * FROM personal WHERE idpersonal='$idpersonal'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql = "SELECT * FROM personal";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros y mostrar en el select
	public function select()
	{
		$sql = "SELECT * FROM personal";
		return ejecutarConsulta($sql);
	}

	public function SelectEmpleadoServicio()
	{
		$sql = "SELECT * FROM personal WHERE cargo = 'Tecnico'";
		return ejecutarConsulta($sql);
	}

	public function eventosCalendario($idpersonal)
	{
		$condition = "WHERE s.deleted_at IS NULL";
		if (!empty($idpersonal)) {
			$condition .= " AND s.idpersonal = '$idpersonal' ";
		}

		$sql = "SELECT
                s.*,
                p.nombre
            FROM seguimiento_clientes s
            INNER JOIN personal p
                ON p.idpersonal = s.idpersonal
            $condition";

		$res = ejecutarConsulta($sql);

		$data = array();

		while ($reg = $res->fetch_object()) {

			switch ($reg->tipo) {

				case 'VISITA':
					$color = '#ffc107';
					break;

				case 'LLAMADA':
					$color = '#17a2b8';
					break;

				case 'WHATSAPP':
					$color = '#28a745';
					break;

				case 'CORREO':
					$color = '#6f42c1';
					break;

				case 'COBRANZA':
					$color = '#dc3545';
					break;

				default:
					$color = '#6c757d';
					break;
			}

			$data[] = array(
				"id" => $reg->idseguimiento,
				"title" => $reg->tipo . " - " . $reg->nombre,
				"start" => $reg->fecha_proxima,
				"end" => !empty($reg->fecha_final)
					? $reg->fecha_final
					: $reg->fecha_proxima,
				"backgroundColor" => $color,
				"borderColor" => $color,
				"extendedProps" => array(
					"idseguimiento" => $reg->idseguimiento,
					"descripcion" => $reg->descripcion,
					"tipo" => $reg->tipo,
					"estado" => $reg->estado,
					"prioridad" => $reg->prioridad,
					"direccion" => $reg->direccion,
					"fecha_proxima" => $reg->fecha_proxima,
					"fecha_final" => $reg->fecha_final,
					"idventa" => $reg->idventa,
					"idcpc" => $reg->idcpc,
					"idcliente" => $reg->idcliente,
					"idpersonal" => $reg->idpersonal,
					"archivos" => $this->dataArchivosAdjuntos($reg->idseguimiento)
				)
			);
		}

		return json_encode($data);
	}

}

?>