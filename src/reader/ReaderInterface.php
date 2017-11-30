<?php

namespace marvin255\fias\reader;

use Iterator;

/**
 * Интерфейс для объекта, который читает данные из файла.
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
     * @return \marvin255\fias\reader\ReaderInterface
     */
    public function open($source);

    /**
     * Закрывает файл после чтения.
     *
     * @return \marvin255\fias\reader\ReaderInterface
     */
    public function close();
}
