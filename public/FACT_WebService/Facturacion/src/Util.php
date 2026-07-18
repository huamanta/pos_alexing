<?php

require_once __DIR__ . '/../../../../configuraciones/bootstrap.php';
use Greenter\Model\DocumentInterface;
use Greenter\Model\Response\CdrResponse;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\See;



final class Util
{

    /**
     * @var Util
     */
    private static $current;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!self::$current instanceof self) {
            self::$current = new self();
        }
        return self::$current;
    }

    public function abrirConexion()
    {
        $server = env('DB_HOST');
        $usuario = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');
        $database = env('DB_DATABASE');


        $conexion = mysqli_connect($server, $usuario, $pass, $database);

        if (!$conexion) {
            die("Error: " . mysqli_connect_error());
        }

        return $conexion;
    }

    public function desconectar($conexion)
    {
        mysqli_close($conexion);
    }

    /**

     * @param string $endpoint

     * @return See

     */
    public function getSee($idsucursal)
    {
        $conexion = $this->abrirConexion();

        $sqlSucursal = mysqli_query($conexion, 'SELECT * FROM sucursal s INNER JOIN empresas e ON s.idempresa = e.idempresa WHERE s.idsucursal = ' . $idsucursal);
        $sucursal = mysqli_fetch_assoc($sqlSucursal);
        $ruc = $sucursal['documento'] ?? '';
        $usuario = $sucursal['usuario_sol'] ?? '';
        $contrasena = $sucursal['clave_sol'] ?? '';
        $contrasenacertificado = $sucursal['clave_certificado'] ?? '';
        $estadocertificado = !empty($sucursal['estado_certificado']) ? $sucursal['estado_certificado'] : 'BETA';
        $rutaCertificado = !empty($sucursal['ruta_certificado']) ? $sucursal['ruta_certificado'] : '/certificado.pem';
        
        $sunatEnpoint = SunatEndpoints::FE_BETA;
        if ($estadocertificado == "PRODUCCION") {
            $sunatEnpoint = SunatEndpoints::FE_PRODUCCION;
        }
        $see = new See();
        $see->setService($sunatEnpoint);

        if (file_exists(__DIR__ . $rutaCertificado)) {
            $pfx = file_get_contents(__DIR__ . $rutaCertificado);
            $see->setCertificate($pfx);
        }

        $see->setCredentials($ruc . '' . $usuario, $contrasena);
        $see->setCachePath(__DIR__ . '/../cache');

        $this->desconectar($conexion);

        return $see;
    }

    public function showResponse(DocumentInterface $document, CdrResponse $cdr, $id, $tipo, $EMP)
    {
        $filename = $document->getName();

        $conexion = $this->abrirConexion();
        if ($cdr->isAccepted()) {
            if ($tipo == "DocVenta") {
                $sql0 = mysqli_query($conexion, "UPDATE venta set dov_Estado='ACEPTADO', estado='Aceptado', dov_Nombre = '" . $filename . "', dov_IdEmpleado='" . $EMP . "' WHERE idventa='" . $id . "'");
            } elseif ($tipo == "Nota") {
                $sql0 = mysqli_query($conexion, "UPDATE venta SET dov_Estado='ACEPTADO', estado='Aceptado',dov_Nombre='" . $filename . "',dov_IdEmpleado='" . $EMP . "' WHERE idventa='" . $id . "'");
            }
        } else {
            if ($tipo == "DocVenta") {
                $sql0 = mysqli_query($conexion, "UPDATE venta set dov_Estado='RECHAZADO' ,estado='Rechazado', dov_IdEmpleado='" . $EMP . "' WHERE idventa='" . $id . "'");
            } elseif ($tipo == "Nota") {
                $sql0 = mysqli_query($conexion, "UPDATE venta set dov_Estado='RECHAZADO' ,estado='Rechazado', dov_IdEmpleado='" . $EMP . "' WHERE idventa='" . $id . "'");
            }
        }

        if ($cdr->isAccepted() == 1) {

            if ($tipo == "ComunicacionBaja") {


                $sql0 = mysqli_query($conexion, "UPDATE comunicacion_baja SET COB_Nombre ='" . $filename . "', COB_Estado ='ACEPTADO' WHERE COB_Id ='" . $id . "'");
                //   echo $sql0;

            }
        } else {
            if ($tipo == "ComunicacionBaja") {
                $sql0 = mysqli_query($conexion, "UPDATE comunicacion_baja SET COB_Nombre ='" . $filename . "', COB_Estado ='RECHAZADO' WHERE COB_Id ='" . $id . "'");
            }
        }

        $this->desconectar($conexion);
        /*       mysqli_close($conexion);
           }
           else
           {
               echo 'error al conectar';
           }*/
    }



    public function getErrorResponse(\Greenter\Model\Response\Error $error)
    {

        $result = <<<HTML

        Error

        Codigo: {$error->getCode()}

        Descripcion: {$error->getMessage()}

        HTML;

        return $result;

    }



    public function writeXml(DocumentInterface $document, $xml)
    {

        $this->writeFile($document->getName() . '.xml', $xml);

    }

    public function writeCdr(DocumentInterface $document, $zip)
    {

        $this->writeFile('R-' . $document->getName() . '.zip', $zip);

    }



    public function writeFile($filename, $content)
    {
        if (getenv('GREENTER_NO_FILES')) {
            return;
        }

        $path = __DIR__ . '/../files/';

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents($path . $filename, $content);
    }



    public function getPdf(DocumentInterface $document): ?string
    {
        return null;
    }



    public static function generator($item, $count)
    {

        $items = [];



        for ($i = 0; $i < $count; $i++) {

            $items[] = $item;

        }



        return $items;

    }



    public function showPdf(?string $content, ?string $filename): void
    {
        $this->writeFile($filename, $content);
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($content));

        echo $content;
    }




    public function imprimePdf($content)
    {

        $handle = printer_open();

        printer_write($handle, $content);

        printer_close($handle);

    }



    public static function getPathBin()
    {

        $path = __DIR__ . '/../vendor/bin/wkhtmltopdf';

        if (self::isWindows()) {

            $path .= '.exe';

        }



        return $path;

    }



    public static function isWindows()
    {

        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

    }



    public static function inPath($command)
    {

        $whereIsCommand = self::isWindows() ? 'where' : 'which';



        $process = proc_open(

            "$whereIsCommand $command",

            array(

                0 => array("pipe", "r"), //STDIN

                1 => array("pipe", "w"), //STDOUT

                2 => array("pipe", "w"), //STDERR

            ),

            $pipes

        );

        if ($process !== false) {

            $stdout = stream_get_contents($pipes[1]);

            stream_get_contents($pipes[2]);

            fclose($pipes[1]);

            fclose($pipes[2]);

            proc_close($process);



            return $stdout != '';

        }



        return false;

    }



    private function getHash(DocumentInterface $document)
    {

        $see = $this->getSee('');

        $xml = $see->getXmlSigned($document);



        $hash = (new \Greenter\Report\XmlUtils())->getHashSign($xml);



        return $hash;

    }



    private static function getParametersPdf(): array
    {
        $logo = file_get_contents(__DIR__ . '/../resources/logo.png');

        return [
            'system' => [
                'logo' => $logo,
                'hash' => ''
            ],
            'user' => [
                'resolucion' => '212321',
                'header' => 'Telf: <b>(056) 123375</b>',
                'extras' => [
                    ['name' => 'FORMA DE PAGO', 'value' => 'Contado'],
                    ['name' => 'VENDEDOR', 'value' => 'GITHUB SELLER'],
                ],
            ]
        ];
    }

}