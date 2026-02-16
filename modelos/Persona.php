<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";

Class Persona
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($tipo_persona,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$fecha_hora)
	{
		$sql="INSERT INTO persona (tipo_persona,nombre,tipo_documento,num_documento,direccion,telefono,email,fecha)
		VALUES ('$tipo_persona','$nombre','$tipo_documento','$num_documento','$direccion','$telefono','$email','$fecha_hora')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editar($idpersona,$tipo_persona,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$fecha_hora)
	{
		$sql="UPDATE persona SET tipo_persona='$tipo_persona',nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email', fecha='$fecha_hora' WHERE idpersona='$idpersona'";
		return ejecutarConsulta($sql);
	}


	//Implementamos un método para eliminar categorías
	public function eliminar($idpersona)
	{

		$clienteExiste = "SELECT * FROM venta v WHERE v.idcliente = '$idpersona'";

		$existeCliente = ejecutarConsulta($clienteExiste);

		$var = 0;

		while ($reg = $existeCliente->fetch_object()) {

			$var = $reg->idventa;
		}

		if($var > 0){

			$sql = 2;

		}else{
			
			$sql="DELETE FROM persona WHERE idpersona='$idpersona'";
			ejecutarConsulta($sql);
			$sql = 1;

		}

		return $sql;
	}

	//Implementamos un método para eliminar categorías
	public function eliminar2($idpersona)
	{

		$clienteExiste = "SELECT * FROM compra c WHERE c.idproveedor = '$idpersona'";

		$existeCliente = ejecutarConsulta($clienteExiste);

		$var = 0;

		while ($reg = $existeCliente->fetch_object()) {

			$var = $reg->idcompra;
		}

		if($var > 0){

			$sql = 2;

		}else{
			
			$sql="DELETE FROM persona WHERE idpersona='$idpersona'";
			ejecutarConsulta($sql);
			$sql = 1;

		}

		return $sql;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idpersona)
	{
		$sql="SELECT * FROM persona WHERE idpersona='$idpersona'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listarp()
	{
		$sql="SELECT * FROM persona WHERE tipo_persona='Proveedor' OR isproveedor = 1";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros 
public function listarc($tipo_documento = "")
{
    $sql = "SELECT * FROM persona WHERE tipo_persona='Cliente'";
    if (!empty($tipo_documento)) {
        $sql .= " AND tipo_documento='" . $tipo_documento . "'";
    }
    return ejecutarConsulta($sql);		
}

	public function obtenerPorId($idcliente)
	{
	    $sql = "SELECT nombre FROM persona WHERE idpersona = '$idcliente' AND tipo_persona = 'Cliente'";
	    return ejecutarConsulta($sql);
	}

	public function listarc2($numero)
	{
		$sql="SELECT * FROM persona WHERE num_documento='$numero' AND tipo_persona = 'Cliente'";
		return ejecutarConsultaSimpleFila($sql);		
	}

	public function listarc3($numero)
	{
		$sql="SELECT * FROM persona WHERE num_documento='$numero' AND tipo_persona = 'Proveedor'";
		return ejecutarConsultaSimpleFila($sql);		
	}

	//Implementar un método para listar los registros 
	public function listarv()
	{
	    $sql = "SELECT DISTINCT p.idpersonal, p.nombre
	            FROM personal p
	            INNER JOIN usuario u ON u.idpersonal = p.idpersonal
	            INNER JOIN usuario_sucursal us ON us.idusuario = u.idusuario
	            WHERE p.condicion = 1
	              AND u.condicion = 1";
	    return ejecutarConsulta($sql);
	}


	// Método para obtener el cargo del personal
	public function obtenerCargo($idusuario)
	{
	    $sql = "SELECT p.cargo 
            FROM personal p
            INNER JOIN usuario u ON u.idpersonal = p.idpersonal
            WHERE u.idusuario = '$idusuario'";
	    $query = ejecutarConsulta($sql);

	    if ($query && $reg = $query->fetch_object()) {
	        return $reg->cargo;
	    } else {
	        return 'jj'; 
	    }
	}

	public function listarvPorSucursal($idsucursal)
	{
	    $sql = "SELECT DISTINCT p.idpersonal, p.nombre
	            FROM personal p
	            INNER JOIN usuario u ON u.idpersonal = p.idpersonal
	            INNER JOIN usuario_sucursal us ON us.idusuario = u.idusuario
	            WHERE us.idsucursal = '$idsucursal'
	              AND p.condicion = 1
	              AND u.condicion = 1";

	    return ejecutarConsulta($sql);
	}


	public function listarProveedor()
	{
		$sql="SELECT * FROM persona WHERE tipo_persona = 'proveedor'";
		return ejecutarConsulta($sql);		
	}

}

?>