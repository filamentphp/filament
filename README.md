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

### Create a Resource

To define a Resource, create a new resource file in `app/Filament/Resources` like the following `Page.php`:

```php
<?php

namespace App\Filament\Resources;

use Filament\Resource;
use App\Http\Livewire\Page\Index;
// use App\Http\Livewire\Page\Show;

class Page extends Resource
{
    public $group = 'Content'; // ability to group resources for organization
    public $label = 'My Pages'; // defaults to formatted & pluralized classname
    public $icon = 'heroicon-o-document-text';
    public $sort = 0;

    public function actions()
    {
        return [
            'index' => Index::class,
            // 'show' => Show::class,
        ];
    }
}
```

### Create a user

```bash
php artisan filament:user
```

---

## Roadmap

- [ ] Settings feature & related tests
- [ ] Resource stubs and associated Artisan commands (e.g. `artisan filament:resource Page --options`)
- [ ] DOCUMENTATION FOR ALL THE THINGS.
