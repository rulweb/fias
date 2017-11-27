<?php

$composerAutoloaderPath = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($composerAutoloaderPath)) {
    require $composerAutoloaderPath;
} else {
    require dirname(__DIR__) . '/lib/Autoloader.php';
}
