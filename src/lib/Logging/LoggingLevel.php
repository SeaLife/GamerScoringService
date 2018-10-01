<?php

namespace Logging;


class LoggingLevel {
    /**
     * const EMERGENCY = 'emergency';
     * const ALERT = 'alert';
     * const CRITICAL = 'critical';
     * const ERROR = 'error';
     * const WARNING = 'warning';
     * const NOTICE = 'notice';
     * const INFO = 'info';
     * const DEBUG = 'debug';
     */
    const ALL        = ".*";
    const INFO       = "info|warning|error|critical|alert|emergency";
    const ERROR      = "error|critical|alert|emergency";
    const DEBUG      = self::ALL;
    const INFO_ONLY  = "info|emergency";
    const ERROR_ONLY = "error|critical|emergency";
    const EMERGENCY  = "emergency";

    const NONE = "xxxxxxxx"; // this matches to no logger so no logging will be done.
}