<?php

require_once dirname(__DIR__) . '/src/Autoloader.php';

use marvin255\fias\pipe\Pipe;
use marvin255\fias\pipe\Flow;
use marvin255\fias\job\GetUrl;
use marvin255\fias\job\Download;
use marvin255\fias\job\UnpackArchive;
use marvin255\fias\utils\filesystem\Directory;
use marvin255\fias\utils\transport\Curl;
use marvin255\fias\utils\unpacker\Rar;

$fiasWsdl = 'http://fias.nalog.ru/WebServices/Public/DownloadService.asmx?WSDL';
$workDir = new Directory(sys_get_temp_dir() . '/fias_loader');

$pipe = new Pipe;
$pipe->addJob(new GetUrl(new SoapClient($fiasWsdl)));
$pipe->addJob(new Download($workDir, new Curl));
$pipe->addJob(new UnpackArchive($workDir, new Rar));

$flow = new Flow;
$result = $pipe->run($flow);
