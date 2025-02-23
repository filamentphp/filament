---
title: Custom layouts
---
import LaracastsBanner from "@components/LaracastsBanner.astro"

<LaracastsBanner
    title="Build a Custom Schema Layout"
    description="Watch the Build Advanced Components for Filament series on Laracasts - it will teach you how to build components, and you'll get to know all the internal tools to help you."
    url="https://laracasts.com/series/build-advanced-components-for-filament/episodes/7"
    series="building-advanced-components"
/>

## View components

Aside from [building custom layout components](#custom-layout-classes), you may create "view" components which allow you to create custom layouts without extra PHP classes.

```php
use Filament\Schemas\Components\View;

View::make('filament.schema.components.wizard')
```

This assumes that you have a `resources/views/filament/schemas/components/wizard.blade.php` file.

## Custom layout classes

You may create your own custom component classes and views, which you can reuse across your project, and even release as a plugin to the community.

> If you're just creating a simple custom component to use once, you could instead use a [view component](#view-components) to render any custom Blade file.

To create a custom component class and view, you may use the following command:

```bash
php artisan make:filament-schema-layout Wizard
```

This will create the following layout component class:

```php
use Filament\Schemas\Components\Component;

class Wizard extends Component
{
    protected string $view = 'filament.schema.components.wizard';

    public static function make(): static
    {
        return app(static::class);
    }
}
```

It will also create a view file at `resources/views/filament/schemas/components/wizard.blade.php`.

## Rendering the component's schema

Inside your view, you may render the component's `schema()` using the `$getChildSchema()` function:

```blade
<div>
    {{ $getChildSchema() }}
</div>
```

## Accessing the Eloquent record

Inside your view, you may access the Eloquent record using the `$getRecord()` function:

```blade
<div>
    {{ $getRecord()->name }}
</div>
```
