---
title: Placeholder
---

Placeholders can be used to render text-only "fields" within your forms. Each placeholder has `content()`, which cannot be changed by the user.

```php
use Filament\Forms\Components\Placeholder;

Placeholder::make('label')
    ->content('Content, displayed underneath the label')
```

> **Important:** All fields require a unique name. That also applies to Placeholders!

## Rendering HTML inside the placeholder

You may even render custom HTML within placeholder content:

```php
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

Placeholder::make('documentation')
    ->content(new HtmlString('<a href="https://filamentphp.com/docs">filamentphp.com</a>'))
```
