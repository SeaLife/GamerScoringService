<?php
/** @noinspection ALL */


function orv ($val, $or) {
    if (!isset($val) || empty($val)) {
        return $or;
    }

    if (is_string($val) && strtolower($val) == "off") return FALSE;
    if (is_string($val) && strtolower($val) == "on") return TRUE;

    return $val;
}

function envvar ($key, $def = NULL) {
    $val = orv(@$_ENV[$key], $def);

    if ($val == $def) $_ENV[$key] = $def;

    if (file_exists("envvar") && is_dir("envvar"))
        file_put_contents("envvar/" . $key . ".env", $val);

    return $val;
}

function setenv ($key, $value) {
    $_ENV[$key] = $value;
}

function toBool ($str) {
    if (strtolower($str) == "false") return FALSE;
    if (!@$str) return FALSE;
    if ($str == '0') return FALSE;
    if (strtolower($str) == 'off') return FALSE;
    return TRUE;
}

function str_startswith ($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}