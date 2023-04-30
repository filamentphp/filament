<p align="center">
    <img src="https://user-images.githubusercontent.com/41773797/131910226-676cb28a-332d-4162-a6a8-136a93d5a70f.png" alt="Banner" style="width: 100%; max-width: 800px;" />
</p>

<p align="center">
    <a href="https://github.com/filamentphp/filament/actions"><img alt="Tests passing" src="https://img.shields.io/badge/Tests-passing-green?style=for-the-badge&logo=github"></a>
    <a href="https://laravel.com"><img alt="Laravel v8.x" src="https://img.shields.io/badge/Laravel-v8.x-FF2D20?style=for-the-badge&logo=laravel"></a>
    <a href="https://laravel-livewire.com"><img alt="Livewire v2.x" src="https://img.shields.io/badge/Livewire-v2.x-FB70A9?style=for-the-badge"></a>
    <a href="https://php.net"><img alt="PHP 8.0" src="https://img.shields.io/badge/PHP-8.0-777BB4?style=for-the-badge&logo=php"></a>
</p>

Admin panel, form builder and table builder for Laravel. Built with the TALL stack. Designed for humans.

## Packages

### Admin Panel • [Documentation](https://filamentphp.com/docs/admin) • [Demo](https://demo.filamentphp.com)

```bash
composer require filament/filament
```

### Form Builder • [Documentation](https://filamentphp.com/docs/forms)

```bash
composer require filament/forms
```

### Table Builder • [Documentation](https://filamentphp.com/docs/tables)

```bash
composer require filament/tables
```

### Notifications • [Documentation](https://filamentphp.com/docs/notifications)

```bash
composer require filament/notifications
```

### Spatie Media Library Plugin • [Documentation](https://filamentphp.com/docs/spatie-laravel-media-library-plugin)

```bash
composer require filament/spatie-laravel-media-library-plugin
```

### Spatie Settings Plugin • [Documentation](https://filamentphp.com/docs/spatie-laravel-settings-plugin)

```bash
composer require filament/spatie-laravel-settings-plugin
```

### Spatie Tags Plugin • [Documentation](https://filamentphp.com/docs/spatie-laravel-tags-plugin)

```bash
composer require filament/spatie-laravel-tags-plugin
```

### Spatie Translatable Plugin • [Documentation](https://filamentphp.com/docs/spatie-laravel-translatable-plugin)

```bash
composer require filament/spatie-laravel-translatable-plugin
```

## Contributing

If you want to contribute to Filament packages, you may want to test it in a real Laravel project:

- Fork this repository to your GitHub account.
- Create a Laravel app locally.
- Clone your fork in your Laravel app's root directory.
- In the `/filament` directory, create a branch for your fix, e.g. `fix/error-message`.

Install the packages in your app's `composer.json`:

```jsonc
{
    // ...
    "require": {
        "filament/filament": "*",
    },
    "minimum-stability": "dev",
    "repositories": [
        {
            "type": "path",
            "url": "filament/packages/*"
        }
    ],
    // ...
}
```

Now, run `composer update`.

### Checking for missing translations

Set up a Laravel app, and install the [admin panel](https://filamentphp.com/docs/admin/installation).

Now, if you want to check for missing Spanish translations, run:

```bash
php artisan filament:check-translations es
```

This will let you know which translations are missing for this locale. You can make a PR with the changes to this repository.

If you've published the translations into your app and you'd like to check those instead, try:

```bash
php artisan filament:check-translations es --source=app
```

## Need Help?

🐞 If you spot a bug, please [submit a detailed issue](https://github.com/filamentphp/filament/issues/new?assignees=&labels=bug%2Cunconfirmed&template=bug_report.yml), and wait for assistance.

🤔 If you have a question or feature request, please [start a new discussion](https://github.com/filamentphp/filament/discussions/new). We also have a [Discord community](https://filamentphp.com/discord). For quick help, ask questions in the appropriate channel.

🔐 If you discover a vulnerability, please review our [security policy](https://github.com/filamentphp/filament/blob/2.x/SECURITY.md).
