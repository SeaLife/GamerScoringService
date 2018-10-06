<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Globals\DB;

define("__ROOT__", __DIR__ . "/src");

include_once __ROOT__ . '/bootstrap/globals.php';
include_once __ROOT__ . '/bootstrap/classloader.php';
include_once __ROOT__ . '/bootstrap/init.php';

$entityManager = DB::getInstance()->getEntityManager();
return ConsoleRunner::createHelperSet($entityManager);