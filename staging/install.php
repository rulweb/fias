<?php

use marvin255\fias\Pipe;
use marvin255\fias\ServiceLocator;
use marvin255\fias\service\fias\UpdateSericeSoap;
use marvin255\fias\service\downloader\Curl;
use marvin255\fias\service\unpacker\Rar;
use marvin255\fias\service\xml\Reader;
use marvin255\fias\service\database\Mysql;
use marvin255\fias\service\filesystem\Directory;
use marvin255\fias\service\console\Logger;
use marvin255\fias\service\bag\Bag;
use marvin255\fias\TaskFactory;
use marvin255\fias\task\DownloadCompleteData;
use marvin255\fias\task\Unpack;
use marvin255\fias\task\Cleanup;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$pdo = require_once __DIR__ . '/includes/db.php';

$dir = new Directory(__DIR__ . '/workdir');
$dir->create();

$serviceLocator = new ServiceLocator;
$serviceLocator->register(new Logger);
$serviceLocator->register(new Bag);
$serviceLocator->register(new UpdateSericeSoap);
$serviceLocator->register($dir);
$serviceLocator->register(new Curl);
$serviceLocator->register(new Rar);
$serviceLocator->register(new Reader);
$serviceLocator->register(new Mysql($pdo));

$factory = new TaskFactory;

$pipe = new Pipe($serviceLocator);
$pipe->pipeTask(new DownloadCompleteData);
$pipe->pipeTask(new Unpack);
$pipe->pipeTask($factory->inserter('ActualStatus', 'actual_statuses'));
$pipe->pipeTask($factory->inserter('CenterStatus', 'center_statuses'));
$pipe->pipeTask($factory->inserter('CurrentStatus', 'current_statuses'));
$pipe->pipeTask($factory->inserter('EstateStatus', 'estate_statuses'));
$pipe->pipeTask($factory->inserter('FlatType', 'flat_types'));
$pipe->pipeTask($factory->inserter('IntervalStatus', 'interval_statuses'));
$pipe->pipeTask($factory->inserter('NormativeDocumentType', 'normative_document_types'));
$pipe->pipeTask($factory->inserter('OperationStatus', 'operation_statuses'));
$pipe->pipeTask($factory->inserter('RoomType', 'room_types'));
$pipe->pipeTask($factory->inserter('AddressObjectType', 'address_object_types'));
$pipe->pipeTask($factory->inserter('StructureStatus', 'structure_statuses'));
$pipe->pipeTask($factory->inserter('HouseStateStatus', 'house_state_statuses'));
$pipe->pipeTask($factory->inserter('Object', 'address_objects'));
$pipe->pipeTask($factory->inserter('Stead', 'steads'));
$pipe->pipeTask($factory->inserter('NormativeDocument', 'normative_documents'));
$pipe->pipeTask($factory->inserter('House', 'houses'));
$pipe->pipeTask($factory->inserter('Room', 'rooms'));
$pipe->setCleanupTask(new Cleanup);
$pipe->run();

$start = microtime(true);
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
echo 'Indexes: ' . round(microtime(true) - $start) . "s\r\n";
