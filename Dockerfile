FROM alpine:3.9
LABEL Maintainer="Ibragim Abubakarov <ibragim.ai95@gmail.com>" \
      Description="Léger conteneur avec Nginx et PHP-FPM 7.2 basé sur Alpine Linux."

# Install packages
RUN apk --no-cache add \
    curl \
    nginx \
    php7 \
    php7-ctype \
    php7-curl \
    php7-dom \
    php7-fpm \
    php7-cgi \
    php7-apc \
    php7-common \
    php7-gd \
    php7-json \
    php7-exif \
    php7-fileinfo \
    php7-opcache \
    php7-pdo \
    php7-pdo_mysql \
    php7-zip \
    php7-intl \
    php7-tokenizer \
    php7-mbstring \
    php7-gettext \
    php7-mysqli \
    php7-openssl \
    php7-phar \
    php7-bcmath \
    php7-xml \
    php7-xmlreader \
    php7-xmlwriter \
    php7-simplexml \
    php7-session \
    php7-zlib \
    supervisor \
    composer


COPY .docker/fpm-pool.conf /etc/php7/php-fpm.d/docker_custom.conf
COPY .docker/php.ini /etc/php7/conf.d/docker_custom.conf
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create webroot directories
RUN mkdir -p /src/opt
WORKDIR /opt/src/
COPY . /opt/src/

RUN rm -rf var/cache/* && sudo rm -rf var/logs/* && sudo rm -rf var/sessions/* && HTTPDUSER=`ps aux | grep -E '[a]pache| && [h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1` && sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/logs var/sessions && sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/logs var/sessions

CMD bash -c "composer install"
CMD ["php", "bin/console", "cache:clear"]

EXPOSE 80
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
