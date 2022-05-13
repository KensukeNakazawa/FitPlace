cache:
	docker-compose exec application php artisan cache:clear
	docker-compose exec application php artisan config:clear
	docker-compose exec application php artisan route:clear
	docker-compose exec application php artisan view:clear
reboot:
	docker-compose down && docker-compose up -d
test:
	docker-compose exec application php artisan test
	docker-compose exec application php artisan migrate:refresh --seed
refresh-db:
	docker-compose exec application php artisan migrate:refresh --seed
set-up:
	docker-compose up -d
	cp .env.example .env
	mkdir -p storage/framework/cache/data/
	mkdir -p storage/framework/aop/cache
	mkdir -p storage/framework/sessions
	mkdir -p storage/framework/views
	docker-compose exec application composer install
	docker-compose exec application npm ci
	docker-compose exec application php artisan key:generate
	docker-compose exec application php artisan migrate --seed