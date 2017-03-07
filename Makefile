APP=docker-compose exec -T app
CONSOLE=$(APP) bin/console

.PHONY: help install start stop deps composer db-create db-fixtures db-update clear-cache clear-all perm clean

help:           ## Show this help
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

install:        ## [start deps db-create, db-fixtures] Setup the project using Docker and docker-compose
install: start deps db-create db-fixtures perm

start:          ## Start the Docker containers
	docker-compose up -d

stop:           ## Stop the Docker containers
	docker-compose down

deps:           ## [composer perm] Install the project PHP dependencies
deps: composer perm

composer:       ## Install the project PHP dependencies
	$(APP) composer install

db-create:      ## Create the database and load the fixtures in it
	$(APP) php -r "for(;;){if(@fsockopen('db',3306)){break;}}" # Wait for MariaDB
	$(CONSOLE) doctrine:database:drop --force --if-exists
	$(CONSOLE) doctrine:database:create --if-not-exists
	$(CONSOLE) doctrine:schema:create

db-fixtures:    ## Reloads the data fixtures for the dev environment
	$(CONSOLE) doctrine:fixtures:load -n

db-update:      ## Update the database structure according to the last changes
	$(CONSOLE) doctrine:schema:update --force

clear-cache:    ## Clear the application cache in development
	$(CONSOLE) cache:clear

clear-all:      ## Deeply clean the application (remove all the cache, the logs, the sessions and the built assets)
	$(CONSOLE) cache:clear --no-warmup
	$(CONSOLE) cache:clear --no-warmup --env=prod
	$(CONSOLE) cache:clear --no-warmup --env=test
	$(APP) rm -rf var/logs/*
	$(APP) rm -rf var/sessions/*
	$(APP) rm -rf supervisord.log supervisord.pid npm-debug.log .tmp

clean:          ## Removes all generated files
	- @make clear-all
	$(APP) rm -rf vendor node_modules

perm:           ## Fix the application cache and logs permissions
	$(APP) chmod 777 -R var

import-offices: ## Import the voting offices and log the error into data/output.log file
	$(CONSOLE) procuration:offices:import > data/output.log

test-php:       ## Run the PHP tests suite
	$(APP) vendor/bin/phpunit
