<?php

namespace Globals;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Yaml\Yaml;
use Util\SingletonFactory;

class DB extends SingletonFactory {

    private $entityManager;

    public function load ($profile) {
        if (empty($profile)) {
            throw new \InvalidArgumentException("Profile cannot be empty");
        }

        $dbConfig = Yaml::parseFile(__DIR__ . "/../../../config/database.yml");

        if (!isset($dbConfig["database"][$profile])) {
            throw new \InvalidArgumentException("Profile " . $profile . " does not exist");
        }

        $paths = array(__DIR__ . "/../");

        $cfg = Setup::createAnnotationMetadataConfiguration($paths, TRUE);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->entityManager = EntityManager::create($dbConfig["database"][$profile], $cfg);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager () {
        return $this->entityManager;
    }
}