# Alpine

A lightweight base admin for your Laravel app.

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
composer install madewithalpine/alpine # See note below
php artisan migrate
```

> ToDo: Add to Packagist (for now you will need to clone and [symlink the package](https://calebporzio.com/bash-alias-composer-link-use-local-folders-as-composer-dependancies)).

Add the necessary `Alpine\Traits\AlpineUser` trait to your `App\User` model.

### Create a user

```bash
php artisan alpine:create-user
```

## Upgrading

There are no special requirements for upgrading, other than `changing ^x.xx` version in your composer.json and running `composer update` as well as `php artisan migrate`. _Your app must meet the minimum requirements as well_.
