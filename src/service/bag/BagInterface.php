<?php

namespace marvin255\fias\service\bag;

/**
 * Интерфейс для объекта, который служит для передачи состояния между задачами.
 */
interface BagInterface
{
    /**
     * Задает именованый параметр для состояния.
     *
     * @param string $name  Название параметра состояния
     * @param mixed  $value Значение параметра состояния
     *
     * @return self
     */
    public function set(string $name, $value): BagInterface;

    /**
     * Возвращает параметр состояния по имени.
     *
     * @param string $name    Название параметра остояния
     * @param mixed  $default Название параметра остояния
     *
     * @return mixed
     */
    public function get(string $name, $default = null);
}
