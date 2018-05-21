<?php

declare(strict_types=1);

namespace marvin255\fias\task;

use marvin255\fias\TaskInterface;
use marvin255\fias\service\fias\UpdateServiceInterface;
use marvin255\fias\service\downloader\DownloaderInterface;
use marvin255\fias\service\filesystem\DirectoryInterface;
use marvin255\fias\service\bag\BagInterface;

/**
 * Задача, которая получает ссылку
 * и загружает по ней архив с полными данными ФИАС.
 */
class DownloadCompleteData implements TaskInterface
{
    /**
     * @var string
     */
    const ARCHIVE_NAME_PARAMETER = 'archive.name';
    /**
     * @var string
     */
    const ARCHIVE_VERSION_PARAMETER = 'archive.version';
    /**
     * @var \marvin255\fias\service\fias\UpdateServiceInterface
     */
    protected $updateService;
    /**
     * @var \marvin255\fias\service\downloader\DownloaderInterface
     */
    protected $downloader;
    /**
     * @var \marvin255\fias\service\filesystem\DirectoryInterface
     */
    protected $workDirectory;
    /**
     * @var \marvin255\fias\service\bag\BagInterface
     */
    protected $paramsBag;

    /**
     * @inheritdoc
     */
    public function run(): bool
    {
        $fiasInfo = $this->getUrlAndVersionForDownload();

        $file = $this->workDirectory->createChildFile(
            pathinfo($fiasInfo['url'], PATHINFO_BASENAME)
        );

        $this->downloader->download($fiasInfo['url'], $file->getPathname());

        $this->paramsBag->set(self::ARCHIVE_NAME_PARAMETER, $file->getBasename());
        $this->paramsBag->set(self::ARCHIVE_VERSION_PARAMETER, $fiasInfo['version']);

        return true;
    }

    /**
     * Сеттер для объекта soap сервиса ФИАС, который возвращает ссылку на файл
     * с данными ФИАС.
     *
     * @param \marvin255\fias\service\fias\UpdateServiceInterface $updateService
     *
     * @return self
     */
    public function setUpdateService(UpdateServiceInterface $updateService): DownloadCompleteData
    {
        $this->updateService = $updateService;

        return $this;
    }

    /**
     * Сеттер для объекта, который скачивает файл по ссылке.
     *
     * @param \marvin255\fias\service\downloader\DownloaderInterface $downloader
     *
     * @return self
     */
    public function setDownloader(DownloaderInterface $downloader): DownloadCompleteData
    {
        $this->downloader = $downloader;

        return $this;
    }

    /**
     * Сеттер для объекта с рабочей папкой, в которую нужно сохранить файл.
     *
     * @param \marvin255\fias\service\filesystem\DirectoryInterface $workDirectory
     *
     * @return self
     */
    public function setWorkDirectory(DirectoryInterface $workDirectory): DownloadCompleteData
    {
        $this->workDirectory = $workDirectory;

        return $this;
    }

    /**
     * Сеттер для объекта, который передает данные между задачами.
     *
     * @param \marvin255\fias\service\bag\BagInterface $paramsBag
     *
     * @return self
     */
    public function setParamsBag(BagInterface $paramsBag): DownloadCompleteData
    {
        $this->paramsBag = $paramsBag;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return 'Download archive with full fias';
    }

    /**
     * Возвращает ссылку и версию файла для скачивания.
     *
     * @return array
     */
    protected function getUrlAndVersionForDownload(): array
    {
        return $this->updateService->getUrlForCompleteData();
    }
}
