<?php

namespace Globals\Routing;

use Globals\ErrorResponder;
use Globals\Security;

class SecuredExecutor implements RouteExecutor {

    /** @var $executor \Globals\Routing\RouteExecutor */
    private $executor;

    private $requiredFlag;

    public function __construct ($executor, $requiredFlag = NULL) {
        $this->executor     = $executor;
        $this->requiredFlag = $requiredFlag;
    }

    public function doRun ($method, $vars) {

        if (!empty($this->requiredFlag) && !Security::hasPermission($this->requiredFlag)) {
            ErrorResponder::error403();
            return;
        }

        $this->executor->doRun($method, $vars);
    }
}