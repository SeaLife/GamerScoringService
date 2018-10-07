<?php
/** @noinspection PhpIncludeInspection */

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
    }

    public static function compilePlumbok ($name) {

        $file = __ROOT__ . "/lib/" . ClassLoader::fixPath($name) . ".php";

        $enabled  = toBool(envvar('PLUMBOK', 'on'));
        $dev      = toBool(envvar('PLUMBOK_DEV', 'on'));
        $caching  = toBool(envvar('PLUMBOK_CACHE', 'on'));
        $location = envvar('PLUMBOK_LOCATION', __ROOT__ . "/../cache");

        if (!file_exists($file)) return FALSE;

        if (!file_exists($location)) mkdir($location);

        if (!$enabled) return $file;

        $cacheFile = $location . '/' . self::fixPath($name, "_") . ".php";

        if (file_exists($cacheFile) && $caching) {
            return $cacheFile;
        }

        try {
            $plumbokCompiler = new Compiler();
            $nodes           = $plumbokCompiler->compile($file);
            $serialize       = new Standard();
            $fileContent     = $serialize->prettyPrint($nodes);

            if (empty(trim($fileContent))) {
                throw new InvalidArgumentException('File cannot be parsed, aborting and including default.');
            }

            if ($dev) {
                $tagsUpdater = new TagsUpdater(new NodeFinder());
                $tagsUpdater->applyNodes($file, ...$nodes);
            }

            file_put_contents($cacheFile, "<?php\n\n" . $fileContent);

            return $cacheFile;
        } catch (InvalidArgumentException $e) {
        } catch (ErrorException $e) {
        } catch (Error $e) {
        }

        return $file;
    }

    public static function fixPath ($path, $separator = "/") {
        $path = str_replace("\\", $separator, $path);
        return str_replace("/", $separator, $path);
    }
}

ClassLoader::add(function ($name) {
    $file = ClassLoader::compilePlumbok($name);

    if ($file) {
        include_once $file;
    }
});

ClassLoader::init();