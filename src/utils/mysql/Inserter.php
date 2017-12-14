<?php

namespace marvin255\fias\utils\mysql;

use marvin255\fias\processor\Exception;
use PDO;

/**
 * Создание записей в mysql.
 *
 * Класс следует использовать только при полной уверенности, что записи в бд
 * не нужно будет обновлять. Позволяет сократить время на поиск перед записью.
 */
class Inserter extends Base
{
    /**
     * Количество элементов для bulk insert.
     *
     * @var int
     */
    protected $bulkCount = null;
    /**
     * Текущий буффер элементов для bulk insert.
     *
     * @var array
     */
    protected $bulkBuffer = [];

    /**
     * {@inheritdoc}
     *
     * @param int $bulkCount Размер стека данных для bulk insert
     */
    public function __construct(PDO $dbh, $table, $primary, array $rows, $bulkCount = 100)
    {
        parent::__construct($dbh, $table, $primary, $rows);

        $this->bulkCount = $bulkCount;

        $this->createPrepared();
    }

    /**
     * @inheritdoc
     *
     * Записывает каждый набор данных в базу данных в качестве новой строки.
     */
    public function process(array $data)
    {
        $this->insert($data);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * Перед закрытием нужно проверить очередь bulk insert и записать то, что
     * осталось.
     *
     * @throws \marvin255\fias\processor\Exception
     */
    public function close()
    {
        if ($this->bulkBuffer) {
            foreach ($this->bulkBuffer as $item) {
                $statement = $this->getPrepared('insert');
                if (!$statement->execute($this->getSetRows($item))) {
                    $error = $statement->errorInfo();
                    throw new Exception("Insert operation failed: {$error[2]}");
                }
            }
            $this->bulkBuffer = [];
        }

        return $this;
    }

    /**
     * Добавляет указанный набор данных в базу.
     *
     * Сначала добавляем набор в очередь, как только очередь достигает
     * указанной длины, записываем данные в БД одним запросом всю очередь.
     *
     * @param array $dataSet
     *
     * @throws \marvin255\fias\processor\Exception
     */
    protected function insert(array $dataSet)
    {
        $this->bulkBuffer[] = $dataSet;

        if (count($this->bulkBuffer) === (int) $this->bulkCount) {
            $insert = [];
            foreach ($this->bulkBuffer as $bulkItem) {
                $insert = array_merge($insert, $this->getSetRows($bulkItem));
            }
            $statement = $this->getPrepared('bulk_insert');
            if (!$statement->execute($insert)) {
                $error = $statement->errorInfo();
                throw new Exception("Insert operation failed: {$error[2]}");
            }
            $this->bulkBuffer = [];
        }
    }

    /**
     * Создает подготовленные запросы для PDO.
     *
     * @return \marvin255\fias\utils\mysql\ProcessorInterface
     */
    protected function createPrepared()
    {
        $table = $this->quoteIdent($this->table);

        $select = '';
        $values = '';
        foreach ($this->rows as $row) {
            $select .= ($select ? ', ' : '') . $this->quoteIdent($row);
            $values .= ($values ? ', ' : '') . '?';
        }

        //создание нового элемента
        $statement = $this->dbh->prepare(
            "INSERT INTO {$table} ({$select}) VALUES ({$values})"
        );
        if ($statement) {
            $this->setPrepared('insert', $statement);
        }

        //создание сразу множества элементов
        $statement = $this->dbh->prepare(
            "INSERT INTO {$table} ({$select}) VALUES (" . implode('), (', array_fill(0, $this->bulkCount, $values)) . ')'
        );
        if ($statement) {
            $this->setPrepared('bulk_insert', $statement);
        }

        return $this;
    }
}
