#!/bin/bash

./bin/php-cs-fixer fix /var/www/wanderluster/src --rules=@Symfony,declare_strict_types,date_time_immutable --allow-risky=yes
./bin/php-cs-fixer fix /var/www/wanderluster/tests --rules=@Symfony,declare_strict_types,date_time_immutable --allow-risky=yes
./vendor/bin/phpstan analyse src tests

./bin/phpunit tests