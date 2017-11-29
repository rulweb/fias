<?php

namespace marvin255\fias\job;

use marvin255\fias\pipe\FlowInterface;
use marvin255\fias\utils\filesystem\DirectoryInterface;
use marvin255\fias\utils\transport\TransportInterface;

/**
 * Задача, которая по ссылке на локальный диск.
 */
class Download implements JobInterface
{
    /**
     * Рабочий каталог, в который будет скопирован файл.
     *
     * @var \marvin255\fias\utils\filesystem\DirectoryInterface
     */
    protected $workDir = null;
    /**
     * Объект, который отвечает за загрузку файла на локальный диск.
     *
     * @var \marvin255\fias\utils\transport\TransportInterface
     */
    protected $transport = null;

    /**
     * Конструктор.
     * Задает рабочий каталог, в который будет скопирован файл и объект,
     * который будет непосредственно загружать файл.
     *
     * @param \marvin255\fias\utils\filesystem\DirectoryInterface $workDir
     * @param \marvin255\fias\utils\transport\TransportInterface  $transport
     */
    public function __construct(DirectoryInterface $workDir, TransportInterface $transport)
    {
        $this->workDir = $workDir;
        $this->transport = $transport;
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

        $url = $flow->get('download_url');
        if (empty($url)) {
            throw new Exception("download_url flow's parameter must be set for Download job");
        }
        $file = $this->workDir->createChildFile(
            pathinfo($url, PATHINFO_BASENAME)
        );

        try {
            $this->transport->download($url, $file->getPathname());
            $flow->set('downloaded_file', $file);
            $return = true;
        } catch (\marvin255\fias\utils\transport\Exception $e) {
            $file->delete();
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $return;
    }
}
