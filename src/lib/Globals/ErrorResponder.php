<?php

namespace Globals;

use Output\Http\Content;
use Output\Http\OutputManager;

/**
 * Responder for 404 pages
 */
class ErrorResponder {
    public static function error404 () {
        self::error(404);
    }
    public static function error403 () {
        self::error(403);
    }

    private static function error($code) {
        http_response_code($code);
        OutputManager::getInstance()->display(new Content("errors/$code.html.twig", array()));
    }
}