<?php

class Operaciones
{
    public function CrearZip($fileName)
    {
        $zip = new ZipArchive();
        $zip->open(__DIR__ . '/../files/' . $fileName . ".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile(__DIR__ . '/../files/' . $fileName . ".xml", $fileName . ".xml");
        $zip->close();
    }
    
    public function ConvertirBase64($fileName)
    {
        $ruta = __DIR__ . '/../files/' . $fileName . ".zip";
        $contenidoBinario = file_get_contents($ruta);
        return base64_encode($contenidoBinario);
    }

    public function ConvertirSHA256($fileName)
    {
        $ruta = __DIR__ . '/../files/' . $fileName . ".zip";
        $contenidoBinario = file_get_contents($ruta);
        return hash('sha256', $contenidoBinario);
    }

    public function ConvertirBase64_Zip($cadena, $fileName)
    {
        $filesDir = __DIR__ . '/../files';
        if (!is_dir($filesDir)) {
            @mkdir($filesDir, 0777, true);
        }

        $decoded = base64_decode($cadena, true);
        if ($decoded === false) {
            return false;
        }

        $tempZipPath = $filesDir . '/temporal.zip';
        $entryName = 'R-' . $fileName . '.zip';

        $zip = new ZipArchive();
        $res = $zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($res === true) {
            $zip->addFromString($entryName, $decoded);
            $zip->close();
        } else {
            return false;
        }

        $zip = new ZipArchive();
        if ($zip->open($tempZipPath) === true) {
            $zip->extractTo($filesDir);
            $zip->close();
            @unlink($tempZipPath);
            return $filesDir . '/' . $entryName;
        }

        return false;
    }
}
