<?php

namespace WebService;

use Globals\Routing\RouteExecutor;
use Output\Http\Content;
use Output\Http\OutputManager;

/**
 * Handler for returning the root document at /
 *
 * Route defined in 'routes.yml'
 */
class RootHandler implements RouteExecutor {
    public function doRun ($method, $vars) {
        $content = new Content("web/root.html.twig");

        OutputManager::getInstance()->display($content);
    }
}