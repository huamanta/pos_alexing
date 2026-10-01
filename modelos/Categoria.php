<?php
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";
require_once __DIR__ . "/../core/Response.php";
require_once __DIR__ . "/Helpers.php";

class Categoria extends Helpers
{
	//Implementamos nuestro constructor
	public function __construct()
	{
		parent::__construct();
	}

	//Implementamos un método para insertar registros
	public function insertar($nombre)
	{
		$sql = "INSERT INTO categoria (nombre,condicion)
		VALUES ('$nombre','1')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para insertar registros
	public function insertarSucursal($nombre, $direccion, $telefono, $distrito, $provincia, $departamento, $ubigeo, $idempresa, $moneda, $simbolo)
	{
		$idempresa_value = $idempresa ? "'$idempresa'" : "NULL";
		$sql = "INSERT INTO sucursal (nombre,direccion,telefono,distrito,provincia,departamento,ubigeo,idempresa,moneda,simbolo)
		VALUES ('$nombre','$direccion','$telefono','$distrito','$provincia','$departamento','$ubigeo',$idempresa_value,'$moneda','$simbolo')";

		$idsucursalnew = ejecutarConsulta_retornarID($sql);

		return $idsucursalnew;
	}

	//Implementamos un método para editar registros
	public function editar($idcategoria, $nombre)
	{
		$sql = "UPDATE categoria SET nombre='$nombre' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editarSucursal($idsucursal, $nombre, $direccion, $telefono, $distrito, $provincia, $departamento, $ubigeo, $idempresa, $moneda, $simbolo)
	{
		$sql = "UPDATE sucursal SET nombre='$nombre',direccion='$direccion',telefono='$telefono',distrito='$distrito',provincia='$provincia',departamento='$departamento',ubigeo='$ubigeo',idempresa='$idempresa',moneda='$moneda',simbolo='$simbolo' WHERE idsucursal='$idsucursal'";
		return ejecutarConsulta($sql);
	}

	//Metodos para Ubigeo
	public function listarDepartamentos()
	{
		$sql = "SELECT id, name FROM ubigeo_peru_departments ORDER BY name ASC";
		return ejecutarConsulta($sql);
	}

	public function listarProvinciasPorDepartamento($id_department)
	{
		$sql = "SELECT id, name FROM ubigeo_peru_provinces WHERE department_id = '$id_department' ORDER BY name ASC";
		return ejecutarConsulta($sql);
	}

	public function listarDistritosPorProvincia($id_province)
	{
		$sql = "SELECT id, name FROM ubigeo_peru_districts WHERE province_id = '$id_province' ORDER BY name ASC";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para desactivar categorías
	public function desactivar($idcategoria)
	{
		$sql = "UPDATE categoria SET condicion='0' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($idcategoria)
	{
		$sql = "UPDATE categoria SET condicion='1' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idcategoria)
	{
		$sql = "SELECT * FROM categoria WHERE idcategoria='$idcategoria'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrarSucursal($idsucursal)
	{
		$sql = "SELECT *
	            FROM sucursal s
	            WHERE s.idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql);
	}

	public function mostrarSucursalExcel($idsucursal)
	{
		$sql = "SELECT nombre, direccion, telefono, distrito
            FROM sucursal
            WHERE idsucursal = '$idsucursal'";

		return ejecutarConsultaSimpleFila($sql);
	}


	public function mostrarSucursalTi($idsucursal)
	{
		$sql = "SELECT nombre, direccion, telefono, distrito
            FROM sucursal
            WHERE idsucursal = '$idsucursal'";
		return ejecutarConsulta($sql); // 👈 No ejecutarConsultaSimpleFila
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql = "SELECT * FROM categoria WHERE nombre != 'SERVICIO' ";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function listarSucursales()
	{
		$sql = "SELECT * FROM sucursal";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros y mostrar en el select
	public function select()
	{
		$sql = "SELECT * FROM categoria where condicion=1";
		return ejecutarConsulta($sql);
	}

	public function mostrarSuc($idsucursal)
	{
		$data = (new DBQuery($this->pdo))
			->select('*')
			->from('sucursal')
			->where('idsucursal', '=', $idsucursal)
			->first();
		return Response::json($data);
	}

	public function eliminarSucursal($idsucursal)
	{
		try {
            $deleted = (new FluentSaver($this->pdo))
                ->table('sucursal')
                ->primaryKey('idsucursal')
                ->softDelete($idsucursal);

            if (!$deleted) {
                throw new Exception("No se pudo eliminar el registro");
            }

            return Response::json([
                "success" => true,
                "message" => "Registro eliminado correctamente"
            ]);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
	}

	public function obtenerUltimaSerie()
	{
		$sql = "SELECT LPAD(MAX(CAST(serie_comprobante AS UNSIGNED)),3,'0') AS ultima_serie
            FROM comp_pago";
		return ejecutarConsultaSimpleFila($sql);
	}


	public function selectEmpresas()
	{
		$sql = "SELECT * FROM empresas";
		return ejecutarConsulta($sql);
	}
}

?>