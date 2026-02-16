<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";

Class NombresPrecios
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($descripcion)
	{
		$sql="INSERT INTO nombre_precios (descripcion,estado)
		VALUES ('$descripcion','1')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idnombre_p,$descripcion)
	{
		$sql="UPDATE nombre_precios SET descripcion='$descripcion' WHERE idnombre_p='$idnombre_p'";
		return ejecutarConsulta($sql);
	}
	//Implementamos un método para desactivar categorías
	public function desactivar($idnombre_p)
	{
		$sql="UPDATE nombre_precios SET estado='0' WHERE idnombre_p='$idnombre_p'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($idnombre_p)
	{
		$sql="UPDATE nombre_precios SET estado='1' WHERE idnombre_p='$idnombre_p'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idnombre_p)
	{
		$sql="SELECT * FROM nombre_precios WHERE idnombre_p='$idnombre_p'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql="SELECT * FROM nombre_precios";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros y mostrar en el select
	public function select()
	{
		$sql="SELECT * FROM nombre_precios where estado=1";
		return ejecutarConsulta($sql);		
	}
}

?>