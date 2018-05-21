<?php

declare(strict_types=1);

namespace marvin255\fias\task;

use marvin255\fias\TaskInterface;
use marvin255\fias\service\filesystem\DirectoryInterface;
use marvin255\fias\service\unpacker\UnpackerInterface;
use marvin255\fias\service\bag\BagInterface;

/**
 * Задача, которая распаковывает полученный на предыдущих шагах архив.
 */
class Unpack implements TaskInterface
{
    /**
     * @var \marvin255\fias\service\unpacker\UnpackerInterface
     */
    protected $unpacker;
    /**
     * @var \marvin255\fias\service\filesystem\DirectoryInterface
     */
    protected $workDirectory;
    /**
     * @var \marvin255\fias\service\bag\BagInterface
     */
    protected $paramsBag;

    /**
     * @inheritdoc
     */
    public function run(): bool
    {
        $archiveName = $this->paramsBag->get('archive.name');
        $file = $this->workDirectory->createChildFile($archiveName);

        if ($file->isExists()) {
            $this->unpacker->unpack(
                $file->getPathname(),
                $this->workDirectory->getPathname()
            );
            $file->delete();
        }

        return true;
    }

    /**
     * Сеттер для объекта с рабочей папкой, в которую нужно сохранить файл.
     *
     * @param \marvin255\fias\service\unpacker\UnpackerInterface $unpacker
     *
     * @return self
     */
    public function setUnpacker(UnpackerInterface $unpacker): Unpack
    {
        $this->unpacker = $unpacker;

        return $this;
    }

    /**
     * Сеттер для объекта с рабочей папкой, в которую нужно сохранить файл.
     *
     * @param \marvin255\fias\service\filesystem\DirectoryInterface $workDirectory
     *
     * @return self
     */
    public function setWorkDirectory(DirectoryInterface $workDirectory): Unpack
    {
        $this->workDirectory = $workDirectory;

        return $this;
    }

    /**
     * Сеттер для объекта, который передает данные между задачами.
     *
     * @param \marvin255\fias\service\bag\BagInterface $paramsBag
     *
     * @return self
     */
    public function setParamsBag(BagInterface $paramsBag): Unpack
    {
        $this->paramsBag = $paramsBag;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return 'Unrar archive';
    }
}
