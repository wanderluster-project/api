#!/bin/bash

php bin/console cache:clear --env=test
rm -Rf /var/www/wanderluster/var/storage/*
./bin/php-cs-fixer fix /var/www/wanderluster/src --rules=@Symfony,declare_strict_types --allow-risky=yes
./bin/php-cs-fixer fix /var/www/wanderluster/tests --rules=@Symfony,declare_strict_types --allow-risky=yes
./vendor/bin/phpstan analyse src tests

#/var/www/wanderluster/bin/phpunit tests
phpdbg -qrr /var/www/wanderluster/bin/phpunit tests --coverage-html /var/www/wanderluster/tests/_reports