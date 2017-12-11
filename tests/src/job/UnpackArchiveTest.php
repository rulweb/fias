<?php

namespace marvin255\fias\tests\job;

class UnpackArchiveTest extends \marvin255\fias\tests\BaseTestCase
{
    public function testRun()
    {
        $filePathName = 'path_name_' . mt_rand() . '/basename_' . mt_rand() . '.tmp';
        $dirPathName = 'path_name_' . mt_rand();

        $file = $this->getMockBuilder('\marvin255\fias\utils\filesystem\FileInterface')
            ->getMock();
        $file->method('isExists')->will($this->returnValue(true));
        $file->method('getPathname')->will($this->returnValue($filePathName));

        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();
        $dir->method('isExists')->will($this->returnValue(false));
        $dir->expects($this->once())->method('create');
        $dir->method('getPathname')->will($this->returnValue($dirPathName));

        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')
            ->getMock();
        $flow->method('get')
            ->with($this->equalTo('downloaded_file'))
            ->will($this->returnValue($file));
        $flow->expects($this->once())
            ->method('set')
            ->with($this->equalTo('downloaded_file'), $this->equalTo(null));

        $unpacker = $this->getMockBuilder('\marvin255\fias\utils\unpacker\UnpackerInterface')
            ->getMock();
        $unpacker->expects($this->once())
            ->method('unpack')
            ->with($this->equalTo($filePathName), $this->equalTo($dirPathName));

        $job = new \marvin255\fias\job\UnpackArchive($dir, $unpacker);

        $this->assertSame(
            true,
            $job->run($flow)
        );
    }

    public function testRunNoDownloadedFileException()
    {
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();
        $unpacker = $this->getMockBuilder('\marvin255\fias\utils\unpacker\UnpackerInterface')
            ->getMock();
        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')
            ->getMock();
        $flow->method('get')->will($this->returnValue(123));

        $job = new \marvin255\fias\job\UnpackArchive($dir, $unpacker);
        $this->setExpectedException('\marvin255\fias\job\Exception', 'downloaded_file');
        $job->run($flow);
    }

    public function testRunUnexistedFileException()
    {
        $filePathName = 'path_name_' . mt_rand() . '/basename_' . mt_rand() . '.tmp';

        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();
        $unpacker = $this->getMockBuilder('\marvin255\fias\utils\unpacker\UnpackerInterface')
            ->getMock();

        $file = $this->getMockBuilder('\marvin255\fias\utils\filesystem\FileInterface')
            ->getMock();
        $file->method('isExists')->will($this->returnValue(false));
        $file->method('getPathname')->will($this->returnValue($filePathName));

        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')
            ->getMock();
        $flow->method('get')
            ->with($this->equalTo('downloaded_file'))
            ->will($this->returnValue($file));

        $job = new \marvin255\fias\job\UnpackArchive($dir, $unpacker);
        $this->setExpectedException('\marvin255\fias\job\Exception', $filePathName);
        $job->run($flow);
    }

    public function testRunUnpackerException()
    {
        $exception = 'exception_' . mt_rand();
        $filePathName = 'path_name_' . mt_rand() . '/basename_' . mt_rand() . '.tmp';
        $dirPathName = 'path_name_' . mt_rand();

        $file = $this->getMockBuilder('\marvin255\fias\utils\filesystem\FileInterface')
            ->getMock();
        $file->method('isExists')->will($this->returnValue(true));
        $file->method('getPathname')->will($this->returnValue($filePathName));
        $file->expects($this->once())->method('delete');

        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();
        $dir->method('isExists')->will($this->returnValue(false));
        $dir->expects($this->once())->method('create');
        $dir->expects($this->once())->method('delete');
        $dir->method('getPathname')->will($this->returnValue($dirPathName));

        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')
            ->getMock();
        $flow->method('get')
            ->with($this->equalTo('downloaded_file'))
            ->will($this->returnValue($file));

        $unpacker = $this->getMockBuilder('\marvin255\fias\utils\unpacker\UnpackerInterface')
            ->getMock();
        $unpacker->expects($this->once())
            ->method('unpack')
            ->will($this->throwException(
                new \marvin255\fias\utils\unpacker\Exception($exception)
            ));

        $job = new \marvin255\fias\job\UnpackArchive($dir, $unpacker);

        $this->setExpectedException('\marvin255\fias\job\Exception', $exception);
        $job->run($flow);
    }
}
