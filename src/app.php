<?php


use Globals\Routing;

Routing::getInstance()->init();

Routing::getInstance()->findRoutes();

Routing::getInstance()->exec();