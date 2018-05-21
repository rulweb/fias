<?php

declare(strict_types=1);

namespace marvin255\fias\service\bag;

/**
 * Объект, который служит для передачи состояния между задачами.
 */
class Bag implements BagInterface
{
    /**
     * @var array
     */
    protected $bag = [];

    /**
     * @inheritdoc
     */
    public function set(string $name, $value): BagInterface
    {
        $this->bag[$name] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get(string $name, $default = null)
    {
        return isset($this->bag[$name]) ? $this->bag[$name] : $default;
    }
}
