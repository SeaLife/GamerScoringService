<?php

namespace Api;

use Globals\Routing;
use Globals\WebResponder;
use OpenApi\Annotations\Get;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\MediaType;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Server;
use OpenApi\Annotations\Tag;

/**
 * @Info(
 *     title="GamerScoring Service",
 *     version="0.1-BETA",
 *     license="MIT",
 *     description="tbd",
 *     @OpenApi(servers={@Server(url="http://localhost:8000")})
 * )
 *
 * @Tag(
 *     name="Debug Controller",
 *     description="Debug Actions"
 * )
 * @Tag(
 *     name="Service Interface",
 *     description="Service Actions"
 * )
 */
class DebugController {

    /**
     * @WebResponder(path="/api/debug/routes", name="test endpoint", method="GET", produces="application/json")
     * @Get(
     *     tags={"Debug Controller"},
     *     path="/api/debug/routes",
     *     @Response(response="200", description="OK"),
     *     @MediaType(mediaType="application/json")
     * )
     */
    public function debugEndpoints () {
        echo json_encode(Routing::getInstance()->getRoutes());
    }
}