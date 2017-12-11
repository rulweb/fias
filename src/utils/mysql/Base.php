<?php

namespace marvin255\fias\utils\mysql;

use marvin255\fias\processor\ProcessorInterface;
use marvin255\fias\processor\Exception;
use PDO;
use InvalidArgumentException;

/**
 * Базовый класс для утилит mysql.
 */
abstract class Base implements ProcessorInterface
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
    public function close()
    {
        return $this;
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
     * Возвращает подготовленный запрос по его имени.
     *
     * @param string $name
     *
     * @return \PDOStatement|null
     */
    protected function getPrepared($name)
    {
        return isset($this->prepared[$name]) ? $this->prepared[$name] : null;
    }

    /**
     * Задает подготовленный запрос по его имени.
     *
     * @param string        $name
     * @param \PDOStatement $statement
     *
     * @return \marvin255\fias\utils\mysql\ProcessorInterface
     */
    protected function setPrepared($name, \PDOStatement $statement)
    {
        $this->prepared[$name] = $statement;

        return $this;
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
