<?php

namespace marvin255\fias\service\downloader;

/**
 * Интерфейс для объекта, который скачивает файл по ссылке.
 */
interface DownloaderInterface
{
    /**
     * Скачивает файл по ссылке из первого параметра в локальный файл,
     * указанный во втором параметре.
     *
     * @param string $urlToDownload
     * @param string $pathToLocalFile
     *
     * @throws \RuntimeException
     */
    public function download(string $urlToDownload, string $pathToLocalFile);
}
