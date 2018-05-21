<?php

namespace marvin255\fias;

/**
 * Интерфейс для объекта, который выполняет какую-либо задачу в рамках
 * обновления данных.
 */
interface TaskInterface
{
    /**
     * Запускает данную задачу на исполнение.
     *
     * @return bool
     */
    public function run(): bool;

    /**
     * Возвращает описание задачи.
     *
     * @return string
     */
    public function getDescription(): string;
}
