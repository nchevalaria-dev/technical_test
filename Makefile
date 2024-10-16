DOCKER_COMPOSE=eval "docker compose -f docker-compose.yml"

filter=.

.PHONY: help install test purge

.DEFAULT_GOAL := help

install:
	${DOCKER_COMPOSE} build --no-cache
	${DOCKER_COMPOSE} run --rm backend composer install
	${DOCKER_COMPOSE} run --rm backend php artisan key:generate
	${DOCKER_COMPOSE} up -d
	@echo ""
	@echo "------------------------------------------------"
	@echo "Installation is completed."
	@echo "------------------------------------------------"
	@echo ""

purge:
	${DOCKER_COMPOSE} down -v
	${DOCKER_COMPOSE} rm -f -v

exec:
	${DOCKER_COMPOSE} exec backend /bin/bash

test:
	${DOCKER_COMPOSE} run --rm -v $${PWD}:/var/www backend ./vendor/bin/phpunit --testdox --filter=$(filter)
