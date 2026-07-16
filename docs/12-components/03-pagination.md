---
title: Pagination Blade component
---

import AutoScreenshot from "@components/AutoScreenshot.astro"

## Introduction

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

<AutoScreenshot name="components/pagination/simple" alt="Pagination" version="5.x" />

Alternatively, you can use simple pagination or cursor pagination, which will just render a "previous" and "next" button:

```php
use App\Models\User;

User::query()->simplePaginate(10)
User::query()->cursorPaginate(10)
```

<AutoScreenshot name="components/pagination/simple-paginator" alt="Simple pagination" version="5.x" />

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
    current-page-option-property="perPage"
/>
```

<AutoScreenshot name="components/pagination/page-options" alt="Pagination with page size options" version="5.x" />

## Displaying links to the first and the last page

Extreme links are the first and last page links. You can add them by passing the `extreme-links` attribute to the component:

```blade
<x-filament::pagination
    :paginator="$users"
    extreme-links
/>
```

<AutoScreenshot name="components/pagination/extreme-links" alt="Pagination with extreme links" version="5.x" />
