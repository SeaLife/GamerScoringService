<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Globals\Annotations\Data;

use ReflectionClass;

class MethodAnnotation {

    private $class;

    private $method;

    private $annotation;

    public function __construct (\ReflectionClass $class, \ReflectionMethod $method, $annotation) {
        $this->class      = $class->getName();
        $this->method     = $method->getName();
        $this->annotation = $annotation;
    }

    public function getAnnotation () {
        return $this->annotation;
    }

    public function getClass () {
        return new ReflectionClass($this->class);
    }

    public function getMethod () {
        return $this->getClass()->getMethod($this->method);
    }
}