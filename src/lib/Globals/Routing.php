<?php

namespace Globals;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Globals\Routing\RouteExecutor;
use Globals\Routing\SecuredExecutor;
use Globals\Routing\SubExecutor;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Util\SingletonFactory;

class Routing extends SingletonFactory {

    private $routes = array();

    public function getRoutes () {
        return $this->routes;
    }

    public function addRoute ($route, RouteExecutor $executor, $method = "GET", $name = "unknown", $source = 'routes.yml', $permission = '') {
        array_push($this->routes, array("route" => $route, "executor" => $executor, "method" => $method, "name" => $name, "source" => $source, "permission" => $permission));
    }

    public function init () {
        $routes = ConfigurationManager::getInstance()->getConfigContext()['routes'];

        foreach ($routes as $k => $v) {
            if (class_exists($v["handler"])) {
                $executor = new SecuredExecutor(new $v["handler"](), orv($v["requiredPermission"], ''));

                $this->addRoute($v["path"], $executor, orv(@$v["method"], "GET"), $v["name"], 'routes.yml', orv($v["requiredPermission"], ''));
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
                ErrorResponder::error404();
                break;

            case Dispatcher::FOUND:
                /** @var $handler RouteExecutor */
                $handler = $routeInfo[1];
                $handler->doRun($httpMethod, $routeInfo[2]);
                break;
        }
    }

    public function findRoutes () {
        AnnotationRegistry::registerFile(__DIR__ . "/../Globals/Annotations/WebResponder.php");

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
                /** @var $responder \Globals\Annotations\WebResponder */
                $responder = $reader->getMethodAnnotation($method, "Globals\Annotations\WebResponder");

                if ($responder != NULL) {
                    $this->addRoute($responder->path, new SubExecutor($instance, $method, $responder), $responder->method, $responder->name, $loaded, $responder->requiredPermission);
                }
            }
        }
    }

    public function listAllRequiredPermissions () {
        $distinctList = array();

        $routes = Routing::getRoutes();

        foreach ($routes as $route) {
            if (!empty($route["permission"])) {
                if (!in_array($route["permission"], $distinctList)) {
                    array_push($distinctList, $route["permission"]);
                }
            }
        }

        return $distinctList;
    }

    public static function route ($destination) {
        header("Location: $destination");
    }
}