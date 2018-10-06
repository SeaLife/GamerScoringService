<?php

namespace Globals;

use Doctrine\Common\Cache\SQLite3Cache;
use SQLite3;
use Symfony\Component\Cache\Adapter\ChainAdapter;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Util\SingletonFactory;

class Cache extends SingletonFactory {

    /** @var ChainAdapter */
    private $adapter;

    private $cacheInstances = array();

    private $enabled        = FALSE;

    public function afterConstruct () {
        $cacheConfiguration = ConfigurationManager::getInstance()->getConfigContext()['cache'];
        $this->enabled      = orv($cacheConfiguration['enabled'], TRUE);

        if (isset($cacheConfiguration['providers'])) {
            foreach ($cacheConfiguration['providers'] as $provider) {
                if (!orv($provider["enabled"], TRUE)) continue;

                $type = strtolower($provider['provider-type']);

                switch ($type) {

                    case 'redis':
                        $url = $provider['config']['url'];
                        array_push($this->cacheInstances, new RedisAdapter(RedisAdapter::createConnection($url), 'AppCache', orv($provider['config']['ttl'], 60 * 5)));
                        break;

                    case 'fs':
                    case 'filesystem':
                        $directory = $provider['config']['dir'];
                        $directory = str_replace("{BASE}", __DIR__ . "/../../../", $directory);
                        $directory = str_replace("{ROOT}", __ROOT__, $directory);
                        $directory = str_replace("{DIR}", __DIR__, $directory);

                        array_push($this->cacheInstances, new FilesystemAdapter('AppCache', orv($provider['config']['ttl'], 60 * 5), $directory));
                        break;

                    case 'database':
                    case 'db':
                        $url  = $provider['config']['url'];
                        $user = $provider['config']['user'];
                        $pass = $provider['config']['pass'];

                        $adapter = new PdoAdapter(new \PDO($url, $user, $pass, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION)));

                        array_push($this->cacheInstances, $adapter);

                        break;

                    case 'sqlite':
                    case 'sqlite3':
                        $location = $provider['config']['location'];
                        $location = str_replace("{BASE}", __DIR__ . "/../../../", $location);
                        $location = str_replace("{ROOT}", __ROOT__, $location);
                        $location = str_replace("{DIR}", __DIR__, $location);

                        if (!class_exists('\\SQLite3')) {
                            $this->getLogger()->warning('Failed to initialize SQLite3 Cache, extension is missing.');
                            break;
                        }

                        $provider = new SQLite3Cache(new SQLite3($location), 'cache');
                        $adapter  = new DoctrineAdapter($provider, 'AppCache', 60 * 5);

                        array_push($this->cacheInstances, $adapter);

                        break;

                    default:
                        throw new \InvalidArgumentException('Cache-Provider ' . $provider . ' does not exist');
                }

                $this->getLogger()->debug("Loaded Cache-Provider {}", array($type));
            }
        }

        if ($this->isEnabled()) {
            $this->adapter = new ChainAdapter($this->cacheInstances);
        }
    }

    public function isEnabled () {
        return count($this->cacheInstances) > 0 && $this->enabled;
    }

    public function get () {
        return $this->adapter;
    }
}