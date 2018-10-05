<?php

namespace Globals;

use Util\CallableWrapper;
use Util\SingletonFactory;

class MenuHandler extends SingletonFactory {

    private $menuItems = array();

    /**
     * @param $name       string
     * @param $path       string
     * @param $shouldShow callable
     */
    public function add ($name, $path, $shouldShow = NULL) {

        if ($shouldShow == NULL) $shouldShow = function () {
            return TRUE;
        };

        array_push($this->menuItems, array(
            "name"      => $name,
            "path"      => $path,
            "isEnabled" => CallableWrapper::of($shouldShow)
        ));
    }

    public function getMenuItems () {
        return $this->menuItems;
    }
}