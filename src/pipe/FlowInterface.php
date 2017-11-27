<?php

namespace marvin255\fias\pipe;

/**
 * Интерфейс для объекта, который передает информацию между задачами очереди.
 *
 * Вспомогательный объект, который служит для того, чтобы передавать данные
 * между задачами внутри очереди. Данный объект попадает в каждую задачу очереди.
 */
interface FlowInterface
{
    /**
     * Задает именованный параметр, который будет передан в последующие задачи
     * очереди.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return \marvin255\fias\pipe\FlowInterface
     */
    public function set($name, $value);

    /**
     * Задает параметры из массива. Предварительно очищает все ранее установленные
     * параметры.
     *
     * @param array $values Массив вида "имя параметра => значение"
     *
     * @return \marvin255\fias\pipe\FlowInterface
     */
    public function setAll(array $values);

    /**
     * Возвращает значение параметра по имени или null, если параметр не задан.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Возвращает массив со всеми зарегистрированными параметрами.
     *
     * @return array Массив вида "имя параметра => значение"
     */
    public function getAll();
}
