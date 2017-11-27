<?php

namespace marvin255\fias\archiver;

use RarArchive;

/**
 * Распаковывает файлы из rar архива с помощью расширения php rar.
 */
class Rar implements IArchiver
{
    /**
     * @inhertidoc
     */
    public function extract(\marvin255\fias\file\IFile $from, $to)
    {
        if (!extension_loaded('rar')) {
            throw new Exception('Rar php extension must be loaded. See http://php.net/manual/ru/rar.installation.php');
        }

        $rarFile = RarArchive::open($from->getPath());
        if ($rarFile === false) {
            throw new Exception("Can't open archive " . $from->getPath());
        }

        $list = $rarFile->getEntries();
        if ($list === false) {
            throw new Exception("Can't load entries from archive " . $from->getPath());
        }

        foreach ($list as $entry) {
            $entry->extract($to);
        }

        $rarFile->close();
    }
}
