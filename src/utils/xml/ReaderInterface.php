<?php

namespace marvin255\fias\utils\xml;

use Iterator;

/**
 * Интерфейс для объекта, который читает данные из xml файла.
 *
 * Расширяет интерфейс итератора.
 */
interface ReaderInterface extends Iterator
{
    /**
     * Открывает указанный файл для чтения.
     *
     * @param string $source Абсолютный путь к файлу, который нужно открыть
     *
     * @return \marvin255\fias\utils\xml\ReaderInterface
     */
    public function open($source);

    /**
     * Закрывает файл после чтения.
     *
     * @return \marvin255\fias\utils\xml\ReaderInterface
     */
    public function close();
}
