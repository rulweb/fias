<?php

namespace marvin255\fias\tests\job;

class DownloadTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $basename = 'basename_' . mt_rand() . '.tmp';
        $url = 'http://test.test/url_' . mt_rand() . '/' . $basename;
        $pathName = 'path_name_' . mt_rand() . '/' . $basename;

        $file = $this->getMockBuilder('\marvin255\fias\utils\filesystem\FileInterface')
            ->getMock();
        $file->method('getPathname')->will($this->returnValue($pathName));

        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();
        $dir->method('isExists')->will($this->returnValue(false));
        $dir->expects($this->once())->method('create');
        $dir->expects($this->once())
            ->method('createChildFile')
            ->with($this->equalTo($basename))
            ->will($this->returnValue($file));

        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')
            ->getMock();
        $flow->expects($this->once())
            ->method('set')
            ->with($this->equalTo('downloaded_file'), $this->equalTo($file));
        $flow->method('get')
            ->with($this->equalTo('download_url'))
            ->will($this->returnValue($url));

        $transport = $this->getMockBuilder('\marvin255\fias\utils\transport\TransportInterface')
            ->getMock();
        $transport->expects($this->once())
            ->method('download')
            ->with($this->equalTo($url), $this->equalTo($pathName));

        $job = new \marvin255\fias\job\Download($dir, $transport);

        $this->assertSame(
            true,
            $job->run($flow)
        );
    }

    public function testRunNoDownloadUrlException()
    {
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();
        $transport = $this->getMockBuilder('\marvin255\fias\utils\transport\TransportInterface')
            ->getMock();
        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')
            ->getMock();
        $flow->method('get')->will($this->returnValue(null));

        $job = new \marvin255\fias\job\Download($dir, $transport);
        $this->setExpectedException('\marvin255\fias\job\Exception');
        $job->run($flow);
    }

    public function testRunTransportException()
    {
        $exception = 'exception_' . mt_rand();
        $basename = 'basename_' . mt_rand() . '.tmp';
        $url = 'http://test.test/url_' . mt_rand() . '/' . $basename;
        $pathName = 'path_name_' . mt_rand() . '/' . $basename;

        $file = $this->getMockBuilder('\marvin255\fias\utils\filesystem\FileInterface')
            ->getMock();
        $file->method('getPathname')->will($this->returnValue($pathName));
        $file->expects($this->once())->method('delete');

        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();
        $dir->method('isExists')->will($this->returnValue(false));
        $dir->expects($this->once())->method('create');
        $dir->expects($this->once())
            ->method('createChildFile')
            ->with($this->equalTo($basename))
            ->will($this->returnValue($file));

        $flow = $this->getMockBuilder('\marvin255\fias\pipe\FlowInterface')
            ->getMock();
        $flow->method('get')
            ->with($this->equalTo('download_url'))
            ->will($this->returnValue($url));

        $transport = $this->getMockBuilder('\marvin255\fias\utils\transport\TransportInterface')
            ->getMock();
        $transport->expects($this->once())
            ->method('download')
            ->will($this->throwException(
                new \marvin255\fias\utils\transport\Exception($exception)
            ));

        $job = new \marvin255\fias\job\Download($dir, $transport);

        $this->setExpectedException('\marvin255\fias\job\Exception', $exception);
        $job->run($flow);
    }
}
