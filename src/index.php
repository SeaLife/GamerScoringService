<?php
/** @noinspection PhpIncludeInspection */

define("__ROOT__", __DIR__);

include_once __DIR__ . '/bootstrap/globals.php';
include_once __DIR__ . '/bootstrap/classloader.php';

session_start();

// default exception handler, will be used in bootstrap errors.
set_exception_handler(function (Throwable $ex) {
    echo "<pre>";
    echo get_class($ex) . ": " . $ex->getMessage();
    echo "\n";
    echo "\n";
    echo $ex->getTraceAsString();
    echo "</pre>";
});

include_once envvar("COMPOSE_LOCATION", __DIR__ . "/../") . "vendor/autoload.php";

ClassLoader::init();

include __DIR__ . '/config.php';
include __DIR__ . '/bootstrap/bootstrap.php';
include __DIR__ . '/app.php';