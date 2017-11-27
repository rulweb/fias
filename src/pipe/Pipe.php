<?php

namespace marvin255\fias\pipe;

use marvin255\fias\job\JobInterface;
use UnexpectedValueException;

/**
 * Объект очереди.
 *
 * Наполнение базы данных из файлов ФИАС требует выполнения нескольких
 * последовательных операций. Данный объект является контроллером для выполнения
 * этих операций. Для него можно зарегистрировать несколько задач и запустить
 * их на исполнение через метод run.
 */
class Pipe implements PipeInterface
{
    /**
     * Массив с текущими объектами задач.
     *
     * @var array
     */
    protected $jobs = [];

    /**
     * {@inheritdoc}
     *
     * @throws \UnexpectedValueException
     */
    public function run(FlowInterface $flow)
    {
        $return = true;

        $jobs = $this->getJobs();
        if (empty($jobs)) {
            throw new UnexpectedValueException('Jobs list is empty');
        }

        foreach ($jobs as $job) {
            $return = $job->run($flow);
            if (!$return) {
                break;
            }
        }

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function addJob(JobInterface $job)
    {
        $this->jobs[] = $job;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getJobs()
    {
        return $this->jobs;
    }
}
