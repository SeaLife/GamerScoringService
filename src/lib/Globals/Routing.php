<?php

namespace Globals;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;
use Util\SingletonFactory;

class Routing extends SingletonFactory {

    private $routes = array();

    public function getRoutes () {
        return $this->routes;
    }

    public function addRoute ($route, RouteExecutor $executor, $method = "GET") {
        array_push($this->routes, array(
            "route"    => $route,
            "executor" => $executor,
            "method"   => $method
        ));
    }

    public function init () {
        $routes = Yaml::parseFile(__DIR__ . "/../../../config/routes.yml");

        foreach ($routes["routes"] as $k => $v) {
            if (class_exists($v["handler"])) {
                $this->addRoute($v["path"], new $v["handler"](), orv(@$v["method"], "GET"));
            } else {
                $this->getLogger()->warning("Executor '{}' for Path '{}'@'{}' does not exist", array($v["handler"], $v["name"], $v["path"]));
            }
        }
    }

    public function exec () {
        $router = \FastRoute\simpleDispatcher(function (RouteCollector $collector) {
            foreach ($this->routes as $route) {
                $collector->addRoute($route["method"], $route["route"], $route["executor"]);
            }
        });

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri        = $_SERVER['REQUEST_URI'];

        if (FALSE !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        if (str_startswith($uri, "/index.php")) $uri = substr($uri, strlen("/index.php"), strlen($uri));
        $uri = rawurldecode($uri);

        $routeInfo = $router->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            default:
            case Dispatcher::NOT_FOUND:
                throw new InvalidArgumentException("Page not found");
                break;

            case Dispatcher::FOUND:
                /** @var $handler RouteExecutor */
                $handler = $routeInfo[1];
                $handler->doRun($httpMethod, $routeInfo[2]);
                break;
        }
    }
}