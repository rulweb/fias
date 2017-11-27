<?php

namespace marvin255\fias\pipe;

use InvalidArgumentException;

/**
 * Объект, который передает информацию между задачами очереди.
 *
 * Вспомогательный объект, который служит для того, чтобы передавать данные
 * между задачами внутри очереди. Данный объект попадает в каждую задачу очереди.
 */
class Flow implements FlowInterface
{
    /**
     * Массив, который хранит в себе параметры для передачи.
     *
     * @var array
     */
    protected $params = [];

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function set($name, $value)
    {
        if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $name)) {
            throw new InvalidArgumentException("name parameter must consist only of digits and letters, got: {$name}");
        }

        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setAll(array $values)
    {
        $this->params = [];
        foreach ($values as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->params;
    }
}
