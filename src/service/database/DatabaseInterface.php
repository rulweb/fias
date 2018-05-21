<?php

declare(strict_types=1);

namespace marvin255\fias\service\database;

/**
 * Интерфейс для объекта, который отвечает за соединение с базой данных.
 */
interface DatabaseInterface
{
    /**
     * Очищает таблицу от содержимого.
     *
     * @param string $tableName
     *
     * @return self
     */
    public function truncateTable(string $tableName): DatabaseInterface;

    /**
     * Множественная вставка данных в таблицу.
     *
     * @param string $tableName
     * @param array  $data
     *
     * @return self
     */
    public function bulkInsert(string $tableName, array $data): DatabaseInterface;

    /**
     * Ищет в таблице запись по указанному полю.
     *
     * @param string $tableName
     * @param string $fieldName
     * @param mixed  $value
     *
     * @return array
     */
    public function fetchItemByFieldValue(string $tableName, string $fieldName, $value): array;

    /**
     * Ищет в таблице запись по указанному полю и обновляет список ее полей
     * в соответствии с четвертым параметром.
     *
     * @param string $tableName
     * @param string $fieldName
     * @param mixed  $value
     * @param array  $toUpdate
     *
     * @return self
     */
    public function updateItemByFieldValue(string $tableName, string $fieldName, $value, array $toUpdate): DatabaseInterface;

    /**
     * Добавляет новуюзапись в указанную таблицу.
     *
     * @param string $tableName
     * @param array  $toInsert
     *
     * @return self
     */
    public function insertItem(string $tableName, array $toInsert): DatabaseInterface;

    /**
     * Удаляет в таблице запись по указанному полю.
     *
     * @param string $tableName
     * @param string $fieldName
     * @param mixed  $value
     *
     * @return self
     */
    public function deleteItemByFieldValue(string $tableName, string $fieldName, $value): DatabaseInterface;
}
