<?php

namespace marvin255\fias\archiver;

/**
 * Интерфес для объекта, который распаковывает файл из архива.
 */
interface IArchiver
{
    /**
     * Распаковывает файл, указанный в первом параметре, в каталог, указанный во втором параметре.
     *
     * @param \marvin255\fias\file\IFile $from
     * @param string $to
     */
    public function extract(\marvin255\fias\file\IFile $from, $to);
}
