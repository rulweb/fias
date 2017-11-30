<?php

namespace marvin255\fias\processor;

/**
 * Интерфейс для объекта, который обрабатывает данные полученные из файла.
 */
interface ProcessorInterface
{
    /**
     * Уведомляет обработчик о начале работы.
     *
     * @return \marvin255\fias\processor\ProcessorInterface
     */
    public function open();

    /**
     * Обрабатывает указанный набор данных.
     *
     * @param array $data Массив с данными, которые необходимо обработать
     *
     * @return \marvin255\fias\processor\ProcessorInterface
     */
    public function process(array $data);

    /**
     * Уведомляет обработчик о завершении работы.
     *
     * @return \marvin255\fias\processor\ProcessorInterface
     */
    public function close();
}
