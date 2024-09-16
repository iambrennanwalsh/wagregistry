#!/bin/sh
set -e

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
  # 1. Install npm dependencies.
  if [ -z "$(ls -A 'node_modules/' 2>/dev/null)" ]; then
	  npm install
  fi

  # 2. Install composer dependencies.
  if [ -z "$(ls -A 'vendor/' 2>/dev/null)" ]; then
		composer install --prefer-dist --no-progress --no-interaction
	fi

  # 3. Create the database if it doesnt exist.
  php bin/console doctrine:database:create --if-not-exists


  # 4. Update the schema.
  php bin/console doctrine:schema:drop -f
	php bin/console doctrine:schema:update -f

  # 5. Configure access control for var/.
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

  # 6. Run the message consumer.
  php bin/console messenger:consume async &

  npm run watch &
fi

exec docker-php-entrypoint "$@"
