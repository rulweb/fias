<?php

namespace marvin255\fias\utils\unpacker;

/**
 * Интерфейс для объекта, который распаковывает файлы из архива.
 */
interface UnpackerInterface
{
    /**
     * Распаковывает файл, указанный в первом параметре, в каталог, указанный во втором параметре.
     *
     * @param string $archive
     * @param string $extractTo
     */
    public function unpack($archive, $extractTo);
}
