FROM serversideup/php:8.2-fpm-nginx

# Privremeno prelazimo na root korisnika kako bismo resili dozvole i instalaciju
USER root

# Podesavanje dokument korena za Laravel
ENV WEB_DOCUMENT_ROOT=/var/www/html/public

# Kopiranje projektnih fajlova
COPY . /var/www/html

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

# Dodeljujemo vlasnistvo nad fajlovima web serveru (korisnik 9999 na serversideup slikama)
RUN chown -R 9999:9999 /var/www/html && \
    chmod 777 database/database.sqlite

# Vracamo se na sigurnog korisnika (9999) koji ce pokretati web server
USER 9999
