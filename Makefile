fix: ## Fixes php Lint
	docker run --rm -it -w=/app -v ${PWD}:/app oskarstark/php-cs-fixer-ga:latest
