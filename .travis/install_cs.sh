#!/usr/bin/env bash

set -ex

composer self-update --1

phpenv config-rm xdebug.ini

composer update -n
