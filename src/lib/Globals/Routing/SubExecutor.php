<?php

namespace Globals\Routing;

use Globals\Annotations\WebResponder;

class SubExecutor implements RouteExecutor {

    /** @var $instance RouteExecutor */
    private $instance;
    /** @var $method \ReflectionMethod */
    private $method;
    /** @var $responder WebResponder */
    private $responder = NULL;

    public function __construct ($instance, \ReflectionMethod $method, WebResponder $responder) {
        $this->instance  = $instance;
        $this->method    = $method;
        $this->responder = $responder;
    }

    public function doRun ($method, $vars) {
        if (!empty($this->responder->produces)) header("Content-Type: " . $this->responder->produces);

        $this->method->invoke($this->instance, $vars);
    }
}