
_STATIC=src/Hoborg/Dashboard/Resources/htdocs/static

all: deps test js css

server:
	ant build
	cd example/htdocs && \
	DASHBOARD_ROOT=`pwd`/.. php -S localhost:4080 2>&1

test: deps
	./vendor/bin/phpunit -c phpunit.xml

test_ci: deps
	./vendor/bin/phpunit -c phpunit.ci.xml

##
# Builds JS
js:
	node \
		scripts/r.js \
		-o scripts/dashboard.build.js \
		out=$(_STATIC)/scripts/hoborglabs/dashboard.js
	node \
		scripts/r.js \
		-o scripts/dashboard.build.js \
		optimize=none \
		out=$(_STATIC)/scripts/hoborglabs/dashboard.unminified.js

##
# Builds CSS files
css:
	./node_modules/recess/bin/recess \
		--compress \
		styles/less/dashboard.less > $(_STATIC)/styles/hoborglabs/css/dashboard.min.css
	./node_modules/recess/bin/recess \
		--compile \
		styles/less/dashboard.less > $(_STATIC)/styles/hoborglabs/css/dashboard.css

deps: composer.phar
	./composer.phar install

composer.phar:
	php -r "readfile('https://getcomposer.org/installer');" | php
	chmod +x composer.phar
