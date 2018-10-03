<?php

namespace Globals\Routing;

interface RouteExecutor {
    public function doRun ($method, $vars);
}