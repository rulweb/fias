<?php

namespace marvin255\fias\tests\utils\filesystem;

class FileTest extends \marvin255\fias\tests\BaseTestCase
{
    public function testConstructorEmptyPathException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new \marvin255\fias\utils\filesystem\File(false);
    }

    public function testConstructorWrongPathException()
    {
        $name = '/dir_' . mt_rand() . '/file';
        $dir = dirname($name);
        $this->setExpectedException('\InvalidArgumentException', $dir);
        new \marvin255\fias\utils\filesystem\File($name);
    }

    public function testGetPathName()
    {
        $file = new \marvin255\fias\utils\filesystem\File($this->templateFile);

        $this->assertSame(
            $this->info['dirname'] . '/' . $this->info['basename'],
            $file->getPathname()
        );
    }

    public function testGetPath()
    {
        $file = new \marvin255\fias\utils\filesystem\File($this->templateFile);

        $this->assertSame(
            $this->info['dirname'],
            $file->getPath()
        );
    }

    public function testGetFilename()
    {
        $file = new \marvin255\fias\utils\filesystem\File($this->templateFile);

        $this->assertSame(
            $this->info['filename'],
            $file->getFilename()
        );
    }

    public function testGetExtension()
    {
        $file = new \marvin255\fias\utils\filesystem\File($this->templateFile);

        $this->assertSame(
            $this->info['extension'],
            $file->getExtension()
        );
    }

    public function testGetBasename()
    {
        $file = new \marvin255\fias\utils\filesystem\File($this->templateFile);

        $this->assertSame(
            $this->info['basename'],
            $file->getBasename()
        );
    }

    public function testIsExists()
    {
        $file = new \marvin255\fias\utils\filesystem\File($this->templateFile);

        $this->assertSame(
            true,
            $file->isExists()
        );
    }

    public function testDelete()
    {
        $file = new \marvin255\fias\utils\filesystem\File($this->templateFile);

        $this->assertSame(
            true,
            $file->delete()
        );
        $this->assertSame(
            false,
            $file->isExists()
        );
    }

    public function setUp()
    {
        $name = sys_get_temp_dir() . '/temp_' . mt_rand() . '.tmp';
        file_put_contents($name, mt_rand());
        $this->templateFile = $name;
        $this->info = pathinfo($this->templateFile);
        $this->info['dirname'] = realpath($this->info['dirname']);
        $this->info['extension'] = isset($this->info['extension'])
            ? $this->info['extension']
            : null;
    }

    public function tearDown()
    {
        if (file_exists($this->templateFile)) {
            unlink($this->templateFile);
        }
    }
}
