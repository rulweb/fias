<?php

/**
 * Подключение базы данных и создание структуры таблиц.
 */
$dbh = new PDO('mysql:dbname=fias;host=localhost', 'root', 'password');

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
