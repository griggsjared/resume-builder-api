# Resume API

Requires composer and php 8+

To run the dev environment:

```
cp .env.example .env
touch database/database.sqlite
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve

```
