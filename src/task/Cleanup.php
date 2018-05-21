<?php

declare(strict_types=1);

namespace marvin255\fias\task;

use marvin255\fias\TaskInterface;
use marvin255\fias\service\filesystem\DirectoryInterface;

/**
 * Задача, которая удаляет содержимое рабочей папки после завершения очереди.
 */
class Cleanup implements TaskInterface
{
    /**
     * @var \marvin255\fias\service\filesystem\DirectoryInterface
     */
    protected $workDirectory;

    /**
     * @inheritdoc
     */
    public function run(): bool
    {
        $this->workDirectory->deleteChildren();

        return true;
    }

    /**
     * Сеттер для объекта с рабочей папкой, в которую нужно сохранить файл.
     *
     * @param \marvin255\fias\service\filesystem\DirectoryInterface $workDirectory
     *
     * @return self
     */
    public function setWorkDirectory(DirectoryInterface $workDirectory): Cleanup
    {
        $this->workDirectory = $workDirectory;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return 'Clean up xml file after work';
    }
}
