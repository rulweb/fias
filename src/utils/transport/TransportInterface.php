<?php

namespace marvin255\fias\transport;

use marvin255\fias\utils\filesystem\FileInterface;

/**
 * Интерфес для объекта, который загружает файл с удаленного url.
 */
interface TransportInterface
{
    /**
     * Загружает файл по указанной в первом параметре ссылке на локальный диск в файл, указанный во втором параметре.
     *
     * @param string                                         $from
     * @param \marvin255\fias\utils\filesystem\FileInterface $file
     */
    public function load($from, FileInterface $file);
}
