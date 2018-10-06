<?php /** @noinspection all */

namespace Globals;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Globals\Annotations\Data\AnnotationHolder;
use Globals\Annotations\Data\MethodAnnotation;
use Symfony\Component\Finder\Finder;
use Util\SingletonFactory;

class AnnotationHelper extends SingletonFactory {

    /**
     * @param $annotationClass
     *
     * @return MethodAnnotation[]
     * @throws \ReflectionException
     */
    public function findAllMethodsAnnotatedWith ($annotationClass) {
        $cachedData = $this->isCached('app.annotations.methods.' . md5($annotationClass));

        if ($cachedData) return $cachedData;

        $this->includeAll();

        $reader = $this->getAnnotationReader();
        $result = array();

        foreach (get_declared_classes() as $class) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $rfClass = new \ReflectionClass($class);
            $methods = $rfClass->getMethods();

            foreach ($methods as $method) {
                $annotation = $reader->getMethodAnnotation($method, $annotationClass);
                if ($annotation != NULL) {
                    array_push($result, new MethodAnnotation($rfClass, $method, $annotation));
                }
            }
        }

        $this->saveCache('app.annotations.methods.' . md5($annotationClass), $result);

        return $result;
    }

    /**
     * @param $annotationClass
     *
     * @return AnnotationHolder[]
     * @throws \ReflectionException
     */
    public function findClassesAnnotatedWith ($annotationClass) {

        $cachedData = $this->isCached('app.annotations.class.' . md5($annotationClass));

        if ($cachedData) return $cachedData;

        $this->includeAll();

        $annotationReader = $this->getAnnotationReader();
        $classes          = array();

        foreach (get_declared_classes() as $class) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $rfClass    = new \ReflectionClass($class);
            $annotation = $annotationReader->getClassAnnotation($rfClass, $annotationClass);

            if ($annotation != NULL) {
                array_push($classes, new AnnotationHolder($rfClass, $annotation));
            }
        }

        $this->saveCache('', $classes);

        return $classes;
    }

    private function getAnnotationReader () {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new AnnotationReader(new DocParser());;
    }

    private function includeAll () {
        /** @var $result \SplFileInfo[] */
        $result = Finder::create()->name("*.php")->in(__DIR__ . "/../");

        foreach ($result as $item) {
            /** @noinspection PhpIncludeInspection */
            include_once $item->getRealPath();
        }
    }

    private function saveCache ($key, $data) {
        if (Cache::getInstance()->isEnabled()) {
            $cacheItem = Cache::getInstance()->get()->getItem($key);

            $cacheItem->set($data);

            Cache::getInstance()->get()->save($cacheItem);

            $this->getLogger()->debug('Saving Cache-Data {} [{}].', array($key, $data));
        }
    }

    private function isCached ($key) {
        if (Cache::getInstance()->isEnabled()) {
            $cacheItem = Cache::getInstance()->get()->getItem($key);

            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }
        }
        return FALSE;
    }
}