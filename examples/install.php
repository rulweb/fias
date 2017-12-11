<?php

/**
 * Установка полной базы фиас.
 */
use marvin255\fias\pipe\Pipe;
use marvin255\fias\pipe\Flow;
use marvin255\fias\job\GetUrl;
use marvin255\fias\job\Download;
use marvin255\fias\job\UnpackArchive;
use marvin255\fias\job\ReadAndProcess;
use marvin255\fias\utils\filesystem\Directory;
use marvin255\fias\utils\filesystem\FilterRegexp;
use marvin255\fias\utils\transport\Curl;
use marvin255\fias\utils\unpacker\Rar;
use marvin255\fias\utils\xml\Reader;
use marvin255\fias\utils\mysql\Inserter;

require_once dirname(__DIR__) . '/src/Autoloader.php';

$dbh = require_once __DIR__ . '/includes/refresh_db.php';
$fiasWsdl = 'http://fias.nalog.ru/WebServices/Public/DownloadService.asmx?WSDL';
$workDir = new Directory(__DIR__ . '/fias_data');

$pipe = new Pipe;
$pipe->addJob(new GetUrl(new SoapClient($fiasWsdl)));
$pipe->addJob(new Download($workDir, new Curl));
$pipe->addJob(new UnpackArchive($workDir, new Rar));
$pipe->addJob(new ReadAndProcess(
    $workDir,
    new Reader(
        '/StructureStatuses/StructureStatus',
        [
            'STRSTATID' => '@STRSTATID',
            'NAME' => '@NAME',
            'SHORTNAME' => '@SHORTNAME',
        ]
    ),
    new Inserter(
        $dbh,
        'structure_statuses',
        'STRSTATID',
        ['STRSTATID', 'NAME', 'SHORTNAME']
    ),
    [
        new FilterRegexp('.*_STRSTAT_.*\.XML'),
    ]
));
$pipe->run(new Flow);
