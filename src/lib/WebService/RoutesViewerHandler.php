<?php

namespace WebService;

use Globals\RouteExecutor;
use Globals\Routing;

class RoutesViewerHandler implements RouteExecutor {

    public function doRun ($method, $vars) {
        header("Content-Type: application/json");
        echo json_encode(Routing::getInstance()->getRoutes());
    }
}