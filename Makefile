PHP := php
CONSOLE := $(PHP) bin/console

COMPOSE_FILE := -f docker-compose.yml

install:
	composer install

docker-up-local:
	docker-compose $(COMPOSE_FILE) up --build -d

docker-exec-php:
	docker-compose $(COMPOSE_FILE) exec php bash