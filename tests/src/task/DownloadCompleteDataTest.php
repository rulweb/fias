<?php

namespace marvin255\fias\tests\task;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\task\DownloadCompleteData;
use marvin255\fias\service\fias\UpdateServiceInterface;
use marvin255\fias\service\downloader\DownloaderInterface;
use marvin255\fias\service\filesystem\DirectoryInterface;
use marvin255\fias\service\filesystem\FileInterface;
use marvin255\fias\service\bag\BagInterface;
use Mockery;

class DownloadCompleteDataTest extends BaseTestCase
{
    public function testRun()
    {
        $version = $this->faker()->unique()->randomNumber;
        $updateUrl = 'http://test.test/test.rar';
        $fileName = $this->faker()->unique()->word . '.rar';
        $filePath = '/' . $this->faker()->unique()->word . '/' . $fileName;

        $updateService = Mockery::mock(UpdateServiceInterface::class);
        $updateService->shouldReceive('getUrlForCompleteData')->once()->andReturn([
            'url' => $updateUrl,
            'version' => $version,
        ]);

        $file = Mockery::mock(FileInterface::class);
        $file->shouldReceive('getPathname')->andReturn($filePath);
        $file->shouldReceive('getBasename')->andReturn($fileName);

        $directory = Mockery::mock(DirectoryInterface::class);
        $directory->shouldReceive('createChildFile')->andReturn($file);

        $downloader = Mockery::mock(DownloaderInterface::class);
        $downloader->shouldReceive('download')->once()->with($updateUrl, $filePath);

        $paramsBag = Mockery::mock(BagInterface::class);
        $paramsBag->shouldReceive('set')
            ->once()
            ->with(DownloadCompleteData::ARCHIVE_NAME_PARAMETER, $fileName);
        $paramsBag->shouldReceive('set')
            ->once()
            ->with(DownloadCompleteData::ARCHIVE_VERSION_PARAMETER, $version);

        $downloadTask = new DownloadCompleteData;
        $downloadTask->setUpdateService($updateService);
        $downloadTask->setDownloader($downloader);
        $downloadTask->setWorkDirectory($directory);
        $downloadTask->setParamsBag($paramsBag);

        $this->assertSame(true, $downloadTask->run());
    }

    public function testGetDescription()
    {
        $downloadTask = new DownloadCompleteData;

        $this->assertNotEmpty($downloadTask->getDescription());
    }
}
