<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Globals\Annotations\Data;

use ReflectionClass;

class AnnotationHolder {

    /** @var \ReflectionClass */
    private $class;

    private $annotation;

    public function __construct (\ReflectionClass $class, $annotation) {
        $this->class      = $class->getName();
        $this->annotation = $annotation;
    }

    public function getAnnotation () {
        return $this->annotation;
    }

    public function getClass () {
        return new ReflectionClass($this->class);
    }
}