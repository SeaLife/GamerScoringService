<?php

namespace Globals;

use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use Util\SingletonFactory;

class Locks extends SingletonFactory {

    /** @var $factory Factory */
    private $factory;

    function afterConstruct () {
        $store   = new SemaphoreStore();
        $factory = new Factory($store);

        $this->factory = $factory;
    }

    public function getLock ($lock) {
        return $this->factory->createLock($lock, 30);
    }
}