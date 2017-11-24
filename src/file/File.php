<?php

namespace marvin255\fias\file;

/**
 * Обработчик для экземпляра файла.
 */
class File implements IFile
{
    /**
     * @var string
     */
    protected $path = null;

    /**
     * @inheritdoc
     */
    public function __construct($path)
    {
        $dir = realpath(pathinfo($path, PATHINFO_DIRNAME));
        if ($dir === false) {
            throw new Exception("Can't find path for {$path}");
        }
        if (!is_writable($dir)) {
            throw new Exception("Directory {$dir} must be writable");
        }
        $file = pathinfo($path, PATHINFO_BASENAME);
        if (is_dir("{$dir}/{$file}")) {
            throw new Exception("{$dir}/{$file} is a directory");
        }
        $this->path = "{$dir}/{$file}";
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
    public function getDirname()
    {
        $path = $this->getPath();

        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        if (!unlink($this->getPath())) {
            throw new Exception("Can't delete file " . $this->getPath());
        }

        return $this;
    }
}
