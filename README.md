# Filament

A lightweight admin for your Laravel app.

_[In Active Development]_

---

## Installation in laravel

This package can be used with `Laravel 7.x` or higher.

> The following instructions assume a new installation of Laravel.

```bash
laravel new [appName] # follow installation instructions to get your Laravel app setup (DB, mail etc.)
cd [appName]
```

## Installation

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

Change the `filament/filament` version `^x.xx` in your composer.json.

> Note: This is not yet a composer package, so for now the above referenced version will be `@dev` (_symlinked_ as noted in the installation instructions).

Run the following commands:

```bash
composer update
php artisan migrate
php artisan vendor:publish --tag=filament-seeds --force
composer dump-autoload
php artisan db:seed --class=FilamentSeeder
```
