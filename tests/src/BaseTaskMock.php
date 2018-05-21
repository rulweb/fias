<?php

namespace marvin255\fias\tests;

use marvin255\fias\TaskInterface;
use marvin255\fias\service\bag\BagInterface;
use Exception;

/**
 * Объект, который мокает задачу, чтобы проверить работу с рефлексией.
 */
class BaseTaskMock implements TaskInterface
{
    /**
     * @param \marvin255\fias\service\bag\BagInterface
     */
    protected $bag;

    /**
     * Сеттер для объекта сервиса для передачи параметров.
     *
     * @param \marvin255\fias\service\bag\BagInterface $bag
     */
    public function setBag(BagInterface $bag)
    {
        $this->bag = $bag;
    }

    /**
     * Геттер для проверки.
     *
     * @return \marvin255\fias\service\bag\BagInterface
     */
    public function getBag()
    {
        return $this->bag;
    }

    /**
     * @inheritdoc
     */
    public function run(): bool
    {
        if (!$this->bag) {
            throw new Exception('Empty bag service for task');
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return 'Mock for task';
    }
}
