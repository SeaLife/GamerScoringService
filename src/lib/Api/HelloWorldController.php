<?php

namespace Api;

use OpenApi\Annotations\Contact;
use OpenApi\Annotations\Get;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\Response;

/**
 * Class HelloWorldController
 * @package Api
 *
 * @Info(
 *     version="1.0",
 *     title="Hello World Controller",
 *     license="MIT",
 *     contact=@Contact(url="https://r3ktm8.de", name="GamingScoring Service")
 * )
 */
class HelloWorldController {

    /**
     * @Get(path="/api/test", @Response(response="200", description="Request Successfully"))
     */
    public function test () {

    }
}