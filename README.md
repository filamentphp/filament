# Filament

A lightweight admin package to jumpstart your Laravel app.

## Installation

This package can be used with `Laravel 8.x` or higher.

> The following instructions assume a new installation of Laravel with database and mail setup.

```bash
composer require filament/filament dev-master
php artisan migrate
```

### Create a Resource

To define a Resource, create a new resource file in `app/Filament/Resources` like the following `Page.php`.

```php
<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\Auth;
use Filament\Resource;
use App\Http\Livewire\Page\Index;

class Page extends Resource
{
    public $label = 'My Pages'; // defaults to formatted & pluralized classname
    public $icon = 'heroicon-o-document-text';
    public $sort = 0;

    public function __construct()
    {
        // Enable navigation visibilty and access to entire resource via policy etc.
        $this->enabled = Auth::user()->can('Access My Pages');
    }

    public function actions()
    {
        return [
            'index' => Index::class,
        ];
    }
}
```

> Below is an example of a corresponding Livewire component defined in the resource above:

```php
<?php

namespace App\Http\Livewire\Page;

use Livewire\Component;
use App\Models\Page;

class Index extends Component
{
    public function render()
    {
        return view('livewire.page.index', [
            'pages' => Page::paginate(12),
        ])->layout('filament::layouts.app', ['title' => __('Pages')]);
    }
}
```

### Create a user

```bash
php artisan make:filament-user
```

---

## Roadmap

- [ ] Dashboard widgets
    - Bookmarks Feature with associated widget
- [ ] Resource stubs and associated Artisan commands (e.g. `artisan filament:resource Page --options`)
- [ ] DOCUMENTATION FOR ALL THE THINGS.
