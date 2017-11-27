<?php

namespace marvin255\fias\utils\transport;

use marvin255\fias\utils\filesystem\FileInterface;

/**
 * Загрузка файла с помощью curl.
 */
class Curl implements TransportInterface
{
    /**
     * @inheritdoc
     */
    public function load($from, FileInterface $file)
    {
        $hLocal = fopen($file->getPathname(), 'wb');
        if ($hLocal === false) {
            throw new Exception("Can't open local file for writing: " . $file->getPath());
        }

        $ch = curl_init($from);
        curl_setopt($ch, CURLOPT_FILE, $hLocal);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        fclose($hLocal);
        curl_close($ch);

        if ($res === false) {
            $file->delete();
            throw new Exception("Error while downloading {$error}");
        } elseif ($httpCode !== 200) {
            $file->delete();
            throw new Exception("Url returns status {$httpCode}");
        }
    }
}
