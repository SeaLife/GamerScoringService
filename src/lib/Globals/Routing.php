<?php

namespace Globals;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Globals\Annotations\WebResponder;
use Globals\Routing\RouteExecutor;
use Globals\Routing\SecuredExecutor;
use Globals\Routing\SubExecutor;
use Util\SingletonFactory;

/**
 * Global routing for the application (is the main entry point of the application)
 */
class Routing extends SingletonFactory {

    private $routes = array();

    /**
     * Returns all configured routes.
     *
     * @return array
     */
    public function getRoutes () {
        return $this->routes;
    }

    /**
     * Adds a new route to the system.
     *
     * @param string        $route      to be added
     * @param RouteExecutor $executor   to be executed if matched
     * @param string        $method     http method
     * @param string        $name       of the resource.
     * @param string        $source     of the route (routes.yml or the class of the dynamic route)
     * @param string        $permission required to access the endpoint.
     * @param string        $accept     header for accessing the endpoint
     */
    public function addRoute ($route, RouteExecutor $executor, $method = "GET", $name = "unknown", $source = 'routes.yml', $permission = '', $accept = 'any') {
        array_push($this->routes, array("route" => $route, "executor" => $executor, "method" => $method, "name" => $name, "source" => $source, "permission" => $permission, 'accept' => $accept));
    }

    /**
     * Loading the configuration.
     */
    public function init () {
        $routes = ConfigurationManager::getInstance()->getConfigContext()['routes'];

        foreach ($routes as $k => $v) {
            if (class_exists($v["handler"])) {
                $executor = new SecuredExecutor(new $v["handler"](), orv($v["requiredPermission"], ''));

                $this->addRoute($v["path"], $executor, orv(@$v["method"], "GET"), $v["name"], 'routes.yml', orv($v["requiredPermission"], ''), orv($v["accept"], 'any'));
            } else {
                $this->getLogger()->warning("Executor '{}' for Path '{}'@'{}' does not exist", array($v["handler"], $v["name"], $v["path"]));
            }
        }
    }

    /**
     * Initialize's the routing.
     */
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

    /**
     * Find all dynamic routes.
     */
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

    /**
     * A combined function for running all of the required initialize functions:
     * {@code self::run}, {@code self::findRoutes}, {@code self::exec}
     */
    public function run () {
        $this->init();
        $this->findRoutes();
        $this->exec();
    }

    /**
     * Returns all required permissions for all routes in a simple array.
     *
     * @return string[]
     */
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

    /**
     * Redirects the user to another destination.
     *
     * @param string $destination to be redirected.
     */
    public static function route ($destination) {
        header("Location: $destination");
    }

    /**
     * A helper method to return a singleton instance of a class required for the dynamic routes.
     *
     * @param \ReflectionClass $class
     * @return mixed
     */
    private function getSingleInstanceOf (\ReflectionClass $class) {
        if (isset($this->__instances[md5($class)])) {
            return $this->__instances[md5($class)];
        }

        $this->__instances[md5($class)] = $class->newInstance();

        return $this->getSingleInstanceOf($class);
    }

    private $__instances = array();
}