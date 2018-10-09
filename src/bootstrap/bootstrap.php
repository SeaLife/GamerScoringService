<?php
/** @noinspection SpellCheckingInspection */

use Logging\FileLogger;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

## define the root
define('__ROOT__', __DIR__ . "/..");

## load globals and config
include_once __DIR__ . '/globals.php';
include_once __DIR__ . '/../config.php';

## initialize some basics
error_reporting(0);
session_start();

## is debug enabled?
$webDebugEnabled = toBool(envvar("SYSTEM_DEBUG", "false"));


## load composer
include_once envvar("COMPOSE_LOCATION", __DIR__ . "/../../") . "vendor/autoload.php";

## register the master exception handling, use Symfonie's one if in debug mode,
## otherwise use a simple one to minify the error output for a potential hacker
if ($webDebugEnabled) {
    ErrorHandler::register();
    ExceptionHandler::register();
} else {
    set_exception_handler(function (Throwable $ex) {
        $exceptionLogger = FileLogger::getLogger("app.exhandler");

        $exceptionLogger->critical("Caught unhandled exception, {}", array($ex));

        echo "<link href='https://bootswatch.com/4/simplex/bootstrap.min.css' rel='stylesheet'>";
        echo "<br><br>";
        /** output actual error */
        echo "<div class='container'><div class='alert alert-danger'>";
        echo "There was a unexpected error while processing your requests. If this behaviour persists, contact the system administrator.";
        echo "";
        echo "</div></div>";
    });
}

## include local class loader and init file to initialize app base items (like db, cache, ...)
include_once __DIR__ . '/classloader.php';
include_once __DIR__ . '/init.php';