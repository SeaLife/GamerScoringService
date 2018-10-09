#!/bin/bash

ci/setup.sh

isLoaded=$(php -ini | grep 'xdebug' | wc -l)

echo "Is XDebug Loaded?: " ${isLoaded}

vendor/bin/phpunit --whitelist "src/lib" --coverage-html coverage.html --bootstrap src/bootstrap/bootstrap.php src/lib/Tests/*