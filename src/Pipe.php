<?php

declare(strict_types=1);

namespace marvin255\fias;

use Psr\Log\LoggerInterface;
use Throwable;
use ReflectionClass;
use ReflectionMethod;

/**
 * Объект, который хранит в себе очередь задач и позволяет
 * запустить их на исполнение.
 */
class Pipe implements PipeInterface
{
    /**
     * @var \marvin255\fias\ServiceLocatorInterface
     */
    protected $serviceLocator;
    /**
     * @var array
     */
    protected $tasks = [];
    /**
     * @var \marvin255\fias\TaskInterface
     */
    protected $cleanupTask;
    /**
     * @var float
     */
    protected $pipeStart;

    /**
     * Задает service locator.
     *
     * @param \marvin255\fias\ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @inheritdoc
     */
    public function pipeTask(TaskInterface $task): PipeInterface
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Возвращает список задач в очереди.
     *
     * @return array
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }

    /**
     * @inheritdoc
     */
    public function clearTasks(): PipeInterface
    {
        $this->tasks = [];

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setCleanupTask(TaskInterface $task): PipeInterface
    {
        $this->cleanupTask = $task;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCleanupTask()
    {
        return $this->cleanupTask;
    }

    /**
     * @inheritdoc
     * @throws Throwable
     */
    public function run(): bool
    {
        $runResult = true;
        $this->startTimeMarker();

        foreach ($this->tasks as $task) {
            $this->info($task->getDescription());

            try {
                $taskResult = $this->resolveTaskServices($task)->run();
            } catch (Throwable $e) {
                $this->error($e);
                $this->cleanup();
                throw $e;
            }

            if ($taskResult !== true) {
                $this->info('Task ' . get_class($task) . ' stopped piping');
                $runResult = false;
                break;
            }
        }

        $this->cleanup();
        $this->info('Pipe completed');

        return $runResult;
    }

    /**
     * Логгирует информацию.
     *
     * @param string $message
     *
     * @return self
     */
    protected function info(string $message): PipeInterface
    {
        $logger = $this->serviceLocator->resolve(LoggerInterface::class);
        if ($logger) {
            $message = $this->getTimeMarker() . ' - ' . $message;
            $logger->info($message);
        }

        return $this;
    }

    /**
     * Логгирует ошибку.
     *
     * @param \Throwable $e
     *
     * @return self
     */
    protected function error(Throwable $e): PipeInterface
    {
        $logger = $this->serviceLocator->resolve(LoggerInterface::class);
        if ($logger) {
            $message = $this->getTimeMarker() . ' - ' . $e->getMessage();
            $logger->error($message);
        }

        return $this;
    }

    /**
     * Запускает задачу, которая очищает все временные данные после выполнения очереди.
     *
     * @return self
     */
    protected function cleanup(): PipeInterface
    {
        if ($this->cleanupTask) {
            $taskName = get_class($this->cleanupTask);
            $this->resolveTaskServices($this->cleanupTask)->run();
            $this->info("Task {$taskName} cleaned up temp data");
        }

        return $this;
    }

    /**
     * Задает время начала отсчета для очереди.
     *
     * @return self
     */
    protected function startTimeMarker(): PipeInterface
    {
        $this->pipeStart = microtime(true);

        return $this;
    }

    /**
     * Возвращает разницы во времени между текущей отметкой и началом работы очереди.
     *
     * @return string
     */
    protected function getTimeMarker(): string
    {
        $return = '';
        if ($this->pipeStart) {
            $time = round(microtime(true) - $this->pipeStart);

            $hours = floor($time / (60 * 60));
            $time -= $hours * 60 * 60;
            $return .= $hours . 'h';

            $minutes = floor($time / 60);
            $time -= $minutes * 60;
            $return .= ' ' . $minutes . 'm';

            $return .= ' ' . $time . 's';
        }

        return trim($return);
    }

    /**
     * С помощью рефлексии находит сеттеры в объекте задачи и инжектит
     * соответствующие сервисы из Service Locator.
     *
     * @param \marvin255\fias\TaskInterface $task
     *
     * @return \marvin255\fias\TaskInterface
     *
     * @throws \ReflectionException
     */
    protected function resolveTaskServices(TaskInterface $task): TaskInterface
    {
        $reflection = new ReflectionClass($task);
        $methods = $this->getMethodsFromReflection($reflection);
        foreach ($methods as $method) {
            $toResolve = $method->getParameters()[0]->getClass()->getName();
            $resolved = $this->serviceLocator->resolve($toResolve);
            if ($resolved) {
                $task->{$method->getName()}($resolved);
            }
        }

        return $task;
    }

    /**
     * Возвращает список публичных сеттеров для класса из рефлексии.
     *
     * @param \ReflectionClass $reflection
     *
     * @return array
     */
    protected function getMethodsFromReflection(ReflectionClass $reflection): array
    {
        $return = [];
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $parameters = $method->getParameters();
            $isSetter = strpos($method->getName(), 'set') === 0;
            $hasParameter = count($parameters) === 1 && $parameters[0]->getClass();
            if ($isSetter && $hasParameter) {
                $return[] = $method;
            }
        }

        return $return;
    }
}
