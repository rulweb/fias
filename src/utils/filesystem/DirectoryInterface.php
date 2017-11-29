<?php

namespace marvin255\fias\utils\filesystem;

use Iterator;

/**
 * Интерфейс для объекта, который инкапсулирует обращение к папке в локальной
 * файловой системе.
 */
interface DirectoryInterface extends Iterator
{
    /**
     * Конструктор. Задает абсолютный путь к папке, а так же классы для
     * создания вложенных папок и файлов.
     *
     * Папка должна существовать и должна быть доступна на запись.
     *
     * @param string $absolutePath
     * @param string $fileClass
     * @param string $directoryClass
     */
    public function __construct($absolutePath, $fileClass = File::class, $directoryClass = Directory::class);

    /**
     * Возвращает путь и имя папки.
     *
     * @return string
     */
    public function getPathname();

    /**
     * Возвращает путь без имени папки.
     *
     * @return string
     */
    public function getPath();

    /**
     * Возвращает имя папки.
     *
     * @return string
     */
    public function getFoldername();

    /**
     * Возвращает true, если папка существует в файловой системе.
     *
     * @return bool
     */
    public function isExists();

    /**
     * Удаляет папку из файловой системы.
     *
     * @return bool
     */
    public function delete();

    /**
     * Создает папку и все родительские.
     *
     * @return bool
     */
    public function create();

    /**
     * Создает вложенную папку.
     *
     * @param string $name
     *
     * @return \marvin255\fias\utils\filesystem\DirectoryInterface
     */
    public function createChildDirectory($name);

    /**
     * Создает вложенный файл.
     *
     * @param string $name
     *
     * @return \marvin255\fias\utils\filesystem\FileInterface
     */
    public function createChildFile($name);
}
