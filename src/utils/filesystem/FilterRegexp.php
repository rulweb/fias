<?php

namespace marvin255\fias\utils\filesystem;

use InvalidArgumentException;

/**
 * Проверяет, чтобы имя файла подходило под указанное регулярное выражение.
 */
class FilterRegexp implements FilterInterface
{
    /**
     * Регулярное выражение для проверки.
     *
     * @var string
     */
    protected $regexp = null;

    /**
     * Конструктор.
     * Задает допустимые регулярное выражение.
     *
     * @param string $regexp
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($regexp)
    {
        if (empty($regexp)) {
            throw new InvalidArgumentException('Empty regexp list');
        }
        $this->regexp = '/' . trim($regexp, '/ ') . '/i';
    }

    /**
     * @inheritdoc
     */
    public function check(FileInterface $file)
    {
        return (bool) preg_match($this->regexp, $file->getFilename());
    }
}
