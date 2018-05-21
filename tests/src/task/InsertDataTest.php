<?php

namespace marvin255\fias\tests\task;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\task\InsertData;
use marvin255\fias\service\filesystem\DirectoryInterface;
use marvin255\fias\service\filesystem\FileInterface;
use marvin255\fias\service\xml\ReaderInterface;
use marvin255\fias\service\database\DatabaseInterface;
use Mockery;

class InsertDataTest extends BaseTestCase
{
    public function testRun()
    {
        $tableName = 'table';
        $filePattern = 'file_pattern';
        $pathToFile = '/file_pattern.xml';
        $xmlNode = '/root/item';
        $xmlSelect = ['row_in_db' => 'row_in_xml'];
        $readerData = [
            ['row_in_db' => $this->faker()->unique()->word],
            ['row_in_db' => $this->faker()->unique()->word],
            ['row_in_db' => $this->faker()->unique()->word],
        ];

        $file = Mockery::mock(FileInterface::class);
        $file->shouldReceive('getPathname')->andReturn($pathToFile);

        $dir = Mockery::mock(DirectoryInterface::class);
        $dir->shouldReceive('findFilesByPattern')->with($filePattern)->andReturn([$file]);

        $readerCounter = 0;
        $reader = Mockery::mock(ReaderInterface::class);
        $reader->shouldReceive('open')->once()->with($pathToFile, $xmlNode, $xmlSelect);
        $reader->shouldReceive('close')->once();
        $reader->shouldReceive('rewind')->andReturnUsing(function () use (&$readerCounter) {
            $readerCounter = 0;
        });
        $reader->shouldReceive('current')->andReturnUsing(function () use ($readerData, &$readerCounter) {
            return $readerData[$readerCounter];
        });
        $reader->shouldReceive('key')->andReturnUsing(function () use (&$readerCounter) {
            return $readerCounter;
        });
        $reader->shouldReceive('next')->andReturnUsing(function () use (&$readerCounter) {
            ++$readerCounter;
        });
        $reader->shouldReceive('valid')->andReturnUsing(function () use ($readerData, &$readerCounter) {
            return $readerCounter < count($readerData);
        });

        $database = Mockery::mock(DatabaseInterface::class);
        $database->shouldReceive('truncateTable')->once()->with($tableName);
        $database->shouldReceive('bulkInsert')->once()->with($tableName, [$readerData[0], $readerData[1]]);
        $database->shouldReceive('bulkInsert')->once()->with($tableName, [$readerData[2]]);

        $insertTask = new InsertData($tableName, $filePattern, $xmlNode, $xmlSelect, 2);
        $insertTask->setXmlReader($reader);
        $insertTask->setWorkDirectory($dir);
        $insertTask->setDatabase($database);

        $this->assertSame(true, $insertTask->run());
    }

    public function testGetDescription()
    {
        $tableName = $this->faker()->unique()->word;
        $filePattern = 'file_pattern';
        $pathToFile = '/file_pattern.xml';
        $xmlNode = '/root/item';
        $xmlSelect = ['row_in_db' => 'row_in_xml'];

        $insertTask = new InsertData($tableName, $filePattern, $xmlNode, $xmlSelect, 2);

        $this->assertContains($tableName, $insertTask->getDescription());
        $this->assertContains($filePattern, $insertTask->getDescription());
    }
}
