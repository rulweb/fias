<?php

$pdo = include __DIR__ . '/db.php';

$pdo->exec('ALTER TABLE actual_statuses ADD PRIMARY KEY(ACTSTATID)');
$pdo->exec('ALTER TABLE center_statuses ADD PRIMARY KEY(CENTERSTID)');
$pdo->exec('ALTER TABLE current_statuses ADD PRIMARY KEY(CURENTSTID)');
$pdo->exec('ALTER TABLE estate_statuses ADD PRIMARY KEY(ESTSTATID)');
$pdo->exec('ALTER TABLE flat_types ADD PRIMARY KEY(FLTYPEID)');
$pdo->exec('ALTER TABLE interval_statuses ADD PRIMARY KEY(INTVSTATID)');
$pdo->exec('ALTER TABLE normative_document_types ADD PRIMARY KEY(NDTYPEID)');
$pdo->exec('ALTER TABLE operation_statuses ADD PRIMARY KEY(OPERSTATID)');
$pdo->exec('ALTER TABLE room_types ADD PRIMARY KEY(RMTYPEID)');
$pdo->exec('ALTER TABLE address_object_types ADD PRIMARY KEY(KOD_T_ST)');
$pdo->exec('ALTER TABLE structure_statuses ADD PRIMARY KEY(STRSTATID)');
$pdo->exec('ALTER TABLE house_state_statuses ADD PRIMARY KEY(HOUSESTID)');
$pdo->exec('ALTER TABLE address_objects ADD PRIMARY KEY(AOID)');
$pdo->exec('ALTER TABLE steads ADD PRIMARY KEY(STEADGUID)');
$pdo->exec('ALTER TABLE normative_documents ADD PRIMARY KEY(NORMDOCID)');
$pdo->exec('ALTER TABLE houses ADD PRIMARY KEY(HOUSEID)');
$pdo->exec('ALTER TABLE rooms ADD PRIMARY KEY(ROOMID)');

return $pdo;
