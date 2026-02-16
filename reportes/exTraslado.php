<?php
ob_start();
if (strlen(session_id()) < 1) session_start();

if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
    require_once "../modelos/Traslado.php";
    require_once "../modelos/Negocio.php";
    require_once "../modelos/Categoria.php";

    $traslado = new Traslado();
    $rspta = $traslado->mostrarCabecera($_GET["id"]);
    $reg = $rspta->fetch_object();

    // Datos de la empresa
    $negocio = new Negocio();
    $rsptan = $negocio->listar();
    $regn = $rsptan->fetch_object();

    $empresa = $regn->nombre;
    $documento = $regn->documento;
    $ndocumento = $regn->ndocumento;
    $telefono = $regn->telefono;
    $email = $regn->email;
    $pais = $regn->pais;
    $ciudad = $regn->ciudad;
    $imagen = $regn->logo;
    $direccion = $regn->direccion;

    // Información de la sucursal origen
    /*$categoria = new Categoria();
    $rsptas = $categoria->mostrarSucursalTi($reg->idorigen);
    $regs = $rsptas ? $rsptas->fetch_object() : null;
    $direccion = $regs ? $regs->direccion : $direccionEmpresa;
    $telefono = $regs && $regs->telefono ? $regs->telefono : $telefono;*/
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; margin: 0; padding: 0; color: #000; }
        .zona_impresion { width: 260px; margin: auto; }
        .ticket-header { text-align: center; margin-bottom: 5px; }
        .ticket-header img { margin-bottom: 3px; }
        .ticket-header strong { font-size: 11pt; display: block; margin-top: 2px; }
        .ticket-header div { font-size: 9pt; }
        .ticket-section { margin: 5px 0; font-size: 10pt; }
        .ticket-section table { width: 100%; border-collapse: collapse; }
        .ticket-section td { padding: 1px 0; vertical-align: top; }
        .line-separator { border-top: 1px dashed #000; margin: 5px 0; }
        .productos th { font-size: 8pt; text-align: left; border-bottom: 1px dashed #000; padding-bottom: 2px; }
        .productos td { font-size: 8pt; padding: 2px 0; }
        .nota-final { text-align: center; font-size: 8pt; margin-top: 6px; border-top: 1px dashed #000; padding-top: 4px; }
    </style>
</head>

<body onload="window.print();">

<div class="zona_impresion">

    <!-- CABECERA EMPRESA -->
    <div class="ticket-header">
        <img src="../reportes/<?php echo $imagen; ?>" width="100" height="50"><br>
        <strong><?php echo $empresa; ?></strong>
        <div><?php echo $ndocumento; ?>: <?php echo $documento; ?></div>
        <div><?php echo $direccion; ?></div>
        <div>Tel: <?php echo $telefono; ?> | <?php echo $email; ?></div>
        <div><?php echo $ciudad . " - " . $pais; ?></div>
    </div>

    <!-- TÍTULO -->
    <div class="ticket-section" style="text-align:center;">
        <strong>COMPROBANTE DE TRASLADO</strong><br>
        <span>Fecha: <?php echo $reg->fecha; ?></span>
    </div>

    <div class="line-separator"></div>

    <!-- DETALLES DE TRASLADO -->
    <div class="ticket-section">
        <table>
            <tr><td><b>Origen:</b></td><td style="font-size:14px"><?php echo $reg->origen; ?></td></tr>
            <tr><td><b>Destino:</b></td><td style="font-size:14px"><?php echo $reg->destino; ?></td></tr>
            <tr><td><b>Usuario:</b></td><td style="font-size:14px"><?php echo $_SESSION["nombre"]; ?></td></tr>
            <tr><td><b>Estado:</b></td><td style="font-size:14px"><?php echo ($reg->estado == 1) ? "COMPLETADO" : "PENDIENTE"; ?></td></tr>
        </table>
    </div>

    <div class="line-separator"></div>

    <!-- PRODUCTOS -->
    <table class="productos">
        <tr>
            <th style="width:35px;font-size:14px">Cant</th>
            <th style="text-align:center;font-size:14px">Producto</th>
            <th style="text-align:right;font-size:14px">Unidad</th>
        </tr>
        <?php
        $rsptad = $traslado->listarDetalleTicket($_GET["id"]);
        $itemTotal = 0;
        while ($regd = $rsptad->fetch_object()) {
            echo "<tr>";
            echo "<td style='font-size:14px'>".number_format($regd->cantidad,2)."</td>";
            echo "<td style='text-align:center;font-size:14px'>".$regd->producto."</td>";
            echo "<td style='text-align:right;font-size:14px'>".$regd->unidad."</td>";
            echo "</tr>";
            $itemTotal++;
        }
        ?>
    </table>

    <div class="line-separator"></div>

    <div class="ticket-section">
        <b>Total de Items:</b> <?php echo number_format($itemTotal,0); ?><br>
    </div>

    <div class="nota-final">
        Traslado generado exitosamente.<br>
        Documento interno sin valor comercial.
    </div>

</div>

<script type="text/javascript">
    window.onafterprint=function(){ window.close(); }
</script>
</body>
</html>
<?php
}
ob_end_flush();
?>
