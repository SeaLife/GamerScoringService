#!/bin/bash

if [ "$1" == "docker" ]; then
    docker run --rm -v $(pwd):/app -w /app sealife/php-phpunit-composer-test:7.1 ci/run-locally.sh
else
    vendor/bin/phpunit --whitelist "src/lib" --colors=never --coverage-text --coverage-html coverage --bootstrap src/bootstrap/bootstrap.php src/tests/

    result=$?

    exit ${result}
fi