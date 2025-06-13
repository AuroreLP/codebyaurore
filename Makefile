# Commandes par défaut
COMPOSE=docker-compose
COMPOSE_PROD=$(COMPOSE) -f docker-compose.yaml
COMPOSE_DEV=$(COMPOSE)

up-dev:
	$(COMPOSE_DEV) up -d

down-dev:
	$(COMPOSE_DEV) down

logs-dev:
	$(COMPOSE_DEV) logs -f

up-prod:
	$(COMPOSE_PROD) up -d

down-prod:
	$(COMPOSE_PROD) down

logs-prod:
	$(COMPOSE_PROD) logs -f

build:
	$(COMPOSE_DEV) build

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
