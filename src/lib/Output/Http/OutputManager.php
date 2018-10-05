<?php

namespace Output\Http;

use Globals\MenuHandler;
use Globals\Security;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Util\CallableWrapper;
use Util\SingletonFactory;

class OutputManager extends SingletonFactory {

    /** @var $environment Environment */
    private $environment;

    function afterConstruct () {
        $loader            = new FilesystemLoader(__DIR__ . "/../../../templates/");
        $this->environment = new Environment($loader, array(
            'debug' => TRUE
        ));
        $this->environment->addExtension(new DebugExtension());
    }

    public function display (Content $content) {
        /** @noinspection PhpUnhandledExceptionInspection */
        $out = $this->environment->load($content->getFile());

        $this->render($out, $content->getContext());
    }

    public function render (\Twig_TemplateWrapper $wrapper, $context = array()) {
        $context = $this->applyGlobals($context);
        echo $wrapper->render($context);
    }

    private function applyGlobals ($arr) {
        // set if not set
        if (!isset($arr["THEME"])) $arr["THEME"] = envvar("THEME", "flatly");
        if (!isset($arr["PAGE_TITLE"])) $arr["PAGE_TITLE"] = "GamerScoring - Infoscore for Gamer's";

        // set always.
        $arr["PROFILE"]      = envvar("PROFILE", "none");
        $arr["HANDLER_MENU"] = MenuHandler::getInstance()->getMenuItems();
        $arr["hasAccess"]    = CallableWrapper::of(function ($flag) {
            return Security::hasPermission($flag);
        });

        return $arr;
    }
}