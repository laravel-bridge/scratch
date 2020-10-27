#!/usr/bin/make -f

.PHONY: clean clean-all check test analyse coverage

# ---------------------------------------------------------------------

all: test analyse

clean:
	rm -rf ./build

clean-all: clean
	rm -rf ./vendor
	rm -f ./composer.json

check:
	php vendor/bin/phpcs

test: check
	phpdbg -qrr vendor/bin/phpunit

coverage: test
	@if [ "`uname`" = "Darwin" ]; then open build/coverage/index.html; fi
