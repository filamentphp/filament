---
title: Pagination Blade component
---

## Overview

The pagination component can be used in a Livewire Blade view only. It can render a list of paginated links:

```php
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListUsers extends Component
{
    // ...
    
    public function render(): View
    {
        return view('livewire.list-users', [
            'users' => User::query()->paginate(10),
        ]);
    }
}
```

```blade
<x-filament::pagination :paginator="$users" />
```

Alternatively, you can use simple pagination or cursor pagination, which will just render a "previous" and "next" button:

```php
use App\Models\User;

User::query()->simplePaginate(10)
User::query()->cursorPaginate(10)
```

## Allowing the user to customize the number of items per page

You can allow the user to customize the number of items per page by passing an array of options to the `page-options` attribute. You must also define a Livewire property where the user's selection will be stored:

```php
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListUsers extends Component
{
    public int | string $perPage = 10;
    
    // ...
    
    public function render(): View
    {
        return view('livewire.list-users', [
            'users' => User::query()->paginate($this->perPage),
        ]);
    }
}
```

```blade
<x-filament::pagination
    :paginator="$users"
    :page-options="[5, 10, 20, 50, 100, 'all']"
    :current-page-option-property="perPage"
/>
```
