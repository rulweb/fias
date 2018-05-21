<?php

namespace marvin255\fias\tests\task;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\task\UpdateData;
use marvin255\fias\service\filesystem\DirectoryInterface;
use marvin255\fias\service\filesystem\FileInterface;
use marvin255\fias\service\xml\ReaderInterface;
use marvin255\fias\service\database\DatabaseInterface;
use Mockery;
use RuntimeException;

class UpdateDataTest extends BaseTestCase
{
    public function testRun()
    {
        $tableName = 'table';
        $primaryName = 'primary';
        $filePattern = 'file_pattern';
        $pathToFile = '/file_pattern.xml';
        $xmlNode = '/root/item';
        $xmlSelect = ['primary' => 'row_in_xml'];
        $readerData = [
            ['primary' => $this->faker()->unique()->word, 'test' => 'test 1'],
            ['primary' => $this->faker()->unique()->word, 'test' => 'test 2'],
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
        $database->shouldReceive('fetchItemByFieldValue')
            ->with($tableName, $primaryName, $readerData[0][$primaryName])
            ->andReturn($readerData[0]);
        $database->shouldReceive('fetchItemByFieldValue')
            ->with($tableName, $primaryName, $readerData[1][$primaryName])
            ->andReturn([]);
        $database->shouldReceive('updateItemByFieldValue')->once()
            ->with($tableName, $primaryName, $readerData[0][$primaryName], $readerData[0]);
        $database->shouldReceive('insertItem')->once()
            ->with($tableName, $readerData[1]);

        $updateTask = new UpdateData($tableName, $primaryName, $filePattern, $xmlNode, $xmlSelect);
        $updateTask->setXmlReader($reader);
        $updateTask->setWorkDirectory($dir);
        $updateTask->setDatabase($database);

        $this->assertSame(true, $updateTask->run());
    }

    public function testRunNoPrimaryException()
    {
        $tableName = 'table';
        $primaryName = 'primary';
        $filePattern = 'file_pattern';
        $pathToFile = '/file_pattern.xml';
        $xmlNode = '/root/item';
        $xmlSelect = ['primary' => 'row_in_xml'];

        $file = Mockery::mock(FileInterface::class);
        $file->shouldReceive('getPathname')->andReturn($pathToFile);

        $dir = Mockery::mock(DirectoryInterface::class);
        $dir->shouldReceive('findFilesByPattern')->with($filePattern)->andReturn([$file]);

        $reader = Mockery::mock(ReaderInterface::class);
        $reader->shouldReceive('open')->once()->with($pathToFile, $xmlNode, $xmlSelect);
        $reader->shouldReceive('valid')->andReturn(true);
        $reader->shouldReceive('rewind')->andReturn(null);
        $reader->shouldReceive('current')->andReturn([]);

        $database = Mockery::mock(DatabaseInterface::class);

        $updateTask = new UpdateData($tableName, $primaryName, $filePattern, $xmlNode, $xmlSelect);
        $updateTask->setXmlReader($reader);
        $updateTask->setWorkDirectory($dir);
        $updateTask->setDatabase($database);

        $this->expectException(RuntimeException::class);
        $updateTask->run();
    }

    public function testGetDescription()
    {
        $tableName = $this->faker()->unique()->word;
        $primaryName = $this->faker()->unique()->word;
        $filePattern = 'file_pattern';
        $pathToFile = '/file_pattern.xml';
        $xmlNode = '/root/item';
        $xmlSelect = ['row_in_db' => 'row_in_xml'];

        $updateTask = new UpdateData($tableName, $primaryName, $filePattern, $xmlNode, $xmlSelect);

        $this->assertContains($tableName, $updateTask->getDescription());
        $this->assertContains($filePattern, $updateTask->getDescription());
    }
}
