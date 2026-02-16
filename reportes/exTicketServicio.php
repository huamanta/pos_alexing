<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
    session_start();

if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
    if ($_SESSION['crearservicio'] == 1) {
?>
        <html>

        <head>
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />
            <link href="../files/dist/css/ticket.css" rel="stylesheet" type="text/css">
        </head>

        <body onload="window.print();">
            <?php

            //Incluímos la clase Venta
            require_once "../modelos/Servicio.php";
            require_once "Letras.php";
            $V = new EnLetras();
            //Instanaciamos a la clase con el objeto venta
            $servicio = new Servicio();
            //En el objeto $rspta Obtenemos los valores devueltos del método ventacabecera del modelo
            $rspta = $servicio->serviciocabecera($_GET["id"]);
            //Recorremos todos los valores obtenidos
            $reg = $rspta->fetch_object();
            //datos de la empresa
            require_once "../modelos/Negocio.php";
            $cnegocio = new Negocio();
            $rsptan = $cnegocio->listar();
            $regn = $rsptan->fetch_object();
            $empresa = $regn->nombre;
            $ndocumento = $regn->ndocumento;
            $documento = $regn->documento;
            $direccion = $regn->direccion;
            $telefono = $regn->telefono;
            $email = $regn->email;
            $pais = $regn->pais;
            $ciudad = $regn->ciudad;
            $imagen = $regn->logo;

            ?>
            <div class="zona_impresion">
                <!-- codigo imprimir -->
                <table border="0" align="center" width="280px">
                    <td colspan="4" align="center">

                        <img src="../reportes/<?php echo $imagen; ?>" width="150" height="80">

                    </td>

                    <tr>
                        <td align="center" colspan="2">

                            <!-- Mostramos los datos de la empresa en el documento HTML -->
                            <strong style="font-size: 12pt;"><?php echo $empresa; ?></strong><br>
                            <?php echo $ndocumento; ?>: <?php echo $documento; ?><br>
                            <?php echo $direccion; ?><br>
                            <?php echo 'Teléfono: ' . $telefono; ?><br>
                            <?php echo 'Email: ' . $email; ?><br>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">=============================================</td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2">

                                <strong>
                                    <font size="2"><?php echo $reg->tipo_comprobante; ?> de Pedido<br>

                                        <?php echo $reg->serie_comprobante . " - " . $reg->num_comprobante; ?>

                                        <br>


                                    </font>
                                </strong>

                        </td>
                    </tr>
                    <td colspan="4"><hr style="border: none; border-top: 1px solid #000; margin: 5px 0;"></td>
                    <tr>

                    </tr>
                    <tr>
                        <td align="center"></td>
                    </tr>
                    <tr>
                        <!-- Mostramos los datos del cliente en el documento HTML -->
                        <td style="padding-left: 5px; width: 125px; font-weight: bold;">FECHA ENTRADA : </td>
                        <td><p><label><?php echo $reg->fecha_ingreso; ?></p></label></td>
                    </tr>
                    <tr>
                        <!-- Mostramos los datos del cliente en el documento HTML -->
                        <td style="padding-left: 5px; font-weight: bold;">SEÑOR(ES) :</td>
                        <td style="width: 600px;"><p><label><?php echo $reg->cliente; ?></p></label></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 5px; font-weight: bold;"><?php echo $reg->tipo_documento;?>:</td>
                        <td><p><label><?php echo $reg->num_documento; ?></p></label></td>
                    </tr>
                    <tr>
                        <!-- Mostramos los datos del cliente en el documento HTML -->
                        <td style="padding-left: 5px; font-weight: bold;">DIRECCIÓN :</td>
                        <td><p><label><?php echo $reg->direccion; ?></p></label></td>
                    </tr>
                    <tr>
                        <!-- Mostramos los datos del cliente en el documento HTML -->
                        <td style="padding-left: 5px; font-weight: bold;">EQUIPO :</td>
                        <td><p><?php echo $reg->equipo . ' - ' . $reg->descripcion_problema; ?></p></label></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 5px; font-weight: bold;">FECHA SALIDA:</td>
                        <td><?php echo $reg->fecha_entrega; ?></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 5px; font-weight: bold;">ESTADO:</td>
                        <td><?php echo $reg->estado; ?></td>
                    </tr>
                    

                </table>
                <!-- Mostramos los detalles de la venta en el documento HTML -->
                <table border="0" align="center" width="320px">
                    <tr>
                        <td colspan="4"><hr style="border: none; border-top: 1px solid #000; margin: 5px 0;"></td>
                    </tr>
                    <tr>
                        <td><b>CANT.</b></td>
                        <td><b>DESCRIPCIÓN</b></td>
                        <td align="right"><b>IMPORTE</b></td>
                    </tr>
                    <tr>
                        <td colspan="4"><hr style="border: none; border-top: 1px solid #000; margin: 5px 0;"></td>
                    </tr>
                    <?php
                    $rsptad = $servicio->serviciodetalle($_GET["id"]);
                    $cantidad = 0;
                    $total = 0;
                    $subtotal = 0;
                    while ($regd = $rsptad->fetch_object()) {
                        echo "<tr>";
                        echo "<td>" . number_format($regd->cantidad,2, ",", ".") . "</td>";
                        echo "<td>" . $regd->nombre;
                        echo "<td align='right'>S/ " . number_format($regd->subtotal,2, ",", ".") . "</td>";
                        echo "</tr>";
                        $cantidad += $regd->cantidad;
                        $subtotal += $regd->subtotal;
                    }
                    ?>

                    <tr>
                        <td>&nbsp;</td>
                        <td align="right"><b>TOTAL:</b></td>
                        <td align="right"><b>S/ <?php echo $reg->total?></b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center">

                    <br>

                        <?php
                            if ($reg->tipo_comprobante != "Nota") {

                            ?>

                                Representación Impresa del <?php echo $reg->tipo_comprobante; ?> Electrónico del registro del servicio

                            <?php

                            }

                            ?>


                        </td>
                        
                        
                    </tr>


                </table>
                <br>
            </div>
            <p>&nbsp;</p>

        </body>
        <script type="text/javascript">
            window.onafterprint=function(){
                window.close()
            }
        </script>

        </html>
<?php
    } else {
        echo 'No tiene permiso para visualizar el reporte';
    }
}
ob_end_flush();
?>
