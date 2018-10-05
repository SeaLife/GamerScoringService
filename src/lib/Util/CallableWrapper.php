<?php

namespace Util;

class CallableWrapper {

    private $callable = NULL;

    private function __construct (callable $callable) {
        $this->callable = $callable;
    }

    public static function of (callable $cb) {
        return new CallableWrapper($cb);
    }

    public function run () {
        $callable = $this->callable;

        return call_user_func_array($callable, func_get_args());
    }
}