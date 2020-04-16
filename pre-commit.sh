#!/bin/bash

./bin/php-cs-fixer fix /var/www/wanderluster/src
./bin/php-cs-fixer fix /var/www/wanderluster/tests

./bin/phpunit tests