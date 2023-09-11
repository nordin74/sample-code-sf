FROM ubuntu:focal
ENV DEBIAN_FRONTEND=noninteractive TZ=UTC

RUN apt update && apt upgrade -y && apt install -y apt-utils software-properties-common curl git zip nginx
RUN add-apt-repository -y ppa:ondrej/php \
    && apt install -y php7.4-sqlite3 php7.4-fpm php7.4-mysql php7.4-mbstring php7.4-curl php7.4-dom php7.4-apcu php7.4-redis\
    && mkdir -p /run/php

RUN sed -i 's~;opcache.enable=1~opcache.enable=1~g' /etc/php/7.4/fpm/php.ini \
    && sed -i 's~;realpath_cache_size~realpath_cache_size~g' /etc/php/7.4/fpm/php.ini \
    && sed -i 's~;realpath_cache_ttl~realpath_cache_ttl~g' /etc/php/7.4/fpm/php.ini \
    && sed -i 's~;opcache.preload=~opcache.preload=/var/www/html/config/preload.php~g' /etc/php/7.4/fpm/php.ini \
    && sed -i 's~;opcache.preload_user=~opcache.preload_user=www-data~g' /etc/php/7.4/fpm/php.ini \
    && sed -i 's~;opcache.validate_timestamps=1~opcache.validate_timestamps=0~g' /etc/php/7.4/fpm/php.ini \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN sed -i 's~root /var/www/html~root /var/www/html/public~g' /etc/nginx/sites-available/default \
    && sed -i 's~server_name _;~server_name localhost;~g' /etc/nginx/sites-available/default \
    && sed -i 's~try_files $uri $uri/ =404;~try_files $uri /index.php$is_args$args;~g' /etc/nginx/sites-available/default \
    && sed -i 's/#location \~ \\.php/location ~ \\.php/g' /etc/nginx/sites-available/default \
    && sed -i 's~#\tinclude snippets/fastcgi-php.conf;~\tinclude snippets/fastcgi-php.conf;~g' /etc/nginx/sites-available/default \
    && sed -i 's~#\tfastcgi_pass unix:/var/run/php/php7.4~\tfastcgi_pass unix:/var/run/php/php7.4~g' /etc/nginx/sites-available/default \
    && sed -zi 's~9000;\n\t#}~9000;\n\t}~g' /etc/nginx/sites-available/default

ADD start.sh /
RUN chmod +x /start.sh
CMD ["/start.sh"]
