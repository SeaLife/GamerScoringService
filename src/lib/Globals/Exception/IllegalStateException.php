<?php

namespace Globals\Exception;

use Throwable;

class IllegalStateException extends \RuntimeException {
    public function __construct ($message = "", $code = 0, Throwable $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}