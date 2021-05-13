EXEC=./scripts/natif/exec.sh

.DEFAULT_GOAL := help
.PHONY: help

help:
		@grep -E '(^[0-9a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-25s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

##---------------------------------------------------------------------------
## Application
##---------------------------------------------------------------------------

init: ## Init project
init: images-build hosts-add env up install
	$(EXEC) info

clear: ## Init project
clear: down hosts-remove
	$(EXEC) envs-remove
	sudo rm -rf vendor

env: ## Set .docker file
	$(EXEC) envs

info: ## Display information
	$(EXEC) info

##---------------------------------------------------------------------------
## Docker
##---------------------------------------------------------------------------
up: ## Deploy the stack
up: networks-create volumes-create
	$(EXEC) up
	$(EXEC) info

swarm-init: ## Init swarm
	$(EXEC) swarm-init

down: ## Remove the stack
	$(EXEC) remove

exec: ## Go to container
	$(EXEC) exec

images-build: ## Build images
	$(EXEC) images-build

images-remove:
	$(EXEC) images-remove

networks-create:
	$(EXEC) networks-create

networks-remove:
	$(EXEC) networks-remove

hosts-add: ## Add hosts
	sudo $(EXEC) hosts-add

hosts-remove: ## Remove hosts
	sudo $(EXEC) hosts-remove

volumes-create:
	$(EXEC) volumes-create

##---------------------------------------------------------------------------
## Docker Sync (OSX)
##---------------------------------------------------------------------------

sync-start: ## Start files synchonization
	docker-sync start

sync-stop: ## Stop files synchonization
	docker-sync stop

sync-clean: ## Clean files synchonization
	docker-sync clean

sync-logs: ## See Docker Sync logs
	docker-sync logs -f

##---------------------------------------------------------------------------
## Symfony commands
##---------------------------------------------------------------------------

install: ## Install dependencies
	$(EXEC) install

fixtures: ## Generate fixtures
	$(EXEC) fixtures

swagger: ## Generate Swagger Contract
	$(EXEC) swagger

migration:
	$(EXEC) migration

##---------------------------------------------------------------------------
## Tests
##---------------------------------------------------------------------------

test: ## Run the tests
	$(EXEC) tests

tu: ## Run the units tests
	$(EXEC) tu

tf: ## Run the functional tests
	$(EXEC) tf

tu-coverage: ## Run the units tests coverage
	$(EXEC) tu_coverage

tf-coverage: ## Run the units tests coverage
	$(EXEC) tf_coverage
##---------------------------------------------------------------------------
## Audit
##---------------------------------------------------------------------------

phpcs: ## Run PHPCS
	$(EXEC) phpcs

phpcpd: ## Run PHPCPD
	$(EXEC) phpcpd

phpmd: ## Run PHPMD
	$(EXEC) phpmd

phpcs-fixer: ## Run PHPMD
	$(EXEC) php_cs_fixer

phpcs-fixer-apply: ## Run PHPMD
	$(EXEC) php_cs_fixer_apply

phpmetrics: ## Run PHPMetrics
	$(EXEC) phpmetrics
