install:
	composer install

dev-install:
	composer install
	yarn install
	yarn encore --dev

fix: ## Fixes php Lint
	docker run --rm -it -w=/app -v ${PWD}:/app oskarstark/php-cs-fixer-ga:latest

test:
	vendor/phpunit/phpunit/phpunit

run:
	symfony server:start