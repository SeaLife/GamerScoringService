<?php

namespace WebService;

use Globals\RouteExecutor;

class RootHandler implements RouteExecutor {

    public function doRun ($method, $vars) {
        echo "Hello World";
    }
}