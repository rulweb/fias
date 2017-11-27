<?php

$composerAutoloaderPath = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($composerAutoloaderPath)) {
    require_once $composerAutoloaderPath;
} else {
    require_once dirname(__DIR__) . '/src/Autoloader.php';
}
