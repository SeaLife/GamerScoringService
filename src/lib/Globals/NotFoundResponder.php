<?php

namespace Globals;

/**
 * Responder for 404 pages
 */
class NotFoundResponder {
    public static function run() {
        http_response_code(404);
    }
}