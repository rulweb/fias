<?php

namespace marvin255\fias\task;

use Exception;
use marvin255\fias\service\database\DatabaseInterface;
use marvin255\fias\service\database\Mysql;
use marvin255\fias\TaskInterface;

class DropAndCreateTables implements TaskInterface
{
    /**
     * @var \marvin255\fias\service\database\DatabaseInterface|Mysql
     */
    protected $database;

    private $tables = [
        'structure_statuses' => 'CREATE TABLE structure_statuses
        (
            STRSTATID int(11) unsigned not null,
            NAME varchar(255) not null,
            SHORTNAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'address_object_types' => 'CREATE TABLE address_object_types
        (
            KOD_T_ST int(11) unsigned not null,
            LEVEL int(5) unsigned not null,
            SOCRNAME varchar(255) not null,
            SCNAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'room_types' => 'CREATE TABLE room_types
        (
            RMTYPEID int(11) unsigned not null,
            NAME varchar(255) not null,
            SHORTNAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'operation_statuses' => 'CREATE TABLE operation_statuses
        (
            OPERSTATID int(11) unsigned not null,
            NAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'normative_document_types' => 'CREATE TABLE normative_document_types
        (
            NDTYPEID int(11) unsigned not null,
            NAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'interval_statuses' => 'CREATE TABLE interval_statuses
        (
            INTVSTATID int(11) unsigned not null,
            NAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'house_state_statuses' => 'CREATE TABLE house_state_statuses
        (
            HOUSESTID int(11) unsigned not null,
            NAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'flat_types' => 'CREATE TABLE flat_types
        (
            FLTYPEID int(11) unsigned not null,
            NAME varchar(255) not null,
            SHORTNAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'estate_statuses' => 'CREATE TABLE estate_statuses
        (
            ESTSTATID int(11) unsigned not null,
            NAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'current_statuses' => 'CREATE TABLE current_statuses
        (
            CURENTSTID int(11) unsigned not null,
            NAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'center_statuses' => 'CREATE TABLE center_statuses
        (
            CENTERSTID int(11) unsigned not null,
            NAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'actual_statuses' => 'CREATE TABLE actual_statuses
        (
            ACTSTATID int(11) unsigned not null,
            NAME varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'steads' => 'CREATE TABLE steads
        (
            STEADGUID varchar(255) not null,
            `NUMBER` varchar(255) not null,
            REGIONCODE varchar(255) not null,
            POSTALCODE varchar(255) not null,
            IFNSFL varchar(255) not null,
            IFNSUL varchar(255) not null,
            OKATO varchar(255) not null,
            UPDATEDATE varchar(255) not null,
            PARENTGUID varchar(255) not null,
            STEADID varchar(255) not null,
            OPERSTATUS varchar(255) not null,
            STARTDATE varchar(255) not null,
            ENDDATE varchar(255) not null,
            OKTMO varchar(255) not null,
            LIVESTATUS varchar(255) not null,
            DIVTYPE varchar(255) not null,
            NORMDOC varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'rooms' => 'CREATE TABLE rooms
        (
            ROOMID varchar(255) not null,
            ROOMGUID varchar(255) not null,
            HOUSEGUID varchar(255) not null,
            REGIONCODE varchar(255) not null,
            FLATNUMBER varchar(255) not null,
            FLATTYPE varchar(255) not null,
            POSTALCODE varchar(255) not null,
            UPDATEDATE varchar(255) not null,
            STARTDATE varchar(255) not null,
            ENDDATE varchar(255) not null,
            OPERSTATUS varchar(255) not null,
            LIVESTATUS varchar(255) not null,
            NORMDOC varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'normative_documents' => 'CREATE TABLE normative_documents
        (
            NORMDOCID varchar(255) not null,
            DOCNAME text not null,
            DOCDATE varchar(255) not null,
            DOCNUM varchar(255) not null,
            DOCTYPE varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
        'houses' => 'CREATE TABLE houses
        (
            HOUSEID varchar(255) not null,
            HOUSEGUID varchar(255) not null,
            AOGUID varchar(255) not null,
            HOUSENUM varchar(255) not null,
            STRSTATUS varchar(255) not null,
            ESTSTATUS varchar(255) not null,
            STATSTATUS varchar(255) not null,
            IFNSFL varchar(255) not null,
            IFNSUL varchar(255) not null,
            OKATO int(15) not null,
            OKTMO varchar(255) not null,
            POSTALCODE varchar(255) not null,
            STARTDATE varchar(255) not null,
            ENDDATE varchar(255) not null,
            UPDATEDATE varchar(255) not null,
            COUNTER varchar(255) not null,
            DIVTYPE varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci PARTITION BY LINEAR HASH (OKATO) PARTITIONS 4;
    ',
        'address_objects' => 'CREATE TABLE address_objects
        (
            AOID varchar(255) not null,
            AOGUID varchar(255) not null,
            PARENTGUID varchar(255) not null,
            NEXTID varchar(255) not null,
            FORMALNAME varchar(255) not null,
            OFFNAME varchar(255) not null,
            SHORTNAME varchar(255) not null,
            AOLEVEL int(11) unsigned not null,
            REGIONCODE varchar(255) not null,
            AREACODE varchar(255) not null,
            AUTOCODE varchar(255) not null,
            CITYCODE varchar(255) not null,
            CTARCODE varchar(255) not null,
            PLACECODE varchar(255) not null,
            PLANCODE varchar(255) not null,
            STREETCODE varchar(255) not null,
            EXTRCODE varchar(255) not null,
            SEXTCODE varchar(255) not null,
            PLAINCODE varchar(255) not null,
            CURRSTATUS varchar(255) not null,
            ACTSTATUS varchar(255) not null,
            LIVESTATUS varchar(255) not null,
            CENTSTATUS varchar(255) not null,
            OPERSTATUS varchar(255) not null,
            IFNSFL varchar(255) not null,
            IFNSUL varchar(255) not null,
            TERRIFNSFL varchar(255) not null,
            TERRIFNSUL varchar(255) not null,
            OKATO varchar(255) not null,
            OKTMO varchar(255) not null,
            POSTALCODE varchar(255) not null,
            STARTDATE date,
            ENDDATE date,
            UPDATEDATE date,
            DIVTYPE varchar(255) not null
        ) CHARACTER SET utf8 COLLATE utf8_general_ci;
    ',
    ];

    /**
     * Запускает данную задачу на исполнение.
     *
     * @return bool
     * @throws Exception
     */
    public function run(): bool
    {
        foreach ($this->tables as $tableName => $createSql) {
            $this->database->dropTable($tableName);
            $this->database->exec($createSql);
        }

        return true;
    }

    /**
     * Сеттер для объекта базы данных.
     *
     * @param \marvin255\fias\service\database\DatabaseInterface $database
     *
     * @return self
     */
    public function setDatabase(DatabaseInterface $database): DropAndCreateTables
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
        return "Drop and create all tables";
    }
}