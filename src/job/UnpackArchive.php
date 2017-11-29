<?php

namespace marvin255\fias\job;

use marvin255\fias\pipe\FlowInterface;
use marvin255\fias\utils\filesystem\DirectoryInterface;
use marvin255\fias\utils\filesystem\FileInterface;
use marvin255\fias\utils\unpacker\UnpackerInterface;

/**
 * Задача, которая распаковывает архив в указанную папку.
 */
class UnpackArchive implements JobInterface
{
    /**
     * Рабочий каталог, в который будет скопирован файл.
     *
     * @var \marvin255\fias\utils\filesystem\DirectoryInterface
     */
    protected $workDir = null;
    /**
     * Объект, который отвечает за распаковку файла из архива.
     *
     * @var \marvin255\fias\utils\unpacker\UnpackerInterface
     */
    protected $unpacker = null;

    /**
     * Конструктор.
     * Задает рабочий каталог, в который будет распакован архив, и объект,
     * который будет непосредственно загружать файл.
     *
     * @param \marvin255\fias\utils\filesystem\DirectoryInterface $workDir
     * @param \marvin255\fias\utils\transport\TransportInterface  $transport
     */
    public function __construct(DirectoryInterface $workDir, UnpackerInterface $unpacker)
    {
        $this->workDir = $workDir;
        $this->unpacker = $unpacker;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\fias\job\Exception
     */
    public function run(FlowInterface $flow)
    {
        $return = false;

        if (!$this->workDir->isExists()) {
            $this->workDir->create();
        }

        $archive = $flow->get('downloaded_file');
        if (empty($archive) || !($archive instanceof FileInterface)) {
            throw new Exception("downloaded_file flow's parameter must be set for UnpackArchive job and implements FileInterface");
        } elseif (!$archive->isExists()) {
            throw new Exception($archive->getPathname() . " doesn't exist");
        }

        try {
            $this->unpacker->unpack(
                $archive->getPathname(),
                $this->workDir->getPathname()
            );
            $archive->delete();
            $flow->set('downloaded_file', null);
            $return = true;
        } catch (\marvin255\fias\utils\unpacker\Exception $e) {
            $archive->delete();
            $this->workDir->delete();
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $return;
    }
}
