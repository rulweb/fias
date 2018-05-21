<?php

namespace marvin255\fias\tests\service\unpacker;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\service\unpacker\Rar;
use Mockery;
use RuntimeException;

class RarTest extends BaseTestCase
{
    public function testUnpack()
    {
        $pathToArchive = $this->faker()->unique()->word;
        $pathToUnpack = $this->faker()->unique()->word;

        $rarEntry1 = Mockery::mock('\RarEntry');
        $rarEntry1->shouldReceive('extract')->once()->with($pathToUnpack)->andReturn(true);
        $rarEntry2 = Mockery::mock('\RarEntry');
        $rarEntry2->shouldReceive('extract')->once()->with($pathToUnpack)->andReturn(true);

        $rarArchive = Mockery::mock('\RarArchive');
        $rarArchive->shouldReceive('close')->once();
        $rarArchive->shouldReceive('getEntries')->once()->andReturn([$rarEntry1, $rarEntry2]);

        $unpacker = Mockery::mock(Rar::class . '[callRarArchiveOpen]')->shouldAllowMockingProtectedMethods();
        $unpacker->shouldReceive('callRarArchiveOpen')->with($pathToArchive)->andReturn($rarArchive);
        $unpacker->unpack($pathToArchive, $pathToUnpack);
    }

    public function testOpenException()
    {
        $pathToArchive = $this->faker()->unique()->word;
        $pathToUnpack = $this->faker()->unique()->word;

        $unpacker = Mockery::mock(Rar::class . '[callRarArchiveOpen]')->shouldAllowMockingProtectedMethods();
        $unpacker->shouldReceive('callRarArchiveOpen')->with($pathToArchive)->andReturn(false);

        $this->expectException(RuntimeException::class);
        $unpacker->unpack($pathToArchive, $pathToUnpack);
    }

    public function testGetEntriesException()
    {
        $pathToArchive = $this->faker()->unique()->word;
        $pathToUnpack = $this->faker()->unique()->word;

        $rarArchive = Mockery::mock('\RarArchive');
        $rarArchive->shouldReceive('close')->once();
        $rarArchive->shouldReceive('getEntries')->once()->andReturn(false);

        $unpacker = Mockery::mock(Rar::class . '[callRarArchiveOpen]')->shouldAllowMockingProtectedMethods();
        $unpacker->shouldReceive('callRarArchiveOpen')->with($pathToArchive)->andReturn($rarArchive);

        $this->expectException(RuntimeException::class);
        $unpacker->unpack($pathToArchive, $pathToUnpack);
    }

    public function testExtractException()
    {
        $pathToArchive = $this->faker()->unique()->word;
        $pathToUnpack = $this->faker()->unique()->word;

        $rarEntry1 = Mockery::mock('\RarEntry');
        $rarEntry1->shouldReceive('extract')->once()->with($pathToUnpack)->andReturn(true);
        $rarEntry1->shouldReceive('getName')->andReturn('entry');
        $rarEntry2 = Mockery::mock('\RarEntry');
        $rarEntry2->shouldReceive('extract')->once()->with($pathToUnpack)->andReturn(false);
        $rarEntry2->shouldReceive('getName')->andReturn('entry');

        $rarArchive = Mockery::mock('\RarArchive');
        $rarArchive->shouldReceive('close')->once();
        $rarArchive->shouldReceive('getEntries')->once()->andReturn([$rarEntry1, $rarEntry2]);

        $unpacker = Mockery::mock(Rar::class . '[callRarArchiveOpen]')->shouldAllowMockingProtectedMethods();
        $unpacker->shouldReceive('callRarArchiveOpen')->with($pathToArchive)->andReturn($rarArchive);

        $this->expectException(RuntimeException::class);
        $unpacker->unpack($pathToArchive, $pathToUnpack);
    }
}
