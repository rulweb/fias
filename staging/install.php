<?php

use marvin255\fias\Pipe;
use marvin255\fias\service\bag\Bag;
use marvin255\fias\service\console\Logger;
use marvin255\fias\service\database\Mysql;
use marvin255\fias\service\downloader\Curl;
use marvin255\fias\service\fias\UpdateSericeSoap;
use marvin255\fias\service\filesystem\Directory;
use marvin255\fias\service\unpacker\Rar;
use marvin255\fias\service\xml\Reader;
use marvin255\fias\ServiceLocator;
use marvin255\fias\task\AddPrimaryKeys;
use marvin255\fias\task\Cleanup;
use marvin255\fias\task\DropAndCreateTables;
use marvin255\fias\task\DownloadCompleteData;
use marvin255\fias\task\Unpack;
use marvin255\fias\TaskFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$config = require_once __DIR__ . '/config.php';

$dir = new Directory($config['work_dir']);
$dir->create();

$serviceLocator = new ServiceLocator;
$serviceLocator->register(new Logger);
$serviceLocator->register(new Bag);
$serviceLocator->register(new UpdateSericeSoap);
$serviceLocator->register($dir);
$serviceLocator->register(new Curl);
$serviceLocator->register(new Rar);
$serviceLocator->register(new Reader);
$serviceLocator->register(new Mysql($config['database']));

$factory = new TaskFactory;

$pipe = new Pipe($serviceLocator);
$pipe->pipeTask(new DropAndCreateTables);
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
$pipe->setCleanupTask(new AddPrimaryKeys);
$pipe->setCleanupTask(new Cleanup);
$pipe->run();
