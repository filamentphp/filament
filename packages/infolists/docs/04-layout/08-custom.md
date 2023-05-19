---
title: Custom layouts
---

## View components

Aside from [building custom layout components](custom), you may create "view" components which allow you to create custom layouts without extra PHP classes.

```php
use Filament\Infolists\Components\View;

View::make('filament.infolists.components.box')
```

Inside your view, you may render the component's `schema()` using the `$getChildComponentContainer()` closure:

```blade
<div>
    {{ $getChildComponentContainer() }}
</div>
```

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

Inside your view, you may render the component's `schema()` using the `$getChildComponentContainer()` closure:

```blade
<div>
    {{ $getChildComponentContainer() }}
</div>
```
