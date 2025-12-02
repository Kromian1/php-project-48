gendiff:
	./bin/gendif
install:
	composer install
validate:
	composer validate
dump:
	composer dump-autoload
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
test:
	composer exec --verbose phpunit tests
testdox:
	composer exec phpunit -- --testdox tests
test-coverage:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text --coverage-filter=src tests
test-coverage-html:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage --coverage-filter=src tests
test-coverage-clover:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-clover=build/logs/clover.xml --coverage-filter=src tests