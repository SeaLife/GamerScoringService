<?php /** @noinspection SpellCheckingInspection */

namespace WebService;

use Globals\RouteExecutor;

/**
 * Handler for returning the swagger docs at /api/docs
 *
 * Route defined in 'routes.yml'
 */
class ApiHandler implements RouteExecutor {
    public function doRun ($method, $vars) {

        $headers = getallheaders();
        $accept  = orv($headers["Accept"], "application/json");
        $openapi = \OpenApi\scan(__DIR__ . "/../Api");

        if (strpos($accept, 'application/x-yaml') !== FALSE || strpos($accept, 'application/yml') !== FALSE) {
            header("Content-Type: application/x-yaml");
            echo $openapi->toYaml();
        } else {
            header("Content-Type: application/json");
            echo $openapi->toJson();
        }
    }
}