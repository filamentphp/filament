---
title: Navigation
---

## Getting started

By default, Filament will register navigation items for each of your [resources](resources) and [custom pages](pages). These classes contain static properties that you can override, to configure that navigation item and its order:

```php
protected static ?string $navigationIcon = 'heroicon-o-document-text';

protected static ?string $navigationLabel = 'Custom Navigation Label';

protected static ?int $navigationSort = 3;
```

The `$navigationIcon` supports the name of any Blade component, and passes a set of formatting classes to it. By default, the [Blade Heroicons](https://github.com/blade-ui-kit/blade-heroicons) package is installed, so you may use the name of any [Heroicon](https://heroicons.com) out of the box. However, you may create your own custom icon components or install an alternative library if you wish.

## Grouping navigation items

You may group navigation items by specifying a `$navigationGroup` property on a [resource](resources) and [custom page](pages):

```php
protected static ?string $navigationGroup = 'Settings';
```

All items in the same navigation group will be displayed together under the same group label, "Settings" in this case. Ungrouped items will remain at the top of the sidebar.

### Ordering navigation groups

If you wish to enforce a specific order for your navigation groups, you may call `Filament::registerNavigationGroups()` from the `boot()` method of any service provider.

```php
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::registerNavigationGroups([
            'Shop',
            'Blog',
            'Settings',
        ]);
    }
}
```

## Registering custom navigation items

Alternatively, you may completely override the static `registerNavigationItems()` method on the class and register as many custom navigation items as you require:

```php
use Filament\Navigation\NavigationItem;

public static function registerNavigationItems(): void
{
    Filament::registerNavigationItems([
        NavigationItem::make()
            ->group($group)
            ->icon($icon)
            ->isActiveWhen($closure)
            ->label($label)
            ->sort($sort)
            ->url($url),
    ]);
}
```
