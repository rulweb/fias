<?php

namespace marvin255\fias\service\filesystem;

use Iterator;

/**
 * Интерфейс для объекта, который инкапсулирует обращение к папке в локальной
 * файловой системе.
 */
interface DirectoryInterface extends Iterator
{
    /**
     * Возвращает путь и имя папки.
     *
     * @return string
     */
    public function getPathname(): string;

    /**
     * Возвращает путь без имени папки.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Возвращает имя папки.
     *
     * @return string
     */
    public function getFoldername(): string;

    /**
     * Возвращает true, если папка существует в файловой системе.
     *
     * @return bool
     */
    public function isExists(): bool;

    /**
     * Удаляет папку из файловой системы.
     *
     * @return bool
     */
    public function delete(): bool;

    /**
     * Удаляет все содержимое папки, сама папка остается нетронутой.
     *
     * @return bool
     */
    public function deleteChildren(): bool;

    /**
     * Создает папку и все родительские.
     *
     * @return bool
     */
    public function create(): bool;

    /**
     * Создает вложенную папку.
     *
     * @param string $name
     *
     * @return \marvin255\fias\service\filesystem\DirectoryInterface
     */
    public function createChildDirectory(string $name): DirectoryInterface;

    /**
     * Создает вложенный файл.
     *
     * @param string $name
     *
     * @return \marvin255\fias\service\filesystem\FileInterface
     */
    public function createChildFile(string $name): FileInterface;

    /**
     * Ищет файл в текущей папке по указанному паттерну.
     *
     * @param string $pattern
     *
     * @return array
     */
    public function findFilesByPattern(string $pattern): array;
}
