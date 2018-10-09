#!/bin/bash

vendor/bin/phpunit --bootstrap src/bootstrap/bootstrap.php src/lib/Tests/*

result=$?

exit ${result}