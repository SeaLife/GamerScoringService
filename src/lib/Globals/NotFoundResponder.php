<?php

namespace Globals;

use Output\Http\Content;
use Output\Http\OutputManager;

/**
 * Responder for 404 pages
 */
class NotFoundResponder {
    public static function run () {
        http_response_code(404);

        OutputManager::getInstance()->display(new Content("errors/404.html.twig", array()));
    }
}