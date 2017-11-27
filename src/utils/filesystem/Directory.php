<?php

namespace marvin255\fias\utils\filesystem;

use DirectoryIterator;
use RuntimeException;
use InvalidArgumentException;

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
     * Класс для создания новых папок.
     *
     * @var string
     */
    protected $directoryClass = null;
    /**
     * Внутренний итератор для обхода вложенных файлов и папок.
     *
     * @var DirectoryIterator
     */
    protected $iterator = null;

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($absolutePath, $fileClass = File::class, $directoryClass = self::class)
    {
        if (trim($absolutePath, ' \t\n\r\0\x0B\\/') === '') {
            throw new InvalidArgumentException("absolutePath parameter can't be empty");
        }
        if (!preg_match('/^\/[a-z_]+.*[^\/]+$/', $absolutePath)) {
            throw new InvalidArgumentException("absolutePath must starts from root, and consist of digits and letters");
        }
        if (!is_subclass_of($fileClass, FileInterface::class)) {
            throw new InvalidArgumentException("{$fileClass} must be a FileInterface instance");
        }
        if (!is_subclass_of($directoryClass, DirectoryInterface::class)) {
            throw new InvalidArgumentException("{$directoryClass} must be a DirectoryInterface instance");
        }
        $this->absolutePath = $absolutePath;
        $this->fileClass = $fileClass;
        $this->directoryClass = $directoryClass;
    }

    /**
     * @inheritdoc
     */
    public function getPathname()
    {
        return $this->absolutePath;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return dirname($this->absolutePath);
    }

    /**
     * @inheritdoc
     */
    public function getFoldername()
    {
        return pathinfo($this->absolutePath, PATHINFO_BASENAME);
    }

    /**
     * @inheritdoc
     */
    public function isExists()
    {
        return (bool) is_dir($this->absolutePath);
    }

    /**
     * @inheritdoc
     */
    public function delete()
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
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    public function create()
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
    public function createChildFolder($name)
    {
        if (!preg_match('/^[a-z]+[a-z0-9_]*$/', $name)) {
            throw new InvalidArgumentException("Wrong folder name {$name}");
        }

        $class = $this->folderClass;

        return new $class($this->absolutePath . '/' . $name);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function createChildFile($name)
    {
        if (!preg_match('/^[a-z]+[a-z0-9_\.]*$/', $name)) {
            throw new InvalidArgumentException("Wrong file name {$name}");
        }

        $class = $this->fileClass;

        return new $class($this->absolutePath . '/' . $name);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        $return = null;
        if ($iterator = $this->getIterator()) {
            $item = $iterator->current();
            while ($item && $item->isDot()) {
                $iterator->next();
                $item = $iterator->current();
            }
            if ($item) {
                if ($item->isDir()) {
                    var_dump($item);
                    $return = $this->createChildFolder($item->getFilename());
                } elseif ($item->isFile()) {
                    $return = $this->createChildFile($item->getFilename());
                }
            }
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        $return = null;
        if ($iterator = $this->getIterator()) {
            $return = $iterator->key();
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        if ($iterator = $this->getIterator()) {
            $iterator->next();
        }
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        if ($iterator = $this->getIterator()) {
            $iterator->rewind();
        }
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        $iterator = $this->getIterator();

        return $iterator && $iterator->valid();
    }

    /**
     * Возвращает внутренний объект итератора для перебора содержимого данной папки.
     *
     * @return \DirectoryIterator
     */
    protected function getIterator()
    {
        if ($this->iterator === null && $this->isExists()) {
            $this->iterator = new DirectoryIterator($this->getPathname());
        }

        return $this->iterator;
    }
}
