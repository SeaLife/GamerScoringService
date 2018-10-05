<?php


use Globals\ConfigurationManager;
use Globals\DB;
use Globals\Routing;

ConfigurationManager::getInstance()->init();

DB::getInstance()->load(envvar("PROFILE", "dev"));

Routing::getInstance()->init();
Routing::getInstance()->findRoutes();
Routing::getInstance()->exec();