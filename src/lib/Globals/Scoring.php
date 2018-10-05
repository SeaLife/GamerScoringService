<?php

namespace Globals;

use App\Entity\PlayerInformation;
use Scoring\ScoreProvider;
use Util\SingletonFactory;

class Scoring extends SingletonFactory {

    /** @var $providers ScoreProvider[] */
    private $providers = array();

    public function register (ScoreProvider $provider) {
        array_push($this->providers, $provider);
    }

    function afterConstruct () {
        $providers = ConfigurationManager::getInstance()->getConfigContext()['scoring']['providers'];

        foreach ($providers as $provider) {
            $instance = new $provider();

            array_push($this->providers, $instance);
        }
    }


    /**
     * @return ScoreProvider[]
     */
    public function getProviders (): array {
        return $this->providers;
    }

    public function fetchFor (PlayerInformation $playerInformation) {
        // TODO: implement
    }

    public function processFor (PlayerInformation $playerInformation) {
        // TODO: implement
    }
}