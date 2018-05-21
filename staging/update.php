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
use marvin255\fias\task\DownloadDeltaData;
use marvin255\fias\task\Unpack;
use marvin255\fias\task\Cleanup;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dir = new Directory(__DIR__ . '/workdir');
$dir->create();

$serviceLocator = new ServiceLocator;
$serviceLocator->register(new Logger);
$serviceLocator->register((new Bag)->set(DownloadDeltaData::ARCHIVE_CURRENT_VERSION_PARAMETER, 440));
$serviceLocator->register(new UpdateSericeSoap);
$serviceLocator->register($dir);
$serviceLocator->register(new Curl);
$serviceLocator->register(new Rar);
$serviceLocator->register(new Reader);
$serviceLocator->register(new Mysql(require_once __DIR__ . '/includes/db_primary.php'));

$factory = new TaskFactory;

$pipe = new Pipe($serviceLocator);
$pipe->pipeTask(new DownloadDeltaData);
$pipe->pipeTask(new Unpack);
$pipe->pipeTask($factory->updater('ActualStatus', 'actual_statuses'));
$pipe->pipeTask($factory->updater('CenterStatus', 'center_statuses'));
$pipe->pipeTask($factory->updater('CurrentStatus', 'current_statuses'));
$pipe->pipeTask($factory->updater('EstateStatus', 'estate_statuses'));
$pipe->pipeTask($factory->updater('FlatType', 'flat_types'));
$pipe->pipeTask($factory->updater('IntervalStatus', 'interval_statuses'));
$pipe->pipeTask($factory->updater('NormativeDocumentType', 'normative_document_types'));
$pipe->pipeTask($factory->updater('OperationStatus', 'operation_statuses'));
$pipe->pipeTask($factory->updater('RoomType', 'room_types'));
$pipe->pipeTask($factory->updater('AddressObjectType', 'address_object_types'));
$pipe->pipeTask($factory->updater('StructureStatus', 'structure_statuses'));
$pipe->pipeTask($factory->updater('HouseStateStatus', 'house_state_statuses'));
$pipe->pipeTask($factory->updater('Object', 'address_objects'));
$pipe->pipeTask($factory->updater('Stead', 'steads'));
$pipe->pipeTask($factory->updater('NormativeDocument', 'normative_documents'));
$pipe->pipeTask($factory->updater('House', 'houses'));
$pipe->pipeTask($factory->updater('Room', 'rooms'));
$pipe->setCleanupTask(new Cleanup);
$pipe->run();
