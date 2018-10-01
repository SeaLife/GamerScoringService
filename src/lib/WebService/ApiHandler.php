<?php /** @noinspection SpellCheckingInspection */

namespace WebService;

use Globals\RouteExecutor;

class ApiHandler implements RouteExecutor {

    public function doRun ($method, $vars) {
        header("Content-Type: application/json");
        $openapi = \OpenApi\scan(__DIR__ . "/../Api");

        echo $openapi->toJson();
    }
}