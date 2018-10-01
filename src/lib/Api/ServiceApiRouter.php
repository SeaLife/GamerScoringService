<?php

namespace Api;

use Globals\WebResponder;
use OpenApi\Annotations\Get;
use OpenApi\Annotations\Response;

class ServiceApiRouter {

    /**
     * @Get(path="/api/test", @Response(response="200", description="Request Successfully"), tags={"Service Interface"})
     * @WebResponder(path="GET", path="/api/test")
     */
    public function test () {
        echo "Hallo :D";
    }

    /**
     * @Get(path="/debug/routes", @Response(response="200", description="Request Successfully"), tags={"Service Interface"})
     * @WebResponder(path="GET", path="/debug/routes_dev")
     */
    public function getRoutes () {
        echo "Running 'routes/dev'";
    }
}