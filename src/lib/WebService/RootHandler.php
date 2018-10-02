<?php

namespace WebService;

use Globals\RouteExecutor;

/**
 * Handler for returning the root document at /
 *
 * Route defined in 'routes.yml'
 */
class RootHandler implements RouteExecutor {
    public function doRun ($method, $vars) {
        echo "Hello World";
    }
}