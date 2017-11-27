<?php

namespace marvin255\fias\tests\pipe;

class PipeTest extends \PHPUnit_Framework_TestCase
{
    public function testAddJob()
    {
        $pipe = new \marvin255\fias\pipe\Pipe;

        $job1 = $this->getMockBuilder('\marvin255\fias\job\JobInterface')->getMock();

        $this->assertSame(
            $pipe,
            $pipe->addJob($job1)
        );
        $this->assertSame(
            [$job1],
            $pipe->getJobs()
        );
    }

    public function testRun()
    {
        $pipe = new \marvin255\fias\pipe\Pipe;

        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')->getMock();

        $job1 = $this->getMockBuilder('\marvin255\fias\job\JobInterface')->getMock();
        $job1->expects($this->once())
            ->method('run')
            ->with($this->equalTo($flow))
            ->will($this->returnValue(true));
        $pipe->addJob($job1);

        $job2 = $this->getMockBuilder('\marvin255\fias\job\JobInterface')->getMock();
        $job2->expects($this->once())
            ->method('run')
            ->with($this->equalTo($flow))
            ->will($this->returnValue(true));
        $pipe->addJob($job2);

        $this->assertSame(
            true,
            $pipe->run($flow)
        );
    }

    public function testFailedRun()
    {
        $pipe = new \marvin255\fias\pipe\Pipe;

        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')->getMock();

        $job1 = $this->getMockBuilder('\marvin255\fias\job\JobInterface')->getMock();
        $job1->expects($this->once())
            ->method('run')
            ->with($this->equalTo($flow))
            ->will($this->returnValue(false));
        $pipe->addJob($job1);

        $job2 = $this->getMockBuilder('\marvin255\fias\job\JobInterface')->getMock();
        $job2->expects($this->never())->method('run');
        $pipe->addJob($job2);

        $this->assertSame(
            false,
            $pipe->run($flow)
        );
    }

    public function testRunEmptyJobsListException()
    {
        $pipe = new \marvin255\fias\pipe\Pipe;
        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')->getMock();
        $this->setExpectedException('\UnexpectedValueException');
        $pipe->run($flow);
    }
}
