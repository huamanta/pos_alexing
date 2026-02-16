<?php
if (strlen(session_id()) < 1) session_start();

if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
    exit;
}

if ($_SESSION['almacen'] != 1) {
    echo 'No tiene permiso para visualizar el reporte';
    exit;
}

header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Negocio.php';
ini_set('pcre.backtrack_limit', '5000000');
ini_set('pcre.recursion_limit', '5000000');

use Mpdf\Mpdf;

// -------------------------
// Función para limpiar UTF-8
// -------------------------
function limpiar_utf8($texto)
{
    if ($texto === null) return '';
    return trim(mb_convert_encoding($texto, 'UTF-8', 'auto'));
}
// Instancia de modelos
$articulo = new Producto();
$negocio = new Negocio();

// Datos del negocio
$datosNegocio = $negocio->mostrarNombreNegocio(); 
$empresa = limpiar_utf8($datosNegocio['nombre']);
$ndocumento = limpiar_utf8($datosNegocio['ndocumento']);
$documento = limpiar_utf8($datosNegocio['documento']);
$telefono = limpiar_utf8($datosNegocio['telefono']);
$email = limpiar_utf8($datosNegocio['email']);
$pais = limpiar_utf8($datosNegocio['pais']);
$ciudad = limpiar_utf8($datosNegocio['ciudad']);
$direccion = limpiar_utf8($datosNegocio['direccion']);
$logoNegocio = $datosNegocio['logo'];
$logoFile = realpath(__DIR__ . '/../reportes/' . $logoNegocio);

$idcategoria = isset($_GET['idcategoria']) ? intval($_GET['idcategoria']) : 0;
$precios = isset($_GET['precios']) ? explode(',', $_GET['precios']) : ['precio_venta'];
$mostrar_precio_publico = in_array('precio_venta', $precios);
$precios_adicionales = array_filter($precios, function($p) {
    return $p !== 'precio_venta';
});


// Traer productos y imágenes
$rspta = $articulo->listarcatalogo($_SESSION['idsucursal'], $idcategoria);
$rsImagenes = $articulo->obtenerImagenesCatalogo($_SESSION['idsucursal']);

// Generar PDF
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 2,
    'margin_right' => 2,
    'margin_top' => 2,
    'margin_bottom' => 2,
    'tempDir' => __DIR__ . '/../temp',
]);

ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Catálogo PDF</title>
<style>
  body { font-family: 'Arial', sans-serif; color: #333; margin:0; padding:0; background:#f5f5f5; }
  .container { width: 100%; max-width: 1000px; margin: 0 auto; padding:10px; }
  .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #00796b;
      color: #fff;
      padding: 10px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
  }
  .header img { max-height: 50px; object-fit: contain; border-radius:6px; }
  .header .empresa-info { text-align: right; font-size:12px; line-height:1.2; }
  h2 { text-align:center; color:#00796b; margin-bottom:20px; }
  table { width: 100%; border-collapse: separate; border-spacing: 0 12px; }
  td { border-radius: 12px; background: #fff; padding: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
  .producto-img { width:130px; height:130px; object-fit:contain; border-radius:8px; }
  .producto-sin-img { width:130px; height:130px; display:flex;align-items:center;justify-content:center; color:#aaa; border-radius:8px; border:1px solid #eee; font-size:12px; }
  .detalle-nombre { font-size: 14px; font-weight: bold; color: #222; margin-bottom: 6px; background-color: #e0f2f1; border-radius:6px; padding:6px; }
  .detalle-info td { padding:2px 4px; font-size:12px; color:#555; }
  .detalle-info td:first-child { font-weight:bold; width:30%; white-space:nowrap; }
  .detalle-precio span { 
      background-color:#00796b; color:#fff; font-size:13px; font-weight:bold; 
      padding:4px 8px; border-radius:5px; display:inline-block; margin-bottom:3px;
  }
  .producto-row { margin-bottom: 12px; }
</style>
</head>
<body>
<div class="container">

  <!-- Imágenes principales del catálogo -->
  <?php while ($img = $rsImagenes->fetch_object()) : ?>
    <div style="text-align:center; margin-bottom: 20px;">
      <img src="<?php echo realpath(__DIR__ . '/../files/' . $img->nombre_imagen); ?>" style="max-width:100%;height:auto; border-radius:6px;">
    </div>
  <?php endwhile; ?>

  <div class="header">
    <div class="empresa-info">
      <strong><?php echo $empresa; ?></strong><br>
      <?php echo $direccion . ', ' . $ciudad . ', ' . $pais; ?><br>
      Tel: <?php echo $telefono; ?> | Email: <?php echo $email; ?>
    </div>
  </div>
  <h2>LISTA DE PRODUCTOS</h2>
  <table>
<?php
$headerHtml = ob_get_clean();
$mpdf->WriteHTML($headerHtml);

$contador = 0;
$productos_por_tabla = 50; // Ajusta este número si es necesario

foreach ($rspta as $reg) {
    if ($contador > 0 && $contador % $productos_por_tabla === 0) {
        $mpdf->WriteHTML('</table>'); // Cierra la tabla anterior
        $mpdf->WriteHTML('<table style="width: 100%; border-collapse: separate; border-spacing: 0 12px;">'); // Abre una nueva
    }

    ob_start();
?>
      <tr class="producto-row">
        <td>
          <table style="width:100%; border-collapse: collapse;">
            <tr>
              <!-- Imagen -->
              <td style="width:130px; text-align:center; vertical-align:middle; display:flex; align-items:center; justify-content:center; height:130px;">
                  <?php 
                    $imgPath = __DIR__ . '/../files/productos/' . $reg->imagen;
                    if ($reg->imagen && file_exists($imgPath)) {
                        echo '<img src="file://' . $imgPath . '" alt="Producto" class="producto-img">';
                    } else {
                        echo '<div class="producto-sin-img">Sin imagen</div>';
                    }
                  ?>
              </td>

              <!-- Detalles -->
              <td style="padding-left:12px; vertical-align:top;">
                <div class="detalle-nombre"><?php echo limpiar_utf8($reg->nombre); ?></div>
                <table class="detalle-info">
                  <tr><td>U.M:</td><td><?php echo limpiar_utf8($reg->unidad);?></td></tr>
                  <tr><td>Categoría:</td><td><?php echo limpiar_utf8($reg->categoria); ?></td></tr>
                  <tr><td>Procedencia:</td><td><?php echo limpiar_utf8($reg->registrosan); ?></td></tr>
                  <tr><td>Cant x Caja:</td><td><?php echo limpiar_utf8($reg->modelo); ?></td></tr>
                  <tr><td>Código:</td><td><?php echo limpiar_utf8($reg->codigo); ?></td></tr>
                </table>

                <div class="detalle-precio">
                  <?php 
                    $listaPrecios = [];
                    if ($mostrar_precio_publico) {
                        $listaPrecios[] = 'S/ ' . number_format($reg->precio_venta, 2) . ' (Público)';
                    }
                    if (!empty($reg->precios_adicionales)) {
                        $preciosLista = explode('|', $reg->precios_adicionales);
                        foreach ($preciosLista as $precioItem) {
                            list($id, $desc, $valor) = explode(':', $precioItem);
                            if (in_array($id, $precios_adicionales)) {
                                $listaPrecios[] = 'S/ ' . number_format($valor, 2) . ' (' . limpiar_utf8($desc) . ')';
                            }
                        }
                    }
                    foreach ($listaPrecios as $p) {
                        echo '<span>' . $p . '</span>';
                    }
                  ?>
                </div>
              </td>

              <!-- Logo pequeño -->
              <td style="width:100px; text-align:center; vertical-align:middle;">
                <?php if ($logoNegocio && file_exists($logoFile)) : ?>
                  <img src="<?php echo $logoFile; ?>" style="max-width:80%; max-height:50px; border-radius:6px;">
                <?php endif; ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
<?php
    $productHtml = ob_get_clean();
    $mpdf->WriteHTML($productHtml);
    $contador++;
}

ob_start();
?>
  </table>

</div>
</body>
</html>
<?php
$footerHtml = ob_get_clean();
$mpdf->WriteHTML($footerHtml);

$mpdf->Output('catalogo.pdf', 'I');
