# Filament

A lightweight admin package to jumpstart your Laravel app.

## Installation

This package can be used with `Laravel 8.x` or higher.

> The following instructions assume a new installation of Laravel with database and mail setup.

```bash
composer require filament/filament dev-master
php artisan migrate
```

## Setup

Before you can use Filament, you must add the necessary `Filament\Traits\FilamentUser` trait to your `App\User` model.

Example:

```php
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Traits\FilamentUser;

class User extends Authenticatable
{
    use Notifiable, FilamentUser;

    //...
}
```

### Create a user

```bash
php artisan filament:user
```
