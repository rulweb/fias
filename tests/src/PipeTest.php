<?php

namespace marvin255\fias\tests;

use marvin255\fias\Pipe;
use marvin255\fias\TaskInterface;
use marvin255\fias\ServiceLocatorInterface;
use marvin255\fias\service\bag\BagInterface;
use Psr\Log\LoggerInterface;
use Mockery;
use Exception;

class PipeTest extends BaseTestCase
{
    public function testPipeTask()
    {
        $serviceLocator = Mockery::mock(ServiceLocatorInterface::class);
        $task = Mockery::mock(TaskInterface::class);

        $taskBag = new Pipe($serviceLocator);
        $taskBag->pipeTask($task);
        $tasks = $taskBag->getTasks();

        $this->assertCount(1, $tasks);
        $this->assertSame($task, $tasks[0]);
    }

    public function testClear()
    {
        $serviceLocator = Mockery::mock(ServiceLocatorInterface::class);
        $task1 = Mockery::mock(TaskInterface::class);
        $task2 = Mockery::mock(TaskInterface::class);

        $taskBag = new Pipe($serviceLocator);
        $taskBag->pipeTask($task1);
        $taskBag->clearTasks();
        $taskBag->pipeTask($task2);
        $tasks = $taskBag->getTasks();

        $this->assertCount(1, $tasks);
        $this->assertSame($task2, $tasks[0]);
    }

    public function testSetCleanupTask()
    {
        $serviceLocator = Mockery::mock(ServiceLocatorInterface::class);
        $task = Mockery::mock(TaskInterface::class);

        $taskBag = new Pipe($serviceLocator);

        $this->assertSame(null, $taskBag->getCleanupTask());
        $taskBag->setCleanupTask($task);
        $this->assertSame($task, $taskBag->getCleanupTask());
    }

    public function testRun()
    {
        $bag = Mockery::mock(BagInterface::class);

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info')->atLeast()->times(1);

        $serviceLocator = Mockery::mock(ServiceLocatorInterface::class);
        $serviceLocator->shouldReceive('resolve')->once()->with(BagInterface::class)->andReturn($bag);
        $serviceLocator->shouldReceive('resolve')->with(LoggerInterface::class)->andReturn($logger);
        $serviceLocator->shouldReceive('resolve')->andReturn(null);

        $task1 = new BaseTaskMock;
        $task2 = Mockery::mock(TaskInterface::class);
        $task2->shouldReceive('run')->once()->andReturn(false);
        $task2->shouldReceive('getDescription')->andReturn('task2');
        $task3 = Mockery::mock(TaskInterface::class);
        $task3->shouldReceive('run')->never();
        $task3->shouldReceive('getDescription')->andReturn('task3');

        $cleanupTask = Mockery::mock(TaskInterface::class);
        $cleanupTask->shouldReceive('run')->once();

        $pipe = new Pipe($serviceLocator);
        $pipe->pipeTask($task1);
        $pipe->pipeTask($task2);
        $pipe->pipeTask($task3);
        $pipe->setCleanupTask($cleanupTask);
        $pipe->run();
    }

    public function testRunException()
    {
        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info')->andReturn(null);
        $logger->shouldReceive('error')->once()->andReturn(null);

        $serviceLocator = Mockery::mock(ServiceLocatorInterface::class);
        $serviceLocator->shouldReceive('resolve')->with(LoggerInterface::class)->andReturn($logger);
        $serviceLocator->shouldReceive('resolve')->andReturn(null);

        $task = Mockery::mock(TaskInterface::class);
        $task->shouldReceive('run')->once()->andThrow(new Exception);
        $task->shouldReceive('getDescription')->andReturn('task');

        $cleanupTask = Mockery::mock(TaskInterface::class);
        $cleanupTask->shouldReceive('run')->once();

        $pipe = new Pipe($serviceLocator);
        $pipe->pipeTask($task);
        $pipe->setCleanupTask($cleanupTask);

        $this->expectException(Exception::class);
        $pipe->run();
    }
}
