<?php

header("Content-type: text/html; charset=utf8");

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/src/Util.php';
require_once __DIR__ . '/../../../configuraciones/ConexionPdo.php';
require_once __DIR__ . '/../../../core/FluentQuery.php';

use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\FormaPagos\FormaPagoCredito;
use Greenter\Model\Sale\Cuota;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;

date_default_timezone_set('America/Lima');

$pdo = Conexion::conectar();

$idVenta = $_GET['idventa'] ?? null;
$codColab = $_GET['codColab'] ?? null;

if (!$idVenta) {
    die('No se recibió el id de venta.');
}

$util = Util::getInstance();

function cleanStringSunat($string)
{
    if ($string === null) {
        return '';
    }

    $string = preg_replace('/[^\P{C}\n\t]/u', '', $string);

    return trim(mb_convert_encoding($string, 'UTF-8', 'UTF-8'));
}

function money($value)
{
    return round((float) $value, 2);
}

$venta = (new DBQuery($pdo))
    ->select([
        'v.idventa',
        'v.idsucursal',
        'v.serie_comprobante as serieDOC',
        'v.num_comprobante as numDoc',
        'c.num_documento as numDocClie',
        'c.nombre as clien',
        'c.direccion as direcClien',
        'v.fecha_hora as fechaVen',
        'CAST(v.total_venta AS DECIMAL(11,2)) as importe',
        'CAST(v.impuesto AS DECIMAL(11,2)) as igv',
        'v.ventacredito'
    ])
    ->from('venta v')
    ->join('persona c', 'c.idpersona = v.idcliente')
    ->where('v.idventa', '=', $idVenta)
    ->first();

if (!$venta) {
    die('No se encontró la venta.');
}

$IdDOV = $venta['idventa'];
$numeroDOC = $venta['numDoc'];
$serieDOC = $venta['serieDOC'];
$clienNumero = $venta['numDocClie'];
$clien = cleanStringSunat($venta['clien']);
$fechaVenta = $venta['fechaVen'];
$totalBD = money($venta['importe']);
$ventacredito = $venta['ventacredito'];
$idalmacen = $venta['idsucursal'];

$client = new Client();

$client
    ->setTipoDoc('6')
    ->setNumDoc($clienNumero)
    ->setRznSocial($clien);

$sucursal = (new DBQuery($pdo))
    ->select('*')
    ->from('sucursal s')
    ->join('empresas e', 's.idempresa = e.idempresa')
    ->where('s.idsucursal', '=', $idalmacen)
    ->first();

if (!$sucursal) {
    die('No se encontró la sucursal.');
}

$companyAddress = new Address();

$companyAddress
    ->setUbigueo($sucursal['ubigeo'])
    ->setDistrito(cleanStringSunat($sucursal['distrito']))
    ->setProvincia(cleanStringSunat($sucursal['provincia']))
    ->setDepartamento(cleanStringSunat($sucursal['departamento']))
    ->setUrbanizacion('-')
    ->setCodLocal('0000')
    ->setDireccion(cleanStringSunat($sucursal['direccion']));

$company = new Company();

$company
    ->setRuc($sucursal['ruc'])
    ->setNombreComercial(cleanStringSunat($sucursal['nombre']))
    ->setRazonSocial(cleanStringSunat($sucursal['razon_social']))
    ->setAddress($companyAddress);

$detalles = (new DBQuery($pdo))
    ->select([
        'p.idproducto as COD',
        'p.nombre as nombreProd',
        'p.proigv as proigv',
        'CAST((dv.precio_venta - dv.descuento) AS DECIMAL(11,2)) AS precioUnitario',
        'CAST(dv.cantidad AS DECIMAL(11,3)) AS cantidad',

        'CASE
            WHEN p.proigv = "No Gravada"
                THEN CAST(
                    (dv.precio_venta - dv.descuento)
                    AS DECIMAL(11,6)
                )
            ELSE
                CAST(
                    (dv.precio_venta - dv.descuento) / 1.18
                    AS DECIMAL(11,6)
                )
        END AS valorUnitario',

        'CASE
            WHEN p.proigv = "No Gravada"
                THEN CAST(
                    (dv.precio_venta - dv.descuento) * dv.cantidad
                    AS DECIMAL(11,2)
                )
            ELSE
                CAST(
                    ((dv.precio_venta - dv.descuento) / 1.18)
                    * dv.cantidad
                    AS DECIMAL(11,2)
                )
        END AS importe',

        'CASE
            WHEN p.proigv = "No Gravada"
                THEN CAST(0 AS DECIMAL(11,2))
            ELSE
                CAST(
                    (
                        (dv.precio_venta - dv.descuento)
                        -
                        ((dv.precio_venta - dv.descuento) / 1.18)
                    ) * dv.cantidad
                    AS DECIMAL(11,2)
                )
        END AS Igv'
    ])
    ->from('detalle_venta dv')
    ->join('producto p', 'p.idproducto = dv.idproducto')
    ->join('producto_configuracion pg', 'pg.idproducto = p.idproducto')
    ->where('dv.idventa', '=', $idVenta)
    ->get();

if (!$detalles) {
    die('La venta no tiene detalles.');
}

$arrayItem = [];

$totalGravado = 0;
$totalExonerado = 0;
$totalIGV = 0;
$totalICBPER = 0;

foreach ($detalles as $detalle) {

    $codigo = $detalle['COD'];
    $nombre = cleanStringSunat($detalle['nombreProd']);
    $tipo = trim($detalle['proigv']);

    $cantidad = (float) $detalle['cantidad'];
    $precioUnitario = money($detalle['precioUnitario']);
    $valorUnitario = (float) $detalle['valorUnitario'];
    $importe = money($detalle['importe']);
    $igv = money($detalle['Igv']);

    if ($tipo === 'Gravada') {

        $icbper = 0;
        $factorIcbper = 0;

        if (strtoupper($nombre) === 'BOLSA') {
            $factorIcbper = 0.30;
            $icbper = money($cantidad * $factorIcbper);
            $totalICBPER += $icbper;
        }

        $totalGravado += $importe;
        $totalIGV += $igv;

        $item = new SaleDetail();

        $item
            ->setCodProducto($codigo)
            ->setUnidad('NIU')
            ->setCantidad($cantidad)
            ->setDescripcion($nombre)
            ->setMtoValorUnitario($valorUnitario)
            ->setMtoPrecioUnitario($precioUnitario)
            ->setMtoValorVenta($importe)
            ->setMtoBaseIgv($importe)
            ->setPorcentajeIgv(18)
            ->setIgv($igv)
            ->setTipAfeIgv('10')
            ->setTotalImpuestos(
                money($igv + $icbper)
            );

        if ($icbper > 0) {
            $item
                ->setIcbper($icbper)
                ->setFactorIcbper($factorIcbper);
        }

        $arrayItem[] = $item;

    } elseif ($tipo === 'No Gravada') {

        $totalExonerado += $importe;

        $item = new SaleDetail();

        $item
            ->setCodProducto($codigo)
            ->setUnidad('NIU')
            ->setCantidad($cantidad)
            ->setDescripcion($nombre)
            ->setMtoValorUnitario($valorUnitario)
            ->setMtoPrecioUnitario($precioUnitario)
            ->setMtoValorVenta($importe)
            ->setMtoBaseIgv($importe)
            ->setPorcentajeIgv(0)
            ->setIgv(0)
            ->setTipAfeIgv('20')
            ->setTotalImpuestos(0);

        $arrayItem[] = $item;

    } else {

        die(
            "Tipo de afectación no soportado. " .
            "Producto: {$codigo}, proigv: {$tipo}"
        );
    }
}

$totalGravado = money($totalGravado);
$totalExonerado = money($totalExonerado);
$totalIGV = money($totalIGV);
$totalICBPER = money($totalICBPER);

$valorVenta = money(
    $totalGravado + $totalExonerado
);

$totalImpuestos = money(
    $totalIGV + $totalICBPER
);

$totalFactura = money(
    $valorVenta + $totalImpuestos
);

$arrayCuota = [];
$saldo = 0;

if ($ventacredito === 'Si') {

    $creditos = (new DBQuery($pdo))
        ->select('*')
        ->from('cuentas_por_cobrar')
        ->where('idventa', '=', $idVenta)
        ->get();

    foreach ($creditos as $credito) {

        $monto = money(
            $credito['deudatotal'] -
            $credito['abonototal']
        );

        $saldo += $monto;

        $cuota = new Cuota();

        $cuota
            ->setMonto($monto)
            ->setFechaPago(
                new DateTime(
                    $credito['fechavencimiento'] . ' 00:00:00'
                )
            );

        $arrayCuota[] = $cuota;
    }

    $saldo = money($saldo);
}

$invoice = new Invoice();

$invoice
    ->setUblVersion('2.1')
    ->setTipoOperacion('0101')
    ->setTipoDoc('01')
    ->setSerie($serieDOC)
    ->setCorrelativo($numeroDOC)
    ->setFechaEmision(new DateTime($fechaVenta))
    ->setTipoMoneda('PEN')
    ->setClient($client)
    ->setCompany($company)
    ->setMtoOperGravadas($totalGravado)
    ->setMtoOperExoneradas($totalExonerado)
    ->setMtoIGV($totalIGV)
    ->setIcbper($totalICBPER)
    ->setTotalImpuestos($totalImpuestos)
    ->setValorVenta($valorVenta)
    ->setSubTotal($totalFactura)
    ->setMtoImpVenta($totalFactura);

if ($ventacredito === 'No') {

    $invoice->setFormaPago(
        new FormaPagoContado()
    );

} else {

    $invoice
        ->setFormaPago(
            new FormaPagoCredito($saldo)
        )
        ->setCuotas($arrayCuota);
}

$fg = new FuncionesGlobales();

$legend = new Legend();

$legend
    ->setCode('1000')
    ->setValue(
        $fg->numletras($totalFactura)
    );

$invoice
    ->setDetails($arrayItem)
    ->setLegends([$legend]);

$see = $util->getSee($idalmacen);

$res = $see->send($invoice);

$xml = $see->getFactory()->getLastXml();

$util->writeXml(
    $invoice,
    $xml
);

if (!$res->isSuccess()) {

    echo $util->getErrorResponse(
        $res->getError()
    );

    exit;
}

$cdr = method_exists($res, 'getCdrResponse')
    ? $res->getCdrResponse()
    : null;

$cdrZip = method_exists($res, 'getCdrZip')
    ? $res->getCdrZip()
    : null;

if (!$cdr) {
    echo 'No se recibió respuesta de CDR desde SUNAT.';
    exit;
}

$util->writeCdr(
    $invoice,
    $cdrZip
);

$util->showResponse(
    $invoice,
    $cdr,
    $IdDOV,
    'DocVenta',
    $codColab
);

$code = (int) $cdr->getCode();

if ($code === 0) {

    echo 'ESTADO: ACEPTADA' . PHP_EOL;

    if (
        method_exists($cdr, 'getNotes') &&
        count($cdr->getNotes()) > 0
    ) {
        echo 'OBSERVACIONES:' . PHP_EOL;
        var_dump($cdr->getNotes());
    }

} elseif ($code >= 2000 && $code <= 3999) {

    echo 'ESTADO: RECHAZADA' . PHP_EOL;

} else {

    echo 'Excepción' . PHP_EOL;
}

echo $cdr->getDescription() . PHP_EOL;

class FuncionesGlobales
{
    function IndiceDocumentVenta($Num)
    {
        $newNum = '';
        if (($Num / 100) >= 1) {
            return 'F' . $Num;
        } elseif (($Num / 10) >= 1) {
            $newNum = 'F0' . $Num;
            return $newNum;
        } else {
            $newNum = 'F00' . $Num;
            return $newNum;
        }
    }

    function numletras($numero)
    {
        $tempnum = explode('.', $numero);

        if ($tempnum[0] !== "") {
            $numf = self::milmillon($tempnum[0]);
            /*if ($numf == "UNO")
            {
                $numf = substr($numf, 0, -1);
            }*/
            if ($numf == "") {
                $numf = "CERO";
            }

            $TextEnd = $numf . ' CON ';
            //$TextEnd .= $_nommoneda.' CON ';
        }
        if ($tempnum[0] == "" || $tempnum[0] >= 100) {
            $tempnum[0] = "0";
        }
        if (empty($tempnum[1])) //empty: Determina si una variable es considerada vac�a. Una variable se considera vac�a si no existe o si su valor es igual a FALSE. empty() no genera una advertencia si la variable no existe.
        {
            $TextEnd .= "00/100 SOLES";
        } else if (substr($tempnum[1], 0, -1) != "0" && $tempnum[1] <= "9") {
            $TextEnd .= $tempnum[1];
            $TextEnd .= "0/100 SOLES";
        } else {
            $TextEnd .= $tempnum[1];
            $TextEnd .= "/100 SOLES";
        }

        return $TextEnd;
    }

    function unidad($numuero)
    {
        switch ($numuero) {

            case 9: {
                $numu = "NUEVE";
                break;
            }
            case 8: {

                $numu = "OCHO";

                break;

            }

            case 7: {

                $numu = "SIETE";

                break;

            }

            case 6: {

                $numu = "SEIS";

                break;

            }

            case 5: {

                $numu = "CINCO";

                break;

            }

            case 4: {

                $numu = "CUATRO";

                break;

            }

            case 3: {

                $numu = "TRES";

                break;

            }

            case 2: {

                $numu = "DOS";

                break;

            }

            case 1: {

                $numu = "UNO";

                break;

            }

            case 0: {

                $numu = "";

                break;

            }

        }

        return $numu;

    }



    function decena($numdero)
    {



        if ($numdero >= 90 && $numdero <= 99) {

            $numd = "NOVENTA ";

            if ($numdero > 90)

                $numd = $numd . "Y " . (self::unidad($numdero - 90));

        } else if ($numdero >= 80 && $numdero <= 89) {

            $numd = "OCHENTA ";

            if ($numdero > 80)

                $numd = $numd . "Y " . (self::unidad($numdero - 80));

        } else if ($numdero >= 70 && $numdero <= 79) {

            $numd = "SETENTA ";

            if ($numdero > 70)

                $numd = $numd . "Y " . (self::unidad($numdero - 70));

        } else if ($numdero >= 60 && $numdero <= 69) {

            $numd = "SESENTA ";

            if ($numdero > 60)

                $numd = $numd . "Y " . (self::unidad($numdero - 60));

        } else if ($numdero >= 50 && $numdero <= 59) {

            $numd = "CINCUENTA ";

            if ($numdero > 50)

                $numd = $numd . "Y " . (self::unidad($numdero - 50));

        } else if ($numdero >= 40 && $numdero <= 49) {

            $numd = "CUARENTA ";

            if ($numdero > 40)

                $numd = $numd . "Y " . (self::unidad($numdero - 40));

        } else if ($numdero >= 30 && $numdero <= 39) {

            $numd = "TREINTA ";

            if ($numdero > 30)

                $numd = $numd . "Y " . (self::unidad($numdero - 30));

        } else if ($numdero >= 20 && $numdero <= 29) {

            if ($numdero == 20)

                $numd = "VEINTE ";
            else

                $numd = "VEINTI" . (self::unidad($numdero - 20));

        } else if ($numdero >= 10 && $numdero <= 19) {

            switch ($numdero) {

                case 10: {

                    $numd = "DIEZ ";

                    break;

                }

                case 11: {

                    $numd = "ONCE ";

                    break;

                }

                case 12: {

                    $numd = "DOCE ";

                    break;

                }

                case 13: {

                    $numd = "TRECE ";

                    break;

                }

                case 14: {

                    $numd = "CATORCE ";

                    break;

                }

                case 15: {

                    $numd = "QUINCE ";

                    break;

                }

                case 16: {

                    $numd = "DIECISEIS ";

                    break;

                }

                case 17: {

                    $numd = "DIECISIETE ";

                    break;

                }

                case 18: {

                    $numd = "DIECIOCHO ";

                    break;

                }

                case 19: {

                    $numd = "DIECINUEVE ";

                    break;

                }

            }

        } else

            $numd = self::unidad($numdero);

        return $numd;

    }



    function centena($numc)
    {

        if ($numc >= 100) {

            if ($numc >= 900 && $numc <= 999) {

                $numce = "NOVECIENTOS ";

                if ($numc > 900)

                    $numce = $numce . (self::decena($numc - 900));

            } else if ($numc >= 800 && $numc <= 899) {

                $numce = "OCHOCIENTOS ";

                if ($numc > 800)

                    $numce = $numce . (self::decena($numc - 800));

            } else if ($numc >= 700 && $numc <= 799) {

                $numce = "SETECIENTOS ";

                if ($numc > 700)

                    $numce = $numce . (self::decena($numc - 700));

            } else if ($numc >= 600 && $numc <= 699) {

                $numce = "SEISCIENTOS ";

                if ($numc > 600)

                    $numce = $numce . (self::decena($numc - 600));

            } else if ($numc >= 500 && $numc <= 599) {

                $numce = "QUINIENTOS ";

                if ($numc > 500)

                    $numce = $numce . (self::decena($numc - 500));

            } else if ($numc >= 400 && $numc <= 499) {

                $numce = "CUATROCIENTOS ";

                if ($numc > 400)

                    $numce = $numce . (self::decena($numc - 400));

            } else if ($numc >= 300 && $numc <= 399) {

                $numce = "TRESCIENTOS ";

                if ($numc > 300)

                    $numce = $numce . (self::decena($numc - 300));

            } else if ($numc >= 200 && $numc <= 299) {

                $numce = "DOSCIENTOS ";

                if ($numc > 200)

                    $numce = $numce . (self::decena($numc - 200));

            } else if ($numc >= 100 && $numc <= 199) {

                if ($numc == 100)

                    $numce = "CIEN ";
                else

                    $numce = "CIENTO " . (self::decena($numc - 100));

            }

        } else

            $numce = self::decena($numc);



        return $numce;

    }



    function miles($nummero)
    {

        if ($nummero >= 1000 && $nummero < 2000) {

            $numm = "MIL " . (self::centena($nummero % 1000));

        }

        if ($nummero >= 2000 && $nummero < 10000) {

            $numm = self::unidad(Floor($nummero / 1000)) . " MIL " . (self::centena($nummero % 1000));

        }

        if ($nummero < 1000)

            $numm = self::centena($nummero);



        return $numm;

    }



    function decmiles($numdmero)
    {

        if ($numdmero == 10000)

            $numde = "DIEZ MIL";

        if ($numdmero > 10000 && $numdmero < 20000) {

            $numde = self::decena(Floor($numdmero / 1000)) . "MIL " . (self::centena($numdmero % 1000));

        }

        if ($numdmero >= 20000 && $numdmero < 100000) {

            $numde = self::decena(Floor($numdmero / 1000)) . " MIL " . (self::miles($numdmero % 1000));

        }

        if ($numdmero < 10000)

            $numde = self::miles($numdmero);



        return $numde;

    }



    function cienmiles($numcmero)
    {

        if ($numcmero == 100000)

            $num_letracm = "CIEN MIL";

        if ($numcmero >= 100000 && $numcmero < 1000000) {

            $num_letracm = self::centena(Floor($numcmero / 1000)) . " MIL " . (self::centena($numcmero % 1000));

        }

        if ($numcmero < 100000)

            $num_letracm = self::decmiles($numcmero);

        return $num_letracm;

    }



    function millon($nummiero)
    {

        if ($nummiero >= 1000000 && $nummiero < 2000000) {

            $num_letramm = "UN MILLON " . (self::cienmiles($nummiero % 1000000));

        }

        if ($nummiero >= 2000000 && $nummiero < 10000000) {

            $num_letramm = self::unidad(Floor($nummiero / 1000000)) . " MILLONES " . (self::cienmiles($nummiero % 1000000));

        }

        if ($nummiero < 1000000)

            $num_letramm = self::cienmiles($nummiero);



        return $num_letramm;

    }



    function decmillon($numerodm)
    {

        if ($numerodm == 10000000)

            $num_letradmm = "DIEZ MILLONES";

        if ($numerodm > 10000000 && $numerodm < 20000000) {

            $num_letradmm = self::decena(Floor($numerodm / 1000000)) . "MILLONES " . (self::cienmiles($numerodm % 1000000));

        }

        if ($numerodm >= 20000000 && $numerodm < 100000000) {

            $num_letradmm = self::decena(Floor($numerodm / 1000000)) . " MILLONES " . (self::millon($numerodm % 1000000));

        }

        if ($numerodm < 10000000)

            $num_letradmm = self::millon($numerodm);



        return $num_letradmm;

    }



    function cienmillon($numcmeros)
    {

        if ($numcmeros == 100000000)

            $num_letracms = "CIEN MILLONES";

        if ($numcmeros >= 100000000 && $numcmeros < 1000000000) {

            $num_letracms = self::centena(Floor($numcmeros / 1000000)) . " MILLONES " . (self::millon($numcmeros % 1000000));

        }

        if ($numcmeros < 100000000)

            $num_letracms = self::decmillon($numcmeros);

        return $num_letracms;

    }



    function milmillon($nummierod)
    {

        if ($nummierod >= 1000000000 && $nummierod < 2000000000) {

            $num_letrammd = "MIL " . (self::cienmillon($nummierod % 1000000000));

        }

        if ($nummierod >= 2000000000 && $nummierod < 10000000000) {

            $num_letrammd = self::unidad(Floor($nummierod / 1000000000)) . " MIL " . (self::cienmillon($nummierod % 1000000000));

        }

        if ($nummierod < 1000000000)

            $num_letrammd = self::cienmillon($nummierod);



        return $num_letrammd;

    }

}
