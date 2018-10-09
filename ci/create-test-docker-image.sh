#!/bin/bash

VERSION=${1:-7.1}

sed "s/PHP_VERSION/${VERSION}/g" Dockerfile > Dockerfile.build

docker build -t sealife/php-phpunit-composer-test:${VERSION} -f Dockerfile.build .

docker login

docker push sealife/php-phpunit-composer-test:${VERSION}