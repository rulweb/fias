<?php

namespace marvin255\fias\utils\filesystem;

/**
 * Интерфейс для объекта, который инкапсулирует обращение к файлу в локальной
 * файловой системе.
 */
interface FileInterface
{
    /**
     * Конструктор. Задает абсолютный путь к файлу.
     *
     * Папка должна существовать и должна быть доступна на запись.
     *
     * @param string $absolutePath
     */
    public function __construct($absolutePath);

    /**
     * Возвращает путь и имя файла.
     *
     * @return string
     */
    public function getPathname();

    /**
     * Возвращает путь без имени файла.
     *
     * @return string
     */
    public function getPath();

    /**
     * Возвращает имя файла.
     *
     * @return string
     */
    public function getFilename();

    /**
     * Возвращает расширение файла.
     *
     * @return string
     */
    public function getExtension();

    /**
     * Возвращает имя файла (без расширения).
     *
     * @return string
     */
    public function getBasename();

    /**
     * Возвращает true, если файл существует в файловой системе.
     *
     * @return bool
     */
    public function isExists();

    /**
     * Удаляет файл из файловой системы.
     *
     * @return bool
     */
    public function delete();
}
