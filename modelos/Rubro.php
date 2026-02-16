<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";

Class Rubro
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre)
	{
		$sql="INSERT INTO rubro (nombre,condicion)
		VALUES ('$nombre','1')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idrubro,$nombre)
	{
		$sql="UPDATE rubro SET nombre='$nombre' WHERE idrubro='$idrubro'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para desactivar categorías
	public function desactivar($idrubro)
	{
		$sql="UPDATE rubro SET condicion='0' WHERE idrubro='$idrubro'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($idrubro)
	{
		$sql="UPDATE rubro SET condicion='1' WHERE idrubro='$idrubro'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idrubro)
	{
		$sql="SELECT * FROM rubro WHERE idrubro='$idrubro'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql="SELECT * FROM rubro";
		return ejecutarConsulta($sql);		
	}
	//Implementar un método para listar los registros y mostrar en el select
	public function select()
	{
		$sql="SELECT * FROM rubro where condicion=1";
		return ejecutarConsulta($sql);		
	}
}

?>