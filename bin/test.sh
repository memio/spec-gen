#!/usr/bin/env sh

composer --quiet install --optimize-autoloader

vendor/bin/phpspec --no-interaction run --format=dot
vendor/bin/phpunit
vendor/bin/php-cs-fixer fix --dry-run 
