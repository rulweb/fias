<?php

namespace marvin255\fias\tests\utils\filesystem;

class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyPathConstructException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $folder = new \marvin255\fias\utils\filesystem\Directory(false);
    }

    public function testNotRootPathConstructException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $folder = new \marvin255\fias\utils\filesystem\Directory('/../test');
    }

    public function testWrongFileClassConstructException()
    {
        $this->setExpectedException('\InvalidArgumentException', self::class);
        $folder = new \marvin255\fias\utils\filesystem\Directory(
            $this->folderPath,
            self::class
        );
    }

    public function testWrongDirectoryClassConstructException()
    {
        $this->setExpectedException('\InvalidArgumentException', self::class);
        $folder = new \marvin255\fias\utils\filesystem\Directory(
            $this->folderPath,
            \marvin255\fias\utils\filesystem\File::class,
            self::class
        );
    }

    public function testGetPathname()
    {
        $folder = new \marvin255\fias\utils\filesystem\Directory($this->folderPath);

        $this->assertSame(
            $this->folderPath,
            $folder->getPathname()
        );
    }

    public function testGetPath()
    {
        $folder = new \marvin255\fias\utils\filesystem\Directory($this->folderPath);

        $this->assertSame(
            dirname($this->folderPath),
            $folder->getPath()
        );
    }

    public function testGetFolderName()
    {
        $folder = new \marvin255\fias\utils\filesystem\Directory($this->folderPath);

        $this->assertSame(
            $this->folderName,
            $folder->getFolderName()
        );
    }

    public function testIsExists()
    {
        $folder = new \marvin255\fias\utils\filesystem\Directory($this->folderPath);

        $this->assertSame(
            false,
            $folder->isExists()
        );
    }

    public function testDelete()
    {
        $folder = new \marvin255\fias\utils\filesystem\Directory($this->folderPath);
        $folder->create();
        file_put_contents($this->folderPath . '/test.tmp', mt_rand());
        $this->assertSame(
            true,
            $folder->isExists()
        );
        $this->assertSame(
            true,
            $folder->delete()
        );
        $this->assertSame(
            false,
            $folder->isExists()
        );
    }

    public function testCreate()
    {
        $folder = new \marvin255\fias\utils\filesystem\Directory($this->folderPath);

        $this->assertSame(
            false,
            $folder->isExists()
        );
        $this->assertSame(
            true,
            $folder->create()
        );
        $this->assertSame(
            true,
            $folder->isExists()
        );
    }

    public function setUp()
    {
        $this->folderName = 'temp_' . mt_rand();
        $this->folderPath = sys_get_temp_dir() . '/sub_folder_' . mt_rand() . '/' . $this->folderName;
    }

    public function tearDown()
    {
        if (is_dir($this->folderPath)) {
            $it = new \RecursiveDirectoryIterator(
                $this->folderPath,
                \RecursiveDirectoryIterator::SKIP_DOTS
            );
            $files = new \RecursiveIteratorIterator(
                $it,
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach($files as $file) {
                if ($file->isDir()){
                    rmdir($file->getRealPath());
                } elseif ($file->isFile()) {
                    unlink($file->getRealPath());
                }
            }
            rmdir($this->folderPath);
        }
    }
}
