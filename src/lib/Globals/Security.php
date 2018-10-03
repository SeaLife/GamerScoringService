<?php

namespace Globals;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use Globals\Security\AbstractSecurityProvider;
use Symfony\Component\Finder\Finder;

class Security {

    public static function isLoggedIn () {
        return orv($_SESSION["_LOGIN"], FALSE);
    }

    public static function hasPermission ($flag) {
        return FALSE;
    }

    public static function doLogin ($username, $password) {
        AnnotationRegistry::registerFile(__DIR__ . "/../Globals/Annotations/SecurityProvider.php");

        $finder = Finder::create()->files()->name('*Provider.php')->in(__DIR__ . "/Security");

        /** @noinspection PhpUnhandledExceptionInspection */
        $reader = new AnnotationReader(new DocParser());

        foreach ($finder as $file) {
            /** @var $file \SplFileInfo */
            /** @noinspection PhpIncludeInspection */
            include_once $file->getRealPath();
            $loaded = get_declared_classes();
            $loaded = $loaded[count($loaded) - 1];

            /** @noinspection PhpUnhandledExceptionInspection */
            $rfClass = new \ReflectionClass($loaded);

            $annotation = $reader->getClassAnnotation($rfClass, "Globals\Annotations\SecurityProvider");

            if ($annotation != NULL) {
                if ($rfClass->isSubclassOf(AbstractSecurityProvider::class)) {
                    $instance = $rfClass->newInstance();

                    $result = $instance->doLogin($username, $password);

                    if ($result) {
                        return TRUE;
                    }
                }
            }
        }

        return FALSE;
    }

    public static function markLogin () {
        $_SESSION["_LOGIN"] = TRUE;
    }

    public static function markLogout () {
        $_SESSION["_LOGIN"] = FALSE;
    }
}