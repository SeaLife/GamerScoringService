<?php

namespace Util;

use Logging\FileLogger;
use Psr\Log\LoggerInterface;

abstract class SingletonFactory {
    private static $instanceList = array();
    private        $logger       = NULL;

    /**
     * Returns the instance of the called class.
     *
     * @return $this
     */
    public static function getInstance () {
        $currentClass = get_called_class();
        if (!isset(self::$instanceList[md5($currentClass)])) self::$instanceList[md5($currentClass)] = new $currentClass();
        return self::$instanceList[md5($currentClass)];
    }

    final function __construct () {
        $this->afterConstruct();
    }

    function afterConstruct () {
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger () {
        if ($this->logger == NULL) {
            $this->logger = FileLogger::getLogger(get_class($this));
        }
        return $this->logger;
    }
}
