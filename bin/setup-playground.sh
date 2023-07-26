#!/usr/bin/env bash
if [ -d "./playground" ];
then
    echo "Playground already setup."
    exit
fi

echo "Cloning repository..."

git clone git@github.com:filamentphp/demo.git -b 3.x playground &> /dev/null

echo "Configuring application..."

cd playground

rm composer.json
mv composer-local.json composer.json
composer config repositories.0 path ../packages/*
composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed

if [ -x "$(command -v valet)" ];
then
    valet link filament
fi

cd ..
