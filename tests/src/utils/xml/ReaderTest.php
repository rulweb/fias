<?php

namespace marvin255\fias\tests\utils\xml;

use marvin255\fias\utils\xml\Reader;

class ReaderTest extends \marvin255\fias\tests\BaseTestCase
{
    public function testIterator()
    {
        $path = '/part_1_' . mt_rand() . '/part_2_' . mt_rand() . '/part_3_' . mt_rand();
        $key = 'key' . mt_rand();

        $fileData = $this->fillFile(10, $path, $key);

        $reader = new Reader($path, [
            "tag1{$key}" => "tag1{$key}",
            "tag3{$key}" => "tag3{$key}",
            "attr{$key}" => "@attr{$key}",
        ]);
        $this->assertSame(
            $reader,
            $reader->open($this->templateFile)
        );

        $this->assertSame(
            reset($fileData),
            $reader->current()
        );

        $loadedData = [];
        foreach ($reader as $key => $value) {
            $loadedData[] = $value;
        }

        $loadedDataSecond = [];
        foreach ($reader as $key => $value) {
            $loadedDataSecond[] = $value;
        }

        $this->assertSame(
            $fileData,
            $loadedDataSecond
        );
        $this->assertSame(
            $reader,
            $reader->close()
        );
    }

    public function testIteratorEmptyFile()
    {
        $path = '/part_1_' . mt_rand() . '/part_2_' . mt_rand() . '/part_3_' . mt_rand();
        $key = 'key' . mt_rand();

        $fileData = $this->fillFile(0, $path, $key);

        $reader = new Reader($path, [
            "tag1{$key}" => "tag1{$key}",
            "tag3{$key}" => "tag3{$key}",
            "attr{$key}" => "@attr{$key}",
        ]);
        $reader->open($this->templateFile);

        $loadedData = [];
        foreach ($reader as $key => $value) {
            $loadedData[] = $value;
        }

        $loadedDataSecond = [];
        foreach ($reader as $key => $value) {
            $loadedDataSecond[] = $value;
        }

        $this->assertSame(
            $fileData,
            $loadedDataSecond
        );
    }

    public function testIteratorSelfClosedFile()
    {
        $path = '/part_1_' . mt_rand() . '/part_2_' . mt_rand();
        $key = 'key' . mt_rand();

        $fileData = $this->fillFileWithSelfClosed(10, $path, $key);

        $reader = new Reader($path, [
            "attr{$key}" => "@attr{$key}",
        ]);
        $reader->open($this->templateFile);

        $loadedData = [];
        foreach ($reader as $key => $value) {
            $loadedData[] = $value;
        }

        $loadedDataSecond = [];
        foreach ($reader as $key => $value) {
            $loadedDataSecond[] = $value;
        }

        $this->assertSame(
            $fileData,
            $loadedDataSecond
        );
    }

    public function testNotOpenException()
    {
        $this->setExpectedException('\marvin255\fias\reader\Exception');
        $reader = new Reader('/root/test', [
            'testKey' => 'testValue',
        ]);
        $reader->current();
    }

    public function testEmptyFileException()
    {
        $file = 'file_' . mt_rand();
        $this->setExpectedException('\InvalidArgumentException', $file);
        $reader = new Reader('/root/test', [
            'testKey' => 'testValue',
        ]);
        $reader->open($file);
    }

    public function testEmptyPathToNodeException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $reader = new Reader(null, [
            'testKey' => 'testValue',
        ]);
    }

    public function testEmptySelectException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $reader = new Reader('/root/test', []);
    }

    protected function fillFile($items, $path, $key)
    {
        $return = [];

        $arPath = array_diff(explode('/', $path), ['', null]);
        $itemString = array_pop($arPath);
        $arPath = array_values($arPath);
        $arPathCount = count($arPath);

        $fileContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";

        for ($i = 0; $i < $arPathCount; ++$i) {
            $pathItem = $arPath[$i];
            if ($i !== 0) {
                $fileContent .= "<{$pathItem}prev><{$pathItem}previtem>prev</{$pathItem}previtem></{$pathItem}prev>\r\n";
            }
            $fileContent .= "<{$pathItem}>\r\n";
            $fileContent .= "<{$pathItem}post><{$pathItem}postitem>post</{$pathItem}postitem></{$pathItem}post>\r\n";
        }

        for ($item = 0; $item < $items; ++$item) {
            $tag1 = "tag1{$key}";
            $value1 = "value1{$key}" . mt_rand();
            $tag2 = "tag2{$key}";
            $value2 = "value2{$key}" . mt_rand();
            $tag3 = "tag3{$key}";
            $value3 = "value3{$key}" . mt_rand();
            $attr = "attr{$key}";
            $attrValue = "attrvalue{$key}" . mt_rand();

            $return[] = [
                $tag1 => $value1,
                $tag3 => $value3,
                $attr => $attrValue,
            ];

            $fileContent .= "<{$itemString} {$attr}=\"{$attrValue}\">";
            $fileContent .= "<{$tag1}>{$value1}</{$tag1}>";
            $fileContent .= "<{$tag2}>{$value2}</{$tag2}>";
            $fileContent .= "<{$tag3}>{$value3}</{$tag3}>";
            $fileContent .= "</{$itemString}>\r\n";
        }

        for ($i = $arPathCount - 1; $i >= 0; --$i) {
            $pathItem = $arPath[$i];
            $fileContent .= "<{$pathItem}endprev><{$pathItem}endprevitem>endprev</{$pathItem}endprevitem></{$pathItem}endprev>\r\n";
            $fileContent .= "</{$pathItem}>\r\n";
            if ($i !== 0) {
                $fileContent .= "<{$pathItem}endpost><{$pathItem}endpostitem>endpost</{$pathItem}endpostitem></{$pathItem}endpost>\r\n";
            }
        }

        file_put_contents($this->templateFile, $fileContent);

        return $return;
    }

    protected function fillFileWithSelfClosed($items, $path, $key)
    {
        $return = [];

        $arPath = array_diff(explode('/', $path), ['', null]);
        $itemString = array_pop($arPath);
        $arPath = array_values($arPath);
        $arPathCount = count($arPath);

        $fileContent = '<?xml version="1.0" encoding="UTF-8"?>';

        for ($i = 0; $i < $arPathCount; ++$i) {
            $pathItem = $arPath[$i];
            $fileContent .= "<{$pathItem}>";
        }

        for ($item = 0; $item < $items; ++$item) {
            $attr = "attr{$key}";
            $attrValue = "attrvalue{$key}" . mt_rand();

            $return[] = [
                $attr => $attrValue,
            ];

            $fileContent .= "<{$itemString} {$attr}=\"{$attrValue}\" />";
        }

        for ($i = $arPathCount - 1; $i >= 0; --$i) {
            $pathItem = $arPath[$i];
            $fileContent .= "</{$pathItem}>";
        }

        file_put_contents($this->templateFile, $fileContent);

        return $return;
    }

    protected $templateFile = null;

    public function setUp()
    {
        $this->templateFile = tempnam(sys_get_temp_dir(), mt_rand());
    }

    public function tearDown()
    {
        unlink($this->templateFile);
    }
}
