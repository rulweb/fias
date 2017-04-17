<?php

namespace marvin255\fias\file;

/**
 * Интерфес для объекта-обработчика файла.
 */
interface IFile
{
    /**
     * При создании объекта требуется указать путь, по которому будет расположен файл.
     *
     * @param string $path
     */
    public function __construct($path);

    /**
     * Возвращает полный канонизированный путь к файлу на диске.
     *
     * @return string
     */
    public function getPath();

    /**
     * Удаляет файл.
     *
     * @return \marvin255\fias\file\File
     */
    public function delete();
}
