<?php
//incluir la conexion de base de datos
require_once __DIR__ . "/../configuraciones/Conexion.php";
require_once __DIR__ . "/Helpers.php";
require_once __DIR__ . "/../core/Response.php";

class Negocio extends Helpers
{

	//implementamos nuestro constructor
	public function __construct()
	{
		parent::__construct();
	}

	//metodo insertar regiustro
	public function insertar($nombre, $ndocumento, $documento, $direccion, $telefono, $email, $logo, $pais, $ciudad, $nombre_impuesto, $monto_impuesto, $moneda, $simbolo, $usuario_sol, $clave_sol, $ruta_certificado, $clave_certificado, $estado_certificado, $client_id, $client_secret)
	{
		$sql = "INSERT INTO datos_negocio (nombre,ndocumento,documento,direccion,telefono,email,logo,pais,ciudad,nombre_impuesto,monto_impuesto,moneda,simbolo,usuario_sol,clave_sol,estado_certificado,ruta_certificado,clave_certificado,client_id,client_secret,condicion)
	 VALUES ('$nombre','$ndocumento','$documento','$direccion','$telefono','$email','$logo','$pais','$ciudad','$nombre_impuesto','$monto_impuesto','$moneda','$simbolo','$usuario_sol','$clave_sol','$estado_certificado','$ruta_certificado','$clave_certificado','$client_id','$client_secret','1')";
		return ejecutarConsulta($sql);
	}

	public function editar($id_negocio, $nombre, $ndocumento, $documento, $direccion, $telefono, $email, $logo, $pais, $ciudad, $nombre_impuesto, $monto_impuesto, $moneda, $simbolo, $usuario_sol, $clave_sol, $ruta_certificado, $clave_certificado, $estado_certificado, $client_id, $client_secret)
	{
		$sql = "UPDATE datos_negocio SET nombre='$nombre',ndocumento='$ndocumento',documento='$documento',direccion='$direccion',telefono='$telefono',email='$email',logo='$logo',pais='$pais',ciudad='$ciudad',nombre_impuesto='$nombre_impuesto',monto_impuesto='$monto_impuesto',moneda='$moneda',simbolo='$simbolo',usuario_sol='$usuario_sol',clave_sol='$clave_sol',ruta_certificado='$ruta_certificado',clave_certificado='$clave_certificado',estado_certificado='$estado_certificado',client_id='$client_id',client_secret='$client_secret'
	WHERE id_negocio='$id_negocio'";
		return ejecutarConsulta($sql);
	}

	public function mostrarNombreNegocio()
	{
		$sql = "SELECT * FROM datos_negocio";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function desactivar($id_negocio)
	{
		$sql = "UPDATE datos_negocio SET condicion='0' WHERE id_negocio='$id_negocio'";
		return ejecutarConsulta($sql);
	}
	public function activar($id_negocio)
	{
		$sql = "UPDATE datos_negocio SET condicion='1' WHERE id_negocio='$id_negocio'";
		return ejecutarConsulta($sql);
	}
	//metodo para mostrar registros
	public function mostrar($id_negocio)
	{
		$sql = "SELECT * FROM datos_negocio WHERE id_negocio='$id_negocio'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function mostrar_impuesto($idsucursal)
	{
		$impuesto = Helpers::get_impuesto_empresa($idsucursal);
		Response::json($impuesto);
	}
	public function nombre_impuesto($idsucursal)
	{
		$sql = "SELECT e.nombre_impuesto FROM sucursal s, empresas e WHERE s.idempresa = e.idempresa AND s.idsucursal='$idsucursal'";
		return ejecutarConsulta($sql);
	}
	public function mostrar_registros()
	{
		$sql = "SELECT id_negocio FROM datos_negocio";
		return ejecutarConsulta($sql);
	}
	public function mostrar_simbolo($idsucursal)
	{
		$sql = "SELECT s.simbolo FROM sucursal s, empresas e WHERE s.idempresa = e.idempresa AND s.idsucursal='$idsucursal'";
		return ejecutarConsulta($sql);
	}
	//listar registros
	public function listar($idsucursal)
	{
		return (new DBQuery($this->pdo))
		->select('*')
		->from('sucursal s')
		->join('empresas e', 's.idempresa = e.idempresa')
		->where('s.idsucursal', '=', $idsucursal)
		->first();
		$sql = "SELECT * FROM sucursal s INNER JOIN empresas e ON s.idempresa = e.idempresa WHERE s.idsucursal = $idsucursal";
		return ejecutarConsulta($sql);
	}

}

?>