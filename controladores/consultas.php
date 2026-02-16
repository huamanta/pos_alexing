<?php
ob_start();
if (strlen(session_id()) < 1) {
	session_start(); //Validamos si existe o no la sesión
}
if (!isset($_SESSION["nombre"])) {
	header("Location: ../vistas/login.html"); //Validamos el acceso solo a los usuarios logueados al sistema.
} else {
	require_once "../modelos/Consultas.php";

	$consulta = new Consultas();

	switch ($_GET["op"]) {

		case 'totalutilidadnetapv':
		    $fecha_inicio = $_REQUEST["fecha_inicio"];
		    $fecha_fin = $_REQUEST["fecha_fin"];
		    $idvendedor = $_REQUEST["idvendedor"];
		    $idsucursal = $_REQUEST["idsucursal"];
		    $idproducto = $_REQUEST["idproducto"];
		    // ✅ Si no hay sucursal seleccionada, obtener la primera asociada al usuario
			    if (empty($idsucursal) || $idsucursal == "0" || $idsucursal == "Todos") {
			        $sqlSucursal = "SELECT s.idsucursal 
			                        FROM usuario_sucursal us 
			                        INNER JOIN sucursal s ON s.idsucursal = us.idsucursal 
			                        INNER JOIN usuario u ON u.idusuario = us.idusuario 
			                        WHERE u.idpersonal = '" . $_SESSION['idpersonal'] . "' 
			                        ORDER BY s.idsucursal ASC 
			                        LIMIT 1";
			        $rsSucursal = ejecutarConsultaSimpleFila($sqlSucursal);

			        if ($rsSucursal && isset($rsSucursal['idsucursal'])) {
			            $idsucursal = $rsSucursal['idsucursal'];
			        } else {
			            $idsucursal = "Todos";
			        }
			    }

		    $rspta = $consulta->TotalUtilidadNetaPV($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal, $idproducto);
		    echo json_encode($rspta);
		    break;


		case 'totalcantidadpv2':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idproducto = $_REQUEST["idproducto"];

			$rspta = $consulta->TotalCantidadPV2($fecha_inicio, $fecha_fin, $idvendedor,$idsucursal,$idproducto);
			echo json_encode($rspta);

			break;

		case 'totalcomprapv2':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idproducto = $_REQUEST["idproducto"];

			$rspta = $consulta->TotalCompraPV2($fecha_inicio, $fecha_fin, $idvendedor,$idsucursal,$idproducto);
			echo json_encode($rspta);

			break;

		case 'totalventapv2':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idproducto = $_REQUEST["idproducto"];

			$rspta = $consulta->TotalVentaPV2($fecha_inicio, $fecha_fin, $idvendedor,$idsucursal,$idproducto);
			echo json_encode($rspta);

			break;

		case 'totalutilidadpv2':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idproducto = $_REQUEST["idproducto"];

			$rspta = $consulta->TotalUtilidadPV2($fecha_inicio, $fecha_fin, $idvendedor,$idsucursal,$idproducto);
			echo json_encode($rspta);

			break;

		// ====================== TOTAL CANTIDAD PV ======================
			case 'totalcantidadpv':

			    $fecha_inicio = $_REQUEST["fecha_inicio"];
			    $fecha_fin = $_REQUEST["fecha_fin"];
			    $idvendedor = $_REQUEST["idvendedor"];
			    $idsucursal = $_REQUEST["idsucursal"];
			    $idproducto = $_REQUEST["idproducto"];

			    // ✅ Si no hay sucursal seleccionada, obtener la primera asociada al usuario
			    if (empty($idsucursal) || $idsucursal == "0" || $idsucursal == "Todos") {
			        $sqlSucursal = "SELECT s.idsucursal 
			                        FROM usuario_sucursal us 
			                        INNER JOIN sucursal s ON s.idsucursal = us.idsucursal 
			                        INNER JOIN usuario u ON u.idusuario = us.idusuario 
			                        WHERE u.idpersonal = '" . $_SESSION['idpersonal'] . "' 
			                        ORDER BY s.idsucursal ASC 
			                        LIMIT 1";
			        $rsSucursal = ejecutarConsultaSimpleFila($sqlSucursal);

			        if ($rsSucursal && isset($rsSucursal['idsucursal'])) {
			            $idsucursal = $rsSucursal['idsucursal'];
			        } else {
			            $idsucursal = "Todos";
			        }
			    }

			    $rspta = $consulta->TotalCantidadPV($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal, $idproducto);
			    echo json_encode($rspta);
			break;


			// ====================== TOTAL COMPRA PV ======================
			case 'totalcomprapv':

			    $fecha_inicio = $_REQUEST["fecha_inicio"];
			    $fecha_fin = $_REQUEST["fecha_fin"];
			    $idvendedor = $_REQUEST["idvendedor"];
			    $idsucursal = $_REQUEST["idsucursal"];
			    $idproducto = $_REQUEST["idproducto"];

			    // ✅ Validar primera sucursal
			    if (empty($idsucursal) || $idsucursal == "0" || $idsucursal == "Todos") {
			        $sqlSucursal = "SELECT s.idsucursal 
			                        FROM usuario_sucursal us 
			                        INNER JOIN sucursal s ON s.idsucursal = us.idsucursal 
			                        INNER JOIN usuario u ON u.idusuario = us.idusuario 
			                        WHERE u.idpersonal = '" . $_SESSION['idpersonal'] . "' 
			                        ORDER BY s.idsucursal ASC 
			                        LIMIT 1";
			        $rsSucursal = ejecutarConsultaSimpleFila($sqlSucursal);

			        if ($rsSucursal && isset($rsSucursal['idsucursal'])) {
			            $idsucursal = $rsSucursal['idsucursal'];
			        } else {
			            $idsucursal = "Todos";
			        }
			    }

			    $rspta = $consulta->TotalCompraPV($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal, $idproducto);
			    echo json_encode($rspta);
			break;


			// ====================== TOTAL VENTA PV ======================
			case 'totalventapv':

			    $fecha_inicio = $_REQUEST["fecha_inicio"];
			    $fecha_fin = $_REQUEST["fecha_fin"];
			    $idvendedor = $_REQUEST["idvendedor"];
			    $idsucursal = $_REQUEST["idsucursal"];
			    $idproducto = $_REQUEST["idproducto"];
			    if (empty($idsucursal) || $idsucursal == "0" || $idsucursal == "Todos") {
			        $sqlSucursal = "SELECT s.idsucursal 
			                        FROM usuario_sucursal us 
			                        INNER JOIN sucursal s ON s.idsucursal = us.idsucursal 
			                        INNER JOIN usuario u ON u.idusuario = us.idusuario 
			                        WHERE u.idpersonal = '" . $_SESSION['idpersonal'] . "' 
			                        ORDER BY s.idsucursal ASC 
			                        LIMIT 1";
			        $rsSucursal = ejecutarConsultaSimpleFila($sqlSucursal);

			        if ($rsSucursal && isset($rsSucursal['idsucursal'])) {
			            $idsucursal = $rsSucursal['idsucursal'];
			        } else {
			            $idsucursal = "Todos";
			        }
			    }

			    $rspta = $consulta->TotalVentaPV($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal, $idproducto);
			    echo json_encode($rspta);
			break;


			// ====================== TOTAL UTILIDAD PV ======================
			case 'totalutilidadpv':

			    $fecha_inicio = $_REQUEST["fecha_inicio"];
			    $fecha_fin = $_REQUEST["fecha_fin"];
			    $idvendedor = $_REQUEST["idvendedor"];
			    $idsucursal = $_REQUEST["idsucursal"];
			    $idproducto = $_REQUEST["idproducto"];

			    // ✅ Validar primera sucursal
			    if (empty($idsucursal) || $idsucursal == "0" || $idsucursal == "Todos") {
			        $sqlSucursal = "SELECT s.idsucursal 
			                        FROM usuario_sucursal us 
			                        INNER JOIN sucursal s ON s.idsucursal = us.idsucursal 
			                        INNER JOIN usuario u ON u.idusuario = us.idusuario 
			                        WHERE u.idpersonal = '" . $_SESSION['idpersonal'] . "' 
			                        ORDER BY s.idsucursal ASC 
			                        LIMIT 1";
			        $rsSucursal = ejecutarConsultaSimpleFila($sqlSucursal);

			        if ($rsSucursal && isset($rsSucursal['idsucursal'])) {
			            $idsucursal = $rsSucursal['idsucursal'];
			        } else {
			            $idsucursal = "Todos";
			        }
			    }

			    $rspta = $consulta->TotalUtilidadPV($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal, $idproducto);
			    echo json_encode($rspta);
			break;


		case 'totalcompracantidad':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idproveedor = $_REQUEST["idproveedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->TotalCompraCantidad($fecha_inicio, $fecha_fin, $idproveedor,$idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalcompraproveedor':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idproveedor = $_REQUEST["idproveedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->TotalCompraProveedor($fecha_inicio, $fecha_fin, $idproveedor,$idsucursal);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalSalidaTarjeta':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}
			if($idsucursal != "Todos"){
				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
					$idsucursal = "Todos";					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){	
					$idsucursal = $_SESSION['idsucursal'];	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){	
					$idsucursal = $idsucursal;	
				}else{	
					$idsucursal = $_REQUEST["idsucursal"];	
				}
			}
			$rspta = $consulta->mostrarTotalSalidaTarjeta($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalSalidaEfectivo':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}
			if($idsucursal != "Todos"){
				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
					$idsucursal = "Todos";					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){	
					$idsucursal = $_SESSION['idsucursal'];	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){	
					$idsucursal = $idsucursal;	
				}else{	
					$idsucursal = $_REQUEST["idsucursal"];	
				}
			}
			$rspta = $consulta->mostrarTotalSalidaEfectivo($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalTarjeta':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalTarjeta($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalEfectivoC':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalEfectivoC($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);
			break;

		case 'mostrarTotalEgresosTar':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}
			if($idsucursal != "Todos"){
				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
					$idsucursal = "Todos";					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){	
					$idsucursal = $_SESSION['idsucursal'];	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){	
					$idsucursal = $idsucursal;	
				}else{	
					$idsucursal = $_REQUEST["idsucursal"];	
				}
			}
			$rspta = $consulta->mostrarTotalEgresosTar($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'totalTcomprass2':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}
			if($idsucursal != "Todos"){
				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
					$idsucursal = "Todos";					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){	
					$idsucursal = $_SESSION['idsucursal'];	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){	
					$idsucursal = $idsucursal;	
				}else{	
					$idsucursal = $_REQUEST["idsucursal"];	
				}
			}

			$rspta = $consulta->totalTCompras($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor);
			echo json_encode($rspta);

			break;

		case 'totalTickets':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalTickets($fecha_inicio, $fecha_fin, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalFacturascount':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalFacturascount($fecha_inicio, $fecha_fin, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalBoletascount':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalBoletascount($fecha_inicio, $fecha_fin, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalNotasCompraCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}


			$rspta = $consulta->mostrarTotalNotasCompraCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalNotasCompraTCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalNotasCompraTCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor);
			echo json_encode($rspta);

			break;

		/*case 'totalTcomprass':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalTcomp($fecha_inicio, $fecha_fin, $idsucursal);
			echo json_encode($rspta);

			break;*/

		case 'mostrarTotalTransferenciaSalida':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}
			if($idsucursal != "Todos"){
				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
					$idsucursal = "Todos";					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){	
					$idsucursal = $_SESSION['idsucursal'];	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){	
					$idsucursal = $idsucursal;	
				}else{
						$idsucursal = $_REQUEST["idsucursal"];	
				}
			}
			$rspta = $consulta->mostrarTotalTransferenciaSalida($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalEfectivoSalida':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}
			if($idsucursal != "Todos"){
				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
					$idsucursal = "Todos";					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){	
					$idsucursal = $_SESSION['idsucursal'];	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){	
					$idsucursal = $idsucursal;	
				}else{
						$idsucursal = $_REQUEST["idsucursal"];	
				}
			}

			$rspta = $consulta->totalEfectivoSalida($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);
			break;

		case 'mostrarTotalCuentasPagarVentaCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}
			if($idsucursal != "Todos"){
				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
					$idsucursal = "Todos";					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];	
				}
			}
			$rspta = $consulta->mostrarTotalCuentasPagarVentaCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalCuentasPagarVentaTCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}
			if($idsucursal != "Todos"){
				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
					$idsucursal = "Todos";					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];	
				}
			}

			$rspta = $consulta->mostrarTotalCuentasPagarVentaTCaja($fecha_inicio, $fecha_fin, $idsucursal, $idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalBoletasCajaSalida':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalBoletasCajaSalida($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalBoletasTCajaSalida':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalBoletasTCajaSalida($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalFacturasCajaSalida':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalFacturasCajaSalida($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalFacturasTCajaSalida':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalFacturasTCajaSalida($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrartotalpedidos':
			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];

			if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

				$idsucursal = "Todos";
				
			}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){

				$idsucursal = $_SESSION['idsucursal'];

			}else{

				$idsucursal = $_REQUEST["idsucursal"];

			}

			// var_dump($fecha_inicio, $fecha_fin, $idcliente, $idsucursal);

			$rspta = $consulta->mostrarTotalPedidos($fecha_inicio, $fecha_fin, $idsucursal);
			$data = array();
			

			while ($reg = $rspta->fetch_object()) {
			    $cantidad_vendida = intval($reg->cantidad_vendida);
			    $cantidad_comprada = intval($reg->cantidad_comprada);
			    
			    // Cálculo del porcentaje de ventas
			    $porcentaje_ventas = ($cantidad_comprada > 0) ? ($cantidad_vendida / $cantidad_comprada) * 100 : 0;

			    // Crear la barra de progreso
			    $barra_progreso = "<div style='width: 100%; background-color: #e0e0e0; border-radius: 5px; overflow: hidden;'>
			                           <div style='width: " . round($porcentaje_ventas, 2) . "%; background-color: #4caf50; height: 20px;'></div>
			                       </div>
			                       <span style='margin-left: 5px;'>" . round($porcentaje_ventas, 2) . "%</span>";

			    $data[] = array(
			        "0" => $reg->nombre,
			        "1" => $reg->stock,
			        // Convertimos a entero y aplicamos estilos
			        "2" => "<span class='badge badge-danger'>" . $cantidad_vendida . "</span>",
			        "3" => "<span class='badge bg-green'>" . $cantidad_comprada . "</span>",
			        "4" => $barra_progreso, // Muestra la barra de progreso
			    );
			}





			if (empty($data)) {
			    // Aquí podrías devolver un mensaje o manejar el caso donde no hay datos
			    $results = array(
			        "sEcho" => 1,
			        "iTotalRecords" => 0,
			        "iTotalDisplayRecords" => 0,
			        "aaData" => []
			    );
			} else {
			    $results = array(
			        "sEcho" => 1,
			        "iTotalRecords" => count($data),
			        "iTotalDisplayRecords" => count($data),
			        "aaData" => $data
			    );
			}

			echo json_encode($results);

			break;

		case 'reportesdigemid':
			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
				$idsucursal = "Todos";				
			}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
				$idsucursal = $_SESSION['idsucursal'];
			}else{
				$idsucursal = $_REQUEST["idsucursal"];
			}

			$rspta = $consulta->reportesdigemid($fecha_inicio, $fecha_fin, $idsucursal);
			//Vamos a declarar un array
			$data = array();

			while ($reg = $rspta->fetch_object()) {
				$data[] = array(
					"0" => '<span style="font-weight:bold">'.$reg->nombre.'</span>',
					"1" => $reg->registrosan,
					"2" => $reg->nlote,
					"3" => ($reg->fvencimiento != '0000-00-00') ? $reg->fvencimiento : '' ,
					"4" => $reg->cantidad,
					"5" => '<span class="badge bg-green">'.'S/. '.$reg->precio_venta.'</span>'
				);
			}
			$results = array(
				"sEcho" => 1, //Información para el datatables
				"iTotalRecords" => count($data), //enviamos el total registros al datatable
				"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
				"aaData" => $data
			);
			echo json_encode($results);

			break;

		case 'reportesvencimiento':
			$idsucursal = $_REQUEST["idsucursal"];
			if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
				$idsucursal = "Todos";				
			}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
				$idsucursal = $_SESSION['idsucursal'];
			}else{
				$idsucursal = $_REQUEST["idsucursal"];
			}

			$rspta = $consulta->reportesvencimiento($idsucursal);
			//Vamos a declarar un array
			$data = array();

			while ($reg = $rspta->fetch_object()) {
				$data[] = array(
					"0" => '<span style="font-weight:bold">'.$reg->nombre.'</span>',
					"1" => $reg->registrosan,
					"2" => $reg->nlote,
					"3" => ($reg->fvencimiento != '0000-00-00') ? $reg->fvencimiento : '' ,
					"4" => $reg->cantidad,
					//"5" => '<span class="badge bg-green">'.'S/. '.$reg->precio_venta.'</span>'
					"5"=>$consulta->calcularDiasVencimiento2($reg->dias_transcurridos2)
				);
			}
			$results = array(
				"sEcho" => 1, //Información para el datatables
				"iTotalRecords" => count($data), //enviamos el total registros al datatable
				"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
				"aaData" => $data
			);
			echo json_encode($results);

			break;

		case 'comprasfecha':
			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];

			if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

				$idsucursal = "Todos";
				
			}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){

				$idsucursal = $_SESSION['idsucursal'];

			}else{

				$idsucursal = $_REQUEST["idsucursal"];

			}

			$rspta = $consulta->comprasfecha($fecha_inicio, $fecha_fin, $idsucursal);
			//Vamos a declarar un array
			$data = array();

			while ($reg = $rspta->fetch_object()) {
				$data[] = array(
					"0" => $reg->fecha,
					"1" => $reg->personal,
					"2" => $reg->proveedor,
					"3" => $reg->tipo_comprobante,
					"4" => $reg->serie_comprobante . ' ' . $reg->num_comprobante,
					"5" => $reg->total_compra,
					"6" => $reg->impuesto,
					"7" => ($reg->estado == 'REGISTRADO') ? '<span class="badge bg-green">ACEPTADO</span>' :
						'<span class="badge bg-red">ANULADO</span>'
				);
			}
			$results = array(
				"sEcho" => 1, //Información para el datatables
				"iTotalRecords" => count($data), //enviamos el total registros al datatable
				"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
				"aaData" => $data
			);
			echo json_encode($results);

			break;

		case 'ventasfechacliente':
			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idcliente = $_REQUEST["idcliente"];
			$idsucursal = $_REQUEST["idsucursal"];

			if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

				$idsucursal = "Todos";
				
			}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){

				$idsucursal = $_SESSION['idsucursal'];

			}else{

				$idsucursal = $_REQUEST["idsucursal"];

			}

			// var_dump($fecha_inicio, $fecha_fin, $idcliente, $idsucursal);

			$rspta = $consulta->ventasfechacliente($fecha_inicio, $fecha_fin, $idcliente, $idsucursal);
			//Vamos a declarar un array
			$data = array();

			// var_dump($rspta);

			while ($reg = $rspta->fetch_object()) {
				$data[] = array(
					"0" => $reg->fecha,
					"1" => $reg->personal,
					"2" => $reg->cliente,
					"3" => $reg->tipo_comprobante,
					"4" => $reg->serie_comprobante . ' ' . $reg->num_comprobante,
					"5" => $reg->total_venta,
					"6" => $reg->impuesto,
					"7" => ($reg->estado == 'Aceptado' || $reg->estado == 'Activado' || $reg->estado == 'Por Enviar') ? '<span class="badge bg-green">'.$reg->estado.'</span>' :
						'<span class="badge bg-red">'.$reg->estado.'</span>'
				);
			}
			$results = array(
				"sEcho" => 1, //Información para el datatables
				"iTotalRecords" => count($data), //enviamos el total registros al datatable
				"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
				"aaData" => $data
			);
			echo json_encode($results);

			break;

		case 'ventasfechavendedor':
			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idcliente = $_REQUEST["idcliente"];
			$idsucursal = $_REQUEST["idsucursal"];

			if($idcliente == ""){

				$idcliente = "Todos";

			}

			if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

				$idsucursal = "Todos";
				
			}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){

				$idsucursal = $_SESSION['idsucursal'];

			}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){

				$idsucursal = $idsucursal;

			}else{

				$idsucursal = $_REQUEST["idsucursal"];

			}

			$rspta = $consulta->ventasfechavendedor($fecha_inicio, $fecha_fin, $idcliente, $idsucursal);

			//Vamos a declarar un array
			$data = array();

			while ($reg = $rspta->fetch_object()) {
				$data[] = array(
					"0" => $reg->fecha,
					"1" => $reg->personal,
					"2" => $reg->cliente,
					"3" => $reg->tipo_comprobante,
					"4" => $reg->serie_comprobante . ' - ' . $reg->num_comprobante,
					"5" => $reg->total_venta,
					"6" => $reg->descuento,
					"7" => $reg->comisionV,
					"8" => ($reg->estado == 'Aceptado' || $reg->estado == 'Activado' || $reg->estado == 'Por Enviar') ? '<span class="badge bg-green">'.$reg->estado.'</span>' :
						'<span class="badge bg-red">'.$reg->estado.'</span>'
				);
			}
			$results = array(
				"sEcho" => 1, //Información para el datatables
				"iTotalRecords" => count($data), //enviamos el total registros al datatable
				"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
				"aaData" => $data
			);
			echo json_encode($results);

			break;

			case 'ventasfechaservicio':
				$fecha_inicio = $_REQUEST["fecha_inicio"];
				$fecha_fin = $_REQUEST["fecha_fin"];
				$idproducto = $_REQUEST["idproducto"];
				$idvendedor = $_REQUEST["idvendedor"];
				$idsucursal = $_REQUEST["idsucursal"];
	
				if($idsucursal != "Todos"){
	
					if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
	
						$idsucursal = "Todos";
						
					}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
		
						$idsucursal = $_SESSION['idsucursal'];
		
					}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
		
						$idsucursal = $_SESSION['idsucursal'];
		
					}else{
		
						$idsucursal = $_REQUEST["idsucursal"];
		
					}
	
				}
	
				$rspta = $consulta->ventasfechaservicio($fecha_inicio, $fecha_fin, $idproducto, $idvendedor, $idsucursal);
	
				//Vamos a declarar un array
				$data = array();
	
				while ($reg = $rspta->fetch_object()) {
					$data[] = array(
						"0" => $reg->fecha_hora,
						"1" => $reg->nombre . " - " . $reg->unidadmedida,
						"2" => $reg->cantidad,
						"3" => $reg->precio,
						"4" => $reg->precioCompra,
						"5" => $reg->utilidad,
						"6" => $reg->nombreVendedor
					);
				}
				$results = array(
					"sEcho" => 1, //Información para el datatables
					"iTotalRecords" => count($data), //enviamos el total registros al datatable
					"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
					"aaData" => $data
				);
				echo json_encode($results);
	
			break;	

		case 'ventasfechaproducto':
    $fecha_inicio = $_REQUEST["fecha_inicio"];
    $fecha_fin = $_REQUEST["fecha_fin"];
    $idproducto = $_REQUEST["idproducto"];
    $idvendedor = $_REQUEST["idvendedor"];
    $idsucursal = $_REQUEST["idsucursal"];

    if ($idproducto == NULL) {
        $idproducto = "Todos";
    }

    if ($idvendedor == NULL) {
        $idvendedor = "Todos";
    }

    // ✅ Si no hay sucursal seleccionada, obtener la primera asociada al usuario
    if (empty($idsucursal) || $idsucursal == "0" || $idsucursal == "Todos") {
        $sqlSucursal = "SELECT s.idsucursal 
                        FROM usuario_sucursal us 
                        INNER JOIN sucursal s ON s.idsucursal = us.idsucursal 
                        INNER JOIN usuario u ON u.idusuario = us.idusuario 
                        WHERE u.idpersonal = '" . $_SESSION['idpersonal'] . "' 
                        ORDER BY s.idsucursal ASC 
                        LIMIT 1";
        $rsSucursal = ejecutarConsultaSimpleFila($sqlSucursal);

        if ($rsSucursal && isset($rsSucursal['idsucursal'])) {
            $idsucursal = $rsSucursal['idsucursal'];
        } else {
            $idsucursal = "Todos"; // fallback
        }
    }

    // 📦 Obtener datos de ventas
    $rspta = $consulta->ventasfechaproducto($fecha_inicio, $fecha_fin, $idproducto, $idvendedor, $idsucursal);

    $data = array();

    while ($reg = $rspta->fetch_object()) {

        // ✅ Variables ya alineadas con tu SQL
        $check_precio   = isset($reg->check_precio) ? $reg->check_precio : 0;
        $cantidad_total = floatval($reg->cantidad_total);
        $precio_total   = floatval($reg->precio_total);
        $precioCompra   = floatval($reg->precioCompra);
        $utilidadSC     = floatval($reg->utilidadSC);
        $margenUtilidad = floatval($reg->margen_utilidad);

        // Evitar divisiones por cero o valores nulos
        if ($cantidad_total <= 0) $cantidad_total = 1;

        // Calcular precio unitario (para visualización)
        $precio_unitario = ($precio_total / $cantidad_total);

        // 🔹 Mostrar correctamente el subtotal según check_precio
        $subtotal = ($check_precio == 1) ? $precio_total : $cantidad_total * $precio_unitario;

        // 🔹 Calcular utilidad final (por si deseas mostrar o usar)
        $utilidadFinal = $subtotal - $precioCompra;

        $data[] = array(
            "0" => $reg->fecha_kardex,
            "1" => "<strong>" . htmlspecialchars($reg->nombre) . " - " . htmlspecialchars($reg->contenedor) . "</strong>",
            "2" => '<span>' . number_format($cantidad_total, 2, '.', ',') . '</span> - <span>' . htmlspecialchars($reg->unidadmedida) . '</span>',
            "3" => "<span class='precio-venta'>S/. " . number_format($subtotal, 2, '.', ',') . "</span>",
            "4" => "<span class='precio-compra'>S/. " . number_format($precioCompra, 2, '.', ',') . "</span>",
            "5" => "<span>S/. " . number_format($utilidadSC, 2, '.', ',') . "</span>",
            "6" => "<span>" . number_format($margenUtilidad, 2, '.', ',') . "%</span>",
            "7" => htmlspecialchars($reg->nombreVendedor)
        );
    }

    $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    );

    echo json_encode($results);
break;



		case 'ventasfechaproducto2':
		    $fecha_inicio = $_REQUEST["fecha_inicio"];
		    $fecha_fin = $_REQUEST["fecha_fin"];
		    $idproducto = $_REQUEST["idproducto"];
		    $idvendedor = $_REQUEST["idvendedor"];
		    $idsucursal = $_REQUEST["idsucursal"];

		    if($idproducto == NULL){
		        $idproducto = "Todos";
		    }

		    if($idvendedor == NULL){
		        $idvendedor = "Todos";
		    }

		    if($idsucursal != "Todos"){
		        if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
		            $idsucursal = "Todos";
		        }else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
		            $idsucursal = $_SESSION['idsucursal'];
		        }else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
		            $idsucursal = $idsucursal;
		        }else{
		            $idsucursal = $_REQUEST["idsucursal"];
		        }
		    }

		    $rspta = $consulta->ventasfechaproducto2($fecha_inicio, $fecha_fin, $idproducto, $idvendedor, $idsucursal);

		    // Array para almacenar los datos
		    $data = array();

		    while ($reg = $rspta->fetch_object()) {
		        // Cálculo de la utilidad y barra de progreso
		        $nombreProducto = $reg->nombre . " - " . $reg->contenedor;
		        $precioVenta = number_format($reg->precio, 2, '.', ',');
		        $precioCompra = number_format($reg->precioCompra, 2, '.', ',');
		        $utilidad = number_format($reg->utilidadSC, 2, '.', ',');
		        $margenUtilidad = number_format($reg->margen_utilidad, 2, '.', ',');
		        $nombreVendedor = $reg->nombreVendedor;

		        // Calcular el porcentaje de la utilidad
		        $utilidadPorcentaje = ($reg->precio - $reg->precioCompra) / $reg->precio * 100; 

		        // Agregar la fila de datos con HTML para el frontend
		        $data[] = array(
		            //"0" => $reg->fecha_hora,
		            "0" => "<strong>" . $nombreProducto . "</strong>", // Resaltado del nombre
		            "1" => '<span>' . number_format($reg->cantidad, 0, '.', ',') . '</span> - <span>UND</span>',
		            "2" => "<span class='precio-venta'>" . "S/. " . $precioVenta . "</span>", // Resaltar precio de venta
		            "3" => "<span class='precio-compra'>" . "S/. " . $precioCompra . "</span>", // Resaltar precio de compra
		            "4" => "<span>" . "S/. " . $utilidad . "</span>"
		        );
		    }

		    $results = array(
		        "sEcho" => 1,
		        "iTotalRecords" => count($data),
		        "iTotalDisplayRecords" => count($data),
		        "aaData" => $data
		    );

		    echo json_encode($results);

		break;


		// Suponiendo que este es tu controlador y estás generando una respuesta HTML:
		case 'ventasfechaproductoproveedor':
			    $fecha_inicio = $_REQUEST["fecha_inicio"];
			    $fecha_fin = $_REQUEST["fecha_fin"];
			    $idproducto = $_REQUEST["idproducto"];
			    $idproveedor = $_REQUEST["idproveedor"];
			    $idsucursal = $_REQUEST["idsucursal"];

			    if($idproducto == NULL){
				$idproducto = "Todos";
					}
					if($idproveedor == NULL){
						$idproveedor = "Todos";
					}
					if($idsucursal != "Todos"){
						if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
							$idsucursal = "Todos";					
						}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
								$idsucursal = $_SESSION['idsucursal'];
						}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){	
							$idsucursal = $idsucursal;	
						}else{	
							$idsucursal = $_REQUEST["idsucursal"];	
						}
					}

			    // Lógica de consulta...
			    $rspta = $consulta->ventasfechaproductoproveedor($fecha_inicio, $fecha_fin, $idproducto, $idproveedor, $idsucursal);
			    
			    // Declaramos un array para los datos
			    $data = array();
			    while ($reg = $rspta->fetch_object()) {
			        $data[] = array(
			            "0" => '<span class="fecha-kardex">' . $reg->fecha_kardex . '</span>',
			            "1" => '<span class="comprobante-info">' . $reg->tipo_comprobante . " - " . $reg->serie_comprobante . " - " . $reg->num_comprobante . '</span>',
			            "2" => '<span class="proveedor-name">' . $reg->proveedor . '</span>',
			            "3" => '<span class="producto-name">' . $reg->nombre . '</span>',
			            "4" => '<span class="cantidad">' . '<span class="cantidad-num">' . intval($reg->cantidad) . '</span> <span class="unidad">UND</span>' . '</span>',
			            "5" => '<span class="precio"><b>S/ ' . number_format($reg->precio, 2, ".", ",") . '</b></span>'
			        );
			    }

			    // Respuesta para el DataTable
			    $results = array(
			        "sEcho" => 1,
			        "iTotalRecords" => count($data),
			        "iTotalDisplayRecords" => count($data),
			        "aaData" => $data
			    );

			    // Si es una respuesta JSON (como en el caso de DataTables)
			    echo json_encode($results);
		    
		    break;


		case 'kardex':
			
			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idproducto = $_REQUEST["idproducto"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			if($idproducto == NULL){
				$idproducto = "Todos";
			}

			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}
		$rspta=$consulta->listarKardex($fecha_inicio, $fecha_fin, $idproducto, $idvendedor, $idsucursal);
 		//Vamos a declarar un array
 		$data= Array();

		$contador = 0;
		$stockUltimo = 0;

 		while ($reg=$rspta->fetch_object()){

			/*if($reg->cantidad > 0 && $contador == 0){
				$reg->stock = $reg->cantidad;
				$stockInicial = $reg->stock;
			}

			if($reg->cantidad > 0 && $reg->salida == 0 && $contador > 0){
				$reg->stock = $stockUltimo + $reg->cantidad;
			}else if($reg->cantidad == 0 && $reg->salida > 0 && $contador > 0){
				$reg->stock = $stockUltimo - $reg->salida;
			}*/

 			$data[]=array(
 				"0"=>$reg->fecha_kardex,
 				"1"=>$consulta->verSucursal($reg->idsucursal),
 				"2"=>$consulta->verProducto($reg->idproducto),
 				"3"=>$reg->motivo,
 				"4"=>$reg->tipo_movimiento == 0?'<span class="badge badge-neon neon-green">Entrada</span>':'<span class="badge badge-neon neon-red">Salida</span>',
 				"5"=>$reg->cantidad.' Und.',
 				"6"=>'S/. '.$reg->precio_unitario,
 				"7"=>'S/. '.number_format(($reg->cantidad/$reg->cantidad_contenedor) * $reg->precio_unitario, 2, ".", ","),
 				"8"=>$reg->cantidad_contenedor == 1 ?$reg->stock_actual:$reg->stock_actual.' - '.floor($reg->stock_actual/$reg->cantidad_contenedor),
 				"9"=>'S/. '.number_format(($reg->stock_actual/$reg->cantidad_contenedor) * $reg->precio_unitario, 2, ".", ",")
 			);

 		}

 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;

		case 'ventadetallecomprobante':
			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idproducto = $_REQUEST["idproducto"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];
			$tipo_comprobante = $_REQUEST["tipo_comprobante"];

			if($idproducto == NULL){
				$idproducto = "Todos";
			}

			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}
			

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->VentaDetalleComprobante($fecha_inicio, $fecha_fin, $idproducto, $idvendedor, $idsucursal,$tipo_comprobante);

			//Vamos a declarar un array
			$data = array();

			while ($reg = $rspta->fetch_object()) {
				$data[] = array(
					"0" => '<span style="font-weight: bold;">'.$reg->nombre.'</span>' . " - " . '<span class="badge bg-green">'.$reg->contenedor.'</span>',
					"1" => '<span class="badge bg-primary">'.$reg->comprobante.'</span>',
					"2" => intval($reg->cantidad),
					"3" => number_format($reg->precio, 2),
					"4" => number_format($reg->precioCompra, 2),
					"5" => number_format($reg->utilidad, 2),
					"6" => $reg->nombreVendedor
				);
			}
			$results = array(
				"sEcho" => 1, //Información para el datatables
				"iTotalRecords" => count($data), //enviamos el total registros al datatable
				"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
				"aaData" => $data
			);
			echo json_encode($results);

		break;

		case 'ventadetallecomprobante2':
			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idproducto = $_REQUEST["idproducto"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];
			if($idproducto == NULL){
				$idproducto = "Todos";
			}

			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}
			

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->VentaDetalleComprobante2($fecha_inicio, $fecha_fin, $idproducto, $idvendedor, $idsucursal);

				// Declarar array
				$data = array();

				while ($reg = $rspta->fetch_object()) {
				    // Si total_venta no existe, se asigna 0
				    $totalVenta = isset($reg->precio) ? $reg->precio : 0;
				    
				    // Si el abono es igual al total venta, está cancelado; si no, está por cancelar
				    $cancelado = ($reg->abonos == $totalVenta) 
				                 ? '<span class="badge bg-success">Cancelado</span>' 
				                 : '<span class="badge bg-danger">Por Cancelar</span>';
				    
				    // Si el abono es menor que el precioCompra, la utilidad se mostrará en 0
				    $utilidad = ($reg->abonos < $reg->precioCompra) ? 0 : $reg->utilidad;
				    
				    $data[] = array(
				        "0" => '<span class="badge bg-primary">' . $reg->comprobante . ' - ' . $reg->serie_comprobante . ' - ' . $reg->num_comprobante . '</span>',
				        "1" => number_format($reg->precio, 2),
				        "2" => number_format($reg->precioCompra, 2),
				        "3" => number_format($reg->abonos, 2),
				        "4" => number_format($utilidad, 2),
				        "5" => $cancelado
				    );
				}


			$results = array(
				"sEcho" => 1, //Información para el datatables
				"iTotalRecords" => count($data), //enviamos el total registros al datatable
				"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
				"aaData" => $data
			);
			echo json_encode($results);

		break;





		case 'listarp':
			$rspta = $consulta->stockproductosmasbajos();
			//Vamos a declarar un array
			$data = array();

			while ($reg = $rspta->fetch_object()) {
				$data[] = array(
					"0" => $reg->nombre,
					"1" => $reg->categoria,
					"2" => $reg->stock
				);
			}
			$results = array(
				"sEcho" => 1, //Información para el datatables
				"iTotalRecords" => count($data), //enviamos el total registros al datatable
				"iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
				"aaData" => $data
			);
			echo json_encode($results);

			break;

			case 'totalVentas':
				$rspta=$consulta->ventasultimos_12meses();
				$reg=$rspta->fetch_all();
				echo json_encode($reg);
		
			break;

			/*case 'productosmasvendidos':
				$rspta=$consulta->productosmasvendidos();
				$reg=$rspta->fetch_all();
				echo json_encode($reg);
		
			break;*/

			case 'utilidades12meses':
			    $idvendedor = $_REQUEST["idvendedor"] ?? "Todos"; // Usar null coalescing operator
			    $idsucursal = $_REQUEST["idsucursal"] ?? "Todos"; // Usar null coalescing operator

			    // Llamar a la función para obtener los datos
			    $rspta = $consulta->utilidadUltimos12Meses($idvendedor, $idsucursal);
			    $reg = $rspta->fetch_all(MYSQLI_ASSOC); // Obtener datos como array asociativo

			    // Prepara los datos para el gráfico
			    $labels = [];
			    $data = [];
			    foreach ($reg as $row) {
			        $labels[] = $row['mes']; // Mes
			        $data[] = floatval($row['total_utilidad']); // Total de utilidad
			    }

			    // Devuelve un JSON con las etiquetas y los datos
			    echo json_encode(['labels' => $labels, 'data' => $data]);
			    break;


		   case 'ingresos_egresos':
			    $rspta = $consulta->IngresosEgresosMesesDelAnio();

			    $labels = [];
			    $ingresos = [];
			    $egresos = [];

			    while ($reg = $rspta->fetch_object()) {
			        $labels[] = ucfirst($reg->mes); // Poner mayúscula inicial
			        $ingresos[] = floatval($reg->ingresos);
			        $egresos[] = floatval($reg->egresos);
			    }

			    echo json_encode([
			        "labels" => $labels,
			        "ingresos" => $ingresos,
			        "egresos" => $egresos
			    ]);
			break;


			case 'productosmasvendidos':
			    $rspta = $consulta->productosmasvendidos();
			    $reg = [];
			    while ($row = $rspta->fetch_assoc()) {
			        $reg[] = [
			            'nombre' => $row['nombre'],
			            'cantidad' => (int)$row['cantidad']
			        ];
			    }
			    echo json_encode($reg);
		    break;



			case 'totalCompras':
				$rspta=$consulta->comprasultimos_10dias();
				$reg=$rspta->fetch_all();
				echo json_encode($reg);
		
			break;

		case 'totalDocumentosPendientes2':

			if($_SESSION['idsucursal'] != 0){

				$idsucursal = $_SESSION['idsucursal'];

			}else{

				$idsucursal = $_REQUEST['idsucursal'];

			}


			$rspta = $consulta->totalDocumentosPendientes2($idsucursal);
			echo json_encode($rspta);

		break;

		case 'mostrarTotalBoletasCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalBoletasCaja($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalBoletasTCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalBoletasTCaja($fecha_inicio, $fecha_fin,$idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;



		case 'mostrarTotalFacturasCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalFacturasCaja($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalFacturasTCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalFacturasTCaja($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalNotasVentaCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalNotasVentaCaja($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalNotasVentaTCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalNotasVetnaTCaja($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalCuentasCobrarVentaCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalCuentasCobrarVentaCaja($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalCuentasCobrarVentaTCaja':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalCuentasCobrarVentaTCaja($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalEfectivo':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->totalEfectivo($fecha_inicio, $fecha_fin,$idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalTransferencia':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalTransferencia($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalIngresos':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalIngresos($fecha_inicio, $fecha_fin,$idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalEgresos':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalEgresos($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'mostrarTotalIngresosTar':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->mostrarTotalIngresosTar($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'totalFacturas':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalFacturas($fecha_inicio, $fecha_fin, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalBoletas':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalBoletas($fecha_inicio, $fecha_fin, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalNotas':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalNotas($fecha_inicio, $fecha_fin, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalCuentas':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalCuentas($fecha_inicio, $fecha_fin, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalT':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];
			$idvendedor = $_REQUEST["idvendedor"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}

			if($idsucursal != "Todos"){

				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){

					$idsucursal = "Todos";
					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){
	
					$idsucursal = $_SESSION['idsucursal'];
	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){
	
					$idsucursal = $idsucursal;
	
				}else{
	
					$idsucursal = $_REQUEST["idsucursal"];
	
				}

			}

			$rspta = $consulta->totalT($fecha_inicio, $fecha_fin, $idsucursal,$idvendedor);
			echo json_encode($rspta);

			break;

		case 'totalEC':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalEC($fecha_inicio, $fecha_fin, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalStocksBajos':

			$rspta = $consulta->totalStocksBajos();
			echo json_encode($rspta);

			break;

		case 'totalCreditoPendientes':

			$rspta = $consulta->totalCreditoPendientes();
			echo json_encode($rspta);

			break;

		case 'totalcomprahoy':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalcomprahoy($fecha_inicio, $fecha_fin, $idvendedor,$idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalcomprahoyc':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalcomprahoyC($fecha_inicio, $fecha_fin, $idvendedor,$idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalcomprahoyefectivo':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalcomprahoyefectivo($fecha_inicio, $fecha_fin, $idvendedor,$idsucursal);
			echo json_encode($rspta);

			break;	

		case 'totalcomprahoyyape':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalcomprahoyyape($fecha_inicio, $fecha_fin, $idvendedor,$idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalcomprahoyplin':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalcomprahoyplin($fecha_inicio, $fecha_fin, $idvendedor,$idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalcomprahoyop':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalcomprahoyop($fecha_inicio, $fecha_fin, $idvendedor,$idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalventahoy':

    // Recuperar los datos de entrada desde el request
    $fecha_inicio = $_REQUEST["fecha_inicio"];
    $fecha_fin = $_REQUEST["fecha_fin"];
    $idvendedor = $_REQUEST["idvendedor"];
    $idsucursal = $_REQUEST["idsucursal"];

    // Llamar a la función que calcula el total de ventas de hoy
    $rspta = $consulta->totalventahoy($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal);

    // Verificar si 'total_venta' está presente y formatear con dos decimales
    if (isset($rspta['total_venta'])) {
        $rspta['total_venta'] = number_format((float)$rspta['total_venta'], 2, '.', '');
    }

    // Enviar respuesta formateada en JSON
    echo json_encode($rspta);

    break;

		case 'totalutilidadhoy':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalutilidadhoy($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalusuariosr':

			$rspta = $consulta->totalusuariosr();
			echo json_encode($rspta);

			break;

		case 'totalproveedoresr':

			$rspta = $consulta->totalproveedoresr();
			echo json_encode($rspta);

			break;

		case 'totalventachoy':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalventachoy($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalcuentasporcobrar':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalcuentasporcobrar($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalabonoscobrados':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];
			if($idvendedor == NULL){
				$idvendedor = "Todos";
			}
			if($idsucursal != "Todos"){
				if($idsucursal == null AND $_SESSION['idsucursal'] == 0){
					$idsucursal = "Todos";					
				}else if($idsucursal == null AND $_SESSION['idsucursal'] != 0){	
					$idsucursal = $_SESSION['idsucursal'];	
				}else if($idsucursal != null AND $_SESSION['idsucursal'] == 0){	
					$idsucursal = $idsucursal;	
				}else{	
					$idsucursal = $_REQUEST["idsucursal"];	
				}
			}
			$rspta = $consulta->totalabonoscobrados($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalcuentasporpagar':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalcuentasporpagar($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal);
			echo json_encode($rspta);

			break;

		case 'totalabonospagados':

			$fecha_inicio = $_REQUEST["fecha_inicio"];
			$fecha_fin = $_REQUEST["fecha_fin"];
			$idvendedor = $_REQUEST["idvendedor"];
			$idsucursal = $_REQUEST["idsucursal"];

			$rspta = $consulta->totalabonospagados($fecha_inicio, $fecha_fin, $idvendedor, $idsucursal);
			echo json_encode($rspta);

			break;


		case 'totalcategorias':

			$rspta = $consulta->totalcategorias();
			echo json_encode($rspta);

			break;

		case 'totalproductos':

			$rspta = $consulta->totalproductos();
			echo json_encode($rspta);

			break;

		case 'listarStocksBajos':

			$rspta = $consulta->stockproductosmasbajos();

			while ($reg = $rspta->fetch_object()) {

				echo '<li>
					<a href="producto.php">
                      <i class="fa fa-warning text-yellow"></i> ' . $reg->nombre . '
                    </a>
                  </li>';
			}

			break;

		case 'listarCreditosPendientes':

			$rspta = $consulta->listarCreditosPendientes();

			while ($reg = $rspta->fetch_object()) {

				echo '<li>
					<a href="cuentasxcobrar.php">
                      <i class="fa fa-warning text-yellow"></i> Comprobante: ' . $reg->tipo_comprobante . ' - ' . $reg->serie_comprobante . ' - ' . $reg->num_comprobante . '
                    </a>
                  </li>';
			}

			break;

		case 'totalDocumentosPendientes':

			$rspta = $consulta->totalDocumentosPendientes();
			echo json_encode($rspta);

			break;

		case 'listarDocumentosPendientes':

			$rspta = $consulta->listarDocumentosPendientes();

			while ($reg = $rspta->fetch_object()) {

				echo '<li>
					<a href="venta.php">
                      <i class="fa fa-warning text-yellow"></i> Comprobante: ' . $reg->tipo_comprobante . ' - ' . $reg->serie_comprobante . ' - ' . $reg->num_comprobante . '
                    </a>
                  </li>';
			}

		break;

		case 'ProductosVencer':

			if($_SESSION['idsucursal'] != 0){

				$idsucursal = $_SESSION['idsucursal'];

			}else{

				$idsucursal = $_REQUEST['idsucursal'];

			}

			$rspta = $consulta->listarDocumentosPendientes2($idsucursal);

			while ($reg = $rspta->fetch_object()) {

				if($reg->numserie == ""){
					$reg->numserie = "-";
				}

				echo '<li>
					<a href="venta.php">
                      <i class="fa fa-warning text-yellow"></i>' . $reg->nombre . ' - UM: ' . $reg->unidadmedida . ' - Lote: ' . $reg->numserie . '
                    </a>
                  </li>';
			}

		break;

	}
}
ob_end_flush();
