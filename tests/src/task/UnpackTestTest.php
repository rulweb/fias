<?php

namespace marvin255\fias\tests\task;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\task\Unpack;
use marvin255\fias\service\filesystem\DirectoryInterface;
use marvin255\fias\service\filesystem\FileInterface;
use marvin255\fias\service\unpacker\UnpackerInterface;
use marvin255\fias\service\bag\BagInterface;
use Mockery;

class UnpackTest extends BaseTestCase
{
    public function testRun()
    {
        $archiveName = 'file.rar';
        $dirPath = '/' . $this->faker()->unique()->word . '/' . $this->faker()->unique()->word;

        $paramsBag = Mockery::mock(BagInterface::class);
        $paramsBag->shouldReceive('get')->with('archive.name')->andReturn($archiveName);

        $file = Mockery::mock(FileInterface::class);
        $file->shouldReceive('delete')->once();
        $file->shouldReceive('getPathname')->andReturn($dirPath . '/' . $archiveName);
        $file->shouldReceive('isExists')->andReturn(true);

        $directory = Mockery::mock(DirectoryInterface::class);
        $directory->shouldReceive('getPathname')->andReturn($dirPath);
        $directory->shouldReceive('createChildFile')->with($archiveName)->andReturn($file);

        $unpacker = Mockery::mock(UnpackerInterface::class);
        $unpacker->shouldReceive('unpack')->once()->with($dirPath . '/' . $archiveName, $dirPath);

        $unrarTask = new Unpack;
        $unrarTask->setUnpacker($unpacker);
        $unrarTask->setWorkDirectory($directory);
        $unrarTask->setParamsBag($paramsBag);

        $this->assertSame(true, $unrarTask->run());
    }

    public function testGetDescription()
    {
        $unrarTask = new Unpack;

        $this->assertNotEmpty($unrarTask->getDescription());
    }
}
