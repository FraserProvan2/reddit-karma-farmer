
##########################################################
# Docker Targets
##########################################################

docker_create:
	git clone -b 'v8.0' --single-branch --depth 1 https://github.com/laradock/laradock.git
	cp laradock-env laradock && cd laradock && cp laradock-env .env
	cd laradock && docker-compose build --no-cache nginx mysql workspace php-fpm

docker_up:
	cd laradock && docker-compose up -d nginx mysql php-fpm 
	cd laradock && docker-compose exec workspace bash

docker_down:
	cd laradock && docker-compose down
  
##########################################################
# Build
##########################################################

build:
	php -r "copy('.env.example', '.env');"
	composer install
	php artisan key:generate

##########################################################
# Local Development
##########################################################

clear_cache: 
	composer dump-autoload
	php artisan cache:clear
	php artisan route:cache
	php artisan config:clear
	php artisan view:clear

test:
	vendor/bin/phpunit 