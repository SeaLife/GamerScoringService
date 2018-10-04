<?php

namespace Globals\Annotations;

use OpenApi\Annotations\AbstractAnnotation;

/**
 * @Annotation
 */
class WebResponder extends AbstractAnnotation {

    public $method             = 'GET';
    public $path               = '';
    public $name               = 'unknown';
    public $produces           = '';
    public $requiredPermission = '';
}