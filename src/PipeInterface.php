<?php

namespace marvin255\fias;

/**
 * Интерфейс для объекта, который хранит в себе очередь задач и позволяет
 * запустить их на исполнение.
 */
interface PipeInterface
{
    /**
     * Добавляет задачу в очередь.
     *
     * @param \marvin255\fias\TaskInterface $task
     *
     * @return self
     */
    public function pipeTask(TaskInterface $task): PipeInterface;

    /**
     * Возвращает список задач в очереди.
     *
     * @return array
     */
    public function getTasks(): array;

    /**
     * Очищает очередь задач.
     *
     * @return self
     */
    public function clearTasks(): PipeInterface;

    /**
     * Задает задачу, которая будет запускаться после каждого завершения очереди.
     *
     * @param \marvin255\fias\TaskInterface $task
     *
     * @return self
     */
    public function setCleanupTask(TaskInterface $task): PipeInterface;

    /**
     * Возвращает задачу, которая будет запускаться после каждого завершения очереди.
     *
     * @return \marvin255\fias\TaskInterface|null
     */
    public function getCleanupTask();

    /**
     * Запускает все задачи в очереди на исполнение.
     *
     * @return bool
     */
    public function run(): bool;
}
