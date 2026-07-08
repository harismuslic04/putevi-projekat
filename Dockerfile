FROM serversideup/php:8.2-fpm-nginx

USER root

ENV WEB_DOCUMENT_ROOT=/var/www/html/public

COPY . /var/www/html

RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader

RUN npm install && npm run build

RUN touch database/database.sqlite

ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/var/www/html/database/database.sqlite

RUN chmod -R 775 storage bootstrap/cache database

RUN chown -R 9999:9999 /var/www/html

