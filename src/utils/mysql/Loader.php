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
     */
    protected function find(array $dataSet)
    {
        $where = $this->getWhereRows($dataSet);
        $sth = $this->getPrepared('select');
        $sth->execute($where);
        $res = $sth->fetch(PDO::FETCH_ASSOC);

        return $res ?: false;
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
        $return['select'] = $this->dbh->prepare(
            "SELECT {$select} FROM {$table} WHERE {$where}"
        );
        //обновление элемента
        $return['update'] = $this->dbh->prepare(
            "UPDATE {$table} SET {$set} WHERE {$where}"
        );

        return $return;
    }
}
