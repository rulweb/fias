<?php

namespace marvin255\fias\utils\transport;

/**
 * Интерфес для объекта, который загружает файл с удаленного url.
 */
interface TransportInterface
{
    /**
     * Загружает файл по указанной в первом параметре ссылке на локальный диск
     * в файл, указанный во втором параметре.
     *
     * @param string $from
     * @param string $to
     *
     * @return \marvin255\fias\utils\transport\TransportInterface
     */
    public function download($from, $to);
}
