FROM php:8.3-apache
RUN docker-php-ext-install pdo_mysql \
 && a2enmod headers rewrite \
 && sed -ri 's!/var/www/html!/var/www/app/public!g' /etc/apache2/sites-available/000-default.conf \
 && printf "ServerTokens Prod\nServerSignature Off\n" > /etc/apache2/conf-enabled/zz-hardening.conf \
 && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
 && printf "expose_php=Off\ndisplay_errors=Off\nlog_errors=On\nsession.cookie_httponly=1\n" > "$PHP_INI_DIR/conf.d/zz-security.ini"
WORKDIR /var/www/app
# Code en lecture seule pour www-data (root propriétaire)
COPY . /var/www/app

