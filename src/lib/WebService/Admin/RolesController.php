<?php

namespace WebService\Admin;

use Globals\Annotations\WebResponder;

/**
 * Admin Controller for managing Roles
 */
class RolesController {

    /**
     * @WebResponder(path="/admin/roles", method="GET", requiredPermission="R")
     */
    public function viewAllRoles() {

    }

    /**
     * @WebResponder(path="/admin/role/{id:\d+}", method="GET", requiredPermission="R")
     * @param $vars
     */
    public function viewRole($vars) {

    }

    /**
     * @WebResponder(path="/admin/role", method="POST", requiredPermission="R")
     */
    public function actionAdd() {

    }

    /**
     * @WebResponder(path="/admin/role/{id:\d+}", method="POST", requiredPermission="R")
     * @param $vars
     */
    public function actionModify($vars) {

    }
}