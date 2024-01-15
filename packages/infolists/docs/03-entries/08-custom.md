---
title: Custom entries
---
import LaracastsBanner from "@components/LaracastsBanner.astro"

<LaracastsBanner
    title="Build a Custom Infolist Entry"
    description="Watch the Build Advanced Components for Filament series on Laracasts - it will teach you how to build components, and you'll get to know all the internal tools to help you."
    url="https://laracasts.com/series/build-advanced-components-for-filament/episodes/8"
    series="building-advanced-components"
/>

## View entries

You may render a custom view for an entry using the `view()` method:

```php
use Filament\Infolists\Components\ViewEntry;

ViewEntry::make('status')
    ->view('filament.infolists.entries.status-switcher')
```

This assumes that you have a `resources/views/filament/infolists/entries/status-switcher.blade.php` file.

## Custom classes

You may create your own custom entry classes and entry views, which you can reuse across your project, and even release as a plugin to the community.

> If you're just creating a simple custom entry to use once, you could instead use a [view entry](#view-entries) to render any custom Blade file.

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

It will also create a view file at `resources/views/filament/infolists/entries/status-switcher.blade.php`.

## Accessing the state

Inside your view, you may retrieve the state of the entry using the `$getState()` function:

```blade
<div>
    {{ $getState() }}
</div>
```

## Accessing the Eloquent record

Inside your view, you may access the Eloquent record using the `$getRecord()` function:

```blade
<div>
    {{ $getRecord()->name }}
</div>
```
