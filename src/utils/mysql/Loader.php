<?php

namespace marvin255\fias\utils\mysql;

use marvin255\fias\processor\ProcessorInterface;
use marvin255\fias\processor\Exception;
use PDO;
use InvalidArgumentException;

/**
 * Создание или обновление записей в mysql.
 */
class Loader implements ProcessorInterface
{
    /**
     * Объект pdo для подключения к бд.
     *
     * @var \PDO
     */
    protected $dbh = null;
    /**
     * Название таблицы для загрузки.
     *
     * @var string
     */
    protected $table = null;
    /**
     * Объект pdo для подключения к бд.
     *
     * @var array|string
     */
    protected $primary = null;
    /**
     * Массив имен столбцов, которые будут обновлены или загружены.
     *
     * @var array
     */
    protected $rows = null;
    /**
     * Количество элементов для bulk insert.
     *
     * @var int
     */
    protected $bulkCount = 100;

    /**
     * Текущий буффер элементов для bulk insert.
     *
     * @var array
     */
    protected $bulkBuffer = [];
    /**
     * Массив подготовленных запросов.
     *
     * @var array
     */
    protected $prepared = null;

    /**
     * Конструктор.
     *
     * @param \PDO         $dbh     Объект pdo для подключения к бд
     * @param string       $table   Название таблицы для загрузки
     * @param array|string $primary Имя столбца или столбцов для поиска
     * @param array        $rows    Массив имен столбцов, которые будут обновлены или загружены
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(PDO $dbh, $table, $primary, array $rows)
    {
        $this->dbh = $dbh;

        if (trim($table) === '') {
            throw new InvalidArgumentException("table parameter can't be empty");
        }
        $this->table = $table;

        if (empty($primary)) {
            throw new InvalidArgumentException("primary parameter can't be empty");
        } elseif (is_array($primary)) {
            foreach ($primary as $key => $item) {
                if (trim($item) !== '') {
                    continue;
                }
                throw new InvalidArgumentException("primary with key {$key} has wrong type");
            }
            $this->primary = $primary;
        } else {
            $this->primary = [$primary];
        }

        if (empty($rows)) {
            throw new InvalidArgumentException("primary parameter can't be empty");
        }
        foreach ($rows as $key => $item) {
            if (trim($item) !== '') {
                continue;
            }
            throw new InvalidArgumentException("row with key {$key} has wrong type");
        }
        $this->rows = $rows;
    }

    /**
     * @inheritdoc
     */
    public function open()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function process(array $data)
    {
        if ($dbItem = $this->find($data)) {
            $dbItem = array_map('trim', $dbItem);
            ksort($dbItem);
            $checkItem = array_map('trim', $data);
            ksort($checkItem);
            if ($dbItem !== $checkItem) {
                $this->update($data);
            }
        } else {
            $this->insert($data);
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * Перед закрытием нужно проверить очередь bulk insert и записать то, что
     * осталось.
     */
    public function close()
    {
        if ($this->bulkBuffer) {
            foreach ($this->bulkBuffer as $item) {
                $this->getPrepared('insert')->execute($this->getSetRows($item));
            }
            $this->bulkBuffer = [];
        }

        return $this;
    }

    /**
     * Пробует найти элемент в БД для указанного набора данных.
     *
     * @param array $dataSet
     *
     * @return array|null
     */
    protected function find(array $dataSet)
    {
        $where = $this->getWhereRows($dataSet);

        return $this->getPrepared('select')->execute($where)->fetchAll();
    }

    /**
     * Обновляет указанный в наборе данных элемент.
     *
     * @param array $dataSet
     */
    protected function update(array $dataSet)
    {
        $where = $this->getWhereRows($dataSet);
        $set = $this->getSetRows($dataSet);

        $this->getPrepared('update')->execute(array_merge($set, $where));
    }

    /**
     * Добавляет указанный набор данных в базу.
     *
     * Сначала добавляем набор в очередь, как только очередь достигает
     * указанной длины, записываем данные в БД одним запросом всю очередь.
     *
     * @param array $dataSet
     */
    protected function insert(array $dataSet)
    {
        $this->bulkBuffer[] = $dataSet;

        if (count($this->bulkBuffer) === (int) $this->bulkCount) {
            $insert = [];
            foreach ($this->bulkBuffer as $bulkItem) {
                $insert = array_merge($insert, $this->getSetRows($bulkItem));
            }
            $this->getPrepared('bulk_insert')->execute($insert);
            $this->bulkBuffer = [];
        }
    }

    /**
     * Возвращает поля из набора данных, которые соответствуют условию для поиска.
     *
     * @param array $dataSet
     *
     * @return array
     *
     * @throws \marvin255\fias\processor\Exception
     */
    protected function getWhereRows(array $dataSet)
    {
        $where = [];
        foreach ($this->primary as $primary) {
            if (!isset($dataSet[$primary])) {
                throw new Exception("Can't find primary row {$primary} for dataset");
            }
            $where[] = $dataSet[$primary];
        }

        return $where;
    }

    /**
     * Возвращает поля из набора данных, которые нужно записать в БД.
     *
     * @param array $dataSet
     *
     * @return array
     *
     * @throws \marvin255\fias\processor\Exception
     */
    protected function getSetRows(array $dataSet)
    {
        $set = [];
        foreach ($this->rows as $row) {
            if (!isset($dataSet[$row])) {
                throw new Exception("Can't find row {$row} for dataset");
            }
            $set[] = $dataSet[$row];
        }

        return $set;
    }

    /**
     * Возвращает подготовленный запрос по его имени.
     *
     * @param string $name
     *
     * @return \PDOStatement|null
     */
    protected function getPrepared($name)
    {
        if ($this->prepared === null) {
            $this->prepared = $this->createPrepared();
        }

        return isset($this->prepared[$name]) ? $this->prepared[$name] : null;
    }

    /**
     * Создает подготовленные запросы для PDO.
     *
     * @return array
     */
    protected function createPrepared()
    {
        $return = [];

        $table = $this->quoteIdent($this->table);

        $where = '';
        foreach ($this->primary as $primary) {
            $where .= ($where ? ' AND ' : '') . $this->quoteIdent($primary) . ' = ?';
        }

        $set = '';
        $select = '';
        $values = '';
        foreach ($this->rows as $row) {
            $select .= ($select ? ', ' : '') . $this->quoteIdent($row);
            $set .= ($set ? ', ' : '') . $this->quoteIdent($row) . ' = ?';
            $values .= ($values ? ', ' : '') . '?';
        }

        //выборка элемента по идентификатору
        $return['select'] = $dbh->prepare(
            "SELECT {$select} FROM {$table} WHERE {$where}"
        );
        //обновление элемента
        $return['update'] = $dbh->prepare(
            "UPDATE {$table} SET {$set} WHERE {$where}"
        );
        //создание нового элемента
        $return['insert'] = $dbh->prepare(
            "INSERT INTO {$table} ({$select}) VALUES ({$values})"
        );
        //создание сразу множества элементов
        $return['bulk_insert'] = $dbh->prepare(
            "INSERT INTO {$table} ({$select}) VALUES (" . implode('), (', array_fill(0, $this->bulkCount, $values)) . ')'
        );

        return $return;
    }

    /**
     * Подготавливает имя поля или таблицы для вставки в запрос.
     *
     * @param string $identifier
     *
     * @return string
     */
    protected function quoteIdent($identifier)
    {
        return '`' . str_replace('`', '', $identifier) . '`';
    }
}
