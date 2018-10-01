<?php

namespace Logging;

use DateTime;
use InvalidArgumentException;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class FileLogger extends AbstractLogger {

    public static function getLogger ($className) {
        $logger = new FileLogger($className);
        return $logger;
    }

    private static $dir  = __DIR__ . "/../../../logs/";
    private static $file = "app.log";

    private $loggerName = NULL;
    private $level      = NULL;

    public function __construct ($className, $level = "ALL") {
        if (!file_exists(self::$dir)) {
            mkdir(self::$dir);
        }

        $level = envvar("SYSTEM_LOGGING_LEVEL", $level);

        /** @noinspection PhpUndefinedFieldInspection */
        $this->level      = constant("Logging\LoggingLevel::" . $level);
        $this->loggerName = $className;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log ($level, $message, array $context = array()) {

        if (!preg_match("/({$this->level})/", $level)) {
            return;
        }

        $count = substr_count($message, "{}");

        if ($count != sizeof($context)) {
            throw new InvalidArgumentException("String replacement count does not equals context");
        }

        for ($i = 0; $i < sizeof($context); $i++) {
            $val = $context[$i];

            if ($val instanceof \Throwable) {
                $val = sprintf("%s ('%s'):\n%s", get_class($val), $val->getMessage(), $val->getTraceAsString());
            }

            if (is_bool($val)) {
                $val = "bool(" . (($val) ? 'true' : 'false') . ")";
            }

            if (is_array($val)) {
                $val = json_encode($val);
            }

            $message = $this->replaceFirst("{}", $val, $message);
        }

        if (!file_put_contents(self::$dir . self::$file, sprintf("%s  [%-10s]  %-14s: %s\n", $this->getTime(), ucfirst($level), $this->loggerName, $message), FILE_APPEND)) {
            throw new \RuntimeException("Failed to write logfile");
        }
    }

    private function replaceFirst ($from, $to, $content) {
        $from = '/' . preg_quote($from, '/') . '/';
        return preg_replace($from, $to, $content, 1);
    }

    private function getTime () {
        $t     = microtime(TRUE);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d     = new DateTime(date('Y-m-d H:i:s.' . $micro, $t));

        return $d->format("Y-m-d H:i:s.u"); // note at point on "u"
    }

    /**
     * @param string $level
     */
    public function setLevel ($level) {
        $this->level = $level;
    }
}