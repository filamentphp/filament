# Filament

A lightweight admin for your Laravel app.

_[In Active Development]_

---

## Installation in laravel

This package can be used with `Laravel 6.x` or higher.

> The following instructions assume a new installation of Laravel.

```bash
laravel new [appName] # follow installation instructions to get your Laravel app setup (DB, mail etc.)
cd [appName]
```

### Install the package via composer

```bash
composer install filament/filament # See note below
php artisan migrate
php artisan vendor:publish --tag=filament-seeds
composer dump-autoload
php artisan db:seed --class=FilamentSeeder
```

> ToDo: Add to Packagist (for now you will need to clone and [symlink the package](https://calebporzio.com/bash-alias-composer-link-use-local-folders-as-composer-dependancies)).

Add the necessary `Filament\Traits\FilamentUser` trait to your `App\User` model.

### Create a user

```bash
php artisan filament:user
```

## Upgrading

Changing `^x.xx` version in your composer.json.

Run the following commands:

```bash
composer update
php artisan migrate
php artisan vendor:publish --tag=filament-seeds --force
composer dump-autoload
php artisan db:seed --class=FilamentSeeder
```
