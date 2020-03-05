#!/usr/bin/env bash

set -ex

vendor/bin/phpunit $PHPUNIT_FLAGS
phpenv config-rm xdebug.ini || true
