<?php

namespace marvin255\fias\transport;

/**
 * Загрузка файла с помощью curl.
 */
class Curl implements ITransport
{
    /**
     * @inheritdoc
     */
    public function load($from, \marvin255\fias\file\IFile $file)
    {
        $hLocal = fopen($file->getPath(), 'wb');
        if ($hLocal === false) {
            throw new Exception("Can't open local file for writing");
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
