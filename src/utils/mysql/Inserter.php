<?php

namespace marvin255\fias\utils\mysql;

use marvin255\fias\processor\ProcessorInterface;
use marvin255\fias\processor\Exception;
use PDO;
use InvalidArgumentException;

/**
 * Создание записей в mysql.
 *
 * Класс следует использовать только при полной уверенности, что записи в бд
 * не нужно будет обновлять. Позволяет сократить время на поиск перед записью.
 */
class Inserter implements ProcessorInterface
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
    protected $bulkCount = null;

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
     * @param \PDO         $dbh       Объект pdo для подключения к бд
     * @param string       $table     Название таблицы для загрузки
     * @param array|string $primary   Имя столбца или столбцов для поиска
     * @param array        $rows      Массив имен столбцов, которые будут обновлены или загружены
     * @param int          $bulkCount Размер стека данных для bulk insert
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(PDO $dbh, $table, $primary, array $rows, $bulkCount = 100)
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

        $this->bulkCount = $bulkCount;
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

        $select = '';
        $values = '';
        foreach ($this->rows as $row) {
            $select .= ($select ? ', ' : '') . $this->quoteIdent($row);
            $values .= ($values ? ', ' : '') . '?';
        }

        //создание нового элемента
        $return['insert'] = $this->dbh->prepare(
            "INSERT INTO {$table} ({$select}) VALUES ({$values})"
        );
        //создание сразу множества элементов
        $return['bulk_insert'] = $this->dbh->prepare(
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
