<?php
//Incluímos inicialmente la conexión a la base de datos
require_once __DIR__ . "/../configuraciones/Conexion.php";
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";
class Empleado extends Helpers
{

	//Implementamos nuestro constructor
	public function __construct()
	{
		parent::__construct();
	}

	//Implementamos un método para insertar registros
	public function insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $imagen, $porcentaje, $salario)
	{
		try {
			$this->pdo->beginTransaction();
			$adjunto = (new FluentSaver($this->pdo))
				->table('personal')
				->nullable([
					'direccion',
					'telefono',
					'email',
					'cargo',
					'imagen',
					'porcentaje',
					'salario'
				])
				->data([
					'nombre' => $nombre,
					'tipo_documento' => $tipo_documento,
					'num_documento' => $num_documento,
					'direccion' => $direccion,
					'telefono' => $telefono,
					'email' => $email,
					'cargo' => $cargo,
					'imagen' => $imagen,
					'condicion' => 1,
					'porcentaje' => $porcentaje,
					'salario' => $salario
				])
				->save();

			if (!$adjunto) {
				throw new Exception("Error al guardar el personal.");
			}
			$this->pdo->commit();
			return json_encode([
				"success" => true,
				"message" => "Personal guardo correctamente"
			]);
		} catch (Exception $e) {

			if ($this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}

			return json_encode([
				"success" => false,
				"message" => $e->getMessage()
			]);
		}

	}

	//Implementamos un método para editar registros
	public function editar($idpersonal, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $imagen, $porcentaje, $salario)
	{
		try {
			$this->pdo->beginTransaction();
			$adjunto = (new FluentSaver($this->pdo))
				->table('personal')
				->primaryKey('idpersonal')
				->nullable([
					'direccion',
					'telefono',
					'email',
					'cargo',
					'imagen',
					'porcentaje',
					'salario'
				])
				->data([
					'idpersonal' => $idpersonal,
					'nombre' => $nombre,
					'tipo_documento' => $tipo_documento,
					'num_documento' => $num_documento,
					'direccion' => $direccion,
					'telefono' => $telefono,
					'email' => $email,
					'cargo' => $cargo,
					'imagen' => $imagen,
					'porcentaje' => $porcentaje,
					'salario' => $salario
				])
				->update();

			if (!$adjunto) {
				throw new Exception("Error al actualizar el personal.");
			}
			$this->pdo->commit();
			return json_encode([
				"success" => true,
				"message" => "Personal actualizado correctamente"
			]);
		} catch (Exception $e) {

			if ($this->pdo->inTransaction()) {
				$this->pdo->rollBack();
			}

			return json_encode([
				"success" => false,
				"message" => $e->getMessage()
			]);

		}
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
		$sql = "SELECT * FROM personal ORDER BY idpersonal DESC";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros y mostrar en el select
	public function select($idusuario, $idpersonal, $only_personal = false)
	{
		if ($only_personal) {
			return ejecutarConsulta("SELECT * FROM personal WHERE idpersonal = $idpersonal");
		}

		$usuario = ejecutarConsultaSimpleFila("SELECT idpersonal, superusuario FROM usuario WHERE idusuario = $idusuario");

		if (!$usuario) {
			return ejecutarConsulta("SELECT * FROM personal WHERE 1=0");
		}

		// Superusuario ve todo
		if ((int) $usuario['superusuario'] === 1) {
			$sql = "SELECT * FROM personal";
		} else {
			$idpersonal = (int) $usuario['idpersonal'];
			$sql = "SELECT * FROM personal WHERE idpersonal = $idpersonal";
		}
		return ejecutarConsulta($sql);
	}


	public function SelectEmpleadoServicio()
	{
		$sql = "SELECT * FROM personal WHERE cargo = 'Tecnico'";
		return ejecutarConsulta($sql);
	}

	// public function eventosCalendario($idpersonal)
	// {

	// 	$condition = "WHERE s.deleted_at IS NULL";
	// 	if (!empty($idpersonal)) {
	// 		$condition .= " AND s.idpersonal = '$idpersonal' ";
	// 	}

	// 	$sql = "SELECT
	//             s.*,
	//             p.nombre
	//         FROM seguimiento_clientes s
	//         INNER JOIN personal p
	//             ON p.idpersonal = s.idpersonal
	//         $condition";

	// 	$res = ejecutarConsulta($sql);

	// 	$data = array();

	// 	while ($reg = $res->fetch_object()) {

	// 		switch ($reg->tipo) {

	// 			case 'VISITA':
	// 				$color = '#ffc107';
	// 				break;

	// 			case 'LLAMADA':
	// 				$color = '#17a2b8';
	// 				break;

	// 			case 'WHATSAPP':
	// 				$color = '#28a745';
	// 				break;

	// 			case 'CORREO':
	// 				$color = '#6f42c1';
	// 				break;

	// 			case 'COBRANZA':
	// 				$color = '#dc3545';
	// 				break;

	// 			default:
	// 				$color = '#6c757d';
	// 				break;
	// 		}

	// 		$data[] = array(
	// 			"id" => $reg->idseguimiento,
	// 			"title" => $reg->tipo . " - " . $reg->nombre,
	// 			"start" => $reg->fecha_proxima,
	// 			"end" => !empty($reg->fecha_final)
	// 				? $reg->fecha_final
	// 				: $reg->fecha_proxima,
	// 			"backgroundColor" => $color,
	// 			"borderColor" => $color,
	// 			"extendedProps" => array(
	// 				"idseguimiento" => $reg->idseguimiento,
	// 				"descripcion" => $reg->descripcion,
	// 				"tipo" => $reg->tipo,
	// 				"estado" => $reg->estado,
	// 				"prioridad" => $reg->prioridad,
	// 				"direccion" => $reg->direccion,
	// 				"fecha_proxima" => $reg->fecha_proxima,
	// 				"fecha_final" => $reg->fecha_final,
	// 				"idventa" => $reg->idventa,
	// 				"idcpc" => $reg->idcpc,
	// 				"idcliente" => $reg->idcliente,
	// 				"idpersonal" => $reg->idpersonal,
	// 				"archivos" => $this->dataArchivosAdjuntos($reg->idseguimiento)
	// 			)
	// 		);
	// 	}

	// 	return json_encode($data);
	// }


	public function eventosCalendario($idusuario)
	{

		$isAdmin = Helpers::esSuperusuario($idusuario);
		$query = (new DBQuery($this->pdo))
			->select([
				's.*',
				'p.nombre'
			])
			->from('seguimiento_clientes s')
			->join('personal p','p.idpersonal = s.idpersonal')
			->leftJoin('usuario u', 'u.idpersonal = p.idpersonal')
			->whereNull('s.deleted_at');

		if (!$isAdmin) {
			$query->where('u.idusuario', '=', $idusuario);
		}

		$registros = $query->get();

		$data = [];

		foreach ($registros as $reg) {

			switch ($reg['tipo']) {

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

			$data[] = [
				'id' => $reg['idseguimiento'],
				'title' => $reg['tipo'] . ' - ' . $reg['nombre'],
				'start' => $reg['fecha_proxima'],
				'end' => !empty($reg['fecha_final'])
					? $reg['fecha_final']
					: $reg['fecha_proxima'],

				'backgroundColor' => $color,
				'borderColor' => $color,

				'extendedProps' => [
					'idseguimiento' => $reg['idseguimiento'],
					'descripcion' => $reg['descripcion'],
					'tipo' => $reg['tipo'],
					'estado' => $reg['estado'],
					'prioridad' => $reg['prioridad'],
					'direccion' => $reg['direccion'],
					'fecha_proxima' => $reg['fecha_proxima'],
					'fecha_final' => $reg['fecha_final'],
					'idventa' => $reg['idventa'],
					'idcpc' => $reg['idcpc'],
					'idcliente' => $reg['idcliente'],
					'idpersonal' => $reg['idpersonal'],
					'archivos' => $this->dataArchivosAdjuntos(
						$reg['idseguimiento']
					)
				]
			];
		}

		return Response::json($data);
	}

}

?>