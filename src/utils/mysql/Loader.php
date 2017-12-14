<?php

namespace marvin255\fias\utils\mysql;

use marvin255\fias\processor\Exception;
use PDO;

/**
 * Создание или обновление записей в mysql.
 */
class Loader extends Inserter
{
    /**
     * @inheritdoc
     *
     * Каждый раз пробует отыскать указаный набор данных в базе. Если находит,
     * то обновляет при наличии различий, если не находит, то создает.
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
     * Пробует найти элемент в БД для указанного набора данных.
     *
     * @param array $dataSet
     *
     * @return array|bool
     *
     * @throws \marvin255\fias\processor\Exception
     */
    protected function find(array $dataSet)
    {
        $where = $this->getWhereRows($dataSet);
        $sth = $this->getPrepared('select');
        if (!$sth->execute($where)) {
            $error = $sth->errorInfo();
            throw new Exception("Select operation failed: {$error[2]}");
        }
        $res = $sth->fetch(PDO::FETCH_ASSOC);

        return $res ?: false;
    }

    /**
     * Обновляет указанный в наборе данных элемент.
     *
     * @param array $dataSet
     *
     * @throws \marvin255\fias\processor\Exception
     */
    protected function update(array $dataSet)
    {
        $where = $this->getWhereRows($dataSet);
        $set = $this->getSetRows($dataSet);

        $sth = $this->getPrepared('update');
        if (!$sth->execute(array_merge($set, $where))) {
            $error = $sth->errorInfo();
            throw new Exception("Select operation failed: {$error[2]}");
        }
    }

    /**
     * @inheritdoc
     */
    protected function createPrepared()
    {
        $return = parent::createPrepared();

        $table = $this->quoteIdent($this->table);

        $where = '';
        foreach ($this->primary as $primary) {
            $where .= ($where ? ' AND ' : '') . $this->quoteIdent($primary) . ' = ?';
        }

        $set = '';
        $select = '';
        foreach ($this->rows as $row) {
            $select .= ($select ? ', ' : '') . $this->quoteIdent($row);
            $set .= ($set ? ', ' : '') . $this->quoteIdent($row) . ' = ?';
        }

        //выборка элемента по идентификатору
        $statement = $this->dbh->prepare(
            "SELECT {$select} FROM {$table} WHERE {$where}"
        );
        if ($statement) {
            $this->setPrepared('select', $statement);
        }

        //обновление элемента
        $statement = $this->dbh->prepare(
            "UPDATE {$table} SET {$set} WHERE {$where}"
        );
        if ($statement) {
            $this->setPrepared('update', $statement);
        }

        return $this;
    }
}
