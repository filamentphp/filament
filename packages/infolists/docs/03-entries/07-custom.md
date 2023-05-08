---
title: Custom entries
---

## View entry

You may render a custom view for a cell using the `view()` method:

```php
use Filament\Infolists\Components\ViewEntry;

ViewEntry::make('status')
    ->view('filament.infolists.entries.status-switcher')
```

Inside your view, you may retrieve the state of the entry using the `$getState()` method:

```blade
<div>
    {{ $getState() }}
</div>
```

You can also access the entire Eloquent record with `$getRecord()`.

## Custom classes

You may create your own custom entry classes and cell views, which you can reuse across your project, and even release as a plugin to the community.

> If you're just creating a simple custom entry to use once, you could instead use a [view entry](#view-entry) to render any custom Blade file.

To create a custom entry class and view, you may use the following command:

```bash
php artisan make:infolist-entry StatusSwitcher
```

This will create the following entry class:

```php
use Filament\Infolists\Components\Entry;

class StatusSwitcher extends Entry
{
    protected string $view = 'filament.infolists.entries.status-switcher';
}
```

Inside your view, you may retrieve the state of the cell using the `$getState()` method:

```blade
<div>
    {{ $getState() }}
</div>
```

You can also access the entire Eloquent record with `$getRecord()`.
