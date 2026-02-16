<?php
//Incluímos inicialmente la conexión a la base de datos
require "../configuraciones/Conexion.php";

class Cajachica
{
	//Implementamos nuestro constructor
	public function __construct() {}

	//Implementamos un método para insertar registros
	//Implementamos un método para insertar registros
	public function insertar($tipo, $idcaja, $idsucursal, $idpersonal, $monto, $descripcion, $formapago, $totaldeposito, $noperacion, $idconcepto_movimiento)
	{
		$idpersonal_sql = ($idpersonal === '' || $idpersonal === null) ? "NULL" : "'$idpersonal'";
		$fechaActual = date('Y-m-d H:i:s');
		$sql = "INSERT INTO movimiento (tipo,idcaja,idsucursal,idpersonal,monto,descripcion, formapago, totaldeposito, noperacion, idconcepto_movimiento,fecha)
		VALUES ('$tipo','$idcaja','$idsucursal',$idpersonal_sql,'$monto','$descripcion', '$formapago', '$totaldeposito', '$noperacion', '$idconcepto_movimiento','$fechaActual')";
		return ejecutarConsulta($sql);
	}

	public function editar($idmovimiento, $tipo, $idcaja, $idsucursal, $idpersonal, $monto, $descripcion, $formapago, $totaldeposito, $noperacion, $idconcepto_movimiento)
	{
		$idpersonal_sql = ($idpersonal === '' || $idpersonal === null) ? "NULL" : "'$idpersonal'";
		$sql = "UPDATE movimiento SET tipo='$tipo', idcaja=$idcaja, idsucursal=$idsucursal, idpersonal=$idpersonal_sql, monto='$monto', formapago='$formapago', totaldeposito='$totaldeposito', noperacion='$noperacion', descripcion='$descripcion', idconcepto_movimiento='$idconcepto_movimiento' WHERE idmovimiento='$idmovimiento'";
		return ejecutarConsulta($sql);
	}


	public function listar($fecha_inicio, $fecha_fin, $idsucursal)
	{
		$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
		$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
		$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
		$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

		$condicion = "WHERE DATE(fecha) >= '$fecha_inicio' AND DATE(fecha) <= '$fecha_fin'";

		if ($idsucursal !== "Todos") {
			$condicion .= " AND idsucursal = '$idsucursal'";
		}

		if (!empty($search)) {
			$condicion .= " AND (
            descripcion LIKE '%$search%' OR
            formapago LIKE '%$search%' OR
            tipo LIKE '%$search%' OR
            monto LIKE '%$search%' OR
            totaldeposito LIKE '%$search%'
        )";
		}

		// Total de registros filtrados
		$sql_total = "SELECT COUNT(*) as total FROM movimiento $condicion";
		$rspta_total = ejecutarConsultaSimpleFila($sql_total);
		$total = $rspta_total["total"];

		// Consulta con paginación
		$sql = "SELECT m.*,cm.* FROM movimiento m
						INNER JOIN concepto_movimiento cm ON m.idconcepto_movimiento = cm.idconcepto_movimiento 
						$condicion ORDER BY m.idmovimiento DESC LIMIT $start, $length";
		$rspta = ejecutarConsulta($sql);

		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => $reg->fecha,
				"1" => $reg->descripcion,
				"2" => (strtolower($reg->tipo) == 'egresos') ? 
						    '<span class="badge bg-danger">EGRESO</span>' : 
						    '<span class="badge bg-success">INGRESO</span>',
				"3" => $reg->formapago,
				"4" => $reg->monto,
				"5" => $reg->totaldeposito,
				"6" => '<div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
            <i class="fa fa-list-ul"></i> <span class="caret"></span>
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" style="cursor:pointer;" onclick="mostrar(' . $reg->idmovimiento . ')">Editar</a>
            <a class="dropdown-item" style="cursor:pointer;" onclick="eliminar(' . $reg->idmovimiento . ')">Eliminar</a>

            <div class="dropdown-divider"></div>

            <a class="dropdown-item text-primary" style="cursor:pointer;" 
               onclick="abrirRecibo(' . $reg->idmovimiento . ')">
               <i class="fa fa-print"></i> Ver Recibo
            </a>
          </div>
        </div>',
			);
		}

		return array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $data
		);
	}




	public function eliminar($idmovimiento)
	{
		$sql = "DELETE FROM movimiento WHERE idmovimiento='$idmovimiento'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idmovimiento)
	{
		$sql = "SELECT * FROM movimiento WHERE idmovimiento='$idmovimiento'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function coceptoMovimiento($tipo)
	{
		$sql = "SELECT * FROM concepto_movimiento WHERE tipo='$tipo' AND estado='1'";
		return ejecutarConsulta($sql);
	}

	public function listarConceptos()
	{
		$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
		$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
		$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
		$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

		$condicion = "";

		if (!empty($search)) {
			$search = str_replace("'", "", $search); // seguridad básica
			$condicion .= "(
        descripcion LIKE '%$search%' OR
        tipo LIKE '%$search%' OR
        categoria_concepto LIKE '%$search%'
    )";
		}

		// Agregar WHERE si hay condición
		$condicion_sql = !empty($condicion) ? "WHERE $condicion" : "";

		// Total filtrado
		$sql_total = "SELECT COUNT(*) as total FROM concepto_movimiento $condicion_sql";
		$rspta_total = ejecutarConsultaSimpleFila($sql_total);
		$total = $rspta_total["total"] ?? 0;

		// Consulta principal con paginación
		$sql = "SELECT * FROM concepto_movimiento $condicion_sql 
        ORDER BY idconcepto_movimiento DESC 
        LIMIT $start, $length";

		$rspta = ejecutarConsulta($sql);
		$rspta = ejecutarConsulta($sql);

		$data = array();
		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => $reg->descripcion,
				"1" => ($reg->tipo == 'egresos') ? '<span class="badge bg-danger">EGRESO</span>' :
					'<span class="badge bg-success">INGRESO</span>',
				"2" => $reg->categoria_concepto,
				"3" => '<div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-list-ul"></i> <span class="caret"></span>
                      </button>
                      <div class="dropdown-menu">
            <a class="dropdown-item" style="cursor:pointer;" onclick=\'mostrarConcepto(' . json_encode($reg) . ')\'>Editar</a>
            <a class="dropdown-item" style="cursor:pointer;" onclick="eliminarConcepto(' . $reg->idconcepto_movimiento . ')">Eliminar</a>
          </div>
                    </div>',
			);
		}

		return array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $data
		);
	}

	public function insertarConcepto($descripcion, $tipo, $categoria_concepto)
	{
		$sql = "INSERT INTO concepto_movimiento (descripcion, tipo, categoria_concepto, estado)
		  VALUES ('$descripcion', '$tipo', '$categoria_concepto', '1')";
		return ejecutarConsulta($sql);
	}
	
	public function editarConcepto($idconcepto_movimiento, $descripcion, $tipo, $categoria_concepto)
	{
		$sql = "UPDATE concepto_movimiento SET descripcion='$descripcion', tipo='$tipo', categoria_concepto='$categoria_concepto' WHERE idconcepto_movimiento='$idconcepto_movimiento'";
		return ejecutarConsulta($sql);
	}

	function guardarPagoDiario($tipo, $idcaja, $idsucursal, $idpersonal, $monto, $descripcion, $formapago, $totaldeposito, $noperacion, $idconcepto_movimiento, $idasistencia){
		if (!$idcaja) {
			return false; // Tipo inválido
		}
		$idpersonal_sql = ($idpersonal === '' || $idpersonal === null) ? "NULL" : "'$idpersonal'";
		$sql = "INSERT INTO movimiento (tipo,idcaja,idsucursal,idpersonal,monto,descripcion, formapago, totaldeposito, noperacion, idconcepto_movimiento)
		VALUES ('$tipo','$idcaja','$idsucursal',$idpersonal_sql,'$monto','$descripcion', '$formapago', '$totaldeposito', '$noperacion', '$idconcepto_movimiento')";
		ejecutarConsulta($sql);

		$sql_asistencia = "UPDATE asistencias SET estado_pago='1', monto='$monto' WHERE idasistencia='$idasistencia'";
		return ejecutarConsulta($sql_asistencia);
	}

	public function obtenerIdConceptoAdelanto() {
	    $sql = "SELECT idconcepto_movimiento 
	            FROM concepto_movimiento
	            WHERE descripcion LIKE '%adelanto%'
	            LIMIT 1";
	    return ejecutarConsultaSimpleFila($sql);
	}

	public function listarAdelantos($idpersonal, $desde, $hasta){
    // obtener id dinámico
    $id = $this->obtenerIdConceptoAdelanto();
    $id_adelanto = $id['idconcepto_movimiento'];

    $sql = "SELECT fecha, descripcion, monto 
            FROM movimiento
            WHERE idpersonal='$idpersonal'
            AND idconcepto_movimiento = '$id_adelanto'
            AND DATE(fecha) BETWEEN '$desde' AND '$hasta'
            ORDER BY fecha ASC";

    return ejecutarConsulta($sql);
}

public function listarIngresosSemana($idpersonal, $desde, $hasta) {

    $sql = "SELECT fecha, descripcion, monto
            FROM movimiento
            WHERE idpersonal = '$idpersonal'
            AND tipo = 'Ingresos'
            AND DATE(fecha) BETWEEN '$desde' AND '$hasta'
            ORDER BY fecha ASC";

    return ejecutarConsulta($sql);
}

public function listarAdelantosPorFechas($desde, $hasta) {
    // obtener id dinámico del concepto "adelanto"
    $id = $this->obtenerIdConceptoAdelanto();
    $id_adelanto = $id['idconcepto_movimiento'];

    $sql = "SELECT 
                DATE_FORMAT(m.fecha, '%d/%m/%Y %h:%i %p') AS fecha, 
                m.descripcion, 
                m.monto, 
                p.nombre AS trabajador
            FROM movimiento m
            LEFT JOIN personal p ON p.idpersonal = m.idpersonal
            WHERE m.idconcepto_movimiento = '$id_adelanto'
            AND DATE(m.fecha) BETWEEN '$desde' AND '$hasta'
            ORDER BY m.fecha ASC";

    return ejecutarConsulta($sql);
}

public function listarDiasTrabajadosPorFechas($desde, $hasta) {

    $sql = "SELECT 
                p.nombre AS trabajador,
                a.fecha,
                a.monto AS monto_dia
            FROM asistencias a
            LEFT JOIN personal p ON p.idpersonal = a.idpersonal
            WHERE DATE(a.fecha) BETWEEN '$desde' AND '$hasta'
              AND a.estado = 'asistio'
            ORDER BY p.nombre, a.fecha ASC";

    return ejecutarConsulta($sql);
}



}
