#!/usr/bin/env bash

set -ex

if [[ "$COVERAGE" = true ]]; then wget https://scrutinizer-ci.com/ocular.phar; fi
if [[ "$COVERAGE" = true ]]; then php ocular.phar code-coverage:upload --format=php-clover build/coverage.xml; fi
