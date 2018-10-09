#!/bin/bash

vendor/bin/phpunit --whitelist "src/lib" --coverage-html coverage --coverage-text --bootstrap src/bootstrap/bootstrap.php src/tests/