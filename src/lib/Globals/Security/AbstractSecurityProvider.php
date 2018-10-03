<?php

namespace Globals\Security;

abstract class AbstractSecurityProvider {
    abstract public function doLogin($username, $password);
}