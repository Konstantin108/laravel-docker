up:
	docker-compose up -d
install:
	docker-compose exec php composer install
env:
	docker-compose exec php cp .env.example .env
migrate:
	docker-compose exec php php artisan migrate --seed
seed:
	docker-compose exec php php artisan db:seed
lint:
	docker-compose exec php ./vendor/bin/pint
stan:
	docker-compose exec php ./vendor/bin/phpstan analyse --memory-limit=512M
test:
	docker-compose exec php php artisan test
optimize:
	docker-compose exec php php artisan optimize:clear
