<?php /** @noinspection SpellCheckingInspection */

namespace WebService;

use Globals\Routing\RouteExecutor;
use Output\Http\Content;
use Output\Http\OutputManager;

/**
 * Handler for returning the swagger docs at /api/docs-ui
 *
 * Route defined in 'routes.yml'
 */
class SwaggerUiHandler implements RouteExecutor {
    public function doRun ($method, $vars) {
        $content = new Content("web/swagger-ui.html.twig");
        $content->assign("SWAGGER_VERSION", "3.19.0");
        $content->assign("URL", "/api/docs");

        OutputManager::getInstance()->display($content);
    }
}