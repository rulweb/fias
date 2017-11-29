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

        file_put_contents($this->folderPath . '/test_' . mt_rand() . '.tmp', mt_rand());
        $subfolder = $this->folderPath . '/sub_folder_' . mt_rand();
        mkdir($subfolder);
        file_put_contents($subfolder . '/test_' . mt_rand() . '.tmp', mt_rand());

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
        $this->assertSame(
            false,
            is_dir($this->folderPath)
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

    public function testCreateChildDirectory()
    {
        $folder = new \marvin255\fias\utils\filesystem\Directory($this->folderPath);
        $childName = 'child_folder_' . mt_rand();
        $child = $folder->createChildDirectory($childName);

        $this->assertInstanceOf(
            \marvin255\fias\utils\filesystem\DirectoryInterface::class,
            $child
        );
        $this->assertSame(
            $childName,
            $child->getFoldername()
        );
        $this->assertSame(
            $this->folderPath . '/' . $childName,
            $child->getPathname()
        );
    }

    public function testCreateChildDirectoryWrongNameException()
    {
        $name = '/~folder_' . mt_rand();
        $folder = new \marvin255\fias\utils\filesystem\Directory($this->folderPath);
        $this->setExpectedException('\InvalidArgumentException', $name);
        $folder->createChildDirectory($name);
    }

    public function testCreateChildFile()
    {
        $folder = new \marvin255\fias\utils\filesystem\Directory($this->folderPath);
        $folder->create();
        $childName = 'child_file_' . mt_rand() . '.tmp';
        $child = $folder->createChildFile($childName);

        $this->assertInstanceOf(
            \marvin255\fias\utils\filesystem\FileInterface::class,
            $child
        );
        $this->assertSame(
            $childName,
            $child->getBasename()
        );
        $this->assertSame(
            $this->folderPath . '/' . $childName,
            $child->getPathname()
        );
        $this->assertSame(
            'tmp',
            $child->getExtension()
        );
    }

    public function testCreateChildFileWrongNameException()
    {
        $name = '/~file_' . mt_rand();
        $folder = new \marvin255\fias\utils\filesystem\Directory($this->folderPath);
        $this->setExpectedException('\InvalidArgumentException', $name);
        $folder->createChildFile($name);
    }

    public function testIterator()
    {
        $folder = new \marvin255\fias\utils\filesystem\Directory($this->folderPath);
        $folder->create();

        $children = [
            $this->folderPath . '/file_0_' . mt_rand() . '.tmp',
            $this->folderPath . '/file_1_' . mt_rand() . '.tmp',
            $this->folderPath . '/sub_folder_2_' . mt_rand(),
            $this->folderPath . '/sub_folder_3_' . mt_rand(),
        ];
        file_put_contents($children[0], mt_rand());
        file_put_contents($children[1], mt_rand());
        mkdir($children[2]);
        mkdir($children[3]);

        $forTest = [];
        foreach ($folder as $key => $child) {
            $forTest[] = $child->getPathname();
        }

        sort($children);
        sort($forTest);

        $this->assertSame(
            $forTest,
            $children
        );
    }

    public function setUp()
    {
        $this->folderName = 'temp_' . mt_rand();
        $this->rootPath = sys_get_temp_dir() . '/test_sub_folder_' . mt_rand();
        $this->folderPath = $this->rootPath . '/' . $this->folderName;
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
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } elseif ($file->isFile()) {
                    unlink($file->getRealPath());
                }
            }
            rmdir($this->folderPath);
        }
        if (is_dir($this->rootPath)) {
            rmdir($this->rootPath);
        }
    }
}
