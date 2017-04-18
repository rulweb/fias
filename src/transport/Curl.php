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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $from);
        curl_setopt($ch, CURLOPT_FILE, $hLocal);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $start = microtime(true);
        $res = curl_exec($ch);
        if ($res === false) {
            throw new Exception('Error while downloading ' . curl_error($ch));
        }
        $time = microtime(true) - $start;
        printf('Скрипт выполнялся %.4F сек.', $time); echo "\r\n";

        curl_close($ch);
    }
}
