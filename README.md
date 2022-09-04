# Resume Builder

Requires composer 2.0+, php 8.1+, and node 16.6+.

To serve the dev env:
```
cp .env.example .env
touch database/database.sqlite
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

To start the dev build:
```
npm i
npm run dev
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
