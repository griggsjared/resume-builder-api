# Resume Builder API

Requires composer 2.0+, and php 8.2+.

To serve the dev env:
```
cp .env.example .env
touch database/database.sqlite
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

To run tests:
```
php artisan test
```

To run code style test:
```
./vendor/bin/pint --test
```

To run code style fix:
```
./vendor/bin/pint
```
