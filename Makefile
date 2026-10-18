.PHONY: up down build test lint types sec e2e arch quality shell composer npm

up:
	docker compose up -d --build

down:
	docker compose down

build:
	docker compose build

shell:
	docker compose exec php bash

composer:
	COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction

npm:
	npm ci || npm install

lint:
	./vendor/bin/pint --test

lint-fix:
	./vendor/bin/pint

types:
	./vendor/bin/phpstan analyse --memory-limit=1G

arch:
	./vendor/bin/deptrac analyse --config-file=deptrac.yaml

test:
	php artisan test

sec:
	COMPOSER_ALLOW_SUPERUSER=1 composer audit
	@echo "npm audit / Trivy / gitleaks: BK-SEC-01 ainda parcial"

e2e:
	@echo "E2E Playwright: adiado até haver UI de negócio (BK-E2E-01). Skip."

quality: lint types arch test sec

ci: quality
