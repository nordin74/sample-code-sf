#!/bin/bash

curl https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh > /usr/local/bin/wait-for-it
chmod 755 /usr/local/bin/wait-for-it

cd /var/www/html/ && ln -sf ../docker/micros/cache.yaml config/packages && composer install -o
bin/console cache:warmup
chmod 777 -R /var/www/html/var

wait-for-it -t 30 database:3306
bin/console doctrine:database:create --if-not-exists && bin/console doctrine:migrations:migrate -vv --no-interaction

nohup bin/backgroundProcess &
/usr/sbin/php-fpm7.4
nginx -g 'daemon off;'
