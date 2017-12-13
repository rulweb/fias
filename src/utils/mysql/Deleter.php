<?php

namespace marvin255\fias\utils\mysql;

use PDO;

/**
 * Удаление записей в mysql.
 */
class Deleter extends Base
{
    /**
     * Количество элементов для общего запроса удаления.
     *
     * @var int
     */
    protected $deleteCount = null;
    /**
     * Текущий буффер элементов для общего запроса удаления.
     *
     * @var array
     */
    protected $deleteBuffer = [];

    /**
     * {@inheritdoc}
     *
     * @param int $deleteCount Размер стека данных для общего запроса удаления
     */
    public function __construct(PDO $dbh, $table, $primary, array $rows, $deleteCount = 100)
    {
        parent::__construct($dbh, $table, $primary, $rows);

        $this->deleteCount = $deleteCount;

        $this->createPrepared();
    }

    /**
     * @inheritdoc
     *
     * Записывает каждый набор данных в базу данных в качестве новой строки.
     */
    public function process(array $data)
    {
        $this->delete($data);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * Перед закрытием нужно проверить очередь общего запроса удаления и удалить
     * то, что осталось.
     */
    public function close()
    {
        if ($this->deleteBuffer) {
            foreach ($this->deleteBuffer as $item) {
                $this->getPrepared('delete')->execute($this->getWhereRows($item));
            }
            $this->deleteBuffer = [];
        }

        return $this;
    }

    /**
     * Удаляет указанный набор данных из базы данных.
     *
     * Сначала добавляем набор в очередь, как только очередь достигает
     * указанной длины, удаляем данные из БД одним запросом всю очередь.
     *
     * @param array $dataSet
     */
    protected function delete(array $dataSet)
    {
        $this->deleteBuffer[] = $dataSet;

        if (count($this->deleteBuffer) === (int) $this->deleteCount) {
            $delete = [];
            foreach ($this->deleteBuffer as $deleteItem) {
                $delete = array_merge($delete, $this->getWhereRows($deleteItem));
            }
            $this->getPrepared('bulk_delete')->execute($delete);
            $this->deleteBuffer = [];
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

        $delete = '';
        foreach ($this->primary as $row) {
            $delete .= ($delete ? ' AND ' : '') . $this->quoteIdent($row) . ' = ?';
        }

        //удаление элемента
        $statement = $this->dbh->prepare(
            "DELETE FROM {$table} WHERE {$delete}"
        );
        if ($statement) {
            $this->setPrepared('delete', $statement);
        }

        //удаление сразу множества элементов
        $statement = $this->dbh->prepare(
            "DELETE FROM {$table} WHERE (" . implode(') OR (', array_fill(0, $this->deleteCount, $delete)) . ')'
        );
        if ($statement) {
            $this->setPrepared('bulk_delete', $statement);
        }

        return $this;
    }
}
