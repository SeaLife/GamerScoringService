<?php
/** @noinspection SpellCheckingInspection */

use Logging\FileLogger;

// loadup custom error handler :D
set_exception_handler(function (Throwable $ex) {
    $exceptionLogger = FileLogger::getLogger("app.exhandler");

    $exceptionLogger->critical("Caught unhandled exception, {}", array($ex));

    $debug = toBool(envvar("SYSTEM_DEBUG", "false"));

    if ($debug) {
        echo "<pre>";
        echo "** DEBUG MODE IS ENABLED " . date("d.m.Y H:i") . " **\n\n";
        echo get_class($ex) . ": " . $ex->getMessage();
        echo "\n";
        echo "\n";
        echo $ex->getTraceAsString();
        echo "</pre>";
    } else {
        echo "<link href='https://bootswatch.com/4/flatly/bootstrap.min.css' rel='stylesheet'>";
        echo "<br><br>";
        /** output actual error */
        echo "<div class='container'><div class='alert alert-danger'>";
        echo "There was a unexpected error while processing your requests. If this behaviour persists, contact the system administrator.";
        echo "";
        echo "</div></div>";
    }
});

include 'app.php';