<?php

namespace Globals;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Util\SingletonFactory;

/**
 * Database Interface for the application.
 */
class DB extends SingletonFactory {

    private $entityManager;

    /**
     * Load a database profile configuration (dev, prod, ...)
     *
     * @param string $profile to be loaded.
     * @throws \Doctrine\ORM\ORMException
     */
    public function load ($profile) {
        if (empty($profile)) {
            throw new \InvalidArgumentException("Profile cannot be empty");
        }

        $dbConfig = ConfigurationManager::getInstance()->getConfigContext()['database'];

        if (!isset($dbConfig[$profile])) {
            throw new \InvalidArgumentException("Profile " . $profile . " does not exist");
        }

        $paths = array(__ROOT__ . "/lib/App/Entity");

        $cfg = Setup::createAnnotationMetadataConfiguration($paths, TRUE, NULL, NULL, FALSE);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->entityManager = EntityManager::create($dbConfig[$profile], $cfg);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager () {
        return $this->entityManager;
    }
}