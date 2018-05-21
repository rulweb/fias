<?php

namespace marvin255\fias\service\xml;

use Iterator;

/**
 * Интерфейс для объекта, который читает данные из файла xml.
 */
interface ReaderInterface extends Iterator
{
    /**
     * Открывает указанный файл для чтения.
     *
     * @param string $source     Абсолютный путь к файлу, который нужно открыть
     * @param string $pathToNode Путь до узла, который нужно прочитать
     * @param array  $select     Массив параметров, который нужно выбрать из узла
     *
     * @return \marvin255\fias\reader\ReaderInterface
     */
    public function open(string $source, string $pathToNode, array $select): ReaderInterface;

    /**
     * Закрывает файл после чтения.
     *
     * @return \marvin255\fias\reader\ReaderInterface
     */
    public function close(): ReaderInterface;
}
