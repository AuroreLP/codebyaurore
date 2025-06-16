# Commandes par défaut
COMPOSE=docker-compose

# Définition des fichiers compose
COMPOSE_FILES_DEV=-f compose.yaml -f compose.override.yaml
COMPOSE_FILES_PROD=-f compose.yaml

# Définition des fichiers .env
ENV_FILE_DEV=--env-file .env.local
ENV_FILE_PROD=--env-file .env

# Commandes pour dev
COMPOSE_DEV=$(COMPOSE) $(ENV_FILE_DEV) $(COMPOSE_FILES_DEV)

# Commandes pour prod
COMPOSE_PROD=$(COMPOSE) $(ENV_FILE_PROD) $(COMPOSE_FILES_PROD)

up-dev:
	$(COMPOSE_DEV) up -d

down-dev:
	$(COMPOSE_DEV) down

build-dev:
	$(COMPOSE_DEV) build

logs-dev:
	$(COMPOSE_DEV) logs -f

up-prod:
	$(COMPOSE_PROD) up -d

down-prod:
	$(COMPOSE_PROD) down

logs-prod:
	$(COMPOSE_PROD) logs -f

build-prod:
	$(COMPOSE_PROD) build

restart-dev:
	@echo "🔄 Redémarrage de l'environnement de développement..."
	$(MAKE) down-dev
	$(MAKE) up-dev
	@echo "✅ Environnement de développement relancé !"

restart-prod:
	@echo "🔄 Redémarrage de l'environnement de production..."
	$(MAKE) down-prod
	$(MAKE) up-prod
	@echo "✅ Environnement de production relancé !"

ps:
	$(COMPOSE_DEV) ps

sh:
	$(COMPOSE_DEV) exec www bash

sh-prod:
	$(COMPOSE_PROD) exec www bash
