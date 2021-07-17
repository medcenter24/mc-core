run:
	docker-compose up -d
	docker exec -it mc-core-build composer install
	docker exec -it mc-core-build yarn && yarn run dev
	docker exec -it mc-core-build /usr/local/bin/php artisan serve --port 4043 --host 0.0.0.0 --env APP_CONFIG_PATH=/var/www/html/DevEnvConfig
stop:
	docker-compose down

setup:
	docker exec -it mc-core-build /usr/local/bin/php artisan setup:seed
	docker exec -it mc-core-build chown 1000:1000 -R /var/www/html/DevEnvConfig
	docker exec -it mc-core-build chmod +rw -R /var/www/html/DevEnvConfig

test:
	docker exec -it mc-core-build /usr/local/bin/php artisan test
