<?php
/** @noinspection PhpIncludeInspection */

use Doctrine\Common\Annotations\AnnotationRegistry;
use PhpParser\PrettyPrinter\Standard;
use Plumbok\Compiler;
use Plumbok\Compiler\NodeFinder;
use Plumbok\TagsUpdater;

define("__ROOT__", __DIR__);

include_once __DIR__ . '/bootstrap/globals.php';

session_start();

/**
 * Class ClassLoader
 */
class ClassLoader {

    private static $loaders = array();

    public static function add (Closure $closure) {
        array_push(self::$loaders, $closure);
    }

    public static function init () {
        $loader = function ($name) {
            if (!class_exists($name)) {
                foreach (self::$loaders as $v) {
                    $v($name);

                    if (class_exists($name)) return TRUE;
                }
            } else {
                return TRUE;
            }

            return FALSE;
        };

        spl_autoload_register($loader);

        AnnotationRegistry::registerLoader($loader);
    }
}

ClassLoader::add(function ($name) {

    $pathCorrect = function ($path, $replace = "/") {
        $str = str_replace("\\", $replace, $path);
        return str_replace("/", $replace, $str);
    };

    $cacheLocation    = envvar("SYSTEM_PLUMBOK_LOCATION", __DIR__ . "/../cache/");
    $cacheEnabled     = toBool(envvar("SYSTEM_PLUMBOK_CACHE_ENABLED", "true"));
    $plumbokEnabled   = toBool(envvar("SYSTEM_PLUMBOK_ENABLED", "true"));
    $plumbokOverwrite = toBool(envvar("SYSTEM_PLUMBOK_OVERWRITE_ORIGINAL", "true"));

    if (!file_exists($cacheLocation)) mkdir($cacheLocation);

    $phpFile   = __DIR__ . "/lib/" . $pathCorrect($name) . ".php";
    $cacheFile = $cacheLocation . $pathCorrect($name, "_") . ".php";

    /**
     * Include Cached-File :)
     */
    if ($cacheEnabled && $plumbokEnabled && file_exists($cacheFile)) {
        include_once $cacheFile;
        return;
    }

    if (file_exists($phpFile)) {
        if ($plumbokEnabled) {
            try {
                $plumbokCompiler = new Compiler();
                $nodes           = $plumbokCompiler->compile($phpFile);
                $serialize       = new Standard();
                $fileContent     = $serialize->prettyPrint($nodes);

                /**
                 * Include source file if plumbok could not create a class
                 */
                if (empty(trim($fileContent))) {
                    include_once $phpFile;
                    return;
                }

                file_put_contents($cacheFile, "<?php \n\n" . $fileContent);

                if ($plumbokOverwrite) {
                    $tagsUpdater = new TagsUpdater(new NodeFinder());
                    $tagsUpdater->applyNodes($phpFile, ...$nodes);
                }

                include_once $cacheFile;
            } catch (InvalidArgumentException $e) {
                include_once $phpFile;
            } catch (Error $e) {
                include_once $phpFile;
            }
        } else {
            include_once $phpFile;
        }
    }
});

// default exception handler, will be used in bootstrap errors.
set_exception_handler(function (Throwable $ex) {
    echo "<pre>";
    echo get_class($ex) . ": " . $ex->getMessage();
    echo "\n";
    echo "\n";
    echo $ex->getTraceAsString();
    echo "</pre>";
});

include_once envvar("COMPOSE_LOCATION", __DIR__ . "/../") . "vendor/autoload.php";

ClassLoader::init();

include __DIR__ . '/config.php';
include __DIR__ . '/bootstrap/bootstrap.php';
include __DIR__ . '/app.php';