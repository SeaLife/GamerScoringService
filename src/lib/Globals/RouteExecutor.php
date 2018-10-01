<?php

namespace Globals;

interface RouteExecutor {
    public function doRun ($method, $vars);
}