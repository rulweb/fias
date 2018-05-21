<?php

declare(strict_types=1);

namespace marvin255\fias\service\filesystem;

use CallbackFilterIterator;
use DirectoryIterator;
use RuntimeException;
use InvalidArgumentException;
use Iterator;

/**
 * Объект, который инкапсулирует обращение к папке в локальной файловой системе.
 */
class Directory implements DirectoryInterface
{
    /**
     * Абсолютный путь к папке.
     *
     * @var string
     */
    protected $absolutePath = null;
    /**
     * Класс для создания новых файлов.
     *
     * @var string
     */
    protected $fileClass = null;
    /**
     * Внутренний итератор для обхода вложенных файлов и папок.
     *
     * @var DirectoryIterator
     */
    protected $iterator = null;

    /**
     * Конструктор. Задает абсолютный путь к папке, а так же классы для
     * создания вложенных папок и файлов.
     *
     * Папка должна существовать и должна быть доступна на запись.
     *
     * @param string $absolutePath
     * @param string $fileClass
     */
    public function __construct(string $absolutePath, string $fileClass = File::class)
    {
        if (trim($absolutePath, ' \t\n\r\0\x0B\\/') === '') {
            throw new InvalidArgumentException(
                "absolutePath parameter can't be empty"
            );
        }

        if (!preg_match('/^\/[a-z_]+.*[^\/]+$/', $absolutePath)) {
            throw new InvalidArgumentException(
                'absolutePath must starts from root, and consist of digits and letters'
            );
        }

        if (!is_subclass_of($fileClass, FileInterface::class)) {
            throw new InvalidArgumentException(
                "{$fileClass} must implements a FileInterface"
            );
        }

        $this->absolutePath = $absolutePath;
        $this->fileClass = $fileClass;
    }

    /**
     * @inheritdoc
     */
    public function getPathname(): string
    {
        return $this->absolutePath;
    }

    /**
     * @inheritdoc
     */
    public function getPath(): string
    {
        return dirname($this->absolutePath);
    }

    /**
     * @inheritdoc
     */
    public function getFoldername(): string
    {
        return pathinfo($this->absolutePath, PATHINFO_BASENAME);
    }

    /**
     * @inheritdoc
     */
    public function isExists(): bool
    {
        return (bool) is_dir($this->absolutePath);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function delete(): bool
    {
        $return = false;
        if ($this->isExists()) {
            foreach ($this as $child) {
                $child->delete();
            }
            if (!rmdir($this->getPathname())) {
                throw new RuntimeException("Can't delete folder: " . $this->getPathname());
            }
            $return = true;
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function deleteChildren(): bool
    {
        $return = false;
        if ($this->isExists()) {
            foreach ($this as $child) {
                $child->delete();
            }
            $return = true;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function create(): bool
    {
        $return = false;
        if (!$this->isExists()) {
            $path = $this->getPathname();
            $arPath = explode('/', ltrim($path, '/\\'));
            $current = '';
            foreach ($arPath as $folder) {
                $current .= '/' . $folder;
                if (is_dir($current)) {
                    continue;
                }
                if (!mkdir($current)) {
                    throw new RuntimeException("Can't create {$current} folder");
                }
            }
            $return = true;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function createChildDirectory(string $name): DirectoryInterface
    {
        if (!preg_match('/^[a-z0-9_\-]*$/i', $name)) {
            throw new InvalidArgumentException("Wrong folder name {$name}");
        }

        return new self($this->absolutePath . '/' . $name, $this->fileClass);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function createChildFile(string $name): FileInterface
    {
        if (!preg_match('/^[a-z0-9_\.\-]*$/i', $name)) {
            throw new InvalidArgumentException("Wrong file name {$name}");
        }

        $class = $this->fileClass;

        return new $class($this->absolutePath . '/' . $name);
    }

    /**
     * @inheritdoc
     */
    public function findFilesByPattern(string $pattern): array
    {
        $return = [];
        $regexp = '/^' . implode('[^\/\.]+', array_map('preg_quote', explode('*', $pattern))) . '$/';

        foreach ($this->getIterator() as $file) {
            if ($file->isFile() && preg_match($regexp, $file->getFilename())) {
                $return[] = $this->createChildFile($file->getFilename());
            }
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        $item = $this->getIterator()->current();

        if ($item->isDir()) {
            $return = $this->createChildDirectory($item->getFilename());
        } elseif ($item->isFile()) {
            $return = $this->createChildFile($item->getFilename());
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->getIterator()->key();
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        $this->getIterator()->next();
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->getIterator()->rewind();
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->getIterator()->valid();
    }

    /**
     * Возвращает внутренний объект итератора для перебора содержимого данной папки.
     *
     * @return \DirectoryIterator
     */
    protected function getIterator(): Iterator
    {
        if ($this->iterator === null && $this->isExists()) {
            $dirIterator = new DirectoryIterator($this->getPathname());
            $this->iterator = new CallbackFilterIterator($dirIterator, function ($current, $key, $iterator) {
                return !$iterator->isDot();
            });
        }

        return $this->iterator;
    }
}
