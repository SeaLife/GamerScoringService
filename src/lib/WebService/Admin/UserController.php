<?php

namespace WebService\Admin;

use Globals\Annotations\WebResponder;

/**
 * Admin Controller for managing Roles
 */
class UserController {

    /**
     * @WebResponder(path="/admin/users", method="GET", requiredPermission="U")
     */
    public function viewAllUsers() {

    }

    /**
     * @WebResponder(path="/admin/user/{id:\d+}", method="GET", requiredPermission="U")
     * @param $vars
     */
    public function viewUser($vars) {

    }

    /**
     * @WebResponder(path="/admin/user", method="POST", requiredPermission="U")
     */
    public function actionAdd() {

    }

    /**
     * @WebResponder(path="/admin/user/{id:\d+}", method="POST", requiredPermission="U")
     * @param $vars
     */
    public function actionModify($vars) {

    }
}