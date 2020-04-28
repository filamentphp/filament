# Filament

A lightweight admin for your Laravel app.

_[In Active Development]_

---

## Installation in laravel

This package can be used with `Laravel 7.x` or higher.

> The following instructions assume a new installation of Laravel.

```bash
mkdir packages
cd packages
git clone git@github.com:laravel-filament/filament.git filament
git clone git@github.com:laravel-filament/field-file.git filament-field-file
cd ../
laravel new site
cd site # setup laravel with a DB, mail etc. like normal, then return to these instructions for adding Filament.
```

> !!! **VERY IMPORTANT** This package is not yet submitted to Packagist _(for now you will need to clone and symlink the required packages)_ with the following instructions:

#### Add local path directory for packages:

```json
"repositories": [
    {
        "type": "path",
        "url": "../local-path-to-filament-packages-folder/*"
    }
]
```

> This is relative to your core Laravel installation.

#### Include the required filament packages:

```json
"require": {
    "filament/filament": "*",
    "filament/field-file": "*"
}
```

Add the necessary `Filament\Traits\FilamentUser` trait to your `App\User` model.

### Update Composer

```bash
composer update
php artisan migrate
php artisan vendor:publish --tag=filament-seeds
composer dump-autoload
php artisan db:seed --class=FilamentSeeder
```

---

## Create a user

```bash
php artisan filament:user
```
