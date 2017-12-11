<?php

namespace marvin255\fias\utils\transport;

/**
 * Загрузка файла с помощью curl.
 */
class Curl implements TransportInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\fias\utils\transport\Exception
     */
    public function download($from, $to)
    {
        $hLocal = fopen($to, 'wb');
        if ($hLocal === false) {
            throw new Exception("Can't open local file for writing: " . $to);
        }

        $ch = curl_init($from);
        curl_setopt($ch, CURLOPT_FILE, $hLocal);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        $res = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        fclose($hLocal);
        curl_close($ch);

        if ($res === false) {
            throw new Exception("Error while downloading {$error}");
        } elseif ($httpCode !== 200) {
            throw new Exception("Url returns status {$httpCode}");
        }

        return $this;
    }
}
