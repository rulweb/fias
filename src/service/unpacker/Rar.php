<?php

declare(strict_types=1);

namespace marvin255\fias\service\unpacker;

use RarArchive;
use RuntimeException;

/**
 * Объект, который распаковывает данные из rar архива.
 */
class Rar implements UnpackerInterface
{
    /**
     * @inheritdoc
     */
    public function unpack(string $pathToArchive, string $pathToUnpack)
    {
        $rarArchive = $this->openArchive($pathToArchive);
        try {
            $this->extractArchiveTo($rarArchive, $pathToUnpack);
        } finally {
            $rarArchive->close();
        }
    }

    /**
     * Открывает архив на чтение.
     *
     * @param string $pathToArchive
     *
     * @return \RarArchive
     *
     * @throws \RuntimeException
     */
    protected function openArchive(string $pathToArchive): RarArchive
    {
        $rarArchive = $this->callRarArchiveOpen($pathToArchive);

        if ($rarArchive === false) {
            throw new RuntimeException(
                "Can't open rar archive {$pathToArchive}"
            );
        }

        return $rarArchive;
    }

    /**
     * Инкапсулирует в себе вызов RarArchive::open.
     *
     * @param string $pathToArchive
     *
     * @return \RarArchive|false
     */
    protected function callRarArchiveOpen(string $pathToArchive)
    {
        return RarArchive::open($pathToArchive);
    }

    /**
     * Извлекает содержимое архива в указанную папку.
     *
     * @param \RarArchive $rarArchive
     * @param string      $pathToUnpack
     *
     * @throws \RuntimeException
     */
    protected function extractArchiveTo(RarArchive $rarArchive, string $pathToUnpack)
    {
        $entries = $this->getEntries($rarArchive);

        foreach ($entries as $entry) {
            if ($entry->extract($pathToUnpack) === false) {
                $entryName = $entry->getName();
                throw new RuntimeException(
                    "Can't extract entry {$entryName} to {$pathToUnpack}"
                );
            }
        }
    }

    /**
     * Получает список сущностей из архива.
     *
     * @param \RarArchive $rarArchive
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function getEntries(RarArchive $rarArchive): array
    {
        $entries = $rarArchive->getEntries();

        if ($entries === false) {
            throw new RuntimeException(
                "Can't read entries from archive"
            );
        }

        return $entries;
    }
}
