<?php

namespace marvin255\fias\tests\job;

use marvin255\fias\job\ReadAndProcess;

class ReadAndProcessTest extends \PHPUnit_Framework_TestCase
{
    public function testRunUnexistedDirException()
    {
        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')
            ->getMock();

        $dirPath = 'dirPath_' . mt_rand();
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();
        $dir->method('isExists')->will($this->returnValue(false));
        $dir->method('getPathname')->will($this->returnValue($dirPath));

        $reader = $this->getMockBuilder('\marvin255\fias\reader\ReaderInterface')
            ->getMock();

        $processor = $this->getMockBuilder('\marvin255\fias\processor\ProcessorInterface')
            ->getMock();

        $job = new ReadAndProcess($dir, $reader, $processor);
        $this->setExpectedException('\marvin255\fias\job\Exception', $dirPath);
        $job->run($flow);
    }

    public function testRun()
    {
        $fileName = 'fileName_' . mt_rand();
        $file = $this->getMockBuilder('\marvin255\fias\utils\filesystem\FileInterface')
            ->getMock();
        $file->method('getPathname')->will($this->returnValue($fileName));

        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();
        $dir->method('isExists')->will($this->returnValue(true));

        $count = 0;
        $dir->method('rewind')->will($this->returnCallback(function () use (&$count) {
            $count = 0;
        }));
        $dir->method('next')->will($this->returnCallback(function () use (&$count) {
            ++$count;
        }));
        $dir->method('valid')->will($this->returnCallback(function () use (&$count) {
            return $count < 1;
        }));
        $dir->method('key')->will($this->returnCallback(function () use (&$count) {
            return $count;
        }));
        $dir->method('current')->will($this->returnCallback(function () use (&$count, $file) {
            return $count == 0 ? $file : null;
        }));

        $data = ['data_' . mt_rand()];
        $reader = $this->getMockBuilder('\marvin255\fias\reader\ReaderInterface')
            ->getMock();
        $reader->expects($this->once())->method('open')->with($this->equalTo($fileName));
        $reader->expects($this->once())->method('close');
        $count = 0;
        $reader->method('rewind')->will($this->returnCallback(function () use (&$count) {
            $count = 0;
        }));
        $reader->method('next')->will($this->returnCallback(function () use (&$count) {
            ++$count;
        }));
        $reader->method('valid')->will($this->returnCallback(function () use (&$count) {
            return $count < 1;
        }));
        $reader->method('key')->will($this->returnCallback(function () use (&$count) {
            return $count;
        }));
        $reader->method('current')->will($this->returnCallback(function () use (&$count, $data) {
            return $count == 0 ? $data : null;
        }));

        $processor = $this->getMockBuilder('\marvin255\fias\processor\ProcessorInterface')
            ->getMock();
        $processor->expects($this->once())
            ->method('process')
            ->with($this->equalTo($data));
        $processor->expects($this->once())->method('open');
        $processor->expects($this->once())->method('close');

        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')
            ->getMock();

        $job = new ReadAndProcess($dir, $reader, $processor);

        $this->assertSame(
            true,
            $job->run($flow)
        );
    }
}
