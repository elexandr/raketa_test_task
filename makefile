.PHONY: help build up down restart logs logs-app logs-web composer-install fix-permissions init

# Определяем переменные
DOCKER_COMPOSE = docker compose
EXEC_PHP = $(DOCKER_COMPOSE) exec app
COMPOSER = $(EXEC_PHP) composer

# Цель по умолчанию
help:
	@echo "Доступные команды:"
	@echo "  make build        - Собрать контейнеры"
	@echo "  make up           - Запустить контейнеры в фоне"
	@echo "  make down         - Остановить контейнеры"
	@echo "  make restart      - Перезапустить контейнеры"
	@echo "  make logs         - Показать логи всех сервисов"
	@echo "  make logs-app     - Показать логи приложения"
	@echo "  make logs-web     - Показать логи nginx"
	@echo "  make composer-install - Установить зависимости Composer"
	@echo "  make fix-permissions - Исправить права доступа на папках"
	@echo "  make env-file     - Создать .env файл из примера (если не существует)"
	@echo "  make init         - Полная инициализация проекта (env-file + build + up + composer-install + fix-permissions)"

# Создание .env файла из примера (если не существует)
env-file:
	@if [ ! -f .env ]; then \
		echo "Создание .env файла из example.env..."; \
		cp example.env .env; \
		echo "Не забудьте отредактировать .env файл и установить правильные значения!"; \
	else \
		echo ".env файл уже существует"; \
	fi

# Сборка контейнеров
build: env-file
	@echo "Сборка Docker контейнеров..."
	$(DOCKER_COMPOSE) build --build-arg UID=${UID} --build-arg GID=${GID} --build-arg APP_ENV=${APP_ENV}

# Запуск контейнеров
up:
	@echo "Запуск Docker контейнеров..."
	$(DOCKER_COMPOSE) up -d

# Остановка контейнеров
down:
	@echo "Остановка Docker контейнеров..."
	$(DOCKER_COMPOSE) down

# Перезапуск контейнеров
restart: down up

# Просмотр логов
logs:
	$(DOCKER_COMPOSE) logs -f

logs-app:
	$(DOCKER_COMPOSE) logs -f app

logs-web:
	$(DOCKER_COMPOSE) logs -f web

# Установка Composer зависимостей
composer-install:
	@echo "Установка Composer зависимостей..."
	$(COMPOSER) install --optimize-autoloader --no-interaction

# Исправление прав доступа
fix-permissions:
	@echo "Исправление прав доступа на папках..."
	$(EXEC_PHP) chmod -R 775 var
	$(EXEC_PHP) setfacl -dR -m u:www-data:rwX var
	$(EXEC_PHP) setfacl -R -m u:www-data:rwX var

# Полная инициализация проекта
init: env-file build up composer-install fix-permissions
	@echo "Проект успешно инициализирован!"
	@echo "Приложение доступно по адресу: http://localhost:8080"