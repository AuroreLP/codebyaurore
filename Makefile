# Utilisation de docker compose v2 (nouvelle syntaxe)
COMPOSE=docker compose

# Fichiers compose
COMPOSE_FILES_DEV=-f compose.yaml -f compose.override.yaml
COMPOSE_FILES_PROD=-f compose.yaml

# Fichiers .env
ENV_FILE_DEV=--env-file .env
ENV_FILE_PROD=--env-file .env.prod

# -- Commandes avec profil DEV --
up-dev-profile:
	$(COMPOSE) $(COMPOSE_FILES_DEV) $(ENV_FILE_DEV) --profile dev up -d --build

down-dev-profile:
	$(COMPOSE) $(COMPOSE_FILES_DEV) $(ENV_FILE_DEV) --profile dev down

logs-dev-profile:
	$(COMPOSE) $(COMPOSE_FILES_DEV) $(ENV_FILE_DEV) --profile dev logs -f

# -- Commandes avec profil PROD --
up-prod-profile:
	$(COMPOSE) $(COMPOSE_FILES_PROD) $(ENV_FILE_PROD) --profile prod up -d --build

down-prod-profile:
	$(COMPOSE) $(COMPOSE_FILES_PROD) $(ENV_FILE_PROD) --profile prod down

logs-prod-profile:
	$(COMPOSE) $(COMPOSE_FILES_PROD) $(ENV_FILE_PROD) --profile prod logs -f

# -- COMMANDES UTILES --
restart-dev:
	@echo "🔄 Redémarrage de l'environnement de développement..."
	$(MAKE) down-dev-profile
	$(MAKE) up-dev-profile
	@echo "✅ Environnement de développement relancé !"

restart-prod:
	@echo "🔄 Redémarrage de l'environnement de production..."
	$(MAKE) down-prod-profile
	$(MAKE) up-prod-profile
	@echo "✅ Environnement de production relancé !"

ps:
	$(COMPOSE) $(COMPOSE_FILES_DEV) $(ENV_FILE_DEV) ps

sh:
	$(COMPOSE) $(COMPOSE_FILES_DEV) $(ENV_FILE_DEV) exec www bash

sh-prod:
	$(COMPOSE) $(COMPOSE_FILES_PROD) $(ENV_FILE_PROD) exec www bash
