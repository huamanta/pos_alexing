<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";

Class CondicionVenta
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre)
	{
		$sql="INSERT INTO condicionventa (nombre,condicion)
		VALUES ('$nombre','1')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idcondicionventa,$nombre)
	{
		$sql="UPDATE condicionventa SET nombre='$nombre' WHERE idcondicionventa='$idcondicionventa'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para desactivar categorías
	public function desactivar($idcondicionventa)
	{
		$sql="UPDATE condicionventa SET condicion='0' WHERE idcondicionventa='$idcondicionventa'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($idcondicionventa)
	{
		$sql="UPDATE condicionventa SET condicion='1' WHERE idcondicionventa='$idcondicionventa'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idcondicionventa)
	{
		$sql="SELECT * FROM condicionventa WHERE idcondicionventa='$idcondicionventa'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar()
	{
		$sql="SELECT * FROM condicionventa";
		return ejecutarConsulta($sql);		
	}
	//Implementar un método para listar los registros y mostrar en el select
	public function select()
	{
		$sql="SELECT * FROM condicionventa where condicion=1";
		return ejecutarConsulta($sql);		
	}
}

?>