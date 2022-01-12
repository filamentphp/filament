<p align="center">
    <img src="https://user-images.githubusercontent.com/41773797/131910226-676cb28a-332d-4162-a6a8-136a93d5a70f.png" alt="Banner" style="width: 100%; max-width: 800px;" />
</p>

<p align="center">
    <a href="https://github.com/laravel-filament/filament/actions"><img alt="Tests passing" src="https://img.shields.io/badge/Tests-passing-green?style=for-the-badge&logo=github"></a>
    <a href="https://laravel.com"><img alt="Laravel v8.x" src="https://img.shields.io/badge/Laravel-v8.x-FF2D20?style=for-the-badge&logo=laravel"></a>
    <a href="https://laravel-livewire.com"><img alt="Livewire v2.x" src="https://img.shields.io/badge/Livewire-v2.x-FB70A9?style=for-the-badge"></a>
    <a href="https://php.net"><img alt="PHP 8.0" src="https://img.shields.io/badge/PHP-8.0-777BB4?style=for-the-badge&logo=php"></a>
</p>

Filament is a collection of tools for rapidly building beautiful TALL stack interfaces, designed for humans.

## Packages

### Admin Panel • [Documentation](https://filamentadmin.com/docs/admin) • [Demo](https://demo.filamentadmin.com)

```bash
composer require filament/filament
```

### Form Builder • [Documentation](https://filamentadmin.com/docs/forms)

```bash
composer require filament/forms
```

### Table Builder • [Documentation](https://filamentadmin.com/docs/tables)

```bash
composer require filament/tables
```

### spatie/laravel-medialibrary Plugin • [Documentation](https://filamentadmin.com/docs/spatie-laravel-media-library-plugin)

```bash
composer require filament/spatie-laravel-media-library-plugin
```

### spatie/laravel-settings Plugin • [Documentation](https://filamentadmin.com/docs/spatie-laravel-settings-plugin)

```bash
composer require filament/spatie-laravel-settings-plugin
```

### spatie/laravel-tags Plugin • [Documentation](https://filamentadmin.com/docs/spatie-laravel-tags-plugin)

```bash
composer require filament/spatie-laravel-tags-plugin
```

### spatie/laravel-translatable Plugin • [Documentation](https://filamentadmin.com/docs/spatie-laravel-translatable-plugin)

```bash
composer require filament/spatie-laravel-translatable-plugin
```

## Setup Filament in local repository
If you want to contribute to Filament you might want to to test it in a real Laravel project.

* Fork the filament repository
* Create your own branch, example `patch-2.x`
* Create a Laravel app
* Clone the filament repository into the root of your Laravel app
* Checkout your `patch-2.x` branch

```
|-Laravel app
   |-app/
   |-filament/
```

In `composer.json`
```json
"repositories": [
    {
        "type": "path",
        "url": "filament/packages/*",
        "options": {
            "symlink": true
        }
    }
],
"require": {
    "filament/filament": "patch-2.x as 2.x-dev",
    "filament/forms": "patch-2.x as 2.x-dev",
    "filament/tables": "patch-2.x as 2.x-dev",
}
```

```
composer update
```


## Need Help?

🐞 If you spot a bug, please [submit a detailed issue](https://github.com/laravel-filament/filament/issues/new), and wait for assistance.

🤔 If you have a question or feature request, please [start a new discussion](https://github.com/laravel-filament/filament/discussions/new). We also have a [Discord community](https://discord.gg/cpqnMTHZja). For quick help, ask questions in the appropriate package help channel.

🔐 If you discover a vulnerability, please review our [security policy](https://github.com/laravel-filament/filament/blob/2.x/SECURITY.md).
