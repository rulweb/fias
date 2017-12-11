<?php

namespace marvin255\fias\tests\utils\filesystem;

use marvin255\fias\utils\filesystem\FilterRegexp;

class FilterRegexpTest extends \marvin255\fias\tests\BaseTestCase
{
    public function testConstructEmptyExtensionsListException()
    {
        $this->setExpectedException('\\InvalidArgumentException');
        $filter = new FilterRegexp(null);
    }

    public function testCheck()
    {
        $name = mt_rand() . '_findme_' . mt_rand() . '.XML';
        $file = $this->getMockBuilder('\marvin255\fias\utils\filesystem\FileInterface')
            ->getMock();
        $file->method('getBasename')->will($this->returnValue($name));

        $name1 = mt_rand() . '_dontfindme_' . mt_rand() . '.XML';
        $file1 = $this->getMockBuilder('\marvin255\fias\utils\filesystem\FileInterface')
            ->getMock();
        $file1->method('getBasename')->will($this->returnValue($name1));

        $filter = new FilterRegexp('.*_findme_.*\.xml');

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
