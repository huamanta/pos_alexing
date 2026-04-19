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
	public function insertarSucursal($nombre,$direccion,$telefono, $distrito,$provincia,$departamento,$ubigeo, $idempresa, $moneda, $simbolo)
	{
		$idempresa_value = $idempresa ? "'$idempresa'" : "NULL";
		$sql="INSERT INTO sucursal (nombre,direccion,telefono,distrito,provincia,departamento,ubigeo,idempresa,moneda,simbolo)
		VALUES ('$nombre','$direccion','$telefono','$distrito','$provincia','$departamento','$ubigeo',$idempresa_value,'$moneda','$simbolo')";

		$idsucursalnew=ejecutarConsulta_retornarID($sql);

		return $idsucursalnew;

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
	            LEFT JOIN comp_pago c ON (s.idempresa = c.idempresa OR (c.idempresa IS NULL AND s.idsucursal = c.idsucursal))
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
	    $empresa = ejecutarConsultaSimpleFila("SELECT idempresa FROM sucursal WHERE idsucursal='$idsucursal'");
	    $fk_column = ($empresa && $empresa['idempresa']) ? 'idempresa' : 'idsucursal';
	    $fk_value = ($empresa && $empresa['idempresa']) ? $empresa['idempresa'] : $idsucursal;

	    $sql="DELETE FROM comp_pago WHERE $fk_column='$fk_value'";
	    ejecutarConsulta($sql);

	    $num_elementos=0;
	    $sw=true;
	    while ($num_elementos < count($nombre)) {
	        $sql="INSERT INTO comp_pago (nombre,serie_comprobante,num_comprobante,$fk_column,condicion)
	              VALUES ('$nombre[$num_elementos]','$serie[$num_elementos]','$numero[$num_elementos]','$fk_value','1')";
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
            // Delete the sucursal record only; comp_pago es por empresa y no debe eliminarse al borrar una sucursal
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
            error_log("Error al eliminar sucursal: " . $e->getMessage());
            return false;
        }
    }

public function obtenerUltimaSerie()
{
    $sql = "SELECT LPAD(MAX(CAST(serie_comprobante AS UNSIGNED)),3,'0') AS ultima_serie
            FROM comp_pago";
    return ejecutarConsultaSimpleFila($sql);
}

	public function mostrarComprobantesEmpresa($idempresa)
	{
	    $sql = "SELECT * FROM comp_pago WHERE idempresa = '$idempresa'";
	    return ejecutarConsulta($sql);
	}

	public function actualizarComprobantesEmpresa($idempresa, $nombre, $serie, $numero)
	{
	    $sql="DELETE FROM comp_pago WHERE idempresa='$idempresa'";
	    ejecutarConsulta($sql);

	    $num_elementos=0;
	    $sw=true;
	    while ($num_elementos < count($nombre)) {
	        $sql="INSERT INTO comp_pago (nombre,serie_comprobante,num_comprobante,idempresa,condicion)
	              VALUES ('$nombre[$num_elementos]','$serie[$num_elementos]','$numero[$num_elementos]','$idempresa','1')";
	        ejecutarConsulta($sql) or $sw=false;
	        $num_elementos++;
	    }
	    return $sw;
	}

	public function selectEmpresas() {
		$sql = "SELECT * FROM empresas";
		return ejecutarConsulta($sql);
	}
}

?>