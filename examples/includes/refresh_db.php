<?php

/**
 * Подключение базы данных и создание структуры таблиц.
 */
$dbh = new PDO('mysql:dbname=fias;host=localhost', 'root', 'password');

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
