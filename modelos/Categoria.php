<?php 
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";

Class Categoria
{
	//Implementamos nuestro constructor
	public function __construct()
	{

	}

	//Implementamos un método para insertar registros
	public function insertar($nombre)
	{
		$sql="INSERT INTO categoria (nombre,condicion)
		VALUES ('$nombre','1')";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para insertar registros
	public function insertarSucursal($nombre,$direccion,$telefono,$nombreSucursal,$serie_comprobante,$num_comprobante,$distrito,$provincia,$departamento,$ubigeo, $idempresa, $moneda, $simbolo)
	{
		$sql="INSERT INTO sucursal (nombre,direccion,telefono,distrito,provincia,departamento,ubigeo,idempresa,moneda,simbolo)
		VALUES ('$nombre','$direccion','$telefono','$distrito','$provincia','$departamento','$ubigeo','$idempresa','$moneda','$simbolo')";

		$idsucursalnew=ejecutarConsulta_retornarID($sql);

		$num_elementos=0;

		$sw=true;

		while ($num_elementos < count($nombreSucursal)) {
            // Get the current serie and numero from the arrays
            $current_nombre = $nombreSucursal[$num_elementos];
            $current_serie = $serie_comprobante[$num_elementos];

            // Query to find the maximum num_comprobante for the current comprobante type and serie
           $current_numero = $num_comprobante[$num_elementos];

            $sql="INSERT INTO comp_pago 
		      (nombre, serie_comprobante, num_comprobante, idsucursal, condicion)
		      VALUES 
		      ('$current_nombre', '$current_serie', '$current_numero', '$idsucursalnew', '1')";

				ejecutarConsulta($sql) or $sw=false;

				$num_elementos=$num_elementos+1;

		}
		
		return $sw;
		
	}

	public function insertarComprobantes($nombre,$serie_comprobante,$num_comprobante,$idsucursal)
	{

		

	}

	//Implementamos un método para editar registros
	public function editar($idcategoria,$nombre)
	{
		$sql="UPDATE categoria SET nombre='$nombre' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para editar registros
	public function editarSucursal($idsucursal,$nombre,$direccion,$telefono,$distrito,$provincia,$departamento,$ubigeo, $idempresa, $moneda, $simbolo)
	{
		$sql="UPDATE sucursal SET nombre='$nombre',direccion='$direccion',telefono='$telefono',distrito='$distrito',provincia='$provincia',departamento='$departamento',ubigeo='$ubigeo',idempresa='$idempresa',moneda='$moneda',simbolo='$simbolo' WHERE idsucursal='$idsucursal'";
		return ejecutarConsulta($sql);
	}

	//Metodos para Ubigeo
	public function listarDepartamentos()
	{
		$sql="SELECT id, name FROM ubigeo_peru_departments ORDER BY name ASC";
		return ejecutarConsulta($sql);
	}

	public function listarProvinciasPorDepartamento($id_department)
	{
		$sql="SELECT id, name FROM ubigeo_peru_provinces WHERE department_id = '$id_department' ORDER BY name ASC";
		return ejecutarConsulta($sql);
	}

	public function listarDistritosPorProvincia($id_province)
	{
		$sql="SELECT id, name FROM ubigeo_peru_districts WHERE province_id = '$id_province' ORDER BY name ASC";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para desactivar categorías
	public function desactivar($idcategoria)
	{
		$sql="UPDATE categoria SET condicion='0' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	//Implementamos un método para activar categorías
	public function activar($idcategoria)
	{
		$sql="UPDATE categoria SET condicion='1' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar($idcategoria)
	{
		$sql="SELECT * FROM categoria WHERE idcategoria='$idcategoria'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrarSucursal($idsucursal)
	{
	    $sql = "SELECT s.*, c.id_comp_pago, c.nombre AS comp_nombre, c.serie_comprobante, c.num_comprobante 
	            FROM sucursal s
	            LEFT JOIN comp_pago c ON s.idsucursal = c.idsucursal
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

	public function actualizarComprobantes($idsucursal, $nombre, $serie, $numero)
	{
	    $sql="DELETE FROM comp_pago WHERE idsucursal='$idsucursal'";
	    ejecutarConsulta($sql);

	    $num_elementos=0;
	    $sw=true;
	    while ($num_elementos < count($nombre)) {
	        $sql="INSERT INTO comp_pago (nombre,serie_comprobante,num_comprobante,idsucursal,condicion)
	              VALUES ('$nombre[$num_elementos]','$serie[$num_elementos]','$numero[$num_elementos]','$idsucursal','1')";
	        ejecutarConsulta($sql) or $sw=false;
	        $num_elementos++;
	    }
	    return $sw;
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
		$sql="SELECT * FROM categoria WHERE nombre != 'SERVICIO' ";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros
	public function listarSucursales()
	{
		$sql="SELECT * FROM sucursal";
		return ejecutarConsulta($sql);		
	}

	//Implementar un método para listar los registros y mostrar en el select
	public function select()
	{
		$sql="SELECT * FROM categoria where condicion=1";
		return ejecutarConsulta($sql);		
	}

	public function mostrarSuc($idsucursal)
	{
	    $sql = "SELECT * FROM sucursal WHERE idsucursal='$idsucursal'";
	    return ejecutarConsultaSimpleFila($sql);
	}

    public function eliminarSucursal($idsucursal)
    {
        global $conexion; // Assuming $conexion is globally available as per other functions

        // Start transaction
        $conexion->begin_transaction();
        try {
            // Delete associated comp_pago records
            $sql_comp_pago = "DELETE FROM comp_pago WHERE idsucursal='$idsucursal'";
            if (!ejecutarConsulta($sql_comp_pago)) {
                throw new Exception("Error al eliminar comprobantes de pago.");
            }

            // Delete the sucursal record
            $sql_sucursal = "DELETE FROM sucursal WHERE idsucursal='$idsucursal'";
            if (!ejecutarConsulta($sql_sucursal)) {
                throw new Exception("Error al eliminar la sucursal.");
            }

            // Commit transaction
            $conexion->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $conexion->rollback();
            // Log the error for debugging purposes, if necessary
            error_log("Error al eliminar sucursal y sus comprobantes: " . $e->getMessage());
            return false;
        }
    }

public function obtenerUltimaSerie()
{
    $sql = "SELECT LPAD(MAX(CAST(serie_comprobante AS UNSIGNED)),3,'0') AS ultima_serie
            FROM comp_pago";
    return ejecutarConsultaSimpleFila($sql);
}

	public function selectEmpresas() {
		$sql = "SELECT * FROM empresas";
		return ejecutarConsulta($sql);
	}
}

?>