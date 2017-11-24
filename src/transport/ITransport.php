<?php

namespace marvin255\fias\transport;

/**
 * Интерфес для объекта, который загружает файл с удаленного url.
 */
interface ITransport
{
    /**
     * Загружает файл по указанной в первом параметре ссылке на локальный диск в файл, указанный во втором параметре.
     *
     * @param string                     $from
     * @param \marvin255\fias\file\IFile $file
     */
    public function load($from, \marvin255\fias\file\IFile $file);
}
