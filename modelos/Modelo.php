<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";

Class Modelo
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre, $descripcion)
	{
		$sql="INSERT INTO modelo (nombre, descripcion, estado)
		VALUES ('$nombre', '$descripcion', '1')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idmodelo,$nombre, $descripcion)
	{
		$sql="UPDATE modelo SET nombre='$nombre', descripcion='$descripcion' WHERE idmodelo='$idmodelo'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para desactivar modelos
	public function desactivar($idmodelo)
	{
		$sql="UPDATE modelo SET estado='0' WHERE idmodelo='$idmodelo'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar modelos
	public function activar($idmodelo)
	{
		$sql="UPDATE modelo SET estado='1' WHERE idmodelo='$idmodelo'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idmodelo)
	{
		$sql="SELECT * FROM modelo WHERE idmodelo='$idmodelo'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql="SELECT * FROM modelo";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros y mostrar en el select
	public function select()
	{
		$sql="SELECT * FROM modelo where estado=1";
		return ejecutarConsulta($sql);		
	}
}

?>