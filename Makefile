# Utilisation de docker compose v2 (nouvelle syntaxe)
COMPOSE=docker compose

# Fichiers compose
COMPOSE_FILES_DEV=-f compose.yaml -f compose.override.yaml
COMPOSE_FILES_PROD=-f compose.yaml

# Fichiers .env
ENV_FILE_DEV=--env-file .env.local
ENV_FILE_PROD=--env-file .env

# Commandes compose DEV (ancienne méthode)
COMPOSE_DEV=$(COMPOSE) $(ENV_FILE_DEV) $(COMPOSE_FILES_DEV)

# Commandes compose PROD (ancienne méthode)
COMPOSE_PROD=$(COMPOSE) $(ENV_FILE_PROD) $(COMPOSE_FILES_PROD)

# Nouvelle commande DEV avec profil
COMPOSE_PROFILE_DEV=$(COMPOSE) --profile dev

# -- COMMANDES DEV (ancienne méthode) --
up-dev:
	$(COMPOSE_DEV) up -d

down-dev:
	$(COMPOSE_DEV) down

build-dev:
	$(COMPOSE_DEV) build

logs-dev:
	$(COMPOSE_DEV) logs -f

# -- NOUVELLES COMMANDES DEV (avec profils) --
up-dev-profile:
	$(COMPOSE_PROFILE_DEV) up -d --build

down-dev-profile:
	$(COMPOSE_PROFILE_DEV) down

logs-dev-profile:
	$(COMPOSE_PROFILE_DEV) logs -f

# -- COMMANDES PROD --
up-prod:
	$(COMPOSE_PROD) up -d

down-prod:
	$(COMPOSE_PROD) down

logs-prod:
	$(COMPOSE_PROD) logs -f

build-prod:
	$(COMPOSE_PROD) build

# -- COMMANDES UTILES --
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

