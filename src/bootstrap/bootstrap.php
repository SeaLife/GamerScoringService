<?php
/** @noinspection SpellCheckingInspection */

use Logging\FileLogger;
use Symfony\Component\Debug\ExceptionHandler;


$__DEBUG = toBool(envvar("SYSTEM_DEBUG", "false"));

if ($__DEBUG) {
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