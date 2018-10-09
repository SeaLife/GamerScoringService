<?php

namespace Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class ServerCommand extends Command {

    protected function configure () {
        $this->setName('app:run-server');
        $this->setDescription("Starts the local Server (php -S 0.0.0.0)");
        $this->setHelp("This commands starts a PHP built-in server in src/");
    }

    protected function execute (InputInterface $input, OutputInterface $output) {
        set_time_limit(0);

        chdir(__DIR__ . "/../../");
        exec("php -S 0.0.0.0:8000");
    }
}