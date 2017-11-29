<?php

namespace marvin255\fias\utils\unpacker;

use RarArchive;

/**
 * Распаковывает файлы из rar архива с помощью расширения php rar.
 */
class Rar implements UnpackerInterface
{
    /**
     * {@inhertidoc}
     *
     * @throws \marvin255\fias\utils\unpacker\Exception
     */
    public function unpack($archive, $extractTo)
    {
        $rarFile = RarArchive::open($archive);
        if ($rarFile === false) {
            throw new Exception("Can't open rar archive {$archive}");
        }

        $list = $rarFile->getEntries();
        if ($list === false) {
            throw new Exception("Can't load entries from archive {$archive}");
        }

        foreach ($list as $entry) {
            $entry->extract($extractTo);
        }

        $rarFile->close();
    }
}
