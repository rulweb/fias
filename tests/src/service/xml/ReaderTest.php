<?php

namespace marvin255\fias\tests\service\xml;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\service\xml\Reader;
use InvalidArgumentException;
use RuntimeException;

class ReaderTest extends BaseTestCase
{
    public function testIterator()
    {
        $reader = new Reader;
        $reader->open(
            __DIR__ . '/_fixture/test_iterator.xml',
            '/root/firstLevel/secondLevel/realItem',
            [
                'third' => 'thirdParam',
                'first' => 'firstParam',
                'attr' => '@test',
            ]
        );

        $beforeResetData = [];
        foreach ($reader as $key => $value) {
            $beforeResetData[] = $value;
        }

        $afterResetData = [];
        foreach ($reader as $key => $value) {
            $afterResetData[] = $value;
        }

        $fileData = include __DIR__ . '/_fixture/test_iterator.php';

        $this->assertSame($fileData, $beforeResetData);
        $this->assertSame($fileData, $afterResetData);
    }

    public function testEmpty()
    {
        $reader = new Reader;
        $reader->open(
            __DIR__ . '/_fixture/test_empty.xml',
            '/root/firstLevel/secondLevel/realItem',
            [
                'third' => 'thirdParam',
                'first' => 'firstParam',
                'attr' => '@test',
            ]
        );

        $beforeResetData = [];
        foreach ($reader as $key => $value) {
            $beforeResetData[] = $value;
        }

        $afterResetData = [];
        foreach ($reader as $key => $value) {
            $afterResetData[] = $value;
        }

        $fileData = include __DIR__ . '/_fixture/test_empty.php';

        $this->assertSame($fileData, $beforeResetData);
        $this->assertSame($fileData, $afterResetData);
    }

    public function testSelfClosed()
    {
        $reader = new Reader;
        $reader->open(
            __DIR__ . '/_fixture/test_self_closed.xml',
            '/root/realItem',
            ['attr' => '@test']
        );

        $beforeResetData = [];
        foreach ($reader as $key => $value) {
            $beforeResetData[] = $value;
        }

        $afterResetData = [];
        foreach ($reader as $key => $value) {
            $afterResetData[] = $value;
        }

        $fileData = include __DIR__ . '/_fixture/test_self_closed.php';

        $this->assertSame($fileData, $beforeResetData);
        $this->assertSame($fileData, $afterResetData);
    }

    public function testEmptyFileException()
    {
        $reader = new Reader;

        $this->expectException(InvalidArgumentException::class);
        $reader->open('test', '/root/test', ['testKey' => 'testValue']);
    }

    public function testNotOpenException()
    {
        $reader = new Reader;

        $this->expectException(RuntimeException::class);
        $reader->current();
    }

    public function testEmptyPathToNodeException()
    {
        $reader = new Reader;

        $this->expectException(InvalidArgumentException::class);
        $reader->open(
            __DIR__ . '/_fixture/test_empty.xml',
            '',
            ['testKey' => 'testValue']
        );
    }

    public function testEmptySelectException()
    {
        $reader = new Reader;

        $this->expectException(InvalidArgumentException::class);
        $reader->open(
            __DIR__ . '/_fixture/test_empty.xml',
            '/root/test',
            []
        );
    }
}
