<?php

namespace marvin255\fias\pipe;

use marvin255\fias\job\JobInterface;

/**
 * Интерфейс для объекта очереди.
 *
 * Наполнение базы данных из файлов ФИАС требует выполнения нескольких
 * последовательных операций. Данный объект является контроллером для выполнения
 * этих операций. Для него можно зарегистрировать несколько задач и запустить
 * их на исполнение через метод run.
 */
interface PipeInterface
{
    /**
     * Служит для запуска очереди задач. Прнимает на вход объект,
     * который хранит и передает результаты обработки задач далее. Возвращает
     * bool того удалось выполнить все работы или нет.
     *
     * @param \marvin255\fias\pipe\FlowInterface $flow
     *
     * @return bool
     */
    public function run(FlowInterface $flow);

    /**
     * Регистрация новой задачи для обработки. Задачи регистрируеются друг
     * за другом в том же порядке, в котором для них вызывался addJob.
     *
     * @param \marvin255\fias\job\JobInterface $job
     *
     * @return \marvin255\fias\pipe\PipeInterface
     */
    public function addJob(JobInterface $job);

    /**
     * Возвращает список всех зарегистрироанных в контроллере задач.
     *
     * @return array
     */
    public function getJobs();
}
