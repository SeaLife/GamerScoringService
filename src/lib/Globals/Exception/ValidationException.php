<?php

namespace Globals\Exception;

use Throwable;

class ValidationException extends \RuntimeException {
    public function __construct (string $message = "", int $code = 0, Throwable $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }
}