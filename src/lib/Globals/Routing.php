<?php

namespace Globals;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Globals\Annotations\WebResponder;
use Globals\Routing\RouteExecutor;
use Globals\Routing\SecuredExecutor;
use Globals\Routing\SubExecutor;
use Util\SingletonFactory;

class Routing extends SingletonFactory {

    private $routes = array();

    public function getRoutes () {
        return $this->routes;
    }

    public function addRoute ($route, RouteExecutor $executor, $method = "GET", $name = "unknown", $source = 'routes.yml', $permission = '', $accept = 'any') {
        array_push($this->routes, array("route" => $route, "executor" => $executor, "method" => $method, "name" => $name, "source" => $source, "permission" => $permission, 'accept' => $accept));
    }

    public function init () {
        $routes = ConfigurationManager::getInstance()->getConfigContext()['routes'];

        foreach ($routes as $k => $v) {
            if (class_exists($v["handler"])) {
                $executor = new SecuredExecutor(new $v["handler"](), orv($v["requiredPermission"], ''));

                $this->addRoute($v["path"], $executor, orv(@$v["method"], "GET"), $v["name"], 'routes.yml', orv($v["requiredPermission"], ''), orv($v["accept"], 'any'));
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

        /** @noinspection PhpUnhandledExceptionInspection */
        $classes = AnnotationHelper::getInstance()->findAllMethodsAnnotatedWith(WebResponder::class);

        foreach ($classes as $annotation) {
            $instance = $this->getSingleInstanceOf($annotation->getClass());

            /** @var $responder WebResponder */
            $responder = $annotation->getAnnotation();
            $executor  = new SubExecutor($instance, $annotation->getMethod(), $responder);

            $this->addRoute($annotation->getAnnotation()->path, $executor, $responder->method, $responder->name, $annotation->getClass()->getName(), $responder->requiredPermission, $responder->accept);
        }
    }

    public function run () {
        $this->init();
        $this->findRoutes();
        $this->exec();
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

    private function getSingleInstanceOf (\ReflectionClass $class) {
        if (isset($this->__instances[md5($class)])) {
            return $this->__instances[md5($class)];
        }

        $this->__instances[md5($class)] = $class->newInstance();

        return $this->getSingleInstanceOf($class);
    }

    private $__instances = array();
}