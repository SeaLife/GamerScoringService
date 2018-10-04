<?php
/** @noinspection PhpIncludeInspection */

use Doctrine\Common\Annotations\AnnotationRegistry;
use PhpParser\PrettyPrinter\Standard;
use Plumbok\Compiler;
use Plumbok\Compiler\NodeFinder;
use Plumbok\TagsUpdater;

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
            }
            else {
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

    $cacheLocation    = envvar("SYSTEM_PLUMBOK_LOCATION", __ROOT__ . "/../cache/");
    $cacheEnabled     = toBool(envvar("SYSTEM_PLUMBOK_CACHE_ENABLED", "false"));
    $plumbokEnabled   = toBool(envvar("SYSTEM_PLUMBOK_ENABLED", "true"));
    $plumbokOverwrite = toBool(envvar("SYSTEM_PLUMBOK_OVERWRITE_ORIGINAL", "true"));

    if (!file_exists($cacheLocation)) mkdir($cacheLocation);

    $phpFile   = __ROOT__ . "/lib/" . $pathCorrect($name) . ".php";
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
        }
        else {
            include_once $phpFile;
        }
    }
});