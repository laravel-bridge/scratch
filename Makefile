#!/usr/bin/make -f

PHP_MAJOR_VERSION := $(shell php -r "echo PHP_MAJOR_VERSION;")

.PHONY: clean clean-all check test analyse coverage

# ---------------------------------------------------------------------

all: test analyse

clean:
	rm -rf ./build

clean-all: clean
	rm -rf ./vendor

check:
	php vendor/bin/phpcs

test: check
ifeq ($(PHP_MAJOR_VERSION), 7)
	phpdbg -qrr vendor/bin/phpunit
else
	php vendor/bin/phpunit
endif

analyse:
	php vendor/bin/phpstan analyse src tests --level=max

coverage: test
	@if [ "`uname`" = "Darwin" ]; then open build/coverage/index.html; fi
