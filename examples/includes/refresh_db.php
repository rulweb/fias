<?php

/**
 * Подключение базы данных и создание структуры таблиц.
 */
$dbh = new PDO('mysql:dbname=fias;host=localhost', 'root', 'password');

$dbh->exec('DROP TABLE IF EXISTS address_objects');
$dbh->exec('CREATE TABLE address_objects
    (
        AOID varchar(36) not null,
        AOGUID varchar(36) not null,
        PARENTGUID varchar(36) not null,
        NEXTID varchar(36) not null,
        FORMALNAME varchar(36) not null,
        OFFNAME varchar(36) not null,
        SHORTNAME varchar(36) not null,
        AOLEVEL int(11) unsigned not null,
        REGIONCODE varchar(36) not null,
        AREACODE varchar(36) not null,
        AUTOCODE varchar(36) not null,
        CITYCODE varchar(36) not null,
        CTARCODE varchar(36) not null,
        PLACECODE varchar(36) not null,
        PLANCODE varchar(36) not null,
        STREETCODE varchar(36) not null,
        EXTRCODE varchar(36) not null,
        SEXTCODE varchar(36) not null,
        PLAINCODE varchar(36) not null,
        CURRSTATUS varchar(36) not null,
        ACTSTATUS varchar(36) not null,
        LIVESTATUS varchar(36) not null,
        CENTSTATUS varchar(36) not null,
        OPERSTATUS varchar(36) not null,
        IFNSFL varchar(36) not null,
        IFNSUL varchar(36) not null,
        TERRIFNSFL varchar(36) not null,
        TERRIFNSUL varchar(36) not null,
        OKATO varchar(36) not null,
        OKTMO varchar(36) not null,
        OKTMO varchar(36) not null,
        POSTALCODE varchar(36) not null,
        STARTDATE date,
        ENDDATE date,
        UPDATEDATE date,
        DIVTYPE varchar(36) not null,
        PRIMARY KEY(AOID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS houses');
$dbh->exec('CREATE TABLE houses
    (
        HOUSEID varchar(36) not null,
        HOUSEGUID varchar(36) not null,
        AOGUID varchar(36) not null,
        HOUSENUM varchar(36) not null,
        STRSTATUS varchar(36) not null,
        ESTSTATUS varchar(36) not null,
        STATSTATUS varchar(36) not null,
        IFNSFL varchar(36) not null,
        IFNSUL varchar(36) not null,
        OKATO varchar(36) not null,
        OKTMO varchar(36) not null,
        POSTALCODE varchar(36) not null,
        STARTDATE date,
        ENDDATE date,
        UPDATEDATE date,
        COUNTER varchar(36) not null,
        DIVTYPE varchar(36) not null,
        PRIMARY KEY(HOUSEID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS normative_documents');
$dbh->exec('CREATE TABLE normative_documents
    (
        NORMDOCID varchar(36) not null,
        DOCNAME varchar(36) not null,
        DOCDATE date,
        DOCNUM varchar(36) not null,
        DOCTYPE varchar(36) not null,
        PRIMARY KEY(NORMDOCID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS rooms');
$dbh->exec('CREATE TABLE rooms
    (
        ROOMID varchar(36) not null,
        ROOMGUID varchar(36) not null,
        HOUSEGUID varchar(36) not null,
        REGIONCODE varchar(36) not null,
        FLATNUMBER varchar(36) not null,
        FLATTYPE varchar(36) not null,
        POSTALCODE varchar(36) not null,
        UPDATEDATE date,
        STARTDATE date,
        ENDDATE date,
        OPERSTATUS varchar(36) not null,
        LIVESTATUS varchar(36) not null,
        NORMDOC varchar(36) not null,
        PRIMARY KEY(ROOMID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS steads');
$dbh->exec('CREATE TABLE steads
    (
        STEADGUID varchar(36) not null,
        `NUMBER` int(11) unsigned not null,
        REGIONCODE int(11) unsigned not null,
        POSTALCODE int(11) unsigned not null,
        IFNSFL int(11) unsigned not null,
        IFNSUL int(11) unsigned not null,
        OKATO int(11) unsigned not null,
        UPDATEDATE date,
        PARENTGUID varchar(36) not null,
        STEADID varchar(36) not null,
        OPERSTATUS int(11) unsigned not null,
        STARTDATE date,
        ENDDATE date,
        OKTMO int(11) unsigned not null,
        LIVESTATUS int(11) unsigned not null,
        DIVTYPE int(11) unsigned not null,
        NORMDOC varchar(36) not null,
        PRIMARY KEY(STEADGUID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS actual_statuses');
$dbh->exec('CREATE TABLE actual_statuses
    (
        ACTSTATID int(11) unsigned not null,
        NAME varchar(30) not null,
        PRIMARY KEY(ACTSTATID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS center_statuses');
$dbh->exec('CREATE TABLE center_statuses
    (
        CENTERSTID int(11) unsigned not null,
        NAME varchar(30) not null,
        PRIMARY KEY(CENTERSTID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS current_statuses');
$dbh->exec('CREATE TABLE current_statuses
    (
        CURENTSTID int(11) unsigned not null,
        NAME varchar(30) not null,
        PRIMARY KEY(CURENTSTID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS estate_statuses');
$dbh->exec('CREATE TABLE estate_statuses
    (
        ESTSTATID int(11) unsigned not null,
        NAME varchar(30) not null,
        PRIMARY KEY(ESTSTATID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS flat_types');
$dbh->exec('CREATE TABLE flat_types
    (
        FLTYPEID int(11) unsigned not null,
        NAME varchar(30) not null,
        SHORTNAME varchar(30) not null,
        PRIMARY KEY(FLTYPEID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS house_state_statuses');
$dbh->exec('CREATE TABLE house_state_statuses
    (
        HOUSESTID int(11) unsigned not null,
        NAME varchar(30) not null,
        PRIMARY KEY(HOUSESTID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS interval_statuses');
$dbh->exec('CREATE TABLE interval_statuses
    (
        INTVSTATID int(11) unsigned not null,
        NAME varchar(30) not null,
        PRIMARY KEY(INTVSTATID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS normative_document_types');
$dbh->exec('CREATE TABLE normative_document_types
    (
        NDTYPEID int(11) unsigned not null,
        NAME varchar(30) not null,
        PRIMARY KEY(NDTYPEID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS operation_statuses');
$dbh->exec('CREATE TABLE operation_statuses
    (
        OPERSTATID int(11) unsigned not null,
        NAME varchar(30) not null,
        PRIMARY KEY(OPERSTATID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS room_types');
$dbh->exec('CREATE TABLE room_types
    (
        RMTYPEID int(11) unsigned not null,
        NAME varchar(30) not null,
        SHORTNAME varchar(30) not null,
        PRIMARY KEY(RMTYPEID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS address_object_types');
$dbh->exec('CREATE TABLE address_object_types
    (
        KOD_T_ST int(11) unsigned not null,
        LEVEL int(5) unsigned not null,
        SOCRNAME varchar(30) not null,
        SCNAME varchar(30) not null,
        PRIMARY KEY(KOD_T_ST)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

$dbh->exec('DROP TABLE IF EXISTS structure_statuses');
$dbh->exec('CREATE TABLE structure_statuses
    (
        STRSTATID int(11) unsigned not null,
        NAME varchar(30) not null,
        SHORTNAME varchar(30) not null,
        PRIMARY KEY(STRSTATID)
    ) CHARACTER SET utf8 COLLATE utf8_general_ci;
');

return $dbh;
