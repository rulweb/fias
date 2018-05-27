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
            NAME varchar(100) not null,
            SHORTNAME varchar(20) not null,
            PRIMARY KEY(STRSTATID)
        );
    ',
        'address_object_types' => 'CREATE TABLE address_object_types
        (
            KOD_T_ST int(4) unsigned not null,
            LEVEL int(5) unsigned not null,
            SOCRNAME varchar(50) not null,
            SCNAME varchar(10) not null,
            PRIMARY KEY(KOD_T_ST)
        );
    ',
        'room_types' => 'CREATE TABLE room_types
        (
            RMTYPEID int(11) unsigned not null,
            NAME varchar(100) not null,
            SHORTNAME varchar(20) not null,
            PRIMARY KEY(RMTYPEID)
        );
    ',
        'operation_statuses' => 'CREATE TABLE operation_statuses
        (
            OPERSTATID int(11) unsigned not null,
            NAME varchar(100) not null,
            PRIMARY KEY(OPERSTATID)
        );
    ',
        'normative_document_types' => 'CREATE TABLE normative_document_types
        (
            NDTYPEID int(11) unsigned not null,
            NAME varchar(100) not null,
            PRIMARY KEY(NDTYPEID)
        );
    ',
        'interval_statuses' => 'CREATE TABLE interval_statuses
        (
            INTVSTATID int(11) unsigned not null,
            NAME varchar(100) not null,
            PRIMARY KEY(INTVSTATID)
        );
    ',
        'house_state_statuses' => 'CREATE TABLE house_state_statuses
        (
            HOUSESTID int(11) unsigned not null,
            NAME varchar(100) not null,
            PRIMARY KEY(HOUSESTID)
        );
    ',
        'flat_types' => 'CREATE TABLE flat_types
        (
            FLTYPEID int(11) unsigned not null,
            NAME varchar(100) not null,
            SHORTNAME varchar(20) not null,
            PRIMARY KEY(FLTYPEID)
        );
    ',
        'estate_statuses' => 'CREATE TABLE estate_statuses
        (
            ESTSTATID int(11) unsigned not null,
            NAME varchar(100) not null,
            PRIMARY KEY(ESTSTATID)
        );
    ',
        'current_statuses' => 'CREATE TABLE current_statuses
        (
            CURENTSTID int(11) unsigned not null,
            NAME varchar(100) not null,
            PRIMARY KEY(CURENTSTID)
        );
    ',
        'center_statuses' => 'CREATE TABLE center_statuses
        (
            CENTERSTID int(11) unsigned not null,
            NAME varchar(100) not null,
            PRIMARY KEY(CENTERSTID)
        );
    ',
        'actual_statuses' => 'CREATE TABLE actual_statuses
        (
            ACTSTATID int(11) unsigned not null,
            NAME varchar(100) not null,
            PRIMARY KEY(ACTSTATID)
        );
    ',
        'steads' => 'CREATE TABLE steads
        (
            STEADGUID varchar(36) not null,
            `NUMBER` varchar(255) not null,
            REGIONCODE varchar(2) not null,
            POSTALCODE varchar(6) not null,
            IFNSFL varchar(4) not null,
            IFNSUL varchar(4) not null,
            OKATO varchar(11) not null,
            OKTMO varchar(11) not null,
            PARENTGUID varchar(36) not null,
            STEADID varchar(36) not null,
            OPERSTATUS varchar(255) not null,
            STARTDATE date,
            UPDATEDATE date,
            ENDDATE date,
            LIVESTATUS varchar(255) not null,
            DIVTYPE varchar(255) not null,
            NORMDOC varchar(36) not null,
            PRIMARY KEY(STEADGUID)
        );
    ',
        'rooms' => 'CREATE TABLE rooms
        (
            ROOMID varchar(36) not null,
            ROOMGUID varchar(36) not null,
            HOUSEGUID varchar(36) not null,
            REGIONCODE varchar(2) not null,
            FLATNUMBER varchar(50) not null,
            FLATTYPE int(11) not null,
            POSTALCODE varchar(6) not null,
            UPDATEDATE date,
            STARTDATE date,
            ENDDATE date,
            OPERSTATUS varchar(255) not null,
            LIVESTATUS varchar(255) not null,
            NORMDOC varchar(36) not null,
            PRIMARY KEY(ROOMID)
        );
    ',
        'normative_documents' => 'CREATE TABLE normative_documents
        (
            NORMDOCID varchar(36) not null,
            DOCNAME text not null,
            DOCDATE varchar(255) not null,
            DOCNUM varchar(255) not null,
            DOCTYPE varchar(255) not null,
            PRIMARY KEY(NORMDOCID)
        );
    ',
        'houses' => 'CREATE TABLE houses
        (
            HOUSEID varchar(36) not null,
            HOUSEGUID varchar(36) not null,
            AOGUID varchar(36) not null,
            HOUSENUM varchar(20) not null,
            STRSTATUS int(11) not null,
            ESTSTATUS int(11) not null,
            STATSTATUS int(11) not null,
            IFNSFL varchar(4) not null,
            IFNSUL varchar(4) not null,
            OKATO varchar(11) not null,
            OKTMO varchar(11) not null,
            POSTALCODE varchar(6) not null,
            STARTDATE date,
            ENDDATE date,
            UPDATEDATE date,
            COUNTER int(11) not null,
            DIVTYPE int(11) not null,
            PRIMARY KEY(HOUSEID)
        );
    ',
        'address_objects' => 'CREATE TABLE address_objects
        (
            AOID varchar(36) not null,
            AOGUID varchar(36) not null,
            PARENTGUID varchar(36) not null,
            NEXTID varchar(36) not null,
            FORMALNAME varchar(120) not null,
            OFFNAME varchar(120) not null,
            SHORTNAME varchar(10) not null,
            AOLEVEL int(11) unsigned not null,
            REGIONCODE varchar(2) not null,
            AREACODE varchar(3) not null,
            AUTOCODE varchar(1) not null,
            CITYCODE varchar(3) not null,
            CTARCODE varchar(3) not null,
            PLACECODE varchar(3) not null,
            PLANCODE varchar(4) not null,
            STREETCODE varchar(4) not null,
            EXTRCODE varchar(4) not null,
            SEXTCODE varchar(3) not null,
            PLAINCODE varchar(15) not null,
            CURRSTATUS int(11) not null,
            ACTSTATUS int(11) not null,
            LIVESTATUS int(11) not null,
            CENTSTATUS int(11) not null,
            OPERSTATUS int(11) not null,
            IFNSFL varchar(4) not null,
            IFNSUL varchar(4) not null,
            TERRIFNSFL varchar(4) not null,
            TERRIFNSUL varchar(4) not null,
            OKATO varchar(11) not null,
            OKTMO varchar(11) not null,
            POSTALCODE varchar(6) not null,
            STARTDATE date,
            ENDDATE date,
            UPDATEDATE date,
            DIVTYPE int(11) not null,
            PRIMARY KEY(AOID)
        );
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