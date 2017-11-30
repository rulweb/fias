<?php

namespace marvin255\fias\tests\utils\filesystem;

use marvin255\fias\utils\filesystem\FilterExtension;

class FilterExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructEmptyExtensionsListException()
    {
        $this->setExpectedException('\\InvalidArgumentException');
        $filter = new FilterExtension([]);
    }

    public function testConstructNonStringExtensionException()
    {
        $this->setExpectedException('\\InvalidArgumentException', 2);
        $filter = new FilterExtension(['xml', 'csv', false]);
    }

    public function testConstructEmptyExtensionException()
    {
        $this->setExpectedException('\\InvalidArgumentException', 1);
        $filter = new FilterExtension(['xml', '']);
    }

    public function testCheck()
    {
        $ext = 'ext_' . mt_rand();
        $file = $this->getMockBuilder('\marvin255\fias\utils\filesystem\FileInterface')
            ->getMock();
        $file->method('getExtension')->will($this->returnValue($ext));

        $ext1 = 'ext_1_' . mt_rand();
        $file1 = $this->getMockBuilder('\marvin255\fias\utils\filesystem\FileInterface')
            ->getMock();
        $file1->method('getExtension')->will($this->returnValue($ext1));

        $filter = new FilterExtension([$ext]);

        $this->assertSame(
            true,
            $filter->check($file)
        );
        $this->assertSame(
            false,
            $filter->check($file1)
        );
    }
}
