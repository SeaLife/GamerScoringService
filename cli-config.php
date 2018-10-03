<?php

define("__ROOT__", __DIR__ . "/src");

include_once __ROOT__ . '/bootstrap/globals.php';
include_once __ROOT__ . '/bootstrap/classloader.php';

ClassLoader::init();

\Globals\DB::getInstance()->load(envvar("PROFILE", "dev"));

$entityManager = \Globals\DB::getInstance()->getEntityManager();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);