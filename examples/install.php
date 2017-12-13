<?php

/**
 * Установка полной базы фиас.
 */
use marvin255\fias\pipe\Pipe;
use marvin255\fias\pipe\Flow;
use marvin255\fias\job\GetUrl;
use marvin255\fias\job\Download;
use marvin255\fias\job\UnpackArchive;
use marvin255\fias\utils\filesystem\Directory;
use marvin255\fias\utils\transport\Curl;
use marvin255\fias\utils\unpacker\Rar;
use marvin255\fias\FiasJobFactory;

require_once dirname(__DIR__) . '/src/Autoloader.php';

$fiasWsdl = 'http://fias.nalog.ru/WebServices/Public/DownloadService.asmx?WSDL';

$dbh = require_once __DIR__ . '/includes/refresh_db.php';
$workDir = new Directory(__DIR__ . '/fias_data');
$factory = new FiasJobFactory($dbh, $workDir);

$pipe = new Pipe;
//$pipe->addJob(new GetUrl(new SoapClient($fiasWsdl)));
//$pipe->addJob(new Download($workDir, new Curl));
//$pipe->addJob(new UnpackArchive($workDir, new Rar));
$pipe->addJob($factory->inserter('ActualStatus', 'actual_statuses'));
$pipe->addJob($factory->inserter('CenterStatus', 'center_statuses'));
$pipe->addJob($factory->inserter('CurrentStatus', 'current_statuses'));
$pipe->addJob($factory->inserter('EstateStatus', 'estate_statuses'));
$pipe->addJob($factory->inserter('FlatType', 'flat_types'));
$pipe->addJob($factory->inserter('HouseStateStatus', 'house_state_statuses'));
$pipe->addJob($factory->inserter('IntervalStatus', 'interval_statuses'));
$pipe->addJob($factory->inserter('NormativeDocumentType', 'normative_document_types'));
$pipe->addJob($factory->inserter('OperationStatus', 'operation_statuses'));
$pipe->addJob($factory->inserter('RoomType', 'room_types'));
$pipe->addJob($factory->inserter('AddressObjectType', 'address_object_types'));
$pipe->addJob($factory->inserter('StructureStatus', 'structure_statuses'));
$pipe->run(new Flow);
