<?php

namespace marvin255\fias\task;

use marvin255\fias\service\database\DatabaseInterface;
use marvin255\fias\service\database\Mysql;
use marvin255\fias\TaskInterface;

class AddPrimaryKeys implements TaskInterface
{
    /**
     * @var \marvin255\fias\service\database\DatabaseInterface|Mysql
     */
    protected $database;

    /**
     * Запускает данную задачу на исполнение.
     *
     * @return bool
     * @throws \marvin255\fias\service\database\Exception
     */
    public function run(): bool
    {
        $this->database->exec('ALTER TABLE actual_statuses ADD PRIMARY KEY(ACTSTATID)');
        $this->database->exec('ALTER TABLE center_statuses ADD PRIMARY KEY(CENTERSTID)');
        $this->database->exec('ALTER TABLE current_statuses ADD PRIMARY KEY(CURENTSTID)');
        $this->database->exec('ALTER TABLE estate_statuses ADD PRIMARY KEY(ESTSTATID)');
        $this->database->exec('ALTER TABLE flat_types ADD PRIMARY KEY(FLTYPEID)');
        $this->database->exec('ALTER TABLE interval_statuses ADD PRIMARY KEY(INTVSTATID)');
        $this->database->exec('ALTER TABLE normative_document_types ADD PRIMARY KEY(NDTYPEID)');
        $this->database->exec('ALTER TABLE operation_statuses ADD PRIMARY KEY(OPERSTATID)');
        $this->database->exec('ALTER TABLE room_types ADD PRIMARY KEY(RMTYPEID)');
        $this->database->exec('ALTER TABLE address_object_types ADD PRIMARY KEY(KOD_T_ST)');
        $this->database->exec('ALTER TABLE structure_statuses ADD PRIMARY KEY(STRSTATID)');
        $this->database->exec('ALTER TABLE house_state_statuses ADD PRIMARY KEY(HOUSESTID)');
        $this->database->exec('ALTER TABLE address_objects ADD PRIMARY KEY(AOID)');
        $this->database->exec('ALTER TABLE steads ADD PRIMARY KEY(STEADGUID)');
        $this->database->exec('ALTER TABLE normative_documents ADD PRIMARY KEY(NORMDOCID)');
        $this->database->exec('ALTER TABLE houses ADD PRIMARY KEY(HOUSEID)');
        $this->database->exec('ALTER TABLE rooms ADD PRIMARY KEY(ROOMID)');
    }

    /**
     * Сеттер для объекта базы данных.
     *
     * @param \marvin255\fias\service\database\DatabaseInterface $database
     *
     * @return self
     */
    public function setDatabase(DatabaseInterface $database): AddPrimaryKeys
    {
        $this->database = $database;

        return $this;
    }

    /**
     * Возвращает описание задачи.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Add primary keys from tables";
    }
}