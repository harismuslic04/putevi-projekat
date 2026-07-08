FROM serversideup/php:8.2-fpm-nginx

# Podesavanje dokument korena za Laravel
ENV WEB_DOCUMENT_ROOT=/var/www/html/public

# Kopiranje projektnih fajlova sa odgovarajucim vlasnistvom
COPY --chown=9999:9999 . /var/www/html

# Instaliranje PHP zavisnosti preko Composera
RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist

# Kreiranje SQLite baze podataka i dodela dozvola
RUN touch database/database.sqlite && \
    chmod -R 777 storage bootstrap/cache database/database.sqlite

# Pokretanje migracija i seeder-a tokom build faze tako da baza bude upakovana u Docker sliku
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/var/www/html/database/database.sqlite
ENV APP_KEY=base64:081WI5fRy6c5p0SrkAcRkEEvqfEvTqcuev8eYtb1Nt8=

RUN php artisan migrate --force && \
    php artisan db:seed --force

# Ponovno postavljanje dozvola za bazu nakon sto je popunjena podacima
RUN chmod 777 database/database.sqlite
