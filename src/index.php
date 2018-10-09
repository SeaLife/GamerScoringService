<?php

## load the bootstrap..
include_once __DIR__ . '/bootstrap/bootstrap.php';

## run the application
Globals\Routing::getInstance()->run();