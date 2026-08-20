#!/usr/bin/env bash

mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/database
chmod -R 775 /var/www/html/database

/usr/bin/php /var/www/html/artisan migrate --force --no-interaction
/usr/bin/php /var/www/html/artisan db:seed --force --no-interaction
/usr/bin/php /var/www/html/artisan rates:sync-currencies --no-interaction
/usr/bin/php /var/www/html/artisan rates:sync-latest --no-interaction
/usr/bin/php /var/www/html/artisan rates:sync-parallel --no-interaction

chown -R www-data:www-data /var/www/html/database
chmod -R 775 /var/www/html/database

/usr/bin/php /var/www/html/artisan config:cache --no-ansi -q
/usr/bin/php /var/www/html/artisan route:cache --no-ansi -q
/usr/bin/php /var/www/html/artisan view:cache --no-ansi -q
