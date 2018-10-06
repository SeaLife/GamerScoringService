<?php

use Globals\Cache;
use Globals\ConfigurationManager;
use Globals\DB;

ConfigurationManager::getInstance()->init();

DB::getInstance()->load(envvar("PROFILE", "dev"));

Cache::getInstance();