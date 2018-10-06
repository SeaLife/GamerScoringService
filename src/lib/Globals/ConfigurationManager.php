<?php

namespace Globals;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Util\SingletonFactory;

class ConfigurationManager extends SingletonFactory {
    private $configContext = array();

    public function init () {
        /** @var $finder \SplFileInfo[] */
        $finder = Finder::create()->name("*.yml")->name("*.yaml")->in(__DIR__ . "/../../../config");

        foreach ($finder as $item) {
            $itemPath = $item->getRealPath();

            $yamlData = Yaml::parseFile($itemPath);

            $this->configContext = array_merge($this->configContext, $yamlData);
        }

        if (isset($this->configContext['env'])) {
            $envData = $this->configContext['env'];

            foreach ($envData as $k => $v) {
                setenv($k, $v);
            }
        }

        $this->registerAnnotations();
    }

    private function registerAnnotations() {
        foreach($this->getConfigContext()['annotations']['classes'] as $cl) {
            $this->getLogger()->debug("Loading Annotation-Class {}", array($cl));
            AnnotationRegistry::loadAnnotationClass($cl);
        }
    }

    /**
     * @return array
     */
    public function getConfigContext () {
        return $this->configContext;
    }
}