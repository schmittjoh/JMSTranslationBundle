#!/usr/bin/env bash

set -ex

composer self-update --1

if [[ $SYMFONY_VERSION ]]; then composer require symfony/symfony:${SYMFONY_VERSION} --no-update; fi

COMPOSER_MEMORY_LIMIT=-1 composer update ${COMPOSER_FLAGS} --no-interaction
