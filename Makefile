ifneq (,$(wildcard ./.env))
  include ./.env
  export
endif

ifneq (,$(wildcard ./.env.local))
  include ./.env.local
  export
endif

ifneq (,$(wildcard ./.env.$(APP_ENV)))
  include ./.env.$(APP_ENV)
  export
endif

ifneq (,$(wildcard ./.env.$(APP_ENV).local))
  include ./.env.$(APP_ENV).local
  export
endif

DOCKER_COMPOSE=docker compose

## —— Miscellaneous ————————————————————————————————————————————————————————————————
.DEFAULT_GOAL = help
.PHONY        : help docker.build docker.up docker.start docker.down docker.logs symfony symfony.list symfony.cache composer composer.install npm npm.install npm.dev npm.build npm.prod

help: ## Outputs the help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
docker.build: ## Builds the Docker images
	@$(DOCKER_COMPOSE) build --pull --no-cache

docker.up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMPOSE) up --build --detach

docker.down: ## Stop the docker hub
	@$(DOCKER_COMPOSE) down --remove-orphans

docker.sh: ## Connect to the container
	@$(DOCKER_COMPOSE) exec app /bin/bash
