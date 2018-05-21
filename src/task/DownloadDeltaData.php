<?php

declare(strict_types=1);

namespace marvin255\fias\task;

use InvalidArgumentException;

/**
 * Задача, которая получает ссылку  и загружает по ней архив со списком изменений
 * между определенными версиями ФИАС.
 */
class DownloadDeltaData extends DownloadCompleteData
{
    /**
     * @var string
     */
    const ARCHIVE_CURRENT_VERSION_PARAMETER = 'archive.current_version';

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return 'Download archive with delta for setted version';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function getUrlAndVersionForDownload(): array
    {
        $currentVersion = $this->paramsBag->get(self::ARCHIVE_CURRENT_VERSION_PARAMETER);

        if (!$currentVersion) {
            throw new InvalidArgumentException(
                "Can't find archive.current_version parameter to find delta"
            );
        }

        return $this->updateService->getUrlForDeltaData($currentVersion);
    }
}
