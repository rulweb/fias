<?php

namespace marvin255\fias\tests\task;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\task\DownloadDeltaData;
use marvin255\fias\service\fias\UpdateServiceInterface;
use marvin255\fias\service\downloader\DownloaderInterface;
use marvin255\fias\service\filesystem\DirectoryInterface;
use marvin255\fias\service\filesystem\FileInterface;
use marvin255\fias\service\bag\BagInterface;
use Mockery;
use InvalidArgumentException;

class DownloadDeltaDataTest extends BaseTestCase
{
    public function testRun()
    {
        $currentVersion = $this->faker()->unique()->randomNumber;
        $newVersion = $this->faker()->unique()->randomNumber;
        $updateUrl = 'http://test.test/test.rar';
        $fileName = $this->faker()->unique()->word . '.rar';
        $filePath = '/' . $this->faker()->unique()->word . '/' . $fileName;

        $updateService = Mockery::mock(UpdateServiceInterface::class);
        $updateService->shouldReceive('getUrlForDeltaData')
            ->once()
            ->with($currentVersion)
            ->andReturn([
                'url' => $updateUrl,
                'version' => $newVersion,
            ]);

        $file = Mockery::mock(FileInterface::class);
        $file->shouldReceive('getPathname')->andReturn($filePath);
        $file->shouldReceive('getBasename')->andReturn($fileName);

        $directory = Mockery::mock(DirectoryInterface::class);
        $directory->shouldReceive('createChildFile')->andReturn($file);

        $downloader = Mockery::mock(DownloaderInterface::class);
        $downloader->shouldReceive('download')->once()->with($updateUrl, $filePath);

        $paramsBag = Mockery::mock(BagInterface::class);
        $paramsBag->shouldReceive('get')
            ->once()
            ->with(DownloadDeltaData::ARCHIVE_CURRENT_VERSION_PARAMETER)
            ->andReturn($currentVersion);
        $paramsBag->shouldReceive('set')
            ->once()
            ->with(DownloadDeltaData::ARCHIVE_NAME_PARAMETER, $fileName);
        $paramsBag->shouldReceive('set')
            ->once()
            ->with(DownloadDeltaData::ARCHIVE_VERSION_PARAMETER, $newVersion);

        $downloadTask = new DownloadDeltaData;
        $downloadTask->setUpdateService($updateService);
        $downloadTask->setDownloader($downloader);
        $downloadTask->setWorkDirectory($directory);
        $downloadTask->setParamsBag($paramsBag);

        $this->assertSame(true, $downloadTask->run());
    }

    public function testRunNoCurrentVersionException()
    {
        $updateService = Mockery::mock(UpdateServiceInterface::class);
        $directory = Mockery::mock(DirectoryInterface::class);
        $downloader = Mockery::mock(DownloaderInterface::class);

        $paramsBag = Mockery::mock(BagInterface::class);
        $paramsBag->shouldReceive('get')
            ->once()
            ->with(DownloadDeltaData::ARCHIVE_CURRENT_VERSION_PARAMETER)
            ->andReturn(null);

        $downloadTask = new DownloadDeltaData;
        $downloadTask->setUpdateService($updateService);
        $downloadTask->setDownloader($downloader);
        $downloadTask->setWorkDirectory($directory);
        $downloadTask->setParamsBag($paramsBag);

        $this->expectException(InvalidArgumentException::class);
        $downloadTask->run();
    }

    public function testGetDescription()
    {
        $downloadTask = new DownloadDeltaData;

        $this->assertNotEmpty($downloadTask->getDescription());
    }
}
