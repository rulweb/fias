<?php

declare(strict_types=1);

namespace marvin255\fias\task;

use RuntimeException;

/**
 * Задача, которая удаляет данные из таблицы согласно файлу.
 */
class DeleteData extends UpdateData
{
    /**
     * Удаляет данные из базы.
     *
     * @throws \RuntimeException
     */
    protected function proceedData()
    {
        foreach ($this->reader as $item) {
            if (!isset($item[$this->primaryName])) {
                throw new RuntimeException(
                    "Can't find primary key {$this->primaryName} in dataset: "
                    . json_encode($item, JSON_UNESCAPED_UNICODE)
                );
            }
            $this->database->deleteItemByFieldValue(
                $this->tableName,
                $this->primaryName,
                $item[$this->primaryName]
            );
        }
    }
}
