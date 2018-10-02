<?php


use Globals\DB;
use Globals\Routing;

DB::getInstance()->load(envvar("PROFILE", "dev"));

Routing::getInstance()->init();
Routing::getInstance()->findRoutes();
Routing::getInstance()->exec();