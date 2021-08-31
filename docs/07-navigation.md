---
title: Navigation
---

By default, Filament will register navigation items for each of your [resources](resources) and [custom pages](pages). These classes contain static properties that you can override, to configure that navigation item and its order:

```php
public static $icon = 'heroicon-o-document-text';

public static $navigationLabel = 'Custom Navigation Label';

public static $navigationSort = 3;
```

The `$icon` supports the name of any Blade component, and passes a set of formatting classes to it. By default, the [Blade Heroicons](https://github.com/blade-ui-kit/blade-heroicons) package is installed, so you may use the name of any [Heroicon](https://heroicons.com) out of the box. However, you may create your own custom icon components or install an alternative library if you wish.

Alternatively, you may completely override the static `navigationItems()` method on the class and register as many custom navigation items as you require:

```php
use Filament\NavigationItem;

public static function navigationItems()
{
    return [
        NavigationItem::make($label, $url)
            ->activeRule($activeRule)
            ->icon($icon = 'heroicon-o-document-text')
            ->sort($sort = 0),
    ];
}
```
