<?php

declare(strict_types=1);

namespace marvin255\fias\service\downloader;

use RuntimeException;

/**
 * Объект, который скачивает файл по ссылке с помощью curl.
 */
class Curl implements DownloaderInterface
{
    /**
     * @inheritdoc
     */
    public function download(string $urlToDownload, string $pathToLocalFile)
    {
        $fh = $this->openLocalFile($pathToLocalFile);
        $requestOptions = [
            CURLOPT_URL => $urlToDownload,
            CURLOPT_FILE => $fh,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FRESH_CONNECT => true,
        ];

        list($res, $httpCode, $error) = $this->curlDownload($requestOptions);
        fclose($fh);

        if ($res === false) {
            throw new RuntimeException("Error while downloading: {$error}");
        } elseif ($httpCode !== 200) {
            throw new RuntimeException("Url returns status: {$httpCode}");
        }
    }

    /**
     * Загружает файл по ссылке в указанный файл.
     *
     * @param array $requestOptions
     *
     * @return
     */
    protected function curlDownload(array $requestOptions)
    {
        $ch = curl_init();
        curl_setopt_array($ch, $requestOptions);

        $res = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        return [$res, $httpCode, $error];
    }

    /**
     * Открывает локальный файл, в который будет вестись запись и возвращает его
     * ресурс.
     *
     * @param string $pathToLocalFile
     *
     * @return resource
     *
     * @throws \RuntimeException
     */
    protected function openLocalFile(string $pathToLocalFile)
    {
        $hLocal = fopen($pathToLocalFile, 'wb');
        if ($hLocal === false) {
            throw new RuntimeException(
                "Can't open local file for writing: {$pathToLocalFile}"
            );
        }

        return $hLocal;
    }
}
