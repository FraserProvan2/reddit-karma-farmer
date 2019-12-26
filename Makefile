docker_create:
	git clone -b 'v8.0' --single-branch --depth 1 https://github.com/laradock/laradock.git
	cp laradock-env laradock && cd laradock && cp laradock-env .env
	cd laradock && docker-compose build --no-cache nginx mysql workspace php-fpm

docker_up:
	cd laradock && docker-compose up -d nginx mysql php-fpm 
	cd laradock && docker-compose exec workspace bash

docker_down:
	cd laradock && docker-compose down
