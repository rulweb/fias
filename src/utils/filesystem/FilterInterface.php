<?php

namespace marvin255\fias\utils\filesystem;

/**
 * Интерфейс для объекта, который проверяет соответствует ли файл фильтру или нет.
 */
interface FilterInterface
{
    /**
     * Проверяет файл на соответствие данному фильтру.
     *
     * @param \marvin255\fias\utils\filesystem\FileInterface $file
     *
     * @return bool
     */
    public function check(FileInterface $file);
}
