DOCKER_COMPOSE?=docker-compose
RUN=$(DOCKER_COMPOSE) run --rm app
EXEC?=$(DOCKER_COMPOSE) exec app
CONSOLE=bin/console
PHPCSFIXER?=$(EXEC) php -d memory_limit=1024m vendor/bin/php-cs-fixer

.DEFAULT_GOAL := help
.PHONY: help start stop restart install uninstall reset clear-cache tty clear clean
.PHONY: db-diff db-migrate db-rollback db-reset db-validate wait-for-db
.PHONY: es-populate wait-for-es
.PHONY: watch assets assets-build
.PHONY: lint lint-symfony lint-yaml lint-twig php-cs php-cs-fix security-check
.PHONY: deps
.PHONY: build up perm

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

##
## Project setup
##---------------------------------------------------------------------------

start:                                                                                                 ## Start docker containers
	$(DOCKER_COMPOSE) start

stop:                                                                                                  ## Stop docker containers
	$(DOCKER_COMPOSE) stop

restart:                                                                                               ## Restart docker containers
	$(DOCKER_COMPOSE) restart

install: build up deps perm db-migrate es-populate                                                     ## Create and start docker containers

uninstall: stop                                                                                        ## Remove docker containers
	$(DOCKER_COMPOSE) rm -vf

reset: uninstall install                                                                               ## Remove and re-create docker containers

clear-cache: perm
	$(EXEC) $(CONSOLE) cache:clear --no-warmup
	$(EXEC) $(CONSOLE) cache:warmup

tty:                                                                                                   ## Run app container in interactive mode
	$(RUN) /bin/bash

clear: perm                                                                                            ## Remove all the cache, the logs, the sessions and the built assets
	$(EXEC) rm -rf var/cache/*
	$(EXEC) $(CONSOLE) redis:flushall -n
	rm -rf var/log/*
	rm -rf public/build
	rm -f var/.php_cs.cache

clean: clear                                                                                           ## Clear and remove dependencies
	rm -rf vendor node_modules


##
## Database
##---------------------------------------------------------------------------

wait-for-db:
	$(EXEC) php -r "set_time_limit(60);for(;;){if(@fsockopen('db',3306)){break;}echo \"Waiting for MySQL\n\";sleep(1);}"

db-diff: vendor wait-for-db                                                                            ## Generate a migration by comparing your current database to your mapping information
	$(EXEC) $(CONSOLE) doctrine:migration:diff

db-migrate: vendor wait-for-db                                                                         ## Migrate database schema to the latest available version
	$(EXEC) $(CONSOLE) doctrine:migration:migrate -n

db-rollback: vendor wait-for-db                                                                        ## Rollback the latest executed migration
	$(EXEC) $(CONSOLE) doctrine:migration:migrate prev -n

db-reset: vendor wait-for-db                                                                           ## Reset the database
	$(EXEC) $(CONSOLE) doctrine:database:drop --force --if-exists
	$(EXEC) $(CONSOLE) doctrine:database:create --if-not-exists
	$(EXEC) $(CONSOLE) doctrine:migrations:migrate -n

db-validate: vendor wait-for-db                                                                        ## Check the ORM mapping
	$(EXEC) $(CONSOLE) doctrine:schema:validate


##
## Elasticsearch
##---------------------------------------------------------------------------

wait-for-es:
	$(EXEC) php -r "set_time_limit(60);for(;;){if(@fsockopen('es1',9200)){break;}echo \"Waiting for MySQL\n\";sleep(1);}"

es-populate: vendor wait-for-es                                                                            ## Populate Elasticsearch
	$(EXEC) $(CONSOLE) fos:elastica:populate


##
## Assets
##---------------------------------------------------------------------------

watch: node_modules                                                                                    ## Watch the assets and build their development version on change
	$(EXEC) yarn watch

assets: node_modules                                                                                   ## Build the development version of the assets
	$(EXEC) yarn dev

assets-build: node_modules                                                                              ## Build the production version of the assets
	$(EXEC) yarn build

##
## Tests
##---------------------------------------------------------------------------

lint: lint-symfony php-cs                                                                              ## Run lint on Twig, YAML, PHP and Javascript files

lint-symfony: lint-yaml lint-twig                                                                      ## Lint Symfony (Twig and YAML) files

lint-yaml:                                                                                             ## Lint YAML files
	$(EXEC) $(CONSOLE) lint:yaml config

lint-twig:                                                                                             ## Lint Twig files
	$(EXEC) $(CONSOLE) lint:twig templates

php-cs: vendor                                                                                         ## Lint PHP code
	$(PHPCSFIXER) fix --diff --dry-run --no-interaction -v

php-cs-fix: vendor                                                                                     ## Lint and fix PHP code to follow the convention
	$(PHPCSFIXER) fix

security-check: vendor                                                                                 ## Check for vulnerable dependencies
	$(EXEC) vendor/bin/security-checker security:check

##
## Dependencies
##---------------------------------------------------------------------------

deps: vendor assets                                                                                    ## Install the project PHP and JS dependencies


##


# Internal rules

build:
	$(DOCKER_COMPOSE) pull --parallel --ignore-pull-failures
	$(DOCKER_COMPOSE) build --force-rm

up:
	$(DOCKER_COMPOSE) up -d --remove-orphans

perm:
	$(EXEC) chmod -R 777 var files public/build node_modules vendor
	$(EXEC) chown -R www-data:root var files public/build node_modules vendor

# Rules from files

vendor: composer.lock
	$(EXEC) composer install -n

composer.lock: composer.json
	@echo compose.lock is not up to date.

node_modules: yarn.lock
	$(EXEC) yarn install

yarn.lock: package.json
	@echo yarn.lock is not up to date.
