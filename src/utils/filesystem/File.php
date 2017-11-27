<?php

namespace marvin255\fias\utils\filesystem;

use InvalidArgumentException;

/**
 * Объект, который инкапсулирует обращение к файлу в локальной
 * файловой системе.
 */
class File implements FileInterface
{
    /**
     * Абсолютный путь к файлу.
     *
     * @var string
     */
    protected $absolutePath = null;
    /**
     * Данные о файле, возвращаемые pathinfo.
     *
     * @var array
     */
    protected $info = [];

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($absolutePath)
    {
        if (empty($absolutePath)) {
            throw new InvalidArgumentException('absolutePath parameter can\'t be empty');
        }
        $info = pathinfo($absolutePath);
        $dir = $info['dirname'];
        $info['dirname'] = realpath($info['dirname']);
        if (empty($info['dirname']) || !is_writable($info['dirname'])) {
            throw new InvalidArgumentException("Can't find canonical path {$dir} or dir is unwritable");
        }
        $this->absolutePath = $info['dirname'] . '/' . $info['basename'];
        $this->info = $info;
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
        return isset($this->info['dirname']) ? $this->info['dirname'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getFilename()
    {
        return isset($this->info['filename']) ? $this->info['filename'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getExtension()
    {
        return isset($this->info['extension']) ? $this->info['extension'] : null;
    }

    /**
     * @inheritdoc
     */
    public function getBasename()
    {
        return isset($this->info['basename']) ? $this->info['basename'] : null;
    }

    /**
     * @inheritdoc
     */
    public function isExists()
    {
        return file_exists($this->absolutePath);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $return = false;
        if ($this->isExists()) {
            $return = unlink($this->absolutePath);
        }

        return $return;
    }
}
