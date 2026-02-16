<?php
require_once "../modelos/Producto.php";
session_start();
if (!isset($_SESSION['idusuario']) || empty($_SESSION['idusuario'])) {
    echo json_encode([
        'status' => 0,
        'message' => 'Sesión expirada. Inicie sesión nuevamente para continuar.'
    ]);
    exit;
}
$producto = new Producto();

$idproducto = isset($_POST["idproducto"]) ? limpiarCadena($_POST["idproducto"]) : "";
$idsucursal = isset($_POST["idsucursal"]) ? limpiarCadena($_POST["idsucursal"]) : "";
$idsucursal2 = isset($_POST["idsucursal2"]) ? limpiarCadena($_POST["idsucursal2"]) : "";
$idcategoria = isset($_POST["idcategoria"]) ? limpiarCadena($_POST["idcategoria"]) : "";
$idunidad_medida = isset($_POST["idunidad_medida"]) ? limpiarCadena($_POST["idunidad_medida"]) : "";
$idrubro = isset($_POST["idrubro"]) ? limpiarCadena($_POST["idrubro"]) : "";
$idcondicionventa = isset($_POST["idcondicionventa"]) ? limpiarCadena($_POST["idcondicionventa"]) : "";
$registrosan = isset($_POST["registrosan"]) ? limpiarCadena($_POST["registrosan"]) : "";
$fabricante = isset($_POST["fabricante"]) ? limpiarCadena($_POST["fabricante"]) : "";
$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$stock = isset($_POST["stock"]) ? limpiarCadena($_POST["stock"]) : "";
$stockMinimo = isset($_POST["stockMinimo"]) ? limpiarCadena($_POST["stockMinimo"]) : "";
$precio = isset($_POST["precio"]) ? limpiarCadena($_POST["precio"]) : "";
$preciocigv = isset($_POST["preciocigv"]) ? limpiarCadena($_POST["preciocigv"]) : "";
$precioB = isset($_POST["precioB"]) ? limpiarCadena($_POST["precioB"]) : "";
$precioC = isset($_POST["precioC"]) ? limpiarCadena($_POST["precioC"]) : "";
$precioD = isset($_POST["precioD"]) ? limpiarCadena($_POST["precioD"]) : "";
$precioE = isset($_POST["precioE"]) ? limpiarCadena($_POST["precioE"]) : "";
$margenpubl = isset($_POST["margenpubl"]) ? limpiarCadena($_POST["margenpubl"]) : "";
$margendes = isset($_POST["margendes"]) ? limpiarCadena($_POST["margendes"]) : "";
$margenp1 = isset($_POST["margenp1"]) ? limpiarCadena($_POST["margenp1"]) : "";
$margenp2 = isset($_POST["margenp2"]) ? limpiarCadena($_POST["margenp2"]) : "";
$margendist = isset($_POST["margendist"]) ? limpiarCadena($_POST["margendist"]) : "";
$utilprecio = isset($_POST["utilprecio"]) ? limpiarCadena($_POST["utilprecio"]) : "";
$utilprecioB = isset($_POST["utilprecioB"]) ? limpiarCadena($_POST["utilprecioB"]) : "";
$utilprecioC = isset($_POST["utilprecioC"]) ? limpiarCadena($_POST["utilprecioC"]) : "";
$utilprecioD = isset($_POST["utilprecioD"]) ? limpiarCadena($_POST["utilprecioD"]) : "";
$utilprecioE = isset($_POST["utilprecioE"]) ? limpiarCadena($_POST["utilprecioE"]) : "";
$precioCompra = isset($_POST["precioCompra"]) ? limpiarCadena($_POST["precioCompra"]) : "";
$fecha = isset($_POST["fecha_hora"]) ? limpiarCadena($_POST["fecha_hora"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";
$modelo = isset($_POST["modelo"]) ? limpiarCadena($_POST["modelo"]) : "";
$nserie = isset($_POST["nserie"]) ? limpiarCadena($_POST["nserie"]) : "";


$tipoigv = isset($_POST["tipoigv"]) ? limpiarCadena($_POST["tipoigv"]) : "";
$comisionV = isset($_POST["comisionV"]) ? limpiarCadena($_POST["comisionV"]) : "";

$idproductoE = isset($_POST["idproductoE"]) ? limpiarCadena($_POST["idproductoE"]) : "";
$idproductoD = isset($_POST["idproductoD"]) ? limpiarCadena($_POST["idproductoD"]) : "";
$cantidadE = isset($_POST["cantidadE"]) ? limpiarCadena($_POST["cantidadE"]) : "";
$cantidadD = isset($_POST["cantidadD"]) ? limpiarCadena($_POST["cantidadD"]) : "";
$productoEmpaquetado = isset($_POST["productoE"]) ? limpiarCadena($_POST["productoE"]) : "";
$productoDesempaquetar = isset($_POST["productoD"]) ? limpiarCadena($_POST["productoD"]) : "";

$almacenOrigen = isset($_POST["idsucursal3"]) ? limpiarCadena($_POST["idsucursal3"]) : "";
$almacenDestino = isset($_POST["idsucursal4"]) ? limpiarCadena($_POST["idsucursal4"]) : "";
$productoTrasladar = isset($_POST["idproducto2"]) ? limpiarCadena($_POST["idproducto2"]) : "";
$productoTraslado = isset($_POST["idproducto3"]) ? limpiarCadena($_POST["idproducto3"]) : "";
$cantidadTrasladar = isset($_POST["cantidadT"]) ? limpiarCadena($_POST["cantidadT"]) : "";
function tienePermiso($modulo, $submodulo, $accion) {
    return isset($_SESSION['acciones'][$modulo][$submodulo][$accion]) && $_SESSION['acciones'][$modulo][$submodulo][$accion] === true;
}

switch ($_GET["op"]) {
	case 'guardaryeditar':

		if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
			$imagen = $_POST["imagenactual"];
		} else {
			$ext = explode(".", $_FILES["imagen"]["name"]);
			if ($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png") {
				$imagen = round(microtime(true)) . '.' . end($ext);
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/productos/" . $imagen);
			}
		}
		if (empty($idproducto)) {
			$rspta = $producto->insertar($idsucursal, $idcategoria, $idunidad_medida, $idrubro, $idcondicionventa, $registrosan, $fabricante, $codigo, strtoupper($nombre), $stock, $stockMinimo, $precio, $preciocigv, $precioB, $precioC, $precioD, $precioE, $margenpubl, $margendes, $margenp1, $margenp2, $margendist, $utilprecio, $utilprecioB, $utilprecioC, $utilprecioD, $utilprecioE, $precioCompra, $fecha, $descripcion, $imagen, $modelo, $nserie, $tipoigv, $comisionV, $_POST['sucursales']);
			echo $rspta ? "Datos registrados correctamente" : "No se pudo completar el registro";
		} else {
			$rspta = $producto->editar($idproducto, $idsucursal, $idcategoria, $idunidad_medida, $idrubro, $idcondicionventa, $registrosan, $fabricante, $codigo, $nombre, $stock, $stockMinimo, $precio, $preciocigv, $precioB, $precioC, $precioD, $precioE, $margenpubl, $margendes, $margenp1, $margenp2, $margendist, $utilprecio, $utilprecioB, $utilprecioC, $utilprecioD, $utilprecioE, $precioCompra, $fecha, $descripcion, $imagen, $modelo, $nserie, $tipoigv, $comisionV);
			echo $rspta ? "Datos actualizados" : "No se pudo actualizar";
		}
		break;

	case 'guardarimagenes':
	  $idsucursal = $_SESSION['idsucursal'];

	  // Primero eliminamos las imágenes anteriores
	  $rspta1 = $producto->eliminarImagenesCatalogo($idsucursal);

	  $orden = 1;
	  foreach ($_FILES['imagenes']['name'] as $key => $nombre) {
	    $tmp = $_FILES['imagenes']['tmp_name'][$key];
	    $nombre_final = uniqid() . "_" . basename($nombre);
	    move_uploaded_file($tmp, "../files/" . $nombre_final);
	    $producto->guardarImagenCatalogo($idsucursal, $nombre_final, $orden++);
	  }
	  echo json_encode(["status" => "ok"]);
	  break;

	case 'obtenerimagenes':
	  $rspta = $producto->obtenerImagenesCatalogo($_SESSION['idsucursal']);
	  $data = [];
	  while ($reg = $rspta->fetch_object()) {
	    $data[] = $reg;
	  }
	  echo json_encode($data);
	  break;

	case 'guardarimagenindividual':
	  $idsucursal = $_SESSION['idsucursal'];

	  if (isset($_FILES['imagen'])) {
	    $tmp = $_FILES['imagen']['tmp_name'];
	    $nombre = $_FILES['imagen']['name'];
	    $nombre_final = uniqid() . "_" . basename($nombre);

	    if (move_uploaded_file($tmp, "../files/" . $nombre_final)) {
	      $orden = 1; // O calcula último orden si deseas ordenarlos
	      $producto->guardarImagenCatalogo($idsucursal, $nombre_final, $orden);
	      echo json_encode(["status" => "ok"]);
	    } else {
	      echo json_encode(["status" => "error", "msg" => "No se pudo mover el archivo"]);
	    }
	  } else {
	    echo json_encode(["status" => "error", "msg" => "No se recibió imagen"]);
	  }
	  break;

	case 'eliminarimagenindividual':
	  $nombre_imagen = isset($_POST['nombre_imagen']) ? limpiarCadena($_POST['nombre_imagen']) : '';

	  if (!empty($nombre_imagen)) {
	    $rspta = $producto->eliminarImagenCatalogo($nombre_imagen);

	    $ruta = "../files/" . $nombre_imagen;
	    if (file_exists($ruta)) {
	      if (unlink($ruta)) {
	        echo json_encode(['status' => 'ok']);
	      } else {
	        echo json_encode(['status' => 'error', 'msg' => 'No se pudo eliminar el archivo físico']);
	      }
	    } else {
	      echo json_encode(['status' => 'ok']); // Imagen ya no existe en disco, pero se eliminó de BD
	    }
	  } else {
	    echo json_encode(['status' => 'error', 'msg' => 'Nombre de imagen no recibido']);
	  }
	  break;

	case 'obtenercategorias':
	  $rspta = $producto->selectcateg(); // Asegúrate de tener este método en tu modelo
	  $data = [];
	  while ($reg = $rspta->fetch_object()) {
	    $data[] = $reg;
	  }
	  echo json_encode($data);
	  break;

	case 'obtenerprecios':
	  $rspta = $producto->obtenerPrecios();
	  $data = [];
	  while ($reg = $rspta->fetch_object()) {
	    $data[] = $reg;
	  }
	  echo json_encode($data);
	  break;

	case 'listarStockBajoAlert':
		$fechaActual = date('Y-m-d');
		$idsucursal2 = $_GET['idsucursal2'];

		// Si la sucursal seleccionada es 0, se consultan todos los productos
		if ($_SESSION['idsucursal'] == 0) {
			$rspta = $producto->listarstock22($idsucursal2, $_SESSION['idsucursal']);
		} else {
			$rspta = $producto->listarstock33($idsucursal2, $_SESSION['idsucursal']);
		}

		// Array para almacenar los productos
		$productosBajosStock = [];

		while ($reg = $rspta->fetch_object()) {
			$productosBajosStock[] = [
				"idproducto" => $reg->idproducto,
				"nombre" => $reg->nombre,
				"stock" => $reg->stock,
				"imagen" => $reg->imagen
			];
		}

		// Devolver los datos como JSON
		echo json_encode($productosBajosStock);
		break;

	case 'actualizarProductoEmpaquetado':
		$rspta = $producto->desempaquetar($idproductoE, $idproductoD, $cantidadE, $cantidadD, $productoEmpaquetado, $productoDesempaquetar);
		echo $rspta ? "Producto desempaquetado" : "Producto no se puede desempaquetar";
		break;

	case 'trasladarProducto':
		$rspta = $producto->trasladar($almacenOrigen, $almacenDestino, $productoTrasladar, $productoTraslado, $cantidadTrasladar);
		echo $rspta ? "Producto Traslado" : "Producto no se puede Trasladar";
		break;

	case 'desactivar':
		$rspta = $producto->desactivar($idproducto);
		echo $rspta ? "Producto Desactivado" : "Producto no se puede desactivar";
		break;

	case 'activar':
		$rspta = $producto->activar($idproducto);
		echo $rspta ? "Producto activado" : "Producto no se puede activar";
		break;

	case 'mostrar':
		$rspta = $producto->mostrar($idproducto);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);
		break;

	case 'porcentaje':
		$rspta = $producto->porcentaje($idcategoria);
		//Codificar el resultado utilizando json
		echo json_encode($rspta);
		break;

	case 'mostrarProducto':
		$rspta = $producto->mostrarProducto($idproducto);
		echo json_encode($rspta);
		break;

	case 'sucursales':
	    $idusuario = $_SESSION['idusuario']; // usuario logueado
	    $idsucursalSeleccionada = isset($_POST['idsucursal']) ? $_POST['idsucursal'] : $_SESSION['idsucursal'];
	    
	    $rspta = $producto->listarsucursales($idusuario);

	    while ($reg = $rspta->fetch_object()) {
	        $checked = ($reg->idsucursal == $idsucursalSeleccionada) ? 'checked' : '';
	        echo '<li>
	                <input type="checkbox" name="sucursales[]" value="' . $reg->idsucursal . '" ' . $checked . '> 
	                ' . htmlspecialchars($reg->nombre) . '
	              </li>';
	    }
	    break;

	case 'listarServicio':

		$fechaActual = date('Y-m-d');

		$idsucursal2 = $_GET['idsucursal2'];

		if ($_SESSION['idsucursal'] == 0) {

			$rspta = $producto->listarS2($idsucursal2, $_SESSION['idsucursal']);
		} else {

			$rspta = $producto->listarS3($idsucursal2, $_SESSION['idsucursal']);
		}

		//Vamos a declarar un array
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => $reg->nombre,
				"1" => $reg->categoria,
				//"2"=>($reg->stock <= $reg->stock_minimo)?'<span class="badge bg-red">'.$reg->stock.'</span>':
				//'<span class="badge bg-green">'.$reg->stock.'</span>',
				"2" => ($reg->stock <= $reg->stock_minimo) ? '<span class="badge bg-red">' . $reg->stock . '</span>' :
					'<span class="badge bg-green">' . $reg->stock . '</span>',
				"3" => '<span class="badge bg-info">' . 'S/ ' . $reg->precio . '</span>',
				"4" => ($reg->condicion) ? '<span class="badge bg-green">ACTIVADO</span>' :
					'<span class="badge bg-red">DESACTIVADO</span>',
				"5" => ($reg->condicion) ? '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idproducto . ')"><i class="fas fa-edit"></i></button>' .
					' <button class="btn btn-success btn-xs" onclick=\'config(' . json_encode($reg) . ')\'><i class="fas fa-cog"></i></button> ' .
					' <button class="btn btn-danger btn-xs" onclick="desactivar(' . $reg->idproducto . ')"><i class="fas fa-times-circle"></i></button>' :
					'<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idproducto . ')"><i class="fas fa-edit"></i></button>' .
					' <button class="btn btn-primary btn-xs" onclick="activar(' . $reg->idproducto . ')"><i class="fa fa-check"></i></button>'
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


	case 'listar':
    $idsucursal2 = $_GET['idsucursal2'];
    $stock_filtro = isset($_GET["stock_filtro"]) ? floatval($_GET["stock_filtro"]) : 0;
    
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
    $search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
    $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;

    $es_admin = ($_SESSION['idsucursal'] == 0);
    
    $rspta = $producto->listarPaginado($idsucursal2, $_SESSION['idsucursal'], $stock_filtro, $start, $length, $search, $es_admin);
    $total = $producto->contarTotalPaginado($idsucursal2, $_SESSION['idsucursal'], $stock_filtro, $search);

    $data = array();

    while ($reg = $rspta->fetch_object()) {
        // Truncar nombre si es muy largo
        $nombre_corto = strlen($reg->nombre) > 80 ? substr($reg->nombre, 0, 50) . '...' : $reg->nombre;
        $descripcion_corta = strlen($reg->descripcion) > 40 ? substr($reg->descripcion, 0, 40) . '...' : $reg->descripcion;
        
        // Escapar correctamente para evitar problemas con comillas
        $nombre_tooltip = htmlspecialchars($reg->nombre, ENT_QUOTES, 'UTF-8');
        $descripcion_tooltip = htmlspecialchars($reg->descripcion, ENT_QUOTES, 'UTF-8');
        
        $data[] = array(
            "0" => '<div style="display:flex;gap:8px;align-items:flex-start;max-width:350px;">
                    <div class="img-container" onclick="verimagen(' . $reg->idproducto . ', \'' . addslashes($reg->imagen) . '\', \'' . addslashes($reg->nombre) . '\',\'' . $reg->stock . '\',\'' . addslashes($reg->categoria) . '\',\'' . addslashes($reg->registrosan) . '\',\'' . addslashes($reg->rubro) . '\',\'' . addslashes($reg->condicionventa) . '\',\'' . $reg->precio . '\',\'' . $reg->precio_compra . '\',\'' . $reg->precioB . '\',\'' . $reg->precioC . '\',\'' . $reg->precioD . '\',\'' . addslashes($reg->fabricante) . '\',\'' . addslashes($reg->descripcion) . '\')" style="width:45px;height:45px;flex-shrink:0;cursor:pointer;border-radius:3px;overflow:hidden;border:1px solid #ddd;">
                        <img src="files/productos/' . $reg->imagen . '" alt="' . $nombre_tooltip . '" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:bold;font-size:12px;margin-bottom:3px;cursor:help;" title="' . $nombre_tooltip . '">' . htmlspecialchars($nombre_corto) . '</div>
                        <div style="font-size:10px;">
                            <span class="badge badge-neon neon-green" style="font-size:9px;padding:1px 5px;">' . $reg->unidad . '</span> 
                            <span style="color:#666;cursor:help;" title="' . $descripcion_tooltip . '">' . htmlspecialchars($descripcion_corta) . '</span>
                        </div>
                    </div>
                </div>',
            "1" => $reg->categoria,
            "2" => $reg->fabricante,
            "3" => $reg->codigo,
            "4" => ($reg->stock <= $reg->stock_minimo) ? 
                '<span class="badge badge-neon neon-red">' . $reg->stock . '</span>' :
                '<span class="badge badge-neon neon-green">' . $reg->stock . '</span>',
            "5" => '<span class="editable-price badge badge-neon neon-blue" contenteditable="false" data-id="' . $reg->idproducto . '" data-field="precio">' . $reg->precio . '</span>',
            "6" => '<span class="editable-price badge badge-neon neon-yellow" contenteditable="false" data-id="' . $reg->idproducto . '" data-field="precio_compra">' . $reg->precio_compra . '</span>',
            "7" => ($reg->condicion) ? 
                '<span class="badge badge-neon neon-green">ACTIVADO</span>' :
                '<span class="badge badge-neon neon-red">DESACTIVADO</span>',
            "8" => ($reg->condicion) ?
                (tienePermiso('Almacen', 'Productos', 'Editar Productos') ? '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idproducto . ')"><i class="fas fa-edit"></i></button> ' : '') .
                (tienePermiso('Almacen', 'Productos', 'Movimientos Productos') ? '<button class="btn btn-primary btn-xs" onclick="entradaSalida(' . $reg->idproducto . ',' . $reg->idsucursal . ')"><i class="fas fa-archive"></i></button> ' : '') .
                (tienePermiso('Almacen', 'Productos', 'Configurar Productos') ? '<button class="btn btn-success btn-xs" onclick=\'config(' . json_encode($reg) . ')\'><i class="fas fa-cog"></i></button> ' : '') .
                (tienePermiso('Almacen', 'Productos', 'Listar Vencimientos') ? '<button class="btn btn-info btn-xs" onclick="fechaVencimiento(' . $reg->idproducto . ')"><i class="fa fa-list"></i></button> ' : '') .
                (tienePermiso('Almacen', 'Productos', 'Desactivar Productos') ? '<button class="btn btn-danger btn-xs" onclick="desactivar(' . $reg->idproducto . ')"><i class="fas fa-times-circle"></i></button> ' :'') .
                (tienePermiso('Almacen', 'Productos', 'Eliminar Productos') ? '<button class="btn btn-danger btn-xs" onclick="eliminarProducto(' . $reg->idproducto . ')"><i class="fas fa-trash"></i></button>' : '') :
                '<button class="btn btn-primary btn-xs" onclick="activar(' . $reg->idproducto . ')"><i class="fa fa-check"></i></button>'
        );
    }
    
    $results = array(
        "draw" => $draw,
        "recordsTotal" => $total,
        "recordsFiltered" => $total,
        "data" => $data
    );
    echo json_encode($results);

    break;

	case 'eliminar':
	    $idproducto = $_POST['idproducto'];

	    // Llamar al modelo para eliminar
	    $rspta = $producto->eliminar($idproducto);

	    if($rspta){
	        echo json_encode(['status' => true]);
	    } else {
	        echo json_encode(['status' => false, 'msg' => 'No se pudo eliminar el producto']);
	    }
	break;


	case "selectCategoria2":
		require_once "../modelos/Categoria.php";
		$categoria = new Categoria();

		$rspta = $categoria->select();
		echo '<option value="" selected>Seleccionar...</option>';
		while ($reg = $rspta->fetch_object()) {

			if ($reg->nombre == 'SERVICIO') {

				echo '<option value=' . $reg->idcategoria . '>' . $reg->nombre . '</option>';
			}
		}
		break;

	case "selectCategoria":
		require_once "../modelos/Categoria.php";
		$categoria = new Categoria();
		$rspta = $categoria->select();

		// Agrega manualmente la opción vacía
		echo '<option value="" selected>Seleccionar...</option>';

		while ($reg = $rspta->fetch_object()) {
			if ($reg->nombre != 'SERVICIO') {
				echo '<option value=' . $reg->idcategoria . '>' . $reg->nombre . '</option>';
			}
		}
		break;


	case "selectUnidadMedida":
		require_once "../modelos/UnidadMedida.php";
		$unidadmedida = new UnidadMedida();
		$rspta = $unidadmedida->select();

		// Agrega manualmente la opción vacía
		//echo '<option value="" selected></option>';

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idunidad_medida . '>' . $reg->nombre . '</option>';
		}
		break;

	case "selectRubro":
		require_once "../modelos/Rubro.php";
		$rubro = new Rubro();

		$rspta = $rubro->select();

		// Agrega manualmente la opción vacía
		echo '<option value="14" selected>Sin Rubro</option>';

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idrubro . '>' . $reg->nombre . '</option>';
		}
		break;

	case "selectCondicionVenta":
		require_once "../modelos/CondicionVenta.php";
		$condicionventa = new CondicionVenta();

		$rspta = $condicionventa->select();

		// Agrega manualmente la opción vacía
		echo '<option value="4" selected>Sin CD</option>';

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idcondicionventa . '>' . $reg->nombre . '</option>';
		}
		break;

	case 'mostrarStockProductoE':

		$idproducto = $_REQUEST["idproductoE"];

		$rspta = $producto->mostrarStockProductoE($idproducto);
		echo json_encode($rspta);

		break;

	case 'mostrarStockProductoD':

		$idproducto = $_REQUEST["idproductoD"];

		$rspta = $producto->mostrarStockProductoD($idproducto);
		echo json_encode($rspta);

		break;

	case 'guardarcategoria':
		if (empty($idcategoria)) {
			$rspta = $producto->insertarcat($nombre);
			echo $rspta ? "Categoría registrada" : "Categoría no se pudo registrar";
		}
		break;

	case 'mostrarUltimaCategoria':

		$rspta = $producto->mostrarUltimaCategoria();
		echo json_encode($rspta);

		break;

	case 'saveCofiguration':
		$idproducto = isset($_POST["idproducto"]) ? limpiarCadena($_POST["idproducto"]) : "";
		$jsonData = isset($_POST["configuraciones"]) ? $_POST["configuraciones"] : "";

		$rspta = $producto->saveCofigurationFromJson($jsonData, $idproducto);
		echo json_encode($rspta);
		break;

	case 'listCofiguration':
		$idproducto = isset($_GET["idproducto"]) ? limpiarCadena($_GET["idproducto"]) : "";
		$rspta = $producto->listCofiguration($idproducto);
		echo json_encode($rspta);
		break;

	case 'eliminarCofiguration':
		$idconfig = isset($_GET["idconfig"]) ? limpiarCadena($_GET["idconfig"]) : "";
		$rspta = $producto->eliminarCofiguration($idconfig);
		echo json_encode($rspta);
		break;

	case 'listarvencimiento':
	    ob_clean();
	    header('Content-Type: application/json');
	    
	    $idproducto = intval($_GET["id"]);
	    
	    // Calcular stock total
	    $sql_total = "SELECT SUM(cantidad_restante) as total_stock
	                  FROM stock_fifo
	                  WHERE idproducto='$idproducto' AND estado=1";
	    
	    $result_total = ejecutarConsultaSimpleFila($sql_total);
	    $total_stock_actual_producto = isset($result_total['total_stock']) ? $result_total['total_stock'] : 0;
	    
	    // Obtener nombre del producto
	    $sql_nombre = "SELECT nombre FROM producto WHERE idproducto='$idproducto'";
	    $result_nombre = ejecutarConsultaSimpleFila($sql_nombre);
	    $nombre_producto = isset($result_nombre['nombre']) ? $result_nombre['nombre'] : '';
	    
	    // Obtener el ID del lote activo
	    $sql_lote_activo = "SELECT idfifo 
	                        FROM stock_fifo 
	                        WHERE idproducto='$idproducto' 
	                        AND cantidad_restante > 0 
	                        AND estado=1 
	                        ORDER BY 
	                            CASE WHEN fvencimiento IS NULL THEN 1 ELSE 0 END,
	                            fvencimiento ASC, 
	                            fecha_ingreso ASC,
	                            idfifo ASC 
	                        LIMIT 1";
	    
	    $lote_activo_result = ejecutarConsultaSimpleFila($sql_lote_activo);
	    $idfifo_activo = isset($lote_activo_result['idfifo']) ? $lote_activo_result['idfifo'] : null;
	    
	    echo json_encode([
	        'nombre_producto' => $nombre_producto,
	        'total_stock' => number_format($total_stock_actual_producto,2),
	        'idfifo_activo' => $idfifo_activo
	    ]);
	    exit;
	break;

	case 'listarvencimiento_datatable':
	    ob_clean();
	    header('Content-Type: application/json');
	    
	    $idproducto = intval($_GET["id"]);
	    
	    // Parámetros de DataTables
	    $draw = isset($_GET['draw']) ? intval($_GET['draw']) : 1;
	    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
	    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
	    $searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
	    
	    // Ordenamiento
	    $orderColumnIndex = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 1;
	    $orderDir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
	    
	    $columns = ['', 'sf.fecha_ingreso', 'dc.fvencimiento', 'dias_transcurridos', 'sf.cantidad_ingreso', 'sf.cantidad_restante', 'dc.nlote', 'sf.precio_compra', 'sf.precio_venta'];
	    $orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'sf.fecha_ingreso';
	    
	    // Obtener el lote activo
	    $sql_lote_activo = "SELECT idfifo 
	                        FROM stock_fifo 
	                        WHERE idproducto='$idproducto' 
	                        AND cantidad_restante > 0 
	                        AND estado=1 
	                        ORDER BY 
	                            CASE WHEN fvencimiento IS NULL THEN 1 ELSE 0 END,
	                            fvencimiento ASC, 
	                            fecha_ingreso ASC,
	                            idfifo ASC 
	                        LIMIT 1";
	    
	    $lote_activo_result = ejecutarConsultaSimpleFila($sql_lote_activo);
	    $idfifo_activo = isset($lote_activo_result['idfifo']) ? $lote_activo_result['idfifo'] : null;
	    
	    // Consulta base
	    $sql_base = "FROM stock_fifo sf
	                 INNER JOIN producto p ON p.idproducto = sf.idproducto
	                 LEFT JOIN detalle_compra dc ON dc.iddetalle_compra = sf.referencia_id AND sf.origen = 'COMPRA'
	                 WHERE sf.idproducto = '$idproducto' AND sf.estado = 1";
	    
	    // Búsqueda
	    $whereSearch = "";
	    if (!empty($searchValue)) {
	        $whereSearch = " AND (dc.nlote LIKE '%$searchValue%' 
	                         OR DATE_FORMAT(sf.fecha_ingreso, '%d-%m-%Y') LIKE '%$searchValue%'
	                         OR DATE_FORMAT(dc.fvencimiento, '%d-%m-%Y') LIKE '%$searchValue%')";
	    }
	    
	    // Contar total de registros
	    $sql_count = "SELECT COUNT(*) as total $sql_base";
	    $total_records = ejecutarConsultaSimpleFila($sql_count)['total'];
	    
	    // Contar registros filtrados
	    $sql_count_filtered = "SELECT COUNT(*) as total $sql_base $whereSearch";
	    $total_filtered = ejecutarConsultaSimpleFila($sql_count_filtered)['total'];
	    
	    // Consulta con paginación
	    $sql = "SELECT
	                sf.idfifo,
	                sf.fecha_ingreso,
	                dc.fvencimiento,
	                DATEDIFF(dc.fvencimiento, NOW()) AS dias_transcurridos,
	                sf.cantidad_ingreso AS cantidad,
	                sf.cantidad_restante AS stock_lote,
	                dc.nlote,
	                sf.precio_compra,
	                sf.precio_venta
	            $sql_base $whereSearch
	            ORDER BY $orderColumn $orderDir
	            LIMIT $start, $length";
	    
	    $rspta = ejecutarConsulta($sql);
	    
	    $data = [];
	    $numero = $start + 1;
	    
	    while ($reg = $rspta->fetch_object()) {
	        $fecha_ingreso = date('d-m-Y', strtotime($reg->fecha_ingreso));
	        $fvencimiento = ($reg->fvencimiento != '0000-00-00 00:00:00' && !empty($reg->fvencimiento)) 
	            ? date('d-m-Y', strtotime($reg->fvencimiento)) 
	            : 'Sin fecha';
	        
	        // Calcular días restantes
	        if ($fvencimiento != 'Sin fecha') {
	            $producto = new Producto();
	            $dias_restantes = $producto->calcularDiasVencimiento($reg->dias_transcurridos);
	            $estado = $reg->stock_lote <= 0 ? 'agotado' : ($reg->dias_transcurridos < 7 ? 'por_vencer' : 'ok');
	        } else {
	            $dias_restantes = '<span class="badge bg-secondary">N/A</span>';
	            $estado = 'sin_fecha';
	        }
	        
	        // Stock por lote
	        if ($reg->stock_lote === null) {
	            $stock_lote = '<span class="badge bg-secondary">Antiguo</span>';
	        } elseif ($reg->stock_lote <= 0) {
	            $stock_lote = '<span class="badge bg-danger">Agotado</span>';
	        } elseif ($reg->stock_lote / $reg->cantidad < 0.3) {
	            $stock_lote = '<span class="badge bg-warning">' . number_format($reg->stock_lote,2) . ' Unid.</span>';
	        } else {
	            $stock_lote = '<span class="badge bg-success">' . number_format($reg->stock_lote,2) . ' Unid.</span>';
	        }
	        
	        // Identificar si es el lote activo
	        $es_lote_activo = ($idfifo_activo && $reg->idfifo == $idfifo_activo);
	        $claseResaltado = $es_lote_activo ? 'table-info' : '';
	        $indicadorActivo = $es_lote_activo ? '<span class="badge bg-primary" style="margin-left:5px"><i class="fa fa-star"></i> EN VENTA</span>' : '';
	        
	        $nlote_display = ($reg->nlote ? $reg->nlote : 'N/A') . $indicadorActivo;
	        
	        $data[] = [
	            'numero' => $numero++,
	            'fecha_ingreso' => $fecha_ingreso,
	            'fvencimiento' => $fvencimiento,
	            'dias_restantes' => $dias_restantes,
	            'cantidad' => number_format($reg->cantidad,2),
	            'stock_lote' => $stock_lote,
	            'nlote' => $nlote_display,
	            'precio_compra' => 'S/ ' . number_format($reg->precio_compra,2),
	            'precio_venta' => 'S/ ' . number_format($reg->precio_venta,2),
	            'clase' => $claseResaltado
	        ];
	    }
	    
	    echo json_encode([
	        'draw' => $draw,
	        'recordsTotal' => $total_records,
	        'recordsFiltered' => $total_filtered,
	        'data' => $data
	    ]);
	    exit;
	break;

	case 'movimientoEntradaSalida':
	    $idproducto = limpiarCadena($_POST["idproducto"]);
	    $idsucursal = limpiarCadena($_POST["idsucursal"]);
	    $tipo_movimiento = limpiarCadena($_POST["tipo_movimiento"]);
	    $cantidad = limpiarCadena($_POST["cantidad"]);
	    $motivo = limpiarCadena($_POST["motivo"]);
	    $precio_venta  = $_POST["precio_venta"] ?? null;
	    $precio_compra = $_POST["precio_compra"] ?? null;
	    
	    // Convertir cadena vacía o "0" a 0
		$idfifo = $_POST["idfifo"] ?? '';
		$idfifo = ($idfifo === '' || $idfifo === null || $idfifo === '0') ? 0 : intval($idfifo);
	    
	    $rspta = $producto->movimientoEntradaSalida(
	        $idproducto,
	        $idsucursal,
	        $tipo_movimiento,
	        $cantidad,
	        $motivo,
	        1,
	        $precio_compra,
	        $precio_venta,
	        $idfifo
	    );
	    echo json_encode($rspta);
	break;

	case 'listarLotesFifo':
	    $idproducto = limpiarCadena($_POST['idproducto']);
	    $idsucursal = limpiarCadena($_POST['idsucursal']);
	    
	    $sql = "SELECT 
	                idfifo, 
	                cantidad_restante, 
	                precio_venta, 
	                precio_compra,
	                DATE_FORMAT(fecha_ingreso, '%d/%m/%Y') as fecha_ingreso_format
	            FROM stock_fifo
	            WHERE idproducto='$idproducto' 
	              AND idsucursal='$idsucursal' 
	              AND estado=1
	            ORDER BY fecha_ingreso ASC";
	    
	    $rs = ejecutarConsulta($sql);
	    
	    // Opciones iniciales
	    $opt = "<option value=''>── Seleccionar lote ──</option>";
	    $opt .= "<option value='0'>➕ Crear nuevo lote</option>";
	    $opt .= "<option disabled>──────────────────</option>";
	    
	    // Listar lotes existentes con formato mejorado
	    while($r = $rs->fetch_object()){
	        $idfifo = $r->idfifo;
	        $stock = number_format($r->cantidad_restante, 2);
	        $pv = number_format($r->precio_venta, 2);
	        $pc = number_format($r->precio_compra, 2);
	        $fecha = $r->fecha_ingreso_format;
	        
	        // Formato mejorado con información clara
	        $texto = "Lote #$idfifo │ Stock: $stock │ PC: S/ $pc │ PV: S/ $pv │ $fecha";
	        
	        $opt .= "<option value='$idfifo'>$texto</option>";
	    }
	    
	    echo $opt;
	break;

	case 'listarservice':
		$rspta = $producto->listarService();
		$data = [];
		while ($reg = $rspta->fetch_object()) {
			$data[] = [
				"idproducto" => $reg->idproducto,
				"nombre" => $reg->nombre,
				"precio" => $reg->precio
			];
		}
		echo json_encode($data);
		break;

	case 'actualizarPrecio':
		$idproducto = $_POST['idproducto'];
		$campo = $_POST['campo']; // precio o precio_compra
		$valor = $_POST['valor'];

		if ($campo == 'precio') {
			$sql = "UPDATE producto SET precio = '$valor' WHERE idproducto = '$idproducto'";
			$sql2 = "UPDATE producto_configuracion SET precio_venta = '$valor' WHERE idproducto = '$idproducto' AND cantidad_contenedor = 1";
			ejecutarConsulta($sql2);
		} elseif ($campo == 'precio_compra') {
			$sql = "UPDATE producto SET precio_compra = '$valor' WHERE idproducto = '$idproducto'";
		} else {
			echo "Campo inválido";
			return;
		}

		echo ejecutarConsulta($sql) ? "Actualizado correctamente" : "Error al actualizar";
		break;

	case 'selectNombrePrecios':
		$rspta = $producto->selectNombrePrecios();

		// Agrega manualmente la opción vacía
		echo '<option value="" selected>Seleccionar...</option>';

		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->idnombre_p . '>' . $reg->descripcion . '</option>';
		}
		break;

	case 'precios_adicionales':
	    $idusuario = $_SESSION['idusuario'];

	    $sqlCargo = "SELECT p.cargo 
	                 FROM usuario u 
	                 INNER JOIN personal p ON u.idpersonal = p.idpersonal 
	                 WHERE u.idusuario = '$idusuario' LIMIT 1";
	    $cargoRow = ejecutarConsultaSimpleFila($sqlCargo);
	    $cargoUsuario = $cargoRow ? $cargoRow['cargo'] : '';
	    $es_admin = ($cargoUsuario == 'Administrador'); 

	    $idproducto = isset($_POST["idproducto"]) ? limpiarCadena($_POST["idproducto"]) : 0;

	    $sql = "SELECT 
	                pc.id AS idconfig,
	                pc.contenedor,
	                pc.cantidad_contenedor,
	                pc.precio_venta,
	                pc.precio_promocion,
	                pcp.margen_utilidad AS margen,
	                np.descripcion AS nombre_precio,
	                pcp.precio
	            FROM producto_configuracion pc
	            LEFT JOIN producto_configuracion_precios pcp 
				  ON pc.id = pcp.producto_configuracion_id 
				 AND pcp.estado = 1
				 AND (pcp.deleted_at IS NULL OR pcp.deleted_at = '')
	            LEFT JOIN nombre_precios np 
	              ON pcp.idnombre_p = np.idnombre_p
	            WHERE pc.estado = 1 AND pc.idproducto = '$idproducto'
	            ORDER BY pc.id, np.idnombre_p ASC";

	    $res = ejecutarConsulta($sql);

	    $configuraciones = [];
	    while ($row = $res->fetch_object()) {
	        $id = $row->idconfig;

	        if (!isset($configuraciones[$id])) {
	            $configuraciones[$id] = [
	                'contenedor' => $row->contenedor,
	                'cantidad_contenedor' => $row->cantidad_contenedor,
	                'precio_venta' => $row->precio_venta,
	                'precio_promocion' => $row->precio_promocion,
	                'precios' => []
	            ];
	        }

	        if (!is_null($row->nombre_precio)) {
	            $configuraciones[$id]['precios'][] = [
	                'nombre' => $row->nombre_precio,
	                'precio' => $row->precio,
	                'margen' => $row->margen
	            ];
	        }
	    }

	    //  Estilos exactamente iguales, pero limitados al modal
	    $html = '<style>
	        #modalDetalleProducto .card { 
	            border:2px solid #4a68ff;
	            border-radius:10px;
	            background:#f9fbff;
	            box-shadow:0 2px 6px rgba(0,0,0,0.08);
	        }
	        #modalDetalleProducto .card-header { 
	            background:#4a68ff;
	            color:white;
	            border-radius:10px 10px 0 0;
	        }
	        #modalDetalleProducto .card-header button {
	            color:white !important;
	            font-weight:bold;
	            font-size:15px;
	        }
	        #modalDetalleProducto table.table-sm {
	            background:white;
	            border-radius:6px;
	            overflow:hidden;
	        }
	        #modalDetalleProducto .table-sm thead th {
	            background:#e3e9ff !important;
	            font-weight:bold;
	            color:#001f6d;
	            text-align:center;
	        }
	        #modalDetalleProducto .table-sm td {
	            font-size:14px;
	            font-weight:600;
	            color:#001a47;
	            vertical-align:middle;
	            text-align:center;
	        }
	        #modalDetalleProducto tr:hover {
	            background:#cfd8ff !important;
	            transition:0.3s;
	        }

	        /* PRECIOS DESTACADOS */
	        #modalDetalleProducto .badge-precio {
	            background:#ff6b00;
	            color:white !important;
	            font-size:14px;
	            padding:6px 12px;
	            border-radius:8px;
	            font-weight:bold;
	            display:inline-block;
	            min-width:80px;
	            text-align:center;
	            box-shadow:0 0 6px rgba(255,107,0,0.4);
	        }

	        /* MÁRGENES MUY VISIBLES */
	        #modalDetalleProducto .margen-box {
	            font-size:16px;
	            font-weight:bold;
	            padding:8px 14px;
	            border-radius:8px;
	            display:inline-block;
	            min-width:60px;
	            text-align:center;
	            box-shadow:0 0 6px rgba(0,0,0,0.15);
	        }
	        #modalDetalleProducto .margen-bajo { background:#ff1a1a; color:white; }   /* Rojo */
	        #modalDetalleProducto .margen-medio { background:#ffc107; color:black; } /* Amarillo */
	        #modalDetalleProducto .margen-alto { background:#2ecc71; color:white; }  /* Verde */
	    </style>';
	    $contador = 1;

	    foreach ($configuraciones as $id => $config) {
	        $collapseId = 'collapseConfig' . $id;

	        $html .= '
	        <div class="card mb-2">
	          <div class="card-header p-2">
	            <h6 class="mb-0 d-flex justify-content-between align-items-center">
	              <button class="btn btn-link text-left" type="button" data-toggle="collapse" data-target="#' . $collapseId . '">
	                Configuración #' . $contador++ . ': ' . $config['contenedor'] . ' - ' . $config['cantidad_contenedor'] . ' unidades
	              </button>
	            </h6>
	          </div>

	          <div id="' . $collapseId . '" class="collapse" data-parent="#acordeonConfiguraciones">
	            <div class="card-body">
	              <p>
	                <strong>Precio Venta:</strong> 
	                <span class="badge-precio">S/ ' . number_format($config['precio_venta'], 2) . '</span><br>
	                <!--<strong>Precio Promoción:</strong> 
	                <span class="badge-precio">S/ ' . number_format($config['precio_promocion'], 2) . '</span>-->
	              </p>
	              <h6>Precios adicionales:</h6>
	              <table class="table table-sm table-bordered">
	                <thead class="table-light">
	                  <tr>
	                    <th>Tipo de Precio</th>
	                    <th>Valor (S/)</th>';

	        if ($es_admin) {
	            $html .= '<th>Margen %</th>';
	        }

	        $html .= '</tr>
	                </thead>
	                <tbody>';

	        if (count($config['precios']) > 0) {
	            foreach ($config['precios'] as $p) {
	                $margenClass = '';
	                if ($es_admin) {
	                    if ($p['margen'] < 15) { $margenClass = 'margen-bajo'; }
	                    elseif ($p['margen'] < 30) { $margenClass = 'margen-medio'; }
	                    else { $margenClass = 'margen-alto'; }
	                }

	                $html .= '<tr>
	                  <td><strong>' . $p['nombre'] . '</strong></td>
	                  <td><span class="badge-precio">S/ ' . number_format($p['precio'], 2) . '</span></td>';

	                if ($es_admin) {
	                    $html .= '<td><span class="margen-box ' . $margenClass . '">' . number_format($p['margen'], 2) . '%</span></td>';
	                }

	                $html .= '</tr>';
	            }
	        } else {
	            $colspan = $es_admin ? 3 : 2;
	            $html .= '<tr><td colspan="' . $colspan . '"><em>Sin precios adicionales</em></td></tr>';
	        }

	        $html .= '
	                </tbody>
	              </table>
	            </div>
	          </div>
	        </div>';
	    }

	    echo $html;
	break;



	case 'consultarStockOtrasSucursales':
	    $idproducto = $_POST['idproducto'];
	    $idsucursalActual = $_SESSION['idsucursal'];
	    $producto = new Producto();

	    $rspta = $producto->consultarStockOtrasSucursales($idproducto, $idsucursalActual);
	    $data = array();

	    while ($reg = $rspta->fetch_object()) {
	        $data[] = array(
	            "sucursal" => $reg->sucursal,
	            "stock" => $reg->stock
	        );
	    }

	    echo json_encode($data);
	break;

	case 'buscarStockPorSucursales':
	    $termino = $_POST['termino'];
	    $idsucursalActual = $_SESSION['idsucursal'];
	    $idsucursalFiltro = isset($_POST['idsucursalFiltro']) ? $_POST['idsucursalFiltro'] : '';
	    $producto = new Producto();
	    $rspta = $producto->buscarStockPorSucursales($termino, $idsucursalActual, $idsucursalFiltro);
	    $data = [];
	    while ($reg = $rspta->fetch_object()) {
	        $data[] = [
	            "idproducto" => $reg->idproducto,
	            "nombre" => $reg->nombre,
	            "codigo" => $reg->codigo,
	            "idsucursal" => $reg->idsucursal,
	            "sucursal" => $reg->sucursal,
	            "stock" => $reg->stock
	        ];
	    }
	    echo json_encode($data);
	break;

	case 'generar_codigo':
	  require_once "../modelos/Producto.php";
	  $producto = new Producto();
	  $codigo = $producto->generarCodigo();
	  echo json_encode(["codigo" => $codigo]);
	  break;


}
