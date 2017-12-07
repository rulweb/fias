<?php

namespace marvin255\fias\job;

use marvin255\fias\pipe\FlowInterface;
use marvin255\fias\reader\ReaderInterface;
use marvin255\fias\processor\ProcessorInterface;
use marvin255\fias\utils\filesystem\DirectoryInterface;
use marvin255\fias\utils\filesystem\FileInterface;
use marvin255\fias\utils\filesystem\FilterInterface;
use InvalidArgumentException;

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
     * Массив с фильтрами для поиска файлов для данной задачи.
     *
     * @var array
     */
    protected $filters = null;

    /**
     * Конструктор.
     *
     * @param \marvin255\fias\utils\filesystem\DirectoryInterface $workDir
     * @param \marvin255\fias\reader\ReaderInterface              $reader    Объект для чтения данных из файла
     * @param \marvin255\fias\processor\ProcessorInterface        $processor Объект, который отвечает за обработку полученных данных
     * @param array                                               $filters   Массив с фильтрами для поиска файлов для данной задачи
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(DirectoryInterface $workDir, ReaderInterface $reader, ProcessorInterface $processor, array $filters = [])
    {
        $this->workDir = $workDir;
        $this->reader = $reader;
        $this->processor = $processor;
        foreach ($filters as $fkey => $filter) {
            if ($filter instanceof FilterInterface) {
                continue;
            }
            throw new InvalidArgumentException("Filter with key {$fkey} must implements FilterInterface");
        }
        $this->filters = $filters;
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
            if (!$this->checkFileForLoad($item)) {
                continue;
            }
            $this->reader->open($item->getPathname());
            foreach ($this->reader as $data) {
                $this->processor->process($data);
            }
            $this->reader->close();
        }
        $this->processor->close();

        return $return;
    }

    /**
     * Проверяет нужно ли обабатвать данный файл или нет.
     *
     * @param mixed $item
     *
     * @return bool
     */
    protected function checkFileForLoad($item)
    {
        if ($return = $item instanceof FileInterface) {
            foreach ($this->filters as $filter) {
                if ($filter->check($item)) {
                    continue;
                }
                $return = false;
                break;
            }
        }

        return $return;
    }
}
