---
title: Getting started
---

Filament allows you to create completely custom pages for the admin panel.

## Creating a page

To create a new page, you can use:

```bash
php artisan make:filament-page Settings
```

This command will create two files - a page class in the `/Pages` directory of the Filament directory, and a view in the `/pages` directory of the Filament views directory.

Page classes are all full-page [Livewire](https://laravel-livewire.com) components with a few extra utilities you can use with the admin panel.

## Conditionally hiding pages in navigation

You can prevent pages from appearing in the menu by overriding the `shouldRegisterNavigation()` method in your Page class. This is useful if you want to control which users can see the page in the sidebar.

```php
protected static function shouldRegisterNavigation(): bool
{
    return auth()->user()->canManageSettings();
}
```

Please be aware that all users will still be able to visit this page through its direct URL, so to fully limit access you must also also check in the `mount()` method of the page:

```php
public function mount(): void
{
    abort_unless(auth()->user()->canManageSettings(), 403);
}
```

## Customization

Filament will automatically generate a title, navigation label and URL (slug) for your page based on its name. You may override it using static properties of your page class:

```php
protected static ?string $title = 'Custom Page Title';

protected static ?string $navigationLabel = 'Custom Navigation Label';

protected static ?string $slug = 'custom-url-slug';
```

You may also specify a custom header and footer view for any page. You may return them from the `getHeader()` and `getFooter()` methods:

```php
use Illuminate\Contracts\View\View;

protected function getHeader(): View
{
    return view('filament.settings.custom-header');
}

protected function getFooter(): View
{
    return view('filament.settings.custom-footer');
}
```
