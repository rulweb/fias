<?php

namespace marvin255\fias\job;

use marvin255\fias\pipe\FlowInterface;
use marvin255\fias\reader\ReaderInterface;
use marvin255\fias\processor\ProcessorInterface;
use marvin255\fias\utils\filesystem\DirectoryInterface;
use marvin255\fias\utils\filesystem\FileInterface;

/**
 * Задача, которая читает данные из файла и передает их на обработку.
 */
class ReadAndProcess implements JobInterface
{
    /**
     * Рабочий каталог, в котором расположены файлы для обработки.
     *
     * @var \marvin255\fias\utils\filesystem\DirectoryInterface
     */
    protected $workDir = null;
    /**
     * Объект для чтения данных из файла.
     *
     * @var \marvin255\fias\reader\ReaderInterface
     */
    protected $reader = null;
    /**
     * Объект, который отвечает за обработку полученных данных.
     *
     * @var \marvin255\fias\processor\ProcessorInterface
     */
    protected $processor = null;

    /**
     * Конструктор.
     *
     * @param \marvin255\fias\utils\filesystem\DirectoryInterface $workDir
     * @param \marvin255\fias\reader\ReaderInterface              $reader    Объект для чтения данных из файла
     * @param \marvin255\fias\processor\ProcessorInterface        $processor Объект, который отвечает за обработку полученных данных
     */
    public function __construct(DirectoryInterface $workDir, ReaderInterface $reader, ProcessorInterface $processor)
    {
        $this->workDir = $workDir;
        $this->reader = $reader;
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\fias\job\Exception
     */
    public function run(FlowInterface $flow)
    {
        $return = true;

        if (!$this->workDir->isExists()) {
            throw new Exception(
                'Directory ' . $this->workDir->getPathname() . ' doesn\'t exist'
            );
        }

        $this->processor->open();
        foreach ($this->workDir as $item) {
            if ($item instanceof FileInterface) {
                $this->reader->open($item->getPathname());
                foreach ($this->reader as $data) {
                    $this->processor->process($data);
                }
                $this->reader->close();
            }
        }
        $this->processor->close();

        return $return;
    }
}
