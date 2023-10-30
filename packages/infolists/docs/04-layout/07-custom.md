---
title: Custom layouts
---
import LaracastsBanner from "@components/LaracastsBanner.astro"

<LaracastsBanner
    title="Build a Custom Infolist Layout"
    description="Watch the Build Advanced Components for Filament series on Laracasts - it will teach you how to build components, and you'll get to know all the internal tools to help you."
    url="https://laracasts.com/series/build-advanced-components-for-filament/episodes/9"
/>

## View components

Aside from [building custom layout components](custom), you may create "view" components which allow you to create custom layouts without extra PHP classes.

```php
use Filament\Infolists\Components\View;

View::make('filament.infolists.components.box')
```

This assumes that you have a `resources/views/filament/infolists/components/box.blade.php` file.

## Custom layout classes

You may create your own custom component classes and views, which you can reuse across your project, and even release as a plugin to the community.

> If you're just creating a simple custom component to use once, you could instead use a [view component](#view) to render any custom Blade file.

To create a custom column class and view, you may use the following command:

```bash
php artisan make:infolist-layout Box
```

This will create the following layout component class:

```php
use Filament\Infolists\Components\Component;

class Box extends Component
{
    protected string $view = 'filament.infolists.components.box';

    public static function make(): static
    {
        return app(static::class);
    }
}
```

It will also create a view file at `resources/views/filament/infolists/components/box.blade.php`.

## Rendering the component's schema

Inside your view, you may render the component's `schema()` using the `$getChildComponentContainer()` function:

```blade
<div>
    {{ $getChildComponentContainer() }}
</div>
```

## Accessing the Eloquent record

Inside your view, you may access the Eloquent record using the `$getRecord()` function:

```blade
<div>
    {{ $getRecord()->name }}
</div>
```
