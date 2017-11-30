<?php

namespace marvin255\fias\utils\filesystem;

use InvalidArgumentException;

/**
 * Проверяет, чтобы файл был с одним из указанных в конструкторе расширений.
 */
class FilterExtension implements FilterInterface
{
    /**
     * Массив допустимых расширений.
     *
     * @var array
     */
    protected $extensions = null;

    /**
     * Конструктор.
     * Задает допустимые расширения.
     *
     * @param array $extensions
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $extensions)
    {
        if (empty($extensions)) {
            throw new InvalidArgumentException('Empty extensions list');
        }
        foreach ($extensions as $key => $ext) {
            if (!is_string($ext)) {
                throw new InvalidArgumentException("Extension number {$key} must be a string instance");
            } elseif (trim($ext) === '') {
                throw new InvalidArgumentException("Extension number {$key} is empty");
            }
        }
        $this->extensions = $extensions;
    }

    /**
     * @inheritdoc
     */
    public function check(FileInterface $file)
    {
        $ext = $file->getExtension();
        $return = false;
        foreach ($this->extensions as $try) {
            if (strtolower($try) !== strtolower($ext)) {
                continue;
            }
            $return = true;
            break;
        }

        return $return;
    }
}
