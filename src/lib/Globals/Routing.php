<?php

namespace Globals;

use Api\SubExecutor;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Util\SingletonFactory;

class Routing extends SingletonFactory {

    private $routes = array();

    public function getRoutes () {
        return $this->routes;
    }

    public function addRoute ($route, RouteExecutor $executor, $method = "GET", $name = "unknown", $source = 'routes.yml') {
        array_push($this->routes, array("route" => $route, "executor" => $executor, "method" => $method, "name" => $name, "source" => $source));
    }

    public function init () {
        $routes = Yaml::parseFile(__DIR__ . "/../../../config/routes.yml");

        foreach ($routes["routes"] as $k => $v) {
            if (class_exists($v["handler"])) {
                $this->addRoute($v["path"], new $v["handler"](), orv(@$v["method"], "GET"), $v["name"]);
            }
            else {
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
                NotFoundResponder::run();
                break;

            case Dispatcher::FOUND:
                /** @var $handler RouteExecutor */
                $handler = $routeInfo[1];
                $handler->doRun($httpMethod, $routeInfo[2]);
                break;
        }
    }

    public function findRoutes () {
        AnnotationRegistry::registerFile(__DIR__ . "/../Globals/WebResponder.php");

        $finder = Finder::create()->files()->name('*Router.php')->name('*Controller.php')->in(__DIR__ . "/../");

        /** @noinspection PhpUnhandledExceptionInspection */
        $reader = new AnnotationReader(new DocParser());

        foreach ($finder as $file) {
            /** @var $file \SplFileInfo */
            /** @noinspection PhpIncludeInspection */
            include_once $file->getRealPath();
            $loaded = get_declared_classes();
            $loaded = $loaded[count($loaded) - 1];

            /** @noinspection PhpUnhandledExceptionInspection */
            $rfClass = new \ReflectionClass($loaded);

            $methods = $rfClass->getMethods();

            $instance = new $loaded();

            foreach ($methods as $method) {
                /** @var $responder \Globals\WebResponder */
                $responder = $reader->getMethodAnnotation($method, "Globals\WebResponder");

                if ($responder != NULL) {
                    $this->addRoute($responder->path, new SubExecutor($instance, $method, $responder), $responder->method, $responder->name, $loaded);
                }
            }
        }
    }
}