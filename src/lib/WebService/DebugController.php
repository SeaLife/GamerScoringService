<?php

namespace WebService;

use Globals\Annotations\WebResponder;
use Globals\ErrorResponder;
use Globals\Routing;
use OpenApi\Annotations\Get;
use OpenApi\Annotations\MediaType;
use OpenApi\Annotations\Response;
use Output\Http\Content;
use Output\Http\OutputManager;


class DebugController {

    /**
     * @WebResponder(path="/api/debug/routes", name="Returns all configured routes as json/html", method="GET", requiredPermission="DX")
     * @Get(
     *     tags={"Debug Controller"},
     *     path="/api/debug/routes",
     *     description="Required Permissions: \n- ``ROLE_ADMIN``",
     *     @Response(response="200", description="OK"),
     *     @MediaType(mediaType="application/json")
     * )
     */
    public function debugEndpoints () {
        if (!toBool(envvar("SYSTEM_DEBUG")) && envvar("PROFILE") != "dev") {
            ErrorResponder::error404();
            return;
        }

        $headers = getallheaders();
        $accept  = orv($headers["Accept"], "text/html");

        if (strpos($accept, 'text/html') !== FALSE) {
            OutputManager::getInstance()->display(new Content("debug/routes.html.twig", array("routes" => Routing::getInstance()->getRoutes())));
        } else {
            header("Content-Type: application/json");
            echo json_encode(Routing::getInstance()->getRoutes());
        }
    }
}