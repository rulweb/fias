<?php

declare(strict_types=1);

namespace marvin255\fias\service\database;

use PDO;
use PDOStatement;
use PDOException;

/**
 * Объект, который отвечает за соединение с базой данных с помощью php PDO.
 */
class Mysql implements DatabaseInterface
{
    /**
     * @var \PDO
     */
    protected $pdoConnection;
    /**
     * @var array
     */
    protected $prepared = [];

    /**
     * Задает объект PDO для соединения с базой данных.
     *
     * @param \PDO $pdoConnection
     */
    public function __construct(PDO $pdo)
    {
        $this->pdoConnection = $pdo;
    }

    /**
     * @inheritdoc
     */
    public function truncateTable(string $tableName): DatabaseInterface
    {
        $sql = 'DELETE FROM ' . $this->quoteIdent($tableName);
        $this->exec($sql);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function bulkInsert(string $tableName, array $data): DatabaseInterface
    {
        $firstItem = reset($data);
        $fields = array_keys($firstItem);

        $setOfFields = implode(', ', array_map([$this, 'quoteIdent'], $fields));
        $setOfValues = implode(', ', array_fill(0, count($fields), '?'));
        $sqlForBulkInsert = 'INSERT INTO ' . $this->quoteIdent($tableName) . " ({$setOfFields}) VALUES ("
            . implode('), (', array_fill(0, count($data), $setOfValues))
            . ')';

        $flatAray = call_user_func_array('array_merge', array_map('array_values', $data));

        $this->exec($sqlForBulkInsert, $flatAray);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function fetchItemByFieldValue(string $tableName, string $fieldName, $value): array
    {
        $sql = 'SELECT *'
            . ' FROM ' . $this->quoteIdent($tableName)
            . ' WHERE ' . $this->quoteIdent($fieldName) . ' = ?'
            . ' LIMIT 1';
        $res = $this->fetch($sql, [$value]);

        return $res ? reset($res) : [];
    }

    /**
     * @inheritdoc
     */
    public function updateItemByFieldValue(string $tableName, string $fieldName, $value, array $toUpdate): DatabaseInterface
    {
        $fields = array_map([$this, 'quoteIdent'], array_keys($toUpdate));
        $values = array_values($toUpdate);
        $values[] = $value;

        $sql = 'UPDATE ' . $this->quoteIdent($tableName)
            . ' SET ' . implode(' = ?, ', $fields) . ' = ?'
            . ' WHERE ' . $this->quoteIdent($fieldName) . ' = ?';

        $this->exec($sql, $values);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function insertItem(string $tableName, array $toInsert): DatabaseInterface
    {
        $fields = array_map([$this, 'quoteIdent'], array_keys($toInsert));
        $values = array_values($toInsert);

        $sql = 'INSERT INTO ' . $this->quoteIdent($tableName)
            . ' (' . implode(', ', $fields) . ')'
            . ' VALUES (' . implode(', ', array_fill(0, count($values), '?')) . ')';

        $this->exec($sql, $values);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function deleteItemByFieldValue(string $tableName, string $fieldName, $value): DatabaseInterface
    {
        $sql = 'DELETE  FROM ' . $this->quoteIdent($tableName)
            . ' WHERE ' . $this->quoteIdent($fieldName) . ' = ?';

        $res = $this->fetch($sql, [$value]);

        return $this;
    }

    /**
     * Ищет данные в базе и возвращает результат в виде ассоциативного массива.
     *
     * @param string $sql
     * @param array  $data
     *
     * @return array
     *
     * @throws \marvin255\fias\service\database\Exception
     */
    protected function fetch(string $sql, array $data = []): array
    {
        try {
            $statement = $this->getStatement($sql);
            $res = $statement->execute($data);
            if (!$res) {
                $error = $statement->errorInfo();
                throw new Exception($error[2]);
            }
            $list = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }

        return $list;
    }

    /**
     * Запускает запрос на исполнение и обрабатывает исключительные ситуации.
     *
     * @param string $sql
     * @param array  $data
     *
     * @return mixed
     *
     * @throws \marvin255\fias\service\database\Exception
     */
    protected function exec(string $sql, array $data = [])
    {
        try {
            $statement = $this->getStatement($sql);
            $res = $statement->execute($data);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 0, $e);
        }

        if (!$res) {
            $error = $statement->errorInfo();
            throw new Exception($error[2]);
        }

        return $res;
    }

    /**
     * Возвращает подговтовленное выражение, если оно уже есть,
     * либо создает новое и добавляет в список.
     *
     * @param string $sql
     *
     * @return \PDOStatement
     */
    protected function getStatement(string $sql): PDOStatement
    {
        foreach ($this->prepared as $prepared) {
            if ($prepared->queryString === $sql) {
                return $prepared;
            }
        }

        $newPrepared = $this->pdoConnection->prepare($sql);
        $this->prepared[] = $newPrepared;

        return $newPrepared;
    }

    /**
     * Подготавливает имя поля или таблицы для вставки в запрос.
     *
     * @param string $identifier
     *
     * @return string
     */
    protected function quoteIdent(string $identifier): string
    {
        return '`' . str_replace('`', '', trim($identifier)) . '`';
    }
}
